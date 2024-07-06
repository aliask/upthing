<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
    Route::get('webhooks/{hookid}/delete', 'WebhooksController@delete')->name('webhooks.delete');
    Route::get('webhooks/serverdelete/{hookid}', 'WebhooksController@serverdelete')->name('webhooks.serverdelete');
    Route::delete('webhooks/serverdelete/{hookid}', 'WebhooksController@serverdestroy')->name('webhooks.serverdestroy');
    Route::get('webhooks/{hookid}/ping', 'WebhooksController@ping')->name('webhooks.ping');
});
Route::post('hook/{user}/{hookid}', 'WebhooksController@handle')->name('webhooks.handle');

Auth::routes(['register' => true, 'reset' => false, 'confirm' => false]);