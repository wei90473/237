<?php
namespace App\Http\Controllers\Admin;
set_time_limit(0);

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Services\User_groupService;

class SendtrainingQuotaController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('sendtraining_quota', $user_group_auth)){
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
        //取得班別
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclassEx();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
        $termArr=$temp;
        $result = '';
        return view('admin/sendtraining_quota/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    public function export(Request $request)
    {
        $class=$request->input('classes');
        $startterm=$request->input('startterm');
        $endterm=$request->input('endterm');
        //檢查期別
        if($startterm*1>$endterm*1)
        {
            $temp=DB::select("SELECT DISTINCT class,RTRIM(name) as name FROM t01tb WHERE type <> '13' ORDER BY class DESC");
            $classArr =$temp;
            $temp = json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");

            $termArr=$temp;
            $result = '起始期別應小於結束期別';
            return view('admin/sendtraining_quota/list',compact('classArr','termArr' ,'result'));
        }

        // #'取得班別基本資料檔(t01tb)->訓期類別(kind)訓期、(period)  period例=2  kind 1="週" 2=天 3=小時 name=班別名
        $sql="SELECT name,period,kind FROM t01tb WHERE class='".$class."'";
        $classbasic=json_decode(json_encode(DB::select($sql)), true);;
        $classbasickeys=array_keys((array)$classbasic[0]);

        $kind="";

        switch ($classbasic[0][$classbasickeys[2]]) {
            case "1":
                $kind="週";
              break;
            case "2":
                $kind="天";
              break;
            case "3":
                $kind="小時";
              break;
          }



        // #取得研習時間
        $sql="SELECT DISTINCT A.term, B.sdate, B.edate
        FROM t03tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
        WHERE A.class='".$class."' AND A.term BETWEEN '".$startterm."' AND '".$endterm."'
        ORDER BY A.term";
        $allterms=json_decode(json_encode(DB::select($sql)), true);

        if(empty($allterms)){
            $temp=DB::select("SELECT DISTINCT class,RTRIM(name) as name FROM t01tb WHERE type <> '13' ORDER BY class DESC");
            $classArr =$temp;
            $temp = json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");

            $termArr=$temp;
            $result = '此班期條件查無研習時間';
            return view('admin/sendtraining_quota/list',compact('classArr','termArr' ,'result'));

        }


        $alltermskeys=array_keys((array)$allterms[0]);

        $dataArr =array();
        //取得名額分配資料
        foreach($allterms as $value){

            $sql="SELECT
            CASE
            WHEN B.lname IS NULL
            THEN RTRIM(C.name)
            ELSE RTRIM(B.lname)
            END as orgname,
            SUM(CASE WHEN A.term='".$value[$alltermskeys[0]]."'THEN A.quota ELSE 0 END ) AS '".$value[$alltermskeys[0]]."',
            0 AS 合計
            FROM t03tb A  LEFT JOIN m13tb B ON A.organ=B.organ LEFT JOIN m07tb C ON A.organ=C.agency
            WHERE class='$class'
            GROUP BY A.organ,B.lname,B.rank, C.name
            ORDER BY B.rank";

            $dataArr[]=json_decode(json_encode(DB::select($sql)), true);

        }

        if(empty($dataArr)){
            $temp=DB::select("SELECT DISTINCT class,RTRIM(name) as name FROM t01tb WHERE type <> '13' ORDER BY class DESC");
            $classArr =$temp;
            $temp = json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");

            $termArr=$temp;
            $result = '此條件查無名額資料！';
            return view('admin/sendtraining_quota/list',compact('classArr','termArr' ,'result'));
        }

        // 範本檔案名稱
        $fileName = 'F2';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = IOFactory::load($filePath);

        //set page info
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle("第".strval((int)$allterms[0][$alltermskeys[0]])."期名額分配表");
        $objActSheet->setCellValue("A1", $classbasic[0][$classbasickeys[0]]."第".strval((int)$allterms[0][$alltermskeys[0]])."期名額分配表");
        $objActSheet->setCellValue("A2", "研習日期：自".strval((int)substr($allterms[0][$alltermskeys[1]],0,3))."年"
        .strval((int)substr($allterms[0][$alltermskeys[1]],3,2))."月".strval((int)substr($allterms[0][$alltermskeys[1]],5,2))
        ."日至".strval((int)substr($allterms[0][$alltermskeys[2]],3,2))."月".strval((int)substr($allterms[0][$alltermskeys[2]],5,2))
        ."日止，計".floatval($classbasic[0][$classbasickeys[1]])."天");


        for($i=0; $i<sizeof($dataArr); $i++) {
            if($objPHPExcel->getSheetCount()<sizeof($dataArr)){
               $clonedWorksheet= clone $objPHPExcel->getSheet(0);
               $clonedWorksheet->setTitle("第".strval((int)$allterms[$i+1][$alltermskeys[0]])."期名額分配表");
               $clonedWorksheet->setCellValue("A1", $classbasic[0][$classbasickeys[0]]."第".strval((int)$allterms[$i+1][$alltermskeys[0]])."期名額分配表");
               $clonedWorksheet->setCellValue("A2", "研習日期：自".strval((int)substr($allterms[$i+1][$alltermskeys[1]],0,3)).
               "年".strval((int)substr($allterms[$i+1][$alltermskeys[1]],3,2))."月".strval((int)substr($allterms[$i+1][$alltermskeys[1]],5,2))."日至"
               .strval((int)substr($allterms[$i+1][$alltermskeys[2]],3,2))."月".strval((int)substr($allterms[$i+1][$alltermskeys[2]],5,2))."日止，計"
               .floatval($classbasic[0][$classbasickeys[1]])."天");
               $objPHPExcel->addSheet($clonedWorksheet);
            }
        }

        for($i=0;$i<sizeof($dataArr);$i++){
            $objgetSheet= $objPHPExcel->getSheet($i);
            //填入欄合計
            $objgetSheet->setCellValue("A".(sizeof($dataArr[$i])+6),"合計");
            $objgetSheet->setCellValue("B".(sizeof($dataArr[$i])+6),
                '=IF(B5<>"",SUM(B5:B'.(sizeof($dataArr[$i])+4).'),"")');


            for($j=0; $j<sizeof($dataArr[$i]); $j++){
                $dataArrkeys=array_keys((array)$dataArr[$i][$j]);
                for($k=0; $k<(sizeof($dataArrkeys)-1); $k++){
                    //fill values
                    $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($j+5),$dataArr[$i][$j][$dataArrkeys[$k]]);
                }
            }

            //apply borders
            $styleArray = [
                'borders' => [
                    'allBorders'=> [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $objgetSheet->getStyle('A5:B'.(sizeof($dataArr[$i])+6))->applyFromArray($styleArray);

        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"名額分配表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 


        // //export excel
        // ob_end_clean();
        // ob_start();

        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // // 設定下載 Excel 的檔案名稱
        // header('Content-Disposition: attachment;filename="名額分配表.xlsx"');
        // header('Cache-Control: max-age=0');
        // // If you're serving to IE 9, then the following may be needed
        // header('Cache-Control: max-age=1');

        // // If you're serving to IE over SSL, then the following may be needed
        // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        // header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        // header ('Pragma: public'); // HTTP/1.0

        // //匯出
        // $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        // $objWriter->save('php://output');
        // exit;

    }
}
