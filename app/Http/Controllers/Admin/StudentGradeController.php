<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\StudentGradeService;
use App\Services\Term_processService;
use App\Services\User_groupService;
use DB;
use Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpWord\PhpWord;

use App\Models\S01tb;
use App\Models\M09tb;

use DateTime;

/*
    學員成績處理
*/
class StudentGradeController extends Controller
{
    /**
     * StudentGradeController constructor.
     * @param
     */
    public function __construct(StudentGradeService $studentGradeService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        setProgid('student_grade');
        $this->studentGradeService = $studentGradeService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_grade', $user_group_auth)){
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
            $data = $this->studentGradeService->getOpenClassList($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData['t01tb']['class'] = $sess['class'];
                $queryData['t04tb']['term'] = $sess['term'];
                $queryData['t01tb']['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->studentGradeService->getOpenClassList($queryData);
            }
        }

        return view('admin/student_grade/class_list', compact('data', 'queryData', 'sponsors', 's01tbM'));
    }

    public function index($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->studentGradeService->getT04tb($t04tb_info);
        $grade_main_options = $t04tb->grade_main_options;
        $grades = $this->studentGradeService->computeGrades($t04tb_info);

        return view('admin/student_grade/index', compact(['t04tb', 'grade_main_options', 'grades']));
    }

    public function inputGrade($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->studentGradeService->getT04tb($t04tb_info);
        $grade_main_options = $t04tb->grade_main_options;

        return view('admin/student_grade/input_grade', compact(['t04tb', 'grade_main_options']));
    }

    public function main_option($id)
    {
        $main_option = $this->studentGradeService->getMainOption($id);
        $grade_sub_options = $main_option->grade_sub_options;
        $t04tb = $main_option->t04tb;

        return view('admin/student_grade/main_option', compact(['t04tb', 'main_option', 'grade_sub_options']));
    }

    public function storeSubOption(Request $request, $id)
    {

        // 匯入轉換
        if ($request->action == "import"){
            if ($request->hasFile('import_file')){
                $path = $request->file('import_file')->getRealPath();
                $sub_options = Excel::load($path)->sheet(0)->toArray();
                unset($sub_options[0]);

                $input = $this->studentGradeService->transSubOptionImportData($sub_options, ['main_option_id' => $id]);
                $request->merge($input);
            }else{
                return back()->with('result', 0)->with('message', '請選擇檔案');
            }
        }

        $is_100_persent = $this->studentGradeService->checkSubOption100Persent($request->only(['new_sub_option', 'sub_option']));

        if ($is_100_persent === false){
            return back()->with('message', '儲存失敗，項目總和未滿或超過 100 %')->with('result', 0);
        }
        $main_option = $this->studentGradeService->getMainOption($id);
        $grade_sub_options = $main_option->grade_sub_options;

        $subOption = (isset($request->sub_option)) ? $request->sub_option : [];

        $delete = array_diff($grade_sub_options->pluck('id')->toArray(), array_keys($subOption));

        DB::beginTransaction();

        try {

            if (!empty($request->new_sub_option)){
                $this->studentGradeService->storeGradeSubOption($id, $request->new_sub_option, 'insert');
            }

            if (!empty($request->sub_option)){
                $this->studentGradeService->storeGradeSubOption($id, $request->sub_option, 'update');
            }

            $this->studentGradeService->deleteSubOptions($delete);

            DB::commit();

            return back()->with('message', '儲存成功')->with('result', 1);

        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;
            return back()->with('message', '儲存失敗')->with('result', 0);
        }

    }

    public function setting($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->studentGradeService->getT04tb($t04tb_info);
        $grade_main_options = $t04tb->grade_main_options;
        return view('admin/student_grade/setting', compact(['t04tb', 'grade_main_options']));
    }

    public function sub_option($id)
    {
        return view('admin/student_grade/sub_option');
    }

