<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Infrastructure;

use Hoyvoy\Currencies\Domain\CurrencyRateHistory;
use Hoyvoy\Currencies\Domain\CurrencyRateHistoryRepository;
use Hoyvoy\Currencies\Infrastructure\Eloquent\CurrencyRateHistoryModel;

final class EloquentCurrencyRateHistoryRepository implements CurrencyRateHistoryRepository
{
    /**@param iterable<CurrencyRateHistory> $entries */
    public function saveMany(iterable $entries): void
    {
        $rows = [];
        foreach ($entries as $entry) {
            $rows[] = [
                'currency_code' => $entry->currencyCode,
                'rate_usd' => $entry->rateUsd,
                'provider' => $entry->provider,
                'fetched_at' => $entry->fetchedAt->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($rows) {
            CurrencyRateHistoryModel::query()->insert($rows);
        }
    }
}
