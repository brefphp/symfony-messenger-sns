<?php

declare(strict_types=1);

namespace Bref\MessengerSns\Event;

trait SnsMessageTrait
{
    private $snsEvent;

    public function getSnsEvent(): array
    {
        return $this->snsEvent;
    }
}