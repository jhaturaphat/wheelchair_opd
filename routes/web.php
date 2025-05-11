<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

//Route for normal user
Route::group(['middleware' => ['auth']], function () {
    Route::get('/select_depart', 'CribbookingConrtoller@select_depart')->name('select_depart');
    Route::get('/Cribbooking', 'CribbookingConrtoller@Cribbooking')->name('Cribbooking');
    Route::get('/Showdata', 'CribbookingConrtoller@Showdata')->name('Showdata');
    Route::get('/showsucc', 'CribbookingConrtoller@showsucc')->name('showsucc');
});


//Route for admin
Route::group(['middleware' => ['admin']], function(){
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/select_service', 'AdminController@select_service')->name('select_service');
    Route::get('/drive_refer_from', 'AdminController@drive_refer_from')->name('drive_refer_from');
    Route::get('/drive_refer_tables', 'AdminController@drive_refer_tables')->name('drive_refer_tables');
    Route::get('/drive_refer_sum', 'AdminController@drive_refer_sum')->name('drive_refer_sum');
    Route::get('/tables_ssn', 'AdminController@tables_ssn')->name('tables_ssn');
    Route::get('/tables', 'AdminController@tables')->name('tables');
    Route::get('/summary', 'AdminController@summary')->name('summary');
    Route::get('/otherform', 'AdminController@otherform')->name('otherform');
    // Telegram
    Route::get('/telegram', 'TelegramController@getUpdateLast')->name('telegram');

});

Route::post('search_hn', 'AdminController@search_hn');
Route::post('depart_', 'CribbookingConrtoller@depart_')->name('depart_');
Route::post('depart_admin', 'AdminController@depart_admin')->name('depart_admin');
Route::get('test_table','AdminController@test_table');
Route::post('send_drive', 'AdminController@send_drive')->name('send_drive');

Route::post('search_1', 'AdminController@search_1');
Route::post('search_2', 'AdminController@search_2');


Route::post('save','CribbookingConrtoller@save');
Route::post('sent_to','CribbookingConrtoller@sent_to');
Route::get('notify','NotifyController@notify');
Route::post('save_token','NotifyController@save_token');

Route::post('dataTable/booking','AdminController@dataBooking');
Route::post('dataTable/data_custom','AdminController@data_custom');
Route::post('dataTable/data_ssn','AdminController@data_ssn');
Route::post('dataTable/data_sum','AdminController@data_sum');
Route::post('dataTable/booking2','CribbookingConrtoller@dataBooking');
Route::post('dataTable/booking_succ','CribbookingConrtoller@dataBooking_succ');
Route::post('dataTable/booking3','AdminController@dataBooking');
Route::post('refer_tables','AdminController@refer_tables');
Route::post('data_drive_sum','AdminController@data_drive_sum');
Route::post('search_3','AdminController@search_3');

Route::post('confirm', 'AdminController@conFirm');
Route::post('confirm_edit', 'AdminController@confirm_edit');
Route::post('confirm2', 'CribbookingConrtoller@conFirm');
Route::post('confirm3', 'CribbookingConrtoller@conFirm2');
Route::post('conFirm_drive', 'AdminController@conFirm_drive');

Route::post('soft_delete', 'AdminController@softDelete');
Route::post('soft_delete2', 'CribbookingConrtoller@softDelete');

Auth::routes();
Route::post('countBook','AdminController@count');
Route::post('countBook_wait','AdminController@count_wait');
Route::post('countBook_succ','AdminController@count_succ');
Route::post('countBook_sum','AdminController@count_sum');

Route::post('countBook2','CribbookingConrtoller@count');
Route::post('countBook_wait2','CribbookingConrtoller@count_wait');
Route::post('countBook_succ2','CribbookingConrtoller@count_succ');

Route::get('getEdit','CribbookingConrtoller@edit');




