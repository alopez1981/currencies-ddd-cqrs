<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Infrastructure\Bus\Command;

use Hoyvoy\Shared\Domain\Bus\Command\Command;
use Hoyvoy\Shared\Domain\Bus\Command\CommandBus;
use ReflectionClass;

final class InMemorySimpleCommandBus implements CommandBus
{
    public function __construct()
    {
    }

    public function dispatch(Command $command): void
    {
        $reflection  = new ReflectionClass($command);
        $handlerName = str_replace("Command", "CommandHandler", $reflection->getShortName());
        $handlerName = str_replace($reflection->getShortName(), $handlerName, $reflection->getName());
        $handler     = app($handlerName);

        $handler($command);
    }
}
