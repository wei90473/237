<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ItineracyService;
use App\Services\User_groupService;
use App\Models\itineracy_sitting;
use App\Models\Itineracy;
use DB;

class ItineracyUnitController extends Controller
{
    private $type = '3'; //單元
    /**
     * ItineracyController constructor.
     * @param ItineracyService $ItineracyService
     */
    public function __construct(ItineracyService $itineracyService, User_groupService $user_groupService)
    {
        $this->itineracyService = $itineracyService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('itineracy_unit', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request){
        $queryData['code'] = $request->get('code');
        $queryData['name'] = $request->get('name');
        $queryData['type'] = $this->type;
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        $queryData['max'] = $this->itineracyService->getItineracyMax($queryData['type'])+1;
        if(empty($request->all()) ){
           return view('admin/itineracy_unit/list', compact('queryData'));
        }
        $data = $this->itineracyService->getItineracyList($queryData);
        return view('admin/itineracy_unit/list', compact('data','queryData'));
    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request){
        $data = $request->all();
        $check = $this->itineracyService->getItineracyList(array('type'=>$this->type,'code'=>$data['c_code']));
        if( count($check) > 0 )  return back()->with('result', '0')->with('message', '新增失敗，代號重複!');

        $insert = $this->itineracyService->insertItineracy( array('type'=>$this->type,'code'=>$data['c_code'],'name'=>$data['c_name']) );
        if($insert){
            return back()->with('result', '1')->with('message', '新增成功!');
        }else{
            return back()->with('result', '0')->with('message', '新增失敗!');
        }
    }
    /**
     * 編輯頁更新處理
     */
    public function update(Request $request, $code) {
        if ($code==999) return back()->with('result', '0')->with('message', '更新失敗!');

        $data = $request->all();
        if($code != $data['E_code']){
            $check = $this->itineracyService->getItineracyList(array('type'=>$this->type,'code'=>$data['E_code']));
            if( count($check) > 0 )  return back()->with('result', '0')->with('message', '更新失敗，代號重複!');

        }
        $updata = itineracy_sitting::where('type', $this->type)->where('code',$code)->update( array('code'=>$data['E_code'],'name'=>$data['E_name']) );
        if($update){
            return back()->with('result', '1')->with('message', '更新成功!');
        }else{
            return back()->with('result', '0')->with('message', '更新失敗!');
        }
    }

    /**
     * 刪除處理
     */
    public function destroy($code) {
        if ($code==999) return back()->with('result', '0')->with('message', '刪除失敗，錯誤代號!');

        $del = itineracy_sitting::where('type', $this->type)->where('code',$code)->delete();
        if($del){
            return redirect('admin/itineracy_unit')->with('result', '1')->with('message', '刪除成功!');
        } else {
            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}

