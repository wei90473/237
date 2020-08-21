<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CalendarService;
use App\Services\ScheduleService;

use App\Helpers\Common;

/*
    訓練排程處理控制器
*/
class CalendarController extends Controller
{
    /**
     * ScheduleController constructor.
     * @param CalendarService $ScheduleService
     */
    public function __construct(
        CalendarService $calendarService,
        ScheduleService $scheduleService
    )
    {
        $this->calendarService = $calendarService;
        $this->scheduleService = $scheduleService;
    }

    public function index(Request $request)
    {
        $queryData = $request->only([
            'class',
            'term'
        ]);

        if (empty($queryData['class']) || empty($queryData['term'])){
            $t04tb_terms = [];
        }else{
            $t04tb = $this->scheduleService->getT04tb($queryData['class'], $queryData['term']);
            if (empty($t04tb)){
                return back()->with('result', '0')->with('message', '找不到開班資料');
            }
            $t01tb = $this->scheduleService->getClass($queryData['class']);
            $t04tb_terms = $t01tb->t04tbs->pluck("term")->toArray();
        }
        // 辦班人員
        $sponsors = $this->scheduleService->getSponsors();
        // 教室
        $class_rooms = $this->scheduleService->getClassRooms();
        // 部門
        $sections = $this->scheduleService->getSections();
        // 可開班清單
        // $t01tbs = $this->scheduleService->getAllT01tbs();  
        
        return view('admin/calendar/index', compact('queryData', 'sponsors', 'class_rooms', 'sections', 't04tb', 't04tb_terms'));
    }

    public function store(Request $request, $class, $term)
    {
        $insert_data['t36tb'] = $request->only([
            "date",
            "site"
        ]);
        $insert_data['t36tb']['class'] = $class;
        $insert_data['t36tb']['term'] = $term;

        // $insert_data['t04tb'] = $request->only([
        //     "sponsor",
        //     "section",
        //     "quota"
        // ]);

        $insert = $this->calendarService->storeCalendar($insert_data, "insert");
        if ($insert){
            return back()->with('result', 1)
                         ->with('message', '新增成功');
        }else{
            return back()->with('result', 0)
                         ->with('message', '新增失敗');                
        }
    }

    public function update(Request $request, $class, $term)
    {
        $update_data = [];

        $update_data['t36tb'] = $request->only([
            "date",
            // "site"
        ]);

        $update_data['t04tb'] = $request->only([
            "sponsor",
            "section",
            "quota"
        ]);
        
        $update_data['orgin'] = [
            "class" => $class,
            "term" => $term,
            "date" => $request->origin_date
        ];

        $update = $this->calendarService->storeCalendar($update_data, "update");

        if ($update === 1){
            return back()->with('result', 0)
                         ->with('message', '日期重複！');            
        }

        if ($update){
            return back()->with('result', 1)
                         ->with('message', '更新成功');
        }else{
            return back()->with('result', 0)
                         ->with('message', '更新失敗');                
        }
    }

    public function delete(Request $request, $class, $term)
    {
        if (!empty($request->origin_date)){
            $delete_data = [
                "class" => $class,
                "term" => $term,
                "date" => $request->origin_date
            ];            
            $delete = $this->calendarService->deleteCalendar($delete_data);
            if ($delete){
                return back()->with('result', 1)
                             ->with('message', '刪除成功');
            }else{
                return back()->with('result', 0)
                             ->with('message', '刪除失敗');                
            }
        }
    }
}