<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Domain\Criteria;

final class Filters
{
    private array $items;

    public function __construct(Filter ...$items)
    {
        $this->items = $items;
    }

    public function all(): array
    {
        return $this->items;
    }

    public static function none(): self
    {
        return new self();
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }
}
