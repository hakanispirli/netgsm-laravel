<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Data;

use HakanIspirli\NetgsmLaravel\Enums\Encoding;
use HakanIspirli\NetgsmLaravel\Enums\IysFilter;
use InvalidArgumentException;

final readonly class NormalSmsRequest
{
    /**
     * @param list<SmsMessage> $messages
     */
    public function __construct(
        public array $messages,
        public string $msgheader,
        public Encoding $encoding,
        public IysFilter $iysFilter,
        public ?string $appname = null,
        public ?string $partnercode = null,
        public ?string $referenceId = null,
    ) {
        if ($this->messages === []) {
            throw new InvalidArgumentException('At least one SMS message is required.');
        }

        foreach ($this->messages as $message) {
            if (! $message instanceof SmsMessage) {
                throw new InvalidArgumentException('All normal SMS messages must be SmsMessage instances.');
            }
        }

        if (trim($this->msgheader) === '') {
            throw new InvalidArgumentException('Netgsm message header cannot be empty.');
        }
    }
}
