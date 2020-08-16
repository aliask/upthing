<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhooksController;
use App\WebhookEndpoint;
use Illuminate\Support\Facades\Http;

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

Route::middleware('auth')->group(function() {
    Route::resource('accounts', 'AccountsController');
    Route::resource('webhooks', 'WebhooksController');
    Route::get('webhooks/{hookid}/ping', 'WebhooksController@ping')->name('webhooks.ping');
});
Route::post('hook/{user}/{hookid}', 'WebhooksController@handle')->name('webhooks.handle');

Auth::routes(['register' => true, 'reset' => false, 'confirm' => false]);