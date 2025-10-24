<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Application;

use Hoyvoy\Currencies\Domain\Currency;
use Hoyvoy\Shared\Domain\Bus\Query\Response;

final class CurrencyResponse implements Response
{
    public function __construct(
        public string $code,
        public string $name,
        public float  $rate
    )
    {
    }

    public static function fromCurrency(Currency $currency): self
    {
        return new self(
            $currency->code->value(),
            $currency->name->value(),
            $currency->rate->value()
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'rate' => $this->rate,
        ];
    }

    public function code(): string
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function rate(): float
    {
        return $this->rate;
    }
}
