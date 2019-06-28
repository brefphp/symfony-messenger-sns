<?php

declare(strict_types=1);

namespace Bref\MessengerSns;

use Bref\MessengerSns\Event\SnsMessageDecodeFailed;
use Bref\MessengerSns\Event\SnsMessageFailed;
use Bref\MessengerSns\Event\SnsMessageReceived;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class Consumer
{
    private $bus;
    private $serializer;
    private $transportName;
    private $eventDispatcher;
    private $logger;

    public function __construct(
        MessageBusInterface $bus,
        SerializerInterface $serializer,
        string $transportName = 'sns',
        EventDispatcherInterface $eventDispatcher = null,
        LoggerInterface $logger = null
    ) {
        $this->bus = $bus;
        $this->serializer = $serializer;
        $this->transportName = $transportName;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    public function consume(array $snsEvent)
    {
        $sfEvent = new SnsMessageReceived($snsEvent, $this->transportName);
        $this->dispatchEvent($sfEvent);
        if (!$sfEvent->shouldHandle()) {
            return;
        }

        try {
            $envelope = $this->serializer->decode(['body' => $snsEvent['Message']]);
        } catch (\Throwable $e) {
            $this->dispatchEvent(new SnsMessageDecodeFailed($snsEvent, $e, $this->transportName));
        }

        $sfEvent = new WorkerMessageReceivedEvent($envelope, $this->transportName);
        $this->dispatchEvent($sfEvent);

        if (!$sfEvent->shouldHandle()) {
            return;
        }

        try {
            $this->bus->dispatch($envelope->with(new ReceivedStamp($this->transportName)));
        } catch (\Throwable $e) {
            if ($this->logger !== null) {
                $this->logger->critical('Could not consume message from SNS.', [
                    'exception' => $e,
                    'category' => 'sns',
                ]);
            }

            $this->dispatchEvent(new SnsMessageFailed($snsEvent, $envelope, $this->transportName, $e));

            // Start handle next event.
            return;
        }

        $this->dispatchEvent(new WorkerMessageHandledEvent($envelope, $this->transportName));
    }

    private function dispatchEvent($event)
    {
        if (null === $this->eventDispatcher) {
            return;
        }

        $this->eventDispatcher->dispatch($event);
    }
}
