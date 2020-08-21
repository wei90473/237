<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
// use PhpOffice\PhpSpreadsheet\IOFactory;
// use PhpOffice\PhpSpreadsheet\Style\Border;
// use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Services\User_groupService;
//use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class StudyplanDistributionDetailController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('studyplan_distribution_detail', $user_group_auth)){
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
        $temp = $RptBasic->getclass();
        $classArr =$temp; 
        $result = '';
        return view('admin/studyplan_distribution_detail/list', compact('classArr', 'result'));
    }

    // 搜尋下拉『第幾次調查』
    public function gettime(Request $request)
    {   
        $result = '';
        $RptBasic = new \App\Rptlib\RptBasic();   
        return $RptBasic->gettime($request->input('yerly'));
    }

    // 匯出
    public function export(Request $request)
    {
       
        $yerly = $request->input('yerly');
        $times = $request->input('times');
        $classes = $request->input('classes');
        $branch = $request->input('area');
        $condition="";
        $conditionB="";

        if ($branch!="3"){
            $condition.=" AND branch ='".$branch."' ";
            $conditionB.=" AND B.branch ='".$branch."' ";
        }

        // 搜尋所有班別
        $sql = "SELECT class, name FROM t01tb ";
        if($yerly != '') {
            $sql .= "WHERE yerly='".$yerly."' AND times='".$times."'";
        }
        else {
            $sql .= "WHERE class='".$classes."'";
        }
        $sql .= "  AND EXISTS( SELECT * FROM t03tb WHERE class=t01tb.class ) ORDER BY class";
        $classAllArr = DB::select($sql);
        $classAllArr = json_decode(json_encode($classAllArr), true);

        // 查無資料
        if(sizeof($classAllArr) == 0) {
            $RptBasic = new \App\Rptlib\RptBasic(); 
            $temp = $RptBasic->getclass();
            $classArr =$temp;
            //$classArr = DB::select("SELECT DISTINCT class, RTRIM(name) as name FROM t01tb ORDER BY class DESC");
            $result = '查無資料，請重新查詢';
            return view('admin/studyplan_distribution_detail/list', compact('classArr', 'result'));
        }

        $classTermArr = array();
        $totalDataArr = array();
        
        // 範本檔案名稱
        $fileName = 'D8';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = PHPExcel_IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getDefaultStyle()->getFont()->setName('標楷體');

                //畫圖
                $objDrawing = new PHPExcel_Worksheet_Drawing();
                $objDrawing->setName('avatar');
                $objDrawing->setDescription('avatar');
                $objDrawing->setPath('../example/img/'.iconv('UTF-8', 'GBK', 'D8').'.PNG');
                //不指定長寬，截圖大小就是圖片大小
                $objDrawing->setHeight(133);
                $objDrawing->setWidth(175);
                $objDrawing->setCoordinates('A4');
                //圖片塞進A4
                $objDrawing->setWorksheet($objActSheet);

        for($i=0; $i<sizeof($classAllArr); $i++) {
             if($objPHPExcel->getSheetCount()<sizeof($classAllArr)){
                $clonedWorksheet= clone $objPHPExcel->getSheet(0);
                $clonedWorksheet->setTitle(strval($i+2));
                $clonedWorksheet->setCellValue('A1', '');
                $objPHPExcel->addSheet($clonedWorksheet);
             }
            
//         搜尋班別下的所有期別之預定開課月份
            $sql = "SELECT A.term as term,
                    IFNULL((
                    SELECT concat(SUBSTRING(sdate,4,2)*1,'月')
                    FROM t04tb 
                    WHERE class='".$classAllArr[$i]['class']."' AND term=A.term),'') AS smonth
                    FROM t03tb A 
                    WHERE A.class='".$classAllArr[$i]['class']."' GROUP BY A.term";
            $classTermArr[] = json_decode(json_encode(DB::select($sql)), true);
            $objgetSheet=$objPHPExcel->getSheet($i);
            $objgetSheet->setCellValue('A2', $classAllArr[$i]["name"]);
            $objgetSheet->getHeaderFooter()->setOddFooter( ' &L&B&"標楷體"'.$classAllArr[$i]['class']);            
            for($j=0;$j<sizeof($classTermArr[$i]); $j++) {
                
                if(sizeof($classTermArr[$i])<=2 ){
                    $objgetSheet->getColumnDimension($this->getNameFromNumber($j+2))->setWidth(22);
                }elseif(sizeof($classTermArr[$i])>2 && sizeof($classTermArr[$i])<=5){
                    $objgetSheet->getColumnDimension($this->getNameFromNumber($j+2))->setWidth(13);
                }elseif(sizeof($classTermArr[$i])>5 && sizeof($classTermArr[$i])<=10){
                    $objgetSheet->getColumnDimension($this->getNameFromNumber($j+2))->setWidth(7);
                }elseif(sizeof($classTermArr[$i])>10){
                    $objgetSheet->getColumnDimension($this->getNameFromNumber($j+2))->setWidth(5);
                }


                $objgetSheet->setCellValue($this->getNameFromNumber($j+2)."4","第".strval((int)($classTermArr[$i][$j]["term"]))."期");
                $objgetSheet->setCellValue($this->getNameFromNumber($j+2)."5",$classTermArr[$i][$j]["smonth"]);
                $objgetSheet->getStyle($this->getNameFromNumber($j+2)."5")->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'bfbfbf'))); 
            }

            if(sizeof($classTermArr[$i])<=2 ){
                $objgetSheet->getColumnDimension($this->getNameFromNumber($j+2))->setWidth(22);
            }elseif(sizeof($classTermArr[$i])>2 && sizeof($classTermArr[$i])<=5){
                $objgetSheet->getColumnDimension($this->getNameFromNumber($j+2))->setWidth(13);
            }elseif(sizeof($classTermArr[$i])>5 && sizeof($classTermArr[$i])<=10){
                $objgetSheet->getColumnDimension($this->getNameFromNumber($j+2))->setWidth(7);
            }elseif(sizeof($classTermArr[$i])>10 ){
                $objgetSheet->getColumnDimension($this->getNameFromNumber($j+2))->setWidth(5);
            }

            // $objgetSheet->setCellValue($this->getNameFromNumber(sizeof($classTermArr[$i])+2)."4","合 計");
            // $objgetSheet->getStyle($this->getNameFromNumber(sizeof($classTermArr[$i])+2)."5")->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'bfbfbf'))); 

            /**
             * 取得各班的 名額分配明細
             * A【t03tb 各期參訓單位報名檔】
             * B【m13tb 機關基本資料檔】
             * C【m07tb 訓練機構資料檔】的資料
             */
            $sql = "SELECT CASE WHEN B.lname IS NULL THEN RTRIM(C.name) ELSE RTRIM(B.lname) END as organ";

            for($j=0; $j<sizeof($classTermArr[$i]); $j++) {
                $sql .= ", SUM(CASE WHEN A.term='".$classTermArr[$i][$j]['term']."'THEN A.quota ELSE 0 END ) AS '".$classTermArr[$i][$j]['term']."'"; 
            }

            $sql .= ", 0 AS '合計' 
                    FROM t03tb A 
                    LEFT JOIN m13tb B 
                    ON A.organ=B.organ 
                    LEFT JOIN m07tb C 
                    ON A.organ=C.agency 
                    WHERE class='".$classAllArr[$i]['class']."' 
                    GROUP BY A.organ,B.rank,B.lname,C.name 
                    ORDER BY B.rank";

            $totalDataArr[] = json_decode(json_encode(DB::select($sql)), true);
        }

        for($i=0; $i<sizeof($totalDataArr); $i++){
            $objgetSheet= $objPHPExcel->getSheet($i);
            
           
            for($j=0; $j<sizeof($totalDataArr[$i]); $j++){
                $arraykeys=array_keys((array)$totalDataArr[$i][$j]);
                //填入欄合計    
                if($j==0){
                    $objgetSheet->setCellValue("A".(sizeof($totalDataArr[$i])+7),"合計");
                    $objgetSheet->setCellValue($this->getNameFromNumber(sizeof($arraykeys)+1).(sizeof($totalDataArr[$i])+7),
                                            '=IF(B'.(sizeof($totalDataArr[$i])+7).'<>"",SUM(B'.(sizeof($totalDataArr[$i])+7).':'.$this->getNameFromNumber(sizeof($arraykeys)).($j+6).(sizeof($totalDataArr[$i])+7).'),"")');            
                }

                for($k=0; $k<sizeof($arraykeys); $k++){
                    
                    if($k==sizeof($arraykeys)-1){
                        //sum of rows
                        if($j==0){
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1)."4","合 計");
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1)."5","");
                            $objgetSheet->getStyle($this->getNameFromNumber($k+1)."5")->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'bfbfbf'))); 
                        }
                        $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($j+6),'=IF(B'.($j+6).'<>"",SUM(B'.($j+6).':'.$this->getNameFromNumber($k).($j+6).'),"")');
                    }else{
                        //fill values
                        $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($j+6),$totalDataArr[$i][$j][$arraykeys[$k]]);
                    }

                    if($j==0){
                        //sum of columns
                        $objgetSheet->setCellValue($this->getNameFromNumber($k+2).(sizeof($totalDataArr[$i])+7),
                        '=IF('.$this->getNameFromNumber($k+2).'4<>"",SUM('.$this->getNameFromNumber($k+2).'6:'.$this->getNameFromNumber($k+2).(sizeof($totalDataArr[$i])+5).'),"")');
                        
                        //apply borders
                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );
                        
                         $objgetSheet->getStyle('A4:'.$this->getNameFromNumber(sizeof($arraykeys)).(sizeof($totalDataArr[$i])+7))->applyFromArray($styleArray);
                    }               


                }
            }
        }
        
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"3",$request->input('doctype'),"名額分配明細表");
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
        // header('Content-Disposition: attachment;filename="名額分配明細表.xlsx"');
        // header('Cache-Control: max-age=0');
        // // If you're serving to IE 9, then the following may be needed
        // header('Cache-Control: max-age=1');
        
        // // If you're serving to IE over SSL, then the following may be needed
        // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        // header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        // header ('Pragma: public'); // HTTP/1.0

        // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        // $objWriter->save('php://output');
        // exit;
    }

}
