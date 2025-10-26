<?php

use App\Http\Controllers\Currencies\GetCurrenciesController;
use App\Http\Controllers\Currencies\GetCurrencyController;
use App\Http\Controllers\Currencies\GetRateConversionController;
use App\Http\Controllers\Currencies\PutCurrencyController;
use Illuminate\Support\Facades\Route;


Route::name('currencies')->prefix('currencies')->group(function () {
    Route::get('/', GetCurrenciesController::class);

    Route::get('/rate-conversion', GetRateConversionController::class);

    Route::name('.currency')->prefix('{currencyCode}')->group(function () {
        Route::get('/', GetCurrencyController::class);
        Route::put('/', PutCurrencyController::class);
    });
});
