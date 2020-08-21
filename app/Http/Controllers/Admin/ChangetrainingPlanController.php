<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\ArrangementService;
use App\Models\ChangeTrainingPlanFiles;
use DB;
use App\Models\T01tb;

class ChangetrainingPlanController extends Controller
{
    public function __construct(ArrangementService $arrangementService, User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        $this->arrangementService=$arrangementService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('changetraining_plan', $user_group_auth)){
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
        //取得班別
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclassEx();

        $classArr =$temp;
        $result = '';
        return view('admin/changetraining_plan/list',compact('classArr' ,'result'));
    }

    private function getFile($class)
    {
        $temp= DB::select("SELECT concat( RTRIM(class),'.',
        case when hex(substring(planmk,1,8))='D0CF11E0A1B11AE1' then 'doc'
         when  hex(substring(planmk,1,8))='504B030414000600'  then 'docx'
         when  hex(substring(planmk,1,4))='25504446'  then 'pdf'
         else
         ''
         end ) as filename,
         planmk
         FROM t01tb
         WHERE class = '".$class."'
         AND planmk IS NOT NULL");
        return $temp;
    }

    public function export(Request $request)
    {

        $this->validate($request, [
            'classes' => 'required'
        ],[
            "classes.required" => "班號 欄位不可為空"
        ]);

        $class = $request->input('classes');
        $file = $this->getFile($class);

        // $t01tb = T01tb::select('*')->where('class',$class)->first();
        // $file = ChangeTrainingPlanFiles::select('filepath')->where('class',$class)->orderby('id')->first();
        if(isset($file[0]))
        {

         $headers = array('Content-Type: application/octet-stream',);
         return response($file[0]->planmk, '200')->header('Content-Type', 'application/octet-stream')->header('Content-disposition','attachment; filename="'.$file[0]->filename.'"');
        }
       else{    //不存在檔案就自動產生

        $resultiDstribution = '';
        //取得該班期別
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        foreach($termArr as $clasterm){
            $class_info = [
                "class" => $request->input('classes'),
                "term"  => $clasterm->term
             ];
             //計算各期課程與時數
             $t04tb = $this->arrangementService->getT04tb($class_info);
             if(isset($t04tb->t06tbs))
             $resultiDstribution.= ('第'.$clasterm->term.'期<w:br />');
             foreach($t04tb->t06tbs as $infoDstribution){
                $resultiDstribution.= ($infoDstribution->name.'('.$infoDstribution->hour.'小時)<w:br />');
             }
        }


            $sql="SELECT class, RTRIM(name), target, object,(
                CASE board
                 WHEN 'Y' THEN '研習期間提供住宿申請，惟結訓當日不供宿。'
                 WHEN 'N' THEN '採不住班研習，但遠道者可登記住宿。'
                 WHEN 'X' THEN '採不住班研習。'
                 ELSE ''
                END ) as cboard
               FROM t01tb WHERE class='".$request->input('classes')."'";
            $classBasic=json_decode(json_encode(DB::select($sql)), true);;

            if($classBasic==[]){
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclassEx();

                $classArr =$temp;
                $result = '查無資料，請重新查詢。';
                return view('admin/changetraining_plan/list',compact('classArr' ,'result'));
            }

            $classBasickeys=array_keys((array)$classBasic[0]);

            $sql="SELECT DISTINCT A.term, B.sdate, B.edate ,B.quota
            FROM t03tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
            WHERE A.class='".$request->input('classes')."' ORDER BY A.term ";

            $termDetail=json_decode(json_encode(DB::select($sql)), true);

            if($termDetail==[]){
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclassEx();

                $classArr =$temp;
                $result = '查無資料，請重新查詢。';
                return view('admin/changetraining_plan/list',compact('classArr' ,'result'));
            }

            $termDetailkeys=array_keys((array)$termDetail[0]);

            $resultYear=substr($classBasic[0][$classBasickeys[0]],0,3);
            $resultName=$classBasic[0][$classBasickeys[1]];
            $resultTarget=$classBasic[0][$classBasickeys[2]];
            $resultObject=$classBasic[0][$classBasickeys[3]];
            $resultBoard=$classBasic[0][$classBasickeys[4]];

            $resultTerm="";
            foreach($termDetail as $value){

                //第1期：自108年05月20日起至108年05月21日止，計42人。<w:br />
                $resultTerm.="第".strval((int)$value[$termDetailkeys[0]])."期：自".substr($value[$termDetailkeys[1]],0,3)."年".substr($value[$termDetailkeys[1]],3,2)."月".
                substr($value[$termDetailkeys[1]],5,2)."日起至".substr($value[$termDetailkeys[2]],0,3)."年".substr($value[$termDetailkeys[2]],3,2)."月".
                substr($value[$termDetailkeys[2]],5,2)."日止，計".$value[$termDetailkeys[3]]."人。<w:br />";

            }

            // 讀檔案
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F4').'.docx');

            // 替換的文字
            $templateProcessor->setValue('year', $resultYear);
            $templateProcessor->setValue('name', $resultName);
            $templateProcessor->setValue('target', $resultTarget);
            $templateProcessor->setValue('distribution', $resultiDstribution);
            $templateProcessor->setValue('object', $resultObject);
            $templateProcessor->setValue('term', $resultTerm);
            $templateProcessor->setValue('board', $resultBoard);

            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),'實施計畫');
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel
            //$doctype:1.ooxml 2.odf
            //$filename:filename

        }



    }


    public function import(Request $request) {

        $this->validate($request, [
            'classes' => 'required',
            'import_file' => 'required'
        ],[
            "classes.required" => "班號 欄位不可為空",
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
                $file->move(storage_path()."/Uploads/ChangeTrainingPlan/", $newfilename);
                $filepath = storage_path()."/Uploads/ChangeTrainingPlan/". $newfilename;
			};
        };
        $user_data = \Auth::user();
        unset($data['import_file']);
        $createdata = [
            'class' =>$request->input('classes'),
            'filepath' => $filepath,
            'filename' => $filename,
            'delete' => FALSE,
            'modified_time' => date("Y-m-d H:i:s"),
            'modified_user' => $user_data->userid
        ];
        ChangeTrainingPlanFiles::create($createdata);
        return back()->with('result', '1')->with('message', '上傳成功');
    }

}
