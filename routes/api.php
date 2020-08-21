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

// 伺服器測試
Route::get('/connection', 'ConnectionController@index');

// 自動同步到南投測試機
Route::get('/sync', 'ConnectionController@sync2csdi');

// 測試簡訊
Route::get('/sms', 'ConnectionController@sms');


// 測試APP問卷介接
Route::get('/sunnet_wholeans', 'ConnectionController@sunnet_wholeans'); //研習問卷資料
Route::get('/sunnet_profans', 'ConnectionController@sunnet_profans');//講座問卷資料
Route::get('/sunnet_sign', 'ConnectionController@sunnet_sign'); //簽到資料


//測試福華介接
Route::get('/BQTRANS_Taipei/usp_out_site', 'Schedule\BQTRANS_Controller@usp_out_site'); //匯出場地預約資料
Route::get('/BQTRANS_Taipei/usp_in_site', 'Schedule\BQTRANS_Controller@usp_in_site'); //匯入地預約資料
Route::get('/BQTRANS_Taipei/usp_in_punch', 'Schedule\BQTRANS_Controller@usp_in_punch'); //匯入學員刷卡資料
Route::get('/BQTRANS_Taipei/usp_out_affirm', 'Schedule\BQTRANS_Controller@usp_out_affirm'); //匯出辦班需求確認資料
Route::get('/BQTRANS_Taipei/usp_out_pupil', 'Schedule\BQTRANS_Controller@usp_out_pupil'); //匯出班別學員資料
Route::get('/BQTRANS_Taipei/usp_in_punch_dim', 'Schedule\BQTRANS_Controller@usp_in_punch_dim'); //匯入學員刷卡資料 (南投)
Route::get('/BQTRANS_Taipei/usp_out_pupil_dim', 'Schedule\BQTRANS_Controller@usp_out_pupil_dim'); //匯出班別學員資料(南投)
Route::get('/BQTRANS_Taipei/usp_xml', 'Schedule\BQTRANS_Controller@usp_xml'); //匯出場地預約資料
Route::get('/BQTRANS_Taipei/usp_xml_rad', 'Schedule\BQTRANS_Controller@usp_xml_rad'); //匯出場地預約資料