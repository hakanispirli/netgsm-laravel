<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Contracts;

use HakanIspirli\NetgsmLaravel\Data\NetgsmSmsResponse;
use HakanIspirli\NetgsmLaravel\Data\NormalSmsRequest;
use HakanIspirli\NetgsmLaravel\Data\ScheduledSmsRequest;

interface SmsSenderInterface
{
    public function sendRequest(NormalSmsRequest $request): NetgsmSmsResponse;

    public function scheduleRequest(ScheduledSmsRequest $request): NetgsmSmsResponse;
}
