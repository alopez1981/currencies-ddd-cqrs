<?php

declare(strict_types=1);

namespace App\Http\Controllers\Currencies;

use App\Http\Controllers\ApiController;
use App\Http\Requests\GetRateConversionRequest;
use Hoyvoy\Currencies\Application\RateConversion\RateConversionQuery;
use Hoyvoy\Currencies\Application\RateConversionResponse;
use Hoyvoy\Shared\Domain\Bus\Query\QueryBus;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class GetRateConversionController extends ApiController
{
    public function __construct(private readonly QueryBus $queryBus)
    {
    }

    public function __invoke(GetRateConversionRequest $request): JsonResponse
    {
        /**@var RateConversionResponse $rateConversion */
        $rateConversion = $this->queryBus->ask(new RateConversionQuery(
            from: $request->input('from'),
            to: $request->input('to'),
            amount: (int)$request->integer('amount'),
        ));

        return new JsonResponse(
            [
                'data' => $rateConversion->jsonSerialize(),
            ],
            Response::HTTP_OK
        );
    }
}
