<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Facades;

use DateTimeInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array<string, mixed> send(string $to, string $message, ?string $header = null, array $options = [])
 * @method static array<string, mixed> sendMany(array $messages, ?string $header = null, array $options = [])
 * @method static array<string, mixed> schedule(string $to, string $message, DateTimeInterface $startsAt, ?DateTimeInterface $stopsAt = null, ?string $header = null, array $options = [])
 * @method static array<string, mixed> scheduleMany(array $messages, DateTimeInterface $startsAt, ?DateTimeInterface $stopsAt = null, ?string $header = null, array $options = [])
 * @method static array<string, mixed> otp(string $to, string $message, ?string $header = null, array $options = [])
 * @method static array<string, mixed> queueSend(array $messages, ?string $header = null, array $options = [])
 * @method static array<string, mixed> queueOtp(string $to, string $message, ?string $header = null, array $options = [])
 */
final class NetgsmSms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'netgsm-sms';
    }
}
