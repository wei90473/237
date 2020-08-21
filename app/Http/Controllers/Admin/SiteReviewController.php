<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SiteReviewService;
use App\Services\User_groupService;
use DB;
use App\Helpers\Des;
use DateTime;

use App\Models\S01tb;
use App\Models\M09tb;
use Excel;
class SiteReviewController extends Controller
{
    /**
     * SiteReviewController constructor.
     * @param SiteReviewService $site_reviewService
     */
    public function __construct(SiteReviewService $siteReviewService, User_groupService $user_groupService)
    {
        setProgid('site_review');
        $this->siteReviewService = $siteReviewService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('site_review', $user_group_auth)){
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

        $queryData['t01tb']['type'] = 13;
        
        if (empty($queryData['t01tb']['yerly'])){
            $queryData['t01tb']['yerly'] = new DateTime();
            $queryData['t01tb']['yerly'] = $queryData['t01tb']['yerly']->format('Y') - 1911;
        }

        $s01tbM = S01tb::where('type', '=', 'M')->get()->pluck('name', 'code');
        $sponsors = M09tb::all();

        $data = [];
        if ($request->all()){
            $data = $this->siteReviewService->getOpenClassList($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData['t01tb']['class'] = $sess['class'];
                $queryData['t04tb']['term'] = $sess['term'];
                $queryData['t01tb']['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->siteReviewService->getOpenClassList($queryData);
            }
        }

        return view('admin/site_review/class_list', compact('data', 'queryData', 'sponsors', 's01tbM'));
    }

    public function index(Request $request, $class, $term)
    {

        $queryData = $request->only([
            'idno', 'cname', 'enrollid', 'position', 'prove'
        ]);

        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->siteReviewService->getT04tb($t04tb_info);

        if (empty($t04tb)){
            return back()->with('result', 1)->with('message', '找不到該班期');
        }

        $t04tb->t39tbs = $this->siteReviewService->getT39tbs($t04tb_info, $queryData);

        $t39tb_fields = config('database_fields.t39tb');
        return view('admin/site_review/index', compact(['t04tb', 'queryData', 't39tb_fields']));

    }

    public function create($class, $term, $des_idno)
    {
        $idno = Des::decode($des_idno, 'KLKLKL'); 
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->siteReviewService->getT04tb($t04tb_info);
        $m02tb = $this->siteReviewService->getStudentOrNew($idno);

        $t39tb_fields = config('database_fields.t39tb');
        $m02tb_fields = config('database_fields.m02tb');
        $now = new DateTime();
        return view('admin/site_review/form', compact(['t04tb', 't39tb_fields', 'm02tb_fields', 'now', 'm02tb', 'des_idno']));
    }

    public function checkExistCreate(Request $request, $class, $term)
    {
        $this->validate($request, ['idno' => 'required']);
        $des_idno = Des::encode($request->idno, 'KLKLKL'); 
        $t04tb_info = compact(['class', 'term']);
        $checkExsit = $this->siteReviewService->checkIdnoExsit($t04tb_info, $request->idno);

        if ($checkExsit){
            return back()->with('result', 0)->with('message', '該身分證已存在')->withInput();
        }

        return redirect("admin/site_review/create/{$class}/{$term}/{$des_idno}");
    }

    public function store(Request $request, $class, $term)
    {
        $this->validate($request, [
            'idno' => 'required',
            'cname' => 'required',
            'birth' => 'required'
        ]);

        $t39tb = $request->only([
            'idno',
            'cname',
            'race',
            'birth',
            'fee',
            'logdate',
            'logtime',
            'reject',
            'ecode',
            'education',
            'offtela',
            'offtelb',
            'offtelc',
            'offfaxa',
            'offfaxb',
            'homtela',
            'homtelb',
            'mobiltel',
            'extranote',
            'position',
            'dept',
            'sex',
            'email',
            'source'
        ]);

        $t04tbKey = compact(['class', 'term']);
        $t39tbKey = $t04tbKey;
        $t39tbKey['idno'] = $t39tb['idno'];
        $t39tb = $this->siteReviewService->getT39tb($t39tbKey);
        $create = $this->siteReviewService->createT39tb($t04tbKey, $t39tb);

        if ($create['status'] == 'repeat'){
            return back()->with('result', 0)->with('message', '該學員已存在')->withInput();
        }elseif ($create['status'] === true){
            return back()->with('result', 1)->with('message', '儲存成功');
        }else{
            return back()->with('result', 0)->with('message', '儲存失敗');
        }
    }

    public function edit($class, $term, $des_idno)
    {
        $idno = Des::decode($des_idno, 'KLKLKL');
        $t39tb_info = compact(['class', 'term', 'idno']);
        $t39tb = $this->siteReviewService->getT39tb($t39tb_info);
        $t04tb = $t39tb->t04tb;

        $t39tb_fields = config('database_fields.t39tb');
        $m02tb_fields = config('database_fields.m02tb');

        return view('admin/site_review/form', compact(['t04tb', 't39tb', 't39tb_fields', 'm02tb_fields']));
    }

    public function update(Request $request, $class, $term, $des_idno)
    {
        $this->validate($request, [
            'cname' => 'required',
            'birth' => 'required'
        ]);

        $idno = Des::decode($des_idno, 'KLKLKL');
        $t39tb_info = compact(['class', 'term', 'idno']);
        $t39tb = $request->only([
            'cname',
            'race',
            'birth',
            'fee',
            'logdate',
            'logtime',
            'reject',
            'ecode',
            'education',
            'offtela',
            'offtelb',
            'offtelc',
            'offfaxa',
            'offfaxb',
            'homtela',
            'homtelb',
            'mobiltel',
            'extranote',
            'position',
            'chk1',
            'chk2',
            'chk3',
            'chk4',
            'dept',
            'sex',
            'email'
        ]);


        $t39tb = $this->siteReviewService->getT39tb($t39tb_info);
        $updateResult = $this->siteReviewService->updateT39tb($t39tb_info, $newT39tb, $t39tb);

        if ($updateResult['status'] === true){
            return back()->with('result', 1)->with('message', '儲存成功');
        }else{
            return back()->with('result', 0)->with('message', '儲存失敗');
        }

    }

    public function updateProve(Request $request, $class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $review = $this->siteReviewService->review($t04tb_info, $request->prove);

        if ($review){
            return back()->with('result', 1)->with('message', '儲存完成');
        }else{
            return back()->with('result', 1)->with('message', '儲存失敗');
        }
    }

    public function checkCondition($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->siteReviewService->getT04tb($t04tb_info);
        $check_result = $this->siteReviewService->checkCondition($t04tb);
        $t39tb_fields = config('database_fields.t39tb');
        return view('admin/site_review/check_condition', compact('t04tb', 'check_result', 't39tb_fields'));
    }

    public function filterStudent($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->siteReviewService->getT04tb($t04tb_info);
        $check_result = $this->siteReviewService->checkCondition($t04tb);
        $check_result = collect($check_result)->groupBy('condition');
        $status = $this->siteReviewService->setCondition($t04tb_info, $check_result);
        if ($status){
            return back()->with('result', 1)->with('message', '篩選完成');
        }else{
            return back()->with('result', 1)->with('message', '篩選失敗');
        }
    }

    public function importApplyData(Request $request, $class, $term)
    {
        $t04tbKey = compact(['class', 'term']);

        if ($request->hasFile('import_file')){
            $applyData = Excel::load($request->file('import_file'))->sheet(0)->toArray();
            $newT39tbs = $this->siteReviewService->parseImportApplyData($applyData);
            $importResult = $this->siteReviewService->importT39tbs($t04tbKey, $newT39tbs);
            // dd($importResult);
            if ($importResult){
                return back()->with('result', 1)->with('message', '匯入成功');
            }else{
                return back()->with('result', 0)->with('message', '匯入失敗');
            }

            
        }else{
            return back()->with('result', 0)->with('message', '請選擇匯入檔案');
        }
    }
}
