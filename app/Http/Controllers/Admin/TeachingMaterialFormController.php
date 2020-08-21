<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Models\T49tb;
use DB;

class TeachingMaterialFormController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('teaching_material_form', $user_group_auth)){
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
    public function index(Request $request)
    {
        $temp=DB::select("SELECT class, RTRIM(NAME) as name FROM t01tb WHERE EXISTS (SELECT * FROM t49tb WHERE class = t01tb.class) ORDER BY class DESC ");
        $classArr=$temp;
        $result="";
        return view('admin/teaching_material_form/list',compact('result','classArr'));
    }

    /**
     * 取得班級所有教材
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getMaterial(Request $request)
    {
        $class=$request->input("classes");
        $data = T49tb::select('serno','class','term','branch','material','copy','applicant')->where('class', $class)->get()->toarray();
        $termArr=$data;
        return $termArr;
    }

    
    public function export(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $type=$request->input("type");
        $class=$request->input("classes");
        $serial=trim($request->input("serial"));
        $material=trim($request->input("material"));
        $condition="";

        $serial=str_replace("'","",$serial);
        $serial=str_replace('"',"",$serial);
        $material=str_replace("'","",$material);
        $material=str_replace('"',"",$material);

        $sql="SELECT
                A.serno AS '編號',
                A.senddate,     /*'交印日期*/
                A.material AS '教材名稱',
                B.username AS '申請單位',
                IFNULL((SELECT name FROM t01tb WHERE class=A.class ),'') AS '班別',
                RTRIM(term) AS '期別',
                A.serno,       /*'交印主檔編號*/
                A.material,    /*'印製教材名稱*/
                A.class,       /*'班號*/
                A.term,        /*'期別*/
                A.typing,    /*'是否膠裝*/
                A.bind,       /*'是否裝訂*/
                A.punch,       /*'是否打孔*/
                A.copy,       /*'份數*/
                A.page,       /*'張數*/
                A.print,       /*'印刷方式*/
                A.sendtime,    /*'交印時間*/
                A.duedate,    /*'預定交貨日期*/
                A.duetime,    /*'預定交貨時間*/
                A.applicant, /*'申請者*/
                A.kind,       /*'開支科目*/
                A.client,    /*'委辦單位*/
                A.total,       /*'總金額*/
                A.paiddate,    /*'支付月份*/
                A.extranote,   /*'其他附註*/
                B.username,    /*'姓名*/
                B.ext,         /*'分機*/
                B.section,      /*'部門 */
                A.branch  /*院區 */
            FROM t49tb A LEFT JOIN m09tb B ON A.applicant=B.userid
            WHERE A.serno <> 0 ";

        if($type=="1"){         //依編號輸出
            $sql.=" AND A.serno = '".$serial."' ";
        }elseif($type=="2"){    //依班別輸出與所選的教材輸出
            $serno_array=$request->input("serno");
            $serno_list = implode(",",$serno_array);
            
            $sql.=" AND A.class = '".$class."' ";
            $sql.=" AND A.serno in (".$serno_list.") ";
        }elseif($type=="3"){    //依教材名稱輸出
            $sql.=" AND A.material LIKE '%".$material."%' ";
        }
        $sql.=" ORDER BY A.senddate DESC, A.serno DESC ";
        // die($sql);
        $temp = json_decode(json_encode(DB::select($sql)), true);

        $templeate_file = 'P2' ;//預設台北
        if($temp==[]){
            $result = "此條件查無資料，請重新查詢。";
            $temp=DB::select("SELECT class, RTRIM(NAME) as name FROM t01tb WHERE EXISTS (SELECT * FROM t49tb WHERE class = t01tb.class) ORDER BY class DESC ");
            $classArr=$temp;
            return view('admin/teaching_material_form/list',compact('result','classArr'));
        }
        $maindata = $temp;
        if($temp[0]['branch']==2){
            $templeate_file = 'P2S' ;
        }
        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', $templeate_file ).'.docx');
        ini_set('pcre.backtrack_limit', 999999999);
        $templateProcessor->cloneBlock('page',sizeof($maindata), true, true);

