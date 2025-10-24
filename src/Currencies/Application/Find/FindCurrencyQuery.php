<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Application\Find;

use Hoyvoy\Shared\Domain\Bus\Query\Query;

final class FindCurrencyQuery implements Query
{
    public function __construct(private readonly string $code)
    {
    }

    public function code(): string
    {
        return $this->code;
    }
}
