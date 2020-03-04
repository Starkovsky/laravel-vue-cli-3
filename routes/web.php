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

// For admin application
Route::get('/admin{any}', 'FrontendController@admin')->where('any', '.*');
// For public application
Route::any('/{any}', 'FrontendController@app')->where('any', '^(?!api).*$');
