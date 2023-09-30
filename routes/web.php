<?php

use System\Router\Web\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. Make something great!
*/

Route::get('/', 'HomeController@index', 'index');
Route::get('/create', 'HomeController@create', 'create');
Route::post('/store', 'HomeController@store', 'store');
Route::get('/edit/{id}', 'HomeController@edit', 'edit');
Route::put('/update/{id}', 'HomeController@update', 'update');
Route::delete('/delete/{id}', 'HomeController@destroy', 'destroy');