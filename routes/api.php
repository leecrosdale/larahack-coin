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



Route::get('transactions/pending', function() {
    return \App\Transaction::where('status', 0)->get();
});

Route::get('block/latest', function() {
    return \App\Block::all()->last();
});

Route::post('block/create', 'Api\BlockController@store');
