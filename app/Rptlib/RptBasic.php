<?php 
namespace App\Rptlib;

use Illuminate\Pagination\LengthAwarePaginator;
use DB;
use App\Rptlib\OfficeConverterTool;
use PHPExcel_IOFactory;

    class RptBasic {
        public function ok() {
            return 'myFunction is OK';
            //     sample code to call the function
            //     use App\Rptlib\RptWord;
            //     $RptRptWord = new \App\Rptlib\RptWord();
            //     dd($RptRptWord->ok());
            
        }
        
        public function gettime( $yerly) {     
            return DB::select("SELECT DISTINCT times FROM t01tb WHERE yerly='".$yerly."' and times is not null and times!='' GROUP BY times ORDER BY times");
        }

        public function getclass() {
            return DB::select("SELECT DISTINCT class, RTRIM(name) as name FROM t01tb ORDER BY class DESC");
            
        }

        public function getclasstypek() {
            return DB::select("SELECT RTRIM(code) AS value,RTRIM(name) AS text,CONCAT(RTRIM(code),' ',RTRIM(name)) AS item FROM s01tb WHERE type='K'");
           
        }

        public function getclassEx() {
            return DB::select("SELECT DISTINCT class, RTRIM(name) as name FROM t01tb WHERE type <> '13' ORDER BY class DESC");
            
        }

        public function getTerms($class)
        {
            return DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$class."' ORDER BY term");
        }

        public function getProcess($class)
        {       
            return DB::select("SELECT process FROM t01tb WHERE class='".$class."'"); 
        }

        public function getBranch($class,$term)
        {
            return DB::select("SELECT site_branch FROM t04tb WHERE class='".$class."' AND term = '".$term."'"); 
        }

        public function getTermByClass( $class) {     
            $sql = "SELECT DISTINCT term FROM t53tb WHERE class='".$class."' AND times<>'' ORDER BY 1";
            $data = DB::select($sql);
            return $data;            
        }    
        
        public function getTimeByClass( $class, $term) {     
            return DB::select("SELECT DISTINCT times FROM t53tb WHERE class='".$class."' AND term= '".$term."' AND times<>'' ORDER BY 1 ");
        }

        public function getorgan(  $yerly,  $temptimes) {   
            $times="";

            for ($i=0; $i < sizeof($temptimes); $i++) { 
                if ($i == sizeof($temptimes)-1) {
                    $times=$times."'".$temptimes[$i]."'";
                } else {
                    $times=$times."'".$temptimes[$i]."',";
                }
            }

            $sql = "SELECT A.organ,IFNULL(C.lname,D.name) AS 機關 
            FROM t03tb A 
            INNER JOIN t01tb B ON A.class=B.class
            LEFT JOIN m13tb C ON A.organ=C.organ 
            LEFT JOIN m07tb D ON A.organ=D.agency 
            WHERE yerly='$yerly'    
            AND B.times IN ($times)     
            GROUP BY A.organ,C.lname,D.NAME  
            ORDER BY A.organ ";

            $data = DB::select($sql);

            return $data;
        }

        public function getClassBysDate($request,$queryData = []){

            // 日期設定
            $sdate=$queryData['yerly'].$queryData['month'];
            $sdatenext=$this->getnextDate($sdate);
            
            $sql="SELECT IFNULL(A.section,'') AS 單位, RTRIM(B.name) AS 班別, B.target AS 參訓對象,A.term AS 期別, 
                CONCAT(CAST(CAST(LEFT(A.sdate,3) AS int) AS char),'/',SUBSTRING(A.sdate,4,2),'/',SUBSTRING(A.sdate,6,2)) AS 開課日期, 
                CONCAT(CAST(CAST(LEFT(A.edate,3) AS int) AS char),'/',SUBSTRING(A.edate,4,2),'/',SUBSTRING(A.edate,6,2)) AS 結束日期, 
                CONCAT(CAST(CAST(LEFT(A.sdate,3) AS int) AS char),'/',SUBSTRING(A.sdate,4,2),'/',SUBSTRING(A.sdate,6,2),'~', 
                CAST(CAST(LEFT(A.edate,3) AS int) AS char),'/',SUBSTRING(A.edate,4,2),'/',SUBSTRING(A.edate,6,2)) AS 受訓日期,
                ( CASE  
                WHEN A.pubsdate<>'' THEN CONCAT(CAST(CAST(LEFT(A.pubsdate,3) AS int) AS varchar(3)),'/',SUBSTRING(A.pubsdate,4,2),'/',SUBSTRING(A.pubsdate,6,2)) 
                ELSE '' 
                END ) AS 報名開始日期, 
                ( CASE  
                WHEN A.pubedate<>'' THEN CONCAT(CAST(CAST(LEFT(A.pubedate,3) AS int) AS varchar(3)),'/',SUBSTRING(A.pubedate,4,2),'/',SUBSTRING(A.pubedate,6,2))  
                ELSE '' 
                END ) AS 報名截止日期, 
                IFNULL(C.username,'') AS 承辦人員, 
                ( CASE WHEN A.notice='Y' THEN '' ELSE '＊' END ) AS 聯合派訓, 
                (SELECT CASE WHEN COUNT(*) =0 THEN '＊' ELSE '' END FROM t51tb WHERE class=A.class AND term=A.term ) AS 名額分配, 
                ( CASE WHEN B.classified='3' THEN '＊' ELSE '' END ) AS 混成班,
                A.class, A.term, 
                ( CASE B.branch WHEN '1' THEN '臺北院區' WHEN '2' THEN '南投院區' END ) as branch
                FROM t04tb A INNER JOIN t01tb B ON A.class=B.class AND B.type<>'13' LEFT JOIN m09tb C ON A.sponsor=C.userid 
                WHERE  
                ( CASE 
                    WHEN A.sdate LIKE '".substr($sdate,0,5)."%' AND B.classified<>'3' THEN 1 /* 該月非混成班 */ 
                    WHEN A.sdate LIKE '".$sdatenext."%' AND B.classified='3'  THEN 1 /* 下月混成班 */ 
                    END ) = 1
                ORDER BY A.section,A.sdate,A.edate,A.class,A.term ";

            $query=DB::select($sql);

            // total count of the set, this is necessary so the paginator will know the total pages to display
            $total = count($query); 
            // get current page from the request, first page is null
            $page = $request->page ? $request->page : 1; 
            // how many items you want to display per page?
            $perPage = $queryData['_paginate_qty']; 
            // get the offset, how many items need to be "skipped" on this page
            $offset = ($page - 1) * $perPage; 
            // the array that we actually pass to the paginator is sliced
            $items = array_slice($query, $offset, $perPage); 

            return new LengthAwarePaginator($items, $total, $perPage, $page, [
                'path' => $request->url(),
                'query' => $request->query()
            ]);

        }

        public function getClassBysDate2($sdate){
            
            $sdatenext=$this->getnextDate($sdate);

            $sql="SELECT IFNULL(A.section,'') AS 單位, RTRIM(B.name) AS 班別, B.target AS 參訓對象,A.term AS 期別, 
                CONCAT(CAST(CAST(LEFT(A.sdate,3) AS int) AS char),'/',SUBSTRING(A.sdate,4,2),'/',SUBSTRING(A.sdate,6,2)) AS 開課日期, 
                CONCAT(CAST(CAST(LEFT(A.edate,3) AS int) AS char),'/',SUBSTRING(A.edate,4,2),'/',SUBSTRING(A.edate,6,2)) AS 結束日期, 
                CONCAT(CAST(CAST(LEFT(A.sdate,3) AS int) AS char),'/',SUBSTRING(A.sdate,4,2),'/',SUBSTRING(A.sdate,6,2),'~', 
                CAST(CAST(LEFT(A.edate,3) AS int) AS char),'/',SUBSTRING(A.edate,4,2),'/',SUBSTRING(A.edate,6,2)) AS 受訓日期,
                ( CASE  
                WHEN A.pubsdate<>'' THEN CONCAT(CAST(CAST(LEFT(A.pubsdate,3) AS int) AS varchar(3)),'/',SUBSTRING(A.pubsdate,4,2),'/',SUBSTRING(A.pubsdate,6,2)) 
                ELSE '' 
                END ) AS 報名開始日期, 
                ( CASE  
                WHEN A.pubedate<>'' THEN CONCAT(CAST(CAST(LEFT(A.pubedate,3) AS int) AS varchar(3)),'/',SUBSTRING(A.pubedate,4,2),'/',SUBSTRING(A.pubedate,6,2))  
                ELSE '' 
                END ) AS 報名截止日期, 
                IFNULL(C.username,'') AS 承辦人員, 
                ( CASE WHEN A.notice='Y' THEN '' ELSE '＊' END ) AS 聯合派訓, 
                (SELECT CASE WHEN COUNT(*) =0 THEN '＊' ELSE '' END FROM t51tb WHERE class=A.class AND term=A.term ) AS 名額分配, 
                ( CASE WHEN B.classified='3' THEN '＊' ELSE '' END ) AS 混成班,
                A.class, A.term, 
                ( CASE B.branch WHEN '1' THEN '臺北院區' WHEN '2' THEN '南投院區' END ) as branch
                FROM t04tb A INNER JOIN t01tb B ON A.class=B.class AND B.type<>'13' LEFT JOIN m09tb C ON A.sponsor=C.userid 
                WHERE  
                ( CASE 
                    WHEN A.sdate LIKE '".substr($sdate,0,5)."%' AND B.classified<>'3' THEN 1 /* 該月非混成班 */ 
                    WHEN A.sdate LIKE '".$sdatenext."%' AND B.classified='3'  THEN 1 /* 下月混成班 */ 
                    END ) = 1
                ORDER BY A.section,A.sdate,A.edate,A.class,A.term ";

            $query=DB::select($sql);

            return $query;

        }

        public function getnextDate($sdate){
            $sdatenext="";
            if(substr($sdate,3,2)=="12"){
                if(substr($sdate,0,3)=="099"){
                    $sdatenext="10001";
                }else{
                    $sdatenext=substr($sdate,0,1).strval((int)substr($sdate,1,2)+1)."01";
                }
            }else{
                if(substr($sdate,4,1)=="9"){
                    $sdatenext=substr($sdate,0,3)."10";
                }
                else{
                $sdatenext=substr($sdate,0,4).strval((int)substr($sdate,4,1)+1);
                }
            }
            return $sdatenext;
        }
        
        public function gettdate(){
            $sql="SELECT date,CONCAT(SUBSTRING(date,1,3),'/',SUBSTRING(date,4,2),'/', SUBSTRING(date,6,2)) AS sdate
            FROM t11tb WHERE transfor = '1' GROUP BY date ORDER BY date DESC";
            $temp=DB::select($sql);
            $tdateArr=$temp;
            return $tdateArr;
        }

        public function gettdatebank(){
            $sql="SELECT date,CONCAT(SUBSTRING(date,1,3),'/',SUBSTRING(date,4,2),'/', SUBSTRING(date,6,2)) AS sdate
            FROM t11tb WHERE transfor = '2' GROUP BY date ORDER BY date DESC ";
            $temp=DB::select($sql);
            $tdateArr=$temp;
            return $tdateArr;
        }

        public function getpaidday(){
            $sql="SELECT paidday as date, CONCAT(SUBSTRING(paidday,1,3),'/',SUBSTRING(paidday,4,2),'/',SUBSTRING(paidday,6,2)) AS sdate
            FROM t09tb WHERE paidday <>'' GROUP BY paidday ORDER BY paidday DESC ";
            $temp=DB::select($sql);
            $tdateArr=$temp;
            return $tdateArr;
        }



        public function toCHTnum($num){
            //說明:10120 =>壹萬壹佰貳拾元整 MAX玖仟玖佰玖拾玖萬
            if($num>99999999)
                return "error";
            $CHTnum="";
            $digits = preg_split("//", $num,-1,PREG_SPLIT_NO_EMPTY);

            $pos=0;

            for($i=(sizeof($digits)-1);$i>=0;$i--)
            {
                if($pos>7)
                    return "error";

                switch ($pos) {
                    case 0:
                        if($digits[$i]==0)
                            $CHTnum="元整".$CHTnum;
                        else
                            $CHTnum=$this->digitoword($digits[$i])."元整".$CHTnum;    
                        break;
                    case 1:
                        if($digits[$i]!=0)
                            $CHTnum=$this->digitoword($digits[$i])."拾".$CHTnum;
                        break;
                    case 2:
                        if($digits[$i]!=0)
                            $CHTnum=$this->digitoword($digits[$i])."佰".$CHTnum;
                        break;
                    case 3:
                        if($digits[$i]!=0)
                            $CHTnum=$this->digitoword($digits[$i])."仟".$CHTnum;
                        break;
                    case 4:
                        if($digits[$i]==0)
                            $CHTnum="萬".$CHTnum;
                        else
                            $CHTnum=$this->digitoword($digits[$i])."萬".$CHTnum;
                        break;
                    case 5:
                        if($digits[$i]!=0)
                            $CHTnum=$this->digitoword($digits[$i])."拾".$CHTnum;
                        break;
                    case 6:
                        if($digits[$i]!=0)
                            $CHTnum=$this->digitoword($digits[$i])."佰".$CHTnum;
                        break;
                    case 7:
                        if($digits[$i]!=0)
                            $CHTnum=$this->digitoword($digits[$i])."仟".$CHTnum;
                        break;
                                                         
                }
                $pos++;
            }

            return $CHTnum;
        }

        public function digitoword($digit){

            switch ($digit) {
                case 0:
                    return "零";
                    break;
                case 1:
                    return "壹";
                    break;
                case 2:
                    return "貳";
                    break;
                case 3:
                    return "參";
                    break;
                case 4:
                    return "肆";
                    break;
                case 5:
                    return "伍";
                    break;
                case 6:
                    return "陸";
                    break;
                case 7:
                    return "柒";
                    break;
                case 8:
                    return "捌";
                    break;
                case 9:
                    return "玖";
                    break;
                                                     
            }
        }

        function toCHTnum2($num){
            //說明:10120 =>ㄧ萬一百二十 MAX99999999

            if($num>99999999)
                return "error";
            $CHTnum="";
            $digits = preg_split("//", $num,-1,PREG_SPLIT_NO_EMPTY);

            $pos=0;

            if($num==0)
                return "〇";

            for($i=(sizeof($digits)-1);$i>=0;$i--)
             {
                if($pos>7)
                    return "error";

                switch ($pos) {
                    case 0:
                        if($digits[$i]==0)
                            $CHTnum=$CHTnum;
                        else
                             $CHTnum=$this->digitoword2($digits[$i]).$CHTnum;    
                        break;
                    case 1:
                        if($digits[$i]!=0 && $digits[$i]!=1)
                            $CHTnum=$this->digitoword2($digits[$i])."十".$CHTnum;
                        if( $digits[$i]==1)
                            $CHTnum="十".$CHTnum;
                         break;
                    case 2:
                        if($digits[$i]!=0)
                            $CHTnum=$this->digitoword2($digits[$i])."百".$CHTnum;
                        break;
                    case 3:
                        if($digits[$i]!=0)
                            $CHTnum=$this->digitoword2($digits[$i])."千".$CHTnum;
                        break;
                    case 4:
                        if($digits[$i]==0)
                            $CHTnum="萬".$CHTnum;
                        else
                            $CHTnum=$this->digitoword2($digits[$i])."萬".$CHTnum;
                        break;
                    case 5:
                        if($digits[$i]!=0)
                            $CHTnum=$this->digitoword2($digits[$i])."十".$CHTnum;
                        break;
                    case 6:
                        if($digits[$i]!=0)
                            $CHTnum=$this->digitoword2($digits[$i])."百".$CHTnum;
                        break;
                    case 7:
                         if($digits[$i]!=0)
                            $CHTnum=$this->digitoword2($digits[$i])."千".$CHTnum;
                        break;
                                                         
                }
                $pos++;
            }
    
            return $CHTnum;
        }
    
        public function digitoword2($digit){
    
            switch ($digit) {
                case 0:
                    return "〇";
                    break;
                case 1:
                    return "一";
                    break;
                case 2:
                    return "二";
                    break;
                case 3:
                    return "三";
                    break;
                case 4:
                    return "四";
                    break;
                case 5:
                    return "五";
                    break;
                case 6:
                    return "六";
                    break;
                case 7:
                    return "七";
                    break;
                case 8:
                    return "八";
                    break;
                case 9:
                    return "九";
                    break;                                       
              }
        }

        public function exportfile($obj,$objtype,$doctype,$filename){     
        //$obj: entity of file
        //$objtype:1.templateProcessor 2.PhpSpreadsheet 3.PhpExcel 4.PhpWord
        //$doctype:1.ooxml 2.odf
        //$filename:filename

            $officeConverterTool = new OfficeConverterTool();
            $filename=urlencode($filename);

            if($objtype=="1"){    //templateProcessor
                if($doctype=="2"){
                    //odt
                    $today_filepath  =  $officeConverterTool->today_filepath();
                    $r_filename = $today_filepath.DS.time().'.docx';
                    $outfilename =''; //不設定新名稱則沿用原來擋案名稱
                    $obj->saveAs($r_filename);
                    $new_file = $officeConverterTool->Converter2OtherFileType($r_filename,$outfilename,'odt');

                    $file_size = filesize($new_file);
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Cache-Control: private', false);
                    header('Content-Type: application/octet-stream');
                    header('Content-Length: ' . $file_size);
                    header('Content-Disposition: attachment; filename="' . $filename . '.odt";');
                    header('Content-Transfer-Encoding: binary');
                    readfile($new_file);  

                }else{
                    //docx
                    header('Content-Type: application/vnd.ms-word');
                    header("Content-Disposition: attachment;filename=$filename.docx");
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $obj->saveAs('php://output');
                    exit;

                }
            }elseif($objtype=="2"){ //PhpSpreadsheet
                if($doctype=="2")
                {
                    //export ods
                    $today_filepath  =  $officeConverterTool->today_filepath();
                    $r_filename = $today_filepath.DS.time().'.xlsx';
                    $outfilename =''; //不設定新名稱則沿用原來擋案名稱
                    $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($obj, 'Xlsx');
                    $objWriter->save($r_filename);
                    $new_file = $officeConverterTool->Converter2OtherFileType($r_filename,$outfilename,'ods');

                    $file_size = filesize($new_file);
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Cache-Control: private', false);
                    header('Content-Type: application/octet-stream');
                    header('Content-Length: ' . $file_size);
                    header('Content-Disposition: attachment; filename="' . $filename . '.ods";');
                    header('Content-Transfer-Encoding: binary');
                    readfile($new_file);  

                }else{
                    //export excel
                    ob_end_clean();
                    ob_start();
        
                    // Redirect output to a client’s web browser (Excel2007)
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    // 設定下載 Excel 的檔案名稱
                    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
                    header('Cache-Control: max-age=0');
                    // If you're serving to IE 9, then the following may be needed
                    header('Cache-Control: max-age=1');
        
                    // If you're serving to IE over SSL, then the following may be needed
                    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                    header ('Pragma: public'); // HTTP/1.0

                    $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($obj, 'Xlsx');
                    $objWriter->save('php://output');
                    exit;
                }

            }elseif($objtype=="3"){ //PhpExcel

                if($doctype=="2")
                {
                    //export ods
                    $today_filepath  =  $officeConverterTool->today_filepath();
                    $r_filename = $today_filepath.DS.time().'.xlsx';
                    $outfilename =''; //不設定新名稱則沿用原來擋案名稱
                    $objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
                    $objWriter->save($r_filename);  
                    $new_file = $officeConverterTool->Converter2OtherFileType($r_filename,$outfilename,'ods');

                    $file_size = filesize($new_file);
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Cache-Control: private', false);
                    header('Content-Type: application/octet-stream');
                    header('Content-Length: ' . $file_size);
                    header('Content-Disposition: attachment; filename="' . $filename . '.ods";');
                    header('Content-Transfer-Encoding: binary');
                    readfile($new_file);  

                }else{
                    //export excel
                    ob_end_clean();
                    ob_start();
                    // Redirect output to a client’s web browser (Excel2007)
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    // 設定下載 Excel 的檔案名稱
                    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
                    header('Cache-Control: max-age=0');
                    // If you're serving to IE 9, then the following may be needed
                    header('Cache-Control: max-age=1');
                    
                    // If you're serving to IE over SSL, then the following may be needed
                    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                    header ('Pragma: public'); // HTTP/1.0

                    $objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
                    $objWriter->setIncludeCharts(true);
                    $objWriter->save('php://output');
                    exit;
                }
            }elseif($objtype=="4"){    //PhpWord
                if($doctype=="2"){
                    //odt 
                    $today_filepath  =  $officeConverterTool->today_filepath();
                    $r_filename = $today_filepath.DS.time().'.docx';
                    $outfilename =''; //不設定新名稱則沿用原來擋案名稱
                    $obj->save($r_filename);
                    $new_file = $officeConverterTool->Converter2OtherFileType($r_filename,$outfilename,'odt');

                    $file_size = filesize($new_file);
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Cache-Control: private', false);
                    header('Content-Type: application/octet-stream');
                    header('Content-Length: ' . $file_size);
                    header('Content-Disposition: attachment; filename="' . $filename . '.odt";');
                    header('Content-Transfer-Encoding: binary');
                    readfile($new_file);  

                }else{
                    //docx
                    header('Content-Type: application/vnd.ms-word');
                    header("Content-Disposition: attachment;filename=$filename.docx");
                    header('Cache-Control: max-age=0');
                    ob_clean();
                    $obj->save('php://output');
                    exit;

                }
            }
        }


        public function savefile($obj,$objtype,$doctype,$filename,$zipFilePath){     

    
                $officeConverterTool = new OfficeConverterTool();
    
                if($objtype=="1"){    //PhpWord
                    $this->filePathMakeSure($zipFilePath);
                    $s_filename = $zipFilePath.DS.$filename.'.docx';
                    $outfilename =$filename;
                    $obj->saveAs($s_filename);
                    if($doctype=="2"){
                        $r_filename = $officeConverterTool->Converter2OtherFileType($s_filename,$outfilename,'odt');
                    }
                    

                }elseif($objtype=="2"){ //PhpSpreadsheet
                    
                    $this->filePathMakeSure($zipFilePath);
                    $s_filename =  $zipFilePath.DS.$filename.'.xlsx';

                    $outfilename =$filename;
                    $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($obj, 'Xlsx');

                    $objWriter->save($s_filename);
                    if($doctype=="2")
                    {
                        //export ods
                        $r_filename = $officeConverterTool->Converter2OtherFileType($s_filename,$outfilename,'ods');
                    }              

                }elseif($objtype=="3"){ //PhpExcel    
                    $this->filePathMakeSure($zipFilePath);
                    $s_filename =  $zipFilePath.DS.$filename.'.xlsx';
                    $outfilename =$filename;
                    $objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
                    $objWriter->save($s_filename);
                    if($doctype=="2")
                    {
                        //export ods
                        $r_filename = $officeConverterTool->Converter2OtherFileType($s_filename,$outfilename,'ods');
                    }
                    
                }
        }

        public function filePathMakeSure($path) {
          
            if (!file_exists( $path)) {
                mkdir( $path, 0777, true);
            }
            return $path;        
        }

    }
