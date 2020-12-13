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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*All Event related routes configured here*/
Route::get('/getAllEvents', 'EventController@getAllEvents');
Route::get('/getAllPromoCodes', 'EventController@getAllPromoCodes');
Route::get('/getActivePromoCodes', 'EventController@getActivePromoCodes');
Route::post('/createEvent', 'EventController@createEvent');
Route::post('/updateEvent/{event_id}', 'EventController@updateEvent');
Route::get('/checkPromoCode', 'EventController@checkPromoCode');

