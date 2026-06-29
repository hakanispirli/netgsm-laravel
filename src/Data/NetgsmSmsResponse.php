<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Data;

final readonly class NetgsmSmsResponse
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public bool $success,
        public ?string $code,
        public ?string $jobId,
        public ?string $description,
        public ?string $warning,
        public int $httpStatus,
        public array $raw = [],
    ) {}

    /**
     * @return array{success: bool, data: array<string, mixed>}|array{success: bool, message: string, data: array<string, mixed>}
     */
    public function toResultArray(string $safeErrorMessage): array
    {
        $data = [
            'code' => $this->code,
            'job_id' => $this->jobId,
            'description' => $this->description,
            'warning' => $this->warning,
            'http_status' => $this->httpStatus,
        ];

        if ($this->success) {
            return [
                'success' => true,
                'data' => $data,
            ];
        }

        return [
            'success' => false,
            'message' => $safeErrorMessage,
            'data' => $data,
        ];
    }
}
