<?php

declare(strict_types=1);

namespace Bref\MessengerSns\Event;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;

class SnsMessageFailed extends WorkerMessageFailedEvent
{
    use SnsMessageTrait;

    public function __construct(array $snsEvent, Envelope $envelope, string $receiverName, \Throwable $error, bool $willRetry)
    {
        $this->snsEvent = $snsEvent;

        parent::__construct($envelope, $receiverName, $error, $willRetry);
    }
}
