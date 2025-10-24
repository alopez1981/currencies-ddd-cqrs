<?php

declare(strict_types=1);

namespace App\Http\Controllers\Currencies;

use App\Http\Controllers\ApiController;
use Hoyvoy\Currencies\Application\CurrenciesResponse;
use Hoyvoy\Currencies\Application\Search\SearchCurrenciesQuery;
use Hoyvoy\Shared\Domain\Bus\Query\QueryBus;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class GetCurrenciesController extends ApiController
{
    public function __construct(private readonly QueryBus $queryBus)
    {

    }

    public function __invoke(): JsonResponse
    {
        /** @var CurrenciesResponse $response */
        $response = $this->queryBus->ask(
            new SearchCurrenciesQuery()
        );
        return new JsonResponse(
            $response->jsonSerialize(),
            Response::HTTP_OK
        );
    }
}
