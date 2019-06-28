<?php

declare(strict_types=1);

namespace Bref\MessengerSns\Event;

/**
 * A raw SNS message was received.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class SnsMessageReceived
{
    private $snsEvent;
    private $receiverName;
    private $shouldHandle = true;

    public function __construct(array $snsEvent, string $receiverName)
    {
        $this->receiverName = $receiverName;
        $this->snsEvent = $snsEvent;
    }

    public function shouldHandle(bool $shouldHandle = null): bool
    {
        if (null !== $shouldHandle) {
            $this->shouldHandle = $shouldHandle;
        }

        return $this->shouldHandle;
    }

    public function getSnsEvent(): array
    {
        return $this->snsEvent;
    }

    public function getReceiverName(): string
    {
        return $this->receiverName;
    }
}
