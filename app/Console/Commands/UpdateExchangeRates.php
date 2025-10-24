<?php

declare (strict_types=1);

// app/Console/Commands/UpdateExchangeRates.php
namespace App\Console\Commands;

use Hoyvoy\Currencies\Application\Put\PutCurrencyCommand;
use Hoyvoy\Currencies\Domain\CurrencyRepository;
use Hoyvoy\Currencies\Domain\Rates\ExchangeRatesSource;
use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyCode;
use Hoyvoy\Shared\Domain\Bus\Command\CommandBus;
use Illuminate\Console\Command;
use Throwable;

final class UpdateExchangeRates extends Command
{
    protected $signature = 'rates:update';
    protected $description = 'Updates exchange rates from Fixer API.';

    private array $symbolsCache = [];

    public function __construct(
        private readonly ExchangeRatesSource $rates,
        private readonly CommandBus          $commandBus,
        private readonly CurrencyRepository  $repository,
    )
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $base = config('services.fixer.base', 'EUR');
        $this->info("Currency rates updated successfully (base: {$base}).");

        $this->symbolsCache = $this->rates->symbols();
        $rates = $this->rates->latest($base);

        $count = 0;
        foreach ($rates as $code => $rate) {

            $name = $this->guessName((string)$code);

            $this->commandBus->dispatch(
                new PutCurrencyCommand(
                    code: (string)$code,
                    name: $name,
                    rate: (float)$rate
                )
            );
            $count++;
        }
        $this->info("Currency rates updated successfully {$count} .");
        return self::SUCCESS;
    }

    private function guessName(string $code): string
    {
        try {
            $currency = $this->repository->find(CurrencyCode::fromValue($code));
            $dbName = $currency->name()?->value();
            if ($dbName && $dbName !== $code) {
                return $dbName;
            }
        } catch (Throwable) {
        }
        return $this->symbolsCache[$code] ?? $code;
    }
}
