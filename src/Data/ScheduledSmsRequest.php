<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Data;

use DateTimeInterface;
use InvalidArgumentException;

final readonly class ScheduledSmsRequest
{
    public function __construct(
        public NormalSmsRequest $sms,
        public DateTimeInterface $startsAt,
        public ?DateTimeInterface $stopsAt = null,
    ) {
        if ($this->stopsAt !== null && $this->stopsAt->getTimestamp() < $this->startsAt->getTimestamp()) {
            throw new InvalidArgumentException('Scheduled SMS stop date cannot be before start date.');
        }
    }
}
