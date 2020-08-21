<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Models\T09tb;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Services\User_groupService;

class LectureMoneyDetailController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_money_detail', $user_group_auth)){
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
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
        $termArr=$temp;
        $result = '';
        return view('admin/lecture_money_detail/list',compact('classArr','termArr' ,'result'));
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
        $term=$request->input('term');
        $weekpicker=$request->input('weekpicker');
        $condition=$request->input('condition');
        $branch=$request->input('area');
        $sdate="";
        $edate="";
        $inwclause="";
        $outwclause="";
        $wclause="";

        // Validate date value.
        $tflag="";
        if($weekpicker!=""){
            try {
                $ttemp=explode(" ",$weekpicker);
                $sdatetmp=explode("/",$ttemp[0]);
                $edatetmp=explode("/",$ttemp[2]);
                $sdate=$sdatetmp[0].$sdatetmp[1].$sdatetmp[2];
                $edate=$edatetmp[0].$edatetmp[1].$edatetmp[2];
                $tflag="1";

            } catch (\Exception $e) {
                    $ttemp="error";
            }

            if($ttemp=="error" || $sdate=="NaNundefinedundefined" )
            {
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclass();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = "日期格式錯誤，請重新輸入。";
                return view('admin/lecture_money_detail/list',compact('classArr','termArr' ,'result'));
            }
        }

        if($condition==1){
            if($weekpicker!=""){
                $inwclause=" AND A.class = '".$class."' AND A.term = '".$term."' AND 1 = (CASE WHEN '".$sdate."' = '' THEN 1 WHEN A.date BETWEEN ".$sdate." AND ".$edate." THEN 1 END ) ";
                $outwclause=" AND B.class = '".$class."' AND B.term = '".$term."' AND 1 = (CASE WHEN '".$sdate."' = '' THEN 1 WHEN D.date BETWEEN ".$sdate." AND ".$edate." THEN 1 END ) ";
                $wclause=" A.date BETWEEN ".$sdate." AND ".$edate." AND A.class='".$class."' AND A.term ='".$term."' ";
            }
            else{
                $inwclause=" AND A.class = '".$class."' AND A.term = '".$term."' ";
                $outwclause=" AND B.class = '".$class."' AND B.term = '".$term."'  ";
                $wclause=" A.class='".$class."' AND A.term ='".$term."' ";

            }

        }else{

            if($weekpicker==""){
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclass();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = "請先選擇日期。";
                return view('admin/lecture_money_detail/list',compact('classArr','termArr' ,'result'));
            }

            $inwclause=" AND 1 = (CASE WHEN '".$sdate."' = '' THEN 1 WHEN A.date BETWEEN ".$sdate." AND ".$edate." THEN 1 END ) ";
            $outwclause=" AND A.branch ='".$branch."' AND 1 = (CASE WHEN '".$sdate."' = '' THEN 1 WHEN D.date BETWEEN ".$sdate." AND ".$edate." THEN 1 END ) ";
            $wclause=" A.date BETWEEN ".$sdate." AND ".$edate." AND  C.branch ='".$branch."' ";
        }


            $sql="SELECT
            A.class,
            A.term,
            RTRIM(C.name) class_name,
            '".$sdate."' AS sdate ,
            '".$edate."' AS edate ,
            B.kind as acccode ,                     /* 開支科目代碼 */
            RTRIM(IFNULL(D.accname,'')) AS accname   /* 開支科目名稱 */
            FROM t06tb A /* 【t06tb 課程表資料檔】 */
            INNER JOIN t04tb B /* 【t04tb 開班資料檔】 */
            ON A.class = B.class
            AND A.term = B.term
            INNER JOIN t01tb C /* 【t01tb 班別基本資料檔】 */
            ON A.class = C.class
            LEFT JOIN s06tb D /* 【s06tb 開支科目代碼檔】 */
            ON LEFT(A.class,3) = D.yerly
            AND B.kind = D.acccode
            WHERE  ".$wclause."
            GROUP BY A.class, A.term, B.kind, C.name, D.accname
            ORDER BY (CASE WHEN D.accname = '代收款' THEN 2 ELSE 1 END),A.class,A.term ";

            $temp=DB::select($sql);
            $classdata=json_decode(json_encode($temp), true);

            if($classdata==[])
            {
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclass();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = "查無資料，請重新查詢。";
                return view('admin/lecture_money_detail/list',compact('classArr','termArr' ,'result'));

            }

            // 範本檔案名稱
            $fileName = 'H9';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel

            $objPHPExcel = IOFactory::load($filePath);
            $sheet_id = 0;

            //fill values
            for($i=0; $i<sizeof($classdata); $i++) {
                
                $clonedWorksheet= clone $objPHPExcel->getSheet(0);
                $indexname="";

                if($classdata[$i]["accname"]=='代收款')
                    $indexname='代收款'.$classdata[$i]["class"].$classdata[$i]["term"];
                else
                    $indexname=$classdata[$i]["class"].$classdata[$i]["term"];

                $clonedWorksheet->setTitle($indexname);
                $objPHPExcel->addSheet($clonedWorksheet);
                $sheet_id++;
                $objgetSheet=$objPHPExcel->getSheet($sheet_id);

                $sql="select B.section from t04tb A  inner join m09tb B on A.sponsor=B.userid where A.class='".$class."' and A.term ='".$term."'";
                $temp=DB::select($sql);
                $section=json_decode(json_encode($temp), true);
                $csection="";
                if($section!=[]){
                    $csection=$section[0]["section"];
                }
                $footer="製表　　　　　　　     秘書室 　　　　　　　      ".$csection."　　　　　　　   主計室   　               機關長官";
                $objgetSheet->getHeaderFooter()->setOddFooter( ' &L&"標楷體"'.$footer);

                $Cname="";
                $Ftotal=0;
                $Gtotal=0;
                $Htotal=0;
                $Itotal=0;
                $Jtotal=0;
                $Ktotal=0;
                $Ltotal=0;
                $Mtotal=0;
                $Ntotal=0;
                $styleArray = [
                    'borders' => [
                            'allBorders'=> [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];

                $pagecnt=1;

                $objgetSheet->getStyle('A'.strval($pagecnt).':P'.strval($pagecnt))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $objgetSheet->getStyle('A'.strval($pagecnt).':P'.strval($pagecnt))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $objgetSheet->mergeCells('A'.strval($pagecnt).':P'.strval($pagecnt));
                

                $objgetSheet->getStyle('A'.strval($pagecnt+1).':P'.strval($pagecnt+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $objgetSheet->getStyle('A'.strval($pagecnt+1).':P'.strval($pagecnt+1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $objgetSheet->setCellValue('A'.strval($pagecnt+1),"日期");
                $objgetSheet->setCellValue('B'.strval($pagecnt+1),"課　程");
                $objgetSheet->setCellValue('C'.strval($pagecnt+1),"講　座");
                $objgetSheet->setCellValue('D'.strval($pagecnt+1),"時數");
                $objgetSheet->setCellValue('E'.strval($pagecnt+1),"單價");
                $objgetSheet->setCellValue('F'.strval($pagecnt+1),"鐘點費");
                $objgetSheet->setCellValue('G'.strval($pagecnt+1),"短程\n車資");
                $objgetSheet->setCellValue('H'.strval($pagecnt+1),"國內\n旅費");
                $objgetSheet->setCellValue('I'.strval($pagecnt+1),"演講費");
                $objgetSheet->setCellValue('J'.strval($pagecnt+1),"其他");
                $objgetSheet->setCellValue('K'.strval($pagecnt+1),"總計");
                $objgetSheet->setCellValue('L'.strval($pagecnt+1),"補充\n保險費");
                $objgetSheet->setCellValue('M'.strval($pagecnt+1),"扣繳\n稅額");
                $objgetSheet->setCellValue('N'.strval($pagecnt+1),"實付\n總計");
                $objgetSheet->setCellValue('O'.strval($pagecnt+1),"講座\n身分證字號");
                $objgetSheet->setCellValue('P'.strval($pagecnt+1),"講座地址");
                $objgetSheet->setCellValue('Q'.strval($pagecnt+1),"備註");

                $class=$classdata[$i]["class"];
                $term=$classdata[$i]["term"];

                $outputname="講座費用請領清冊";

               
                if($condition==1){
                    $outputname.="-班期";
                    $inwclause.=" AND A.class = '".$class."' AND A.term = '".$term."' ";
                    $outwclause.=" AND B.class = '".$class."' AND B.term = '".$term."'  ";

                }else{
                    $outputname.="-整週";
                    $inwclause=" AND 1 = (CASE WHEN '".$sdate."' = '' THEN 1 WHEN A.date BETWEEN ".$sdate." AND ".$edate." THEN 1 END ) AND A.class = '".$class."' AND A.term = '".$term."' ";
                    $outwclause="AND 1 = (CASE WHEN '".$sdate."' = '' THEN 1 WHEN D.date BETWEEN ".$sdate." AND ".$edate." THEN 1 END ) AND B.class = '".$class."' AND B.term = '".$term."'  ";

                }

                $sql="select * from
                (
                (
                SELECT
                RTRIM(LTRIM(D.date)) AS 日期,
                RTRIM(D.name) AS 課程,
                RTRIM(F.cname) AS 講座,
                B.lecthr AS 時數,
                (CASE WHEN B.lecthr = 0 THEN 0 ELSE CAST(B.lectamt/B.lecthr AS INT) END ) AS 單價,
                B.lectamt AS 鐘點費,
                B.motoramt AS 短程車資,
                IFNULL(B.planeamt+B.mrtamt+B.trainamt+B.ship+B.otheramt,0) AS 國內旅費,  #=>國內旅費=飛機高鐵planeamt+汽車捷運mrtamt+火車trainamt+船舶ship+住宿費otheramt、
                B.noteamt+B.speakamt+B.review_total AS 演講費, #=>演講費=稿費noteamt+演講費speakamt+評閱費review_total、
                B.other_salary AS 其他, #新增=>其他=其他薪資所得other_salary(新增的欄位，加在演講費與總計之間)
                B.teachtot+B.tratot AS 總計,
                B.insuretot AS 補充保險費,
                B.deductamt AS 扣繳稅額,
                B.totalpay AS 實付總計,
                F.idno AS 講座身分證字號,
                RTRIM(F.regaddress) AS 講座地址,
                B.id,
                ROW_NUMBER() OVER (ORDER BY D.date,F.idno,D.stime,(B.course + B.type)) AS sort_1,
                IFNULL(C.kind, '') AS kind,
                A.name,
                C.term,
                 IFNULL(E.week_name,'') AS week_name ,
                  ROW_NUMBER() OVER (PARTITION BY B.class,B.term,D.date,B.idno ORDER BY D.date,D.stime) AS sort_2
                FROM t01tb A
                INNER JOIN t09tb B
                ON A.class = B.class
                INNER JOIN t04tb C
                ON B.class = C.class
                AND B.term = C.term
                INNER JOIN t06tb D
                ON B.class = D.class
                AND B.term = D.term
                AND B.course = D.course
                LEFT JOIN
                (
                    SELECT DISTINCT
                    X.class,
                    X.term,
                    X.date,
                    CONCAT('(第',CAST(DENSE_RANK() OVER (PARTITION BY X.class,X.term ORDER BY CAST(LEFT(X.date,3) AS int)*100+DATE_FORMAT(CAST(CONCAT(CAST(LEFT(X.date,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(X.date,4,2)*1 as CHAR),'-',RIGHT(X.date,2)*1) AS DATE),'%U')) AS CHAR),'週)') AS week_name
                    FROM t06tb X
                    WHERE EXISTS(
                    SELECT DISTINCT
                    A.class,
                     A.term
                     FROM t06tb A
                    INNER JOIN t04tb B
                     ON A.class=B.class
                    AND A.term=B.term "
                    .$inwclause.
                    " AND B.sdate IS NOT NULL and B.edate IS NOT NULL
                    WHERE A.class = X.class AND A.term = X.term AND X.date<>''
                    )
                ) AS E
                ON D.class=E.class
                AND D.term=E.term
                AND D.date=E.date
                INNER JOIN m01tb F
                ON B.idno = F.idno
                WHERE D.date <> ''
                AND B.totalpay > 0 "
                .$outwclause.
                " ORDER BY D.date,D.stime,(B.course + B.type),F.idno
                )
                UNION
                (
                SELECT
                '小計' AS col_01 ,
                '' AS col_02 ,
                '' AS col_03,
                NULL AS col_04 ,
                NULL AS col_05 ,
                SUM(鐘點費) AS col_06,
                SUM(短程車資) AS col_07,
                SUM(國內旅費) AS col_08,
                SUM(演講費) AS col_09,
                SUM(其他) AS col_16,
                SUM(總計) AS col_10,
                SUM(補充保險費) AS col_11,
                SUM(扣繳稅額) AS col_12,
                SUM(實付總計) AS col_13,
                '' AS col_14,
                '' AS col_15,
                '' AS col_16,
                MAX(sort_1) AS sort_1,
                '' AS kind,
                '' AS class_name,
                '' AS term,
                '' AS week_name,
                MAX(sort_2)+1 AS sort_2
                FROM
                (
                SELECT
                  RTRIM(LTRIM(D.date)) AS 日期,
                  RTRIM(D.name) AS 課程,
                  RTRIM(F.cname) AS 講座,
                  B.lecthr AS 時數,
                  (CASE WHEN B.lecthr = 0 THEN 0 ELSE CAST(B.lectamt/B.lecthr AS INT) END ) AS 單價,
                  B.lectamt AS 鐘點費,
                  B.motoramt AS 短程車資,
                  IFNULL(B.planeamt+B.mrtamt+B.trainamt+B.ship+B.otheramt,0) AS 國內旅費,
                  B.noteamt+B.speakamt+B.review_total AS 演講費,
                  B.other_salary AS 其他,
                  B.teachtot+B.tratot AS 總計,
                  B.insuretot AS 補充保險費,
                  B.deductamt AS 扣繳稅額,
                  B.totalpay AS 實付總計,
                  F.idno AS 講座身分證字號,
                  RTRIM(F.regaddress) AS 講座地址,
                  B.id,
                  ROW_NUMBER() OVER (ORDER BY D.date,F.idno,D.stime,(B.course + B.type)) AS sort_1,
                  IFNULL(C.kind, '') AS kind,
                A.name,
                C.term,
                IFNULL(E.week_name,'') AS week_name ,
                ROW_NUMBER() OVER (PARTITION BY B.class,B.term,D.date,B.idno ORDER BY D.date,D.stime) AS sort_2
                FROM t01tb A
                INNER JOIN t09tb B
                ON A.class = B.class
                INNER JOIN t04tb C
                ON B.class = C.class
                AND B.term = C.term
                INNER JOIN t06tb D
                ON B.class = D.class
                AND B.term = D.term
                AND B.course = D.course
                LEFT JOIN
                (
                    SELECT DISTINCT
                    X.class,
                    X.term,
                    X.date,
                    CONCAT('(第',CAST(DENSE_RANK() OVER (PARTITION BY X.class,X.term ORDER BY CAST(LEFT(X.date,3) AS int)*100+DATE_FORMAT(CAST(CONCAT(CAST(LEFT(X.date,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(X.date,4,2)*1 as CHAR),'-',RIGHT(X.date,2)*1) AS DATE),'%U')) AS CHAR),'週)') AS week_name
                    FROM t06tb X
                    WHERE EXISTS(
                    SELECT DISTINCT
                    A.class,
                    A.term
                    FROM t06tb A
                    INNER JOIN t04tb B
                    ON A.class=B.class
                    AND A.term=B.term "
                    .$inwclause.
                    " AND B.sdate IS NOT NULL and B.edate IS NOT NULL
                    WHERE A.class = X.class AND A.term = X.term AND X.date<>''
                    )
                ) AS E
                ON D.class=E.class
                AND D.term=E.term
                AND D.date=E.date
                INNER JOIN m01tb F
                ON B.idno = F.idno
                WHERE D.date <> ''
                AND B.totalpay > 0 "
                .$outwclause.
                " ORDER BY D.date,D.stime,(B.course + B.type),F.idno
                ) AA
                WHERE EXISTS(
                SELECT
                日期,
                講座身分證字號
                FROM
                (
                    SELECT
                        RTRIM(LTRIM(D.date)) AS 日期,
                        RTRIM(D.name) AS 課程,
                        RTRIM(F.cname) AS 講座,
                        B.lecthr AS 時數,
                        (CASE WHEN B.lecthr = 0 THEN 0 ELSE CAST(B.lectamt/B.lecthr AS INT) END ) AS 單價,
                        B.lectamt AS 鐘點費,
                        B.motoramt AS 短程車資,
                        IFNULL(B.planeamt+B.mrtamt+B.trainamt+B.ship+B.otheramt,0) AS 國內旅費,
                        B.noteamt+B.speakamt+B.review_total AS 演講費,
                        B.other_salary AS 其他,
                        B.teachtot+B.tratot AS 總計,
                        B.insuretot AS 補充保險費,
                        B.deductamt AS 扣繳稅額,
                        B.totalpay AS 實付總計,
                        F.idno AS 講座身分證字號,
                        RTRIM(F.regaddress) AS 講座地址,
                        B.id,
                        ROW_NUMBER() OVER (ORDER BY D.date,F.idno,D.stime,(B.course + B.type)) AS sort_1,
                        IFNULL(C.kind, '') AS kind,
                        A.name,
                        C.term,
                        IFNULL(E.week_name,'') AS week_name ,
                        ROW_NUMBER() OVER (PARTITION BY B.class,B.term,D.date,B.idno ORDER BY D.date,D.stime) AS sort_2
                    FROM t01tb A
                    INNER JOIN t09tb B
                    ON A.class = B.class
                    INNER JOIN t04tb C
                    ON B.class = C.class
                    AND B.term = C.term
                    INNER JOIN t06tb D
                    ON B.class = D.class
                    AND B.term = D.term
                    AND B.course = D.course
                    LEFT JOIN
                    (
                        SELECT DISTINCT
                        X.class,
                        X.term,
                         X.date,
                         CONCAT('(第',CAST(DENSE_RANK() OVER (PARTITION BY X.class,X.term ORDER BY CAST(LEFT(X.date,3) AS int)*100+DATE_FORMAT(CAST(CONCAT(CAST(LEFT(X.date,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(X.date,4,2)*1 as CHAR),'-',RIGHT(X.date,2)*1) AS DATE),'%U')) AS CHAR),'週)') AS week_name
                        FROM t06tb X
                        WHERE EXISTS(
                        SELECT DISTINCT
                        A.class,
                        A.term
                        FROM t06tb A
                        INNER JOIN t04tb B
                        ON A.class=B.class
                        AND A.term=B.term "
                        .$inwclause.
                        " AND B.sdate IS NOT NULL and B.edate IS NOT NULL
                        WHERE A.class = X.class AND A.term = X.term AND X.date<>''
                        )
                    ) AS E
                    ON D.class=E.class
                    AND D.term=E.term
                    AND D.date=E.date
                    INNER JOIN m01tb F
                    ON B.idno = F.idno
                    WHERE D.date <> ''
                    AND B.totalpay > 0 "
                .$outwclause.
                " ) AS F
                GROUP BY 日期, 講座身分證字號
                HAVING COUNT(*)>1 AND 日期 = AA.日期 AND 講座身分證字號 = AA.講座身分證字號
                )
                GROUP BY
                日期,
                講座身分證字號,
                講座
                )
                )AS Z ORDER BY sort_1,sort_2";

                $temp=DB::select($sql);
                if($temp==[]){
                    $objPHPExcel->removeSheetByIndex($sheet_id);
                    if($sheet_id >= 1){
                        $sheet_id-- ;
                    }
                    continue;
                }

            
                $data=json_decode(json_encode($temp), true);
                $datakeys=array_keys((array)$data[0]);


                $sql="select count(w.wn) as cnt from 
                (
                SELECT DISTINCT DENSE_RANK() OVER (PARTITION BY X.class,X.term ORDER BY CAST(LEFT(X.date,3) AS int)*100+DATE_FORMAT(CAST(CONCAT(CAST(LEFT(X.date,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(X.date,4,2)*1 as CHAR),'-',RIGHT(X.date,2)*1) AS DATE),'%U')) AS wn
                    FROM t06tb X
                    WHERE EXISTS(
                        SELECT DISTINCT
                        A.class,
                        A.term
                        FROM t06tb A
                        INNER JOIN t04tb B
                        ON A.class=B.class
                        AND A.term=B.term  AND A.class = '$class' AND A.term = '$term'  AND B.sdate IS NOT NULL and B.edate IS NOT NULL
                        WHERE A.class = X.class AND A.term = X.term AND X.date<>''
                    )
                ) as w";
                $temp=DB::select($sql);
                $weekcnt=json_decode(json_encode($temp), true);

                if(intval($weekcnt[0]['cnt'])>1)
                    $objgetSheet->setCellValue('A'.strval($pagecnt), $classdata[$i]["class_name"].'第'.strval((int)$classdata[$i]["term"]).'期'.$data[0]['week_name'].'講座鐘點費及交通費請領清冊');
                else
                    $objgetSheet->setCellValue('A'.strval($pagecnt), $classdata[$i]["class_name"].'第'.strval((int)$classdata[$i]["term"]).'期講座鐘點費及交通費請領清冊');


                //count totals
                $Cname="";
                $Ftotal=0;
                $Gtotal=0;
                $Htotal=0;
                $Itotal=0;
                $Jtotal=0;
                $Ktotal=0;
                $Ltotal=0;
                $Mtotal=0;
                $Ntotal=0;

                for($j=0;$j<sizeof($data);$j++)
                {
                    $Cname=$data[$j]["講座"];
                    if($j==0){
                        $tmpname =$data[$j]["講座"];
                    }else{
                        if($data[$j]["日期"]=="小計")
                            $Cname=$tmpname;
                        else
                            $tmpname=$data[$j]["講座"];
                    }

                    if($data[$j]["日期"]!="小計"){
                        if($j==0){
                            $Ftotal=$data[$j]["鐘點費"];
                            $Gtotal=$data[$j]["短程車資"];
                            $Htotal=$data[$j]["國內旅費"];
                            $Itotal=$data[$j]["演講費"];
                            $Jtotal=$data[$j]["其他"];
                            $Ktotal=$data[$j]["總計"];
                            $Ltotal=$data[$j]["補充保險費"];
                            $Mtotal=$data[$j]["扣繳稅額"];
                            $Ntotal=$data[$j]["實付總計"];
                        }else{

                            $Ftotal+=$data[$j]["鐘點費"];
                            $Gtotal+=$data[$j]["短程車資"];
                            $Htotal+=$data[$j]["國內旅費"];
                            $Itotal+=$data[$j]["演講費"];
                            $Jtotal+=$data[$j]["其他"];
                            $Ktotal+=$data[$j]["總計"];
                            $Ltotal+=$data[$j]["補充保險費"];
                            $Mtotal+=$data[$j]["扣繳稅額"];
                            $Ntotal+=$data[$j]["實付總計"];
                        }

                    }else{
                        $objgetSheet->getStyle("A".strval($pagecnt+2+($j%15)).":N".strval($pagecnt+2+($j%15)))->getFont()->setBold(true);
                    }

                    for($k=0;$k<17;$k++){
                        if($k==2){
                            $objgetSheet->setCellValue('C'.strval($pagecnt+2+($j%15)),$Cname);
                        }else{
                            if($k == 16){
                                if($data[$j][$datakeys[$k]] != ''){
                                    $t09tb = T09tb::select(DB::raw('IFNULL(planeamt, 0)+IFNULL(mrtamt, 0)+IFNULL(trainamt, 0)+IFNULL(ship, 0) a_total, planestart, plane_d, mrt_o, mrt_d, train_o, train_d, ship_o, ship_d, otheramt'))->where('id', $data[$j][$datakeys[$k]])->get()->toArray();
                                    $remark = '';
                                    if(!empty($t09tb)){
                                        // dd($t09tb);
                                        if($t09tb[0]['a_total'] > 0){
                                            $remark = "交通費".$t09tb[0]['a_total']."元";
                                            if(!empty($t09tb[0]['planestart']) && !empty($t09tb[0]['plane_d'])){
                                                $remark .= "( 飛機高鐵 - ".$t09tb[0]['planestart']." - ".$t09tb[0]['plane_d']." )\n";
                                            }
                                            if(!empty($t09tb[0]['mrt_o']) && !empty($t09tb[0]['mrt_d'])){
                                                $remark .= "( 汽車捷運 - ".$t09tb[0]['mrt_o']." - ".$t09tb[0]['mrt_d']." )\n";
                                            }
                                            if(!empty($t09tb[0]['train_o']) && !empty($t09tb[0]['train_d'])){
                                                $remark .= "( 火車 - ".$t09tb[0]['train_o']." - ".$t09tb[0]['train_d']." )\n";
                                            }
                                            if(!empty($t09tb[0]['ship_o']) && !empty($t09tb[0]['ship_d'])){
                                                $remark .= "( 船舶 - ".$t09tb[0]['ship_o']." - ".$t09tb[0]['ship_d']." )\n";
                                            }
                                        }
                                        // dd($remark);
                                        if($t09tb[0]['otheramt'] > 0){
                                            $remark .= "住宿費".$t09tb[0]['otheramt']."元";
                                        }
                                    }
                                    $objgetSheet->setCellValue($this->getNameFromNumber($k+1).strval($pagecnt+2+($j%15)),$remark);
                                }

                            }else{
                                $objgetSheet->setCellValue($this->getNameFromNumber($k+1).strval($pagecnt+2+($j%15)),$data[$j][$datakeys[$k]]);
                            }
                        }
                    }


                    if((($j+1) % 15)==0 && $j>14) {

                        for($r=$pagecnt;$r<$pagecnt+17;$r++){
                            $objgetSheet->getRowDimension($r)->setRowHeight(29);
                        }
                        $pagecnt+=22;
                        $objgetSheet->getRowDimension($pagecnt+17)->setRowHeight(14);
                        $objgetSheet->getRowDimension($pagecnt+18)->setRowHeight(14);

                        $objgetSheet->getStyle('A'.strval($pagecnt).':P'.strval($pagecnt))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $objgetSheet->getStyle('A'.strval($pagecnt).':P'.strval($pagecnt))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $objgetSheet->getStyle('A'.strval($pagecnt).':P'.strval($pagecnt))->getFont()->setSize(16);
                        $objgetSheet->mergeCells('A'.strval($pagecnt).':P'.strval($pagecnt));
                        $objgetSheet->setCellValue('A'.strval($pagecnt), $classdata[$i]["class_name"].'第'.strval((int)$classdata[$i]["term"]).'期講座鐘點費及交通費請領清冊');

                        $objgetSheet->getStyle('A'.strval($pagecnt+1).':P'.strval($pagecnt+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $objgetSheet->getStyle('A'.strval($pagecnt+1).':P'.strval($pagecnt+1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $objgetSheet->setCellValue('A'.strval($pagecnt+1),"日期");
                        $objgetSheet->setCellValue('B'.strval($pagecnt+1),"課　程");
                        $objgetSheet->setCellValue('C'.strval($pagecnt+1),"講　座");
                        $objgetSheet->setCellValue('D'.strval($pagecnt+1),"時數");
                        $objgetSheet->setCellValue('E'.strval($pagecnt+1),"單價");
                        $objgetSheet->setCellValue('F'.strval($pagecnt+1),"鐘點費");
                        $objgetSheet->setCellValue('G'.strval($pagecnt+1),"短程\n車資");
                        $objgetSheet->setCellValue('H'.strval($pagecnt+1),"國內\n旅費");
                        $objgetSheet->setCellValue('I'.strval($pagecnt+1),"演講費");
                        $objgetSheet->setCellValue('J'.strval($pagecnt+1),"其他");
                        $objgetSheet->setCellValue('K'.strval($pagecnt+1),"總計");
                        $objgetSheet->setCellValue('L'.strval($pagecnt+1),"補充\n保險費");
                        $objgetSheet->setCellValue('M'.strval($pagecnt+1),"扣繳\n稅額");
                        $objgetSheet->setCellValue('N'.strval($pagecnt+1),"實付\n總計");
                        $objgetSheet->setCellValue('O'.strval($pagecnt+1),"講座\n身分證字號");
                        $objgetSheet->setCellValue('P'.strval($pagecnt+1),"講座地址");
                        $objgetSheet->setCellValue('Q'.strval($pagecnt+1),"備註");

                        $objgetSheet->setCellValue('M'.strval($pagecnt+18),"經費科目:");
                        $objgetSheet->getColumnDimension('M')->setWidth(9);
                        $objgetSheet->mergeCells('N'.strval($pagecnt+18).':P'.strval($pagecnt+18));
                        $objgetSheet->getStyle('N'.strval($pagecnt+18).':P'.strval($pagecnt+18))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $objgetSheet->setCellValue('N'.strval($pagecnt+18),$classdata[$i]["accname"]);
                        //apply borders
                        $objgetSheet->getStyle('A'.strval($pagecnt+1).':P'.strval($pagecnt+17))->applyFromArray($styleArray);


                    }

                }

                for($r=$pagecnt;$r<$pagecnt+17;$r++){
                    $objgetSheet->getRowDimension($r)->setRowHeight(29);
                }
                $objgetSheet->getRowDimension($pagecnt+17)->setRowHeight(14);
                $objgetSheet->getRowDimension($pagecnt+18)->setRowHeight(14);

                $objgetSheet->setCellValue('B'.strval($pagecnt+17),"合計");
                $objgetSheet->setCellValue('F'.strval($pagecnt+17),$Ftotal);
                $objgetSheet->setCellValue('G'.strval($pagecnt+17),$Gtotal);
                $objgetSheet->setCellValue('H'.strval($pagecnt+17),$Htotal);
                $objgetSheet->setCellValue('I'.strval($pagecnt+17),$Itotal);
                $objgetSheet->setCellValue('J'.strval($pagecnt+17),$Jtotal);
                $objgetSheet->setCellValue('K'.strval($pagecnt+17),$Ktotal);
                $objgetSheet->setCellValue('L'.strval($pagecnt+17),$Ltotal);
                $objgetSheet->setCellValue('M'.strval($pagecnt+17),$Mtotal);
                $objgetSheet->setCellValue('N'.strval($pagecnt+17),$Ntotal);
                $objgetSheet->getStyle("A".strval($pagecnt+17).":O".strval($pagecnt+17))->getFont()->setBold(true);

                $objgetSheet->setCellValue('M'.strval($pagecnt+18),"經費科目:");
                $objgetSheet->getColumnDimension('M')->setWidth(9);
                $objgetSheet->mergeCells('N'.strval($pagecnt+18).':P'.strval($pagecnt+18));
                $objgetSheet->getStyle('N'.strval($pagecnt+18).':P'.strval($pagecnt+18))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $objgetSheet->setCellValue('N'.strval($pagecnt+18),$classdata[$i]["accname"]);
                //apply borders
                $objgetSheet->getStyle('A'.strval($pagecnt+1).':P'.strval($pagecnt+17))->applyFromArray($styleArray);

            }
            $objPHPExcel->removeSheetByIndex(0);


            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),$outputname);
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 

    }

}
