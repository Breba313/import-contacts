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
    return Redirect::route('login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//  Rutas para manejo de archivos
Route::post('load_file', 'FilesController@UploadFile')->name('load_file');
Route::get('import/{id_file}', 'FilesController@importFile')->name('import');
Route::get('delete_file/{id_file}', 'FilesController@deleteFile')->name('delete_file');
