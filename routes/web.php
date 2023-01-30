<?php

use App\Http\Middleware\RestrictedMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('')
    // Site-wide forbidden access to banned users.
    // Yes that means you cannot even log out. That's completely intended.
    ->middleware(RestrictedMiddleware::class)
    ->group(function () {

    Route::group([], __DIR__ . '/web/V1/V1.php');
});
