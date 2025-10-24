<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Domain;

use DateTimeImmutable;

final class CurrencyRateHistory
{
    public function __construct(
        public string            $currencyCode,
        public float             $rateUsd,
        public DateTimeImmutable $fetchedAt,
        public ?string           $provider = null
    )
    {

    }
}
