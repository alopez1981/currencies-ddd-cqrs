<?php

namespace Hoyvoy\Currencies\Application\Find;

use Hoyvoy\Currencies\Domain\Currency;
use Hoyvoy\Currencies\Domain\CurrencyRepository;
use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyCode;

class CurrencyFinder
{
    public function __construct(
        private readonly CurrencyRepository $repository,
    )
    {

    }

    public function __invoke(CurrencyCode $code): Currency
    {
        return $this->repository->find($code);
    }

}
