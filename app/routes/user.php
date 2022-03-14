<?php

use App\Http\Controllers\User\Auth\EditUserInformationController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\DeleteController;
use App\Http\Controllers\User\FileUploadController;
use App\Http\Controllers\User\FileDownloadController;
use App\Http\Controllers\User\ListController;
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

    Route::get('/uploaded/{key}', [FileDownloadController::class, 'showFiles'])
        ->name('show.files');

    Route::get('/create/download', [FileDownloadController::class, 'showCreateForm'])
        ->name('create.download');

    Route::post('/create/download', [FileDownloadController::class, 'showCreateForm'])
        ->name('create.download');

    Route::post('/create/download/check', [FileDownloadController::class, 'checkBeforeCreateLink'])
        ->name('create.download.check');

    Route::post('/create/download/link', [FileDownloadController::class, 'createLink'])
        ->name('create.download.link');

    Route::post('/delete/file', [DeleteController::class, 'deleteFile'])
        ->name('delete.file');

    Route::post('/delete/upload', [DeleteController::class, 'deleteUploadLink'])
        ->name('delete.upload');

    Route::post('/delete/download', [DeleteController::class, 'deleteDownloadLink'])
        ->name('delete.download');

    Route::get('/list', [ListController::class, 'index'])
        ->name('list');

    Route::post('/list/register', [ListController::class, 'register'])
        ->name('list.register');

    Route::post('/list/search', [ListController::class, 'search'])
        ->name('list.search');
});

Route::get('/upload/{key}', [FileUploadController::class, 'showUploadForm'])
    ->name('upload');

Route::post('/upload/{key}', [FileUploadController::class, 'uploadFiles'])
    ->name('upload');

Route::get('/download/{key}', [FileDownloadController::class, 'showDownload'])
    ->name('download');

Route::post('/download/file', [FileDownloadController::class, 'downloadFile'])
    ->name('file.download');

require __DIR__ . '/auth.php';
