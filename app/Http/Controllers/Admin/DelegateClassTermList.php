<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\T01tb;
use App\Services\User_groupService;
use PhpOffice\PhpWord\PhpWord;

class DelegateClassTermList extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('delegate_class_term_list', $user_group_auth)){
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
        return view('admin/delegate_class_term_list/list',compact('result'));
    }

    public function export(Request $request)
    {
        $this->validate($request,[
            'condition' => 'required'
        ]);

        $t01tbs = T01tb::select([
                        'm17tb.enrollname',
                        't01tb.name',
                        't01tb.class',
                        't04tb.term',
                        't01tb.object',
                        't01tb.quotatot',
                        't01tb.target',
                        't01tb.quota',
                        't01tb.period',
                        't04tb.sdate',
                        't04tb.edate',
                        't01tb.kind'
                        ])
                      ->where('t01tb.process', '=', 2)
                      ->join('t04tb', 't04tb.class', '=', 't01tb.class')
                      ->leftJoin('m17tb', 'm17tb.enrollorg', '=', 't01tb.commission');

        if ($request->condition == 'yerly'){
            $this->validate($request,['yerly' => 'required']);
            $yerly = str_pad($request->yerly, 3, '0', STR_PAD_LEFT);

            $t01tbs->where('t01tb.yerly', '=', $yerly);
        }elseif ($request->condition == 'sedate'){
            $this->validate($request,['sdate' => 'required', 'edate' => 'required']);

            $t01tbs->where(function($query) use($request){
                $query->where(function($query1) use($request){
                    $query1->where('t04tb.sdate', '>=', $request->sdate)
                           ->where('t04tb.sdate', '<=', $request->edate);
                });

                $query->orWhere(function($query2) use($request){
                    $query2->where('t04tb.edate', '>=', $request->sdate)
                           ->where('t04tb.edate', '<=', $request->edate);
                });
            });

        }

        $list = $t01tbs->get();
        $list = $list->groupBy('class')->map(function($group){

            $t04tbs = $group->keyBy('term')->map(function($t04tb){
                return [
                    'sdate' => $t04tb->sdate,
                    'edate' => $t04tb->edate
                ];
            });

            $group = collect($group[0]->toArray())->only([
                'enrollname',
                'name',
                'class',
                'term',
                'object',
                'quotatot',
                'target',
                'quota',
                'period',
                'kind'
            ]);

            $group['t04tbs'] = $t04tbs;

            return $group;

        })->values();

        $this->exportDoc($list);
        // // // 讀檔案
        // $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'D14').'.docx');

        // // $templateProcessor
        // dd($templateProcessor->getSections());
        // $templateProcessor->cloneRow('enrollname', $list->count());

        // foreach ($list as $key => $t01tb){
        //     $templateProcessor->setValue('enrollname#'.($key+1), $t01tb['enrollname']);
        //     $templateProcessor->setValue('t01tbName#'.($key+1), $t01tb['name']);
        //     $templateProcessor->setValue('t01tbObject#'.($key+1), $t01tb['object']);
        //     $templateProcessor->setValue('t01tbQuotatot#'.($key+1), $t01tb['quotatot']);
        //     $templateProcessor->setValue('t01tbTarget#'.($key+1), $t01tb['target']);
        //     $templateProcessor->setValue('t01tbQota#'.($key+1), $t01tb['quota']);
        //     $templateProcessor->setValue('period#'.($key+1), $t01tb['period']);
        //     $templateProcessor->setValue('termCount#'.($key+1), $t01tb['t04tbs']->count());

        // }

        // ob_clean();

        // header('Content-Type: application/vnd.ms-word');
        // header("Content-Disposition: attachment;filename=接受委訓班期訓期一覽表.docx");
        // header('Cache-Control: max-age=0');
        // $templateProcessor->saveAs('php://output');
        // exit;

        // //docx
        // header('Content-Type: application/vnd.ms-word');
        // header("Content-Disposition: attachment;filename=接受委訓班期訓期一覽表.docx");
        // header('Cache-Control: max-age=0');
        // ob_clean();
        // $templateProcessor->saveAs('php://output');
        // exit;
    }

    private function exportDoc($list){
        $phpWord = new PhpWord();

        $phpWord->setDefaultFontName('標楷體');

        $section = $phpWord->addSection();

        $header = $section->addHeader(); //頁首
        $header->addHeader();

        if (request()->condition == 'yerly'){
            $header->addPreserveText(request()->yerly.'年度 接受委訓班期訓期一覽表');
        }elseif (request()->condition == 'sedate'){
            $sdate = request()->sdate;
            $sdate = substr($sdate, 0, 3).'/'.substr($sdate, 3, 2).'/'.substr($sdate, 5, 2);
            $edate = request()->edate;
            $edate = substr($edate, 0, 3).'/'.substr($edate, 3, 2).'/'.substr($edate, 5, 2);
            $header->addPreserveText($sdate.'-'.$edate.' 接受委訓班期訓期一覽表');
        }

        $section->addText(htmlspecialchars('部會業務知能訓練'));

        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '000000',
        ];

        $table = $section->addTable('data', $tableStyle);

        $cellStyle = [
            'vMerge' => 'restart',
            'align' => 'center',
            'valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER,
            'borderSize' => 6
        ];

        // 表頭
        $table->addRow(900, array('tblHeader' => true));

        $table->addCell(1030, $cellStyle)->addText(htmlspecialchars('委訓機關'), ['size' => 10], array('align' => 'center'));
        $table->addCell(1030, $cellStyle)->addText(htmlspecialchars('班別'), ['size' => 10], array('align' => 'center'));
        $table->addCell(1500, $cellStyle)->addText(htmlspecialchars('研習目標'), ['size' => 10], array('align' => 'center'));
        $table->addCell(700, $cellStyle)->addText(htmlspecialchars('年度研習總額'), ['size' => 10], array('align' => 'center'));
        $table->addCell(1030, $cellStyle)->addText(htmlspecialchars('研習對象'), ['size' => 10], array('align' => 'center'));
        $table->addCell(1030, $cellStyle)->addText(htmlspecialchars('年度期數'), ['size' => 10], array('align' => 'center'));
        $table->addCell(800, $cellStyle)->addText(htmlspecialchars('每期人數'), ['size' => 10], array('align' => 'center'));
        $table->addCell(800, $cellStyle)->addText(htmlspecialchars('訓期'), ['size' => 10], array('align' => 'center'));
        $table->addCell(1350, $cellStyle)->addText(htmlspecialchars('研習期間'), ['size' => 10], array('align' => 'center'));

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

        foreach ($list as $t01tb){
            $i = 0;
            foreach ($t01tb['t04tbs'] as $term => $t04tb){
                $table->addRow();
                if ($i == 0){
                    $kind = config('database_fields.t01tb.kind');
                    $kind = (isset($kind[$t01tb['kind']])) ? $kind[$t01tb['kind']] : null;
                    $table->addCell(1030, $cellStyle)->addText(htmlspecialchars($t01tb['enrollname']), ['size' => 10], array('align' => 'center'));
                    $table->addCell(1030, $cellStyle)->addText(htmlspecialchars($t01tb['name'].'('.$t01tb['class'].')'), ['size' => 10], array('align' => 'center'));

                    // 研習目標需要換行
                    $cell = $table->addCell(1500, $cellStyle);
                    $objectTextLines = explode("\n", $t01tb['object']);
                    foreach($objectTextLines as $line){
                        $cell->addText(htmlspecialchars($line), ['size' => 10], array('align' => 'center'));
                    }

                    $table->addCell(700, $cellStyle)->addText(htmlspecialchars($t01tb['quotatot']), ['size' => 10], array('align' => 'center'));

                    // 研習對象需要換行
                    $cell = $table->addCell(1030, $cellStyle);
                    $targetTextLines = explode("\n", $t01tb['target']);
                    foreach($targetTextLines as $line){
                        $cell->addText(htmlspecialchars($line), ['size' => 10], array('align' => 'center'));
                    }

                    $table->addCell(1030, $cellStyle)->addText(htmlspecialchars($t01tb['t04tbs']->count()), ['size' => 10], array('align' => 'center'));
                    $table->addCell(1030, $cellStyle)->addText(htmlspecialchars($t01tb['quota']), ['size' => 10], array('align' => 'center'));
                    $table->addCell(500, $cellStyle)->addText(htmlspecialchars($t01tb['period'].' '.$kind), ['size' => 10], array('align' => 'center'));
                }else{
                    $table->addCell(1030, $cellStyleMerge);
                    $table->addCell(1030, $cellStyleMerge);
                    $table->addCell(1500, $cellStyleMerge);
                    $table->addCell(700, $cellStyleMerge);
                    $table->addCell(1030, $cellStyleMerge);
                    $table->addCell(1030, $cellStyleMerge);
                    $table->addCell(800, $cellStyleMerge);
                    $table->addCell(800, $cellStyleMerge);
                }
                $table->addCell(1350,
                [
                    'align' => 'center',
                    'borderSize' => 6
                ]
                )->addText(htmlspecialchars("($term)".$t04tb['sdate'].'~'.$t04tb['edate']), ['size' => 10]);
                $i++;
            }
        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        header('Content-Type: application/vnd.ms-word');
        header("Content-Disposition: attachment;filename=接受委訓班期訓期一覽表.docx");
        header('Cache-Control: max-age=0');
        ob_clean();

        $objWriter->save('php://output');
        exit;
    }
}
