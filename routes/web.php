<?php

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

Route::get('/', function () {
    return view('main');
})->name('main');

Route::resource('urls', \App\Http\Controllers\UrlController::class);

Route::post(
    '/urls/{id}/checks',
    [\App\Http\Controllers\UrlController::class, 'check']
)->name('urlChecks.store');
