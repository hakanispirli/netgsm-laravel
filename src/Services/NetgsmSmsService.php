<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Services;

use DateTimeInterface;
use HakanIspirli\NetgsmLaravel\Contracts\NetgsmClientInterface;
use HakanIspirli\NetgsmLaravel\Contracts\OtpSenderInterface;
use HakanIspirli\NetgsmLaravel\Contracts\SmsSenderInterface;
use HakanIspirli\NetgsmLaravel\Data\NetgsmSmsResponse;
use HakanIspirli\NetgsmLaravel\Data\NormalSmsRequest;
use HakanIspirli\NetgsmLaravel\Data\OtpSmsRequest;
use HakanIspirli\NetgsmLaravel\Data\ScheduledSmsRequest;
use HakanIspirli\NetgsmLaravel\Data\SmsMessage;
use HakanIspirli\NetgsmLaravel\Enums\Encoding;
use HakanIspirli\NetgsmLaravel\Enums\IysFilter;
use HakanIspirli\NetgsmLaravel\Enums\NetgsmEndpoint;
use HakanIspirli\NetgsmLaravel\Jobs\SendNetgsmOtpSmsJob;
use HakanIspirli\NetgsmLaravel\Jobs\SendNetgsmSmsJob;
use HakanIspirli\NetgsmLaravel\Support\NetgsmPayloadBuilder;
use HakanIspirli\NetgsmLaravel\Support\SensitiveDataMasker;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Bus\Dispatcher;
use Psr\Log\LoggerInterface;
use Throwable;

final class NetgsmSmsService implements SmsSenderInterface, OtpSenderInterface
{
    public function __construct(
        private readonly NetgsmClientInterface $client,
        private readonly NetgsmPayloadBuilder $payloadBuilder,
        private readonly Repository $config,
        private readonly Dispatcher $bus,
        private readonly LoggerInterface $logger,
        private readonly SensitiveDataMasker $masker,
    ) {}

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function send(string $to, string $message, ?string $header = null, array $options = []): array
    {
        return $this->sendMany([
            ['to' => $to, 'message' => $message],
        ], $header, $options);
    }

    /**
     * @param array<int, array{to?: string, no?: string, message?: string, msg?: string}|SmsMessage> $messages
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function sendMany(array $messages, ?string $header = null, array $options = []): array
    {
        return $this->safely('Netgsm normal SMS send failed', function () use ($messages, $header, $options): NetgsmSmsResponse {
            return $this->sendRequest($this->makeNormalRequest($messages, $header, $options));
        }, [
            'message_count' => count($messages),
        ]);
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function schedule(
        string $to,
        string $message,
        DateTimeInterface $startsAt,
        ?DateTimeInterface $stopsAt = null,
        ?string $header = null,
        array $options = [],
    ): array {
        return $this->scheduleMany([
            ['to' => $to, 'message' => $message],
        ], $startsAt, $stopsAt, $header, $options);
    }

    /**
     * @param array<int, array{to?: string, no?: string, message?: string, msg?: string}|SmsMessage> $messages
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function scheduleMany(
        array $messages,
        DateTimeInterface $startsAt,
        ?DateTimeInterface $stopsAt = null,
        ?string $header = null,
        array $options = [],
    ): array {
        return $this->safely('Netgsm scheduled SMS send failed', function () use (
            $messages,
            $startsAt,
            $stopsAt,
            $header,
            $options,
        ): NetgsmSmsResponse {
            return $this->scheduleRequest(new ScheduledSmsRequest(
                sms: $this->makeNormalRequest($messages, $header, $options),
                startsAt: $startsAt,
                stopsAt: $stopsAt,
            ));
        }, [
            'message_count' => count($messages),
            'starts_at' => $startsAt->format(DATE_ATOM),
            'stops_at' => $stopsAt?->format(DATE_ATOM),
        ]);
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function otp(string $to, string $message, ?string $header = null, array $options = []): array
    {
        return $this->safely('Netgsm OTP SMS send failed', function () use ($to, $message, $header, $options): NetgsmSmsResponse {
            return $this->sendOtpRequest($this->makeOtpRequest($to, $message, $header, $options));
        }, [
            'recipient' => $this->masker->maskPayload(['no' => $to])['no'],
            'otp' => true,
        ]);
    }

    /**
     * @param array<int, array{to?: string, no?: string, message?: string, msg?: string}|SmsMessage> $messages
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function queueSend(array $messages, ?string $header = null, array $options = []): array
    {
        $job = new SendNetgsmSmsJob($messages, $header, $options);
        $this->bus->dispatch($job);

        return $this->queuedResult();
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function queueOtp(string $to, string $message, ?string $header = null, array $options = []): array
    {
        $job = new SendNetgsmOtpSmsJob($to, $message, $header, $options);
        $this->bus->dispatch($job);

        return $this->queuedResult();
    }

    public function sendRequest(NormalSmsRequest $request): NetgsmSmsResponse
    {
        return $this->client->post(
            endpoint: NetgsmEndpoint::SmsSend,
            payload: $this->payloadBuilder->normal($request),
        );
    }

    public function scheduleRequest(ScheduledSmsRequest $request): NetgsmSmsResponse
    {
        return $this->client->post(
            endpoint: NetgsmEndpoint::SmsSend,
            payload: $this->payloadBuilder->scheduled($request),
        );
    }

    public function sendOtpRequest(OtpSmsRequest $request): NetgsmSmsResponse
    {
        return $this->client->post(
            endpoint: NetgsmEndpoint::Otp,
            payload: $this->payloadBuilder->otp($request),
        );
    }

    /**
     * @param array<int, array{to?: string, no?: string, message?: string, msg?: string}|SmsMessage> $messages
     * @param array<string, mixed> $options
     */
    private function makeNormalRequest(array $messages, ?string $header, array $options): NormalSmsRequest
    {
        return new NormalSmsRequest(
            messages: $this->normalizeMessages($messages),
            msgheader: $this->resolveHeader($header),
            encoding: Encoding::fromString((string) ($options['encoding'] ?? $this->config->get('netgsm.default_encoding', 'TR'))),
            iysFilter: IysFilter::fromString((string) ($options['iysfilter'] ?? $this->config->get('netgsm.default_iysfilter', '0'))),
            appname: $this->optionalString($options['appname'] ?? $this->config->get('netgsm.appname')),
            partnercode: $this->optionalString($options['partnercode'] ?? $this->config->get('netgsm.partnercode')),
            referenceId: $this->optionalString($options['referansID'] ?? $options['reference_id'] ?? null),
        );
    }

