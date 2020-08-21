<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Services\User_groupService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
// use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


class StudyplanDistributionAllController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('studyplan_distribution_all', $user_group_auth)){
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
        $result='';
        return view('admin/studyplan_distribution_all/list',compact('result'));
    }

    public function gettime(Request $request)
    {   
        $RptBasic = new \App\Rptlib\RptBasic();   
        return $RptBasic->gettime($request->input('yerly'));
    }

    public function export(Request $request)
    {
        $yerly = $request->input('yerly');
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
        
        $sql = "
        SELECT
        X.organ,
        X.name
        FROM
        (
        SELECT
        A.organ,
        C.rank,
        RTRIM(C.lname) AS NAME
        FROM t02tb A
        INNER JOIN t01tb B
        ON A.class=B.class
        INNER JOIN m13tb C
        ON A.organ=C.organ
        WHERE B.yerly='$yerly'
        AND  B.times IN ($times)
        AND C.kind='Y' $conditionB
        GROUP BY A.organ,C.lname  ,C.rank
        UNION  ALL
        SELECT
        A.organ,
        A.organ,
        RTRIM(C.name) AS NAME
        FROM t02tb A
        INNER JOIN t01tb B
        ON A.class=B.class
        INNER JOIN m07tb C
        ON A.organ=C.agency
        WHERE B.yerly='$yerly'
        AND  B.times IN ($times) $conditionB
        GROUP BY A.organ,C.name
        ) X
        ORDER BY X.rank ";

        $organlist = DB::select($sql);

        if($organlist==[]){
            $result="此條件查無資料，請重新查詢。";
            return view('admin/studyplan_distribution_all/list',compact('result'));
        }

        $sql = " SELECT RTRIM(IFNULL(C.name,A.type)) AS type_name, CONCAT(RTRIM(IFNULL(A.name,A.class)),'(',A.class,')') AS class_name"; 
        for ($i=0; $i < sizeof($organlist); $i++) {
            $sql.=",SUM(CASE WHEN B.organ='".$organlist[$i]->organ."'THEN B.quota ELSE 0 END ) AS '".$organlist[$i]->name."'"; 
        }
        $sql.=", '' AS 合計
        FROM t01tb A
        INNER JOIN t02tb B ON A.class = B.class
        LEFT JOIN s01tb C ON A.TYPE = C.CODE 
        WHERE A.yerly = '$yerly' AND A.times IN($times) $condition
        GROUP BY A.class,A.rank,A.name,A.type,C.name
        ORDER BY A.type,A.class";

        $reportlist = DB::select($sql);
        
        if($reportlist==[]){
            $result="此條件查無資料，請重新查詢。";
            return view('admin/studyplan_distribution_all/list',compact('result'));
        }

    

        //取出全部機關名稱
        $arraykeys=array_keys((array)$reportlist[0]);

        // 範本檔案名稱
        $fileName = 'D7';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = IOFactory::load($filePath);
        
        $objActSheet = $objPHPExcel->getActiveSheet();

        $objActSheet->getHeaderFooter()->setOddHeader( '&L&G&C&附表3　'.$request->input('yerly').'年度研習名額分配總表');

        //A欄合併初始值
        $tempmerge=2;

        //stdClass轉Array
        $reportlist = json_decode(json_encode($reportlist), true);
        //機關數量迴圈
        for ($i=0; $i < sizeof($arraykeys); $i++) {
            //excel 欄位 1 == A, etc
            $NameFromNumber=$this->getNameFromNumber($i+1);
            //資料by班別迴圈
            for ($j=0; $j < sizeof($reportlist); $j++) {
                //A2開始
                $objActSheet->setCellValue($NameFromNumber.($j+2), $reportlist[$j][$arraykeys[$i]]);
                //A欄合併，遇到不一樣的值，上面合併
                if($i==0 && $j>0 && ($reportlist[$j][$arraykeys[0]]!=$reportlist[$j-1][$arraykeys[0]]) ){
                    $objActSheet->mergeCells('A'.$tempmerge.':A'.($j+1));
                    $tempmerge=$j+2;
                }
                //還沒遇到不一樣的就最後一筆資料，上面合併
                if($j==sizeof($reportlist)-1){
                    $objActSheet->mergeCells('A'.$tempmerge.':A'.($j+2));
                    //最下方合計語法
                    if($i==0){
                        $objActSheet->setCellValue($NameFromNumber.($j+4), '');
                    }elseif($i==1){
                        $objActSheet->setCellValue($NameFromNumber.($j+4), '合計');
                    }else{
                        $objActSheet->setCellValue($NameFromNumber.($j+4), '=IF('.$NameFromNumber.'2<>"",SUM('.$NameFromNumber.'2:'.$NameFromNumber.($j+3).'),"")');
                    }
                }
                //最右方合計語法
                if($i == sizeof($arraykeys)-1){
                    $PreNameFromNumber=$this->getNameFromNumber($i);
                    $objActSheet->setCellValue($NameFromNumber.($j+2), '=IF(C'.($j+2).'<>"",SUM(C'.($j+2).':'.$PreNameFromNumber.($j+2).'),"")');
                }
            }
            if($i>1){
                //C1開始
                $objActSheet->setCellValue($NameFromNumber."1", $arraykeys[$i]);
            }
        }
        //全部套框線
        
        $styleArray = [
            'borders' => [
                'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        
        $objActSheet->getStyle('A1:'.$NameFromNumber.($j+3))->applyFromArray($styleArray);
        $objActSheet->getColumnDimension($NameFromNumber)->setAutoSize(true);

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"名額分配總表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 


        //export excel
//         ob_end_clean();
//         ob_start();
//         // Redirect output to a client’s web browser (Excel2007)
//         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//         // 設定下載 Excel 的檔案名稱
//         header('Content-Disposition: attachment;filename="名額分配總表.xlsx"');
//         header('Cache-Control: max-age=0');
//         // If you're serving to IE 9, then the following may be needed
//         header('Cache-Control: max-age=1');
        
//         // If you're serving to IE over SSL, then the following may be needed
//         header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
//         header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
//         header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
//         header ('Pragma: public'); // HTTP/1.0
// //         //凍結A
// //         $objPHPExcel->getActiveSheet()->freezePane('C1');
//         //匯出
//         $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
//         $objWriter->save('php://output');
//         exit;
    }
}
