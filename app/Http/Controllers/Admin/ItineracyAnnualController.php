<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ItineracyService;
use App\Services\User_groupService;
use App\Models\itineracy;
use App\Models\itineracy_sitting;
use App\Models\itineracy_annual;
use App\Models\itineracy_survey;
use App\Models\T01tb;
use App\Models\S03tb;
use DB;

class ItineracyAnnualController extends Controller
{
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
          if(in_array('itineracy_annual', $user_group_auth)){
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
        $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // $queryData['term'] = '1';
        $queryData['name'] = $request->get('name');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        $queryData['max'] = $this->itineracyService->getAnnualMax($queryData['yerly'])+1;
        if(empty($request->all()) ){
           $queryData['choices'] = $this->_get_year_list();
           return view('admin/itineracy_annual/list', compact('queryData'));
        }
        $data = $this->itineracyService->getAnnualList($queryData);
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/itineracy_annual/list', compact('data','queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        $queryData['yerly'] = date('Y')-1911;
        $term = $this->itineracyService->getAnnualMax($queryData['yerly']);
        $queryData['term'] = $term+1;
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/itineracy_annual/form', compact('queryData'));
    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request){
        $data = $request->all();
        $check = $this->itineracyService->getAnnualList(array('yerly'=>$data['yerly'],'term'=>$data['term']));
        if( count($check)>0 ){
            return back()->with('result', '0')->with('message', '新增失敗，期別重複!');
        }
        unset($data['_token']);
        itineracy::create($data);
        for($i=1;$i<23;$i++){
            itineracy_survey::create(array('yerly'=>$data['yerly'],'term'=>$data['term'],'city'=>str_pad($i,2,'0',STR_PAD_LEFT)));
        }
        return redirect('admin/itineracy_annual/edit/'.$data['yerly'].$data['term'])->with('result', '1')->with('message', '新增成功!');
    }


    /**
     * 編輯頁
     *
     * @param $yerly_term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($yerly_term)
    {
        $yerly = substr($yerly_term, 0,3);
        $term  = substr($yerly_term, 3);

        $data = $this->itineracyService->getAnnualList(array('yerly'=>$yerly,'term'=>$term ));
        $data = $data[0];
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/itineracy_annual/form', compact('data','queryData'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $yerly_term)
    {
        $yerly = substr($yerly_term, 0,3);
        $term  = substr($yerly_term, 3);
        $data = $request->all();
        if($yerly != $data['yerly'] || $term != $data['term']){
            $check = $this->itineracyService->getAnnualList(array('yerly'=>$data['yerly'],'term'=>$data['term']));
            if( count($check)>0 ){
                return back()->with('result', '0')->with('message', '更新失敗，期別重複!');
            }
        }

        unset($data['_token'],$data['_method']);
        $updata = itineracy::where('yerly',$yerly)->where('term',$term)->update($data);
        if($updata){
            return redirect('admin/itineracy_annual/edit/'.$data['yerly'].$data['term'])->with('result', '1')->with('message', '更新成功!');
        }else{
            return back()->with('result', '0')->with('message', '更新失敗!');
        }
    }

    /**
     * 主題頁
     *
     * @param $yerly_term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function setting(Request $request,$yerly_term){
        if(!isset($yerly_term)) return view('admin/errors/error');

        $queryData['yerly'] = substr($yerly_term, 0,3);
        $queryData['term'] = substr($yerly_term, 3);
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        $items = itineracy_annual::where('yerly',$queryData['yerly'])->where('term',$queryData['term'])->max('items');
        $queryData['items'] = $items+1;
        $data = $this->itineracyService->getAnnual($queryData);
        $themelist = itineracy_sitting::select('type','code','name')->where('type','1')->get();
        $unitlist = itineracy_sitting::select('type','code','name')->where('type','3')->get();
        $categorylist = itineracy_sitting::select('type','code','name')->where('type','2')->get();

        return view('admin/itineracy_annual/setting', compact('queryData','data','themelist','unitlist','categorylist'));
    }
    //主題新增
    public function settingstore(Request $request){
        $data = $request->all();
        if(!isset($data['unit'])){
          $data['unit'] = '';
        }
        if(!isset($data['category'])){
          $data['category'] = '';
        }

        $check_items = itineracy_annual::select('id','items')->where('yerly', $data['yerly'])->where('term', $data['term'])->where('items', $data['items'])->first();
        // dd($check_items);
        if(is_null($check_items)){
          $insert = itineracy_annual::insert( array('yerly'=>$data['yerly'],'term'=>$data['term'],'items'=>$data['items'],'type1'=>$data['theme'],'type3'=>$data['unit'],'type2'=>$data['category'] ) );
        }else{
          return back()->with('result', '0')->with('message', '項次重覆，請重新輸入！');
        }
        if($insert){
            return back()->with('result', '1')->with('message', '新增成功!');
        }else{
            return back()->with('result', '0')->with('message', '新增失敗!');
        }
    }
    //主題修改
    public function settingupdate(Request $request,$id){
        $check = itineracy_annual::select('id','items','yerly','term')->where('id', $id)->first()->toArray();
        // var_dump($check[0]);exit();
        if (is_null($check)) return back()->with('result', '0')->with('message', '修改失敗，錯誤代號!');

        $data = $request->all();
        if(!isset($data['E_unit'])){
          $data['E_unit'] = '';
        }
        if(!isset($data['E_category'])){
          $data['E_category'] = '';
        }
        // dd($check);
        // dd($data);
        if($data['E_items']!=$check['items']){
          $check_items = itineracy_annual::select('id','items')->where('yerly', $check['yerly'])->where('term', $check['term'])->where('items', $data['E_items'])->first();
          if(is_null($check_items)){
            $updata = itineracy_annual::where('id', $id)->update( array('type1'=>$data['E_theme'],'items'=>$data['E_items'],'type3'=>$data['E_unit'],'type2'=>$data['E_category']) );
          }else{
            return back()->with('result', '0')->with('message', '項次重覆，請重新輸入！');
          }
        }else{
          $updata = itineracy_annual::where('id', $id)->update( array('type1'=>$data['E_theme'],'type3'=>$data['E_unit'],'type2'=>$data['E_category']) );
        }

        if($updata){
            return back()->with('result', '1')->with('message', '更新成功!');
        }else{
            return back()->with('result', '0')->with('message', '更新失敗!');
        }
    }

    //主題刪除
    public function destroy($id){
        $check = itineracy_annual::select('id','yerly','term')->where('id', $id)->first();
        if (is_null($check)) return back()->with('result', '0')->with('message', '刪除失敗，錯誤代號!');

        $del = itineracy_annual::where('id', $id)->delete();
        if($del){
            return redirect('admin/itineracy_annual/setting/'.$check['yerly'].$check['term'])->with('result', '1')->with('message', '刪除成功!');
        } else {
            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    public function _get_year_list(){
        $year_list = array();
        $year_now = date('Y');
        $this_yesr = $year_now - 1910;

        for($i=$this_yesr; $i>=90; $i--){
            $year_list[$i] = $i;
        }
        // jd($year_list,1);
        return $year_list;
    }
    private function __result( $code,$msg ){
        echo json_encode(array('status' => $code , 'msg' => $msg));
    exit;
  }
}
