<?php
namespace App\Http\Controllers\Admin;
use App\Services\User_groupService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TeachingmethodRepository;
use App\Models\method;
use App\Helpers\ModifyLog;
use DB;


class TeachingmethodController extends Controller
{
    /**
     * TeachingmethodController constructor.
     * @param TeachingmethodRepository $TeachingmethodRepository
     */
    public function __construct(TeachingmethodRepository $teachingmethodRepository, User_groupService $user_groupService)
    {
        $this->teachingmethodRepository = $teachingmethodRepository;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('teachingmethod', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('teachingmethod');
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 教法名稱
        $queryData['name'] = $request->get('name');
        // 狀態
        $queryData['mode'] = $request->get('mode');
        $year = date("Y")-1911;

        //創建年度
        $queryData['yerly'] = is_null($request->get('yerly') )? $year: $request->get('yerly');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 10;
      

        // 取得列表資料
        $data = $this->teachingmethodRepository->getList($queryData);
    

        return view('admin/teaching_method/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin/teaching_method/form');
    }
    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 取得POST資料
        $data = $request->all();
        // dd($data);
        unset($data['_token']);
        $name_array = explode('_',$data['name']);
        // $method = method::select('method')->orderby('id','DESC')->first();
        // $data['method'] = 'M'.(substr($method['method'],1)+1);
        $data['method'] = $name_array[0];
        $year = date("Y");
        $data['yerly'] = $year-1911;
        $data['modifytime'] =  date('Y-m-d H:i:s');
        //新增
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        $result = method::create($data);
        $sql = DB::getQueryLog();
        $nowdata = method::where('yerly',$data['yerly'])->where('method',$data['method'])->get()->toarray();
        createModifyLog('I','method','',$nowdata,end($sql));

        return redirect('/admin/teachingmethod/'.$result->id)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $classes_id
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * 編輯頁
     *
     * @param $classes_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {

        $data = method::where('id', $id)->first();

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/teaching_method/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {

        // 取得POST資料
        $data = $request->all();
        unset($data['_method'], $data['_token']);
        $name_array = explode('_',$data['name']);
        $data['method'] = $name_array[0];
        $data['modifytime'] = date('Y-m-d H:i:s');
        //更新
        $olddata = method::where('id', $id)->get()->toarray();
        
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        method::where('id', $id)->update($data);
        $sql = DB::getQueryLog();
        $nowdata = method::where('id', $id)->get()->toarray();
        createModifyLog('U','method',$olddata,$nowdata,end($sql));
        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    /**
     * 刪除處理
     *
     * @param $classes_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($id) {
            DB::connection()->enableQueryLog(); //啟動SQL_LOG
            $olddata = method::where('id', $id)->get()->toarray();
            method::where('id', $id)->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','method',$olddata,'',end($sql));
            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
    public function _get_year_list()
    {
        $year_list = array();
        $year_now = date('Y');
        $this_yesr = $year_now - 1911;

        for($i=$this_yesr; $i>=90; $i--){
            $year_list[$i] = $i;
        }
        // jd($year_list,1);
        return $year_list;
    }
}
