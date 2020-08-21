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

class MonthlyMoneyController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('monthly_money', $user_group_auth)){
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
        return view('admin/monthly_money/list');
    }

    /*
    各月份費用統計表 CSDIR6180
    參考Tables:
    使用範本:N14.xlsx
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

        //費用統計明細
        $sql="  SELECT  T.name, T.daterange,
                        SUM(T.sincnt), SUM(T.dodt) ,  SUM(T.admcnt), SUM(T.tot1),    SUM(T.meacnt), SUM(T.luncnt),  SUM(T.dincnt),
                        SUM(T.tabtot), SUM(T.buftot), SUM(T.teatot), SUM(T.othertot), SUM(T.tot2),  SUM(T.placetot), SUM(T.total) , T.accname
                FROM (
                        SELECT A.name, A.daterange,
                                    B.sincnt, B.dodt ,B.admcnt, B.tot1, B.meacnt, B.luncnt, B.dincnt,
                                    B.tabtot, B.buftot, B.teatot, B.othertot, B.tot2, B.placetot, B.total , A.accname,
                                    A.kind, A.class,A.term
                        FROM (
                                SELECT M.name, M.daterange, M.accname, M.kind, M.class,M.term
                                    FROM (
                                            SELECT
                                                        X.kind,
                                                        Z.class,
                                                        Z.term,
                                                        CONCAT(RTRIM(Z.name),'第',X.term,'期') as name,
                                                        CONCAT(SUBSTRING(X.sdate,4,2),'/',SUBSTRING(X.sdate,6,2),'-',
                                                               SUBSTRING(X.edate,4,2),'/',SUBSTRING(X.sdate,6,2)) AS daterange,
                                                        IFNULL(V.accname,'') as accname
                                            FROM (
                                                            SELECT IFNULL(B.name,'') AS name,A.class,A.term
                                                            FROM t23tb A inner join t01tb B  on A.class=B.class
                                                            WHERE A.type='5' AND LEFT(A.date,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                                            GROUP BY B.name,A.class,A.term
                                            ) Z
                                            INNER JOIN t04tb X ON Z.class=X.class AND Z.term=X.term
                                                LEFT JOIN s06tb V ON X.kind=V.acccode AND V.yerly= LPAD('".$startYear."',3,'0')
                                            UNION ALL
                                            SELECT
                                                            B.kind,
                                                            A.class,A.term ,
                                                            CONCAT(RTRIM(B.name),'第',B.serno,'期') as name,
                                                            CONCAT(SUBSTRING(B.sdate,4,2),'/',SUBSTRING(B.sdate,6,2),'-',
                                                                   SUBSTRING(B.edate,4,2),'/',SUBSTRING(B.edate,6,2)) AS daterange,
                                                            IFNULL(C.accname,'') as accname
                                            FROM (
                                                        SELECT class,term
                                                        FROM t23tb
                                                        WHERE type='5' AND LEFT(date,5)= CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                                        AND left(class,1)='0'
                                                        GROUP BY class,term
                                                        ) A INNER JOIN t38tb B ON A.class=B.meet AND A.term=B.serno
                                                                INNER JOIN s06tb C ON B.kind=C.acccode AND C.yerly= LPAD('".$startYear."',3,'0')
                                            ) M
                                ORDER BY M.kind,M.class,M.term
                                ) A INNER JOIN

                                (
                                SELECT
                                            Z.class,
                                            Z.term,
                                            SUM(Z.sincnt) AS sincnt,
                                            SUM(Z.sintot) AS sintot,
                                            SUM(Z.dodt) AS dodt,
                                            SUM(Z.dodttot) AS dodttot,
                                            SUM(Z.admcnt) AS  admcnt,
                                            SUM(Z.admtot) AS admtot,
                                            (SUM(Z.sintot)+SUM(Z.dodttot)+SUM(Z.admtot)) AS tot1,
                                            SUM(Z.meacnt) AS meacnt,
                                            SUM(Z.meatot) AS meatot,
                                            SUM(Z.luncnt) AS luncnt,
                                            SUM(Z.luntot) AS luntot,
                                            SUM(Z.dincnt) AS dincnt,
                                            SUM(Z.dintot) AS dintot,
                                            SUM(Z.tabtot) AS tabtot,
                                            SUM(Z.buftot) AS buftot,
                                            SUM(Z.teatot) AS teatot,
                                            SUM(Z.othertot) AS othertot,
                                            (SUM(Z.meatot)+SUM(Z.luntot)+SUM(Z.dintot)
                                            +SUM(Z.tabtot)+SUM(Z.buftot)+SUM(Z.teatot)+SUM(Z.othertot)) AS tot2,
                                            SUM(Z.placetot) AS placetot,
                                            (SUM(Z.sintot)+SUM(Z.dodttot)+SUM(Z.admtot)
                                            +SUM(Z.meatot)+SUM(Z.luntot)+SUM(Z.dintot)
                                            +SUM(Z.tabtot)+SUM(Z.buftot)+SUM(Z.teatot)+SUM(Z.othertot)) AS total
                                FROM (
                                            SELECT
                                                            class,
                                                            term,
                                                            date,
                                                            sincnt  ,
                                                            sincnt*sinunit AS sintot,
                                                            donecnt+dtwocnt AS dodt,
                                                            (donecnt*doneunit)+(dtwocnt*dtwounit) AS dodttot,
                                                            meacnt  ,
                                                            meacnt*meaunit AS meatot,
                                                            luncnt  ,
                                                            luncnt*lununit AS luntot ,
                                                            dincnt  ,
                                                            dincnt*dinunit AS dintot ,
                                                            tabcnt*tabunit AS tabtot ,
                                                            bufcnt*bufunit AS buftot ,
                                                            teacnt*teaunit AS teatot,
                                                            otheramt AS othertot ,
                                                            siteamt AS placetot ,
                                                            0 AS admcnt,
                                                            0 AS admtot
                                                FROM t23tb
                                            WHERE type='5' AND LEFT(date,5)= CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                            UNION ALL
                                            SELECT class,term, date,
                                                            0 AS sincnt  ,
                                                            0 AS sintot,
                                                            0 AS dodt,
                                                            0 AS dodttot,
                                                            0 AS meacnt  ,
                                                            0 AS meatot,
                                                            0 AS luncnt  ,
                                                            0 AS luntot ,
                                                            0 AS dincnt  ,
                                                            0 AS dintot ,
                                                            0 AS tabtot ,
                                                            0 AS buftot ,
                                                            0 AS teatot,
                                                            0 AS othertot ,
                                                            0 AS placetot ,
                                                            IFNULL(COUNT(*),0) as admcnt,
                                                            IFNULL(SUM(fee),0) as admtot
                                                FROM t22tb
                                                where (site='V01' or site='V02' )
                                                    AND LEFT(date,5)= CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                                group by class,term, date
                                            ) Z
                                GROUP BY Z.class,Z.term
                            ) B ON A.class=B.class AND A.term=B.term
                UNION ALL
                SELECT '總計' AS name,NULL AS daterange,
                            SUM(B.sincnt) AS sincnt,
                            SUM(B.dodt) AS  dodt,
                            SUM(B.admcnt) AS admcnt,
                            SUM(B.tot1) AS tot1,
                            SUM(B.meacnt) AS meacnt,
                            SUM(B.luncnt) AS luncnt,
                            SUM(B.dincnt) AS dincnt,
                            SUM(B.tabtot) AS tabtot,
                            SUM(B.buftot) AS buftot,
                            SUM(B.teatot) AS teatot,
                            SUM(B.othertot) AS othertot,
                            SUM(B.tot2) AS tot2,
                            SUM(B.placetot) AS placetot,
                            SUM(B.total) AS total,
                            NULL AS accname,
                            'ZZZ' AS kind, B.class,B.term
                FROM (
                                SELECT
                                            Z.class,
                                            Z.term,
                                            SUM(Z.sincnt) AS sincnt,
                                            SUM(Z.sintot) AS sintot,
                                            SUM(Z.dodt) AS dodt,
                                            SUM(Z.dodttot) AS dodttot,
                                            SUM(Z.admcnt) AS  admcnt,
                                            SUM(Z.admtot) AS admtot,
                                            (SUM(Z.sintot)+SUM(Z.dodttot)+SUM(Z.admtot)) AS tot1,
                                            SUM(Z.meacnt) AS meacnt,
                                            SUM(Z.meatot) AS meatot,
                                            SUM(Z.luncnt) AS luncnt,
                                            SUM(Z.luntot) AS luntot,
                                            SUM(Z.dincnt) AS dincnt,
                                            SUM(Z.dintot) AS dintot,
                                            SUM(Z.tabtot) AS tabtot,
                                            SUM(Z.buftot) AS buftot,
                                            SUM(Z.teatot) AS teatot,
                                            SUM(Z.othertot) AS othertot,
                                            (SUM(Z.meatot)+SUM(Z.luntot)+SUM(Z.dintot)
                                            +SUM(Z.tabtot)+SUM(Z.buftot)+SUM(Z.teatot)+SUM(Z.othertot)) AS tot2,
                                            SUM(Z.placetot) AS placetot,
                                            (SUM(Z.sintot)+SUM(Z.dodttot)+SUM(Z.admtot)
                                            +SUM(Z.meatot)+SUM(Z.luntot)+SUM(Z.dintot)
                                            +SUM(Z.tabtot)+SUM(Z.buftot)+SUM(Z.teatot)+SUM(Z.othertot)) AS total
                                FROM (
                                            SELECT
                                                            class,
                                                            term,
                                                            date,
                                                            sincnt  ,
                                                            sincnt*sinunit AS sintot,
                                                            donecnt+dtwocnt AS dodt,
                                                            (donecnt*doneunit)+(dtwocnt*dtwounit) AS dodttot,
                                                            meacnt  ,
                                                            meacnt*meaunit AS meatot,
                                                            luncnt  ,
                                                            luncnt*lununit AS luntot ,
                                                            dincnt  ,
                                                            dincnt*dinunit AS dintot ,
                                                            tabcnt*tabunit AS tabtot ,
                                                            bufcnt*bufunit AS buftot ,
                                                            teacnt*teaunit AS teatot,
                                                            otheramt AS othertot ,
                                                            siteamt AS placetot ,
                                                            0 AS admcnt,
                                                            0 AS admtot
                                            FROM t23tb
                                            WHERE type='5' AND LEFT(date,5)= CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                            UNION ALL
                                            SELECT class,term, date,
                                                            0 AS sincnt  ,
                                                            0 AS sintot,
                                                            0 AS dodt,
                                                            0 AS dodttot,
                                                            0 AS meacnt  ,
                                                            0 AS meatot,
                                                            0 AS luncnt  ,
                                                            0 AS luntot ,
                                                            0 AS dincnt  ,
                                                            0 AS dintot ,
                                                            0 AS tabtot ,
                                                            0 AS buftot ,
                                                            0 AS teatot,
                                                            0 AS othertot ,
                                                            0 AS placetot ,
                                                            IFNULL(COUNT(*),0) as admcnt,
                                                            IFNULL(SUM(fee),0) as admtot
                                                FROM t22tb
                                                where (site='V01' or site='V02' )
                                                    AND LEFT(date,5)=CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                                group by class,term, date
                                            ) Z
                                GROUP BY Z.class,Z.term
                                ) B
                        GROUP BY B.class,B.term
                                    ) T
                GROUP BY T.name, T.daterange, T.accname
                ORDER BY T.kind, T.class,T.term			 ";






        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        //科目彙總統計
        $sql2="SELECT A.accname, SUM(B.total) AS total
                FROM (
                            SELECT M.name, M.daterange, M.accname, M.kind, M.class,M.term
                                FROM (
                                        SELECT
                                                    X.kind,
                                                    Z.class,
                                                    Z.term,
                                                    CONCAT(RTRIM(Z.name),'第',X.term,'期') as name,
                                                    CONCAT(SUBSTRING(X.sdate,4,2),'/',SUBSTRING(X.sdate,6,2),'-',
                                                                    SUBSTRING(X.edate,4,2),'/',SUBSTRING(X.sdate,6,2)) AS daterange,
                                                    IFNULL(V.accname,'') as accname
                                        FROM (
                                                        SELECT IFNULL(B.name,'') AS name,A.class,A.term
                                                        FROM t23tb A inner join t01tb B  on A.class=B.class
                                                        WHERE A.type='5' AND LEFT(A.date,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                                        GROUP BY B.name,A.class,A.term
                                        ) Z
                                        INNER JOIN t04tb X ON Z.class=X.class AND Z.term=X.term
                                            LEFT JOIN s06tb V ON X.kind=V.acccode AND V.yerly= LPAD('".$startYear."',3,'0')
                                        UNION ALL
                                        SELECT
                                                        B.kind,
                                                        A.class,A.term ,
                                                        CONCAT(RTRIM(B.name),'第',B.serno,'期') as name,
                                                        CONCAT(SUBSTRING(B.sdate,4,2),'/',SUBSTRING(B.sdate,6,2),'-',
                                                                        SUBSTRING(B.edate,4,2),'/',SUBSTRING(B.edate,6,2)) AS daterange,
                                                        IFNULL(C.accname,'') as accname
                                        FROM (
                                                    SELECT class,term
                                                        FROM t23tb
                                                        WHERE type='5' AND LEFT(date,5)= CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                                            AND left(class,1)='0'
                                                        GROUP BY class,term
                                                    ) A INNER JOIN t38tb B ON A.class=B.meet AND A.term=B.serno
                                                            INNER JOIN s06tb C ON B.kind=C.acccode AND C.yerly= LPAD('".$startYear."',3,'0')
                                        ) M
                            ORDER BY M.kind,M.class,M.term
                            ) A INNER JOIN

                            (
                            SELECT
                                        Z.class,
                                        Z.term,
                                        SUM(Z.sincnt) AS sincnt,
                                        SUM(Z.sintot) AS sintot,
                                        SUM(Z.dodt) AS dodt,
                                        SUM(Z.dodttot) AS dodttot,
                                        SUM(Z.admcnt) AS  admcnt,
                                        SUM(Z.admtot) AS admtot,
                                        (SUM(Z.sintot)+SUM(Z.dodttot)+SUM(Z.admtot)) AS tot1,
                                        SUM(Z.meacnt) AS meacnt,
                                        SUM(Z.meatot) AS meatot,
                                        SUM(Z.luncnt) AS luncnt,
                                        SUM(Z.luntot) AS luntot,
                                        SUM(Z.dincnt) AS dincnt,
                                        SUM(Z.dintot) AS dintot,
                                        SUM(Z.tabtot) AS tabtot,
                                        SUM(Z.buftot) AS buftot,
                                        SUM(Z.teatot) AS teatot,
                                        SUM(Z.othertot) AS othertot,
                                        (SUM(Z.meatot)+SUM(Z.luntot)+SUM(Z.dintot)
                                        +SUM(Z.tabtot)+SUM(Z.buftot)+SUM(Z.teatot)+SUM(Z.othertot)) AS tot2,
                                        SUM(Z.placetot) AS placetot,
                                        (SUM(Z.sintot)+SUM(Z.dodttot)+SUM(Z.admtot)
                                        +SUM(Z.meatot)+SUM(Z.luntot)+SUM(Z.dintot)
                                        +SUM(Z.tabtot)+SUM(Z.buftot)+SUM(Z.teatot)+SUM(Z.othertot)) AS total
                            FROM (
                                        SELECT
                                                        class,
                                                        term,
                                                        date,
                                                        sincnt  ,
                                                        sincnt*sinunit AS sintot,
                                                        donecnt+dtwocnt AS dodt,
                                                        (donecnt*doneunit)+(dtwocnt*dtwounit) AS dodttot,
                                                        meacnt  ,
                                                        meacnt*meaunit AS meatot,
                                                        luncnt  ,
                                                        luncnt*lununit AS luntot ,
                                                        dincnt  ,
                                                        dincnt*dinunit AS dintot ,
                                                        tabcnt*tabunit AS tabtot ,
                                                        bufcnt*bufunit AS buftot ,
                                                        teacnt*teaunit AS teatot,
                                                        otheramt AS othertot ,
                                                        siteamt AS placetot ,
                                                        0 AS admcnt,
                                                        0 AS admtot
                                            FROM t23tb
                                        WHERE type='5' AND LEFT(date,5)= CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                        UNION ALL
                                        SELECT class,term, date,
                                                        0 AS sincnt  ,
                                                        0 AS sintot,
                                                        0 AS dodt,
                                                        0 AS dodttot,
                                                        0 AS meacnt  ,
                                                        0 AS meatot,
                                                        0 AS luncnt  ,
                                                        0 AS luntot ,
                                                        0 AS dincnt  ,
                                                        0 AS dintot ,
                                                        0 AS tabtot ,
                                                        0 AS buftot ,
                                                        0 AS teatot,
                                                        0 AS othertot ,
                                                        0 AS placetot ,
                                                        IFNULL(COUNT(*),0) as admcnt,
                                                        IFNULL(SUM(fee),0) as admtot
                                            FROM t22tb
                                            where (site='V01' or site='V02' )
                                                AND LEFT(date,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                            group by class,term, date
                                        ) Z
                            GROUP BY Z.class,Z.term
                            ) B ON A.class=B.class AND A.term=B.term
            GROUP BY A.accname
            ORDER BY 1 ";
        $reportlist2 = DB::select($sql2);
        //取出全部項目
        if(sizeof($reportlist2) != 0) {
            $arraykeys2=array_keys((array)$reportlist2[0]);
        }

        // 檔案名稱
        $fileName = 'N14';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objPHPExcel->setActiveSheetIndex(0);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&18 '.substr($startYear,0,3).'年'.$startMonth.'月行政院人事行政總處公務人力發展學院費用統計 &R&14第&P頁');

        $reportlist = json_decode(json_encode($reportlist), true);
        $reportlist2 = json_decode(json_encode($reportlist2), true);

        //費用統計明細
        if(sizeof($reportlist) != 0) {
            $linenum=1;
            $k=0;
            $jumpnum=0;

            //資料by班別迴圈
            for ($j=0; $j < sizeof($reportlist); $j++) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys); $i++) {
                    //excel 欄位 1 == A, etc
                    $NameFromNumber=$this->getNameFromNumber($i+1); //A
                    //4開始
                    if($reportlist[$j][$arraykeys[$i]]==0 && ($i+1) > 2 && ($i+1) < 17 && sizeof($reportlist) > $j+1){
                        //數值欄位為0顯示空白
                        $objActSheet->setCellValue($NameFromNumber.($j+4+$k), '');
                    }else{
                        $objActSheet->setCellValue($NameFromNumber.($j+4+$k), $reportlist[$j][$arraykeys[$i]]);
                    }
                    //高 25
                    $objActSheet->getRowDimension($j+4)->setRowHeight(25);
                }

                //'第1頁資料範圍 1-20資料列(20筆)  21總計列(1-20)
                //'第2頁資料範圍 2-20資料列(19筆)  1(上一頁總計)21(總計列1-20)
                //'......
                //'最後頁, 最後項增加總計
                if($linenum==20){
                    $objActSheet->setCellValue('A'.($j+4+$k+1), '接下頁');
                    $objActSheet->setCellValue('A'.($j+4+$k+2), '接上頁');
                    //sum of columns C~P =SUM(C4:C23)
                    for($t=3;$t<17;$t++)
                    {
                        $NameFromNumber=$this->getNameFromNumber($t); //A
                        $objActSheet->setCellValue($NameFromNumber.($j+4+$k+1),
                                '=SUM('.$this->getNameFromNumber($t).'4:'.$this->getNameFromNumber($t).($j+4+$k).')');
                        $objActSheet->setCellValue($NameFromNumber.($j+4+$k+2),
                                '=SUM('.$this->getNameFromNumber($t).'4:'.$this->getNameFromNumber($t).($j+4+$k).')');
                        //高 25
                        $objActSheet->getRowDimension($j+4+$k+1)->setRowHeight(25);
                        $objActSheet->getRowDimension($j+4+$k+2)->setRowHeight(25);
                    }
                    $k=$k+2;
                    $jumpnum=20+19;
                }
                if($linenum>20 && $linenum==$jumpnum){
                    $objActSheet->setCellValue('A'.($j+4+$k+1), '接下頁');
                    $objActSheet->setCellValue('A'.($j+4+$k+2), '接上頁');
                    //sum of columns C~P =SUM(CXX:CXX) , XX:19
                    for($t=3;$t<17;$t++)
                    {
                        $NameFromNumber=$this->getNameFromNumber($t); //A
                        $objActSheet->setCellValue($NameFromNumber.($j+4+$k+1),
                                '=SUM('.$this->getNameFromNumber($t).($j+4+$k-19).':'.$this->getNameFromNumber($t).($j+4+$k).')');
                        $objActSheet->setCellValue($NameFromNumber.($j+4+$k+2),
                                '=SUM('.$this->getNameFromNumber($t).($j+4+$k-19).':'.$this->getNameFromNumber($t).($j+4+$k).')');
                        //
                        $objActSheet->getRowDimension($j+4+$k+1)->setRowHeight(25);
                        $objActSheet->getRowDimension($j+4+$k+2)->setRowHeight(25);
                    }
                    $k=$k+2;
                    $jumpnum=$jumpnum+19;
                }
                if($linenum>1 && $linenum==$j+1){
                    //$objActSheet->setCellValue('A'.($j+4+$k+1), '總計');
                    //高 25
                    $objActSheet->getRowDimension($j+4+$k+1)->setRowHeight(25);
                }
                $linenum++;

                $styleArray = [
                    'borders' => [
                //只有外框           'outline' => [
                            'allBorders'=> [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];

                $objActSheet->getStyle('A4:'.$NameFromNumber.($j+4+$k))->applyFromArray($styleArray);
            }
        }

        //科目彙總統計
        $objPHPExcel->setActiveSheetIndex(1);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&14 '.substr($startYear,0,3).'年'.$startMonth.'月行政院人事行政總處公務人力發展學院費用統計');

        if(sizeof($reportlist2) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys2); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //A
                $k=0;
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist2); $j++) {
                    //1開始
                    $objActSheet->setCellValue($NameFromNumber.($j*$k+1), $reportlist2[$j][$arraykeys2[$i]]);

                    $objActSheet->setCellValue('C'.($j*$k+1+0), '一般事務費(宿費)OA');
                    $objActSheet->setCellValue('C'.($j*$k+1+1), '一般事務費(膳費)OA');
                    $objActSheet->setCellValue('C'.($j*$k+1+2), '一般事務費(膳費)');
                    $objActSheet->setCellValue('C'.($j*$k+1+3), '一般事務費(其他)');
                    $objActSheet->getRowDimension($j*$k+1+0)->setRowHeight(25);
                    $objActSheet->getRowDimension($j*$k+1+1)->setRowHeight(25);
                    $objActSheet->getRowDimension($j*$k+1+2)->setRowHeight(25);
                    $objActSheet->getRowDimension($j*$k+1+3)->setRowHeight(25);
                    $objActSheet->mergeCells('A'.($j*$k+1+0).':A'.($j*$k+1+3));
                    $objActSheet->mergeCells('B'.($j*$k+1+0).':B'.($j*$k+1+3));
                    $k=4;
                }
            }

            $styleArray = [
                'borders' => [
            //只有外框           'outline' => [
                        'allBorders'=> [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $objActSheet->getStyle('A1:D'.($j*$k))->applyFromArray($styleArray);

            $objActSheet->setCellValue('A'.($j*$k+1), '製表：');
            $objActSheet->mergeCells('A'.($j*$k+1).':B'.($j*$k+1));
            $objActSheet->setCellValue('C'.($j*$k+1), '單位主管：');
            $objActSheet->mergeCells('C'.($j*$k+1).':D'.($j*$k+1));
            $objActSheet->getRowDimension($j*$k+1)->setRowHeight(25);
            

        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"各月份費用統計表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename

    }

}
