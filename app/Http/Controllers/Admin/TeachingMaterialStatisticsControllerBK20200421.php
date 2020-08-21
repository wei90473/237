<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MethodService;
use App\Models\T49tb;
use App\Models\T50tb;
use App\Models\T67tb;
use App\Models\S04tb;
use App\Models\S06tb;
use DB;

class TeachingMaterialStatisticsController extends Controller
{
    /**
     * TeachingMaterialStatisticsController constructor.
     * @param MethodService $methodService
     */
    public function __construct(MethodService $methodService)
    {
        $this->methodService = $methodService;
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
        // 委訓機關
        $queryData['commission'] = $request->get('commission');
        // 訓練性質
        $queryData['traintype'] = $request->get('traintype');
        // 班別性質
        $queryData['type'] = $request->get('type');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        if(empty($request->all())) {
            $queryData['choices'] = $this->_get_year_list();
            return view('admin/teaching_material_statistics/list', compact('queryData'));
        }

        $data = $this->methodService->getClassList($queryData);
        // $ranklist = $this->classesService->getClassesList(array('yerly'=>$queryData['yerly'],'_paginate_qty'=>'999'));
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/teaching_material_statistics/list', compact('data', 'queryData'));
    }


    /**
     * 清單頁
     *
     * @param $class_term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request,$class_term){
        $term = $queryData['term'] = substr($class_term, -2); 
        $class = $queryData['class'] = substr($class_term, 0,-2);
        // 預定交貨月份
        $queryData['duedate'] = $request->get('duedate');
        // 支付月份
        $queryData['paiddate'] = $request->get('paiddate');
        // 支付選項
        $queryData['ispaid'] = $request->get('ispaid');
        $materialdata = $this->methodService->getMaterialList($queryData);
        // var_dump($data);exit();
        $data = $this->methodService->getClassList(array('term'=>$term,'class'=>$class));
        $data = $data[0];
        
        // $data = T49tb::select('serno','material','total','applicant','copy')->where('class', $class)->where('term', $term)->get()->toarray();
        
        return view('admin/teaching_material_statistics/form', compact('queryData', 'materialdata','data'));
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
        // 院區差異未確定 先不分區
        // if($queryData->branch==1){ //台北

        // }elseif($queryData->branch==2){//南投

        // }else{
        //     return view('admin/errors/error');
        // }
        if($serno==''){  //新增
            $queryData->maxserno = (T49tb::max('serno')+1);
            $datalist = S04tb::orderby('sequence')->get()->toarray();
            $datalist = $this->getType($datalist);
            return view('admin/teaching_material_statistics/edit', compact('queryData', 'datalist','kindlist'));
        }else{//編輯   
            $data = T49tb::where('serno',$serno)->first();
            $data['branch'] = is_null($data['branch'])? $queryData->branch:$data['branch'];

            $datalist = T50tb::where('serno',$serno)->orderby('sequence')->get()->toarray();
            $datalist = $this->getType($datalist);
            return view('admin/teaching_material_statistics/edit', compact('queryData', 'datalist','kindlist','data')); 
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
     * 更新單價
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upprice($class_term_serno){
        $class = substr($class_term_serno, 0,6);
        $term  = substr($class_term_serno, 6,2); 
        $serno = substr($class_term_serno, 8);
        DB::beginTransaction();
        try{
            T49tb::where('serno',$serno)->update(array('total' =>'0' ));
            T67tb::where('serno',$serno)->update(array('fee' =>'0' ));
            T50tb::where('serno',$serno)->delete();
            $datalist = S04tb::orderby('sequence')->get()->toarray();
            foreach ($datalist as $key => $value) {
                T50tb::create(array('serno' =>$serno,
                                'sequence'  =>$value['sequence'],
                                'item'      =>$value['item'],
                                'unit'      =>$value['unit'],
                                'price'     =>$value['price'],
                                'type'      =>$value['type'],
                                'remark'    =>$value['remark'],
                                'quantity'  =>'0',
                                'copy'      =>'0'));
            }
            
            DB::commit();
            return redirect('/admin/teaching_material_statistics/list/'.$class.$term)->with('result', '1')->with('message', '更新成功!');
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
    public function update(Request $request)    {
        $data = $request->all();
        $check = T49tb::select('paiddate')->where('serno', $data['serno'])->first();
        if($check['paiddate']=='' || $check['paiddate']){
            return back()->with('result', '0')->with('message', '此筆資料已支付，不可修改');
        }
        unset($data['_method'],$data['_token']);
        DB::beginTransaction();
        try{
            $datalist = T50tb::where('serno', $data['serno'])->get()->toarray();
            $totalpay = 0;
            foreach ($datalist as $key => $value) {
                T50tb::where('sequence',$value['sequence'])->update(array('quantity'=>$data['quantity'.$value['sequence']],
                                                                          'copy'=>$data['copy'.$value['sequence']] ));
                $totalpay = $totalpay + ($value['price']*$data['quantity'.$value['sequence']]*$data['copy'.$value['sequence']]);
                unset($data['quantity'.$value['sequence']],$data['copy'.$value['sequence']]);
            }
            $data['total'] = $totalpay;
            T49tb::where('serno', $data['serno'])->update($data);
            DB::commit();
            return back()->with('result', '1')->with('message', '儲存成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '新增失敗，請稍後再試!'); 
        } 
    }

    /**
     * 刪除處理(X)
     *
     * @param $classes_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($class_term_serno){
        exit();
        $class = $queryData['class'] = substr($class_term_serno, 0,6);
        $term = $queryData['term'] = substr($class_term_serno, 6,2); 
        $serno = substr($class_term_serno, 8);
        $check = T49tb::select('paiddate')->where('serno', $serno)->first();
        if($check['paiddate']=='' || is_null($check['paiddate'])){
            DB::beginTransaction();
            try{
                T49tb::where('serno', $serno)->delete();
                T50tb::where('serno', $serno)->delete();
                T67tb::where('serno', $serno)->delete();
                DB::commit();
                return redirect('/admin/teaching_material_statistics/list/'.$class.$term)->with('result', '1')->with('message', '刪除成功!');
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
