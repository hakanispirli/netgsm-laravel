<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Support;

use HakanIspirli\NetgsmLaravel\Data\NormalSmsRequest;
use HakanIspirli\NetgsmLaravel\Data\OtpSmsRequest;
use HakanIspirli\NetgsmLaravel\Data\ScheduledSmsRequest;

final readonly class NetgsmPayloadBuilder
{
    public function __construct(
        private NetgsmDateFormatter $dateFormatter,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function normal(NormalSmsRequest $request): array
    {
        $payload = [
            'msgheader' => $request->msgheader,
            'messages' => array_map(
                static fn ($message): array => $message->toNetgsmArray(),
                $request->messages,
            ),
            'encoding' => $request->encoding->value,
            'iysfilter' => $request->iysFilter->value,
        ];

        return $this->appendOptional($payload, [
            'appname' => $request->appname,
            'partnercode' => $request->partnercode,
            'referansID' => $request->referenceId,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function scheduled(ScheduledSmsRequest $request): array
    {
        $payload = $this->normal($request->sms);
        $payload['startdate'] = $this->dateFormatter->format($request->startsAt);

        if ($request->stopsAt !== null) {
            $payload['stopdate'] = $this->dateFormatter->format($request->stopsAt);
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    public function otp(OtpSmsRequest $request): array
    {
        return $this->appendOptional([
            'msgheader' => $request->msgheader,
            'msg' => $request->message,
            'no' => $request->to,
        ], [
            'appname' => $request->appname,
        ]);
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $optional
     * @return array<string, mixed>
     */
    private function appendOptional(array $payload, array $optional): array
    {
        foreach ($optional as $key => $value) {
            if ($value !== null && $value !== '') {
                $payload[$key] = $value;
            }
        }

        return $payload;
    }
}
