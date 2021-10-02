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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('calendar')->middleware('auth')->group(function () {
    Route::put('/add', 'CalendarController@add');
    Route::patch('/edit', 'CalendarController@edit');
    Route::delete('/delete', 'CalendarController@delete');
    Route::post('/list', 'CalendarController@list');
});