<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Enums;

enum NetgsmEndpoint: string
{
    case SmsSend = 'sms_send';
    case Otp = 'otp';
}
