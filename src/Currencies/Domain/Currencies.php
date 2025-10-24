<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Domain;

use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyCode;
use Hoyvoy\Shared\Domain\Collection;

final class Currencies extends Collection
{
    protected function types(): array
    {
        return [Currency::class];
    }

    public function findByCode(CurrencyCode $code): ?Currency
    {
        foreach ($this->items() as $currency) {
            if ($currency->code()->value() === $code->value()) {
                return $currency;
            }
        }
        return null;
    }
}
