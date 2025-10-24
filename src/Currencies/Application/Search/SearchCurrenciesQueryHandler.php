<?php
declare(strict_types=1);

namespace Hoyvoy\Currencies\Application\Search;

use Hoyvoy\Currencies\Application\CurrenciesResponse;
use Hoyvoy\Currencies\Application\CurrencyResponse;
use Hoyvoy\Currencies\Domain\CurrencyRepository;
use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyCode;
use Hoyvoy\Shared\Domain\Bus\Query\QueryHandler;

final class SearchCurrenciesQueryHandler implements QueryHandler
{
    public function __construct(private readonly CurrencyRepository $repository)
    {
    }

    public function __invoke(SearchCurrenciesQuery $query): CurrenciesResponse
    {
        $currencies = $this->repository->search();

        $usd = $currencies->findByCode(CurrencyCode::fromValue('USD'));
        $usdRate = $usd->rate()->value() ?? 1.0;

        $dto = [];
        foreach ($currencies as $currency) {
            $rateUsd = $usdRate / max($currency->rate()->value(), 1e-12);
            $dto[] = new CurrencyResponse(
                $currency->code()->value(),
                $currency->name()->value(),
                $rateUsd
            );
        }
        return new CurrenciesResponse(...$dto);
    }
}
