<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MethodService;
use App\Services\User_groupService;
use App\Models\T49tb;
use App\Models\T50tb;
use App\Models\T67tb;
use App\Models\S04tb;
use App\Models\S06tb;
use DB;

class TeachingMaterialMaintainController extends Controller
{
    /**
     * TeachingMaterialMaintainController constructor.
     * @param MethodService $methodService
     */
    public function __construct(MethodService $methodService, User_groupService $user_groupService)
    {
        $this->methodService = $methodService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('teaching_material_maintain', $user_group_auth)){
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
    public function index(Request $request){
        // 辦班院區
        $branch = is_null($request->get('branch'))? '1':$request->get('branch');
        $queryData = (object)['branch'=>$branch];
        $typeA = array('一、','二、','三、','四、','五、','六、','七、','八、','九、','十、');
        $typeB = array('(一)','(二)','(三)','(四)','(五)','(六)','(七)','(八)','(九)','(十)');
        $typeC = array('1','2','3','4','5','6','7','8','9');
        $datalist = S04tb::where('branch',$branch)->orderby('sequence')->get()->toarray();
        // var_dump($datalist);exit();
        $i=0;
        $rankA=0;
        $rankB=0;
        $rankC=0;
        $check=1;
        $type = '';
        foreach ($datalist as $key => $value) {
            if($value['type']=='A'){
                $datalist[$i]['title'] = $typeA[$rankA];
                $rankA ++;
            }elseif($value['type']=='B' && $check==$rankA){
                $datalist[$i]['title'] = $typeB[$rankB];
                $rankB ++;
            }elseif($value['type']=='B' && $check!=$rankA){
                $rankB = 0;
                $datalist[$i]['title'] = $typeB[$rankB];
                $check = $rankA;
                $rankB ++;
            }elseif($value['type']=='C' && $value['type']==$type){
                $datalist[$i]['title'] = $typeC[$rankC];
                $rankC ++;
            }else{
                $rankC = 0;
                $datalist[$i]['title'] = $typeC[$rankC];
                $rankC ++;
            }
            $type = $value['type'];
            $i++;
        }
        return view('admin/teaching_material_maintain/edit', compact('datalist','queryData'));
    }
    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request,$branch)    {
        $data = $request->all();
        unset($data['_token']);
        $data['price'] = isset($data['price'])? $data['price'] : '0';
        $data['sequence'] = (S04tb::where('branch',$branch)->max('sequence'))+1;
        $data['branch'] = $branch;
        S04tb::create($data);
        return back()->with('result', '1')->with('message', '新增成功!');

    }
    //排序

    public function ChangeSort(Request $request,$branch)    {
        $data = $request->all();
        unset($data['_token'],$data['_method']);
        $sequence =1;
        DB::beginTransaction();
        try{
            foreach ($data as $key => $value) {
                S04tb::where('branch',$branch)->where('serno',$key)->update(array('sequence'=>$sequence));
                $sequence++;
            }
            DB::commit();
            return back()->with('result', '1')->with('message', '儲存成功');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '排序失敗，請稍後再試!');
        }
    }
    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request,$branch)    {
        $data = $request->all();
        DB::beginTransaction();
        try{
            S04tb::where('branch',$branch)->delete();
            for($i=0;$i<count($data['serno']);$i++){
                S04tb::create(array('serno' =>$data['serno'][$i],
                                    'item'  =>$data['item'][$i],
                                    'unit'  =>$data['unit'][$i],
                                    'type'  =>$data['type'][$i],
                                    'price' =>$data['price'][$i],
                                    'remark'=>$data['remark'][$i],
                                    'branch'=>$branch,
                                    'sequence'=>($i+1)));
            }
            DB::commit();
            return back()->with('result', '1')->with('message', '儲存成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '新增失敗，請稍後再試!');
        }
    }

    /**
     * 刪除處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request,$branch)    {
        $data = $request->all();
        // var_dump($data);exit();
        DB::beginTransaction();
        try{
            S04tb::where('branch',$branch)->where('serno', $data['serno'])->delete();
            DB::commit();
            return back()->with('result', '1')->with('message', '刪除成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '刪除失敗，請稍後再試!');
        }
    }

    public function _get_year_list()
    {
        $year_list = array();
        $year_now = date('Y');
        $this_yesr = $year_now - 1910;

        for($i=$this_yesr; $i>=90; $i--){
            $year_list[$i] = $i;
        }
        // jd($year_list,1);
        return $year_list;
    }
}
