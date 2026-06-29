<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Enums;

use InvalidArgumentException;

enum Encoding: string
{
    case Utf8 = 'UTF-8';
    case Turkish = 'TR';
    case Unicode = 'UNICODE';

    public static function fromString(string $value): self
    {
        return self::tryFrom(strtoupper($value))
            ?? throw new InvalidArgumentException("Invalid Netgsm encoding [{$value}].");
    }
}
