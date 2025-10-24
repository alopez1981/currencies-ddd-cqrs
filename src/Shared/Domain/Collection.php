<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Domain;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

abstract class Collection implements Countable, IteratorAggregate
{
    /**@var object[] */
    private array $items;

    /**@param object[] $items */

    public function __construct(array $items = [])
    {
        $this->assertArrayOf($items, ...$this->types());
        $this->items = array_values($items);
    }

    /**@return array<int, string> */

    abstract protected function types(): array;

    /**@return ArrayIterator<int, object> */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items());
    }

    public function add(object ...$items): void
    {
        $this->assertArrayOf($items, ...$this->types());
        array_push($this->items, ...$items);
    }

    public function count(): int
    {
        return count($this->items());
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return $this->count() !== 0;
    }

    /**return object[] */
    public function items(): array
    {
        return $this->items;
    }

    /**param object[] $items */
    private function assertArrayOf(array $items, string ...$classes): void
    {
        foreach ($items as $item) {
            $this->assertInstanceOfAny($item, $classes);
        }
    }

    /**@param string[] $classes */
    private function assertInstanceOfAny(object $item, array $classes): void
    {
        foreach ($classes as $class) {
            if ($item instanceof $class) {
                return;
            }
        }
        $allowed = implode(', ', $classes);
        throw new InvalidArgumentException(
            sprintf('The object <%s> is not an instance of <%s>', get_class($item), $allowed));
    }
}
