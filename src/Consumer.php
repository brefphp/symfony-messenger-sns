<?php

declare(strict_types=1);

namespace Bref\MessengerSns;

use Bref\MessengerSns\Event\SnsMessageDecodeFailed;
use Bref\MessengerSns\Event\SnsMessageFailed;
use Bref\MessengerSns\Event\SnsMessageHandled;
use Bref\MessengerSns\Event\SnsMessageReceived;
use Psr\Log\LoggerInterface;
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
    private $logger;
    private $eventDispatcher;

    public function __construct(
        MessageBusInterface $bus,
        SerializerInterface $serializer,
        string $transportName = 'sns',
        LoggerInterface $logger = null,
        EventDispatcherInterface $eventDispatcher = null
    ) {

        $this->bus = $bus;
        $this->serializer = $serializer;
        $this->transportName = $transportName;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function consume(array $snsEvent)
    {
        try {
            $envelope = $this->serializer->decode(['body' => $snsEvent['Message']]);
        } catch (\Throwable $e) {
            $this->dispatchEvent(new SnsMessageDecodeFailed($snsEvent, $e, $this->transportName));
        }

        $sfEvent = new SnsMessageReceived($snsEvent, $envelope, $this->transportName);
        $this->dispatchEvent($sfEvent);

        if (!$sfEvent->shouldHandle()) {
            return;
        }

        try {
            $this->bus->dispatch($envelope->with(new ReceivedStamp($this->transportName)));
        } catch (\Throwable $e) {
            $this->logger->critical('Could not consume message from SNS.', [
                'exception' => $e,
                'category' => 'sns',
            ]);

            $this->dispatchEvent(new SnsMessageFailed($snsEvent, $envelope, $this->transportName, $e, /* $willRetry */ false));

            return;
        }

        $this->dispatchEvent(new SnsMessageHandled($snsEvent, $envelope, $this->transportName));
    }

    private function dispatchEvent($event)
    {
        if (null === $this->eventDispatcher) {
            return;
        }

        $this->eventDispatcher->dispatch($event);
    }
}
