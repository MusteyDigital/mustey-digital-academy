<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DrabTaskGenerator
{
    public function generate(string $difficulty = 'easy'): array
    {
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-2.5-flash');
        $endpoint = rtrim(config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta/models'), '/');

        if (!$apiKey) {
            throw new \RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $prompt = $this->buildPrompt($difficulty);
        $url = "{$endpoint}/{$model}:generateContent?key={$apiKey}";

        $response = Http::timeout(30)
            ->acceptJson()
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                    'temperature' => 0.5,
                ],
            ]);

        if (!$response->successful()) {
            Log::warning('Gemini DRAB structured generation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('Gemini request failed.');
        }

        $data = $response->json();
        $text = data_get($data, 'candidates.0.content.parts.0.text');

        if (!$text) {
            Log::warning('Gemini DRAB generation returned empty text', ['response' => $data]);
            throw new \RuntimeException('Gemini returned no task text.');
        }

        $task = json_decode($text, true);

        if (!is_array($task)) {
            Log::warning('Gemini DRAB generation returned invalid JSON', ['text' => $text]);
            throw new \RuntimeException('Gemini returned invalid JSON.');
        }

        $this->validateTask($task);

        return [
            'input' => $task['input'],
            'family' => (string) $task['family'],
            'complexity_level' => (int) $task['complexity_level'],
            'signature' => (string) $task['signature'],
            'rule_type' => (string) $task['rule_type'],
            'params' => $task['params'],
            'rule' => (string) $task['rule_text'],
        ];
    }

    private function buildPrompt(string $difficulty): string
    {
        $rules = <<<TEXT
You are generating ONE structured reasoning task for a student.

Return ONLY valid JSON. No markdown. No commentary.

Required keys:
- input
- family
- complexity_level
- signature
- rule_type
- params
- rule_text

Allowed families and rule types:
1. parity -> parity_transform
2. threshold -> threshold_transform
3. sequence -> sequence_pattern
4. discovery -> rule_discovery
5. multi_step -> two_step_parity
6. chain -> conditional_chain
7. reverse -> reverse_logic
8. distractor -> distractor_sequence
9. position -> position_based
10. mixed -> mixed_operations

Rules:
- complexity_level must be 1 to 4
- signature must be short and deterministic for the rule shape
- do not include answer
- do not include explanation
- keep tasks learner-friendly but mentally engaging
TEXT;

        $difficultyGuide = match ($difficulty) {
            'easy' => 'Use complexity_level 1 or 2. Prefer parity_transform and threshold_transform.',
            'medium' => 'Use complexity_level 2 or 3. Prefer threshold_transform, sequence_pattern, rule_discovery, distractor_sequence, mixed_operations.',
            'hard' => 'Use complexity_level 3 or 4. Prefer two_step_parity, conditional_chain, reverse_logic, position_based, mixed_operations, distractor_sequence.',
            default => 'Use complexity_level 1.',
        };

        return $rules . "\n\nDifficulty: {$difficulty}\n{$difficultyGuide}";
    }

    private function validateTask(array $task): void
    {
        foreach (['input', 'family', 'complexity_level', 'signature', 'rule_type', 'params', 'rule_text'] as $key) {
            if (!array_key_exists($key, $task)) {
                throw new \RuntimeException("Gemini task missing key: {$key}");
            }
        }

        if (!is_string($task['family']) || trim($task['family']) === '') {
            throw new \RuntimeException('Gemini task family must be a non-empty string.');
        }

        if (!is_numeric($task['complexity_level'])) {
            throw new \RuntimeException('Gemini task complexity_level must be numeric.');
        }

        if (!is_string($task['signature']) || trim($task['signature']) === '') {
            throw new \RuntimeException('Gemini task signature must be a non-empty string.');
        }

        if (!is_string($task['rule_type']) || trim($task['rule_type']) === '') {
            throw new \RuntimeException('Gemini task rule_type must be a non-empty string.');
        }

        if (!is_array($task['params']) || empty($task['params'])) {
            throw new \RuntimeException('Gemini task params must be a non-empty array.');
        }

        if (!is_string($task['rule_text']) || trim($task['rule_text']) === '') {
            throw new \RuntimeException('Gemini task rule_text must be a non-empty string.');
        }
    }
}
