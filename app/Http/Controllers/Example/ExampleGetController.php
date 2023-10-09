<?php

declare(strict_types=1);

namespace App\Http\Controllers\Example;

use App\Http\Controllers\ApiController;
use Hoyvoy\Example\Application\ExampleResponse;
use Hoyvoy\Example\Application\Find\FindExampleQuery;
use Illuminate\Http\JsonResponse;
use Ramsey\Uuid\Uuid;

final class ExampleGetController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        /** @var ExampleResponse $example */
        $example = $this->ask(new FindExampleQuery(Uuid::uuid4()->toString()));

        return new JsonResponse([
            'data' => [
                'id' => $example->id(),
            ],
        ], JsonResponse::HTTP_OK);
    }
}
