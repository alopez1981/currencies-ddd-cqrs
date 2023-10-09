<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Domain\Bus\Event;

use DateTimeImmutable;
use Hoyvoy\Shared\Domain\DateUtils;
use Ramsey\Uuid\Uuid;

abstract class DomainEvent
{
    private string $eventId;
    private string $occurredAt;

    public function __construct(
        private string $aggregateId,
        string $eventId = null,
        string $occurredAt = null,
    ) {
        /** @var DateTimeImmutable $now */
        $now = DateTimeImmutable::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''));

        $this->eventId    = $eventId ?: Uuid::uuid4()->toString();
        $this->occurredAt = $occurredAt ?: DateUtils::dateToString($now, 'Y-m-d\TH:i:s.uP');
    }

    abstract public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredAt,
    ): self;

    abstract public static function eventName(): string;

    abstract public function toPrimitives(): array;

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function occurredAt(): string
    {
        return $this->occurredAt;
    }
}
