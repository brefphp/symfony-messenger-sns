<?php

declare(strict_types=1);

namespace Bref\MessengerSns\Event;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;

class SnsMessageDecodeFailed
{
    use SnsMessageTrait;
    private $throwable;
    private $receiverName;

    public function __construct(array $snsEvent, \Throwable $throwable, string $receiverName)
    {
        $this->snsEvent = $snsEvent;
        $this->throwable = $throwable;
        $this->receiverName = $receiverName;
    }

    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }

    public function getReceiverName(): string
    {
        return $this->receiverName;
    }
}
