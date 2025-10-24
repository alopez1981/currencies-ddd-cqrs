<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class CurrencyRateHistoryModel extends Model
{
    protected $table = 'currency_rate_histories';

    protected $fillable = [
        'currency_code',
        'fetched_at',
        'rate_usd',
        'provider',
    ];

    protected $casts = [
        'rate_usd' => 'float',
        'fetched_at' => 'datetime',
    ];

    public $timestamps = true;
}
