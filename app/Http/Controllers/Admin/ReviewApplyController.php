<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ReviewApplyService;
use App\Services\Term_processService;
use App\Services\User_groupService;
use App\Helpers\Des;
use App\Helpers\Common;
use Excel;
use DB;

use App\Models\S01tb;
use App\Models\M09tb;
use DateTime;

class ReviewApplyController extends Controller
{
    public function __construct(ReviewApplyService $reviewApplyService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        setProgid('review_apply');
        $this->reviewApplyService = $reviewApplyService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('review_apply', $user_group_auth)){
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
            $data = $this->reviewApplyService->getOpenClassList($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData['t01tb']['class'] = $sess['class'];
                $queryData['t04tb']['term'] = $sess['term'];
                $queryData['t01tb']['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->reviewApplyService->getOpenClassList($queryData);
            }
        }

        return view('admin/review_apply/list', compact('data', 'queryData', 'sponsors', 's01tbM'));
    }

    public function index(Request $request, $class, $term)
    {
        $t04tb_info = [
            'class' => $class,
            'term' => $term
        ];
        $queryData = $request->only([
            'name',
            'idno',
            'prove',
            'enrollorg',
            'enrollname',
            'organ_name',
            'rank',
            'email'
        ]);
        $t04tb = $this->reviewApplyService->getT04tb($t04tb_info);
        $t27tbs = $this->reviewApplyService->getT27tbs($t04tb_info, $queryData);

        return view('admin/review_apply/index', compact('t04tb', 't27tbs', 'queryData'));
    }

    public function edit($class, $term, $des_idno)
    {
        $t27tb_info = [
            'class' => $class,
            'term' => $term,
            'idno' => $des_idno
        ];
        $t27tb_info['idno'] = Des::decode($t27tb_info['idno'], 'KLKLK');

        $t27tb = $this->reviewApplyService->getT27tb($t27tb_info);

        $t04tb = $t27tb->t04tb;
        $m13tbs = ['' => '請選擇'];
        $m13tbs += $this->reviewApplyService->getM13tbs()->pluck('lname', 'organ')->toArray();

        $t27tb_fileds =  config('database_fields.t27tb');
        $address = ['' =>  '請選擇'];
        $address += config('address.county_text');
        $action = "edit";

        return view('admin/review_apply/form', compact('t27tb', 't04tb', 'm13tbs', 't27tb_fileds', 'address', 'action'));
    }

    public function update(Request $request, $class, $term, $des_idno)
    {

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('review_apply', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法更新');
        }

        $this->validate($request,[
            'organ' => 'required',
            'rank' => 'required',
            'ecode' => 'required',
            'birth' => 'required'
        ]);

        $t27tb_info = [
            'class' => $class,
            'term' => $term,
            'idno' => $des_idno
        ];

        $t27tb_info['idno'] = Des::decode($t27tb_info['idno'], 'KLKLK');
        $t27tb = $this->reviewApplyService->getT27tb($t27tb_info);

        if ($t27tb->prove == 'S'){
            return back()->with('result', 0)->with('message', '更新失敗');
        }

        $t27tb = $request->only([
            'cname',  // 姓名
            'sex',  // 性別
            'birth',  // 出生日期
            'organ',  // 主管機關
            'dept',  // 服務機關
            'dorm',  // 住宿
            'extradorm',  // 提前住宿
            'nonlocal',  // 遠道者
            'handicap',  // 身心障礙
            'vegan',  // 素食
            'rank',  // 官職等
            'position',  // 職稱
            'ecode',  //  最高學歷
            'education', //  最高學歷
            'offaddr1',  // 機關地址 縣市
            'offaddr2',  // 機關地址 完整
            'offzip',  // 機關地址 郵遞區號
            'homaddr1',  // 住家地址 縣市
            'homaddr2',  // 住家地址 完整
            'mobiltel', // 行動電話
            'homzip',  // 住家地址 郵遞區號
            'offtela',  // 電話(公)
            'offtelb',  // 電話(公)
            'offtelc',  // 電話(公)
            'offfaxa',  // 傳真(公)
            'offfaxb',  // 傳真(公)
            'homtela',  // 電話(宅)
            'homtelb',  // 電話(宅)
            'chief',  // 主管
            'personnel',  // 人事人員
            'aborigine',  // 原住民
            'offname',  // 人事單位姓名
            'offemail',  // 人事單位E-mail
            'offtel',  // 人事單位辦公室電話
        ]);
        $update = $this->reviewApplyService->storeT27tb($t27tb, "update", $t27tb_info);
        if ($update){
            return redirect("/admin/review_apply/edit/{$class}/{$term}/{$des_idno}")->with('result', 1)->with('message', '更新成功');
        }else{
            return back()->with('result', 0)->with('message', '更新失敗');
        }
    }

