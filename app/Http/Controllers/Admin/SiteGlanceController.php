<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DateTime;

use App\Models\T04tb;
use App\Models\T36tb;

use App\Models\T97tb;
use App\Models\T22tb;
use App\Models\T06tb;
// use App\Models\





use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Models\M14tb;
use App\Models\Edu_classroom;

class SiteGlanceController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('SiteGlanceController', $user_group_auth)){
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
        $classrooms = $this->getClassRooms();
        return view('admin/siteGlance/list', compact(['classrooms']));
    }


    public function export(Request $request)
    {
        $this->validate($request, ['trainingSdate' => 'required', 'trainingEdate' => 'required']);

        $queryData = $request->only(['class', 'class_name', 'term', 'trainingSdate', 'trainingEdate', 'site_branch', 'site', 'teacher_id', 'course_name', 'commission']);

        $sites = $this->getRoomData($queryData);
        

        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();

        $sheet->getPageSetup()
              ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()
              ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $spreadSheet->getDefaultStyle()->getFont()->setName('標楷體');

        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');
        
        $sheet->getCell('A1')->setValue('行政院人事行政總處公務人力發展學院');
        $sheet->getCell('A2')->setValue('教室使用一覽表');
        $sheet->getCell('A3')->setValue('使用期間');
        $sheet->getStyle('A1:A3')->getFont()->setSize(14);

        $styleArray = array(
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => array(
                'allBorders' => array(
                    'color' => array('argb' => '000000'),
                ),
            ),
        );
        $sheet->getStyle('A1:H3')->applyFromArray($styleArray);

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

        $sheet->getCell('A4')->setValue('教室名稱');
        $sheet->getCell('B4')->setValue('次數');
        $sheet->getCell('C4')->setValue('班名(分班名稱)');
        $sheet->getCell('D4')->setValue('科目名稱');
        $sheet->getCell('E4')->setValue('講座');
        $sheet->getCell('F4')->setValue('節次');
        $sheet->getCell('G4')->setValue('日期');
        $sheet->getCell('H4')->setValue('承辦人');

        $row = 4;

        foreach ($sites as $Csrms){
            foreach ($Csrms as $site => $t06tbGroup){
                $merge = false;

                foreach ($t06tbGroup as $key => $t06tb){
                    if ($merge){
                        $sheet->mergeCells('A'.$row.':'.'A'.($row+1));
                    }
                    $row++;
                    if ($key == 0){
                        $sheet->getCell('A'.$row)->setValue($t06tb->site_name);
                    }

                    $sheet->getRowDimension($row)->setRowHeight(30); 

                    $sheet->getCell('B'.$row)->setValue($t06tbGroup->count());
                    $commission = (empty($t06tb->commission)) ? '' : "({$t06tb->commission})";
                    $sheet->getCell('C'.$row)->setValue($t06tb->class_name.$commission);
                    $sheet->getCell('D'.$row)->setValue($t06tb->course_name);
                    $sheet->getCell('E'.$row)->setValue($t06tb->teacher_name);
                    $sheet->getCell('F'.$row)->setValue($t06tb->stime.'~'.$t06tb->etime);
                    $sheet->getCell('G'.$row)->setValue($t06tb->date);
                    $sheet->getCell('H'.$row)->setValue($t06tb->sponsor_name);
                    $merge = !$merge;
                }
            }
        }

        $sheet->getStyle('A4:H'.$row)->applyFromArray($styleArray);
        $sheet->getStyle('A4:H'.$row)->getFont()->setSize(12);
        $sheet->getStyle('A4:H'.$row)->getAlignment()->setWrapText(true);

        $sheet->getColumnDimension('A')->setWidth(28);
        
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('D')->setWidth(28);
        $sheet->getColumnDimension('F')->setWidth(13);

        $objWriter = new Xlsx($spreadSheet);
        $fileName = '教室使用一覽表';

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

    public function getClassRooms()
    {
        $class_room = [
            "m14tb" => M14tb::all(),
            "m25tb" => Edu_classroom::all()
        ];

        $class_room['m14tb'] = $class_room['m14tb']->map(function($m14tb){
            return [
                'site' => $m14tb->site,
                'name' => $m14tb->name
            ];
        })->toArray();

        $class_room['m25tb'] = $class_room['m25tb']->map(function($m25tb){
            return [
                'site' => $m25tb->roomno,
                'name' => $m25tb->roomname
            ];
        })->toArray();

        return $class_room;
    }

    public function getRoomData($queryData)
    {
        
        // 南投教室資料 (課程資料中有設定場地)
        $nantouCsrms = T06tb::selectRaw('
                            t06tb.class,
                            t06tb.term,
                            t01tb.name as class_name, 
                            t06tb.name as course_name,
                            t08tb.cname as teacher_name, 
                            t06tb.stime,
                            t06tb.etime,
                            t06tb.date,
                            m09tb.username as sponsor_name,
                            edu_classroom.roomno as site,
                            edu_classroom.fullname as site_name,
                            t01tb.commission'
                        )                 
                      ->join('t04tb', function($join){
                          $join->on('t04tb.class', '=', 't06tb.class')
                               ->on('t04tb.term', '=', 't06tb.term');
                      })  
                      ->join('t01tb', 't01tb.class', '=', 't04tb.class')
                      ->join('edu_classroom', 'edu_classroom.roomno', '=', 't06tb.site') 
                      ->leftjoin('t08tb', function($join){
                          $join->on('t08tb.class', '=', 't06tb.class')
                               ->on('t08tb.term', '=', 't06tb.term')
                               ->on('t08tb.course', '=', 't06tb.course');
                      })      
                      ->leftJoin('m09tb', 'm09tb.userid', '=', 't04tb.sponsor')                                      
                      ->where('t06tb.date', '>', $queryData['trainingSdate'])
                      ->where('t06tb.date', '<', $queryData['trainingEdate'])
                      ->where('t06tb.branch', '=', 2)
                      ->where('t06tb.site', '<>', null);

        // 南投教室資料 (課程資料中無設定場地)
        $nantouCsrmsDefault = T06tb::selectRaw('
                            t06tb.class,
                            t06tb.term,
                            t01tb.name as class_name, 
                            t06tb.name as course_name,
                            t08tb.cname as teacher_name, 
                            t06tb.stime,
                            t06tb.etime,
                            t06tb.date,
                            m09tb.username as sponsor_name,
                            edu_classroom.roomno as site,
                            edu_classroom.fullname as site_name,
                            t01tb.commission'
                        )                 
                      ->join('t04tb', function($join){
                          $join->on('t04tb.class', '=', 't06tb.class')
                               ->on('t04tb.term', '=', 't06tb.term');
                      })
                      ->join('t01tb', 't01tb.class', '=', 't04tb.class')  
                      ->join('edu_classroom', 'edu_classroom.roomno', '=', 't04tb.site') 
                      ->leftjoin('t08tb', function($join){
                          $join->on('t08tb.class', '=', 't06tb.class')
                               ->on('t08tb.term', '=', 't06tb.term')
                               ->on('t08tb.course', '=', 't06tb.course');
                      })      
                      ->leftJoin('m09tb', 'm09tb.userid', '=', 't04tb.sponsor')                                      
                      ->where('t06tb.date', '>', $queryData['trainingSdate'])
                      ->where('t06tb.date', '<', $queryData['trainingEdate'])
                      ->where('t04tb.site_branch', '=', 2)
                      ->where('t04tb.site', '<>', null)
                      ->where('t06tb.site', '=', null);
                      
        // 臺北教室資料 (課程資料中有設定場地)
        $taipeiCsrms = T06tb::selectRaw('
                            t06tb.class,
                            t06tb.term,
                            t01tb.name as class_name, 
                            t06tb.name as course_name,
                            t08tb.cname as teacher_name, 
                            t06tb.stime,
                            t06tb.etime,
                            t06tb.date,
                            m09tb.username as sponsor_name,
                            m14tb.site,
                            m14tb.name as site_name,
                            t01tb.commission'
                        )                 
                      ->join('t04tb', function($join){
                          $join->on('t04tb.class', '=', 't06tb.class')
                               ->on('t04tb.term', '=', 't06tb.term');
                      })  
                      ->join('t01tb', 't01tb.class', '=', 't04tb.class')
                      ->join('m14tb', 'm14tb.site', '=', 't06tb.site') 
                      ->join('t08tb', function($join){
                          $join->on('t08tb.class', '=', 't06tb.class')
                               ->on('t08tb.term', '=', 't06tb.term')
                               ->on('t08tb.course', '=', 't06tb.course');
                      })      
                      ->leftJoin('m09tb', 'm09tb.userid', '=', 't04tb.sponsor')                                      
                      ->where('t06tb.date', '>', $queryData['trainingSdate'])
                      ->where('t06tb.date', '<', $queryData['trainingEdate'])
                      ->where('t06tb.branch', '=', 1)
                      ->where('t06tb.site', '<>', null);

        // 臺北教室資料 (課程資料中無設定場地)              
        $taipeiCsrmsDefault = T06tb::selectRaw('
                            t06tb.class,
                            t06tb.term,
                            t01tb.name as class_name, 
                            t06tb.name as course_name,
                            t08tb.cname as teacher_name, 
                            t06tb.stime,
                            t06tb.etime,
                            t06tb.date,
                            m09tb.username as sponsor_name,
                            m14tb.site,
                            m14tb.name as site_name,
                            t01tb.commission'
                        )                 
                      ->join('t04tb', function($join){
                          $join->on('t04tb.class', '=', 't06tb.class')
                               ->on('t04tb.term', '=', 't06tb.term');
                      })  
                      ->join('t01tb', 't01tb.class', '=', 't04tb.class')
                      ->join('m14tb', 'm14tb.site', '=', 't04tb.site') 
                      ->leftJoin('t08tb', function($join){
                          $join->on('t08tb.class', '=', 't06tb.class')
                               ->on('t08tb.term', '=', 't06tb.term')
                               ->on('t08tb.course', '=', 't06tb.course');
                      })      
                      ->leftJoin('m09tb', 'm09tb.userid', '=', 't04tb.sponsor')                                      
                      ->where('t06tb.date', '>', $queryData['trainingSdate'])
                      ->where('t06tb.date', '<', $queryData['trainingEdate'])
                      ->where('t04tb.site_branch', '=', 1)
                      ->where('t04tb.site', '<>', null)
                      ->where('t06tb.site', '=', null);

        if (!empty($queryData['class'])){
            $taipeiCsrms->where('t06tb.class', 'LIKE', "%{$queryData['class']}%");
            $taipeiCsrmsDefault->where('t06tb.class', 'LIKE', "%{$queryData['class']}%");
            $nantouCsrms->where('t06tb.class', 'LIKE', "%{$queryData['class']}%");
            $nantouCsrmsDefault->where('t06tb.class', 'LIKE', "%{$queryData['class']}%");
        }
        
        if (!empty($queryData['class_name'])){
            $taipeiCsrms->where('t01tb.name', 'LIKE', "%{$queryData['class_name']}%");
            $taipeiCsrmsDefault->where('t01tb.name', 'LIKE', "%{$queryData['class_name']}%");
            $nantouCsrms->where('t01tb.name', 'LIKE', "%{$queryData['class_name']}%");
            $nantouCsrmsDefault->where('t01tb.name', 'LIKE', "%{$queryData['class_name']}%");
        }

        if (!empty($queryData['term'])){
            $queryData['term'] = str_pad($queryData['term'], 2, '0', STR_PAD_LEFT);
            $taipeiCsrms->where('t04tb.term', '=', $queryData['term']);
            $taipeiCsrmsDefault->where('t04tb.term', '=', $queryData['term']);
            $nantouCsrms->where('t04tb.term', '=', $queryData['term']);
            $nantouCsrmsDefault->where('t04tb.term', '=', $queryData['term']);
        }

        if (!empty($queryData['site_branch'])){
            if ($queryData['site_branch'] == 1){
                $taipeiCsrms->where('t06tb.site', '=', $queryData['site']);
                $taipeiCsrmsDefault->where('t04tb.site', '=', $queryData['site']);
            }else if ($queryData['site_branch'] == 2){
                $nantouCsrms->where('t06tb.site', '=', $queryData['site']);
                $nantouCsrmsDefault->where('t04tb.site', '=', $queryData['site']);                
            }
        }

        if (!empty($queryData['course_name'])){
            $taipeiCsrms->where('t06tb.name', 'LIKE', "%{$queryData['course_name']}%");
            $taipeiCsrmsDefault->where('t06tb.name', 'LIKE', "%{$queryData['course_name']}%");
            $nantouCsrms->where('t06tb.name', 'LIKE', "%{$queryData['course_name']}%");
            $nantouCsrmsDefault->where('t06tb.name', 'LIKE', "%{$queryData['course_name']}%");                
        }

        if (!empty($queryData['teacher_id'])){
            $taipeiCsrms->where('t08tb.idno', '=', $queryData['teacher_id']);
            $taipeiCsrmsDefault->where('t08tb.idno', '=', $queryData['teacher_id']);
            $nantouCsrms->where('t08tb.idno', '=', $queryData['teacher_id']);
            $nantouCsrmsDefault->where('t08tb.idno', '=', $queryData['teacher_id']);        
        }

        if (!empty($queryData['commission'])){
            $taipeiCsrms->where('t01tb.commission', 'LIKE', "%{$queryData['commission']}%");
            $taipeiCsrmsDefault->where('t01tb.commission', 'LIKE', "%{$queryData['commission']}%");
            $nantouCsrms->where('t01tb.commission', 'LIKE', "%{$queryData['commission']}%");
            $nantouCsrmsDefault->where('t01tb.commission', 'LIKE', "%{$queryData['commission']}%");          
        }


        if (!empty($queryData['site_branch']) && $queryData['site_branch'] == 1){             
            $nantouCsrms = collect([]);
        }else{
            $nantouCsrms = $nantouCsrms->union($nantouCsrmsDefault)->get()->groupBy('site');
        }

        if (!empty($queryData['site_branch']) && $queryData['site_branch'] == 2){             
            $nantouCsrms = collect([]);
        }else{
            $taipeiCsrms = $taipeiCsrms->union($taipeiCsrmsDefault)->get()->groupBy('site'); 
        }   

        return [$taipeiCsrms, $nantouCsrms];   
    }

}
