<?php

namespace App\Services;

use App\Models\Lesson;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LessonDoubtSolver
{
    /**
     * Ask Gemini a student question, grounded in the lesson's own content.
     *
     * @param  Lesson  $lesson
     * @param  string  $question
     * @param  array<int, array{role: string, body: string}>  $history  Prior turns, oldest first.
     * @return string  Plain-text answer.
     */
    public function ask(Lesson $lesson, string $question, array $history = []): string
    {
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-2.5-flash');
        $endpoint = rtrim(config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta/models'), '/');

        if (!$apiKey) {
            throw new \RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $systemPrompt = $this->buildSystemPrompt($lesson);
        $contents = $this->buildContents($systemPrompt, $history, $question);

        $url = "{$endpoint}/{$model}:generateContent?key={$apiKey}";

        $response = Http::timeout(30)
            ->acceptJson()
            ->post($url, [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.4,
                ],
            ]);

        if (!$response->successful()) {
            Log::warning('Gemini doubt-solver request failed', [
                'lesson_id' => $lesson->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('Gemini request failed.');
        }

        $data = $response->json();
        $text = data_get($data, 'candidates.0.content.parts.0.text');

        if (!$text) {
            Log::warning('Gemini doubt-solver returned empty text', [
                'lesson_id' => $lesson->id,
                'response' => $data,
            ]);

            throw new \RuntimeException('Gemini returned no answer text.');
        }

        return trim($text);
    }

    private function buildSystemPrompt(Lesson $lesson): string
    {
        $lessonContent = strip_tags((string) $lesson->content);
        $lessonContent = trim(preg_replace('/\s+/', ' ', $lessonContent));

        // Keep the lesson context bounded so prompts stay cheap and fast.
        if (mb_strlen($lessonContent) > 6000) {
            $lessonContent = mb_substr($lessonContent, 0, 6000) . '…';
        }

        return <<<TEXT
You are a helpful teaching assistant embedded in a lesson page on an online course platform.
A student is asking a question about THIS specific lesson:

Lesson title: {$lesson->title}

Lesson content:
{$lessonContent}

Instructions:
- Answer using the lesson content above as your primary source of truth.
- If the question is unrelated to this lesson or the course subject, politely say so and steer the student back to the lesson topic. Do not answer unrelated general-knowledge questions.
- Keep answers concise and clear, aimed at a learner (not a fellow expert).
- Use plain text. No markdown headers. Short paragraphs or a simple numbered list if listing steps.
- If you don't know or the lesson doesn't cover it, say so honestly instead of guessing.
TEXT;
    }

    private function buildContents(string $systemPrompt, array $history, string $question): array
    {
        $contents = [
            [
                'role' => 'user',
                'parts' => [['text' => $systemPrompt]],
            ],
            [
                'role' => 'model',
                'parts' => [['text' => 'Understood. I will answer strictly based on this lesson and keep responses clear and concise.']],
            ],
        ];

        foreach ($history as $turn) {
            $contents[] = [
                'role' => $turn['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $turn['body']]],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $question]],
        ];

        return $contents;
    }
}
