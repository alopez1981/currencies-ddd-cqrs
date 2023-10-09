<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Hoyvoy\Shared\Domain\Bus\Command\Command;
use Hoyvoy\Shared\Domain\Bus\Command\CommandBus;
use Hoyvoy\Shared\Domain\Bus\Query\Query;
use Hoyvoy\Shared\Domain\Bus\Query\QueryBus;
use Hoyvoy\Shared\Domain\Bus\Query\Response;

abstract class ApiController
{
    protected function ask(Query $query): ?Response
    {
        return app(QueryBus::class)->ask($query);
    }

    protected function dispatch(Command $command): void
    {
        app(CommandBus::class)->dispatch($command);
    }
}
