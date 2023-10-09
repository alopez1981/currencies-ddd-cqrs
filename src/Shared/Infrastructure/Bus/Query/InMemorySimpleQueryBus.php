<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Infrastructure\Bus\Query;

use Hoyvoy\Shared\Domain\Bus\Query\Query;
use Hoyvoy\Shared\Domain\Bus\Query\QueryBus;
use Hoyvoy\Shared\Domain\Bus\Query\Response;
use ReflectionClass;

final class InMemorySimpleQueryBus implements QueryBus
{
    public function __construct()
    {
    }

    public function ask(Query $query): ?Response
    {
        $reflection  = new ReflectionClass($query);
        $handlerName = str_replace("Query", "QueryHandler", $reflection->getShortName());
        $handlerName = str_replace($reflection->getShortName(), $handlerName, $reflection->getName());
        $handler     = app($handlerName);

        return $handler($query);
    }
}
