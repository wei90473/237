<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\StudentService;
use App\Services\User_groupService;
use App\Helpers\Des;
use DB;


/*
    學員基本資料
*/
class StudentController extends Controller
{
    /**
     * StudentController constructor.
     * @param
     */
    public function __construct(StudentService $studentService, User_groupService $user_groupService)
    {
        setProgid('student');
        $this->studentService = $studentService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    public function index(Request $request)
    {
        $queryData = $request->only([
            'idno', 'cname', 'enrollid','position', 'rank', 'email',
            'chief', 'personnel', 'aborigine', 'handicap', 'special_situation', 'identity', 'enrollid'
        ]);

        $students = [];

        if (!empty($request->all())){
            $students = $this->studentService->getStudents($queryData);
        }

        $m02tb_fields = config('database_fields')['m02tb'];
        return view('admin/student/index', compact(['students', 'm02tb_fields', 'queryData']));
    }

    public function edit($des_idno)
    {

        $idno = des::decode($des_idno, 'KLKLKL');
        $student = $this->studentService->getStudent($idno);

        if (empty($student)){
            return back()->with('result', 1)->with('message', '找不到該學員');
        }

        $m13tbs = $this->studentService->getM13tbs()->pluck('lname', 'organ');

        if (!empty($student->m22tb)){
            if (!empty($student->birth)){
                // $studentPswIsDefault = \Hash::check($student->birth, $student->student_account->userpsw);
                if(md5($student->birth) == $student->m22tb->userpsw){
                    $studentPswIsDefault = true;
                }else{
                    $studentPswIsDefault = false;
                }
            }else{
                $studentPswIsDefault = false;
            }
        }else{
            $studentPswIsDefault = false;
        }

        if (!empty($student->m21tb)){
            // $m21tbPswIsDefault = \Hash::check('csdi1234', $student->m21tb->userpsw);
            if(md5('csdi1234') == $student->m21tb->userpsw){
                    $m21tbPswIsDefault = true;
                }else{
                    $m21tbPswIsDefault = false;
                }
        }else{
            $m21tbPswIsDefault = false;
        }
        
        $m02tb_fields = config('database_fields')['m02tb'];
        return view('admin/student/form', compact(['student', 'm02tb_fields', 'm13tbs', 'studentPswIsDefault', 'm21tbPswIsDefault']));
    }

    public function update(Request $request, $des_idno)
    {
        $idno = des::decode($des_idno, 'KLKLKL');

        $student = $this->studentService->getStudent($idno);
        if (isset($student) && $student->identity == 1){
            $this->validate($request, [
                'cname' => 'required',
                'rank' => 'required',
                'ecode' => 'required',
                'organ' => 'required'
            ]);
        }elseif (isset($student) && $student->identity == 2){
            $this->validate($request, [
                'cname' => 'required'
            ]);            
        }else{
            return back()->with('result', 1)->with('message', '找不到該學員');
        }

        $newStudent = $request->only(['m22tb.usertype1', 'm22tb.usertype2', 'm22tb.usertype3', 'm22tb.selfid', 'm22tb.userpsw', 'm22tb.status', 'm22tb.selfid', 'm22tb.userpsw', 'm22tb.status', 'm21tb.enrollorg', 'm21tb.selfid', 'm21tb.userpsw', 'm21tb.status']);

        $newStudent['m02tb'] = $request->only(['identity', 'cname', 'ename', 'organ', 'enrollid', 'rank', 'position', 'ecode', 'education', 'offaddr1', 'offaddr2', 'homaddr1', 'homaddr2', 'sex', 'offtela1', 'offtelb1', 'offtela2', 'offtelb2', 'email', 'homtela', 'homtelb', 'mobiltel', 'personnel', 'chief', 'personnel', 'aborigine', 'handicap', 'send', 'special_situation', 'offzip', 'homzip']);

        // if (empty($student->m22tb) && isset($newStudent['m22tb']['usertype1'])){
        //     $this->validate($request, ['m22tb.userpsw' => 'required'], ['student_account.userpsw.required' => '請輸入個人帳號密碼']);
        // }

        // if (empty($student->m21tb) && isset($request->is_worker)){
        //     $this->validate($request, ['m21tb.userpsw' => 'required'], ['student_account.userpsw.required' => '請輸入訓練持承辦人密碼']);
        // }

        DB::beginTransaction();

        try {
            $this->studentService->storeM02tb($idno, $newStudent['m02tb']);
            $m22tbKey = ['userid' => $idno];
            $this->studentService->storeM22tb($m22tbKey, $newStudent['m22tb']);

            // $m22tbKey = $m21tbKey;
            if ($request->is_worker == "Y"){
                $this->studentService->storeM21tb($m22tbKey, $newStudent['m21tb']);
            }

            DB::commit();

            return back()->with('result', 1)->with('message', '儲存成功');
        } catch (\Exception $e) {
            DB::rollback();
            $status = false;
            // return back()->with('result', 0)->with('message', '更新失敗');
            var_dump($e->getMessage());
            die;
        }

    }

    public function resetPassword(Request $request, $des_idno)
    {
        $this->validate($request, ['resetType' => 'required', 'resetIdentity' => 'required']);
        $idno = des::decode($des_idno, 'KLKLKL');
        $reset = $this->studentService->reset($idno, $request->resetIdentity, $request->resetType);
        if ($reset){
            return back()->with('result', 1)->with('message', '重置成功');
        }else{
            return back()->with('result', 0)->with('message', '重置失敗');
        }

    }

    public function modifyIdno(Request $request, $des_idno)
    {
        $this->validate($request, ['new_idno' => 'required']);
        $idno = des::decode($des_idno, 'KLKLKL');
        $student = $this->studentService->getStudent($request->new_idno);
        if (empty($student)){
            $result = $this->studentService->modifyIdno($idno, $request->new_idno);
            if ($result){
                $new_idno = des::encode($request->new_idno, 'KLKLKL'); 
                return redirect("/admin/student/edit/{$new_idno}")->with('result', 1)->with('message', '修改身分證成功');
            }else{
                return back()->with('result', 0)->with('message', '修改身分證失敗');
            }
        }else{
            return back()->with('result', 0)->with('message', '該身分證已存在');
        }
    }
}