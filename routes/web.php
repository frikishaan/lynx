<?php

use App\Http\Controllers\LinkController;
use App\Http\Middleware\CustomDomain;
use Illuminate\Support\Facades\Route;

Route::domain(config('app.url'))->group(function () {
    Route::get('/', function () {
        return redirect('app/login');
    });
});

Route::get('/{shortId}', LinkController::class)
    ->middleware(CustomDomain::class);
