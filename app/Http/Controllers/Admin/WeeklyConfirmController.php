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

class WeeklyConfirmController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('weekly_confirm', $user_group_auth)){
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
        return view('admin/weekly_confirm/list');
    }

    /*
    每週確認表 CSDIR6030
    參考Tables:
    使用範本:N2.xlsx
    分別為三個區域串接, 1.教室、場地, 2.住所, 3.用餐
    '同N1 請參考CSDIR6020,除了ReadData(Where不同)與DataToExcel(這支只跑一次最外層的迴圈),其餘皆一樣
    'History:
    '2003/10/13 Update
    '若內容超過多行以上 , 最後一行可能無法印出
    '->一個儲存格每行可有18個字元，若超過，則增加儲存格高度
    '2003/02/13 Update
    '若內容超過多行以上 , 最後一行可能無法印出
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //週範圍, 由週日至週六
        $weekpicker = $request->input('weekpicker');
        //dd($weekpicker);
        //取得 每週確認表
        $sql="SELECT CONCAT('行政院人事行政總處公務人力發展學院',SUBSTRING('".$weekpicker."',1,3),'年',
                        CASE WHEN SUBSTRING('".$weekpicker."',5,1) = '0' THEN SUBSTRING('".$weekpicker."',6,1) ELSE SUBSTRING('".$weekpicker."',5,2) END ,'月',
                        ' 第', WEEK(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',SUBSTRING('".$weekpicker."',5,2),SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d')) -
                        WEEK(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',SUBSTRING('".$weekpicker."',5,2),'01'), '%Y%m%d')) + 1,
                        '週 各訓練班期教室、場地、用餐及住宿確認表') AS TITLE,
                        CONCAT(SUBSTRING(B.DATE1,6,2),'月',SUBSTRING(B.DATE1,9,2),'日') sdate1,
                        CONCAT(SUBSTRING(B.DATE2,6,2),'月',SUBSTRING(B.DATE2,9,2),'日') sdate2,
                        CONCAT(SUBSTRING(B.DATE3,6,2),'月',SUBSTRING(B.DATE3,9,2),'日') sdate3,
                        CONCAT(SUBSTRING(B.DATE4,6,2),'月',SUBSTRING(B.DATE4,9,2),'日') sdate4,
                        CONCAT(SUBSTRING(B.DATE5,6,2),'月',SUBSTRING(B.DATE5,9,2),'日') sdate5,
                        CONCAT(SUBSTRING(B.DATE6,6,2),'月',SUBSTRING(B.DATE6,9,2),'日') sdate6,
                        CONCAT(SUBSTRING(B.DATE7,6,2),'月',SUBSTRING(B.DATE7,9,2),'日') sdate7
                FROM (SELECT DATE_ADD(A.DATE1,INTERVAL 0 DAY) AS DATE1,
                             DATE_ADD(A.DATE1,INTERVAL 1 DAY) AS DATE2,
                             DATE_ADD(A.DATE1,INTERVAL 2 DAY) AS DATE3,
                             DATE_ADD(A.DATE1,INTERVAL 3 DAY) AS DATE4,
                             DATE_ADD(A.DATE1,INTERVAL 4 DAY) AS DATE5,
                             DATE_ADD(A.DATE1,INTERVAL 5 DAY) AS DATE6,
                             DATE_ADD(A.DATE1,INTERVAL 6 DAY) AS DATE7
                        FROM (
                                SELECT DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                   SUBSTRING('".$weekpicker."',5,2),
                                                                   SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 0 DAY) AS DATE1
                                FROM DUAL ) A
                            ) B ";
        $reportlist = DB::select($sql);
        $dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'N2';

        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet = $objPHPExcel->getSheet(0);

        //TITLE, 由週日至週六的日期
        $objActSheet->setCellValue('A1', ''.$dataArr[0]['TITLE']);
        $objActSheet->setCellValue('C2', ''.$dataArr[0]['sdate1']);
        $objActSheet->setCellValue('D2', ''.$dataArr[0]['sdate2']);
        $objActSheet->setCellValue('E2', ''.$dataArr[0]['sdate3']);
        $objActSheet->setCellValue('F2', ''.$dataArr[0]['sdate4']);
        $objActSheet->setCellValue('G2', ''.$dataArr[0]['sdate5']);
        $objActSheet->setCellValue('H2', ''.$dataArr[0]['sdate6']);
        $objActSheet->setCellValue('I2', ''.$dataArr[0]['sdate7']);

        $reportlist = json_decode(json_encode($reportlist), true);

        //1.教室、場地
        $sql="SELECT D.type, D.time,
                    CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 6 THEN
                            CONCAT(A.SORT,A.name_site) ELSE ''
                        END WEEK0,
                    CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 0 THEN
                            CONCAT(A.SORT,A.name_site) ELSE ''
                        END WEEK1,
                    CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 1 THEN
                            CONCAT(A.SORT,A.name_site) ELSE ''
                        END WEEK2,
                    CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 2 THEN
                            CONCAT(A.SORT,A.name_site) ELSE ''
                        END WEEK3,
                    CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 3 THEN
                            CONCAT(A.SORT,A.name_site) ELSE ''
                        END WEEK4,
                    CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 4 THEN
                            CONCAT(A.SORT,A.name_site) ELSE ''
                        END WEEK5,
                    CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 5 THEN
                            CONCAT(A.SORT,A.name_site) ELSE ''
                        END WEEK6,
                        '' AS REMARK
            FROM ( SELECT '教室' AS type, '上午' AS time FROM DUAL
                            UNION ALL
                            SELECT '教室' AS type, '下午' AS time FROM DUAL
                            UNION ALL
                            SELECT '教室' AS type, '晚間' AS time FROM DUAL
                            UNION ALL
                            SELECT '會議' AS type, '上午' AS time FROM DUAL
                            UNION ALL
                            SELECT '會議' AS type, '下午' AS time FROM DUAL
                            UNION ALL
                            SELECT '會議' AS type, '晚間' AS time FROM DUAL
                    ) D LEFT JOIN
                    (
                        SELECT  CASE T.type WHEN '1' THEN '教室' WHEN '2' THEN '會議' END AS type,
                                CASE T.time WHEN '1' THEN '上午' WHEN '2' THEN '下午' WHEN '3'THEN '晚間' END AS time,
                                CONCAT(T.name, T.site) AS name_site, T.date, CCC.SORT
                        FROM (  SELECT t37.date, m14.type,
                                        CASE t37.time WHEN 'A' THEN 1 WHEN 'B' THEN 2 WHEN 'C' THEN 3 ELSE 0 END AS time,
                                        CONCAT(RTRIM(IFNULL(t01.name,'')) ,
                                                    RTRIM(IFNULL(t38.name,'')) ,
                                                    CASE WHEN LEFT(t37.class,1) <> 'M' THEN CONCAT('第' , t37.term , '期') ELSE '' END) AS name,
                                        CONCAT('(' , t37.site ,
                                                    CASE t37.seattype WHEN 'B' THEN '，馬蹄型' WHEN 'C' THEN '，T型' WHEN 'D' THEN '，菱型'   WHEN 'E' THEN '，其他' ELSE '' END,
                                                                '，' , RTRIM(t37.cnt) , '人)') AS site,
                                        t37.term, t37.class, t37.stime, t37.etime,
                                        m14.timetype, t37.purpose
                                FROM t37tb t37  LEFT JOIN m14tb m14 ON t37.site = m14.site AND m14.type IS NOT NULL
                                                LEFT JOIN t01tb t01 ON t37.class = t01.class
                                                LEFT JOIN t38tb t38 On t37.class = t38.meet AND t37.term =  t38.serno
                                WHERE t37.date between SUBSTRING(REPLACE('".$weekpicker."','/',''),1,7) AND CONCAT(DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                SUBSTRING('".$weekpicker."',5,2),
                                                                                                SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%Y') -'1911',
                                           DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                SUBSTRING('".$weekpicker."',5,2),
                                                                                                SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%m%d'))
                                AND t37.type = '2'
                            ) T LEFT JOIN (  SELECT (@rownum := @rownum + 1) AS SORT, CC.class, CC.term, CC.site
                                             FROM (
                                                    SELECT C.name, C.site, C.class, C.term
                                                    FROM (
                                                            SELECT  CONCAT(RTRIM(IFNULL(t01.name,'')) ,
                                                                    RTRIM(IFNULL(t38.name,'')) ,
                                                                    CASE WHEN LEFT(t37.class,1) <> 'M' THEN
                                                                         CONCAT('第' , t37.term , '期') ELSE '' END) AS name,
                                                                    CONCAT('(' , t37.site ,
                                                                    (CASE t37.seattype
                                                                        WHEN 'B' THEN '，馬蹄型'
                                                                        WHEN 'C' THEN '，T型'
                                                                        WHEN 'D' THEN '，菱型'
                                                                        WHEN 'E' THEN '，其他'
                                                                        ELSE '' END),
                                                                        '，' , RTRIM(t37.cnt) , '人)') AS site,
                                                                    t37.class, t37.stime, t37.etime, t37.term
                                                            FROM t37tb t37  LEFT JOIN m14tb m14 ON t37.site = m14.site AND m14.type IS NOT NULL
                                                                            LEFT JOIN t01tb t01 ON t37.class = t01.class
                                                                            LEFT JOIN t38tb t38 On t37.class = t38.meet AND t37.term =  t38.serno
                                                            WHERE t37.date between SUBSTRING(REPLACE('".$weekpicker."','/',''),1,7)
                                                            AND CONCAT(DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                SUBSTRING('".$weekpicker."',5,2),
                                                                                                SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%Y') -'1911',
                                                                       DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                SUBSTRING('".$weekpicker."',5,2),
                                                                                                SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%m%d'))
                                                            AND t37.type = '2'
                                                            ORDER BY t37.class, t37.stime, t37.etime) C
                                                            GROUP BY C.class, C.term, C.name, C.site ) CC,
                                                            (SELECT @rownum := 0) b
                                                        ORDER BY CC.class, CC.term, CC.name, CC.site
                                        ) CCC ON T.class = CCC.class AND T.term = CCC.term AND T.site = CCC.site
                            GROUP BY T.type, T.time, T.name, T.site, T.date, CCC.SORT
                            ) A ON D.type = A.type AND D.time = A.time
            GROUP BY D.type, D.time, A.SORT, A.name_site, A.date
            ORDER BY D.type, D.time, A.SORT, A.name_site, A.date
        ";

        $reportlist1 = DB::select($sql);
        $reportlist1 = json_decode(json_encode($reportlist1), true);
        //取出全部項目
        if(sizeof($reportlist1) != 0) {
            $arraykeys1=array_keys((array)$reportlist1[0]);
        }
        $megrenhumA = 3;
        $megrenhumB = 3;
        $newline = 0;
        $linenum = 3;
        $linenumPos = 4;
        $tempColA ='';
        $tempColB ='';
        $tempColC ='';


        if(sizeof($reportlist1) != 0) {

            //資料by迴圈
            $newline='true';
            for ($j=0; $j < sizeof($reportlist1); $j++) {

                //A4~J所有框線
                $styleArray = [
                    'borders' => [
                            //'outline' => [
                            'allBorders'=> [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];
                $objActSheet->getStyle('A'.($linenum+1).':J'.($linenum+1))->applyFromArray($styleArray);

                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys1); $i++) {
                    //excel 欄位 1 == A, etc
                    $NameFromNumber=$this->getNameFromNumber($i+1);
                    if($i==0){

                        if($tempColA<>$reportlist1[$j][$arraykeys1[0]]){
                            if(($megrenhumA+1)<$linenum){
                                $objActSheet->mergeCells('A'.($megrenhumA+1).':A'.($linenum));

                                //雙框線
                                $styleDoubleBlackBorderOutline = [
                                    'borders' => [
                                            'outline' => [
                                            'borderStyle' => Border::BORDER_DOUBLE,
                                            'color' => ['rgb' => '000000'],
                                        ],
                                    ],
                                ];
                                $objActSheet->getStyle('A'.($megrenhumA+1).':B'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                                $objActSheet->getStyle('C'.($megrenhumA+1).':J'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                                $objActSheet->getStyle('J'.($megrenhumA+1).':J'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);

                            }
                            $megrenhumA=$linenum;
                            $tempColA = $reportlist1[$j][$arraykeys1[0]];
                        }

                        if($j==0){
                            $linenum++;
                            $newline='false';
                            $tempColA = $reportlist1[$j][$arraykeys1[0]];
                            $tempColB = $reportlist1[$j][$arraykeys1[1]];
                        }elseif($tempColB<>$reportlist1[$j][$arraykeys1[1]]){
                            if(($megrenhumB+1)<>$linenum){
                                $objActSheet->mergeCells('B'.($megrenhumB+1).':B'.($linenum));
                            }
                            $megrenhumB=$linenum;
                            $linenum++;
                            $newline='false';

                            $tempColB = $reportlist1[$j][$arraykeys1[1]];
                        }

                    }elseif($i>1){
                        if($tempColC<>$reportlist1[$j][$arraykeys1[$i]] && $reportlist1[$j][$arraykeys1[$i]]<>''){
                            if($newline=='true'){
                                $linenum++;
                            }
                            $newline='true';
                        }
                        $objActSheet->setCellValue('A'.($linenum), $reportlist1[$j][$arraykeys1[0]]);
                        $objActSheet->setCellValue('B'.($linenum), $reportlist1[$j][$arraykeys1[1]]);
                        if($reportlist1[$j][$arraykeys1[$i]]<>''){
                            $objActSheet->setCellValue($NameFromNumber.($linenum), $reportlist1[$j][$arraykeys1[$i]]);
                            $tempColC = $reportlist1[$j][$arraykeys1[$i]];
                        }

                        if(($j+1)==sizeof($reportlist1) && ($i+1)==sizeof($arraykeys1)){
                            if(($megrenhumB+1)<$linenum){
                                $objActSheet->mergeCells('B'.($megrenhumB+1).':B'.($linenum));
                            }
                            $megrenhumB=$linenum;

                            if(($megrenhumA+1)<$linenum){
                                $objActSheet->mergeCells('A'.($megrenhumA+1).':A'.($linenum));
                                //dd('A'.($megrenhumA+1).':A'.($linenum));
                                //雙框線
                                $styleDoubleBlackBorderOutline = [
                                    'borders' => [
                                            'outline' => [
                                            'borderStyle' => Border::BORDER_DOUBLE,
                                            'color' => ['rgb' => '000000'],
                                        ],
                                    ],
                                ];
                                $objActSheet->getStyle('A'.($megrenhumA+1).':B'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                                $objActSheet->getStyle('C'.($megrenhumA+1).':J'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                                $objActSheet->getStyle('J'.($megrenhumA+1).':J'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                            }
                            $megrenhumA=$linenum;
                        }
                    }
                }
            }

       }


        //2.住所
        $sql=" SELECT D.type, D.room,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 6 THEN
                                    CONCAT(A.SORT,A.name_cnt) ELSE ''
                                END WEEK0,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 0 THEN
                                    CONCAT(A.SORT,A.name_cnt) ELSE ''
                                END WEEK1,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 1 THEN
                                    CONCAT(A.SORT,A.name_cnt) ELSE ''
                                END WEEK2,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 2 THEN
                                    CONCAT(A.SORT,A.name_cnt) ELSE ''
                                END WEEK3,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 3 THEN
                                    CONCAT(A.SORT,A.name_cnt) ELSE ''
                                END WEEK4,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 4 THEN
                                    CONCAT(A.SORT,A.name_cnt) ELSE ''
                                END WEEK5,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 5 THEN
                                    CONCAT(A.SORT,A.name_cnt) ELSE ''
                                END WEEK6,
                                '' AS REMARK
                    FROM ( SELECT '住宿及休閒設施' AS type, '1.單人房' AS room FROM DUAL
                            UNION ALL
                            SELECT '住宿及休閒設施' AS type, '2.雙人、愛心房(單床)' AS room FROM DUAL
                            UNION ALL
                            SELECT '住宿及休閒設施' AS type, '3.雙人房(雙床)' AS room FROM DUAL
                            UNION ALL
                            SELECT '住宿及休閒設施' AS type, '4.行政套房' AS room FROM DUAL
                            UNION ALL
                            SELECT '住宿及休閒設施' AS type, '5.附屬設施' AS room FROM DUAL
                            UNION ALL
                            SELECT '住宿及休閒設施' AS type, '6.KTV1' AS room FROM DUAL
                            UNION ALL
                            SELECT '住宿及休閒設施' AS type, '7.KTV2' AS room FROM DUAL
                            ) D LEFT JOIN
                            (
                            SELECT CASE WHEN T.sincnt > 0 OR T.donecnt > 0 OR T.dtwocnt > 0 THEN '住宿及休閒設施'
                                            END AS type,
                                            CASE WHEN T.sincnt > 0 THEN '1.單人房'
                                                        WHEN T.donecnt > 0 THEN '2.雙人、愛心房(單床)'
                                                        WHEN T.dtwocnt > 0 THEN '3.雙人房(雙床)'
                                            END AS room,
                                            CONCAT(T.name, CASE WHEN T.sincnt > 0 THEN T.sincnt
                                                                                    WHEN T.donecnt > 0 THEN T.donecnt
                                                                                    WHEN T.dtwocnt > 0 THEN T.dtwocnt
                                                                                    ELSE ''
                                                                            END ) AS name_cnt,
                                T.date, CCC.SORT
                                    FROM (SELECT t23.class, t23.term, date, sincnt, donecnt, dtwocnt,
                                                            CONCAT(RTRIM(IFNULL(t01.name,'')) ,
                                                                            RTRIM(IFNULL(t38.name,'')) ,
                                                                            CASE WHEN LEFT(t23.class,1) <> 'M' THEN CONCAT('第' , t23.term , '期') ELSE '' END ) AS name,
                                                            CONCAT(RTRIM(meacnt) ,
                                                                            CASE WHEN meavegan > 0 THEN CONCAT('(' , RTRIM(meavegan) , ')') ELSE '' END ) AS mea,
                                                            CONCAT(RTRIM(luncnt) ,
                                                                            CASE WHEN lunvegan > 0 THEN CONCAT('(' , RTRIM(lunvegan) , ')') ELSE '' END ) AS lun,
                                                            CONCAT(RTRIM(dincnt) ,
                                                                            CASE WHEN dinvegan > 0 THEN CONCAT('(' , RTRIM(dinvegan) , ')') ELSE '' END ) AS din,
                                                            CONCAT(CASE tabtype WHEN '1'  THEN '午餐' WHEN '2' THEN '晚餐' ELSE '' END ,
                                                                            CASE WHEN tabcnt > 0   THEN RTRIM(tabcnt) ELSE '' END ,
                                                                            CASE WHEN tabvegan > 0 THEN CONCAT('(' , RTRIM(tabvegan) , ')') ELSE '' END ,
                                                                            CASE WHEN tabcnt > 0 OR tabvegan > 0 THEN '人' ELSE '' END ,
                                                                            CASE WHEN tabunit > 0  THEN CONCAT('*' , RTRIM(tabunit) , '元') ELSE '' END
                                                                        ) AS tab,
                                                            CONCAT(CASE buftype WHEN '1'  THEN '午餐' WHEN '2' THEN '晚餐' ELSE '' END ,
                                                                            CASE WHEN bufcnt >0    THEN RTRIM(bufcnt) ELSE '' END ,
                                                                            CASE WHEN bufvegan > 0 THEN CONCAT('(' , RTRIM(bufvegan) , ')') ELSE '' END ,
                                                                            CASE WHEN bufcnt > 0 OR bufvegan > 0 THEN '人' ELSE '' END ,
                                                                            CASE WHEN bufunit > 0  THEN CONCAT('*' , RTRIM(bufunit) , '元') ELSE '' END
                                                                            ) AS buf,
                                                            CONCAT(CASE WHEN teacnt > 0 THEN
                                                                            CONCAT(RTRIM(teacnt) , '人*' , RTRIM(teaunit) , '元') ELSE '' END ,
                                                                            CASE WHEN LTRIM(RTRIM(teatime)) <> '' THEN teatime ELSE '' END
                                                                            )AS tea
                                                FROM t23tb t23  LEFT JOIN t01tb t01 ON t23.class = t01.class
                                                                LEFT JOIN t38tb t38 On t23.class = t38.meet AND t23.term = t38.serno
                                                WHERE t23.date between SUBSTRING(REPLACE('".$weekpicker."','/',''),1,7)
                                                    AND CONCAT(DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%Y') -'1911',
                                                            DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%m%d')
                                                                                                )
                                            ) T LEFT JOIN (  SELECT (@rownum := @rownum + 1) AS SORT, CC.class, CC.term, CC.site
                                                                        FROM (
                                                                                    SELECT C.name, C.site, C.class, C.term
                                                                                        FROM (
                                                                                                    SELECT CONCAT(RTRIM(IFNULL(t01.name,'')) ,
                                                                                                                                        RTRIM(IFNULL(t38.name,'')) ,
                                                                                                                                        CASE WHEN LEFT(t37.class,1) <> 'M' THEN CONCAT('第' , t37.term , '期') ELSE '' END) AS name,
                                                                                                                            CONCAT('(' , t37.site ,
                                                                                                                                        CASE t37.seattype WHEN 'B' THEN '，馬蹄型' WHEN 'C' THEN '，T型'
                                                                                                                                                                            WHEN 'D' THEN '，菱型'   WHEN 'E' THEN '，其他'
                                                                                                                                                    ELSE '' END,
                                                                                                                                                    '，' , RTRIM(t37.cnt) , '人)') AS site,
                                                                                                                                        t37.class, t37.stime, t37.etime, t37.term
                                                                                                                FROM t37tb t37  LEFT JOIN m14tb m14 ON t37.site = m14.site AND m14.type IS NOT NULL
                                                                                                                                LEFT JOIN t01tb t01 ON t37.class = t01.class
                                                                                                                                LEFT JOIN t38tb t38 On t37.class = t38.meet AND t37.term =  t38.serno
                                                                                                                WHERE t37.date between SUBSTRING(REPLACE('".$weekpicker."','/',''),1,7)
                                                    AND CONCAT(DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%Y') -'1911',
                                                            DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%m%d')
                                                                                                )
                                                                                                                    AND t37.type = '2'
                                                                                                                ORDER BY t37.class, t37.stime, t37.etime) C
                                                                                                    GROUP BY C.class, C.term, C.name, C.site ) CC,
                                                                                                                    (SELECT @rownum := 0) b
                                                                                                    ORDER BY CC.class, CC.term, CC.name, CC.site
                                                            ) CCC ON T.class = CCC.class AND T.term = CCC.term
                                WHERE (T.sincnt > 0 OR T.donecnt > 0 OR T.dtwocnt > 0) AND CCC.SORT IS NOT NULL
                                    ) A ON D.type = A.type AND D.room = A.room
                    GROUP BY D.type, D.room, A.SORT,A.name_cnt, A.date
                    ORDER BY D.type, D.room, A.SORT,A.name_cnt, A.date
        ";
        $reportlist2 = DB::select($sql);
        $reportlist2 = json_decode(json_encode($reportlist2), true);
        //取出全部項目
        if(sizeof($reportlist2) != 0) {
            $arraykeys2=array_keys((array)$reportlist2[0]);
        }

        $megrenhumA = $linenum;
        $megrenhumB = $linenum;
        $linenumPos = $linenum+1;
        $newline = 0;
        $tempColA ='';
        $tempColB ='';
        $tempColC ='';

        if(sizeof($reportlist2) != 0) {

            //資料by迴圈
            $newline='true';
            for ($j=0; $j < sizeof($reportlist2); $j++) {

                //A4~J所有框線
                $styleArray = [
                    'borders' => [
                            //'outline' => [
                            'allBorders'=> [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];
                $objActSheet->getStyle('A'.($linenum+1).':J'.($linenum+1))->applyFromArray($styleArray);

                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys2); $i++) {
                    //excel 欄位 1 == A, etc
                    $NameFromNumber=$this->getNameFromNumber($i+1);
                    if($i==0){

                        if($tempColA<>$reportlist2[$j][$arraykeys2[0]]){
                            if(($megrenhumA+1)<$linenum){
                                $objActSheet->mergeCells('A'.($megrenhumA+1).':A'.($linenum));

                                //雙框線
                                $styleDoubleBlackBorderOutline = [
                                    'borders' => [
                                            'outline' => [
                                            'borderStyle' => Border::BORDER_DOUBLE,
                                            'color' => ['rgb' => '000000'],
                                        ],
                                    ],
                                ];
                                $objActSheet->getStyle('A'.($megrenhumA+1).':B'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                                $objActSheet->getStyle('C'.($megrenhumA+1).':J'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                                $objActSheet->getStyle('J'.($megrenhumA+1).':J'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);

                            }
                            $megrenhumA=$linenum;
                            $tempColA = $reportlist2[$j][$arraykeys2[0]];
                        }

                        if($j==0){
                            $linenum++;
                            $newline='false';
                            $tempColA = $reportlist2[$j][$arraykeys2[0]];
                            $tempColB = $reportlist2[$j][$arraykeys2[1]];
                        }elseif($tempColB<>$reportlist2[$j][$arraykeys2[1]]){
                            if(($megrenhumB+1)<>$linenum){
                                $objActSheet->mergeCells('B'.($megrenhumB+1).':B'.($linenum));
                            }
                            $megrenhumB=$linenum;
                            $linenum++;
                            $newline='false';

                            $tempColB = $reportlist2[$j][$arraykeys2[1]];
                        }

                    }elseif($i>1){
                        $str_secC = explode('期',$reportlist2[$j][$arraykeys2[$i]]);
                        //if($tempColC<>$reportlist2[$j][$arraykeys2[$i]] && $reportlist2[$j][$arraykeys2[$i]]<>''){
                        if($tempColC<>$str_secC[0] && $str_secC[0]<>''){
                            if($newline=='true'){
                                $linenum++;
                            }
                            $newline='true';
                        }

                        $objActSheet->setCellValue('A'.($linenum), $reportlist2[$j][$arraykeys2[0]]);
                        //$objActSheet->setCellValue('B'.($linenum), $reportlist2[$j][$arraykeys2[1]]);
                        $objActSheet->setCellValue('B'.($linenum), substr($reportlist2[$j][$arraykeys2[1]],2));
                        if(substr($reportlist2[$j][$arraykeys2[1]],2) == '雙人、愛心房(單床)'){
                            $objActSheet->getStyle('B'.($linenum))->getFont()->setSize(6);
                        }
                        if($reportlist2[$j][$arraykeys2[$i]]<>''){
                            $objActSheet->setCellValue($NameFromNumber.($linenum), $reportlist2[$j][$arraykeys2[$i]]);
                            //$tempColC = $reportlist2[$j][$arraykeys2[$i]];
                            $tempColC = $str_secC[0];
                        }

                        if(($j+1)==sizeof($reportlist2) && ($i+1)==sizeof($arraykeys2)){
                            if(($megrenhumB+1)<$linenum){
                                $objActSheet->mergeCells('B'.($megrenhumB+1).':B'.($linenum));
                            }
                            $megrenhumB=$linenum;

                            if(($megrenhumA+1)<$linenum){
                                $objActSheet->mergeCells('A'.($megrenhumA+1).':A'.($linenum));
                                //dd('A'.($megrenhumA+1).':A'.($linenum));
                                //雙框線
                                $styleDoubleBlackBorderOutline = [
                                    'borders' => [
                                            'outline' => [
                                            'borderStyle' => Border::BORDER_DOUBLE,
                                            'color' => ['rgb' => '000000'],
                                        ],
                                    ],
                                ];
                                $objActSheet->getStyle('A'.($megrenhumA+1).':B'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                                $objActSheet->getStyle('C'.($megrenhumA+1).':J'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                                $objActSheet->getStyle('J'.($megrenhumA+1).':J'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                            }
                            $megrenhumA=$linenum;
                        }
                    }
                }
            }
        }


        //3.用餐
        $sql=" SELECT D.type, D.room,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 6 THEN
                                CONCAT(A.SORT,A.name_cnt) ELSE ''
                            END WEEK0,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 0 THEN
                                CONCAT(A.SORT,A.name_cnt) ELSE ''
                            END WEEK1,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 1 THEN
                                CONCAT(A.SORT,A.name_cnt) ELSE ''
                            END WEEK2,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 2 THEN
                                CONCAT(A.SORT,A.name_cnt) ELSE ''
                            END WEEK3,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 3 THEN
                                CONCAT(A.SORT,A.name_cnt) ELSE ''
                            END WEEK4,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 4 THEN
                                CONCAT(A.SORT,A.name_cnt) ELSE ''
                            END WEEK5,
                        CASE WHEN WEEKDAY(str_to_date(CONCAT(SUBSTRING(A.date,1,3)+'1911',SUBSTRING(A.date,4,4)), '%Y%m%d')) = 5 THEN
                                CONCAT(A.SORT,A.name_cnt) ELSE ''
                            END WEEK6,
                        '' AS REMARK
                    FROM ( SELECT '用餐' AS type, '1.早餐(素食)' AS room FROM DUAL
                            UNION ALL
                            SELECT '用餐' AS type, '2.午餐(素食)' AS room FROM DUAL
                            UNION ALL
                            SELECT '用餐' AS type, '3.晚餐(素食)' AS room FROM DUAL
                            UNION ALL
                            SELECT '用餐' AS type, '4.訂席桌餐' AS room FROM DUAL
                            UNION ALL
                            SELECT '用餐' AS type, '5.自助餐' AS room FROM DUAL
                            UNION ALL
                            SELECT '用餐' AS type, '6.茶點' AS room FROM DUAL
                    ) D LEFT JOIN
                    (SELECT CASE WHEN T.mea > 0 OR T.lun > 0 OR T.din > 0 OR T.tab > 0 OR T.buf > 0 OR T.tea > 0 THEN '用餐'
                                        END AS type,
                            CASE WHEN T.mea > 0 THEN '1.早餐(素食)'
                                                    WHEN T.lun > 0 THEN '2.午餐(素食)'
                                                    WHEN T.din > 0 THEN '3.晚餐(素食)'
                                                    WHEN T.tab > 0 THEN '4.訂席桌餐'
                                                    WHEN T.buf > 0 THEN '5.自助餐'
                                                    WHEN T.tea > 0 THEN '6.茶點'
                                        END AS room,
                            CONCAT(T.name, CASE WHEN T.mea > 0 THEN T.mea
                                                                                WHEN T.lun > 0 THEN T.lun
                                                                                WHEN T.din > 0 THEN T.din
                                                                                WHEN T.tab > 0 THEN T.tab
                                                                                WHEN T.buf > 0 THEN T.buf
                                                                                WHEN T.tea > 0 THEN T.tea
                                                                                ELSE ''
                                                                        END ) AS name_cnt,
                            T.date  , CCC.SORT
                        FROM (
                            (SELECT t23.class, t23.term, date,
                                    CONCAT(RTRIM(IFNULL(t01.name,'')) ,
                                            RTRIM(IFNULL(t38.name,'')) ,
                                            CASE WHEN LEFT(t23.class,1) <> 'M' THEN CONCAT('第' , t23.term , '期') ELSE '' END ) AS name,
                                    CONCAT(RTRIM(meacnt) ,
                                            CASE WHEN meavegan > 0 THEN CONCAT('(' , RTRIM(meavegan) , ')') ELSE '' END ) AS mea,
                                    '0' AS lun,
                                    '0' AS din,
                                    '0' AS tab,
                                    '0' AS buf,
                                    '0' tea
                                FROM t23tb t23 LEFT JOIN t01tb t01 ON t23.class = t01.class
                                                LEFT JOIN t38tb t38 On t23.class = t38.meet AND t23.term = t38.serno
                                WHERE t23.date between SUBSTRING(REPLACE('".$weekpicker."','/',''),1,7)
                                                    AND CONCAT(DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%Y') -'1911',
                                                            DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%m%d')
                                                                                                )
                                    )
                                UNION ALL
                                    (
                                SELECT t23.class, t23.term, date,
                                    CONCAT(RTRIM(IFNULL(t01.name,'')) ,
                                            RTRIM(IFNULL(t38.name,'')) ,
                                            CASE WHEN LEFT(t23.class,1) <> 'M' THEN CONCAT('第' , t23.term , '期') ELSE '' END ) AS name,
                                    '0' AS mea,
                                    CONCAT(RTRIM(luncnt) ,
                                            CASE WHEN lunvegan > 0 THEN CONCAT('(' , RTRIM(lunvegan) , ')') ELSE '' END ) AS lun,
                                    '0' AS din,
                                    '0' AS tab,
                                    '0' AS buf,
                                    '0' AS tea
                                FROM t23tb t23 LEFT JOIN t01tb t01 ON t23.class = t01.class
                                                LEFT JOIN t38tb t38 On t23.class = t38.meet AND t23.term = t38.serno
                                WHERE t23.date between SUBSTRING(REPLACE('".$weekpicker."','/',''),1,7)
                                                    AND CONCAT(DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%Y') -'1911',
                                                            DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%m%d')
                                                                                                )
                                    )
                                UNION ALL
                                    (
                                SELECT t23.class, t23.term, date,
                                    CONCAT(RTRIM(IFNULL(t01.name,'')) ,
                                            RTRIM(IFNULL(t38.name,'')) ,
                                            CASE WHEN LEFT(t23.class,1) <> 'M' THEN CONCAT('第' , t23.term , '期') ELSE '' END ) AS name,
                                    '0' AS mea,
                                    '0' AS lun,
                                    CONCAT(RTRIM(dincnt) ,
                                            CASE WHEN dinvegan > 0 THEN CONCAT('(' , RTRIM(dinvegan) , ')') ELSE '' END ) AS din,
                                    '0' AS tab,
                                    '0' AS buf,
                                    '0' AS tea
                                FROM t23tb t23 LEFT JOIN t01tb t01 ON t23.class = t01.class
                                                LEFT JOIN t38tb t38 On t23.class = t38.meet AND t23.term = t38.serno
                                WHERE t23.date between SUBSTRING(REPLACE('".$weekpicker."','/',''),1,7)
                                                    AND CONCAT(DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%Y') -'1911',
                                                            DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%m%d')
                                                                                                )
                                    )
                                UNION ALL
                                    (
                                SELECT t23.class, t23.term, date,
                                    CONCAT(RTRIM(IFNULL(t01.name,'')) ,
                                            RTRIM(IFNULL(t38.name,'')) ,
                                            CASE WHEN LEFT(t23.class,1) <> 'M' THEN CONCAT('第' , t23.term , '期') ELSE '' END ) AS name,
                                    '0' AS mea,
                                    '0' AS lun,
                                    '0' AS din,
                                    CONCAT(CASE tabtype WHEN '1'  THEN '午餐' WHEN '2' THEN '晚餐' ELSE '' END ,
                                            CASE WHEN tabcnt > 0   THEN RTRIM(tabcnt) ELSE '' END ,
                                            CASE WHEN tabvegan > 0 THEN CONCAT('(' , RTRIM(tabvegan) , ')') ELSE '' END ,
                                            CASE WHEN tabcnt > 0 OR tabvegan > 0 THEN '人' ELSE '' END ,
                                            CASE WHEN tabunit > 0  THEN CONCAT('*' , RTRIM(tabunit) , '元') ELSE '' END
                                            ) AS tab,
                                    '0' buf,
                                    '0' AS tea
                                FROM t23tb t23 LEFT JOIN t01tb t01 ON t23.class = t01.class
                                                LEFT JOIN t38tb t38 On t23.class = t38.meet AND t23.term = t38.serno
                                WHERE t23.date between SUBSTRING(REPLACE('".$weekpicker."','/',''),1,7)
                                                    AND CONCAT(DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%Y') -'1911',
                                                            DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%m%d')
                                                                                                )
                                    )
                                UNION ALL
                                    (
                                SELECT t23.class, t23.term, date,
                                    CONCAT(RTRIM(IFNULL(t01.name,'')) ,
                                            RTRIM(IFNULL(t38.name,'')) ,
                                            CASE WHEN LEFT(t23.class,1) <> 'M' THEN CONCAT('第' , t23.term , '期') ELSE '' END ) AS name,
                                    '0' AS mea,
                                    '0' AS lun,
                                    '0' AS din,
                                    '0' AS tab,
                                    CONCAT(CASE buftype WHEN '1'  THEN '午餐' WHEN '2' THEN '晚餐' ELSE '' END ,
                                            CASE WHEN bufcnt >0    THEN RTRIM(bufcnt) ELSE '' END ,
                                            CASE WHEN bufvegan > 0 THEN CONCAT('(' , RTRIM(bufvegan) , ')') ELSE '' END ,
                                            CASE WHEN bufcnt > 0 OR bufvegan > 0 THEN '人' ELSE '' END ,
                                            CASE WHEN bufunit > 0  THEN CONCAT('*' , RTRIM(bufunit) , '元') ELSE '' END
                                            ) AS buf,
                                    '0' AS tea
                                FROM t23tb t23 LEFT JOIN t01tb t01 ON t23.class = t01.class
                                                LEFT JOIN t38tb t38 On t23.class = t38.meet AND t23.term = t38.serno
                                WHERE t23.date between SUBSTRING(REPLACE('".$weekpicker."','/',''),1,7)
                                                    AND CONCAT(DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%Y') -'1911',
                                                            DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%m%d')
                                                                                                )
                                    )
                                UNION ALL
                                    (
                                SELECT t23.class, t23.term, date,
                                    CONCAT(RTRIM(IFNULL(t01.name,'')) ,
                                            RTRIM(IFNULL(t38.name,'')) ,
                                            CASE WHEN LEFT(t23.class,1) <> 'M' THEN CONCAT('第' , t23.term , '期') ELSE '' END ) AS name,
                                    '0' AS mea,
                                    '0' AS lun,
                                    '0' AS din,
                                    '0' AS tab,
                                    '0' AS buf,
                                    CONCAT(CASE WHEN teacnt > 0 THEN
                                            CONCAT(RTRIM(teacnt) , '人*' , RTRIM(teaunit) , '元') ELSE '' END ,
                                            CASE WHEN LTRIM(RTRIM(teatime)) <> '' THEN teatime ELSE '' END
                                            ) AS tea
                                FROM t23tb t23 LEFT JOIN t01tb t01 ON t23.class = t01.class
                                            LEFT JOIN t38tb t38 On t23.class = t38.meet AND t23.term = t38.serno
                                WHERE t23.date between SUBSTRING(REPLACE('".$weekpicker."','/',''),1,7)
                                                    AND CONCAT(DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%Y') -'1911',
                                                            DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%m%d')
                                                                                                )
                                                )
                                            ) T LEFT JOIN (  SELECT (@rownum := @rownum + 1) AS SORT, CC.class, CC.term
                                                                FROM (SELECT C.name, C.site, C.class, C.term
                                                                        FROM (SELECT CONCAT(RTRIM(IFNULL(t01.name,'')) ,
                                                                                            RTRIM(IFNULL(t38.name,'')) ,
                                                                                            CASE WHEN LEFT(t37.class,1) <> 'M' THEN
                                                                                                                                                                    CONCAT('第' , t37.term , '期') ELSE '' END) AS name,
                                                                                    CONCAT('(' , t37.site ,
                                                                                            CASE t37.seattype WHEN 'B' THEN '，馬蹄型'
                                                                                                                                                                                        WHEN 'C' THEN '，T型'                                                                                             WHEN 'D' THEN '，菱型'
                                                                                                                                                                                                        WHEN 'E' THEN '，其他'
                                                                                                            ELSE '' END,
                                                                                                '，' , RTRIM(t37.cnt) , '人)') AS site,                                                                    t37.class, t37.stime, t37.etime, t37.term
                                                                                FROM t37tb t37 LEFT JOIN m14tb m14 ON t37.site = m14.site
                                                                                            LEFT JOIN t01tb t01 ON t37.class = t01.class
                                                                                            LEFT JOIN t38tb t38 On t37.class = t38.meet
                                                                                                                                                                                                AND t37.term =  t38.serno
                                                                            WHERE t37.date between SUBSTRING(REPLACE('".$weekpicker."','/',''),1,7)
                                                    AND CONCAT(DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%Y') -'1911',
                                                            DATE_FORMAT(DATE_ADD(str_to_date(CONCAT(SUBSTRING('".$weekpicker."',1,3)+'1911',
                                                                                                    SUBSTRING('".$weekpicker."',5,2),
                                                                                                    SUBSTRING('".$weekpicker."',8,2)), '%Y%m%d'),INTERVAL 6 DAY),'%m%d')
                                                                                                )
                                                                                AND t37.type = '2'
                                                                            ORDER BY t37.class, t37.stime, t37.etime) C
                                                                            GROUP BY C.class, C.term, C.name, C.site ) CC,
                                                                                    (SELECT @rownum := 0) b
                                                                            ORDER BY CC.class, CC.term, CC.name, CC.site
                                                        ) CCC ON T.class = CCC.class
                            WHERE (T.mea > 0 OR T.lun > 0 OR T.din > 0  OR T.tab > 0 OR T.buf > 0 OR T.tea > 0) AND CCC.SORT IS NOT NULL
                        ) A ON D.type = A.type AND D.room = A.room
                    GROUP BY D.type, D.room, A.SORT,A.name_cnt, A.date
                    ORDER BY D.type, D.room, A.SORT,A.name_cnt, A.date
                                        ";


        $reportlist3 = DB::select($sql);
        $reportlist3 = json_decode(json_encode($reportlist3), true);
        //取出全部項目
        if(sizeof($reportlist3) != 0) {
            $arraykeys3=array_keys((array)$reportlist3[0]);
        }

        $megrenhumA = $linenum;
        $megrenhumB = $linenum;
        $linenumPos = $linenum+1;
        $newline = 0;
        $tempColA ='';
        $tempColB ='';
        $tempColC ='';

        if(sizeof($reportlist3) != 0) {

            //資料by迴圈
            $newline='true';
            for ($j=0; $j < sizeof($reportlist3); $j++) {

                //A4~J所有框線
                $styleArray = [
                    'borders' => [
                            //'outline' => [
                            'allBorders'=> [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];
                $objActSheet->getStyle('A'.($linenum+1).':J'.($linenum+1))->applyFromArray($styleArray);

                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys3); $i++) {
                    //excel 欄位 1 == A, etc
                    $NameFromNumber=$this->getNameFromNumber($i+1);
                    if($i==0){

                        if($tempColA<>$reportlist3[$j][$arraykeys3[0]]){
                            if(($megrenhumA+1)<$linenum){
                                $objActSheet->mergeCells('A'.($megrenhumA+1).':A'.($linenum));

                                //雙框線
                                $styleDoubleBlackBorderOutline = [
                                    'borders' => [
                                            'outline' => [
                                            'borderStyle' => Border::BORDER_DOUBLE,
                                            'color' => ['rgb' => '000000'],
                                        ],
                                    ],
                                ];
                                $objActSheet->getStyle('A'.($megrenhumA+1).':B'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                                $objActSheet->getStyle('C'.($megrenhumA+1).':J'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                                $objActSheet->getStyle('J'.($megrenhumA+1).':J'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);

                            }
                            $megrenhumA=$linenum;
                            $tempColA = $reportlist3[$j][$arraykeys3[0]];
                        }

                        if($j==0){
                            $linenum++;
                            $newline='false';
                            $tempColA = $reportlist3[$j][$arraykeys3[0]];
                            $tempColB = $reportlist3[$j][$arraykeys3[1]];
                        }elseif($tempColB<>$reportlist3[$j][$arraykeys3[1]]){
                            if(($megrenhumB+1)<>$linenum){
                                $objActSheet->mergeCells('B'.($megrenhumB+1).':B'.($linenum));
                            }
                            $megrenhumB=$linenum;
                            $linenum++;
                            $newline='false';

                            $tempColB = $reportlist3[$j][$arraykeys3[1]];
                        }

                    }elseif($i>1){
                        $str_secC = explode('期',$reportlist3[$j][$arraykeys3[$i]]);
                        //if($tempColC<>$reportlist3[$j][$arraykeys3[$i]] && $reportlist3[$j][$arraykeys3[$i]]<>''){
                        if($tempColC<>$str_secC[0] && $str_secC[0]<>''){
                            if($newline=='true'){
                                $linenum++;
                            }
                            $newline='true';
                        }

                        $objActSheet->setCellValue('A'.($linenum), $reportlist3[$j][$arraykeys3[0]]);
                        //$objActSheet->setCellValue('B'.($linenum), $reportlist3[$j][$arraykeys3[1]]);
                        $objActSheet->setCellValue('B'.($linenum), substr($reportlist3[$j][$arraykeys3[1]],2));
                        if($reportlist3[$j][$arraykeys3[$i]]<>''){
                            $objActSheet->setCellValue($NameFromNumber.($linenum), $reportlist3[$j][$arraykeys3[$i]]);
                            //$tempColC = $reportlist3[$j][$arraykeys3[$i]];
                            $tempColC = $str_secC[0];
                        }

                        if(($j+1)==sizeof($reportlist3) && ($i+1)==sizeof($arraykeys3)){
                            if(($megrenhumB+1)<$linenum){
                                $objActSheet->mergeCells('B'.($megrenhumB+1).':B'.($linenum));
                            }
                            $megrenhumB=$linenum;

                            if(($megrenhumA+1)<$linenum){
                                $objActSheet->mergeCells('A'.($megrenhumA+1).':A'.($linenum));
                                //dd('A'.($megrenhumA+1).':A'.($linenum));
                                //雙框線
                                $styleDoubleBlackBorderOutline = [
                                    'borders' => [
                                            'outline' => [
                                            'borderStyle' => Border::BORDER_DOUBLE,
                                            'color' => ['rgb' => '000000'],
                                        ],
                                    ],
                                ];
                                $objActSheet->getStyle('A'.($megrenhumA+1).':B'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                                $objActSheet->getStyle('C'.($megrenhumA+1).':J'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                                $objActSheet->getStyle('J'.($megrenhumA+1).':J'.($linenum))->applyFromArray($styleDoubleBlackBorderOutline);
                            }
                            $megrenhumA=$linenum;
                        }
                    }
                }
        }

        //外框粗線
        $styleThinBlackBorderOutline = [
                                'borders' => [
                                        'outline' => [
                                        'borderStyle' => Border::BORDER_THICK,
                                        'color' => ['rgb' => '000000'],
                                    ],
                                ],
                            ];
        $objActSheet->getStyle('A2:'.$NameFromNumber.($linenum))->applyFromArray($styleThinBlackBorderOutline);
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"每週確認表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
