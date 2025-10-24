<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Domain\Rates;

interface ExchangeRatesSource
{
    public function latest(string $base): array;

    public function symbols(): array;

}
