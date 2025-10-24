<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Application\RateConversion;


use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyCode;
use Hoyvoy\Currencies\Infrastructure\EloquentCurrencyRepository;

final class RaterConversion
{
    public function __construct(private readonly EloquentCurrencyRepository $repository)
    {
    }

    public function __invoke(CurrencyCode $from, CurrencyCode $to, float $amount): float
    {
        $fromCurrency = $this->repository->find($from);
        $toCurrency = $this->repository->find($to);

        return $amount * ($toCurrency->rate()->value() / $fromCurrency->rate()->value());
    }
}
