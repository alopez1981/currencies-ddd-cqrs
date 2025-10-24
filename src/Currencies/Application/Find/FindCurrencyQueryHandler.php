<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Application\Find;

use Hoyvoy\Currencies\Application\CurrenciesResponse;
use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyCode;
use Hoyvoy\Shared\Domain\Bus\Query\QueryHandler;

final class FindCurrencyQueryHandler implements QueryHandler
{
    public function __construct(private readonly CurrencyFinder $finder)
    {
    }

    public function __invoke(FindCurrencyQuery $query): CurrenciesResponse
    {
        $code = new CurrencyCode($query->code());

        $currency = $this->finder->__invoke($code);

        return CurrenciesResponse::fromCurrency($currency);
    }


}