        for($i=0;$i<sizeof($maindata);$i++){
            // ☐ <=checked box　☒ <= unchecked box ☑ <=wrong size in word
            //set variables
            $p1=""; //單面印刷
            $p2=""; //雙面印刷
            $sdate="";
            $stime="";
            $ddate="";
            $dtime="";
            $applicant=""; //申請人
            $tp=""; //膠裝
            $bd=""; //裝訂
            $ph=""; //打孔
            $accnamea="";
            $accnameb="";
            $unit = '無';//申請單位

            if($maindata[$i]["print"]=="1"){
                $p1="☐";
                $p2="☒";
            }else{
                $p1="☒";
                $p2="☐";
            }

            if($maindata[$i]["senddate"]==""){
                $sdate="   /   /   ";
            }else{
                $sdate=substr($maindata[$i]["senddate"],0,3)."/".substr($maindata[$i]["senddate"],3,2)."/".substr($maindata[$i]["senddate"],5,2);
            }

            if($maindata[$i]["duedate"]==""){
                $ddate="   /   /   ";
            }else{
                $ddate=substr($maindata[$i]["duedate"],0,3)."/".substr($maindata[$i]["duedate"],3,2)."/".substr($maindata[$i]["duedate"],5,2);
            }

            if($maindata[$i]["sendtime"]=="1"){
                $stime="上";
            }elseif($maindata[$i]["sendtime"]=="2"){
                $stime="下";
            }else{
                $stime="";
            }

            if($maindata[$i]["duetime"]=="1"){
                $dtime="上";
            }elseif($maindata[$i]["duetime"]=="2"){
                $dtime="下";
            }else{
                $dtime="";
            }
            if( $templeate_file == 'P2S' ){
                $unit =$maindata[$i]["section"];
                $applicant.=$maindata[$i]["username"]."<w:br />"."83691399 分機：".$maindata[$i]["ext"];

            }else{
                $applicant.=$maindata[$i]["section"].$maindata[$i]["username"]."<w:br />"."83691399 分機：".$maindata[$i]["ext"];

            }

           
            // ☐ <=checked box　☒ <= unchecked box
            if($maindata[$i]["typing"]=="Y"){
                $tp="☒";
            }else{
                $tp="☐";
            }

            if($maindata[$i]["bind"]=="Y"){
                $bd="☒";
            }else{
                $bd="☐";
            }

            if($maindata[$i]["punch"]=="Y"){
                $ph="☒";
            }else{
                $ph="☐";
            }

            $sql=" SELECT * from s06tb where yerly='".substr($maindata[$i]["duedate"],0,3)."'";
            $temp = json_decode(json_encode(DB::select($sql)), true);
            $accnameArr=$temp;

            $cnt=1;
            $pad=0;
            foreach($accnameArr as $v){
                $tmpstr="";
                if($v["acccode"]==$maindata[$i]["kind"])
                    $tmpstr.="☒";
                else
                    $tmpstr.="☐";

                $tmpstr.=$v["accname"];

                $tmpstr.="<w:br />";

                if($cnt%2==0){
                    $accnameb.=$tmpstr;
                }else{
                    $accnamea.=$tmpstr;
                }

                $cnt++;
            }

            $sql=" SELECT type   AS '項目類型',
            item   AS '項目',
            unit   AS '單位',
            price  AS '合約單價',
            remark AS '備註',
            serno
            FROM t50tb
            WHERE serno = '".$maindata[$i]["serno"]."'
            ORDER BY sequence";

            $temp = json_decode(json_encode(DB::select($sql)), true);
            $detail=$temp;
           
            if($detail==[])
            {
                $templateProcessor->setValue('item#'.strval($i+1),"");
                $templateProcessor->setValue('type#'.strval($i+1),"");
                $templateProcessor->setValue('u#'.strval($i+1),"");
                $templateProcessor->setValue('pe#'.strval($i+1),"");
                
            }
            //create cntArr to fill items
            $cntArr=[];
            $cntArrkey=-1;
            foreach($detail as $v)
            {
                if($v["項目類型"]=="A"){
                    array_push( $cntArr,0);
                    $cntArrkey++;
                }elseif(isset($cntArr[$cntArrkey])){
                    $cntArr[$cntArrkey]++;
                }
            }

            //fill values
            $templateProcessor->setValue('y#'.strval($i+1),substr($maindata[$i]["class"],0,3));
            $templateProcessor->setValue('s#'.strval($i+1),$maindata[$i]["serno"]);
            $templateProcessor->setValue('name#'.strval($i+1),$maindata[$i]["material"]);
            $templateProcessor->setValue('classterm#'.strval($i+1),$maindata[$i]["class"]." ".$maindata[$i]["班別"]." 第".$maindata[$i]["term"]."期");
            $templateProcessor->setValue('copy#'.strval($i+1),$maindata[$i]["copy"]);
            $templateProcessor->setValue('ps#'.strval($i+1),$maindata[$i]["page"]);
            $templateProcessor->setValue('p1#'.strval($i+1),$p1);
            $templateProcessor->setValue('p2#'.strval($i+1),$p2);
            $templateProcessor->setValue('sdate#'.strval($i+1),$sdate);
            $templateProcessor->setValue('stime#'.strval($i+1),$stime);
            $templateProcessor->setValue('ddate#'.strval($i+1),$ddate);
            $templateProcessor->setValue('dtime#'.strval($i+1),$dtime);
            $templateProcessor->setValue('applicant#'.strval($i+1),$applicant);
            $templateProcessor->setValue('tp#'.strval($i+1),$tp);
            $templateProcessor->setValue('bd#'.strval($i+1),$bd);
            $templateProcessor->setValue('ph#'.strval($i+1),$ph);
            $templateProcessor->setValue('accnamea#'.strval($i+1),$accnamea);
            $templateProcessor->setValue('accnameb#'.strval($i+1),$accnameb);
            $templateProcessor->setValue('unit#'.strval($i+1),$unit);
          
            //fill items according to cntArr
            if($cntArr!=[]){
                $templateProcessor->cloneBlock('b#'.strval($i+1),sizeof($cntArr), true, true);
                $typecnt=0;
                $itemcnt=1;
                $bcnt=1;
                $ccnt=1;
   
                for($j=0;$j<sizeof($detail);$j++){

                    if($detail[$j]["項目類型"]=="A"){
                        $templateProcessor->setValue('type#'.strval($i+1).'#'.strval($typecnt+1),$detail[$j]["項目"]);

                        if($cntArr[$typecnt]>0){
                            $templateProcessor->cloneRow('item#'.strval($i+1).'#'.strval($typecnt+1),$cntArr[$typecnt]);
                            $itemcnt=1;
                            $bcnt=1;
                            $ccnt=1;
                        }else{
                            $templateProcessor->setValue('type#'.strval($i+1).'#'.strval($typecnt+1),"");
                            $templateProcessor->setValue('u#'.strval($i+1).'#'.strval($typecnt+1),"");
                            $templateProcessor->setValue('pe#'.strval($i+1).'#'.strval($typecnt+1),"");
                            $templateProcessor->setValue('remark#'.strval($i+1).'#'.strval($typecnt+1),"");
                        }

                        $typecnt++;

                    }elseif($detail[$j]["項目類型"]=="B"){

                        $templateProcessor->setValue('item#'.strval($i+1).'#'.strval($typecnt).'#'.strval($itemcnt),"(".$RptBasic->digitoword2($bcnt).")".$detail[$j]["項目"]);
                        $templateProcessor->setValue('u#'.strval($i+1).'#'.strval($typecnt).'#'.strval($itemcnt),$detail[$j]["單位"]);
                        $templateProcessor->setValue('pe#'.strval($i+1).'#'.strval($typecnt).'#'.strval($itemcnt),$detail[$j]["合約單價"]);
                        $templateProcessor->setValue('remark#'.strval($i+1).'#'.strval($typecnt).'#'.strval($itemcnt),$detail[$j]["備註"]);
                        $itemcnt++;
                        $bcnt++;
                        $ccnt=1;

                    }elseif($detail[$j]["項目類型"]=="C"){

                        $templateProcessor->setValue('item#'.strval($i+1).'#'.strval($typecnt).'#'.strval($itemcnt),"  ".strval($ccnt).".".$detail[$j]["項目"]);
                        $templateProcessor->setValue('u#'.strval($i+1).'#'.strval($typecnt).'#'.strval($itemcnt),$detail[$j]["單位"]);
                        $templateProcessor->setValue('pe#'.strval($i+1).'#'.strval($typecnt).'#'.strval($itemcnt),$detail[$j]["合約單價"]);
                        $templateProcessor->setValue('remark#'.strval($i+1).'#'.strval($typecnt).'#'.strval($itemcnt),$detail[$j]["備註"]);
                        $itemcnt++;
                        $ccnt++;
                    }
                }
            }else{
                $templateProcessor->setValue('item#'.strval($i+1),"");
                $templateProcessor->setValue('type#'.strval($i+1),"");
                $templateProcessor->setValue('u#'.strval($i+1),"");
                $templateProcessor->setValue('pe#'.strval($i+1),"");
                $templateProcessor->setValue('remark#'.strval($i+1),""); 
            }
        }


        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"教材交印單");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
