<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use PhpOffice\PhpWord\PhpWord;
use App\Models\DemandSurveyCommissioned;



class DelegateTrainingRequest extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('delegate_training_request', $user_group_auth)){
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
        return view('admin/delegate_training_request/list',compact('result'));
    }

    public function export(Request $request)
    {
        $this->validate($request, ['yerly' => 'required']);
        // // 讀檔案
        // $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'D15').'.docx');
        $list = $this->getExportData($request->yerly);

        $this->exportDoc($list);
    }

    private function getExportData($yerly){
        $list = DemandSurveyCommissioned::select(["pre.*", "demand_survey_commissioned.yerly", 'm17tb.enrollname'])
                                        ->join('demand_survey_commissioned_pre as pre', 'pre.item_id', '=', 'demand_survey_commissioned.item_id')
                                        ->join('m17tb', 'm17tb.enrollorg', '=', 'pre.entrusting_orga')
                                        ->where('demand_survey_commissioned.yerly', '=', $yerly)
                                        ->get();
        return $list->groupBy('entrusting_orga')->toArray();
    }

    private function exportDoc($list){
        $phpWord = new PhpWord();

        $phpWord->setDefaultFontName('標楷體');

        $sectionStyle = [
            'marginLeft' => 700,
            'marginRight' => 700
        ];

        // $phpWord->addSectionStyle('mySection', $sectionStyle);
        $section = $phpWord->addSection($sectionStyle);

        $header = $section->addHeader(); //頁首
        $header->addHeader();

        // 標題
        $fontStyle = [
            'align' => 'center'
        ];

        $section->addText(htmlspecialchars('行政院人事行政總處公務人力發展學院'), ['bold'=>true, 'size' => 14], $fontStyle);
        $section->addText(htmlspecialchars(request()->yerly.'年度接受委託辦理訓練需求彙總表'), ['bold'=>true, 'size' => 14], $fontStyle);

        $tableStyle = array(
            'borderColor' => '006699',
            'borderSize'  => 6,
            'cellMargin'  => 50
        );

        $phpWord->addTableStyle('myTable', $tableStyle);
        $table = $section->addTable('myTable', $tableStyle);

        $cellStyle = [
            'vMerge' => 'restart',
            'align' => 'center',
            'valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER,
            'borderSize' => 6
        ];

        // 表頭
        $table->addRow(900, array('tblHeader' => true));

        $table->addCell(800, $cellStyle)->addText(htmlspecialchars('委訓機關'), ['size' => 12], $fontStyle);
        $table->addCell(2000, $cellStyle)->addText(htmlspecialchars('班別'), ['size' => 12], $fontStyle);
        $table->addCell(2000, $cellStyle)->addText(htmlspecialchars('研習目標'), ['size' => 12], $fontStyle);
        $table->addCell(2000, $cellStyle)->addText(htmlspecialchars('訓練對象'), ['size' => 12], $fontStyle);
        $table->addCell(700, $cellStyle)->addText(htmlspecialchars('期數'), ['size' => 12], $fontStyle);
        $table->addCell(800, $cellStyle)->addText(htmlspecialchars('每期人數'), ['size' => 12], $fontStyle);
        $table->addCell(800, $cellStyle)->addText(htmlspecialchars('訓期(天)'), ['size' => 12], $fontStyle);
        $table->addCell(1500, $cellStyle)->addText(htmlspecialchars('建議辦理時間'), ['size' => 12], $fontStyle);

        $cellStyle = [
            'vMerge' => 'restart',
            'align' => 'center',
            'borderSize' => 6,
            'valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER,
        ];

        $cellStyleMerge = [
            'vMerge' => 'continue',
            'align' => 'center',
            'borderSize' => 6,
            'valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER,
        ];

        foreach ($list as $demands){
            foreach ($demands as $key => $demand){
                $table->addRow();

                if ($key == 0){
                    $cell = $table->addCell(800, $cellStyle);
                    for($i=0; $i<mb_strlen($demand['enrollname']); $i++){
                        $cell->addText(htmlspecialchars(mb_substr($demand['enrollname'], $i, 1)), ['size' => 12], $fontStyle);
                    }

                }else{
                    $table->addCell(800, $cellStyleMerge);
                }

                $table->addCell(2000, $cellStyle)->addText(htmlspecialchars($demand['class_name']), ['size' => 12]);

                // 研習目標需要換行
                $cell = $table->addCell(2000, $cellStyle);
                $objectTextLines = explode("\n", $demand['object']);
                foreach($objectTextLines as $line){
                    $cell->addText(htmlspecialchars($line), ['size' => 12]);
                }

                // 訓練對象需要換行
                $cell = $table->addCell(2000, $cellStyle);
                $targetTextLines = explode("\n", $demand['target']);
                foreach($targetTextLines as $line){
                    $cell->addText(htmlspecialchars($line), ['size' => 12]);
                }

                $table->addCell(700, $cellStyle)->addText(htmlspecialchars($demand['periods']), ['size' => 12], $fontStyle);
                $table->addCell(700, $cellStyle)->addText(htmlspecialchars($demand['periods_people']), ['size' => 12], $fontStyle);
                $table->addCell(500, $cellStyle)->addText(htmlspecialchars($demand['training_days']), ['size' => 12], $fontStyle);

                $cell = $table->addCell(1500,  [
                    'align' => 'center',
                    'borderSize' => 6
                ]);

                $sdate = $demand['sdate'];
                $sdate = substr($sdate, 0, 3).'/'.substr($sdate, 3, 2).'/'.substr($sdate, 5, 2);
                $cell->addText(htmlspecialchars('起'.$sdate), ['size' => 12]);

                $edate = $demand['edate'];
                $edate = substr($edate, 0, 3).'/'.substr($edate, 3, 2).'/'.substr($edate, 5, 2);
                $cell->addText(htmlspecialchars('迄'.$edate), ['size' => 12]);

                if (isset($demand['sdate2'])){
                    $sdate = $demand['sdate2'];
                    $sdate = substr($sdate, 0, 3).'/'.substr($sdate, 3, 2).'/'.substr($sdate, 5, 2);
                    $cell->addText('', ['size' => 12]);
                    $cell->addText(htmlspecialchars('起'.$sdate), ['size' => 12]);
                }

                if (isset($demand['edate2'])){
                    $edate = $demand['edate2'];
                    $edate = substr($edate, 0, 3).'/'.substr($edate, 3, 2).'/'.substr($edate, 5, 2);
                    $cell->addText(htmlspecialchars('迄'.$edate), ['size' => 12]);
                }
            }
        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        ob_clean();
        header('Content-Type: application/vnd.ms-word');
        header("Content-Disposition: attachment;filename=接受委託辦理訓練需求彙總表.docx");
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit;
    }
}
