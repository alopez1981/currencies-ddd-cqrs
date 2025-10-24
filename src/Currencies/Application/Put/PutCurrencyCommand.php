<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Application\Put;

use Hoyvoy\Shared\Domain\Bus\Command\Command;

final class PutCurrencyCommand implements Command
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly float  $rate)
    {
    }

    /**
     * @return string
     */
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
