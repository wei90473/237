<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\User_groupService;
use App\Services\PlaceNantouClassroomclsService;
use DB;

class PlaceNantouClassroomclsController extends Controller
{
    /**
     * PlaceNantouController constructor.
     * @param AgencyService $agencyService
     */
    public function __construct(User_groupService $user_groupService, PlaceNantouClassroomclsService $placeNantouClassroomclsService)
    {
        $this->placeNantouClassroomclsService = $placeNantouClassroomclsService;
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
        $queryData = $request->only('croomclsname', '_paginate_qty');
        $data = $this->placeNantouClassroomclsService->getClassRoomlsList($queryData);

        return view('admin/place_nantou_classroomcls/index', compact('data', 'queryData'));
    }

    public function create()
    {
        // 教室別
        $classroomcls = \App\Models\Edu_classroomcls::all()->pluck('croomclsname', 'croomclsno')->toArray();
        $classroomcls = ['' => '請選擇'] + $classroomcls;

        // 區域別
        $classcode = \App\Models\Edu_classcode::where('deleted', '=', 0)->where('class', '=', '49')->get()->pluck('name', 'code')->toArray();
        $classcode = ['' => '請選擇'] + $classcode;
        $action = 'create';

        return view('admin/place_nantou_classroomcls/form', compact('action', 'classroomcls', 'classcode'));
    }

    public function store(Request $request)
    {
        $this->validate($request, ['croomclsno' => 'required']);

        $classrooom = $request->only([
            'croomclsno', 'croomclsname', 'croomclsfullname', 'borrow', 'classroom', 'description', 'link', 'summary2', 'summary1', 'note', 'printseq'
        ]);

        $create = $this->placeNantouClassroomclsService->createClassRoomcls($classrooom);

        if ($create === true){
            return back()->withResult(1)->withMessage('新增成功');
        }elseif ($create === 1){
            $message = '場地代碼重複';   
        }else{
            $message = '新增失敗';
        }
        return back()->withResult(0)->withMessage($message);
    }

    public function edit($croomclsno)
    {
        $classroomcls = $this->placeNantouClassroomclsService->getClassRoomcls($croomclsno);

        if (empty($classroomcls)){
            return back()->withResult(0)->withMessage('找不到該教室別');
        }

        $action = 'edit';
        
        if ($classroomcls->classroom == 0){
            $classroomcls->fees = (isset($classroomcls->fees)) ? $classroomcls->fees[0] : null;
        }elseif ($classroomcls->classroom){
            $classroomcls->fees = $classroomcls->fees->keyBy('timetype');
        }
        

        return view('admin/place_nantou_classroomcls/form', compact('action', 'classroomcls', 'fees'));
    }

    public function update(Request $request, $croomclsno)
    {
        $classrooom = $request->only([
            'croomclsname', 'croomclsfullname', 'borrow', 'classroom', 'description', 'link', 'summary2', 'summary1', 'note', 'printseq'
        ]);

        $classroomcls = $this->placeNantouClassroomclsService->getClassRoomcls($croomclsno);
        if (empty($classroomcls)){
            return back()->withResult(0)->withMessage('找不到該教室別');
        }

        $update = $this->placeNantouClassroomclsService->updateClassRoomcls($classroomcls, $classrooom);

        if ($update){
            return back()->withResult(1)->withMessage('更新成功');
        }else{
            return back()->withResult(0)->withMessage('更新失敗');
        }
    }

    public function editFeeSetting($type, $croomclsno)
    {
        $classroomcls = $this->placeNantouClassroomclsService->getClassRoomcls($croomclsno);
        if (empty($classroomcls)){
            return back()->withResult(0)->withMessage('找不到該教室別');
        }

        if ($classroomcls->classroom == 1){
            $fees = null;
            switch ($type) {
                case 'weekdays':
                    $fees = $this->placeNantouClassroomclsService->getFeeByDay($croomclsno);
                    $feetype = ($fees->isEmpty()) ? null : $fees[0]->feetype;
                    $fees = $fees->keyBy('timetype');
                    break;
                case 'holiday':
                    $fees = $this->placeNantouClassroomclsService->getFeeByDay($croomclsno);
                    $feetype = ($fees->isEmpty()) ? null : $fees[0]->feetype;
                    $fees = $fees->keyBy('timetype');
                    break;
                case 'time':
                    $fees = $this->placeNantouClassroomclsService->getFeeByTime($croomclsno);
                    $feetype = ($fees->isEmpty()) ? null : $fees[0]->feetype;
                    $fees = $fees->keyBy('timetype');
                    break;
                    break;                            
                default:
                    # code...
                    break;
            }            
        }elseif ($classroomcls->classroom == 0){
            $fees = $this->placeNantouClassroomclsService->getFee($croomclsno);
            $feetype = (isset($fees)) ? $fees->feetype : null;
        }

        return view('admin/place_nantou_classroomcls/feeSettingForm', compact('type', 'fees', 'feetype', 'croomclsno', 'classroomcls'));
    }

    public function updateFeeSetting(Request $request, $type, $croomclsno)
    {
        if (in_array($type, ['weekdays', 'holiday'])){
            $this->validate($request,  ['feetype' => 'required']);
        }

        $classroomcls = $this->placeNantouClassroomclsService->getClassRoomcls($croomclsno);
        
        if (empty($classroomcls)){
            return back()->withResult(0)->withMessage('找不到該教室別');
        }

        if ($classroomcls->classroom == 1){
            switch ($type) {
                case 'weekdays':

                    if ($request->feetype == 1){
                        $fee = $request->only(['feetype', 'fee.201', 'fee.202']);
                    }elseif ($request->feetype == 2){
                        $fee = $request->only(['feetype', 'fee.203']);
                    }
                    $fee['type'] = 'weekdays';
            
                    break;
                case 'holiday':

                    if ($request->feetype == 1){
                        $fee = $request->only(['feetype', 'fee.201', 'fee.202']);
                    }elseif ($request->feetype == 2){
                        $fee = $request->only(['feetype', 'fee.203']);
                    }  
                    $fee['type'] = 'holiday';

                    break;
                case 'time':

                    $fee = $request->only(['feetype', 'fee.401', 'fee.402', 'fee.403']);
                    $fee['feetype'] = 4;

                    break;                            
                default:
                    # code...
                    break;
            }            
        }elseif ($classroomcls->classroom == 0){
            $fee = $request->only(['feetype', 'fee', 'holidayfee']);
        }


        $result = $this->placeNantouClassroomclsService->insertFeeSetting($classroomcls, $fee);

        if ($result){
            return back()->withResult(1)->withMessage('設定成功');
        }

        // dd($fee);
    }
}
