<?php

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

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', 'IndexController@index')->name('welcome');

    Auth::routes(['register' => false]);

    Route::get('auth/{provider}/login', 'Auth\SocialiteController@login')->name('auth.social.login');
    Route::get('auth/{provider}/callback', 'Auth\SocialiteController@callback')->name('auth.social.callback');
    Route::post('auth/{provider}/register', 'Auth\SocialiteController@register')->name('auth.social.register');
});

Route::group([/*'middleware' => ['auth']*/], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    Route::get('/configuration', 'ConfigurationController@show')->name('configuration.show');
    Route::put('/configuration', 'ConfigurationController@update')->name('configuration.update');

    // Route::resource('/system_logs', 'SystemLogController')->only(['index', 'show']);
    // Route::resource('/owner_logs', 'OwnerLogController')->only(['index', 'show']);
    // Route::resource('/engagements', 'EngagementController')->only(['index']);
});
