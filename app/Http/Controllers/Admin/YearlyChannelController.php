<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DateTime;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Models\T04tb;
use App\Models\T13tb;

use App\Helpers\TWDateTime;

/*  
    年度流路明細表
*/
class YearlyChannelController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('YearlyChannelController', $user_group_auth)){
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
        return view('admin.yearly_channel.index');
    }

    public function export(Request $request)
    {
        $this->validate($request, ['branch' => 'required', 'sdate' => 'required', 'edate' => 'required']);
        $queryData = $request->only(['branch', 'sdate', 'edate', 'bedQuantity']);
        $bedQuantity = isset($queryData['bedQuantity']) ? (int)$queryData['bedQuantity'] : 256;

        $t04tbs = $this->getT04tbs($queryData);

        if ($t04tbs->isEmpty()){
            return back()->with('result', 0)->with('message', '查無資料');
        }

        $now = new DateTime();
        $now = new DateTime($now->format('Y0101'));

        $thisYear = $now->format('Y');
        $end = clone $now;
        $end->modify('+1 year');

        $week = 1;

        if ($now->format('w') == 0){
            $now->modify('-6 day');
        }else{
            $now->modify('-'.($now->format('w')-1).' day');
        }

        $weekMonths = [];
        $weekDay = [
            'sdate' => 0,
            'edate' => 0,
        ];

        $dateLocation = [];

        $field = [66, 64];
        while($weekDay['sdate'] < $end->format('Ymd')){
            $month = $now->format('m');
            $year = $now->format('Y');
            $weekDay['sdate'] = $now->format('Ymd');

            for($i=0; $i<7; $i++){
                $rowName = "";
                
                foreach ($field as $ascCode){
                    if ($ascCode > 64 && $ascCode < 91){
                        $rowName = chr($ascCode).$rowName;
                    } 
                }

                $field[0] = $field[0] + 1;
                if ($field[0] >= 91){
                    $field[0] = 65;
                    $field[1]++;
                }

                $dateLocation[$now->format('Ymd')] = $rowName;
                $now->modify('+1 day');

                if ($i==6){
                    $weekDay['edate'] = $now->format('Ymd');
                }
            }

            if ($month == '12' && $year == $thisYear - 1){
                // 上一年 12 月歸類於今年 1 月
                $month = '01';
            }elseif ($month == '01' && $year == $thisYear + 1){
                // 下一年 1 月歸類於今年 12 月
                $month = '12';
            }

            $weekMonths[(int)$month][] = $weekDay; 
            $week++;
        }

        $spreadSheet = new Spreadsheet();

        $sheet = $spreadSheet->getActiveSheet();
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setScale(68); // 縮放比例
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        
        $sheet->getPageMargins()->setTop(0.4);
        $sheet->getPageMargins()->setBottom(0.4);
        $sheet->getPageMargins()->setLeft(0.6);
        $sheet->getPageMargins()->setRight(0.6);

        $spreadSheet->getDefaultStyle()->getFont()->setName('標楷體');
        $sheet->mergeCells('A3:A6');
        $sheet->getCell("A7")->setValue('訓練班別');
        
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

        // 月份 日期
        $field = [66, 64];
        $weekText = ['', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二'];

        $break = 0;
        foreach ($weekMonths as $month => $weekDays){
            
            $monthSRowName = "";
            foreach ($field as $ascCode){
                if ($ascCode > 64 && $ascCode < 91){
                    $monthSRowName = chr($ascCode).$monthSRowName;
                } 
            }       

            $sheet->getCell("{$monthSRowName}3")->setValue($weekText[$month].'月');
            foreach ($weekDays as $weekDay){
                $break++;
                $sRowName = "";
                foreach ($field as $ascCode){
                    if ($ascCode > 64 && $ascCode < 91){
                        $sRowName = chr($ascCode).$sRowName;
                    } 
                }

                $field[0] = $field[0] + 6;
                if ($field[0] >= 91){
                    $field[0] -= 26;
                    $field[1]++;
                }

                $sheet->getCell("{$sRowName}4")->setValue(substr($weekDay['sdate'], 4, 2).'/'.substr($weekDay['sdate'], 6, 2).'-'.substr($weekDay['edate'], 4, 2).'/'.substr($weekDay['edate'], 6, 2));

                $eRowName = "";
                foreach ($field as $ascCode){
                    if ($ascCode > 64 && $ascCode < 91){
                        $eRowName = chr($ascCode).$eRowName;
                    } 
                }

                if ($break == 3){

                    $breakField = $field;
                    $breakField[0]++;
                    if ($breakField[0] >= 91){
                        $breakField[0] -= 26;
                        $breakField[1]++;
                    }

                    $breakRowName="";
                    foreach ($breakField as $ascCode){
                        if ($ascCode > 64 && $ascCode < 91){
                            $breakRowName = chr($ascCode).$breakRowName;
                        } 
                    }

                    $sheet->setBreak($breakRowName.'1', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN); // 分頁符號
                    $break = 0;
                }

                // 日期儲存格合併
                $sheet->mergeCells("{$sRowName}4:{$eRowName}4");
                
                $field[0] = $field[0] + 1;
                if ($field[0] >= 91){
                    $field[0] = 65;
                    $field[1]++;
                }
            }

            $sheet->mergeCells("{$monthSRowName}3:{$eRowName}3");
        }

        $sheet->getStyle("A3:{$eRowName}6")->applyFromArray($styleArray);

        $rowdatas = [];

        foreach ($t04tbs as $key => $t04tb){
            $sdate = (substr($t04tb->sdate, 0, 3) + 1911).substr($t04tb->sdate, 3);
            $edate = (substr($t04tb->edate, 0, 3) + 1911).substr($t04tb->edate, 3);
            $row = 7;
            if (DateTime::createFromFormat('Ymd', $sdate) !== false && DateTime::createFromFormat('Ymd', $edate) !== false){
                
                do{
                    $empty = true;
                    $sdateClass = new DateTime($sdate);
                    $edateClass = new DateTime($edate);
                    $tmpData = [];
                    while($sdateClass->format('Ymd') <= $edateClass->format('Ymd') && $empty){
                        if (isset($rowdatas[$row][$sdateClass->format('Ymd')])){
                            $empty = false;
                        }
                        $tmpData[$row][$sdateClass->format('Ymd')] = $t04tb;
                        $sdateClass->modify('+1 day');
                    }
                    if ($empty){
                       
                    }else{
                        $row = $row + 3;
                    }                   
                }while($empty == false);

            }

            foreach ($tmpData as $row => $dateGroup){
                foreach ($dateGroup as $date => $tmp){
                    if (!isset($rowdatas[$row][$date])) $rowdatas[$row][$date] = [];
                    $rowdatas[$row][$date] = $tmp;
                }                
            }
        }

        $maxRow = max(array_keys($rowdatas)) + 2;

        $sheet->mergeCells("A7:A".$maxRow);

        // 框線 每三格當成一格
        for($i=7; $i<=$maxRow+1; $i = $i + 3){
            $sheet->getStyle("A{$i}:{$eRowName}{$i}")
                  ->getBorders()
                  ->getTop()
                  ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);               
        }

        $field = [65, 64];

        $rowname = "";
        while($rowname != $eRowName){
            $rowname = "";
            foreach ($field as $ascCode){
                if ($ascCode > 64 && $ascCode < 91){
                    $rowname = chr($ascCode).$rowname;
                } 
            }

            $sheet->getStyle("{$rowname}7:{$rowname}{$maxRow}")
                  ->getBorders()
                  ->getRight()
                  ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN); 
            $field[0]++;
            if ($field[0] >= 91){
                $field[0] = 65;
                $field[1]++;
            }            
        }

        $sheet->getStyle("A1:{$eRowName}{$maxRow}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sectionColor = [
            '綜合規劃組' => 'FF8888',
            '培育發展組' => 'FFBB66',
            '專業訓練組' => 'DDFF77', 
            '數位學習組' => '66FF66', 
            '秘書室' => '77FFEE', 
            '人事室' => '99BBFF', 
            '主計室' => 'E38EFF'
        ];


        foreach ($rowdatas as $row => $rowdata){
            foreach ($rowdata as $date => $t04tb) {
                $sheet->getCell($dateLocation[$date].$row)->setValue($t04tb->term.' '.$t04tb->real_quota);
                $sheet->getCell($dateLocation[$date].($row+1))->setValue($t04tb->class_name);
                $sheet->getCell($dateLocation[$date].($row+2))->setValue(substr($t04tb->sdate, 5, 2).'-'.substr($t04tb->edate, 5, 2));
                // 組室顏色(部門) 綜合規劃組、培育發展組、專業訓練組、數位學習組、秘書室、人事室、主計室

                if (isset($sectionColor[$t04tb->section])){
                    $sheet->getStyle($dateLocation[$date].$row.':'.$dateLocation[$date].($row+2))
                          ->getFill()
                          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                          ->getStartColor()
                          ->setARGB($sectionColor[$t04tb->section]);                     
                }

            }
        }


        // 整理班期上課時間資訊
        $totalQuota = [];
        $classCount = [];
        $classCountOutSide = []; 

        $merged = [];

        foreach ($rowdatas as $row => $rowdata){
            foreach ($rowdata as $date => $t04tb) {
                if (!isset($merged[$t04tb->class.'-'.$t04tb->term][$row])){
                    $merged[$t04tb->class.'-'.$t04tb->term][$row] = [];
                }
                $merged[$t04tb->class.'-'.$t04tb->term][$row][] = $date;

                // 排除外地班, 一天以下的班級(計算方式以小時算的都等於一天以下)
                if (($t04tb->process != 4 && $t04tb->kind != 3 && $t04tb->kind != 2) || ($t04tb->kind == 2 && $t04tb->period >= 1)){
                    if (!isset($totalQuota[$date])){
                        $totalQuota[$date] = 0;
                    }                    

                    $totalQuota[$date] += $t04tb->real_quota;                  
                }

                if ($t04tb->process != 4){
                    if (!isset($classCount[$date])){
                        $classCount[$date] = 0;
                    }  

                    $classCount[$date]++;  
                }else{
                    if (!isset($classCountOutSide[$date])){
                        $classCountOutSide[$date] = 0;
                    }  

                    $classCountOutSide[$date]++;  
                }

            }
        }

        // 合併連續上課的儲存格
        foreach ($merged as $rowGroup){
            foreach ($rowGroup as $row => $dates){
                $sRow = $dateLocation[min($dates)];
                $eRow = $dateLocation[max($dates)];
                $sheet->mergeCells($sRow.$row.':'.$eRow.$row);
                $sheet->mergeCells($sRow.($row+1).':'.$eRow.($row+1));
                $sheet->mergeCells($sRow.($row+2).':'.$eRow.($row+2));
            }
        }

        // 填入合計人數 (不含外地班一天以下的班級)
        foreach ($dateLocation as $date => $location){
            $quota = isset($totalQuota[$date]) ? $totalQuota[$date] : 0;
            $sheet->getCell($location.'5')->setValue($quota);
            $surplusBed = (($bedQuantity - $quota) > 0) ? $bedQuantity - $quota : 0;
            $sheet->getCell($location.'6')->setValue("({$surplusBed})");
        }

        $styleArray = array(
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        );

        // 自動換行
        $sheet->getStyle("A3:{$eRowName}{$maxRow}")->getAlignment()->setWrapText(true);

        // 最右邊框線
        $sheet->getStyle("A3:A{$maxRow}")
              ->getBorders()
              ->getLeft()
              ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // 班次
        $classCountLocation = $maxRow + 1;
        $sheet->mergeCells("A".$classCountLocation.":A".($classCountLocation + 1));      
        $sheet->getCell("A".($classCountLocation))->setValue("班次");

        $sheet->getStyle("A{$classCountLocation}:{$eRowName}".($classCountLocation+1))
              ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);            

        // 垂直 水平 置中 
        $sheet->getStyle("A3:{$eRowName}".($maxRow+3))->applyFromArray($styleArray);

        $field = [66, 64];

        foreach ($dateLocation as $date => $location){
            $quota = isset($classCount[$date]) ? $classCount[$date] : 0;
            $sheet->getCell($location.$classCountLocation)->setValue($quota);
            $quota = isset($classCountOutSide[$date]) ? $classCountOutSide[$date] + $quota : $quota;
            $sheet->getCell($location.($classCountLocation+1))->setValue("({$quota})"); 
        }

        $now = (new DateTime())->format('Y/m/d');

        $sheet->getHeaderFooter()->setOddFooter("&L&B &R {$now} &P");

        // 開始輸出
        $objWriter = new Xlsx($spreadSheet);
        $fileName = '年度流路明細表';

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

        $objWriter->save('php://output');
        exit;
    }

    private function getT04tbs($queryData){

        $t13tbCount = 'select class, term, count(*) as real_quota from `t13tb` group by `class`, `term`';
        $t04tbs = T04tb::join('t01tb', 't01tb.class', '=', 't04tb.class')
                       ->leftJoin(\DB::raw("($t13tbCount) as t13tbCount"), function($join){
                          $join->on('t13tbCount.class', '=', 't04tb.class')
                               ->on('t13tbCount.term', '=', 't04tb.term');
                       })
                       ->where(function($query) use ($queryData){
                            $query->where(function($query1) use ($queryData){
                                $query1->where('sdate', '>=', $queryData['sdate'])
                                       ->where('sdate', '<=', $queryData['edate']);
                            });

                            $query->orWhere(function($query1) use ($queryData){
                                $query1->where('edate', '>=', $queryData['sdate'])
                                       ->where('edate', '<=', $queryData['edate']); 
                            });

                       })
                       ->selectRaw('t04tb.*, t01tb.name as class_name, IFNULL(t13tbCount.real_quota, 0) as real_quota, t01tb.process, t01tb.kind, t01tb.period');

        if (!empty($queryData['branch'])){
            $t04tbs->where('t01tb.branch', '=', $queryData['branch']);
        }

        return $t04tbs->get();
    }

}
