<?php

declare(strict_types=1);

namespace App\Consumer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SnsConsumer
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

    public function consume(array $event)
    {
        try {
            $envelope = $this->serializer->decode(['body' => $event['Message']]);

            $sfEvent = new WorkerMessageReceivedEvent($envelope, $this->transportName);
            if ($this->eventDispatcher !== null) {
                $this->eventDispatcher->dispatch($sfEvent);
            }

            if (!$sfEvent->shouldHandle()) {
                return;
            }

            $this->bus->dispatch($envelope->with(new ReceivedStamp($this->transportName)));
        } catch (\Throwable $e) {
            $retryAttempt = (int) ($event['MessageAttributes']['retry_attempt']['Value'] ?? 0);
            $this->logger->critical('Could not consume message from SNS.', [
                'exception' => $e,
                'category' => 'sns',
                'retry_attempt' => $retryAttempt,
            ]);

            // TODO publish the message on a queue
        }
    }
}
