<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Application\RateConversion;

use Hoyvoy\Shared\Domain\Bus\Query\Query;

final class RateConversionQuery implements Query
{
    public function __construct(
        private readonly string $from,
        private readonly string $to,
        private readonly float  $amount,
    )
    {
    }

    public function from(): string
    {
        return $this->from;
    }

    public function to(): string
    {
        return $this->to;
    }

    public function amount(): float
    {
        return $this->amount;
    }

}
