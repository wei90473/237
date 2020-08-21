<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MethodService;
use App\Services\User_groupService;
use App\Services\Term_processService;
use App\Models\T04tb;
use App\Models\T49tb;
use App\Models\T50tb;
use App\Models\T67tb;
use App\Models\S04tb;
use App\Models\S06tb;
use App\Models\T06tb;
use DB;

class TeachingMaterialPrintController extends Controller
{
    /**
     * TeachingMaterialPrintController constructor.
     * @param MethodService $methodService
     */
    public function __construct(MethodService $methodService, User_groupService $user_groupService, Term_processService $term_processService)
    {
        $this->methodService = $methodService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('teaching_material_print', $user_group_auth)){
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
        $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // 班號
        $queryData['class'] = $request->get('class');
        // 辦班院區
        $queryData['branch'] = $request->get('branch');
        // 班別名稱
        $queryData['name'] = $request->get('name');
        // 分班名稱
        $queryData['branchname'] = $request->get('branchname');
        // 期別
        $queryData['term'] = $request->get('term');
        // 班別類型
        $queryData['process'] = $request->get('process');
        // 訓練性質
        $queryData['traintype'] = $request->get('traintype');
        // 班別性質
        $queryData['type'] = $request->get('type');
        // 類別1
        $queryData['categoryone'] = $request->get('categoryone');
        //開訓日期
        $queryData['sdate'] = $request->get('sdate');
        $queryData['edate'] = $request->get('edate');
        $queryData['sdate2'] = $request->get('sdate2');
        $queryData['edate2'] = $request->get('edate2');
        $queryData['sdate3'] = $request->get('sdate3');
        $queryData['edate3'] = $request->get('edate3');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        if(empty($request->all())) {
            $queryData['choices'] = $this->_get_year_list();
            $sess = $request->session()->get('lock_class');
            if($sess){
              $queryData2['class'] = $sess['class'];
              $queryData2['term'] = $sess['term'];
              $queryData2['yerly'] = substr($sess['class'], 0, 3);
              $data = $this->methodService->getClassList($queryData2);
              return view('admin/teaching_material_print/list', compact('data', 'queryData'));
            }
            return view('admin/teaching_material_print/list', compact('queryData'));
        }

        $data = $this->methodService->getClassList($queryData);
        // $ranklist = $this->classesService->getClassesList(array('yerly'=>$queryData['yerly'],'_paginate_qty'=>'999'));
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/teaching_material_print/list', compact('data', 'queryData'));
    }


