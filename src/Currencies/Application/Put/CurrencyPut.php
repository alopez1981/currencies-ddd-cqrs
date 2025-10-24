<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Application\Put;

use Hoyvoy\Currencies\Domain\Currency;
use Hoyvoy\Currencies\Domain\CurrencyRepository;
use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyCode;
use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyName;
use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyRate;
use Hoyvoy\Shared\Domain\Bus\Event\EventBus;
use Hoyvoy\Shared\Domain\Exceptions\NotFoundException;

final class CurrencyPut
{

    public function __construct(
        private readonly CurrencyRepository $repository,
        private readonly EventBus           $eventBus
    )
    {
    }

    public function __invoke(CurrencyCode $code, CurrencyName $name, CurrencyRate $rate): void
    {
        try {
            $currency = $this->repository->find($code);
            $currency->update($rate);
        } catch (NotFoundException) {
            $currency = Currency::create($code, $name, $rate);
        }

        $this->repository->save($currency);
        $this->eventBus->publish(...$currency->pullDomainEvents());
    }
}
