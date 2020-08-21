<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPEXcel_RichText;
use PHPExcel_Chart;
use PHPExcel_Chart_Title;
use PHPExcel_Chart_Layout;
use PHPExcel_Chart_Axis;
use PHPExcel_Style_Alignment;
use PHPExcel_Worksheet;
use PHPExcel;

class TrainingResultAllController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
          $user_data = \Auth::user();
          $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
          if(in_array('training_result_all', $user_group_auth)){
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
        return view('admin/training_result_all/list');
    }

    // 搜尋下拉『班別』
    public function getclass(Request $request) {
        /* '問卷版本 新:0 舊:1 */
        $ratioInfo = $request->input('info');
        if($ratioInfo=='0') {
            $sql = "SELECT DISTINCT t53tb.class, t01tb.name
                      FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                     WHERE t53tb.times<>''
                     ORDER BY t53tb.class DESC  ";
        }
        else{
            $sql = "SELECT DISTINCT t19tb.class, t01tb.name
                      FROM t19tb INNER JOIN t01tb ON t19tb.class = t01tb.class
                     ORDER BY t19tb.class DESC";
        }
        $classArr = DB::select($sql);
        return $classArr;
    }

    // 搜尋下拉『期別』
    public function getTermByClass(Request $request)
    {
      /* '問卷版本 新:0 舊:1  		 */
      $ratioInfo = $request->input('info');
      $class = $request->input('class');
      if($ratioInfo=='0') {
        $RptBasic = new \App\Rptlib\RptBasic;
        return $RptBasic->getTermByClass($request->input('class'));
      }
      else{
        $sql = "SELECT DISTINCT term FROM t19tb
                WHERE class = '".$class."'
                 ORDER By 1";
        $classArr = DB::select($sql);
        return $classArr;
      }

    }

    /*
    訓後成效評估結果統計圖表 CSDIR5070
    參考Tables:
    使用範本:L22A.xlsx(新版), L22B.xlsx (舊版)
    'History:
    '2003/04/28
    '若題目數被【統計圖表最大題目數】整除
    '統計圖表會多出一個
    '2003/04/04
    '只顯示有問卷題目之班期
    '統計圖表標題，標示(一)、(二)..
    '2003/04/01 Update
    '統計圖表最大題目數
    '需更新CSDIR5070.xlt
    '2002/12/25 Update
    '修正bug
    '2002/12/24 Update
    'New Vsersion 2002/12/24 start 2002/12/24 end
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
      //版本 新:0 舊:1
      $ratioInfo = $request->input('info');
      //班別
      $class = $request->input('class');
      //期別
      $term = $request->input('term');
      //原始資料, 統計資料
      $ratioInfo1 = $request->input('info1');
      //統計圖表最大題目數
      $qnum = $request->input('qnum');

      // 讀檔案
      /* '版本 新:0 舊:1
        */
      if($ratioInfo=="0") {
            //TITLE
            $sqlTitle="SELECT DISTINCT t53tb.class, t01tb.name, t53tb.term, t53tb.times,
                            CONCAT(t01tb.name, '第',
                            CASE t53tb.term WHEN '01' THEN '1'
                                            WHEN '02' THEN '2'
                                            WHEN '03' THEN '3'
                                            WHEN '04' THEN '4'
                                            WHEN '05' THEN '5'
                                            WHEN '06' THEN '6'
                                            WHEN '07' THEN '7'
                                            WHEN '08' THEN '8'
                                            WHEN '09' THEN '9'
                                            ELSE t53tb.term END
                            , '期訓練訓後評估結果統計表')  AS TITLE
                    FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                    WHERE t53tb.times<>''
                    AND t53tb.class = '".$class."'
                    AND t53tb.term= '".$term."'
                    ORDER BY t53tb.class DESC

                    ";
            $reportlistTitle = DB::select($sqlTitle);
            $dataArrTitle=json_decode(json_encode(DB::select($sqlTitle)), true);


            //Begin Date & End Date
            $sqlDate="SELECT sdate,edate ,
                            CONCAT('訓期：',
                                                    SUBSTRING(sdate,1,3),'/', SUBSTRING(sdate,4,2), '/', SUBSTRING(sdate,6,2),'~',
                                                    SUBSTRING(edate,1,3),'/', SUBSTRING(edate,4,2), '/', SUBSTRING(edate,6,2)
                                            ) AS sdate_edate
                    FROM t04tb
                    WHERE class='".$class."'
                    AND term= '".$term."'
                    ";
            $reportlistDate = DB::select($sqlDate);
            $dataArrDate=json_decode(json_encode(DB::select($sqlDate)), true);

            //題目統計
            $sql="SELECT
                        RTRIM(C.name),
                        COUNT((CASE WHEN ans=5 THEN 1 ELSE NULL  END)),
                        COUNT((CASE WHEN ans=4 THEN 1 ELSE NULL  END)),
                        COUNT((CASE WHEN ans=3 THEN 1 ELSE NULL  END)),
                        COUNT((CASE WHEN ans=2 THEN 1 ELSE NULL  END)),
                        COUNT((CASE WHEN ans=1 THEN 1 ELSE NULL  END))
                    FROM t58tb A INNER JOIN t60tb B ON A.class=B.class AND A.term=B.term AND A.course=B.course
                                 LEFT JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                    WHERE A.class = '".$class."'
                      AND A.term = '".$term."'
                    GROUP BY A.course,A.sequence,C.name
                    ORDER BY A.sequence
              ";
            $reportlist = DB::select($sql);
            $dataArr=json_decode(json_encode(DB::select($sql)), true);
            //取出全部項目
            if(sizeof($reportlist) != 0) {
                $arraykeys=array_keys((array)$reportlist[0]);
            }

            //REMARK 1
            //不需在額外判斷最大次數
            $sqlRemark1="SELECT CONCAT('1.本次調查問卷計發出', A.lngCopy_Count, '份，共回收', B.lngBack_Count, '份，') AS REMARK1A,
                            CONCAT('回收率',
                                    CASE WHEN A.lngCopy_Count = 0 THEN
                                            '0%。'
                                        ELSE
                            CONCAT(FORMAT( B.lngBack_Count / A.lngCopy_Count * (100), 2), '%。')
                                        END ) AS REMARK1B,
                            CONCAT('受訓人數：', A.lngCopy_Count, '人 回收份數：', B.lngBack_Count, '人 ') AS TITLE_OLD
                            FROM (
                            SELECT copy AS lngCopy_Count
                            FROM t53tb
                            WHERE class= '".$class."'
                                AND term= '".$term."'
                            ) A LEFT JOIN
                            ( SELECT COUNT(*) AS lngBack_Count
                                FROM t59tb
                            WHERE class='".$class."'
                                AND term= '".$term."'
                            ) B ON 1 = 1
                            ";
            $reportlistRemark1 = DB::select($sqlRemark1);
            $dataArrRemark1=json_decode(json_encode(DB::select($sqlRemark1)), true);

            //REMARK:AVG, STDDEV, sngMax, sngMin
            //比照EXCEL輸出結果, SELECT 不排除0項目, 故加IFNULL為0
            $sqlRemark2STDDEV="SELECT ROUND(AVG(IFNULL((T.D5*5+T.D4*4+T.D3*3+T.D2*2+T.D1*1)/(T.D5+T.D4+T.D3+T.D2+T.D1),0)),2) AS AVG,
                                      ROUND(STDDEV_SAMP(IFNULL((T.D5*5+T.D4*4+T.D3*3+T.D2*2+T.D1*1)/(T.D5+T.D4+T.D3+T.D2+T.D1),0)),2) AS STDDEV,
                                      ROUND(AVG(IFNULL((T.D5*5+T.D4*4+T.D3*3+T.D2*2+T.D1*1)/(T.D5+T.D4+T.D3+T.D2+T.D1),0)),2) +
                                          (ROUND(STDDEV_SAMP(IFNULL((T.D5*5+T.D4*4+T.D3*3+T.D2*2+T.D1*1)/(T.D5+T.D4+T.D3+T.D2+T.D1),0)),2) * 2) AS sngMax,
                                      ROUND(AVG(IFNULL((T.D5*5+T.D4*4+T.D3*3+T.D2*2+T.D1*1)/(T.D5+T.D4+T.D3+T.D2+T.D1),0)),2) -
                                          (ROUND(STDDEV_SAMP(IFNULL((T.D5*5+T.D4*4+T.D3*3+T.D2*2+T.D1*1)/(T.D5+T.D4+T.D3+T.D2+T.D1),0)),2) * 2) AS sngMin
                                FROM (SELECT
                                          RTRIM(C.name) AS Q,
                                          COUNT((CASE WHEN ans=5 THEN 1 ELSE NULL  END)) AS D5,
                                          COUNT((CASE WHEN ans=4 THEN 1 ELSE NULL  END)) AS D4,
                                          COUNT((CASE WHEN ans=3 THEN 1 ELSE NULL  END)) AS D3,
                                          COUNT((CASE WHEN ans=2 THEN 1 ELSE NULL  END)) AS D2,
                                          COUNT((CASE WHEN ans=1 THEN 1 ELSE NULL  END)) AS D1
                                      FROM t58tb A INNER JOIN t60tb B ON A.class=B.class AND A.term=B.term AND A.course=B.course
                                                    LEFT JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                      WHERE A.class= '".$class."'
                                        AND A.term= '".$term."'
                                      GROUP BY A.course,A.sequence,C.name
                                      ORDER BY A.sequence
                                      ) T
                    ";
            $reportlist2STDDEV = DB::select($sqlRemark2STDDEV);
            $dataArr2STDDEV=json_decode(json_encode(DB::select($sqlRemark2STDDEV)), true);

            //問答題
            $sql2="SELECT comment,
                              addcourse,
                              delcourse,
                              othercom
                        FROM t59tb
                       WHERE class='".$class."'
                         AND term='".$term."'
                ";
            $reportlist2 = DB::select($sql2);
            $dataArr2=json_decode(json_encode(DB::select($sql2)), true);
            //取出全部項目
            if(sizeof($reportlist2) != 0) {
                $arraykeys2=array_keys((array)$reportlist2[0]);
            }

            // 檔案名稱
            $fileName = 'L22A';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel

            //$objPHPExcel = PHPExcel_IOFactory::load($filePath);
            $excelReader = PHPExcel_IOFactory::createReaderForFile($filePath);
            $excelReader->setReadDataOnly(false);
            $excelReader->setIncludeCharts(true);
            $objPHPExcel = $excelReader->load($filePath);

            //題目統計
            //指定sheet
            $objActSheet = $objPHPExcel->getActiveSheet();
            $objActSheet = $objPHPExcel->getSheet(0);
            $reportlist = json_decode(json_encode($reportlist), true);

            $lineName = 'C';
            if(sizeof($reportlist) != 0) {
            //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys); $i++) {
                    //excel 欄位 1 == A, etc
                    if($i==0){
                        $lineName = 'A';
                    } elseif($i==1){
                        $lineName = 'B';
                    } elseif($i==2){
                        $lineName = 'D';
                    } elseif($i==3){
                        $lineName = 'F';
                    } elseif($i==4){
                        $lineName = 'H';
                    } elseif($i==5){
                        $lineName = 'J';
                    }else {
                        //$NameFromNumber=$this->getNameFromNumber($i+2); //B
                        $lineName = 'A';
                    }
                    //資料by班別迴圈
                    for ($j=0; $j < sizeof($reportlist); $j++) {
                        //3開始
                        $objActSheet->setCellValue($lineName.($j+3), $reportlist[$j][$arraykeys[$i]]);
                        //=IF(ISERROR(B3/(B3+D3+F3+H3+J3)),0,B3/(B3+D3+F3+H3+J3))
                        $objActSheet->setCellValue('C'.($j+3),'=IF(ISERROR(B'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'+J'.($j+3).')),0,B'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'+J'.($j+3).'))');
                        $objActSheet->setCellValue('E'.($j+3),'=IF(ISERROR(D'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'+J'.($j+3).')),0,D'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'+J'.($j+3).'))');
                        $objActSheet->setCellValue('G'.($j+3),'=IF(ISERROR(F'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'+J'.($j+3).')),0,F'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'+J'.($j+3).'))');
                        $objActSheet->setCellValue('I'.($j+3),'=IF(ISERROR(H'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'+J'.($j+3).')),0,H'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'+J'.($j+3).'))');
                        $objActSheet->setCellValue('K'.($j+3),'=IF(ISERROR(J'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'+J'.($j+3).')),0,J'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'+J'.($j+3).'))');

                        //=IF(ISERROR((B3*5+D3*4+F3*3+H3*2+J3*1)/(B3+D3+F3+H3+J3)),0,(B3*5+D3*4+F3*3+H3*2+J3*1)/(B3+D3+F3+H3+J3))
                        $objActSheet->setCellValue('L'.($j+3),'=IF(ISERROR((B'.($j+3).'*5+D'.($j+3).'*4+F'.($j+3).'*3+H'.($j+3).'*2+J'.($j+3).'*1)/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'+J'.($j+3).')),0,(B'.($j+3).'*5+D'.($j+3).'*4+F'.($j+3).'*3+H'.($j+3).'*2+J'.($j+3).'*1)/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'+J'.($j+3).'))');
                        //=IF(ISERROR((C3*100+E3*80+G3*60+I3*40+K3*20)/(C3+E3+G3+I3+K3)),0,(C3*100+E3*80+G3*60+I3*40+K3*20)/(C3+E3+G3+I3+K3))
                        $objActSheet->setCellValue('M'.($j+3),'=IF(ISERROR((C'.($j+3).'*100+E'.($j+3).'*80+G'.($j+3).'*60+I'.($j+3).'*40+K'.($j+3).'*20)/(C'.($j+3).'+E'.($j+3).'+G'.($j+3).'+I'.($j+3).'+K'.($j+3).')),0,(C'.($j+3).'*100+E'.($j+3).'*80+G'.($j+3).'*60+I'.($j+3).'*40+K'.($j+3).'*20)/(C'.($j+3).'+E'.($j+3).'+G'.($j+3).'+I'.($j+3).'+K'.($j+3).'))');

                    }
                }

                //框線
                $styleArray = array(
                      'borders' => array(
                          'allborders' => array(
                           'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                      )
                );
                $objActSheet->getStyle('A3:M'.($j+2))->applyFromArray($styleArray);

                //VERTICAL_CENTER 垂直置中
                //HORIZONTAL_CENTER 水平置中
                $styleArrayCenter = array(
                      'alignment' => array(
                          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                          'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
                      )
                );
                $objActSheet->setCellValue('A'.($j+3), '平　　　均');
                $objActSheet->getStyle('A'.($j+3))->applyFromArray($styleArrayCenter);

                //=IF(ISERROR(AVERAGE(C3:C37)),0,AVERAGE(C3:C37))
                $objActSheet->setCellValue('C'.($j+3), '=IF(ISERROR(AVERAGE(C3:C'.($j+2).')),0,AVERAGE(C3:C'.($j+2).'))');
                $objActSheet->setCellValue('E'.($j+3), '=IF(ISERROR(AVERAGE(E3:E'.($j+2).')),0,AVERAGE(E3:E'.($j+2).'))');
                $objActSheet->setCellValue('G'.($j+3), '=IF(ISERROR(AVERAGE(G3:G'.($j+2).')),0,AVERAGE(G3:G'.($j+2).'))');
                $objActSheet->setCellValue('I'.($j+3), '=IF(ISERROR(AVERAGE(I3:I'.($j+2).')),0,AVERAGE(I3:I'.($j+2).'))');
                $objActSheet->setCellValue('K'.($j+3), '=IF(ISERROR(AVERAGE(K3:K'.($j+2).')),0,AVERAGE(K3:K'.($j+2).'))');
                //=IF(ISERROR(AVERAGE(L3:L37)),0,AVERAGE(L3:L37))
                $objActSheet->setCellValue('L'.($j+3), '=ROUND(IF(ISERROR(AVERAGE(L3:L'.($j+2).')),0,AVERAGE(L3:L'.($j+2).')),2)');
                $objActSheet->setCellValue('M'.($j+3), '=ROUND(IF(ISERROR(AVERAGE(M3:M'.($j+2).')),0,AVERAGE(M3:M'.($j+2).')),2)');
                //=IF(ISERROR(STDEV(L3:L37)),0,STDEV(L3:L37))
                $objActSheet->setCellValue('L'.($j+3+1), '=ROUND(IF(ISERROR(STDEV(L3:L'.($j+2).')),0,STDEV(L3:L'.($j+2).')),2)');
            }


            //dd($reportlist);
            if(empty($dataArrTitle)){
              $objActSheet->setCellValue('A1', '訓練成效評估結果統計表');
              $objActSheet->setCellValue('A'.(sizeof($reportlist)+8+1+1+1+1+4), '訓期：');
            }else{
              $objActSheet->setCellValue('A1', $dataArrTitle[0]['TITLE']);
              $objActSheet->setCellValue('A'.(sizeof($reportlist)+8+1+1+1+1+4), str_replace('~0','~',str_replace('：0','：',$dataArrDate[0]['sdate_edate'])));
            }
            $objActSheet->setCellValue('G'.(sizeof($reportlist)+8+1+1+1+1+4),'製表：'.(Date("Y")-'1911').Date("/m/d"));
            $objActSheet->mergeCells('G'.(sizeof($reportlist)+8+1+1+1+1+4).':M'.(sizeof($reportlist)+8+1+1+1+1+4));
            $objActSheet->getStyle('G'.(sizeof($reportlist)+8+1+1+1+1+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            //Remark1
            if(empty($dataArrRemark1)){
              $objActSheet->setCellValue('A'.(sizeof($reportlist)+8), '1.本次調查問卷計發出0份，共回收0份，回收率0.00%。');
            }else{
              $objActSheet->setCellValue('A'.(sizeof($reportlist)+8), $dataArrRemark1[0]['REMARK1A'].$dataArrRemark1[0]['REMARK1B']);
            }
            $objActSheet->mergeCells('A'.(sizeof($reportlist)+8).':L'.(sizeof($reportlist)+8));
            $objActSheet->getStyle('A'.(sizeof($reportlist)+8))->getFont()->setName('標楷體');
            $objActSheet->getStyle('A'.(sizeof($reportlist)+8))->getFont()->setSize("12");

            //Remark2, 4
            if(empty($dataArr2STDDEV)){
              $objActSheet->setCellValue('A'.(sizeof($reportlist)+8+1), '2.平均數係將各答項非常同意、無意見、不同意、非常不同意，分別賦予5、4、3、2及1分，加總後之平均；總平均數為0.00。');
              $objActSheet->setCellValue('A'.(sizeof($reportlist)+8+1+1+1), '4.依常態分配之概念，平均數正負兩個標準差佔總次數分配的95%；以原始分數之平均數標準差(0.00)進行分析，2個標準差值之範圍為');
              $objActSheet->setCellValue('A'.(sizeof($reportlist)+8+1+1+1+1), '  0.00±(0.00)x2=0.00～0.00。');
            }else{
              $objActSheet->setCellValue('A'.(sizeof($reportlist)+8+1), '2.平均數係將各答項非常同意、無意見、不同意、非常不同意，分別賦予5、4、3、2及1分，加總後之平均；總平均數為'.$dataArr2STDDEV[0]['AVG'].'。');
              $objActSheet->setCellValue('A'.(sizeof($reportlist)+8+1+1+1), '4.依常態分配之概念，平均數正負兩個標準差佔總次數分配的95%；以原始分數之平均數標準差('.$dataArr2STDDEV[0]['STDDEV'].')進行分析，2個標準差值之範圍為');
              $objActSheet->setCellValue('A'.(sizeof($reportlist)+8+1+1+1+1), '  '.$dataArr2STDDEV[0]['AVG'].'±('.$dataArr2STDDEV[0]['STDDEV'].')x2='.$dataArr2STDDEV[0]['sngMax'].'～'.$dataArr2STDDEV[0]['sngMin'].'。');
            }
            $objActSheet->mergeCells('A'.(sizeof($reportlist)+8+1).':L'.(sizeof($reportlist)+8+1));
            $objActSheet->getStyle('A'.(sizeof($reportlist)+8+1))->getFont()->setName('標楷體');
            $objActSheet->getStyle('A'.(sizeof($reportlist)+8+1))->getFont()->setSize("12");

            $objActSheet->mergeCells('A'.(sizeof($reportlist)+8+1+1+1).':L'.(sizeof($reportlist)+8+1+1+1));
            $objActSheet->getStyle('A'.(sizeof($reportlist)+8+1+1+1))->getFont()->setName('標楷體');
            $objActSheet->getStyle('A'.(sizeof($reportlist)+8+1+1+1))->getFont()->setSize("12");

            $objActSheet->mergeCells('A'.(sizeof($reportlist)+8+1+1+1+1).':L'.(sizeof($reportlist)+8+1+1+1+1));
            $objActSheet->getStyle('A'.(sizeof($reportlist)+8+1+1+1+1))->getFont()->setName('標楷體');
            $objActSheet->getStyle('A'.(sizeof($reportlist)+8+1+1+1+1))->getFont()->setSize("12");

            //Remark3
            //=CONCATENATE("3.本次問卷調查之平均數值如轉化為百分位數（以100分為滿分），相當於",M38,"分。")
            //3.依常態分配之概念，平均數正負兩個標準差佔總次數分配的95%；以原始分數之平均數標準差(0.24)進行分析，2個標準差值之範圍為
            $objActSheet->setCellValue('A'.(sizeof($reportlist)+8+1+1), '=CONCATENATE("3.本次問卷調查之平均數值如轉化為百分位數（以100分為滿分），相當於",M'.(sizeof($reportlist)+3).',"分。")');
            $objActSheet->mergeCells('A'.(sizeof($reportlist)+8+1+1).':L'.(sizeof($reportlist)+8+1+1));
            $objActSheet->getStyle('A'.(sizeof($reportlist)+8+1+1))->getFont()->setName('標楷體');
            $objActSheet->getStyle('A'.(sizeof($reportlist)+8+1+1))->getFont()->setSize("12");

            //問答題
            //指定sheet
            $objActSheet = $objPHPExcel->getSheet(1);
            $reportlist2 = json_decode(json_encode($reportlist2), true);
            if(sizeof($reportlist2) != 0) {
              //項目數量迴圈
              $A4=1;
              $comments4 ='';
              $A7=1;
              $comments7 ='';
              $A10=1;
              $comments10 ='';
              $A13=1;
              $comments13 ='';
              for ($i=0; $i < sizeof($arraykeys2); $i++) {
                  for ($j=0; $j < sizeof($reportlist2); $j++) {
                      if($i==0 && $reportlist2[$j][$arraykeys2[$i]] <> ''){
                          $comments4 = $comments4.$A4.'.'.$reportlist2[$j][$arraykeys2[$i]]."\n";
                          $A4++;
                      }
                      if($i==1 && $reportlist2[$j][$arraykeys2[$i]] <> ''){
                          $comments7 = $comments7.$A7.'.'.$reportlist2[$j][$arraykeys2[$i]]."\n";
                          $A7++;
                      }
                      if($i==2 && $reportlist2[$j][$arraykeys2[$i]] <> ''){
                          $comments10 = $comments10.$A10.'.'.$reportlist2[$j][$arraykeys2[$i]]."\n";
                          $A10++;
                      }
                      if($i==3 && $reportlist2[$j][$arraykeys2[$i]] <> ''){
                        $comments13 = $comments13.$A13.'.'.$reportlist2[$j][$arraykeys2[$i]]."\n";
                        $A13++;
                      }

                  }
              }

              $objActSheet->setCellValue('A4', $comments4);
              //$objActSheet->getRowDimension(4)->setRowHeight(((strlen($comments4)/80+$A4)*14));
              $objActSheet->getStyle('A4')->getAlignment()->setWrapText(true);

              $objActSheet->setCellValue('A7', $comments7);
              //$objActSheet->getRowDimension(7)->setRowHeight(((strlen($comments7)/80+$A7)*14));
              $objActSheet->getStyle('A7')->getAlignment()->setWrapText(true);

              $objActSheet->setCellValue('A10', $comments10);
              //$objActSheet->getRowDimension(10)->setRowHeight(((strlen($comments10)/80+$A10)*14));
              $objActSheet->getStyle('A10')->getAlignment()->setWrapText(true);

              $objActSheet->setCellValue('A13', $comments13);
              //$objActSheet->getRowDimension(13)->setRowHeight(((strlen($comments13)/80+$A13)*14));
              $objActSheet->getStyle('A13')->getAlignment()->setWrapText(true);
          }

          //題目統計圖表
          $Fig=1;
          $Sindex=2;
          $PosStart=3;
          $PosEnd=3;
          //dd(ceil(sizeof($reportlist)/$qnum));
          if(ceil(sizeof($reportlist)/$qnum)>0){
            for ($i=0; $i < (ceil(sizeof($reportlist)/$qnum)); $i++) {

              //EX:qnum=10, 3-12, 13-22, 23-32, ...
              if($PosStart<>$PosEnd){
                $PosStart = $PosEnd + 1;
              }
              //最後一筆位置
              if(sizeof($reportlist)>($PosStart + $qnum -1)){
                $PosEnd = $PosStart + $qnum -1;
              }else{
                $PosEnd = sizeof($reportlist)+2;
              }


              $objPHPExcel->createSheet();
              $objPHPExcel->setActiveSheetIndex($Sindex);


              //指定sheet
              $objActSheet = $objPHPExcel->getSheet($Sindex);
              $objActSheet->setTitle("統計圖表".$Fig);
              $Sindex++;

              if(empty($dataArrTitle)){

              }else{
                  $title = new PHPExcel_Chart_Title(str_replace('訓練訓後評估結果統計表','訓後成效評估結果統計圖',$dataArrTitle[0]['TITLE']).'('.$Fig.')');
              }
              $X_title = new PHPExcel_Chart_Title('各題次問項內容');
              $Y_title = new PHPExcel_Chart_Title('百分位數');
              $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String', NULL, NULL, 1));
              $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', '題目統計!$A$'.$PosStart.':$A$'.$PosEnd, NULL, 100));
              $dsv = array(new \PHPExcel_Chart_DataSeriesValues('Number', '題目統計!$M$'.$PosStart.':$M$'.$PosEnd, NULL, 100));
              //顯示數值
              $layout=new PHPExcel_Chart_Layout();
              $layout->setShowVal(true);
              //設定最大值,這是1.81版才有的功能，若是1.80則無此功能
              $axis=new PHPExcel_Chart_Axis();
              $axis->setAxisOptionsProperties("nextTo", null, null, null, null, null,0, 100);
              //長條圖
              $ds = new \PHPExcel_Chart_DataSeries(\PHPExcel_Chart_DataSeries::TYPE_BARCHART, \PHPExcel_Chart_DataSeries::GROUPING_STANDARD, range(0, count($dsv) - 1), $dsl, $xal, $dsv);
              $pa = new \PHPExcel_Chart_PlotArea($layout, array($ds));
              $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, $layout, false);
              $chart1 = new PHPExcel_Chart('Chart1', $title, $legend, $pa, true,0,$X_title,$Y_title, $axis);
              $chart1->setTopLeftPosition('A1');
              $chart1->setBottomRightPosition('Q33');
              $objActSheet->addChart($chart1);

              $Fig++;
            }

          }

          // 設定下載 Excel 的檔案名稱
          $outputname="訓練訓後評估結果統計表(新版問卷)";

      } else {

            //TITLE
            $sqlTitleOld="SELECT DISTINCT CONCAT(SUBSTRING(t19tb.class,1,3),'年度',t01tb.name) AS TITLE,
                                    CONCAT('第',CASE t19tb.term WHEN '01' THEN '1'
                                                                WHEN '02' THEN '2'
                                                                WHEN '03' THEN '3'
                                                                WHEN '04' THEN '4'
                                                                WHEN '05' THEN '5'
                                                                WHEN '06' THEN '6'
                                                                WHEN '07' THEN '7'
                                                                WHEN '08' THEN '8'
                                                                WHEN '09' THEN '9'
                                                                ELSE t19tb.term END
                                                , '期訓後評估結果統計表')  AS TITLE_TERM
                        FROM t19tb INNER JOIN t01tb ON t19tb.class = t01tb.class
                        WHERE t19tb.class = '".$class."'
                        AND t19tb.term= '".$term."'
                        ORDER BY t19tb.class DESC
                    ";
            $reportlistTitleOld = DB::select($sqlTitleOld);
            $dataArrTitleOld=json_decode(json_encode(DB::select($sqlTitleOld)), true);

            //REMARK 3
            //不需在額外判斷最大次數
            $sqlRemark3Old="SELECT CONCAT('3.本次調查問卷計發出', A.lngCopy_Count, '份，共回收', B.lngBack_Count, '份，',
                                        '回收率',
                                        CASE WHEN A.lngCopy_Count = 0 THEN
                                              '0%。'
                                            ELSE
                                              CONCAT(FORMAT( B.lngBack_Count / A.lngCopy_Count * (100), 2), '%。')
                                        END ) AS REMARK3
                            FROM (
                            SELECT copy AS lngCopy_Count
                            FROM t19tb
                            WHERE class= '".$class."'
                                AND term= '".$term."'
                            ) A LEFT JOIN
                            ( SELECT COUNT(*) AS lngBack_Count
                                FROM t20tb
                            WHERE class='".$class."'
                                AND term= '".$term."'
                            ) B ON 1 = 1
                            ";
            $reportlistRemark3Old = DB::select($sqlRemark3Old);
            $dataArrRemark3Old=json_decode(json_encode(DB::select($sqlRemark3Old)), true);

            //題目統計
            $sqlOld="SELECT SUBSTRING(A.caption,INSTR(A.caption, '.')+1) AS caption,
                            (CASE WHEN A.key1='a01' THEN COUNT((CASE WHEN itema1=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a02' THEN COUNT((CASE WHEN itema2=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a03' THEN COUNT((CASE WHEN itema3=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a04' THEN COUNT((CASE WHEN itema4=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a05' THEN COUNT((CASE WHEN itema5=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a06' THEN COUNT((CASE WHEN itema6=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a07' THEN COUNT((CASE WHEN itema7=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a08' THEN COUNT((CASE WHEN itema8=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a09' THEN COUNT((CASE WHEN itema9=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a10' THEN COUNT((CASE WHEN itema10=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a11' THEN COUNT((CASE WHEN itema11=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a12' THEN COUNT((CASE WHEN itema12=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a13' THEN COUNT((CASE WHEN itema13=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a14' THEN COUNT((CASE WHEN itema14=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a15' THEN COUNT((CASE WHEN itema15=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a16' THEN COUNT((CASE WHEN itema16=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a17' THEN COUNT((CASE WHEN itema17=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a18' THEN COUNT((CASE WHEN itema18=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a19' THEN COUNT((CASE WHEN itema19=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a20' THEN COUNT((CASE WHEN itema20=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b01' THEN COUNT((CASE WHEN itema1=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b02' THEN COUNT((CASE WHEN itema2=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b03' THEN COUNT((CASE WHEN itema3=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b04' THEN COUNT((CASE WHEN itema4=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b05' THEN COUNT((CASE WHEN itema5=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b06' THEN COUNT((CASE WHEN itema6=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b07' THEN COUNT((CASE WHEN itema7=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b08' THEN COUNT((CASE WHEN itema8=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b09' THEN COUNT((CASE WHEN itema9=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b10' THEN COUNT((CASE WHEN itema10=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b11' THEN COUNT((CASE WHEN itema11=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b12' THEN COUNT((CASE WHEN itema12=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b13' THEN COUNT((CASE WHEN itema13=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b14' THEN COUNT((CASE WHEN itema14=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b15' THEN COUNT((CASE WHEN itema15=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b16' THEN COUNT((CASE WHEN itema16=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b17' THEN COUNT((CASE WHEN itema17=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b18' THEN COUNT((CASE WHEN itema18=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b19' THEN COUNT((CASE WHEN itema19=1 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b20' THEN COUNT((CASE WHEN itema20=1 THEN 1 ELSE NULL  END))
                                  ELSE 0
                              END) AS D1,
                            (CASE WHEN A.key1='a01' THEN COUNT((CASE WHEN itema1=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a02' THEN COUNT((CASE WHEN itema2=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a03' THEN COUNT((CASE WHEN itema3=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a04' THEN COUNT((CASE WHEN itema4=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a05' THEN COUNT((CASE WHEN itema5=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a06' THEN COUNT((CASE WHEN itema6=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a07' THEN COUNT((CASE WHEN itema7=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a08' THEN COUNT((CASE WHEN itema8=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a09' THEN COUNT((CASE WHEN itema9=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a10' THEN COUNT((CASE WHEN itema10=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a11' THEN COUNT((CASE WHEN itema11=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a12' THEN COUNT((CASE WHEN itema12=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a13' THEN COUNT((CASE WHEN itema13=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a14' THEN COUNT((CASE WHEN itema14=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a15' THEN COUNT((CASE WHEN itema15=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a16' THEN COUNT((CASE WHEN itema16=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a17' THEN COUNT((CASE WHEN itema17=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a18' THEN COUNT((CASE WHEN itema18=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a19' THEN COUNT((CASE WHEN itema19=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a20' THEN COUNT((CASE WHEN itema20=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b01' THEN COUNT((CASE WHEN itema1=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b02' THEN COUNT((CASE WHEN itema2=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b03' THEN COUNT((CASE WHEN itema3=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b04' THEN COUNT((CASE WHEN itema4=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b05' THEN COUNT((CASE WHEN itema5=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b06' THEN COUNT((CASE WHEN itema6=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b07' THEN COUNT((CASE WHEN itema7=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b08' THEN COUNT((CASE WHEN itema8=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b09' THEN COUNT((CASE WHEN itema9=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b10' THEN COUNT((CASE WHEN itema10=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b11' THEN COUNT((CASE WHEN itema11=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b12' THEN COUNT((CASE WHEN itema12=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b13' THEN COUNT((CASE WHEN itema13=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b14' THEN COUNT((CASE WHEN itema14=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b15' THEN COUNT((CASE WHEN itema15=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b16' THEN COUNT((CASE WHEN itema16=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b17' THEN COUNT((CASE WHEN itema17=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b18' THEN COUNT((CASE WHEN itema18=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b19' THEN COUNT((CASE WHEN itema19=2 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b20' THEN COUNT((CASE WHEN itema20=2 THEN 1 ELSE NULL  END))
                                  ELSE 0
                              END) AS D2,
                            (CASE WHEN A.key1='a01' THEN COUNT((CASE WHEN itema1=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a02' THEN COUNT((CASE WHEN itema2=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a03' THEN COUNT((CASE WHEN itema3=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a04' THEN COUNT((CASE WHEN itema4=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a05' THEN COUNT((CASE WHEN itema5=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a06' THEN COUNT((CASE WHEN itema6=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a07' THEN COUNT((CASE WHEN itema7=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a08' THEN COUNT((CASE WHEN itema8=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a09' THEN COUNT((CASE WHEN itema9=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a10' THEN COUNT((CASE WHEN itema10=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a11' THEN COUNT((CASE WHEN itema11=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a12' THEN COUNT((CASE WHEN itema12=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a13' THEN COUNT((CASE WHEN itema13=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a14' THEN COUNT((CASE WHEN itema14=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a15' THEN COUNT((CASE WHEN itema15=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a16' THEN COUNT((CASE WHEN itema16=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a17' THEN COUNT((CASE WHEN itema17=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a18' THEN COUNT((CASE WHEN itema18=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a19' THEN COUNT((CASE WHEN itema19=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a20' THEN COUNT((CASE WHEN itema20=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b01' THEN COUNT((CASE WHEN itema1=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b02' THEN COUNT((CASE WHEN itema2=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b03' THEN COUNT((CASE WHEN itema3=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b04' THEN COUNT((CASE WHEN itema4=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b05' THEN COUNT((CASE WHEN itema5=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b06' THEN COUNT((CASE WHEN itema6=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b07' THEN COUNT((CASE WHEN itema7=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b08' THEN COUNT((CASE WHEN itema8=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b09' THEN COUNT((CASE WHEN itema9=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b10' THEN COUNT((CASE WHEN itema10=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b11' THEN COUNT((CASE WHEN itema11=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b12' THEN COUNT((CASE WHEN itema12=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b13' THEN COUNT((CASE WHEN itema13=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b14' THEN COUNT((CASE WHEN itema14=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b15' THEN COUNT((CASE WHEN itema15=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b16' THEN COUNT((CASE WHEN itema16=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b17' THEN COUNT((CASE WHEN itema17=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b18' THEN COUNT((CASE WHEN itema18=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b19' THEN COUNT((CASE WHEN itema19=3 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b20' THEN COUNT((CASE WHEN itema20=3 THEN 1 ELSE NULL  END))
                                  ELSE 0
                              END) AS D3,
                            (CASE WHEN A.key1='a01' THEN COUNT((CASE WHEN itema1=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a02' THEN COUNT((CASE WHEN itema2=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a03' THEN COUNT((CASE WHEN itema3=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a04' THEN COUNT((CASE WHEN itema4=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a05' THEN COUNT((CASE WHEN itema5=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a06' THEN COUNT((CASE WHEN itema6=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a07' THEN COUNT((CASE WHEN itema7=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a08' THEN COUNT((CASE WHEN itema8=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a09' THEN COUNT((CASE WHEN itema9=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a10' THEN COUNT((CASE WHEN itema10=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a11' THEN COUNT((CASE WHEN itema11=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a12' THEN COUNT((CASE WHEN itema12=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a13' THEN COUNT((CASE WHEN itema13=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a14' THEN COUNT((CASE WHEN itema14=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a15' THEN COUNT((CASE WHEN itema15=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a16' THEN COUNT((CASE WHEN itema16=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a17' THEN COUNT((CASE WHEN itema17=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a18' THEN COUNT((CASE WHEN itema18=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a19' THEN COUNT((CASE WHEN itema19=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a20' THEN COUNT((CASE WHEN itema20=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b01' THEN COUNT((CASE WHEN itema1=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b02' THEN COUNT((CASE WHEN itema2=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b03' THEN COUNT((CASE WHEN itema3=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b04' THEN COUNT((CASE WHEN itema4=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b05' THEN COUNT((CASE WHEN itema5=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b06' THEN COUNT((CASE WHEN itema6=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b07' THEN COUNT((CASE WHEN itema7=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b08' THEN COUNT((CASE WHEN itema8=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b09' THEN COUNT((CASE WHEN itema9=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b10' THEN COUNT((CASE WHEN itema10=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b11' THEN COUNT((CASE WHEN itema11=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b12' THEN COUNT((CASE WHEN itema12=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b13' THEN COUNT((CASE WHEN itema13=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b14' THEN COUNT((CASE WHEN itema14=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b15' THEN COUNT((CASE WHEN itema15=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b16' THEN COUNT((CASE WHEN itema16=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b17' THEN COUNT((CASE WHEN itema17=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b18' THEN COUNT((CASE WHEN itema18=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b19' THEN COUNT((CASE WHEN itema19=4 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b20' THEN COUNT((CASE WHEN itema20=4 THEN 1 ELSE NULL  END))
                                  ELSE 0
                              END) AS D4,
                            (CASE WHEN A.key1='a01' THEN COUNT((CASE WHEN itema1=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a02' THEN COUNT((CASE WHEN itema2=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a03' THEN COUNT((CASE WHEN itema3=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a04' THEN COUNT((CASE WHEN itema4=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a05' THEN COUNT((CASE WHEN itema5=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a06' THEN COUNT((CASE WHEN itema6=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a07' THEN COUNT((CASE WHEN itema7=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a08' THEN COUNT((CASE WHEN itema8=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a09' THEN COUNT((CASE WHEN itema9=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a10' THEN COUNT((CASE WHEN itema10=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a11' THEN COUNT((CASE WHEN itema11=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a12' THEN COUNT((CASE WHEN itema12=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a13' THEN COUNT((CASE WHEN itema13=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a14' THEN COUNT((CASE WHEN itema14=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a15' THEN COUNT((CASE WHEN itema15=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a16' THEN COUNT((CASE WHEN itema16=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a17' THEN COUNT((CASE WHEN itema17=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a18' THEN COUNT((CASE WHEN itema18=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a19' THEN COUNT((CASE WHEN itema19=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='a20' THEN COUNT((CASE WHEN itema20=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b01' THEN COUNT((CASE WHEN itema1=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b02' THEN COUNT((CASE WHEN itema2=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b03' THEN COUNT((CASE WHEN itema3=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b04' THEN COUNT((CASE WHEN itema4=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b05' THEN COUNT((CASE WHEN itema5=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b06' THEN COUNT((CASE WHEN itema6=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b07' THEN COUNT((CASE WHEN itema7=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b08' THEN COUNT((CASE WHEN itema8=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b09' THEN COUNT((CASE WHEN itema9=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b10' THEN COUNT((CASE WHEN itema10=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b11' THEN COUNT((CASE WHEN itema11=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b12' THEN COUNT((CASE WHEN itema12=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b13' THEN COUNT((CASE WHEN itema13=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b14' THEN COUNT((CASE WHEN itema14=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b15' THEN COUNT((CASE WHEN itema15=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b16' THEN COUNT((CASE WHEN itema16=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b17' THEN COUNT((CASE WHEN itema17=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b18' THEN COUNT((CASE WHEN itema18=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b19' THEN COUNT((CASE WHEN itema19=5 THEN 1 ELSE NULL  END))
                                  WHEN A.key1='b20' THEN COUNT((CASE WHEN itema20=5 THEN 1 ELSE NULL  END))
                                  ELSE 0
                              END) AS D5
                      FROM
                            (  SELECT 'a01' AS key1, t19tb.themea1 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a02' AS key1, t19tb.themea2 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a03' AS key1, t19tb.themea3 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a04' AS key1, t19tb.themea4 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a05' AS key1, t19tb.themea5 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a06' AS key1, t19tb.themea6 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a07' AS key1, t19tb.themea7 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a08' AS key1, t19tb.themea8 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a09' AS key1, t19tb.themea9 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a10' AS key1, t19tb.themea10 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a11' AS key1, t19tb.themea11 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a12' AS key1, t19tb.themea12 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a13' AS key1, t19tb.themea13 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a14' AS key1, t19tb.themea14 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a15' AS key1, t19tb.themea15 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a16' AS key1, t19tb.themea16 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a17' AS key1, t19tb.themea17 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a18' AS key1, t19tb.themea18 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a19' AS key1, t19tb.themea19 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'a20' AS key1, t19tb.themea20 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b01' AS key1, t19tb.themeb1 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b02' AS key1, t19tb.themeb2 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b03' AS key1, t19tb.themeb3 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b04' AS key1, t19tb.themeb4 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b05' AS key1, t19tb.themeb5 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b06' AS key1, t19tb.themeb6 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b07' AS key1, t19tb.themeb7 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b08' AS key1, t19tb.themeb8 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b09' AS key1, t19tb.themeb9 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b10' AS key1, t19tb.themeb10 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b11' AS key1, t19tb.themeb11 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b12' AS key1, t19tb.themeb12 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b13' AS key1, t19tb.themeb13 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b14' AS key1, t19tb.themeb14 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b15' AS key1, t19tb.themeb15 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b16' AS key1, t19tb.themeb16 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b17' AS key1, t19tb.themeb17 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b18' AS key1, t19tb.themeb18 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b19' AS key1, t19tb.themeb19 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                              UNION ALL
                              SELECT 'b20' AS key1, t19tb.themeb20 AS caption
                                FROM t19tb
                                WHERE t19tb.class = '".$class."'
                                  AND t19tb.term = '".$term."'
                                  ) A INNER JOIN t20tb t20tb ON 1 = 1
                    WHERE t20tb.class= '".$class."'
                      AND t20tb.term= '".$term."'
                      AND SUBSTRING(A.caption,INSTR(A.caption, '.')+1) <> ''
                    GROUP BY A.caption, A.key1
                    ORDER BY A.key1
                    ";

            $reportlistOld = DB::select($sqlOld);
            $dataArrOld=json_decode(json_encode(DB::select($sqlOld)), true);
            //取出全部項目
            if(sizeof($reportlistOld) != 0) {
                $arraykeysOld=array_keys((array)$reportlistOld[0]);
            }

            // 檔案名稱
            $fileName = 'L22B';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel

            //$objPHPExcel = PHPExcel_IOFactory::load($filePath);
            $excelReader = PHPExcel_IOFactory::createReaderForFile($filePath);
            $excelReader->setReadDataOnly(false);
            $excelReader->setIncludeCharts(true);
            $objPHPExcel = $excelReader->load($filePath);

            //題目統計
            //指定sheet
            $objActSheet = $objPHPExcel->getActiveSheet();
            $objActSheet = $objPHPExcel->getSheet(0);
            $reportlistOld = json_decode(json_encode($reportlistOld), true);

            if(empty($dataArrTitleOld)){
              $objActSheet->setCellValue('A1', '年度');
              $objActSheet->setCellValue('E1', '訓後評估結果統計表');
            }else{
              $objActSheet->setCellValue('A1', ltrim($dataArrTitleOld[0]['TITLE'],'0'));
              $objActSheet->setCellValue('E1', $dataArrTitleOld[0]['TITLE_TERM']);
            }

            $lineName = 'C';
            if(sizeof($reportlistOld) != 0) {
            //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeysOld); $i++) {
                    //excel 欄位 1 == A, etc
                    if($i==0){
                        $lineName = 'A';
                    } elseif($i==1){
                        $lineName = 'B';
                    } elseif($i==2){
                        $lineName = 'D';
                    } elseif($i==3){
                        $lineName = 'F';
                    } elseif($i==4){
                        $lineName = 'H';
                    } elseif($i==5){
                        $lineName = 'J';
                    }else {
                        $lineName = 'A';
                    }
                    //資料by班別迴圈
                    for ($j=0; $j < sizeof($reportlistOld); $j++) {
                        //3開始
                        $objActSheet->setCellValue($lineName.($j+4), $reportlistOld[$j][$arraykeysOld[$i]]);

                        //C, E, G, I, K
                        //=ROUND(B4/(B4+D4+F4+H4+J4),4)
                        $objActSheet->setCellValue('C'.($j+4),'=IF(ISERROR(B'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,B'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).'))');
                        $objActSheet->setCellValue('E'.($j+4),'=IF(ISERROR(D'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,D'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).'))');
                        $objActSheet->setCellValue('G'.($j+4),'=IF(ISERROR(F'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,F'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).'))');
                        $objActSheet->setCellValue('I'.($j+4),'=IF(ISERROR(H'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,H'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).'))');
                        $objActSheet->setCellValue('K'.($j+4),'=IF(ISERROR(J'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,J'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).'))');

                        //L, M, N
                        //=ROUND((B4+D4)/(B4+D4+F4+H4+J4),4)
                        //=ROUND((G4)/(C4+E4+G4+I4+K4),4)
                        //=ROUND((H4+J4)/(B4+D4+F4+H4+J4),4)
                        $objActSheet->setCellValue('L'.($j+4),'=IF(ISERROR((B'.($j+4).'+D'.($j+4).')/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,(B'.($j+4).'+D'.($j+4).')/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).'))');
                        $objActSheet->setCellValue('M'.($j+4),'=IF(ISERROR((F'.($j+4).')/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,(F'.($j+4).')/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).'))');
                        $objActSheet->setCellValue('N'.($j+4),'=IF(ISERROR((H'.($j+4).'+J'.($j+4).')/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,(H'.($j+4).'+J'.($j+4).')/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).'))');
                    }
                }

                //框線
                $styleArray = array(
                                  'borders' => array(
                                      'allborders' => array(
                                       'style' => \PHPExcel_Style_Border::BORDER_THIN
                                    )
                                  )
                            );
                $objActSheet->getStyle('A4:N'.($j+3))->applyFromArray($styleArray);

                //C, E, G, I, K, L, M, N
                //=ROUND(AVERAGE(C4:C20),4)
                //=IF(ISERROR(AVERAGE(C4:C20)),0,AVERAGE(C4:C20))
                $objActSheet->setCellValue('C'.($j+4), '=IF(ISERROR(AVERAGE(C4:C'.($j+3).')),0,AVERAGE(C4:C'.($j+3).'))');
                $objActSheet->setCellValue('E'.($j+4), '=IF(ISERROR(AVERAGE(E4:E'.($j+3).')),0,AVERAGE(E4:E'.($j+3).'))');
                $objActSheet->setCellValue('G'.($j+4), '=IF(ISERROR(AVERAGE(G4:G'.($j+3).')),0,AVERAGE(G4:G'.($j+3).'))');
                $objActSheet->setCellValue('I'.($j+4), '=IF(ISERROR(AVERAGE(I4:I'.($j+3).')),0,AVERAGE(I4:I'.($j+3).'))');
                $objActSheet->setCellValue('K'.($j+4), '=IF(ISERROR(AVERAGE(K4:K'.($j+3).')),0,AVERAGE(K4:K'.($j+3).'))');
                $objActSheet->setCellValue('L'.($j+4), '=IF(ISERROR(AVERAGE(L4:L'.($j+3).')),0,AVERAGE(L4:L'.($j+3).'))');
                $objActSheet->setCellValue('M'.($j+4), '=IF(ISERROR(AVERAGE(M4:M'.($j+3).')),0,AVERAGE(M4:M'.($j+3).'))');
                $objActSheet->setCellValue('N'.($j+4), '=IF(ISERROR(AVERAGE(N4:N'.($j+3).')),0,AVERAGE(N4:N'.($j+3).'))');

            }

            //Remark1
            //=CONCATENATE("1.同意比係指非常同意與同意的總和，共計有",L21,"表示同意")
            //=CONCATENATE("1.同意比係指非常同意與同意的總和，共計有",ROUND(L21*100,2),"%表示同意")
            $objActSheet->setCellValue('A'.(sizeof($reportlistOld)+5+1), '=CONCATENATE("1.同意比係指非常同意與同意的總和，共計有",ROUND(L'.(sizeof($reportlistOld)+4).'*100,2),"%表示同意")');
            $objActSheet->getStyle('A'.(sizeof($reportlistOld)+5+1))->getFont()->setName('標楷體');
            $objActSheet->getStyle('A'.(sizeof($reportlistOld)+5+1))->getFont()->setSize("12");

            //Remark2
            //=CONCATENATE("2.不同意比係指不同意與非常不同意的總和，共計有",N21,"表示不同意")
            $objActSheet->setCellValue('A'.(sizeof($reportlistOld)+5+2), '=CONCATENATE("2.不同意比係指不同意與非常不同意的總和，共計有",ROUND(N'.(sizeof($reportlistOld)+4).'*100,2),"%表示不同意")');
            $objActSheet->getStyle('A'.(sizeof($reportlistOld)+5+2))->getFont()->setName('標楷體');
            $objActSheet->getStyle('A'.(sizeof($reportlistOld)+5+2))->getFont()->setSize("12");

            //Remark3
            if(empty($dataArrRemark3Old)){
              $objActSheet->setCellValue('A'.(sizeof($reportlistOld)+5+3), '3.本次調查問卷計發出0份，共回收0份，回收率0.00%。');
            }else{
              $objActSheet->setCellValue('A'.(sizeof($reportlistOld)+5+3), $dataArrRemark3Old[0]['REMARK3']);
            }
            //$objActSheet->mergeCells('A'.(sizeof($reportlist)+8).':L'.(sizeof($reportlist)+8));
            $objActSheet->getStyle('A'.(sizeof($reportlistOld)+5+3))->getFont()->setName('標楷體');
            $objActSheet->getStyle('A'.(sizeof($reportlistOld)+5+3))->getFont()->setSize("12");


            //指定sheet
            $objActSheet = $objPHPExcel->getSheet(2);
            $lineName = 'C';
            if(sizeof($reportlistOld) != 0) {
            //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeysOld); $i++) {
                    //excel 欄位 1 == A, etc
                    if($i==0){
                        $lineName = 'A';
                    } elseif($i==1){
                        $lineName = 'B';
                    } elseif($i==2){
                        $lineName = 'D';
                    } elseif($i==3){
                        $lineName = 'F';
                    } elseif($i==4){
                        $lineName = 'H';
                    } elseif($i==5){
                        $lineName = 'J';
                    }else {
                        $lineName = 'A';
                    }
                    //資料by班別迴圈
                    for ($j=0; $j < sizeof($reportlistOld); $j++) {
                        //3開始
                        $objActSheet->setCellValue($lineName.($j+4), $reportlistOld[$j][$arraykeysOld[$i]]);

                        //C, E, G, I, K
                        //=ROUND(B4/(B4+D4+F4+H4+J4),4)
                        $objActSheet->setCellValue('C'.($j+4),'=ROUND(IF(ISERROR(B'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,B'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),4)');
                        $objActSheet->setCellValue('E'.($j+4),'=ROUND(IF(ISERROR(D'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,D'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),4)');
                        $objActSheet->setCellValue('G'.($j+4),'=ROUND(IF(ISERROR(F'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,F'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),4)');
                        $objActSheet->setCellValue('I'.($j+4),'=ROUND(IF(ISERROR(H'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,H'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),4)');
                        $objActSheet->setCellValue('K'.($j+4),'=ROUND(IF(ISERROR(J'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,J'.($j+4).'/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),4)');

                        //L, M, N
                        //=ROUND((B4+D4)/(B4+D4+F4+H4+J4),4)
                        //=ROUND((G4)/(C4+E4+G4+I4+K4),4)
                        //=ROUND((H4+J4)/(B4+D4+F4+H4+J4),4)
                        $objActSheet->setCellValue('L'.($j+4),'=ROUND(IF(ISERROR((B'.($j+4).'+D'.($j+4).')/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,(B'.($j+4).'+D'.($j+4).')/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),4)');
                        $objActSheet->setCellValue('M'.($j+4),'=ROUND(IF(ISERROR((F'.($j+4).')/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,(F'.($j+4).')/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),4)');
                        $objActSheet->setCellValue('N'.($j+4),'=ROUND(IF(ISERROR((H'.($j+4).'+J'.($j+4).')/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),0,(H'.($j+4).'+J'.($j+4).')/(B'.($j+4).'+D'.($j+4).'+F'.($j+4).'+H'.($j+4).'+J'.($j+4).')),4)');
                    }
                }

                //C, E, G, I, K, L, M, N
                //=ROUND(AVERAGE(C4:C20),4)
                //=IF(ISERROR(AVERAGE(C4:C20)),0,AVERAGE(C4:C20))
                $objActSheet->setCellValue('C'.($j+4), '=ROUND(IF(ISERROR(AVERAGE(C4:C'.($j+3).')),0,AVERAGE(C4:C'.($j+3).')),4)');
                $objActSheet->setCellValue('E'.($j+4), '=ROUND(IF(ISERROR(AVERAGE(E4:E'.($j+3).')),0,AVERAGE(E4:E'.($j+3).')),4)');
                $objActSheet->setCellValue('G'.($j+4), '=ROUND(IF(ISERROR(AVERAGE(G4:G'.($j+3).')),0,AVERAGE(G4:G'.($j+3).')),4)');
                $objActSheet->setCellValue('I'.($j+4), '=ROUND(IF(ISERROR(AVERAGE(I4:I'.($j+3).')),0,AVERAGE(I4:I'.($j+3).')),4)');
                $objActSheet->setCellValue('K'.($j+4), '=ROUND(IF(ISERROR(AVERAGE(K4:K'.($j+3).')),0,AVERAGE(K4:K'.($j+3).')),4)');
                $objActSheet->setCellValue('L'.($j+4), '=ROUND(IF(ISERROR(AVERAGE(L4:L'.($j+3).')),0,AVERAGE(L4:L'.($j+3).')),4)');
                $objActSheet->setCellValue('M'.($j+4), '=ROUND(IF(ISERROR(AVERAGE(M4:M'.($j+3).')),0,AVERAGE(M4:M'.($j+3).')),4)');
                $objActSheet->setCellValue('N'.($j+4), '=ROUND(IF(ISERROR(AVERAGE(N4:N'.($j+3).')),0,AVERAGE(N4:N'.($j+3).')),4)');

            }

            //統計圖表
            //指定sheet
            $objActSheet = $objPHPExcel->getSheet(1);
            if(empty($dataArrTitleOld)){
                $title = new PHPExcel_Chart_Title('');
            }else{
                $title = new PHPExcel_Chart_Title(ltrim($dataArrTitleOld[0]['TITLE'],'0').$dataArrTitleOld[0]['TITLE_TERM']);
            }
            $X_title = new PHPExcel_Chart_Title('各題次問項內容');
            $Y_title = new PHPExcel_Chart_Title('百分位數');
            $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String', 'Sheet3!$L$3', NULL, 1),
                         new \PHPExcel_Chart_DataSeriesValues('String', 'Sheet3!$M$3', NULL, 1),
                         new \PHPExcel_Chart_DataSeriesValues('String', 'Sheet3!$N$3', NULL, 1)
                        );
            $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', 'Sheet3!$A$4:$A$'.(sizeof($reportlistOld)+3), NULL, 100));
            $dsv = array(new \PHPExcel_Chart_DataSeriesValues('Number', 'Sheet3!$L$4:$L$'.(sizeof($reportlistOld)+3), NULL, 100),
                         new \PHPExcel_Chart_DataSeriesValues('Number', 'Sheet3!$M$4:$M$'.(sizeof($reportlistOld)+3), NULL, 100),
                         new \PHPExcel_Chart_DataSeriesValues('Number', 'Sheet3!$N$4:$N$'.(sizeof($reportlistOld)+3), NULL, 100)
                        );
            //顯示數值
            $layout=new PHPExcel_Chart_Layout();
            $layout->setShowVal(true);
            //設定最大值,這是1.81版才有的功能，若是1.80則無此功能
            $axis=new PHPExcel_Chart_Axis();
            $axis->setAxisOptionsProperties("nextTo", null, null, null, null, null,0, 1.2);
            //長條圖
            $ds = new \PHPExcel_Chart_DataSeries(\PHPExcel_Chart_DataSeries::TYPE_BARCHART, \PHPExcel_Chart_DataSeries::GROUPING_STANDARD, range(0, count($dsv) - 1), $dsl, $xal, $dsv);
            $pa = new \PHPExcel_Chart_PlotArea($layout, array($ds));
            $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, $layout, false);
            $chart1 = new PHPExcel_Chart('Chart1', $title, $legend, $pa, true,0,$X_title,$Y_title, $axis);
            $chart1->setTopLeftPosition('A1');
            $chart1->setBottomRightPosition('Q33');
            $objActSheet->addChart($chart1);

            $outputname="訓練訓後評估結果統計表(舊版問卷)";
      }

      $RptBasic = new \App\Rptlib\RptBasic();
      $RptBasic->exportfile($objPHPExcel,"3",$request->input('doctype'),$outputname);
      //$obj: entity of file
      //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
      //$doctype:1.ooxml 2.odf
      //$filename:filename 
    }

}
