<?php

namespace App\Http\Controllers;

use App\Models\DrabAttempt;
use App\Models\Lesson;
use App\Services\DrabTaskGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DrabController extends Controller
{
    public function __construct(
        protected DrabTaskGenerator $taskGenerator
    ) {
    }

    public function index(Request $request, Lesson $lesson)
    {
        $requestedDifficulty = $request->query('difficulty', 'easy');
        $user = $request->user();

        $sessionMode = (bool) session('drab_session_active_' . $lesson->id, false);
        $sessionLockedDifficulty = session('drab_session_difficulty_' . $lesson->id);

        if ($sessionMode && !empty($sessionLockedDifficulty)) {
            $requestedDifficulty = $sessionLockedDifficulty;
        }

        $easyCorrectCount = $user
            ? DrabAttempt::where('user_id', $user->id)->where('difficulty', 'easy')->where('correct_tasks', '>', 0)->count()
            : 0;

        $mediumCorrectCount = $user
            ? DrabAttempt::where('user_id', $user->id)->where('difficulty', 'medium')->where('correct_tasks', '>', 0)->count()
            : 0;

        $difficultyUnlocks = [
            'easy' => true,
            'medium' => $easyCorrectCount >= 3,
            'hard' => $mediumCorrectCount >= 3,
        ];

        $difficulty = $requestedDifficulty;

        if ($difficulty === 'medium' && !$difficultyUnlocks['medium']) {
            $difficulty = 'easy';
        }

        if ($difficulty === 'hard' && !$difficultyUnlocks['hard']) {
            $difficulty = $difficultyUnlocks['medium'] ? 'medium' : 'easy';
        }

        $recentPerformanceAttempts = $user
            ? DrabAttempt::where('user_id', $user->id)->latest()->take(5)->get(['difficulty', 'accuracy'])
            : collect();

        $adaptiveAverageAccuracy = $recentPerformanceAttempts->count() > 0
            ? (float) $recentPerformanceAttempts->avg('accuracy')
            : null;

        $adaptiveSuggestedDifficulty = $difficulty;
        $adaptiveReason = 'Stay consistent and keep building your reasoning skills.';

        if ($adaptiveAverageAccuracy !== null) {
            if ($adaptiveAverageAccuracy >= 80) {
                if ($difficulty === 'easy' && $difficultyUnlocks['medium']) {
                    $adaptiveSuggestedDifficulty = 'medium';
                    $adaptiveReason = 'You are performing strongly. Try Medium for a harder reasoning challenge.';
                } elseif ($difficulty === 'medium' && $difficultyUnlocks['hard']) {
                    $adaptiveSuggestedDifficulty = 'hard';
                    $adaptiveReason = 'Your recent accuracy is strong. You are ready for Hard mode.';
                } else {
                    $adaptiveReason = 'You are performing strongly. Keep pushing at this level.';
                }
            } elseif ($adaptiveAverageAccuracy < 50) {
                if ($difficulty === 'hard') {
                    $adaptiveSuggestedDifficulty = $difficultyUnlocks['medium'] ? 'medium' : 'easy';
                    $adaptiveReason = 'Your recent accuracy dropped. Step down one level to rebuild confidence.';
                } elseif ($difficulty === 'medium') {
                    $adaptiveSuggestedDifficulty = 'easy';
                    $adaptiveReason = 'Your recent accuracy suggests you should rebuild on Easy first.';
                } else {
                    $adaptiveReason = 'Take your time on Easy and focus on understanding each rule clearly.';
                }
            } else {
                $adaptiveReason = 'Your current level looks balanced. Keep practicing to improve consistency.';
            }
        }

        $adaptiveWeakRuleType = $user
            ? $this->getAdaptiveWeakRuleType($user->id, $difficulty)
            : null;

        
try {
            $generated = $this->taskGenerator->generate($difficulty);
            $task = $this->buildVerifiedTask($generated);
            $taskSource = 'ai';
        } catch (\Throwable $e) {
            Log::warning('Falling back to local benchmark task generator', [
                'difficulty' => $difficulty,
                'error' => $e->getMessage(),
            ]);

            $task = $this->generateLocalTask($difficulty, $adaptiveWeakRuleType, $lesson->id);
            $taskSource = 'local';
        }

        session([
            'drab_task_' . $lesson->id => $task,
            'drab_difficulty_' . $lesson->id => $difficulty,
            'drab_task_source_' . $lesson->id => $taskSource,
        ]);

        $recentAttempts = $user
            ? DrabAttempt::where('user_id', $user->id)->where('lesson_id', $lesson->id)->latest()->take(5)->get()
            : collect();

        $sessionMode = (bool) session('drab_session_active_' . $lesson->id, false);
        $sessionTarget = (int) session('drab_session_target_' . $lesson->id, 5);
        $sessionCompleted = (int) session('drab_session_completed_' . $lesson->id, 0);
        $sessionCorrect = (int) session('drab_session_correct_' . $lesson->id, 0);
        $sessionXp = (int) session('drab_session_xp_' . $lesson->id, 0);

        $timedMode = (bool) session('drab_timed_active_' . $lesson->id, false);
        $timedStartedAt = (int) session('drab_timed_started_at_' . $lesson->id, 0);
        $timedDuration = (int) session('drab_timed_duration_' . $lesson->id, 90);
        $timedTarget = (int) session('drab_timed_target_' . $lesson->id, 5);
        $timedCompleted = (int) session('drab_timed_completed_' . $lesson->id, 0);
        $timedRemaining = $timedMode && $timedStartedAt
            ? max(0, ($timedStartedAt + $timedDuration) - now()->timestamp)
            : 0;

        if ($timedMode && $timedRemaining <= 0) {
            session()->forget([
                'drab_timed_active_' . $lesson->id,
                'drab_timed_started_at_' . $lesson->id,
                'drab_timed_duration_' . $lesson->id,
                'drab_timed_target_' . $lesson->id,
                'drab_timed_completed_' . $lesson->id,
                'drab_timed_difficulty_' . $lesson->id,
            ]);

            $timedMode = false;
            $timedCompleted = 0;
            $timedRemaining = 0;
        }

        return view('drab.index', compact(
            'lesson',
            'task',
            'recentAttempts',
            'difficulty',
            'taskSource',
            'difficultyUnlocks',
            'easyCorrectCount',
            'mediumCorrectCount',
            'timedMode',
            'timedStartedAt',
            'timedDuration',
            'timedTarget',
            'timedCompleted',
            'timedRemaining',
            'adaptiveAverageAccuracy',
            'adaptiveSuggestedDifficulty',
            'adaptiveReason',
            'adaptiveWeakRuleType',
            'sessionMode',
            'sessionTarget',
            'sessionCompleted',
            'sessionCorrect',
            'sessionXp',
            'sessionLockedDifficulty'
        ));
    }

    public function submit(Request $request, Lesson $lesson)
    {
        $request->validate([
            'student_answer' => ['required', 'numeric'],
            'difficulty' => ['required'],
        ]);

        $task = session('drab_task_' . $lesson->id);
        $difficulty = session('drab_difficulty_' . $lesson->id, 'easy');
        $taskSource = session('drab_task_source_' . $lesson->id, 'local');

        if (!$task) {
            return redirect()
                ->route('drab.index', $lesson->id)
                ->with('error', 'No active benchmark task found. Please start again.');
        }

        $studentAnswer = (int) $request->student_answer;
        $correctAnswer = (int) $task['answer'];
        $isCorrect = ($studentAnswer === $correctAnswer);
        $accuracy = $isCorrect ? 100 : 0;
        $explanation = $task['explanation'] ?? $this->buildLocalExplanation($task);

        DrabAttempt::create([
            'user_id' => $request->user()->id,
            'lesson_id' => $lesson->id,
            'difficulty' => $difficulty,
            'total_tasks' => 1,
            'correct_tasks' => $isCorrect ? 1 : 0,
            'accuracy' => $accuracy,
            'results_json' => [
                'task' => $task,
                'difficulty' => $difficulty,
                'task_source' => $taskSource,
                'student_answer' => $studentAnswer,
                'expected_answer' => $correctAnswer,
                'is_correct' => $isCorrect,
                'explanation' => $explanation,
            ],
        ]);

        $recentAttempts = DrabAttempt::where('user_id', $request->user()->id)
            ->where('lesson_id', $lesson->id)
            ->latest()
            ->take(5)
            ->get();

        $adaptiveWeakRuleType = $this->getAdaptiveWeakRuleType($request->user()->id, $difficulty);

        $unlockMessage = null;

        $easyCorrect = DrabAttempt::where('user_id', $request->user()->id)->where('difficulty', 'easy')->where('correct_tasks', '>', 0)->count();
        $mediumCorrect = DrabAttempt::where('user_id', $request->user()->id)->where('difficulty', 'medium')->where('correct_tasks', '>', 0)->count();

        if ($easyCorrect === 3 && $difficulty === 'easy' && $isCorrect) {
            $unlockMessage = '🎉 Medium unlocked!';
        }

        if ($mediumCorrect === 3 && $difficulty === 'medium' && $isCorrect) {
            $unlockMessage = '🚀 Hard unlocked!';
        }

        $sessionMode = (bool) session('drab_session_active_' . $lesson->id, false);
        $sessionTarget = (int) session('drab_session_target_' . $lesson->id, 5);
        $sessionCompleted = (int) session('drab_session_completed_' . $lesson->id, 0);
        $sessionCorrect = (int) session('drab_session_correct_' . $lesson->id, 0);
        $sessionXp = (int) session('drab_session_xp_' . $lesson->id, 0);

        if ($sessionMode) {
            $xpEarned = 10;

            $difficultyBonus = match ($difficulty) {
                'easy' => 2,
                'medium' => 5,
                'hard' => 8,
                default => 0,
            };

            $accuracyBonus = $accuracy >= 100 ? 5 : ($accuracy >= 60 ? 3 : 0);

            $sessionCompleted++;
            if ($isCorrect) {
                $sessionCorrect++;
            }
            $sessionXp += ($xpEarned + $difficultyBonus + $accuracyBonus);

            session([
                'drab_session_completed_' . $lesson->id => $sessionCompleted,
                'drab_session_correct_' . $lesson->id => $sessionCorrect,
                'drab_session_xp_' . $lesson->id => $sessionXp,
            ]);
        }

        $timedMode = (bool) session('drab_timed_active_' . $lesson->id, false);
        $timedTarget = (int) session('drab_timed_target_' . $lesson->id, 5);
        $timedDuration = (int) session('drab_timed_duration_' . $lesson->id, 90);
        $timedStartedAt = (int) session('drab_timed_started_at_' . $lesson->id, 0);
        $timedCompleted = (int) session('drab_timed_completed_' . $lesson->id, 0);
        $timedCompleted = $timedMode ? ($timedCompleted + 1) : 0;

        if ($timedMode) {
            session(['drab_timed_completed_' . $lesson->id => $timedCompleted]);
        }

        $timedElapsed = ($timedMode && $timedStartedAt)
            ? min($timedDuration, max(0, now()->timestamp - $timedStartedAt))
            : 0;

        $timedRemaining = $timedMode
            ? max(0, $timedDuration - $timedElapsed)
            : 0;

        $timedFinished = $timedMode && ($timedRemaining <= 0 || $timedCompleted >= $timedTarget);

        if ($timedFinished) {
            session()->forget([
                'drab_timed_active_' . $lesson->id,
                'drab_timed_started_at_' . $lesson->id,
                'drab_timed_duration_' . $lesson->id,
                'drab_timed_target_' . $lesson->id,
                'drab_timed_completed_' . $lesson->id,
                'drab_timed_difficulty_' . $lesson->id,
            ]);
        }

        $sessionFinished = $sessionMode && ($sessionCompleted >= $sessionTarget);
        $sessionAccuracy = $sessionCompleted > 0 ? round(($sessionCorrect / $sessionCompleted) * 100, 2) : 0;

        $sessionRecommendedDifficulty = $difficulty;
        $sessionReportMessage = 'Good work. Keep practicing to build stronger reasoning consistency.';

        if ($sessionFinished) {
            if ($sessionAccuracy >= 80) {
                if ($difficulty === 'easy') {
                    $sessionRecommendedDifficulty = 'medium';
                    $sessionReportMessage = 'Excellent session. You are ready to step up to Medium.';
                } elseif ($difficulty === 'medium') {
                    $sessionRecommendedDifficulty = 'hard';
                    $sessionReportMessage = 'Strong performance. You are ready to challenge Hard mode.';
                } else {
                    $sessionRecommendedDifficulty = 'hard';
                    $sessionReportMessage = 'Excellent work. You are performing strongly at the highest level.';
                }
            } elseif ($sessionAccuracy < 50) {
                if ($difficulty === 'hard') {
                    $sessionRecommendedDifficulty = 'medium';
                    $sessionReportMessage = 'This session was tough. Drop to Medium and rebuild confidence.';
                } elseif ($difficulty === 'medium') {
                    $sessionRecommendedDifficulty = 'easy';
                    $sessionReportMessage = 'Rebuild on Easy to strengthen your foundation, then come back up.';
                } else {
                    $sessionRecommendedDifficulty = 'easy';
                    $sessionReportMessage = 'Stay on Easy and focus on understanding each rule clearly.';
                }
            } else {
                $sessionRecommendedDifficulty = $difficulty;
                $sessionReportMessage = 'Balanced session. Stay at this level and improve your consistency.';
            }
        }

        if ($sessionFinished) {
            session()->forget([
                'drab_session_active_' . $lesson->id,
                'drab_session_target_' . $lesson->id,
                'drab_session_completed_' . $lesson->id,
                'drab_session_correct_' . $lesson->id,
                'drab_session_xp_' . $lesson->id,
                'drab_session_difficulty_' . $lesson->id,
            ]);
        }

        return view('drab.result', [
            'lesson' => $lesson,
            'results' => [[
                'input' => $task['input'],
                'rule' => $task['rule'],
                'expected' => $correctAnswer,
                'output' => $studentAnswer,
                'correct' => $isCorrect,
            ]],
            'accuracy' => $accuracy,
            'totalTasks' => 1,
            'correctTasks' => $isCorrect ? 1 : 0,
            'difficulty' => $difficulty,
            'studentAnswer' => $studentAnswer,
            'expectedAnswer' => $correctAnswer,
            'taskDescription' => $task['rule'],
            'explanationLines' => is_array($explanation) ? $explanation : [$explanation],
            'recentAttempts' => $recentAttempts,
            'taskSource' => $taskSource,
            'unlockMessage' => $unlockMessage,
            'adaptiveWeakRuleType' => $adaptiveWeakRuleType,
            'timedMode' => $timedMode,
            'timedTarget' => $timedTarget,
            'timedCompleted' => $timedCompleted,
            'timedDuration' => $timedDuration,
            'timedElapsed' => $timedElapsed,
            'timedRemaining' => $timedRemaining,
            'timedFinished' => $timedFinished,
            'sessionMode' => $sessionMode,
            'sessionTarget' => $sessionTarget,
            'sessionCompleted' => $sessionCompleted,
            'sessionCorrect' => $sessionCorrect,
            'sessionXp' => $sessionXp,
            'sessionAccuracy' => $sessionAccuracy,
            'sessionFinished' => $sessionFinished,
            'sessionRecommendedDifficulty' => $sessionRecommendedDifficulty,
            'sessionReportMessage' => $sessionReportMessage,
        ]);
    }

    public function startAdaptiveSession(Request $request, Lesson $lesson)
    {
        $difficulty = $request->input('difficulty', 'easy');

        session([
            'drab_session_active_' . $lesson->id => true,
            'drab_session_target_' . $lesson->id => 5,
            'drab_session_completed_' . $lesson->id => 0,
            'drab_session_correct_' . $lesson->id => 0,
            'drab_session_xp_' . $lesson->id => 0,
            'drab_session_difficulty_' . $lesson->id => $difficulty,
        ]);

        return redirect()->route('drab.index', ['lesson' => $lesson->id, 'difficulty' => $difficulty]);
    }

    public function resetAdaptiveSession(Request $request, Lesson $lesson)
    {
        session()->forget([
            'drab_session_active_' . $lesson->id,
            'drab_session_target_' . $lesson->id,
            'drab_session_completed_' . $lesson->id,
            'drab_session_correct_' . $lesson->id,
            'drab_session_xp_' . $lesson->id,
            'drab_session_difficulty_' . $lesson->id,
        ]);

        return redirect()->route('drab.index', ['lesson' => $lesson->id]);
    }

    public function startTimed(Request $request, Lesson $lesson)
    {
        $difficulty = $request->input('difficulty', 'easy');

        session([
            'drab_timed_active_' . $lesson->id => true,
            'drab_timed_started_at_' . $lesson->id => now()->timestamp,
            'drab_timed_duration_' . $lesson->id => 90,
            'drab_timed_target_' . $lesson->id => 5,
            'drab_timed_completed_' . $lesson->id => 0,
            'drab_timed_difficulty_' . $lesson->id => $difficulty,
        ]);

        return redirect()->route('drab.index', ['lesson' => $lesson->id, 'difficulty' => $difficulty]);
    }

    public function resetTimed(Request $request, Lesson $lesson)
    {
        session()->forget([
            'drab_timed_active_' . $lesson->id,
            'drab_timed_started_at_' . $lesson->id,
            'drab_timed_duration_' . $lesson->id,
            'drab_timed_target_' . $lesson->id,
            'drab_timed_completed_' . $lesson->id,
            'drab_timed_difficulty_' . $lesson->id,
        ]);

        return redirect()->route('drab.index', ['lesson' => $lesson->id]);
    }

    private function getAdaptiveWeakRuleType(int $userId, string $difficulty): ?string
    {
        $eligibleTypes = match ($difficulty) {
            'easy' => ['parity_transform'],
            'medium' => ['threshold_transform', 'sequence_pattern', 'distractor_sequence', 'mixed_operations'],
            'hard' => ['two_step_parity', 'conditional_chain', 'rule_discovery', 'reverse_logic', 'distractor_sequence', 'position_based', 'mixed_operations'],
            default => ['parity_transform'],
        };

        $attempts = DrabAttempt::where('user_id', $userId)
            ->latest()
            ->take(20)
            ->get(['accuracy', 'results_json']);

        if ($attempts->isEmpty()) {
            return null;
        }

        $stats = [];

        foreach ($eligibleTypes as $type) {
            $stats[$type] = [
                'count' => 0,
                'total_accuracy' => 0,
            ];
        }

        foreach ($attempts as $attempt) {
            $ruleType = data_get($attempt->results_json, 'task.rule_type');

            if (!$ruleType || !in_array($ruleType, $eligibleTypes, true)) {
                continue;
            }

            $stats[$ruleType]['count']++;
            $stats[$ruleType]['total_accuracy'] += (float) $attempt->accuracy;
        }

        $weakestType = null;
        $weakestScore = null;

        foreach ($stats as $type => $data) {
            if ($data['count'] === 0) {
                return $type;
            }

            $avg = $data['total_accuracy'] / $data['count'];

            if ($weakestScore === null || $avg < $weakestScore) {
                $weakestScore = $avg;
                $weakestType = $type;
            }
        }

        return $weakestType;
    }

    private function buildVerifiedTask(array $generated): array
    {
        $input = $generated['input'];
        $ruleType = (string) ($generated['rule_type'] ?? '');
        $params = $generated['params'] ?? [];
        $rule = (string) ($generated['rule'] ?? $generated['rule_text'] ?? '');
        $family = (string) ($generated['family'] ?? 'general');
        $complexityLevel = (int) ($generated['complexity_level'] ?? 1);
        $signature = (string) ($generated['signature'] ?? $ruleType);

        [$answer, $explanation] = $this->solveRuleTask($input, $ruleType, $params);

        return [
            'input' => $input,
            'rule' => $rule,
            'family' => $family,
            'complexity_level' => $complexityLevel,
            'signature' => $signature,
            'rule_type' => $ruleType,
            'params' => $params,
            'answer' => $answer,
            'explanation' => $explanation,
        ];
    }

    private function solveRuleTask($input, string $ruleType, array $params): array
    {
        return match ($ruleType) {
            'parity_transform' => $this->solveParityTransform((int) $input, $params),
            'threshold_transform' => $this->solveThresholdTransform($input, $params),
            'two_step_parity' => $this->solveTwoStepParity((int) $input, $params),
            'sequence_pattern' => $this->solveSequencePattern($input, $params),
            'rule_discovery' => $this->solveRuleDiscovery($input, $params),
            'reverse_logic' => $this->solveReverseLogic($input, $params),
            'conditional_chain' => $this->solveConditionalChain((int) $input, $params),
            'distractor_sequence' => $this->solveDistractorSequence($input, $params),
            'position_based' => $this->solvePositionBased($input, $params),
            'mixed_operations' => $this->solveMixedOperations((int) $input, $params),
            default => throw new \RuntimeException("Unsupported rule type: {$ruleType}"),
        };
    }

    private function applyOperation(int|float $number, string $operation, int $value): int|float
    {
        return match ($operation) {
            'add' => $number + $value,
            'subtract' => $number - $value,
            'multiply' => $number * $value,
            'divide' => $value !== 0 ? $number / $value : $number,
            default => throw new \RuntimeException("Unsupported operation: {$operation}"),
        };
    }

    private function operationText(string $operation): string
    {
        return match ($operation) {
            'add' => 'add',
            'subtract' => 'subtract',
            'multiply' => 'multiply by',
            'divide' => 'divide by',
            default => $operation,
        };
    }

    private function symbolText(string $operation): string
    {
        return match ($operation) {
            'add' => '+',
            'subtract' => '-',
            'multiply' => '×',
            'divide' => '÷',
            default => '?',
        };
    }

    private function solveParityTransform(int $input, array $params): array
    {
        $isEven = $input % 2 === 0;

        if ($isEven) {
            $operation = (string) $params['even_operation'];
            $value = (int) $params['even_value'];
            $answer = (int) $this->applyOperation($input, $operation, $value);

            return [$answer, [
                "Step 1: The input is {$input}.",
                "Step 2: {$input} is even.",
                "Step 3: For even numbers, {$this->operationText($operation)} {$value}.",
                "Step 4: {$input} {$this->symbolText($operation)} {$value} = {$answer}.",
                "Final Answer: {$answer}.",
            ]];
        }

        $operation = (string) $params['odd_operation'];
        $value = (int) $params['odd_value'];
        $answer = (int) $this->applyOperation($input, $operation, $value);

        return [$answer, [
            "Step 1: The input is {$input}.",
            "Step 2: {$input} is odd.",
            "Step 3: For odd numbers, {$this->operationText($operation)} {$value}.",
            "Step 4: {$input} {$this->symbolText($operation)} {$value} = {$answer}.",
            "Final Answer: {$answer}.",
        ]];
    }

    private function solveThresholdTransform($input, array $params): array
    {
        $value = is_numeric($input) ? (int) $input : 0;
        $mode = (string) ($params['mode'] ?? 'greater');

        if ($mode === 'less') {
            $threshold = (int) $params['threshold'];
            $trueOp = (string) $params['true_operation'];
            $trueVal = (int) $params['true_value'];
            $falseOp = (string) $params['false_operation'];
            $falseVal = (int) $params['false_value'];

            if ($value < $threshold) {
                $answer = (int) $this->applyOperation($value, $trueOp, $trueVal);

                return [$answer, [
                    "Step 1: The input is {$value}.",
                    "Step 2: Check whether {$value} is less than {$threshold}.",
                    "Step 3: Yes.",
                    "Step 4: {$this->operationText($trueOp)} {$trueVal}.",
                    "Step 5: {$value} {$this->symbolText($trueOp)} {$trueVal} = {$answer}.",
                    "Final Answer: {$answer}.",
                ]];
            }

            $answer = (int) $this->applyOperation($value, $falseOp, $falseVal);

            return [$answer, [
                "Step 1: The input is {$value}.",
                "Step 2: Check whether {$value} is less than {$threshold}.",
                "Step 3: No.",
                "Step 4: Use the else rule: {$this->operationText($falseOp)} {$falseVal}.",
                "Step 5: {$value} {$this->symbolText($falseOp)} {$falseVal} = {$answer}.",
                "Final Answer: {$answer}.",
            ]];
        }

        if ($mode === 'between') {
            $min = (int) $params['min'];
            $max = (int) $params['max'];
            $insideOp = (string) $params['inside_operation'];
            $insideVal = (int) $params['inside_value'];
            $outsideOp = (string) $params['outside_operation'];
            $outsideVal = (int) $params['outside_value'];

            if ($value >= $min && $value <= $max) {
                $answer = (int) $this->applyOperation($value, $insideOp, $insideVal);

                return [$answer, [
                    "Step 1: The input is {$value}.",
                    "Step 2: Check whether {$value} is between {$min} and {$max}.",
                    "Step 3: Yes.",
                    "Step 4: {$this->operationText($insideOp)} {$insideVal}.",
                    "Step 5: {$value} {$this->symbolText($insideOp)} {$insideVal} = {$answer}.",
                    "Final Answer: {$answer}.",
                ]];
            }

            $answer = (int) $this->applyOperation($value, $outsideOp, $outsideVal);

            return [$answer, [
                "Step 1: The input is {$value}.",
                "Step 2: Check whether {$value} is between {$min} and {$max}.",
                "Step 3: No.",
                "Step 4: Use the outside rule: {$this->operationText($outsideOp)} {$outsideVal}.",
                "Step 5: {$value} {$this->symbolText($outsideOp)} {$outsideVal} = {$answer}.",
                "Final Answer: {$answer}.",
            ]];
        }

        $threshold = (int) $params['threshold'];

        if ($value > $threshold) {
            $operation = (string) $params['greater_operation'];
            $opValue = (int) $params['greater_value'];
            $answer = (int) $this->applyOperation($value, $operation, $opValue);

            return [$answer, [
                "Step 1: The input is {$value}.",
                "Step 2: Check whether {$value} is greater than {$threshold}.",
                "Step 3: Yes.",
                "Step 4: Apply the rule: {$this->operationText($operation)} {$opValue}.",
                "Step 5: {$value} {$this->symbolText($operation)} {$opValue} = {$answer}.",
                "Final Answer: {$answer}.",
            ]];
        }

        $operation = (string) $params['otherwise_operation'];
        $opValue = (int) $params['otherwise_value'];
        $answer = (int) $this->applyOperation($value, $operation, $opValue);

        return [$answer, [
            "Step 1: The input is {$value}.",
            "Step 2: Check whether {$value} is greater than {$threshold}.",
            "Step 3: No.",
            "Step 4: Apply the otherwise rule: {$this->operationText($operation)} {$opValue}.",
            "Step 5: {$value} {$this->symbolText($operation)} {$opValue} = {$answer}.",
            "Final Answer: {$answer}.",
        ]];
    }

    private function solveTwoStepParity(int $input, array $params): array
    {
        $isEven = $input % 2 === 0;

        if ($isEven) {
            $op1 = (string) $params['even_step_1_operation'];
            $val1 = (int) $params['even_step_1_value'];
            $op2 = (string) $params['even_step_2_operation'];
            $val2 = (int) $params['even_step_2_value'];

            $stepOne = (int) $this->applyOperation($input, $op1, $val1);
            $answer = (int) $this->applyOperation($stepOne, $op2, $val2);

            return [$answer, [
                "Step 1: The input is {$input}.",
                "Step 2: {$input} is even.",
                "Step 3: First, {$this->operationText($op1)} {$val1}.",
                "Step 4: {$input} {$this->symbolText($op1)} {$val1} = {$stepOne}.",
                "Step 5: Then, {$this->operationText($op2)} {$val2}.",
                "Step 6: {$stepOne} {$this->symbolText($op2)} {$val2} = {$answer}.",
                "Final Answer: {$answer}.",
            ]];
        }

        $op1 = (string) $params['odd_step_1_operation'];
        $val1 = (int) $params['odd_step_1_value'];
        $op2 = (string) $params['odd_step_2_operation'];
        $val2 = (int) $params['odd_step_2_value'];

        $stepOne = (int) $this->applyOperation($input, $op1, $val1);
        $answer = (int) $this->applyOperation($stepOne, $op2, $val2);

        return [$answer, [
            "Step 1: The input is {$input}.",
            "Step 2: {$input} is odd.",
            "Step 3: First, {$this->operationText($op1)} {$val1}.",
            "Step 4: {$input} {$this->symbolText($op1)} {$val1} = {$stepOne}.",
            "Step 5: Then, {$this->operationText($op2)} {$val2}.",
            "Step 6: {$stepOne} {$this->symbolText($op2)} {$val2} = {$answer}.",
            "Final Answer: {$answer}.",
        ]];
    }

    private function solveSequencePattern($input, array $params): array
    {
        $mode = (string) ($params['mode'] ?? 'add');

        if ($mode === 'multiply') {
            $start = (int) $params['start'];
            $factor = (int) $params['factor'];
            $answer = $start * ($factor ** 4);

            return [$answer, [
                "Step 1: Look at the sequence: {$input}.",
                "Step 2: Each term is multiplied by {$factor}.",
                "Step 3: Multiply the last visible term by {$factor}.",
                "Final Answer: {$answer}.",
            ]];
        }

        $start = (int) $params['start'];
        $step = (int) $params['step'];

        if ($mode === 'subtract') {
            $answer = $start - (4 * $step);

            return [$answer, [
                "Step 1: Look at the sequence: {$input}.",
                "Step 2: The pattern decreases by {$step} each time.",
                "Step 3: Subtract {$step} from the last visible number.",
                "Final Answer: {$answer}.",
            ]];
        }

        $answer = $start + (4 * $step);

        return [$answer, [
            "Step 1: Look at the sequence: {$input}.",
            "Step 2: The pattern increases by {$step} each time.",
            "Step 3: Add {$step} to the last visible number.",
            "Final Answer: {$answer}.",
        ]];
    }

    private function solveRuleDiscovery($input, array $params): array
    {
        $multiplier = (int) $params['multiplier'];
        $add = (int) $params['add'];
        $question = (int) $params['question'];
        $answer = ($question * $multiplier) + $add;

        return [$answer, [
            "Step 1: Study the examples: {$input}.",
            "Step 2: The rule is multiply by {$multiplier}, then add {$add}.",
            "Step 3: Apply that rule to {$question}.",
            "Step 4: {$question} × {$multiplier} + {$add} = {$answer}.",
            "Final Answer: {$answer}.",
        ]];
    }

    private function solveReverseLogic($input, array $params): array
    {
        $shownOutput = (int) $params['shown_output'];
        $multiplier = (int) $params['multiplier'];
        $add = (int) $params['add'];

        $answer = (int) (($shownOutput - $add) / $multiplier);

        return [$answer, [
            "Step 1: Work backwards from the shown output {$shownOutput}.",
            "Step 2: Reverse the +{$add} step first: {$shownOutput} - {$add} = " . ($shownOutput - $add) . ".",
            "Step 3: Reverse the ×{$multiplier} step next.",
            "Step 4: " . ($shownOutput - $add) . " ÷ {$multiplier} = {$answer}.",
            "Final Answer: {$answer}.",
        ]];
    }

    private function solveConditionalChain(int $input, array $params): array
    {
        $firstOp = (string) $params['first_operation'];
        $firstVal = (int) $params['first_value'];
        $threshold = (int) $params['threshold'];
        $highOp = (string) $params['high_operation'];
        $highVal = (int) $params['high_value'];
        $lowOp = (string) $params['low_operation'];
        $lowVal = (int) $params['low_value'];

        $stepOne = (int) $this->applyOperation($input, $firstOp, $firstVal);

        if ($stepOne > $threshold) {
            $answer = (int) $this->applyOperation($stepOne, $highOp, $highVal);

            return [$answer, [
                "Step 1: Start with {$input}.",
                "Step 2: First, {$this->operationText($firstOp)} {$firstVal}: {$input} {$this->symbolText($firstOp)} {$firstVal} = {$stepOne}.",
                "Step 3: Check whether {$stepOne} is greater than {$threshold}.",
                "Step 4: Yes, so {$this->operationText($highOp)} {$highVal}.",
                "Step 5: {$stepOne} {$this->symbolText($highOp)} {$highVal} = {$answer}.",
                "Final Answer: {$answer}.",
            ]];
        }

        $answer = (int) $this->applyOperation($stepOne, $lowOp, $lowVal);

        return [$answer, [
            "Step 1: Start with {$input}.",
            "Step 2: First, {$this->operationText($firstOp)} {$firstVal}: {$input} {$this->symbolText($firstOp)} {$firstVal} = {$stepOne}.",
            "Step 3: Check whether {$stepOne} is greater than {$threshold}.",
            "Step 4: No, so {$this->operationText($lowOp)} {$lowVal}.",
            "Step 5: {$stepOne} {$this->symbolText($lowOp)} {$lowVal} = {$answer}.",
            "Final Answer: {$answer}.",
        ]];
    }

    private function solveDistractorSequence($input, array $params): array
    {
        $start = (int) $params['start'];
        $stepA = (int) $params['step_a'];
        $stepB = (int) $params['step_b'];

        $n1 = $start;
        $n2 = $n1 + $stepA;
        $n3 = $n2 + $stepB;
        $n4 = $n3 + $stepA;
        $answer = $n4 + $stepB;

        return [$answer, [
            "Step 1: Look carefully at the sequence: {$input}.",
            "Step 2: The pattern alternates between +{$stepA} and +{$stepB}.",
            "Step 3: Continue the alternating pattern from the last visible number.",
            "Final Answer: {$answer}.",
        ]];
    }

    private function solvePositionBased($input, array $params): array
    {
        $numbers = array_map('intval', $params['numbers'] ?? []);
        $oddIncrement = (int) $params['odd_position_add'];
        $evenMultiplier = (int) $params['even_position_multiply'];

        $results = [];
        foreach ($numbers as $index => $number) {
            $position = $index + 1;
            if ($position % 2 === 1) {
                $results[] = $number + $oddIncrement;
            } else {
                $results[] = $number * $evenMultiplier;
            }
        }

        $answer = array_sum($results);

        return [$answer, [
            "Step 1: Use the position-based rule on {$input}.",
            "Step 2: Odd positions add {$oddIncrement}; even positions multiply by {$evenMultiplier}.",
            "Step 3: Apply the rule to each position and sum the results.",
            "Final Answer: {$answer}.",
        ]];
    }

    private function solveMixedOperations(int $input, array $params): array
    {
        $op1 = (string) $params['step_1_operation'];
        $val1 = (int) $params['step_1_value'];
        $op2 = (string) $params['step_2_operation'];
        $val2 = (int) $params['step_2_value'];

        $stepOne = $this->applyOperation($input, $op1, $val1);
        $answer = (int) $this->applyOperation((int) $stepOne, $op2, $val2);

        return [$answer, [
            "Step 1: Start with {$input}.",
            "Step 2: {$this->operationText($op1)} {$val1}.",
            "Step 3: Result: {$input} {$this->symbolText($op1)} {$val1} = {$stepOne}.",
            "Step 4: Then {$this->operationText($op2)} {$val2}.",
            "Step 5: {$stepOne} {$this->symbolText($op2)} {$val2} = {$answer}.",
            "Final Answer: {$answer}.",
        ]];
    }

    private function generateLocalTask(string $difficulty, ?string $preferredType = null, ?int $lessonId = null): array
    {
        $candidates = $this->buildLocalCandidates($difficulty);
        $selected = $this->selectBestCandidate($candidates, $preferredType, $lessonId);

        return $this->buildVerifiedTask($selected);
    }

    private function buildLocalCandidates(string $difficulty): array
    {
        $candidates = [];

        for ($i = 0; $i < 14; $i++) {
            if ($difficulty === 'easy') {
                $mode = rand(1, 3);
                $input = rand(2, 20);

                if ($mode === 1) {
                    $evenMultiply = rand(2, 4);
                    $oddAdd = rand(2, 5);

                    $candidates[] = [
                        'input' => $input,
                        'family' => 'parity',
                        'complexity_level' => 1,
                        'signature' => "parity_even_mul{$evenMultiply}_odd_add{$oddAdd}",
                        'rule' => "If the number is even, multiply by {$evenMultiply}. If the number is odd, add {$oddAdd}.",
                        'rule_type' => 'parity_transform',
                        'params' => [
                            'even_operation' => 'multiply',
                            'even_value' => $evenMultiply,
                            'odd_operation' => 'add',
                            'odd_value' => $oddAdd,
                        ],
                    ];
                } elseif ($mode === 2) {
                    $evenAdd = rand(2, 5);
                    $oddSubtract = rand(1, 4);

                    $candidates[] = [
                        'input' => $input,
                        'family' => 'parity',
                        'complexity_level' => 2,
                        'signature' => "parity_even_add{$evenAdd}_odd_sub{$oddSubtract}",
                        'rule' => "If the number is even, add {$evenAdd}. If the number is odd, subtract {$oddSubtract}.",
                        'rule_type' => 'parity_transform',
                        'params' => [
                            'even_operation' => 'add',
                            'even_value' => $evenAdd,
                            'odd_operation' => 'subtract',
                            'odd_value' => $oddSubtract,
                        ],
                    ];
                } else {
                    $threshold = rand(6, 12);
                    $lessAdd = rand(2, 5);
                    $elseMultiply = rand(2, 3);

                    $candidates[] = [
                        'input' => $input,
                        'family' => 'threshold',
                        'complexity_level' => 2,
                        'signature' => "threshold_lt{$threshold}_add{$lessAdd}_else_mul{$elseMultiply}",
                        'rule' => "If the number is less than {$threshold}, add {$lessAdd}. Otherwise, multiply by {$elseMultiply}.",
                        'rule_type' => 'threshold_transform',
                        'params' => [
                            'mode' => 'less',
                            'threshold' => $threshold,
                            'true_operation' => 'add',
                            'true_value' => $lessAdd,
                            'false_operation' => 'multiply',
                            'false_value' => $elseMultiply,
                        ],
                    ];
                }

                continue;
            }

            if ($difficulty === 'medium') {
                $mode = rand(1, 5);

                if ($mode === 1) {
                    $input = rand(2, 20);
                    $threshold = rand(8, 14);
                    $greaterValue = rand(2, 4);
                    $otherwiseValue = rand(3, 6);

                    $candidates[] = [
                        'input' => $input,
                        'family' => 'threshold',
                        'complexity_level' => 2,
                        'signature' => "threshold_gt{$threshold}_mul{$greaterValue}_else_add{$otherwiseValue}",
                        'rule' => "If the number is greater than {$threshold}, multiply by {$greaterValue}. Otherwise, add {$otherwiseValue}.",
                        'rule_type' => 'threshold_transform',
                        'params' => [
                            'mode' => 'greater',
                            'threshold' => $threshold,
                            'greater_operation' => 'multiply',
                            'greater_value' => $greaterValue,
                            'otherwise_operation' => 'add',
                            'otherwise_value' => $otherwiseValue,
                        ],
                    ];
                } elseif ($mode === 2) {
                    $start = rand(1, 12);
                    $step = rand(2, 6);

                    $candidates[] = [
                        'input' => implode(', ', [
                            $start,
                            $start + $step,
                            $start + ($step * 2),
                            $start + ($step * 3),
                        ]) . ', ?',
                        'family' => 'sequence',
                        'complexity_level' => 2,
                        'signature' => "sequence_add{$step}",
                        'rule' => 'Find the pattern in the sequence and determine the next number.',
                        'rule_type' => 'sequence_pattern',
                        'params' => [
                            'mode' => 'add',
                            'start' => $start,
                            'step' => $step,
                        ],
                    ];
                } elseif ($mode === 3) {
                    $start = rand(20, 40);
                    $step = rand(2, 5);

                    $candidates[] = [
                        'input' => implode(', ', [
                            $start,
                            $start - $step,
                            $start - ($step * 2),
                            $start - ($step * 3),
                        ]) . ', ?',
                        'family' => 'sequence',
                        'complexity_level' => 3,
                        'signature' => "sequence_sub{$step}",
                        'rule' => 'Find the decreasing pattern in the sequence and determine the next number.',
                        'rule_type' => 'sequence_pattern',
                        'params' => [
                            'mode' => 'subtract',
                            'start' => $start,
                            'step' => $step,
                        ],
                    ];
                } elseif ($mode === 4) {
                    $start = rand(1, 10);
                    $stepA = rand(2, 4);
                    $stepB = rand(3, 6);

                    $n1 = $start;
                    $n2 = $n1 + $stepA;
                    $n3 = $n2 + $stepB;
                    $n4 = $n3 + $stepA;

                    $candidates[] = [
                        'input' => "{$n1}, {$n2}, {$n3}, {$n4}, ?",
                        'family' => 'distractor',
                        'complexity_level' => 3,
                        'signature' => "distractor_alt{$stepA}_{$stepB}",
                        'rule' => 'Find the hidden alternating pattern in the sequence and determine the next number.',
                        'rule_type' => 'distractor_sequence',
                        'params' => [
                            'start' => $start,
                            'step_a' => $stepA,
                            'step_b' => $stepB,
                        ],
                    ];
                } else {
                    $input = rand(4, 20);
                    $op1 = rand(0, 1) ? 'add' : 'multiply';
                    $val1 = $op1 === 'multiply' ? rand(2, 3) : rand(2, 5);
                    $op2 = rand(0, 1) ? 'subtract' : 'add';
                    $val2 = rand(1, 4);

                    $candidates[] = [
                        'input' => $input,
                        'family' => 'mixed',
                        'complexity_level' => 3,
                        'signature' => "mixed_{$op1}{$val1}_{$op2}{$val2}",
                        'rule' => "First {$this->operationLabel($op1, $val1)}, then {$this->operationLabel($op2, $val2)}.",
                        'rule_type' => 'mixed_operations',
                        'params' => [
                            'step_1_operation' => $op1,
                            'step_1_value' => $val1,
                            'step_2_operation' => $op2,
                            'step_2_value' => $val2,
                        ],
                    ];
                }

                continue;
            }

            $mode = rand(1, 7);

            if ($mode === 1) {
                $input = rand(2, 20);
                $evenMultiply = rand(2, 4);
                $evenSubtract = rand(1, 3);
                $oddAdd = rand(2, 5);
                $oddMultiply = rand(2, 3);

                $candidates[] = [
                    'input' => $input,
                    'family' => 'multi_step',
                    'complexity_level' => 3,
                    'signature' => "twostep_even_mul{$evenMultiply}_sub{$evenSubtract}_odd_add{$oddAdd}_mul{$oddMultiply}",
                    'rule' => "If the number is even, multiply by {$evenMultiply} and then subtract {$evenSubtract}. If the number is odd, add {$oddAdd} and then multiply by {$oddMultiply}.",
                    'rule_type' => 'two_step_parity',
                    'params' => [
                        'even_step_1_operation' => 'multiply',
                        'even_step_1_value' => $evenMultiply,
                        'even_step_2_operation' => 'subtract',
                        'even_step_2_value' => $evenSubtract,
                        'odd_step_1_operation' => 'add',
                        'odd_step_1_value' => $oddAdd,
                        'odd_step_2_operation' => 'multiply',
                        'odd_step_2_value' => $oddMultiply,
                    ],
                ];
            } elseif ($mode === 2) {
                $base = rand(2, 6);
                $multiplier = rand(2, 3);
                $add = rand(1, 5);
                $question = $base + rand(3, 5);

                $candidates[] = [
                    'input' => "{$base} → " . (($base * $multiplier) + $add) . ", " . ($base + 2) . " → " . ((($base + 2) * $multiplier) + $add) . ", {$question} → ?",
                    'family' => 'discovery',
                    'complexity_level' => 3,
                    'signature' => "discovery_mul{$multiplier}_add{$add}",
                    'rule' => 'Identify the rule from the examples and apply it.',
                    'rule_type' => 'rule_discovery',
                    'params' => [
                        'multiplier' => $multiplier,
                        'add' => $add,
                        'question' => $question,
                    ],
                ];
            } elseif ($mode === 3) {
                $input = rand(3, 10);
                $firstOperation = rand(0, 1) ? 'multiply' : 'add';
                $firstValue = $firstOperation === 'multiply' ? rand(2, 3) : rand(3, 6);
                $threshold = rand(12, 20);
                $highOperation = rand(0, 1) ? 'subtract' : 'add';
                $highValue = rand(2, 5);
                $lowOperation = rand(0, 1) ? 'add' : 'multiply';
                $lowValue = $lowOperation === 'multiply' ? rand(2, 3) : rand(2, 5);

                $candidates[] = [
                    'input' => $input,
                    'family' => 'chain',
                    'complexity_level' => 4,
                    'signature' => "chain_{$firstOperation}{$firstValue}_gt{$threshold}_{$highOperation}{$highValue}_else_{$lowOperation}{$lowValue}",
                    'rule' => "First {$this->operationLabel($firstOperation, $firstValue)}. Then if the result is greater than {$threshold}, {$this->operationLabel($highOperation, $highValue)}. Otherwise, {$this->operationLabel($lowOperation, $lowValue)}.",
                    'rule_type' => 'conditional_chain',
                    'params' => [
                        'first_operation' => $firstOperation,
                        'first_value' => $firstValue,
                        'threshold' => $threshold,
                        'high_operation' => $highOperation,
                        'high_value' => $highValue,
                        'low_operation' => $lowOperation,
                        'low_value' => $lowValue,
                    ],
                ];
            } elseif ($mode === 4) {
                $inputAnswer = rand(3, 12);
                $multiplier = rand(2, 3);
                $add = rand(2, 6);
                $shownOutput = ($inputAnswer * $multiplier) + $add;

                $candidates[] = [
                    'input' => $shownOutput,
                    'family' => 'reverse',
                    'complexity_level' => 4,
                    'signature' => "reverse_mul{$multiplier}_add{$add}",
                    'rule' => "A number was multiplied by {$multiplier} and then added by {$add} to give {$shownOutput}. What was the original number?",
                    'rule_type' => 'reverse_logic',
                    'params' => [
                        'shown_output' => $shownOutput,
                        'multiplier' => $multiplier,
                        'add' => $add,
                    ],
                ];
            } elseif ($mode === 5) {
                $numbers = [rand(1, 5), rand(2, 6), rand(3, 7), rand(4, 8)];
                $oddAdd = rand(2, 4);
                $evenMultiply = rand(2, 3);
                $input = implode(', ', $numbers);

                $candidates[] = [
                    'input' => $input,
                    'family' => 'position',
                    'complexity_level' => 4,
                    'signature' => "position_oddadd{$oddAdd}_evenmul{$evenMultiply}",
                    'rule' => "For numbers in odd positions, add {$oddAdd}. For numbers in even positions, multiply by {$evenMultiply}. Then sum the results.",
                    'rule_type' => 'position_based',
                    'params' => [
                        'numbers' => $numbers,
                        'odd_position_add' => $oddAdd,
                        'even_position_multiply' => $evenMultiply,
                    ],
                ];
            } elseif ($mode === 6) {
                $start = rand(2, 4);
                $factor = rand(2, 3);

                $candidates[] = [
                    'input' => implode(', ', [
                        $start,
                        $start * $factor,
                        $start * ($factor ** 2),
                        $start * ($factor ** 3),
                    ]) . ', ?',
                    'family' => 'sequence',
                    'complexity_level' => 4,
                    'signature' => "sequence_mul{$factor}",
                    'rule' => 'Find the multiplying pattern in the sequence and determine the next number.',
                    'rule_type' => 'sequence_pattern',
                    'params' => [
                        'mode' => 'multiply',
                        'start' => $start,
                        'factor' => $factor,
                    ],
                ];
            } else {
                $input = rand(6, 18);
                $min = rand(5, 8);
                $max = rand(12, 16);
                $insideOp = rand(0, 1) ? 'multiply' : 'add';
                $insideVal = $insideOp === 'multiply' ? rand(2, 3) : rand(2, 5);
                $outsideOp = rand(0, 1) ? 'subtract' : 'add';
                $outsideVal = rand(2, 4);

                $candidates[] = [
                    'input' => $input,
                    'family' => 'threshold',
                    'complexity_level' => 4,
                    'signature' => "threshold_between{$min}_{$max}_inside{$insideOp}{$insideVal}_outside{$outsideOp}{$outsideVal}",
                    'rule' => "If the number is between {$min} and {$max}, {$this->operationLabel($insideOp, $insideVal)}. Otherwise, {$this->operationLabel($outsideOp, $outsideVal)}.",
                    'rule_type' => 'threshold_transform',
                    'params' => [
                        'mode' => 'between',
                        'min' => $min,
                        'max' => $max,
                        'inside_operation' => $insideOp,
                        'inside_value' => $insideVal,
                        'outside_operation' => $outsideOp,
                        'outside_value' => $outsideVal,
                    ],
                ];
            }
        }

        return $candidates;
    }

    private function selectBestCandidate(array $candidates, ?string $preferredType, ?int $lessonId): array
    {
        $recentSignatures = collect(session('drab_recent_signatures_' . ($lessonId ?? 0), []));
        $pool = collect($candidates);

        if ($preferredType) {
            $preferredPool = $pool->where('rule_type', $preferredType)->values();
            if ($preferredPool->isNotEmpty()) {
                $pool = $preferredPool;
            }
        }

        $nonRepeated = $pool->reject(fn ($candidate) => $recentSignatures->contains($candidate['signature']))->values();

        $selected = ($nonRepeated->isNotEmpty() ? $nonRepeated : $pool)->random();

        if ($lessonId !== null) {
            $updated = $recentSignatures->push($selected['signature'])->take(-10)->values()->all();
            session(['drab_recent_signatures_' . $lessonId => $updated]);
        }

        return $selected;
    }

    private function operationLabel(string $operation, int $value): string
    {
        return match ($operation) {
            'add' => "add {$value}",
            'subtract' => "subtract {$value}",
            'multiply' => "multiply by {$value}",
            'divide' => "divide by {$value}",
            default => "{$operation} {$value}",
        };
    }

    private function buildLocalExplanation(array $task): array
    {
        $input = $task['input'] ?? 0;
        $ruleType = (string) ($task['rule_type'] ?? '');
        $params = $task['params'] ?? [];

        try {
            [, $explanation] = $this->solveRuleTask($input, $ruleType, $params);
            return is_array($explanation) ? $explanation : [$explanation];
        } catch (\Throwable $e) {
            return [
                "Step 1: Review the rule carefully.",
                "Step 2: Apply the correct pattern or transformation.",
                "Final Answer: " . ((int) ($task['answer'] ?? 0)) . ".",
            ];
        }
    }
}
