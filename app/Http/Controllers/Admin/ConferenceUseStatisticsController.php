<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ConferenceUseStatisticsController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('conference_use_statistics', $user_group_auth)){
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
        return view('admin/conference_use_statistics/list');
    }

    /*
    各月份費用統計表 CSDIR6180
    參考Tables:
    使用範本:N13A.xlsx, N13B.xlsx
            每月統計表.xlsx, 年度統計表.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //1:每月統計表  ,2:年度統計表
        $radiotype = $request->input('radiotype');

        if($radiotype!="2"){
            //起始年
            $startYear = $request->input('startYear');
            //起始月
            $startMonth = $request->input('startMonth');
            //結束年
            $endYear = $request->input('endYear');
            //結束月
            $endMonth = $request->input('endMonth');
            //取得 每月統計表
            $sql="SELECT T.YYYMM, SUBSTRING(T.YYYMM,1,3) AS YYY,
                         CASE WHEN SUBSTRING(T.YYYMM,4,1) = '0' THEN SUBSTRING(T.YYYMM,5,1) ELSE SUBSTRING(T.YYYMM,4,2) END AS MM
                    FROM
                        (
                            SELECT LEFT(date,5) AS YYYMM
                            FROM t22tb
                            WHERE site IN ('C01','C02','C14','V01','V02')
                            AND (usertype='1' or usertype='6')
                            AND LEFT(date,5) BETWEEN CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0')) AND
                                                    CONCAT(LPAD('".$endYear."',3,'0'),LPAD('".$endMonth."',2,'0'))
                        GROUP BY LEFT(date,5)
                        ) T ";
        }else{
            //年度
            $yerly = $request->input('yerly');
            //取得 年度統計表
            $sql="SELECT
                        LEFT(date,5),
                        SUM(CASE WHEN site='C01' THEN 1 ELSE 0 END )  AS  C01,
                        '場次' as C01_1,
                        SUM(CASE WHEN site='C02' THEN 1 ELSE 0 END )  AS  C02,
                        '場次' as C02_1,
                        SUM(CASE WHEN site='C14' THEN 1 ELSE 0 END )  AS  C14,
                        '場次' as C14_1,
                        SUM(CASE WHEN site='V01' THEN 1 ELSE 0 END )+
                        SUM(CASE WHEN site='V02' THEN 1 ELSE 0 END ) AS V01V02,
                        '間次' as V_1,
                        '需求明細詳附表' AS remark
                    FROM t22tb
                   WHERE site IN ('C01','C02','C14','V01','V02')
                    AND (usertype='1' OR usertype='6')
                    AND LEFT(date,3)= LPAD('".$yerly."',3,'0')
                    GROUP BY LEFT(date,5)
                    ORDER BY 1 ";
        }

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);

        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        //1:每月統計表  ,2:年度統計表
        if($radiotype!="2"){
            $fileName = 'N13A';
        }
        else{
            $fileName = 'N13B';
        }

        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();

        $reportlist = json_decode(json_encode($reportlist), true);

        if($radiotype=="2"){
            $objActSheet->setCellValue('A2', ''.$yerly.'年度會議場地使用統計表');
            $objActSheet->setCellValue('J20', (date("Y")-'1911').date(".m.d"));

            if(sizeof($reportlist) != 0) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys); $i++) {
                    //excel 欄位 1 == A, etc
                    $NameFromNumber=$this->getNameFromNumber($i+1);
                    //資料by班別迴圈
                    for ($j=0; $j < sizeof($reportlist); $j++) {
                        //A2開始
                        if(!($radiotype=="2" && $i == 0)){
                            $objActSheet->setCellValue($NameFromNumber.($j+5), $reportlist[$j][$arraykeys[$i]]);
                            //高 36
                            $objActSheet->getRowDimension($j+5)->setRowHeight(36);
                        }
                    }
                }
            }
        } else {
            //sheet
            $count=0;
            for ($k=0; $k < sizeof($reportlist); $k++) {
                $YYYMM = $reportlist[$k]['YYYMM'];
                $YYY = $reportlist[$k]['YYY'];
                $MM = $reportlist[$k]['MM'];

                if((int)$count>0){
                    //讀取excel，範本用
                    $objPHPExcel1 = IOFactory::load($filePath);
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
                $objActSheet->setCellValue('A1', ''.$YYY.'年度'.$MM.'月份會議場地使用明細表');
                $objActSheet->setTitle(''.$YYY.'年'.$MM.'月');

            //取得月份會議場地使用明細表
            //最後一列為合計
            $sql="SELECT
                        CONCAT( (CASE WHEN LEFT(S.YD,1) = '0' THEN SUBSTRING(S.YD,2,1) ELSE LEFT(S.YD,2) END),
                                '/',
                                RIGHT(S.YD,2)) AS YYDD,
                        CASE WHEN C.name IS NOT NULL THEN
                                CONCAT(RTRIM(C.name),'第' ,S.term ,'期')
                            ELSE
                            (
                            CASE
                                WHEN LEFT(B.meet,1)='I' THEN RTRIM(B.activity)
                                ELSE RTRIM(B.name)
                            END
                            )
                        END,
                        S.C01,
                        S.C02,
                        S.C14,
                        S.V,
                        CASE WHEN usertype='1' THEN '*' ELSE ''END,
                        CASE WHEN usertype='6' THEN '*' ELSE ''END,
                        NULL AS REMARK
                    FROM
                        (
                        SELECT
                            LEFT(date,5) AS YM,
                            RIGHT(date,4) AS YD,
                            SUM(CASE WHEN site='C01' THEN 1 ELSE 0 END )  AS  C01,
                            SUM(CASE WHEN site='C02' THEN 1 ELSE 0 END )  AS  C02,
                            SUM(CASE WHEN site='C14' THEN 1 ELSE 0 END )  AS  C14,
                            SUM(CASE WHEN site='V01' THEN 1 ELSE 0 END )+
                            SUM(CASE WHEN site='V02' THEN 1 ELSE 0 END ) AS V ,
                            class,
                            term ,
                            usertype
                        FROM t22tb
                        WHERE site IN ('C01','C02','C14','V01','V02')
                        AND (usertype='1' or usertype='6')
                        AND LEFT(date,5) = '".$YYYMM."'
                        GROUP BY LEFT(date,5),RIGHT(date,4),class,term ,usertype
                        ) S LEFT JOIN t04tb A ON A.class=S.class AND A.term=S.term
                            LEFT join t38tb B ON B.meet=S.class AND B.serno=S.term
                            LEFT JOIN t01tb C ON C.class=S.class
                    UNION ALL
                    SELECT
                            '合計', NULL,
                            SUM(CASE WHEN site='C01' THEN 1 ELSE 0 END )  AS  C01,
                            SUM(CASE WHEN site='C02' THEN 1 ELSE 0 END )  AS  C02,
                            SUM(CASE WHEN site='C14' THEN 1 ELSE 0 END )  AS  C14,
                            SUM(CASE WHEN site='V01' THEN 1 ELSE 0 END )+
                            SUM(CASE WHEN site='V02' THEN 1 ELSE 0 END ) AS V ,
                            NULL,NULL,NULL
                    FROM t22tb
                    WHERE site IN ('C01','C02','C14','V01','V02')
                    AND (usertype='1' or usertype='6')
                    AND LEFT(date,5) = '".$YYYMM."'
                    ORDER BY 1     ";

                $reportlist1 = DB::select($sql);
                $reportlist1 = json_decode(json_encode($reportlist1), true);

                //取出全部項目
                if(sizeof($reportlist1) != 0) {
                    $arraykeys=array_keys((array)$reportlist1[0]);
                }


                if(sizeof($reportlist1) != 0) {
                    //項目數量迴圈
                    for ($i=0; $i < sizeof($arraykeys); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+1);
                        //資料by班別迴圈
                        for ($j=0; $j < sizeof($reportlist1); $j++) {
                            //A2開始
                            if(!($radiotype=="2" && $i == 0)){
                                $objActSheet->setCellValue($NameFromNumber.($j+5), $reportlist1[$j][$arraykeys[$i]]);
                                //高 36
                                $objActSheet->getRowDimension($j+5)->setRowHeight(36);
                            }
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
                    $objActSheet->getStyle('A5:'.$NameFromNumber.($j+4))->applyFromArray($arraykeys);
                }


                $count++;
            }

        }

        $outputname="";
        // 設定下載 Excel 的檔案名稱
        if($radiotype!="2"){
            $outputname="會議場地使用統計表-每月統計表";
        }
        else{
            $outputname="會議場地使用統計表-年度統計表";
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"名額分配表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
