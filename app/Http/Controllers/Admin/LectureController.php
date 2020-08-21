<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\LectureService;
use App\Services\User_groupService;
use App\Models\M01tb;
use App\Models\S01tb;
use App\Models\M16tb;
use App\Models\T08tb;
use App\Models\T09tb;
use DB;


class LectureController extends Controller
{
    /**
     * LectureController constructor.
     * @param LectureService $lectureService
     */
    public function __construct(LectureService $lectureService, User_groupService $user_groupService)
    {
        $this->lectureService = $lectureService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('lecture');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 班別
		if('' == $request->get('class')){
            $queryData['class'] = '';
        }else{
            $queryData['class'] = $request->get('class')=='全部'?'':$request->get('class');
        }


        // 取得關鍵字
        $queryData['keyword'] = $request->get('keyword');
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData);
        // echo "\n</pre>\n";
        // die();
        // 身分證字號
        $queryData['idno'] = $request->get('idno');
        // email
        $queryData['email'] = $request->get('email');
        // 服務機關名稱
        $queryData['dept'] = $request->get('dept');

        // 班別名稱
        $queryData['class_name'] = $request->get('class_name');
        // 班期
        $queryData['term'] = $request->get('term');
        // 專長領域
        $queryData['experience'] = $request->get('experience');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;

        // 取得列表資料
        $sql="SELECT RTRIM(code) AS 代碼, RTRIM(name) AS 名稱
        FROM s01tb WHERE type='D';";
        $postallist = DB::select($sql);

        $queryData['search'] = $request->get('search');

