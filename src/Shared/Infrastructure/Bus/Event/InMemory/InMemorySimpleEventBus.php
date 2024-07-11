<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Infrastructure\Bus\Event\InMemory;

use Hoyvoy\Shared\Domain\Bus\Event\DomainEvent;
use Hoyvoy\Shared\Domain\Bus\Event\DomainEventSubscriber;
use Hoyvoy\Shared\Domain\Bus\Event\EventBus;
use Hoyvoy\Shared\Domain\FileUtils;
use Throwable;
use function Lambdish\Phunctional\each;

class InMemorySimpleEventBus implements EventBus
{
    private array $subscribers;

    public function __construct()
    {
        /** @var string[] $scanDirs */
        $scanDirs[] = base_path('src/**/*');

        $eventsSubscribers = [];

        each(function (string $subscriber) use (&$eventsSubscribers) {
            $domainEvents = $subscriber::subscribedTo();
            foreach ($domainEvents as $domainEvent) {
                if (!isset($eventsSubscribers[$domainEvent])) {
                    $eventsSubscribers[$domainEvent] = [];
                }

                $eventsSubscribers[$domainEvent][] = $subscriber;
            }
        }, FileUtils::classesThatImplements(DomainEventSubscriber::class, ...$scanDirs));

        $this->subscribers = file_exists(base_path('bootstrap/cache/event_bus.php')) ?
            require base_path('bootstrap/cache/event_bus.php') :
            $eventsSubscribers;
    }

    public function publish(DomainEvent ...$events): void
    {
        each($this->publishEvent(), $events);
    }

    private function publishEvent(): callable
    {
        return function (DomainEvent $event) {
            try {
                $eventSubscribers = $this->subscribers[$event::class] ?? [];

                each(function (string $subscriber) use ($event) {
                    app($subscriber)->__invoke($event);
                }, $eventSubscribers);

            } catch (Throwable $e) {
                throw $e;
            }
        };
    }
}
