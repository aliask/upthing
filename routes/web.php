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
    return redirect(route('accounts.index'));
});

Route::resource('accounts', 'AccountsController');
Route::resource('webhooks', 'WebhooksController');

Auth::routes(['register' => true, 'reset' => false, 'confirm' => false]);