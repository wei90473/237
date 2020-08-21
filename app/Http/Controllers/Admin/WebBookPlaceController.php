<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\WebBookPlace\WebBookPlaceRequest;
use App\Http\Requests\WebBookPlace\ArgRequest;
use App\Services\WebBookPlaceService;
use App\Services\ArrangementService;
use App\Services\User_groupService;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\S02tb;
use App\Models\M13tb;//機關基本資料檔
use App\Models\M14tb;//場地清單
use App\Models\T21tb;//網路預約場地紀錄檔
use App\Models\T22tb;//場地預約檔
use App\Models\T37tb;//場地預約歷史檔
use App\Models\T38tb;//會議基本資料檔
use File;
use Response;
use View;
use Session;
use config;


class WebBookPlaceController extends Controller
{
    public function __construct(WebBookPlaceService $wbs, User_groupService $user_groupService)
    {
        $this->wbs=$wbs;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
        	$user_data = \Auth::user();
        	$user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
        	if(in_array('webbookplace', $user_group_auth)){
        		return $next($request);
        	}else{
        		return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
        	}
        });
    }

    public function index(Request $request)
    {
		return view("/admin/webbookplace/index");
    }

    public function webbookplaceNantou(Request $request)
    {
    	$result=[];
		$condition=[];
        if($request->input()){
            $condition=$request->input();
			unset($condition["_token"]);
			if(isset($condition["date1"])){
				$condition["date1"]=str_replace("/","",$condition["date1"]);
			}
            if(isset($condition["date2"])){
				$condition["date2"]=str_replace("/","",$condition["date2"]);
			}
			//dd($condition);
            $result=$this->wbs->getList($condition);
        }else{
			$condition["status"]='N';
			$result=$this->wbs->getList($condition);
		}
        return view("/admin/webbookplace/list",compact("result","condition"));
    }

    public function webbookplaceTaipei(Request $request){
    	// 日期
    	$condition['date1'] = $request->get('date1');
        $condition['sdate'] = str_replace("/","",$condition['date1']);
        $condition['date2'] = $request->get('date2');
        $condition['edate'] = str_replace("/","",$condition['date2']);
        // 申請單位
        $condition['name'] = $request->get('name');
        // 申請單處理狀態
        $condition['result'] = $request->get('result');
        // 每頁幾筆
        $condition['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
		$default['sdate'] = (date('Y',strtotime('now'))-1911).date('md',strtotime('now - 7day'));
		$default['edate'] = (date('Y',strtotime('now'))-1911).date('md',strtotime('now'));
        if(empty($request->all())) {
        	return view("/admin/webbookplace/tlist",compact("condition","default"));
        }else{
			$result=$this->wbs->getTpaList($condition);
			// var_dump($result[0]['meet']);exit();
			return view("/admin/webbookplace/tlist",compact("result","condition","default"));
		}

    }

    public function edit(Request $request,$applyno)
    {
		$place=$this->wbs->get_edu_loanplacelst($applyno);
		$discount=$this->wbs->getArg();
		$mode='money';
		$discount_data=$this->wbs->getBookRoomList($applyno,$mode);
		$discount_data=$discount_data->toArray();

		$edu_loanplace=$this->wbs->get_edu_loanplace($applyno);
		$lock=["申請人可以修改","申請人不可以修改"];
		$status=["無法借用","可以借用","審核中","F"=>"可以借用未收費","N"=>"尚未處理","T"=>"可以借用已收費"];
		$applydate=$request->input("applydate");

		/*if($request->input()){
			var_dump($request->input());
			die();
			$input=$request->input();
			if($input["batch"]==1){
				$this->wbs->save_for_spaceprocessub($input,true);
			}

		}*/
		return view("/admin/webbookplace/form",compact("applyno","place","applydate","discount","discount_data","edu_loanplace","lock","status"));
	}

	public function Taipei_edit(Request $request,$meet_serno){
		$condition['meet'] = substr($meet_serno, 0,-2);
		$condition['serno'] = substr($meet_serno, -2);
		$result=$this->wbs->getTpaList($condition);
		$place=$this->wbs->get_Tpa_loanplacelst($condition);
		foreach ($place as $key => $value) {
			$newdate = (substr($value['date'],0,3 )+1911).substr($value['date'], 3);
			$place[$key]['week'] = date('w',strtotime($newdate));
		}
		$siteList = M14tb::select('site','name')->where('branch','1')->get();


		return view("/admin/webbookplace/tform",compact("result","place","siteList"));
	}


	// 南投審核
	public function loanplace_store(WebBookPlaceRequest $request)
	{
		$input=$request->input();
		// var_dump($input);exit();
		if(!isset($input["is_need_receipt"])&&$input["receiptname"]==''){
			return redirect()->route("webbook.edit.get",$input["applyno"])->with("result",0)->with("message","收據抬頭必填，若不需要請勾'不需要收據'");
		}else if(isset($input["is_need_receipt"])){
			$input["is_need_receipt"]=0;
			$input["receiptname"]=null;
		}else{
			$input["is_need_receipt"]=1;
		}
		//dd($input);
		// if($input["delete"]==1){
		// 	DB::table("edu_loanplace")->where("applyno",$input["applyno"])->delete();
		// 	DB::table("edu_loanplacelst")->where("applyno",$input["applyno"])->delete();
		// 	return redirect("/admin/webbookplace")->with("result",1)->with("message","刪除成功");
		// }
		//dd($input);
		if($input["batch"]==1){
			$rop=$this->wbs->save_for_spaceprocessub($input,true);
			$rop["discounttype"]=$input["discounttype"];
			DB::table("edu_loanplace")->where("applyno",$rop["applyno"])->update($rop);
			return redirect()->route("webbook.edit.get",$input["applyno"])->with("result",1)->with("message","修改成功");
		}else{
			$rop=$this->wbs->save_for_spaceprocessub($input);
			//var_dump($rop);
			// dd($input);
			// echo '<pre>';
			// print_r($input);
			// die();
			$input["discount1"]=$rop["discount1"];
			$input["discount2"]=$rop["discount2"];
			$input["applydate"]=str_replace("/","",$input["applydate"]);
			unset($input["_method"]);
			unset($input["_token"]);
			unset($input["confirm_fee"]);
			unset($input["ndiscounts"]);
			unset($input["hdiscounts"]);
			unset($input["ids"]);
			unset($input["nfees"]);
			unset($input["hfees"]);
			unset($input["batch"]);
			unset($input["delete"]);
			//$input["inner_check"]=0;
			if(isset($input["inner_check"])){
				$input["inner_check"]=1;
			}else{
				$input["inner_check"]=0;
			}

			try {
		            DB::table("edu_loanplace")->where("applyno",$rop["applyno"])->update($input);
	        } catch (\Exception $e) {
	            $status = false;
	            // return back()->with('result', 0)->with('message', '更新失敗');
	            var_dump($e->getMessage());
	            die;
	        }

			if($input['status'] == 'F' || $input['locked'] == '1'){
				try {
		            $this->wbs->save_to_taipei($input['applyno']);
		        } catch (\Exception $e) {
		            $status = false;
		            // return back()->with('result', 0)->with('message', '更新失敗');
		            var_dump($e->getMessage());
		            die;
		        }
			}

			return redirect()->route("webbook.edit.get",$input["applyno"])->with("result",1)->with("message","修改成功");
		}
	}
	// 審核資料編輯(台北)
	public function Taipei_loanplace_store(Request $request)
	{
		$data = $request->all();
		if($data['result']=='Y'){
			T38tb::where('meet',$data['meet'])->where('serno',$data['serno'])->update(array('email'=>$data['email']));
			return back()->with('result', '1')->with('message', '更新成功!');
		}else{
			T38tb::where('meet',$data['meet'])->where('serno',$data['serno'])
			->update(array( 'activity'=>$data['activity'],
							'email'=>$data['email'],
							'cnt'=>$data['cnt'],
							'type'=>$data['type'],
							'name'=>$data['name'],
							'payer'=>$data['payer'],
							'address'=>$data['address'],
							'liaison'=>$data['liaison'],
							'position'=>$data['position'],
							'telno'=>$data['telno'],
							'faxno'=>$data['faxno'],
							'mobiltel'=>$data['mobiltel'],
							'totalfee'=>$data['totalfee'], //費用
							'duedate'=>$data['duedate'], //繳費截止日期
							'replymk'=>isset($data['replymk'])? $data['replymk']:'',
							'replynote'=>isset($data['replynote'])? $data['replynote']:'' ));
			return back()->with('result', '1')->with('message', '更新成功!');
		}


	}

	public function saveConfirmFee(Request $request){
		$confirm_fee = $request->only(['confirm_fee']);
		$applyno = $request->only(['applyno']);
		try {
            $this->wbs->saveConfirmFee($confirm_fee,$applyno);

            return '1';
        } catch (\Exception $e) {
           return $e;
        }
	}

	// 台北審核
	public function audit(Request $request){
		$data = $request->all();
		$meet = substr($data['key'], 0,-2);
		$serno = substr($data['key'], -2);
		$result = T38tb::where('meet',$meet)->where('serno',$serno)->first();
		if(empty($result) ) return back()->with('result', '0')->with('message', '更新失敗，申請編號異常!');
		// 檢查資料
		if( is_null($result['activity']) || is_null($result['type']) || is_null($result['name']) || is_null($result['payer']) || is_null($result['applydate']) || is_null($result['liaison']) || is_null($result['telno']) || is_null($result['email'])){
			return back()->with('result', '0')->with('message', '資料缺失，請編輯完善資料!');
		}
		if($data['result']=='Y'){ //同意
			$base = T21tb::where('meet',$meet)->where('serno',$serno)->get()->toarray();
			foreach ($base as $key => $value) {
				$check = T22tb::where('site',$value['site'])->where('date',$value['date'])->where('time',$value['time'])->first();
				// if($check['class']==$meet && $check['term']==$serno){

				// }else{
				// 	return back()->with('result', '0')->with('message', '更新失敗，'.$value['date'].'場地'.$value['site'].'已被預約!');
				// } 
			}
			T38tb::where('meet',$meet)->where('serno',$serno)->update(array('result'=>$data['result'],'casemk'=>'Y','prove'=>'Y') );
			return back()->with('result', '1')->with('message', '更新成功!');
		}else{ //駁回
			T38tb::where('meet',$meet)->where('serno',$serno)
			->update(array('result'=>$data['result'],'casemk'=>'Y','prove'=>'Y','totalfee'=>'0','duedate'=>'') );
			T22tb::where('class',$meet)->where('term',$serno)->delete();
			T37tb::where('class',$meet)->where('term',$serno)->delete();
			return back()->with('result', '1')->with('message', '更新成功!');
		}
	}
	//新增教室(南投)
	public function room_add(Request $request,$applyno)
	{
		$mode=$request->input("mode");
		$id=$request->input("id");

		$data=[];

		$apply = $this->wbs->get_edu_loanplace_by_applyno($applyno);
		if(empty($apply)){
			$locked = 0;
		} else {
			$locked = $apply[0]['locked'];
		}

		if($id!=''){
			$data=$this->wbs->get_edu_loanplacelst_by_id($id);
		}

		if(empty($data)){
			$data[0]['id'] = 0;
			$data[0]['applyno'] = $applyno;
			$data[0]['placenum'] = 0;
			$data[0]['timestart'] = '1500';
			$data[0]['timeend'] = '1159';
		} else {
			if($locked == 0){
				if($this->wbs->getCountLoanRoom($data[0]['applyno'],$data[0]['croomclsno'],$data[0]['startdate']) > 0){
					$locked = 1;
				} else if($this->wbs->getCountLoansRoom($data[0]['applyno'],$data[0]['croomclsno'],$data[0]['startdate']) > 0) {
					$locked = 1;
				}
			}
		}

		$placeList = $this->wbs->getBorrowForDropdown();

		if($request->input()&&$mode==''){
			$rules = array(
	            'croomclsno' => 'required',
	            'placenum' => 'required',
	            'startdate' => 'required',
	            'timestart' => 'required',
	            'timeend' => 'required'
	        );

	        $messages = array(
	            'croomclsno.required' => '請選擇借用場地',
	            'placenum.required' => '請填寫使用間數',
	            'startdate.required' => '請選擇借用日期(起)',
	            'timestart.required' => '請選擇借用時間(起)',
	            'timeend.required' => '請選擇借用時間(迄)'
	        );

	        $this->validate($request, $rules, $messages);

			$op=$request->input();

			$checkloanplacelst = $this->wbs->checkloanplacelst($applyno,$op['startdate'],$op['timestart'],$op['croomclsno']);

			if($checkloanplacelst == 0){
				$edate=$request->input('enddate');
				if (empty($edate)){
					$edate=$request->input('startdate');
				}

				$op["enddate"]=$edate;
				$vids_sdate=$this->wbs->convertYear($op['startdate'],'twtovids');
				$vids_edate=$this->wbs->convertYear($op['enddate'],'twtovids');
				$days=floor((strtotime($vids_edate) - strtotime($vids_sdate))/ (60*60*24))+1; //計算相差之天數

				$cls = $this->wbs->getClassroom($op['croomclsno']);
				//$cls=SingleRowOrDefault($this->classroomcls_model->get_field_by('classroom',array('edu_classroomcls.croomclsno'=>$op['croomclsno'])));
				if (!empty($cls) && $cls[0]['classroom'] == 1){//教室
					$nday=$this->wbs->workdate($op['startdate'],$op['enddate']);
				}else{
					$nday=$this->wbs->workdate3($op['startdate'],$op['enddate']);//workdate2($op['startdate'],$op['enddate']);
				}
				$op["startdate"]=str_replace("/","",$op["startdate"]);
				$op["enddate"]=str_replace("/","",$op["enddate"]);
				$op["applyno"]=$applyno;
				$op['nday']=$nday;
				$op['hday']=$days-$nday;

				//dd($op);
				unset($op["_token"]);
				unset($op["delete"]);
				DB::table('edu_loanplacelst')->insert($op);
				return redirect()->route("webbook.edit.get",$applyno)->with("result",1)->with("message","新增成功!");
			} else {
				return redirect()->route("webbook.place.get",$applyno)->with('result', '1')->with('message', '申請借用場地重覆！');
			}
		}
		return view("/admin/webbookplace/add",compact("applyno","mode","id","data","locked","placeList"));
	}

	// 新增場地(台北)
	public function createSite(Request $request){
		$data = $request->all();
		$data['site'] = $data['C_site'];
		$data['time'] = $data['C_time'];
		$data['date'] = $data['C_date'];
		$data['seattype'] = $data['C_seattype'];
		$data['usertype'] = $data['C_usertype'];
		unset($data['C_site'],$data['C_time'],$data['C_date'],$data['C_seattype'],$data['C_usertype'],$data['_method'],$data['_token']);

		$check = T22tb::where('site',$data['site'])->where('date',$data['date'])->where('time',$data['time'])->count();
		if($check>0) return back()->with('result', '0')->with('message', '新增失敗，場地已被預約!');

		$create = $this->wbs->createSit($data);
		if($create){
			return back()->with('result', '1')->with('message', '新增成功!');
		}else{
			return back()->with('result', '0')->with('message', '新增失敗!');
		}
	}

	// 場地編輯(台北)
	public function updateSite(Request $request){
		$data = $request->all();
		if($data['E_site'] == $data['site'] && $data['E_date'] == $data['date'] && $data['E_time'] == $data['time']){
			T21tb::where('meet',$data['meet'])->where('serno',$data['serno'])->where('site',$data['site'])->where('date',$data['date'])->where('time',$data['time'])->update(array('seattype'=>$data['seattype'],'usertype'=>$data['usertype']));
			T22tb::where('class',$data['meet'])->where('term',$data['serno'])->where('site',$data['site'])->where('date',$data['date'])->where('time',$data['time'])->update(array('seattype'=>$data['seattype'],'usertype'=>$data['usertype']));
			return back()->with('result', '1')->with('message', '更新成功!');
		}else{
			$check = T22tb::where('site',$data['site'])->where('date',$data['date'])->where('time',$data['time'])->count();
			if($check>0) return back()->with('result', '0')->with('message', '更新失敗，場地已被預約!');

			T21tb::where('meet',$data['meet'])->where('serno',$data['serno'])->where('site',$data['E_site'])->where('date',$data['E_date'])->where('time',$data['E_time'])->delete();
	  		T22tb::where('class',$data['meet'])->where('term',$data['serno'])->where('site',$data['E_site'])->where('date',$data['E_date'])->where('time',$data['E_time'])->delete();
	  		unset($data['_method'],$data['_token'],$data['E_site'],$data['E_date'],$data['E_time']);
	  		$create = $this->wbs->createSit($data);
			if($create){
				return back()->with('result', '1')->with('message', '更新成功!');
			}else{
				return back()->with('result', '0')->with('message', '更新失敗!');
			}
		}
	}
	// 場地刪除(台北)
	public function deleteSite(Request $request){
		$data = $request->all();
		T21tb::where('meet',$data['meet'])->where('serno',$data['serno'])->where('site',$data['D_site'])->where('date',$data['D_date'])->where('time',$data['D_time'])->delete();
		T22tb::where('class',$data['meet'])->where('term',$data['serno'])->where('site',$data['D_site'])->where('date',$data['D_date'])->where('time',$data['D_time'])->delete();
		T37tb::where('class',$data['meet'])->where('term',$data['serno'])->where('site',$data['D_site'])->where('date',$data['D_date'])->where('time',$data['D_time'])->delete();
		return back()->with('result', '1')->with('message', '刪除成功!');
	}

	// 刪除申請單(南投)
	public function delete($applyno){
		if($applyno > 0){
			try {
	            DB::beginTransaction();
	            $this->wbs->deleteEduLoanplace($applyno);
	            $this->wbs->deleteEduLoanplacelst($applyno);
	            $this->wbs->deleteEduLoanroom($applyno);
	            $this->wbs->deleteEduLoansroom($applyno);
	            $this->wbs->deleteT97tb($applyno);
	            DB::commit();
	            $updateStauts = true;   
	        } catch (\Exception $e) {
	            DB::rollback();
	            var_dump($e->getMessage());
	            die;
	        } 
		}

		return back()->with('result', '1')->with('message', '刪除成功!');	
	}
	/**
     * 列印申請單word檔案
     *
     * @return file
     */
    public function apply_doc($key) {
    	$meet = substr($key, 0,-2);
		$serno = substr($key, -2);


        //取得資料
        $data = T38tb::where('meet',$meet)->where('serno',$serno)->first();
        $sitedata = T21tb::where('meet',$meet)->where('serno',$serno)->join('m14tb','m14tb.site','t21tb.site')->get()->toarray();
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('標楷體'); //設定預設字型
        $phpWord->setDefaultFontSize(12); //設定預設字型大小
        $sectionStyle = array( //上邊界 1.5cm*566.929134
		    'marginTop'         => 850,
		    'marginLeft'		=> 850,
		    'marginRight'		=> 850,
		    'marginBottom'		=> 1134);
		$section = $phpWord->addSection($sectionStyle); //建立一個區域

        $section->addText('行政院人事行政總處公務人力發展學院會議場地預約申請單',array('bold' => true,'size'=>16), array('align' => 'center'));
        $cellRowSpan  = array('vMerge' => 'restart'); //垂直合併
		$cellRowContinue = array('vMerge' => 'continue'); //略過
		$cellColSpan  = array('gridSpan' => 2); //水平合併
        $styleTable = ['borderColor' => '000000', 'borderSize' => 6, 'cellMargin' => 80];
        $phpWord->addTableStyle('myTable', $styleTable); //建立表格樣式
        $table = $section->addTable('myTable'); //建立表格
        //申 請 單 位 基 本 資 料
        $table->addRow();
        $cell = $table->addCell(800,$cellRowSpan);
        $cell->addText('申 請 單 位 基 本 資 料',array('size'=>16));
        $cell = $table->addCell(1196);
        $cell->addText('申請編號');
        $cell = $table->addCell(2959,$cellColSpan);
        $cell->addText($key);
        $cell = $table->addCell(1587);
        $cell->addText('申請日期');
        $cell = $table->addCell(4275,$cellColSpan);
        $cell->addText(substr($data['applydate'],0,3).'/'.substr($data['applydate'],3,2).'/'.substr($data['applydate'],5,2) );

    	$table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('活動名稱');
        $cell = $table->addCell(4546,array('gridSpan' => 3));
        $cell->addText($data['activity']);
        $cell = $table->addCell(1587);
        $cell->addText('人數');
        $cell = $table->addCell(2688);
        $cell->addText($data['cnt'].'人');

        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('單位類型');
        $cell = $table->addCell(8821,array('gridSpan' => 5));
        $deptA=$deptB=$deptC = '□';
        if( $data['type'] == '1' ) {
	        $deptA = '☑';
	    } elseif( $data['type'] == '2' ) {
	        $deptB = '☑';
	    } elseif( $data['type'] == '3' ) {
	        $deptC = '☑';
	    }
	    $cell->addText($deptA.'政府機關 '.$deptB.'民間單位 '.$deptC.'受政府機關委託  ');
	    $cell->addText('後續再提供',array('underline' => 'single'));

	    $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('申請單位');
        $cell = $table->addCell(8821,array('gridSpan' => 5));
        $cell->addText($data['name']);

        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('收據抬頭');
        $cell = $table->addCell(8821,array('gridSpan' => 5));
        $cell->addText($data['payer']);

        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('單位地址');
        $cell = $table->addCell(8821,array('gridSpan' => 5));
        $cell->addText($data['address']);

        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('聯 絡 人');
        $cell = $table->addCell(2959,$cellColSpan);
        $cell->addText($data['liaison']);
        $cell = $table->addCell(1587);
        $cell->addText('職  稱');
        $cell = $table->addCell(4275,$cellColSpan);
        $cell->addText($data['position']);

        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('電    話');
        $cell = $table->addCell(2000);
        $cell->addText($data['telno']);
        $cell = $table->addCell(959);
        $cell->addText('傳  真');
        $cell = $table->addCell(1587);
        $cell->addText($data['faxno']);
        $cell = $table->addCell(1587);
        $cell->addText('行動電話');
        $cell = $table->addCell(2688);
        $cell->addText($data['mobiltel']);

        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('E－Mail');
        $cell = $table->addCell(8821,array('gridSpan' => 5));
        $cell->addText($data['email']);
        //預 約 之 場 地
        $table->addRow();
        $cell = $table->addCell(800,$cellRowSpan);
        $cell->addText('預 約 之 場 地',array('size'=>16));
        $cell = $table->addCell(4155,array('gridSpan' => 3));
        $cell->addText('場地名稱');
        $cell = $table->addCell(1587);
        $cell->addText('日期');
        $cell = $table->addCell(4275,$cellColSpan);
        $cell->addText('時段');
        foreach ($sitedata as $key => $value) {
        	$table->addRow();
	        $cell = $table->addCell(null, $cellRowContinue);
	        $cell = $table->addCell(4155,array('gridSpan' => 3));
	        $cell->addText($value['name']);
	        $cell = $table->addCell(1587);
	        $cell->addText(substr($value['date'],0,3).'/'.substr($value['date'],3,2).'/'.substr($value['date'],5,2));
	        $cell = $table->addCell(4275,$cellColSpan);
	        if( $value['time'] == 'A' ) {
	        	$time = '早上';
		    } elseif( $data['type'] == 'B' ) {
		        $time = '下午';
		    } elseif( $data['type'] == 'C' ) {
		        $time = '晚間';
		    } else{
		    	$time = '其他';
		    }
	        $cell->addText($time);
        }
        if(count($sitedata)<1){
        	$table->addRow();
	        $cell = $table->addCell(null, $cellRowContinue);
	        $cell = $table->addCell(4155,array('gridSpan' => 3));
	        $cell->addText('');
	        $cell = $table->addCell(1587);
	        $cell->addText('');
	        $cell = $table->addCell(4275,$cellColSpan);
	        $cell->addText('');
	        $table->addRow();
	        $cell = $table->addCell(null, $cellRowContinue);
	        $cell = $table->addCell(4155,array('gridSpan' => 3));
	        $cell->addText('');
	        $cell = $table->addCell(1587);
	        $cell->addText('');
	        $cell = $table->addCell(4275,$cellColSpan);
	        $cell->addText('');
        }
        //擬處理情形
        $table->addRow();
        $cell = $table->addCell(800,$cellRowSpan);
        $cell->addText('擬處理情形',array('size'=>16));
        $cell = $table->addCell(10017,array('gridSpan' => 6,'vMerge' => 'restart'));

        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(null, array('gridSpan' => 6,'vMerge' => 'continue'));
        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(null, array('gridSpan' => 6,'vMerge' => 'continue'));
        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(null, array('gridSpan' => 6,'vMerge' => 'continue'));
        //簽章
        $table->addRow();
        $cell = $table->addCell(3605,array('gridSpan' => 3));
        $cell->addText('承辦人',array('bold' => true,'size'=>14), array('align' => 'center'));
        $cell = $table->addCell(3605,array('gridSpan' => 2));
        $cell->addText('訓練排程承辦人',array('bold' => true,'size'=>14), array('align' => 'center'));
        $cell = $table->addCell(3605,array('gridSpan' => 2));
        $cell->addText('批　　　示',array('bold' => true,'size'=>14), array('align' => 'center'));
        $cell->addText('（單位主管或其授權人）',array('bold' => true,'size'=>11), array('align' => 'center'));

        $table->addRow(2500);
        $cell = $table->addCell(3605,array('gridSpan' => 3));
        $cell->addText('');
        $cell = $table->addCell(3605,array('gridSpan' => 2));
        $cell->addText('');
        $cell = $table->addCell(3605,array('gridSpan' => 2));
        $cell->addText('');


        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        try {
            $objWriter->save(storage_path('行政院人事行政總處公務人力發展學院會議場地預約申請單.docx'));
        } catch (Exception $e) {
        }
        return response()->download(storage_path('行政院人事行政總處公務人力發展學院會議場地預約申請單.docx'));
    }
    /**
     * 列印回覆單word檔案
     *
     * @return file
     */
    public function export_doc($key) {
    	$meet = substr($key, 0,-2);
		$serno = substr($key, -2);


        //取得資料
        $data = T38tb::where('meet',$meet)->where('serno',$serno)->first();
        $sitedata = T21tb::where('meet',$meet)->where('serno',$serno)->join('m14tb','m14tb.site','t21tb.site')->get()->toarray();
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('標楷體'); //設定預設字型
        $phpWord->setDefaultFontSize(12); //設定預設字型大小
        $sectionStyle = array( //上邊界 1.5cm*566.929134
		    'marginTop'         => 850,
		    'marginLeft'		=> 850,
		    'marginRight'		=> 850,
		    'marginBottom'		=> 1134);
		$section = $phpWord->addSection($sectionStyle); //建立一個區域

        $section->addText('行政院人事行政總處公務人力發展學院會議場地回覆單',array('bold' => true,'size'=>16), array('align' => 'center'));
        $section->addText('');
        $cellRowSpan  = array('vMerge' => 'restart'); //垂直合併
		$cellRowContinue = array('vMerge' => 'continue'); //略過
		$cellColSpan  = array('gridSpan' => 2); //水平合併
        $styleTable = ['borderColor' => '000000', 'borderSize' => 6, 'cellMargin' => 80];
        $phpWord->addTableStyle('myTable', $styleTable); //建立表格樣式
        $table = $section->addTable('myTable'); //建立表格
        //申 請 單 位 基 本 資 料
        $table->addRow();
        $cell = $table->addCell(800,$cellRowSpan);
        $cell->addText('申 請 單 位 基 本 資 料',array('size'=>16));
        $cell = $table->addCell(1196);
        $cell->addText('申請編號');
        $cell = $table->addCell(2959,$cellColSpan);
        $cell->addText($key);
        $cell = $table->addCell(1587);
        $cell->addText('申請日期');
        $cell = $table->addCell(4275,$cellColSpan);
        $cell->addText(substr($data['applydate'],0,3).'/'.substr($data['applydate'],3,2).'/'.substr($data['applydate'],5,2) );

    	$table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('活動名稱');
        $cell = $table->addCell(4546,array('gridSpan' => 3));
        $cell->addText($data['activity']);
        $cell = $table->addCell(1587);
        $cell->addText('人數');
        $cell = $table->addCell(2688);
        $cell->addText($data['cnt'].'人');

        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('單位類型');
        $cell = $table->addCell(8821,array('gridSpan' => 5));
        $deptA=$deptB=$deptC = '□';
        if( $data['type'] == '1' ) {
	        $deptA = '☑';
	    } elseif( $data['type'] == '2' ) {
	        $deptB = '☑';
	    } elseif( $data['type'] == '3' ) {
	        $deptC = '☑';
	    }
	    $cell->addText($deptA.'政府機關 '.$deptB.'民間單位 '.$deptC.'受政府機關委託  ');
	    $cell->addText('後續再提供',array('underline' => 'single'));

	    $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('申請單位');
        $cell = $table->addCell(8821,array('gridSpan' => 5));
        $cell->addText($data['name']);

        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('收據抬頭');
        $cell = $table->addCell(8821,array('gridSpan' => 5));
        $cell->addText($data['payer']);

        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('單位地址');
        $cell = $table->addCell(8821,array('gridSpan' => 5));
        $cell->addText($data['address']);

        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('聯 絡 人');
        $cell = $table->addCell(2959,$cellColSpan);
        $cell->addText($data['liaison']);
        $cell = $table->addCell(1587);
        $cell->addText('職  稱');
        $cell = $table->addCell(4275,$cellColSpan);
        $cell->addText($data['position']);

        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('電    話');
        $cell = $table->addCell(2000);
        $cell->addText($data['telno']);
        $cell = $table->addCell(959);
        $cell->addText('傳  真');
        $cell = $table->addCell(1587);
        $cell->addText($data['faxno']);
        $cell = $table->addCell(1587);
        $cell->addText('行動電話');
        $cell = $table->addCell(2688);
        $cell->addText($data['mobiltel']);

        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(1196);
        $cell->addText('E－Mail');
        $cell = $table->addCell(8821,array('gridSpan' => 5));
        $cell->addText($data['email']);
        //預 約 之 場 地
        $table->addRow();
        $cell = $table->addCell(800,$cellRowSpan);
        $cell->addText('預 約 之 場 地',array('size'=>16));
        $cell = $table->addCell(4155,array('gridSpan' => 3));
        $cell->addText('場地名稱');
        $cell = $table->addCell(1587);
        $cell->addText('日期');
        $cell = $table->addCell(4275,$cellColSpan);
        $cell->addText('時段');
        foreach ($sitedata as $key => $value) {
        	$table->addRow();
	        $cell = $table->addCell(null, $cellRowContinue);
	        $cell = $table->addCell(4155,array('gridSpan' => 3));
	        $cell->addText($value['name']);
	        $cell = $table->addCell(1587);
	        $cell->addText(substr($value['date'],0,3).'/'.substr($value['date'],3,2).'/'.substr($value['date'],5,2));
	        $cell = $table->addCell(4275,$cellColSpan);
	        if( $value['time'] == 'A' ) {
	        	$time = '早上';
		    } elseif( $data['type'] == 'B' ) {
		        $time = '下午';
		    } elseif( $data['type'] == 'C' ) {
		        $time = '晚間';
		    } else{
		    	$time = '其他';
		    }
	        $cell->addText($time);
        }
        if(count($sitedata)<1){
        	$table->addRow();
	        $cell = $table->addCell(null, $cellRowContinue);
	        $cell = $table->addCell(4155,array('gridSpan' => 3));
	        $cell->addText('');
	        $cell = $table->addCell(1587);
	        $cell->addText('');
	        $cell = $table->addCell(4275,$cellColSpan);
	        $cell->addText('');
        }
        //回覆意見
        $table->addRow();
        $cell = $table->addCell(800,$cellRowSpan);
        $cell->addText('回覆意見',array('size'=>16));
        $cell = $table->addCell(10017,array('gridSpan' => 6,'vMerge' => 'restart'));
        $replymk = config('app.replymk.'.$data['replymk']);
        $cell->addText($replymk);
        $cell->addText($data['replynote']);
        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(null, array('gridSpan' => 6,'vMerge' => 'continue'));
        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(null, array('gridSpan' => 6,'vMerge' => 'continue'));
        $table->addRow();
        $cell = $table->addCell(null, $cellRowContinue);
        $cell = $table->addCell(null, array('gridSpan' => 6,'vMerge' => 'continue'));
        $section->addText('※如有其他需服務事項，請電洽本學院綜合規劃組薛智云小姐(02)83691399#8110。',array('size'=>12), array('align' => 'both'));


        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        try {
            $objWriter->save(storage_path('公務人力發展中心會議場地預約申請單.docx'));
        } catch (Exception $e) {
        }
        return response()->download(storage_path('公務人力發展中心會議場地預約申請單.docx'));
    }

	//email回復
	public function send_email(Request $request,$email)
	{
        if($request->input()){
            $data=[];
            $data=['title'=>$request->input('title'),'content'=>$request->input('content')];

            Mail::send("admin/dataexport/send", $data, function ($message) use ($email,$data){
				$message->from('csdi.send@gmail.com', 'tobytest');
				$message->subject($data['title']);
				$message->to($email);
            });
            return back()->with('result',1)->with("message","寄送成功!");
        }else{
            return view("admin/webbookplace/mail",compact('email'));
        }
	}

	public function updateApplyLimit(Request $request)
	{
		$spaceappday = $request->input('spaceappday');

		try {
            $this->wbs->updateApplyLimit($spaceappday);

            return redirect()->route('webbook.parameter.get')->with('result', '1')->with('message', '儲存成功');
        } catch (\Exception $e) {
            $status = false;
            // return back()->with('result', 0)->with('message', '更新失敗');
            var_dump($e->getMessage());
            die;
        }
	}

	public function parameter_set()
	{
		$data=$this->wbs->get_webbook_parameter(Auth::user()->userid);
		$spaceappday=$this->wbs->getSpaceappday();
		return view("admin/webbookplace/parameter_set",compact("data","spaceappday"));
	}

	public function parameter_add(Request $request,$id=null)
	{
		$mode="add";
		if($request->input()){
			/*var_dump(Auth::user()->userid);
			var_dump(date('Y-m-d H:i:s'));
			dd($request->input());*/
			$input=$request->input();
			$input["cre_user"]=Auth::user()->userid;
			$input["cre_date"]=date('Y-m-d H:i:s');
			unset($input["_token"]);
			unset($input["delete"]);
			DB::table("webbook_parameter")->insert($input);
			return redirect()->route("webbook.parameter.get")->with("result","1")->with("message","新增成功!");
		}
		if($id!=null){
			$mode='put';
			$data=DB::table("webbook_parameter")->where('id',$id)->first();
		}
		return view("admin/webbookplace/parameter_add",compact("mode","data"));
	}

	public function parameter_put(Request $request,$id)
	{
		$update=$request->input();
		if($update["delete"]==1){
			DB::table("webbook_parameter")->where("id",$id)->delete();
			return redirect()->route("webbook.parameter.get")->with("result","1")->with("message","刪除成功!");
		}

		unset($update["_token"]);
		unset($update["_method"]);
		unset($update["delete"]);
		DB::table("webbook_parameter")->where("id",$id)->update($update);
		return redirect()->route("webbook.parameter.get")->with("result","1")->with("message","修改成功!");

	}

	//發送異動通知
	public function send_change()
	{
		$mode='';
		$data=$this->wbs->get_webbook_parameter(Auth::user()->userid);

		return view("/admin/webbookplace/send_change",compact("mode","data"));
	}

	public function send_change_email(Request $request)
	{
		$ids=$request->input("checkboxs");
		$data=$this->wbs->get_webbook_parameter_by_id($ids);
		//dd($data);
		$email=[];
		for($i=0;$i<count($data);$i++){
			$email[$i]=$data[$i]['email'];
		}
		$mail=implode(",",$email);
		return view("/admin/webbookplace/change_mail",compact("mail"));
	}

	public function send_change_email_post(Request $request)
	{
		//dd($request->input());
		$email=explode(",",$request->input('emails'));
		$data=['title'=>$request->input('title'),'content'=>$request->input('content')];
		Mail::send("admin/dataexport/send", $data, function ($message) use ($email,$data){
			for($i=0;$i<count($email);$i++){
				$message->from('csdi.send@gmail.com', 'tobytest');
				$message->subject($data['title']);
				$message->to($email[$i]);
			}
		});
		return redirect("webbook.Nantou.get")->with("result","1")->with("message","寄信成功");
	}

	//修改教室
	public function room_put(Request $request,$id)
	{
		$op=$request->input();

		$applyno=$op["applyno"];
		if($op["delete"]==1){
			DB::table("edu_loanplacelst")->where('id',$id)->delete();
			return redirect()->route("webbook.edit.get",$applyno)->with("result",1)->with("message","刪除成功!");
		}else{
			$rules = array(
	            'croomclsno' => 'required',
	            'placenum' => 'required',
	            'startdate' => 'required',
	            'timestart' => 'required',
	            'timeend' => 'required'
	        );

	        $messages = array(
	            'croomclsno.required' => '請選擇借用場地',
	            'placenum.required' => '請填寫使用間數',
	            'startdate.required' => '請選擇借用日期(起)',
	            'timestart.required' => '請選擇借用時間(起)',
	            'timeend.required' => '請選擇借用時間(迄)'
	        );

	        $this->validate($request, $rules, $messages);

			$checkloanplacelst = $this->wbs->checkloanplacelst($applyno,$op['startdate'],$op['timestart'],$op['croomclsno'],$id);

			if($checkloanplacelst == 0){
				$edate=$request->input('enddate');
				if (empty($edate)){
					$edate=$request->input('startdate');
				}

				$op["enddate"]=$edate;
				$vids_sdate=$this->wbs->convertYear($op['startdate'],'twtovids');
				$vids_edate=$this->wbs->convertYear($op['enddate'],'twtovids');
				$days=floor((strtotime($vids_edate) - strtotime($vids_sdate))/ (60*60*24))+1; //計算相差之天數

				$cls = $this->wbs->getClassroom($op['croomclsno']);
				//$cls=SingleRowOrDefault($this->classroomcls_model->get_field_by('classroom',array('edu_classroomcls.croomclsno'=>$op['croomclsno'])));
				if (!empty($cls) && $cls[0]['classroom'] == 1){//教室
					$nday=$this->wbs->workdate($op['startdate'],$op['enddate']);
				}else{
					$nday=$this->wbs->workdate3($op['startdate'],$op['enddate']);//workdate2($op['startdate'],$op['enddate']);
				}
				$op["startdate"]=str_replace("/","",$op["startdate"]);
				$op["enddate"]=str_replace("/","",$op["enddate"]);
				$op["applyno"]=$applyno;
				$op['nday']=$nday;
				$op['hday']=$days-$nday;

				//dd($op);
				unset($op["_token"]);
				unset($op["delete"]);
				unset($op["applyno"]);
				unset($op["_method"]);
				DB::table("edu_loanplacelst")->where('id',$id)->update($op);
			} else {
				return redirect()->route("webbook.place.get",$applyno)->with('result', '1')->with('message', '申請借用場地重覆！');
			}
		}
		return redirect()->route("webbook.edit.get",$applyno)->with("result",1)->with("message","修改成功!");
	}

	//寢室安排
	public function bed(Request $request)
	{
		$data=[];
		$applyno=$request->input("applyno");
		$croomclsno=$request->input("croomclsno");
		$id=$request->input("id");
		$edu_loanplace=$this->wbs->get_edu_loanplace($applyno);
		$place=$this->wbs->get_edu_loanplacelst_by_id($id);
		$mode='bed';
		$book_info=$this->wbs->getBookRoomList($id,$mode);
		$book_info=$book_info->toArray();
		$croomclsno_arr=['20','24','21'];
		//dd($book_info);
		if(in_array($croomclsno,$croomclsno_arr)){
			$list=$this->wbs->get_room_bed($applyno,$place[0]["croomclsno"],$place[0]["startdate"],$place[0]["enddate"]);
			$list=$list->toArray();
			$title = '場地管理-寢室安排';
			//dd($list);
		}else{
			$applydate=$request->input("applydate");
			$list2=$this->wbs->get_room_bed2($applyno,$place[0]["croomclsno"],$place[0]["startdate"],$place[0]["timestart"],$place[0]["timeend"]);
			$title = '場地管理-借出場地安排';
			//dd($list2->toArray());
		}

		return view("/admin/webbookplace/bed",compact("data","place","book_info","list","list2","id","title","edu_loanplace"));
	}
	//寢室安排 新增修改
	public function bed_post(Request $request)
	{
		//var_dump($request->input());
		$input=$request->input();
		$croomclsno_arr=['20','24','21'];

		if(in_array($input["croomclsno"],$croomclsno_arr)){
			$final=[];
			$checkbox=$request->input("checkboxs");
			if(is_array($checkbox)){
				for($i=0;$i<count($checkbox);$i++){
					$final_input[$i]=explode("_",$checkbox[$i]);
				}
				for($j=0;$j<count($final_input);$j++){
					$final_input[$j]["sex"]=$input[$final_input[$j][0]];
					$final_input[$j]["applyno"]=$input["applyno"];
					$final_input[$j]["startdate"]=$input["startdate"];
					$final_input[$j]["enddate"]=$input["enddate"];
					if($input["enddate"]==null){
						$final_input[$j]["enddate"]=$input["startdate"];
					}
					$final_input[$j]["croomclsno"]=$input["croomclsno"];
					$final_input[$j]["bedroom"]=$final_input[$j][0];
					$final_input[$j]["floorno"]=$final_input[$j][1];
					$final_input[$j]["bedamount"]=$final_input[$j][2];
					unset($final_input[$j][0]);
					unset($final_input[$j][1]);
					unset($final_input[$j][2]);
					$bedamount=$final_input[$j]["bedamount"];
					for($k=1;$k<=$bedamount;$k++){
						$final_input[$j]["bedno"]=$final_input[$j]["bedroom"].$k;
						unset($final_input[$j]["bedamount"]);
						array_push($final,$final_input[$j]);
					}
				}
			}
			$this->wbs->delete_loansroom($input["applyno"],$input["croomclsno"],$input["startdate"]);
			$this->wbs->save_loansroom($final);
			if ($input['applykind']=='1'){//外部單別
				$fee=$this->wbs->get_clsroomfee($input["croomclsno"]);//收費設定
				//dd($input["id"]);
				$this->wbs->update_for_fee($input["id"],$input["applyno"],$input["croomclsno"],$fee,$input['startdate']);//費用計算
			}
		}else{
			//dd($input);
			$final_input=[];
			$roomno=$request->input("roomno");
			if(is_array($roomno)){
				for($i=0;$i<count($roomno);$i++){
					$final_input[$i]["applyno"]=$input["applyno"];
					$final_input[$i]["applydate"]=$input["startdate"];
					$final_input[$i]["starttime"]=$input["timestart"];
					$final_input[$i]["endtime"]=$input["timeend"];
					$final_input[$i]["croomclsno"]=$input["croomclsno"];
					$final_input[$i]["classroomno"]=$roomno[$i];
				}
			}

			$this->wbs->delete_loanroom($input["applyno"],$input["croomclsno"],$input["startdate"],$input["timestart"],$input["timeend"]);
			$this->wbs->save_loanroom($final_input);
			if ($input['applykind']=='1'){//外部單別
				$fee=$this->wbs->get_clsroomfee($input["croomclsno"]);//收費設定
				//dd($input["id"]);
				$this->wbs->update_for_fee($input["id"],$input["applyno"],$input["croomclsno"],$fee,$input['startdate']);//費用計算
			}
		}

		return redirect()->route("webbook.edit.get",$input["applyno"])->with("result",1)->with("message","修改成功");
		//var_dump($final_input);
	}


    //金額頁面(詳細)
    public function editProcessSub2(Request $request)
    {
        $op=[];
        $op=$request->input();
        if ($op){
			if ((isset($op['back'])) or (isset($op['cancel']))){
				return back();
			}else {
				if (isset($op['save'])){
               		$rop=$this->wbs->save_for_spaceprocessub($op);
					//DB::table("")$rop);
				}else{
					if (isset($_POST['discount'])){//批次折扣
						$rop=$this->wbs->save_for_spaceprocessub($op,true);
						//$this->loanplace_model->save($rop);
                        //redirect($parentnode);
                        return back();
					}
				}
			}

			$row=(object)$op;
        }else{
			$data=$this->wbs->get_edu_loanplacelst($op["applyno"]);
        }

    }

    //新增外部單別、內部單別
    public function editProcess($applyno=0,$applykind=1)
    {
        $parentnode="space/Process";
		$data=array();
		$op=array();
		$row=false;
		if ($applykind==1){//外部申請單
			$this->SetSimpleRequired(array('orgname','applyuser','title','num','reason','tel','applydate'));
        }else{ //內部申請單
			$this->SetSimpleRequired(array('orgname','applyuser','title','num','reason','tel','applydate','croomclsno','startdate','enddate','timestart','timeend'));
        }

		if ($this->input->post()){
			$op=$this->input->post();
			if (isset($_POST['cancel'])){
				redirect($parentnode);
			}else if (isset($_POST['save'])){
                if ($this->form_validation->run()!=FALSE){
                	if (intval($op['applyno'])==0){
                		$op['applyno']=$this->loanplace_model->createApplyno();
                	}
					//儲存時.如果沒有流水號,就產生一個,審核中才產生....20120726
					if ((intval($op['waterno'])==0) && ($op['status']=='2')){
						$op['waterno']=$this->loanplace_model->createWaterno(substr($op['applydate'], 0,3));
					}
					$this->loanplace_model->save($op);

                	if ($op['applykind']!=1) {//外部單別儲存明細資料
                		$lstop=array();

                		$row=SingleRowOrDefault($this->loanplacelst_model->get_by(array('edu_loanplacelst.applyno'=>$op['applyno'])));
						if ($row) $lstop['id']=$row->id;
						else  $lstop['id']=0;

						$lstop['applyno']=$op['applyno'];
						$lstop['startdate']=$op['startdate'];
						$lstop['enddate']=$op['enddate'];
						$lstop['timestart']=$op['timestart'];
						$lstop['timeend']=$op['timeend'];
						$lstop['croomclsno']=$op['croomclsno'];
						$lstop['placenum']=$op['placenum'];
						//平日，假日
						$days=howmanydays($lstop['startdate'],$lstop['enddate']);
						$nday=$this->holiday_model->workdate($lstop['startdate'],$lstop['enddate']);
						$lstop['nday']=$nday;
						$lstop['hday']=$days-$nday;
						$this->loanplacelst_model->save($lstop);
                	}

					redirect($parentnode);
				}
				$row=(object)$op;
			}else if (isset($_POST['delete'])){
                $this->loanplace_model->deleteby(array('id'=>$op['id']));
				$this->loanplacelst_model->deleteby(array('applyno'=>$op['applyno']));
                redirect($parentnode);
            }
		}else{
			if ($applyno>0){
				if ($applykind==1)
					$row=SingleRowOrDefault($this->loanplace_model->get_by(array('edu_loanplace.applyno'=>$applyno)));
				else $row=SingleRowOrDefault($this->loanplace_model->get_by2(array('edu_loanplace.applyno'=>$applyno)));
			}
			if (!$row){
				$row=new stdClass();
				$row->id=0;
				$row->applydate=rocdate();
				$row->num=0;
				$row->applyno=0;
				$row->loginip='管理單位登錄';
				$row->status='N';
				$row->discount1=0;
				$row->discount2=0;
				$row->passwd=substr(strval(time()),0,9);
				$row->applykind=$applykind;
				$row->waterno='';
				$row->discounttype='';//101.08.07加入
			}
		}

		if ($applykind==1) $data['title']='場地管理-申請單基本資料';
		else $data['title']='場地借用處理(內部)';
		//var_dump($op);
		$data['vd']=$row;
	}


    //批次折扣設定
    public function arg_set(Request $request,$id=0)
    {
		$data=$this->wbs->getArg();
		return view("/admin/webbookplace/arg_set",compact('data'));
	}

	//批次折扣設定 修改
	public function arg_modify(Request $request,$id)
	{
		$data=$this->wbs->getArg($id);
		$mode='put';
		if($request->input()){
			$op=$request->input();
			unset($op["_method"]);
			unset($op["_token"]);
			if($op["delete"]==0){
				DB::table('edu_classcode')->where("class",64)->where("id",$id)->update($op);
				return redirect()->route('webbook.arg.get')->with("result",1)->with("message","修改成功");
			}else{
				DB::table('edu_classcode')->where("class",64)->where("id",$id)->delete();
				return redirect()->route('webbook.arg.get')->with("result",1)->with("message","刪除成功");
			}

		}
		return view("/admin/webbookplace/arg_add",compact('data','mode'));
	}

	//批次折扣設定 新增
	public function arg_add(Request $request)
	{
		$mode='post';
		if($request->input()){
			$myclasscode='64';
			$op=$request->input();
			if (empty($op['code'])){
				$op['code']=$this->wbs->createCode($myclasscode);
			}
			unset($op['_token']);
			unset($op['delete']);
			$op['class']=64;
			DB::table('edu_classcode')->insert($op);
			return redirect()->route('webbook.arg.get')->with("result",1)->with("message","新增成功");
		}
		return view("/admin/webbookplace/arg_add",compact('mode'));
	}


}

