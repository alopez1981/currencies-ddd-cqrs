<?php

namespace Hoyvoy\Currencies\Domain\ValueObjects;


use InvalidArgumentException;

final class CurrencyRate
{
    public function __construct(private readonly float $value)
    {
        if (!is_finite($value) || $value < 0.0) {
            throw new InvalidArgumentException('Currency rate usage must be a positive number');
        }
    }

    public static function fromValue(float $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function value(): float
    {
        return $this->value;
    }

}
