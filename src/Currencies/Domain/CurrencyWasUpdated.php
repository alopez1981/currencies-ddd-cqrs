<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Domain;

use Hoyvoy\Shared\Domain\Bus\Event\DomainEvent;

final class CurrencyWasUpdated extends DomainEvent
{
    public function __construct(
        public string $code,
        public string $name,
        public float  $rate,
        ?string       $eventId = null,
        ?string       $occurredAt = null,
    )
    {
        parent::__construct($code, $eventId, $occurredAt);
    }

    public static function fromPrimitives(
        string  $aggregateId,
        array   $body,
        ?string $eventId,
        ?string $occurredAt
    ): DomainEvent
    {
        return new self($aggregateId, $body['name'], $body['rate'], $eventId, $occurredAt);
    }

    public static function eventName(): string
    {
        return 'currency.rate_was_updated';
    }

    public function toPrimitives(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'rate' => $this->rate,
        ];
    }

}
