<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Services;

use HakanIspirli\NetgsmLaravel\Contracts\NetgsmClientInterface;
use HakanIspirli\NetgsmLaravel\Data\NetgsmSmsResponse;
use HakanIspirli\NetgsmLaravel\Enums\NetgsmEndpoint;
use HakanIspirli\NetgsmLaravel\Support\NetgsmResponseParser;
use Illuminate\Config\Repository;
use Illuminate\Http\Client\Factory;
use InvalidArgumentException;

final readonly class LaravelNetgsmClient implements NetgsmClientInterface
{
    public function __construct(
        private Factory $http,
        private Repository $config,
        private NetgsmResponseParser $parser,
    ) {}

    /**
     * @param array<string, mixed> $payload
     */
    public function post(NetgsmEndpoint $endpoint, array $payload): NetgsmSmsResponse
    {
        $username = $this->stringConfig('netgsm.username');
        $password = $this->stringConfig('netgsm.password');
        $timeout = (int) $this->config->get('netgsm.timeout', 30);

        $response = $this->http
            ->timeout($timeout > 0 ? $timeout : 30)
            ->acceptJson()
            ->asJson()
            ->withBasicAuth($username, $password)
            ->post($this->url($endpoint), $payload);

        $data = $response->json();

        if (! is_array($data)) {
            $data = [
                'code' => null,
                'description' => $response->body(),
            ];
        }

        return $this->parser->parse($data, $response->status());
    }

    private function url(NetgsmEndpoint $endpoint): string
    {
        $baseUrl = rtrim($this->stringConfig('netgsm.base_url'), '/');
        $path = match ($endpoint) {
            NetgsmEndpoint::SmsSend => $this->stringConfig('netgsm.endpoints.sms_send'),
            NetgsmEndpoint::Otp => $this->stringConfig('netgsm.endpoints.otp'),
        };

        return $baseUrl.'/'.ltrim($path, '/');
    }

    private function stringConfig(string $key): string
    {
        $value = $this->config->get($key);

        if (! is_string($value) || trim($value) === '') {
            throw new InvalidArgumentException("Missing Netgsm configuration value [{$key}].");
        }

        return trim($value);
    }
}
