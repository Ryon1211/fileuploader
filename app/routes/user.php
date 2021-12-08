<?php

use App\Http\Controllers\User\Auth\EditUserInformationController;
use App\Http\Controllers\User\FileUploadController;
use App\Http\Controllers\User\DashboardController;
use Illuminate\Support\Facades\Auth;
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

Route::group(['middleware' => 'auth:users'], function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/account/{id}', [EditUserInformationController::class, 'show'])
        ->name('account');

    Route::post('/account/{id}', [EditUserInformationController::class, 'edit'])
        ->name('account');

    Route::get('/create/upload', [FileUploadController::class, 'showCreateForm'])
        ->name('create.upload');

    Route::post('/create/upload', [FileUploadController::class, 'createLink'])
        ->name('create.upload');
});

Route::get('/upload/{key}', [FileUploadController::class, 'showUploadForm'])
    ->name('upload');

Route::post('/upload/{key}', [FileUploadController::class, 'uploadFiles'])
    ->name('upload');

require __DIR__ . '/auth.php';
