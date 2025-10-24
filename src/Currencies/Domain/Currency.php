<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Domain;

use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyCode;
use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyName;
use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyRate;
use Hoyvoy\Shared\Domain\Aggregate\AggregateRoot;


final class Currency extends AggregateRoot
{
    public function __construct(
        public CurrencyCode $code,
        public CurrencyName $name,
        public CurrencyRate $rate
    )
    {
    }

    public static function fromPrimitives(string $code, string $name, float $rate): self
    {
        return new self(
            CurrencyCode::fromValue($code),
            CurrencyName::fromValue($name),
            CurrencyRate::fromValue($rate)
        );
    }

    public function toPrimitives(): array
    {
        return [
            'code' => $this->code->value(),
            'name' => $this->name->value(),
            'rate' => $this->rate->value(),
        ];
    }

    public static function create(
        CurrencyCode $code,
        CurrencyName $name,
        CurrencyRate $rate
    ): self
    {
        $currency = new self($code, $name, $rate);
        $currency->record(new CurrencyWasCreated($code->value(), $name->value(), $rate->value()));

        return $currency;
    }

    public function update(CurrencyRate $rate): self
    {
        $this->rate = $rate;
        $this->record(new CurrencyWasUpdated($this->code->value(), $this->name->value(), $this->rate->value()));

        return $this;
    }

    public function code(): CurrencyCode
    {
        return $this->code;
    }

    public function name(): CurrencyName
    {
        return $this->name;
    }

    public function rate(): CurrencyRate
    {
        return $this->rate;
    }
}
