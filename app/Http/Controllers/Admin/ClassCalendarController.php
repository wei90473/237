<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DateTime;

use App\Models\T04tb;
use App\Models\T36tb;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Models\M14tb;
use App\Models\Edu_classroom;

class ClassCalendarController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('ClassCalendarController', $user_group_auth)){
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
        return view('admin/classCalendar/list');
    }


    public function export(Request $request)
    {
        $this->validate($request, ['yerly', 'month', 'branch']);
        $condition = $request->only(['yerly', 'month', 'branch']);
        $condition['yerly'] = str_pad($condition['yerly'], 3, '0', STR_PAD_LEFT);
        $condition['month'] = str_pad($condition['month'], 2, '0', STR_PAD_LEFT);


        $queryDate = new DateTime(($condition['yerly']+1911).$condition['month'].'01');
        $querySdate = str_pad(($queryDate->format('Y') - 1911), 3, '0', STR_PAD_LEFT).$queryDate->format('md');
        $queryDate->modify('+1 month -1 day');
        $queryEdate = str_pad(($queryDate->format('Y') - 1911), 3, '0', STR_PAD_LEFT).$queryDate->format('md');

        // 依照院區取得 教室資訊
        if ($condition['branch'] == 1){
            $classRoomNo = collect([
                '303' => 'C', 
                '304' => 'D', 
                '305' => 'E', 
                '401' => 'F', 
                '402' => 'G', 
                '403' => 'H', 
                '404' => 'I', 
                '405' => 'J', 
                '501' => 'K', 
                '502' => 'L', 
                '601' => 'M',
                '602' => 'N', 
                'C01' => 'O', 
                'C14' => 'P',
                '407' => 'Q' 
            ]);
    
            $classRooms = M14tb::selectRaw('site, name')->whereIn('site', $classRoomNo->keys())->get()->keyBy('site');
        }elseif ($condition['branch'] == 2){
            $classRoomNo = collect([
                '501' => 'C',
                '502' => 'D',
                '503' => 'E',
                '504' => 'F',
                '601' => 'G',
                '602' => 'H',
                '603' => 'I',
                '604' => 'J',
                '701' => 'K',
                '702' => 'L',
                '703' => 'M',
                '704' => 'N',
                '0303' => 'O',
                '612' => 'P',
                '0212' => 'Q',
                '1143' => 'R'
            ]);
            $classRooms = Edu_classroom::selectRaw('roomno as site, roomname as name')->whereIn('roomno', $classRoomNo->keys())->get()->keyBy('site');
        }

        // 取得行事曆資料
        $t36tbs = T36tb::where(function($query) use($querySdate, $queryEdate){
            $query->where('date', '>=', $querySdate)
                  ->where('date', '<=', $queryEdate);
        })
        ->join('t01tb', 't01tb.class', '=', 't36tb.class')
        ->where('site', '<>', null)
        ->where('site', '<>', '')
        ->where('type', '<>', 13)
        ->whereIn('site', $classRoomNo->keys())
        ->where('site_branch', '=', $condition['branch'])
        ->orderBy('date')
        ->selectRaw('t36tb.*')
        ->get();

        if ($t36tbs->isEmpty()){
            return back()->with('result', 0)->with('message', '無行事曆資料');
        }
        
        $t04tbKeys = $t36tbs->map(function($t36tb){
            return [
                'class' => $t36tb->class,
                'term' => $t36tb->term
            ];
        });

        $outSideData = collect();

        $t36tbs = $t36tbs->groupBy('date')->map(function($t36tbGroup) use(&$outSideData){
            return $t36tbGroup->groupBy('site')->map(function($t36tbSame) use(&$outSideData){
                if ($t36tbSame->count() > 1){
                    $t36tbSame->map(function($t36tb, $index) use(&$outSideData){
                        if ($index == 0) return null;
                        $outSideData[] = $t36tb;
                    });
                }
                return $t36tbSame[0];
            });
        });

        $outSideData = $outSideData->sortBy('class')->values()->all();

        $t36tbs = $t36tbs->collapse()->values();

        $t36tbs = $t36tbs->groupBy('class');
        $t36tbs = $t36tbs->map(function($t36tb){
            return $t36tb->groupBy('term');
        });


        // 取得班期資料
        $t04tbs = T04tb::where(function($query) use($t04tbKeys){
            foreach($t04tbKeys as $t04tbKey){
                $query->orWhere($t04tbKey);
            }
        })
        ->where('t04tb.sponsor', '<>', null)
        ->where('t04tb.sponsor', '<>', '')        
        ->with(['t01tb', 'm09tb'])->get();

        // 取得外地班班期
        $outSdieClass = T04tb::whereExists(function($query) use($querySdate, $queryEdate){
            $query->from('t36tb')
                  ->where('t36tb.date', '>=', $querySdate)
                  ->where('t36tb.date', '<=', $queryEdate)
                  ->whereRaw('t36tb.class = t04tb.`class` AND t36tb.term = t04tb.term');
        })
        ->join('t01tb', 't01tb.class', '=', 't04tb.class')
        ->where('type', '<>', 13)
        ->where('t01tb.process', '=', 4)
        ->where('t01tb.branch', '=', $condition['branch'])
        ->selectRaw('t04tb.*')
        ->with(['t01tb', 'm09tb'])
        ->get();

        $sponsors = $t04tbs->pluck('m09tb')->keyBy('userid');
        $sponsors->merge($outSdieClass->pluck('m09tb')->keyBy('userid'));

        // 開始產製 Excel 

        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();

        // 設定標題
        $sheet->getHeaderFooter()
              ->setOddHeader('行政院人事行政總處公務人力發展學院'.(($condition['branch'] == 1) ? '臺北院區' : '南投院區').$condition['yerly'].'年'.$condition['month'].'月行事表');

        // 設定紙張大小
        $sheet->getPageSetup()
              ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A3);

        $spreadSheet->getDefaultStyle()->getFont()->setName('標楷體');

        $sheet->getPageSetup()->setScale(90);


        $sheet->mergeCells('A1:B2');
        $sheet->getCell('A1')->setValue("辦班\n人員\n(電話\n分機)");

        $color = config('calendarColor');

        $styleArray = array(
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            ),
        );
        

        // 辦班人員列表

        $colum = 67;
        $row = 1;
        $colorIndex = 0;
        $fontColorIndex = 0;

        foreach ($sponsors as $sponsor){

            if (count($color['background']) <= $colorIndex){
                $colorIndex = 0;
                
                if (count($color['font']) <= $fontColorIndex){
                    $fontColorIndex = 0;
                }else{
                    $fontColorIndex++;
                }
            }

            // 背景 以及 文字顏色
            $sponsor->color = $color['background'][$colorIndex];
            $sponsor->fontColor = $color['font'][$fontColorIndex];
            $colorIndex++;

            if (!empty($sponsor->fontColor)){
                $sheet->getStyle(chr($colum).$row)
                      ->getFont()
                      ->getColor()
                      ->setARGB($sponsor->fontColor);
            }

            // 姓名 分機
            $sheet->getCell(chr($colum).$row)->setValue("$sponsor->username\n($sponsor->ext)");

            if (!empty($sponsor->color)){
                $sheet->getStyle(chr($colum).$row)
                      ->getFill()
                      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()
                      ->setARGB($sponsor->color);  
            }

            if ($colum == 81){
                $colum = 67;
                $row++;
            }else{
                $colum++;
            }
        }

        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setWidth(4);

        $sheet->getRowDimension('1')->setRowHeight(40); 
        $sheet->getRowDimension('2')->setRowHeight(40);

        $sheet->mergeCells('A3:B3');

        $sheet->getRowDimension('3')->setRowHeight(15);
        $sheet->getCell('A3')->setValue('教室編號');
        $sheet->getStyle('A3')->getFont()->setSize(6);

        // 教室標題
        foreach ($classRooms as $classRoom){

            if (empty($classRoomNo[$classRoom->site])){
                dd($classRoom->site);
            }

            $sheet->getCell($classRoomNo[$classRoom->site].'3')->setValue($classRoom->site);
            $sheet->getCell($classRoomNo[$classRoom->site].'4')->setValue($classRoom->name);
        }

        $sheet->getCell('A4')->setValue("日\n期");
        $sheet->getCell('B4')->setValue("星\n期");
        
        $sheet->getRowDimension('4')->setRowHeight(30);

        $monthFirstDay = new DateTime(($condition['yerly']+1911).$condition['month'].'01');

        $nextMonth = str_pad($condition['month'] + 1, 2, '0', STR_PAD_LEFT);

        $weekText = ['日', '一', '二', '三', '四', '五', '六'];

        $datePosition = (int)ceil($sponsors->count() / 15) + 3;
        $datePositions = [];

        while($monthFirstDay->format('m') != $nextMonth){
            $sheet->getCell('A'.$datePosition)->setValue($monthFirstDay->format('d'));
            $sheet->getCell('B'.$datePosition)->setValue($weekText[$monthFirstDay->format('w')]);
            $sheet->getRowDimension($datePosition)->setRowHeight(30);

            $datePositions[$monthFirstDay->format('d')] = $datePosition;
            // 六日 呈現灰色
            if ($monthFirstDay->format('w') == 0 || $monthFirstDay->format('w') == 6){
                $sheet->getStyle('A'.$datePosition.':R'.$datePosition)
                      ->getFill()
                      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()
                      ->setARGB('888888');                 
            }

            $monthFirstDay->modify('+1 day');
            $datePosition++;
        }
        
        $sheet->getStyle('A1:R'.($datePosition-1))->applyFromArray($styleArray);

        foreach ($t04tbs as $t04tb){
            if (isset($t36tbs[$t04tb->class][$t04tb->term])){
                $classT36tbs = $t36tbs[$t04tb->class][$t04tb->term];
                
                $needMerged = [];
                foreach($classT36tbs->groupBy('site') as $t36tbGroup){
                    $merged = [];
                    // 檢查有無連續儲存格需合併
                    for ($i=0; $i<count($t36tbGroup)-1; $i++){

                        if (empty($merged)){
                            $merged[] = $t36tbGroup[$i];
                        }
                        
                        if ($t36tbGroup[$i]->date + 1 == $t36tbGroup[$i + 1]->date){
                            $merged[] = $t36tbGroup[$i + 1];
                        }else{
                            if (count($merged) > 1){
                                $needMerged[$merged[0]->site][$merged[0]->date] = $merged;
                            }
                            $merged = [];
                        }
                    }
                    // 檢查有無連續儲存格需合併 最後結算
                    if (count($merged) > 1){
                        $needMerged[$merged[0]->site][$merged[0]->date] = $merged;
                    }

                }
                
                foreach ($classT36tbs as $t36tb){
                    
                    if (isset($classRoomNo[$t36tb->site])){
                        $t36tbDate = substr($t36tb->date, 5, 2);
                        if (isset($datePositions[$t36tbDate])){

                            $classInfo = $t04tb->t01tb->name."第".$t04tb->term.'期('.$t04tb->t01tb->class.')';
                            $rowName = $classRoomNo[$t36tb->site].$datePositions[$t36tbDate];
                    
                            if (mb_strlen($classInfo) > 15 && empty($needMerged[$t36tb->site][$t36tb->date])){
                                $sheet->getStyle($rowName)->getFont()->setSize(6);
                            }else{
                                $sheet->getStyle($rowName)->getFont()->setSize(9);
                            }
                            
                            $sheet->getCell($rowName)->setValue($classInfo);

                            if (isset($sponsors[$t04tb->sponsor])){
                                if (!empty($sponsors[$t04tb->sponsor]->fontColor)){
                                    $sheet->getStyle($rowName)
                                          ->getFont()
                                          ->getColor()
                                          ->setARGB($sponsors[$t04tb->sponsor]->fontColor);
                                }
    
                                if (!empty($sponsors[$t04tb->sponsor]->color)){
                                    $sheet->getStyle($rowName)
                                          ->getFill()
                                          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                          ->getStartColor()
                                          ->setARGB($sponsors[$t04tb->sponsor]->color);  
                                } 
                            }
                        }
                    }
                }

                foreach($needMerged as $site => $mergeds){
                    foreach ($mergeds as $merged){
                        $mergeSDate = substr($merged[0]->date, 5, 2);
                        $mergeEDate = substr($merged[count($merged)-1]->date, 5, 2);                      
                        $sheet->mergeCells($classRoomNo[$site].$datePositions[$mergeSDate].':'.$classRoomNo[$site].$datePositions[$mergeEDate]);
                    }
                }            
            }
        }

        for($i=67;$i<=82;$i++){
            $sheet->getColumnDimension(chr($i))->setWidth(8);
        }

        for($i=5; $i<100; $i++) { 
            $sheet->getRowDimension($i)->setRowHeight(30); 
        }

        $sheet->getStyle('A1:Z100')->getAlignment()->setWrapText(true);
        
        // 剩餘外地班或是同日重複班級加在表格下方
        $start = $datePositions[count($datePositions)] + 1;

        $t04tbs = $t04tbs->groupBy('class')->map(function($t04tb){
            return $t04tb->keyBy('term');
        });

        $outSideData = collect($outSideData)->groupBy('class')->map(function($t36tb){
            return $t36tb->pluck('term', 'term');
        });

        $key = 1;
        foreach ($outSideData as $class => $terms){
            foreach ($terms as $term){

                if (isset($t04tbs[$class][$term])){
                    $rowName = 'A'.$start;
                    $t04tb = clone $t04tbs[$class][$term];
                    $t04tb->sdate = substr($t04tb->sdate, 0, 3).'/'.substr($t04tb->sdate, 3, 2).'/'.substr($t04tb->sdate, 5, 2);
                    $t04tb->edate = substr($t04tb->edate, 0, 3).'/'.substr($t04tb->edate, 3, 2).'/'.substr($t04tb->edate, 5, 2);

                    $sheet->mergeCells($rowName.':'.'R'.$start);
                    $sheet->getCell($rowName)->setValue("註".($key)."：{$t04tb->t01tb->name}第{$t04tb->term}期($t04tb->class){$t04tb->sdate}～{$t04tb->edate}未排入");

                    if (isset($sponsors[$t04tb->sponsor])){
                        if (!empty($sponsors[$t04tb->sponsor]->fontColor)){
                            $sheet->getStyle($rowName)
                                  ->getFont()
                                  ->getColor()
                                  ->setARGB($sponsors[$t04tb->sponsor]->fontColor);
                        }

                        if (!empty($sponsors[$t04tb->sponsor]->color)){
                            $sheet->getStyle($rowName)
                                  ->getFill()
                                  ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                  ->getStartColor()
                                  ->setARGB($sponsors[$t04tb->sponsor]->color);  
                        } 
                    }

                    $start++; 
                    $key++;               
                }                
            }

        }

        foreach ($outSdieClass as $t04tb){

            if (isset($t04tbs[$t04tb->class][$t04tb->term])){
                $rowName = 'A'.$start;
                $t04tb->sdate = substr($t04tb->sdate, 0, 3).'/'.substr($t04tb->sdate, 3, 2).'/'.substr($t04tb->sdate, 5, 2);
                $t04tb->edate = substr($t04tb->edate, 0, 3).'/'.substr($t04tb->edate, 3, 2).'/'.substr($t04tb->edate, 5, 2);

                $sheet->mergeCells($rowName.':'.'R'.$start);
                $sheet->getCell($rowName)->setValue("註".($key)."：{$t04tb->t01tb->name}第{$t04tb->term}期($t04tb->class){$t04tb->sdate}～{$t04tb->edate}未排入(外地班)");

                if (isset($sponsors[$t04tb->sponsor])){
                    if (!empty($sponsors[$t04tb->sponsor]->fontColor)){
                        $sheet->getStyle($rowName)
                              ->getFont()
                              ->getColor()
                              ->setARGB($sponsors[$t04tb->sponsor]->fontColor);
                    }

                    if (!empty($sponsors[$t04tb->sponsor]->color)){
                        $sheet->getStyle($rowName)
                              ->getFill()
                              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                              ->getStartColor()
                              ->setARGB($sponsors[$t04tb->sponsor]->color);  
                    } 
                }

                $start++; 
                $key++;               
            }                
        }

        $fileName = $condition['yerly'].'年度'.$condition['month'].'月行事表';
        $objWriter = new Xlsx($spreadSheet);

        ob_end_clean();
        ob_start();
        

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
        header ('Pragma: public'); // HT

        // //匯出

        

        
        $objWriter->save('php://output');
        exit;

    }

    public function colourDistance($c1, $c2)
    {
        $rmean = ($c1['R'] + $c2['R']) / 2;
        $r = $c1['R'] - $c2['R'];
        $g = $c1['G'] - $c2['G'];
        $b = $c1['B'] - $c2['B'];
        $weightR = 2 + $rmean / 256;
        $weightG = 4.0;
        $weightB = 2 + (255 - $rmean) / 256;
        return sqrt($weightR * $r * $r + $weightG * $g * $g + $weightB * $b * $b);
    }
}
