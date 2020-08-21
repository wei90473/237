<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\StudentApplyService;
use App\Services\Term_processService;
use App\Services\User_groupService;
use App\Helpers\Des;
use App\Helpers\Common;
use DB;

use App\Models\T13tb;
use App\Models\M02tb;
use App\Models\S01tb;
use App\Models\M09tb;
use App\Models\T04tb;

use Excel;
use Validator;
use Auth;
use DateTime;

class StudentApplyController extends Controller
{
    /**
     * StudentApplyController constructor.
     * @param
     */
    public function __construct(StudentApplyService $studentApplyService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        setProgid('student_apply');
        $this->studentApplyService = $studentApplyService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_apply', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });

    }

    public function classList(Request $request)
    {

        $queryData = $request->only([
            't01tb.yerly',                // 年度
            't01tb.class',                // 班號
            't01tb.name',                 // 班級名稱
            't01tb.branchname',           // 分班名稱
            't01tb.branch',               // 辦班院區
            't01tb.process',              // 班別類型
            't01tb.commission',           // 委訓單位
            't01tb.traintype',            // 訓練性質
            't01tb.type',                 // 班別性質
            't01tb.categoryone',           // 類別1
            "t04tb.term",                  // 期別
            "t04tb.site_branch",           // 上課地點
            "t04tb.sponsor",                // 班務人員
            "t04tb.month",                 // 月份
            'sdate_start',                      // 開訓日期範圍(起)
            'sdate_end',                        // 開訓日期範圍(訖)
            'edate_start',                      // 結訓日期範圍(起)
            'edate_end',                        // 結訓日期範圍(訖)
            'training_start',                   // 在訓期間範圍(起)
            'training_end',                     // 在訓期間範圍(起)
            '_paginate_qty'
        ]);

        if (empty($queryData['t01tb']['yerly'])){
            $queryData['t01tb']['yerly'] = new DateTime();
            $queryData['t01tb']['yerly'] = $queryData['t01tb']['yerly']->format('Y') - 1911;
        }

        $s01tbM = S01tb::where('type', '=', 'M')->get()->pluck('name', 'code');
        $sponsors = M09tb::all();

        $data = [];
        if ($request->all()){
            $data = $this->studentApplyService->getOpenClassList($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData['t01tb']['class'] = $sess['class'];
                $queryData['t04tb']['term'] = $sess['term'];
                $queryData['t01tb']['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->studentApplyService->getOpenClassList($queryData);
            }
        }

        return view('admin/student_apply/class_list', compact('data', 'queryData', 'sponsors', 's01tbM'));

        // $queryData = $request->only([
        //     'yerly',                // 年度
        //     'class',                // 班號
        //     'class_name',           // 班級名稱
        //     'sub_class_name',       // 分班名稱
        //     'term',                 // 期別
        //     'class_location',       // 上課地點
        //     'branch',               // 辦班院區
        //     'process',              // 班別類型
        //     'entrust_train_unit',   // 委訓單位
        //     'worker',               // 班務人員
        //     'train_start_date',     // 開訓日期範圍(起)
        //     'train_end_date',       // 開訓日期範圍(訖)
        //     'graduate_start_date',  // 結訓日期範圍(起)
        //     'graduate_end_date',    // 結訓日期範圍(訖)
        //     '_paginate_qty',        // 分頁資料數量
        // ]);

        // $data = [];
        // if ($request->all()){
        //     $data = $this->studentApplyService->getOpenClassList($queryData); // 取得開班資料
        // }else{
        //     $sess = $request->session()->get('lock_class');
        //     if($sess){
        //       $queryData2['class'] = $sess['class'];
        //       $queryData2['term'] = $sess['term'];
        //       $queryData2['yerly'] = substr($sess['class'], 0, 3);
        //       $data = $this->studentApplyService->getOpenClassList($queryData2);
        //     }
        // }
        // return view('admin/student_apply/class_list', compact('data', 'queryData'));
    }

    public function index(Request $request, $class, $term)
    {
        $queryData = $request->only([
            'name',
            'idno',
            'status',
            'organ',
            'organ_name',
            'rank',
            'position',
            'email',
            'dept',
            'identity',
            'organ_name'
        ]);
        $t04tb_info = compact(['class', 'term']);

        $t04tb = $this->studentApplyService->getT04tb($t04tb_info);

        $t13tbs = $this->studentApplyService->getT13tbsByT04tb($t04tb_info, $queryData);

        return view('admin/student_apply/index', compact(['t04tb', 't13tbs', 'queryData']));
    }

    public function redirectCreateStudent(Request $request, $class, $term)
    {
        $this->validate($request, [
            'idno' => 'required',
            'identity' => 'required'
        ],[
            "idno.required" => "學員身分證 不可為空",
            "identity.required" => "人員身份 不可為空"
        ]);

        $des_idno = Des::encode($request->idno, 'KLKLK');

        return redirect("admin/student_apply/create/{$class}/{$term}/{$des_idno}/$request->identity");

    }

    public function create($class, $term, $des_idno, $identity)
    {
        $idno = Des::decode($des_idno, 'KLKLK');
        $t13tb_info = compact(['class', 'term', 'idno']);
        $m02tb = $this->studentApplyService->getM02tb($idno);

        if (!empty($m02tb)){
            if($m02tb->identity != $identity)
            {
                return redirect("/admin/student_apply/{$class}/{$term}")->with('result', 0)->with('message', '學員身份錯誤');
            }
        }

        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->studentApplyService->getT04tb($t04tb_info);

        $t13tb = new T13tb();
        if(empty($m02tb)){
            $t13tb->m02tb = new M02tb();
            $t13tb->m02tb->idno = $idno;
            $t13tb->m02tb->identity = $identity;
        }else{
            $t13tb->m02tb = $m02tb;
            $m02tb = collect($m02tb->toArray())->only(['organ', 'dept', 'rank', 'position', 'ecode', 'education'])->toArray();
            $t13tb->fill($m02tb);
        }

        $m13tbs = ['' => '請選擇'];
        $m13tbs += $this->studentApplyService->getM13tbs()->pluck('lname', 'organ')->toArray();

        $t13tb_fileds = config('database_fields.t13tb');

        $address = ['' =>  '請選擇'];
        $address += config('address.county_text');
        $action = "create";
        return view('admin/student_apply/form', compact(['identity', 't13tb', 't04tb', 'm13tbs', 't13tb_fileds', 'address', 'action']));
    }

    public function edit(Request $request, $class, $term, $des_idno)
    {
        $idno = Des::decode($des_idno, 'KLKLK');
        $t13tb_info = compact(['class', 'term', 'idno']);
        $t13tb = $this->studentApplyService->getT13tb($t13tb_info);
        $t04tb = $t13tb->t04tb;

        $m13tbs = ['' => '請選擇'];
        $m13tbs += $this->studentApplyService->getM13tbs()->pluck('lname', 'organ')->toArray();

        $t13tb_fileds = config('database_fields.t13tb');

        $address = ['' =>  '請選擇'];
        $address += config('address.county_text');

        $action = "edit";
        return view('admin/student_apply/form', compact(['t13tb', 't04tb', 'm13tbs', 't13tb_fileds', 'address', 'action']));
    }

    public function update(Request $request, $class, $term, $des_idno)
    {
        $t04tbKey = compact(['class', 'term']);
        $t04tb = $this->studentApplyService->getT04tb($t04tbKey);

        if (empty($t04tb)){
            return back()->with('result', 0)->with('message', '該班級不存在');
        }

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('student_apply', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $idno = Des::decode($des_idno, 'KLKLK');
        $t04tbKey = compact(['class', 'term']);
        $t13tbKey = compact(['class', 'term', 'idno']);
        $m02tb_info = compact(['idno']);

        $t13tb = $this->studentApplyService->getT13tb($t13tbKey);

        $newM02tb = $request->only([
            'm02tb.cname',        // 中文姓名
            'm02tb.ename',        // 英文姓名
            'm02tb.sex',
            'm02tb.offaddr1',
            'm02tb.offaddr2',
            'm02tb.offzip',
            'm02tb.homaddr1',
            'm02tb.homaddr2',
            'm02tb.homzip',
            'm02tb.send',
            'm02tb.offtela1',
            'm02tb.offtelb1',
            'm02tb.offtelc1',
            'm02tb.offtela2',
            'm02tb.offtelb2',
            'm02tb.email',
            'm02tb.offfaxa',
            'm02tb.offfaxb',
            'm02tb.homtela',
            'm02tb.homtelb',
            'm02tb.handicap',
            'm02tb.chief',
            'm02tb.personnel',
            'm02tb.aborigine',
            'm02tb.special_situation',
            'm02tb.birth'
            // 'm02tb.organ',
            // 'm02tb.dept',
            // 'm02tb.rank',
            // 'm02tb.position',
            // 'm02tb.ecode',
            // 'm02tb.education'
        ])['m02tb'];

        $newT13tb = $request->only([
            'no',
            'groupno',
            'offname',
            'offemail',
            'offtel',
            'dorm',
            'vegan',
            'nonlocal',
            'extradorm',
            'status',
            'not_present_notification',
            'authorize',
            'dropdate',
            'droptime',
            'organ',
            'dept',
            'rank',
            'position',
            'ecode',
            'education'
        ]);

        $newM02tb = array_merge($newM02tb, collect($newT13tb)->only([
            'organ',
            'dept',
            'rank',
            'position',
            'ecode',
            'education'
        ])->toArray());

        if ($t13tb->m02tb->identity == 2){
            $newT13tb['authorize'] = 'N';
        }
        $newT13tb = $this->studentApplyService->setT13tbDefaultValue($newT13tb, $newM02tb, $t04tb);

        DB::beginTransaction();

        try {
            $this->studentApplyService->storeM02tb($m02tb_info, $newM02tb);
            $this->studentApplyService->updateT13tb($t13tbKey, $newT13tb);
            DB::commit();
            return back()->with('result', 1)->with('message', '更新成功');
        } catch (\Exception $e) {
            DB::rollback();
            $status = false;
            return back()->with('result', 0)->with('message', '更新失敗');
            var_dump($e->getMessage());
            die;
        }


    }

    public function delete($class, $term, $des_idno)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('student_apply', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法刪除');
        }

        $idno = Des::decode($des_idno, 'KLKLK');
        $t13tb_info = compact(['class', 'term', 'idno']);
        $delete = $this->studentApplyService->deleteT13tb($t13tb_info);
        if ($delete){
            return redirect("/admin/student_apply/{$class}/{$term}")->with('result', 1)->with('message', '刪除成功');
        }else{
            return back()->with('result', 1)->with('message', '刪除失敗');
        }
    }

    /*
        換員、補報及取消報名管理
        此功能只會列出自己的班期中，尚未審核的換員、補報及取消報名申請。
    */
    public function modifyManage(Request $request)
    {
        $queryData = $request->only(['yerly', 'class', 'term', 'class_name', 'type']);

        if (empty($queryData['yerly'])){
            $queryData['yerly'] = new DateTime();
            $queryData['yerly'] = $queryData['yerly']->format('Y') - 1911;
        }

        $userid = Auth::user()->userid;
        $modifyLogs = $this->studentApplyService->getModifyLogBySponsor($userid, $queryData);
        $t04tbs = $this->studentApplyService->getT04tbBySponsor($userid);

        return view('admin/student_apply/modify_manage', compact(['modifyLogs', 't04tbs', 'queryData']));
    }

    public function arrangeStNo($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t13tbs = $this->studentApplyService->getT13tbsByT04tb($t04tb_info);
        return view('admin/student_apply/arrange_stno',compact(['t13tbs', 't04tb_info']));
    }

    public function arrangeGroup($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $order_by = [
            ['no', 'asc'],
            ['groupno', 'asc']
        ];
        $t13tbs = $this->studentApplyService->getT13tbsByT04tb($t04tb_info, [], $order_by);
        // dd($t13tbs);
        return view('admin/student_apply/arrange_group',compact(['t13tbs', 't04tb_info']));
    }

    public function autoArrangeGroup(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('arrange_group', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $t04tb_info = compact(['class', 'term']);

        if ($request->btn == 'diff'){
            if (empty($request->condition)){
                return back()->with('result', 0)->with('message', '分配失敗，請選擇分配條件');
            }
            $arrange_group = $this->studentApplyService->diffArrangeGroup($t04tb_info, $request->condition);

        }elseif ($request->btn == 'random'){
            if (empty($request->group_num)){
                return back()->with('result', 0)->with('message', '分配失敗，請輸入組別數量');
            }

            $arrange_group = $this->studentApplyService->randomArrangeGroup($t04tb_info, $request->group_num);
        }else{
            return back()->with('result', 0)->with('message', '參數有誤');
        }

        if ($arrange_group){
            return back()->with('result', 1)->with('message', '分配成功');
        }else{
            return back()->with('result', 0)->with('message', '分配失敗');
        }


    }

    public function autoArrangeStNo($class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('arrange_stno', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $t04tb_info = compact(['class', 'term']);
        $arrange_stno = $this->studentApplyService->arrangeStNo($t04tb_info);

        if ($arrange_stno){
            return back()->with('result', 1)->with('message', '編排成功');
        }else{
            return back()->with('result', 0)->with('message', '編排失敗');
        }

    }

    public function stno_edit(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('arrange_stno', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $t04tb_info = compact(['class', 'term']);
        $store_result = $this->studentApplyService->storeT13tbStno($t04tb_info, $request->stno);

        if ($store_result['status']){
            return back()->with('result', 1)->with('message', '儲存成功');
        }else{
            return back()->withInput()->with('result', 0)->with('message', $store_result['message']);
        }

    }

    public function group_edit(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('arrange_group', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $t04tb_info = compact(['class', 'term']);
        $store_result = $this->studentApplyService->storeT13tbGroup($t04tb_info, $request->groups);

        if ($store_result['status']){
            return back()->with('result', 1)->with('message', '儲存成功');
        }else{
            return back()->withInput()->with('result', 0)->with('message', '儲存失敗');
        }
    }

    public function redirectChangeStudent(Request $request, $class, $term)
    {
        $old_des_idno = Des::encode($request->old_student, 'KLKLK');
        $new_des_idno = Des::encode($request->new_student, 'KLKLK');
        return redirect("/admin/student_apply/changeStudent/{$class}/{$term}/{$old_des_idno}/{$new_des_idno}");
    }

    public function store(Request $request, $class, $term, $identity)
    {
        $t04tbKey = compact(['class', 'term']);
        $t04tb = $this->studentApplyService->getT04tb($t04tbKey);

        if (empty($t04tb)){
            return back()->with('result', 0)->with('message', '該班級不存在');
        }

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('student_apply', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法新增');
        }

        $idno = $request->m02tb['idno'];

        $t13tbKey = compact(['class', 'term', 'idno']);
        $t13tb = $this->studentApplyService->getT13tb($t13tbKey);

        if (!empty($t13tb)){
            return redirect("/admin/student_apply/{$class}/{$term}")->with('result', 1)->with('message', '報名資料已存在');
        }

        $m02tb_info = compact(['idno']);
        $newM02tb = $request->only([
            'm02tb.cname',        // 中文姓名
            'm02tb.ename',        // 英文姓名
            'm02tb.sex',
            'm02tb.offaddr1',
            'm02tb.offaddr2',
            'm02tb.offzip',
            'm02tb.homaddr1',
            'm02tb.homaddr2',
            'm02tb.homzip',
            'm02tb.send',
            'm02tb.offtela1',
            'm02tb.offtelb1',
            'm02tb.offtelc1',
            'm02tb.offtela2',
            'm02tb.offtelb2',
            'm02tb.email',
            'm02tb.offfaxa',
            'm02tb.offfaxb',
            'm02tb.homtela',
            'm02tb.homtelb',
            'm02tb.handicap',
            'm02tb.chief',
            'm02tb.personnel',
            'm02tb.aborigine',
            'm02tb.birth'
        ])['m02tb'];

        $newM02tb['identity'] = $identity;

        $newT13tb = $request->only([
            'no',
            'groupno',
            'offname',
            'offemail',
            'offtel',
            'dorm',         // 住宿
            'vegan',        // 素食
            'nonlocal',     // 遠道者
            'extradorm',    // 提前住宿
            'status',
            'organ',
            'dept',
            'rank',
            'position',
            'ecode',
            'education'
        ]);

        $newM02tb = array_merge($newM02tb, collect($newT13tb)->only(['organ', 'dept', 'rank', 'position', 'ecode', 'education'])->toArray());
        $newT13tb = $this->studentApplyService->setT13tbDefaultValue($newT13tb, $newM02tb, $t04tb);

        if ($identity == 2){
            $newT13tb['authorize'] = 'N';
        }

        DB::beginTransaction();

        try {

            $this->studentApplyService->insertT13tb($t13tbKey, $newT13tb);
            $this->studentApplyService->storeM02tb($m02tb_info, $newM02tb);
            $des_idno = Des::encode($request->m02tb['idno'], 'KLKLK');
            DB::commit();
            return redirect("admin/student_apply/edit/{$class}/{$term}/{$des_idno}")->with('result', 1)->with('message', '新增成功');
        } catch (\Exception $e) {
            DB::rollback();
            $status = false;
            // return back()->with('result', 0)->with('message', '更新失敗');
            var_dump($e->getMessage());
            die;
        }


    }

    public function changeStudent($class, $term, $des_idno, $new_des_idno)
    {
        $idno = Des::decode($des_idno, 'KLKLK');
        $new_idno = Des::decode($new_des_idno, 'KLKLK');
        $student = $this->studentApplyService->getM02tb($idno);
        $new_student = $this->studentApplyService->getM02tb($new_idno);

        $t04tb_info = compact(['class', 'term']);

        $check_student = $this->studentApplyService->checkChangeStudent($t04tb_info, $new_idno, $idno);

        if ($check_student['status'] === false){
            return redirect("admin/student_apply/edit/{$class}/{$term}/{$des_idno}")->with('result', 0)->with('message', $check_student['message']);
        }

        $t04tb = $this->studentApplyService->getT04tb($t04tb_info);

        $m13tbs = ['' => '請選擇'];
        $m13tbs += $this->studentApplyService->getM13tbs()->pluck('lname', 'organ')->toArray();

        $t13tb_fileds = config('database_fields.t13tb');

        $address = ['' =>  '請選擇'];
        $address += config('address.county_text');

        $t13tb = new T13tb();
        $t13tb->identity = $t13tb->identity;

        if (empty($new_student)){
            $t13tb->m02tb = new M02tb();
            $t13tb->m02tb->idno = $new_idno;
            $t13tb->m02tb->identity = $student->identity;
        }else{
            $t13tb->m02tb = $new_student;
            $t13tb->fill(collect($new_student->toArray())->only(['organ', 'dept', 'rank', 'position', 'ecode', 'education'])->toArray());
        }

        $t13tb->no = $check_student['t13tb']->no;

        $action = "change_student";
        return view("admin/student_apply/form", compact(['action', 'des_idno', 'student', 't13tb', 'address', 't13tb_fileds' , 'm13tbs', 't04tb']));
    }

    public function storeForChangeStudent(Request $request, $class, $term, $des_idno)
    {

        $t04tbKey = compact(['class', 'term']);
        $t04tb = $this->studentApplyService->getT04tb($t04tbKey);

        if (empty($t04tb)){
            return back()->with('result', 0)->with('message', '該班級不存在');
        }


        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('student_apply', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $idno = Des::decode($des_idno, 'KLKLK');
        $new_idno = $request->m02tb['idno'];

        $t04tb_info = compact(['class', 'term']);
        $check_student = $this->studentApplyService->checkChangeStudent($t04tb_info, $new_idno, $idno);

        if ($check_student['status'] === false){
            return redirect("student_apply/edit/{$class}/{$term}/{$des_idno}")->with('result', 0)->with('message', $check_student['message']);
        }

        $newT13tb = $request->only([
            'groupno',
            'offname',
            'offemail',
            'offtel',
            'dorm',
            'vegan',
            'nonlocal',
            'extradorm',
            'status'
        ]);

        $newT13tb['idno'] = $new_idno;
        $newM02tb = $request->only([
            'm02tb.cname',        // 中文姓名
            'm02tb.ename',        // 英文姓名
            'm02tb.sex',
            'm02tb.offaddr1',
            'm02tb.offaddr2',
            'm02tb.offzip',
            'm02tb.homaddr1',
            'm02tb.homaddr2',
            'm02tb.homzip',
            'm02tb.send',
            'm02tb.offtela1',
            'm02tb.offtelb1',
            'm02tb.offtelc1',
            'm02tb.offtela2',
            'm02tb.offtelb2',
            'm02tb.email',
            'm02tb.offfaxa',
            'm02tb.offfaxb',
            'm02tb.homtela',
            'm02tb.homtelb',
            'm02tb.handicap',
            'm02tb.chief',
            'm02tb.personnel',
            'm02tb.aborigine',
            'm02tb.idno',
            'm02tb.organ',
            'm02tb.dept',
            'm02tb.rank',
            'm02tb.position',
            'm02tb.ecode',
            'm02tb.education',
            'm02tb.birth'
        ])['m02tb'];

        $newM02tb['identity'] = $check_student['t13tb']->m02tb->identity;

        $newT13tb = array_merge($newT13tb, collect($newM02tb)->only([
            'organ',
            'dept',
            'rank',
            'position',
            'ecode',
            'education',
        ])->toArray());

        $t13tb_info = $t04tb_info;
        $t13tb_info['idno'] = $idno;

        $m02tb_info = ['idno' => $new_idno];

        $des_idno = Des::encode($request->m02tb['idno'], 'KLKLK');

        $newT13tb = $this->studentApplyService->setT13tbDefaultValue($newT13tb, $newM02tb, $t04tb);

        DB::beginTransaction();

        try {
            $this->studentApplyService->storeM02tb($m02tb_info, $newM02tb);
            $this->studentApplyService->changeStudent($t13tb_info, $newT13tb);
            $this->studentApplyService->insertApplyModifyLogForAmdin($t04tb_info, 1, [
                                                                                        'idno' => $idno,
                                                                                        'new_idno' => $new_idno,
                                                                                        'student_dept' => $check_student['t13tb']->dept,
                                                                                        'new_student_dept' => $newT13tb['dept']
                                                                                    ]);
            DB::commit();

            return redirect("/admin/student_apply/edit/{$class}/{$term}/{$des_idno}")->with('result', 1)->with('message', '更新成功');
        } catch (\Exception $e) {
            DB::rollback();
            $status = false;
            // return back()->with('result', 0)->with('message', '更新失敗');
            var_dump($e->getMessage());
            die;
        }

    }

    public function changeTerm(Request $request, $class, $term, $des_idno)
    {

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('student_apply', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $this->validate($request, [
            'new_term' => 'required'
        ],[
            "new_term.required" => "期別 欄位不可為空",
        ]);

        $t04tb_info = compact(['class', 'term']);

        $idno = Des::decode($des_idno, 'KLKLK');

        $newTerm = $request->new_term;
        $t13tb_info = compact(['class', 'term', 'idno']);
        $t13tb_info['term'] = $newTerm;
        $new_t13tb = $this->studentApplyService->getT13tb($t13tb_info);

        if (!empty($new_t13tb)){
            return redirect("/admin/student_apply/edit/{$class}/{$term}/{$des_idno}")->with('result', 0)->with('message', '換期失敗，新期別已報名');
        }

        $t13tb_info = compact(['class', 'term', 'idno']);
        $t13tb = $this->studentApplyService->getT13tb($t13tb_info);

        if (empty($t13tb)){
            return redirect("/admin/student_apply/edit/{$class}/{$term}/{$des_idno}")->with('result', 0)->with('message', '報名資料不存在');
        }

        DB::beginTransaction();
        try {
            $store = $this->studentApplyService->changeTerm($t13tb_info, $newTerm);

            if ($store){
                $this->studentApplyService->insertApplyModifyLogForAmdin($t04tb_info, 2, [
                    'idno' => $idno,
                    'term' => $t13tb->term,
                    'student_dept' => $t13tb->dept,
                    'new_term' => $newTerm,
                ]);
            }

            DB::commit();

            return redirect("/admin/student_apply/edit/{$class}/{$newTerm}/{$des_idno}")->with('result', 1)->with('message', '換期成功');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect("/admin/student_apply/edit/{$class}/{$term}/{$des_idno}")->with('result', 0)->with('message', '換期失敗');

            var_dump($e->getMessage());
            die;
        }
    }

    public function modifylogForAdmin($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->studentApplyService->getT04tbAndModifyinfo($t04tb_info);

        return view("admin/student_apply/modify_logs", compact(['t04tb']));
    }

    public function publishStudentList(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('student_apply', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $this->validate($request, [
            'publish' => 'required'
        ],[
            "required" => "非法操作",
        ]);

        $t04tb_info = compact(['class', 'term']);
        $update = $this->studentApplyService->updateT04tbPublish($t04tb_info, $request->publish);

        if ($update){
            return back()->with('result', 1)->with('message', '改變公告狀態成功');
        }else{
            return back()->with('result', 0)->with('message', '改變公告狀態失敗');
        }
    }

    public function importStudent(Request $request, $class, $term)
    {

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('student_apply', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法匯入');
        }

        $this->validate($request, [
            'import_file' => 'required',
            'import_identity' => 'required'
        ],[
            "import_file.required" => "請選擇一個檔案",
            'import_identity.required' => "請選擇人員身份"
        ]);

        $t04tb_info = compact(['class', 'term']);
        $version = ($request->import_identity == 2) ? 'full' : $request->import_version;

        $path = $request->file('import_file')->getRealPath();
        $apply_datas = Excel::load($path)->sheet(0)->toArray();

        $apply_datas = $this->studentApplyService->importFileTransFormat($apply_datas, $request->import_identity, $version);

        $errors = $this->studentApplyService->validateImport($apply_datas, $request->import_identity, $version);

        if (!empty($errors)){
            return back()->with('result', 0)->with('html_message', $errors);
        }

        $apply_datas = $this->studentApplyService->splitApplyData($apply_datas, $request->import_identity, $version);
        $import = $this->studentApplyService->importApplyData($t04tb_info, $apply_datas, $version);

        if ($import){
            return back()->with('message', '匯入成功')->with('result', 1);
        }else{
            return back()->with('message', '匯入失敗')->with('result', 0);
        }

    }

    public function checkExsitT13tb($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $exsit = count($this->studentApplyService->getT13tbsByT04tb($t04tb_info)) > 0;
        return response()->json(['exsit' => $exsit]);
    }

    public function stopChange(Request $request)
    {
        $this->validate($request, [
            'class' => 'required',
            'term' => 'required',
            'isStopChange' => 'required|in:Y,N'
        ]);

        $t04tbKey = $request->only(['class', 'term']);

        $this->studentApplyService->stopChange($t04tbKey, $request->isStopChange);
    }

    public function reviewModify(Request $request)
    {
        $this->validate($request, [
            'status' => 'required'
        ]);

        $status = collect($request->status);
        $result = $this->studentApplyService->reviewModify($status);

        if ($result){
            return back()->with('result', 1)->with('message', '審核成功');
        }else{
            return back()->with('result', 0)->with('message', '審核失敗');
        }
    }
}