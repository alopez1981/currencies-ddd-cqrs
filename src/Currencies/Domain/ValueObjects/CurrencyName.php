<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Domain\ValueObjects;

use Hoyvoy\Shared\Domain\ValueObject\StringValueObject;
use InvalidArgumentException;

final class CurrencyName extends StringValueObject
{
    /**
     * @param string $value
     * @return static
     */
    public static function fromValue(string $value): static
    {
        $value = trim(preg_replace('/\s+/', ' ', $value));

        if ($value === '') {
            throw new InvalidArgumentException('Currency name cannot be empty.');
        }
        return new static($value);
    }
}
