<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\BookPlaceService;
use App\Services\TrainingProcessService;
use App\Services\User_groupService;
use App\Models\T22tb;
use App\Models\T01tb;
use App\Models\T38tb;
use DB ;
use Auth;

class BookPlaceController extends Controller
{
    public function __construct(BookPlaceService $bookplaceservice,TrainingProcessService $trainingProcessService, User_groupService $user_groupService)
    {
        $this->bps=$bookplaceservice;
        $this->trainingProcessService = $trainingProcessService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('bookplace', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    public function index(Request $request)
    {
        
        $data = $request->all();
        // var_dump($data);exit();
        $sdate = $request->input("sdate3");
        $edate = $request->input("edate3");
        
        $site = $request->input("site");
        $branch=$request->input("branch");
        $classroom = '';
        $classList = T22tb::select('t01tb.class','t01tb.name')->leftjoin('t01tb','t22tb.class','t01tb.class')->where('t01tb.class','<>','')->where('t22tb.class','<>','')->groupby('class')->orderby('t01tb.class','DESC')->get();
        if($request->input()){
            $condition = array();
            $condition['sdate']=$sdate;
            $condition['edate']=$edate;
            $condition['branch'] =  $request->input("branch");
            if($site!=''){
                $site = substr($site,0,-1);
                $condition['site'] = $site;
                $classroom=explode(",",$condition["site"]);
                $sitelist = $this->getPlace($branch);

            }else{
                $sitelist = '';
            }

            $result=$this->bps->getlist($condition);
            return view("admin/bookplace/list",compact("sdate","edate","data","result","branch","classList","classroom","sitelist"));
        }else{
            $result = array();
            return view("admin/bookplace/list",compact("sdate","edate","data","result","branch","classList","classroom"));
        }
      
        
        // dd($classList);
        
    }

    public function form(Request $request)
    {

        //$arr=unserialize($arr);
        // 取得課程列表
        $arr=$request->all();

        $classList = $this->bps->getClassList();
        $meetList = $this->bps->getMeetList();
        $mode=$arr['action'];
        if($mode=='post'){ //新增
            $reserve_info='';
        }else{ //編輯
            $reserve_info=$this->bps->getInfo($arr);

        }
        
        $show_class_name = '本預約非課程與會議';
        if(isset($reserve_info->class)){
            /*判斷顯示是否為班級或會議 */
            if(''!=$reserve_info->class){
                //查詢班級
                $class_data  = T01tb::where('class', $reserve_info->class)->first();
                if($class_data){
                $show_class_name = $class_data->name;
                }

                $class_data  = T38tb::where('meet', $reserve_info->class)->first();
                if($class_data){
                $show_class_name = $class_data->name;
                }
                //查詢會議
            }
        }else{
            $show_class_name = '';
        }
      

        return view("admin/bookplace/form",compact("classList","meetList","mode","arr","reserve_info","show_class_name"));
    }

    public function store(Request $request)
    {
 
        $post=$request->input();
        if($post['type']=='1'){
            unset($post['meet']);
        }else{
            $post['class'] = substr($post['meet'], 0,-2);
            $post['term'] = substr($post['meet'],-2);
            unset($post['meet']);
        }
        $result=$this->bps->_insert($post);

        if(!empty($result)){
            $result=implode(",",$result);
            return back()->with('result',0)->with('message', '新增失敗!'.$result);
        }else{
            if($post["branch"]=='1'){
                $DB='t22tb';
            }else{
                $DB='t97tb';
            }
            $post["date"]=str_replace("/","",$post["date"]);
            $info=DB::table($DB)->where("site",$post["site"])->where("date",$post["date"])->where("time",$post["time"])->where("class",$post["class"])->where("term",$post["term"])->first();  
            if(empty($info)){
                $url = '/admin/bookplace/index';
            }else{
                $url = '/admin/bookplace?site='.$post["site"].'&date='.$post["date"].'&class='.$post['class'].'&term='.$post['term'].'&time='.$post['time'].'&stime='.$info->stime.'&etime='.$info->etime.'&branch='.$post['branch'].'&action=edit';
            }
            return redirect($url)->with('result', '1')->with('message', '新增成功!');
        }
    }

    public function update(Request $request)
    {
        $update=$request->input();
        // dd($update);
        // $fuhua_arr=['101','103','201','202','203','204','205','C01','C02','C14'];
        // ***移除福華場地限制***
        // if(in_array($update['site'],$fuhua_arr)){
        //     $result='無法對福華場地做修改';
        //      return back()->with('result',0)->with('message', '修改失敗!'.$result);
        // }
        $result=$this->bps->_update($update);
        if(!empty($result)){
            $result=implode(",",$result);
            return back()->with('result',0)->with('message', '修改失敗!'.$result);
        }else{
            $update["date"]=str_replace("/","",$update["date"]);
            $url = '/admin/bookplace?site='.$update["site"].'&date='.$update["date"].'&class='.$update['class'].'&term='.$update['term'].'&time='.$update['time'].'&branch='.$update['branch'].'&action=edit';
            return redirect($url)->with('result',1)->with('message', '修改成功!');
        }
    }
    public function destroy(Request $request)
    {
        $data = $request->all();
        if($data['branch']=='1'){
            $DB='t22tb';
        }else{
            $DB='t97tb';
        }
        DB::table($DB)->where("site",$data["site"])->where("date",$data["date"])->where("time",$data["time"])->delete(); 
        return redirect('admin/bookplace/index')->with('result', '1')->with('message', '刪除成功!');
    }
    public function addClassroom(Request $request,$type)
    {
        $not_select=$this->getPlace($type);
        //dd($not_select);
        $select=[];
        $savefield='classroom2';
        return view("admin/bookplace/set_column",compact('not_select','select','savefield'));
    }
    //取得場地列表
    public function getPlace($type)
    {
        $data = $this->bps->getPlace($type);
        return $data;
    }

    public function setWeek()
    {
        $savefield="setweek";
        return view("admin/bookplace/set_week",compact('savefield'));
    }
    //批次場地修改
    public function batchVerify()
    {   
        // $data=$this->bps->getplacedata(array('branch'=>'2','sdate'=>'109/05/08','edate'=>'109/05/10'));
        // var_dump($data);exit;
        $data=[];
        // $courseNot=[["date"=>"1090229","site"=>"501","time"=>"A","class"=>"108805","term"=>"01"],
        //             ["date"=>"1090301","site"=>"456","time"=>"B","class"=>"108800","term"=>"03"]];
        // $course=[["date"=>"1090229","site"=>"123","time"=>"B","class"=>"","term"=>""],
        //         ["date"=>"1090322","site"=>"789","time"=>"C","class"=>"123456","term"=>"10"]];

        $classList = $this->bps->getClassList();
        $meetList = $this->bps->getMeetList();
        $placeT = $this->bps->getPlace(1);
        $placeN = $this->bps->getPlace(2);
        return view("admin/bookplace/batchverify",compact("data","placeT","placeN","classList","meetList"));
    }
    //type是4、5且timetype=2則顯示場地資料錯誤
    public function batchUpdate(Request $request)
    {	
    	$data = $request->all();
    	$final_info=$request->input("final_info");
        $final_info=explode(",",$final_info);
        $condition=[];
        for($i=0;$i<count($final_info);$i++){
            $condition=explode("&",$final_info[$i]);
            $this->bps->batchUpdate($condition,$data['branch']);
        }
        return back()->with('result', '1')->with('message', '更新成功!'); 
        //dd($request->input("final_info"));
    }

    public function getT22tbAjax(Request $request)
    {
        $condition['sdate']=$request->input('final_sdate');
        $condition['edate']=$request->input('final_edate');
        $condition['branch']=$request->input('branch');
        $sdate=str_replace("/","",$condition["sdate"]);
        $edate=str_replace("/","",$condition["edate"]);
        if($sdate > $edate || $sdate=='' || $edate=='') {
            echo json_encode( array() ) ;
            exit;
        }
        $condition['class']=$request->input("class_id");
        $condition['term'] =$request->input("term");
        $classroom = $request->input("room");
        $data=$this->bps->getplacedata($condition,$classroom);
        // if($data){
        //     echo json_encode( array( 'status' => '0' , 'msg' => $data ) );
        // }else{
        //     echo json_encode( array( 'status' => '1' , 'msg' => 'error' ) );
        // }
        return $data;
        //return [$condition,$classroom];
    }

    public function setTime(Request $request)
    {
        $savefield='';
        $condition=$request->all();
        return view("admin/bookplace/set_time",compact('savefield','condition'));
    }

    public function seatUpdate(Request $request)
    {
        $condition=$request->all();
        $data=DB::table('t22tb')->where('class',$condition['class'])->where('term',$condition['term'])->get();
        $count=count($data);
        if($count==0){
            return redirect("admin/bookplace/index")->with("result",'0')->with("message","無符合之資料可被修改!");
        }
        $this->bps->seatUpdate($condition);
        return back()->with("result","1")->with('message','修改完成!');
    }


}