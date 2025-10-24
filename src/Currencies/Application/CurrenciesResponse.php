<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Application;

use Hoyvoy\Currencies\Domain\Currency;
use Hoyvoy\Shared\Domain\Bus\Query\Response;

final class CurrenciesResponse implements Response
{
    /** @var CurrencyResponse[] */
    private array $currencies;

    public function __construct(CurrencyResponse ...$currencies)
    {
        $this->currencies = $currencies;
    }

    public static function fromCurrency(Currency $currency)
    {

    }

    public function toArray(): array
    {
        return $this->payload();
    }

    public function jsonSerialize(): array
    {
        return $this->payload();
    }

    /** @return array{data: array<int, array{code:string, name:string, rate_USD:string}>} */
    private function payload(): array
    {
        $formatted = static function (float $value) {
            $string = number_format($value, 2, '.', '');
            $string = rtrim($string, '0');
            $string = rtrim($string, '.');
            return $string;
        };

        $list = array_map(static function (CurrencyResponse $currency) use ($formatted) {
            return [
                'code' => $currency->code(),
                'name' => $currency->name(),
                'rate_USD' => $formatted($currency->rate()),
            ];
        }, $this->currencies);
        return ['data' => $list];
    }

    /**@return CurrencyResponse[] */

    public function currencies(): array
    {
        return $this->currencies;
    }
}
