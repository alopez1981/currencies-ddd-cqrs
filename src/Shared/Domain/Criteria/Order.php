<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Domain\Criteria;

final class Order
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    public function __construct(
        private string $orderBy,
        private string $orderType = self::ASC)
    {
    }

    public function orderBy(): string
    {
        return $this->orderBy;
    }

    public function orderType(): string
    {
        return $this->orderType;
    }
}
