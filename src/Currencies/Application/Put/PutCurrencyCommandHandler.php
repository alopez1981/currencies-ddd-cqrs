<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Application\Put;

use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyCode;
use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyName;
use Hoyvoy\Currencies\Domain\ValueObjects\CurrencyRate;
use Hoyvoy\Shared\Domain\Bus\Command\CommandHandler;

/**
 * @method invoke()
 */
final class PutCurrencyCommandHandler implements CommandHandler
{
    public CurrencyPut $updater;

    public function __construct(CurrencyPut $updater)
    {
        $this->updater = $updater;
    }

    public function __invoke(PutCurrencyCommand $command): void
    {
        $code = CurrencyCode::fromValue($command->code);
        $name = CurrencyName::fromValue($command->name);
        $rate = CurrencyRate::fromValue($command->rate);

        ($this->updater)($code, $name, $rate);
    }
}
