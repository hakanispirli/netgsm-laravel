<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Jobs;

use HakanIspirli\NetgsmLaravel\Services\NetgsmSmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

final class SendNetgsmOtpSmsJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        private readonly string $to,
        private readonly string $message,
        private readonly ?string $header = null,
        private readonly array $options = [],
    ) {
        $this->onQueue((string) config('netgsm.queue', 'netgsm-sms'));

        $connection = config('netgsm.queue_connection');
        if (is_string($connection) && $connection !== '') {
            $this->onConnection($connection);
        }
    }

    public function handle(NetgsmSmsService $service): void
    {
        $result = $service->otp($this->to, $this->message, $this->header, $this->options);

        if (($result['success'] ?? false) !== true) {
            $code = is_array($result['data'] ?? null) ? ($result['data']['code'] ?? 'unknown') : 'unknown';

            throw new RuntimeException("Netgsm OTP SMS job failed with code [{$code}].");
        }
    }
}
