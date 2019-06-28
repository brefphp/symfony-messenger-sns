<?php

declare(strict_types=1);

namespace Bref\MessengerSns\Event;

/**
 * Thrown when a SNS event could not be decoded.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class SnsMessageDecodeFailed
{
    private $snsEvent;
    private $throwable;
    private $receiverName;

    public function __construct(array $snsEvent, \Throwable $throwable, string $receiverName)
    {
        $this->snsEvent = $snsEvent;
        $this->throwable = $throwable;
        $this->receiverName = $receiverName;
    }

    public function getSnsEvent(): array
    {
        return $this->snsEvent;
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
