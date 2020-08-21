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

class CountOnjobTrainController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('count_onjob_train', $user_group_auth)){
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

        return view('admin/count_onjob_train/list',compact('result','class'));
    }

    /*
    在職訓練人數 CSDIR4140
    參考Tables:
    使用範本:J17A.xlsx, J17B.xlsx , J17C.xlsx (A:訓練班別, B:游於藝講堂, C: 學歷性別及年齡性別)
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //起始年
        $syear = $request->input('syear');
        //起始月
        $smonth = str_pad($request->input('smonth'),2,'0',STR_PAD_LEFT);
        //結束年
        $eyear = $request->input('eyear');
        //結束月
        $emonth = str_pad($request->input('emonth'),2,'0',STR_PAD_LEFT);
        //1:訓練班別, 2:游於藝講堂, 3: 學歷性別及年齡性別
        $outputtype = $request->input('outputtype');
        //0:請選擇訓練性質, 1 中高階公務人員訓練, 2 人事人員專業訓練, 3 一般公務人員訓練
        $traintype = $request->input('training');
        //0:請選擇班別性質
        $classtype = $request->input('classtype');
        //1:依班期個別統計, 2:依班號合併統計
        $statics = $request->input('statics');

        if($outputtype=='1'){
            $sqlPara='';
            if($classtype!='0' && $classtype<>''){
                // AND E.type=''
                $sqlPara=$sqlPara." AND E.type='".$classtype."' ";
            }else{
                //AND E.type<>'13'
                $sqlPara=$sqlPara." AND E.type<>'13' ";
            }
            if($traintype!='0' && $traintype<>''){
                //AND '1' = (CASE WHEN '1'='0' THEN '' ELSE E.traintype END )
                $sqlPara=$sqlPara." AND '".$traintype."' = (CASE WHEN '".$traintype."'='0' THEN '' ELSE E.traintype END )";
            }
            if($statics=='1'){
                $sqlPara=$sqlPara." GROUP BY A.class, A.term, E.name,E.type
                                    ORDER BY classname_term";
                //1:依班期個別統計, SELECT CONCAT(RTRIM(E.name),'第',A.term,'期')  AS  classname_term,
                $sql1="SELECT CONCAT(RTRIM(E.name),'第',A.term,'期')  AS  classname_term,
                                COUNT(C.type) AS type_total,
                                COUNT(CASE C.type WHEN '1' THEN A.idno ELSE NULL END) AS type_1,
                                COUNT(CASE C.type WHEN '2' THEN A.idno ELSE NULL END) AS type_2,
                                COUNT(CASE C.type WHEN '3' THEN A.idno ELSE NULL END) AS type_3,
                                COUNT(CASE C.type WHEN '4' THEN A.idno ELSE NULL END) AS type_4,
                                COUNT(CASE C.type WHEN '5' THEN A.idno ELSE NULL END) AS type_5,
                                CAST(SUM(A.age) AS float)/COUNT( CASE WHEN A.age>=0 THEN A.idno ELSE NULL END) AS age_average,
                                COUNT(CASE WHEN A.age<=24 THEN A.idno ELSE NULL END) AS age_24,
                                COUNT(CASE WHEN ( A.age>=25 AND A.age<=29 ) THEN A.idno ELSE NULL END) AS age_25_29,
                                COUNT(CASE WHEN ( A.age>=30 AND A.age<=34 ) THEN A.idno ELSE NULL END) AS age_30_34,
                                COUNT(CASE WHEN ( A.age>=35 AND A.age<=39 ) THEN A.idno ELSE NULL END) AS age_35_39,
                                COUNT(CASE WHEN ( A.age>=40 AND A.age<=44 ) THEN A.idno ELSE NULL END) AS age_40_44,
                                COUNT(CASE WHEN ( A.age>=45 AND A.age<=49 ) THEN A.idno ELSE NULL END) AS age_45_49,
                                COUNT(CASE WHEN ( A.age>=50 ) THEN A.idno ELSE NULL END) AS age_50,
                                COUNT(CASE B.sex WHEN 'M' THEN A.idno ELSE NULL END) AS sex_M,
                                COUNT(CASE B.sex WHEN 'F' THEN A.idno ELSE NULL END) AS sex_F,
                                COUNT(CASE WHEN A.ecode='1' THEN 1 ELSE NULL END) AS ecode_1,
                                COUNT(CASE WHEN A.ecode='2' THEN 1 ELSE NULL END) AS ecode_2,
                                COUNT(CASE WHEN A.ecode='3' THEN 1 ELSE NULL END) AS ecode_3,
                                COUNT(CASE WHEN A.ecode='4' THEN 1 ELSE NULL END) AS ecode_4,
                                COUNT(CASE WHEN A.ecode='5' THEN 1 ELSE NULL END) AS ecode_5,
                                COUNT(CASE WHEN A.ecode='6' THEN 1 ELSE NULL END) AS ecode_6,
                                COUNT(CASE WHEN A.ecode='7' THEN 1 ELSE NULL END) AS ecode_7,
                                E.type
                        FROM t13tb A LEFT JOIN m02tb B ON A.idno = B.idno
                                        LEFT JOIN m13tb C ON A.organ = C.organ
                                        LEFT JOIN t04tb D ON A.class = D.class AND A.term=D.term
                                        LEFT JOIN t01tb E ON A.class = E.class
                        WHERE A.status='1'
                        AND (CASE WHEN E.classified='3' THEN
                                        SUBSTRING((STR_TO_DATE(DATE_ADD(DATE_FORMAT(D.edate+'19110000','%Y%m%d'),INTERVAL 14 DAY),'%Y-%m-%d')-'19110000'),1,5)
                                    ELSE SUBSTRING(D.edate,1,5)
                                    END ) BETWEEN '".$syear.$smonth."' AND '".$eyear.$emonth."'
                        ".$sqlPara;
                        //dd($sql1);
            }else{
                //2:依班號合併統計, SELECT A.class AS classname_term,
                $sqlPara=$sqlPara." GROUP BY A.class,E.type
                                    ORDER BY classname_term ";
                $sql1="SELECT A.class  AS  classname_term,
                                COUNT(C.type) AS type_total,
                                COUNT(CASE C.type WHEN '1' THEN A.idno ELSE NULL END) AS type_1,
                                COUNT(CASE C.type WHEN '2' THEN A.idno ELSE NULL END) AS type_2,
                                COUNT(CASE C.type WHEN '3' THEN A.idno ELSE NULL END) AS type_3,
                                COUNT(CASE C.type WHEN '4' THEN A.idno ELSE NULL END) AS type_4,
                                COUNT(CASE C.type WHEN '5' THEN A.idno ELSE NULL END) AS type_5,
                                CAST(SUM(A.age) AS float)/COUNT( CASE WHEN A.age>=0 THEN A.idno ELSE NULL END) AS age_average,
                                COUNT(CASE WHEN A.age<=24 THEN A.idno ELSE NULL END) AS age_24,
                                COUNT(CASE WHEN ( A.age>=25 AND A.age<=29 ) THEN A.idno ELSE NULL END) AS age_25_29,
                                COUNT(CASE WHEN ( A.age>=30 AND A.age<=34 ) THEN A.idno ELSE NULL END) AS age_30_34,
                                COUNT(CASE WHEN ( A.age>=35 AND A.age<=39 ) THEN A.idno ELSE NULL END) AS age_35_39,
                                COUNT(CASE WHEN ( A.age>=40 AND A.age<=44 ) THEN A.idno ELSE NULL END) AS age_40_44,
                                COUNT(CASE WHEN ( A.age>=45 AND A.age<=49 ) THEN A.idno ELSE NULL END) AS age_45_49,
                                COUNT(CASE WHEN ( A.age>=50 ) THEN A.idno ELSE NULL END) AS age_50,
                                COUNT(CASE B.sex WHEN 'M' THEN A.idno ELSE NULL END) AS sex_M,
                                COUNT(CASE B.sex WHEN 'F' THEN A.idno ELSE NULL END) AS sex_F,
                                COUNT(CASE WHEN A.ecode='1' THEN 1 ELSE NULL END) AS ecode_1,
                                COUNT(CASE WHEN A.ecode='2' THEN 1 ELSE NULL END) AS ecode_2,
                                COUNT(CASE WHEN A.ecode='3' THEN 1 ELSE NULL END) AS ecode_3,
                                COUNT(CASE WHEN A.ecode='4' THEN 1 ELSE NULL END) AS ecode_4,
                                COUNT(CASE WHEN A.ecode='5' THEN 1 ELSE NULL END) AS ecode_5,
                                COUNT(CASE WHEN A.ecode='6' THEN 1 ELSE NULL END) AS ecode_6,
                                COUNT(CASE WHEN A.ecode='7' THEN 1 ELSE NULL END) AS ecode_7,
                                E.type
                        FROM t13tb A LEFT JOIN m02tb B ON A.idno = B.idno
                                        LEFT JOIN m13tb C ON A.organ = C.organ
                                        LEFT JOIN t04tb D ON A.class = D.class AND A.term=D.term
                                        LEFT JOIN t01tb E ON A.class = E.class
                        WHERE A.status='1'
                        AND (CASE WHEN E.classified='3' THEN
                                     SUBSTRING((STR_TO_DATE(DATE_ADD(DATE_FORMAT(D.edate+'19110000','%Y%m%d'),INTERVAL 14 DAY),'%Y-%m-%d')-'19110000'),1,5)
                                 ELSE SUBSTRING(D.edate,1,5)
                                 END ) BETWEEN '".$syear.$smonth."' AND '".$eyear.$emonth."'
                        ".$sqlPara;
            }
            $reportlist1 = DB::select($sql1);
            $dataArr1=json_decode(json_encode(DB::select($sql1)), true);
            //取出全部項目
            if(sizeof($reportlist1) != 0) {
                $arraykeys1=array_keys((array)$reportlist1[0]);
            }
        }elseif($outputtype=='2'){

            if($statics=='1'){
                //1:依班期個別統計, SELECT CONCAT(RTRIM(E.name),'第',A.term,'期')  AS  classname_term,
                $sql2="SELECT CONCAT(RTRIM(E.name),'第',A.term,'期')  AS  classname_term,
                                COUNT(A.race) AS race_total,
                                COUNT(CASE A.race WHEN '1' THEN A.idno ELSE NULL END) AS race_1,
                                COUNT(CASE A.race WHEN '2' THEN A.idno ELSE NULL END) AS race_2,
                                COUNT(CASE A.race WHEN '3' THEN A.idno ELSE NULL END) AS race_3,
                                CAST(SUM(A.age) AS float)/COUNT(CASE WHEN A.age>=0 THEN A.idno ELSE NULL END) AS age_average,
                                COUNT(CASE WHEN (A.age<=24) THEN A.idno ELSE NULL END) AS age_24,
                                COUNT(CASE WHEN (A.age>=25 AND A.age<=29) THEN A.idno ELSE NULL END) AS age_25_29,
                                COUNT(CASE WHEN (A.age>=30 AND A.age<=34) THEN A.idno ELSE NULL END) AS age_30_34,
                                COUNT(CASE WHEN (A.age>=35 AND A.age<=39) THEN A.idno ELSE NULL END) AS age_35_39,
                                COUNT(CASE WHEN (A.age>=40 AND A.age<=44) THEN A.idno ELSE NULL END) AS age_40_44,
                                COUNT(CASE WHEN (A.age>=45 AND A.age<=49) THEN A.idno ELSE NULL END) AS age_45_49,
                                COUNT(CASE WHEN (A.age>=50) THEN A.idno ELSE NULL END) AS age_50,
                                COUNT(CASE B.sex WHEN 'M' THEN A.idno ELSE NULL END) AS sex_M,
                                COUNT(CASE B.sex WHEN 'F' THEN A.idno ELSE NULL END) AS sex_F,
                                COUNT(CASE WHEN A.ecode='1' THEN 1 ELSE NULL END) AS ecode_1,
                                COUNT(CASE WHEN A.ecode='2' THEN 1 ELSE NULL END) AS ecode_2,
                                COUNT(CASE WHEN A.ecode='3' THEN 1 ELSE NULL END) AS ecode_3,
                                COUNT(CASE WHEN A.ecode='4' THEN 1 ELSE NULL END) AS ecode_4,
                                COUNT(CASE WHEN A.ecode='5' THEN 1 ELSE NULL END) AS ecode_5,
                                COUNT(CASE WHEN A.ecode='6' THEN 1 ELSE NULL END) AS ecode_6,
                                COUNT(CASE WHEN A.ecode='7' THEN 1 ELSE NULL END) AS ecode_7,
                                E.type
                        FROM t13tb A LEFT JOIN m02tb B ON A.idno = B.idno
                                        LEFT JOIN m13tb C ON A.organ = C.organ
                                        LEFT JOIN t04tb D ON A.class = D.class AND A.term=D.term
                                        LEFT JOIN t01tb E ON A.class = E.class
                        WHERE A.status='1'
                            AND E.type='13'
                            AND (CASE WHEN E.classified='3' THEN
                                    SUBSTRING((STR_TO_DATE(DATE_ADD(DATE_FORMAT(D.edate+'19110000','%Y%m%d'),INTERVAL 14 DAY),'%Y-%m-%d')-'19110000'),1,5)
                                ELSE SUBSTRING(D.edate,1,5)
                                END ) BETWEEN '".$syear.$smonth."' AND '".$eyear.$emonth."'
                        GROUP BY A.class, A.term, E.name,E.type
                        ORDER BY classname_term ";
            }else{
                //2:依班號合併統計, SELECT A.class AS classname_term,
                $sql2="SELECT A.class AS classname_term,
                                COUNT(A.race) AS race_total,
                                COUNT(CASE A.race WHEN '1' THEN A.idno ELSE NULL END) AS race_1,
                                COUNT(CASE A.race WHEN '2' THEN A.idno ELSE NULL END) AS race_2,
                                COUNT(CASE A.race WHEN '3' THEN A.idno ELSE NULL END) AS race_3,
                                CAST(SUM(A.age) AS float)/COUNT(CASE WHEN A.age>=0 THEN A.idno ELSE NULL END) AS age_average,
                                COUNT(CASE WHEN (A.age<=24) THEN A.idno ELSE NULL END) AS age_24,
                                COUNT(CASE WHEN (A.age>=25 AND A.age<=29) THEN A.idno ELSE NULL END) AS age_25_29,
                                COUNT(CASE WHEN (A.age>=30 AND A.age<=34) THEN A.idno ELSE NULL END) AS age_30_34,
                                COUNT(CASE WHEN (A.age>=35 AND A.age<=39) THEN A.idno ELSE NULL END) AS age_35_39,
                                COUNT(CASE WHEN (A.age>=40 AND A.age<=44) THEN A.idno ELSE NULL END) AS age_40_44,
                                COUNT(CASE WHEN (A.age>=45 AND A.age<=49) THEN A.idno ELSE NULL END) AS age_45_49,
                                COUNT(CASE WHEN (A.age>=50) THEN A.idno ELSE NULL END) AS age_50,
                                COUNT(CASE B.sex WHEN 'M' THEN A.idno ELSE NULL END) AS sex_M,
                                COUNT(CASE B.sex WHEN 'F' THEN A.idno ELSE NULL END) AS sex_F,
                                COUNT(CASE WHEN A.ecode='1' THEN 1 ELSE NULL END) AS ecode_1,
                                COUNT(CASE WHEN A.ecode='2' THEN 1 ELSE NULL END) AS ecode_2,
                                COUNT(CASE WHEN A.ecode='3' THEN 1 ELSE NULL END) AS ecode_3,
                                COUNT(CASE WHEN A.ecode='4' THEN 1 ELSE NULL END) AS ecode_4,
                                COUNT(CASE WHEN A.ecode='5' THEN 1 ELSE NULL END) AS ecode_5,
                                COUNT(CASE WHEN A.ecode='6' THEN 1 ELSE NULL END) AS ecode_6,
                                COUNT(CASE WHEN A.ecode='7' THEN 1 ELSE NULL END) AS ecode_7,
                                E.type
                        FROM t13tb A LEFT JOIN m02tb B ON A.idno = B.idno
                                LEFT JOIN m13tb C ON A.organ = C.organ
                                LEFT JOIN t04tb D ON A.class = D.class AND A.term=D.term
                                LEFT JOIN t01tb E ON A.class = E.class
                        WHERE A.status='1'
                        AND E.type='13'
                        AND (CASE WHEN E.classified='3' THEN
                            SUBSTRING((STR_TO_DATE(DATE_ADD(DATE_FORMAT(D.edate+'19110000','%Y%m%d'),INTERVAL 14 DAY),'%Y-%m-%d')-'19110000'),1,5)
                        ELSE SUBSTRING(D.edate,1,5)
                        END ) BETWEEN '".$syear.$smonth."' AND '".$eyear.$emonth."'
                        GROUP BY A.class,E.type
                        ORDER BY classname_term ";
            }
            $reportlist2 = DB::select($sql2);
            $dataArr2=json_decode(json_encode(DB::select($sql2)), true);
            //取出全部項目
            if(sizeof($reportlist2) != 0) {
                $arraykeys2=array_keys((array)$reportlist2[0]);
            }

        }elseif($outputtype=='3'){
            $sqlPara='';
            if($classtype!='0' && $classtype<>''){
                // AND E.type=''
                $sqlPara=$sqlPara." AND E.type='".$classtype."' ";
            }else{
                //AND E.type<>'13'
                $sqlPara=$sqlPara." AND E.type<>'13' ";
            }
            if($traintype!='0' && $traintype<>''){
                //AND '1' = (CASE WHEN '1'='0' THEN '' ELSE E.traintype END )
                $sqlPara=$sqlPara." AND '".$traintype."' = (CASE WHEN '".$traintype."'='0' THEN '' ELSE E.traintype END )";
            }
            $sql3A="SELECT  COUNT(A.idno) AS idno_total,
                            COUNT(CASE WHEN A.ecode='1' AND B.sex='M' THEN 1 ELSE NULL END) AS ecode_1_M,
                            COUNT(CASE WHEN A.ecode='1' AND B.sex='F' THEN 1 ELSE NULL END) AS ecode_1_F,
                            COUNT(CASE WHEN A.ecode='2' AND B.sex='M'THEN 1 ELSE NULL END) AS ecode_2_M,
                            COUNT(CASE WHEN A.ecode='2' AND B.sex='F'THEN 1 ELSE NULL END) AS ecode_2_F,
                            COUNT(CASE WHEN A.ecode='3' AND B.sex='M'THEN 1 ELSE NULL END) AS ecode_3_M,
                            COUNT(CASE WHEN A.ecode='3' AND B.sex='F'THEN 1 ELSE NULL END) AS ecode_3_F,
                            COUNT(CASE WHEN A.ecode='4' AND B.sex='M'THEN 1 ELSE NULL END) AS ecode_4_M,
                            COUNT(CASE WHEN A.ecode='4' AND B.sex='F'THEN 1 ELSE NULL END) AS ecode_4_F,
                            COUNT(CASE WHEN A.ecode='5' AND B.sex='M'THEN 1 ELSE NULL END) AS ecode_5_M,
                            COUNT(CASE WHEN A.ecode='5' AND B.sex='F'THEN 1 ELSE NULL END) AS ecode_5_F,
                            COUNT(CASE WHEN A.ecode='6' AND B.sex='M'THEN 1 ELSE NULL END) AS ecode_6_M,
                            COUNT(CASE WHEN A.ecode='6' AND B.sex='F'THEN 1 ELSE NULL END) AS ecode_6_F,
                            COUNT(CASE WHEN A.ecode='7' AND B.sex='M'THEN 1 ELSE NULL END) AS ecode_7_M,
                            COUNT(CASE WHEN A.ecode='7' AND B.sex='F'THEN 1 ELSE NULL END) AS ecode_7_F
                    FROM t13tb A LEFT JOIN m02tb B ON A.idno = B.idno
                                                LEFT JOIN t04tb D ON A.class = D.class AND A.term=D.term
                                                LEFT JOIN t01tb E ON A.class = E.class
                    WHERE A.status='1'
                    AND (CASE WHEN E.classified='3' THEN
						     SUBSTRING((STR_TO_DATE(DATE_ADD(DATE_FORMAT(D.edate+'19110000','%Y%m%d'),INTERVAL 14 DAY),'%Y-%m-%d')-'19110000'),1,5)
						ELSE SUBSTRING(D.edate,1,5)
						END ) BETWEEN '".$syear.$smonth."' AND '".$eyear.$emonth."'
                    ".$sqlPara;
            $reportlist3A = DB::select($sql3A);
            $dataArr3A=json_decode(json_encode(DB::select($sql3A)), true);
            //取出全部項目
            if(sizeof($reportlist3A) != 0) {
                $arraykeys3A=array_keys((array)$reportlist3A[0]);
            }

            $sql3B="SELECT
                            COUNT(A.idno) AS idno_total,
                            CAST(SUM(A.age) AS float)/COUNT(CASE WHEN A.age>=0 THEN A.idno ELSE NULL END) AS age_average,
                            COUNT(CASE WHEN (A.age<=24 AND B.sex='M') THEN A.idno ELSE NULL END) AS age_24_M,
                            COUNT(CASE WHEN (A.age<=24 AND B.sex='F') THEN A.idno ELSE NULL END) AS age_24_F,
                            COUNT(CASE WHEN (A.age>=25 AND A.age<=29 AND B.sex='M') THEN A.idno ELSE NULL END) AS age_25_29_M,
                            COUNT(CASE WHEN (A.age>=25 AND A.age<=29 AND B.sex='F') THEN A.idno ELSE NULL END) AS age_25_29_F,
                            COUNT(CASE WHEN (A.age>=30 AND A.age<=34 AND B.sex='M') THEN A.idno ELSE NULL END) AS age_30_34_M,
                            COUNT(CASE WHEN (A.age>=30 AND A.age<=34 AND B.sex='F') THEN A.idno ELSE NULL END) AS age_30_34_F,
                            COUNT(CASE WHEN (A.age>=35 AND A.age<=39 AND B.sex='M') THEN A.idno ELSE NULL END) AS age_35_39_M,
                            COUNT(CASE WHEN (A.age>=35 AND A.age<=39 AND B.sex='F') THEN A.idno ELSE NULL END) AS age_35_39_F,
                            COUNT(CASE WHEN (A.age>=40 AND A.age<=44 AND B.sex='M') THEN A.idno ELSE NULL END) AS age_40_44_M,
                            COUNT(CASE WHEN (A.age>=40 AND A.age<=44 AND B.sex='F') THEN A.idno ELSE NULL END) AS age_40_44_F,
                            COUNT(CASE WHEN (A.age>=45 AND A.age<=49 AND B.sex='M') THEN A.idno ELSE NULL END) AS age_45_49_M,
                            COUNT(CASE WHEN (A.age>=45 AND A.age<=49 AND B.sex='F') THEN A.idno ELSE NULL END) AS age_45_49_F,
                            COUNT(CASE WHEN (A.age>=50 AND B.sex='M') THEN A.idno ELSE NULL END) AS age_50_M,
                            COUNT(CASE WHEN (A.age>=50 AND B.sex='F') THEN A.idno ELSE NULL END) AS age_50_F
                    FROM t13tb A LEFT JOIN m02tb B ON A.idno = B.idno
                                                LEFT JOIN t04tb D ON A.class = D.class AND A.term=D.term
                                                LEFT JOIN t01tb E ON A.class = E.class
                    WHERE A.status='1'
                    AND (CASE WHEN E.classified='3' THEN
						     SUBSTRING((STR_TO_DATE(DATE_ADD(DATE_FORMAT(D.edate+'19110000','%Y%m%d'),INTERVAL 14 DAY),'%Y-%m-%d')-'19110000'),1,5)
						ELSE SUBSTRING(D.edate,1,5)
						END ) BETWEEN '".$syear.$smonth."' AND '".$eyear.$emonth."'
                        ".$sqlPara;
            $reportlist3B = DB::select($sql3B);
            $dataArr3B=json_decode(json_encode(DB::select($sql3B)), true);
            //取出全部項目
            if(sizeof($reportlist3B) != 0) {
                $arraykeys3B=array_keys((array)$reportlist3B[0]);
            }
            //dd($sql3B);
        }

        if($outputtype=='1'){
            $fileName = 'J17A';
        }elseif($outputtype=='2'){
            $fileName = 'J17B';
        }elseif($outputtype=='3'){
            $fileName = 'J17C';
        }

        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        //$reportlist = json_decode(json_encode($reportlist), true);
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&14'.'行政院人事行政總處公務人力發展學院　在職訓練人數
        &12中華民國　'.$syear.'年'.$smonth.'月至'.$eyear.'年'.$emonth.'月');

        if($outputtype=='1'){


            $reportlist1 = json_decode(json_encode($reportlist1), true);
            if(sizeof($reportlist1) != 0) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys1); $i++) {
                    //excel 欄位 1 == A, etc
                    $NameFromNumber=$this->getNameFromNumber($i+1); //A
                    //資料by班別迴圈
                    for ($j=0; $j < sizeof($reportlist1); $j++) {
                        //A2開始
                        if($i+1 != sizeof($arraykeys1)){
                            if($reportlist1[$j][$arraykeys1[$i]]=='0'){
                                $objActSheet->setCellValue($NameFromNumber.($j+4), '');
                            }else{
                                $objActSheet->setCellValue($NameFromNumber.($j+4), $reportlist1[$j][$arraykeys1[$i]]);
                            }

                            //'班級分類為 12:其他類 時，整筆變成藍色
                            //'班級分類為 13:游於藝類 時，整筆變成棕色
                            //'班級分類為 14:研習會議及活動時，整筆變成紅色
                            if($reportlist1[$j][$arraykeys1[24]]=='12'){
                                $objActSheet->getStyle($NameFromNumber.($j+4))->getFont()->getColor()->setARGB('0000ff'); //藍色
                            }
                            if($reportlist1[$j][$arraykeys1[24]]=='13'){
                                $objActSheet->getStyle($NameFromNumber.($j+4))->getFont()->getColor()->setARGB('802A2A'); //棕色
                            }
                            if($reportlist1[$j][$arraykeys1[24]]=='14'){
                                $objActSheet->getStyle($NameFromNumber.($j+4))->getFont()->getColor()->setARGB('FF0000'); //紅色
                            }

                            if(($j+1)==sizeof($reportlist1)){
                                //第３列 總計
                                if($i!=0){
                                    if($i!=7){
                                        //=SUM(B4:B8)
                                        $objActSheet->setCellValue($NameFromNumber.'3','=SUM('.$NameFromNumber.'4:'.$NameFromNumber.($j+4).')');
                                    }else{
                                        //=AVERAGE(F4:F8)
                                        $objActSheet->setCellValue($NameFromNumber.'3','=AVERAGE('.$NameFromNumber.'4:'.$NameFromNumber.($j+4).')');
                                    }
                                }
                            }
                        }
                        //高 25
                        $objActSheet->getRowDimension($j+4)->setRowHeight(25);
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

                $objActSheet->getStyle('A4:X'.($j+3))->applyFromArray($styleArray);
            }
        }elseif($outputtype=='2'){
            $reportlist2 = json_decode(json_encode($reportlist2), true);
            if(sizeof($reportlist2) != 0) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys2); $i++) {
                    //excel 欄位 1 == A, etc
                    $NameFromNumber=$this->getNameFromNumber($i+1); //A
                    //資料by班別迴圈
                    for ($j=0; $j < sizeof($reportlist2); $j++) {
                        //A2開始
                        if($i+1 != sizeof($arraykeys2)){
                            if($reportlist2[$j][$arraykeys2[$i]]=='0'){
                                $objActSheet->setCellValue($NameFromNumber.($j+4), '');
                            }else{
                                $objActSheet->setCellValue($NameFromNumber.($j+4), $reportlist2[$j][$arraykeys2[$i]]);
                            }
                            //游於藝類，整筆變成棕色
                            $objActSheet->getStyle($NameFromNumber.($j+4))->getFont()->getColor()->setARGB('802A2A');
                            if(($j+1)==sizeof($reportlist2)){
                                //第３列 總計
                                if($i!=0){
                                    if($i!=5){
                                        //=SUM(B4:B8)
                                        $objActSheet->setCellValue($NameFromNumber.'3','=SUM('.$NameFromNumber.'4:'.$NameFromNumber.($j+4).')');
                                    }else{
                                        //=AVERAGE(F4:F8)
                                        $objActSheet->setCellValue($NameFromNumber.'3','=AVERAGE('.$NameFromNumber.'4:'.$NameFromNumber.($j+4).')');
                                    }
                                }
                            }
                        }
                        //高 25
                        $objActSheet->getRowDimension($j+4)->setRowHeight(25);
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

                $objActSheet->getStyle('A4:V'.($j+3))->applyFromArray($styleArray);
            }

        }elseif($outputtype=='3'){
            $reportlist3A = json_decode(json_encode($reportlist3A), true);
            $reportlist3B = json_decode(json_encode($reportlist3B), true);
            //資料統計期間：105年01月至12月
            $objActSheet->setCellValue('G2', '資料統計期間：'.$syear.'年'.$smonth.'月至'.$eyear.'年'.$emonth.'月');
            $objActSheet->setCellValue('H9', '資料統計期間：'.$syear.'年'.$smonth.'月至'.$eyear.'年'.$emonth.'月');
            //學歷性別
            if(sizeof($reportlist3A) != 0) {
                for($i=0; $i < sizeof($arraykeys3A); $i++){
                    if($i>=1){
                        $NameFromNumber=$this->getNameFromNumber($i+2); //A
                        $objActSheet->setCellValue($NameFromNumber.'5', $reportlist3A[0][$arraykeys3A[$i]]);
                    }
                }
            }
            //年齡性別
            if(sizeof($reportlist3B) != 0) {
                for($i=0; $i < sizeof($arraykeys3B); $i++){
                    if($i>=1){
                        $NameFromNumber=$this->getNameFromNumber($i+2); //A
                        $objActSheet->setCellValue($NameFromNumber.'12', $reportlist3B[0][$arraykeys3B[$i]]);
                    }
                }
            }

        }

        $outputname=""; 
        if($outputtype=='1'){
            $outputname="在職訓練人數統計表";
        }elseif($outputtype=='2'){
            $outputname="在職訓練人數統計表-游於藝講堂";
        }elseif($outputtype=='3'){
            $outputname="在職訓練人數統計表-學歷性別及年齡性別";
        }
       
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),$outputname);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
