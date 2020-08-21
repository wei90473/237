<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SiteScheduleService;
use App\Services\User_groupService;
use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\T06tb;
use App\Models\T22tb;
use App\Models\T23tb;
use App\Models\T36tb;
use App\Models\T37tb;
use App\Models\T47tb;
use App\Models\T97tb;
use App\Models\M09tb;
use App\Models\M12tb;
use App\Models\M14tb;
use App\Models\M25tb;
use App\Models\Edu_classroom;
use App\Models\S02tb;
use App\Helpers\ModifyLog;
use DB ;

class SiteScheduleController extends Controller
{
    /**
     * WaitingController constructor.
     * @param SiteScheduleService $siteScheduleService
     */
    public function __construct(SiteScheduleService $siteScheduleService, User_groupService $user_groupService)
    {
        $this->siteScheduleService = $siteScheduleService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('site_schedule', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }

        });
        setProgid('site_schedule');
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 年度
        $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // 月份
        $queryData['month'] = $request->get('month');
        // 班號
        $queryData['class'] = $request->get('class');
        // 班別名稱
        $queryData['name'] = $request->get('name');
        // 取得期別
        $queryData['term'] = $request->get('term');
        // 上課地點 
        $queryData['sitebranch'] = $request->get('sitebranch');
        // 院區
        $queryData['branch'] = $request->get('branch');
        // 訓練性質
        $queryData['traintype'] = $request->get('traintype');
        // 班務人員
        $queryData['sponsor'] = $request->get('sponsor');
        //開訓日期
        $queryData['sdate'] = $request->get('sdate_begin');
        $queryData['edate']   = $request->get('sdate_end');
        //結訓日期
        $queryData['sdate2'] = $request->get('edate_begin');
        $queryData['edate2']   = $request->get('edate_end');
        //在訓期間
        $queryData['sdate3'] = $request->get('indate_begin');
        $queryData['edate3']   = $request->get('indate_end');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        if(empty($request->all())) {
            $queryData['choices'] = $this->_get_year_list();
            return view('admin/site_schedule/list', compact('queryData'));
        }
        $data = $this->siteScheduleService->getSiteScheduleList($queryData);
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/site_schedule/list', compact('data', 'queryData'));

    }

    /**
     * 編輯頁
     *
     * @param $class_term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($class_term)
    {
        $term = $queryData['term'] = substr($class_term, -2);
        $class = $queryData['class'] = substr($class_term, 0,-2);
        $check = T01tb::where('class', $class)->first();

        if ( ! $check) {
            return view('admin/errors/error');
        }
        $queryData['class'] = $class;
        $data = $this->siteScheduleService->getSiteScheduleList($queryData);
        $data = $data[0];
        if($data['sdate'] < (date('Y', strtotime('now'))-1911).date('md', strtotime('now')) ){
            return back()->with('result', '0')->with('message', '這筆資料已為歷史資料，可由【調整行事曆】進行修改!');
        }
        if($data['site'] =='oth' ){
            $data['branch'] = '3';
        }
        $section = m09tb::select('section')->groupby('section')->orderby('id')->get()->toArray();

        $Nantoulist = Edu_classroom::select('roomno','roomname')->get();
        $Taipeilist = M14tb::get();
        return view('admin/site_schedule/edit', compact('data','Nantoulist','Taipeilist','section') );
    }
    //根據人員返回單位(ajax)
    public function getsection(Request $request){
        $Sponsor = $request->get('Sponsor');
        if(!isset($Sponsor)) $this->__result('1','');

        $section = m09tb::select('section')->where('userid',$Sponsor)->first();

        $list = m09tb::select('section')->groupby('section')->orderby('id')->get()->toArray();
        foreach ($list as $key => $value) {
            if($value['section']==$section['section']) $this->__result('0',$value['section']);
        }

        $this->__result('1','');

    }
    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request){
        // 取得POST資料
        $data = $request->all();
        //'1.判斷是否空白
        if($data['sdate']==''){
            return back()->with('result', '0')->with('message', '資料有缺失請更正!');
        }else{
            $sdate = $data['sdate'];
        }
        // if(isset($data['section']) && $data['section']!=''  ){
        //     $list = m09tb::select('section')->groupby('section')->orderby('id')->get()->toArray();
        //     $data['section'] = $list[$data['section']]['section'];
        // }
        if(isset($data['site_branch']) && $data['site_branch']!='2'){
            //檢查時間
            $check = t22tb::select('affirm')->where('class',$data['class'])->where('term',$data['term'])->first(); //確認凍結日
            if(!empty($check) &&  $check['affirm'] < (date('Y', strtotime('now'))-1911).date('md', strtotime('now')) ){
                return back()->with('result', '0')->with('message', '開課日期已過確認凍結日期，無法修改');
            }
        }
        unset($data['_method'], $data['_token'] );
        $queryData['class'] = $data['class'];
        $queryData['term'] = $data['term'];
        $olddata = $this->siteScheduleService->getSiteScheduleList($queryData);
        $data['quota'] = $olddata[0]['quota'];
        $data['time'] = isset($data['time'])? $data['time'] : $olddata[0]['time'];
        $timebase = $this->gettime($data['time']);
        if(isset($data['site_branch'])){
            $type = '1';
            if ($data['site_branch']=='1'){
                $data['site'] = $data['siteT'];
            }elseif($data['site_branch']=='2'){
                $data['site'] = $data['siteN'];
            }elseif($data['site_branch']=='3'){
                $data['site'] = 'oth';
            }else{
                $data['site_branch'] = NULL;
                $data['site'] = NULL;
            }
            unset($data['siteT'], $data['siteN']);
        }else{
            $type = '2';
            //排程明細 修改
            $data['site_branch'] = $olddata[0]['site_branch'];
            $data['site'] = $olddata[0]['site'];
            $data['fee'] = $olddata[0]['fee'];
            $data['sponsor'] = $olddata[0]['sponsor'];
        }
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try{
            $sdateVids = (substr($data['sdate'], 0,-4)+1911).substr($data['sdate'], -4);
            $check = $this->checksdate($data,$sdateVids);
            if($check['result']==0){
                return back()->with('result', '0')->with('message', $check['msg']);
            }else{
                $datearray = $check['msg'];
            }
            $data['edate'] = end($datearray);
            $olddata = t36tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t36tb::where('class',$data['class'])->where('term',$data['term'])->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t36tb',$olddata,'',end($sql));

            $olddata = t97tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t97tb::where('class',$data['class'])->where('term',$data['term'])->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t97tb',$olddata,'',end($sql));
            
            $olddata = t22tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t22tb::where('class',$data['class'])->where('term',$data['term'])->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t22tb',$olddata,'',end($sql));

            $olddata = t37tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t37tb::where('class',$data['class'])->where('term',$data['term'])->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t37tb',$olddata,'',end($sql));
            //檢查預約
            $check = $this->checksite($data);
            if($check['result']==0)  return back()->with('result', '0')->with('message', $check['msg']);
            //寫入
            if(!is_null($data['site'])){
                $check = $this->reservation($data,$datearray,$sdateVids,$timebase);
                if($check['result']==0)  return back()->with('result', '0')->with('message', $check['msg']);

            }
            //需先檢查(t23tb)中是否已有該班期資料。
            $olddata = t23tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            if(!empty($olddata)){
                t23tb::where('class',$data['class'])->where('term',$data['term'])->delete();
                $sql = DB::getQueryLog();
                createModifyLog('D','t23tb',$olddata,'',end($sql));
            }
            //將資料Update至【t47tb入口網站班別資料檔】
            $indata = array();
            $indata['sdate'] = (date('Y',strtotime($sdateVids .' -15 Day'))-1911).date('md',strtotime($sdateVids .' -15 Day'));
            $indata['edate'] = (date('Y',strtotime($sdateVids .' -1 Day'))-1911).date('md',strtotime($sdateVids .' -1 Day'));
            $olddata = t47tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t47tb::where('class',$data['class'])->where('term',$data['term'])->update($indata);
            $sql = DB::getQueryLog();
            $nowdata = t47tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            createModifyLog('U','t47tb',$olddata,$nowdata,end($sql));
            //修改
            $olddata = t04tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t04tb::where('class',$data['class'])->where('term',$data['term'])->update($data);
            $sql = DB::getQueryLog();
            $nowdata = t04tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            createModifyLog('U','t04tb',$olddata,$nowdata,end($sql));
            DB::commit();
            if($type=='1'){
                return back()->with('result', '1')->with('message', '修改成功');
            }else{
                return redirect('/admin/site_schedule/details?yerly='.substr($data['class'],0,-3))->with('result', '1')->with('message', '修改成功!');
            }
            
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '更新失敗，請稍後再試!');
        }
        
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
        if ($data['site_branch']=='1'){
            $data['site'] = $data['siteT'];
        }elseif($data['site_branch']=='2'){
            $data['site'] = $data['siteN'];
        }elseif($data['site_branch']=='3'){
            $data['site'] = 'oth';
        }else{
            $data['site'] = NULL;
        }
        // if($data['section']!=''){
        //     $list = m09tb::select('section')->groupby('section')->orderby('id')->get()->toArray();
        //     $data['section'] = $list[$data['section']]['section'];
        // }
        $data['time'] = isset($data['time'])?$data['time'] : 'D';
        //'1.判斷是否空白
        if($data['class']==''|| $data['term']=='' || $data['quota']=='' || $data['sdate']==''){
            return back()->with('result', '0')->with('message', '資料有缺失請更正!');
        }
        $data['term'] = str_pad($data['term'],2,'0',STR_PAD_LEFT);
        //'2.開課日期是否正確
        $sdateVids = (substr($data['sdate'], 0,-4)+1911).substr($data['sdate'], -4);
        $check = $this->checksdate($data,$sdateVids);
        if($check['result']==0){
            return back()->with('result', '0')->with('message', $check['msg']);
        }else{
            $datearray = $check['msg'];
        }
        $data['edate'] = end($datearray);
        // '4.t04tb 開班資料檔是否有資料
        $check = t04tb::select('class')->where('class',$data['class'])->where('term',$data['term'])->get();
        if(count($check)!=0)  return back()->with('result', '0')->with('message', '資料重複(已有此班的開班資料)!');
        // '5.t36tb 行事曆檔是否有資料
        $check = t36tb::select('class')->where('class',$data['class'])->where('term',$data['term'])->get();
        if(count($check)!=0)  return back()->with('result', '0')->with('message', '行事曆檔已有此班資料，請重新輸入!');

        // '7.檢查是否可預約場地
        $check = $this->checksite($data);
        if($check['result']==0)  return back()->with('result', '0')->with('message', $check['msg']);
        // if($data['site']!=''){
        //     if(strtotime($requestVids) < strtotime('now') ) return back()->with('result', '0')->with('message', '上課日期已過需求凍結日，不可預約會議室!');
        // }
        unset($data['_token'],$data['siteT'],$data['siteN']);
        $style = T01tb::select('style','time1' ,'time2' ,'time3' ,'time4' ,'time5' ,'time6' ,'time7' ,'holiday','trainday','kind','period','trainhour')->where('class',$data['class'])->get()->toArray();
        $style = $style[0];
        // var_dump($data);exit;
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        $timebase = $this->gettime($data['time']);
        try{
            //寫入
            $check = $this->reservation($data,$datearray,$sdateVids,$timebase);
            if($check['result']==0)  return back()->with('result', '0')->with('message', $check['msg']);

            t04tb::insert($data);
            $sql = DB::getQueryLog();
            $nowdata = t04tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            createModifyLog('I','t04tb','',$nowdata,end($sql));
            unset($indata);
            $indata['class'] = $data['class'];
            $indata['term'] = $data['term'];
            $indata['degree'] = '6';
            $indata['enroll'] = '3';
            $indata['county'] = '10';
            $indata['site'] = '臺北市新生南路3段30號。';
            $indata['sdate'] = (date('Y',strtotime($sdateVids .' -15 Day'))-1911).date('md',strtotime($sdateVids .' -15 Day'));
            $indata['edate'] = (date('Y',strtotime($sdateVids .' -1 Day'))-1911).date('md',strtotime($sdateVids .' -1 Day'));
            $indata['credit'] = $style['kind'] == '3'? $style['trainhour']:'0';
            $indata['unit'] = '1';
            $indata['lodging'] = '0';
            $indata['meal'] = '0';
            $indata['upload2'] = 'N';
            $indata['grade'] = 'N';
            $indata['leave'] = 'N';
            t47tb::insert($indata);
            $sql = DB::getQueryLog();
            $nowdata = t47tb::where('class',$indata['class'])->where('term',$indata['term'])->get()->toArray();
            createModifyLog('I','t47tb','',$nowdata,end($sql));
            DB::commit();
            return redirect('/admin/site_schedule/'.$data['class'].$data['term'].'/edit')->with('result', '1')->with('message', '創建成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '創建失敗，請稍後再試!');
        }
    }

    // 新增頁面
    public function add(Request $request)
    {
        $classlist = T01tb::select('class','name')->where('classtype','<>','')->orderby('class','DESC')->get()->toArray();
        $section = m09tb::select('section')->groupby('section')->orderby('id')->get()->toArray();
        $Nantoulist = Edu_classroom::select('roomno','roomname')->get();
        $Taipeilist = M14tb::get();
        return view('admin/site_schedule/edit', compact('classlist','Nantoulist','Taipeilist','section'));
    }
    /**
     * 刪除處理
     *
     * @param $class_term
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($class_term){
        $term = $queryData['term'] = substr($class_term, -2);
        $class = $queryData['class'] = substr($class_term, 0,-2);
        $check = T01tb::where('class', $class)->first();
        if ( ! $check) {
            return view('admin/errors/error');
        }
        $check = T06tb::select('class')->where('class',$class)->where('term',$term)->get()->toArray();
        if(count($check)!=0)  return back()->with('result', '0')->with('message', '已有課程資料(t06tb 課程表資料檔)，無法刪除!');

        $queryData['class'] = $class;
        $data = $this->siteScheduleService->getSiteScheduleList($queryData);
        $data = $data[0];
        if($data['site_branch']!='2'){
            //檢查時間
            $check = t22tb::select('affirm')->where('class',$data['class'])->where('term',$data['term'])->first(); //確認凍結日
            if(!empty($check) &&  $check < (date('Y', strtotime('now'))-1911).date('md', strtotime('now')) ){
                return back()->with('result', '0')->with('message', '開課日期已過確認凍結日期，無法刪除');
            }
        }
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try{
            $olddata = t04tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t04tb::where('class',$data['class'])->where('term',$data['term'])->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t04tb',$olddata,'',end($sql));
            $olddata = t36tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t36tb::where('class',$data['class'])->where('term',$data['term'])->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t36tb',$olddata,'',end($sql));
            $olddata = t22tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t22tb::where('class',$data['class'])->where('term',$data['term'])->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t22tb',$olddata,'',end($sql));
            $olddata = t37tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t37tb::where('class',$data['class'])->where('term',$data['term'])->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t37tb',$olddata,'',end($sql));
            $olddata = t47tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t47tb::where('class',$data['class'])->where('term',$data['term'])->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t47tb',$olddata,'',end($sql));
            $olddata = t23tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t23tb::where('class',$data['class'])->where('term',$data['term'])->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t23tb',$olddata,'',end($sql));

            DB::commit();
            return redirect('/admin/site_schedule')->with('result', '1')->with('message', '刪除成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '刪除失敗，請稍後再試!');
        }
    }
    /*
     * 排程明細
     *
     */
    public function details(Request $request){
        // 年度(預設今年)
        $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // 月份
        $queryData['smonth'] = $request->get('smonth');
        $queryData['emonth'] = $request->get('emonth');
        if($queryData['smonth'] > $queryData['emonth'] || $queryData['smonth'] > 12 || $queryData['emonth'] > 12) return back()->with('result', '0')->with('message', '開始月份不可大於結束月份，請重新輸入');
        $list = T01tb::select('class','name')->where('type','13')->where('class','like', str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT).'%')->orderby('class')->get()->toArray();
        $data = $this->siteScheduleService->getSiteScheduleList($queryData);
        // 刻度
        $queryData['weekday'] = is_null($request->get('weekday') )? '1': $request->get('weekday'); //(1:週 2:天)
        $queryData['day'] = array();
        $queryData['month'] = array();
        $firstday = ($queryData['yerly']+1911).'0101';
        $week = date('w',strtotime($firstday));
        $queryData['sumquota']['all'] = 0;
        $i=0;
        if($queryData['weekday']=='1'){
            if($week<1){
                $firstday = date('Ymd',strtotime($firstday.' +1 DAY'));
            }elseif($week>1){
                $firstday = date('Ymd',strtotime($firstday.' +'.(8 - $week).' DAY'));
            }
        }
        //數據
        foreach ($data as $key => $value) {
            $sdate = $value->sdate + 19110000;
            $edate = $value->edate + 19110000;
            $data[$i]['days'] = (strtotime($sdate)-strtotime($firstday))/86400;
            $data[$i]['days'] = ($data[$i]['days'] < 0)? 0:$data[$i]['days'] ;
            $data[$i]['enddays'] = (strtotime($edate)-strtotime($firstday))/86400;
            $data[$i]['enddays'] = ($data[$i]['enddays'] < 0)? 0:$data[$i]['enddays'] ;
            if($queryData['weekday']=='1'){
                $data[$i]['days'] = floor($data[$i]['days'] /7);
                $data[$i]['enddays'] = floor($data[$i]['enddays'] /7);
            }
            //合計
            $queryData['sumquota'][$data[$i]['days']] = isset($queryData['sumquota'][$data[$i]['days']])? $queryData['sumquota'][$data[$i]['days']] + $value->quota : $value->quota;
            $queryData['sumquota']['all'] = $queryData['sumquota']['all'] + $value->quota;
            $i++;
        }
        //基本表格
        if($queryData['weekday']=='1'){
            while ( date('Y',strtotime($firstday)) == ($queryData['yerly']+1911)) {
               $queryData['day'][] =  date('d',strtotime($firstday));
               $month = date('n',strtotime($firstday));
               $queryData['month'][$month] = isset($queryData['month'][$month])?$queryData['month'][$month]+1:1 ;
               $firstday = date('Ymd',strtotime($firstday.' +7 DAY'));
            }
        }elseif($queryData['weekday']=='2'){
            while ( date('Y',strtotime($firstday)) == ($queryData['yerly']+1911)) {
               $queryData['day'][] =  date('d',strtotime($firstday));
               $month = date('n',strtotime($firstday));
               $queryData['month'][$month] = isset($queryData['month'][$month])?$queryData['month'][$month]+1:1 ;
               $firstday = date('Ymd',strtotime($firstday.' +1 DAY'));
            }
        }else{
            return view('admin/errors/error');
        }

        return view('admin/site_schedule/details',compact('list','data', 'queryData'));
    }
    /*
     * 調整行事曆
     *
     */
    public function calendar(Request $request){
        $class = $request->get('class');
        $term = $request->get('term');
        // 年度(預設今年)
        $queryData['yerly'] = is_null($request->get('class') )? date('Y')-1912: substr($class,0,-3);

        $list = $this->siteScheduleService->getSiteScheduleList($queryData);
        $data = array();
        foreach ($list as $key => $value) {
            $data[$value['class'].$value['term']] = $value;
        }
        $list = T01tb::select('class','name')->where('type','13')->where('class','like', str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT).'%')->orderby('class')->get()->toArray();
        $queryData['class'] = $class;
        $queryData['term'] = $term;
        // var_dump($data);
        // exit();
        $calendarlist = $this->siteScheduleService->getcalendarlist($class,$term);
        if(count($calendarlist)==0) {
            return view('admin/site_schedule/calendar',compact('list','data', 'queryData'));
        }else{
            return view('admin/site_schedule/calendar',compact('list','data', 'queryData','calendarlist'));
        }

    }
    /**
     * 行事曆更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function calendarupdate(Request $request){
        // 取得POST資料
        $data = $request->all();
        // 判斷是否空白
        if($data['select']=='' || strlen($data['select']) !=7 ) return back()->with('result', '0')->with('message', '請選擇正確的日期!');

        $check = t36tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->get()->toarray();
        if(count($check) > 0 ) return back()->with('result', '0')->with('message', '錯誤，上課日期重複!');

        $t04 = t04tb::select('sdate','edate')->where('class',$data['class'])->where('term',$data['term'])->first();
        unset($data['_token'],$data['_method']);
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try{
            $olddata = t36tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['select'])->get()->toArray();
            t36tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['select'])->update(array('date'=>$data['date'],'site'=>''));
            $sql = DB::getQueryLog();
            $nowdata = t36tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['select'])->get()->toArray();
            createModifyLog('U','t36tb',$olddata,$nowdata,end($sql));
            $olddata = t23tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['select'])->get()->toArray();
            t23tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['select'])->update(array('date'=>$data['date']));
            $sql = DB::getQueryLog();
            $nowdata = t23tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['select'])->get()->toArray();
            createModifyLog('U','t23tb',$olddata,$nowdata,end($sql));
            $olddata = t47tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            if($data['date'] > $t04['edate']){
                $data['edate'] = $data['date'];
                t47tb::where('class',$data['class'])->where('term',$data['term'])->update(array('edate'  => $data['date']));
            }elseif($data['date'] < $t04['sdate']){
                $data['sdate'] = $data['date'];
                t47tb::where('class',$data['class'])->where('term',$data['term'])->update(array('sdate'  => $data['date']));
            }
            $sql = DB::getQueryLog();
            $nowdata = t47tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            createModifyLog('U','t47tb',$olddata,$nowdata,end($sql));
            unset($data['date'],$data['select']);
            $olddata = t04tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t04tb::where('class',$data['class'])->where('term',$data['term'])->update($data);
            $sql = DB::getQueryLog();
            $nowdata = t04tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            createModifyLog('U','t04tb',$olddata,$nowdata,end($sql));
            DB::commit();
            return back()->with('result', '1')->with('message','修改成功');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message','修改失敗');
        }

    }
    /**
     * 行事曆刪除處理
     *
     * @param $class_term
     * @return \Illuminate\Http\RedirectResponse
     */
    public function calendardestroy(Request $request){
        // 取得POST資料
        $data = $request->all();
        // 判斷是否空白
        if($data['date']=='' || strlen($data['date']) !=7 ) return back()->with('result', '0')->with('message', '請選擇正確的日期!!');

        $t04 = t04tb::select('sdate','edate')->where('class',$data['class'])->where('term',$data['term'])->first();
        unset($data['_token'],$data['_method'],$data['lineup']);
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try{
            $olddata = t36tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->get()->toArray();
            t36tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t36tb',$olddata,'',end($sql));
            $olddata = t36tb::select(DB::raw('Max(date) as edate'), DB::raw('Min(date) as sdate'))->where('class',$data['class'])->where('term',$data['term'])->get()->toarray();
            if($data['date'] == $t04->edate){
                $update = array('edate'=>$olddata[0]['edate']);
            }elseif($data['date'] == $t04->sdate){
                $update = array('sdate'=>$olddata[0]['sdate']);
            }
            $t04data = t04tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t04tb::where('class',$data['class'])->where('term',$data['term'])->update($update);
            $sql = DB::getQueryLog();
            $nowdata = t04tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            createModifyLog('U','t04tb',$t04data,$nowdata,end($sql));
            $t47data = t47tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t47tb::where('class',$data['class'])->where('term',$data['term'])->update($update);
            $sql = DB::getQueryLog();
            $nowdata = t47tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            createModifyLog('U','t47tb',$t47data,$nowdata,end($sql));
            //刪除【t23tb 辦班需求確認檔】
            $olddata = t23tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->get()->toArray();
            t23tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t23tb',$olddata,'',end($sql));
            DB::commit();
            return back()->with('result', '1')->with('message','刪除成功');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message','刪除失敗');
        }

    }
    /**
     * 行事曆新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function calendarstore(Request $request){
        // 取得POST資料
        $data = $request->all();
        // 判斷是否空白
        if($data['date']=='' || strlen($data['date']) !=7 ) return back()->with('result', '0')->with('message', '請輸入正確的日期!');

        $t04 = t04tb::select('sdate','edate')->where('class',$data['class'])->where('term',$data['term'])->first();
        unset($data['_token'],$data['_method'],$data['lineup'],$data['site']);
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try{
            $insert = t36tb::insert($data);
            $sql = DB::getQueryLog();
            $nowdata = t36tb::where('class',$data['class'])->where('term',$data['term'])->where('site',$data['site'])->where('date',$data['date'])->get()->toArray();
            createModifyLog('U','t36tb','',$nowdata,end($sql));
            if($data['date'] > $t04->edate){
                $update = array('edate'=>$data['date']);
            }elseif($data['date'] < $t04->sdate){
                $update = array('sdate'=>$data['date']);
            }
            $olddata = t04tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t04tb::where('class',$data['class'])->where('term',$data['term'])->update($update);
            $sql = DB::getQueryLog();
            $nowdata = t04tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            createModifyLog('U','t04tb',$olddata,$nowdata,end($sql));
            $olddata = t47tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            t47tb::where('class',$data['class'])->where('term',$data['term'])->update($update);
            $sql = DB::getQueryLog();
            $nowdata = t47tb::where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
            createModifyLog('U','t47tb',$olddata,$nowdata,end($sql));
            DB::commit();
            return back()->with('result', '1')->with('message','新增成功');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message','新增失敗');
        }
    }


    public function _get_year_list()
    {
        $year_list = array();
        $year_now = date('Y');
        $this_yesr = $year_now - 1910;

        for($i=$this_yesr; $i>=90; $i--){
            $year_list[$i] = $i;
        }
        // jd($year_list,1);
        return $year_list;
    }
    //取得時間
    public function gettime($timetype=NULL){ //A:早上 B:下午 C:晚間 D:白天(A+B) E:全天(A+B+C)
        $data = array();
        if($timetype=='A'){
            $data[0]['time'] = 'A';
            $data[0]['stime'] = '0830';
            $data[0]['etime'] = '1200';
        }elseif($timetype=='B'){
            $data[0]['time'] = 'B';
            $data[0]['stime'] = '1300';
            $data[0]['etime'] = '1630';
        }elseif($timetype=='C'){
            $data[0]['time'] = 'C';
            $data[0]['stime'] = '1800';
            $data[0]['etime'] = '2130';
        }elseif(is_null($timetype) || $timetype=='D'){  //預設
            $data[0]['time'] = 'A';
            $data[0]['stime'] = '0830';
            $data[0]['etime'] = '1200';
            $data[1]['time'] = 'B';
            $data[1]['stime'] = '1300';
            $data[1]['etime'] = '1630';
        }elseif($timetype=='E'){
            $data[0]['time'] = 'A';
            $data[0]['stime'] = '0830';
            $data[0]['etime'] = '1200';
            $data[1]['time'] = 'B';
            $data[1]['stime'] = '1300';
            $data[1]['etime'] = '1630';
            $data[2]['time'] = 'C';
            $data[2]['stime'] = '1800';
            $data[2]['etime'] = '2130';
        }else{
            return FALSE;
        }
        return $data;
    }
    //取得結束日期(ajax)
    public function getedate(Request $request){
        // 取得POST資料
        $data = $request->all();
        $sdateVids = (substr($data['sdate'], 0,-4)+1911).substr($data['sdate'], -4);
        $style = T01tb::select('style','time1' ,'time2' ,'time3' ,'time4' ,'time5' ,'time6' ,'time7' ,'holiday','trainday','kind','period','trainhour')->where('class',$data['_class'])->get()->toArray();
        if(count($style)==0)  $this->__result('1','無此班別');

        $style = $style[0];
        $day = $style['kind'] == '2'? $style['period']:$style['trainday']; //課程天數
        //每週上課日規則
        if($style['style']=='2') {
            $weekrule = array(1,3,5);
        }elseif($style['style']=='3'){
            $weekrule = array(2,4);
        }elseif($style['style']=='4'){
            $weekrule = array();
            for($i=0;$i<7;$i++){
                if($i==0 && $style['time7']=='Y'){
                    $weekrule[] = $i;
                }elseif(isset($style['time'.$i]) && $style['time'.$i]=='Y'){
                    $weekrule[] = $i;
                }
            }
        }
        if(count($weekrule)==0)  $this->__result('1','未設定上課方式，請至班別資料處理編輯');
        // if(count($weekrule)==0)  $this->__result('1','開班日期與上課方式不符，上課方式'.$day.'天');
        $week = date('w', strtotime($sdateVids) );
        $ch = array('1' =>'一.','2' =>'二.','3' =>'三.','4' =>'四.','5' =>'五.','6' =>'六.','0' =>'日.');
        if(!in_array($week, $weekrule))  {
            $errormsg = '開班日期與上課方式不符，上課方式每週';
            for($i=0;$i<count($weekrule);$i++){
                $errormsg .= $ch[$weekrule[$i]];
            }
            $errormsg = substr($errormsg,0,-1);
            $errormsg .='上課';
            if($style['holiday']=="Y") $errormsg .= '含國定假日' ;

            $this->__result('1',$errormsg);
        }else{
            $mission=0;
            $checklist = m12tb::select('date')->where('date','>',(date('Y')-1913).'1231')->get()->toArray(); //抓年度開始的資料
            $holidaylist = array();
            foreach ($checklist as $key => $value) {
                $holidaylist[$value['date']]='1';
            }
            $datearray = array();
            while ($mission<$day){
                $week = date('w', strtotime($sdateVids) );
                $head = intval(substr($sdateVids,0,-4))-1911;
                $bady = substr($sdateVids,-4);
                if($style['holiday']=='N' && isset($holidaylist[$head.$bady]) ){ //如果是國定假日 跳過

                }elseif(in_array($week, $weekrule) ) {
                    $datearray[] = $head.$bady;
                    $mission = $mission +1;
                }
                if($mission==$day) {
                    break;
                }
                $sdateVids = date('Ymd',strtotime($sdateVids .' +1 Day'));
            }

        }
         $this->__result('0',end($datearray));


    }
    //檢測時間 回傳結束時間
    public function checksdate($data,$sdateVids){
            $style = T01tb::select('style','time1' ,'time2' ,'time3' ,'time4' ,'time5' ,'time6' ,'time7' ,'holiday','trainday','kind','period','trainhour')->where('class',$data['class'])->get()->toArray();
            if(count($style)==0) return back()->with('result', '0')->with('message','無此班別');

            $style = $style[0];
            $day = $style['kind'] == '2'? $style['period']:$style['trainday']; //課程天數
            //每週上課日規則
            if($style['style']=='2') {
                $weekrule = array(1,3,5);
            }elseif($style['style']=='3'){
                $weekrule = array(2,4);
            }elseif($style['style']=='4'){
                $weekrule = array();
                for($i=0;$i<7;$i++){
                    if($i==0 && $style['time7']=='Y'){
                        $weekrule[] = $i;
                    }elseif(isset($style['time'.$i]) && $style['time'.$i]=='Y'){
                        $weekrule[] = $i;
                    }
                }
            }
            if(count($weekrule)==0) return array('result'=>'0','msg'=>'開班日期與上課方式不符，未設定上課方式，天數'.$day);
            //判斷日期 錯誤回傳錯誤訊息
            $week = date('w', strtotime($sdateVids) );
            $ch = array('1' =>'一.','2' =>'二.','3' =>'三.','4' =>'四.','5' =>'五.','6' =>'六.','0' =>'日.');
            if(!in_array($week, $weekrule))  {
                $errormsg = '開班日期與上課方式不符，上課方式每週';
                for($i=0;$i<count($weekrule);$i++){
                    $errormsg .= $ch[$weekrule[$i]];
                }
                $errormsg = substr($errormsg,0,-1);
                $errormsg .='上課';
                if($style['holiday']=="Y"){
                    $errormsg .= '含國定假日' ;
                    // back()->with('result', '0')->with('message',$errormsg);
                }
                // else{
                //     return back()->with('result', '0')->with('message',$errormsg);
                // }
                return array('result'=>'0','msg'=>$errormsg);
            }else{
                $mission=0;
                $checklist = m12tb::select('date')->where('date','>',(date('Y')-1913).'1231')->get()->toArray(); //抓年度開始的資料
                $holidaylist = array();
                foreach ($checklist as $key => $value) {
                    $holidaylist[$value['date']]='1';
                }
                $datearray = array();
                while ($mission<$day){
                    $week = date('w', strtotime($sdateVids) );
                    $head = intval(substr($sdateVids,0,-4))-1911;
                    $bady = substr($sdateVids,-4);
                    if($style['holiday']=='N' && isset($holidaylist[$head.$bady]) ){ //如果是國定假日 跳過

                    }elseif(in_array($week, $weekrule) ) {
                        $datearray[] = $head.$bady;
                        $mission = $mission +1;
                    }
                    if($mission==$day) {
                        break;
                    }
                    $sdateVids = date('Ymd',strtotime($sdateVids .' +1 Day'));
                }

            }
            return array('result'=>'1','msg'=>$datearray);
        }
        //檢查預約
        private function checksite($data){
            if($data['site_branch']=='2'){
                if($data['time']=='D'){
                    $check = t97tb::select('date','class','time')->whereBetween('date',array($data['sdate'],$data['edate']))->whereIn('time',array('A','B','D'))->where('site',$data['site'])->get()->toarray();
                }elseif($data['time']=='E'){
                    $check = t97tb::select('date','class','time')->whereBetween('date',array($data['sdate'],$data['edate']))->whereIn('time',array('A','B','C','E'))->where('site',$data['site'])->get()->toarray();
                }else{
                    $check = t97tb::select('date','class','time')->whereBetween('date',array($data['sdate'],$data['edate']))->where('time',$data['time'])->where('site',$data['site'])->get()->toarray();
                }
            }elseif($data['site_branch']=='1'){
                if($data['time']=='D'){
                    $check = t22tb::select('date','class','time')->whereBetween('date',array($data['sdate'],$data['edate']))->whereIn('time',array('A','B','D'))->where('site',$data['site'])->get()->toarray();
                }elseif($data['time']=='E'){
                    $check = t22tb::select('date','class','time')->whereBetween('date',array($data['sdate'],$data['edate']))->whereIn('time',array('A','B','C','E'))->where('site',$data['site'])->get()->toarray();
                }else{
                    $check = t22tb::select('date','class','time')->whereBetween('date',array($data['sdate'],$data['edate']))->where('time',$data['time'])->where('site',$data['site'])->get()->toarray();
                }
            }else{
                return array('result'=>'1','msg'=>'');
            }
            if(count($check)>0) {
                $errormsg = '該時段的場地已被預約:';
                foreach ($check as $key => $value) {
                    $errormsg .= $value['date'].'時段'.$value['time'].'，預約班別'.$value['class'].'。';
                }
                return array('result'=>'0','msg'=>$errormsg);
            }else{
                return array('result'=>'1','msg'=>'');
            }
        }
        //場地預約
        private function reservation($data,$datearray,$sdateVids,$timebase){
            $user_data = \Auth::user();
            $freezeday = s02tb::select('weekly','monthly')->first(); /*凍結日(週、月)(2、05) */
            $affirmVids = date('Ymd',strtotime($sdateVids .' -'.(date('w',strtotime($sdateVids))+8-$freezeday['weekly']).'Day')); //確認凍結日
            $requestVids = date('Ym',strtotime($sdateVids .' -1 month')).$freezeday['monthly'];  //需求凍結日
            // '檢查開課日期是否已過確認凍結日
            if(strtotime($affirmVids) < strtotime('now') ) return array('result'=>'0','msg'=>'上課日期已過確認凍結日期!');

            if (is_null($data['site'])) return array('result'=>'1','msg'=>'');
            //寫入
            for($i=0;$i<count($datearray);$i++){
                $indata = array();
                $indata['class'] = $data['class'];
                $indata['term'] = $data['term'];
                $indata['site'] = $data['site'];
                $indata['date'] = $datearray[$i];
                $indata['site_branch'] = $data['site_branch'];
                T36tb::insert($indata);
                $sql = DB::getQueryLog();
                $nowdata = T36tb::where('class',$indata['class'])->where('term',$indata['term'])->where('date',$indata['date'])->where('site',$indata['site'])->get()->toArray();
                createModifyLog('I','t36tb','',$nowdata,end($sql));
                unset($indata['site_branch']);
                if($data['site']!='' && $data['site_branch']!='2'){
                    $indata['cnt'] = $data['quota'];
                    // $indata['reserve'] = $data['sponsor'];
                    $indata['reserve'] = $user_data->userid; // **預約人userid
                    $indata['liaison'] = $data['sponsor'];
                    $indata['seattype'] = 'C';
                    $indata['fee'] = $data['fee']; //場地費用***
                    $indata['request'] = (date('Y',strtotime($requestVids))-1911).date('md',strtotime($requestVids)); //需求凍結日
                    $indata['affirm'] = (date('Y',strtotime($affirmVids))-1911).date('md',strtotime($affirmVids)); //確認凍結日
                    if($data['site']=='404' || $data['site']=='405'){
                        $indata['status'] = 'Y';
                    }else{
                        $indata['status'] = 'N';
                    }
                    for($j=0;$j<count($timebase);$j++){
                        unset($indata['type']);
                        $indata['stime'] = $timebase[$j]['stime'];
                        $indata['etime'] = $timebase[$j]['etime'];
                        $indata['time'] = $timebase[$j]['time'];
                        T22tb::insert($indata);
                        $sql = DB::getQueryLog();
                        $nowdata = T22tb::where('class',$indata['class'])->where('term',$indata['term'])->where('date',$indata['date'])->where('site',$indata['site'])->where('time',$indata['time'])->get()->toArray();
                        createModifyLog('I','t22tb','',$nowdata,end($sql));
                        for($k=1;$k<3;$k++){
                            unset($indata['affirm'],$indata['status']);
                            $indata['type'] = $k;
                            T37tb::insert($indata);
                            $sql = DB::getQueryLog();
                            $nowdata = T37tb::where('class',$indata['class'])->where('term',$indata['term'])->where('date',$indata['date'])->where('site',$indata['site'])->where('time',$indata['time'])->get()->toArray();
                            createModifyLog('I','t37tb','',$nowdata,end($sql));
                        }
                    }
                }else{
                    for($j=0;$j<count($timebase);$j++){
                        unset($indata['type']);
                        $indata['stime'] = $timebase[$j]['stime'];
                        $indata['etime'] = $timebase[$j]['etime'];
                        $indata['time'] = $timebase[$j]['time'];
                        T97tb::insert($indata);
                        $sql = DB::getQueryLog();
                        $nowdata = T97tb::where('class',$indata['class'])->where('term',$indata['term'])->where('date',$indata['date'])->where('site',$indata['site'])->where('time',$indata['time'])->get()->toArray();
                        createModifyLog('I','t97tb','',$nowdata,end($sql));
                        for($k=1;$k<3;$k++){
                            $indata['type'] = $k;
                            T37tb::insert($indata);
                            $sql = DB::getQueryLog();
                            $nowdata = T37tb::where('class',$indata['class'])->where('term',$indata['term'])->where('date',$indata['date'])->where('site',$indata['site'])->where('time',$indata['time'])->get()->toArray();
                            createModifyLog('I','t37tb','',$nowdata,end($sql));
                        }
                    }
                }
            }
            return array('result'=>'1','msg'=>'');
        }
        private function __result( $inputCode,$inputMsg ){
            echo json_encode(array('status' => $inputCode , 'msg' => $inputMsg));
            exit;
        }
}