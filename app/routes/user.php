<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\EditUserInformationController;

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

Route::group(['middleware' => 'auth:users'], function () {
    Route::get('/', function () {
        return view('user.dashboard');
    })->name('dashboard');

    Route::get('/account/{id}', [EditUserInformationController::class, 'show'])->name('account');

    Route::post('/account/{id}', [EditUserInformationController::class, 'edit'])->name('account');
});
require __DIR__ . '/auth.php';
