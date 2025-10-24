<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Application\RateConversion;

use Hoyvoy\Currencies\Application\RateConversionResponse;
use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyCode;
use Hoyvoy\Shared\Domain\Bus\Query\QueryHandler;

final class RateConversionQueryHandler implements QueryHandler
{
    public function __construct(private readonly RaterConversion $raterConversion)
    {
    }

    public function __invoke(RateConversionQuery $query): RateConversionResponse
    {
        $fromCode = CurrencyCode::fromValue($query->from());
        $toCode = CurrencyCode::fromValue($query->to());

        $result = $this->raterConversion->__invoke($fromCode, $toCode, $query->amount());

        return new RateConversionResponse(
            $fromCode->value(),
            $toCode->value(),
            $query->amount(),
            $result
        );
    }


}
