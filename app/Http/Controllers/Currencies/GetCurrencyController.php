<?php

declare(strict_types=1);

namespace App\Http\Controllers\Currencies;

use App\Http\Controllers\ApiController;
use Hoyvoy\Currencies\Application\CurrencyResponse;
use Hoyvoy\Currencies\Application\Find\FindCurrencyQuery;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


final class GetCurrencyController extends ApiController
{

    public function __invoke(string $currencyCode): JsonResponse
    {
        /** @var CurrencyResponse $currency */
        $currency = $this->ask(
            new FindCurrencyQuery($currencyCode),
        );

        if ($currency === null) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            [
                'data' => $currency->jsonSerialize(),
            ],
            Response::HTTP_OK
        );
    }
}
