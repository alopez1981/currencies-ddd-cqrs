<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Domain;

interface CurrencyRateHistoryRepository
{
    /**param iterable<CurrencyRateHistory> $entries */

    public function saveMany(iterable $entries): void;
}
