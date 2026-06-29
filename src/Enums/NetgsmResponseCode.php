<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Enums;

enum NetgsmResponseCode: string
{
    case Accepted = '00';
    case StartDateAdjusted = '01';
    case StopDateAdjusted = '02';

    public static function isSuccessful(?string $code): bool
    {
        return in_array($code, [
            self::Accepted->value,
            self::StartDateAdjusted->value,
            self::StopDateAdjusted->value,
        ], true);
    }

    public static function warningFor(?string $code): ?string
    {
        return match ($code) {
            self::StartDateAdjusted->value => 'Netgsm baslangic tarihini sistem tarihine gore duzelterek isleme aldi.',
            self::StopDateAdjusted->value => 'Netgsm bitis tarihini sistem kurallarina gore duzelterek isleme aldi.',
            default => null,
        };
    }
}
