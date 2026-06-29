<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Enums;

use InvalidArgumentException;

enum IysFilter: string
{
    case Informational = '0';
    case IndividualCommercial = '11';
    case MerchantCommercial = '12';

    public static function fromString(string $value): self
    {
        return self::tryFrom($value)
            ?? throw new InvalidArgumentException("Invalid Netgsm IYS filter [{$value}].");
    }
}
