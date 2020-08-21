<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\ClassesService;
use App\Models\T01tb;
use App\Models\T06tb;
use Config;

class ClassTermPlan extends Controller
{
    // public $typeA = array('一、','二、','三、','四、','五、','六、','七、','八、','九、','十、','十一、','十二、','十三、','十四、','十五、','十六、','十七、','十八、');
    public function __construct(User_groupService $user_groupService,ClassesService $classesService )
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('class_term_plan', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });

        $this->classesService = $classesService;
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=$RptBasic->getTerms($temp[0][$arraykeys[0]]);
        $termArr=$temp;
        $result="";
        return view('admin/class_term_plan/list',compact('classArr','termArr' ,'result'));
    }
    public function getTerms(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $termArr=$RptBasic->getTerms($request->input('classes'));
        return $termArr;
    }

    public function export(Request $request)
    {
        $post = $request->all();
        if(!isset($post['classes']) || !isset($post['terms']) ) return back()->with('result', '0')->with('message', '班期或期別缺失');

        $data = $this->classesService->getClassPlanData($post['classes'],$post['terms']);
        if(empty($data))  return back()->with('result', '0')->with('message', '查無資料，請輸入正確班期');

        $lecture = $this->classesService->getLecture($post['classes'],$post['terms']);
        // var_dump($lecture);exit();
        
        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F9').'.docx');
        //title
        $templateProcessor->setValue('name',$data['name']);
        $templateProcessor->setValue('branch',config('app.branch.'.$data['branch']));
        $templateProcessor->setValue('yerly',is_null($data['yerly'])? substr($data['class'],0,3): $data['yerly']);
        $templateProcessor->setValue('term',$data['term']);
        $templateProcessor->setValue('period',$data['period']);
        $templateProcessor->setValue('kind',config('app.kind.'.$data['kind']));
        $templateProcessor->setValue('username',$data['username']);
        $templateProcessor->setValue('object',$data['object']);
        $templateProcessor->setValue('target',$data['target']);
        $templateProcessor->setValue('content',$data['content']);
        $t06data = t06tb::select('name','hour')->where('class',$post['classes'])->where('term',$post['terms'])->where('name','<>','報到')->get()->toarray();
        if(!empty($t06data)) {
            foreach ($t06data as $key => $value) {
                $t06data[$key]['course'] = ($key+1).'、'.$value['name'].'('.$value['hour'].'小時)';
            }
            $count = (sizeof($lecture) > sizeof($t06data))?sizeof($lecture) : sizeof($t06data);
            $templateProcessor->cloneRow('course', $count);
            //body
            for($i=0;$i<$count;$i++){
                $templateProcessor->setValue('course#'.strval($i+1),isset($t06data[$i]['course'])?$t06data[$i]['course']:'' ) ;
                $templateProcessor->setValue('cname#'.strval($i+1),isset($lecture[$i]['cname'])?$lecture[$i]['cname']:'' ) ;
                $templateProcessor->setValue('dept#'.strval($i+1),isset($lecture[$i]['dept'])?$lecture[$i]['dept']:'' ) ;
                $templateProcessor->setValue('position#'.strval($i+1),isset($lecture[$i]['position'])?$lecture[$i]['position']:'' ) ;
                $templateProcessor->setValue('okrateAll#'.strval($i+1),isset($lecture[$i]['okrateAll'])?$lecture[$i]['okrateAll'].'%':'' ) ;
                $templateProcessor->setValue('okrate#'.strval($i+1),isset($lecture[$i]['okrate'])?$lecture[$i]['okrate'].'%':'' ) ;
            }
        }else{
            $templateProcessor->setValue('course','');
            $templateProcessor->setValue('cname','');
            $templateProcessor->setValue('dept','');
            $templateProcessor->setValue('position','');
            $templateProcessor->setValue('okrateAll','');
            $templateProcessor->setValue('okrate','');
        }
        // $templateProcessor->setValue('course',$course);
        //docx
        header('Content-Type: application/vnd.ms-word');
        header("Content-Disposition: attachment;filename=班期計畫表.docx");
        header('Cache-Control: max-age=0');
        ob_clean();
        $templateProcessor->saveAs('php://output');
        exit;
    }

}