    /**
     * @param array<string, mixed> $options
     */
    private function makeOtpRequest(string $to, string $message, ?string $header, array $options): OtpSmsRequest
    {
        return new OtpSmsRequest(
            to: $to,
            message: $message,
            msgheader: $this->resolveHeader($header),
            appname: $this->optionalString($options['appname'] ?? $this->config->get('netgsm.appname')),
        );
    }

    /**
     * @param array<int, array{to?: string, no?: string, message?: string, msg?: string}|SmsMessage> $messages
     * @return list<SmsMessage>
     */
    private function normalizeMessages(array $messages): array
    {
        $normalized = [];

        foreach ($messages as $message) {
            if ($message instanceof SmsMessage) {
                $normalized[] = $message;

                continue;
            }

            $normalized[] = new SmsMessage(
                to: (string) ($message['to'] ?? $message['no'] ?? ''),
                message: (string) ($message['message'] ?? $message['msg'] ?? ''),
            );
        }

        return $normalized;
    }

    private function resolveHeader(?string $header): string
    {
        $header = $this->optionalString($header) ?? $this->optionalString($this->config->get('netgsm.msgheader'));

        if ($header === null) {
            throw new \InvalidArgumentException('Missing Netgsm message header. Set NETGSM_MSGHEADER or pass a header.');
        }

        return $header;
    }

    private function optionalString(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * @param callable(): NetgsmSmsResponse $callback
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    private function safely(string $logMessage, callable $callback, array $context = []): array
    {
        try {
            return $callback()->toResultArray($this->safeErrorMessage());
        } catch (Throwable $e) {
            $this->logger->error($logMessage, array_merge($this->requestContext(), $context, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]));

            return [
                'success' => false,
                'message' => $this->safeErrorMessage(),
                'data' => [
                    'code' => null,
                    'job_id' => null,
                    'description' => null,
                    'warning' => null,
                    'http_status' => null,
                ],
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function queuedResult(): array
    {
        return [
            'success' => true,
            'data' => [
                'queued' => true,
                'queue' => (string) $this->config->get('netgsm.queue', 'netgsm-sms'),
            ],
        ];
    }

    private function safeErrorMessage(): string
    {
        return (string) $this->config->get(
            'netgsm.safe_error_message',
            'SMS gonderimi tamamlanamadi. Lutfen daha sonra tekrar deneyin.',
        );
    }

    /**
     * @return array{url: ?string, user_id: mixed, ip: ?string}
     */
    private function requestContext(): array
    {
        $url = null;
        $ip = null;
        $userId = null;

        try {
            if (function_exists('request')) {
                $request = request();
                $url = method_exists($request, 'fullUrl') ? $request->fullUrl() : null;
                $ip = method_exists($request, 'ip') ? $request->ip() : null;
            }

            if (function_exists('auth')) {
                $userId = auth()->id();
            }
        } catch (Throwable) {
            // Request/auth context is optional because queued jobs and CLI commands may not have it.
        }

        return [
            'url' => $url,
            'user_id' => $userId,
            'ip' => $ip,
        ];
    }
}
