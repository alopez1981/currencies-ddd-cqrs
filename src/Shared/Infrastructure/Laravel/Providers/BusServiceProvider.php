<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Infrastructure\Laravel\Providers;

use Hoyvoy\Shared\Domain\Bus\Command\CommandBus;
use Hoyvoy\Shared\Domain\Bus\Event\DomainEventSubscriber;
use Hoyvoy\Shared\Domain\Bus\Event\EventBus;
use Hoyvoy\Shared\Domain\Bus\Query\QueryBus;
use Hoyvoy\Shared\Domain\FileUtils;
use Hoyvoy\Shared\Infrastructure\Bus\Command\InMemorySimpleCommandBus;
use Hoyvoy\Shared\Infrastructure\Bus\Event\InMemory\InMemorySimpleEventBus;
use Hoyvoy\Shared\Infrastructure\Bus\Query\InMemorySimpleQueryBus;
use Illuminate\Support\ServiceProvider;

final class BusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerQueryBus();
        $this->registerCommandBus();
        $this->registerEventBus();
    }

    private function registerQueryBus(): void
    {
        app()->singleton(QueryBus::class, function () {
            return new InMemorySimpleQueryBus();
        });
    }

    private function registerCommandBus(): void
    {
        app()->singleton(CommandBus::class, function () {
            return new InMemorySimpleCommandBus();
        });
    }

    private function registerEventBus(): void
    {
        $subscribers = FileUtils::classesThatImplements(DomainEventSubscriber::class, base_path('src/**/*'));

        app()->tag($subscribers, 'domain_event_subscriber');

        app()->singleton(EventBus::class, function () {
            return new InMemorySimpleEventBus();
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/bus.php' => config_path('shared/bus.php'),
        ]);
    }
}
