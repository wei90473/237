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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Services\User_groupService;

class TeachingMaterialStatisticsController extends Controller
{
    /**
     * TeachingMaterialStatisticsController constructor.
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
            if(in_array('teaching_material_statistics', $user_group_auth)){
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
        // // 年度
        // $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // // 班號
        // $queryData['class'] = $request->get('class');
        // // 辦班院區
        // $queryData['branch'] = $request->get('branch');
        // // 班別名稱
        // $queryData['name'] = $request->get('name');
        // // 分班名稱
        // $queryData['branchname'] = $request->get('branchname');
        // // 期別
        // $queryData['term'] = $request->get('term');
        // // 班別類型
        // $queryData['process'] = $request->get('process');
        // // 委訓機關
        // $queryData['commission'] = $request->get('commission');
        // // 訓練性質
        // $queryData['traintype'] = $request->get('traintype');
        // // 班別性質
        // $queryData['type'] = $request->get('type');
        // // 排序欄位
        // $queryData['_sort_field'] = $request->get('_sort_field');
        // // 排序方向
        // $queryData['_sort_mode'] = $request->get('_sort_mode');
        // // 每頁幾筆
        // $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // // 取得列表資料
        // if(empty($request->all())) {
        //     $queryData['choices'] = $this->_get_year_list();
        //     $sess = $request->session()->get('lock_class');
        //     if($sess){
        //       $queryData2['class'] = $sess['class'];
        //       $queryData2['term'] = $sess['term'];
        //       $queryData2['yerly'] = substr($sess['class'], 0, 3);
        //       $data = $this->methodService->getClassList($queryData2);
        //       return view('admin/teaching_material_statistics/list', compact('data', 'queryData'));
        //     }
        //     return view('admin/teaching_material_statistics/list', compact('queryData'));
        // }

        // $data = $this->methodService->getClassList($queryData);
        // // $ranklist = $this->classesService->getClassesList(array('yerly'=>$queryData['yerly'],'_paginate_qty'=>'999'));
        // $queryData['choices'] = $this->_get_year_list();
        // return view('admin/teaching_material_statistics/list', compact('data', 'queryData'));
        //
        //
        //20200602
        // 預定交貨月份
        $queryData['date'] = $request->get('date');
        // 支付月份
        $queryData['paiddate'] = $request->get('paiddate');
        // 支付選項
        $queryData['ispaid'] = $request->get('ispaid');
        $queryData['monthType'] = $request->get('monthType');

        $queryData['_paginate_qty'] = 20;
        $queryData2 = $queryData;
        $allserno = '';
        $cakall = 'Y';
        $cakid = '';

        if(empty($request->all())) {
            $queryData2['duedate'] = '0000000';
            $data = $this->methodService->getMaterialListNew($queryData2);
        }else{
        	if($queryData['monthType'] == '2'){
        		$queryData['paiddate'] = $queryData['date'];
        		unset($queryData['duedate']);
        	}else{
        		$queryData['duedate'] = $queryData['date'];
        		unset($queryData['paiddate']);
        	}

            $data = $this->methodService->getMaterialListNew($queryData);
            foreach($data as $row){
            	if(empty($row->paiddate)){
            		$cakall = 'N';
            	}
            	if(!empty($row->paiddate)){
            		if(empty($cakid)){
            			$cakid = $row->serno;
            		}else{
            			$cakid .= '_'.$row->serno;
            		}
            	}
            	if(!empty($row->serno)){
            		if(empty($allserno)){
            			$allserno = $row->serno;
            		}else{
            			$allserno .= '_'.$row->serno;
            		}
            	}
        	}
        }
        unset($queryData['paiddate'],$queryData['duedate']);

        // var_dump($data);exit();
        // dd($data);

        // $data = T49tb::select('serno','material','total','applicant','copy')->where('class', $class)->where('term', $term)->get()->toarray();

        return view('admin/teaching_material_statistics/list', compact('data', 'queryData', 'allserno', 'cakall', 'cakid'));

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
        // $class = $queryData['class'] = substr($class_term_serno, 0,6);
        // $term = $queryData['term'] = substr($class_term_serno, 6,2);
        // $queryData = $this->methodService->getClassList($queryData);
        // $queryData = $queryData[0];
        // $serno = substr($class_term_serno, 8);
        // // 開支科目
        // $kindlist = S06tb::select('acccode','accname')->where('yerly',substr($class, 0,3))->get()->toarray();
        // // 院區差異未確定 先不分區
        // // if($queryData->branch==1){ //台北

        // // }elseif($queryData->branch==2){//南投

        // // }else{
        // //     return view('admin/errors/error');
        // // }
        // if($serno==''){  //新增
        //     $queryData->maxserno = (T49tb::max('serno')+1);
        //     $datalist = S04tb::orderby('sequence')->get()->toarray();
        //     $datalist = $this->getType($datalist);
        //     return view('admin/teaching_material_statistics/edit', compact('queryData', 'datalist','kindlist'));
        // }else{//編輯
        //     $data = T49tb::where('serno',$serno)->first();
        //     $data['branch'] = is_null($data['branch'])? $queryData->branch:$data['branch'];

        //     $datalist = T50tb::where('serno',$serno)->orderby('sequence')->get()->toarray();
        //     $datalist = $this->getType($datalist);
        //     return view('admin/teaching_material_statistics/edit', compact('queryData', 'datalist','kindlist','data'));
        // }
        //20200603

        $serno = $class_term_serno;

        $data = T49tb::where('serno',$serno)->first();

        $class = $queryData['class'] = $data->class;
        $term = $queryData['term'] = $data->term;
        $queryData = $this->methodService->getClass($queryData);

        // dd($queryData);
        // 開支科目
        $kindlist = S06tb::select('acccode','accname')->where('yerly',substr($class, 0,3))->get()->toarray();

        $datalist = T50tb::where('serno',$serno)->orderby('sequence')->get()->toarray();
        $datalist = $this->getType($datalist);
        return view('admin/teaching_material_statistics/edit', compact('queryData', 'datalist','kindlist','data'));

        // 院區差異未確定 先不分區
        // if($queryData->branch==1){ //台北

        // }elseif($queryData->branch==2){//南投

        // }else{
        //     return view('admin/errors/error');
        // }

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
        // dd($datalist);
        return $datalist;
    }
    /**
     * 更新單價
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upprice($class_term_serno){
        $class_term_serno = explode("_",$class_term_serno);
        $serno = $class_term_serno[0];
        $QUERY_STRING = $class_term_serno[1];

        $old_data = T49tb::where('serno',$serno)->first();

        if(!empty($old_data->paiddate)){
            return back()->with('result', '0')->with('message', '此筆資料已支付，不可修改');
        }

        DB::beginTransaction();
        try{
            T49tb::where('serno',$serno)->update(array('total' =>'0' ));
            T67tb::where('serno',$serno)->update(array('fee' =>'0' ));
            T50tb::where('serno',$serno)->delete();
            $datalist = S04tb::where('branch',$old_data->branch)->orderby('sequence')->get()->toarray();
            // dd($datalist);
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
            return redirect('/admin/teaching_material_statistics?'.$QUERY_STRING)->with('result', '1')->with('message', '更新成功!');
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

        $QUERY_STRING = $data['QUERY_STRING'];
        unset($data['QUERY_STRING']);

        $check = T49tb::select('paiddate')->where('serno', $data['serno'])->first();
        // if($check['paiddate']=='' || $check['paiddate']){
        //     return back()->with('result', '0')->with('message', '此筆資料已支付，不可修改');
        // }
        if(!empty($check['paiddate'])){
        	return back()->with('result', '0')->with('message', '此筆資料已支付，不可修改');
        }
        unset($data['_method'],$data['_token'],$data['newtotal']);

        DB::beginTransaction();
        try{
            $datalist = T50tb::where('serno', $data['serno'])->get()->toarray();
            $totalpay = 0;
            foreach ($datalist as $key => $value) {
                T50tb::where('serno', $data['serno'])->where('sequence',$value['sequence'])->update(array('quantity'=>$data['quantity'.$value['sequence']],
                                                                          'copy'=>$data['copy'.$value['sequence']] ));
                $totalpay = $totalpay + round($value['price']*$data['quantity'.$value['sequence']]*$data['copy'.$value['sequence']]);
                unset($data['quantity'.$value['sequence']],$data['copy'.$value['sequence']],$data['hidden'.$value['sequence']]);
            }
            $data['total'] = $totalpay;
            T49tb::where('serno', $data['serno'])->update($data);
            $fields['fee'] = $totalpay;
            T67tb::where('serno', $data['serno'])->update($fields);
            DB::commit();
            // return back()->with('result', '1')->with('message', '儲存成功!');
            return redirect('/admin/teaching_material_statistics/edit/'.$data['serno'].'?'.$QUERY_STRING)->with('result', '1')->with('message', '儲存成功!');
        }catch ( Exception $e ){
            DB::rollback();
            // return back()->with('result', '0')->with('message', '新增失敗，請稍後再試!');
            return redirect('/admin/teaching_material_statistics/edit/'.$data['serno'].'?'.$QUERY_STRING)->with('result', '0')->with('message', '儲存失敗，請稍後再試!');
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

    public function changepay(Request $request, $id)
    {
    	$class_term_serno = explode("_",$id);
    	$data = $request->all();
    	$cakid = '';

    	if($data['cakall'] == 'N'){
    		if(!empty($data['cakid'])){
    			$cakid = explode("_",$data['cakid']);
    		}
    		if(!empty($cakid)){
    			$class_term_serno = array_diff($class_term_serno, $cakid);
    		}
    		// dd($cakid);
    		// dd($class_term_serno);
    	}

        foreach($class_term_serno as $serno){
        	$check = T49tb::where('serno', $serno)->first();
        	$paiddate = substr($check->duedate, 0,5);
        	if(empty($check->paiddate)){
        		$fields=array(
	        		'paiddate' => $paiddate,
	        	);
        	}else{
        		$fields=array(
	        		'paiddate' => '',
	        	);
        	}

        	T49tb::where('serno', $serno)->update($fields);
        }
        return redirect('/admin/teaching_material_statistics?'.$data['QUERY_STRING'])->with('result', '1')->with('message', '修改成功!');
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

    /*
    教材交印統計表列印 CSDIR7120
    參考Tables:
        【t04tb 開班資料檔】
        【t07tb 經費概(結)算資料檔】
        【t38tb 會議基本資料檔】
        【t49tb 教材交印主檔】
    使用範本:P1.xlsx
    'History:
    '2010/06/19 Update
    '排序由t49.kind,t49.class,t49.term,t49.applicant,t49.serno改為t49.kind,t49.serno


    '2008/02/19 Update
    '產製出的報表頁首改為「公務人力發展學院00年00月份A4教材資料印製統計表」

    '2007/05/24 Update
    '修正使其能顯示多期別之資訊

    '2005/07/22 Update
    '
    '2005/03/03 Update
    '將系統中【開支科目】的代碼定義移至【s06tb 開支科目代碼檔】中
    '由modKind.bas統一控制

    '2003/08/20 Update
    '引用modKind.bas -->For 開支科目
    '將開支科目改成11項
    '【t04tb 開班資料檔】
    '【t07tb 經費概(結)算資料檔】
    '【t38tb 會議基本資料檔】
    '【t49tb 教材交印主檔】
    'kind 開支科目 char 2 (‘’)
    '01  在職訓練短期研習班
    '02  在職訓練中期研習班
    '03  在職訓練長期研習班
    '04  國家策略及女性領導者研究班
    '05  游於藝講堂
    '06  訓練輔導研究行政維持
    '07  在職進修專業課程
    '08  人力資源研究發展
    '09  一般行政 (基本行政工作維持)
    '10 代收款
    '11 其他
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //年
        $startYear = $request->input('startYear');
        //月
        $startMonth = $request->input('startMonth');

        /*
        if($startMonth<10){
            $startMonth='0'.$startMonth;
        }
        */

