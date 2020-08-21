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
    Route::group(['namespace' => 'Front' ], function() {
        // 首頁
        Route::get('/', function () {
            return redirect('/admin/login');
        });
    });

    //recaptcha google
    Route::post('/post', function (\Illuminate\Http\Request $request) {
        $client = new \GuzzleHttp\Client();
        $result = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => '6LddJsAUAAAAAJS360OY3oPeieF8mitkKejpDvLw',
                'response' => $request->get('g-recaptcha-response')
            ]
        ]);
        $result = json_decode($result->getBody(), true);
        if (isset($result['success']) && $result['success']) {
            return '驗證成功';
        } else {
            return '驗證失敗';
        }
    });

    // 匯出
    Route::group(['middleware' => ['auth:managers'], 'prefix' => 'export', 'namespace' => 'Export' ], function() {

        // 國定假日
        Route::get('holiday', 'HolidayController@index');
    });


    // 後台登入頁
    Route::get('admin/login', 'Auth\ManagerLoginController@showLoginForm');
    // 後台登入檢查頁
    Route::post('admin/login', 'Auth\ManagerLoginController@login');
    // 後台登出
    Route::get('admin/logout', 'Auth\ManagerLoginController@logout');

    // 後台
    Route::group(['middleware' => ['auth:managers'], 'prefix' => 'admin', 'namespace' => 'Admin' ], function() {

    // 後台首頁
    Route::get('home', 'HomeController@index');
    Route::get('/', 'HomeController@index');
    Route::get('goToTrain', 'HomeController@goToTrain');

    // Profile
    Route::get('profile', 'ProfileController@edit');
    Route::put('profile', 'ProfileController@update');
    // 圖片上傳
    Route::post('upload/image/{channel}', 'UploadImageController@upload');
    // 檔案上傳
    Route::post('upload/file/{channel}', 'UploadFileController@upload');

    // 權限群組維護
    Route::get('user_group', 'User_groupController@index');
    Route::get('user_group/create', 'User_groupController@create');
    Route::post('user_group/', 'User_groupController@store');
    Route::get('user_group/{id}', 'User_groupController@show');
    Route::get('user_group/{id}/edit', 'User_groupController@edit');
    Route::put('user_group/{id}', 'User_groupController@update');
    Route::delete('user_group/{id}', 'User_groupController@destroy');
    Route::delete('user_group/{id}/from', 'User_groupController@destroy_from');

    // 系統帳號維護
    Route::get('system_account', 'System_accountController@index');
    Route::get('system_account/create', 'System_accountController@create');
    Route::post('system_account/', 'System_accountController@store');
    Route::get('system_account/{id}', 'System_accountController@show');
    Route::get('system_account/{id}/edit', 'System_accountController@edit');
    Route::put('system_account/{id}', 'System_accountController@update');
    Route::delete('system_account/{id}', 'System_accountController@destroy');
    Route::delete('system_account/{id}/from', 'System_accountController@destroy_from');

    // 系統管理(管理員)
    Route::get('recommend_user', 'RecommendUserController@index');
    Route::get('recommend_user/active', 'RecommendUserController@active');
    Route::get('recommend_user/print', 'RecommendUserController@print');
    Route::get('recommend_user/create', 'RecommendUserController@create');
    Route::post('recommend_user/', 'RecommendUserController@store');
    Route::get('recommend_user/{id}/{num}', 'RecommendUserController@show');
    Route::get('recommend_user/{id}/{num}/edit', 'RecommendUserController@edit');
    Route::put('recommend_user/{id}/{num}', 'RecommendUserController@update');
    Route::delete('recommend_user/{id}/{num}', 'RecommendUserController@destroy');

    // 國定假日
    Route::get('holiday', 'HolidayController@index');
    Route::get('holiday/create', 'HolidayController@create');
    Route::post('holiday/', 'HolidayController@store');
    Route::get('holiday/{id}', 'HolidayController@show');
    Route::get('holiday/{id}/edit', 'HolidayController@edit');
    Route::put('holiday/{id}', 'HolidayController@update');
    Route::delete('holiday/{id}', 'HolidayController@destroy');

    // 異動紀錄設定
    Route::get('program', 'ProgramController@index');
    Route::get('program/create', 'ProgramController@create');
    Route::post('program/', 'ProgramController@store');
    Route::get('program/{id}', 'ProgramController@show');
    Route::get('program/{id}/edit', 'ProgramController@edit');
    Route::put('program/{id}', 'ProgramController@update');
    Route::delete('program/{id}', 'ProgramController@destroy');

    //異動紀錄查詢
    Route::get('program_search', 'ProgramSearchController@index');
    Route::get('program_search/create', 'ProgramSearchController@create');
    Route::post('program_search/', 'ProgramSearchController@store');
    Route::get('program_search/{id}', 'ProgramSearchController@show');
    Route::get('program_search/{id}/edit', 'ProgramSearchController@edit');
    Route::put('program_search/{id}', 'ProgramSearchController@update');
    Route::delete('program_search/{id}', 'ProgramSearchController@destroy');

    //訓練機構資料維護
    Route::get('agency', 'AgencyController@index');
    Route::get('agency/create', 'AgencyController@create');
    Route::post('agency/', 'AgencyController@store');
    Route::get('agency/{id}', 'AgencyController@show');
    Route::get('agency/{id}/edit', 'AgencyController@edit');
    Route::put('agency/{id}', 'AgencyController@update');
    Route::delete('agency/{id}', 'AgencyController@destroy');
    Route::post('agency/getenrollname', 'AgencyController@getenrollname');

    // 場地資料
    Route::get('place', 'PlaceController@index');
    Route::get('place/create', 'PlaceController@create');
    Route::post('place/', 'PlaceController@store');
    Route::get('place/{id}', 'PlaceController@show');
    Route::get('place/{id}/edit', 'PlaceController@edit');
    Route::put('place/{id}', 'PlaceController@update');
    Route::delete('place/{id}', 'PlaceController@destroy');

    // 機關資料
    Route::get('institution', 'InstitutionController@index');
    Route::get('institution/create', 'InstitutionController@create');
    Route::post('institution/', 'InstitutionController@store');
    Route::get('institution/{id}', 'InstitutionController@show');
    Route::get('institution/{id}/edit', 'InstitutionController@edit');
    Route::put('institution/{id}', 'InstitutionController@update');
    Route::delete('institution/{id}', 'InstitutionController@destroy');

    // 薦送機關維護
    Route::get('recommend', 'RecommendController@index');
    Route::get('recommend/create', 'RecommendController@create');
    Route::post('recommend/', 'RecommendController@store');
    Route::get('recommend/{id}', 'RecommendController@show');
    Route::get('recommend/{id}/edit', 'RecommendController@edit');
    Route::put('recommend/{id}', 'RecommendController@update');
    Route::delete('recommend/{id}', 'RecommendController@destroy');

    // 個人帳號
    Route::get('users', 'UsersController@index');
    Route::get('users/create', 'UsersController@create');
    Route::post('users/', 'UsersController@store');
    Route::get('users/{id}', 'UsersController@show');
    Route::get('users/{id}/edit', 'UsersController@edit');
    Route::put('users/{id}', 'UsersController@update');
    Route::delete('users/{id}', 'UsersController@destroy');

    // 系統代碼
    Route::get('system_code', 'SystemCodeController@index');
    Route::get('system_code/create', 'SystemCodeController@create');
    Route::post('system_code/', 'SystemCodeController@store');
    Route::get('system_code/{id}/{code}', 'SystemCodeController@show');
    Route::get('system_code/{id}/{code}/edit', 'SystemCodeController@edit');
    Route::put('system_code/{id}/{code}', 'SystemCodeController@update');
    Route::delete('system_code/{id}/{code}', 'SystemCodeController@destroy');

    // 系統參數維護
    Route::get('system_parameter/edit', 'SystemParameterController@edit');
    Route::put('system_parameter', 'SystemParameterController@update');

    // 入口網站代碼維護
    Route::get('web_portal', 'WebPortalController@index');
    Route::get('web_portal/edit/{code}', 'WebPortalController@edit'); //代碼串聯
    Route::post('web_portal/edit/{code}', 'WebPortalController@update');
    Route::get('web_portal/category', 'WebPortalController@category'); //班別類別
    Route::post('web_portal/category', 'WebPortalController@store');
    Route::put('web_portal/category/{code}', 'WebPortalController@categoryupdate');
    Route::delete('web_portal/category/{code}', 'WebPortalController@destroy');
    Route::put('web_portal/rank', 'WebPortalController@rank');//排序更新

    // 班別資料
    Route::get('classes', 'ClassesController@index');
    Route::get('classes/create', 'ClassesController@create');
    Route::post('classes/getOrgchk', 'ClassesController@getOrgchk');
    Route::post('classes/getSameCourseList', 'ClassesController@getSameCourseList');
    Route::post('classes/', 'ClassesController@store');
    Route::get('classes/classimport', 'ClassesController@classimport');
    Route::get('classes/ClassOutput', 'ClassesController@ClassOutput');
    Route::post('classes/crossOrg', 'ClassesController@crossOrg');
    Route::get('classes/exportclass', 'ClassesController@exportclass');

    Route::put('classes/rank', 'ClassesController@rank');//排序更新
    Route::get('classes/batch', 'ClassesController@batch');
    Route::post('classes/batchimport', 'ClassesController@batchimport');//匯入
    Route::post('classes/batchadd', 'ClassesController@batchadd');
    Route::get('classes/batchdel', 'ClassesController@batchdel');
    Route::get('classes/showCrossArea', 'ClassesController@showCrossArea');
    Route::get('classes/{id}', 'ClassesController@show');
    Route::get('classes/{id}/edit', 'ClassesController@edit');
    Route::put('classes/{id}', 'ClassesController@update');
    Route::delete('classes/{id}', 'ClassesController@destroy');




    // 需求調查
    Route::get('demand_survey', 'DemandSurveyController@index');
    Route::get('demand_survey/create', 'DemandSurveyController@create');
    Route::get('demand_survey/gettimes', 'DemandSurveyController@gettimes');//ajax
    Route::get('demand_survey/form2', 'DemandSurveyController@form2');
    Route::post('demand_survey/form2/getDemandSurveyData', 'DemandSurveyController@getDemandSurveyData');
    Route::get('demand_survey/bulletin_board', 'DemandSurveyController@bulletin_board');
    Route::put('demand_survey/bulletin_board', 'DemandSurveyController@bulletin_edit');
    Route::post('demand_survey/', 'DemandSurveyController@store');
    Route::post('demand_survey/importdata', 'DemandSurveyController@importdata');
    Route::post('demand_survey/printdata', 'DemandSurveyController@printdata');
    Route::post('demand_survey/canceldata', 'DemandSurveyController@canceldata');
    Route::get('demand_survey/demanddata', 'DemandSurveyController@demanddata');
    Route::post('demand_survey/resetdata', 'DemandSurveyController@resetdata');
    Route::get('demand_survey/{id}', 'DemandSurveyController@show');
    Route::get('demand_survey/{id}/edit', 'DemandSurveyController@edit');
    Route::put('demand_survey/{id}', 'DemandSurveyController@update');
    Route::delete('demand_survey/{id}', 'DemandSurveyController@destroy');

    // 需求分配
    Route::get('demand_distribution', 'DemandDistributionController@index');
    Route::post('demand_distribution/demand_orga', 'DemandDistributionController@demand_orga');
    Route::post('demand_distribution/demand_classes', 'DemandDistributionController@demand_classes');
    Route::get('demand_distribution/getterm', 'DemandDistributionController@getterm');
    Route::get('demand_distribution/{id}', 'DemandDistributionController@show');
    Route::get('demand_distribution/{id}/edit', 'DemandDistributionController@edit');
    Route::get('demand_distribution/{id}/edit2', 'DemandDistributionController@edit2');
    Route::put('demand_distribution/{id}', 'DemandDistributionController@update');
    Route::put('demand_distribution/set_tune_quotatot/{class}', 'DemandDistributionController@update_tune_quotatot');//更改需求分配處理-機關已分配人數
    Route::post('demand_distribution/demand_orga_list_update', 'DemandDistributionController@demand_orga_list_update');//更改機關已分配人數 T02
   
  
  


    // 委訓班需求調查處理
    Route::get('demand_survey_commissioned', 'DemandSurveyCommissionedController@index');
    Route::get('demand_survey_commissioned/create', 'DemandSurveyCommissionedController@create');
    Route::post('demand_survey_commissioned/', 'DemandSurveyCommissionedController@store');
    Route::get('demand_survey_commissioned/{id}', 'DemandSurveyCommissionedController@show');
    Route::get('demand_survey_commissioned/{id}/edit', 'DemandSurveyCommissionedController@edit');
    Route::get('demand_survey_commissioned/{id}/audit_edit', 'DemandSurveyCommissionedController@audit_edit');
    Route::put('demand_survey_commissioned/audit_edit/{id}', 'DemandSurveyCommissionedController@audit_edit_update');
    Route::get('demand_survey_commissioned/{id}/view', 'DemandSurveyCommissionedController@view');
    Route::put('demand_survey_commissioned/{id}', 'DemandSurveyCommissionedController@update');
    Route::delete('demand_survey_commissioned/{id}', 'DemandSurveyCommissionedController@destroy');
    Route::get('demand_survey_commissioned/{id}/export_doc', 'DemandSurveyCommissionedController@export_doc');
    Route::get('demand_survey_commissioned/{id}/export_odf', 'DemandSurveyCommissionedController@export_odf');
    Route::get('demand_survey_commissioned/{id}/import', 'DemandSurveyCommissionedController@import');
    Route::post('demand_survey_commissioned/audit_accept', 'DemandSurveyCommissionedController@audit_accept');
    Route::post('demand_survey_commissioned/audit_reject', 'DemandSurveyCommissionedController@audit_reject');
    Route::post('demand_survey_commissioned/audit_ing', 'DemandSurveyCommissionedController@audit_ing');
    Route::put('demand_survey_commissioned/import_save/{id}', 'DemandSurveyCommissionedController@import_save');

    // 開班期數
    Route::get('periods', 'PeriodsController@index');
    Route::get('periods/checkAssignOtherOrgan', 'PeriodsController@checkAssignOtherOrgan');
    Route::get('periods/{id}', 'PeriodsController@show');
    Route::get('periods/{id}/edit', 'PeriodsController@edit');
    Route::put('periods/{id}', 'PeriodsController@update');
    Route::get('periods/action/online_update', 'PeriodsController@online_update');
    Route::post('periods/action/exec_online_update', 'PeriodsController@exec_online_update');
    Route::post('periods/action/assign', 'PeriodsController@assign');


    Route::get('classes_period/{id}', 'ClassesPeriodController@show');
    Route::get('classes_period/{id}/edit', 'ClassesPeriodController@edit');
    Route::put('classes_period/{id}', 'ClassesPeriodController@update');

   // 訓練排程處理
    Route::get('schedule', 'ScheduleController@index');
    Route::get('schedule/create', 'ScheduleController@create');
    Route::get('schedule/importExample', 'ScheduleController@importExample');

    Route::get('schedule/calendar', 'ScheduleController@calendar');
    Route::get('schedule/details', 'ScheduleController@details');
    Route::get('schedule/{id}', 'ScheduleController@show');
    Route::get('schedule/{class}/{term}/edit', 'ScheduleController@edit');
    Route::put('schedule/{class}/{term}', 'ScheduleController@update');
    Route::delete('schedule/{class}/{term}', 'ScheduleController@delete');
    Route::put('schedule/updateBydetail', 'ScheduleController@updateBydetail');
    Route::get('schedule/getTerms/{class}', 'ScheduleController@getTerms');


    Route::post('schedule/create', 'ScheduleController@store');
    Route::post('schedule/operate', 'ScheduleController@operate');
    Route::post('schedule/import', 'ScheduleController@import');

    //訓練績效
    Route::get('performance', 'PerformanceController@index');
    Route::get('performance/{id}/edit', 'PerformanceController@edit');
    Route::put('performance/{id}', 'PerformanceController@update');

    // 行事曆
    Route::get('calendar', 'CalendarController@index');
    Route::post('calendar/{class}/{term}', 'CalendarController@store');
    Route::put('calendar/{class}/{term}', 'CalendarController@update');
    Route::delete('calendar/{class}/{term}', 'CalendarController@delete');

    // Route::get('schedule/getClassInfo/{class}', 'ScheduleController@getClassInfo');
    Route::get('schedule/getClassInfo/{class}/{term}', 'ScheduleController@getClassInfo');

    // 重覆參訓檢核群組維護
    Route::get('class_group', 'ClassGroupController@index');
    Route::post('class_group', 'ClassGroupController@create');  //清單
    Route::get('class_group/edit/{id}', 'ClassGroupController@edit');  //修改頁
    Route::post('class_group/edit/{id}', 'ClassGroupController@store'); //新增
    Route::put('class_group/edit/{id}', 'ClassGroupController@update'); //修改
    Route::delete('class_group/edit/{id}', 'ClassGroupController@destroy'); //刪除


    // 會議資料處理
    Route::get('session', 'SessionController@index');
    Route::get('session/create', 'SessionController@create');
    Route::post('session/', 'SessionController@store');
    Route::get('session/{id}', 'SessionController@show');
    Route::get('session/{id}/edit', 'SessionController@edit');
    Route::put('session/{id}/edit', 'SessionController@update');
    Route::delete('session/{id}', 'SessionController@destroy');

    //場地預約處理
    Route::get("bookplace/index",'BookPlaceController@index');
    Route::post("bookplace/index",'BookPlaceController@index');
    Route::get("bookplace/getPlace/{type}",'BookPlaceController@getPlace'); //ajax
    Route::get("bookplace/addClassroom/{type}",'BookPlaceController@addClassroom');
    Route::post("bookplace/addClassroom",'BookPlaceController@addClassroom');
    Route::get("bookplace","BookPlaceController@form")->name("bookplace_form");
    Route::put("bookplace/{arr}","BookPlaceController@update")->where('arr','!=','batchVerify');
    Route::post("bookplace","BookPlaceController@store")->name('bookplace_post');
    Route::post("bookplace/getT22tbAjax","BookPlaceController@getT22tbAjax");
    Route::get("bookplace/setWeek/{arr}","BookPlaceController@setWeek");
    Route::get("bookplace/batchVerify","BookPlaceController@batchVerify");
    Route::put("bookplace/batchVerify","BookPlaceController@batchUpdate");
    Route::get("bookplace/setTime","BookPlaceController@setTime");
    Route::put("bookplace/seatType","BookPlaceController@seatUpdate")->name("seattype_update");

    //網路預約場地審核處理(南投)
    Route::get("webbookplace","WebBookPlaceController@index");
    Route::get("webbookplace/saveConfirmFee","WebBookPlaceController@saveConfirmFee");
    Route::get("webbookplace/webbookplaceNantou","WebBookPlaceController@webbookplaceNantou")->name("webbook.Nantou.get");
    Route::post("webbookplace/webbookplaceNantou","WebBookPlaceController@webbookplaceNantou")->name("webbook.Nantou.post");
    Route::put("webbookplace/updateApplyLimit","WebBookPlaceController@updateApplyLimit")->name("webbook.updateApplyLimit.put");
    Route::get("webbookplace/{key}","WebBookPlaceController@edit")->name("webbook.edit.get");
    Route::put("webbookplace/{key}","WebBookPlaceController@loanplace_store")->name("webbook.edit.put");
    Route::get("webbookplace/place/{key}","WebBookPlaceController@room_add")->name("webbook.place.get");
    Route::put("webbookplace/place/{id}","WebBookPlaceController@room_put")->name("webbook.place.put");
    Route::post("webbookplace/place/{key}","WebBookPlaceController@room_add")->name("webbook.place.post");
    Route::get("webbookplace/arg/set","WebBookPlaceController@arg_set")->name("webbook.arg.get");
    Route::post("webbookplace/arg/set","WebBookPlaceController@arg_set")->name("webbook.arg.post");
    //Route::put("webbookplace/arg/set/{id}","WebBookPlaceController@arg_modify")->name("webbook.arg.put");
    Route::get("webbookplace/arg/set/add","WebBookPlaceController@arg_add")->name("webbook.arg.add.get");
    Route::post("webbookplace/arg/set/add","WebBookPlaceController@arg_add")->name("webbook.arg.add.post");
    Route::get("webbookplace/arg/set/add/{id}","WebBookPlaceController@arg_modify")->name("webbook.arg.add.getid");
    Route::put("webbookplace/arg/set/add/{id}","WebBookPlaceController@arg_modify")->name("webbook.arg.add.putid");
    Route::get("webbookplace/bed/get","WebBookPlaceController@bed")->name("webbook.bed.get");
    Route::post("webbookplace/bed/post","WebBookPlaceController@bed_post")->name("webbook.bed.post");
    Route::get("webbookplace/sendemail/get/{email}","WebBookPlaceController@send_email")->name("webbook.email.get");
    Route::post("webbookplace/sendemail/get/{email}","WebBookPlaceController@send_email")->name("webbook.email.post");
    Route::get("webbookplace/parameter/set","WebBookPlaceController@parameter_set")->name("webbook.parameter.get");
    Route::get("webbookplace/parameter/add/{id?}","WebBookPlaceController@parameter_add")->name("webbook.parameter.add");
    Route::post("webbookplace/parameter/add","WebBookPlaceController@parameter_add")->name("webbook.parameter.post");
    Route::put("webbookplace/parameter/add/{id}","WebBookPlaceController@parameter_put")->name("webbook.parameter.put");
    Route::get("webbookplace/send_change/get","WebBookPlaceController@send_change")->name("webbook.change.get");
    Route::post("webbookplace/send_change_email/get","WebBookPlaceController@send_change_email")->name("webbook.change.email.get");
    Route::post("webbookplace/send_change_email/post","WebBookPlaceController@send_change_email_post")->name("webbook.change.email.post");
    //網路預約場地審核處理(台北)
    Route::get("webbookplaceTaipei","WebBookPlaceController@webbookplaceTaipei");
    Route::post("webbookplaceTaipei","WebBookPlaceController@webbookplaceTaipei");
    Route::get("webbookplaceTaipei/edit/{key}","WebBookPlaceController@Taipei_edit");
    Route::put("webbookplaceTaipei/edit/{key}","WebBookPlaceController@Taipei_loanplace_store");
    Route::put("webbookplaceTaipei/updateSite","WebBookPlaceController@updateSite");
    Route::delete("webbookplaceTaipei/deleteSite","WebBookPlaceController@deleteSite");
    Route::put("webbookplaceTaipei/createSite","WebBookPlaceController@createSite");
    Route::put("webbookplaceTaipei/reply","WebBookPlaceController@reply");
    Route::put("webbookplaceTaipei/audit","WebBookPlaceController@audit");
    Route::get('webbookplaceTaipei/edit/{key}/apply_doc', 'WebBookPlaceController@apply_doc');
    Route::get('webbookplaceTaipei/edit/{key}/export_doc', 'WebBookPlaceController@export_doc');
    
    //場地收費(南投院區)
    Route::get("space_charges","SpaceChargesController@index");
    Route::get('space_charges/ChargesSub1/{applyno}', 'SpaceChargesController@ChargesSub1');
    Route::get('space_charges/ChargesSub3/{applyno}/{croomclsno}', 'SpaceChargesController@ChargesSub3');
    Route::get('space_charges/ChargesSub4/{applyno}/{croomclsno}', 'SpaceChargesController@ChargesSub4');
    Route::get('space_charges/EditCharges/{applyno}', 'SpaceChargesController@EditCharges');
    Route::get('space_charges/PrintReceipt/{applyno}', 'SpaceChargesController@PrintReceipt');
    Route::put('space_charges/update', 'SpaceChargesController@update');

    //寢室床位安排(南投院區)
    Route::get("roomset","RoomSetController@index");
    Route::get("roomset/editRoomset/{class}/{term}","RoomSetController@editRoomset");
    Route::put('roomset/updateRoomset', 'RoomSetController@updateRoomset');
    Route::get('roomset/bedSet/{class}/{term}/{sex}', 'RoomSetController@bedSet');
    
    
    // 時段設定
    Route::get('time_setting', 'TimeSettingController@index');
    Route::put('time_setting', 'TimeSettingController@update'); //修改
    
    // 機關密碼維護
    Route::get('password_maintenance', 'PasswordMaintenanceController@index');
    Route::post('password_maintenance/act1', 'PasswordMaintenanceController@act1'); // 使用者設定
    Route::post('password_maintenance/act2', 'PasswordMaintenanceController@act2'); // 密碼重設
    Route::post('password_maintenance/act3', 'PasswordMaintenanceController@act3'); // 帳號重設

    // 個人密碼維護
    Route::get('password_maintenance_user', 'PasswordMaintenanceUserController@index');
    Route::post('password_maintenance_user/act1', 'PasswordMaintenanceUserController@act1'); // 使用者設定
    Route::post('password_maintenance_user/act2', 'PasswordMaintenanceUserController@act2'); // 密碼重設
    Route::post('password_maintenance_user/act3', 'PasswordMaintenanceUserController@act3'); // 帳號重設

    // 中文最新消息維護
    Route::get('news_tw', 'NewsTwController@index');
    Route::get('news_tw/create', 'NewsTwController@create');
    Route::post('news_tw/', 'NewsTwController@store');
    Route::get('news_tw/{id}', 'NewsTwController@show');
    Route::get('news_tw/{id}/edit', 'NewsTwController@edit');
    Route::put('news_tw/{id}', 'NewsTwController@update');
    Route::delete('news_tw/{id}', 'NewsTwController@destroy');

    // 英文最新消息維護
    Route::get('news_en', 'NewsEnController@index');
    Route::get('news_en/create', 'NewsEnController@create');
    Route::post('news_en/', 'NewsEnController@store');
    Route::get('news_en/{id}', 'NewsEnController@show');
    Route::get('news_en/{id}/edit', 'NewsEnController@edit');
    Route::put('news_en/{id}', 'NewsEnController@update');
    Route::delete('news_en/{id}', 'NewsEnController@destroy');

    // 訓練班期公告
    Route::get('train', 'TrainController@index');
    Route::post('train', 'TrainController@update');

    // 洽借場地班期公告
    Route::get('site', 'SiteController@index');
    Route::post('site/year', 'SiteController@year');
    Route::post('site/publish', 'SiteController@publish');
    Route::post('site/date', 'SiteController@date');

    // 人資發展論壇
    Route::get('forum', 'ForumController@index');
    Route::delete('forum/t33/{id}', 'ForumController@t33');
    Route::delete('forum/t34/{id}', 'ForumController@t34');

    // 網路民調維護
    Route::get('poll', 'PollController@index');
    Route::get('poll/create', 'PollController@create');
    Route::post('poll/', 'PollController@store');
    Route::get('poll/{id}', 'PollController@show');
    Route::get('poll/{id}/edit', 'PollController@edit');
    Route::put('poll/{id}', 'PollController@update');
    Route::delete('poll/{id}', 'PollController@destroy');

    // 講座擬聘處理
    Route::get('waiting', 'WaitingController@index');
    Route::get('waiting/create', 'WaitingController@create');
    Route::post('waiting/', 'WaitingController@store');
	Route::post('waiting/sms', 'WaitingController@sms');
	Route::post('waiting/sendsms', 'WaitingController@sendsms');
    Route::post('waiting/mark', 'WaitingController@mark');
    Route::post('waiting/getterm', 'WaitingController@getterm');
    Route::post('waiting/getcourse', 'WaitingController@getcourse');
    Route::post('waiting/changehire/{id}', 'WaitingController@changehire');
    Route::post('waiting/changepay/{id}', 'WaitingController@changepay');
    Route::post('waiting/changenotpay/{id}', 'WaitingController@changenotpay');
    Route::get('waiting/detail', 'WaitingController@detail');
    Route::get('waiting/{id}', 'WaitingController@show');
    Route::get('waiting/{id}/edit', 'WaitingController@edit');
    Route::put('waiting/{id}', 'WaitingController@update');
    Route::delete('waiting/{id}', 'WaitingController@destroy');

    // 鐘點費轉帳處理
    Route::get('transfer_processing', 'Transfer_processingController@index');
    Route::post('transfer_processing/transfer', 'Transfer_processingController@transfer');
    Route::post('transfer_processing/cancelTransfer', 'Transfer_processingController@cancelTransfer');
    Route::post('transfer_processing/frmFile', 'Transfer_processingController@frmFile');

    // 所得稅申報處理
    Route::get('tax_processing', 'Tax_processingController@index');
    Route::post('tax_processing/taxReturn', 'Tax_processingController@taxReturn');
    Route::post('tax_processing/frmFile', 'Tax_processingController@frmFile');

    // 講座用餐、住宿、派車資料登錄
    Route::get('teacher_related', 'Teacher_relatedController@index');
    Route::get('teacher_related/detail', 'Teacher_relatedController@detail');
    Route::post('teacher_related/changeConfirm/{id}', 'Teacher_relatedController@changehire');
    Route::get('teacher_related/{id}/edit1', 'Teacher_relatedController@edit1');
    Route::get('teacher_related/{id}/edit2', 'Teacher_relatedController@edit2');
    Route::get('teacher_related/{id}/edit3', 'Teacher_relatedController@edit3');
    Route::get('teacher_related/{id}/edit4', 'Teacher_relatedController@edit4');
    Route::put('teacher_related/{id}', 'Teacher_relatedController@update');
    Route::post('teacher_related/getLocation', 'Teacher_relatedController@getLocation'); // 取得班別

    // 講座接待管理
    Route::get('teacher_reception', 'Teacher_receptionController@index');
    Route::post('teacher_reception/', 'Teacher_receptionController@car_update');
    Route::get('teacher_reception/detail', 'Teacher_receptionController@detail');
    Route::post('teacher_reception/changeConfirm/{id}', 'Teacher_receptionController@changehire');
    Route::get('teacher_reception/{id}/edit1', 'Teacher_receptionController@edit1');
    Route::get('teacher_reception/{id}/edit2', 'Teacher_receptionController@edit2');
    Route::get('teacher_reception/{id}/edit3', 'Teacher_receptionController@edit3');
    Route::get('teacher_reception/{id}/edit4', 'Teacher_receptionController@edit4');
    Route::put('teacher_reception/{id}', 'Teacher_receptionController@update');
    Route::post('teacher_reception/getLocation', 'Teacher_receptionController@getLocation'); // 取得班別
    Route::get('teacher_reception/car_detail', 'Teacher_receptionController@car_detail');
    Route::get('teacher_reception/room_detail', 'Teacher_receptionController@room_detail');
    Route::get('teacher_reception/food_detail', 'Teacher_receptionController@food_detail');

    // 課程及講座查詢(滿意度)
    Route::get('satisfaction', 'SatisfactionController@index');
    Route::get('satisfaction/export', 'SatisfactionController@export');
    Route::get('satisfaction/export2', 'SatisfactionController@export2');
    Route::get('satisfaction/exportOdf', 'SatisfactionController@exportOdf');
    Route::get('satisfaction/exportOdf2', 'SatisfactionController@exportOdf2');

    // 講座服務參數維護
    Route::get('parameter_setting_1', 'Parameter_settingController@index1');
    Route::get('parameter_setting_1/create', 'Parameter_settingController@create1');
    Route::post('parameter_setting_1/', 'Parameter_settingController@store1');
    Route::get('parameter_setting_1/{id}', 'Parameter_settingController@show1');
    Route::get('parameter_setting_1/{id}/edit', 'Parameter_settingController@edit1');
    Route::put('parameter_setting_1/{id}', 'Parameter_settingController@update1');
    Route::delete('parameter_setting_1/{id}', 'Parameter_settingController@destroy1');
    Route::get('parameter_setting_2', 'Parameter_settingController@index2');
    Route::get('parameter_setting_2/create', 'Parameter_settingController@create2');
    Route::post('parameter_setting_2/', 'Parameter_settingController@store2');
    Route::get('parameter_setting_2/{id}', 'Parameter_settingController@show2');
    Route::get('parameter_setting_2/{id}/edit', 'Parameter_settingController@edit2');
    Route::put('parameter_setting_2/{id}', 'Parameter_settingController@update2');
    Route::delete('parameter_setting_2/{id}', 'Parameter_settingController@destroy2');
    Route::get('parameter_setting_3', 'Parameter_settingController@index3');
    Route::get('parameter_setting_3/create', 'Parameter_settingController@create3');
    Route::post('parameter_setting_3/', 'Parameter_settingController@store3');
    Route::get('parameter_setting_3/{id}', 'Parameter_settingController@show3');
    Route::get('parameter_setting_3/{id}/edit', 'Parameter_settingController@edit3');
    Route::put('parameter_setting_3/{id}', 'Parameter_settingController@update3');
    Route::delete('parameter_setting_3/{id}', 'Parameter_settingController@destroy3');

    // 講座資料維護
    Route::get('lecture', 'LectureController@index');
    Route::get('lecture/create', 'LectureController@specialty');
    Route::get('lecture/create', 'LectureController@create');
    Route::get('lecture/import', 'LectureController@batch_import');
    Route::post('lecture/import', 'LectureController@batch_import');
    Route::post('lecture/', 'LectureController@store');
    Route::get('lecture/{id}', 'LectureController@show');
    Route::get('lecture/{id}/edit', 'LectureController@edit');
    Route::get('lecture/{id}/change', 'LectureController@change');
    Route::put('lecture/{id}', 'LectureController@update');
    Route::delete('lecture/{id}', 'LectureController@destroy');
    Route::delete('lecture/{id}/from', 'LectureController@destroy_from');
    Route::post('lecture/getterm', 'LectureController@getTerm'); // 取得期別



    // 講座聘任處理
    Route::post('employ/getterm', 'EmployController@getTerm'); // 取得期別
    Route::post('employ/getcourse', 'EmployController@getCourse'); // 取得班別
    Route::post('employ/getlecthr', 'EmployController@getlecthr');
    Route::get('employ/getIdno', 'EmployController@getIdno'); // 取得姓名
    Route::get('employ', 'EmployController@index');
    Route::get('employ/detail', 'EmployController@detail');
	Route::get('employ/payroll', 'EmployController@payroll');
    Route::get('employ/create', 'EmployController@create');
    Route::post('employ/', 'EmployController@store');
    Route::get('employ/{id}', 'EmployController@show');
    Route::get('employ/{id}/edit', 'EmployController@edit');
    Route::put('employ/{id}', 'EmployController@update');
    Route::delete('employ/{id}', 'EmployController@destroy');

    // 課程表處理
    Route::post('class_schedule/getterm', 'ClassScheduleController@getTerm'); // 取得期別
    Route::get('class_schedule/siteedit/{id}', 'ClassScheduleController@siteEdit'); // 調整主教室
    Route::put('class_schedule/siteedit', 'ClassScheduleController@siteUpdate'); // 調整主教室處理
    Route::get('class_schedule/publishedit/{id}', 'ClassScheduleController@publishEdit'); // 網頁公告
    Route::put('class_schedule/publishedit', 'ClassScheduleController@publishUpdate'); // 網頁公告處理
    Route::put('class_schedule/cuttingedit/{id}', 'ClassScheduleController@cuttingUpdate'); // 分割課程處理
    Route::get('class_schedule', 'ClassScheduleController@index');
    Route::get('class_schedule/calendar/{id}', 'ClassScheduleController@calendar');  // 課程表
    Route::put('class_schedule/calendar/', 'ClassScheduleController@courseassignment');  // 課程配當
    Route::get('class_schedule/create', 'ClassScheduleController@create');
    Route::post('class_schedule/', 'ClassScheduleController@store');
    Route::get('class_schedule/{id}', 'ClassScheduleController@show');
    Route::get('class_schedule/{id}/edit', 'ClassScheduleController@edit');
    Route::get('class_schedule/{id}/classedit', 'ClassScheduleController@classedit');
    Route::put('class_schedule/{id}', 'ClassScheduleController@update');
    Route::delete('class_schedule/{id}', 'ClassScheduleController@destroy');

    // 場地問卷處理(101)
    Route::get('site_survey/gettimes/{id}', 'SiteSurveyController@getTimes'); // 取得第幾次調查
    Route::get('site_survey', 'SiteSurveyController@index');
    Route::get('site_survey/create', 'SiteSurveyController@create');
    Route::post('site_survey/', 'SiteSurveyController@store');
    Route::get('site_survey/{id}', 'SiteSurveyController@show');
    Route::get('site_survey/{id}/edit', 'SiteSurveyController@edit');
    Route::put('site_survey/{id}', 'SiteSurveyController@update');
    Route::delete('site_survey/{id}', 'SiteSurveyController@destroy');

    // 場地問卷處理(96~100)
    Route::get('site_survey_old/gettimes/{id}', 'SiteSurveyController@getTimes'); // 取得第幾次調查
    Route::get('site_survey_old', 'SiteSurveyController@index');
    Route::get('site_survey_old/create', 'SiteSurveyController@create');
    Route::post('site_survey_old/', 'SiteSurveyController@store');
    Route::get('site_survey_old/{id}', 'SiteSurveyController@show');
    Route::get('site_survey_old/{id}/edit', 'SiteSurveyController@edit');
    Route::put('site_survey_old/{id}', 'SiteSurveyController@update');
    Route::delete('site_survey_old/{id}', 'SiteSurveyController@destroy');

    // 成效問卷製作
    Route::post('effectiveness_survey/getterm', 'EffectivenessSurveyController@getTerm'); // 取得期別
    Route::post('effectiveness_survey/getcourse', 'EffectivenessSurveyController@getCourse'); // 取得課程
    Route::get('effectiveness_survey/change/{id}/edit', 'EffectivenessSurveyController@changeEdit'); // 更換課程/講座
    Route::put('effectiveness_survey/change/{id}', 'EffectivenessSurveyController@changeUpdate'); // 更換課程/講座儲存
    Route::get('effectiveness_survey', 'EffectivenessSurveyController@index');
    Route::get('effectiveness_survey/create/{arr}', 'EffectivenessSurveyController@create');
    Route::post('effectiveness_survey/', 'EffectivenessSurveyController@store');
    Route::get('effectiveness_survey/{id}', 'EffectivenessSurveyController@show');
    Route::get('effectiveness_survey/{class_info}/edit', 'EffectivenessSurveyController@edit');
    Route::get('effectiveness_survey/{class_info}/detail', 'EffectivenessSurveyController@detail');
    Route::put('effectiveness_survey/{id}', 'EffectivenessSurveyController@update');
    Route::delete('effectiveness_survey/{id}', 'EffectivenessSurveyController@destroy');

    //資料匯出處理
    Route::get('dataexport/index/{id?}', 'DataExportController@index');
    Route::post('dataexport/fax/{id?}', 'DataExportController@fax');//無傳真電話
    Route::post('dataexport', 'DataExportController@export');
    Route::get('dataexport/select_class','DataExportController@select_class');
    Route::post('dataexport/select_class','DataExportController@select_class');
    Route::get('dataexport/set_column/{type}/{cond?}','DataExportController@set_column');
    Route::post('dataexport/set_column','DataExportController@set_column');
    Route::get('dataexport/send/{cond}','DataExportController@send');
    Route::post('dataexport/send/{cond}','DataExportController@send');

    //入口網站資料匯出
    Route::get('entryexport', 'EntryExportController@index');
    Route::get('entryexport/select_class/{date}', 'EntryExportController@select_class');//班別資料匯出-挑選班期
    Route::post('entryexport/export/{type}', 'EntryExportController@export');//班別資料匯出
    Route::post('entryexport/search/{type?}','EntryExportController@search');

    //鎖定班期
    Route::get('lockclass', 'LockClassController@index');
    Route::post('lockclass', 'LockClassController@index');
    Route::post('lockclass/lock', 'LockClassController@lock');
    Route::post('lockclass/unlock', 'LockClassController@unlock');

    //圖書系統匯出
    Route::get('libraryexport', 'LibraryExportController@index');
    Route::post('libraryexport/export/{type}', 'LibraryExportController@export');//班別資料匯出
    Route::post('libraryexport/test/','LibraryExportController@test');

    //班務流程指引維護
    Route::get('class_process', 'Class_processController@index');
    Route::get('class_process/create','Class_processController@create');
    Route::post('class_process/store', 'Class_processController@store');
    Route::get('class_process/detail/{id}', 'Class_processController@detail');
    Route::put('class_process/{id}', 'Class_processController@update');
    Route::get('class_process/create_job','Class_processController@create_job');
    Route::post('class_process/store_job', 'Class_processController@store_job');
    Route::get('class_process/edit_job/{id}', 'Class_processController@edit_job');
    Route::put('class_process/update_job/{id}', 'Class_processController@update_job');
    Route::get('class_process/download_file/{id}', 'Class_processController@download_file');
    Route::delete('class_process/{id}', 'Class_processController@destroy');
    Route::delete('class_process/job/{id}', 'Class_processController@destroy_job');

    //重要訊息維護
    Route::get('reportmg', 'ReportmgController@index');
    Route::get('reportmg/create','ReportmgController@create');
    Route::post('reportmg/create','ReportmgController@create');
    Route::get('reportmg/edit/{id}','ReportmgController@edit');
    Route::post('reportmg/edit/{id}','ReportmgController@edit');
    Route::delete('reportmg/edit/{id}','ReportmgController@delete');

    // 訓前訓中訓後問卷設定
    Route::get('trainQuestSetting', 'TrainQuestSettingContrller@index');
    Route::get('trainQuestSetting/setting/{class}/{term}', 'TrainQuestSettingContrller@setting');

    Route::get('trainQuestSetting/quest/edit/{id}', 'TrainQuestSettingContrller@edit');
    Route::get('trainQuestSetting/quest/create/{class}/{term}', 'TrainQuestSettingContrller@create');
    Route::post('trainQuestSetting/{class}/{term}', 'TrainQuestSettingContrller@store');
    Route::put('trainQuestSetting/quest/{id}', 'TrainQuestSettingContrller@update');
    Route::delete('trainQuestSetting/quest/delete/{id}', 'TrainQuestSettingContrller@delete');
    Route::delete('trainQuestSetting/delete/{id}', 'TrainQuestSettingContrller@deleteSetting');

    // 成效問卷處理(105)
    Route::post('effectiveness_process/getterm', 'EffectivenessProcessController@getTerm'); // 取得期別
    Route::post('effectiveness_process/gettimes', 'EffectivenessProcessController@getTimes'); // 取得期別
    Route::post('effectiveness_process/getlist', 'EffectivenessProcessController@getList'); // 取得講座方面
    Route::get('effectiveness_process', 'EffectivenessProcessController@index');
    Route::get('effectiveness_process/create/{arr}', 'EffectivenessProcessController@create');
    Route::post('effectiveness_process/create/', 'EffectivenessProcessController@store');
    Route::get('effectiveness_process/{arr}', 'EffectivenessProcessController@show');
    Route::get('effectiveness_process/{id}/edit', 'EffectivenessProcessController@edit');
    Route::put('effectiveness_process/{id}', 'EffectivenessProcessController@update');
    Route::delete('effectiveness_process/{id}', 'EffectivenessProcessController@destroy');
    Route::get('effectiveness_process/calculate/{arr}', 'EffectivenessProcessController@calculate');//統計
    Route::post('effectiveness_process/year_calculate', 'EffectivenessProcessController@year_calculate');//年統計

    // 訓後問卷製作
    Route::post('training_survey/getterm', 'TrainingSurveyController@getTerm'); // 取得期別
    Route::post('training_survey/getcourse', 'TrainingSurveyController@getCourse'); // 取得課程
    Route::get('training_survey', 'TrainingSurveyController@index');
    Route::get('training_survey/create', 'TrainingSurveyController@create');
    Route::post('training_survey/', 'TrainingSurveyController@store');
    Route::get('training_survey/{id}', 'TrainingSurveyController@show');
    Route::get('training_survey/{id}/edit', 'TrainingSurveyController@edit');
    Route::put('training_survey/{id}', 'TrainingSurveyController@update');
    Route::delete('training_survey/{id}', 'TrainingSurveyController@destroy');

    //E-Mail線上問卷填答通知
    Route::get('notice_emai', 'NoticeEmailController@index');
    Route::get('notice_emai/detail', 'NoticeEmailController@detail');
    Route::post('notice_emai/save_mail', 'NoticeEmailController@save_mail');
    Route::post('notice_emai/save_list', 'NoticeEmailController@save_list');
    Route::post('notice_emai/mail_to_me', 'NoticeEmailController@mail_to_me');
    Route::get('notice_emai/list/{id}', 'NoticeEmailController@list');

    // 場地審核處理

    Route::get('site_check', 'SiteCheckController@index');
    Route::get('site_check/{id}', 'SiteCheckController@show');
    Route::get('site_check/{id}/edit', 'SiteCheckController@edit');
    Route::get('site_check/{id}/pass', 'SiteCheckController@pass'); // 同意
    Route::get('site_check/{id}/return', 'SiteCheckController@returns'); // 退回
    Route::get('site_check/{id}/cancel', 'SiteCheckController@cancel'); // 取消

    // 訓後問卷處理
    Route::post('training_process/getterm', 'TrainingProcessController@getTerm'); // 取得期別
    Route::post('training_process/getcourse', 'TrainingProcessController@getCourse'); // 取得課程
    Route::get('training_process', 'TrainingProcessController@index');
    Route::get('training_process/create', 'TrainingProcessController@create');
    Route::post('training_process/', 'TrainingProcessController@store');
    Route::get('training_process/{id}', 'TrainingProcessController@show');
    Route::get('training_process/{id}/edit', 'TrainingProcessController@edit');
    Route::put('training_process/{id}', 'TrainingProcessController@update');
    Route::delete('training_process/{id}', 'TrainingProcessController@destroy');

    // 早餐及住宿名單處理
    Route::post('stay_list/getterm', 'StayListController@getTerm'); // 取得期別
    Route::get('stay_list', 'StayListController@index');
    Route::get('stay_list/create', 'StayListController@create');
    Route::post('stay_list/', 'StayListController@store');
    Route::get('stay_list/{id}', 'StayListController@show');
    Route::get('stay_list/{id}/edit', 'StayListController@edit');
    Route::put('stay_list/{id}', 'StayListController@update');
    Route::delete('stay_list/{id}', 'StayListController@destroy');

    // 學員請假處理
    // Route::post('leave/getterm', 'LeaveController@getTerm'); // 取得期別
    // Route::get('leave/getname', 'LeaveController@getName'); // 取得學員姓名

    Route::get('leave/suspendClasses/{class}/{term}', 'LeaveController@suspendClassesPage');
    Route::post('leave/suspendClasses/{class}/{term}', 'LeaveController@suspendClasses');
    Route::get('leave/class_list', 'LeaveController@class_list');
    Route::get('leave/{id}/edit', 'LeaveController@edit');
    Route::get('leave/{class}/{term}', 'LeaveController@index');
    Route::get('leave/create/{class}/{term}', 'LeaveController@create');
    Route::post('leave/{class}/{term}', 'LeaveController@store');
    Route::get('leave/{id}', 'LeaveController@show');
    Route::put('leave/{id}', 'LeaveController@update');
    Route::delete('leave/{id}', 'LeaveController@destroy');

    // 控管辦班處理
    Route::get('class_control', 'ClassControlController@index');
    Route::get('class_control/create', 'ClassControlController@create');
    Route::post('class_control/', 'ClassControlController@store');
    Route::get('class_control/{id}', 'ClassControlController@show');
    Route::get('class_control/{id}/edit', 'ClassControlController@edit');
    Route::put('class_control/{id}', 'ClassControlController@update');
    Route::delete('class_control/{id}', 'ClassControlController@destroy');

    // 學員結訓處理
    Route::post('train_certification/getterm', 'TrainCertificationController@getTerm'); // 取得期別
    Route::get('train_certification', 'TrainCertificationController@index'); // 列表
    Route::put('train_certification', 'TrainCertificationController@update'); // 儲存

    // 數位時數處理
    Route::post('digital/getterm', 'DigitalController@getTerm'); // 取得期別
    Route::get('digital/student/{class}/{term}', 'DigitalController@index'); // 列表
    Route::put('digital/student/{class}/{term}', 'DigitalController@update'); // 儲存

    Route::get('digital/class_setting/{class}/{term}', 'DigitalController@classSetting'); // 列表
    Route::put('digital/class_setting/{class}/{term}', 'DigitalController@storeClassSetting'); // 儲存

    Route::get('digital/class_list', 'DigitalController@classList'); // 列表

    // 學費刷卡處理
    Route::post('punch/getterm', 'PunchController@getTerm'); // 取得期別
    Route::get('punch/class_list', 'PunchController@classList'); // 列表
    Route::get('punch/{class}/{term}', 'PunchController@index'); // 列表

    // 教學方法處理
 //   Route::post('method/getterm', 'MethodController@getTerm'); // 取得期別
    Route::get('method', 'MethodController@index'); // 列表
    Route::get('method/{id}/edit', 'MethodController@edit'); // 編輯
    Route::put('method/{id}', 'MethodController@update'); // 儲存

    // 線上報名設定
    Route::post('signup/getterm', 'SignupController@getTerm'); // 取得期別
    Route::get('signup', 'SignupController@index'); // 列表
    Route::get('signup/edit/{class}/{term}', 'SignupController@edit'); // 列表
    Route::put('signup', 'SignupController@update'); // 儲存
    Route::put('signup/process2/{class}/{term}', 'SignupController@updateProcess2'); // 儲存

    // 線上報名設定(委訓班-依機關分配)
    Route::get('signup_organ/create/{class}/{term}', 'SignupOrganController@create');
    Route::post('signup_organ/{class}/{term}', 'SignupOrganController@store');
    Route::get('signup_organ/edit/{id}', 'SignupOrganController@edit');
    Route::put('signup_organ/{id}', 'SignupOrganController@update');
    Route::delete('signup_organ/{id}', 'SignupOrganController@delete');


    Route::get('student_grade/main_option/{id}', 'StudentGradeController@main_option');
    Route::get('student_grade/class_list', 'StudentGradeController@classList');
    Route::get('student_grade/input_grade/main_option/{main_option_id}', 'StudentGradeController@inputGradeSub');
    Route::get('student_grade/input_grade/sub_option/{sub_option_id}', 'StudentGradeController@inputGrade');
    Route::put('student_grade/input_grade/main_option/{main_option_id}', 'StudentGradeController@storeGrade');
    Route::put('student_grade/main_option/{id}', 'StudentGradeController@storeSubOption');

    Route::group(['middleware' => ['t04tb']], function () {
        Route::get('review_apply/create/{class}/{term}', 'ReviewApplyController@create');
        Route::get('review_apply/{class}/{term}', 'ReviewApplyController@index');

        Route::post('review_apply/importApplyData/{class}/{term}', 'ReviewApplyController@importApplyData');
        Route::put('review_apply/review/{class}/{term}', 'ReviewApplyController@review');

        // 成績輸入
        Route::get('student_grade/{class}/{term}', 'StudentGradeController@index');
        Route::get('student_grade/input_grade/{class}/{term}', 'StudentGradeController@inputGrade');
        Route::get('student_grade/downloadExportExample/{class}/{term}', 'StudentGradeController@downloadExportExample');
        Route::get('student_grade/setting/{class}/{term}', 'StudentGradeController@setting');
        Route::post('student_grade/import_grade/{class}/{term}', 'StudentGradeController@importGrade');

        Route::put('student_grade/setting/{class}/{term}', 'StudentGradeController@setSeting');
    });



    // 報名審核處理
    Route::get('review_apply/class_list', 'ReviewApplyController@classList');
    Route::get('review_apply/edit/{class}/{term}/{idno}', 'ReviewApplyController@edit');
    Route::get('review_apply/assign', 'ReviewApplyController@assign');
    Route::get('review_apply/check_apply', 'ReviewApplyController@check_apply');
    Route::get('review_apply/check_repeat_apply', 'ReviewApplyController@check_repeat_apply');
    Route::get('review_apply/copy_apply', 'ReviewApplyController@copy_apply_choose');
    Route::get('review_apply/check_copy_repeat', 'ReviewApplyController@check_copy_repeat');
    Route::get('review_apply/check_is_over', 'ReviewApplyController@check_is_over');
    Route::get('review_apply/apply_history', 'ReviewApplyController@apply_history');

    Route::put('review_apply/assign/{class}/{term}/{organ}', 'ReviewApplyController@storeAssign');
    Route::put('review_apply/{class}/{term}/{idno}', 'ReviewApplyController@update');

    Route::post('review_apply/copy_apply', 'ReviewApplyController@copy_apply');

    Route::delete('review_apply/{class}/{term}/{idno}', 'ReviewApplyController@delete');

    Route::get('field/getData/t01tbs', 'FieldController@t01tbs');
    Route::get('field/getData/m17tbs', 'FieldController@m17tbs');
    Route::get('field/getData/floors', 'FieldController@floors');
    Route::get('field/getData/allFloors', 'FieldController@allFloors');
    Route::get('field/getData/t01tb/{class}', 'FieldController@t01tb');
    Route::get('field/getData/{field}', 'FieldController@getData');

    // 教學教法維護
    Route::get('teachingmethod', 'TeachingmethodController@index'); // 列表
    Route::get('teachingmethod/create', 'TeachingmethodController@create');
    Route::post('teachingmethod/', 'TeachingmethodController@store'); //新增
    Route::get('teachingmethod/{id}', 'TeachingmethodController@show');
    Route::get('teachingmethod/{id}/edit', 'TeachingmethodController@edit'); // 編輯
    Route::put('teachingmethod/{id}', 'TeachingmethodController@update'); // 儲存
    Route::delete('teachingmethod/{id}', 'TeachingmethodController@destroy'); //刪除

    // 認證上傳設定
    Route::post('certification/getterm', 'CertificationController@getTerm'); // 取得期別
    Route::get('certification', 'CertificationController@index'); // 列表
    Route::put('certification', 'CertificationController@update'); // 儲存

    // 班別教材資料處理
    Route::post('class_material/getterm', 'ClassMaterialController@getTerm'); // 取得期別
    Route::post('course_material/get_course_material', 'ClassMaterialController@getCourseMaterial'); // 取得教材
    Route::get('class_material', 'ClassMaterialController@index');
    Route::put('class_material', 'ClassMaterialController@update');
    Route::delete('class_material/{id}', 'ClassMaterialController@destroy');

    // 講座授課及教材資料查詢
    Route::get('print/new', 'PrintController@new');
    Route::get('print', 'PrintController@index');
    Route::get('print/maintain', 'PrintController@maintain');
    Route::put('print', 'PrintController@update');
    Route::delete('print/{id}', 'PrintController@destroy');
    Route::post('print/getterm', 'PrintController@getTerm'); // 取得期別

    // 經費概(結)算

    Route::get('funding/class_list', 'FundingController@classList'); //
    Route::get('funding/selectProbably', 'FundingController@selectProbably'); //
    Route::get('funding/selectConclusion', 'FundingController@selectConclusion'); //

    Route::post('funding/batchInsertProbably', 'FundingController@batchInsertProbably'); //
    Route::post('funding/batchInsetConclusion', 'FundingController@batchInsetConclusion'); //

    Route::get('funding/edit/{class}/{term}/{type}', 'FundingController@edit'); //
    Route::put('funding/{class}/{term}/{type}', 'FundingController@update'); //

    // 洽借場地班期資料處理
    Route::get('site_manage', 'SiteManageController@index');
    Route::post('site_manage/batchadd', 'SiteManageController@batchadd'); //批次加
    Route::get('site_manage/batchdel', 'SiteManageController@batchdel');  //批次減
    Route::get('site_manage/{id}/edit', 'SiteManageController@edit');
    Route::put('site_manage/{id}', 'SiteManageController@update');
    Route::get('site_manage/create', 'SiteManageController@create');
    Route::post('site_manage/create', 'SiteManageController@store');
    Route::delete('site_manage/{id}', 'SiteManageController@destroy');

    // 洽借場地班期排程處理
    Route::get('site_schedule', 'SiteScheduleController@index');
    Route::get('site_schedule/{id}/edit', 'SiteScheduleController@edit');
    Route::put('site_schedule/{id}', 'SiteScheduleController@update');  //改
    Route::delete('site_schedule/{id}', 'SiteScheduleController@destroy'); //刪
    Route::post('site_schedule/', 'SiteScheduleController@store'); //新增
    Route::get('site_schedule/add', 'SiteScheduleController@add');
    Route::get('site_schedule/details', 'SiteScheduleController@details');
    Route::get('site_schedule/getedate', 'SiteScheduleController@getedate'); //ajax
    Route::get('site_schedule/getsection', 'SiteScheduleController@getsection'); //ajax

    Route::get('site_schedule/calendar', 'SiteScheduleController@calendar');
    Route::put('site_schedule/calendar/{id}', 'SiteScheduleController@calendarupdate');  //改
    Route::delete('site_schedule/calendar/{id}', 'SiteScheduleController@calendardestroy'); //刪
    Route::post('site_schedule/calendar', 'SiteScheduleController@calendarstore'); //新增

    //班務流程
    Route::get('term_process', 'Term_processController@index');
    Route::post('term_process/completeChange', 'Term_processController@completeChange');
    Route::get('term_process/download_file/{id}', 'Term_processController@download_file');
    // Route::get('term_process/getMail', 'Term_processController@getMail');

    // 課程配當安排
    Route::get('arrangement', 'ArrangementController@classList');
    Route::get('arrangement/batch_create', 'ArrangementController@batch_create');
    Route::get('arrangement/isHavePlanmk/{class}', 'ArrangementController@isHavePlanmk');
    Route::get('arrangement/{class}/{term}', 'ArrangementController@index');
    Route::get('arrangement/edit/{class}/{term}/{course}', 'ArrangementController@edit');
    Route::put('arrangement/{class}/{term}/{course}', 'ArrangementController@update');
    Route::get('arrangement/create/{class}/{term}', 'ArrangementController@create');
    Route::post('arrangement/batch_store', 'ArrangementController@batch_store');
    Route::post('arrangement/uploadSchedule/{class}', 'ArrangementController@uploadSchedule');
    Route::post('arrangement/{class}/{term}', 'ArrangementController@store');
    Route::delete('arrangement/{class}/{term}/{course}', 'ArrangementController@delete');


    Route::get('unit/{class}/{term}', 'UnitController@index');
    Route::get('unit/edit/{class}/{term}/{unit}', 'UnitController@edit');
    Route::put('unit/{class}/{term}/{unit}', 'UnitController@update');
    Route::get('unit/create/{class}/{term}', 'UnitController@create');
    Route::post('unit/{class}/{term}', 'UnitController@store');
    Route::delete('unit/{class}/{term}/{unit}', 'UnitController@delete');


    // 教學教法運用彙總表
    Route::get('teachlist', 'TeachListController@index');
    Route::put('teachlist/edit', 'TeachListController@edit');
    Route::get('teachlist/maintain', 'TeachListController@maintain');

    //講座授課及教材資料登錄
    Route::get('teaching_material', 'TeachingMaterialController@index');
    Route::get('teaching_material/form', 'TeachingMaterialController@match');
    Route::get('teaching_material/details/{id}', 'TeachingMaterialController@details');
    Route::get('teaching_material/create', 'TeachingMaterialController@create');
    Route::post('teaching_material/', 'TeachingMaterialController@store');
    Route::get('teaching_material/{id}', 'TeachingMaterialController@show');
    Route::get('teaching_material/{id}/edit', 'TeachingMaterialController@edit');
    Route::put('teaching_material/{id}', 'TeachingMaterialController@update');
    Route::delete('teaching_material/{id}', 'TeachingMaterialController@destroy');
    Route::delete('teaching_material/{id}/from', 'TeachingMaterialController@destroy_from');


    Route::get('student_apply/arrange_group/{class}/{term}', 'StudentApplyController@arrangeGroup');
    Route::put('student_apply/arrange_group/{class}/{term}', 'StudentApplyController@autoArrangeGroup');

    Route::get('student_apply/arrange_stno/{class}/{term}', 'StudentApplyController@arrangeStNo');
    Route::put('student_apply/arrange_stno/{class}/{term}', 'StudentApplyController@autoArrangeStNo');

    Route::put('student_apply/group_edit/{class}/{term}', 'StudentApplyController@group_edit');
    Route::put('student_apply/stno_edit/{class}/{term}', 'StudentApplyController@stno_edit');

    Route::get('student_apply/modifylogForAdmin/{class}/{term}', 'StudentApplyController@modifylogForAdmin');
    Route::put('student_apply/changeTerm/{class}/{term}/{old_des_idno}', 'StudentApplyController@changeTerm');

    Route::put('student_apply/publishStudentList/{class}/{term}', 'StudentApplyController@publishStudentList');

    Route::post('student_apply/importStudent/{class}/{term}', 'StudentApplyController@importStudent');
    Route::get('student_apply/checkExsitT13tb/{class}/{term}', 'StudentApplyController@checkExsitT13tb');

    Route::post('student_apply/redirectCreateStudent/{class}/{term}', 'StudentApplyController@redirectCreateStudent');
    Route::get('student_apply/create/{class}/{term}/{des_idno}/{identity}', 'StudentApplyController@create');

    Route::post('student_apply/redirectChangeStudent/{class}/{term}', 'StudentApplyController@redirectChangeStudent');
    Route::post('student_apply/{class}/{term}/{identity}', 'StudentApplyController@store');

    Route::get('student_apply/changeStudent/{class}/{term}/{old_des_idno}/{new_des_idno}', 'StudentApplyController@changeStudent');
    Route::put('student_apply/changeStudent/{class}/{term}/{old_des_idno}', 'StudentApplyController@storeForChangeStudent');


    Route::get('student_apply/modify_manage', 'StudentApplyController@modifyManage');
    Route::get('student_apply/class_list', 'StudentApplyController@classList');
    Route::get('student_apply/{class}/{term}', 'StudentApplyController@index');

    Route::get('student_apply/edit/{class}/{term}/{des_idno}', 'StudentApplyController@edit');
    Route::put('student_apply/{class}/{term}/{des_idno}', 'StudentApplyController@update');
    Route::delete('student_apply/{class}/{term}/{des_idno}', 'StudentApplyController@delete');

    Route::put('student_apply/stopChange', 'StudentApplyController@stopChange');
    Route::put('student_apply/reviewModify', 'StudentApplyController@reviewModify');

    //巡迴研習類別
    Route::get('itineracy', 'ItineracyController@index');
    Route::post('itineracy/', 'ItineracyController@store'); //新增
    Route::put('itineracy/{id}', 'ItineracyController@update'); //修改
    Route::delete('itineracy/{id}', 'ItineracyController@destroy'); //刪除

    //巡迴研習主題
    Route::get('itineracy_theme', 'ItineracyThemeController@index');
    Route::post('itineracy_theme/', 'ItineracyThemeController@store'); //新增
    Route::put('itineracy_theme/{id}', 'ItineracyThemeController@update'); //修改
    Route::delete('itineracy_theme/{id}', 'ItineracyThemeController@destroy'); //刪除

    //巡迴研習單元
    Route::get('itineracy_unit', 'ItineracyUnitController@index');
    Route::post('itineracy_unit/', 'ItineracyUnitController@store'); //新增
    Route::put('itineracy_unit/{id}', 'ItineracyUnitController@update'); //修改
    Route::delete('itineracy_unit/{id}', 'ItineracyUnitController@destroy'); //刪除

    //年度主題設定
    Route::get('itineracy_annual ', 'ItineracyAnnualController@index');
    Route::get('itineracy_annual/create', 'ItineracyAnnualController@create'); //新增頁
    Route::post('itineracy_annual/create', 'ItineracyAnnualController@store'); //新增
    Route::get('itineracy_annual/edit/{id}', 'ItineracyAnnualController@edit');  //修改頁
    Route::put('itineracy_annual/edit/{id}', 'ItineracyAnnualController@update'); //修改
    Route::get('itineracy_annual/setting/{id}', 'ItineracyAnnualController@setting');  //主題設定頁
    Route::post('itineracy_annual/setting', 'ItineracyAnnualController@settingstore'); //主題新增
    Route::put('itineracy_annual/setting/{id}', 'ItineracyAnnualController@settingupdate'); //主題修改
    Route::delete('itineracy_annual/setting/{id}', 'ItineracyAnnualController@destroy'); //主題刪除

    //巡迴研習需求調查登錄
    Route::get('itineracy_surveylogin', 'ItineracySurveyloginController@index');
    Route::put('itineracy_surveylogin/{id}', 'ItineracySurveyloginController@update'); //修改
    Route::get('itineracy_surveylogin/list/{id}', 'ItineracySurveyloginController@list');  //填報資料頁
    Route::get('itineracy_surveylogin/print/{id}', 'ItineracySurveyloginController@print');  //列印日程表
    Route::get('itineracy_surveylogin/list/edit/{id}', 'ItineracySurveyloginController@edit');  //填報資料修改頁
    Route::post('itineracy_surveylogin/list/edit', 'ItineracySurveyloginController@liststore'); //新增
    Route::put('itineracy_surveylogin/list/edit/{id}', 'ItineracySurveyloginController@listupdate'); //修改
    Route::delete('itineracy_surveylogin/list/edit/{id}', 'ItineracySurveyloginController@listdestroy'); //刪除

    //實施日程表
    Route::get('itineracy_schedule', 'ItineracyScheduleController@index');
    Route::get('itineracy_schedule/print/{id}', 'ItineracyScheduleController@print');  //列印
    Route::get('itineracy_schedule/edit/{id}', 'ItineracyScheduleController@edit');  //修改頁
    Route::get('itineracy_schedule/edit/{id}/batchimport', 'ItineracyScheduleController@batchimport'); //匯入
    Route::get('itineracy_schedule/edit/city/{id}', 'ItineracyScheduleController@cityedit');  //縣市別修改頁
    Route::post('itineracy_schedule/edit/city', 'ItineracyScheduleController@liststore'); //新增
    Route::put('itineracy_schedule/edit/city/{id}', 'ItineracyScheduleController@listupdate'); //修改
    Route::delete('itineracy_schedule/edit/city/{id}', 'ItineracyScheduleController@listdestroy'); //刪除
    Route::get('itineracy_schedule/edit/city/settingclass/{id}', 'ItineracyScheduleController@settingclass'); //設定課程頁
    Route::put('itineracy_schedule/edit/city/settingclass/{id}', 'ItineracyScheduleController@classupdate'); //設定課程修改


    //巡迴研習參數設定
    Route::get('itineracy_setting', 'ItineracySettingController@index');

    //辦班需求(確認)處理
    Route::get('classes_requirements', 'ClassesRequirementsController@index');
    Route::put('classes_requirements/unitprice', 'ClassesRequirementsController@unitprice'); // 更新單價
    Route::get('classes_requirements/edit/{id}', 'ClassesRequirementsController@edit');  //修改頁
    Route::post('classes_requirements/edit', 'ClassesRequirementsController@store'); //新增
    Route::put('classes_requirements/edit/{id}', 'ClassesRequirementsController@update'); //修改
    Route::delete('classes_requirements/edit/{id}', 'ClassesRequirementsController@destroy'); //刪除
    Route::post('classes_requirements/edit/group', 'ClassesRequirementsController@groupstore'); //批次新增
    Route::delete('classes_requirements/edit/group/{id}', 'ClassesRequirementsController@groupdestroy'); //批次刪除
    Route::post('classes_requirements/edit/stopcook', 'ClassesRequirementsController@stopcook'); //新增止伙


    // Route::get('student_grade/edit/{class}/{term}', 'StudentGradeController@index');

    Route::get('student', 'StudentController@index');
    Route::get('student/edit/{des_idno}', 'StudentController@edit');

    Route::put('student/{des_idno}', 'StudentController@update');
    Route::put('student/resetPassword/{des_idno}', 'StudentController@resetPassword');

    Route::get('site_review/class_list', 'SiteReviewController@classList');
    Route::get('site_review/{class}/{term}', 'SiteReviewController@index');
    Route::get('site_review/create/{class}/{term}', 'SiteReviewController@create');
    Route::get('site_review/edit/{class}/{term}/{des_idno}', 'SiteReviewController@edit');
    Route::get('site_review/checkCondition/{class}/{term}', 'SiteReviewController@checkCondition');
    Route::put('site_review/filterStudent/{class}/{term}', 'SiteReviewController@filterStudent');

    Route::post('site_review/{class}/{term}', 'SiteReviewController@store');

    Route::put('site_review/{class}/{term}', 'SiteReviewController@updateProve');
    Route::put('site_review/{class}/{term}/{des_idno}', 'SiteReviewController@update');

    //教材交印資料處理
    Route::get('teaching_material_print', 'TeachingMaterialPrintController@index');
    Route::get('teaching_material_print/list/{id}', 'TeachingMaterialPrintController@list');  //清單
    Route::get('teaching_material_print/edit/{id}', 'TeachingMaterialPrintController@edit');  //修改頁
    Route::post('teaching_material_print/edit', 'TeachingMaterialPrintController@store'); //新增
    Route::put('teaching_material_print/edit/{id}', 'TeachingMaterialPrintController@update'); //修改
    Route::delete('teaching_material_print/edit/{id}', 'TeachingMaterialPrintController@destroy'); //刪除

    //教材印製統計處理
    Route::get('teaching_material_statistics', 'TeachingMaterialStatisticsController@index');
    Route::get('teaching_material_statistics/list/{id}', 'TeachingMaterialStatisticsController@list');  //清單
    Route::get('teaching_material_statistics/edit/{id}', 'TeachingMaterialStatisticsController@edit');  //修改頁
    Route::get('teaching_material_statistics/upprice/{id}', 'TeachingMaterialStatisticsController@upprice'); //更新單價
    Route::put('teaching_material_statistics/edit/{id}', 'TeachingMaterialStatisticsController@update'); //修改
    // Route::delete('teaching_material_statistics/edit/{id}', 'TeachingMaterialStatisticsController@destroy'); //刪除

    //教材維護
    Route::get('teaching_material_maintain', 'TeachingMaterialMaintainController@index');
    Route::put('teaching_material_maintain/changesort/{branch}', 'TeachingMaterialMaintainController@ChangeSort');  //排序
    Route::put('teaching_material_maintain/edit/{branch}', 'TeachingMaterialMaintainController@update');  //修改
    Route::post('teaching_material_maintain/creat/{branch}', 'TeachingMaterialMaintainController@store'); //新增
    Route::delete('teaching_material_maintain/edit/{branch}', 'TeachingMaterialMaintainController@destroy'); //刪除

    Route::get('special_class_fee/class_list', 'SpecialClassFeeController@classList');
    Route::get('special_class_fee/edit/{class}/{term}', 'SpecialClassFeeController@edit');

    Route::put('special_class_fee/{class}/{term}', 'SpecialClassFeeController@update');

    Route::get('sponsor_agent', 'SponsorAgentController@index');
    Route::get('sponsor_agent/edit/{userid}', 'SponsorAgentController@edit');
    Route::get('sponsor_agent/create/{userid}', 'SponsorAgentController@create');

    Route::post('sponsor_agent/{userid}', 'SponsorAgentController@store');
    Route::delete('sponsor_agent/{sponsor_agent_id}', 'SponsorAgentController@delete');

    Route::get('signature', 'SignatureController@index');
    Route::get('signature/create', 'SignatureController@create');
    Route::get('signature/edit/{id}', 'SignatureController@edit');

    Route::post('signature', 'SignatureController@store');
    Route::put('signature/{id}', 'SignatureController@update');
    Route::delete('signature/{id}', 'SignatureController@delete');

    Route::get('role_simulate', 'RoleSimulateController@index');
    Route::post('role_simulate/simulate', 'RoleSimulateController@simulate');
    Route::post('role_simulate/returnOriginUser', 'RoleSimulateController@returnOriginUser');


    //報表

    // 需求調查表
    Route::get('demand_measure_report', 'DemandMeasureReportController@index');
    Route::post('demand_measure_report/export', 'DemandMeasureReportController@export');
    Route::post('demand_measure_report/gettime', 'DemandMeasureReportController@gettime'); // 取得梯次

    // 需求名額統計表
    Route::get('demand_quota_report', 'DemandQuotaReportController@index');
    Route::post('demand_quota_report/export', 'DemandQuotaReportController@export');
    Route::post('demand_quota_report/gettime', 'DemandQuotaReportController@gettime'); // 取得梯次

    // 研習實施計畫-總表
    Route::get('studyplan_all', 'StudyplanAllController@index');
    Route::post('studyplan_all/export', 'StudyplanAllController@export');

    // 研習實施計畫-形式表
    Route::get('studyplan_form', 'StudyplanFormController@index');
    Route::post('studyplan_form/export', 'StudyplanFormController@export');

    // 研習實施計畫-訓期一覽表
    Route::get('studyplan_periods', 'StudyplanPeriodsController@index');
    Route::post('studyplan_periods/export', 'StudyplanPeriodsController@export');

    // 研習實施計畫-名額分配總表
    Route::get('studyplan_distribution_all', 'StudyplanDistributionAllController@index');
    Route::post('studyplan_distribution_all/export', 'StudyplanDistributionAllController@export');
    Route::post('studyplan_distribution_all/gettime', 'StudyplanDistributionAllController@gettime'); // 取得梯次
    //Route::post('studyplan_distribution_all/getclass', 'StudyplanDistributionAllController@getclass'); // 取得班別

    // 研習實施計畫-名額分配明細表
    Route::get('studyplan_distribution_detail', 'StudyplanDistributionDetailController@index');
    Route::post('studyplan_distribution_detail/gettime', 'StudyplanDistributionDetailController@gettime');
    Route::post('studyplan_distribution_detail/export', 'StudyplanDistributionDetailController@export');

    // 研習實施計畫-名額彙總表
    Route::get('studyplan_quota_all', 'StudyplanQuotaAllController@index');
    Route::post('studyplan_quota_all/export', 'StudyplanQuotaAllController@export');
    Route::post('studyplan_quota_all/gettime', 'StudyplanQuotaAllController@gettime');
    Route::post('studyplan_quota_all/getorgan', 'StudyplanQuotaAllController@getorgan');

    // 訓練績效報表
    Route::get('training_performance', 'TrainingPerformanceController@index');
    Route::post('training_performance/export', 'TrainingPerformanceController@export');

    // 各類訓練進修研習成果統計彙總表
    Route::get('each_training_all', 'EachTrainingAllController@index');
    Route::post('each_training_all/export', 'EachTrainingAllController@export');

    // 共同考核項目報表
    Route::get('same_assessment', 'SameAssessmentController@index');
    Route::post('same_assessment/export', 'SameAssessmentController@export');

    // 公務統計報表
    Route::get('business_statistics', 'BusinessStatisticsController@index');
    Route::post('business_statistics/export', 'BusinessStatisticsController@export');

    // 派訓-派訓函
    Route::get('sendtraining_mail', 'SendtrainingMailController@index');
    Route::post('sendtraining_mail/export', 'SendtrainingMailController@export');

    // 派訓-聯合派訓通知
    Route::get('sendtraining_joint', 'SendtrainingJointController@index');
    Route::post('sendtraining_joint/{yerly}/{month}/exportreciever', 'SendtrainingJointController@exportreciever');//匯出受訓者
    Route::post('sendtraining_joint/{yerly}/{month}/exportclass', 'SendtrainingJointController@exportclass');//匯出一覽表

    // 派訓-名額分配表
    Route::get('sendtraining_quota', 'SendtrainingQuotaController@index');
    Route::post('sendtraining_quota/export', 'SendtrainingQuotaController@export');
    Route::post('sendtraining_quota/getTerms', 'SendtrainingQuotaController@getTerms');

    // 調訓-調訓函
    Route::get('changetraining_mail', 'ChangetrainingMailController@index');
    Route::post('changetraining_mail/export', 'ChangetrainingMailController@export');

    // 調訓-實施計畫
    Route::get('changetraining_plan', 'ChangetrainingPlanController@index');
    Route::post('changetraining_plan/export', 'ChangetrainingPlanController@export');

    // 課程配當
    Route::get('course_assignment', 'CourseAssignmentController@index');
    Route::post('course_assignment/export', 'CourseAssignmentController@export');
    Route::post('course_assignment/getTerms', 'CourseAssignmentController@getTerms');//get terms
    // 課程表
    Route::get('course_schedule', 'CourseScheduleController@index');
    Route::post('course_schedule/export', 'CourseScheduleController@export');
    Route::post('course_schedule/getTerms', 'CourseScheduleController@getTerms');//get terms

    // 課程經費概(結)算表
    Route::get('course_funding', 'CourseFundingController@index');
    Route::post('course_funding/export', 'CourseFundingController@export');
    Route::post('course_funding/getTerms', 'CourseFundingController@getTerms');//get terms

    // 年度班期費用統計表
    Route::get('yearly_class_funding', 'YearlyClassFundingController@index');
    Route::post('yearly_class_funding/export', 'YearlyClassFundingController@export');

    // 班期調派訓異常統計表
    Route::get('changetraining_error', 'ChangetrainingErrorController@index');
    Route::post('changetraining_error/export', 'ChangetrainingErrorController@export');
    Route::post('changetraining_error/getTerms', 'ChangetrainingErrorController@getTerms');//get terms

    // 綜合報告
    Route::get('complex_report', 'ComplexReportController@index');
    Route::post('complex_report/export', 'ComplexReportController@export');

    // 辦理流程期限表
    Route::get('process_deadline', 'ProcessDeadlineController@index');
    Route::post('process_deadline/export', 'ProcessDeadlineController@export');

    // 班期日報表
    Route::get('class_daily_report', 'ClassDailyReportController@index');
    Route::post('class_daily_report/export', 'ClassDailyReportController@export');

    // 教學方法運用彙整表
    Route::get('teachway_all', 'TeachwayAllController@index');
    Route::post('teachway_all/export', 'TeachwayAllController@export');

    // 講座名單
    Route::get('lecture_list', 'LectureListController@index');
    Route::post('lecture_list/export', 'LectureListController@export');
    Route::post('lecture_list/getTerms', 'LectureListController@getTerms');//取得期別

    // 講師基本資料
    Route::get('teacher_information', 'TeacherInformationController@index');
    Route::post('teacher_information/export', 'TeacherInformationController@export');
    Route::post('teacher_information/getTerms', 'TeacherInformationController@getTerms');//取得期別

    // 講座聘函
    Route::get('lecture_mail', 'LectureMailController@index');
    Route::post('lecture_mail/export', 'LectureMailController@export');
    Route::get('lecture_mail/{class}/{term}/Mail', 'LectureMailController@Mail');       //Mail
    Route::post('lecture_mail/getTerms', 'LectureMailController@getTerms');//取得期別


    // 講座郵寄名條
    Route::get('lecture_post', 'LecturePostController@index');
    Route::post('lecture_post/export', 'LecturePostController@export');

    // 年度講座名冊錄
    Route::get('yearly_lecture_roster', 'YearlyLectureRosterController@index');
    Route::post('yearly_lecture_roster/export', 'YearlyLectureRosterController@export');

    // 講座一覽表-各類別
    Route::get('lecture_categories', 'LectureCategoriesController@index');
    Route::post('lecture_categories/export', 'LectureCategoriesController@export');
    Route::post('lecture_categories/getCategory', 'LectureCategoriesController@getCategory');

    // 講座一覽表-各班期
    Route::get('lecture_class', 'LectureClassController@index');
    Route::post('lecture_class/export', 'LectureClassController@export');
    Route::post('lecture_class/getTerms', 'LectureClassController@getTerms');//取得期別

    // 講座一覽表-各課程
    Route::get('lecture_course', 'LectureCourseController@index');
    Route::post('lecture_course/export', 'LectureCourseController@export');

    // 講師簽名單
    Route::get('lecture_signature', 'LectureSignatureController@index');
    Route::post('lecture_signature/export', 'LectureSignatureController@export');
    Route::post('lecture_signature/getTerms', 'LectureSignatureController@getTerms');//取得期別

    // 講座費用請領清冊
    Route::get('lecture_money_detail', 'LectureMoneyDetailController@index');
    Route::post('lecture_money_detail/export', 'LectureMoneyDetailController@export');
    Route::post('lecture_money_detail/getTerms', 'LectureMoneyDetailController@getTerms');//取得期別

    // 講座費用請領總表
    Route::get('lecture_money_all', 'LectureMoneyAllController@index');
    Route::post('lecture_money_all/export', 'LectureMoneyAllController@export');

    // 講座鐘點費轉帳-委託郵局代存總表
    Route::get('entrusted_save_all', 'EntrustedSaveAllController@index');
    Route::post('entrusted_save_all/export', 'EntrustedSaveAllController@export');
    Route::post('entrusted_save_all/gettdate', 'EntrustedSaveAllController@gettdate');//取得轉存日期

    // 講座鐘點費轉帳-郵政存款單
    Route::get('post_save_slip', 'PostSaveSlipController@index');
    Route::post('post_save_slip/export', 'PostSaveSlipController@export');
    Route::post('post_save_slip/gettdate', 'PostSaveSlipController@gettdate');//取得轉存日期

    // 講座鐘點費轉帳-郵政跨行匯款申請單
    Route::get('interbank_remittance_form', 'InterbankRemittanceFormController@index');
    Route::post('interbank_remittance_form/export', 'InterbankRemittanceFormController@export');
    Route::post('interbank_remittance_form/gettdate', 'InterbankRemittanceFormController@gettdate');//取得轉存日期

    // 講座鐘點費轉帳-跨行匯款明細表
    Route::get('interbank_remittance_detail', 'InterbankRemittanceDetailController@index');
    Route::post('interbank_remittance_detail/export', 'InterbankRemittanceDetailController@export');
    Route::post('interbank_remittance_detail/gettdate', 'InterbankRemittanceDetailController@gettdate');//取得轉存日期

    // 講座鐘點費轉帳-鐘點費入賬通知書
    Route::get('hourlyfee_notice', 'HourlyfeeNoticeController@index');
    Route::post('hourlyfee_notice/export', 'HourlyfeeNoticeController@export');
    Route::post('hourlyfee_notice/getTerms', 'HourlyfeeNoticeController@getTerms');//取得期別
    Route::post('hourlyfee_notice/send', 'HourlyfeeNoticeController@send');       //send

    // 講座鐘點費轉帳-匯款明細表
    Route::get('remittance_detail', 'RemittanceDetailController@index');
    Route::post('remittance_detail/export', 'RemittanceDetailController@export');

    // 年度講座所得統計表
    Route::get('yearly_income_all', 'YearlyIncomeAllController@index');
    Route::post('yearly_income_all/export', 'YearlyIncomeAllController@export');

    // 年度講座所得明細表
    Route::get('yearly_income_detail', 'YearlyIncomeDetailController@index');
    Route::post('yearly_income_detail/export', 'YearlyIncomeDetailController@export');
    Route::post('yearly_income_detail/getidno', 'YearlyIncomeDetailController@getidno');//取得身分證號

    // 智庫一覽表
    Route::get('thinktank_all', 'ThinktankAllController@index');
    Route::post('thinktank_all/export', 'ThinktankAllController@export');

    // 申請表及黏存單
    Route::get('application_stickynote', 'ApplicationStickynoteController@index');
    Route::post('application_stickynote/export', 'ApplicationStickynoteController@export');

    // 學員報名表
    Route::get('student_registration', 'StudentRegistrationController@index');
    Route::post('student_registration/export', 'StudentRegistrationController@export');

    // 學員名冊
    Route::get('student_list', 'StudentListController@index');
    Route::post('student_list/export', 'StudentListController@export');
    Route::post('student_list/getTerms', 'StudentListController@getTerms');//取得期別

    // 學員座位名牌卡
    Route::get('student_seat_namecard', 'StudentSeatNamecardController@index');
    Route::post('student_seat_namecard/export', 'StudentSeatNamecardController@export');
    Route::post('student_seat_namecard/getTerms', 'StudentSeatNamecardController@getTerms');//取得期別

    // 學員名牌
    Route::get('student_namecard', 'StudentNamecardController@index');
    Route::post('student_namecard/export', 'StudentNamecardController@export');
    Route::post('student_namecard/getTerms', 'StudentNamecardController@getTerms');//取得期別

    // 學員座位表
    Route::get('student_seat_list', 'StudentSeatListController@index');
    Route::post('student_seat_list/export', 'StudentSeatListController@export');
    Route::post('student_seat_list/getTerms', 'StudentSeatListController@getTerms');//取得期別
    Route::post('student_seat_list/getSites', 'StudentSeatListController@getSites');//取得教室

    // 學員簽到表
    Route::get('student_signin', 'StudentSigninController@index');
    Route::post('student_signin/export', 'StudentSigninController@export');
    Route::post('student_signin/getTerms', 'StudentSigninController@getTerms');//取得期別

    // 學員請假
    Route::get('student_leave', 'StudentLeaveController@index');
    Route::post('student_leave/export', 'StudentLeaveController@export');
    Route::post('student_leave/getTerms', 'StudentLeaveController@getTerms');//取得期別

    // 學員成績
    Route::get('student_grade', 'StudentGradeController@report_index');
    Route::post('student_grade/export', 'StudentGradeController@export');
    Route::post('student_grade/getTerms', 'StudentGradeController@getTerms');//取得期別

    // 學員研習證書
    Route::get('student_study_certificate', 'StudentStudyCertificateController@index');
    Route::post('student_study_certificate/export', 'StudentStudyCertificateController@export');
    Route::post('student_study_certificate/getTerms', 'StudentStudyCertificateController@getTerms');//取得期別
    Route::post('student_study_certificate/getserial', 'StudentStudyCertificateController@getserial');//getserial

    // 學員通訊錄
    Route::get('student_address_book', 'StudentAddressBookController@index');
    Route::post('student_address_book/export', 'StudentAddressBookController@export');
    Route::post('student_address_book/getTerms', 'StudentAddressBookController@getTerms');//取得期別

    // 學員郵寄名條
    Route::get('student_mail_nametape', 'StudentMailNametapeController@index');
    Route::post('student_mail_nametape/export', 'StudentMailNametapeController@export');
    Route::post('student_mail_nametape/getTerms', 'StudentMailNametapeController@getTerms');//取得期別

    // 學員歷次受訓紀錄
    Route::get('student_training_record', 'StudentTrainingRecordController@index');
    Route::post('student_training_record/export', 'StudentTrainingRecordController@export');
    Route::post('student_training_record/getidno', 'StudentTrainingRecordController@getidno');//取得期別

    // 人數統計-報到人數
    Route::get('count_signin', 'CountSigninController@index');
    Route::post('count_signin/export', 'CountSigninController@export');

    // 人數統計-各機關參訓人數
    Route::get('count_participate', 'CountParticipateController@index');
    Route::post('count_participate/export', 'CountParticipateController@export');

    // 人數統計-訓練人數
    Route::get('count_train', 'CountTrainController@index');
    Route::post('count_train/export', 'CountTrainController@export');
    Route::post('count_train/getTerms', 'CountTrainController@getTerms');//取得期別

    // 人數統計-在職訓練人數
    Route::get('count_onjob_train', 'CountOnjobTrainController@index');
    Route::post('count_onjob_train/export', 'CountOnjobTrainController@export');

    // 各委託機關經費負擔明細表
    Route::get('organ_burden_detail', 'OrganBurdenDetailController@index');
    Route::post('organ_burden_detail/export', 'OrganBurdenDetailController@export');

    // 委託機關地址條及函稿
    Route::get('organ_address_letter', 'OrganAddressLetterController@index');
    Route::post('organ_address_letter/export', 'OrganAddressLetterController@export');

    // 學員報名狀況對照表
    Route::get('student_registration_comparison', 'StudentRegistrationComparisonController@index');
    Route::post('student_registration_comparison/export', 'StudentRegistrationComparisonController@export');
    Route::post('student_registration_comparison/getTerms', 'StudentRegistrationComparisonController@getTerms');//取得期別


    // 學員服務機關與學號對照表
    Route::get('organ_sid_comparison', 'OrganSidComparisonController@index');
    Route::post('organ_sid_comparison/export', 'OrganSidComparisonController@export');
    Route::post('organ_sid_comparison/getTerms', 'OrganSidComparisonController@getTerms');//取得期別

    // 學員資料檢核表
    Route::get('student_checklist', 'StudentChecklistController@index');
    Route::post('student_checklist/export', 'StudentChecklistController@export');

    // 學員刷卡紀錄
    Route::get('student_card_record', 'StudentCardRecordController@index');
    Route::post('student_card_record/export', 'StudentCardRecordController@export');
    Route::post('student_card_record/getTerms', 'StudentCardRecordController@getTerms');

    // 培訓學員升遷異動紀錄
    Route::get('trainees_promotion_record', 'TraineesPromotionRecordController@index');
    Route::post('trainees_promotion_record/export', 'TraineesPromotionRecordController@export');

    // 訓練成效評估表(105)
    Route::get('training_evaluation_105', 'TrainingEvaluation105Controller@index');
    Route::post('training_evaluation_105/export', 'TrainingEvaluation105Controller@export');
    Route::post('training_evaluation_105/getTermByClass', 'TrainingEvaluation105Controller@getTermByClass');  //『期別』
    Route::post('training_evaluation_105/getTimeByClass', 'TrainingEvaluation105Controller@getTimeByClass');  //『第幾次調查』

    // 訓練成效評估結果統計圖表(105)
    Route::get('training_result_105', 'TrainingResult105Controller@index');
    Route::post('training_result_105/export', 'TrainingResult105Controller@export');
    Route::post('training_result_105/getTermByClass', 'TrainingResult105Controller@getTermByClass');  //『期別』
    Route::post('training_result_105/getTimeByClass', 'TrainingResult105Controller@getTimeByClass');  //『第幾次調查』


    // 年度各班期訓練成效評估統計表(105)
    Route::get('class_result_105', 'ClassResult105Controller@index');
    Route::post('class_result_105/export', 'ClassResult105Controller@export');

    // 年度講座之滿意度統計表
    Route::get('yearly_statistic', 'YearlyStatisticController@index');
    Route::post('yearly_statistic/export', 'YearlyStatisticController@export');

    // 講座滿意度一覽表
    Route::get('lecture_satisfactionlist', 'LectureSatisfactionlistController@index');
    Route::post('lecture_satisfactionlist/export', 'LectureSatisfactionlistController@export');

    // 參訓原因統計
    Route::get('participation_reason_statistics', 'ParticipationReasonStatisticsController@index');
    Route::post('participation_reason_statistics/export', 'ParticipationReasonStatisticsController@export');

    // 各班次行政支援成效比較表
    Route::get('class_support_comparison', 'ClassSupportComparisonController@index');
    Route::post('class_support_comparison/export', 'ClassSupportComparisonController@export');

    // 問卷(102~104)-訓練成效評估表
    Route::get('training_evaluation_102_104', 'TrainingEvaluation102104Controller@index');
    Route::post('training_evaluation_102_104/export', 'TrainingEvaluation102104Controller@export');
    Route::post('training_evaluation_102_104/getTermByClass', 'TrainingEvaluation102104Controller@getTermByClass');  //『期別』
    Route::post('training_evaluation_102_104/getTimeByClass', 'TrainingEvaluation102104Controller@getTimeByClass');  //『第幾次調查』

    // 問卷(102~104)-訓練成效評估結果統計圖表(104)
    Route::get('training_result_104', 'TrainingResult104Controller@index');
    Route::post('training_result_104/export', 'TrainingResult104Controller@export');
    Route::post('training_result_104/getTermByClass', 'TrainingResult104Controller@getTermByClass');  //『期別』
    Route::post('training_result_104/getTimeByClass', 'TrainingResult104Controller@getTimeByClass');  //『第幾次調查』

    // 問卷(102~104)-訓練成效評估結果統計圖表(102)
    Route::get('training_result_102', 'TrainingResult102Controller@index');
    Route::post('training_result_102/export', 'TrainingResult102Controller@export');
    Route::post('training_result_102/getTermByClass', 'TrainingResult102Controller@getTermByClass');  //『期別』
    Route::post('training_result_102/getTimeByClass', 'TrainingResult102Controller@getTimeByClass');  //『第幾次調查』

    // 問卷(102~104)-年度各班期訓練成效評估統計表
    Route::get('class_result_102_104', 'ClassResult102104Controller@index');
    Route::post('class_result_102_104/export', 'ClassResult102104Controller@export');

    // 問卷(96~101)-訓練成效評估表
    Route::get('training_evaluation_96_101', 'TrainingEvaluation96101Controller@index');
    Route::post('training_evaluation_96_101/export', 'TrainingEvaluation96101Controller@export');
    Route::post('training_evaluation_96_101/getTermByClass', 'TrainingEvaluation96101Controller@getTermByClass');  //『期別』
    Route::post('training_evaluation_96_101/getTimeByClass', 'TrainingEvaluation96101Controller@getTimeByClass');  //『第幾次調查』

    // 問卷(96~101)-訓練成效評估結果統計圖表
    Route::get('training_result_96_101', 'TrainingResult96101Controller@index');
    Route::post('training_result_96_101/export', 'TrainingResult96101Controller@export');
    Route::post('training_result_96_101/getTermByClass', 'TrainingResult96101Controller@getTermByClass');  //『期別』
    Route::post('training_result_96_101/getTimeByClass', 'TrainingEvaluation96101Controller@getTimeByClass');  //『第幾次調查』

    // 問卷(96~101)-年度各班期訓練成效評估統計表
    Route::get('class_result_96_101', 'ClassResult96101Controller@index');
    Route::post('class_result_96_101/export', 'ClassResult96101Controller@export');

    // 問卷(93~95)-訓練成效評估表
    Route::get('training_evaluation_93_95', 'TrainingEvaluation9395Controller@index');
    Route::post('training_evaluation_93_95/export', 'TrainingEvaluation9395Controller@export');
    Route::post('training_evaluation_93_95/getTermByClass', 'TrainingEvaluation9395Controller@getTermByClass');  //『期別』
    Route::post('training_evaluation_93_95/getTimeByClass', 'TrainingEvaluation9395Controller@getTimeByClass');  //『第幾次調查』

    // 問卷(93~95)-訓練成效評估結果統計圖表
    Route::get('training_result_93_95', 'TrainingResult9395Controller@index');
    Route::post('training_result_93_95/export', 'TrainingResult9395Controller@export');
    Route::post('training_result_93_95/getTermByClass', 'TrainingResult9395Controller@getTermByClass');  //『期別』
    Route::post('training_result_93_95/getTimeByClass', 'TrainingResult9395Controller@getTimeByClass');  //『第幾次調查』

    // 問卷(93~95)-年度各班期訓練成效評估統計表
    Route::get('class_result_93_95', 'ClassResult9395Controller@index');
    Route::post('class_result_93_95/export', 'ClassResult9395Controller@export');

    // 問卷(90~92)-訓練成效評估表
    Route::get('training_evaluation_90_92', 'TrainingEvaluation9092Controller@index');
    Route::post('training_evaluation_90_92/export', 'TrainingEvaluation9092Controller@export');
    Route::post('training_evaluation_90_92/getclass', 'TrainingEvaluation9092Controller@getclass'); // 取得班別
    Route::post('training_evaluation_90_92/getTermByClass', 'TrainingEvaluation9092Controller@getTermByClass');  //『期別』
    Route::post('training_evaluation_90_92/getTimeByClass', 'TrainingEvaluation9092Controller@getTimeByClass');  //『第幾次調查』

    // 問卷(90~92)-訓練成效評估結果統計圖表
    Route::get('training_result_90_92', 'TrainingResult9092Controller@index');
    Route::post('training_result_90_92/export', 'TrainingResult9092Controller@export');
    Route::post('training_result_90_92/getclass', 'TrainingResult9092Controller@getclass'); // 取得班別
    Route::post('training_result_90_92/getTermByClass', 'TrainingResult9092Controller@getTermByClass');  //『期別』
    Route::post('training_result_90_92/getTimeByClass', 'TrainingResult9092Controller@getTimeByClass');  //『第幾次調查』

    // 問卷(90~92)-年度各班期訓練成效評估統計表
    Route::get('class_result_90_92', 'ClassResult9092Controller@index');
    Route::post('class_result_90_92/export', 'ClassResult9092Controller@export');

    // 訓練成效評估表
    Route::get('training_evaluation_all', 'TrainingEvaluationAllController@index');
    Route::post('training_evaluation_all/export', 'TrainingEvaluationAllController@export');
    Route::post('training_evaluation_all/getclass', 'TrainingEvaluationAllController@getclass'); // 取得班別
    Route::post('training_evaluation_all/getTermByClass', 'TrainingEvaluationAllController@getTermByClass');  //『期別』

    // 訓練成效評估結果統計圖表
    Route::get('training_result_all', 'TrainingResultAllController@index');
    Route::post('training_result_all/export', 'TrainingResultAllController@export');
    Route::post('training_result_all/getclass', 'TrainingResultAllController@getclass'); // 取得班別
    Route::post('training_result_all/getTermByClass', 'TrainingResultAllController@getTermByClass');  //『期別』

    // 填寫領用申請處理
    Route::get('use_apply_process', 'UseApplyProcessController@index');
    Route::post('use_apply_process/export', 'UseApplyProcessController@export');

    // 申請流程查詢作業
    Route::get('apply_process_query', 'ApplyProcessQueryController@index');
    Route::post('apply_process_query/export', 'ApplyProcessQueryController@export');

    // 領用歸還資料維護
    Route::get('use_return_maintainance', 'UseReturnMaintainanceController@index');
    Route::post('use_return_maintainance/export', 'UseReturnMaintainanceController@export');

    // 領用情形一覽表
    Route::get('use_happening_list', 'UseHappeningListController@index');
    Route::post('use_happening_list/export', 'UseHappeningListController@export');

    // 借用歸還申請
    Route::get('borrow_return_apply', 'BorrowReturnApplyController@index');
    Route::post('borrow_return_apply/export', 'BorrowReturnApplyController@export');

    // 各訓練班期教室場地用餐及住宿
    Route::get('train_dining_living', 'TrainDiningLivingController@index');
    Route::post('train_dining_living/export', 'TrainDiningLivingController@export');

    // 各月需求表
    Route::get('monthly_demand', 'MonthlyDemandController@index');
    Route::post('monthly_demand/export', 'MonthlyDemandController@export');

    // 每周確認表
    Route::get('weekly_confirm', 'WeeklyConfirmController@index');
    Route::post('weekly_confirm/export', 'WeeklyConfirmController@export');

    // 每日分配表-教室場地
    Route::get('daily_distribution_classroom', 'DailyDistributionClassroomController@index');
    Route::post('daily_distribution_classroom/export', 'DailyDistributionClassroomController@export');

    // 每日分配表-會議場地
    Route::get('daily_distribution_conference', 'DailyDistributionConferenceController@index');
    Route::post('daily_distribution_conference/export', 'DailyDistributionConferenceController@export');

    // 每日分配表-住宿及休閒設施
    Route::get('daily_distribution_living', 'DailyDistributionLivingController@index');
    Route::post('daily_distribution_living/export', 'DailyDistributionLivingController@export');

    // 每日分配表-用餐數量
    Route::get('daily_distribution_dining', 'DailyDistributionDiningController@index');
    Route::post('daily_distribution_dining/export', 'DailyDistributionDiningController@export');

    // 管理月報表-教室場地
    Route::get('manage_monthly_classroom', 'ManageMonthlyClassroomController@index');
    Route::post('manage_monthly_classroom/export', 'ManageMonthlyClassroomController@export');

    // 管理月報表-會議場地
    Route::get('manage_monthly_conference', 'ManageMonthlyConferenceController@index');
    Route::post('manage_monthly_conference/export', 'ManageMonthlyConferenceController@export');

    // 管理月報表-住宿及休閒設施
    Route::get('manage_monthly_living', 'ManageMonthlyLivingController@index');
    Route::post('manage_monthly_living/export', 'ManageMonthlyLivingController@export');

    // 管理月報表-用餐數量
    Route::get('manage_monthly_dining', 'ManageMonthlyDiningController@index');
    Route::post('manage_monthly_dining/export', 'ManageMonthlyDiningController@export');

    // 管理月報表-場地使用管理月報表
    Route::get('manage_monthly_site', 'ManageMonthlySiteController@index');
    Route::post('manage_monthly_site/export', 'ManageMonthlySiteController@export');

    // 早餐及住宿籤名單-學員
    Route::get('breakfast_living_student', 'BreakfastLivingStudentController@index');
    Route::post('breakfast_living_student/export', 'BreakfastLivingStudentController@export');

    // 早餐及住宿籤名單-講座工作人員
    Route::get('breakfast_living_worker', 'BreakfastLivingWorkerController@index');
    Route::post('breakfast_living_worker/export', 'BreakfastLivingWorkerController@export');

    // 場地問卷與統計表(101)
    Route::get('site_survey_101', 'SiteSurvey101Controller@index');
    Route::post('site_survey_101/export', 'SiteSurvey101Controller@export');
    Route::post('site_survey_101/getTimeBySite', 'SiteSurvey101Controller@getTimeBySite');

    // 場地問卷與統計表(96~100)
    Route::get('site_survey_96_100', 'SiteSurvey96100Controller@index');
    Route::post('site_survey_96_100/export', 'SiteSurvey96100Controller@export');
    Route::post('site_survey_96_100/getTimeBySite', 'SiteSurvey96100Controller@getTimeBySite');

    // 會議場地使用統計表
    Route::get('conference_use_statistics', 'ConferenceUseStatisticsController@index');
    Route::post('conference_use_statistics/export', 'ConferenceUseStatisticsController@export');

    // 各班期用餐及住宿統計表
    Route::get('class_dining_living', 'ClassDiningLivingController@index');
    Route::post('class_dining_living/export', 'ClassDiningLivingController@export');

    // 各月份費用統計表
    Route::get('monthly_money', 'MonthlyMoneyController@index');
    Route::post('monthly_money/export', 'MonthlyMoneyController@export');

    // 用餐人數概況表
    Route::get('dining_table', 'DiningTableController@index');
    Route::post('dining_table/export', 'DiningTableController@export');

    // 訓練機構基本資料表
    Route::get('training_organ', 'TrainingOrganController@index');
    Route::post('training_organ/export', 'TrainingOrganController@export');

    // 年度教材總清冊
    Route::get('yearly_teaching_material', 'YearlyTeachingMaterialController@index');
    Route::post('yearly_teaching_material/export', 'YearlyTeachingMaterialController@export');

    // 班別教材一覽表
    Route::get('class_teaching_material', 'ClassTeachingMaterialController@index');
    Route::post('class_teaching_material/export', 'ClassTeachingMaterialController@export');
    Route::post('class_teaching_material/getTermByOP', 'ClassTeachingMaterialController@getTermByOP');  //『期別』

    // 講座教材一覽表
    Route::get('lecture_teaching_material', 'LectureTeachingMaterialController@index');
    Route::post('lecture_teaching_material/export', 'LectureTeachingMaterialController@export');

    // 開班教材清單
    Route::get('course_teaching_material', 'CourseTeachingMaterialController@index');
    Route::post('course_teaching_material/export', 'CourseTeachingMaterialController@export');
    Route::post('course_teaching_material/getTermByOP', 'CourseTeachingMaterialController@getTermByOP');  //『期別』

    // 收據清冊
    Route::get('receipt_inventory', 'ReceiptInventoryController@index');
    Route::post('receipt_inventory/export', 'ReceiptInventoryController@export');

    // 教材交印單
    Route::get('teaching_material_form', 'TeachingMaterialFormController@index');
    Route::post('teaching_material_form/export', 'TeachingMaterialFormController@export');

    // 教材交印統計表
    Route::get('teaching_material_statistics', 'TeachingMaterialStatisticsController@index');
    Route::post('teaching_material_statistics/export', 'TeachingMaterialStatisticsController@export');

    // 人數統計表
    Route::get('people_statistics', 'PeopleStatisticsController@index');
    Route::post('people_statistics/export', 'PeopleStatisticsController@export');

    // 師資一覽表
    Route::get('teacher_list', 'TeacherListController@index');
    Route::post('teacher_list/export', 'TeacherListController@export');

    // 調訓機關承辦人員聯絡名冊
    Route::get('change_organ_contact', 'ChangeOrganContactController@index');
    Route::post('change_organ_contact/export', 'ChangeOrganContactController@export');

    // 產製圖書系統用資料
    Route::get('production_book_system', 'ProductionBookSystemController@index');
    Route::post('production_book_system/export', 'ProductionBookSystemController@export');

    // 組織改制對照表維護
    Route::get('restructuring', 'RestructuringController@index');
    Route::get('restructuring/create', 'RestructuringController@create');
    Route::get('restructuring/edit/{id}', 'RestructuringController@edit');
    Route::post('restructuring', 'RestructuringController@store');
    Route::put('restructuring/{id}', 'RestructuringController@update');
    Route::delete('restructuring/{id}', 'RestructuringController@delete');


     //報表 phaseII

    //D4 行事表
    Route::get('schedule_list', 'ScheduleList@index');
    Route::post('schedule_list/export', 'ScheduleList@export');

    //D5 訓期一覽表
    Route::get('training_period_list', 'TrainingPeriodList@index');
    Route::post('training_period_list/export', 'TrainingPeriodList@export');
    // Route::post('training_period_list/getTerms', 'TrainingPeriodList@getTerms');//get terms
    
    //D6 班次分配表
    Route::get('class_distribution', 'ClassDistribution@index');
    Route::post('class_distribution/export', 'ClassDistribution@export');

    //D10 年度流路明細表
    Route::get('year_flow_path_detail', 'YearFlowPathDetail@index');
    Route::post('year_flow_path_detail/export', 'YearFlowPathDetail@export');

    //D14 接受委訓班期訓期一覽表
    Route::get('delegate_class_term_list', 'DelegateClassTermList@index');
    Route::post('delegate_class_term_list/export', 'DelegateClassTermList@export');

    //D15 接受委託辦理訓練需求彙總表
    Route::get('delegate_training_request', 'DelegateTrainingRequest@index');
    Route::post('delegate_training_request/export', 'DelegateTrainingRequest@export');

    //F3 調訓函
    Route::get('transfer_training_letter', 'TransferTrainingLetter@index');
    Route::post('transfer_training_letter/export', 'TransferTrainingLetter@export');
    Route::post('training_period_list/getTerms', 'TrainingPeriodList@getTerms');//get terms

    //F9 班期計畫表
    Route::get('class_term_plan', 'ClassTermPlan@index');
    Route::post('class_term_plan/export', 'ClassTermPlan@export');
    Route::post('class_term_plan/getTerms', 'ClassTermPlan@getTerms');//get terms

    //F10 講座期間授課課表
    Route::get('lecture_course_list', 'LectureCourseList@index');
    Route::post('lecture_course_list/export', 'LectureCourseList@export');

    //F11 教室使用一覽表
    Route::get('classroom_usage_list', 'ClassroomUsageList@index');
    Route::post('classroom_usage_list/export', 'ClassroomUsageList@export');
    Route::post('classroom_usage_list/getTerms', 'ClassroomUsageList@getTerms');//get terms
    Route::post('classroom_usage_list/getSites', 'ClassroomUsageList@getSites');//取得教室

    //F12 辦理流程期限表
    Route::get('process_timetable', 'ProcessTimetable@index');
    Route::post('process_timetable/export', 'ProcessTimetable@export');

    //F15 委訓費用明細表
    Route::get('delegate_training_cost', 'DelegateTrainingCost@index');
    Route::post('delegate_training_cost/export', 'DelegateTrainingCost@export');
    Route::post('delegate_training_cost/getTerms', 'DelegateTrainingCost@getTerms');//get terms

    //F16 委訓經費各單位分配額度表
    Route::get('delegate_training_cost_quota', 'DelegateTrainingCostQuota@index');
    Route::post('delegate_training_cost_quota/export', 'DelegateTrainingCostQuota@export');
    Route::post('delegate_training_cost_quota/getTerms', 'DelegateTrainingCostQuota@getTerms');//get terms

    //F18 教法運用統計圖表
    Route::get('teach_way_statics', 'TeachWayStatics@index');
    Route::post('teach_way_statics/export', 'TeachWayStatics@export');

    //F19 班別性質教法運用滿意度統計表
    Route::get('teach_way_satisfaction', 'TeachWaySatisfaction@index');
    Route::post('teach_way_satisfaction/export', 'TeachWaySatisfaction@export');

    //F20 課程教學教法數目分析
    Route::get('teach_way_course_analyze', 'TeachWayCourseAnalyze@index');
    Route::post('teach_way_course_analyze/export', 'TeachWayCourseAnalyze@export');

    //F21 班別性質與教法數目分析表
    Route::get('teach_way_calss_analyze', 'TeachWayCalssAnalyze@index');
    Route::post('teach_way_calss_analyze/export', 'TeachWayCalssAnalyze@export');

    //H19 講座特殊需求一覽表
    Route::get('lecture_special_need', 'LectureSpecialNeed@index');
    Route::post('lecture_special_need/export', 'LectureSpecialNeed@export');
    Route::post('lecture_special_need/getTerms', 'LectureSpecialNeed@getTerms');//get terms

    //H20 講座接待一覽表
    Route::get('lecture_reception_list', 'LectureReceptionList@index');
    Route::post('lecture_reception_list/export', 'LectureReceptionList@export');

    //H21 接送講座紀錄結算表
    Route::get('lecture_pickup_record', 'LecturePickupRecord@index');
    Route::post('lecture_pickup_record/export', 'LecturePickupRecord@export');

    //H22 接送講座紀錄結算總表
    Route::get('lecture_pickup_record_summary', 'LecturePickupRecordSummary@index');
    Route::post('lecture_pickup_record_summary/export', 'LecturePickupRecordSummary@export');

    //H23 講座寢室使用情形一覽表
    Route::get('lecture_bedroom_usage', 'LectureBedroomUsage@index');
    Route::post('lecture_bedroom_usage/export', 'LectureBedroomUsage@export');

    //H24 接送地點及次數一覽表
    Route::get('pickup_location_times', 'PickupLocationTimes@index');
    Route::post('pickup_location_times/export', 'PickupLocationTimes@export');

    //J1 學員報名表
    Route::get('student_registration', 'StudentRegistrationController@index');
    Route::post('student_registration/export', 'StudentRegistrationController@export');
    Route::post('student_registration/getTerms', 'StudentRegistrationController@getTerms');//get terms

    //N15 不用晚餐調查表
    Route::get('dinner_survey', 'DinnerSurvey@index');
    Route::post('dinner_survey/export', 'DinnerSurvey@export');
    Route::post('dinner_survey/getTerms', 'DinnerSurvey@getTerms');//get terms

    //N16 兩週班以上週一用早餐統計表
    Route::get('breakfast_statics', 'BreakfastStatics@index');
    Route::post('breakfast_statics/export', 'BreakfastStatics@export');
    Route::post('breakfast_statics/getTerms', 'BreakfastStatics@getTerms');//get terms

    //N17 伙食費核銷明細表
    Route::get('food_expense_writeoff', 'FoodExpenseWriteoff@index');
    Route::post('food_expense_writeoff/export', 'FoodExpenseWriteoff@export');
    Route::post('food_expense_writeoff/getTerms', 'FoodExpenseWriteoff@getTerms');//get terms

    //N18 伙食費核銷總表
    Route::get('food_expense_writeoff_summary', 'FoodExpenseWriteoffSummary@index');
    Route::post('food_expense_writeoff_summary/export', 'FoodExpenseWriteoffSummary@export');

    //N19 住宿登記概況表
    Route::get('stay_registration', 'StayRegistration@index');
    Route::post('stay_registration/export', 'StayRegistration@export');

    //N20 各樓住宿班次人員一覽表
    Route::get('staylist_byfloor', 'StayListByFloor@index');
    Route::post('staylist_byfloor/export', 'StayListByFloor@export');

    //N21 學員住宿分配暨輔導員執勤表
    Route::get('stay_distribution_dutylist', 'StayDistributionDutyList@index');
    Route::post('stay_distribution_dutylist/export', 'StayDistributionDutyList@export');

    //N22 學員住宿分配一覽表(分班)
    Route::get('stay_distribution_byclass', 'StayDistributionByClass@index');
    Route::post('stay_distribution_byclass/export', 'StayDistributionByClass@export');

    //N23 寢室分配情形一覽表
    Route::get('bedroom_distribution', 'BedroomDistribution@index');
    Route::post('bedroom_distribution/export', 'BedroomDistribution@export');

    //N24 開辦班次概況表(含住宿)
    Route::get('class_status', 'ClassStatus@index');
    Route::post('class_status/export', 'ClassStatus@export');

    //N25 寢具洗滌數量統計表
    Route::get('bedding_laundry_statics', 'BeddingLaundryStatics@index');
    Route::post('bedding_laundry_statics/export', 'BeddingLaundryStatics@export');

    //N26 住宿統計表(報到後)
    Route::get('stay_statics_after_reg', 'StayStaticsAfterReg@index');
    Route::post('stay_statics_after_reg/export', 'StayStaticsAfterReg@export');

    //N27 薦任9職等主管名單
    Route::get('level9_list', 'Level9List@index');
    Route::post('level9_list/export', 'Level9List@export');

    //N28 房卡盒標示紙
    Route::get('room_card_box_label', 'RoomCardBoxLabel@index');
    Route::post('room_card_box_label/export', 'RoomCardBoxLabel@export');

    //N29 房卡簽收單
    Route::get('room_card_receipt', 'RoomCardReceipt@index');
    Route::post('room_card_receipt/export', 'RoomCardReceipt@export');

    //N30 借住寢室寢具洗滌數量統計表
    Route::get('loan_bedding_laundry_statics', 'LoanBeddingLaundryStatics@index');
    Route::post('loan_bedding_laundry_statics/export', 'LoanBeddingLaundryStatics@export');

    //N31 場地借用行事曆
    Route::get('site_using_list', 'SiteUsingList@index');
    Route::post('site_using_list/export', 'SiteUsingList@export');

    //N32 場地使用成效統計表
    Route::get('site_usage_statics', 'SiteUsageStatics@index');
    Route::post('site_usage_statics/export', 'SiteUsageStatics@export');
    
    //N33 場地借用維護費收入明細統計表
    Route::get('site_using_maintain_datail', 'SiteUsingMaintainDatail@index');
    Route::post('site_using_maintain_datail/export', 'SiteUsingMaintainDatail@export');

    //N34 場地借用申請表
    Route::get('site_application', 'SiteApplication@index');
    Route::post('site_application/export', 'SiteApplication@export');

    //N35 各場地借用情形及維護費收入統計表
    Route::get('site_using_maintain_all', 'SiteUsingMaintainAll@index');
    Route::post('site_using_maintain_all/export', 'SiteUsingMaintainAll@export');

    //N36 場地借用概況表
    Route::get('site_using_overview', 'SiteUsingOverview@index');
    Route::post('site_using_overview/export', 'SiteUsingOverview@export');

});
