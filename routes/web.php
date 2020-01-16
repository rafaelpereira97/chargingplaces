<?php

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

Route::get('/', 'FrontendController@index')->name('index');
Route::post('/addPlace', 'FrontendController@addPlace')->name('addplace');
Route::post('/nearPlaces', 'FrontendController@getNearbyChargers')->name('nearplaces');
Route::post('/createPolygon', 'FrontendController@createPolygon')->name('createPolygon');


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
