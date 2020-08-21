<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Teacher_relatedService;
use App\Services\Term_processService;
use App\Services\User_groupService;
use App\Models\T08tb;
use App\Models\T09tb;
use App\Models\T01tb;
use App\Models\M01tb;
use App\Models\T04tb;
use App\Models\Teacher_room;
use App\Models\Teacher_car;
use App\Models\Teacher_food;
use App\Models\Teacher_by_week;
use App\Models\Car_fare;
use DB ;

class Teacher_relatedController extends Controller
{
    /**
     * Teacher_relatedController constructor.
     * @param Teacher_relatedService $teacher_relatedService
     */
    public function __construct(Teacher_relatedService $teacher_relatedService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        $this->teacher_relatedService = $teacher_relatedService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('teacher_related', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('teacher_related');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $sponsor = $this->teacher_relatedService->getSponsor();
        //年
        $this_yesr = date('Y') - 1911;
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData['year_list']);
        // echo "\n</pre>\n";
        if(null == $request->get('yerly')){
            $queryData['yerly'] = $this_yesr;
        }else{
            $queryData['yerly'] = $request->get('yerly');
        }
        //班號
        $queryData['class'] = $request->get('class');
        $queryData['name'] = $request->get('name');

        // 分班名稱**
        $queryData['class_branch_name'] = $request->get('class_branch_name');
        // 期別
        $queryData['term'] = $request->get('term');
        // 辦班院區
        $queryData['branch'] = '2';
        // 班別類型
        $queryData['process'] = $request->get('process');

        $queryData['sponsor'] = $request->get('sponsor');
        // 訓練性質
        $queryData['traintype'] = $request->get('traintype');
        // 班別性質
        $queryData['type'] = $request->get('type');
        $queryData['categoryone'] = $request->get('categoryone');
        $queryData['sdate'] = $request->get('sdate');
        $queryData['edate'] = $request->get('edate');
        $queryData['sdate2'] = $request->get('sdate2');
        $queryData['edate2'] = $request->get('edate2');
        $queryData['sdate3'] = $request->get('sdate3');
        $queryData['edate3'] = $request->get('edate3');
        // 遴聘與否
        $queryData['hire'] = $request->get('hire');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData);
        // echo "\n</pre>\n";
        // die();
        $queryData['search'] = $request->get('search');

        if($queryData['search'] != 'search' ){
          $sess = $request->session()->get('lock_class');
            if($sess){
              $queryData2['class'] = $sess['class'];
              $queryData2['term'] = $sess['term'];
              $queryData2['yerly'] = substr($sess['class'], 0, 3);
              $data = $this->teacher_relatedService->getTeacher_relatedList($queryData2);
              return view('admin/teacher_related/list', compact('data', 'queryData', 'sponsor'));
            }
          $queryData2['class'] = 'none';
          $data = $this->teacher_relatedService->getTeacher_relatedList($queryData2);
        }else{
          $data = $this->teacher_relatedService->getTeacher_relatedList($queryData);
        }

        // $begin_date="2020-5-04";
        // $end_date="2020-5-08";

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($this->getSchemeDate($begin_date,$end_date));
        // echo "\n</pre>\n";
        // die();

