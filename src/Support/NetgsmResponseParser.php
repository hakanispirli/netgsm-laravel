<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Support;

use HakanIspirli\NetgsmLaravel\Data\NetgsmSmsResponse;
use HakanIspirli\NetgsmLaravel\Enums\NetgsmResponseCode;

final class NetgsmResponseParser
{
    /**
     * @param array<string, mixed> $data
     */
    public function parse(array $data, int $httpStatus): NetgsmSmsResponse
    {
        $code = $this->nullableString($data['code'] ?? null);
        $jobId = $this->nullableString($data['jobid'] ?? $data['job_id'] ?? null);
        $description = $this->nullableString($data['description'] ?? null);
        $success = $httpStatus >= 200
            && $httpStatus < 300
            && NetgsmResponseCode::isSuccessful($code);

        return new NetgsmSmsResponse(
            success: $success,
            code: $code,
            jobId: $jobId,
            description: $description,
            warning: NetgsmResponseCode::warningFor($code),
            httpStatus: $httpStatus,
            raw: $data,
        );
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
