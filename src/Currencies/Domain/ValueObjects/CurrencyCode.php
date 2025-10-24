<?php

namespace Hoyvoy\Currencies\Domain\ValueObjects;

use Hoyvoy\Shared\Domain\ValueObject\StringValueObject;
use InvalidArgumentException;

final class CurrencyCode extends StringValueObject
{
    public static function fromValue(string $value): static
    {
        $value = strtoupper($value);

        if (!preg_match('/^[A-Z]{3}$/', $value)) {
            throw new InvalidArgumentException('Invalid currency code');
        }
        return new static($value);
    }
}