        return view('admin/teacher_related/list', compact('data', 'queryData', 'sponsor'));
    }

    function getSchemeDate($begin_date = '', $end_date = '')
    {
      $datediff = strtotime($end_date) - strtotime($begin_date);
      $datediff = floor($datediff/(60*60*24));
      $week_list =array();
      $w = '';
      for($i = 0; $i < $datediff + 1; $i++){
          $week_no = date('W',strtotime($begin_date . ' + ' . $i . 'day'));
          if($week_no != $w){
          	if(date('w',strtotime($begin_date . ' + ' . $i . 'day')) != '7' && date('w',strtotime($begin_date . ' + ' . $i . 'day')) != '6'){
          		$week_list[$week_no]['begin_date'] = date("Y", strtotime($begin_date . ' + ' . $i . 'day'))-1911;
            	$week_list[$week_no]['begin_date'] .= date("md", strtotime($begin_date . ' + ' . $i . 'day'));
          	}

            $w = $week_no;
          }
          if(date('w',strtotime($begin_date . ' + ' . $i . 'day')) == '5'){
            $week_list[$week_no]['end_date'] = date("Y", strtotime($begin_date . ' + ' . $i . 'day'))-1911;
            $week_list[$week_no]['end_date'] .= date("md", strtotime($begin_date . ' + ' . $i . 'day'));
          }
          if(strtotime(date("Y-m-d", strtotime($begin_date . ' + ' . $i . 'day'))) == strtotime($end_date)){
            $week_list[$week_no]['end_date'] = date("Y", strtotime($begin_date . ' + ' . $i . 'day'))-1911;
            $week_list[$week_no]['end_date'] .= date("md", strtotime($begin_date . ' + ' . $i . 'day'));
          }
      }

      return $week_list;
    }

    public function detail(Request $request)
    {

        $queryData['class'] = $request->get('class');
        $queryData['term'] = $request->get('term');
        $queryData['sdate'] = $request->get('sdate');

        $class_data = $this->teacher_relatedService->getClass($queryData);

        // dd($class_data);
        $queryData['class_weeks_id'] = $class_data['class_weeks_id'];
        $queryData['sdate'] = $class_data['sdate'];
        $queryData['edate'] = $class_data['edate'];

        $teacher_list = $this->teacher_relatedService->getDetailList($queryData);
        // dd($teacher_list);

        return view('admin/teacher_related/detail', compact('data', 'class_data', 'teacher_list'));
    }

    public function changehire(Request $request, $id)
    {
      $queryData['class_weeks_id'] = $request->get('class_weeks_id');
      $queryData['t09tb_id'] = $id;

      $ClassWeek_data = $this->teacher_relatedService->getClassWeek($queryData);

      //班務流程凍結
        $freeze = $this->term_processService->getFreeze('teacher_related', $ClassWeek_data['class'], $ClassWeek_data['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

      $TeacherByWeek_data = $this->teacher_relatedService->getTeacherByWeek($queryData);

      if (!empty($TeacherByWeek_data)) {
          if($TeacherByWeek_data['confirm2'] == 'N'){
          	if($TeacherByWeek_data['confirm'] == 'N'){
	          	$teacherByWeek_fields = array(
		        	'confirm' => 'Y',
		        );
	          }else{
	          	$teacherByWeek_fields = array(
		        	'confirm' => 'N',
		        );
	          }

            if(checkNeedModifyLog('teacher_related')){
                $olddata = Teacher_by_week::where('id', $TeacherByWeek_data['id'])->get()->toarray();
            }

	          Teacher_by_week::where('id', $TeacherByWeek_data['id'])->update($teacherByWeek_fields);

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('teacher_related')){
                $nowdata = Teacher_by_week::where('id', $TeacherByWeek_data['id'])->get()->toarray();
                createModifyLog('U','Teacher_by_week',$olddata,$nowdata,end($sql));
            }

	          return redirect("/admin/teacher_related/detail?class={$ClassWeek_data['class']}&term={$ClassWeek_data['term']}&sdate={$ClassWeek_data['sdate']}")->with('result', '1')->with('message', '修改成功!');
          }else{
          	  return back()->with('result', '0')->with('html_message', "講座接待管理已確認，不可修改!");
          }

      } else {
          return back()->with('result', '0')->with('html_message', "講座未填寫，班務人員不可確認!");
      }
    }

    /**
     * 編輯頁
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit1($id)
    {

        $data_array = explode("_",$id);

        $queryData['t09tb_id'] = $data_array[0];
        $queryData['class_weeks_id'] = $data_array[1];

        $ClassWeek_data = $this->teacher_relatedService->getClassWeek($queryData);
        if(!empty($ClassWeek_data)){
          $queryData['class'] = $ClassWeek_data['class'];
          $queryData['term'] = $ClassWeek_data['term'];
          $queryData['sdate'] = $ClassWeek_data['sdate'];
          $queryData['edate'] = $ClassWeek_data['edate'];
          $ClassWeek_data['data_isset'] = 'Y';
        }
        $idno_data = $this->teacher_relatedService->getIdno($queryData);
        if(!empty($idno_data)){
          $queryData['idno'] = $idno_data['idno'];
        }

        $TeacherByWeek_data = $this->teacher_relatedService->getTeacherByWeek($queryData);

        if(empty($TeacherByWeek_data)){
          $TeacherByWeek_data['the_day_before'] = 'N';
          $TeacherByWeek_data['the_day_after'] = 'N';
        }
        $teachDate = $this->teacher_relatedService->getTeachDate($queryData);
        $RoomDate = $this->teacher_relatedService->getRoomDate($queryData);
        // dd($TeacherByWeek_data);
        $TeacherByWeek_data['before_day'] = $teachDate['before_day'];
        $TeacherByWeek_data['after_day'] = $teachDate['after_day'];

        $query = Teacher_room::select('teacher_room.date', 'teacher_room.morning', 'teacher_room.noon', 'teacher_room.evening', 'teacher_room.confirm', 'teacher_room.mark');
        $RoomIsset = $query->where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $queryData['idno'])->get()->toArray();
        if(empty($RoomIsset)){
          $ClassWeek_data['data_isset'] = 'N';
        }

        $ClassWeek_data['edit_id'] = $id;
        // if ( ! $data) {
        //     return view('admin/errors/error');
        // }

        return view('admin/teacher_related/form1', compact('data', 'ClassWeek_data', 'idno_data', 'TeacherByWeek_data', 'RoomDate'));
    }

    public function edit2($id)
    {

        $data_array = explode("_",$id);

        $queryData['t09tb_id'] = $data_array[0];
        $queryData['class_weeks_id'] = $data_array[1];

        $ClassWeek_data = $this->teacher_relatedService->getClassWeek($queryData);
        if(!empty($ClassWeek_data)){
          $queryData['class'] = $ClassWeek_data['class'];
          $queryData['term'] = $ClassWeek_data['term'];
          $queryData['sdate'] = $ClassWeek_data['sdate'];
          $queryData['edate'] = $ClassWeek_data['edate'];
          $ClassWeek_data['data_isset'] = 'Y';
        }
        $idno_data = $this->teacher_relatedService->getIdno($queryData);
        if(!empty($idno_data)){
          $queryData['idno'] = $idno_data['idno'];
        }

        $TeacherByWeek_data = $this->teacher_relatedService->getTeacherByWeek($queryData);
        $CarDate = $this->teacher_relatedService->getCarDate($queryData);
        $query = Teacher_car::select('teacher_car.type', 'teacher_car.time', 'teacher_car.date', 'teacher_car.location1', 'teacher_car.location2', 'teacher_car.location3', 'teacher_car.address');
        $CarIsset = $query->where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $queryData['idno'])->get()->toArray();
        if(empty($CarIsset)){
          if(empty($TeacherByWeek_data)){
            $ClassWeek_data['data_isset'] = 'N';
          }else{
            if($TeacherByWeek_data['drive_by_self'] == 'N'){
              $ClassWeek_data['data_isset'] = 'N';
            }
          }
        }
        if(empty($TeacherByWeek_data)){
          $TeacherByWeek_data['drive_by_self'] = 'N';
          $TeacherByWeek_data['go_by_self'] = 'N';
          $TeacherByWeek_data['come_by_self'] = 'N';
        }

        $ClassWeek_data['edit_id'] = $id;

        if(!empty($CarDate)){
          $data = $CarDate;
          if(!isset($data['location2_1'])){
            $data['location2_1'] = '';
          }
          if(!isset($data['location2_2'])){
            $data['location2_2'] = '';
          }
        }

        // if ( ! $data) {
        //     return view('admin/errors/error');
        // }

        return view('admin/teacher_related/form2', compact('data', 'ClassWeek_data', 'idno_data', 'TeacherByWeek_data'));
    }

    public function edit3($id)
    {

        $data_array = explode("_",$id);

        $queryData['t09tb_id'] = $data_array[0];
        $queryData['class_weeks_id'] = $data_array[1];

        $ClassWeek_data = $this->teacher_relatedService->getClassWeek($queryData);
        if(!empty($ClassWeek_data)){
          $queryData['class'] = $ClassWeek_data['class'];
          $queryData['term'] = $ClassWeek_data['term'];
          $queryData['sdate'] = $ClassWeek_data['sdate'];
          $queryData['edate'] = $ClassWeek_data['edate'];
          $ClassWeek_data['data_isset'] = 'Y';
        }
        $idno_data = $this->teacher_relatedService->getIdno($queryData);
        if(!empty($idno_data)){
          $queryData['idno'] = $idno_data['idno'];
        }

        $TeacherByWeek_data = $this->teacher_relatedService->getTeacherByWeek($queryData);
        $FoodDate = $this->teacher_relatedService->getFoodDate($queryData);
        $query = Teacher_food::select('date', 'breakfast', 'breakfast_type', 'breakfast_type2', 'lunch', 'lunch_type', 'lunch_type2', 'dinner', 'dinner_type', 'dinner_type2');
        $foodIsset = $query->where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $queryData['idno'])->orderBy('date', 'asc')->orderBy('date', 'asc')->get()->toArray();
        if(empty($foodIsset)){
          $ClassWeek_data['data_isset'] = 'N';
        }

        $ClassWeek_data['edit_id'] = $id;

        if(!empty($FoodDate)){
          $data = $FoodDate;
        }
        // if ( ! $data) {
        //     return view('admin/errors/error');
        // }

        return view('admin/teacher_related/form3', compact('data', 'ClassWeek_data', 'idno_data', 'TeacherByWeek_data'));
    }

    public function edit4($id)
    {

        $data_array = explode("_",$id);

        $queryData['t09tb_id'] = $data_array[0];
        $queryData['class_weeks_id'] = $data_array[1];

        $ClassWeek_data = $this->teacher_relatedService->getClassWeek($queryData);
        if(!empty($ClassWeek_data)){
          $queryData['class'] = $ClassWeek_data['class'];
          $queryData['term'] = $ClassWeek_data['term'];
          $queryData['sdate'] = $ClassWeek_data['sdate'];
          $queryData['edate'] = $ClassWeek_data['edate'];
        }
        $idno_data = $this->teacher_relatedService->getIdno($queryData);
        if(!empty($idno_data)){
          $queryData['idno'] = $idno_data['idno'];
        }

        $TeacherByWeek_data = $this->teacher_relatedService->getTeacherByWeek($queryData);

        if(empty($TeacherByWeek_data)){
          $TeacherByWeek_data['demand'] = '';
        }

        $ClassWeek_data['edit_id'] = $id;

        // dd($TeacherByWeek_data);
        // if ( ! $data) {
        //     return view('admin/errors/error');
        // }

        return view('admin/teacher_related/form4', compact('data', 'ClassWeek_data', 'idno_data', 'TeacherByWeek_data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {

        $data = $request->all();

        $data_array = explode("_",$id);
        $queryData['t09tb_id'] = $data_array[0];
        $queryData['class_weeks_id'] = $data_array[1];
        $idno_data = $this->teacher_relatedService->getIdno($queryData);

        $ClassWeek_data = $this->teacher_relatedService->getClassWeek($queryData);
        if(!empty($ClassWeek_data)){
          $queryData['class'] = $ClassWeek_data['class'];
          $queryData['term'] = $ClassWeek_data['term'];
          $queryData['sdate'] = $ClassWeek_data['sdate'];
          $queryData['edate'] = $ClassWeek_data['edate'];
        }
        $queryData['idno'] = $idno_data['idno'];

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('teacher_related', $ClassWeek_data['class'], $ClassWeek_data['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $TeacherByWeek_data = $this->teacher_relatedService->getTeacherByWeek($queryData);
        if(empty($TeacherByWeek_data)){
          $teacherByWeek_fields = array(
              'class_week_id' => $queryData['class_weeks_id'],
              't09tb_id' => $queryData['t09tb_id'],
              'idno' => $idno_data['idno'],
              'the_day_before' => 'N',
              'the_day_after' => 'N',
              'drive_by_self' => 'N',
              'come_by_self' => 'N',
              'go_by_self' => 'N',
              'confirm' => 'N',
          );

          if($data['type'] == 'teacher_room'){
              if(isset($data['before']) && $data['before'] == 'Y'){
                  $teacherByWeek_fields['the_day_before'] = 'Y';
              }
              if(isset($data['after']) && $data['after'] == 'Y'){
                  $teacherByWeek_fields['the_day_after'] = 'Y';
              }
          }

          if($data['type'] == 'teacher_car'){
              if(isset($data['self']) && $data['self'] == 'Y'){
                  $teacherByWeek_fields['drive_by_self'] = 'Y';
              }
              if(isset($data['come_by_self']) && $data['come_by_self'] == 'Y'){
                  $teacherByWeek_fields['come_by_self'] = 'Y';
              }
              if(isset($data['go_by_self']) && $data['go_by_self'] == 'Y'){
                  $teacherByWeek_fields['go_by_self'] = 'Y';
              }
          }

          Teacher_by_week::create($teacherByWeek_fields);

          $sql = DB::getQueryLog();
          if(checkNeedModifyLog('teacher_related')){
              $nowdata = Teacher_by_week::where('class_week_id', $teacherByWeek_fields['class_week_id'])->where('t09tb_id', $queryData['t09tb_id'])->where('idno', $idno_data['idno'])->get()->toarray();
              createModifyLog('I','Teacher_by_week','',$nowdata,end($sql));
          }

        }else{

          if($TeacherByWeek_data['confirm2'] == 'Y'){
              return back()->with('result', '0')->with('html_message', "講座接待管理已確認，不可修改!");
          }

          $data_update = 'N';

          if($data['type'] == 'teacher_room'){
              if(isset($data['before'])){
                  if($TeacherByWeek_data['the_day_before'] != 'Y'){
                      $teacherByWeek_fields['the_day_before'] = 'Y';
                      $data_update = 'Y';
                  }
              }else{
                  if($TeacherByWeek_data['the_day_before'] != 'N'){
                      $teacherByWeek_fields['the_day_before'] = 'N';
                      $data_update = 'Y';
                  }
              }
              if(isset($data['after'])){
                  if($TeacherByWeek_data['the_day_after'] != 'Y'){
                      $teacherByWeek_fields['the_day_after'] = 'Y';
                      $data_update = 'Y';
                  }
              }else{
                  if($TeacherByWeek_data['the_day_after'] != 'N'){
                      $teacherByWeek_fields['the_day_after'] = 'N';
                      $data_update = 'Y';
                  }
              }

          }

          if($data['type'] == 'teacher_car'){
              if(isset($data['self'])){
                  if($TeacherByWeek_data['drive_by_self'] != 'Y'){
                      $teacherByWeek_fields['drive_by_self'] = 'Y';
                      $data_update = 'Y';
                  }
              }else{
                  if($TeacherByWeek_data['drive_by_self'] != 'N'){
                      $teacherByWeek_fields['drive_by_self'] = 'N';
                      $data_update = 'Y';
                  }
              }
              if(isset($data['come_by_self'])){
                  if($TeacherByWeek_data['come_by_self'] != 'Y'){
                      $teacherByWeek_fields['come_by_self'] = 'Y';
                      $data_update = 'Y';
                  }
              }else{
                  if($TeacherByWeek_data['come_by_self'] != 'N'){
                      $teacherByWeek_fields['come_by_self'] = 'N';
                      $data_update = 'Y';
                  }
              }
              if(isset($data['go_by_self'])){
                  if($TeacherByWeek_data['go_by_self'] != 'Y'){
                      $teacherByWeek_fields['go_by_self'] = 'Y';
                      $data_update = 'Y';
                  }
              }else{
                  if($TeacherByWeek_data['go_by_self'] != 'N'){
                      $teacherByWeek_fields['go_by_self'] = 'N';
                      $data_update = 'Y';
                  }
              }
          }

          if($data['type'] == 'teacher_other'){
              if(isset($data['demand'])){
                  $teacherByWeek_fields['demand'] = $data['demand'];
                  $data_update = 'Y';
              }
          }

          if($data_update == 'Y'){

            if(checkNeedModifyLog('teacher_related')){
                $olddata = Teacher_by_week::where('id', $TeacherByWeek_data['id'])->get()->toarray();
            }

            Teacher_by_week::where('id', $TeacherByWeek_data['id'])->update($teacherByWeek_fields);

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('teacher_related')){
                $nowdata = Teacher_by_week::where('id', $TeacherByWeek_data['id'])->get()->toarray();
                createModifyLog('U','Teacher_by_week',$olddata,$nowdata,end($sql));
            }

          }
        }

        if($data['type'] == 'teacher_room'){
          $queryData['confirm_date'] = array();
          if(isset($data['confirm_date'])){
            $queryData['confirm_date'] = $data['confirm_date'];
          }
          $this->teacher_relatedService->change_room($queryData);
          $teachDate = $this->teacher_relatedService->getTeachDate($queryData);
          // dd($data['confirm_date']);

          if(checkNeedModifyLog('teacher_related')){
              $olddata = Teacher_room::where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $idno_data['idno'])->get()->toarray();
          }

          Teacher_room::where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $idno_data['idno'])->delete();

          $sql = DB::getQueryLog();
          if(checkNeedModifyLog('teacher_related')){
              createModifyLog('D','Teacher_room',$olddata,'',end($sql));
          }

          if(isset($data['confirm_date'])){
            foreach($data['confirm_date'] as $row){
              $fields = array();
              $fields['date'] = $row;
              $fields['confirm'] = 'Y';
              $fields['class_weeks_id'] = $queryData['class_weeks_id'];
              $fields['idno'] = $idno_data['idno'];

              $tomorrow = $row;
              $tomorrow_day = date('Ymd',strtotime('+1 day', strtotime(($tomorrow+19110000))))-19110000;

              // 前一天晚上有住隔天早上也要住
              if(in_array($tomorrow_day, $data['confirm_date'])){
                if(!in_array('morning', $data[$tomorrow_day]) && in_array('evening', $data[$row])){
                  array_push($data[$tomorrow_day], 'morning');
                }
              }else{
                if(in_array('evening', $data[$row])){
                  $fields2 = array(
                    'date' => $tomorrow_day,
                    'confirm' => 'Y',
                    'class_weeks_id' => $queryData['class_weeks_id'],
                    'idno' => $idno_data['idno'],
                    'morning' => 'Y',
                    'only_morning' => 'Y',
                  );
                  Teacher_room::create($fields2);

                  $sql = DB::getQueryLog();
                  if(checkNeedModifyLog('teacher_related')){
                      $nowdata = Teacher_room::where('class_weeks_id', $queryData['class_weeks_id'])->where('date', $tomorrow_day)->where('idno', $idno_data['idno'])->get()->toarray();
                      createModifyLog('I','Teacher_by_week','',$nowdata,end($sql));
                  }

                }
              }

              if(isset($data[$row])){
                foreach($data[$row] as $key => $room_period){
                  $fields[$room_period] = 'Y';
                }
              }

              if(isset($data[$row])){
                if(!in_array('evening', $data[$row])){
                  $fields['only_morning'] = 'Y';
                }
                if($teachDate['after_day'] == $row && isset($fields['evening'])){

                  if(checkNeedModifyLog('teacher_related')){
                      $olddata = Teacher_by_week::where('id', $TeacherByWeek_data['id'])->get()->toarray();
                  }

                  $teacherByWeek_fields['the_day_after'] = 'Y';
                  Teacher_by_week::where('id', $TeacherByWeek_data['id'])->update($teacherByWeek_fields);

                  $sql = DB::getQueryLog();
                  if(checkNeedModifyLog('teacher_related')){
                      $nowdata = Teacher_by_week::where('id', $TeacherByWeek_data['id'])->get()->toarray();
                      createModifyLog('U','Teacher_by_week',$olddata,$nowdata,end($sql));
                  }

                }
                if($teachDate['after_day'] == $row && !isset($fields['evening'])){

                  if(checkNeedModifyLog('teacher_related')){
                      $olddata = Teacher_by_week::where('id', $TeacherByWeek_data['id'])->get()->toarray();
                  }

                  $teacherByWeek_fields['the_day_after'] = 'N';
                  Teacher_by_week::where('id', $TeacherByWeek_data['id'])->update($teacherByWeek_fields);

                  $sql = DB::getQueryLog();
                  if(checkNeedModifyLog('teacher_related')){
                      $nowdata = Teacher_by_week::where('id', $TeacherByWeek_data['id'])->get()->toarray();
                      createModifyLog('U','Teacher_by_week',$olddata,$nowdata,end($sql));
                  }

                }
                Teacher_room::create($fields);

                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('teacher_related')){
                    $nowdata = Teacher_room::where('class_weeks_id', $queryData['class_weeks_id'])->where('date', $row)->where('idno', $idno_data['idno'])->get()->toarray();
                    createModifyLog('I','Teacher_room','',$nowdata,end($sql));
                }

              }

            }

          }

          // dd($data['confirm_date']);
          return back()->with('result', '1')->with('message', '儲存成功!');
        }


        if($data['type'] == 'teacher_car'){

          if(checkNeedModifyLog('teacher_related')){
              $olddata = Teacher_car::where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $idno_data['idno'])->get()->toarray();
          }

        	Teacher_car::where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $idno_data['idno'])->delete();

          $sql = DB::getQueryLog();
          if(checkNeedModifyLog('teacher_related')){
              createModifyLog('D','Teacher_car',$olddata,'',end($sql));
          }

        	if(!isset($data['self'])){
            if(!isset($data['come_by_self'])){
                $location2_1 = Car_fare::select('area', 'fare')->where('id', $data['location2_1'])->get();
                $price = '0';
                if(!empty($location2_1)){
                  $price = $location2_1[0]->fare;
                }
                $car_type1 = array(
                  'type' => '1',
                  'class_weeks_id' => $queryData['class_weeks_id'],
                  'idno' => $idno_data['idno'],
                  'time' => $data['time1'],
                  'date' => $data['date1'],
                  'location1' => $data['location1_1'],
                  'location2' => $data['location2_1'],
                  'location3' => $data['location3_1'],
                  'address' => $data['address_1'],
                  'start' => config('app.county')[$data['location1_1']].$location2_1[0]->area.$data['address_1'],
                  'end' => config('app.school_location')[$data['location3_1']],
                  'price' => $price,
                );
                if(!empty($data['date1'])){
                  Teacher_car::create($car_type1);

                  $sql = DB::getQueryLog();
                  if(checkNeedModifyLog('teacher_related')){
                      $nowdata = Teacher_car::where('class_weeks_id', $queryData['class_weeks_id'])->where('type', '1')->where('date', $data['date1'])->where('idno', $idno_data['idno'])->get()->toarray();
                      createModifyLog('I','Teacher_car','',$nowdata,end($sql));
                  }

                }
            }
            if(!isset($data['go_by_self'])){
                $location2_2 = Car_fare::select('area', 'fare')->where('id', $data['location2_2'])->get();
                $price = '0';
                if(!empty($location2_2)){
                  $price = $location2_2[0]->fare;
                }
                $car_type2 = array(
                  'type' => '2',
                  'class_weeks_id' => $queryData['class_weeks_id'],
                  'idno' => $idno_data['idno'],
                  'time' => $data['time2'],
                  'date' => $data['date2'],
                  'location1' => $data['location1_2'],
                  'location2' => $data['location2_2'],
                  'location3' => $data['location3_2'],
                  'address' => $data['address_2'],
                  'end' => config('app.county')[$data['location1_2']].$location2_2[0]->area.$data['address_2'],
                  'start' => config('app.school_location')[$data['location3_2']],
                  'price' => $price,
                );
                if(!empty($data['date2'])){
                  Teacher_car::create($car_type2);

                  $sql = DB::getQueryLog();
                  if(checkNeedModifyLog('teacher_related')){
                      $nowdata = Teacher_car::where('class_weeks_id', $queryData['class_weeks_id'])->where('type', '2')->where('date', $data['date2'])->where('idno', $idno_data['idno'])->get()->toarray();
                      createModifyLog('I','Teacher_car','',$nowdata,end($sql));
                  }

                }
            }

        	}
        	return back()->with('result', '1')->with('message', '儲存成功!');
        }

        if($data['type'] == 'teacher_food'){
          $ClassWeek_data = $this->teacher_relatedService->getClassWeek($queryData);
          if(!empty($ClassWeek_data)){
            $queryData['class'] = $ClassWeek_data['class'];
            $queryData['term'] = $ClassWeek_data['term'];
            $queryData['sdate'] = $ClassWeek_data['sdate'];
            $queryData['edate'] = $ClassWeek_data['edate'];
          }
          $queryData['idno'] = $idno_data['idno'];
          $FoodDate = $this->teacher_relatedService->getFoodDate($queryData);

          $FoodDate_key = array_keys($FoodDate);

          if(checkNeedModifyLog('teacher_related')){
              $olddata = Teacher_food::where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $idno_data['idno'])->get()->toarray();
          }

          Teacher_food::where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $idno_data['idno'])->delete();

          $sql = DB::getQueryLog();
          if(checkNeedModifyLog('teacher_related')){
              createModifyLog('D','Teacher_food',$olddata,'',end($sql));
          }

          unset($data['_method']);
          unset($data['_token']);
          unset($data['type']);
          $FoodDate_array = array();
          foreach($FoodDate_key as $FoodDate_key_row){
            foreach($data as $data_key => $data_row){
              if(!empty($data_row)){
                $FoodDate_array = explode("_",$data_key);
              }
              if(!empty($FoodDate_array) && $FoodDate_array[0] == $FoodDate_key_row){
                if($FoodDate_array[1] == 'breakfast'){
                  $FoodDate[$FoodDate_key_row]['breakfast'] = $data[$FoodDate_key_row.'_'.$FoodDate_array[1]];
                }
                if($FoodDate_array[1] == 'breakfasttype'){
                  $FoodDate[$FoodDate_key_row]['breakfast_type'] = $data[$FoodDate_key_row.'_'.$FoodDate_array[1]];
                }
                if($FoodDate_array[1] == 'breakfasttype2'){
                  $FoodDate[$FoodDate_key_row]['breakfast_type2'] = $data[$FoodDate_key_row.'_'.$FoodDate_array[1]];
                }
                if($FoodDate_array[1] == 'lunch'){
                  $FoodDate[$FoodDate_key_row]['lunch'] = $data[$FoodDate_key_row.'_'.$FoodDate_array[1]];
                }
                if($FoodDate_array[1] == 'lunchtype'){
                  $FoodDate[$FoodDate_key_row]['lunch_type'] = $data[$FoodDate_key_row.'_'.$FoodDate_array[1]];
                }
                if($FoodDate_array[1] == 'lunchtype2'){
                  $FoodDate[$FoodDate_key_row]['lunch_type2'] = $data[$FoodDate_key_row.'_'.$FoodDate_array[1]];
                }
                if($FoodDate_array[1] == 'dinner'){
                  $FoodDate[$FoodDate_key_row]['dinner'] = $data[$FoodDate_key_row.'_'.$FoodDate_array[1]];
                }
                if($FoodDate_array[1] == 'dinnertype'){
                  $FoodDate[$FoodDate_key_row]['dinner_type'] = $data[$FoodDate_key_row.'_'.$FoodDate_array[1]];
                }
                if($FoodDate_array[1] == 'dinnertype2'){
                  $FoodDate[$FoodDate_key_row]['dinner_type2'] = $data[$FoodDate_key_row.'_'.$FoodDate_array[1]];
                }

              }

            }

            $fields = $FoodDate[$FoodDate_key_row];
            $fields['idno'] = $idno_data['idno'];
            $fields['class_weeks_id'] = $queryData['class_weeks_id'];
            $fields['date'] = $FoodDate_key_row;

            if(!isset($data[$FoodDate_key_row.'_breakfast'])){
              $fields['breakfast'] = 'N';
              $fields['breakfast_type'] = '0';
              $fields['breakfast_type2'] = '0';
            }
            if(!isset($data[$FoodDate_key_row.'_lunch'])){
              $fields['lunch'] = 'N';
              $fields['lunch_type'] = '0';
              $fields['lunch_type2'] = '0';
            }
            if(!isset($data[$FoodDate_key_row.'_dinner'])){
              $fields['dinner'] = 'N';
              $fields['dinner_type'] = '0';
              $fields['dinner_type2'] = '0';
            }
            $food_field = array('breakfast', 'lunch', 'dinner');
            foreach($food_field as $food_field_row){
              if($fields[$food_field_row] == 'Y' && $fields[$food_field_row.'_type'] == '0' ){
                $fields[$food_field_row.'_type'] = '1';
              }
              if($fields[$food_field_row] == 'Y' && $fields[$food_field_row.'_type2'] == '0' ){
                $fields[$food_field_row.'_type2'] = '1';
              }
            }
            unset($fields['week_day']);
            // echo '<pre style="text-align:left;">' . "\n";
            // print_r($fields);
            // echo "\n</pre>\n";

            Teacher_food::create($fields);

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('teacher_related')){
                $nowdata = Teacher_food::where('class_weeks_id', $queryData['class_weeks_id'])->where('date', $FoodDate_key_row)->where('idno', $idno_data['idno'])->get()->toarray();
                createModifyLog('I','Teacher_food','',$nowdata,end($sql));
            }

          }
          // dd('11');
          return back()->with('result', '1')->with('message', '儲存成功!');
        }


        if($data['type'] == 'teacher_other'){
          return back()->with('result', '1')->with('message', '儲存成功!');
        }



        // dd($fields);


    }

    function getLocation(Request $request)
    {

        $county = $request->input('county');

        $selected = $request->input('selected')!=''?$request->input('selected'):'';

        $data = Car_fare::select('id', 'area')->where('county', $county)->get();

        $result = '';

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($request->input('selected'));
        // echo "\n</pre>\n";
        // die();

        foreach ($data as $va) {
            $result .= '<option value="'.$va->id.'"';
            $result .= ($selected == $va->id)? ' selected>' : '>';
            $result .= $va->area.'</option>';
        }

        return $result;
    }


}
