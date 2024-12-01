<?php

use App\Http\Controllers\RedirectController;
use App\Http\Middleware\CustomDomain;
use Illuminate\Support\Facades\Route;

Route::domain(config('app.url'))->group(function () {
    Route::get('/', function () {
        return redirect('app/login');
    });
});

Route::get('/{shortId}', RedirectController::class)
    ->middleware(CustomDomain::class);
