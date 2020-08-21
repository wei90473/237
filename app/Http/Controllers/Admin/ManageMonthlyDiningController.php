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

class ManageMonthlyDiningController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('manage_monthly_dining', $user_group_auth)){
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
        return view('admin/manage_monthly_dining/list');
    }

    /*
    用餐數量管理月報表 CSDIR6110
    參考Tables:
    使用範本:N10.xlsx
    '主要Table:
    't23tb(辦班需求確認檔)
    '使用範本:
    'CSDIR6110.XLT
    '日期不可空白
    '日期格式是否正確(EX:08801)
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //年
        $startYear = $request->input('startYear');
        //月
        $startMonth = $request->input('startMonth');

        /*
        if($startMonth<10){
            $startMonth='0'.$startMonth;
        }
        */

        //取得t23tb(辦班需求確認檔)的資料
        $sql="SELECT DD, W,
                        sum(meacnt) AS meacnt, sum(luncnt) AS luncnt, sum(dincnt) AS dincnt,
                        sum(tabcnt) AS tabcnt, sum(teacnt) AS teacnt, sum(bufcnt) AS bufcnt
                FROM (
                            SELECT SUBSTRING(date,6,2) AS DD,
                                            CASE
                                            WEEKDAY(str_to_date(CONCAT(SUBSTRING(date,1,3) + '1911',SUBSTRING(date,4,4)),'%Y%m%d') )
                                                WHEN 0 THEN '一'
                                                WHEN 1 THEN '二'
                                                WHEN 2 THEN '三'
                                                WHEN 3 THEN '四'
                                                WHEN 4 THEN '五'
                                                WHEN 5 THEN '六'
                                                WHEN 6 THEN '日'
                                            END AS W,
                                            t23.meacnt AS meacnt, t23.luncnt AS luncnt, t23.dincnt AS dincnt,
                                            t23.tabcnt AS tabcnt, t23.teacnt AS teacnt, t23.bufcnt AS bufcnt
                            From t23tb AS t23
                            WHERE SUBSTRING(t23.date,1,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                AND t23.type='3'
                            UNION ALL
                            SELECT x.d AS DD,
                                            CASE
                                            WEEKDAY(str_to_date(CONCAT(
                                                LPAD('".$startYear."',3,'0')
                                                + '1911',
                                                LPAD('".$startMonth."',2,'0')
                                                , x.d),'%Y%m%d') )
                                                WHEN 0 THEN '一'
                                                WHEN 1 THEN '二'
                                                WHEN 2 THEN '三'
                                                WHEN 3 THEN '四'
                                                WHEN 4 THEN '五'
                                                WHEN 5 THEN '六'
                                                WHEN 6 THEN '日'
                                                ELSE ''
                                            END AS W,
                                            0 AS meacnt, 0 AS luncnt, 0 AS dincnt,
                                            0 AS tabcnt, 0 AS teacnt, 0 AS bufcnt
                            FROM  ( SELECT '01' AS d UNION ALL
                                            SELECT '02' AS d UNION ALL SELECT '03' AS d UNION ALL SELECT '04' AS d UNION ALL
                                            SELECT '05' AS d UNION ALL SELECT '06' AS d UNION ALL SELECT '07' AS d UNION ALL
                                            SELECT '08' AS d UNION ALL
                                            SELECT '09' AS d UNION ALL SELECT '10' AS d UNION ALL SELECT '11' AS d UNION ALL
                                            SELECT '12' AS d UNION ALL
                                            SELECT '13' AS d UNION ALL SELECT '14' AS d UNION ALL SELECT '15' AS d UNION ALL
                                            SELECT '16' AS d UNION ALL
                                            SELECT '17' AS d UNION ALL SELECT '18' AS d UNION ALL SELECT '19' AS d UNION ALL
                                            SELECT '20' AS d UNION ALL
                                            SELECT '21' AS d UNION ALL SELECT '22' AS d UNION ALL SELECT '23' AS d UNION ALL
                                            SELECT '24' AS d UNION ALL
                                            SELECT '25' AS d UNION ALL SELECT '26' AS d UNION ALL SELECT '27' AS d UNION ALL
                                            SELECT '28' AS d UNION ALL
                                            SELECT '29' AS d UNION ALL SELECT '30' AS d UNION ALL SELECT '31' AS d ) AS x
                                ) AS T
                GROUP BY DD, W
                ORDER BY 1 ";

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        //合計
        $sqlT="SELECT SUM(t23.meacnt) AS meacnt, SUM(t23.luncnt) AS luncnt, SUM(t23.dincnt) AS dincnt,
                        SUM(t23.tabcnt) AS tabcnt, SUM(t23.teacnt) AS teacnt, SUM(t23.bufcnt) AS bufcnt
                From t23tb AS t23
                WHERE SUBSTRING(t23.date,1,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                AND t23.type='3' ";

        $reportlistT = DB::select($sqlT);
        //取出全部項目
        if(sizeof($reportlistT) != 0) {
            $arraykeysT=array_keys((array)$reportlistT[0]);
        }

        //管理服務費用 早餐	午餐	晚餐	餐桌	茶點	自助餐
        $sql2="	  SELECT  sum(meacnt*meaunit) AS meafee,
                          sum(luncnt*lununit) AS lunfee,
                          sum(dincnt*dinunit) AS dinfee,
                          sum(tabcnt*tabunit) AS tabfee,
                          sum(teacnt*teaunit) AS teafee,
                          sum(bufcnt*bufunit) AS buffee
                    From t23tb
                    WHERE SUBSTRING(date,1,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                    AND type='3'  ";

        $reportlist2 = DB::select($sql2);
        //取出全部項目
        if(sizeof($reportlist2) != 0) {
            $arraykeys2=array_keys((array)$reportlist2[0]);
        }

        // 檔案名稱
        $fileName = 'N10';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&14'.'行政院人事行政總處公務人力發展學院'.substr($startYear,0,3).'年'.$startMonth.'月用餐數量管理月報表');

        $reportlist = json_decode(json_encode($reportlist), true);
        $reportlistT = json_decode(json_encode($reportlistT), true);  //合計
        $reportlist2 = json_decode(json_encode($reportlist2), true);

        //'<<取得t23tb(辦班需求確認檔)的資料
        if(sizeof($reportlist) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //C
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    //C3開始
                    $objActSheet->setCellValue($NameFromNumber.($j+3), $reportlist[$j][$arraykeys[$i]]);
                }
            }
        }

        //合計
        if(sizeof($reportlistT) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeysT); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+3); //C
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlistT); $j++) {
                    //C3開始
                    $objActSheet->setCellValue($NameFromNumber.($j+34), $reportlistT[$j][$arraykeysT[$i]]);
                }
            }
        }

        //管理服務費用 早餐	午餐	晚餐	餐桌	茶點	自助餐
        if(sizeof($reportlist2) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys2); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+3); //C
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist2); $j++) {
                    //C3開始
                    $objActSheet->setCellValue($NameFromNumber.($j+35), $reportlist2[$j][$arraykeys2[$i]]);
                }
            }
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"用餐數量管理月報表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
