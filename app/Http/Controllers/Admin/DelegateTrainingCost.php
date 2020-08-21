<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
// Models
use App\Models\SpecialClassFee;
use App\Models\T04tb;

use DateTIme;

class DelegateTrainingCost extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('delegate_training_cost', $user_group_auth)){
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
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=$RptBasic->getTerms($temp[0][$arraykeys[0]]);
        $termArr=$temp;
        $result="";
        return view('admin/delegate_training_cost/list',compact('classArr','termArr' ,'result'));

    }

    public function getTerms(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $termArr=$RptBasic->getTerms($request->input('classes'));
        return $termArr;
    }

    public function export(Request $request)
    {
        // $sql = "SELECT * FROM special_class_fee WHERE class=? AND term=? ";
        $t04tbInfo = $request->only(['class', 'term']);
        $t04tb = T04tb::where($t04tbInfo)->first();
        // dd($t04tb);
        if (empty($t04tb)) {
            die('找不到該班期');
        }elseif ($t04tb->t01tb->process != 2){
            die('非委訓班');
        }

        $isQuotaOver40 = $t04tb->t13tbs()->count() > 40;

        $specailClassFee = $t04tb->specailClassFee;
        if (empty($specailClassFee)){
            die('該班級無費用資料');
        }

        // 檔案名稱
        $fileName = 'F15';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        //  班別及訓期
        $worksheet->getCell('A5')->setValue($t04tb->t01tb->name.' 第'.$t04tb->term.'期'."\n".$t04tb->sdate.'-'.$t04tb->edate);

        // 委訓單位：
        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $payable = $richText->createTextRun('委訓單位：');
        $payable->getFont()->setBold(true);
        $payable->getFont()->setName('標楷體');
        $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color('0000FF') );

        if (isset($t04tb->t01tb->intrsutClassOrg)){
            $richText->createText($t04tb->t01tb->intrsutClassOrg->enrollname);
        }
        
        $worksheet->getCell('A3')->setValue($richText);

        $remark = [
        "備註：
1.業務費包含講座鐘點費、業務支出、學員伙食費及額外項等項目(A=B+C+D+E)，扣除額外
    項費用，平均每人每天業務費820元。
2.業務費請儘速撥入本學院銀行帳戶：中央銀行國庫局（代號0000022）、帳號：
    24033402128004、戶名「行政院人事行政總處公務人力發展學院」。
3.各項目不足支應時，得由本學院自行調整勻支。
        ",
        "備註：
1.業務費包含講座鐘點費、業務支出、學員伙食費及額外項等項目(A=B+C+D+E)
2.業務費請儘速撥入本學院銀行帳戶：中央銀行國庫局（代號0000022）、帳號：
    24033402128004、戶名「行政院人事行政總處公務人力發展學院」。
3.各項目不足支應時，得由本學院自行調整勻支。        
        "];

        $remark = ($isQuotaOver40) ? $remark[0] : $remark[1];
        $worksheet->getCell('C3')->setValue('     中華民國'.$t04tb->t01tb->yerly.'年度');
        $worksheet->getCell('A23')->setValue($remark);

        // A-業務費
        $worksheet->getCell('D5')->setValue((int)$specailClassFee->service_fee_quantity); // 數量
        $worksheet->getCell('E5')->setValue((int)$specailClassFee->service_fee_days); // 天數
        $worksheet->getCell('F5')->setValue((int)$specailClassFee->service_fee_unit_price); // 單價
        $worksheet->getCell('G5')->setValue((int)$specailClassFee->service_fee_budget); // 預算

        // B-鐘點費
        $worksheet->getCell('D8')->setValue((int)$specailClassFee->hourly_fee_quantity); // 數量
        $worksheet->getCell('G8')->setValue((int)$specailClassFee->hourly_fee_budget); // 預算

        // B-1外聘
        $worksheet->getCell('D9')->setValue((int)$specailClassFee->oh_hourly_fee_quantity); // 數量
        $worksheet->getCell('F9')->setValue((int)$specailClassFee->oh_hourly_fee_unit_price); // 單價
        $worksheet->getCell('G9')->setValue((int)$specailClassFee->oh_hourly_fee_budget); // 預算

        // B-2外聘隸屬
        $worksheet->getCell('D10')->setValue((int)$specailClassFee->ohbe_hourly_fee_quantity); // 數量
        $worksheet->getCell('F10')->setValue((int)$specailClassFee->ohbe_hourly_fee_unit_price); // 單價
        $worksheet->getCell('G10')->setValue((int)$specailClassFee->ohbe_hourly_fee_budget); // 預算

        // B-3內聘
        $worksheet->getCell('D11')->setValue((int)$specailClassFee->ih_hourly_fee_quantity); // 數量
        $worksheet->getCell('F11')->setValue((int)$specailClassFee->ih_hourly_fee_unit_price); // 單價
        $worksheet->getCell('G11')->setValue((int)$specailClassFee->ih_hourly_fee_budget); // 預算

        // B-4助教
        $worksheet->getCell('D12')->setValue((int)$specailClassFee->ass_hourly_fee_quantity); // 數量
        $worksheet->getCell('F12')->setValue((int)$specailClassFee->ass_hourly_fee_unit_price); // 單價
        $worksheet->getCell('G12')->setValue((int)$specailClassFee->ass_hourly_fee_budget); // 預算

        // C-業務支出
        $worksheet->getCell('D14')->setValue((int)$specailClassFee->business_pay_quantity); // 數量
        $worksheet->getCell('F14')->setValue((int)$specailClassFee->business_pay_unit_price); // 單價
        $worksheet->getCell('G14')->setValue((int)$specailClassFee->business_pay_budget); // 預算

        // D-伙食費
        $worksheet->getCell('D16')->setValue((int)$specailClassFee->food_expenses_quantity); // 數量
        $worksheet->getCell('E16')->setValue((int)$specailClassFee->food_expenses_days); // 天數
        $worksheet->getCell('F16')->setValue((int)$specailClassFee->food_expenses_unit_price); // 單價
        $worksheet->getCell('G16')->setValue((int)$specailClassFee->food_expenses_budget); // 預算

        // E-額外項
        $worksheet->getCell('D19')->setValue((int)$specailClassFee->extra_quantity); // 數量
        $worksheet->getCell('G19')->setValue((int)$specailClassFee->extra_budget); // 預算

        if ($isQuotaOver40){
            // E-1租車
            $worksheet->getCell('B20')->setValue('E-1租車'); // 數量
            $worksheet->getCell('C20')->setValue('輛');
            $worksheet->getCell('D20')->setValue((int)$specailClassFee->rent_car_quantity); // 數量
            $worksheet->getCell('F20')->setValue((int)$specailClassFee->rent_car_unit_price); // 單價
            $worksheet->getCell('G20')->setValue((int)$specailClassFee->rent_car_budget); // 預算

            // E-2保險
            $worksheet->getCell('B21')->setValue('E-2保險'); // 數量
            $worksheet->getCell('C21')->setValue('人');
            $worksheet->getCell('D21')->setValue((int)$specailClassFee->insurance_quantity); // 數量
            $worksheet->getCell('F21')->setValue((int)$specailClassFee->insurance_unit_price); // 單價
            $worksheet->getCell('G21')->setValue((int)$specailClassFee->insurance_budget); // 預算

            $worksheet->getCell('B22')->setValue('');
            $worksheet->getCell('C22')->setValue('');
            $worksheet->getCell('D22')->setValue('');
            $worksheet->getCell('F22')->setValue('');
            $worksheet->getCell('G22')->setValue('');
        }else{
            // E-1租車
            $worksheet->getCell('B20')->setValue(''); // 數量
            $worksheet->getCell('C20')->setValue('');
            $worksheet->getCell('D20')->setValue(''); // 數量
            $worksheet->getCell('F20')->setValue(''); // 單價
            $worksheet->getCell('G20')->setValue(''); // 預算

            // E-2保險
            $worksheet->getCell('B21')->setValue(''); // 數量
            $worksheet->getCell('C21')->setValue('');
            $worksheet->getCell('D21')->setValue(''); // 數量
            $worksheet->getCell('F21')->setValue(''); // 單價
            $worksheet->getCell('G21')->setValue(''); // 預算

            // E-3獎品
            $worksheet->getCell('B22')->setValue('E-3獎品');     
            $worksheet->getCell('C22')->setValue('份');             
            $worksheet->getCell('D22')->setValue((int)$specailClassFee->reward_quantity); // 數量
            $worksheet->getCell('F22')->setValue((int)$specailClassFee->reward_unit_price); // 單價
            $worksheet->getCell('G22')->setValue((int)$specailClassFee->reward_budget); // 預算
        }

        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $payable = $richText->createTextRun('承辦人：');
        $payable->getFont()->setBold(true);
        $payable->getFont()->setName('標楷體');
        $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color('0000FF') );

        if (isset($t04tb->m09tb)){
            $richText->createText($t04tb->m09tb->username);
        }
        $worksheet->getCell('A24')->setValue($richText);

        $today = new DateTime();
        $today = $today->format('Y-m-d');

        $worksheet->getCell('G24')->setValue('填表日期：'.$today);

        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="費用明細表.xlsx"');

        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        //匯出
        //old code
        //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $objWriter->save('php://output');
        exit;

    }


}