    /**
     * 清單頁
     *
     * @param $class_term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list($class_term){
        $term = $queryData['term'] = substr($class_term, -2);
        $class = $queryData['class'] = substr($class_term, 0,-2);
        $queryData = $this->methodService->getClassList($queryData);
        $queryData = $queryData[0];
        $data = T49tb::select('serno','material','copy','applicant')->where('class', $class)->where('term', $term)->get()->toarray();

        return view('admin/teaching_material_print/form', compact('queryData', 'data'));
    }

    /**
     * 編輯頁
     *
     * @param $class_term_serno
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($class_term_serno){
        $class = $queryData['class'] = substr($class_term_serno, 0,6);
        $term = $queryData['term'] = substr($class_term_serno, 6,2);
        $queryData = $this->methodService->getClassList($queryData);
        $queryData = $queryData[0];
        $serno = substr($class_term_serno, 8);
        // 開支科目
        $kindlist = S06tb::select('acccode','accname')->where('yerly',substr($class, 0,3))->get()->toarray();
        // 教材名稱列表
        $material = T06tb::select('term','course','name')->where('class',$class)->orderby('term','course')->get()->toarray();

        if(count($material)<1) $material[0]['term'] = '';

        if($serno==''){  //新增
            $queryData->maxserno = (T49tb::max('serno')+1);
            $Taipeilist = S04tb::where('branch','1')->orderby('sequence')->get()->toarray();
            $Nantoulist = S04tb::where('branch','2')->orderby('sequence')->get()->toarray();
            $datalist[1] = $this->getType($Taipeilist);
            $datalist[2] = $this->getType($Nantoulist);
            return view('admin/teaching_material_print/edit', compact('queryData', 'datalist','kindlist','material'));
        }else{//編輯
            $data = T49tb::where('serno',$serno)->first();
            $data['branch'] = is_null($data['branch'])? $queryData->branch:$data['branch'];
            $t04data = T04tb::select('invoice')->where('class',$data['class'])->where('term',$data['term'])->first();
            $data['invoice'] = $t04data['invoice'];
            $datalist = T50tb::where('serno',$serno)->orderby('sequence')->get()->toarray();
            $datalist = $this->getType($datalist);
            return view('admin/teaching_material_print/edit', compact('queryData', 'datalist','kindlist','data','material'));
        }
    }
    private function getType($datalist){
        $typeA = array('一、','二、','三、','四、','五、','六、','七、','八、','九、','十、');
        $typeB = array('(一)','(二)','(三)','(四)','(五)','(六)','(七)','(八)','(九)','(十)');
        $typeC = array('1','2','3','4','5','6','7','8','9');
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
        return $datalist;
    }
    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)    {

        // 取得POST資料
        $data = $request->all();

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('teaching_material_print', $data['class'], $data['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法新增');
        }

        DB::beginTransaction();
        try{
            T49tb::create(array('serno'     =>$data['serno'],
                                'material'  =>$data['material'],
                                'class'     =>$data['class'],
                                'term'      =>$data['term'],
                                'branch'    =>$data['branch'],
                                'typing'    =>isset($data['typing'])?'Y':'N',
                                'bind'      =>isset($data['bind'])?'Y':'N',
                                'punch'     =>isset($data['punch'])?'Y':'N',
                                'fast'      =>isset($data['fast'])?'Y':'N',
                                'copy'      =>$data['copy'],
                                'page'      =>'0',
                                'print'     =>$data['print'],
                                'duedate'   =>$data['duedate'],
                                'duetime'   =>$data['duetime'],
                                'applicant' =>$data['applicant'],
                                'kind'      =>$data['kind'],
                                'client'    =>$data['client'],
                                'extranote' =>$data['extranote']
                                ));
            T04tb::where('class',$data['class'])->where('term',$data['term'])
            	->update(array( 'kind'      =>$data['kind'],
                                'client'    =>$data['client'],
                            	'invoice'   =>$data['invoice']));
            $datalist = S04tb::where('branch',$data['branch'])->orderby('sequence')->get()->toarray();
            foreach ($datalist as $key => $v) {
                $array = array('serno'     =>$data['serno'],
                               'sequence'  =>$v['sequence'],
                               'item'      =>$v['item'],
                               'unit'      =>$v['unit'],
                               'price'     =>$v['price'],
                               'quantity'  =>'0',
                               'copy'      =>'0',
                               'type'      =>$v['type'],
                               'remark'    =>$data['remark'.$v['sequence']] );
                T50tb::create($array);
            }

            T67tb::create(array('serno'     =>$data['serno'],
                                'class'     =>$data['class'],
                                'term'      =>$data['term'],
                                'fee'       =>'0' ));
            DB::commit();
            return redirect('/admin/teaching_material_print/edit/'.$data['class'].$data['term'].$data['serno'])->with('result', '1')->with('message', '新增成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '新增失敗，請稍後再試!');
        }
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $class)    {

        $data = $request->all();

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('teaching_material_print', $data['class'], $data['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法更新');
        }

        $check = T49tb::select('paiddate')->where('serno', $data['serno'])->first();
        if($check['paiddate']!='' || !is_null($check['paiddate']) ){
            return back()->with('result', '0')->with('message', '此筆資料已支付，不可修改');
        }
        unset($data['_method'],$data['_token']);
        DB::beginTransaction();
        try{

            $datalist = T50tb::where('serno', $data['serno'])->get()->toarray();
            // $totalpay = 0;
            foreach ($datalist as $key => $value) {
                T50tb::where('sequence',$value['sequence'])->update(array('remark'=>$data['remark'.$value['sequence']] ));
                unset($data['remark'.$value['sequence']]);
            }

            T04tb::where('class',$data['class'])->where('term',$data['term'])
            	->update(array( 'kind'      =>$data['kind'],
                                'client'    =>$data['client'],
                            	'invoice'   =>$data['invoice']));
        	unset($data['invoice']);
            T49tb::where('serno', $data['serno'])->update($data);
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
     * @param $classes_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($class_term_serno){

        $class = $queryData['class'] = substr($class_term_serno, 0,6);
        $term = $queryData['term'] = substr($class_term_serno, 6,2);
        $serno = substr($class_term_serno, 8);

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('teaching_material_print', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法刪除');
        }

        $check = T49tb::select('paiddate')->where('serno', $serno)->first();
        if($check['paiddate']=='' || is_null($check['paiddate'])){
            DB::beginTransaction();
            try{
                T49tb::where('serno', $serno)->delete();
                T50tb::where('serno', $serno)->delete();
                T67tb::where('serno', $serno)->delete();
                DB::commit();
                return redirect('/admin/teaching_material_print/list/'.$class.$term)->with('result', '1')->with('message', '刪除成功!');
            }catch ( Exception $e ){
                DB::rollback();
                return back()->with('result', '0')->with('message', '刪除失敗，請稍後再試!');
            }
        }else{
            return back()->with('result', '0')->with('message', '此筆資料已支付，不可刪除');
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
