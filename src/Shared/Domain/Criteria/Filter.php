<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Domain\Criteria;

final class Filter
{
    public function __construct(
        private string $field,
        private string $operator,
        private mixed  $value = null
    )
    {
    }

    public function field(): string
    {
        return $this->field;
    }

    public function operator(): string
    {
        return $this->operator;
    }

    public function value(): mixed
    {
        return $this->value;
    }
}
