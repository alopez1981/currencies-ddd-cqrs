<?php

declare(strict_types=1);

namespace Hoyvoy\Example\Application;

use Hoyvoy\Shared\Domain\Bus\Query\Response;
use Hoyvoy\Shared\Domain\Utils;

final class ExampleResponse implements Response
{
    public function __construct(
        private string $id,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }
}