        //取得教材交印統計表
        $sql="  SELECT T.serno, T.NAME, T.material, T.total, T.accname
                FROM ( SELECT t49.serno,
                                            CASE WHEN t01.NAME IS NULL THEN IFNULL(m09.section,'')
                                                        ELSE CONCAT(t01.NAME,'第',t49.term,'期')
                                            END AS NAME,
                                            t49.material,
                                            t49.total,
                                            s06.accname,
                                            t49.kind
                                FROM t49tb t49
                                    LEFT OUTER JOIN t01tb t01 ON t01.class = t49.class
                                    LEFT OUTER JOIN m09tb m09 ON m09.userid = t49.applicant
                                    LEFT OUTER JOIN s06tb s06 ON s06.acccode = t49.kind AND s06.yerly = '108'
                                WHERE t49.paiddate = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                UNION ALL
                                SELECT '小計' AS serno,
                                            NULL AS NAME,
                                            NULL AS material,
                                            SUM(t49.total) AS total,
                                            NULL AS accname,
                                            t49.kind
                                FROM t49tb t49
                                    LEFT OUTER JOIN t01tb t01 ON t01.class = t49.class
                                    LEFT OUTER JOIN m09tb m09 ON m09.userid = t49.applicant
                                    LEFT OUTER JOIN s06tb s06 ON s06.acccode = t49.kind AND s06.yerly = '108'
                                WHERE t49.paiddate = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                GROUP BY t49.kind
                                UNION ALL
                                SELECT '合計' AS serno,
                                            NULL AS NAME,
                                            NULL AS material,
                                            SUM(t49.total) AS total,
                                            NULL AS accname,
                                            'ZZZ' AS kind
                                FROM t49tb t49
                                    LEFT OUTER JOIN t01tb t01 ON t01.class = t49.class
                                    LEFT OUTER JOIN m09tb m09 ON m09.userid = t49.applicant
                                    LEFT OUTER JOIN s06tb s06 ON s06.acccode = t49.kind AND s06.yerly = '108'
                                WHERE t49.paiddate = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                ) T
                ORDER BY T.Kind, T.serno, 2 ";

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'P1';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&14'.'行政院人事行政總處公務人力發展學院'.substr($startYear,0,3).'年'.$startMonth.'月份A4教材資料印製統計表 &P/&N');
        $reportlist = json_decode(json_encode($reportlist), true);

        if(sizeof($reportlist) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //A
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    //C2開始
                    $objActSheet->setCellValue($NameFromNumber.($j+2), $reportlist[$j][$arraykeys[$i]]);
                    //高 34
                    $objActSheet->getRowDimension($j+2)->setRowHeight(34);
                }
            }

            $styleArray = [
                'borders' => [
            //只有外框           'outline' => [
                        'allBorders'=> [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $objActSheet->getStyle('A2:'.$NameFromNumber.($j+1))->applyFromArray($styleArray);
        }

        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="教材交印統計表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        //匯出
        //old code
        //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $objWriter->save('php://output');
        exit;
    }
}
