<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Data;

use InvalidArgumentException;

final readonly class OtpSmsRequest
{
    public string $to;

    public string $message;

    public string $msgheader;

    public ?string $appname;

    public function __construct(string $to, string $message, string $msgheader, ?string $appname = null)
    {
        $to = trim($to);
        $message = trim($message);
        $msgheader = trim($msgheader);

        if ($to === '') {
            throw new InvalidArgumentException('OTP recipient number cannot be empty.');
        }

        if ($message === '') {
            throw new InvalidArgumentException('OTP message cannot be empty.');
        }

        if ($msgheader === '') {
            throw new InvalidArgumentException('Netgsm OTP message header cannot be empty.');
        }

        if (preg_match('/[çğıöşüÇĞİÖŞÜ]/u', $message) === 1) {
            throw new InvalidArgumentException('Netgsm OTP messages cannot contain Turkish characters.');
        }

        $this->to = $to;
        $this->message = $message;
        $this->msgheader = $msgheader;
        $this->appname = $appname !== null && trim($appname) !== '' ? trim($appname) : null;
    }
}
