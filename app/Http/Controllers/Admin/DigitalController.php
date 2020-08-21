<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\DigitalService;
use App\Services\Term_processService;
use App\Services\User_groupService;
use App\Models\T13tb;
use DB;

use App\Models\S01tb;
use App\Models\M09tb;
use App\Helpers\NetworkTool;
use App\Models\Elearn_history;

use DateTime;

class DigitalController extends Controller
{
    /**
     * DigitalController constructor.
     * @param DigitalService $digitalService
     */
    public function __construct(DigitalService $digitalService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        setProgid('digital');
        $this->digitalService = $digitalService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('digital', $user_group_auth)){
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
    public function index(Request $request, $class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->digitalService->getT04tb($t04tb_info);
        $elearn_classes = $t04tb->elearn_classes->keyBy('id');
        $t13tbs = $t04tb->t13tbs()->where('status', '=', 1)->get();
        
        $uid = '';
        $elearn_code = '';
        foreach ($elearn_classes as $elearn_class){
            $elearn_id   = $elearn_class->id;
            $elearn_code = $elearn_class->code;
            foreach ($t13tbs as $userinfo){
                $uid .= ($userinfo->idno.',');
            }
            $networkTool = new NetworkTool();
            //永遠只更新學習通過的
            //取得數位學習的課程代碼 $elearn_classes->code 
            //透過數位課程代碼組合該班所有學員身分證取得該課程學習資料
            //透過t13tbs組合學生身分證字號(多位學生時，請用半形逗號分隔) *必填
            $url = "https://elearn.hrd.gov.tw/xmlapi/custom/get_info.php";
            $data=array(
            "cid"         => $elearn_code ,
            "uid"         => $uid 
            );
            $result = $networkTool->httpPost($url, $data);  
            $elearn_class_info = json_decode($result);
            if(null!=$elearn_class_info){
                foreach($elearn_class_info as $elearn_info){
                    if($elearn_info->status =='已通過'){
                         $store = $this->digitalService->updateElearnHistorysByEplus($elearn_id, $elearn_info);
                    }
                 
                }
            }

        }
        // dd($elearn_historys);
        $elearn_historys = $this->digitalService->getElearnHistorys($t04tb);
    
        


      

        return view('admin/digital/list', compact('elearn_classes', 'elearn_historys', 't04tb', 't13tbs'));
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
            $data = $this->digitalService->getOpenClassList($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData['t01tb']['class'] = $sess['class'];
                $queryData['t04tb']['term'] = $sess['term'];
                $queryData['t01tb']['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->digitalService->getOpenClassList($queryData);
            }
        }

        return view('admin/digital/class_list', compact('data', 'queryData', 'sponsors', 's01tbM'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('digital_student', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $t04tb_info = compact(['class', 'term']);
        $nos = [];

        $t04tb = $this->digitalService->getT04tb($t04tb_info);

        $store = $this->digitalService->storeElearnHistorys($t04tb, $request->elearn_status);
        if ($store){
            return back()->with('result', '1')->with('message', '儲存成功!');
        }else{
            return back()->with('result', '0')->with('message', '儲存失敗!');
        }

    }

    /**
     * 取得期別
     *
     * @param $class
     * @return string
     */
    public function getTerm(Request $request)
    {
        $class = $request->input('classes');

        $selected = $request->input('selected');

        if (is_numeric( mb_substr($class, 0, 1))) {

            $data = DB::select('SELECT DISTINCT term FROM t04tb WHERE class = \''.$class.'\' ORDER BY `term`');
        } else {

            $data = DB::select('SELECT DISTINCT term FROM t38tb WHERE meet = \''.$class.'\' ORDER BY `term`');
        }

        $result = '';

        foreach ($data as $va) {
            $result .= '<option value="'.$va->term.'"';
            $result .= ($selected == $va->term)? ' selected>' : '>';
            $result .= $va->term.'</option>';
        }

        return $result;
    }

    public function classSetting($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->digitalService->getT04tb($t04tb_info);
        return view('admin/digital/class_setting', compact(['t04tb']));
    }

    public function storeClassSetting(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('digital_class_setting', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $t04tb_info = compact(['class', 'term']);

        DB::beginTransaction();

        try {

            if (!empty($request->new_digital_class)){
                $this->digitalService->storeClassSetting($t04tb_info, $request->new_digital_class, 'insert');
            }
            if (!empty($request->digital_class)){
                $this->digitalService->storeClassSetting($t04tb_info, $request->digital_class, 'update');
            }
            DB::commit();

            return back()->with('message', '儲存成功')->with('result', 1);

        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;
            return back()->with('message', '儲存失敗')->with('result', 0);

            // return back()->with('result', 0)->with('message', '更新失敗');

        }



        // $this->digitalService->storeClassSetting($t04tb_info, $request->digital_class, 'update');
    }
}
