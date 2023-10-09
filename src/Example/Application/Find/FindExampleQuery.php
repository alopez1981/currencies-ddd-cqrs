<?php

declare(strict_types=1);

namespace Hoyvoy\Example\Application\Find;

use Hoyvoy\Shared\Domain\Bus\Query\Query;

final class FindExampleQuery implements Query
{
    public function __construct(private string $id)
    {
    }

    public function id(): string
    {
        return $this->id;
    }
}
