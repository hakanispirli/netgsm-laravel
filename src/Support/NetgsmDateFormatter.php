<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Support;

use DateTimeInterface;

final class NetgsmDateFormatter
{
    public function format(DateTimeInterface $date): string
    {
        return $date->format('dmYHi');
    }
}
