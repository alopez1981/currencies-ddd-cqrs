<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Infrastructure;

use Hoyvoy\Currencies\Domain\Currencies;
use Hoyvoy\Currencies\Domain\Currency;
use Hoyvoy\Currencies\Domain\CurrencyRepository;
use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyCode;
use Hoyvoy\Currencies\Infrastructure\Eloquent\CurrencyModel;
use Hoyvoy\Shared\Domain\Exceptions\NotFoundException;

final class EloquentCurrencyRepository implements CurrencyRepository
{
    public function __construct(private readonly CurrencyModel $model)
    {

    }


    public function find(CurrencyCode $code): Currency
    {
        /** @var ?CurrencyModel $currency */
        $currency = $this->model::query()->find($code->value());

        if (null === $currency) {
            throw new NotFoundException('Currency not found');
        }

        return $this->toDomain($currency);
    }

    public function save(Currency $currency): void
    {
        $this->model::query()->updateOrCreate(
            ['code' => $currency->code()->value()],
            [
                'name' => $currency->name()->value(),
                'rate' => $currency->rate()->value(),
            ],
        );
    }

    private function toDomain(CurrencyModel $eloquentCurrencyModel): Currency
    {
        return Currency::fromPrimitives(
            $eloquentCurrencyModel->code,
            $eloquentCurrencyModel->name,
            $eloquentCurrencyModel->rate
        );
    }

    public function search(): Currencies
    {
        $items = $this->model::query()
            ->orderBy('code')
            ->get(['code', 'name', 'rate'])
            ->map(fn(CurrencyModel $currency) => $this->toDomain($currency))
            ->all();

        return new Currencies($items);
    }


}
