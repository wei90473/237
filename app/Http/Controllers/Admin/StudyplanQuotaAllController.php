<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use App\Models\T01tb;
use DB;
use Excel;
use PHPExcel; 
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Drawing;
use App\Services\User_groupService;

class StudyplanQuotaAllController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('studyplan_quota_all', $user_group_auth)){
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
        $result="";
        return view('admin/studyplan_quota_all/list',compact('result'));
    }

    public function gettime(Request $request)
    {   
        $result = '';
        $RptBasic = new \App\Rptlib\RptBasic();
        return $RptBasic->gettime($request->input('yerly'));
    }

    public function getorgan(Request $request)
    {   
        $yerly = $request->input('yerly');
        $temptimes = explode(",", $request->input('times'));
        $RptBasic = new \App\Rptlib\RptBasic();
        $data = $RptBasic->getorgan($yerly, $temptimes);

        return $data;
    }

    public function export(Request $request)
    {
        $yerly = $request->input('yerly');
        $organ = $request->input('organ');
        $temptimes = explode(",", $request->input('times'));
        $branch = $request->input('area');
        $condition="";
        $conditionB="";

        if ($branch!="3"){
            $condition.=" AND branch ='".$branch."' ";
            $conditionB.=" AND B.branch ='".$branch."' ";
        }

        $times="";

        for ($i=0; $i < sizeof($temptimes); $i++) { 
            if ($i == sizeof($temptimes)-1) {
                $times=$times."'".$temptimes[$i]."'";
            } else {
                $times=$times."'".$temptimes[$i]."',";
            }
        }



        if($organ=="0"){
            $sql = "SELECT A.organ,IFNULL(C.lname,D.name) AS '機關' 
            FROM t03tb A 
            INNER JOIN t01tb B ON A.class=B.class
            LEFT JOIN m13tb C ON A.organ=C.organ 
            LEFT JOIN m07tb D ON A.organ=D.agency 
            WHERE yerly='$yerly' $conditionB    
            AND B.times IN ($times)     
            GROUP BY A.organ,C.lname,D.NAME  
            ORDER BY A.organ ";

            $organlist = DB::select($sql);
        }else{
            $sql = "SELECT A.organ,IFNULL(C.lname,D.name) AS '機關' 
            FROM t03tb A 
            INNER JOIN t01tb B ON A.class=B.class
            LEFT JOIN m13tb C ON A.organ=C.organ 
            LEFT JOIN m07tb D ON A.organ=D.agency 
            WHERE yerly='$yerly' $conditionB    
            AND B.times IN ($times) 
            AND A.organ ='$organ'
            GROUP BY A.organ,C.lname,D.NAME  
            ORDER BY A.organ ";
            $organlist = DB::select($sql);
        }
        $organlist = json_decode(json_encode($organlist), true);

        //sheet名字
        $count=0;
        // 檔案名稱
        $fileName = 'D9';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel，
        $objPHPExcel = PHPExcel_IOFactory::load($filePath);

        for ($k=0; $k < sizeof($organlist); $k++) {
            $organcode=$organlist[$k]['organ'];
            $organname=$organlist[$k]['機關'];

            $sql = "SELECT A.term FROM t03tb A 
            INNER JOIN t01tb B  ON A.class=B.class
            WHERE B.yerly='$yerly' $conditionB
            AND B.times IN ($times)
            AND A.organ='$organcode' 
            GROUP BY A.term ORDER BY A.term ";

            $termlist = DB::select($sql);
            $termlist = json_decode(json_encode($termlist), true);

            $sql = "SELECT RTRIM(IFNULL(B.name,A.class)) AS classname"; 
            for ($i=0; $i < sizeof($termlist); $i++) {
                $sql.=",SUM(CASE WHEN A.term='".$termlist[$i]['term']."'THEN A.quota ELSE 0 END ) AS '".$termlist[$i]['term']."'"; 
            }
            $sql.="FROM t03tb A 
            LEFT JOIN t01tb B ON A.class=B.class
            WHERE A.organ='$organcode' AND B.yerly='$yerly' $conditionB
            AND B.times IN ($times)
            GROUP BY A.class,B.rank, B.NAME  
            ORDER BY A.class";


            $reportlist = DB::select($sql);
            $reportlist = json_decode(json_encode($reportlist), true);
            //取出全部梯次名稱
            $arraykeys=array_keys($reportlist[0]);

            if((int)$count>0){
                //讀取excel，範本用
                $objPHPExcel1 = PHPExcel_IOFactory::load($filePath);
                //第二頁sheet開始，複製範本為new sheet
                foreach($objPHPExcel1->getSheetNames() as $sheetName)
                {
                    $sheet = $objPHPExcel1->getSheetByName($sheetName);
                    $sheet->setTitle((string)((int)$count+1));
                    $objPHPExcel->addExternalSheet($sheet);
                }
            }
            //指定sheet
            $objPHPExcel->setActiveSheetIndex((int)$count);
            $objActSheet = $objPHPExcel->getActiveSheet();

            //A4畫圖
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('avatar');
            $objDrawing->setDescription('avatar');
            $objDrawing->setPath('../example/img/'.iconv('UTF-8', 'GBK', $fileName).'.PNG');
            // 不指定長寬，截圖大小就是圖片大小
            $objDrawing->setWidth(308);
            //$objDrawing->setHeight(130);
            $objDrawing->setCoordinates('A4');
            //圖片塞進A1
            $objDrawing->setWorksheet($objActSheet);
            //標題列
            $objActSheet->setCellValue('A2', $organname);
            $objActSheet->setCellValue($this->getNameFromNumber(sizeof($arraykeys)+1)."4", "合計");
            $objActSheet->setCellValue("A".(sizeof($reportlist)+6), "合計");
            //apply borders
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            
             $objActSheet->getStyle('A4:'.$this->getNameFromNumber(sizeof($arraykeys)+1).(sizeof($reportlist)+6))->applyFromArray($styleArray);


            //期別迴圈
            for ($i=0; $i < sizeof($arraykeys); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1);
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    //A5開始
                    $objActSheet->setCellValue($NameFromNumber.($j+5), $reportlist[$j][$arraykeys[$i]]);
                    //sum of rows  =IF(SUM(B5:G5)>0,SUM(B5:G5),"")
                    if($i==0)
                        $objActSheet->setCellValue($this->getNameFromNumber(sizeof($arraykeys)+1).($j+5), "=SUM(B".($j+5).":".$this->getNameFromNumber(sizeof($arraykeys)).($j+5).")");
                }
                if($i>0){
                    //B4開始
                    $objActSheet->setCellValue($NameFromNumber."4", "第".strval((int)($arraykeys[$i]))."期");
                    //sum of columns =SUM(B5:B47)
                    $objActSheet->setCellValue($NameFromNumber.(sizeof($reportlist)+6), "=SUM(".$NameFromNumber."5:".$NameFromNumber.(sizeof($reportlist)+4).")");
                }else
                {
                    $objActSheet->setCellValue($this->getNameFromNumber(sizeof($arraykeys)+1).(sizeof($reportlist)+6), "=SUM(".$this->getNameFromNumber(sizeof($arraykeys)+1)."5:".$this->getNameFromNumber(sizeof($arraykeys)+1).(sizeof($reportlist)+4).")");
                }
            }
            //凍結
            $objActSheet->freezePane('B5');
            $count++;

        }
        
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"3",$request->input('doctype'),"各機關研習名額彙總表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

        // //export excel
        // ob_end_clean();
        // ob_start();
        // // Redirect output to a client’s web browser (Excel2007)
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // // 設定下載 Excel 的檔案名稱
        // header('Content-Disposition: attachment;filename="各機關研習名額彙總表.xlsx"');
        // header('Cache-Control: max-age=0');
        // // If you're serving to IE 9, then the following may be needed
        // header('Cache-Control: max-age=1');
        
        // // If you're serving to IE over SSL, then the following may be needed
        // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        // header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        // header ('Pragma: public'); // HTTP/1.0
        
        // //匯出
        // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        // $objWriter->save('php://output');
        // exit;
    }
}
