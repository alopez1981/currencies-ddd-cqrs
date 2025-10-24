<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Domain;

use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyCode;

interface CurrencyRepository
{
    //public function delete(CurrencyCode $code): void;

    public function find(CurrencyCode $code): Currency;

    public function search(): Currencies;

    public function save(Currency $currency): void;
}
