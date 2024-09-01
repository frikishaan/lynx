<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

Route::domain(config('app.url'))->group(function () {
    Route::get('/', function () {
        return redirect('app/login');
    });
});
Route::get('/test', function() {
    return request()->getHost();
});
Route::get('/{shortId}', [LinkController::class, 'redirect']);
