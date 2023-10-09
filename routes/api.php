<?php

use App\Http\Controllers\Example\ExampleGetController;
use Illuminate\Support\Facades\Route;


Route::get('/example', ExampleGetController::class);
