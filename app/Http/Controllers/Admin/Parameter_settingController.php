<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Parameter_settingService;
use App\Models\License_plate_setting;
use App\Models\Room;
use App\Models\Car_fare;
use DB ;
use App\Services\User_groupService;

class Parameter_settingController extends Controller
{
    /**
     * Parameter_settingController constructor.
     * @param Parameter_settingService $parameter_settingService
     */
    public function __construct(Parameter_settingService $parameter_settingService, User_groupService $user_groupService)
    {
        $this->parameter_settingService = $parameter_settingService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('parameter_setting', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('parameter_setting');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index1(Request $request)
    {

        // 呼號
        $queryData['call'] = $request->get('call');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData);
        // echo "\n</pre>\n";
        // die();
        $queryData['search'] = $request->get('search');

        if($queryData['search'] != 'search' ){
          $queryData2['call'] = 'none';
          $data = $this->parameter_settingService->getParameter_setting1List($queryData2);
        }else{
          $data = $this->parameter_settingService->getParameter_setting1List($queryData);
        }

        return view('admin/parameter_setting/list1', compact('data', 'queryData', 'sponsor'));
    }

    public function index2(Request $request)
    {

        // 呼號
        $queryData['room_number'] = $request->get('room_number');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData);
        // echo "\n</pre>\n";
        // die();
        $queryData['search'] = $request->get('search');

        if($queryData['search'] != 'search' ){
          $queryData2['room_number'] = 'none';
          $data = $this->parameter_settingService->getParameter_setting2List($queryData2);
        }else{
          $data = $this->parameter_settingService->getParameter_setting2List($queryData);
        }

        return view('admin/parameter_setting/list2', compact('data', 'queryData', 'sponsor'));
    }

    public function index3(Request $request)
    {

        // 呼號
        $queryData['county'] = $request->get('county');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData);
        // echo "\n</pre>\n";
        // die();
        $queryData['search'] = $request->get('search');

        if($queryData['search'] != 'search' ){
          $queryData2['county'] = 'none';
          $data = $this->parameter_settingService->getParameter_setting3List($queryData2);
        }else{
          $data = $this->parameter_settingService->getParameter_setting3List($queryData);
        }