    public function delete($class, $term, $idno){
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('review_apply', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法刪除');
        }

        $t27tb_info = compact('class', 'term', 'idno');
        $t27tb_info['idno'] = Des::decode($t27tb_info['idno'], 'KLKLK');

        $delete = $this->reviewApplyService->deleteT27tb($t27tb_info);
        if ($delete){
            return redirect("/admin/review_apply/{$class}/{$term}")->with('result', 1)->with('message', "刪除成功");
        }else{
            return back()->with('result', 0)->with('message', '刪除失敗');
        }
    }

    public function apply_history(Request $request)
    {
        $t04tb_info = $request->only(['class', 'term']);
        $t04tb = $this->reviewApplyService->getT04tb($t04tb_info);
        return view('admin/review_apply/apply_history', compact('t04tb'));
    }

    public function assign(Request $request)
    {
        $t04tb_info = $request->only(['class', 'term']);

        if ($t04tb_info['class'] != null && $t04tb_info['class'] != null){
            $t04tb = $this->reviewApplyService->getT04tb($t04tb_info);
            $assign_infos = $this->reviewApplyService->getAssignData($t04tb_info);
        }else{
            $assign_infos = [];
        }

        return view('admin/review_apply/assign', compact('assign_infos','t04tb'));
    }

    public function check_apply(Request $request)
    {
        $class_info = $request->only(['class', 'term']);
        $t04tb = $this->reviewApplyService->getT04tb($class_info);
        $check_t01tb = $this->reviewApplyService->getT01tb($request->check_class);
        if(!empty($class_info['class']) && !empty($class_info['term']) && !empty($request->check_class)){
            $check_t27tbs = $this->reviewApplyService->check_apply($class_info, $request->check_class);
        }
        return view('admin/review_apply/check_apply', compact('t04tb', 'check_t27tbs', 'check_t01tb'));
    }

    public function check_repeat_apply(Request $request)
    {
        $queryData = $request->only(['class', 'term', 'sdate_start', 'sdate_end']);

        $repeat_data = $this->reviewApplyService->getRepeatApplyForSdate($queryData);

        $class_info = $request->only(['class', 'term']);
        $t04tb = $this->reviewApplyService->getT04tb($class_info);

        return view('admin/review_apply/check_repeat_apply', compact('t04tb', 'repeat_data', 'queryData'));
    }

    public function copy_apply_choose()
    {
        return view('admin/review_apply/copy_apply', compact('t04tb', 'repeat_data', 'queryData'));
    }

