<?php

declare(strict_types=1);

namespace Bref\MessengerSns\Event;

use Symfony\Component\Messenger\Envelope;

/**
 * An SNS messages failed to be handled properly. Subscribers to this event should handle retry.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class SnsMessageFailed
{
    private $snsEvent;
    private $envelope;
    private $receiverName;
    private $throwable;

    public function __construct(array $snsEvent, Envelope $envelope, string $receiverName, \Throwable $error)
    {
        $this->snsEvent = $snsEvent;
        $this->envelope = $envelope;
        $this->receiverName = $receiverName;
        $this->throwable = $error;
    }

    public function getSnsEvent(): array
    {
        return $this->snsEvent;
    }

    public function getEnvelope(): Envelope
    {
        return $this->envelope;
    }

    public function getReceiverName(): string
    {
        return $this->receiverName;
    }

    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }
}
