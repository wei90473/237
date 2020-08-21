<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Services\User_groupService;

class TrainingPerformanceController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
       //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('training_performance', $user_group_auth)){
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
        $sqlDepartment = "SELECT section FROM m09tb GROUP BY section";
        $department = DB::select($sqlDepartment);
        $sqlEmployee = "SELECT username,userid FROM m09tb ORDER BY (CASE WHEN dimission='Y' THEN 1 ELSE 0 END),username";
        $employee = DB::select($sqlEmployee);
        $result = '';
        return view('admin/training_performance/list', compact('class', 'department', 'employee','result'));
    }

    /**
     * 列印檔案
     * 
     */
    public function export(Request $request)
    {   
        //變數設定
        //年度
        $year = $request->input('yerly');
        $ratiomonthYear = $request->input('monthYear');
        //月份
        $month = $request->input('selectMonth');
        //起始年
        $startYear = $request->input('startYear');
        //起始月
        $startMonth = $request->input('startMonth');
        //結束年
        $endYear = $request->input('endYear');
        //結束月
        $endMonth = $request->input('endMonth');
        //訓練性質
        $training = $request->input('training');
        //班別性質
        $classes = $request->input('classes');
        //開班性質
        $startClass = $request->input('startClass');
        //天數
        $days = $request->input('days');
        //部門
        $department=$request->input('department');
        //辦班人員
        $staff = $request->input('staff');
        //辦班方法  【辦理方式】 1:自行辦理 2:委外辦理 3:合作辦理 4:接受委託 
        $process = $request->input('checkClass');
        //辦班人員滿意度 $satisfiedstaff
        $checkboxsatisfaction = $request->input('satisfaction');
        //行政服務   $admservice
        $checkboxservice = $request->input('service');
        //院區      $area $branch
        $ratioarea = $request->input('area');
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,   
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $numberformat=[
                'code' => NumberFormat::FORMAT_NUMBER_00
        ];
        $departmentArr=array("綜合規劃組","培育發展組","專業訓練組","數位學習組","秘書室","人事室","主計室");
        $classesArr=array("23"=>"領導力發展","24"=>"政策能力訓練","25"=>"部會業務知能訓練","26"=>"自我成長及其他");

        $cbranch="全部院區";

        $yearperiod="";

        if($month<10){
            $month='0'.$month;
        } 
        if($startMonth<10){
            $startMonth='0'.$startMonth;
        }
        if($endMonth<10){
            $endMonth='0'.$endMonth;
        }

        if($ratiomonthYear=="1"){
            $startYear = $year;
            $endYear = $year;
            $startMonth ='01';
            $endMonth ='12';
            $yearperiod = $year;
            $caption=$year.'年度';
        }elseif($ratiomonthYear=="2"){
            $startYear = $year;
            $endYear = $year;
            $startMonth = $month;
            $endMonth = $month;
            $yearperiod = $year;
            $caption=$year.'年度'.$month.'月';
        }else{
            
            if($startYear==$endYear){
                $yearperiod=$startYear;
            }else{
                for($i=$startYear;$i<$endYear;$i++){
                    if($i==$endYear-1)
                        $yearperiod.=$i.",".$endYear;
                    else
                        $yearperiod.=$i.",";    
                }
            }
            $caption=$year.'年'.$startMonth.'月~'.$year.'年'.$endMonth.'月';
        }
       
        // 上課地點 1:臺北院區；2: 南投院區； 
        $branch="0";
        if($ratioarea=='1'){
            $branch="1";
            $cbranch="臺北院區";
        }elseif($ratioarea=='2'){
            $branch="2";
            $cbranch="南投院區";
        }

        $section="0";
        if($startClass=="2")
            $section=" IN ('2','3')";
        else if($startClass=="1")
            $section=" ='1' ";

        $outputname="訓練績效報表-含辦班人員及滿意度、行政服務";

        if($classes!="A0" && $department!="A0") //單報表
        {
            $sql=
            "SELECT 班別代碼,院區, 班別類型, 班別名稱, 起訖日期, 天數, 每日上課時數, 應到, 實到, 報到率, 結業,
                結業率, 人‧天數, 人‧時數,辦班組室, 辦班人員, 研習規劃, 學習投入, 學習輔導, 講師授課, 總體滿意度, 回收率, 行政服務
            FROM(
                (SELECT
                (CASE WHEN B.branch = '1' THEN '臺北院區' WHEN B.branch = '2' THEN '南投院區' ELSE '' END) as 院區 ,
                CONCAT(A.class,A.term) as 班別代碼 ,
                '自行辦理' as 班別類型,
                CONCAT(RTRIM(B.name),'第',CAST(CAST(A.term AS int) AS varchar(2)),'期') as 班別名稱 ,
                CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                B.trainday as 天數,
                B.classhr as 每日上課時數 ,
                (CASE WHEN B.cntflag='1' THEN A.quota WHEN B.cntflag='2' THEN A.regcnt ELSE 0 END) as 應到 ,
                (CASE WHEN B.cntflag='1' THEN C.實到 WHEN B.cntflag='2' THEN A.passcnt ELSE 0 END) as 實到 ,
                0 as 報到率 ,
                (CASE WHEN B.cntflag='1' THEN C.結業 WHEN B.cntflag='2' THEN A.endcnt ELSE 0 END) as 結業 ,
                0 as 結業率 ,
                0 as 人‧天數,
                0 as 人‧時數 ,
                A.section as 辦班組室,
                IFNULL(E.username,A.sponsor) as 辦班人員,
                D.conper as 研習規劃,
                D.worper as 學習輔導,
                D.teaper as 講師授課,
                D.envper as 行政服務,
                D.whoper as 整體評價,
                D.offper as 公務執行,
                D.attper as 學習投入,
                (CASE
                    WHEN '".$checkboxservice."' = '1' THEN D.totper /*含【行政服務】*/
                    WHEN cast(LEFT(A.class,3) as int) >= 96 THEN D.totper /*96年以後的【總平均】不用重算*/
                    ELSE cast(ROUND( (D.conper+D.worper+D.teaper+D.whoper)/4,2 ) as decimal(6,2)) /* 【總平均】 = (【研習規劃】+【學習輔導】+【講師授課】+【整體評價】)/4 */
                    END) as 總體滿意度,
                F.recrate as 回收率,
                A.class,
                A.term,
                A.sdate

                FROM t04tb as A INNER JOIN t01tb as B ON A.class = B.class LEFT JOIN 
                (SELECT class,term,
                COUNT(CASE WHEN status <>'2' THEN 1 ELSE NULL END) as 實到 ,
                COUNT(CASE WHEN status = '1' THEN 1 ELSE NULL END) as 結業
                FROM t13tb 
                WHERE LEFT(class,3) BETWEEN ".$startYear." AND ".$endYear."
                GROUP BY class,term
                ORDER BY class,term) as C
                ON A.class = C.class
                AND A.term = C.term
                LEFT JOIN t57tb as D 
                ON A.class  = D.class
                AND A.term  = D.term
                AND D.times = ''
                LEFT JOIN m09tb as E 
                ON A.sponsor = E.userid
                LEFT JOIN 
                (SELECT class, term, ROUND( SUM(onerecrate)*100/CAST(COUNT(*) AS decimal(6,3)) ,2) AS recrate FROM
                (SELECT Aa.class,Aa.term,Aa.times,Aa.copy,
                IFNULL(SUM(Bb.cnt),0) AS cnt,
                IFNULL(SUM(Bb.cnt),0)/CAST(Aa.copy AS decimal(6,3)) AS onerecrate
                FROM t53tb as Aa
                INNER JOIN 
                ((SELECT class, term, times, COUNT(*) AS cnt FROM t55tb GROUP BY class,term,times)
                UNION ALL (SELECT class, term, times, COUNT(*) AS cnt FROM t72tb GROUP BY class,term,times)
                UNION ALL (SELECT class, term, times, COUNT(*) AS cnt FROM t75tb GROUP BY class,term,times)
                UNION ALL (SELECT  class, term, times, COUNT(*) AS cnt  FROM t91tb GROUP BY class,term,times)
                UNION ALL (SELECT class, term, times, COUNT(*) AS cnt FROM t95tb GROUP BY class,term,times )
                ORDER BY class,term,times
                ) as Bb
                ON Aa.class = Bb.class AND Aa.term = Bb.term AND Aa.times = Bb.times
                WHERE LEFT(Aa.class,3) IN (".$yearperiod.") AND Aa.copy > 0                 
                GROUP BY Aa.class, Aa.term, Aa.times, Aa.copy
                ORDER BY Aa.class, Aa.term, Aa.times) as Cc
                GROUP BY class, term
                ORDER BY class, term) as F
                ON A.class = F.class
                AND A.term = F.term
                WHERE
                    LEFT(
                    (CASE
                    /*【t01tb 班別基本資料檔】classified  學習性質 1:數位 2:實體3:混成 若t01tb.classified='3'，則edate為 t04tb.edate加14天 dbo.ufn_date_to_cdate 西元年轉民國年 dbo.ufn_cdate_to_date 民國年轉西元年*/
                    WHEN B.classified='3' THEN 
                    
                        CONCAT(
                        (CASE WHEN LEFT(A.edate,3)*1<100 THEN
                        CONCAT('0',CAST(LEFT(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),4)*1 -1911 as CHAR))
                        ELSE
                        CAST(LEFT(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),4)*1 -1911 as CHAR)
                        END),
                        SUBSTRING(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),6,2), 
                        RIGHT(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),2) 
                        )
                    
                    ELSE A.edate END),5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth." ";
                    
                if($training!="0")
                    $sql.="AND B.traintype = '".$training."' "; 
                if($department!="0")
                    $sql.="AND A.section = '".$department."' "; 
                if($staff!="0")
                    $sql.="AND A.sponsor = '".$staff."' ";  
                if($section!="0")
                    $sql.="AND B.signin ".$section." ";
                if($branch!="0")
                    $sql.="AND B.branch ='".$branch."' ";
                if($process!="0")
                    $sql.="AND B.process  ='".$process."' ";
                if($days!="")
                    $sql.="AND B.trainday  >=".$days." ";    
                if($classes!="0")
                    $sql.="AND B.type  ='".$classes."' ";       
    

            // //班別性質
            // $classes = $request->input('classes');
            // //開班性質
            // $startClass = $request->input('startClass'); =>section
            // //訓練性質
            //OK $training = $request->input('training');
            // //天數
            //OK $days = $request->input('days');
            // //部門
            //OK $department=$request->input('department');
            // //辦班人員
            //OK $staff = $request->input('staff');
            // //辦班方法  【辦理方式】 1:自行辦理 2:委外辦理 3:合作辦理 4:接受委託 
            //OK $process = $request->input('checkClass');
            // //辦班人員滿意度 $satisfiedstaff
            //OK $checkboxsatisfaction = $request->input('satisfaction');
            // //行政服務   $admservice
            //OK $checkboxservice = $request->input('service');
            // //院區      $area $branch
            //OK $ratioarea = $request->input('area');

                $sql.=")
                UNION ALL
                (SELECT
                '' as 院區,
                A.class as 班別代碼,
                RTRIM(B.name) as 班別類型,
                CONCAT(RTRIM(B.name),(
                    CASE
                    WHEN A.term = '' THEN ''
                    ELSE CONCAT('第',CAST(CAST(A.term AS int) AS varchar(2)),'期')
                    END
                    )) as 班別名稱,
                CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                A.day as 天數 ,
                A.hour as 每日上課時數 ,
                A.regcnt as 應到,
                A.passcnt as  實到 ,
                '' as 報到率,
                A.endcnt as 結業 ,
                '' as 結業率,
                '' as 人‧天數,
                '' as 人‧時數,
                '' as 辦班組室,
                '' as 辦班人員,
                '' as 研習規劃, 
                '' as 學習輔導,
                '' as 講師授課,
                '' as 行政服務,
                '' as 整體評價,
                '' as 公務執行,
                '' as 學習投入,
                '' as 總體滿意度,
                '' as 回收率,
                A.class,
                '' as  term,
                A.sdate

                FROM t24tb A 
                LEFT JOIN m15tb B 
                ON A.school = B.school
                WHERE LEFT(A.edate,5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth."
                )
                UNION ALL
                (SELECT
                '' as 院區,
                CONCAT(A.class,A.term) as 班別代碼,
                (CASE A.class
                    WHEN '091001' THEN '巨匠電腦股份有限公司'
                    WHEN '091002' THEN '巨匠電腦股份有限公司'
                    WHEN '091003' THEN '巨匠電腦股份有限公司'
                    WHEN '091004' THEN '巨匠電腦股份有限公司'
                    WHEN '091005' THEN '巨匠電腦股份有限公司'
                    WHEN '091006' THEN '巨匠電腦股份有限公司'
                    WHEN '091007' THEN '巨匠電腦股份有限公司'
                    ELSE IFNULL((SELECT RTRIM(name) FROM s01tb WHERE  code = '".$startYear."'),'')
                END) as 班別類型,
                CONCAT(RTRIM(B.name),
                    CASE
                    WHEN A.term = '' THEN ''
                    ELSE CONCAT('第',CAST(CAST(A.term AS int) AS varchar(2)),'期')
                    END
                ) as 班別名稱 ,
                CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                A.day as 天數,
                A.dayhour as 每日上課時數 ,
                A.regcnt as 應到,
                A.passcnt as 實到,
                '' as 報到率,
                A.endcnt as 結業,
                '' as 結業率,
                '' as 人‧天數,
                '' as 人‧時數,
                '' as 辦班組室,
                '' as 辦班人員,
                '' as 研習規劃, 
                '' as 學習輔導,
                '' as 講師授課,
                '' as 行政服務,
                '' as 整體評價,
                '' as 公務執行,
                '' as 學習投入,
                '' as 總體滿意度,
                '' as 回收率,
                A.class,
                A.term,
                A.sdate
            
                FROM t26tb A LEFT JOIN t25tb B ON A.class=B.class 
                WHERE LEFT(A.edate,5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth."
                )
                UNION ALL
                (SELECT
                '' as 院區,
                CONCAT(A.class,A.term) as 班別代碼,
                '委外辦理' as 班別類型,
                (CASE WHEN A.term = '' THEN '' ELSE CONCAT('第',CAST(CAST(A.term AS int) AS varchar(2)),'期') END) as 班別名稱 ,
                CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                A.trainday as 天數,
                A.trainhour as  每日上課時數,
                A.regcnt as 應到,
                A.passcnt as 實到,
                '' as 報到率,
                A.endcnt as 結業,
                '' as 結業率,
                '' as 人‧天數,
                '' as 人‧時數,
                '' as 辦班組室,
                '' as 辦班人員,
                '' as 研習規劃, 
                '' as 學習輔導,
                '' as 講師授課,
                '' as 行政服務,
                '' as 整體評價,
                '' as 公務執行,
                '' as 學習投入,
                '' as 總體滿意度,
                '' as 回收率,
                A.class,
                A.term,
                A.sdate
                
                FROM t66tb A
                WHERE LEFT(A.edate,5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth."
                ) 
            )as result
            ORDER BY sdate,class,term"; 

            $dataArr=json_decode(json_encode(DB::select($sql)), true);

            if($dataArr==[]){
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclasstypek();
                $class=$temp;
                $sqlDepartment = "SELECT section FROM m09tb GROUP BY section";
                $department = DB::select($sqlDepartment);
                $sqlEmployee = "SELECT username,userid FROM m09tb ORDER BY (CASE WHEN dimission='Y' THEN 1 ELSE 0 END),username";
                $employee = DB::select($sqlEmployee);
                $result = '此條件查無資料，請重新查詢。';
                return view('admin/training_performance/list', compact('class', 'department', 'employee','result'));
            }

            // 範本檔案名稱
            $fileName = 'D11';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel

            $objPHPExcel = IOFactory::load($filePath);
            $objActSheet = $objPHPExcel->getActiveSheet();
            $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體"&B '.$caption.'訓練績效報表('.$cbranch.')');  

            for($i=0; $i<sizeof($dataArr); $i++){
                $arraykeys=array_keys((array)$dataArr[$i]);
                for($k=0; $k<sizeof($arraykeys); $k++){
                    //fill values & formula =IF(J3>0,IF(E3>0,(J3*E3),""),"")
                    if($k==4){
                        $objActSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),str_replace("~", "", $dataArr[$i][$arraykeys[$k]]));
                    }elseif($k==9){
                        $objActSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(I'.($i+3).'/H'.($i+3).'),""),"")');
                    }elseif($k==11){
                        $objActSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(K'.($i+3).'/I'.($i+3).'),""),"")');
                    }elseif($k==12){
                        $objActSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(K'.($i+3).'*F'.($i+3).'),""),"")');
                    }elseif($k==13){
                        $objActSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(K'.($i+3).'*G'.($i+3).'),""),"")');
                    }else{
                        $objActSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),$dataArr[$i][$arraykeys[$k]]);
                    }
                }        
            }        
            // <24
            $rownum=sizeof($dataArr)<24?23:sizeof($dataArr);
            //sum of columns 6,7,8,9,11,13,14 =SUM(G3:G15)
            for($i=6;$i<15;$i++)
            {
            if(($i==10) ){}elseif(($i==12)){}else{
                    $objActSheet->setCellValue($this->getNameFromNumber($i).($rownum+3),
                    '=SUM('.$this->getNameFromNumber($i).'3:'.$this->getNameFromNumber($i).($rownum+2).')');
            }
            }
            
            //avg,10,12,13,14,16-22
            $objActSheet->setCellValue($this->getNameFromNumber(10).($rownum+4),'=(I'.($rownum+3).'/H'.($rownum+3).')');
            $objActSheet->setCellValue($this->getNameFromNumber(11).($rownum+4), '=(K'.($rownum+3).'/I'.($rownum+3).')');

            for($i=10;$i<23;$i++)
            {
                if(($i==11) ){}elseif(($i==15)){}else{
                    $objActSheet->setCellValue($this->getNameFromNumber($i).($rownum+4),
                    '=IF(COUNT('.$this->getNameFromNumber($i).'3:'.$this->getNameFromNumber($i).($rownum+2).')>0,AVERAGE('.$this->getNameFromNumber($i).'3:'.$this->getNameFromNumber($i).($rownum+2).'),"")');
                }    
            }

            //人˙天數(1天6小時)12
            $objActSheet->setCellValue('M'.($rownum+5),'=(N'.($rownum+3).'/6)');

            //set column A
            $objActSheet->setCellValue('A'.($rownum+3), '加總('.strval(sizeof($dataArr)).'班)');
            $objActSheet->setCellValue('A'.($rownum+4), '平均值');
            //objActSheet->mergeCells('A1:B1');
            $objActSheet->mergeCells('A'.($rownum+5).':B'.($rownum+5));
            $objActSheet->setCellValue('A'.($rownum+5),'人˙天數(1天6小時)');

            //apply format
            $objActSheet->getStyle('A1:'.$this->getNameFromNumber(sizeof($arraykeys)).($rownum+5))->applyFromArray($styleArray);
            $objActSheet->getStyle('L'.($rownum+5).":V".($rownum+5))->getNumberFormat()->applyFromArray($numberformat);
            for($r=sizeof($dataArr);$r<$rownum+1;$r++)
                $objActSheet->getRowDimension($r+2)->setRowHeight(31.4);

            if($checkboxsatisfaction=="1" && $checkboxservice!="2"){
                $outputname="訓練績效報表-含辦班人員及滿意度";
                $objActSheet->getColumnDimension('W')->setVisible(false);
            }
            if($checkboxsatisfaction!="1" && $checkboxservice!="2"){
                $outputname="訓練績效報表";
                for($i=15;$i<24;$i++)
                    $objActSheet->getColumnDimension($this->getNameFromNumber($i))->setVisible(false);

            }
            
            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"$outputname");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 


        }elseif($classes=="A0" && $department!="A0"){ //訓練計畫四大類

            // 範本檔案名稱
            $fileName = 'D11';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel

            $objPHPExcel = IOFactory::load($filePath);
            $sheet_id = 0;
            foreach($classesArr as $key=>$cv){
                $sql=
                "SELECT 班別代碼,院區, 班別類型, 班別名稱, 起訖日期, 天數, 每日上課時數, 應到, 實到, 報到率, 結業,
                    結業率, 人‧天數, 人‧時數,辦班組室, 辦班人員, 研習規劃, 學習投入, 學習輔導, 講師授課, 總體滿意度, 回收率, 行政服務
                FROM(
                    (SELECT
                    (CASE WHEN B.branch = '1' THEN '臺北院區' WHEN B.branch = '2' THEN '南投院區' ELSE '' END) as 院區 ,
                    CONCAT(A.class,A.term) as 班別代碼 ,
                    '自行辦理' as 班別類型,
                    CONCAT(RTRIM(B.name),'第',CAST(CAST(A.term AS int) AS varchar(2)),'期') as 班別名稱 ,
                    CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                    B.trainday as 天數,
                    B.classhr as 每日上課時數 ,
                    (CASE WHEN B.cntflag='1' THEN A.quota WHEN B.cntflag='2' THEN A.regcnt ELSE 0 END) as 應到 ,
                    (CASE WHEN B.cntflag='1' THEN C.實到 WHEN B.cntflag='2' THEN A.passcnt ELSE 0 END) as 實到 ,
                    0 as 報到率 ,
                    (CASE WHEN B.cntflag='1' THEN C.結業 WHEN B.cntflag='2' THEN A.endcnt ELSE 0 END) as 結業 ,
                    0 as 結業率 ,
                    0 as 人‧天數,
                    0 as 人‧時數 ,
                    A.section as 辦班組室,
                    IFNULL(E.username,A.sponsor) as 辦班人員,
                    D.conper as 研習規劃,
                    D.worper as 學習輔導,
                    D.teaper as 講師授課,
                    D.envper as 行政服務,
                    D.whoper as 整體評價,
                    D.offper as 公務執行,
                    D.attper as 學習投入,
                    (CASE
                        WHEN '".$checkboxservice."' = '1' THEN D.totper /*含【行政服務】*/
                        WHEN cast(LEFT(A.class,3) as int) >= 96 THEN D.totper /*96年以後的【總平均】不用重算*/
                        ELSE cast(ROUND( (D.conper+D.worper+D.teaper+D.whoper)/4,2 ) as decimal(6,2)) /* 【總平均】 = (【研習規劃】+【學習輔導】+【講師授課】+【整體評價】)/4 */
                        END) as 總體滿意度,
                    F.recrate as 回收率,
                    A.class,
                    A.term,
                    A.sdate

                    FROM t04tb as A INNER JOIN t01tb as B ON A.class = B.class LEFT JOIN 
                    (SELECT class,term,
                    COUNT(CASE WHEN status <>'2' THEN 1 ELSE NULL END) as 實到 ,
                    COUNT(CASE WHEN status = '1' THEN 1 ELSE NULL END) as 結業
                    FROM t13tb 
                    WHERE LEFT(class,3) BETWEEN ".$startYear." AND ".$endYear."
                    GROUP BY class,term
                    ORDER BY class,term) as C
                    ON A.class = C.class
                    AND A.term = C.term
                    LEFT JOIN t57tb as D 
                    ON A.class  = D.class
                    AND A.term  = D.term
                    AND D.times = ''
                    LEFT JOIN m09tb as E 
                    ON A.sponsor = E.userid
                    LEFT JOIN 
                    (SELECT class, term, ROUND( SUM(onerecrate)*100/CAST(COUNT(*) AS decimal(6,3)) ,2) AS recrate FROM
                    (SELECT Aa.class,Aa.term,Aa.times,Aa.copy,
                    IFNULL(SUM(Bb.cnt),0) AS cnt,
                    IFNULL(SUM(Bb.cnt),0)/CAST(Aa.copy AS decimal(6,3)) AS onerecrate
                    FROM t53tb as Aa
                    INNER JOIN 
                    ((SELECT class, term, times, COUNT(*) AS cnt FROM t55tb GROUP BY class,term,times)
                    UNION ALL (SELECT class, term, times, COUNT(*) AS cnt FROM t72tb GROUP BY class,term,times)
                    UNION ALL (SELECT class, term, times, COUNT(*) AS cnt FROM t75tb GROUP BY class,term,times)
                    UNION ALL (SELECT  class, term, times, COUNT(*) AS cnt  FROM t91tb GROUP BY class,term,times)
                    UNION ALL (SELECT class, term, times, COUNT(*) AS cnt FROM t95tb GROUP BY class,term,times )
                    ORDER BY class,term,times
                    ) as Bb
                    ON Aa.class = Bb.class AND Aa.term = Bb.term AND Aa.times = Bb.times
                    WHERE LEFT(Aa.class,3) IN (".$yearperiod.") AND Aa.copy > 0                 
                    GROUP BY Aa.class, Aa.term, Aa.times, Aa.copy
                    ORDER BY Aa.class, Aa.term, Aa.times) as Cc
                    GROUP BY class, term
                    ORDER BY class, term) as F
                    ON A.class = F.class
                    AND A.term = F.term
                    WHERE
                        LEFT(
                        (CASE
                        /*【t01tb 班別基本資料檔】classified  學習性質 1:數位 2:實體3:混成 若t01tb.classified='3'，則edate為 t04tb.edate加14天 dbo.ufn_date_to_cdate 西元年轉民國年 dbo.ufn_cdate_to_date 民國年轉西元年*/
                        WHEN B.classified='3' THEN 
                        
                            CONCAT(
                            (CASE WHEN LEFT(A.edate,3)*1<100 THEN
                            CONCAT('0',CAST(LEFT(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),4)*1 -1911 as CHAR))
                            ELSE
                            CAST(LEFT(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),4)*1 -1911 as CHAR)
                            END),
                            SUBSTRING(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),6,2), 
                            RIGHT(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),2) 
                            )
                        
                        ELSE A.edate END),5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth." ";
                        
                    if($training!="0")
                        $sql.="AND B.traintype = '".$training."' "; 
                    if($department!="0")
                        $sql.="AND A.section = '".$department."' "; 
                    if($staff!="0")
                        $sql.="AND A.sponsor = '".$staff."' ";  
                    if($section!="0")
                        $sql.="AND B.signin ".$section." ";
                    if($branch!="0")
                        $sql.="AND B.branch ='".$branch."' ";
                    if($process!="0")
                        $sql.="AND B.process  ='".$process."' ";
                    if($days!="")
                        $sql.="AND B.trainday  >=".$days." ";    
                 
                    $sql.="AND B.type  ='".$ckey."' ";       
        
                    $sql.=")
                    UNION ALL
                    (SELECT
                    '' as 院區,
                    A.class as 班別代碼,
                    RTRIM(B.name) as 班別類型,
                    CONCAT(RTRIM(B.name),(
                        CASE
                        WHEN A.term = '' THEN ''
                        ELSE CONCAT('第',CAST(CAST(A.term AS int) AS varchar(2)),'期')
                        END
                        )) as 班別名稱,
                    CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                    A.day as 天數 ,
                    A.hour as 每日上課時數 ,
                    A.regcnt as 應到,
                    A.passcnt as  實到 ,
                    '' as 報到率,
                    A.endcnt as 結業 ,
                    '' as 結業率,
                    '' as 人‧天數,
                    '' as 人‧時數,
                    '' as 辦班組室,
                    '' as 辦班人員,
                    '' as 研習規劃, 
                    '' as 學習輔導,
                    '' as 講師授課,
                    '' as 行政服務,
                    '' as 整體評價,
                    '' as 公務執行,
                    '' as 學習投入,
                    '' as 總體滿意度,
                    '' as 回收率,
                    A.class,
                    '' as  term,
                    A.sdate

                    FROM t24tb A 
                    LEFT JOIN m15tb B 
                    ON A.school = B.school
                    WHERE LEFT(A.edate,5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth."
                    )
                    UNION ALL
                    (SELECT
                    '' as 院區,
                    CONCAT(A.class,A.term) as 班別代碼,
                    (CASE A.class
                        WHEN '091001' THEN '巨匠電腦股份有限公司'
                        WHEN '091002' THEN '巨匠電腦股份有限公司'
                        WHEN '091003' THEN '巨匠電腦股份有限公司'
                        WHEN '091004' THEN '巨匠電腦股份有限公司'
                        WHEN '091005' THEN '巨匠電腦股份有限公司'
                        WHEN '091006' THEN '巨匠電腦股份有限公司'
                        WHEN '091007' THEN '巨匠電腦股份有限公司'
                        ELSE IFNULL((SELECT RTRIM(name) FROM s01tb WHERE  code = '".$startYear."'),'')
                    END) as 班別類型,
                    CONCAT(RTRIM(B.name),
                        CASE
                        WHEN A.term = '' THEN ''
                        ELSE CONCAT('第',CAST(CAST(A.term AS int) AS varchar(2)),'期')
                        END
                    ) as 班別名稱 ,
                    CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                    A.day as 天數,
                    A.dayhour as 每日上課時數 ,
                    A.regcnt as 應到,
                    A.passcnt as 實到,
                    '' as 報到率,
                    A.endcnt as 結業,
                    '' as 結業率,
                    '' as 人‧天數,
                    '' as 人‧時數,
                    '' as 辦班組室,
                    '' as 辦班人員,
                    '' as 研習規劃, 
                    '' as 學習輔導,
                    '' as 講師授課,
                    '' as 行政服務,
                    '' as 整體評價,
                    '' as 公務執行,
                    '' as 學習投入,
                    '' as 總體滿意度,
                    '' as 回收率,
                    A.class,
                    A.term,
                    A.sdate
                
                    FROM t26tb A LEFT JOIN t25tb B ON A.class=B.class 
                    WHERE LEFT(A.edate,5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth."
                    )
                    UNION ALL
                    (SELECT
                    '' as 院區,
                    CONCAT(A.class,A.term) as 班別代碼,
                    '委外辦理' as 班別類型,
                    (CASE WHEN A.term = '' THEN '' ELSE CONCAT('第',CAST(CAST(A.term AS int) AS varchar(2)),'期') END) as 班別名稱 ,
                    CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                    A.trainday as 天數,
                    A.trainhour as  每日上課時數,
                    A.regcnt as 應到,
                    A.passcnt as 實到,
                    '' as 報到率,
                    A.endcnt as 結業,
                    '' as 結業率,
                    '' as 人‧天數,
                    '' as 人‧時數,
                    '' as 辦班組室,
                    '' as 辦班人員,
                    '' as 研習規劃, 
                    '' as 學習輔導,
                    '' as 講師授課,
                    '' as 行政服務,
                    '' as 整體評價,
                    '' as 公務執行,
                    '' as 學習投入,
                    '' as 總體滿意度,
                    '' as 回收率,
                    A.class,
                    A.term,
                    A.sdate
                    
                    FROM t66tb A
                    WHERE LEFT(A.edate,5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth."
                    ) 
                )as result
                ORDER BY sdate,class,term"; 

                $dataArr=json_decode(json_encode(DB::select($sql)), true);

                if($dataArr==[]){
                    // $objPHPExcel->removeSheetByIndex($sheet_id);
                    // if($sheet_id>=1){
                    //     $sheet_id-- ;
                    // }          
                    continue;          
                }

                $clonedWorksheet= clone $objPHPExcel->getSheet(0);
                $indexname=$cv;
                $clonedWorksheet->setTitle($indexname);
                $objPHPExcel->addSheet($clonedWorksheet);
                $sheet_id++;
                $objgetSheet=$objPHPExcel->getSheet($sheet_id);

                $objgetSheet->getHeaderFooter()->setOddHeader('&"標楷體"&B '.$year.'年度'.$month.'月訓練績效報表('.$cbranch.')');  

                for($i=0; $i<sizeof($dataArr); $i++){
                    $arraykeys=array_keys((array)$dataArr[$i]);
                    for($k=0; $k<sizeof($arraykeys); $k++){
                        //fill values & formula =IF(J3>0,IF(E3>0,(J3*E3),""),"")
                        if($k==4){
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),str_replace("~", "", $dataArr[$i][$arraykeys[$k]]));
                        }elseif($k==9){
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(I'.($i+3).'/H'.($i+3).'),""),"")');
                        }elseif($k==11){
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(K'.($i+3).'/I'.($i+3).'),""),"")');
                        }elseif($k==12){
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(K'.($i+3).'*F'.($i+3).'),""),"")');
                        }elseif($k==13){
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(K'.($i+3).'*G'.($i+3).'),""),"")');
                        }else{
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),$dataArr[$i][$arraykeys[$k]]);
                        }
                    }        
                }        
                // <24
                $rownum=sizeof($dataArr)<24?23:sizeof($dataArr);
                //sum of columns 6,7,8,9,11,13,14 =SUM(G3:G15)
                for($i=6;$i<15;$i++)
                {
                    if(($i==10) ){}elseif(($i==12)){}else{
                            $objgetSheet->setCellValue($this->getNameFromNumber($i).($rownum+3),
                            '=SUM('.$this->getNameFromNumber($i).'3:'.$this->getNameFromNumber($i).($rownum+2).')');
                    }
                }
                
                //avg,10,12,13,14,16-22
                $objgetSheet->setCellValue($this->getNameFromNumber(10).($rownum+4),'=(I'.($rownum+3).'/H'.($rownum+3).')');
                $objgetSheet->setCellValue($this->getNameFromNumber(11).($rownum+4), '=(K'.($rownum+3).'/I'.($rownum+3).')');

                for($i=10;$i<23;$i++)
                {
                    if(($i==11) ){}elseif(($i==15)){}else{
                        $objgetSheet->setCellValue($this->getNameFromNumber($i).($rownum+4),
                        '=IF(COUNT('.$this->getNameFromNumber($i).'3:'.$this->getNameFromNumber($i).($rownum+2).')>0,AVERAGE('.$this->getNameFromNumber($i).'3:'.$this->getNameFromNumber($i).($rownum+2).'),"")');
                    }    
                }

                //人˙天數(1天6小時)12
                $objgetSheet->setCellValue('M'.($rownum+5),'=(N'.($rownum+3).'/6)');

                //set column A
                $objgetSheet->setCellValue('A'.($rownum+3), '加總('.strval(sizeof($dataArr)).'班)');
                $objgetSheet->setCellValue('A'.($rownum+4), '平均值');
                //objgetSheet->mergeCells('A1:B1');
                $objgetSheet->mergeCells('A'.($rownum+5).':B'.($rownum+5));
                $objgetSheet->setCellValue('A'.($rownum+5),'人˙天數(1天6小時)');

                //apply format
                $objgetSheet->getStyle('A1:'.$this->getNameFromNumber(sizeof($arraykeys)).($rownum+5))->applyFromArray($styleArray);
                $objgetSheet->getStyle('L'.($rownum+5).":V".($rownum+5))->getNumberFormat()->applyFromArray($numberformat);
                for($r=sizeof($dataArr);$r<$rownum+1;$r++)
                    $objgetSheet->getRowDimension($r+2)->setRowHeight(31.4);

                if($checkboxsatisfaction=="1" && $checkboxservice!="2"){
                    $outputname="訓練績效報表-含辦班人員及滿意度";
                    $objgetSheet->getColumnDimension('W')->setVisible(false);
                }
                if($checkboxsatisfaction!="1" && $checkboxservice!="2"){
                    $outputname="訓練績效報表";
                    for($i=15;$i<24;$i++)
                        $objgetSheet->getColumnDimension($this->getNameFromNumber($i))->setVisible(false);

                }

            }

            $objPHPExcel->removeSheetByIndex(0);

            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),$outputname);
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 

        }elseif($classes!="A0" && $department=="A0"){ //出七個組室報表
            // 範本檔案名稱
            $fileName = 'D11';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel

            $objPHPExcel = IOFactory::load($filePath);
            $sheet_id = 0;
            foreach($departmentArr as $key=>$dv){
                $sql=
                "SELECT 班別代碼,院區, 班別類型, 班別名稱, 起訖日期, 天數, 每日上課時數, 應到, 實到, 報到率, 結業,
                    結業率, 人‧天數, 人‧時數,辦班組室, 辦班人員, 研習規劃, 學習投入, 學習輔導, 講師授課, 總體滿意度, 回收率, 行政服務
                FROM(
                    (SELECT
                    (CASE WHEN B.branch = '1' THEN '臺北院區' WHEN B.branch = '2' THEN '南投院區' ELSE '' END) as 院區 ,
                    CONCAT(A.class,A.term) as 班別代碼 ,
                    '自行辦理' as 班別類型,
                    CONCAT(RTRIM(B.name),'第',CAST(CAST(A.term AS int) AS varchar(2)),'期') as 班別名稱 ,
                    CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                    B.trainday as 天數,
                    B.classhr as 每日上課時數 ,
                    (CASE WHEN B.cntflag='1' THEN A.quota WHEN B.cntflag='2' THEN A.regcnt ELSE 0 END) as 應到 ,
                    (CASE WHEN B.cntflag='1' THEN C.實到 WHEN B.cntflag='2' THEN A.passcnt ELSE 0 END) as 實到 ,
                    0 as 報到率 ,
                    (CASE WHEN B.cntflag='1' THEN C.結業 WHEN B.cntflag='2' THEN A.endcnt ELSE 0 END) as 結業 ,
                    0 as 結業率 ,
                    0 as 人‧天數,
                    0 as 人‧時數 ,
                    A.section as 辦班組室,
                    IFNULL(E.username,A.sponsor) as 辦班人員,
                    D.conper as 研習規劃,
                    D.worper as 學習輔導,
                    D.teaper as 講師授課,
                    D.envper as 行政服務,
                    D.whoper as 整體評價,
                    D.offper as 公務執行,
                    D.attper as 學習投入,
                    (CASE
                        WHEN '".$checkboxservice."' = '1' THEN D.totper /*含【行政服務】*/
                        WHEN cast(LEFT(A.class,3) as int) >= 96 THEN D.totper /*96年以後的【總平均】不用重算*/
                        ELSE cast(ROUND( (D.conper+D.worper+D.teaper+D.whoper)/4,2 ) as decimal(6,2)) /* 【總平均】 = (【研習規劃】+【學習輔導】+【講師授課】+【整體評價】)/4 */
                        END) as 總體滿意度,
                    F.recrate as 回收率,
                    A.class,
                    A.term,
                    A.sdate

                    FROM t04tb as A INNER JOIN t01tb as B ON A.class = B.class LEFT JOIN 
                    (SELECT class,term,
                    COUNT(CASE WHEN status <>'2' THEN 1 ELSE NULL END) as 實到 ,
                    COUNT(CASE WHEN status = '1' THEN 1 ELSE NULL END) as 結業
                    FROM t13tb 
                    WHERE LEFT(class,3) BETWEEN ".$startYear." AND ".$endYear."
                    GROUP BY class,term
                    ORDER BY class,term) as C
                    ON A.class = C.class
                    AND A.term = C.term
                    LEFT JOIN t57tb as D 
                    ON A.class  = D.class
                    AND A.term  = D.term
                    AND D.times = ''
                    LEFT JOIN m09tb as E 
                    ON A.sponsor = E.userid
                    LEFT JOIN 
                    (SELECT class, term, ROUND( SUM(onerecrate)*100/CAST(COUNT(*) AS decimal(6,3)) ,2) AS recrate FROM
                    (SELECT Aa.class,Aa.term,Aa.times,Aa.copy,
                    IFNULL(SUM(Bb.cnt),0) AS cnt,
                    IFNULL(SUM(Bb.cnt),0)/CAST(Aa.copy AS decimal(6,3)) AS onerecrate
                    FROM t53tb as Aa
                    INNER JOIN 
                    ((SELECT class, term, times, COUNT(*) AS cnt FROM t55tb GROUP BY class,term,times)
                    UNION ALL (SELECT class, term, times, COUNT(*) AS cnt FROM t72tb GROUP BY class,term,times)
                    UNION ALL (SELECT class, term, times, COUNT(*) AS cnt FROM t75tb GROUP BY class,term,times)
                    UNION ALL (SELECT  class, term, times, COUNT(*) AS cnt  FROM t91tb GROUP BY class,term,times)
                    UNION ALL (SELECT class, term, times, COUNT(*) AS cnt FROM t95tb GROUP BY class,term,times )
                    ORDER BY class,term,times
                    ) as Bb
                    ON Aa.class = Bb.class AND Aa.term = Bb.term AND Aa.times = Bb.times
                    WHERE LEFT(Aa.class,3) IN (".$yearperiod.") AND Aa.copy > 0                 
                    GROUP BY Aa.class, Aa.term, Aa.times, Aa.copy
                    ORDER BY Aa.class, Aa.term, Aa.times) as Cc
                    GROUP BY class, term
                    ORDER BY class, term) as F
                    ON A.class = F.class
                    AND A.term = F.term
                    WHERE
                        LEFT(
                        (CASE
                        /*【t01tb 班別基本資料檔】classified  學習性質 1:數位 2:實體3:混成 若t01tb.classified='3'，則edate為 t04tb.edate加14天 dbo.ufn_date_to_cdate 西元年轉民國年 dbo.ufn_cdate_to_date 民國年轉西元年*/
                        WHEN B.classified='3' THEN 
                        
                            CONCAT(
                            (CASE WHEN LEFT(A.edate,3)*1<100 THEN
                            CONCAT('0',CAST(LEFT(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),4)*1 -1911 as CHAR))
                            ELSE
                            CAST(LEFT(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),4)*1 -1911 as CHAR)
                            END),
                            SUBSTRING(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),6,2), 
                            RIGHT(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),2) 
                            )
                        
                        ELSE A.edate END),5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth." ";

                    if($training!="0")
                        $sql.="AND B.traintype = '".$training."' "; 
                    if($staff!="0")
                        $sql.="AND A.sponsor = '".$staff."' ";  
                    if($section!="0")
                        $sql.="AND B.signin ".$section." ";
                    if($branch!="0")
                        $sql.="AND B.branch ='".$branch."' ";
                    if($process!="0")
                        $sql.="AND B.process  ='".$process."' ";
                    if($days!="")
                        $sql.="AND B.trainday  >=".$days." ";    
                    if($classes!="0")
                        $sql.="AND B.type  ='".$classes."' ";     

                    $sql.="AND A.section = '".$dv."' "; 
        
                    $sql.=")
                    UNION ALL
                    (SELECT
                    '' as 院區,
                    A.class as 班別代碼,
                    RTRIM(B.name) as 班別類型,
                    CONCAT(RTRIM(B.name),(
                        CASE
                        WHEN A.term = '' THEN ''
                        ELSE CONCAT('第',CAST(CAST(A.term AS int) AS varchar(2)),'期')
                        END
                        )) as 班別名稱,
                    CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                    A.day as 天數 ,
                    A.hour as 每日上課時數 ,
                    A.regcnt as 應到,
                    A.passcnt as  實到 ,
                    '' as 報到率,
                    A.endcnt as 結業 ,
                    '' as 結業率,
                    '' as 人‧天數,
                    '' as 人‧時數,
                    '' as 辦班組室,
                    '' as 辦班人員,
                    '' as 研習規劃, 
                    '' as 學習輔導,
                    '' as 講師授課,
                    '' as 行政服務,
                    '' as 整體評價,
                    '' as 公務執行,
                    '' as 學習投入,
                    '' as 總體滿意度,
                    '' as 回收率,
                    A.class,
                    '' as  term,
                    A.sdate

                    FROM t24tb A 
                    LEFT JOIN m15tb B 
                    ON A.school = B.school
                    WHERE LEFT(A.edate,5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth."
                    )
                    UNION ALL
                    (SELECT
                    '' as 院區,
                    CONCAT(A.class,A.term) as 班別代碼,
                    (CASE A.class
                        WHEN '091001' THEN '巨匠電腦股份有限公司'
                        WHEN '091002' THEN '巨匠電腦股份有限公司'
                        WHEN '091003' THEN '巨匠電腦股份有限公司'
                        WHEN '091004' THEN '巨匠電腦股份有限公司'
                        WHEN '091005' THEN '巨匠電腦股份有限公司'
                        WHEN '091006' THEN '巨匠電腦股份有限公司'
                        WHEN '091007' THEN '巨匠電腦股份有限公司'
                        ELSE IFNULL((SELECT RTRIM(name) FROM s01tb WHERE  code = '".$startYear."'),'')
                    END) as 班別類型,
                    CONCAT(RTRIM(B.name),
                        CASE
                        WHEN A.term = '' THEN ''
                        ELSE CONCAT('第',CAST(CAST(A.term AS int) AS varchar(2)),'期')
                        END
                    ) as 班別名稱 ,
                    CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                    A.day as 天數,
                    A.dayhour as 每日上課時數 ,
                    A.regcnt as 應到,
                    A.passcnt as 實到,
                    '' as 報到率,
                    A.endcnt as 結業,
                    '' as 結業率,
                    '' as 人‧天數,
                    '' as 人‧時數,
                    '' as 辦班組室,
                    '' as 辦班人員,
                    '' as 研習規劃, 
                    '' as 學習輔導,
                    '' as 講師授課,
                    '' as 行政服務,
                    '' as 整體評價,
                    '' as 公務執行,
                    '' as 學習投入,
                    '' as 總體滿意度,
                    '' as 回收率,
                    A.class,
                    A.term,
                    A.sdate
                
                    FROM t26tb A LEFT JOIN t25tb B ON A.class=B.class 
                    WHERE LEFT(A.edate,5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth."
                    )
                    UNION ALL
                    (SELECT
                    '' as 院區,
                    CONCAT(A.class,A.term) as 班別代碼,
                    '委外辦理' as 班別類型,
                    (CASE WHEN A.term = '' THEN '' ELSE CONCAT('第',CAST(CAST(A.term AS int) AS varchar(2)),'期') END) as 班別名稱 ,
                    CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                    A.trainday as 天數,
                    A.trainhour as  每日上課時數,
                    A.regcnt as 應到,
                    A.passcnt as 實到,
                    '' as 報到率,
                    A.endcnt as 結業,
                    '' as 結業率,
                    '' as 人‧天數,
                    '' as 人‧時數,
                    '' as 辦班組室,
                    '' as 辦班人員,
                    '' as 研習規劃, 
                    '' as 學習輔導,
                    '' as 講師授課,
                    '' as 行政服務,
                    '' as 整體評價,
                    '' as 公務執行,
                    '' as 學習投入,
                    '' as 總體滿意度,
                    '' as 回收率,
                    A.class,
                    A.term,
                    A.sdate
                    
                    FROM t66tb A
                    WHERE LEFT(A.edate,5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth."
                    ) 
                )as result
                ORDER BY sdate,class,term"; 

                $dataArr=json_decode(json_encode(DB::select($sql)), true);

                if($dataArr==[])
                    continue;          

                $clonedWorksheet= clone $objPHPExcel->getSheet(0);
                $indexname=$dv;
                $clonedWorksheet->setTitle($indexname);
                $objPHPExcel->addSheet($clonedWorksheet);
                $sheet_id++;
                $objgetSheet=$objPHPExcel->getSheet($sheet_id);
                
                $objgetSheet->getHeaderFooter()->setOddHeader('&"標楷體"&B '.$year.'年度'.$month.'月訓練績效報表('.$cbranch.')');  

                for($i=0; $i<sizeof($dataArr); $i++){
                    $arraykeys=array_keys((array)$dataArr[$i]);
                    for($k=0; $k<sizeof($arraykeys); $k++){
                        //fill values & formula =IF(J3>0,IF(E3>0,(J3*E3),""),"")
                        if($k==4){
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),str_replace("~", "", $dataArr[$i][$arraykeys[$k]]));
                        }elseif($k==9){
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(I'.($i+3).'/H'.($i+3).'),""),"")');
                        }elseif($k==11){
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(K'.($i+3).'/I'.($i+3).'),""),"")');
                        }elseif($k==12){
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(K'.($i+3).'*F'.($i+3).'),""),"")');
                        }elseif($k==13){
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(K'.($i+3).'*G'.($i+3).'),""),"")');
                        }else{
                            $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),$dataArr[$i][$arraykeys[$k]]);
                        }
                    }        
                }        
                // <24
                $rownum=sizeof($dataArr)<24?23:sizeof($dataArr);
                //sum of columns 6,7,8,9,11,13,14 =SUM(G3:G15)
                for($i=6;$i<15;$i++)
                {
                    if(($i==10) ){}elseif(($i==12)){}else{
                            $objgetSheet->setCellValue($this->getNameFromNumber($i).($rownum+3),
                            '=SUM('.$this->getNameFromNumber($i).'3:'.$this->getNameFromNumber($i).($rownum+2).')');
                    }
                }
                
                //avg,10,12,13,14,16-22
                $objgetSheet->setCellValue($this->getNameFromNumber(10).($rownum+4),'=(I'.($rownum+3).'/H'.($rownum+3).')');
                $objgetSheet->setCellValue($this->getNameFromNumber(11).($rownum+4), '=(K'.($rownum+3).'/I'.($rownum+3).')');

                for($i=10;$i<23;$i++)
                {
                    if(($i==11) ){}elseif(($i==15)){}else{
                        $objgetSheet->setCellValue($this->getNameFromNumber($i).($rownum+4),
                        '=IF(COUNT('.$this->getNameFromNumber($i).'3:'.$this->getNameFromNumber($i).($rownum+2).')>0,AVERAGE('.$this->getNameFromNumber($i).'3:'.$this->getNameFromNumber($i).($rownum+2).'),"")');
                    }    
                }

                //人˙天數(1天6小時)12
                $objgetSheet->setCellValue('M'.($rownum+5),'=(N'.($rownum+3).'/6)');

                //set column A
                $objgetSheet->setCellValue('A'.($rownum+3), '加總('.strval(sizeof($dataArr)).'班)');
                $objgetSheet->setCellValue('A'.($rownum+4), '平均值');
                //objgetSheet->mergeCells('A1:B1');
                $objgetSheet->mergeCells('A'.($rownum+5).':B'.($rownum+5));
                $objgetSheet->setCellValue('A'.($rownum+5),'人˙天數(1天6小時)');

                //apply format
                $objgetSheet->getStyle('A1:'.$this->getNameFromNumber(sizeof($arraykeys)).($rownum+5))->applyFromArray($styleArray);
                $objgetSheet->getStyle('L'.($rownum+5).":V".($rownum+5))->getNumberFormat()->applyFromArray($numberformat);
                for($r=sizeof($dataArr);$r<$rownum+1;$r++)
                    $objgetSheet->getRowDimension($r+2)->setRowHeight(31.4);

                if($checkboxsatisfaction=="1" && $checkboxservice!="2"){
                    $outputname="訓練績效報表-含辦班人員及滿意度";
                    $objgetSheet->getColumnDimension('W')->setVisible(false);
                }
                if($checkboxsatisfaction!="1" && $checkboxservice!="2"){
                    $outputname="訓練績效報表";
                    for($i=15;$i<24;$i++)
                        $objgetSheet->getColumnDimension($this->getNameFromNumber($i))->setVisible(false);
                }
            }

            $objPHPExcel->removeSheetByIndex(0);
            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"$outputname");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 
            

        }elseif($classes=="A0" && $department=="A0"){ //出訓練計畫四大類下七個組室的報表

            // 範本檔案名稱
            $fileName = 'D11';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel
            $objPHPExcel = IOFactory::load($filePath);
            $sheet_id = 0;

            foreach($classesArr as $ckey=>$cv){
                foreach($departmentArr as $dkey=>$dv){
                    $sql=
                    "SELECT 班別代碼,院區, 班別類型, 班別名稱, 起訖日期, 天數, 每日上課時數, 應到, 實到, 報到率, 結業,
                    結業率, 人‧天數, 人‧時數,辦班組室, 辦班人員, 研習規劃, 學習投入, 學習輔導, 講師授課, 總體滿意度, 回收率, 行政服務
                    FROM(
                    (SELECT
                    (CASE WHEN B.branch = '1' THEN '臺北院區' WHEN B.branch = '2' THEN '南投院區' ELSE '' END) as 院區 ,
                    CONCAT(A.class,A.term) as 班別代碼 ,
                    '自行辦理' as 班別類型,
                    CONCAT(RTRIM(B.name),'第',CAST(CAST(A.term AS int) AS varchar(2)),'期') as 班別名稱 ,
                    CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                    B.trainday as 天數,
                    B.classhr as 每日上課時數 ,
                    (CASE WHEN B.cntflag='1' THEN A.quota WHEN B.cntflag='2' THEN A.regcnt ELSE 0 END) as 應到 ,
                    (CASE WHEN B.cntflag='1' THEN C.實到 WHEN B.cntflag='2' THEN A.passcnt ELSE 0 END) as 實到 ,
                    0 as 報到率 ,
                    (CASE WHEN B.cntflag='1' THEN C.結業 WHEN B.cntflag='2' THEN A.endcnt ELSE 0 END) as 結業 ,
                    0 as 結業率 ,
                    0 as 人‧天數,
                    0 as 人‧時數 ,
                    A.section as 辦班組室,
                    IFNULL(E.username,A.sponsor) as 辦班人員,
                    D.conper as 研習規劃,
                    D.worper as 學習輔導,
                    D.teaper as 講師授課,
                    D.envper as 行政服務,
                    D.whoper as 整體評價,
                    D.offper as 公務執行,
                    D.attper as 學習投入,
                    (CASE
                        WHEN '".$checkboxservice."' = '1' THEN D.totper /*含【行政服務】*/
                        WHEN cast(LEFT(A.class,3) as int) >= 96 THEN D.totper /*96年以後的【總平均】不用重算*/
                        ELSE cast(ROUND( (D.conper+D.worper+D.teaper+D.whoper)/4,2 ) as decimal(6,2)) /* 【總平均】 = (【研習規劃】+【學習輔導】+【講師授課】+【整體評價】)/4 */
                        END) as 總體滿意度,
                    F.recrate as 回收率,
                    A.class,
                    A.term,
                    A.sdate

                    FROM t04tb as A INNER JOIN t01tb as B ON A.class = B.class LEFT JOIN 
                    (SELECT class,term,
                    COUNT(CASE WHEN status <>'2' THEN 1 ELSE NULL END) as 實到 ,
                    COUNT(CASE WHEN status = '1' THEN 1 ELSE NULL END) as 結業
                    FROM t13tb 
                    WHERE LEFT(class,3) BETWEEN ".$startYear." AND ".$endYear."
                    GROUP BY class,term
                    ORDER BY class,term) as C
                    ON A.class = C.class
                    AND A.term = C.term
                    LEFT JOIN t57tb as D 
                    ON A.class  = D.class
                    AND A.term  = D.term
                    AND D.times = ''
                    LEFT JOIN m09tb as E 
                    ON A.sponsor = E.userid
                    LEFT JOIN 
                    (SELECT class, term, ROUND( SUM(onerecrate)*100/CAST(COUNT(*) AS decimal(6,3)) ,2) AS recrate FROM
                    (SELECT Aa.class,Aa.term,Aa.times,Aa.copy,
                    IFNULL(SUM(Bb.cnt),0) AS cnt,
                    IFNULL(SUM(Bb.cnt),0)/CAST(Aa.copy AS decimal(6,3)) AS onerecrate
                    FROM t53tb as Aa
                    INNER JOIN 
                    ((SELECT class, term, times, COUNT(*) AS cnt FROM t55tb GROUP BY class,term,times)
                    UNION ALL (SELECT class, term, times, COUNT(*) AS cnt FROM t72tb GROUP BY class,term,times)
                    UNION ALL (SELECT class, term, times, COUNT(*) AS cnt FROM t75tb GROUP BY class,term,times)
                    UNION ALL (SELECT  class, term, times, COUNT(*) AS cnt  FROM t91tb GROUP BY class,term,times)
                    UNION ALL (SELECT class, term, times, COUNT(*) AS cnt FROM t95tb GROUP BY class,term,times )
                    ORDER BY class,term,times
                    ) as Bb
                    ON Aa.class = Bb.class AND Aa.term = Bb.term AND Aa.times = Bb.times
                    WHERE LEFT(Aa.class,3) IN (".$yearperiod.") AND Aa.copy > 0                 
                    GROUP BY Aa.class, Aa.term, Aa.times, Aa.copy
                    ORDER BY Aa.class, Aa.term, Aa.times) as Cc
                    GROUP BY class, term
                    ORDER BY class, term) as F
                    ON A.class = F.class
                    AND A.term = F.term
                    WHERE
                        LEFT(
                        (CASE
                        /*【t01tb 班別基本資料檔】classified  學習性質 1:數位 2:實體3:混成 若t01tb.classified='3'，則edate為 t04tb.edate加14天 dbo.ufn_date_to_cdate 西元年轉民國年 dbo.ufn_cdate_to_date 民國年轉西元年*/
                        WHEN B.classified='3' THEN 
                        
                            CONCAT(
                            (CASE WHEN LEFT(A.edate,3)*1<100 THEN
                            CONCAT('0',CAST(LEFT(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),4)*1 -1911 as CHAR))
                            ELSE
                            CAST(LEFT(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),4)*1 -1911 as CHAR)
                            END),
                            SUBSTRING(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),6,2), 
                            RIGHT(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY),2) 
                            )
                        
                        ELSE A.edate END),5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth." ";

                    if($training!="0")
                        $sql.="AND B.traintype = '".$training."' "; 
                    if($staff!="0")
                        $sql.="AND A.sponsor = '".$staff."' ";  
                    if($section!="0")
                        $sql.="AND B.signin ".$section." ";
                    if($branch!="0")
                        $sql.="AND B.branch ='".$branch."' ";
                    if($process!="0")
                        $sql.="AND B.process  ='".$process."' ";
                    if($days!="")
                        $sql.="AND B.trainday  >=".$days." ";    
                  
                    $sql.="AND A.section = '".$dv."' "; 
                    $sql.="AND B.type  ='".$ckey."' ";  
        
                    $sql.=")
                    UNION ALL
                    (SELECT
                    '' as 院區,
                    A.class as 班別代碼,
                    RTRIM(B.name) as 班別類型,
                    CONCAT(RTRIM(B.name),(
                        CASE
                        WHEN A.term = '' THEN ''
                        ELSE CONCAT('第',CAST(CAST(A.term AS int) AS varchar(2)),'期')
                        END
                        )) as 班別名稱,
                    CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                    A.day as 天數 ,
                    A.hour as 每日上課時數 ,
                    A.regcnt as 應到,
                    A.passcnt as  實到 ,
                    '' as 報到率,
                    A.endcnt as 結業 ,
                    '' as 結業率,
                    '' as 人‧天數,
                    '' as 人‧時數,
                    '' as 辦班組室,
                    '' as 辦班人員,
                    '' as 研習規劃, 
                    '' as 學習輔導,
                    '' as 講師授課,
                    '' as 行政服務,
                    '' as 整體評價,
                    '' as 公務執行,
                    '' as 學習投入,
                    '' as 總體滿意度,
                    '' as 回收率,
                    A.class,
                    '' as  term,
                    A.sdate

                    FROM t24tb A 
                    LEFT JOIN m15tb B 
                    ON A.school = B.school
                    WHERE LEFT(A.edate,5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth."
                    )
                    UNION ALL
                    (SELECT
                    '' as 院區,
                    CONCAT(A.class,A.term) as 班別代碼,
                    (CASE A.class
                        WHEN '091001' THEN '巨匠電腦股份有限公司'
                        WHEN '091002' THEN '巨匠電腦股份有限公司'
                        WHEN '091003' THEN '巨匠電腦股份有限公司'
                        WHEN '091004' THEN '巨匠電腦股份有限公司'
                        WHEN '091005' THEN '巨匠電腦股份有限公司'
                        WHEN '091006' THEN '巨匠電腦股份有限公司'
                        WHEN '091007' THEN '巨匠電腦股份有限公司'
                        ELSE IFNULL((SELECT RTRIM(name) FROM s01tb WHERE  code = '".$startYear."'),'')
                    END) as 班別類型,
                    CONCAT(RTRIM(B.name),
                        CASE
                        WHEN A.term = '' THEN ''
                        ELSE CONCAT('第',CAST(CAST(A.term AS int) AS varchar(2)),'期')
                        END
                    ) as 班別名稱 ,
                    CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                    A.day as 天數,
                    A.dayhour as 每日上課時數 ,
                    A.regcnt as 應到,
                    A.passcnt as 實到,
                    '' as 報到率,
                    A.endcnt as 結業,
                    '' as 結業率,
                    '' as 人‧天數,
                    '' as 人‧時數,
                    '' as 辦班組室,
                    '' as 辦班人員,
                    '' as 研習規劃, 
                    '' as 學習輔導,
                    '' as 講師授課,
                    '' as 行政服務,
                    '' as 整體評價,
                    '' as 公務執行,
                    '' as 學習投入,
                    '' as 總體滿意度,
                    '' as 回收率,
                    A.class,
                    A.term,
                    A.sdate
                
                    FROM t26tb A LEFT JOIN t25tb B ON A.class=B.class 
                    WHERE LEFT(A.edate,5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth."
                    )
                    UNION ALL
                    (SELECT
                    '' as 院區,
                    CONCAT(A.class,A.term) as 班別代碼,
                    '委外辦理' as 班別類型,
                    (CASE WHEN A.term = '' THEN '' ELSE CONCAT('第',CAST(CAST(A.term AS int) AS varchar(2)),'期') END) as 班別名稱 ,
                    CONCAT('起',LEFT(A.sdate,3),'.',SUBSTRING(A.sdate,4,2),'.',RIGHT(A.sdate,2),'~','迄',LEFT(A.edate,3),'.',SUBSTRING(A.edate,4,2),'.',RIGHT(A.edate,2)) as 起訖日期 ,
                    A.trainday as 天數,
                    A.trainhour as  每日上課時數,
                    A.regcnt as 應到,
                    A.passcnt as 實到,
                    '' as 報到率,
                    A.endcnt as 結業,
                    '' as 結業率,
                    '' as 人‧天數,
                    '' as 人‧時數,
                    '' as 辦班組室,
                    '' as 辦班人員,
                    '' as 研習規劃, 
                    '' as 學習輔導,
                    '' as 講師授課,
                    '' as 行政服務,
                    '' as 整體評價,
                    '' as 公務執行,
                    '' as 學習投入,
                    '' as 總體滿意度,
                    '' as 回收率,
                    A.class,
                    A.term,
                    A.sdate
                    
                    FROM t66tb A
                    WHERE LEFT(A.edate,5) BETWEEN ".$startYear.$startMonth." AND ".$endYear.$endMonth."
                    ) 
                    )as result
                    ORDER BY sdate,class,term"; 

                    $dataArr=json_decode(json_encode(DB::select($sql)), true);

                    if($dataArr==[])
                        continue;          

                    $clonedWorksheet= clone $objPHPExcel->getSheet(0);
                    $indexname=$cv."_".$dv;
                    $clonedWorksheet->setTitle($indexname);
                    $objPHPExcel->addSheet($clonedWorksheet);
                    $sheet_id++;
                    $objgetSheet=$objPHPExcel->getSheet($sheet_id);
                    
                    $objgetSheet->getHeaderFooter()->setOddHeader('&"標楷體"&B '.$year.'年度'.$month.'月訓練績效報表('.$cbranch.')');  

                    for($i=0; $i<sizeof($dataArr); $i++){
                        $arraykeys=array_keys((array)$dataArr[$i]);
                        for($k=0; $k<sizeof($arraykeys); $k++){
                            //fill values & formula =IF(J3>0,IF(E3>0,(J3*E3),""),"")
                            if($k==4){
                                $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),str_replace("~", "", $dataArr[$i][$arraykeys[$k]]));
                            }elseif($k==9){
                                $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(I'.($i+3).'/H'.($i+3).'),""),"")');
                            }elseif($k==11){
                                $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(K'.($i+3).'/I'.($i+3).'),""),"")');
                            }elseif($k==12){
                                $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(K'.($i+3).'*F'.($i+3).'),""),"")');
                            }elseif($k==13){
                                $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),'=IF(K'.($i+3).'>0,IF(F'.($i+3).'>0,(K'.($i+3).'*G'.($i+3).'),""),"")');
                            }else{
                                $objgetSheet->setCellValue($this->getNameFromNumber($k+1).($i+3),$dataArr[$i][$arraykeys[$k]]);
                            }
                        }        
                    }        
                    // <24
                    $rownum=sizeof($dataArr)<24?23:sizeof($dataArr);
                    //sum of columns 6,7,8,9,11,13,14 =SUM(G3:G15)
                    for($i=6;$i<15;$i++)
                    {
                        if(($i==10) ){}elseif(($i==12)){}else{
                                $objgetSheet->setCellValue($this->getNameFromNumber($i).($rownum+3),
                                '=SUM('.$this->getNameFromNumber($i).'3:'.$this->getNameFromNumber($i).($rownum+2).')');
                        }
                    }
                    
                    //avg,10,12,13,14,16-22
                    $objgetSheet->setCellValue($this->getNameFromNumber(10).($rownum+4),'=(I'.($rownum+3).'/H'.($rownum+3).')');
                    $objgetSheet->setCellValue($this->getNameFromNumber(11).($rownum+4), '=(K'.($rownum+3).'/I'.($rownum+3).')');

                    for($i=10;$i<23;$i++)
                    {
                        if(($i==11) ){}elseif(($i==15)){}else{
                            $objgetSheet->setCellValue($this->getNameFromNumber($i).($rownum+4),
                            '=IF(COUNT('.$this->getNameFromNumber($i).'3:'.$this->getNameFromNumber($i).($rownum+2).')>0,AVERAGE('.$this->getNameFromNumber($i).'3:'.$this->getNameFromNumber($i).($rownum+2).'),"")');
                        }    
                    }

                    //人˙天數(1天6小時)12
                    $objgetSheet->setCellValue('M'.($rownum+5),'=(N'.($rownum+3).'/6)');

                    //set column A
                    $objgetSheet->setCellValue('A'.($rownum+3), '加總('.strval(sizeof($dataArr)).'班)');
                    $objgetSheet->setCellValue('A'.($rownum+4), '平均值');
                    //objgetSheet->mergeCells('A1:B1');
                    $objgetSheet->mergeCells('A'.($rownum+5).':B'.($rownum+5));
                    $objgetSheet->setCellValue('A'.($rownum+5),'人˙天數(1天6小時)');

                    //apply format
                    $objgetSheet->getStyle('A1:'.$this->getNameFromNumber(sizeof($arraykeys)).($rownum+5))->applyFromArray($styleArray);
                    $objgetSheet->getStyle('L'.($rownum+5).":V".($rownum+5))->getNumberFormat()->applyFromArray($numberformat);
                    for($r=sizeof($dataArr);$r<$rownum+1;$r++)
                        $objgetSheet->getRowDimension($r+2)->setRowHeight(31.4);

                   
                    if($checkboxsatisfaction=="1" && $checkboxservice!="2"){
                        $outputname="訓練績效報表-含辦班人員及滿意度";
                        $objgetSheet->getColumnDimension('W')->setVisible(false);
                    }
                    if($checkboxsatisfaction!="1" && $checkboxservice!="2"){
                        $outputname="訓練績效報表";
                        for($i=15;$i<24;$i++)
                            $objgetSheet->getColumnDimension($this->getNameFromNumber($i))->setVisible(false);
                    }
                }
            }

            $objPHPExcel->removeSheetByIndex(0);
            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"$outputname");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 

        }



    }
}
