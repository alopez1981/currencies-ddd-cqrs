<?php

declare(strict_types=1);

namespace Hoyvoy\Example\Application\Find;

use Hoyvoy\Example\Application\ExampleResponse;
use Hoyvoy\Shared\Domain\Bus\Query\QueryHandler;

final class FindExampleQueryHandler implements QueryHandler
{
    public function __construct()
    {
    }

    public function __invoke(FindExampleQuery $query): ExampleResponse
    {
        return new ExampleResponse($query->id());
    }
}
