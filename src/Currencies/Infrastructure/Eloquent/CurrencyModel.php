<?php

declare(strict_types=1);

namespace Hoyvoy\Currencies\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class CurrencyModel extends Model
{
    protected $table = 'currencies';
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    public $incrementing = false;

    public $timestamps = false;

    public $casts = [
        'rate' => 'float',
    ];
    protected $fillable = ['code', 'name', 'rate'];
}