    public function copy_apply(Request $request)
    {

        $this->validate($request, [
            'copy_purpose.class' => 'required',
            'copy_purpose.term' => 'required',
            'copyed.class' => 'required',
            'copyed.term' => 'required',
            'copy_mode' => 'required',
            'over_data' => 'required'
        ],[
            'copy_purpose.class.required' => '欄位不可為空',
            'copy_purpose.term.required' => '欄位不可為空',
            'copyed.class.required' => '欄位不可為空',
            'copyed.term.required' => '欄位不可為空',
            'copy_mode.required' => '欄位不可為空',
            'over_data.required' => '欄位不可為空'
        ]);
        $copy_info = $request->only(['copy_purpose', 'copyed', 'copy_mode', 'over_data']);

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('review_apply', $copy_info['copy_purpose']['class'], $copy_info['copy_purpose']['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法複製');
        }

        $copy_apply = $this->reviewApplyService->copy_apply($copy_info);

        if ($copy_apply){
            return back()->with('result', 1)->with('message', "複製{$copy_info['copyed']['class']} 第 {$copy_info['copyed']['term']} 期報名資訊 至 {$copy_info['copy_purpose']['class']} 第 {$copy_info['copy_purpose']['term']} 期 成功");
        }else{
            return back()->with('result', 0)->with('message', '更新失敗');
        }
    }

    public function check_copy_repeat(Request $request)
    {
        $copy_info = $request->only(['copy_purpose', 'copyed']);
        // 取得重複人數
        $is_repeat = $this->reviewApplyService->getRepeatCount($copy_info);
        return response()->json(['status' => 0, 'is_repeat' => $is_repeat]);

    }

    public function storeAssign(Request $request, $class, $term, $organ)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('assign', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法更新');
        }

        $t51tb_data = $request->only(['share']);
        $t51tb_info = compact(['class', 'term', 'organ']);

        $update = $this->reviewApplyService->storeT51tb($t51tb_data, 'update', $t51tb_info);
        if ($update){
            return back()->with('result', 1)->with('message', "更新成功");
        }else{
            return back()->with('result', 0)->with('message', '更新失敗');
        }
    }

    public function review(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('review_apply', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法更新');
        }

        $this->validate($request, [
            'proves' => 'required',
        ]);

        $class_info = compact(['class', 'term']);

        $review = $this->reviewApplyService->review($class_info, $request->proves);

        $message = "";

        foreach($review['result'] as $result){
            if($result['status'] === false){
                $message .= $result['message'].'<br>';
            }
        }


        if ($review['status']){
            if(!empty($message)){
                $message = '儲存成功有部份報名資料有誤<br><font color="red">'.$message."</font>";
            }else{
                $message = '儲存成功';
            }

            return back()->with('result', 1)->with('html_message', $message);
        }else{
            return back()->with('result', 0)->with('message', '儲存失敗');
        }


    }

    public function importApplyData(Request $request, $class, $term)
    {

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('importApplyData', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法匯入');
        }

        $this->validate($request, [
            'import_file' => 'required',
            'identity' => 'required'
        ],[
            "import_file.required" => "請選擇一個檔案",
            'identity.required' => "請選擇人員身份"
        ]);

        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->reviewApplyService->getT04tb($t04tb_info);

        $path = $request->file('import_file')->getRealPath();
        $apply_datas = Excel::load($path)->sheet(0)->toArray();
        
        // 轉換格式
        $apply_datas = $this->reviewApplyService->importFileTransFormat($apply_datas, $request->identity);

        $errors = $this->reviewApplyService->validateImport($apply_datas, $request->identity);

        if (!empty($errors)){
            return back()->with('result', 0)->with('html_message', $errors);
        }

        // $apply_datas = $this->studentApplyService->splitApplyData($apply_datas, $request->identity, $version);

        $import = $this->reviewApplyService->importApplyData($t04tb_info, $apply_datas);

        if ($import){
            return back()->with('message', '匯入成功')->with('result', 1);
        }else{
            return back()->with('message', '匯入失敗')->with('result', 0);
        }



    }

    /*
        檢查目前有無報名資料
        若有則詢問覆蓋或者附加
    */
    function check_is_over(Request $request){
        $t04tb_info = $request->only(['class', 'term']);
        $t04tb = $this->reviewApplyService->getT04tb($t04tb_info, []);
        return response()->json(['status', 'ask_over' => count($t04tb->t27tbs) > 0]);
    }



}