<?php

declare(strict_types=1);

namespace Bref\MessengerSns\Event;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;

class SnsMessageReceived extends WorkerMessageReceivedEvent
{
    use SnsMessageTrait;

    public function __construct(array $snsEvent, Envelope $envelope, string $receiverName)
    {
        $this->snsEvent = $snsEvent;
        parent::__construct($envelope, $receiverName);
    }
}
