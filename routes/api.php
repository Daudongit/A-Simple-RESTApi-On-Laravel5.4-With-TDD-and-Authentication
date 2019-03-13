<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'api'], function () {

    Route::post('/login', 'AuthenticateController@authenticate')->name('api.login');
    Route::post('/signup', 'AuthenticateController@register')->name('api.signup');
});

Route::group(['middleware' => ['api','jwt.auth']],function (){
    Route::resource('/countries','CountryController',['except'=>['show','edit','create']]);
    Route::get('/activities','ActivityController@index')->name('activities.index');
});