        return view('admin/parameter_setting/list3', compact('data', 'queryData', 'sponsor'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create1(Request $request)
    {

        return view('admin/parameter_setting/form1');
    }

    public function create2(Request $request)
    {

        return view('admin/parameter_setting/form2');
    }

    public function create3(Request $request)
    {

        return view('admin/parameter_setting/form3');
    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store1(Request $request)
    {
        // 取得POST資料
        $data = $request->all();
        unset($data['_token']);
        $data['license_plate'] = strtoupper($data['license_plate']);

        $call_isset = License_plate_setting::where('call', $data['call'])->first();
        $license_isset = License_plate_setting::where('license_plate', $data['license_plate'])->first();

        if(!empty($call_isset)){
        	return back()->with('result', '0')->with('message', '呼號重複!');
        }
        if(!empty($license_isset)){
        	return back()->with('result', '0')->with('message', '車牌重複!');
        }

        //新增
        $result = License_plate_setting::create($data);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('parameter_setting')){
          $nowdata = License_plate_setting::where('license_plate', $data['license_plate'])->get()->toarray();
          createModifyLog('I','License_plate_setting','',$nowdata,end($sql));
        }

        return redirect('/admin/parameter_setting_1/'.$result->id)->with('result', '1')->with('message', '新增成功!');
    }

    public function store2(Request $request)
    {
        // 取得POST資料
        $data = $request->all();
        unset($data['_token']);
        $data['room_number'] = strtoupper($data['room_number']);

        $room_isset = Room::where('room_number', $data['room_number'])->first();

        if(!empty($room_isset)){
        	return back()->with('result', '0')->with('message', '寢室編號重複!');
        }

        //新增
        $result = Room::create($data);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('parameter_setting')){
          $nowdata = Room::where('room_number', $data['room_number'])->get()->toarray();
          createModifyLog('I','Room','',$nowdata,end($sql));
        }

        return redirect('/admin/parameter_setting_2/'.$result->id)->with('result', '1')->with('message', '新增成功!');
    }

    public function store3(Request $request)
    {
        // 取得POST資料
        $data = $request->all();
        unset($data['_token']);

        //新增
        $result = Car_fare::create($data);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('parameter_setting')){
          $nowdata = Car_fare::where('county', $data['county'])->where('area', $data['area'])->get()->toarray();
          createModifyLog('I','Car_fare','',$nowdata,end($sql));
        }

        return redirect('/admin/parameter_setting_3/'.$result->id)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $id
     */
    public function show1($id)
    {
        return $this->edit1($id);
    }

    public function show2($id)
    {
        return $this->edit2($id);
    }

    public function show3($id)
    {
        return $this->edit3($id);
    }

    /**
     * 編輯頁
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit1($id)
    {
        $data = License_plate_setting::find($id);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/parameter_setting/form1', compact('data'));
    }

    public function edit2($id)
    {
        $data = Room::find($id);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/parameter_setting/form2', compact('data'));
    }

    public function edit3($id)
    {
        $data = Car_fare::find($id);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/parameter_setting/form3', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update1(Request $request, $id)
    {

        // 取得POST資料
        $data = $request->all();
        unset($data['_method'], $data['_token']);

        $data['license_plate'] = strtoupper($data['license_plate']);

        $call_isset = License_plate_setting::where('call', $data['call'])->first();
        $license_isset = License_plate_setting::where('license_plate', $data['license_plate'])->first();

        $old_data = License_plate_setting::where('id', $id)->first();

        if(!empty($call_isset) && $old_data->call != $data['call']){
        	return back()->with('result', '0')->with('message', '呼號重複!');
        }
        if(!empty($license_isset) && $old_data->license_plate != $data['license_plate']){
        	return back()->with('result', '0')->with('message', '車牌重複!');
        }

        if(checkNeedModifyLog('parameter_setting')){
            $olddata = License_plate_setting::where('id', $id)->get()->toarray();
        }

        License_plate_setting::where('id', $id)->update($data);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('parameter_setting')){
            $nowdata = License_plate_setting::where('id', $id)->get()->toarray();
            createModifyLog('U','License_plate_setting',$olddata,$nowdata,end($sql));
        }

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    public function update2(Request $request, $id)
    {

        // 取得POST資料
        $data = $request->all();
        unset($data['_method'], $data['_token']);

        $data['room_number'] = strtoupper($data['room_number']);

        $room_isset = Room::where('room_number', $data['room_number'])->first();

        $old_data = Room::where('id', $id)->first();

        if(!empty($room_isset && $old_data->room_number != $data['room_number'])){
        	return back()->with('result', '0')->with('message', '寢室編號重複!');
        }

        if(checkNeedModifyLog('parameter_setting')){
            $olddata = Room::where('id', $id)->get()->toarray();
        }

        Room::where('id', $id)->update($data);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('parameter_setting')){
            $nowdata = Room::where('id', $id)->get()->toarray();
            createModifyLog('U','Room',$olddata,$nowdata,end($sql));
        }

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    public function update3(Request $request, $id)
    {

        // 取得POST資料
        $data = $request->all();
        unset($data['_method'], $data['_token']);

        if(checkNeedModifyLog('parameter_setting')){
            $olddata = Car_fare::where('id', $id)->get()->toarray();
        }

        Car_fare::where('id', $id)->update($data);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('parameter_setting')){
            $nowdata = Car_fare::where('id', $id)->get()->toarray();
            createModifyLog('U','Car_fare',$olddata,$nowdata,end($sql));
        }

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    public function destroy1($id)
    {
        if ($id) {

        	//刪除前使用檢查

            if(checkNeedModifyLog('parameter_setting')){
                $olddata = License_plate_setting::where('id', $id)->get()->toarray();
            }

            License_plate_setting::find($id)->delete();

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('parameter_setting')){
                createModifyLog('D','License_plate_setting',$olddata,'',end($sql));
            }

            return redirect("/admin/parameter_setting_1?search=search")->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    public function destroy2($id)
    {
        if ($id) {

        	//刪除前使用檢查

            if(checkNeedModifyLog('parameter_setting')){
                $olddata = Room::where('id', $id)->get()->toarray();
            }

            Room::find($id)->delete();

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('parameter_setting')){
                createModifyLog('D','Room',$olddata,'',end($sql));
            }

            return redirect("/admin/parameter_setting_2?search=search")->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    public function destroy3($id)
    {
        if ($id) {

        	//刪除前使用檢查

            if(checkNeedModifyLog('parameter_setting')){
                $olddata = Car_fare::where('id', $id)->get()->toarray();
            }

            Car_fare::find($id)->delete();

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('parameter_setting')){
                createModifyLog('D','Car_fare',$olddata,'',end($sql));
            }

            return redirect("/admin/parameter_setting_3?search=search")->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }


}
