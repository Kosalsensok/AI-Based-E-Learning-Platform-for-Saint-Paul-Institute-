<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\AiRecommendationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAiRecommendation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @param int|null $userId Specific user ID to process, or null to process all active students
     * @param array $payload Additional payload/parameters for processing
     */
    public function __construct(
        public ?int $userId = null,
        public array $payload = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AiRecommendationService $service): void
    {
        Log::info("ProcessAiRecommendation job started", [
            'userId' => $this->userId,
            'payload' => $this->payload,
        ]);

        if ($this->userId) {
            $user = User::find($this->userId);
            if ($user) {
                $service->analyzeAndRecommend($user);
            }
        } else {
            // Process students in chunks to optimize memory and CPU usage
            User::where('role', 'student')
                ->where('status', 'active')
                ->chunk(100, function ($students) use ($service) {
                    /** @var User $student */
                    foreach ($students as $student) {
                        try {
                            $service->analyzeAndRecommend($student);
                        } catch (\Throwable $e) {
                            Log::warning("AI analysis failed for student ID {$student->id}: " . $e->getMessage());
                        }
                    }
                });
        }

        Log::info("ProcessAiRecommendation job completed successfully", [
            'userId' => $this->userId
        ]);
    }
}
