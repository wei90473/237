<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassesService;
use App\Services\User_groupService;
use App\Models\Classes;
use App\Models\Class_group;
use App\Models\S01tb;
use App\Models\S03tb;
use DB;

class WebPortalController extends Controller
{
    /**
     * WebPortalController constructor.
     * @param ClassesService $classesService
     */
    public function __construct(ClassesService $classesService, User_groupService $user_groupService)
    {
        $this->classesService = $classesService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('web_portal', $user_group_auth)){
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
        // 年度
        // $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // 學院代碼
        $queryData['code'] = $request->get('code');
        // 學院專長
        $queryData['name'] = $request->get('name');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        if(empty($request->all())) {
            return view('admin/web_portal/list', compact('queryData'));
        }

        $data = $this->classesService->getWebPortal($queryData);
        return view('admin/web_portal/list', compact('data', 'queryData'));
    }

    /**
     * 編輯頁
     *
     * @param $code
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($code)
    {
        $queryData['code'] = $code;
        $data = $this->classesService->getWebPortal($queryData);
        if ( ! $data) {
            return view('admin/errors/error');
        }
        $data = $data['0'];
        // 取得班別類別
        $classCategory = $this->getClassCategory();
        return view('admin/web_portal/form', compact('data', 'classCategory'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $code)
    {
        // 取得POST資料
        $data = $request->all();
        DB::beginTransaction();
        try{
            S01tb::where('code',$data['code'])->where('type','B')->update(array('category'=>$data['category']));
            DB::commit();
            return back()->with('result', '1')->with('message', '儲存成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '更新失敗，請稍後再試!');
        }

    }


    /**
     * 班別類別/專長代碼維護
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function category(){
        // 取得班別類別
        $category = S03tb::where('serno','<>','')->orderby('sequence')->get()->toarray();

        return view('admin/web_portal/category', compact('category'));
    }


    /**排序調整**/
    public function rank(Request $request){
        $data = $request->all();
        // var_dump($data);exit();
        unset($data['_token'],$data['_method']);
        if(count($data)>0){
            foreach ($data['serno'] as $key => $value) {
                S03tb::where('serno',$value)->update(array('indent'=>$data['indent'][$key],'sequence'=>$key+1) );
            }
            return back()->with('result', '1')->with('message', '儲存成功!');
        }else{
            return back()->with('result', '0')->with('message', '查無資料!');
        }
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
        // 組成班級編號(邏輯上有問題)
        unset($data['_token']);

        $sequence = S03tb::max('sequence');
        $data['sequence'] = $sequence +1;
        $serno = S03tb::max('serno');
        $data['serno'] = $serno +1;
        $data['indent'] = '1';
        $data['category'] = isset($data['category'])? $data['category']:'';
        DB::beginTransaction();
        try{
            S03tb::create($data);
            DB::commit();
            return back()->with('result', '1')->with('message', '新增成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '新增失敗，請稍後再試!');
        }
    }

    /**
     * 更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function categoryupdate(Request $request,$serno){
        // 取得POST資料
        $data = $request->all();
        $check = S03tb::where('serno', $data['E_serno'])->first();
        if($check){
            S03tb::where('serno', $data['E_serno'])->update(array('name'=>$data['E_name'],
                                                                  'alias'=>$data['E_alias'],
                                                                  'category'=>$data['E_category'] ) );
            return back()->with('result', '1')->with('result', '1')->with('message', '更新成功!');
        }else{
            return back()->with('result', '0')->with('message', '更新失敗!');
        }
    }
    /**
     * 刪除處理
     *
     * @param $serno
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($serno){
        if ($serno) {
            S03tb::where('serno', $serno)->delete();
            return back()->with('result', '1')->with('result', '1')->with('message', '刪除成功!');
        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }


    // 共用查詢 取得『班別類別』 資料
    function getClassCategory() {
        $data = DB::select("SELECT serno, indent, CONCAT(name, ' ', category) as name, category, sequence FROM s03tb order by sequence+0");

        return $data;
    }


     // 共用查詢 取得『參訓機關-限定機關』資料
     function getOrgchk(Request $request=null, $type='new', $class='xxxxxx') {
        if($type == 'edit') {
            $year = substr($class, 0, 3);
        }
        else if($type == 'new') {
            $year = date('Y')-1911;
        }

        if(isset($request)) {
            $queryData = $request->all();
            // dd($queryData['class']);
            // $year = $queryData['year'];
            $class = $request->class;
       }

        $data[0] = DB::select("SELECT
                                   A.enrollorg,
                                   RTRIM( A.enrollname ) AS enrollname
                               FROM
                                   m17tb A
                                   INNER JOIN m13tb B ON A.organ = B.organ
                               WHERE
                                   A.grade = '1'
                                   AND B.kind = 'Y'
                                   AND  NOT EXISTS ( SELECT NULL
                                                     FROM t82tb
                                                     WHERE class = '{$class}' AND organ = A.enrollorg )
                               ORDER BY
                                   B.rank");

        $data[1] = DB::select("SELECT A.enrollorg, RTRIM(A.enrollname) AS enrollname
                               FROM m17tb A
                               INNER JOIN t82tb B ON A.enrollorg = B.organ
                               LEFT JOIN m13tb C ON A.organ = C.organ
                               WHERE B.class = '{$class}'
                               GROUP BY A.enrollorg,A.enrollname,C.rank
                               ORDER BY C.rank");

        return $data;
    }

    // 取得『相同課程』資料
    // function getSameCourseList(Request $request=null) {
    //     $queryData = $request->all();
    //     $startClass = $queryData['startClass']!=''?$queryData['startClass']:'000000';
    //     $endClass = $queryData['endClass']!=''?$queryData['endClass']:'ZZZZZZ';
    //     $sameCourse = $queryData['sameCourse']!=''?$queryData['sameCourse']:'000000,';

    //     if(strlen($startClass) < 6) {
    //         $countLen = strlen($startClass);
    //         for($i=0; $i<6-$countLen; $i++) {
    //             $startClass .= '0';
    //         }
    //     }
    //     if(strlen($endClass) < 6) {
    //         $countLen = strlen($endClass);
    //         for($i=0; $i<6-$countLen; $i++) {
    //             $endClass .= 'Z';
    //         }
    //     }

    //     $sameCourse = substr($sameCourse,0,-1);

    //     $data[] = DB::select("SELECT class, name FROM t01tb where left(class,6) between '{$startClass}' and '{$endClass}' AND class NOT IN ({$sameCourse}) ");

    //     $data[] = DB::select("SELECT class, name FROM t01tb where class in ({$sameCourse})");

    //     return $data;
    // }

    // 新增『參訓機關』資料
    function setOrgchk($type, $class, $tempOrgchk) {
        $data = explode(",", substr($tempOrgchk,0,-1));

        if($type == 'update') {
            DB::delete("DELETE FROM t82tb WHERE class='".$class."'");
        }

        for($i=0; $i<sizeof($data); $i++) {
            DB::insert("INSERT INTO t82tb (class, organ) VALUES ('".$class."','".$data[$i]."')");
        }
    }
    // 設定『相同課程』資料
    private function setSamecourse($data=array()){
        if($data['samecourse']!=''){
            $class_group = Class_group::select('class_group')->where('groupid',$data['samecourse'])->first();
            if(is_null($class_group)) return back()->with('result', '0')->with('message', '新增失敗，無此群組!');

            Class_group::create(array(  'groupid'       =>$data['samecourse'],
                                        'class_group'   =>$class_group['class_group'],
                                        'class'         =>$data['class'],
                                        'name'          =>$data['name'],
                                        'branchcode'    =>$data['branchcode']));
        }
    }
    private function input_csv($handle) {
        $out = array ();
        $n = 0;
        while ($data = fgetcsv($handle, 10000)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }
            $n++;
        }
        return $out;
}

    //確保輸出內容符合 CSV 格式，定義下列方法來處理
    private function csvstr(array $fields): string{
        $f = fopen('php://memory', 'r+');
        if (fputcsv($f, $fields) === false) {
            return false;
        }
        rewind($f);
        $csv_line = stream_get_contents($f);
        return rtrim($csv_line);
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
    private function __result( $code,$msg ){
        echo json_encode(array('status' => $code , 'msg' => $msg));
    exit;
  }
}
