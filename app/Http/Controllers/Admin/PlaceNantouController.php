<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\User_groupService;
use App\Services\PlaceNantouService;
use DB;

class PlaceNantouController extends Controller
{
    /**
     * PlaceNantouController constructor.
     * @param AgencyService $agencyService
     */
    public function __construct(User_groupService $user_groupService, PlaceNantouService $placeNantouService)
    {
        $this->placeNantouService = $placeNantouService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('place_nantou', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    public function classroom(Request $request)
    {
        $queryData = $request->only('roomno', 'roomname', '_paginate_qty');
        $data = $this->placeNantouService->getClassRoomList($queryData);
        return view('admin/place_nantou/index', compact('data', 'queryData'));
    }

    public function create()
    {
        // 教室別
        $classroomcls = \App\Models\Edu_classroomcls::all()->pluck('croomclsname', 'croomclsno')->toArray();
        $classroomcls = ['' => '請選擇'] + $classroomcls;

        // 區域別
        $classcode = \App\Models\Edu_classcode::where('deleted', '=', 0)->where('class', '=', '49')->pluck('name', 'code')->toArray();
        $classcode = ['' => '請選擇'] + $classcode;
        $action = 'create';

        return view('admin/place_nantou/form', compact('action', 'classroomcls', 'classcode'));
    }

    public function store(Request $request)
    {
        $this->validate($request, ['roomno' => 'required']);

        $classrooom = $request->only([
            'roomno', 'roomname', 'fullname', 'num', 'roomcla', 'zonecode', 'summary'
        ]);

        $create = $this->placeNantouService->createClassRoom($classrooom);

        if ($create === true){
            return back()->withResult(1)->withMessage('新增成功');
        }elseif ($create === 1){
            $message = '場地代碼重複';   
        }else{
            $message = '新增失敗';
        }
        return back()->withResult(0)->withMessage($message);
    }

    public function edit($roomno)
    {
        $classroom = $this->placeNantouService->getClassRoom($roomno);
        
        // 教室別
        $classroomcls = \App\Models\Edu_classroomcls::all()->pluck('croomclsname', 'croomclsno')->toArray();
        $classroomcls = ['' => '請選擇'] + $classroomcls;

        // 區域別
        $classcode = \App\Models\Edu_classcode::where('deleted', '=', 0)->where('class', '=', '49')->pluck('name', 'code')->toArray();
        $classcode = ['' => '請選擇'] + $classcode;
        // dd($classcode);
        $action = 'edit';
        return view('admin/place_nantou/form', compact('classroom', 'action', 'classroomcls', 'classcode'));
    }

    public function update(Request $request, $roomno)
    {
        $classrooom = $request->only([
            'roomname', 'fullname', 'num', 'roomcla', 'zonecode', 'summary'
        ]);

        $update = $this->placeNantouService->updateClassRoom($roomno, $classrooom);

        if ($update){
            return back()->withResult(1)->withMessage('更新成功');
        }else{
            return back()->withResult(0)->withMessage('更新失敗');
        }
    }
}
