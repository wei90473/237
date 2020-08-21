<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\ArrangementService;
use App\Services\EmployService;
use Auth;
use DB;
use Illuminate\Http\Request;

use App\Models\S01tb;
use App\Models\M09tb;
use App\Models\T04tb;

class LockClassController extends Controller
{
    public function __construct(ArrangementService $arrangementService,EmployService $employ)
    {
        $this->arrangementService = $arrangementService;
        $this->employService=$employ;
    }

    public function index(Request $request)
    {

        $queryData = $request->only([
            't01tb.yerly',                // 年度
            't01tb.class',                // 班號
            't01tb.name',                 // 班級名稱
            't01tb.branchname',           // 分班名稱
            't01tb.branch',               // 辦班院區
            't01tb.process',              // 班別類型
            't01tb.commission',           // 委訓單位
            't01tb.traintype',            // 訓練性質
            't01tb.type',                 // 班別性質
            't01tb.categoryone',           // 類別1
            "t04tb.term",                  // 期別
            "t04tb.site_branch",           // 上課地點
            "t04tb.sponsor",                // 班務人員
            "t04tb.month",                 // 月份
            'sdate_start',                      // 開訓日期範圍(起)
            'sdate_end',                        // 開訓日期範圍(訖)
            'edate_start',                      // 結訓日期範圍(起)
            'edate_end',                        // 結訓日期範圍(訖)
            'training_start',                   // 在訓期間範圍(起)
            'training_end',                     // 在訓期間範圍(起)
            '_paginate_qty'
        ]);

        $s01tbM = S01tb::where('type', '=', 'M')->get()->pluck('name', 'code');
        $sponsors = M09tb::all();

        $data = $this->arrangementService->getOpenClassList($queryData); // 取得開班資料

        // $sponsor = $this->employService->getSponsor();
        // //年
        // $this_yesr = date('Y') - 1911;

        // if(null == $request->get('yerly')){
        //     $queryData['yerly'] = $this_yesr;
        // }else{
        //     $queryData['yerly'] = $request->get('yerly');
        // }
        // //班號
        // $queryData['class'] = $request->get('class');
        // $queryData['name'] = $request->get('name');

        // // 分班名稱**
        // $queryData['class_branch_name'] = $request->get('class_branch_name');
        // // 期別
        // $queryData['term'] = $request->get('term');
        // // 辦班院區
        // $queryData['branch'] = $request->get('branch');
        // // 班別類型
        // $queryData['process'] = $request->get('process');

        // $queryData['sponsor'] = $request->get('sponsor');
        // // 訓練性質
        // $queryData['traintype'] = $request->get('traintype');
        // // 班別性質
        // $queryData['type'] = $request->get('type');
        // $queryData['typeone'] = $request->get('typeone');
        // $queryData['sdate'] = $request->get('sdate');
        // $queryData['edate'] = $request->get('edate');
        // $queryData['sdate2'] = $request->get('sdate2');
        // $queryData['edate2'] = $request->get('edate2');
        // $queryData['sdate3'] = $request->get('sdate3');
        // $queryData['edate3'] = $request->get('edate3');

        // // 排序欄位
        // $queryData['_sort_field'] = $request->get('_sort_field');
        // // 排序方向
        // $queryData['_sort_mode'] = $request->get('_sort_mode');
        // // 每頁幾筆
        // $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;


		// $queryData['search'] = $request->get('search');

        // $data = $this->arrangementService->getOpenClassList($queryData); // 取得開班資料

        $sess = $request->session()->get('lock_class');

        return view('admin/lockclass/list' , compact('data', 'queryData', 'classList','sponsors','sess', 's01tbM'));
    }

    public function lock(Request $request)
    {
        $class = $request->input('class_lock');
        $term = $request->input('term_lock');
        // $data = $this->arrangementService->getOpenClassList($queryData); // 取得開班資料

        $t04tb = T04tb::selectRaw('t01tb.class, t04tb.term, t01tb.name, t01tb.branch, t04tb.sdate, t04tb.edate, t01tb.process, t01tb.commission, m09tb.username, t01tb.branchname, t04tb.sponsor')
                      ->join('t01tb', 't01tb.class', '=', 't04tb.class')
                      ->leftJoin('m09tb', 't04tb.sponsor', '=', 'm09tb.userid')
                      ->where('t01tb.class', '=', $class)
                      ->where('t04tb.term', '=', $term)->first();

        // collect($t04tb->toArray())->only();
        $lock_class = $t04tb->toArray();
        //var_dump($data[0]->m09tb->username);
        // $lock_class=[];
        // $lock_class['class']=$data[0]->t01tb->class;
        // $lock_class['name']=$data[0]->t01tb->name;
        // $lock_class['branch']=$data[0]->t01tb->branch;
        // $lock_class['term']=$data[0]->term;
        // $lock_class['sdate']=$data[0]->sdateformat;
        // $lock_class['edate']=$data[0]->edateformat;
        // $lock_class['process']=$data[0]->t01tb->process;
        // $lock_class['commission']=$data[0]->t01tb->commission;
        // if(isset($data[0]->m09tb->username)){
        //     $lock_class['sponsor']=$data[0]->m09tb->username;
        // }else{
        //     $lock_class['sponsor']='';
        // }

        // $lock_class['branchname']=$data[0]->t01tb->branchname;
        $request->session()->put('lock_class',$lock_class);
        //$test=$request->session()->all();
        //dd($test);
        // return back()->with('result', '1')->with('message', '班期鎖定');
        return back();
        // echo '<script>window.close();</script>';
    }

    public function unlock(Request $request)
    {
        $request->session()->forget('lock_class');
        // return back()->with('result', '1')->with('message', '取消班期鎖定');
        return back();
        // echo '<script>window.close();</script>';
    }

}
