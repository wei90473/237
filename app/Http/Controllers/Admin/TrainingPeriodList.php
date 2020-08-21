<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;

use App\Models\T04tb;
use App\Models\S01tb;
use App\Presenters\BasePresenter;
use PhpOffice\PhpWord\PhpWord;

class TrainingPeriodList extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('training_period_list', $user_group_auth)){
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
        return view('admin/training_period_list/list',compact('classArr','termArr' ,'result'));
    }

    public function export(Request $request)
    {
        

        // 讀檔案
        // $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'D5').'.docx');

        $this->validate($request,[
            'condition' => 'required'
        ]);

        if ($request->condition == "yerly"){
            $this->validate($request, [
                'yerly' => 'required'
            ]);
            $queryData = $request->only(['yerly']);
        }elseif ($request->condition == "sedate"){
            $this->validate($request, [
                'sdate' => 'required',
                'edate' => 'required'
            ]);     
            $queryData = $request->only(['sdate', 'edate']);       
        }

        // 取得報表資料 (訓期)
        $exportData = $this->getExportData($queryData)->groupBy('type');
        
        $this->exportDoc($exportData, $request->condition, $queryData);
        
        //docx
        // header('Content-Type: application/vnd.ms-word');
        // header("Content-Disposition: attachment;filename=訓期一覽表.docx");
        // header('Cache-Control: max-age=0');
        // ob_clean();
        // $templateProcessor->saveAs('php://output');
        // exit;
    }

    private function exportDoc($exportData, $condition, $queryData)
    {

        $base = new BasePresenter();
        $types = S01tb::where('type', '=','K')->whereIn('code', [23, 24, 25, 26])->get()->keyBy('code');
        $categoryones = S01tb::where('type', '=','M')->get()->keyBy('code');

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('標楷體');

        $section = $phpWord->addSection(
            array('marginLeft' => 600, 'marginRight' => 600,'marginTop' => 600, 'marginBottom' => 600)
        );

        $header = $section->addHeader(); //頁首
        $header->addHeader();

        if ($condition == 'yerly'){
            $header->addPreserveText($queryData['yerly'].'年度 訓期一覽表');
        }elseif ($condition == 'sedate'){
            $sdate = $queryData['sdate'];
            $sdate = substr($sdate, 0, 3).'/'.substr($sdate, 3, 2).'/'.substr($sdate, 5, 2);
            $edate = $queryData['edate'];
            $edate = substr($edate, 0, 3).'/'.substr($edate, 3, 2).'/'.substr($edate, 5, 2);
            $header->addPreserveText($sdate.'-'.$edate.' 訓期一覽表');
        }

        $cellStyle = [
            'vMerge' => 'restart',
            'align' => 'center',
            'valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER,
            'borderSize' => 6
        ];

        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '000000',
        ];

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

        $headerFontStyle = ['size' => 12];
        $headerAlignStyle = ['align' => 'center'];

        $kinds = config('database_fields.t01tb.kind');
        $branchs = config('database_fields.t01tb.branch');
        // foreach ($exportData as $type => $t04tbGroup){
        foreach ($types as $type => $name){

            $t04tbGroup = isset($exportData[$type]) ? $exportData[$type] : null;

            $section->addText(htmlspecialchars($types[$type]->name), ['size' => 18]);
            $table = $section->addTable('data', $tableStyle);

            // 表頭
            $table->addRow(1000, array('tblHeader' => true));

            $table->addCell(400, $cellStyle)->addText(htmlspecialchars('分類'), $headerFontStyle, $headerAlignStyle);
            $table->addCell(1000, $cellStyle)->addText(htmlspecialchars('班別(班號)'), $headerFontStyle, $headerAlignStyle);
            $table->addCell(1000, $cellStyle)->addText(htmlspecialchars('委訓機關'), $headerFontStyle, $headerAlignStyle);
            $table->addCell(1400, $cellStyle)->addText(htmlspecialchars('研習目標'), $headerFontStyle, $headerAlignStyle);
            $table->addCell(1400, $cellStyle)->addText(htmlspecialchars('研習主題'), $headerFontStyle, $headerAlignStyle);
            $table->addCell(1400, $cellStyle)->addText(htmlspecialchars('研習對象'), $headerFontStyle, $headerAlignStyle);
            $table->addCell(400, $cellStyle)->addText(htmlspecialchars('訓期'), $headerFontStyle, $headerAlignStyle);
            $table->addCell(1000, $cellStyle)->addText(htmlspecialchars('地點'), $headerFontStyle, $headerAlignStyle);
            $table->addCell(400, $cellStyle)->addText(htmlspecialchars('每期人數'), $headerFontStyle, $headerAlignStyle);
            $table->addCell(1000, $cellStyle)->addText(htmlspecialchars('研習期間'), $headerFontStyle, $headerAlignStyle);
            $table->addCell(700, $cellStyle)->addText(htmlspecialchars('備註'), $headerFontStyle, $headerAlignStyle);
            
            if (isset($t04tbGroup)){

                foreach ($t04tbGroup->groupBy('class') as $classGroup){
                    foreach ($classGroup as $index => $t04tb){
                        $table->addRow(1000);
                        $categoryoneName = isset($categoryones[$t04tb->categoryone]) ? $categoryones[$t04tb->categoryone]->name : null;

                        if ($index == 0){
                            $table->addCell(400, $cellStyle)->addText(htmlspecialchars($categoryoneName), $headerFontStyle, $headerAlignStyle);
                        }else{
                            $table->addCell(400, $cellStyleMerge);    
                        }

                        if ($index == 0){
                           $table->addCell(1000, $cellStyle)->addText(htmlspecialchars($t04tb->className.'('.$t04tb->class.')'), $headerFontStyle, $headerAlignStyle);     
                        }else{
                            $table->addCell(1000, $cellStyleMerge);
                        }
                        
                        $table->addCell(1400, $cellStyle)->addText(htmlspecialchars($t04tb->enrollname), $headerFontStyle, $headerAlignStyle);

                        $cell = $table->addCell(1400, $cellStyle);
                        $objectTextLines = explode("\n", $t04tb->object);
                        foreach($objectTextLines as $line){
                            $cell->addText(htmlspecialchars($line), $headerFontStyle, $headerAlignStyle);
                        }

                        $table->addCell(1400, $cellStyle)->addText(htmlspecialchars($t04tb->content), $headerFontStyle, $headerAlignStyle);

                        $cell = $table->addCell(1000, $cellStyle);
                        $targetTextLines = explode("\n", $t04tb->target);
                        foreach($targetTextLines as $line){
                            $cell->addText(htmlspecialchars($line), $headerFontStyle, $headerAlignStyle);
                        }

                        $kind = isset($kinds[$t04tb->kind]) ? $kinds[$t04tb->kind] : null;
                        $table->addCell(400, $cellStyle)->addText(htmlspecialchars($t04tb->period.' '.$kind), $headerFontStyle, $headerAlignStyle);

                        if ($t04tb->process == 4 || $t04tb->process == 5){
                            $location = '外地';
                        }else{
                            $location = isset($branchs[$t04tb->branch]) ? $branchs[$t04tb->branch] : null;
                        }

                        $table->addCell(1000, $cellStyle)->addText(htmlspecialchars($location), $headerFontStyle, $headerAlignStyle);
                        $table->addCell(400, $cellStyle)->addText(htmlspecialchars($t04tb->quota), $headerFontStyle, $headerAlignStyle);
                        $table->addCell(1000, $cellStyle)->addText(htmlspecialchars('('.$t04tb->term.')'.$t04tb->sdate."\n~\n".$t04tb->edate), $headerFontStyle, $headerAlignStyle);
                        $table->addCell(700, $cellStyle)->addText(htmlspecialchars($t04tb->t01tb_remark), $headerFontStyle, $headerAlignStyle);                    
                    }


                }                
            }

            // 分頁符號
            $section->addPageBreak();
        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        header('Content-Type: application/vnd.ms-word');
        header("Content-Disposition: attachment;filename=訓期一覽表.docx");
        header('Cache-Control: max-age=0');
        ob_clean();

        $objWriter->save('php://output');
        exit;
    }

    private function getExportData($queryData)
    {
        $t04tb = T04tb::selectRaw('t04tb.*, t01tb.type, t01tb.name className, t01tb.categoryone, t01tb.remark as t01tb_remark, t01tb.object, t01tb.content, t01tb.target, t01tb.branch, t01tb.commission, m17tb.enrollname, t01tb.period, t01tb.kind, process')
                      ->join('t01tb', 't01tb.class', '=', 't04tb.class')
                      ->leftJoin('m17tb', 'm17tb.enrollorg', '=', 't01tb.commission');

        if (isset($queryData['yerly'])){
            $t04tb->where('t01tb.yerly', '=', $queryData['yerly']);
        }

        if (isset($queryData['sdate']) && isset($queryData['edate'])){
            $t04tb->where(function($query) use($queryData){
                $query->where(function($query1) use($queryData){
                    $query1->where('t04tb.sdate', '>=', $queryData['sdate'])
                           ->where('t04tb.sdate', '<=', $queryData['edate']);
                });
                $query->orWhere(function($query2) use($queryData){
                    $query2->where('t04tb.edate', '>=', $queryData['sdate'])
                           ->where('t04tb.edate', '<=', $queryData['edate']);
                });                
            });
        }

        /*
            一、領導力發展 23
            二、政策能力訓練 24
            三、部會業務知能訓練 25
            四、自我成長及其他 26
        */

        $t04tb->whereIn('t01tb.type', [23, 24, 25, 26])
              ->where('t01tb.categoryone', '<>', '0')
              ->where('t01tb.categoryone', '<>', '')
              ->whereNotNull('t01tb.categoryone')
              ->orderBy('t01tb.type');
        return $t04tb->get();
    }
}
