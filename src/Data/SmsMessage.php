<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Data;

use InvalidArgumentException;

final readonly class SmsMessage
{
    public string $to;

    public string $message;

    public function __construct(string $to, string $message)
    {
        $to = trim($to);
        $message = trim($message);

        if ($to === '') {
            throw new InvalidArgumentException('SMS recipient number cannot be empty.');
        }

        if ($message === '') {
            throw new InvalidArgumentException('SMS message cannot be empty.');
        }

        $this->to = $to;
        $this->message = $message;
    }

    /**
     * @return array{msg: string, no: string}
     */
    public function toNetgsmArray(): array
    {
        return [
            'msg' => $this->message,
            'no' => $this->to,
        ];
    }
}
