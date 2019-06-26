<?php

declare(strict_types=1);

namespace App\Consumer\SnsConsumer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

final class SnsConsumer
{
    private $bus;
    private $serializer;
    private $logger;

    public function __construct(
        MessageBusInterface $bus,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        $this->bus = $bus;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function consume(array $event)
    {
        try {
            $envelope = $this->serializer->decode(['body' => $event['Message']]);
            $this->bus->dispatch($envelope);
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
