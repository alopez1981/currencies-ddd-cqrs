<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Domain\Criteria;

final class Criteria
{
    public function __construct(
        private readonly Filters $filters,
        private readonly ?Order  $order = null,
        private readonly ?int    $offset = null,
        private readonly ?int    $limit = null,
    )
    {
    }

    public function filters(): Filters
    {
        return $this->filters;
    }

    public function order(): ?Order
    {
        return $this->order;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public static function fromValues(array $values): self
    {
        $filters = Filters::none();
        if (!empty($values['filters']) && is_array($values['filters'])) {
            $items = array_map(
                fn(array $f) => new Filter(
                    $f['field'],
                    $f['operator'],
                    $f['value'] ?? null
                ),
                $values['filters']
            );
            $filters = new Filters(...$items);
        }
        $order = null;
        if (!empty($values['order'])) {
            $order = new Order(
                (string)$values['order_by'],
                strtoupper((string)($values['order'] ?? Order::ASC))
            );
        }

        $offset = array_key_exists('offset', $values) ? (int)$values['offset'] : null;
        $limit = array_key_exists('limit', $values) ? (int)$values['limit'] : null;

        return new self($filters, $order, $offset, $limit);
    }
}
