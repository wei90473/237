<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use App\Models\Studyplan_year_files;
use App\Rptlib\OfficeConverterTool;


class StudyplanAllController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('studyplan_all', $user_group_auth)){
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
        return view('admin/studyplan_all/list');
    }

    public function export(Request $request) {
        $this->validate($request, [
            'yerly' => 'required'
        ],[
            "yerly.required" => "年度 欄位不可為空"
        ]);
        $doctype=$request->input('doctype');
        $year = $request->input('yerly');
        $file = Studyplan_year_files::select('filepath')->where('year',$year)->orderby('id')->first();
        $filename=urlencode("研習實施計畫總表");
        if(isset($file))
        {
            if(file_exists($file->filepath)){
                header('content-disposition:attachment;filename='.$filename.'.docx');	//告訴瀏覽器通過何種方式處理檔案
                header('content-length:'.filesize($file->filepath));	//下載檔案的大小
                readfile($file->filepath);	 //讀取檔案
            }
        }else{    //不存在檔案就自動產生
            $yearp=($year-3)."-".($year-1);
            // 讀檔案
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'D3').'.docx');
            // 替換的文字
            $templateProcessor->setValue('year', $year);
            $templateProcessor->setValue('yearp', $yearp);

            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($templateProcessor,"1","1","研習實施計畫總表");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 

        }
           
        return view('admin/studyplan_all/list');
    }


    public function import(Request $request) {
       
        $this->validate($request, [
            'yerly' => 'required',
            'import_file' => 'required'
        ],[
            "yerly.required" => "年度 欄位不可為空",
            "import_file.required" => "上傳檔案 欄位不可為空"
        ]);
        $data = $request->all();
        $filepath = '';
        $filename = '';
		if ($request->hasFile('import_file')) {
			$file = $request->file('import_file');  //獲取UploadFile例項
			if ( $file->isValid()) { //判斷檔案是否有效
				$filename  = $file->getClientOriginalName(); //檔案原名稱
				$extension = $file->getClientOriginalExtension(); //副檔名
                $newfilename  = time().'.'.$extension;    //重新命名
                if($extension != "docx"){
                    return back()->with('result', '0')->with('message', '只允許上傳WORD');
                }
                $file->move(storage_path()."/Uploads/StudyPaln/", $newfilename);
                $filepath = storage_path()."/Uploads/StudyPaln/". $newfilename;
			};
        };
        $user_data = \Auth::user();
        unset($data['import_file']);
        $createdata = [
            'year' =>$request->input('yerly'),
            'filepath' => $filepath,
            'filename' => $filename,
            'delete' => FALSE,
            'modified_time' => date("Y-m-d H:i:s"),
            'modified_user' => $user_data->userid
        ];

        //判斷使用新增還是update
        $filea = Studyplan_year_files::where('year',$request->input('yerly'))->first();
        if($filea==null){
            Studyplan_year_files::create($createdata);
        }else{
            $filea->filepath= $createdata['filepath'];
            $filea->filename= $createdata['filename'];
            $filea->delete=  FALSE;
            $filea->save();
        }

        // Studyplan_year_files::create($createdata);
        return view('admin/studyplan_all/list');
    }






}
