<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Application;

use Hoyvoy\Shared\Domain\Bus\Query\Response;

final class RateConversionResponse implements Response
{
    public function __construct(
        private readonly string $from,
        private readonly string $to,
        private readonly float  $amount,
        private readonly float  $result
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

    public function result(): float
    {
        return $this->result;
    }

    public function jsonSerialize(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'amount' => $this->amount,
            'result' => round($this->result, 2),
        ];
    }
}