        if($queryData['search'] != 'search' ){
        	// 取得關鍵字
	        $queryData2['keyword'] = 'none';
	        // 身分證字號
	        $queryData2['idno'] = 'none';

        	$data = $this->lectureService->getLectureList($queryData2);
        }else{
        	$data = $this->lectureService->getLectureList($queryData);
            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('lecture')){
                $nowdata = $data;
                createModifyLog('S','M01tb','',$nowdata,end($sql));
            }
        }

        // if(null != $request->get('class') || null != $request->get('keyword') || null != $request->get('idno')){
        //     $data = $this->lectureService->getLectureList($queryData);
        //     return view('admin/lecture/list', compact('data', 'queryData', 'postallist'));
        // }

        return view('admin/lecture/list', compact('data', 'queryData','postallist'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $sql="SELECT RTRIM(name) AS 銀行名稱, RTRIM(code) AS 銀行代碼
        FROM s01tb WHERE type='H'
        ORDER BY name ";
        $list = DB::select($sql);
        return view('admin/lecture/form', compact('list'));
    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
		if ($request->hasFile('upload')) {
			$file = $request->file('upload');  //獲取UploadFile例項
			if ( $file->isValid()) { //判斷檔案是否有效
				$filename = $file->getClientOriginalName(); //檔案原名稱
				$extension = $file->getClientOriginalExtension(); //副檔名
                $filename = substr($request->Certificate, 0, -4).'_'.time().'.'.$extension;    //重新命名
                $data['Certificate'] = $filename;
                if($extension != "pdf"){
                    return back()->with('result', '0')->with('message', '個資授權書只允許上傳PDF');
                }
				//$filename = time() . "." . $extension;    //重新命名
				$file->move(public_path()."/Uploads/Authorization/", $filename);
			};
		};
		unset($data['upload']);

        // 姓名組成
        // $data['cname'] = $data['fname'].$data['lname'];
        // unset($data['lname']);

        // 出生日期
        $data['birth'] = ( ! $data['birth'])? NULL : str_pad($data['birth']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['birth']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['birth']['day'] ,2,'0',STR_PAD_LEFT);

        if($data['birth'] == '0000000'){
            $data['birth'] = '';
        }

        $data['update_date'] = date('Y-m-d H:i:s');

        for($i=1;$i<6;$i++){
            if($data['experience'.$i] != '-1'){
                $fields = array(
                    'idno' => $data['idno'],
                    'no' => $i,
                    'specialty' => $data['experience'.$i],
                );
                M16tb::create($fields);
                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('lecture')){
                    $nowdata = M16tb::where('idno', $data['idno'])->get()->toarray();
                    createModifyLog('I','M16tb','',$nowdata,end($sql));
                }
            }
            unset($data['experience'.$i]);
        }

        $name_len = mb_strlen($data['cname']);
        if($name_len > 3){
          $data['lname'] = mb_substr($data['cname'] , 0, 2);
          $data['fname'] = mb_substr($data['cname'] , 2, $name_len);
        }else if($name_len == 3){
          $data['lname'] = mb_substr($data['cname'] , 0, 1);
          $data['fname'] = mb_substr($data['cname'] , 1, 2);
        }else if($name_len == 2){
          $data['lname'] = mb_substr($data['cname'] , 0, 1);
          $data['fname'] = mb_substr($data['cname'] , 1, 1);
        }

        //新增
        $result = M01tb::create($data);
        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('lecture')){
            $nowdata = M01tb::where('idno', $data['idno'])->get()->toarray();
            createModifyLog('I','M01tb','',$nowdata,end($sql));
        }

        return redirect('/admin/lecture/'.$result->serno)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $serno
     */
    public function show($serno)
    {
        return $this->edit($serno);
    }

    public function getTerm(Request $request)
    {
        if(null == $request->get('class'))
			$queryData['class'] = '';
	    else
			$queryData['class'] = $request->get('class');


        return $queryData['class'];
    }

    public function checkidno(Request $request)
    {
    	$respon = array(
    		'status' => false,
    		'repeat' => '',
    		'url' => '',
    	);
    	$data = $request->all();
        if(!empty($data['idno'])){
        	$M01tb_data = M01tb::where('idno', $data['idno'])->first();
        	if(!empty($M01tb_data)){
        		$respon['repeat'] = '身分證字號('.$data['idno'].')重複，是否進行修改？';
        		$respon['url'] = '/admin/lecture/'.$M01tb_data->serno.'/edit';
        	}
            if($this->chk_pid($data['idno']) && empty($M01tb_data)){
                $respon['status'] = true;
                $respon['sex'] = substr($data['idno'] , 1,1);

            }

        }
        return response()->json($respon);
    }

    public function chk_pid($id) {
        if( !$id )return false;
        $id = strtoupper(trim($id)); //將英文字母全部轉成大寫，消除前後空白
        //檢查第一個字母是否為英文字，第二個字元1 2 A~D 其餘為數字共十碼
        $ereg_pattern= "^[A-Z]{1}[12ABCD]{1}[[:digit:]]{8}$";
        if(!preg_match("/".$ereg_pattern."/i", $id))return false;
        $wd_str="BAKJHGFEDCNMLVUTSRQPZWYX0000OI";   //關鍵在這行字串
        $d1=strpos($wd_str, $id[0])%10;
        $sum=0;
        if($id[1]>='A')$id[1]=chr($id[1])-65; //第2碼非數字轉換依[4]說明處理
        for($ii=1;$ii<9;$ii++)
            $sum+= (int)$id[$ii]*(9-$ii);
        $sum += $d1 + (int)$id[9];
        if($sum%10 != 0)return false;
        return true;
    }

    public function batch_import(Request $request)
    {
        $cnt_csv_head = 63;
        $massage = '';
            if (isset($_FILES['aCSV']) && $_FILES['aCSV']['tmp_name'] != '') {
                $file = fopen($_FILES['aCSV']['tmp_name'],"r");
                $i = '0';
                $import_susess = '0';
                $import_falut = '0';
                while(! feof($file))
                {
                    $data = fgetcsv($file);
                    if($i == '0'){
                        $i++;
                        continue;
                    }
                    if(!is_array($data)){
                        continue;
                    }
                    foreach($data as & $row){
                        $row = iconv('big5', 'UTF-8//IGNORE', $row);
                    }
                    $data[0] = strtoupper(trim($data[0]));

                    // echo '<pre style="text-align:left;">' . "\n";
                    // print_r($data);
                    // echo "\n</pre>\n";
                    // die();

                    // jd($data,1);
                    if(count($data) != $cnt_csv_head) { $EACH_IMP_RESULT[$i] = ' 欄位數不符';  continue;}// 欄位跟表頭不符
                    $EACH_STATUS = true;
                    $EACH_IMP_RESULT[$i] = array();
                    if($data[0] == ''){
                        $EACH_IMP_RESULT[$i][] = ' 身分證字號';
                        $EACH_STATUS = false;
                    }

                    // jd($EACH_IMP_RESULT[$i],1);
                    // $insert_date = new DateTime();
                    // $insert_date = $insert_date->format('Y-m-d H:i:s');
                    $fields = array(
                        'idno' => $data[0],//身分證字號
                        'cname' => $data[1],//姓名
                        'ename' => $data[2],//英文姓名
                        'sex' => $data[3],//性別
                        'birth' => $data[4],//出生日期
                        'citizen' => $data[5],//國籍
                        'passport' => $data[6],//護照號碼
                        'insurekind1' => $data[7],//健保第二類被保險人
                        'dept' => $data[8],//服務機關
                        'position' => $data[9],//現職
                        'kind' => $data[10],//分類
                        'offzip' => $data[11],//機關郵遞區號
                        'offaddress' => $data[12],//機關地址
                        'homzip' => $data[13],//住家郵遞區號
                        'homaddress' => $data[14],//住家地址
                        'regzip' => $data[15],//戶籍郵遞區號
                        'regaddress' => $data[16],//戶籍地址
                        'send' => $data[17],//郵寄地址
                        'offtela1' => $data[18],//電話(公一)區碼
                        'offtelb1' => $data[19],//號碼
                        'offtelc1' => $data[20],//分機
                        'offtela2' => $data[3],//電話(公二)區碼
                        'offtelb2' => $data[21],//號碼
                        'offtelc2' => $data[22],//分機
                        'homtela' => $data[23],//電話(宅)區碼
                        'homtelb' => $data[24],//號碼
                        'mobiltel' => $data[25],//行動電話
                        'offfaxa' => $data[26],//傳真(公)區碼
                        'offfaxb' => $data[28],//號碼
                        'homfaxa' => $data[29],//傳真(宅)區碼
                        'homfaxb' => $data[30],//號碼
                        'email' => $data[31],//Email
                        'liaison' => $data[32],//聯絡人
                        'publicly' => $data[33],//人事總處
                        'publish' => $data[34],//公務機關
                        'education' => $data[35],//最高學歷
                        'experience1' => $data[36],//專長領域
                        'experience2' => $data[37],//
                        'experience3' => $data[38],//
                        'experience4' => $data[39],//
                        'experience5' => $data[40],//
                        'experience' => $data[41],//重要經歷
                        'award' => $data[42],//重要著作及得獎紀錄
                        'remark' => $data[43],//公部門授課經歷
                        'major1' => $data[44],//可授課程(一)
                        'major2' => $data[45],//
                        'major3' => $data[46],//
                        'major4' => $data[47],//
                        'major5' => $data[48],//
                        'major6' => $data[49],//
                        'major7' => $data[50],//
                        'major8' => $data[51],//
                        'major9' => $data[52],//
                        'major10' => $data[53],//
                        'transfor' => $data[54],//轉帳帳戶
                        'notify' => $data[55],//入帳通知
                        'post' => $data[56],//郵局
                        'postcode' => $data[57],//局號
                        'postno' => $data[58],//郵局帳號
                        'bankcode' => $data[59],//銀行代碼
                        'bankno' => $data[60],//存摺帳號
                        'bankaccname' => $data[61],//戶名
                        'idkind' => $data[62],//證號別
                        'bank' => $data[63],//證號別
                    );

                    $name_len = mb_strlen($fields['cname']);
                    if($name_len > 3){
                      $fields['lname'] = mb_substr($fields['cname'] , 0, 2);
                      $fields['fname'] = mb_substr($fields['cname'] , 2, $name_len);
                    }else if($name_len == 3){
                      $fields['lname'] = mb_substr($fields['cname'] , 0, 1);
                      $fields['fname'] = mb_substr($fields['cname'] , 1, 2);
                    }else if($name_len == 2){
                      $fields['lname'] = mb_substr($fields['cname'] , 0, 1);
                      $fields['fname'] = mb_substr($fields['cname'] , 1, 1);
                    }

                    if($EACH_STATUS == true){

                        $sql="SELECT idno, serno
                        FROM m01tb WHERE idno = '{$data[0]}' ";
                        $ifExist = DB::select($sql);

                        $post_data = $request->all();

                        if(!empty($ifExist)){

                            if(isset($post_data['update']) && $post_data['update'] == 'Y'){

                                if(checkNeedModifyLog('lecture')){
                                    $olddata = M16tb::where('idno', $fields['idno'])->get()->toarray();
                                }

                                M16tb::where('idno', $fields['idno'])->delete();
                                $sql = DB::getQueryLog();
                                if(checkNeedModifyLog('lecture')){
                                    createModifyLog('D','M16tb',$olddata,'',end($sql));
                                }

                                for($ii=1;$ii<6;$ii++){
                                    if($fields['experience'.$ii] != '-1'){
                                        $experience_fields = array(
                                            'idno' => $fields['idno'],
                                            'no' => $ii,
                                            'specialty' => $fields['experience'.$ii],
                                        );
                                        M16tb::create($experience_fields);
                                        $sql = DB::getQueryLog();
                                        if(checkNeedModifyLog('lecture')){
                                            $nowdata = M16tb::where('idno', $fields['idno'])->get()->toarray();
                                            createModifyLog('I','M16tb','',$nowdata,end($sql));
                                        }
                                    }
                                    unset($fields['experience'.$ii]);
                                }

                                foreach ($fields as $key => $row) {
                                    if(empty($row)){
                                        unset($fields[$key]);
                                    }
                                }

                                M01tb::where('serno', $ifExist[0]->serno)->update($fields);
                                $massage .= "第{$i}筆 更新成功 <br>\n";
                            }else{
                                $massage .= "第{$i}筆 身分證已存在 <br>\n";
                            }

                        }else{

                            M16tb::where('idno', $fields['idno'])->delete();

                            for($ii=1;$ii<6;$ii++){
                                if($fields['experience'.$ii] != '-1'){
                                    $experience_fields = array(
                                        'idno' => $fields['idno'],
                                        'no' => $ii,
                                        'specialty' => $fields['experience'.$ii],
                                    );
                                    M16tb::create($experience_fields);
                                    $sql = DB::getQueryLog();
                                    if(checkNeedModifyLog('lecture')){
                                        $nowdata = M16tb::where('idno', $fields['idno'])->get()->toarray();
                                        createModifyLog('I','M16tb','',$nowdata,end($sql));
                                    }
                                }
                                unset($fields['experience'.$ii]);
                            }

                            foreach ($fields as $key => $row) {
                                if(empty($row)){
                                    unset($fields[$key]);
                                }
                            }

                            $result = M01tb::create($fields);
                            $sql = DB::getQueryLog();
                            if(checkNeedModifyLog('lecture')){
                                $nowdata = M01tb::where('idno', $fields['idno'])->get()->toarray();
                                createModifyLog('I','M01tb','',$nowdata,end($sql));
                            }

                            $massage .= "第{$i}筆 新增成功 <br>\n";
                        }
                    }else{
                        $massage .= "第{$i}筆 匯入失敗 [".implode(', ', $EACH_IMP_RESULT[$i])." ]<br>\n";
                    }

                }
            }
            $import = $massage;

        return view('admin/lecture/import', compact('import'));
    }

    /**
     * 編輯頁
     *
     * @param $serno
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($serno)
    {
        $data = M01tb::find($serno);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        $sql="SELECT *
        FROM m16tb WHERE idno='{$data->idno}'
        ORDER BY no ";
        $experience = DB::select($sql);

        $data->experience1 = '';
        $data->experience2 = '';
        $data->experience3 = '';
        $data->experience4 = '';
        $data->experience5 = '';

        if(!empty($experience)){

            foreach($experience as $row){
                if($row->no == '1'){
                    $data->experience1 = $row->specialty;
                }
                if($row->no == '2'){
                    $data->experience2 = $row->specialty;
                }
                if($row->no == '3'){
                    $data->experience3 = $row->specialty;
                }
                if($row->no == '4'){
                    $data->experience4 = $row->specialty;
                }
                if($row->no == '5'){
                    $data->experience5 = $row->specialty;
                }
            }

            // echo '<pre style="text-align:left;">' . "\n";
            // print_r($data);
            // echo "\n</pre>\n";
            // die();
        }

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($data);
        // echo "\n</pre>\n";
        // die();

        $sql="SELECT RTRIM(name) AS 銀行名稱, RTRIM(code) AS 銀行代碼
        FROM s01tb WHERE type='H'
        ORDER BY name ";
        $list = DB::select($sql);



        return view('admin/lecture/form', compact('data','list'));
    }

    //變更身分證字號+名字
    public function change($serno)
    {
        $data = M01tb::find($serno);

        if ( ! $data) {

            return view('admin/errors/error');
        }
        $sql="SELECT RTRIM(name) AS 銀行名稱, RTRIM(code) AS 銀行代碼
        FROM s01tb WHERE type='H'
        ORDER BY name ";
        $list = DB::select($sql);



        return view('admin/lecture/change', compact('data','list'));
    }

    public function specialty()
    {
        return DB::table(s01tb)
            ->select(code)
            ->distinct()
            ->get();
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $serno)
    {

        $data = $request->all();
    	$filename = '';
		if ($request->hasFile('upload')) {
			$file = $request->file('upload');  //獲取UploadFile例項
			if ( $file->isValid()) { //判斷檔案是否有效
				$filename = $file->getClientOriginalName(); //檔案原名稱
				$extension = $file->getClientOriginalExtension(); //副檔名
                $filename = substr($request->Certificate, 0, -4).'_'.time().'.'.$extension;    //重新命名
                $data['Certificate'] = $filename;
                if($extension != "pdf"){
                    return back()->with('result', '0')->with('message', '個資授權書只允許上傳PDF');
                }
                if(!empty($request['old_file']) && $filename != $request['old_file']){
                    if(file_exists(public_path()."/Uploads/Authorization/".$request['old_file'])){
                        unlink(public_path()."/Uploads/Authorization/".$request['old_file']);
                    }
                }
				//$filename = time() . "." . $extension;    //重新命名
				$file->move(public_path()."/Uploads/Authorization/", $filename);
			};
		};

		unset($data['upload']);
        if(isset($data['old_file'])){
            unset($data['old_file']);
        }

        if(isset($data['old_idno']) && $data['old_idno'] != $data['idno']){
            $M01tb_data = M01tb::where('idno', $data['idno'])->first();
            if($M01tb_data){
                return back()->with('result', '0')->with('message', '操作錯誤，身分證已存在');
            }
            $T08_data = array(
                'idno' => $data['idno'],
            );

            if(checkNeedModifyLog('lecture')){
                $olddata = T08tb::where('idno', $data['old_idno'])->get()->toarray();
            }

            T08tb::where('idno', $data['old_idno'])->update($T08_data);
            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('lecture')){
                $nowdata = T08tb::where('idno', $data['idno'])->get()->toarray();
                createModifyLog('U','T08tb',$olddata,$nowdata,end($sql));
            }

            $T09_data = array(
                'idno' => $data['idno'],
            );

            if(checkNeedModifyLog('lecture')){
                $olddata = T09tb::where('idno', $data['old_idno'])->get()->toarray();
            }

            T09tb::where('idno', $data['old_idno'])->update($T09_data);
            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('lecture')){
                $nowdata = T09tb::where('idno', $data['idno'])->get()->toarray();
                createModifyLog('U','T09tb',$olddata,$nowdata,end($sql));
            }

            if(checkNeedModifyLog('lecture')){
                $olddata = M16tb::where('idno', $data['old_idno'])->get()->toarray();
            }

            M16tb::where('idno', $data['old_idno'])->delete();
            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('lecture')){
                createModifyLog('D','M16tb',$olddata,'',end($sql));
            }
        }

        if(isset($data['old_cname']) && $data['old_cname'] != $data['cname']){

            if(checkNeedModifyLog('lecture')){
                $olddata = T08tb::where('idno', $data['idno'])->get()->toarray();
            }

            $name_len = mb_strlen($data['cname']);
            if($name_len > 3){
              $data['lname'] = mb_substr($data['cname'] , 0, 2);
              $data['fname'] = mb_substr($data['cname'] , 2, $name_len);
            }else if($name_len == 3){
              $data['lname'] = mb_substr($data['cname'] , 0, 1);
              $data['fname'] = mb_substr($data['cname'] , 1, 2);
            }else if($name_len == 2){
              $data['lname'] = mb_substr($data['cname'] , 0, 1);
              $data['fname'] = mb_substr($data['cname'] , 1, 1);
            }
            $T08_data = array(
                'cname' => $data['cname'],
                'lname' => $data['lname'],
                'fname' => $data['fname'],
            );
            T08tb::where('idno', $data['idno'])->update($T08_data);
            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('lecture')){
                $nowdata = T08tb::where('idno', $data['idno'])->get()->toarray();
                createModifyLog('U','T08tb',$olddata,$nowdata,end($sql));
            }
        }

        if(isset($data['old_idno']) && $data['old_idno'] == $data['idno']){
            if(checkNeedModifyLog('lecture')){
                $olddata = M16tb::where('idno', $data['idno'])->get()->toarray();
            }
        }

        M16tb::where('idno', $data['idno'])->delete();
        $sql = DB::getQueryLog();
        if(isset($data['old_idno']) && $data['old_idno'] == $data['idno']){
            if(checkNeedModifyLog('lecture')){
                createModifyLog('D','M16tb',$olddata,'',end($sql));
            }
        }

        if(isset($data['old_idno'])){
            unset($data['old_idno']);
        }
        if(isset($data['old_cname'])){
            unset($data['old_cname']);
        }

        for($i=1;$i<6;$i++){
            if($data['experience'.$i] != '-1'){
                $fields = array(
                    'idno' => $data['idno'],
                    'no' => $i,
                    'specialty' => $data['experience'.$i],
                );
                M16tb::create($fields);
                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('lecture')){
                    $nowdata = M16tb::where('idno', $data['idno'])->get()->toarray();
                    createModifyLog('I','M16tb','',$nowdata,end($sql));
                }
            }
            unset($data['experience'.$i]);
        }

        unset($data['_method'], $data['_token']);
        // 姓名組成
        // $data['cname'] = $data['fname'].$data['lname'];
        // unset($data['lname']);

        // 出生日期
        $data['birth'] = ( ! $data['birth'])? NULL : str_pad($data['birth']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['birth']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['birth']['day'] ,2,'0',STR_PAD_LEFT);
		$data['update_date'] = date('Y-m-d H:i:s');
        // dd($date['update_date']);
        if($data['birth'] == '0000000'){
            $data['birth'] = '';
        }

        if(checkNeedModifyLog('lecture')){
            $olddata = M01tb::where('serno', $serno)->get()->toarray();
        }

        //更新
        M01tb::where('serno', $serno)->update($data);
        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('lecture')){
            $nowdata = M01tb::where('serno', $serno)->get()->toarray();
            createModifyLog('U','M01tb',$olddata,$nowdata,end($sql));
        }

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    /**
     * 刪除處理
     *
     * @param $serno
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($serno)
    {
        if ($serno) {

            $data = M01tb::find($serno);
            $getDelete = $this->lectureService->getDelete($data->idno);
            if($getDelete['delete'] == 'Y'){
                M01tb::find($serno)->delete();
                return back()->with('result', '1')->with('message', '刪除成功!');
            }else{
                return back()->with('result', '0')->with('message', $getDelete['msg']);
            }

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    public function destroy_from($serno)
    {
        if ($serno) {

            $data = M01tb::find($serno);
            $getDelete = $this->lectureService->getDelete($data->idno);
            if($getDelete['delete'] == 'Y'){
                if(!empty($data['Certificate'])){
                    if(file_exists(public_path()."/Uploads/Authorization/".$request['Certificate'])){
                        unlink(public_path()."/Uploads/Authorization/".$request['Certificate']);
                    }
                }
                if(checkNeedModifyLog('lecture')){
                    $olddata = M01tb::where('idno', $data['idno'])->get()->toarray();
                }
                M01tb::find($serno)->delete();
                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('lecture')){
                    createModifyLog('D','M01tb',$olddata,'',end($sql));
                }
                return redirect('/admin/lecture')->with('result', '1')->with('message', '刪除成功!');
            }else{
                return back()->with('result', '0')->with('message', $getDelete['msg']);
            }

        } else {

            return redirect('/admin/lecture')->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
