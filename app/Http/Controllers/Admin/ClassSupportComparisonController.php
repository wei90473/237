<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Services\User_groupService;

class ClassSupportComparisonController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('class_support_comparison', $user_group_auth)){
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
        return view('admin/class_support_comparison/list');
    }

    /*
    年度各班期訓練成效評估統計表 CSDIR5110
    參考Tables:
    使用範本:L6.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //地區
        $area = $request->input('area');
        //訓練日期
        $sdatetw = $request->input('sdatetw');
        $edatetw = $request->input('edatetw');

        //取得 年度各班期訓練成效評估統計表
        $sql="SELECT
                        T.class,
                        CONCAT( RTRIM(T.name)
                                        ,'第', T.term , '期 ',
                                        SUBSTRING(sdate,1,3),'/',
                                    SUBSTRING(sdate,4,2),'/',
                                        SUBSTRING(sdate,6,2),'~',
                                        SUBSTRING(edate,1,3),'/',
                                        SUBSTRING(edate,4,2),'/',
                                        SUBSTRING(edate,6,2)) AS class_name,
                        T.username,
                        T.duration,
                        (
                                            CASE
                                            WHEN SUM(T.q31cnt) = 0 THEN NULL
                                            ELSE SUM(T.q31)/SUM(T.q31cnt)
                                            END
                                        ) AS q31avg,
                        (
                                            CASE
                                            WHEN SUM(T.q33cnt) = 0 THEN NULL
                                            ELSE SUM(T.q33)/SUM(T.q33cnt)
                                            END
                                        ) AS q33avg,
                        (
                                            CASE
                                            WHEN SUM(T.q41cnt) = 0 THEN NULL
                                            ELSE SUM(T.q41)/SUM(T.q41cnt)
                                            END
                                        ) AS q41avg,
                        (
                                            CASE
                                            WHEN SUM(T.q42cnt) = 0 THEN NULL
                                            ELSE SUM(T.q42)/SUM(T.q42cnt)
                                            END
                                        ) AS q42avg,
                        (
                                        CASE
                                        WHEN SUM(T.q31cnt+T.q33cnt+T.q41cnt+T.q42cnt) = 0 THEN NULL
                                        ELSE SUM(T.q31+T.q33+T.q41+T.q42)/SUM(T.q31cnt+T.q33cnt+T.q41cnt+T.q42cnt)
                                        END
                                    ) AS qavg

                FROM (
                                SELECT
                                                A.class,
                                                C.name,
                                                A.term,
                                                B.sdate,
                                                B.edate,
                                                D.username,
                                                CONCAT(IFNULL(C.period,'') ,
                                                            (
                                                            CASE IFNULL(C.kind,'')
                                                                WHEN '1' THEN '週'
                                                                WHEN '2' THEN '天'
                                                                WHEN '3' THEN '小時'
                                                                ELSE ' '
                                                            END
                                                            )) AS duration,
                                                (CASE A.q31
                                                    WHEN 5 THEN 100
                                                    WHEN 4 THEN 80
                                                    WHEN 3 THEN 60
                                                    WHEN 2 THEN 40
                                                    WHEN 1 THEN 20
                                                    ELSE 0
                                                END
                                                ) AS q31,
                                                (CASE WHEN A.q31>0 THEN 1 ELSE 0 END) AS q31cnt,
                                                (
                                                CASE A.q33
                                                    WHEN 5 THEN 100
                                                    WHEN 4 THEN 80
                                                    WHEN 3 THEN 60
                                                    WHEN 2 THEN 40
                                                    WHEN 1 THEN 20
                                                    ELSE 0
                                                END
                                                ) AS q33,
                                                (CASE WHEN A.q33>0 THEN 1 ELSE 0 END) AS q33cnt,
                                                (
                                                CASE A.q41
                                                    WHEN 5 THEN 100
                                                    WHEN 4 THEN 80
                                                    WHEN 3 THEN 60
                                                    WHEN 2 THEN 40
                                                    WHEN 1 THEN 20
                                                    ELSE 0
                                                END
                                                ) AS q41,
                                                (CASE WHEN A.q41>0 THEN 1 ELSE 0 END) AS q41cnt,
                                                (
                                                CASE A.q42
                                                    WHEN 5 THEN 100
                                                    WHEN 4 THEN 80
                                                    WHEN 3 THEN 60
                                                    WHEN 2 THEN 40
                                                    WHEN 1 THEN 20
                                                    ELSE 0
                                                END
                                                ) AS q42,
                                                (CASE WHEN A.q42>0 THEN 1 ELSE 0 END) AS q42cnt
                                FROM t95tb A LEFT JOIN t04tb B ON A.class = B.class AND A.term = B.term
                                                            LEFT JOIN t01tb C ON A.class = C.class
                                                            LEFT JOIN m09tb D ON B.sponsor = D.userid
                                WHERE B.edate BETWEEN REPLACE('".$sdatetw."','/','') AND REPLACE('".$edatetw."','/','')
                                    AND 1 = (
                                                CASE
                                                    WHEN '".$area."' = '3'                    THEN 1
                                                    WHEN '".$area."' = '1' AND C.branch = '1' THEN 1
                                                    WHEN '".$area."' = '2' AND C.branch = '2' THEN 1
                                                END
                                            )
                ) T
                GROUP BY T.class, T.name, T.term, T.sdate, T.edate, T.username, T.duration
                ORDER BY T.sdate, T.class, T.term			 ";


        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);

        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'L6';

        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        //指定sheet
        $objPHPExcel->setActiveSheetIndex(0);
        $objActSheet = $objPHPExcel->getActiveSheet();

        $reportlist = json_decode(json_encode($reportlist), true);


        //行政支援成效比較表
        if(sizeof($reportlist) != 0) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys); $i++) {
                    //excel 欄位 1 == A, etc
                    $NameFromNumber=$this->getNameFromNumber($i+1);
                    //資料by班別迴圈
                    for ($j=0; $j < sizeof($reportlist); $j++) {
                        //A2開始
                        $objActSheet->setCellValue($NameFromNumber.($j+2), $reportlist[$j][$arraykeys[$i]]);
                    }
                }

                $arraykeys = [
                    'borders' => [
                //只有外框           'outline' => [
                            'allBorders'=> [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];
            $objActSheet->getStyle('A2:'.$NameFromNumber.($j+1))->applyFromArray($arraykeys);
        }


        //意見反映
        $sql="SELECT  A.class,
                        CONCAT(RTRIM(C.name)
                                    ,'第' , A.term , '期 ' ,
                                    SUBSTRING(B.sdate,1,3),'/',
                                    SUBSTRING(B.sdate,4,2),'/',
                                    SUBSTRING(B.sdate,6,2),'~',
                                    SUBSTRING(B.edate,1,3),'/',
                                    SUBSTRING(B.edate,4,2),'/',
                                    SUBSTRING(B.edate,6,2)) AS class_name,
                                    A.note
                FROM t95tb A LEFT JOIN t04tb B ON A.class = B.class AND A.term = B.term
                                    LEFT JOIN t01tb C ON A.class = C.class
                WHERE B.edate BETWEEN REPLACE('".$sdatetw."','/','') AND REPLACE('".$edatetw."','/','')
                AND 1 = (
                                CASE
                                WHEN '".$area."' = '3'                    THEN 1
                                WHEN '".$area."' = '1' AND C.branch = '1' THEN 1
                                WHEN '".$area."' = '2' AND C.branch = '2' THEN 1
                                END
                                )
                AND A.note <> ''
                ORDER BY B.sdate, B.class, B.term, A.serno ";

        $reportlist1 = DB::select($sql);
        $reportlist1 = json_decode(json_encode($reportlist1), true);
        //取出全部項目
        if(sizeof($reportlist1) != 0) {
            $arraykeys=array_keys((array)$reportlist1[0]);
        }

        //指定sheet
        $objPHPExcel->setActiveSheetIndex(1);
        $objActSheet = $objPHPExcel->getActiveSheet();

        $linenum = 0;
        $seq = 1;
        $tempCol1 ='';
        $megrenhum = 2;

        //框線, A欄同一Class項目外框線
        $styleColA = [
            'borders' => [
                    'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        //框線,合併B與C欄,靠左對齊的Classname項目外框線
        $styleMerge = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
            ],
            'borders' => [
                    'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        if(sizeof($reportlist1) != 0) {
            //資料by迴圈
            for ($j=0; $j < sizeof($reportlist1); $j++) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys); $i++) {
                    //excel 欄位 1 == A, etc
                    $NameFromNumber=$this->getNameFromNumber($i+1);
                    //A2開始
                    if($i==0){
                        if($tempCol1!=$reportlist1[$j][$arraykeys[0]]){

                            //$objActSheet->setCellValue('A'.($j+2), $reportlist1[$j][$arraykeys[$i]]);
                            $objActSheet->setCellValue('A'.($linenum+2), $reportlist1[$j][$arraykeys[$i]]);
                            $objActSheet->getStyle('A'.($megrenhum).':A'.($linenum+1))->applyFromArray($styleColA);
                            $megrenhum = $linenum+2;
                        }
                    }

                    if($i==1){
                        if($tempCol1!=$reportlist1[$j][$arraykeys[0]]){
                            //合併
                            //$objActSheet->mergeCells('B'.($j+2).':C'.($j+2));
                            $objActSheet->mergeCells('B'.($linenum+2).':C'.($linenum+2));
                            //$objActSheet->setCellValue('B'.($j+2), $reportlist1[$j][$arraykeys[$i]]);
                            //$objActSheet->getStyle('B'.($j+2).':C'.($j+2))->applyFromArray($styleMerge);
                            $objActSheet->setCellValue('B'.($linenum+2), $reportlist1[$j][$arraykeys[$i]]);
                            $objActSheet->getStyle('B'.($linenum+2).':C'.($linenum+2))->applyFromArray($styleMerge);
                            $linenum++;
                            $seq = 1;
                            $tempCol1 = $reportlist1[$j][$arraykeys[0]];
                        }

                        $styleColBC = [
                            'borders' => [
                                    'outline' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => '000000'],
                                ],
                            ],
                        ];
                        $objActSheet->setCellValue('B'.($linenum+2), ''.$seq.''.'.');
                        $objActSheet->setCellValue('C'.($linenum+2), $reportlist1[$j][$arraykeys[2]]);
                        $objActSheet->getStyle('B'.($linenum+2).':C'.($linenum+2))->applyFromArray($styleColBC);
                        $linenum++;
                        $seq++;

                    }

                }
            }

            //外層框線
            $styleArray = [
                'borders' => [
                        'outline' => [
                        //'allBorders'=> [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            //$objActSheet->getStyle('A2:'.$NameFromNumber.($j+1))->applyFromArray($styleArray);
            $objActSheet->getStyle('A2:'.$NameFromNumber.($linenum+1))->applyFromArray($styleArray);
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"各班次行政支援成效比較表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
