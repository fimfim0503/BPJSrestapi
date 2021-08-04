<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/daftarproduk', 'ProdukController@index');
$router->post('/akses', 'AksesvclaimController@akses');
$router->post('/produk', 'ProdukController@create');

$router->post('/register', 'UserController@register');
$router->get('/login', 'UserController@login');


Route::post('getnoantrian', 'GetNoAntrian@Antrianbpjs');
Route::post('statusantrian', 'StatusAntrian@statusantrian');
Route::post('sisaantrian', 'StatusAntrian@sisaantrian');
Route::post('batalantrian', 'StatusAntrian@batalantrian');
Route::post('checkin', 'StatusAntrian@checkin');
Route::post('getlistjadwaloperasi', 'StatusAntrian@Listjadwaoperasi');
//Route::post('getrekapantrian', 'Rekapantrian@rekapantrian');


