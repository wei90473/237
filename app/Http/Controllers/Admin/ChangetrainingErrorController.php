<?php
namespace App\Http\Controllers\Admin;
set_time_limit(0);

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Services\User_groupService;


class ChangetrainingErrorController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('changetraining_error', $user_group_auth)){
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
        $temp=$RptBasic->getTerms($temp[0][$arraykeys[0]]);
        $termArr=$temp;
        $temp=$RptBasic->getclasstypek();
        $class=$temp;
        $result = '';
        return view('admin/changetraining_error/list',compact('classArr','termArr' ,'result','class'));
    }

    public function getTerms(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $termArr=$RptBasic->getTerms($request->input('classes'));
        return $termArr;
    }

    public function export(Request $request)
    {
        //1.班別性質加其他2.班別期別
        $ratiocardselect = $request->input('cardselect');
        //1.異常統計2.實到統計3.各機關請假及未到訓統計
        $ratiooutputtype = $request->input('outputtype');
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $doctype = $request->input('doctype');
        if($ratiocardselect=="1"){    //1.班別性質加時間
            $sdate="";
            $edate="";

            if($request->input('sdatetw')!="" && $request->input('edatetw')!="" ){
                $sdatetw=explode("-",$request->input('sdatetw'));
                $edatetw=explode("-",$request->input('edatetw'));
                $sdate=$sdatetw[0].$sdatetw[1]. $sdatetw[2];
                $edate=$edatetw[0].$edatetw[1]. $edatetw[2];
            }

            $type=$request->input('classtype');

            if($type=="0")
            {
                $type=" ";
            }
            else{
                $type="AND EXISTS (SELECT * FROM t01tb WHERE type=".$type." AND class=A.class";
            }

            if($ratiooutputtype=="1"){  //1.異常統計
                $A2="訓練期間：自民國".$sdatetw[0]."年".$sdatetw[1]."月".$sdatetw[2]."日至".$edatetw[0]."年".$edatetw[1]."月".$edatetw[2]."日";
                $sql="#應派訓而未派訓
                SELECT
                B.organ,
                A.class,
                A.term,
                IFNULL((SELECT RTRIM(lname) FROM m13tb WHERE organ=B.organ),'') AS 主管機關,
                '' AS 服務機關,
                CONCAT(RTRIM(IFNULL((SELECT name FROM t01tb WHERE class=A.class),'')),' ''第', A.term,'期') AS 班期別,
                B.quota-COUNT(C.idno) AS 應派訓而未派訓,
                '' AS 已派訓而未到訓名單,
                '' AS 退訓名單
                FROM t04tb A
                INNER JOIN t03tb B
                ON A.class=B.class
                AND A.term=B.term
                LEFT JOIN t13tb C
                ON B.class=C.class
                AND B.term=C.term
                AND B.organ=C.organ
                WHERE
                NOT EXISTS ( SELECT NULL FROM m07tb WHERE agency=B.organ  ) #不包括 訓練機構
                #時間
                AND A.edate BETWEEN ".$sdate." AND ".$edate."
                #班別性質
                $type
                GROUP BY A.class,A.term,B.organ,B.quota
                HAVING B.quota >COUNT(C.idno)

                 UNION
                #已派訓而未到訓名單
                SELECT
                B.organ,
                A.class,
                A.term,
                IFNULL((SELECT lname FROM m13tb
                WHERE organ=B.organ),'') AS 主管機關,
                B.dept AS 服務機關,
                CONCAT(RTRIM(IFNULL((SELECT name FROM t01tb WHERE class=A.class),'')),' ','第', A.term,'期') AS 班期別,
                0 AS 應派訓而未派訓,
                IFNULL((  SELECT RTRIM(cname) FROM m02tb WHERE idno= B.idno ),'') AS 已派訓而未到訓名單,
                '' AS 退訓名單
                FROM t04tb A  INNER JOIN
                t13tb B ON
                A.class=B.class AND A.term=B.term
                WHERE B.status='2'  # 未報到
                AND NOT EXISTS ( SELECT * FROM m07tb WHERE agency=B.organ )  # 不包括訓練機構
                #時間
                AND A.edate BETWEEN ".$sdate." AND ".$edate."
                #班別性質
                $type
                #已派訓而未到訓名單>

                UNION
                #退訓名單
                SELECT
                B.organ,
                A.class,
                A.term,
                IFNULL((SELECT lname FROM m13tb WHERE organ=B.organ),'') AS 主管機關,
                B.dept AS 服務機關,
                CONCAT(RTRIM(IFNULL((SELECT name FROM t01tb WHERE class=A.class),'')),' ','第',A.term,'期') AS 班期別,
                0 AS 應派訓而未派訓,
                ''AS 已派訓而未到訓名單,
                IFNULL((SELECT RTRIM(cname) FROM m02tb WHERE idno=B.idno),'')  AS 退訓名單
                FROM t04tb A  INNER JOIN t13tb B ON A.class=B.class AND A.term=B.term
                WHERE B.status='3'    #退訓
                AND NOT EXISTS ( SELECT * FROM m07tb WHERE agency=B.organ ) #不包括 訓練機構
                #時間
                AND A.edate BETWEEN ".$sdate." AND ".$edate."
                #班別性質
                $type";

                $data=json_decode(json_encode(DB::select($sql)), true);

                if($data==[]){
                    $result="查無資料，請重新設定搜尋條件。";
                    $RptBasic = new \App\Rptlib\RptBasic();
                    $temp=$RptBasic->getclass();
                    $classArr=$temp;
                    $temp=json_decode(json_encode($temp), true);
                    $arraykeys=array_keys((array)$temp[0]);
                    $temp=$RptBasic->getTerms($temp[0][$arraykeys[0]]);
                    $termArr=$temp;
                    $temp=$RptBasic->getclasstypek();
                    $class=$temp;
                    return view('admin/changetraining_error/list',compact('classArr','termArr' ,'result','class'));
                }

                $datakey=array_keys((array)$data[0]);

                // 檔案名稱
                $fileName = 'F5B';
                //範本位置
                $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
                //讀取excel，
                $objPHPExcel = IOFactory::load($filePath);
                // fill values
                $objSheet = $objPHPExcel->getsheet(0);
                $objSheet->setCellValue('A2', $A2);

                for($i=0;$i<sizeof($data);$i++){
                    for($j=3;$j<sizeof($datakey);$j++){
                        $objSheet->setCellValue($this->getNameFromNumber($j-2).($i+4),$data[$i][$datakey[$j]]);
                        $objSheet->getRowDimension($i+4)->setRowHeight(40);
                    }
                }

                if(sizeof($data)>49){
                    $objSheet->setCellValue('A'.(sizeof($data)+4),'合計');
                    $objSheet->setCellValue('D'.(sizeof($data)+4),'=SUM(D4:D'.(sizeof($data)+3).')');
                    $objSheet->setCellValue('E'.(sizeof($data)+4),'=COUNTA(E4:E'.(sizeof($data)+3).')');
                    $objSheet->setCellValue('F'.(sizeof($data)+4),'=COUNTA(F4:F'.(sizeof($data)+3).')');
                    $objSheet->getRowDimension(sizeof($data)+4)->setRowHeight(40);
                    $objSheet->setCellValue('C'.(sizeof($data)+5),'單主位管');
                    $objSheet->setCellValue('E'.(sizeof($data)+5),'承辦人');
                    $objSheet->getRowDimension(sizeof($data)+5)->setRowHeight(40);


                    //apply borders
                    $objSheet->getStyle('A3:'.$this->getNameFromNumber(sizeof($datakey)-2).(sizeof($data)+4))->applyFromArray($styleArray);

                }else{
                    for($k=4;$k<55;$k++){
                        $objSheet->getRowDimension($k)->setRowHeight(40);
                    }
                    $objSheet->setCellValue('A53','合計');
                    $objSheet->setCellValue('D53','=SUM(D4:D52)');
                    $objSheet->setCellValue('E53','=COUNTA(E4:E52)');
                    $objSheet->setCellValue('F53','=COUNTA(F4:F52)');
                    $objSheet->setCellValue('C54','單主位管');
                    $objSheet->setCellValue('E54','承辦人');

                    //apply borders
                    $objSheet->getStyle('A3:F53')->applyFromArray($styleArray);

                }
                $RptBasic = new \App\Rptlib\RptBasic();
                $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"班期調派訓異常統計表-異常統計");
                //$obj: entity of file
                //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
                //$doctype:1.ooxml 2.odf
                //$filename:filename 
                

            }elseif($ratiooutputtype=="2"){ //2.實到統計

                $A2="訓練期間：自民國".$sdatetw[0]."年".$sdatetw[1]."月".$sdatetw[2]."日至".$edatetw[0]."年".$edatetw[1]."月".$edatetw[2]."日";

                $sql="SELECT    V.主管機關,   SUM(V.應到人數) AS 應到人數, SUM(V.報到人數+V.退訓人數) AS 實到人數,
                SUM(V.應到人數-(V.報到人數+V.退訓人數))  AS 未報到人數,
                (CASE WHEN SUM(V.應到人數)>0 THEN CAST(SUM(V.報到人數+V.退訓人數) AS float)/CAST(SUM(V.應到人數) AS float) ELSE 0 END) AS 實到率
                FROM (
                 SELECT
                 IFNULL((SELECT RTRIM(lname) FROM m13tb WHERE organ=B.organ),'') AS 主管機關,
                 B.quota AS 應到人數,
                 COUNT( CASE WHEN C.status='1' THEN 1 ELSE NULL END) AS 報到人數,
                 COUNT( CASE WHEN C.status='2' THEN 1 ELSE NULL END) AS 未報到人數,
                 COUNT( CASE WHEN C.status='3' THEN 1 ELSE NULL END) AS 退訓人數,
                 A.class,
                 A.term,
                 B.organ
                 FROM t04tb A
                 INNER JOIN t03tb B ON A.class=B.class AND A.term=B.term
                 LEFT JOIN t13tb C ON B.class=C.class AND B.term=C.term AND B.organ=C.organ
                 WHERE
                 NOT EXISTS (SELECT * FROM m07tb
                 WHERE agency=B.organ)
                AND A.edate BETWEEN ".$sdate." AND ".$edate."
                #班別性質
                $type 
                GROUP BY A.class,A.term,B.organ,B.quota
                ) V
                GROUP BY V.organ,V.主管機關
                ORDER BY V.organ  ";

                $data=json_decode(json_encode(DB::select($sql)), true);
                if($data==[]){
                    $result="查無資料，請重新設定搜尋條件。";
                    $RptBasic = new \App\Rptlib\RptBasic();
                    $temp=$RptBasic->getclass();
                    $classArr=$temp;
                    $temp=json_decode(json_encode($temp), true);
                    $arraykeys=array_keys((array)$temp[0]);
                    $temp=$RptBasic->getTerms($temp[0][$arraykeys[0]]);
                    $termArr=$temp;
                    $temp=$RptBasic->getclasstypek();
                    $class=$temp;
                    return view('admin/changetraining_error/list',compact('classArr','termArr' ,'result','class'));
                }
                $datakey=array_keys((array)$data[0]);

                // 檔案名稱
                $fileName = 'F5D';
                //範本位置
                $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
                //讀取excel，
                $objPHPExcel = IOFactory::load($filePath);

                $objSheet = $objPHPExcel->getsheet(0);
                $objSheet->setCellValue('A2', $A2);

                for($i=0;$i<sizeof($data);$i++){
                    for($j=0;$j<sizeof($datakey);$j++){
                        $objSheet->setCellValue($this->getNameFromNumber($j+1).($i+4),$data[$i][$datakey[$j]]);
                    }
                }

                //apply borders
                $objSheet->getStyle('A3:'.$this->getNameFromNumber(sizeof($datakey)).(sizeof($data)+4))->applyFromArray($styleArray);
                
                $RptBasic = new \App\Rptlib\RptBasic();
                $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"班期調派訓異常統計表-實到統計");
                //$obj: entity of file
                //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
                //$doctype:1.ooxml 2.odf
                //$filename:filename 
                
            }else{  
                //3.各機關請假及未到訓統計          
                $period="訓練期間：自民國".$sdatetw[0]."年".$sdatetw[1]."月".$sdatetw[2]."日至".$edatetw[0]."年".$edatetw[1]."月".$edatetw[2]."日";


         
                $sql="SELECT
                C.organ,
                RTRIM(D.lname) AS organ_name ,
                RTRIM(C.dept) AS dept ,
                RTRIM(C.class) AS class ,
                CONCAT(RTRIM(B.name),'第',CAST(CAST(C.term AS int) AS char),'期') AS class_term,
                RTRIM(E.cname) AS cname ,
                 ( CASE WHEN authorize='N' THEN '＊' ELSE '' END ) AS authorize,
                RTRIM(IFNULL(F.username,'')) AS sponsor
                FROM t04tb A
                INNER JOIN t01tb B
                ON A.class=B.class
                AND B.type<>'13'
                AND '".$type."'=(CASE WHEN '".$type."'='' THEN '' ELSE B.type END)
                INNER JOIN t13tb C
                ON A.class=C.class
                AND A.term=C.term
                AND C.status='2'/*未報到*/
                INNER JOIN m13tb D
                ON C.organ=D.organ
                INNER JOIN m02tb E
                ON C.idno=E.idno
                LEFT JOIN m09tb F
                ON A.sponsor = F.userid
                WHERE A.edate BETWEEN ".$sdate." AND ".$edate."
                AND C.organ NOT IN ('9999998','9999999')
                ORDER BY C.organ,C.dept,C.class,C.term,C.no";

                $absent=json_decode(json_encode(DB::select($sql)), true);
                //產生機關未到訓學員
        
                $sql="SELECT
                C.organ,
                RTRIM(D.lname) AS organ_name,
                C.term,
                C.idno,
                C.no,
                RTRIM(C.dept) AS 服務機關,
                C.class,
                CONCAT(RTRIM(B.name),'第',CAST(CAST(C.term AS INT) AS CHAR),'期') AS 班期別 ,
                RTRIM(E.cname) AS 姓名,
                (
                    CASE
                    WHEN LEFT(F.sdate,3)<>LEFT(F.edate,3) THEN
                        CONCAT(SUBSTRING(F.sdate,1,3),'年',
                        SUBSTRING(F.sdate,4,2),'月',
                        SUBSTRING(F.sdate,6,2),'日',
                        LEFT(F.stime,2),'時',RIGHT(F.stime,2),'分','至',
                        SUBSTRING(F.edate,1,3),'年',
                        SUBSTRING(F.edate,4,2),'月',
                        SUBSTRING(F.edate,6,2),'日',
                        LEFT(F.etime,2),'時',RIGHT(F.etime,2),'分')
                    WHEN F.sdate=F.edate THEN
                        CONCAT(SUBSTRING(F.sdate,4,2),'月',
                        SUBSTRING(F.sdate,6,2),'日',
                        LEFT(F.stime,2),'時',RIGHT(F.stime,2),'分','至',
                        LEFT(F.etime,2),'時',RIGHT(F.etime,2),'分')
                    ELSE
                        CONCAT(SUBSTRING(F.sdate,4,2),'月',
                        SUBSTRING(F.sdate,6,2),'日',
                        LEFT(F.stime,2),'時',RIGHT(F.stime,2),'分','至',
                        SUBSTRING(F.edate,4,2),'月',
                        SUBSTRING(F.edate,6,2),'日',
                        LEFT(F.etime,2),'時',RIGHT(F.etime,2),'分')
                    END
                    ) AS 'leave' ,
                IFNULL(G.username,'') AS sponsor
                FROM t04tb A
                INNER JOIN t01tb B
                ON A.class=B.class
                AND B.type<>'13'
                AND '".$type."'=(CASE WHEN '".$type."'='' THEN '' ELSE B.type END)
                INNER JOIN t13tb C
                ON A.class=C.class
                AND A.term=C.term
                INNER JOIN m13tb D
                ON C.organ=D.organ
                INNER JOIN m02tb E
                ON C.idno=E.idno
                INNER JOIN t14tb F
                ON C.class=F.class
                AND C.term=F.term
                AND C.idno=F.idno
                LEFT JOIN m09tb G
                ON A.sponsor = G.userid
                WHERE A.edate BETWEEN ".$sdate." AND ".$edate."
                AND C.organ NOT IN ('9999998','9999999')
                ORDER BY C.organ,C.dept,C.class,C.term,C.no,F.sdate";

                $leave=json_decode(json_encode(DB::select($sql)), true);
                 //產生機關請假學員

                 //取兩個(未到訓/請假)的聯集機關
                $organ_arr = array();
                 foreach ($absent as $a){
                     if(!isset($organ_arr[$a['organ']])){
                        $organ_arr[$a['organ']] = $a['organ_name']; 
                     }
                
                 }
                 foreach ($leave as $a){
                    if(!isset($organ_arr[$a['organ']])){
                       $organ_arr[$a['organ']] = $a['organ_name']; 
                    }
               
                }

                //建立這一次要輸出的zip路徑
                $RptBasic = new \App\Rptlib\RptBasic();
                $zip_filepath  = $this->today_filepath().DIRECTORY_SEPARATOR.time();
                $zip_filepath =  $RptBasic->filePathMakeSure($zip_filepath);

                foreach($organ_arr as $organ_key => $organ_value){//每個單位都產生一個檔案
              
                    // 檔案名稱
                    $fileName = 'F5A';
                    //範本位置
                    $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
                    //讀取excel，
                    $objPHPExcel = IOFactory::load($filePath);
                    $objSheet = $objPHPExcel->getsheet(0);
                    $needout = 0; //等於2就不產生檔案
                    
                    //取得 所有absent內這個單位的資料
                    $absent_sub = array();

                    foreach($absent as $key => $value){
                        if($value['organ']==$organ_key){
                            array_push($absent_sub,$value); 
                        }
                    }

                    //取得 所有leave內這個單位的資料
                    $leave_sub = array();

                    foreach($leave as $key => $value){
                        if($value['organ']==$organ_key){
                            array_push($leave_sub,$value); 
                        }
                    }
        

                    if($absent_sub==[]){ //填入未到訓資料
                    $objSheet->setCellValue('A3',$period);
                    //$needout++;
                    }else{
                    $absentkey=array_keys((array)$absent_sub[0]); //取得所有的key

                    $orgcaption="主管機關：";
                    $rowcnta=2;
                    $stylestart=4;
                    $counter=0;
                    if(sizeof($absent_sub)==0){ //當資料是空的顯示無資料
                        $objSheet->setCellValue('A'.strval($rowcnta+1),$period);
                    }else{   //當資料不是空的將值填入表格
                            for($i=0;$i<sizeof($absent_sub);$i++){
                                if($i==0){
                                    //set titles
                                    $objSheet->setCellValue('A'.$rowcnta,$orgcaption.$absent_sub[$i][$absentkey[1]]);
                                    $objSheetanother = $objPHPExcel->getsheet(1);
                                    $objSheetanother->setCellValue('A'.$rowcnta,$orgcaption.$absent_sub[$i][$absentkey[1]]);
                                    $objSheet->setCellValue('A'.strval($rowcnta+1),$period);
                                    $objSheet->setCellValue('A'.strval($rowcnta+2),"服務機關");
                                    $objSheet->setCellValue('B'.strval($rowcnta+2),"班號");
                                    $objSheet->setCellValue('C'.strval($rowcnta+2),"班期別");
                                    $objSheet->setCellValue('D'.strval($rowcnta+2),"姓名");
                                    $objSheet->setCellValue('E'.strval($rowcnta+2),"備註");
                                    $objSheet->setCellValue('F'.strval($rowcnta+2),"承辦人");

                                    for($j=2;$j<sizeof($absentkey);$j++){
                                        $objSheet->setCellValue($this->getNameFromNumber($j-1).strval($rowcnta+3),$absent_sub[$i][$absentkey[$j]]);
                                    }
                                    $rowcnta++;
                                    $counter++;
                                }else{
                                    if($absent_sub[$i-1][$absentkey[0]]==$absent_sub[$i][$absentkey[0]]){
                                        for($j=2;$j<sizeof($absentkey);$j++){
                                            $objSheet->setCellValue($this->getNameFromNumber($j-1).strval($rowcnta+3),$absent_sub[$i][$absentkey[$j]]);
                                        }
                                        $rowcnta++;
                                        $counter++;
                                    }else{

                                        $objSheet->mergeCells('A'.strval($rowcnta+3).':D'.strval($rowcnta+3));
                                        $objSheet->setCellValue('A'.strval($rowcnta+3),"＊註記表示該服務機關人事單位已寄送未到訓通知單");
                                        //apply borders
                                        $objSheet->getStyle('A'.$stylestart.':F'.strval($rowcnta+2))->applyFromArray($styleArray);
                                        $stylestart=$stylestart+$counter+6;
                                        $rowcnta=$rowcnta+6;
                                        $counter=0;
                                        //set titles
                                        $objSheet->mergeCells('A'.strval($rowcnta).':E'.strval($rowcnta));
                                        $objSheet->mergeCells('A'.strval($rowcnta+1).':E'.strval($rowcnta+1));
                                        $objSheet->setCellValue('A'.$rowcnta,$orgcaption.$absent_sub[$i][$absentkey[1]]);
                                        $objSheetanother = $objPHPExcel->getsheet(1);
                                        $objSheetanother->setCellValue('A'.$rowcnta,$orgcaption.$absent_sub[$i][$absentkey[1]]);
                                        $objSheet->setCellValue('A'.strval($rowcnta+1),$period);
                                        $objSheet->setCellValue('A'.strval($rowcnta+2),"服務機關");
                                        $objSheet->setCellValue('B'.strval($rowcnta+2),"班號");
                                        $objSheet->setCellValue('C'.strval($rowcnta+2),"班期別");
                                        $objSheet->setCellValue('D'.strval($rowcnta+2),"姓名");
                                        $objSheet->setCellValue('E'.strval($rowcnta+2),"備註");
                                        $objSheet->setCellValue('F'.strval($rowcnta+2),"承辦人");

                                        for($j=2;$j<sizeof($absentkey);$j++){
                                            $objSheet->setCellValue($this->getNameFromNumber($j-1).strval($rowcnta+3),$absent_sub[$i][$absentkey[$j]]);
                                        }
                                        if($i!=(sizeof($absent)-1))
                                            $rowcnta++;
                                        $counter++;
                                        $objSheet->mergeCells('A'.strval($rowcnta+4).':D'.strval($rowcnta+4));
                                        $objSheet->setCellValue('A'.strval($rowcnta+4),"＊註記表示該服務機關人事單位已寄送未到訓通知單");
                                    }
                                }
                                if($i==(sizeof($absent_sub)-1)){
                                    //apply borders
                                    $objSheet->mergeCells('A'.strval($rowcnta+3).':D'.strval($rowcnta+3));
                                    $objSheet->setCellValue('A'.strval($rowcnta+3),"＊註記表示該服務機關人事單位已寄送未到訓通知單");
                                    $objSheet->getStyle('A'.$stylestart.':F'.strval($rowcnta+2))->applyFromArray($styleArray);

                                }
                            }
                        }
                    }

                    $objSheet = $objPHPExcel->getsheet(1);

                    if($leave_sub==[]){ //填入請假資料
                        $objSheet->setCellValue('A3',$period);
                        $needout++;
                    }else{

                    $leave_subkey=array_keys((array)$leave_sub[0]);
                    $orgcaption="主管機關：";
                    $rowcnta=2;
                    $stylestart=4;
                    $counter=0;
                    if(sizeof($leave_sub)==0){ //當資料是空的顯示無資料
                            $objSheet->setCellValue('A'.strval($rowcnta+1),$period);
                    }else{   //當資料不是空的將值填入表格
                            for($i=0;$i<sizeof($leave_sub);$i++){
                                if($i==0){
                                    //set titles
                                    $objSheet->setCellValue('A'.$rowcnta,$orgcaption.$leave_sub[$i][$leave_subkey[1]]);
                                    $objSheetanother = $objPHPExcel->getsheet(0);
                                    $objSheetanother->setCellValue('A'.$rowcnta,$orgcaption.$leave_sub[$i][$leave_subkey[1]]);
                                    $objSheet->setCellValue('A'.strval($rowcnta+1),$period);
                                    $objSheet->setCellValue('A'.strval($rowcnta+2),"服務機關");
                                    $objSheet->setCellValue('B'.strval($rowcnta+2),"班號");
                                    $objSheet->setCellValue('C'.strval($rowcnta+2),"班期別");
                                    $objSheet->setCellValue('D'.strval($rowcnta+2),"姓名");
                                    $objSheet->setCellValue('E'.strval($rowcnta+2),"請假時間");
                                    $objSheet->setCellValue('F'.strval($rowcnta+2),"承辦人");

                                    for($j=5;$j<sizeof($leave_subkey);$j++){
                                        $objSheet->setCellValue($this->getNameFromNumber($j-4).strval($rowcnta+3),$leave_sub[$i][$leave_subkey[$j]]);
                                    }
                                    $rowcnta++;
                                    $counter++;
                                }else{
                                    if($leave_sub[$i-1][$leave_subkey[0]]==$leave_sub[$i][$leave_subkey[0]]){
                                        for($j=5;$j<sizeof($leave_subkey);$j++){
                                            $objSheet->setCellValue($this->getNameFromNumber($j-4).strval($rowcnta+3),$leave_sub[$i][$leave_subkey[$j]]);
                                        }
                                        $rowcnta++;
                                        $counter++;
                                    }else{

                                        //apply borders
                                        $objSheet->getStyle('A'.$stylestart.':F'.strval($rowcnta+2))->applyFromArray($styleArray);

                                        $stylestart=$stylestart+$counter+5;
                                        $counter=0;
                                        $rowcnta=$rowcnta+5;

                                        //set titles
                                        $objSheet->mergeCells('A'.strval($rowcnta).':E'.strval($rowcnta));
                                        $objSheet->mergeCells('A'.strval($rowcnta+1).':E'.strval($rowcnta+1));
                                        $objSheet->setCellValue('A'.$rowcnta,$orgcaption.$leave_sub[$i][$leave_subkey[1]]);
                                        $objSheetanother = $objPHPExcel->getsheet(0);
                                        $objSheetanother->setCellValue('A'.$rowcnta,$orgcaption.$leave_sub[$i][$leave_subkey[1]]);
                                        $objSheet->setCellValue('A'.strval($rowcnta+1),$period);
                                        $objSheet->setCellValue('A'.strval($rowcnta+2),"服務機關");
                                        $objSheet->setCellValue('B'.strval($rowcnta+2),"班號");
                                        $objSheet->setCellValue('C'.strval($rowcnta+2),"班期別");
                                        $objSheet->setCellValue('D'.strval($rowcnta+2),"姓名");
                                        $objSheet->setCellValue('E'.strval($rowcnta+2),"請假時間");
                                        $objSheet->setCellValue('F'.strval($rowcnta+2),"承辦人");

                                        for($j=5;$j<sizeof($leave_subkey);$j++){
                                            $objSheet->setCellValue($this->getNameFromNumber($j-4).strval($rowcnta+3),$leave_sub[$i][$leave_subkey[$j]]);
                                        }
                                        if($i!=(sizeof($leave_sub)-1)){
                                            $rowcnta++;
                                        }
                                        $counter++;
                                    }
                                }
                                if($i==(sizeof($leave_sub)-1)){
                                    $objSheet->mergeCells('A'.strval($rowcnta+3).':D'.strval($rowcnta+3));
                                    $objSheet->setCellValue('A'.strval($rowcnta+3),"＊註記表示該服務機關人事單位已寄送未到訓通知單");
                                    //apply borders
                                    $objSheet->getStyle('A'.$stylestart.':F'.strval($rowcnta+2))->applyFromArray($styleArray);
                                }
                            }
                        }

                    }

                    if( $needout < 2 ){
                        if($request->input('doctype')=="2"){
                            $RptBasic->savefile($objPHPExcel,"2",$request->input('doctype'),urlencode($organ_value),$zip_filepath);    
                        }else{
                            $RptBasic->savefile($objPHPExcel,"2",$request->input('doctype'),$organ_value,$zip_filepath);
                        }
                    }
                
                }

                ob_start();
                $zip = new \ZipArchive; 
                $zipfilename=time().'.zip';
                $zip->open($zipfilename,  \ZipArchive::CREATE);                
                $files= scandir($zip_filepath);
                // unset($files[0],$files[1]); dd($files);
                if($request->input('doctype')=="1"){
                    foreach ($files as $file) {
                        if(substr($file,-4)=="xlsx")
                            $zip->addFile($zip_filepath.DIRECTORY_SEPARATOR.$file, $file);
                    }
                }
                else{
                    foreach ($files as $file) {
                        if(substr($file,-3)=="ods")
                            $zip->addFile($zip_filepath.DIRECTORY_SEPARATOR.$file, urldecode(substr($file,0,-4)).substr($file,-4));
                    }
                }    
                $zip->close();
                
                if (headers_sent()) {
                    echo 'HTTP header already sent';
                } else {
                    if (!is_file($zipfilename)) {
                        header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
                        echo 'File not found';
                    } else if (!is_readable($zipfilename)) {
                        header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
                        echo 'File not readable';
                    } else {
                        header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
                        header("Content-Type: application/zip");
                        header("Content-Transfer-Encoding: Binary");
                        header("Content-Length: ".filesize($zipfilename));
                        header('Content-Disposition: attachment;filename="'.urlencode("班期調派訓異常統計表-各機關請假及未到訓統計").'.zip"');
                        while (ob_get_level()) {
                            ob_end_clean();
                          }
                        readfile($zipfilename);
                        exit;
                    }
                }
                

            }

        }else{  //2.班別期別
            $class=$request->input('classes');
            $term=$request->input('terms');
            $sql="#應派訓而未派訓
            SELECT
             B.organ,
             A.class,
             A.term,
             IFNULL((SELECT RTRIM(lname) FROM m13tb WHERE organ=B.organ),'') AS 主管機關,
             '' AS 服務機關,
             CONCAT(RTRIM(IFNULL((SELECT name FROM t01tb WHERE class=A.class),'')),' ''第', A.term,'期') AS 班期別,
             B.quota-COUNT(C.idno) AS 應派訓而未派訓,
             '' AS 已派訓而未到訓名單,
             '' AS 退訓名單
             FROM t04tb A
             INNER JOIN t03tb B
             ON A.class=B.class
             AND A.term=B.term
             LEFT JOIN t13tb C
             ON B.class=C.class
             AND B.term=C.term
             AND B.organ=C.organ
             WHERE
             NOT EXISTS ( SELECT NULL FROM m07tb WHERE agency=B.organ  ) #不包括 訓練機構
            #班期
            AND A.class= '".$class."' AND A.term='".$term."'
             GROUP BY A.class,A.term,B.organ,B.quota
             HAVING B.quota >COUNT(C.idno)

             UNION
            #已派訓而未到訓名單
             SELECT
             B.organ,
             A.class,
             A.term,
             IFNULL((SELECT lname FROM m13tb
             WHERE organ=B.organ),'') AS 主管機關,
             B.dept AS 服務機關,
             CONCAT(RTRIM(IFNULL((SELECT name FROM t01tb WHERE class=A.class),'')),' ','第', A.term,'期') AS 班期別,
             0 AS 應派訓而未派訓,
             IFNULL((  SELECT RTRIM(cname) FROM m02tb WHERE idno= B.idno ),'') AS 已派訓而未到訓名單,
             '' AS 退訓名單
             FROM t04tb A  INNER JOIN
             t13tb B ON
             A.class=B.class AND A.term=B.term
             WHERE B.status='2'  # 未報到
             AND NOT EXISTS ( SELECT * FROM m07tb WHERE agency=B.organ )  # 不包括訓練機構
            # '班期
            AND A.class= '".$class."' AND A.term='".$term."'
            #已派訓而未到訓名單>

            UNION
            #退訓名單
             SELECT
             B.organ,
             A.class,
             A.term,
            IFNULL((SELECT lname FROM m13tb WHERE organ=B.organ),'') AS 主管機關,
             B.dept AS 服務機關,
            CONCAT(RTRIM(IFNULL((SELECT name FROM t01tb WHERE class=A.class),'')),' ','第',A.term,'期') AS 班期別,
             0 AS 應派訓而未派訓,
             ''AS 已派訓而未到訓名單,
             IFNULL((SELECT RTRIM(cname) FROM m02tb WHERE idno=B.idno),'')  AS 退訓名單
             FROM t04tb A  INNER JOIN t13tb B ON A.class=B.class AND A.term=B.term
             WHERE B.status='3'    #退訓
             AND NOT EXISTS ( SELECT * FROM m07tb WHERE agency=B.organ ) #不包括 訓練機構
            #班期
            AND A.class= '".$class."' AND A.term='".$term."'";

            $data=json_decode(json_encode(DB::select($sql)), true);
            if($data==[]){
                $result="查無資料，請重新設定搜尋條件。";
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclass();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=$RptBasic->getTerms($temp[0][$arraykeys[0]]);
                $termArr=$temp;
                $temp=$RptBasic->getclasstypek();
                $class=$temp;
                return view('admin/changetraining_error/list',compact('classArr','termArr' ,'result','class'));
            }
            $datakey=array_keys((array)$data[0]);

            $sql="SELECT sdate,edate FROM t04tb WHERE class='".$class."' AND term='".$term."'";
            $date=json_decode(json_encode(DB::select($sql)), true);

            $classname=$data[0]["班期別"];
            $A1="訓練班別名稱：".$classname;
            $A2="訓練期間：自民國".substr($date[0]["sdate"],0,3)."年".substr($date[0]["sdate"],3,2)."月".substr($date[0]["sdate"],5,2).
            "日至".substr($date[0]["edate"],0,3)."年".substr($date[0]["edate"],3,2)."月".substr($date[0]["edate"],5,2)."日";

             // 檔案名稱
            $fileName = 'F5B';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel，
            $objPHPExcel = IOFactory::load($filePath);

            $objSheet = $objPHPExcel->getsheet(0);
            $objSheet->setCellValue('A1', $A1);
            $objSheet->setCellValue('A2', $A2);

            for($i=0;$i<sizeof($data);$i++){
                for($j=3;$j<sizeof($datakey);$j++){
                    $objSheet->setCellValue($this->getNameFromNumber($j-2).($i+4),$data[$i][$datakey[$j]]);
                    $objSheet->getRowDimension($i+4)->setRowHeight(40);
                }
            }

           if(sizeof($data)>9){
                $objSheet->setCellValue('A'.(sizeof($data)+4),'合計');
                $objSheet->setCellValue('D'.(sizeof($data)+4),'=SUM(D4:D'.(sizeof($data)+3).')');
                $objSheet->setCellValue('E'.(sizeof($data)+4),'=COUNTA(E4:E'.(sizeof($data)+3).')');
                $objSheet->setCellValue('F'.(sizeof($data)+4),'=COUNTA(F4:F'.(sizeof($data)+3).')');
                $objSheet->getRowDimension(sizeof($data)+4)->setRowHeight(40);
                $objSheet->setCellValue('C'.(sizeof($data)+5),'單主位管');
                $objSheet->setCellValue('E'.(sizeof($data)+5),'承辦人');
                $objSheet->getRowDimension(sizeof($data)+5)->setRowHeight(40);


                //apply borders
                $objSheet->getStyle('A3:'.$this->getNameFromNumber(sizeof($datakey)-2).(sizeof($data)+4))->applyFromArray($styleArray);

            }else{
                for($k=4;$k<15;$k++){
                    $objSheet->getRowDimension($k)->setRowHeight(40);
                }
                $objSheet->setCellValue('A13','合計');
                $objSheet->setCellValue('D13','=SUM(D4:D12)');
                $objSheet->setCellValue('E13','=COUNTA(E4:E12)');
                $objSheet->setCellValue('F13','=COUNTA(F4:F12)');
                $objSheet->setCellValue('C14','單主位管');
                $objSheet->setCellValue('E14','承辦人');

                //apply borders
                $objSheet->getStyle('A3:F13')->applyFromArray($styleArray);

            }

            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"班期調派訓異常統計表-".$classname);
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 
            
        }

    }

    public function today_filepath() {
        $today = storage_path(date("Y-m-d"));
        if (!file_exists( $today)) {
            mkdir( $today, 0777, true);
        }
        return $today;        
    }

}
