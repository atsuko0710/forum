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

// Route::resource('threads','ThreadsController');
Route::get('threads','ThreadsController@index');
Route::get('threads/create','ThreadsController@create');
Route::get('threads/{channel}/{thread}','ThreadsController@show');
Route::delete('threads/{channel}/{thread}','ThreadsController@destroy');
Route::post('threads','ThreadsController@store');
Route::get('threads/{channel}','ThreadsController@index');

Route::post('/threads/{channel}/{thread}/reply', 'ReplyController@store');

Route::delete('/replies/{reply}','ReplyController@destroy');
Route::post('replies/{reply}/favorite', 'FavoritesController@store');

Route::get('profile/{user}', 'ProfilesController@show')->name('profile');
