<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpWord\PhpWord;


class StudentGradeRptController extends Controller
{
    public function __construct()
    {

    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //取得班別
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);           
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
        $termArr=$temp;
        $result = '';
        return view('admin/student_grade_rpt/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    public function export(Request $request){

        $class = $request->input('classes');
        $term = $request->input('term');
        $type = $request->input('type');
        $sex="";

        if($type=="1"){ //明細
            $sql="Select 
            t04tb.sdate, t04tb.edate , '' AS A, t13tb.no, m02tb.cname, m02tb.sex, t13tb.education, CONCAT(LTRIM(RTRIM(t13tb.dept)),LTRIM(RTRIM(t13tb.position))) as deptpos,
            t04tb.actratio, t04tb.schratio,
            t15tb.actitem1, t15tb.actitem2, t15tb.actitem3, t15tb.actitem4, t15tb.actitem5, t15tb.actitem6 ,
            t15tb.act1, t15tb.act2, t15tb.act3, t15tb.act4, t15tb.act5, t15tb.act6 ,
            t04tb.schitem1, t04tb.schitem2, t04tb.schitem3, t04tb.schitem4, t04tb.schitem5,
            t04tb.schrate1, t04tb.schrate2, t04tb.schrate3, t04tb.schrate4, t04tb.schrate5,
            t15tb.sch1, t15tb.sch2, t15tb.sch3, t15tb.sch4, t15tb.sch5,
            t15tb.schcom1, t15tb.schcom2, t15tb.schcom3, t15tb.schcom4, t15tb.schcom5,
            '' AS B, '' AS C,
            CONCAT('實得分數：(' , cast(t04tb.basescr as char) , '分+小計)X' , cast(t04tb.actratio as char) ) AS score, 
            t15tb.actsum ,t15tb.schsum ,t15tb.totscr, t15tb.rank , ''
            From t04tb inner join t13tb on t04tb.class = t13tb.class and t04tb.term = t13tb.term
            inner join t15tb on t13tb.class = t15tb.class and t13tb.term =t15tb.term and t13tb.idno = t15tb.idno
            inner join m02tb on t13tb.idno = m02tb.idno
            Where t15tb.class ='".$class."' and t15tb.term ='".$term."' and t13tb.status='1' 
            Order by t13tb.no ";
            $temp = DB::select($sql);
            $data = json_decode(json_encode($temp), true);

            // 查無資料處裡
            if(sizeof($data) == 0) {
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclass();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);           
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = '查無資料，請重新查詢';
                return view('admin/student_grade_rpt/list',compact('classArr','termArr' ,'result'));
            }

            $sql="select name from t01tb where class='".$class."'";
            $temp = DB::select($sql);
            $classname = json_decode(json_encode($temp), true);

            $sql="Select code, name From s01tb Where UPPER(type)='C'";
            $temp = DB::select($sql);
            $codename = json_decode(json_encode($temp), true);

            // 範本檔案名稱
            $fileName = 'J8';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel

            $objPHPExcel = IOFactory::load($filePath);
            $objActSheet = $objPHPExcel->getActiveSheet();
            $objActSheet->setCellValue('C3',substr($class,0,3)."年".$classname[0]["name"]."第".strval((int)$term)."期");
            $objActSheet->setCellValue('G1',"自　".strval((int)substr($data[0]["sdate"],0,3)).".".substr($data[0]["sdate"],3,2).".".substr($data[0]["sdate"],5,2));
            $objActSheet->setCellValue('G2',"至　".strval((int)substr($data[0]["edate"],0,3)).".".substr($data[0]["edate"],3,2).".".substr($data[0]["edate"],5,2));
            $objActSheet->setCellValue('G5',"平 時 考 核 成 績(".$data[0]["actratio"]."%)");
            $objActSheet->setCellValue('G5',"學 科 考 核 成 績(".$data[0]["schratio"]."%)");

            for($i=2;$objPHPExcel->getSheetCount()<sizeof($data);$i++){
                $clonedWorksheet= clone $objPHPExcel->getSheet(0);
                $clonedWorksheet->setTitle(strval($i));
                $objPHPExcel->addSheet($clonedWorksheet);
            }

            for($i=0;$i<sizeof($data);$i++){
                $objSheet = $objPHPExcel->getSheet($i);
                $objSheet->setCellValue('A5',strval((int)$data[$i]["no"]));
                $objSheet->setCellValue('C5',$data[$i]["cname"]);

                if($data[$i]["cname"]="F")
                    $sex="女";
                elseif($data[$i]["cname"]="M")
                    $sex="男";

                $objSheet->setCellValue('D5',$sex);  
                $objSheet->setCellValue('E5',$data[$i]["education"]); 
                $objSheet->setCellValue('G5',$data[$i]["deptpos"]);     
                $objSheet->setCellValue('A8',$this->getactitemname($data[$i]["actitem1"],$codename));
                $objSheet->setCellValue('A9',$this->getactitemname($data[$i]["actitem2"],$codename));
                $objSheet->setCellValue('A10',$this->getactitemname($data[$i]["actitem3"],$codename));
                $objSheet->setCellValue('A11',$this->getactitemname($data[$i]["actitem4"],$codename));
                $objSheet->setCellValue('A12',$this->getactitemname($data[$i]["actitem5"],$codename));
                $objSheet->setCellValue('A13',$this->getactitemname($data[$i]["actitem6"],$codename));
                $objSheet->setCellValue('D8',$data[$i]["act1"]);
                $objSheet->setCellValue('D9',$data[$i]["act2"]);
                $objSheet->setCellValue('D10',$data[$i]["act3"]);
                $objSheet->setCellValue('D11',$data[$i]["act4"]);
                $objSheet->setCellValue('D12',$data[$i]["act5"]);
                $objSheet->setCellValue('D13',$data[$i]["act6"]);

                $objSheet->setCellValue('E8',$data[$i]["schitem1"]);
                $objSheet->setCellValue('E9',$data[$i]["schitem2"]);
                $objSheet->setCellValue('E10',$data[$i]["schitem3"]);
                $objSheet->setCellValue('E11',$data[$i]["schitem4"]);
                $objSheet->setCellValue('E12',$data[$i]["schitem5"]);
                $objSheet->setCellValue('G8',$data[$i]["schrate1"]);
                $objSheet->setCellValue('G9',$data[$i]["schrate2"]);
                $objSheet->setCellValue('G10',$data[$i]["schrate3"]);
                $objSheet->setCellValue('G11',$data[$i]["schrate4"]);
                $objSheet->setCellValue('G12',$data[$i]["schrate5"]);
                $objSheet->setCellValue('H8',$data[$i]["sch1"]);
                $objSheet->setCellValue('H9',$data[$i]["sch2"]);
                $objSheet->setCellValue('H10',$data[$i]["sch3"]);
                $objSheet->setCellValue('H11',$data[$i]["sch4"]);
                $objSheet->setCellValue('H12',$data[$i]["sch5"]);
                $objSheet->setCellValue('I8',$data[$i]["schcom1"]);
                $objSheet->setCellValue('I9',$data[$i]["schcom2"]);
                $objSheet->setCellValue('I10',$data[$i]["schcom3"]);
                $objSheet->setCellValue('I11',$data[$i]["schcom4"]);
                $objSheet->setCellValue('I12',$data[$i]["schcom5"]);

                $objSheet->setCellValue('A20',$data[$i]["score"]);
                $objSheet->setCellValue('D20',$data[$i]["actsum"]);
                $objSheet->setCellValue('H20',$data[$i]["schsum"]);
                $objSheet->setCellValue('B21',$data[$i]["totscr"]);
                $objSheet->setCellValue('E21',$data[$i]["rank"]);
                $objSheet->setCellValue('H21',strval(sizeof($data)));
            
            }
            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"學員成績-成績明細");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 
            
        }else{  //清冊

            $sql="Select
            t13tb.no , m02tb.dept, m02tb.cname ,t04tb.schratio, t04tb.actratio, t04tb.schitem1, t04tb.schrate1, t04tb.schitem2, t04tb.schrate2, t04tb.schitem3, t04tb.schrate3, t04tb.schitem4, t04tb.schrate4, t04tb.schitem5, t04tb.schrate5 , t15tb.schcom1, t15tb.schcom2, t15tb.schcom3, t15tb.schcom4, t15tb.schcom5, t15tb.schsum, t15tb.actsum, t15tb.totscr, t15tb.rank
            From
            t04tb, t13tb, t15tb, m02tb
            Where 
            t15tb.class ='".$class."' and t15tb.term ='".$term."' and t13tb.status='1' 
            and (t04tb.class = t13tb.class and t04tb.term = t13tb.term) and (t13tb.idno = m02tb.idno)  and (t13tb.class = t15tb.class and t13tb.term = t15tb.term and t13tb.idno = t15tb.idno)
            Order by t13tb.no";

            $temp = DB::select($sql);
            $data = json_decode(json_encode($temp), true);

            $sql="select name from t01tb where class='".$class."'";
            $temp = DB::select($sql);
            $classname = json_decode(json_encode($temp), true);


            // 查無資料
            if(sizeof($data) == 0) {
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclass();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);           
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = '查無資料，請重新查詢';
                return view('admin/student_grade_rpt/list',compact('classArr','termArr' ,'result'));
            }
            

            $cnt=0;
            for($i=1;$i<6;$i++){
                if($data[0]["schitem".$i]!="")
                    $cnt++;
            }

            if($cnt==0)
                $cnt=1;

            // 讀檔案
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J8'.$cnt).'.docx');
            $templateProcessor->setValue('title',$classname[0]["name"]);
            $templateProcessor->setValue('sr',$data[0]["schratio"]);
            $templateProcessor->setValue('ar',$data[0]["actratio"]);    
            $templateProcessor->cloneRow('s', sizeof($data));

            for($i=0;$i<sizeof($data);$i++){
               
                $templateProcessor->setValue('s#'.strval($i+1),$data[$i]["no"]);
                $templateProcessor->setValue('dept#'.strval($i+1),$data[$i]["dept"]);
                $templateProcessor->setValue('name#'.strval($i+1),$data[$i]["cname"]);
                $templateProcessor->setValue('A1#'.strval($i+1),$data[$i]["schcom1"]);
                $templateProcessor->setValue('B#'.strval($i+1),$data[$i]["schsum"]);
                $templateProcessor->setValue('C#'.strval($i+1),$data[$i]["actsum"]);
                $templateProcessor->setValue('D#'.strval($i+1),$data[$i]["totscr"]);
                $templateProcessor->setValue('E#'.strval($i+1),$data[$i]["rank"]);

                switch ($cnt) {
                    case 2:
                        $templateProcessor->setValue('A2#'.strval($i+1),$data[$i]["schcom2"]);
                        break;
                    case 3:
                        $templateProcessor->setValue('A2#'.strval($i+1),$data[$i]["schcom2"]);
                        $templateProcessor->setValue('A3#'.strval($i+1),$data[$i]["schcom3"]);
                        break;
                    case 4:
                        $templateProcessor->setValue('A2#'.strval($i+1),$data[$i]["schcom2"]);
                        $templateProcessor->setValue('A3#'.strval($i+1),$data[$i]["schcom3"]);
                        $templateProcessor->setValue('A4#'.strval($i+1),$data[$i]["schcom4"]);
                        break;
                    case 5:
                        $templateProcessor->setValue('A2#'.strval($i+1),$data[$i]["schcom2"]);
                        $templateProcessor->setValue('A3#'.strval($i+1),$data[$i]["schcom3"]);
                        $templateProcessor->setValue('A4#'.strval($i+1),$data[$i]["schcom4"]);
                        $templateProcessor->setValue('A5#'.strval($i+1),$data[$i]["schcom5"]);
                        break;
                }

            }
            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"學員成績-成績清冊");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 
        }
    }

    public function getactitemname($code,$codename){
        foreach($codename as $cn){
            if($cn["code"]==$code)
                return $cn["name"];
        }
        return "";
    }
}
