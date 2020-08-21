<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TrainQuestSettingService;
use App\Services\Term_processService;
use App\Services\User_groupService;

use App\Models\S01tb;
use App\Models\M09tb;

use DateTime;


/*
    訓前訓中訓後設定
*/
class TrainQuestSettingContrller extends Controller
{
    /**
     * TrainQuestSettingContrller constructor.
     * @param TrainQuestSettingService $trainQuestSettingService
     */
    public function __construct(TrainQuestSettingService $trainQuestSettingService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        $this->trainQuestSettingService = $trainQuestSettingService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('train_quest_setting', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    public function index(Request $request)
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
            $data = $this->trainQuestSettingService->getOpenClassList($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData['t01tb']['class'] = $sess['class'];
                $queryData['t04tb']['term'] = $sess['term'];
                $queryData['t01tb']['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->trainQuestSettingService->getOpenClassList($queryData);
            }
        }

        return view('admin/train_quest_setting/index', compact('data', 'queryData', 'sponsors', 's01tbM'));
    }

    public function setting($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->trainQuestSettingService->getClass($t04tb_info);
        $data = $this->trainQuestSettingService->getTrainQuestSettings($class, $term);
        $queryData = [];
        return view('admin/train_quest_setting/setting', compact(['t04tb', 'data', 'queryData']));
    }

    public function create($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->trainQuestSettingService->getClass($t04tb_info);
        return view('admin/train_quest_setting/form', compact(['t04tb']));
    }

    public function edit($id)
    {
        $train_quest_setting = $this->trainQuestSettingService->getTrainQuestSetting($id);

        if (empty($train_quest_setting)){
            return redirect('admin/trainQuestSetting');
        }

        $t04tb = $train_quest_setting->t04tb;
        return view('admin/train_quest_setting/form', compact(['train_quest_setting', 't04tb']));
    }

    public function store(Request $request, $class, $term)
    {

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('trainQuestSetting', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法新增');
        }

        $rules = [
            'quest_type' => 'required'
        ];

        $customMessages = [
            'quest_type.required' => '問卷種類尚未選擇',
        ];

        $validatedData = $this->validate($request, $rules, $customMessages);

        $input = $request->only([
            "quest_url",
            "quest_type"
        ]);
        $train_quest_setting = [
            "class" => $class,
            "term" => $term,
            "url" => $input['quest_url'],
            "type" => $input['quest_type']
        ];
        $quest_setting = $this->trainQuestSettingService->createtrainQuestSetting($train_quest_setting);
        $answer_file = $request->file('answer_file');
        $question_file = $request->file('question_file');
        $this->trainQuestSettingService->uploadQuestionnaire($quest_setting, $question_file, $answer_file);
        return redirect("/admin/trainQuestSetting/setting/{$class}/{$term}")->with('result', '1')->with('message', '新增成功');
    }

    public function update(Request $request, $id)
    {
        $rules = [
            // 'quest_url' => 'required',
            'quest_type' => 'required'
        ];

        $customMessages = [
            // 'quest_url.required' => 'Google網址欄位不可為空',
            'quest_type.required' => '問卷種類尚未選擇',
        ];

        $validatedData = $this->validate($request, $rules, $customMessages);

        $input = $request->only([
            "quest_url",
            "quest_type"
        ]);

        $quest_setting = $this->trainQuestSettingService->getTrainQuestSetting($id);

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('trainQuestSetting', $quest_setting->class, $quest_setting->term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $quest_setting->url = $input['quest_url'];
        $quest_setting->type = $input['quest_type'];
        $quest_setting->save();

        $answer_file = $request->file('answer_file');
        $question_file = $request->file('question_file');
        $this->trainQuestSettingService->uploadQuestionnaire($quest_setting, $question_file, $answer_file);
        return back()->with('result', '1')->with('message', '儲存成功');
    }

    public function delete($id)
    {
        $quest_setting = $this->trainQuestSettingService->getTrainQuestSetting($id);
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('trainQuestSetting', $quest_setting->class, $quest_setting->term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法刪除');
        }
        $this->trainQuestSettingService->deleteTrainQuestionnaire($id);
        return back()->with('result', '1')->with('message', '刪除成功');
    }

    public function deleteSetting($id)
    {
        $quest_setting = $this->trainQuestSettingService->getTrainQuestSetting($id);
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('trainQuestSetting', $quest_setting->class, $quest_setting->term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法刪除');
        }
        $train_quest_setting = $this->trainQuestSettingService->deleteTrainQuestSetting($id);
        return redirect("/admin/trainQuestSetting/setting/{$train_quest_setting->class}/{$train_quest_setting->term}")->with('message', '刪除成功');
    }
}