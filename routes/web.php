<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('app/login');
});

// Route::get('/test', function() {
//     return view('links.password-protected');
// });

Route::get('/{shortId}', [LinkController::class, 'redirect']);
