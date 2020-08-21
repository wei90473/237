<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Services\User_groupService;

class CountParticipateController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('count_participate', $user_group_auth)){
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
        $temp=$RptBasic->getclasstypek();
        $class=$temp;
        $result="";

        return view('admin/count_participate/list',compact('result','class'));
    }

    /*
    各機關參訓人數統計表 CSDIR4120
    參考Tables:
    使用範本:J15.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //起始日期, 年月
        $syear = str_pad($request->input('syear'),3,"0",STR_PAD_LEFT);
        $eyear = str_pad($request->input('eyear'),3,"0",STR_PAD_LEFT);
        $smonth = str_pad($request->input('smonth'),2,"0",STR_PAD_LEFT);
        $emonth = str_pad($request->input('emonth'),2,"0",STR_PAD_LEFT);
        //班別性質
        $classtype = $request->input('classtype');
        $sym=$syear.$smonth;
        $eym=$eyear.$emonth;

        //取得 各機關參訓人數統計表 X軸, 0為全部
        if($classtype=="0"){
            $sqlX = " SELECT RTRIM(X.lname) AS DEPT,
                            RTRIM(X.organ) AS ORGAN
                        FROM m13tb X INNER JOIN
                                                (
                                                SELECT C.organ
                                                FROM t04tb A INNER JOIN t01tb B ON A.class=B.class AND B.type<>'13'
                                                            INNER JOIN t13tb C ON A.class=C.class AND A.term=C.term AND C.status = '1'
                                                        WHERE SUBSTRING(A.edate,1,5) BETWEEN '".$sym."' AND '".$eym."'
                                                GROUP BY C.organ
                                                ) Y ON X.organ=Y.organ
                        ORDER BY X.organ ";
        }else{
            $sqlX = " SELECT RTRIM(X.lname) AS DEPT,
                RTRIM(X.organ) AS ORGAN
                        FROM m13tb X INNER JOIN
                                                (
                                                SELECT C.organ
                                                FROM t04tb A INNER JOIN t01tb B ON A.class=B.class AND B.type<>'13'
                                                            INNER JOIN t13tb C ON A.class=C.class AND A.term=C.term AND C.status = '1'
                                                        WHERE SUBSTRING(A.edate,1,5) BETWEEN '".$sym."' AND '".$eym."'
                                                            AND B.type = '".$classtype."'
                                                GROUP BY C.organ
                                                ) Y ON X.organ=Y.organ
                        ORDER BY X.organ ";

        }
        $reportlistX = json_decode(json_encode(DB::select($sqlX)), true);
        //取出全部項目
        if(sizeof($reportlistX) != 0) {
            $arraykeysX=array_keys((array)$reportlistX[0]);
        }

        // if($classtype=="0"){
        //     $sqlY = " SELECT RTRIM(B.name) AS CLASSNAME,
        //                         COUNT(CASE WHEN C.organ='3010000' THEN 1 ELSE NULL END) AS organ_name
        //                 FROM t04tb A INNER JOIN t01tb B ON A.class=B.class AND B.type<>'13'
        //                             LEFT JOIN t13tb C ON A.class=C.class AND A.term=C.term AND C.status = '1'
        //                 WHERE SUBSTRING(A.edate,1,5) BETWEEN  '".$sym."' AND '".$eym."'
        //                 GROUP BY A.class, B.name, B.rank
        //                 ORDER BY B.rank,A.class";
        // }else{
        //     $sqlY = " SELECT RTRIM(B.name) AS CLASSNAME,
        //                         COUNT(CASE WHEN C.organ='3010000' THEN 1 ELSE NULL END) AS organ_name
        //                 FROM t04tb A INNER JOIN t01tb B ON A.class=B.class AND B.type<>'13'
        //                             LEFT JOIN t13tb C ON A.class=C.class AND A.term=C.term AND C.status = '1'
        //                 WHERE SUBSTRING(A.edate,1,5) BETWEEN  '".$sym."' AND '".$eym."'
        //                 AND B.type = '".$classtype."'
        //                 GROUP BY A.class, B.name, B.rank
        //                 ORDER BY B.rank,A.class";
        // }
        // $reportlistY =json_decode(json_encode(DB::select($sqlY)), true);
        //取出全部項目
        // if(sizeof($reportlistY) != 0) {
        //     $arraykeysY=array_keys((array)$reportlistY[0]);
        // }

        // 檔案名稱
        $fileName = 'J15';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();

     //   $reportlist = json_decode(json_encode($reportlist), true);

        //查詢條件的資料期間：
        $objActSheet->setCellValue('A1', '資料期間：'.$syear.'年'.$smonth.'月至'.$eyear.'年'.$emonth.'月');
        if(sizeof($reportlistX) != 0) {
            for($i=0;$i<sizeof($reportlistX);$i++){
                $objActSheet->setCellValue($this->getNameFromNumber($i+2).'2', $reportlistX[$i]["DEPT"]);

                //取得 各機關參訓人數統計表 Y軸, 0為全部
                if($classtype=="0"){
                    $sqlY = " SELECT RTRIM(B.name) AS CLASSNAME,
                                        COUNT(CASE WHEN C.organ='".$reportlistX[$i]["ORGAN"]."' THEN 1 ELSE NULL END) AS organ_name
                                FROM t04tb A INNER JOIN t01tb B ON A.class=B.class AND B.type<>'13'
                                            LEFT JOIN t13tb C ON A.class=C.class AND A.term=C.term AND C.status = '1'
                                WHERE SUBSTRING(A.edate,1,5) BETWEEN  '".$sym."' AND '".$eym."'
                                GROUP BY A.class, B.name, B.rank
                                ORDER BY B.rank,A.class";
                }else{
                    $sqlY = " SELECT RTRIM(B.name) AS CLASSNAME,
                                        COUNT(CASE WHEN C.organ='".$reportlistX[$i]["ORGAN"]."' THEN 1 ELSE NULL END) AS organ_name
                                FROM t04tb A INNER JOIN t01tb B ON A.class=B.class AND B.type<>'13'
                                            LEFT JOIN t13tb C ON A.class=C.class AND A.term=C.term AND C.status = '1'
                                WHERE SUBSTRING(A.edate,1,5) BETWEEN  '".$sym."' AND '".$eym."'
                                AND B.type = '".$classtype."'
                                GROUP BY A.class, B.name, B.rank
                                ORDER BY B.rank,A.class";
                }
                $reportlistY =json_decode(json_encode(DB::select($sqlY)), true);
                for($j=0;$j<sizeof($reportlistY);$j++){
                    if($i==0){
                        $objActSheet->setCellValue('A'.strval($j+3),$reportlistY[$j]["CLASSNAME"] );
                        $objActSheet->setCellValue($this->getNameFromNumber(sizeof($reportlistX)+2).strval($j+3),"=SUM(B".strval($j+3).":".$this->getNameFromNumber(sizeof($reportlistX)+1).strval($j+3).")");

                        if($j==0){
                            //合計
                            $objActSheet->setCellValue('A'.strval(sizeof($reportlistY)+3),"合計");
                            $objActSheet->setCellValue($this->getNameFromNumber(sizeof($reportlistX)+2).'2',"合計");
                            $objActSheet->setCellValue($this->getNameFromNumber(sizeof($reportlistX)+2).strval(sizeof($reportlistY)+3),"=SUM(".$this->getNameFromNumber(sizeof($reportlistX)+2)."3:".$this->getNameFromNumber(sizeof($reportlistX)+2).strval(sizeof($reportlistY)+2).")");
                            $objActSheet->getColumnDimension($this->getNameFromNumber(sizeof($reportlistX)+2))->setWidth(6);
                        }
                    }
                    if($j==0){
                        $objActSheet->setCellValue($this->getNameFromNumber($i+2).strval(sizeof($reportlistY)+3),"=SUM(".$this->getNameFromNumber($i+2)."3:".$this->getNameFromNumber($i+2).strval(sizeof($reportlistY)+2).")");
                    }
                    $objActSheet->getColumnDimension($this->getNameFromNumber($i+2))->setWidth(5);
                    $objActSheet->setCellValue($this->getNameFromNumber($i+2).strval($j+3),$reportlistY[$j]["organ_name"] );
                }
            }


            //框線
            $styleArray = [
                'borders' => [
                        'allBorders'=> [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];
            //apply borders
            $objActSheet->getStyle('A2:'.$this->getNameFromNumber(sizeof($reportlistX)+2).(sizeof($reportlistY)+3))->applyFromArray($styleArray);
        }

        //freez grid
        $objPHPExcel->getActiveSheet()->freezePane('B3');

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"各機關參訓人數統計表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
