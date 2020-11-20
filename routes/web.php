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
    $data = session();
    return json_encode($data);
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/threads', 'ThreadsController@index');
Route::get('/thread/{thread}', 'ThreadsController@show');
Route::post('/thread', 'ThreadsController@store');

Route::post('/thread/{thread}/reply', 'ReplyController@store');