    public function setSeting(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('student_grade_setting', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $t04tb_info = compact(['class', 'term']);
        // 匯入轉換
        if ($request->action == "import"){
            if ($request->hasFile('import_file')){
                $path = $request->file('import_file')->getRealPath();
                $main_options = Excel::load($path)->sheet(0)->toArray();
                unset($main_options[0]);

                $input = $this->studentGradeService->transMainOptionImportData($main_options, $t04tb_info);
                $request->merge($input);
            }else{
                return back()->with('result', 0)->with('message', '請選擇檔案');
            }
        }

        $is_100_persent = $this->studentGradeService->check100Persent($request->only(['new_main_option', 'main_option']));

        if ($is_100_persent === false){
            return back()->with('message', '儲存失敗，項目總和未滿或超過 100 %')->with('result', 0);
        }

        $t04tb = $this->studentGradeService->getT04tb($t04tb_info);
        $origin_grade_main_options = $t04tb->grade_main_options;

        $newMainOption = (isset($request->main_option)) ? $request->main_option : [];

        $delete = array_diff($origin_grade_main_options->pluck('id')->toArray(), array_keys($newMainOption));

        DB::beginTransaction();

        try {

            if (!empty($request->new_main_option)){
                $this->studentGradeService->storeGradeMainOption($t04tb_info, $request->new_main_option, 'insert');
            }

            if (!empty($request->main_option)){
                $this->studentGradeService->storeGradeMainOption($t04tb_info, $request->main_option, 'update');
            }

            $this->studentGradeService->deleteMainOptions($delete);

            DB::commit();

            return back()->with('message', '儲存成功')->with('result', 1);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('message', '儲存失敗')->with('result', 0);

            // return back()->with('result', 0)->with('message', '更新失敗');
            var_dump($e->getMessage());
            die;
        }

    }

    public function inputGradeSub($id)
    {
        $main_option = $this->studentGradeService->getMainOption($id);
        $grade_sub_options = $main_option->grade_sub_options_with_grade->keyBy('id');
        $t04tb = $main_option->t04tb;
        $t04tb->t13tbs = $t04tb->t13tbs()->where('status', '=', 1)->whereNotNull('no')->get();

        foreach($grade_sub_options as $sub_option){
            $sub_option->student_grades = $sub_option->student_grades->keyBy('idno');
        }

        return view('admin/student_grade/sub_option_list', compact(['t04tb', 'grade_sub_options', 'main_option']));
    }

    public function storeGrade(Request $request, $main_option_id)
    {
        $status = false;
        if (!empty($request->grade)){
            $status = $this->studentGradeService->storeGrade($request->grade);
        }

        if ($status){
            return back()->with('message', '儲存成功')->with('result', 1);
        }else{
            return back()->with('message', '儲存失敗')->with('result', 0)->withInput();
        }

    }

    public function downloadExportExample(Request $request, $class, $term)
    {
        $t04tb = $request->t04tb;
        $t13tbs = $t04tb->t13tbs()->where('status', '=', 1)->get();

        $grade_main_options = $t04tb->grade_main_options()->with('grade_sub_options')->get();

        $export_datas = [
            ['姓名', '學號']
        ];

        foreach ($grade_main_options as $grade_main_option){
            foreach ($grade_main_option->grade_sub_options as $grade_sub_option ){
                $export_datas[0][] = $grade_main_option->name.'-'.$grade_sub_option->name;
            }
        }

        foreach ($t13tbs as $t13tb){
            $student = [];
            $student[0] = $t13tb->m02tb->cname;
            $student[1] = $t13tb->no;
            $export_datas[] = $student;
        }

        Excel::create($t04tb->class." ".$t04tb->t01tb->name." 第".$t04tb->term.'期 成績匯入', function($excel) use($export_datas){
            $excel->sheet('成績', function($sheet) use($export_datas){
               $sheet->fromArray($export_datas, null, 'A1', false, false);
            });
        })->export('xls');

    }

    public function importGrade(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('student_grade_input_grade', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法匯入');
        }

        $this->validate($request, [
            'import_file' => 'required'
        ],[
            "import_file.required" => "請選擇一個檔案"
        ]);

        $t04tb_info = compact(['class', 'term']);
        $t04tb = $request->t04tb;

        $grade_main_options = $t04tb->grade_main_options()->with('grade_sub_options')->get();

        $path = $request->file('import_file')->getRealPath();
        $grades = Excel::load($path)->sheet(0)->toArray();

        // 計算子項目數量
        $grade_sub_option_count = 0;
        foreach ($grade_main_options as $grade_main_option){
            foreach ($grade_main_option->grade_sub_options as $grade_sub_option ){
                $grade_sub_option_count++;
            }
        }

        if (count(array_filter($grades[0]))-2 <> $grade_sub_option_count){
            return back()->with('result', 0)->with('message', '匯入失敗，格式錯誤，請確認匯入格式是否為最新格式');
        }

        unset($grades[0]);
        $sub_option_grades = [];

        $t13tbs = $t04tb->t13tbs->pluck('idno', 'no');

        // 轉換格式成 子項目 => 學號 => 成績
        foreach ($grades as $grade){
            $grade_key = 2;
            foreach ($grade_main_options as $grade_main_option){
                if (isset($t13tbs[$grade[1]])){
                    foreach ($grade_main_option->grade_sub_options as $grade_sub_option ){
                        $sub_option_grades[$grade_sub_option->id][$t13tbs[$grade[1]]] = $grade[$grade_key];
                        $grade_key++;
                    }
                }
            }
        }

        $store = $this->studentGradeService->storeGrade($sub_option_grades);

        if ($store){
            return back()->with('result', 1)->with('message', '匯入成功');
        }else{
            return back()->with('result', 0)->with('message', '匯入失敗');
        }

    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function report_index(Request $request)
    {
        //取得班別
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
        $termArr=$temp;
        $result = '';
        return view('admin/student_grade/list',compact('classArr','termArr' ,'result'));
    }


    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    public function export(Request $request){

        $class = $request->input('classes');
        $term = $request->input('term');
        $type = $request->input('type');
        $sex="";

        if($type=="1"){ //明細
            $sql="Select
            t04tb.sdate, t04tb.edate , '' AS A, t13tb.no, m02tb.cname, m02tb.sex, t13tb.education, CONCAT(LTRIM(RTRIM(t13tb.dept)),LTRIM(RTRIM(t13tb.position))) as deptpos,
            t04tb.actratio, t04tb.schratio,
            t15tb.actitem1, t15tb.actitem2, t15tb.actitem3, t15tb.actitem4, t15tb.actitem5, t15tb.actitem6 ,
            t15tb.act1, t15tb.act2, t15tb.act3, t15tb.act4, t15tb.act5, t15tb.act6 ,
            t04tb.schitem1, t04tb.schitem2, t04tb.schitem3, t04tb.schitem4, t04tb.schitem5,
            t04tb.schrate1, t04tb.schrate2, t04tb.schrate3, t04tb.schrate4, t04tb.schrate5,
            t15tb.sch1, t15tb.sch2, t15tb.sch3, t15tb.sch4, t15tb.sch5,
            t15tb.schcom1, t15tb.schcom2, t15tb.schcom3, t15tb.schcom4, t15tb.schcom5,
            '' AS B, '' AS C,
            CONCAT('實得分數：(' , cast(t04tb.basescr as char) , '分+小計)X' , cast(t04tb.actratio as char) ) AS score,
            t15tb.actsum ,t15tb.schsum ,t15tb.totscr, t15tb.rank , ''
            From t04tb inner join t13tb on t04tb.class = t13tb.class and t04tb.term = t13tb.term
            inner join t15tb on t13tb.class = t15tb.class and t13tb.term =t15tb.term and t13tb.idno = t15tb.idno
            inner join m02tb on t13tb.idno = m02tb.idno
            Where t15tb.class ='".$class."' and t15tb.term ='".$term."' and t13tb.status='1'
            Order by t13tb.no ";
            $temp = DB::select($sql);
            $data = json_decode(json_encode($temp), true);

            // 查無資料處裡
            if(sizeof($data) == 0) {
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclass();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = '查無資料，請重新查詢';
                return view('admin/student_grade/list',compact('classArr','termArr' ,'result'));
            }

            $sql="select name from t01tb where class='".$class."'";
            $temp = DB::select($sql);
            $classname = json_decode(json_encode($temp), true);

            $sql="Select code, name From s01tb Where UPPER(type)='C'";
            $temp = DB::select($sql);
            $codename = json_decode(json_encode($temp), true);

            // 範本檔案名稱
            $fileName = 'J8';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel

            $objPHPExcel = IOFactory::load($filePath);
            $objActSheet = $objPHPExcel->getActiveSheet();
            $objActSheet->setCellValue('C3',substr($class,0,3)."年".$classname[0]["name"]."第".strval((int)$term)."期");
            $objActSheet->setCellValue('G1',"自　".strval((int)substr($data[0]["sdate"],0,3)).".".substr($data[0]["sdate"],3,2).".".substr($data[0]["sdate"],5,2));
            $objActSheet->setCellValue('G2',"至　".strval((int)substr($data[0]["edate"],0,3)).".".substr($data[0]["edate"],3,2).".".substr($data[0]["edate"],5,2));
            $objActSheet->setCellValue('G5',"平 時 考 核 成 績(".$data[0]["actratio"]."%)");
            $objActSheet->setCellValue('G5',"學 科 考 核 成 績(".$data[0]["schratio"]."%)");

            for($i=2;$objPHPExcel->getSheetCount()<sizeof($data);$i++){
                $clonedWorksheet= clone $objPHPExcel->getSheet(0);
                $clonedWorksheet->setTitle(strval($i));
                $objPHPExcel->addSheet($clonedWorksheet);
            }

            for($i=0;$i<sizeof($data);$i++){
                $objSheet = $objPHPExcel->getSheet($i);
                $objSheet->setCellValue('A5',strval((int)$data[$i]["no"]));
                $objSheet->setCellValue('C5',$data[$i]["cname"]);

                if($data[$i]["cname"]="F")
                    $sex="女";
                elseif($data[$i]["cname"]="M")
                    $sex="男";

                $objSheet->setCellValue('D5',$sex);
                $objSheet->setCellValue('E5',$data[$i]["education"]);
                $objSheet->setCellValue('G5',$data[$i]["deptpos"]);
                $objSheet->setCellValue('A8',$this->getactitemname($data[$i]["actitem1"],$codename));
                $objSheet->setCellValue('A9',$this->getactitemname($data[$i]["actitem2"],$codename));
                $objSheet->setCellValue('A10',$this->getactitemname($data[$i]["actitem3"],$codename));
                $objSheet->setCellValue('A11',$this->getactitemname($data[$i]["actitem4"],$codename));
                $objSheet->setCellValue('A12',$this->getactitemname($data[$i]["actitem5"],$codename));
                $objSheet->setCellValue('A13',$this->getactitemname($data[$i]["actitem6"],$codename));
                $objSheet->setCellValue('D8',$data[$i]["act1"]);
                $objSheet->setCellValue('D9',$data[$i]["act2"]);
                $objSheet->setCellValue('D10',$data[$i]["act3"]);
                $objSheet->setCellValue('D11',$data[$i]["act4"]);
                $objSheet->setCellValue('D12',$data[$i]["act5"]);
                $objSheet->setCellValue('D13',$data[$i]["act6"]);

                $objSheet->setCellValue('E8',$data[$i]["schitem1"]);
                $objSheet->setCellValue('E9',$data[$i]["schitem2"]);
                $objSheet->setCellValue('E10',$data[$i]["schitem3"]);
                $objSheet->setCellValue('E11',$data[$i]["schitem4"]);
                $objSheet->setCellValue('E12',$data[$i]["schitem5"]);
                $objSheet->setCellValue('G8',$data[$i]["schrate1"]);
                $objSheet->setCellValue('G9',$data[$i]["schrate2"]);
                $objSheet->setCellValue('G10',$data[$i]["schrate3"]);
                $objSheet->setCellValue('G11',$data[$i]["schrate4"]);
                $objSheet->setCellValue('G12',$data[$i]["schrate5"]);
                $objSheet->setCellValue('H8',$data[$i]["sch1"]);
                $objSheet->setCellValue('H9',$data[$i]["sch2"]);
                $objSheet->setCellValue('H10',$data[$i]["sch3"]);
                $objSheet->setCellValue('H11',$data[$i]["sch4"]);
                $objSheet->setCellValue('H12',$data[$i]["sch5"]);
                $objSheet->setCellValue('I8',$data[$i]["schcom1"]);
                $objSheet->setCellValue('I9',$data[$i]["schcom2"]);
                $objSheet->setCellValue('I10',$data[$i]["schcom3"]);
                $objSheet->setCellValue('I11',$data[$i]["schcom4"]);
                $objSheet->setCellValue('I12',$data[$i]["schcom5"]);

                $objSheet->setCellValue('A20',$data[$i]["score"]);
                $objSheet->setCellValue('D20',$data[$i]["actsum"]);
                $objSheet->setCellValue('H20',$data[$i]["schsum"]);
                $objSheet->setCellValue('B21',$data[$i]["totscr"]);
                $objSheet->setCellValue('E21',$data[$i]["rank"]);
                $objSheet->setCellValue('H21',strval(sizeof($data)));

            }

            //export excel
            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="學員成績-成績明細.xlsx"');
            header('Cache-Control: max-age=0');
             // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
            $objWriter->save('php://output');
            exit;



        }else{  //清冊

            $sql="Select
            t13tb.no , m02tb.dept, m02tb.cname ,t04tb.schratio, t04tb.actratio, t04tb.schitem1, t04tb.schrate1, t04tb.schitem2, t04tb.schrate2, t04tb.schitem3, t04tb.schrate3, t04tb.schitem4, t04tb.schrate4, t04tb.schitem5, t04tb.schrate5 , t15tb.schcom1, t15tb.schcom2, t15tb.schcom3, t15tb.schcom4, t15tb.schcom5, t15tb.schsum, t15tb.actsum, t15tb.totscr, t15tb.rank
            From
            t04tb, t13tb, t15tb, m02tb
            Where
            t15tb.class ='".$class."' and t15tb.term ='".$term."' and t13tb.status='1'
            and (t04tb.class = t13tb.class and t04tb.term = t13tb.term) and (t13tb.idno = m02tb.idno)  and (t13tb.class = t15tb.class and t13tb.term = t15tb.term and t13tb.idno = t15tb.idno)
            Order by t13tb.no";

            $temp = DB::select($sql);
            $data = json_decode(json_encode($temp), true);

            $sql="select name from t01tb where class='".$class."'";
            $temp = DB::select($sql);
            $classname = json_decode(json_encode($temp), true);


            // 查無資料
            if(sizeof($data) == 0) {
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclass();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = '查無資料，請重新查詢';
                return view('admin/student_grade/list',compact('classArr','termArr' ,'result'));
            }


            $cnt=0;
            for($i=1;$i<6;$i++){
                if($data[0]["schitem".$i]!="")
                    $cnt++;
            }

            if($cnt==0)
                $cnt=1;

            // 讀檔案
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J8'.$cnt).'.docx');
            $templateProcessor->setValue('title',$classname[0]["name"]);
            $templateProcessor->setValue('sr',$data[0]["schratio"]);
            $templateProcessor->setValue('ar',$data[0]["actratio"]);
            $templateProcessor->cloneRow('s', sizeof($data));

            for($i=0;$i<sizeof($data);$i++){

                $templateProcessor->setValue('s#'.strval($i+1),$data[$i]["no"]);
                $templateProcessor->setValue('dept#'.strval($i+1),$data[$i]["dept"]);
                $templateProcessor->setValue('name#'.strval($i+1),$data[$i]["cname"]);
                $templateProcessor->setValue('A1#'.strval($i+1),$data[$i]["schcom1"]);
                $templateProcessor->setValue('B#'.strval($i+1),$data[$i]["schsum"]);
                $templateProcessor->setValue('C#'.strval($i+1),$data[$i]["actsum"]);
                $templateProcessor->setValue('D#'.strval($i+1),$data[$i]["totscr"]);
                $templateProcessor->setValue('E#'.strval($i+1),$data[$i]["rank"]);

                switch ($cnt) {
                    case 2:
                        $templateProcessor->setValue('A2#'.strval($i+1),$data[$i]["schcom2"]);
                        break;
                    case 3:
                        $templateProcessor->setValue('A2#'.strval($i+1),$data[$i]["schcom2"]);
                        $templateProcessor->setValue('A3#'.strval($i+1),$data[$i]["schcom3"]);
                        break;
                    case 4:
                        $templateProcessor->setValue('A2#'.strval($i+1),$data[$i]["schcom2"]);
                        $templateProcessor->setValue('A3#'.strval($i+1),$data[$i]["schcom3"]);
                        $templateProcessor->setValue('A4#'.strval($i+1),$data[$i]["schcom4"]);
                        break;
                    case 5:
                        $templateProcessor->setValue('A2#'.strval($i+1),$data[$i]["schcom2"]);
                        $templateProcessor->setValue('A3#'.strval($i+1),$data[$i]["schcom3"]);
                        $templateProcessor->setValue('A4#'.strval($i+1),$data[$i]["schcom4"]);
                        $templateProcessor->setValue('A5#'.strval($i+1),$data[$i]["schcom5"]);
                        break;
                }

            }


            header('Content-Type: application/vnd.ms-word');
            header("Content-Disposition: attachment;filename=學員成績-成績清冊.docx");
            header('Cache-Control: max-age=0');

            ob_clean();
            $templateProcessor->saveAs('php://output');
            exit;


        }
    }

    public function getactitemname($code,$codename){
        foreach($codename as $cn){
            if($cn["code"]==$code)
                return $cn["name"];
        }
        return "";
    }
}