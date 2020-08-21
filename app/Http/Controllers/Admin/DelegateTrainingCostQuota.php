<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Models\SpecialClassFee;
use DateTime;
use App\Models\T01tb;

class DelegateTrainingCostQuota extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('delegate_training_cost_quota', $user_group_auth)){
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
        $t01tbs = T01tb::join('special_class_fee', 'special_class_fee.class', '=', 't01tb.class')->get()->pluck('name', 'class');

        return view('admin/delegate_training_cost_quota/list',compact('t01tbs'));

    }

    public function getTerms(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $termArr=$RptBasic->getTerms($request->input('classes'));
        return $termArr;
    }

    public function export(Request $request)
    {
        $now = new DateTime();
        $t04tbInfo = $request->only(['class', 'term']);

        $specialFee = SpecialClassFee::where($t04tbInfo)->first();

        if (empty($specialFee)){
            die('找不到該班期');
        }

        if (empty($specialFee->t04tb) || empty($specialFee->t04tb->t01tb)){
            die('資料異常');
        }
        // 檔案名稱
        $fileName = 'F16';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $payable = $richText->createTextRun($specialFee->t04tb->t01tb->yerly);
        $payable->getFOnt()->setSize(16);
        $payable->getFont()->setBold(true);
        $payable->getFont()->setName('標楷體');
        $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color('0000FF') );

        $payable = $richText->createTextRun('年度委訓經費各單位分配額度表');
        $payable->getFOnt()->setSize(16);
        $payable->getFont()->setBold(true);
        $payable->getFont()->setName('標楷體');
        $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color('000000') ); 

        $worksheet->getCell('A1')->setValue($richText); // 標題

        $sdateString = substr($specialFee->t04tb->sdate, 0, 3).' 年 '.substr($specialFee->t04tb->sdate, 3, 2).' 月 '.substr($specialFee->t04tb->sdate, 3, 2).' 日';
        $edateString = substr($specialFee->t04tb->edate, 0, 3).' 年 '.substr($specialFee->t04tb->edate, 3, 2).' 月 '.substr($specialFee->t04tb->edate, 3, 2).' 日';

        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $payable = $richText->createTextRun('填表日期：'.($now->format('Y')-1911)."年 {$now->format('m')} 月 {$now->format('d')} 日");
        $payable->getFOnt()->setSize(16);
        $payable->getFont()->setName('標楷體');
        $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color('0000FF') );

        $payable = $richText->createTextRun('        單位：元');
        $payable->getFOnt()->setSize(16);
        $payable->getFont()->setName('標楷體');
        $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color('000000') );        

        $fixedPay = $specialFee->service_fee_budget - $specialFee->business_pay_budget;
        $worksheet->getCell('B2')->setValue($richText); // 填表日期
        $worksheet->getCell('B3')->setValue("{$specialFee->t04tb->t01tb->class} {$specialFee->t04tb->t01tb->name} 第 {$specialFee->t04tb->term} 期"); // 班別期數
        $worksheet->getCell('B4')->setValue("{$sdateString} 至 {$edateString}"); // 訓練期間
        $worksheet->getCell('B5')->setValue($specialFee->service_fee_budget); // 委訓經費

        $sponsorSection = $sponsorName = '';
        
        if (isset($specialFee->t04tb->m09tb)){
            $sponsorSection = $specialFee->t04tb->m09tb->section;
            $sponsorName = $specialFee->t04tb->m09tb->username;
        }
        
        $worksheet->getCell('A10')->setValue($sponsorSection); // 單位別

        $worksheet->getCell('C10')->setValue($specialFee->hourly_fee_budget); // 固定支出-XX組-鐘點費
        $worksheet->getCell('C11')->setValue($specialFee->extra_budget); // 固定支出-XX組-額外項
        $worksheet->getCell('C12')->setValue($specialFee->food_expenses_budget); // 固定支出-綜合規劃組-伙食費
        $worksheet->getCell('C13')->setValue('0'); // 固定支出-秘書室

        $worksheet->getCell('E15')->setValue(''); // 執行分配數-合計\
        $worksheet->getCell('D15')->setValue('填表人：'.$sponsorName); // 填表人
        //export excel
        ob_end_clean();
        ob_start();

        $fileName = $specialFee->t04tb->t01tb->class.'接受委託辦理訓練需求彙總表';
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"');

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
