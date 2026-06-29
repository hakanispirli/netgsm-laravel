<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Support;

final class SensitiveDataMasker
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function maskPayload(array $payload, bool $maskMessageText = false): array
    {
        foreach (['password', 'Authorization', 'authorization'] as $key) {
            if (array_key_exists($key, $payload)) {
                $payload[$key] = '[masked]';
            }
        }

        if (isset($payload['no']) && is_scalar($payload['no'])) {
            $payload['no'] = $this->maskPhone((string) $payload['no']);
        }

        if ($maskMessageText && isset($payload['msg'])) {
            $payload['msg'] = '[masked]';
        }

        if (isset($payload['messages']) && is_array($payload['messages'])) {
            foreach ($payload['messages'] as $index => $message) {
                if (! is_array($message)) {
                    continue;
                }

                if (isset($message['no']) && is_scalar($message['no'])) {
                    $payload['messages'][$index]['no'] = $this->maskPhone((string) $message['no']);
                }

                if ($maskMessageText && isset($message['msg'])) {
                    $payload['messages'][$index]['msg'] = '[masked]';
                }
            }
        }

        return $payload;
    }

    private function maskPhone(string $phone): string
    {
        $length = strlen($phone);

        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return str_repeat('*', max(0, $length - 4)).substr($phone, -4);
    }
}
