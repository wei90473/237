<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Models\T04tb;
use DB;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use NcJoes\OfficeConverter\OfficeConverter;
use App\Helpers\Common;

class StudentRegistrationController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_registration', $user_group_auth)){
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
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=$RptBasic->getTerms($temp[0][$arraykeys[0]]);
        $termArr=$temp;
        $result="";
        return view('admin/student_registration/list',compact('classArr','termArr' ,'result'));

    }

    public function getTerms(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $termArr=$RptBasic->getTerms($request->input('classes'));
        return $termArr;
    }

    public function export(Request $request){
        $t04tbKey = $request->only(['class', 'term']);

        $t04tb = T04tb::where($t04tbKey)->whereHas('t01tb')->first();
        
        if (empty($t04tb)){
            return back()->with('result', 0)->with('message', '該班期不存在');
        }

        $this->exportODT($t04tb);
    }

    private function exportODT($t04tb)
    {
        $templateFileName = "../example/".iconv('UTF-8', 'BIG5', 'student_apply').".docx";
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templateFileName);

        $tempFileName = $t04tb->t01tb->class.'-'.$t04tb->term.'_student_apply';
        $tempPath = "reportTemp/student_registration/{$tempFileName}";

        // 開始替換內容

        // 臺北院區
        $branch1 = ($t04tb->t01tb->branch == 1) ? '■' : '□';
        // 南投院區
        $branch2 = ($t04tb->t01tb->branch == 2) ? '■' : '□';
        $sedate = ($t04tb->sdate == $t04tb->edate) ? Common::addDateSlash($t04tb->sdate) : Common::addDateSlash($t04tb->sdate).'~'."\n".Common::addDateSlash($t04tb->edate);

        $templateProcessor->setValue('branch1', $branch1);
        $templateProcessor->setValue('branch2', $branch2);
        $templateProcessor->setValue('classname', $t04tb->t01tb->class.' '.$t04tb->t01tb->name);
        $templateProcessor->setValue('term', $t04tb->term);
        $templateProcessor->setValue('sedate', $sedate);

        // 結束替換內容
        $templateProcessor->saveAs(storage_path($tempPath.'.docx'));

        $converter = new OfficeConverter(storage_path($tempPath.'.docx')); //讀取要轉檔的檔案
        $converter->convertTo($tempFileName.'.odt'); // 轉成 odt(ODF) 格式

        //export odt
        ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header("Content-Disposition: attachment;filename=".$t04tb->t01tb->class.'-'.$t04tb->term."學員報名表.odt");
        header('Cache-Control: max-age=0');

        readfile(storage_path($tempPath.'.odt'));
        exit;

    }
}
