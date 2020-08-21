<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SiteManageService;
use App\Services\User_groupService;
use App\Models\T01tb;
use App\Models\T36tb;
use App\Helpers\ModifyLog;
use DB ;

class SiteManageController extends Controller
{
    /**
     * WaitingController constructor.
     * @param SiteManageService $siteManageService
     */
    public function __construct(SiteManageService $siteManageService, User_groupService $user_groupService)
    {
        $this->siteManageService = $siteManageService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('site_manage', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
            // dd($user_data);
            // dd(\session());
        });
        setProgid('site_manage');
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //班號
        $queryData['class'] = $request->get('class');
        // 班別名稱
        $queryData['name'] = $request->get('name');
        //年
        $this_yesr = date('Y') - 1911;
        if(null == $request->get('yerly')){
            $queryData['yerly'] = $this_yesr;
        }else{
            $queryData['yerly'] = $request->get('yerly');
        }
        // 辦班院區
        $queryData['branch'] = $request->get('branch');
        // 課程分類
        if(null == $request->get('classtype')){
            $queryData['classtype'] = 'G';
        }else{
            $queryData['classtype'] = $request->get('classtype');
        }
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        if(empty($request->all())) {
            return view('admin/site_manage/list', compact('queryData'));
        }
        // 取得列表資料
        $data = $this->siteManageService->getSiteManageList($queryData);
        return view('admin/site_manage/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     */
    public function create()
    {
        return view('admin/site_manage/form');
    }
    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        // 取得POST資料
        $data = $request->all();
        $data['classtype'] = $data['classtypename'];
        $data['class'] = $data['yerly'].$data['classtype'].$data['class_b'].$data['branchcode'];
        $data['type'] ='13';
        $check = T01tb::where('class',$data['class'])->get()->toArray();
        // 刪除多於變數
        unset($data['_token'],$data['classtypename'],$data['class_b'],$data['newstyle']);

        if(!empty($check)){
            return back()->with('result', '0')->with('message', '班號重複無法創建!');
        }else{
            $data['time1'] = isset($data['time1'])?$data['time1']:'N';
            $data['time2'] = isset($data['time2'])?$data['time2']:'N';
            $data['time3'] = isset($data['time3'])?$data['time3']:'N';
            $data['time4'] = isset($data['time4'])?$data['time4']:'N';
            $data['time5'] = isset($data['time5'])?$data['time5']:'N';
            $data['time6'] = isset($data['time6'])?$data['time6']:'N';
            $data['time7'] = isset($data['time7'])?$data['time7']:'N';
            $data['holiday'] = isset($data['holiday'])?$data['holiday']:'N';
            $data['style']='4';
            //if($data['time1'] =='N' && $data['time2'] =='N' && $data['time3'] =='N' && $data['time4'] =='N' && $data['time5'] =='N' && $data['time6'] =='N' && $data['time7'] =='N' && $data['holiday'] =='N' )  return back()->with('result', '0')->with('message', '請新增上課方式!');
            // 新增
            DB::beginTransaction();
            DB::connection()->enableQueryLog(); //啟動SQL_LOG
            try {
                T01tb::create($data);
                $sql = DB::getQueryLog();
                $nowdata = T01tb::where('class',$data['class'])->get()->toarray();
                createModifyLog('I','t01tb','',$nowdata,end($sql));
                DB::commit();
                return redirect('/admin/site_manage/'.$data['class'].'/edit')->with('result', '1')->with('message', '新增成功!'); 
            } catch (Exception $e) {
                DB::rollback();
                return back()->with('result', '0')->with('message', '新增失敗');
            }
            
        }
    }
    /**
     * 編輯頁
     *
     * @param $class
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($class)
    {
        $data = T01tb::find($class);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/site_manage/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // 取得POST資料
        $data = $request->all();
        $class = $data['class'];
        $check = T01tb::where('class',$class)->get()->toArray();
        // 刪除多於變數
        unset($data['_token'],$data['_method'],$data['classtypename'],$data['class_a'],$data['class_b'],$data['newstyle'],$data['class']);


        if(empty($check)){
            return back()->with('result', '0')->with('message', '查無此班號!');
        }else{
            $data['time1'] = isset($data['time1'])?$data['time1']:'N';
            $data['time2'] = isset($data['time2'])?$data['time2']:'N';
            $data['time3'] = isset($data['time3'])?$data['time3']:'N';
            $data['time4'] = isset($data['time4'])?$data['time4']:'N';
            $data['time5'] = isset($data['time5'])?$data['time5']:'N';
            $data['time6'] = isset($data['time6'])?$data['time6']:'N';
            $data['time7'] = isset($data['time7'])?$data['time7']:'N';
            $data['holiday'] = isset($data['holiday'])?$data['holiday']:'N';
            DB::beginTransaction();
            DB::connection()->enableQueryLog(); //啟動SQL_LOG
            try {
                //更新
                $olddata = T01tb::where('class',$class)->get()->toarray();
                T01tb::where('class', $class)->update($data);
                $sql = DB::getQueryLog();
                $nowdata = T01tb::where('class',$class)->get()->toarray();
                createModifyLog('U','t01tb',$olddata,$nowdata,end($sql));
                //上課方式修改告知需重新預約教室
                if($check[0]['style']!=$data['style']  ){
                    $olddata = T01tb::where('class',$class)->get()->toarray();
                    T36tb::where('class', $class)->delete();
                    $sql = DB::getQueryLog();
                    createModifyLog('D','t01tb',$olddata,'',end($sql));
                    DB::commit();
                    return back()->with('result', '1')->with('message', '修改成功，請重新預約教室!');
                }elseif($data['style']==4){
                    if( $check[0]['time1']!=$data['time1'] || $check[0]['time2']!=$data['time2'] ||$check[0]['time3']!=$data['time3'] ||$check[0]['time4']!=$data['time4'] ||$check[0]['time5']!=$data['time5'] ||$check[0]['time6']!=$data['time6'] ||$check[0]['time7']!=$data['time7'] ||$check[0]['holiday']!=$data['holiday']){
                        $olddata = T36tb::where('class',$class)->get()->toarray();
                        T36tb::where('class', $class)->delete();
                        $sql = DB::getQueryLog();
                        createModifyLog('D','t36tb',$olddata,'',end($sql));
                        DB::commit();
                        return back()->with('result', '1')->with('message', '修改成功，請重新預約教室!');
                    }else{
                        DB::commit();
                        return back()->with('result', '1')->with('message', '修改成功!');
                    }
                }else{
                    DB::commit();
                    return back()->with('result', '1')->with('message', '修改成功!');
                }
            } catch (Exception $e) {
                DB::rollback();
                return back()->with('result', '0')->with('message', '修改失敗');
            }
                
        }
    }

    /**
     * 刪除處理
     *
     * @param $class
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($class)
    {
        if(empty($class) || $class=='') return back()->with('result', '0')->with('message', '查無班期!');

        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try {
            $olddata = T01tb::where('class',$class)->get()->toarray();
            $del = T01tb::where('class',$class)->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t01tb',$olddata,'',end($sql));
            DB::commit();
            return redirect('/admin/site_manage')->with('result', '1')->with('message', '刪除成功!');
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    /*批次新增*/
    public function batchadd(Request $request)
    {
        // 年度
        $queryData['yerly'] = $request->get('groupyerly');
        if(!isset($queryData['yerly'])) redirect('/admin/site_manage');

        $check = $this->siteManageService->getSiteManageList($queryData);
        if(count($check)>0) return redirect('/admin/site_manage')->with('result', '0')->with('message','此年度已有資料，無法使用此功能!');

        $data = $this->siteManageService->getClassesdata($queryData['yerly'] -1); //抓上一年度的資料
        if(!$data) return redirect('/admin/site_manage')->with('result', '0')->with('message','此年度上一年無資料，無法使用此功能!');

        $count = 0;
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try {
            foreach ($data as $key => $value) {
                $count = str_pad($count +1,2,'0',STR_PAD_LEFT);;
                $value['yerly'] = $queryData['yerly'];
                $value['times'] = '';
                $value['class'] = $queryData['yerly'].$value['classtype'].$count.$value['branchcode'];
                T01tb::create($value);// 新增
                $sql = DB::getQueryLog();
                $nowdata = T01tb::where('class',$value['class'])->get()->toarray();
                createModifyLog('I','t01tb','',$nowdata,end($sql));
            }
            DB::commit();
            return redirect('/admin/site_manage')->with('result', '1')->with('message','新增成功!'); 
        } catch (Exception $e) {
            DB::rollback();
            return redirect('/admin/site_manage')->with('result', '0')->with('message','新增失敗!');
        }
    }
    /*批次刪除*/
    public function batchdel(Request $request)
    {
        // 年度
        $queryData['yerly'] = $request->get('groupyerly');
        if(!isset($queryData['yerly'])) redirect('/admin/site_manage');

        $check = $this->siteManageService->getSiteManageList($queryData);
        if(count($check)==0) return redirect('/admin/site_manage')->with('result', '0')->with('message','此年度無資料，無法使用此功能!');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        $olddata = T01tb::where('class','like', $queryData['yerly'].'%')->where('classtype','<>','')->get()->toarray();
        $result = T01tb::where('class','like', $queryData['yerly'].'%')->where('classtype','<>','')->delete();
        $sql = DB::getQueryLog();
        createModifyLog('D','t01tb',$olddata,'',end($sql));
        if($result){
            return redirect('/admin/site_manage')->with('result', '1')->with('message','刪除成功!');
        }else{
            return redirect('/admin/site_manage')->with('result', '0')->with('message','刪除失敗!');
        }
    }

}