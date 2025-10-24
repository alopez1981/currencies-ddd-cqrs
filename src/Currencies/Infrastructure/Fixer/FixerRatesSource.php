<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Infrastructure\Fixer;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Hoyvoy\Currencies\Domain\Rates\ExchangeRatesSource;
use Hoyvoy\Shared\Domain\Exceptions\DomainException;

final class FixerRatesSource implements ExchangeRatesSource
{
    public function __construct(
        private readonly Client $httpClient,
        private readonly string $url,
        private readonly string $apiKey,
        private readonly string $base,
    )
    {
    }

    /**
     * @throws GuzzleException
     * @throws DomainException
     */
    public function latest(string $base): array
    {
        $response = $this->httpClient->get(
            "{$this->url}/latest", [
                'query' => [
                    'access_key' => $this->apiKey,
                ],
                'http_errors' => false,
                'timeout' => 10,
            ]
        );

        $data = json_decode((string)$response->getBody(), true);

        if (!($data ['success'] ?? false)) {
            throw new DomainException('Fixer rates not found');
        }

        return $data['rates'] ?? [];
    }

    /**
     * @throws GuzzleException
     */
    public function symbols(): array
    {
        $response = $this->httpClient->get(
            "{$this->url}/symbols", [
                'query' => [
                    'access_key' => $this->apiKey,
                ],
                'http_errors' => false,
                'timeout' => 10,
            ]
        );
        $data = json_decode((string)$response->getBody(), true);

        if (!($data ['success'] ?? false)) {
            return [];
        }
        return $data['symbols'] ?? [];
    }
}
