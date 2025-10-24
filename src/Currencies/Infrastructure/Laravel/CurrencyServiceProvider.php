<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Infrastructure\Laravel;

use GuzzleHttp\Client;
use Hoyvoy\Currencies\Domain\CurrencyRateHistoryRepository;
use Hoyvoy\Currencies\Domain\CurrencyRepository;
use Hoyvoy\Currencies\Domain\Rates\ExchangeRatesSource;
use Hoyvoy\Currencies\Infrastructure\EloquentCurrencyRateHistoryRepository;
use Hoyvoy\Currencies\Infrastructure\EloquentCurrencyRepository;
use Hoyvoy\Currencies\Infrastructure\Fixer\FixerRatesSource;
use Illuminate\Support\ServiceProvider;

class CurrencyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CurrencyRepository::class,
            EloquentCurrencyRepository::class
        );

        $this->app->bind(
            CurrencyRateHistoryRepository::class,
            EloquentCurrencyRateHistoryRepository::class
        );

        $this->registerRatesSource();
    }

    private function registerRatesSource(): void
    {
        $this->app->bind(ExchangeRatesSource::class, function () {
            return new FixerRatesSource(
                new Client(),
                config('services.fixer.url'),
                config('services.fixer.api_key'),
                config('services.fixer.base')
            );
        });
    }
}
