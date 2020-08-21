<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\DemandSurveyService;
use App\Services\User_groupService;
use App\Models\T02tb;
use App\Models\T68tb;
use App\Models\T69tb;
use App\Models\T70tb;
use App\Models\T71tb;
use App\Models\T79tb;
use App\Models\T80tb;
use App\Models\T01tb;
use App\Models\S02tb;
use App\Helpers\ModifyLog;
use DB;


class DemandSurveyController extends Controller
{
    public $progid = 'demand_survey';
    /**
     * DemandSurveyController constructor.
     * @param DemandSurveyService $demandSurveyService
     */
    public function __construct(DemandSurveyService $demandSurveyService, User_groupService $user_groupService)
    {
        $this->demandSurveyService = $demandSurveyService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('demand_survey', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('demand_survey');
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 取得關鍵字
        $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // 第幾次
        $queryData['times'] = $request->get('times');
        // 辦班院區
        $queryData['branch'] = $request->get('branch');
        // 需求調查名稱
        $queryData['purpose'] = $request->get('purpose');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 班期
        $queryData['class'] = $request->get('class');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        if(empty($request->all())) {
            $queryData['choices'] = $this->_get_year_list();
            return view('admin/demand_survey/list', compact('queryData'));
        }
        // 取得列表資料
        $data = $this->demandSurveyService->getDemandSurveyList($queryData);
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/demand_survey/list', compact('data', 'queryData'));
    }
    /**
     * 公告文字維護
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bulletin_board()
    {
        $data = S02tb::select('announcement')->first();
        return view('admin/demand_survey/bulletin_board',compact('data'));
    }
    /**
     * 修改公告文字維護
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bulletin_edit(Request $request)
    {
        // $data = $request->get('content');
        $data = (isset($_POST['content']) )?$_POST['content']:'';
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        $olddata = S02tb::select('announcement')->first();
        S02tb::where(DB::RAW('1'),1)->update(array('announcement'=>$data));
        $nowdata = S02tb::select('announcement')->first();
        $sql = DB::getQueryLog();
        createModifyLog('U','s02tb',$olddata,$nowdata,end($sql));
        return redirect('/admin/demand_survey/bulletin_board')->with('result', '1')->with('message', '修改成功!');
    }
    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $yerly = date('Y')-1911;
        $queryData['classlist'][0] = T01tb::select('class', 'branch', 'name', 'yerly', 'times')
                                            ->where(function ($query){
                                                $query->where('times', '');
                                                $query->orWhere('times', null);
                                            })
                                            ->where('type','<>','13')
                                            ->where('class','like',$yerly.'%')->get()->toArray();
        //$queryData['classlist'][2] = T01tb::select('class', 'branch', 'name', 'yerly', 'times')->where('times','')->where('type','<>','13')->where('branch','2')->get()->toArray();
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/demand_survey/form', compact('queryData'));
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

        // 取得梯次
        $times = T68tb::where('yerly', $data['yerly'])->where('branch', $data['branch'])->max('times');
        $times = empty($times)? 1 :($times + 1);
        $data['times'] = str_pad($times,2,'0',STR_PAD_LEFT);
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try {
            //新增
            $result = T68tb::create($data);
            $sql = DB::getQueryLog();
            $nowdata = T68tb::where('yerly', $data['yerly'])->where('branch', $data['branch'])->where('times', $data['times'])->get()->toarray();
            createModifyLog('I','t68tb','',$nowdata,end($sql));
            // 取得該梯次的班級
            $class = $request->input('class');
            $classAry = explode(',',substr($class,0,-1));
            if ($classAry) { // 更新班級的年度跟梯次
                $nowdata = T01tb::select('class','yerly','times')->whereIn('class', $classAry)->get()->toarray();
                T01tb::whereIn('class', $classAry)->update(array('yerly' => $data['yerly'], 'times' => $data['times']));
                $sql = DB::getQueryLog();
                $olddata = T01tb::select('class','yerly','times')->whereIn('class', $classAry)->get()->toarray();
                createModifyLog('U','t01tb',$olddata,$nowdata,end($sql));
            }
            DB::commit();
            return redirect('/admin/demand_survey/'.$result->id)->with('result', '1')->with('message', '新增成功!');
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('result', '0')->with('message', '儲存失敗，請稍後再試!');
        }
        
    }
    //取得該年度第幾次調查+班別清單(ajax)
    public function gettimes(Request $request){
        $yerly = $request->get('yerly');
        $branch = $request->get('branch');
        if(!$yerly) $this->__result('1','') ;

        $times = empty(T68tb::where('yerly', $yerly)->where('branch', $branch)->max('times'))? 1 : T68tb::where('yerly', $yerly)->where('branch', $branch)->max('times') + 1;
        $times = str_pad($times ,2,'0',STR_PAD_LEFT);
        $list = T01tb::select('class', 'branch', 'name', 'yerly', 'times')
                                            ->where(function ($query){
                                                $query->where('times', '');
                                                $query->orWhere('times', null);
                                            })
                                            ->where('type','<>','13')
                                            ->where('branch',$branch)
                                            ->where('class','like',$yerly.'%')->get()->toArray();

        $result = array('times'=>$times,'list'=>$list);

        $this->__result('0',$result) ;
    }

    /**
     * 顯示頁
     *
     * @param $id
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * 查看填表資料
     *
     * @param
     */
    public function form2(Request $request)
    {
        //$yerly = $request->input('yerly');
        //$times = $request->input('times');
        // 取得關鍵字
        $queryData['id'] = $request->input('id');
        if(!$queryData['id']) return redirect('/admin/demand_survey/')->with('result', '0')->with('message', '查無此資料!');

        $iddata = $this->demandSurveyService->getDemandSurveyList($queryData);
        $yerly = $iddata[0]['yerly'];
        $times = $iddata[0]['times'];
        $iddata[0]['id'] = $queryData['id'] ;
        if(is_null($yerly) || is_null($times)) return redirect('/admin/demand_survey/')->with('result', '0')->with('message', '查無此資料!!');

        $TrainingInstitutionList = $this->demandSurveyService->getTrainingInstitutionList($yerly, $times);
        $AdministrationList = $this->demandSurveyService->getAdministrationList($yerly, $times);
        //**全部數據很卡 待修正
        foreach ($AdministrationList as $key => $value) {
            $downorgan = $this->demandSurveyService->getAdministrationList($yerly, $times,2,$value->enrollorg);
            $AdministrationList[$key]->downorgan = $downorgan;
        }

        $data = array("Administration" => $AdministrationList,"TrainingInstitution" => $TrainingInstitutionList ); //行政機關 訓練機構
        /* 右下列表 */
        $list = $this->demandSurveyService->getDemandSurveyClasses($yerly, $times);


        // $sql = "SELECT DISTINCT class,RTRIM(name) AS name FROM t01tb
        //         WHERE type<>'13' AND `yerly` = ".$yerly." AND 
        //         ORDER BY class DESC";

        $classlist = T01tb::select('class','name')->where('type','<>','13')->where('yerly',$yerly)->where('times',$times)->ORDERBY('class','DESC')->get();

        $sql = "SELECT enrollorg,enrollname
                from m17tb where grade='1'
                and organ IN (
                select organ from m13tb where kind='Y' )
                order by enrollorg";

        $organizationlist = DB::select($sql);
        $iddata['choices'] = $this->_get_year_list();

        return view('admin/demand_survey/form2', compact('iddata','data','list','classlist','organizationlist'));
    }
    //ajax取得填報資料數據
    public function getDemandSurveyData(Request $request){
        $year = $request->get('year');
        $times = $request->get('times');
        $times = str_pad($times,2,'0',STR_PAD_LEFT);
        $enrollorg = $request->get('enrollorg');
        $data = array('0'=>array('class'=>'','name'=>'加總','applycnt'=>'0','checkcnt'=>'0') );
        $list = $this->demandSurveyService->getDemandSurveyData($year,$times,$enrollorg);
        $i=1;

        foreach ($list as $key => $value) {
            $data[$i]['class'] = $value->class;
            $data[$i]['name'] = $value->name;
            $data[$i]['applycnt'] = is_null($value->applycnt)? '0':$value->applycnt;
            $data[$i]['checkcnt'] = is_null($value->checkcnt)? '0':$value->checkcnt;
            $data[$i]['rank'] = $value->rank;
            $data['0']['applycnt'] = $data['0']['applycnt'] + $data[$i]['applycnt'];
            $data['0']['checkcnt'] = $data['0']['checkcnt'] + $data[$i]['checkcnt'];
            $i++;
        }
        // 左下機關資訊
        // $title = T68tb::select('sdate','edate')->where('yerly',$year)->where('times',$times)->first();

        // $title['type'] = T79tb::select('type')->where('yerly',$year)->where('times',$times)->where('organ',$enrollorg)->first();

        $title = $this->demandSurveyService->getTitleMsg($year, $times,$enrollorg);

        if($list){
            $this->__result('0',array('list'=>$data,'title'=>$title['0'] )) ;
        }else{
            $this->__result('1','查無資料') ;
        }

    }

    /**
     * 編輯頁
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = T68tb::find($id);

        if ( ! $data) {

            return view('admin/errors/error');
        }
        //班別
        $queryData['classlist'][0] =  T01tb::select('class', 'branch', 'name', 'yerly', 'times')
                                            ->where(function ($query){
                                                $query->where('times', '');
                                                $query->orWhere('times', null);
                                            })
                                            ->where('type','<>','13')
                                            ->where('class','like',$data['yerly'].'%')->get()->toArray();
        $queryData['classlist'][1] = T01tb::select('class','branch',  'name', 'yerly', 'times')->where('yerly',$data['yerly'])->where('times',$data['times'])->get()->toArray();

        // 複選轉陣列
        $data->classes_id = array_diff(explode(',', $data->classes_id), array(null, 'null', '', ' '));
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/demand_survey/form', compact('data','queryData'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $classlist = '';
        // 取得POST資料
        $data = $request->all();
        unset($data['class']);
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try {
            //更新基本資料
            $olddata = T68tb::where('id', $id)->get()->toarray();
            $times = $olddata[0]['times'];
            if($olddata[0]['purpose'] != $data['purpose'] || $olddata[0]['sdate'] != $data['sdate'] || $olddata[0]['edate'] != $data['edate'] ){
                T68tb::find($id)->update($data);
                $sql = DB::getQueryLog();
                $nowdata = T68tb::where('id', $id)->get()->toarray();
                createModifyLog('U','t68tb',$olddata,$nowdata,end($sql));
            }
            // 先將該班別調查梯次全部清空
            $olddata = T01tb::select('class','yerly','times')->where('yerly', $olddata[0]['yerly'])->where('times', $olddata[0]['times'])->get()->toarray();
            if(!empty($olddata)){
                T01tb::where('yerly', $olddata[0]['yerly'])->where('times', $olddata[0]['times'])->update(array('times' => ''));
                $sql = DB::getQueryLog();
                foreach ($olddata as $value) {
                    $classlist .= $value['class'].',';
                }
                $classlist = explode(',',substr($classlist,0,-1));
                $nowdata = T01tb::select('class','yerly','times')->whereIn('class',$classlist)->get()->toarray();
                createModifyLog('U','t01tb',$olddata,$nowdata,end($sql));
            }
            // 取得該梯次的班級
            $class = $request->input('class');
            $classAry = explode(',',substr($class,0,-1));
            if ($classAry) {
                // 更新班級的年度跟梯次
                $olddata = T01tb::select('class','yerly','times')->whereIn('class',$classAry)->get()->toarray();
                T01tb::whereIn('class',$classAry)->update(array('yerly' => $olddata[0]['yerly'], 'times' => $times));
                $sql = DB::getQueryLog();
                $nowdata = T01tb::select('class','yerly','times')->whereIn('class',$classAry)->get()->toarray();
                createModifyLog('U','t01tb',$olddata,$nowdata,end($sql));
            }
            DB::commit();
            return back()->with('result', '1')->with('message', '儲存成功!!');
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('result', '0')->with('message', '儲存失敗，請稍後再試!');
        }

        
    }

    /**
     * 刪除處理
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id){
        if (empty($id) ) return back()->with('result', '0')->with('message', '刪除失敗!!');

        $data = T68tb::select('yerly','times')->where('id',$id)->first();
        $t01 = T01tb::select('class')->where('yerly',$data['yerly'])->where('times',$data['times'])->get()->toArray();
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try{
            $olddata = T68tb::where('id', $id)->get()->toarray();
            T68tb::where('id',$id)->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t68tb',$olddata,'',end($sql));
            dd($sql);
            $olddata = T70tb::where('yerly',$data['yerly'])->where('times',$data['times'])->get()->toarray();
            if(!empty($olddata)){
                T70tb::where('yerly',$data['yerly'])->where('times',$data['times'])->delete();
                $sql = DB::getQueryLog();
                createModifyLog('D','t70tb',$olddata,'',end($sql));
            }
            $olddata = T71tb::where('yerly',$data['yerly'])->where('times',$data['times'])->get()->toarray();
            if(!empty($olddata)){
                T71tb::where('yerly',$data['yerly'])->where('times',$data['times'])->delete();
                $sql = DB::getQueryLog();
                createModifyLog('D','t71tb',$olddata,'',end($sql));
            }
            $olddata = T79tb::where('yerly',$data['yerly'])->where('times',$data['times'])->get()->toarray();
            if(!empty($olddata)){
                T79tb::where('yerly',$data['yerly'])->where('times',$data['times'])->delete();
                $sql = DB::getQueryLog();
                createModifyLog('D','t79tb',$olddata,'',end($sql));
            }
            $olddata = T01tb::select('class')->where('yerly',$data['yerly'])->where('times',$data['times'])->get()->toarray();
            if(!empty($olddata)){
                T01tb::select('class')->where('yerly',$data['yerly'])->where('times',$data['times'])->update(array('times'=>''));
                $sql = DB::getQueryLog();
                $nowdata = T01tb::select('class')->where('yerly',$data['yerly'])->where('times',$data['times'])->get()->toarray();
                createModifyLog('U','t01tb',$olddata,$nowdata,end($sql));
            }
            DB::commit();
            return redirect('/admin/demand_survey')->with('result', '1')->with('message', '刪除成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '刪除失敗');
        }
    }
    /**
    * 匯入填報資料
    *
    */
    public function importdata(Request $request)
    {
        $yerly = $request->input('importyerly');
        $times = $request->input('importtimes');
        //先確認是否有重覆資料
        $sql = "SELECT COUNT(*) FROM t02tb A
                INNER JOIN t01tb B ON A.class=B.class
                WHERE B.yerly= '".$yerly."'
                AND B.times='".$times."'";
        $data = DB::select($sql);
        if($data!=0){
            DB::beginTransaction();
            DB::connection()->enableQueryLog(); //啟動SQL_LOG
            try {
                //刪除t02tb 參訓單位報名檔
                $olddata = t02tb::select('t02tb.class','t02tb.organ','t02tb.demand','t02tb.quota','t01tb.yerly','t01tb.times')->join('t01tb','t02tb.class','t01tb.class')->where('t01tb.yerly',$yerly)->where('t01tb.times',$times)->get()->toarray();
                $sql = "DELETE A
                        FROM t02tb A
                        INNER JOIN t01tb B ON A.class=B.class
                        INNER JOIN t70tb C ON B.yerly=C.yerly AND B.times=C.times
                        WHERE B.yerly= '".$yerly."'
                        AND B.times='".$times."'";
                $data = DB::select($sql);
                $sqllog = DB::getQueryLog();
                createModifyLog('D','t02tb',$olddata,'',end($sqllog));
                //資料匯入(行政機關)
                $sql = "INSERT t02tb(class, organ, demand )
                        SELECT A.class,C.organ,A.checkcnt AS demand
                        FROM t69tb A INNER JOIN t01tb B ON A.class=B.class
                        INNER JOIN m17tb C ON A.organ=C.enrollorg
                        INNER JOIN t70tb D ON B.yerly=D.yerly AND B.times=D.times AND A.organ=D.organ
                        WHERE C.grade='1'
                        AND B.yerly= '".$yerly."'
                        AND B.times='".$times."'
                        AND D.status='1'
                        GROUP BY A.class, C.organ,  A.checkcnt";
                $data = DB::select($sql);
                $sqllog = DB::getQueryLog();
                $sql = "SELECT A.class,C.organ,A.checkcnt AS demand
                        FROM t69tb A INNER JOIN t01tb B ON A.class=B.class
                        INNER JOIN m17tb C ON A.organ=C.enrollorg
                        INNER JOIN t70tb D ON B.yerly=D.yerly AND B.times=D.times AND A.organ=D.organ
                        WHERE C.grade='1'
                        AND B.yerly= '".$yerly."'
                        AND B.times='".$times."'
                        AND D.status='1'
                        GROUP BY A.class, C.organ,  A.checkcnt";
                $nowdata = DB::select($sql);
                createModifyLog('I','t02tb','',$nowdata,end($sqllog));
                //資料匯入(訓練機構)
                $sql = "INSERT t02tb(class, organ, demand )
                        SELECT A.class,C.agency,A.checkcnt AS demand
                        FROM t69tb A INNER JOIN t01tb B ON A.class=B.class
                        INNER JOIN m07tb C ON A.organ=C.agency
                        INNER JOIN t70tb D ON B.yerly=D.yerly AND B.times=D.times AND A.organ=D.organ
                        WHERE B.yerly= '".$yerly."' AND B.times='".$times."' AND D.status='1'
                        GROUP BY A.class, C.agency,  A.checkcnt";
                $data = DB::select($sql);
                $sqllog = DB::getQueryLog();
                $sql = "SELECT A.class,C.organ,A.checkcnt AS demand
                        FROM t69tb A INNER JOIN t01tb B ON A.class=B.class
                        INNER JOIN m17tb C ON A.organ=C.enrollorg
                        INNER JOIN t70tb D ON B.yerly=D.yerly AND B.times=D.times AND A.organ=D.organ
                        WHERE C.grade='1'
                        AND B.yerly= '".$yerly."'
                        AND B.times='".$times."'
                        AND D.status='1'
                        GROUP BY A.class, C.organ,  A.checkcnt";
                $nowdata = DB::select($sql);
                createModifyLog('I','t69tb','',$nowdata,end($sqllog));
                DB::commit();
                return back()->with('result', '1')->with('message', '匯入成功!');
            } catch (Exception $e) {
                DB::rollback();
                return back()->with('result', '0')->with('message', '匯入失敗');
            }
        }
        return back()->with('result', '0')->with('message', '匯入失敗!!');

    }
    /**
    * 列印機關建議班別
    *
    */
    public function printdata(Request $request)
    {
        $yerly = $request->input('printyerly');
        $times = $request->input('printtimes');
        $list = $this->demandSurveyService->getprintdata($yerly,$times);
        $word_filename = $yerly.iconv("UTF-8","big-5","年度第").$times.iconv("UTF-8","big-5","次調查之機關建議班別報表");
        //$list = $this->demandSurveyService->getprintdata($yerly,$times,$organ=NULL,$force='N');//來源?
        header("Content-type: text/html; charset=utf8"); //頁面編碼
        header("Content-Type:application/msword");   //將此html頁面轉成word
        header("Content-Disposition:attachment;filename=".$word_filename.".doc");   //設定word檔名
        header("Pragma:no-cache");
        header("Expires:0");
?>
    <html>
        <meta http-equiv=Content-Type content="text/html; charset=utf8">
        <body>
            <p><?=$word_filename?></p>
            <table width=600 cellpadding="6" cellspacing="1">
<?php
        $word_head = iconv("UTF-8","big-5",'<tr bgcolor="#336699"><td>機關代碼</td>
                <td>機關名稱</td>
                <td>建議內容</td>
                <td>最後修改機關代碼</td>
                <td>最後修改機關名稱</td></tr>');
        echo $word_head;
        foreach ($list as $key => $value) {
            echo '<tr><td>'.$value->organ.'</td>
            <td>'.iconv("UTF-8","big-5",$value->name).'</td>
            <td>'.iconv("UTF-8","big-5",$value->content).'</td>
            <td>'.iconv("UTF-8","big-5",$value->modorgan).'</td>
            <td>'.iconv("UTF-8","big-5",$value->modname).'</td></tr>';
        }
?>
            </table>
        </body>
    </html>
<?php
    }
    /**
    * 取消凍結
    *
    */
    public function canceldata(Request $request){
        $organ = $request->input('organizationCode');
        $yerly = $request->input('organyerly');
        $times = $request->input('organtimes');
        $check = T70tb::where('organ',$organ)->where('yerly',$yerly)->where('times',$times)->first();
        if(empty($check)) return back()->with('result', '0')->with('message', '該機關未被凍結!');

        $type = DB::select("SELECT type from t79tb where yerly= '".$yerly."' and times='".$times."' and organ='".$organ."'");  //1:人數 2:名冊
        $olddata = T70tb::where('organ',$organ)->where('yerly',$yerly)->where('times',$times)->get()->toarray();
        if($type[0]->type==1){
        	DB::connection()->enableQueryLog(); //啟動SQL_LOG
            $check = T70tb::where('organ',$organ)->where('yerly',$yerly)->where('times',$times)->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t70tb',$olddata,'',end($sql));
            return back()->with('result', '1')->with('message', '已解除凍結!');
        }elseif($type[0]->type==2){

            $classArray = $this->demandSurveyService->ufn_csditrain_orgchk($organ,$yerly,$times); //[0]->obj
            $class = '';
            foreach ($classArray as $key => $value) {
                $class .= "'".$value->class."',";
            }
            $class = substr($class,0,-1);//刪掉尾數逗號
            $organdata = $this->demandSurveyService->ufn_sub_organ($organ);
            $updata = $this->demandSurveyService->updataprogress($class,$organdata['enrollorg'],$organdata['grade']);
            DB::connection()->enableQueryLog(); //啟動SQL_LOG
            $check = T70tb::where('organ',$organ)->where('yerly',$yerly)->where('times',$times)->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t70tb',$olddata,'',end($sql));

            return back()->with('result', '1')->with('message', '已解除凍結!');
        }else{
            return back()->with('result', '0')->with('message', '查無此資料!');
        }

    }
    /**
    * 需求名冊
    *
    */
    public function demanddata(Request $request)
    {
        $class = $request->input('class');
        $organizationCode2 = $request->input('organizationCode2');
        // $class = '106082';
        // $organizationCode2 = 'A00000000A';

        $sql = "SELECT B.enrollname, concat(rtrim(E.lname),rtrim(E.fname)) AS NAME,
                CASE WHEN E.sex='M' then '男' ELSE '女' END AS sex, rtrim(E.position) AS POSITION , E.rank
                from (select enrollorg,grade1 from view_enrollorg where grade1='".$organizationCode2."') A
                inner join (select enrollorg,organ,enrollname from m17tb) B
                on A.enrollorg = B.enrollorg
                inner join (select organ from m13tb where kind ='Y') C
                on B.organ = C.organ
                inner join (select enrollorg,enrollid from t80tb where progress ='0' and prove ='Y' and class ='".$class."') d
                on A.enrollorg = d.enrollorg
                inner join (select userid,lname,fname,sex,position,rank from m22tb) E
                on d.enrollid = E.userid" ;
        $organizationlist = DB::select($sql);

        return $organizationlist;
    }

    /**
    * 重設填報方式
    *
    */
    public function resetdata(Request $request)
    {
        $id = $request->input('from2id');
        $organ = $request->input('resetdataorgan');
        $yerly = $request->input('resetdatayerly');
        $times = $request->input('resetdatatimes');
        $sql = "SELECT count(*)as count from m17tb where grade='1' and enrollorg ='".$organ."' ";
        $grade = DB::select($sql);
        if($grade[0]->count == 0) return redirect('/admin/demand_survey/form2?id='.$id)->with('result', '0')->with('message', '所選取的機關需為主管機關!');

        $type = DB::select("SELECT type from t79tb where yerly= '".$yerly."' and times='".$times."' and organ='".$organ."'");  //1:人數 2:名冊
        $organdata = $this->demandSurveyService->ufn_sub_organ($organ);
        if(empty($type)){
            return redirect('/admin/demand_survey/form2?id='.$id)->with('result', '0')->with('message', '查無資料');
        }elseif($type[0]->type == 1){
            $check = DB::select("SELECT * FROM t01tb A
                    INNER JOIN t69tb B ON B.class=A.class
                    WHERE B.organ = '".$organ."'  AND A.yerly='".$yerly."' AND A.times='".$times."' ");
        }elseif($type[0]->type == 2){
            $check = DB::select("SELECT * FROM t01tb A
                    INNER JOIN (SELECT class,enrollorg FROM t80tb group by class,enrollorg ) B  ON B.class=A.class
                    WHERE B.enrollorg = '".$organ."' AND A.yerly='".$yerly."' AND A.times='".$times."' ");
        }else{
            return redirect('/admin/demand_survey/form2?id='.$id)->with('result', '0')->with('message', '查無資料!');
        }
        if(empty($ckeck)){
            return redirect('/admin/demand_survey/form2?id='.$id)->with('result', '0')->with('message', '查無資料，無需重設資料!');

        }
        $classArray = $this->demandSurveyService->ufn_csditrain_orgchk($organ,$yerly,$times); //[0]->obj
            $class = array();
            foreach ($classArray as $key => $value) {
                $class[] = $value->class;
            }
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try {    
	        // 【t79tb 機關填報設定資料檔】
	        $olddata = t79tb::where('yerly',$yerly)->where('times',$times)->where('organ',$organ)->get()->toarray();
	        t79tb::where('yerly',$yerly)->where('times',$times)->where('organ',$organ)->delete();
	        $sql = DB::getQueryLog();
			createModifyLog('D','t79tb',$olddata,'',end($sql));
	        // 【t69tb 機關需求填報資料檔】
	        $olddata = t69tb::whereIn('class',$class)->where('organ',$organ)->get()->toarray();
	        t69tb::whereIn('class',$class)->where('organ',$organ)->delete();
	        $sql = DB::getQueryLog();
			createModifyLog('D','t69tb',$olddata,'',end($sql));
	        // 【t70tb 填報凍結機關資料檔】
	        $olddata = t70tb::where('yerly',$yerly)->where('times',$times)->where('organ',$organ)->get()->toarray();
	        t70tb::where('yerly',$yerly)->where('times',$times)->where('organ',$organ)->delete();
	        $sql = DB::getQueryLog();
			createModifyLog('D','t70tb',$olddata,'',end($sql));
	        // 【t80tb 需求填報名冊資料檔】
	        $olddata = t80tb::whereIn('class',$class)->where('enrollorg',$organ)->get()->toarray();
	        t80tb::whereIn('class',$class)->where('enrollorg',$organ)->delete();
	        $sql = DB::getQueryLog();
			createModifyLog('D','t80tb',$olddata,'',end($sql));
	   		DB::commit();
            return redirect('/admin/demand_survey/form2?id='.$id)->with('result', '1')->with('message', '填報方式已重設');
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('result', '0')->with('message', '重設失敗');
        }
    }
    /**
    * 班別需求整併
    *
    */
    public function marge(Request $request){
        $data = $request->all();
        if($data['class1']==$data['class2'])  return back()->with('result', '0')->with('message', '相同班期無法合併!');

        $check = T01tb::where('yerly',$data['yerly'])->where('times',$data['times'])->whereIn('class',array($data['class2'],$data['class1']))->count();
        if($check < '2')  return back()->with('result', '0')->with('message', '班期錯誤，查無此班期!');
        
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try{
            // T69機關數據合併
            $type = ($data['class1'] > $data['class2'])? 'DESC':'ASC';
            $t69base = t69tb::where('class',$data['class2'])->get()->toarray();
            $organ = array();
            foreach ($t69base as $key => $va) {
                $organ[$va['organ']]['applycnt'] = $va['applycnt'];
                $organ[$va['organ']]['checkcnt'] = $va['checkcnt'];
            }   
            // 基底資料  
            $t69base1 = t69tb::where('class',$data['class1'])->get()->toarray();
            foreach ($t69base1 as $k => $v) {
                if( isset($organ[$v['organ']]) ){ //存在 賦予資料相加
                    $applycnt = $organ[$v['organ']]['applycnt']+$v['applycnt'];
                    $checkcnt = $organ[$v['organ']]['checkcnt']+$v['checkcnt'];
                    $olddata = t69tb::where('class',$data['class2'])->where('organ',$v['organ'])->get()->toArray();
                    t69tb::where('class',$data['class2'])->where('organ',$v['organ'])->update(array('applycnt'=>$applycnt,'checkcnt'=>$checkcnt));
                    $sql = DB::getQueryLog();
                    $nowdata = t69tb::where('class',$data['class2'])->where('organ',$v['organ'])->get()->toArray();
					createModifyLog('U','t69tb',$olddata,$nowdata,end($sql));
                }else{  //不存在 新增

                    t69tb::create(array('class'=>$data['class2'],'organ'=>$v['organ'],'applycnt'=>$v['applycnt'],'checkcnt'=>$v['checkcnt']));
                    $sql = DB::getQueryLog();
                    $nowdata = t69tb::where('class',$data['class2'])->where('organ',$v['organ'])->where('applycnt',$v['applycnt'])->where('checkcnt',$v['checkcnt'])->get()->toArray();
                    createModifyLog('I','t69tb','',$nowdata,end($sql));
                }
                // 舊資料 歸零
                $olddata = t69tb::where('class',$v['class'])->where('organ',$v['organ'])->get()->toArray();
                t69tb::where('class',$v['class'])->where('organ',$v['organ'])->update(array('applycnt'=>'0','checkcnt'=>'0'));
                $sql = DB::getQueryLog();
                $nowdata = t69tb::where('class',$v['class'])->where('organ',$v['organ'])->get()->toArray();
				createModifyLog('U','t69tb',$olddata,$nowdata,end($sql));
            }
            // 刪除T80重複資料
            $olddata = T80tb::where('class',$data['class2'])->get()->toArray();
            $sql ="SELECT A.class,A.enrollorg,A.enrollid  FROM( SELECT class,enrollorg,enrollid FROM `t80tb` WHERE `class` = '".$data['class1']."'	)as A  INNER JOIN ( SELECT class,enrollorg,enrollid FROM `t80tb` WHERE `class` = '".$data['class2']."'	) as B
					on A.enrollid = B.enrollid AND A.enrollorg = B.enrollorg";
			$deletelist = DB::select($sql);
			foreach ($deletelist as $key => $value) {
				T80tb::where('class',$data['class2'])->where('enrollorg',$value->enrollorg)->where('enrollid',$value->enrollid)->delete(); 
			}
			// T80名冊轉移
			T80tb::where('class',$data['class1'])->update(array('class'=>$data['class2'])); 
            $sql = DB::getQueryLog();
            $nowdata = T80tb::where('class',$data['class2'])->get()->toArray();
			createModifyLog('U','t80tb',$olddata,$nowdata,end($sql));  
            DB::commit();
            return back()->with('result', '1')->with('message', '合併成功');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '合併失敗');
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
    private function __result( $code,$msg ){
    echo json_encode(array('status' => $code , 'msg' => $msg));
    exit;
  }
}
