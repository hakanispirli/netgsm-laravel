<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Contracts;

use HakanIspirli\NetgsmLaravel\Data\NetgsmSmsResponse;
use HakanIspirli\NetgsmLaravel\Data\OtpSmsRequest;

interface OtpSenderInterface
{
    public function sendOtpRequest(OtpSmsRequest $request): NetgsmSmsResponse;
}
