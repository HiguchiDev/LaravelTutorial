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

//Route::get('book', 'BookController@index');
Route::resource('shopSearch', 'ShopSearchController');
Route::get('/', function () {
    return view('welcome');
});
Route::get('shopImageURL', 'shopImageURLController@index');
Route::get('/shopSearch/distance/ajax', 'ShopSearchController@getDistance');
Route::get('/shopSearch/getShopImage/ajax', 'ShopSearchController@getShopImage');