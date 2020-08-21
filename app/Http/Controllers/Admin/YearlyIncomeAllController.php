<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Services\User_groupService;

class YearlyIncomeAllController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('yearly_income_all', $user_group_auth)){
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
        $result="";
        return view('admin/yearly_income_all/list',compact('result'));
    }
    public function export(Request $request){
        $yerly=$request->input('yerly');

        $sql="Select t06tb.date, m01tb.cname, '50' as incometype, t09tb.lecthr, t09tb.lectamt, t09tb.teachtot, t09tb.deductamt, t09tb.netpay, t01tb.name, t09tb.term, t09tb.lectamt, t09tb.noteamt, t09tb.speakamt,t09tb.idno
        From t01tb, t06tb, t09tb, m01tb
        Where t01tb.class=t09tb.class
        and t06tb.class=t09tb.class And t06tb.term = t09tb.term And t06tb.course = t09tb.course
        and m01tb.idno=t09tb.idno
        and substring(t06tb.class,1,3)='".$yerly."'
        and not (t09tb.lectamt=0 and t09tb.noteamt=0 and t09tb.speakamt=0)
        Order by t09tb.idno, t06tb.date";

        $temp = json_decode(json_encode(DB::select($sql)), true);

        if($temp==[]){
            $result="此條件查無資料，請重新查詢。";
            return view('admin/yearly_income_all/list',compact('result'));
        }

        $data = $temp;
        $datakeys=array_keys((array)$data[0]);

         // 檔案名稱
        $fileName = 'H11';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel，
        $objPHPExcel = IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $objActSheet->setCellValue('A2',"行政院人事行政總處公務人力發展學院".$yerly."年度講座授課總時數及鐘點費統計表");
        //initial counting variables
        $rowcnt=5;
        $dsum=0;
        $fsum=0;
        $gsum=0;
        $hsum=0;
        $dtotal=0;
        $ftotal=0;
        $gtotal=0;
        $htotal=0;
        $tmpname="";
        $tmpid="";

        //fill values
        for($i=0;$i<sizeof($data);$i++){
            if($i==0){
                for($j=0;$j<9;$j++){
                    if($j==0)
                        $objActSheet->setCellValue($this->getNameFromNumber($j+1).strval($i+$rowcnt),substr($data[$i][$datakeys[$j]],0,3).".".substr($data[$i][$datakeys[$j]],3,2).".".substr($data[$i][$datakeys[$j]],5,2));
                    else
                        $objActSheet->setCellValue($this->getNameFromNumber($j+1).strval($i+$rowcnt),$data[$i][$datakeys[$j]]);
                }
                $tmpname=$data[$i]["cname"];
                $tmpid=$data[$i]["idno"];
                $dsum+=(int)$data[$i]["lecthr"];
                $fsum+=(int)$data[$i]["teachtot"];
                $gsum+=(int)$data[$i]["deductamt"];
                $hsum+=(int)$data[$i]["netpay"];
                $dtotal+=(float)$data[$i]["lecthr"];
                $ftotal+=(int)$data[$i]["teachtot"];
                $gtotal+=(int)$data[$i]["deductamt"];
                $htotal+=(int)$data[$i]["netpay"];
            }else{
                if($tmpid==$data[$i]["idno"]){
                    for($j=0;$j<9;$j++){
                        if($j==0 && $data[$i][$datakeys[$j]]!="")
                            $objActSheet->setCellValue($this->getNameFromNumber($j+1).strval($i+$rowcnt),substr($data[$i][$datakeys[$j]],0,3).".".substr($data[$i][$datakeys[$j]],3,2).".".substr($data[$i][$datakeys[$j]],5,2));
                        else
                            $objActSheet->setCellValue($this->getNameFromNumber($j+1).strval($i+$rowcnt),$data[$i][$datakeys[$j]]);
                    }
                    $dsum+=(int)$data[$i]["lecthr"];
                    $fsum+=(int)$data[$i]["teachtot"];
                    $gsum+=(int)$data[$i]["deductamt"];
                    $hsum+=(int)$data[$i]["netpay"];
                    $dtotal+=(float)$data[$i]["lecthr"];
                    $ftotal+=(int)$data[$i]["teachtot"];
                    $gtotal+=(int)$data[$i]["deductamt"];
                    $htotal+=(int)$data[$i]["netpay"];


                }else{

                    $objActSheet->setCellValue('A'.strval($i+$rowcnt),"合計");
                    $objActSheet->setCellValue('B'.strval($i+$rowcnt),$tmpname);
                    $objActSheet->setCellValue('D'.strval($i+$rowcnt),$dsum);
                    $objActSheet->setCellValue('F'.strval($i+$rowcnt),$fsum);
                    $objActSheet->setCellValue('G'.strval($i+$rowcnt),$gsum);
                    $objActSheet->setCellValue('H'.strval($i+$rowcnt),$hsum);
                    $rowcnt++;

                    for($j=0;$j<9;$j++){
                        if($j==0)
                            $objActSheet->setCellValue($this->getNameFromNumber($j+1).strval($i+$rowcnt),substr($data[$i][$datakeys[$j]],0,3).".".substr($data[$i][$datakeys[$j]],3,2).".".substr($data[$i][$datakeys[$j]],5,2));
                        else
                            $objActSheet->setCellValue($this->getNameFromNumber($j+1).strval($i+$rowcnt),$data[$i][$datakeys[$j]]);
                    }

                    $tmpname=$data[$i]["cname"];
                    $tmpid=$data[$i]["idno"];
                    $dsum=(int)$data[$i]["lecthr"];
                    $fsum=(int)$data[$i]["teachtot"];
                    $gsum=(int)$data[$i]["deductamt"];
                    $hsum=(int)$data[$i]["netpay"];
                    $dtotal+=(float)$data[$i]["lecthr"];
                    $ftotal+=(int)$data[$i]["teachtot"];
                    $gtotal+=(int)$data[$i]["deductamt"];
                    $htotal+=(int)$data[$i]["netpay"];


                }

            }
        }

        $objActSheet->setCellValue('A'.strval(sizeof($data)+$rowcnt),"合計");
        $objActSheet->setCellValue('B'.strval(sizeof($data)+$rowcnt),$tmpname);
        $objActSheet->setCellValue('D'.strval(sizeof($data)+$rowcnt),$dsum);
        $objActSheet->setCellValue('F'.strval(sizeof($data)+$rowcnt),$fsum);
        $objActSheet->setCellValue('G'.strval(sizeof($data)+$rowcnt),$gsum);
        $objActSheet->setCellValue('H'.strval(sizeof($data)+$rowcnt),$hsum);

        $add=48-((sizeof($data)+$rowcnt-3)%48);

        $objActSheet->setCellValue('A'.strval(sizeof($data)+$rowcnt+$add-1),"合計");
        $objActSheet->setCellValue('D'.strval(sizeof($data)+$rowcnt+$add-1),$dtotal);
        $objActSheet->setCellValue('F'.strval(sizeof($data)+$rowcnt+$add-1),$ftotal);
        $objActSheet->setCellValue('G'.strval(sizeof($data)+$rowcnt+$add-1),$gtotal);
        $objActSheet->setCellValue('H'.strval(sizeof($data)+$rowcnt+$add-1),$htotal);

        $objActSheet->mergeCells('A'.strval(sizeof($data)+$rowcnt+$add).':L'.strval(sizeof($data)+$rowcnt+$add));
        $objActSheet->setCellValue('A'.strval(sizeof($data)+$rowcnt+$add),"註：〔所得類別〕 '50'代表薪資及演講費、'9B'代表稿費");
        $objActSheet->getStyle('A4:J'.strval(sizeof($data)+$rowcnt+$add))->applyFromArray($styleArray);
        $objActSheet->getStyle('A4:J'.strval(sizeof($data)+$rowcnt+$add))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $objActSheet->getColumnDimension('K')->setVisible(false);
        $objActSheet->getColumnDimension('L')->setVisible(false);
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),$yerly.'年度講座所得統計表');
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
