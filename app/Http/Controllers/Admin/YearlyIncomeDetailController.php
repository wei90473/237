<?php
namespace App\Http\Controllers\Admin;
set_time_limit(0);

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Services\User_groupService;

class YearlyIncomeDetailController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('yearly_income_detail', $user_group_auth)){
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
        $sql="SELECT
        cname,
        Case Upper(sex)
             WHEN 'F' THEN '女'
             WHEN 'M' THEN '男'
             Else ''
        END As sex,dept, idno
        FROM m01tb WHERE LTRIM(RTRIM(cname))='".$request->input('tname')."'";

        $temp=DB::select($sql);

        $idnoArr=$temp;
        $result="";
        return view('admin/yearly_income_detail/list',compact('result','idnoArr'));
    }
    public function getidno(Request $request){
        $sql="SELECT cname,
        Case Upper(sex)
             WHEN 'F' THEN '女'
             WHEN 'M' THEN '男'
             Else ''
        END As sex,dept,idno
        FROM m01tb WHERE LTRIM(RTRIM(cname))='".$request->input('tname')."'";
        $temp=DB::select($sql);
        $idnoArr=$temp;
        return $idnoArr;

    }

    public function export(Request $request){
        $idno=$request->input('idno');
        $tname=$request->input('tname');
        $yerly=trim($request->input('yerly'));
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        if($idno!="0"){ //retrieve personal data
            $sql="SELECT
            CONCAT(CAST(CAST(SUBSTRING(B.date,1,3) AS int)AS char),'.',SUBSTRING(B.date,4,2),'.',SUBSTRING(B.date,6,2)) AS 日期 ,
             (
                          CASE
                           WHEN (lectamt>0 OR speakamt>0) AND noteamt>0 THEN '50,9B'
                           WHEN (lectamt>0 OR speakamt>0) AND noteamt=0 THEN '50'
                           WHEN lectamt=0 AND speakamt=0  AND noteamt>0 THEN '9B'
                           ELSE '50'
                          END
                         ) as 所得類別 ,
            B.hour as 時數 ,
             (CASE WHEN A.lecthr>0 THEN (A.lectamt/A.lecthr) ELSE 0 END) as 單價 ,
            A.teachtot as 給付總額 ,
            A.deductamt as 扣繳稅額 ,
             (A.teachtot - A.deductamt) as 給付淨額,
            CONCAT(RTRIM(C.name),A.term) as 訓練班別 ,
            A.idno,D.cname
            FROM t09tb A
            INNER JOIN t06tb B ON A.class=B.class AND A.term=B.term AND A.course=B.course
            INNER JOIN t01tb C ON A.class=C.class
            LEFT JOIN m01tb D ON A.idno=D.idno
            WHERE
            (A.lectamt>0 OR
            noteamt>0 OR
            speakamt>0)
            AND SUBSTRING(B.date,1,3) = '".$yerly."' AND A.idno='".$idno."'
            ORDER BY B.date ";

            $temp=json_decode(json_encode(DB::select($sql)), true);

            if($temp==[]){
                $sql="SELECT
                cname,
                Case Upper(sex)
                     WHEN 'F' THEN '女'
                     WHEN 'M' THEN '男'
                     Else ''
                END As sex,dept, idno
                FROM m01tb WHERE LTRIM(RTRIM(cname))='".$request->input('tname')."'";
                $temp=DB::select($sql);
                $idnoArr=$temp;
                $result = '查無資料，請重新查詢。';
                return view('admin/yearly_income_detail/list',compact('result','idnoArr'));
            }

            $data=$temp;
            $datakey=array_keys((array)$data[0]);


        // 範本檔案名稱
        $fileName = 'H12';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        for($i=0;$i<sizeof($data); $i++) {
            for($j=0;$j<sizeof($datakey)-2; $j++) {
                $objActSheet->setCellValue($this->getNameFromNumber($j+1).strval($i+6),$data[$i][$datakey[$j]]);
            }
        }

        $objActSheet->setCellValue('A1',"行政院人事行政總處公務人力發展學院".$yerly."年度講座所得清單明細表");
        $objActSheet->setCellValue('A3',"講座姓名：".$tname."   身分證字號：".$idno);

        $objActSheet->mergeCells('A'.(sizeof($data)+7).":I".(sizeof($data)+7));
        $objActSheet->getStyle('A'.(sizeof($data)+7))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $objActSheet->setCellValue('A'.(sizeof($data)+7),"註：〔所得類別〕 '50'代表薪資及演講費、'9B'代表稿費");

        $objActSheet->setCellValue('A'.(sizeof($data)+6),"小計");
        $objActSheet->setCellValue('C'.(sizeof($data)+6),"=SUM(C6:C".(sizeof($data)+5).")");
        $objActSheet->setCellValue('E'.(sizeof($data)+6),"=SUM(E6:E".(sizeof($data)+5).")");
        $objActSheet->setCellValue('F'.(sizeof($data)+6),"=SUM(F6:F".(sizeof($data)+5).")");
        $objActSheet->setCellValue('G'.(sizeof($data)+6),"=SUM(G6:G".(sizeof($data)+5).")");

        $objActSheet->setTitle('1');

         //apply borders
         $objActSheet->getStyle('A6:I'.(sizeof($data)+6))->applyFromArray($styleArray);
         $RptBasic = new \App\Rptlib\RptBasic();
         $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"年度講座所得明細表");
         //$obj: entity of file
         //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
         //$doctype:1.ooxml 2.odf
         //$filename:filename 
               
        }else{ //retrieve data of one year

            $sql="SELECT
            CONCAT(CAST(CAST(SUBSTRING(B.date,1,3) AS int)AS char),'.',SUBSTRING(B.date,4,2),'.',SUBSTRING(B.date,6,2)) AS 日期 ,
             (
                          CASE
                           WHEN (lectamt>0 OR speakamt>0) AND noteamt>0 THEN '50,9B'
                           WHEN (lectamt>0 OR speakamt>0) AND noteamt=0 THEN '50'
                           WHEN lectamt=0 AND speakamt=0  AND noteamt>0 THEN '9B'
                           ELSE '50'
                          END
                         ) as 所得類別 ,
            B.hour as 時數 ,
             (CASE WHEN A.lecthr>0 THEN (A.lectamt/A.lecthr) ELSE 0 END) as 單價 ,
            A.teachtot as 給付總額 ,
            A.deductamt as 扣繳稅額 ,
             (A.teachtot - A.deductamt) as 給付淨額,
            CONCAT(RTRIM(C.name),A.term) as 訓練班別 ,
            A.idno,D.cname
            FROM t09tb A
            INNER JOIN t06tb B ON A.class=B.class AND A.term=B.term AND A.course=B.course
            INNER JOIN t01tb C ON A.class=C.class
            LEFT JOIN m01tb D ON A.idno=D.idno
            WHERE
            (A.lectamt>0 OR
            noteamt>0 OR
            speakamt>0)
            AND SUBSTRING(B.date,1,3) = '".$yerly."' ORDER BY B.date ";

            $temp=json_decode(json_encode(DB::select($sql)), true);

            if($temp==[]){
                $sql="SELECT
                cname,
                Case Upper(sex)
                     WHEN 'F' THEN '女'
                     WHEN 'M' THEN '男'
                     Else ''
                END As sex,dept, idno
                FROM m01tb WHERE LTRIM(RTRIM(cname))='".$request->input('tname')."'";
                $temp=DB::select($sql);
                $idnoArr=$temp;
                $result = '查無資料，請重新查詢。';
                return view('admin/yearly_income_detail/list',compact('result','idnoArr'));
            }

            $data=$temp;
            $datakey=array_keys((array)$data[0]);


            // 範本檔案名稱
            $fileName = 'H12';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel

            $objPHPExcel = IOFactory::load($filePath);
            $objActSheet = $objPHPExcel->getActiveSheet();
            $sheetcnt=1;
            $rowcnt=6;
            $tmpid="start";

            //fill values
            for($i=0; $i<sizeof($data); $i++) {

                if($tmpid!="start" && ($tmpid!=$data[$i]["idno"])){
                    $objgetSheet->mergeCells('A'.($rowcnt+1).":I".($rowcnt+1));
                    $objgetSheet->getStyle('A'.($rowcnt+1).":I".($rowcnt+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $objgetSheet->setCellValue('A'.($rowcnt+1),"註：〔所得類別〕 '50'代表薪資及演講費、'9B'代表稿費");

                    $objgetSheet->setCellValue('A'.$rowcnt,"小計");
                    $objgetSheet->setCellValue('C'.$rowcnt,"=SUM(C6:C".($rowcnt-1).")");
                    $objgetSheet->setCellValue('E'.$rowcnt,"=SUM(E6:E".($rowcnt-1).")");
                    $objgetSheet->setCellValue('F'.$rowcnt,"=SUM(F6:F".($rowcnt-1).")");
                    $objgetSheet->setCellValue('G'.$rowcnt,"=SUM(G6:G".($rowcnt-1).")");

                    //apply borders
                    $objgetSheet->getStyle('A5:I'.($rowcnt))->applyFromArray($styleArray);

                    $rowcnt=6;
                }

                if($rowcnt==6){
                    $clonedWorksheet= clone $objPHPExcel->getSheet(0);
                    $clonedWorksheet->setTitle(strval($sheetcnt));
                    $objPHPExcel->addSheet($clonedWorksheet);
                    $objgetSheet=$objPHPExcel->getSheet($sheetcnt);
                    $sheetcnt++;

                    $objgetSheet->setCellValue('A1',"行政院人事行政總處公務人力發展學院".$yerly."年度講座所得清單明細表");
                    $objgetSheet->setCellValue('A3',"講座姓名：".$data[$i]["cname"]."   身分證字號：".$data[$i]["idno"]);

                }
                for($j=0;$j<sizeof($datakey)-3; $j++) {
                    $objgetSheet->setCellValue($this->getNameFromNumber($j+1).strval($rowcnt),$data[$i][$datakey[$j]]);
                }
                $tmpid=$data[$i]["idno"];
                $rowcnt++;

                // max processing records in test env.
                // if($i>200)
                //     break;

            }

            $objgetSheet->mergeCells('A'.($rowcnt+1).":I".($rowcnt+1));
            $objgetSheet->getStyle('A'.($rowcnt+1).":I".($rowcnt+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $objgetSheet->setCellValue('A'.($rowcnt+1),"註：〔所得類別〕 '50'代表薪資及演講費、'9B'代表稿費");

            $objgetSheet->setCellValue('A'.$rowcnt,"小計");
            $objgetSheet->setCellValue('C'.$rowcnt,"=SUM(C6:C".($rowcnt-1).")");
            $objgetSheet->setCellValue('E'.$rowcnt,"=SUM(E6:E".($rowcnt-1).")");
            $objgetSheet->setCellValue('F'.$rowcnt,"=SUM(F6:F".($rowcnt-1).")");
            $objgetSheet->setCellValue('G'.$rowcnt,"=SUM(G6:G".($rowcnt-1).")");

            //apply borders
            $objgetSheet->getStyle('A5:I'.($rowcnt))->applyFromArray($styleArray);

            // $objgetSheet->mergeCells('A'.$rowcnt.":I".$rowcnt);
            // $objgetSheet->getStyle('A'.$rowcnt)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            // $objgetSheet->setCellValue('A'.($rowcnt),"註：〔所得類別〕 '50'代表薪資及演講費、'9B'代表稿費");
            // //apply borders
            // $objgetSheet->getStyle('A5:I'.($rowcnt-1))->applyFromArray($styleArray);

            $objPHPExcel->removeSheetByIndex(0);
            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"年度講座所得明細表");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 
           
        }
    }
}
