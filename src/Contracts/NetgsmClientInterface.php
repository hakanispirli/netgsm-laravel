<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Contracts;

use HakanIspirli\NetgsmLaravel\Data\NetgsmSmsResponse;
use HakanIspirli\NetgsmLaravel\Enums\NetgsmEndpoint;

interface NetgsmClientInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public function post(NetgsmEndpoint $endpoint, array $payload): NetgsmSmsResponse;
}
