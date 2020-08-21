<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Services\User_groupService;

class ClassroomUsageList extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('classroom_usage_list', $user_group_auth)){
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
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=$RptBasic->getTerms($temp[0][$arraykeys[0]]);
        $termArr=$temp;

        $temp=DB::select("SELECT site, RTRIM(name) AS name FROM m14tb WHERE (type='1' AND seat<>'3') OR site='C14' OR site='303' AND type IS NOT NULL  ORDER BY site ");
        $siteArr=$temp;


        $result="";
        return view('admin/classroom_usage_list/list',compact('classArr','termArr','siteArr' ,'result'));

        // $result="";
        // return view('admin/classroom_usage_list/list',compact('result'));

    }
    public function getTerms(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $termArr=$RptBasic->getTerms($request->input('classes'));
        return $termArr;
    }
    public function getSites(Request $request)
    {
        /*
        $temp=DB::select("SELECT branch FROM t01tb WHERE class='".$request->input('classes')."'");
        $branch=json_decode(json_encode($temp), true);
        $tb="m14tb";

        if($branch[0]["branch"]=="2"){
            $tb="m25tb";
        }
        $temp=DB::select("SELECT A.site,B.name FROM (SELECT site FROM t04tb WHERE class='".$request->input('classes')."' AND term='".$request->input('term')."') AS A LEFT JOIN ".$tb." AS B ON A.site=B.site");
        */
        $temp=DB::select("SELECT site, RTRIM(name) AS name FROM m14tb WHERE (type='1' AND seat<>'3') OR site='C14' OR site='303' AND type IS NOT NULL  ORDER BY site ");
        $siteArr=$temp;
        return $siteArr;
    }


    public function export(Request $request)
    {
        $sdate="0-0-0";
        $edate="0-0-0";
        $A2="教室使用一覽表";
        $A3="";
        if($request->input('sdatetw')!="" && $request->input('edatetw')!="" ){
            $sdatetw=explode("-",$request->input('sdatetw'));
            $edatetw=explode("-",$request->input('edatetw'));
            $sdate=$sdatetw[0].$sdatetw[1]. $sdatetw[2];
            $edate=$edatetw[0].$edatetw[1]. $edatetw[2];
            $A3="使用期間：".$sdatetw[0].'年'.$sdatetw[1].'月'. $sdatetw[2].'日~'.$edatetw[0].'年'.$edatetw[1]. '月'.$edatetw[2].'日';
        }

        $area=$request->input('area');
        $condition="";

        if($area=="1"){
            $A2="台北院區".$A2;
            $condition=" and F.branch='1' ";
        }elseif($area=="2"){
            $A2="南投院區".$A2;
            $condition=" and F.branch='2' ";
        }

        $sql="SELECT
        F.name as 教室名稱,
        E.name as 班名,
        IFNULL(A.name,'') AS 科目 ,
        (CASE IFNULL(B.cname,'') WHEN '' THEN '' ELSE RTRIM(B.cname) END) AS 講座 ,
        concat(A.stime,'~',A.etime) as 節次,
        CONCAT(cast(cast(substr(A.date,1,3) as int)as char),'.',substr(A.date,4,2),'.',substr(A.date,6,2)) as 日期,
        D.username as 承辦人
        FROM t06tb A
        LEFT JOIN t08tb B ON A.course = B.course AND A.class = B.class AND A.term = B.term
        LEFT JOIN t04tb C ON A.class = C.class AND A.term = C.term
        LEFT JOIN m09tb D ON C.sponsor=D.userid
        LEFT JOIN t01tb E ON E.class = A.class
        LEFT JOIN m14tb F ON C.site = F.site and E.branch=F.branch
        WHERE A.date BETWEEN ".$sdate." AND ".$edate." ";
        $sql.= $condition;

        $data=json_decode(json_encode(DB::select($sql)), true);

        if($data==[]){
            $result="查無資料，請重新設定搜尋條件。";
            return view('admin/classroom_usage_list/list',compact('result'));
        }

        $datakey=array_keys((array)$data[0]);

        // 檔案名稱
        $fileName = 'F11';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);

        $objSheet = $objPHPExcel->getsheet(0);

        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $objSheet->setCellValue('A2', $A2);
        $objSheet->setCellValue('A3', $A3);

        $tmpname=$data[0][$datakey[0]];
        $cnt=0;
        $srow=5;
        $erow=4;


        for($i=0;$i<sizeof($data);$i++){

            if($tmpname==$data[$i][$datakey[0]]){
                $cnt++;
                $erow++;
            }else{
                $objSheet->mergeCells('A'. $srow.':A'.$erow);
                $objSheet->mergeCells('B'. $srow.':B'.$erow);

                for($k=$srow;$k<=$erow;$k++)
                    $objSheet->setCellValue('B'.$k,$cnt);

                $tmpname=$data[$i][$datakey[0]];
                $cnt=1;
                $srow=$i+5;
                $erow=$i+5;
            }

            for($j=0;$j<sizeof($datakey);$j++){

                $pos=$j+1;

                if($j>0)
                    $pos=$j+2;

                $objSheet->setCellValue($this->getNameFromNumber($pos).($i+5),$data[$i][$datakey[$j]]);
            }
        }

        //apply borders
        $objSheet->getStyle('A4:H'.(sizeof($data)+4))->applyFromArray($styleArray);


        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"教室使用一覽表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
       

    }


}
