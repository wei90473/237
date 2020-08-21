<?php
namespace App\Services;

use App\Repositories\WebBookPlaceRepository;
use App\Repositories\BookPlaceRepository;
use App\Models\Edu_classcode;
use App\Models\Edu_loanplacelst;
use App\Models\Edu_holiday;
use App\Models\Edu_loanplace;
use App\Models\Edu_classroomcls;
use App\Models\Edu_loansroom;
use App\Models\Edu_loanroom;
use App\Models\Edu_classterm;
use App\Models\Edu_signup;
use App\Models\Edu_coursedt;
use App\Models\Edu_clsroomfee;
use App\Models\Edu_bed;
use App\Models\T97tb;
use DB;

class WebBookPlaceService
{
    public function __construct(WebBookPlaceRepository $WebBookPlaceRepository,BookPlaceRepository $bpr)
    {
		$this->wbr = $WebBookPlaceRepository;
		$this->bpr = $bpr;
    }

    public function getList($condition)
    {
        return $this->wbr->getList($condition);
	}
	public function getTpaList($condition)
    {
        return $this->wbr->getTpaList($condition);
	}

	public function getBorrowForDropdown()
	{
		return $this->wbr->getBorrowForDropdown();
	}

	public function checkloanplacelst($applyno,$startdate,$timestart,$croomclsno,$id=0)
	{
		$startdate = str_replace("/","",$startdate);
		return $this->wbr->checkloanplacelst($applyno,$startdate,$timestart,$croomclsno,$id);
	}

	public function updateApplyLimit($spaceappday){
		$temp['spaceappday'] = $spaceappday;
		\DB::transaction(function () use ($temp){
			DB::table('edu_unitset')->where('id',1)->update($temp);
		});
	}

	public function getSpaceappday(){
		return $this->wbr->getSpaceappday();
	}

	public function saveConfirmFee($confire_fee,$applyno)
	{
		\DB::transaction(function () use ($confire_fee,$applyno){
			DB::table('edu_loanplace')->where('applyno',$applyno['applyno'])->update($confire_fee);
		});
	}

    public function save_for_spaceprocessub($op,$batch=false)
    {
        $updateop=array();
		$discount1=0;
		$discount2=0;//平日折扣
		$ids=isset($op['ids'])? $op['ids']:array();		
		$ndiscounts=isset($op['ndiscounts'])?$op['ndiscounts']:array();
		$hdiscounts=isset($op['hdiscounts'])?$op['hdiscounts']:array();
		$nfees=isset($op['nfees'])?$op['nfees']:array();
		$hfees=isset($op['hfees'])?$op['hfees']:array();
		$count=count($ids);
		$discount_data=DB::table("edu_classcode")->where("class",64)->where("code",$op["discounttype"])->first();
		
		$final_input=[];
		for($i=0;$i<$count;$i++){
			if ($batch){//批次設定
				$updateop['ndiscount']=intval($nfees[$i])*(1-$discount_data->param1);
				$updateop['hdiscount']=intval($hfees[$i])*(1-$discount_data->param1);
			}else{
				$updateop['ndiscount']=intval($ndiscounts[$i]);
				$updateop['hdiscount']=intval($hdiscounts[$i]);				
			}
			$updateop['id']=$ids[$i];
			array_push($final_input,$updateop);
			$discount1=$discount1+$updateop['hdiscount'];
			$discount2=$discount2+$updateop['ndiscount'];					
		}
		//var_dump($op);
		//dd($final_input);
		\DB::transaction(function () use ($final_input){
			foreach($final_input as $temp){
				DB::table('edu_loanplacelst')->where('id',$temp["id"])->update($temp);
			}
		});
		
		$rop=array();
		$rop['applyno']=$op['applyno'];
		$rop['discount1']=$discount1;
		$rop['discount2']=$discount2;
		return $rop;
	}

	public function save_to_taipei($applyno)
	{
		$data = $this->getEduLoanroom($applyno);

		\DB::transaction(function () use ($data){
			$tmp_cnt = count($data);
			for($i=0;$i<$tmp_cnt;$i++){
				$tmp_array = array();
				$tmp_array['site'] = $data[$i]['classroomno'];
				$tmp_array['date'] = $data[$i]['applydate'];
				$tmp_array['stime'] = $data[$i]['starttime'];
				$tmp_array['etime'] = $data[$i]['endtime'];
				$tmp_array['time'] = 'D';
				$tmp_array['croomclsno'] = $data[$i]['croomclsno'];
				$tmp_array['applyno'] = $data[$i]['applyno'];

				DB::table('t97tb')->insert($tmp_array);
			}	
		});
	}

	public function get_webbook_parameter($userid)
	{
		return $this->wbr->get_webbook_parameter($userid);
	}

	public function get_webbook_parameter_by_id($ids)
	{
		return $this->wbr->get_webbook_parameter_by_id($ids);
	}

	public function delete_loansroom($applyno,$croomclsno,$startdate)
	{
		return $this->wbr->delete_loansroom($applyno,$croomclsno,$startdate);
	}
	

	public function delete_loanroom($applyno,$croomclsno,$startdate,$timestart,$timeend)
	{
		return $this->wbr->delete_loanroom($applyno,$croomclsno,$startdate,$timestart,$timeend);
	}
	public function save_loansroom($final)
	{
		// \DB::transaction(function () use ($final){
		// 	foreach($final as $temp){
		// 		DB::table('edu_loansroom')->where('applyno',$temp["applyno"])->where('startdate',$temp["startdate"])->delete();
		// 	}
		// });
		DB::table('edu_loansroom')->insert($final);		
	}


	public function save_loanroom($final_input)
	{
		// \DB::transaction(function () use ($final_input){
		// 	foreach($final_input as $temp){
		// 		DB::table('edu_loanroom')->where('applyno',$temp["applyno"])->where('croomclsno',$temp["croomclsno"])->delete();
		// 	}
		// });
		DB::table('edu_loanroom')->insert($final_input);	
	}

	public function get_edu_loanplace($applyno)
	{
		return $this->wbr->get_edu_loanplace($applyno);
	}

	public function get_edu_loanplace_by_applyno($applyno)
	{
		return $this->wbr->get_edu_loanplace_by_applyno($applyno);
	}

    public function get_edu_loanplacelst($applyno)
    {
        return $this->wbr->get_edu_loanplacelst($applyno);
	}

	public function get_Tpa_loanplacelst($condition =[] )
    {
        return $this->wbr->get_Tpa_loanplacelst($condition);
	}

	public function get_edu_loanplacelst_by_id($id)
	{
		return $this->wbr->get_edu_loanplacelst_by_id($id);
	}

	public function getCountLoanRoom($applyno,$croomclsno,$applydate=''){
		return $this->wbr->getCountLoanRoom($applyno,$croomclsno,$applydate);
	}

	public function getCountLoansRoom($applyno,$croomclsno,$startdate=''){
		return $this->wbr->getCountLoansRoom($applyno,$croomclsno,$startdate);
	}

	public function getEduLoanroom($applyno){
		return $this->wbr->getEduLoanroom($applyno);
	}

	public function get_room_bed($applyno,$croomclsno,$sdate,$edate)
	{
		return $this->wbr->get_room_bed($applyno,$croomclsno,$sdate,$edate);
	}
	public function get_room_bed2($applyno,$croomclsno,$applydate,$stime,$etime)
	{
		return $this->wbr->get_room_bed2($applyno,$croomclsno,$applydate,$stime,$etime);
	}

	public function getBookRoomList($id,$mode)
	{
		return $this->wbr->getBookRoomList($id,$mode);
	}

	public function update_for_fee($id,$applyno,$croomclsno,$feequery,$sdate='')
	{
		
		$roomq=Edu_loanplacelst::select("edu_loanplacelst.id","edu_loanplacelst.applyno","edu_loanplacelst.croomclsno","edu_loanplacelst.startdate"
										,"edu_loanplacelst.enddate","edu_loanplacelst.timestart","edu_loanplacelst.timeend","edu_loanplacelst.hday"
										,"edu_loanplacelst.nday","edu_classroomcls.classroom",DB::raw("count(edu_loanroom.id) AS roomcount")
										,DB::raw("count(edu_loansroom.id) AS bedcount"));
		$roomq->leftJoin("edu_loanroom",function($join){
			$join->on("edu_loanplacelst.applyno","edu_loanroom.applyno");
			$join->on("edu_loanplacelst.croomclsno","edu_loanroom.croomclsno");
			$join->on("edu_loanplacelst.startdate","edu_loanroom.applydate");
		});
		$roomq->leftJoin("edu_loansroom",function($join){
			$join->on("edu_loanplacelst.applyno","edu_loansroom.applyno");
			$join->on("edu_loanplacelst.croomclsno","edu_loansroom.croomclsno");
			$join->on("edu_loanplacelst.startdate","edu_loansroom.startdate");
		});
		$roomq->leftJoin("edu_classroomcls",function($join){
			$join->on("edu_loanplacelst.croomclsno","edu_classroomcls.croomclsno");
		});
		$roomq->where("edu_loanplacelst.id",$id);
		$roomq->groupBy("edu_loanplacelst.applyno");
		$roomq->groupBy("edu_loanplacelst.croomclsno");
		$data_roomq=$roomq->get();
	
		$sroomq=Edu_loansroom::select("edu_loansroom.bedroom");
		$sroomq->where("edu_loansroom.applyno",$applyno)->where("edu_loansroom.croomclsno",$croomclsno)->where("edu_loansroom.startdate",$sdate);
		$sroomq->groupBy("edu_loansroom.bedroom");
		$data_sroomq=$sroomq->get();
											
		$nfee=0;
		$hfee=0;
		$newfee=false;
		
		if (!empty($data_roomq)){
			$stime=$data_roomq[0]->timestart;
			$etime=$data_roomq[0]->timeend;
			$roomcount=$data_roomq[0]->roomcount;
			$bedcount=$data_roomq[0]->bedcount;
			$hday=$data_roomq[0]->hday;
			$nday=$data_roomq[0]->nday;
			
			//計算房間數									
			//$sq=SingleRowOrDefault($sroomq);
			$sroomcount=0;
			if (!empty($data_sroomq)){
				foreach($data_sroomq as $sq){
					$sroomcount++;
				}
			}
			//var_dump($feequery);die;
			foreach($feequery as $feeq){
				if ($feeq->feetype==4){
					$newfee=true;
					if ((($stime >= $feeq->param1) AND ($stime <= $feeq->param2)) OR
						(($stime < $feeq->param1) AND ($etime >= $feeq->param1))){
						//edit by julie 102.1.15(($etime >= $feeq->param1) AND ($etime <= $feeq->param2))){
							$nfee=$nfee+($roomcount*$feeq->fee*$nday);
							$hfee=$hfee+($roomcount*$feeq->holidayfee*$hday);
					}
				}
			}
			
			if (!$newfee){
				foreach($feequery as $feeq){
					switch ($feeq->feetype) {
						case '1':
							if ($feeq->timetype='201'){//半日
								if ($etime-$stime <= 1200){
									$nfee=$nfee+($roomcount*$feeq->fee*$nday);
									$hfee=$hfee+($roomcount*$feeq->holidayfee*$hday);
								}
							}else{//全日
								if ($etime-$stime > 1200){
									$nfee=$nfee+($roomcount*$feeq->fee*$nday);
									$hfee=$hfee+($roomcount*$feeq->holidayfee*$hday);
								}							
							}
							break;
						case '2'://每間每小時
							//$hours=
							break;
						case '3'://每人每日
								$nfee=$nfee+($bedcount*$feeq->fee*$nday);
								$hfee=$hfee+($bedcount*$feeq->holidayfee*$hday);
							break;											
						case '5'://每間每日
								$nfee=$nfee+($sroomcount*$feeq->fee*$nday);
								$hfee=$hfee+($sroomcount*$feeq->holidayfee*$hday);
							break;
					}
				}	
			}
		}

		$updateop=array();
		$updateop['id']=$data_roomq[0]->id;
		$updateop['nfee']=$nfee;
		$updateop['hfee']=$hfee;
		$updateop['fee']=$nfee+$hfee;
		if ($data_roomq[0]->classroom != 1){
			$updateop['placenum']=$sroomcount; //101.08.07 更新借用寢室間數,教室不更新
		}

		DB::table("edu_loanplacelst")->where("id",$updateop["id"])->update($updateop);
	}

	public function get_clsroomfee($croomclsno)
	{
		return $this->wbr->get_clsroomfee($croomclsno);
	}
	
	public function workdate($sdate,$edate='')
	{
    	if (empty($edate)){
    		$edate=$sdate;
    	}
		//$sql="select count(0) as days from edu_holiday where holiday between {$sdate} and {$edate}";
		$params["sdate"]=$sdate;
		$params["edate"]=$edate;
		$params["mode"]="workdate";
		$hd=$this->wbr->getHoliday($params);

		if ($sdate==$edate){
			return 1-$hd;
		}else{
			$vids_sdate=$this->bpr->convertYear($sdate,'twtovids');
			$vids_edate=$this->bpr->convertYear($edate,'twtovids');
			$days=((strtotime($vids_edate) - strtotime($vids_sdate))/ (60*60*24))+1; //計算相差之天數
			return $days-$hd;
		}
	}

	public function workdate3($sdate,$edate='')
	{
    	if (empty($edate)){
    		$edate=$sdate;
		}

		$vids_sdate=$this->bpr->convertYear($sdate,'twtovids');
		$vids_edate=$this->bpr->convertYear($edate,'twtovids');
		$days=floor((strtotime($vids_edate) - strtotime($vids_sdate))/ (60*60*24))+1; //計算相差之天數
		$edate2=date('Y/m/d',strtotime("+1 day",strtotime($vids_edate)));

		$params["sdate"]=$sdate;
		$params["edate"]=$edate2;
		$params["mode"]="workdate3";
		$query=$this->wbr->getHoliday($params);

		$hd=0;
		for($i=0;$i<=$days-1;$i++){//逐天判斷
			$sd = date('Y/m/d',strtotime("+{i} day",strtotime($vids_sdate)));
			$sd = $this->bpr->convertYear($sd,'vidstotw');
			$sd = str_replace("/","",$sd);

			foreach($query as $q){
				if ($q['holiday']==$sd){
					$hd++;
					break;
				}
			}
		}

		unset($query);
		return $days-$hd;
	}
	
	public function createCode($myclasscode)
	{
		return $this->createMaxCode('code','edu_classcode',array('class'=>$myclasscode));
	}

	public function createMaxCode($field,$table,$op=array())
	{
		
		$query=Edu_classcode::select(DB::raw("max(code) as code"));
		$query->where('class',$op['class']);
		$data=$query->get();
		if($data[0]->code!=null){
			$code=$data[0]->code;
			if (is_numeric($code)){
				$len=strlen($code);
				$n=strval(intval($code)+1);		
				return str_pad($n, $len,'0',STR_PAD_LEFT); 
			}else{
			    return $code;
			}
		}
		return '';
		
	}

	public function getArg($id = null)
	{
		return $this->wbr->getArg($id);
	}

	public function convertYear($year,$mode)
	{
		return $this->bpr->convertYear($year,$mode);
	}

   	public function getClassroom($croomclsno)
   	{
   		return $this->wbr->getClassroom($croomclsno);
   	}
   	
   	public function createSit($queryData = []){
   		return $this->wbr->createSit($queryData);
   	}

   	public function deleteEduLoanplace($applyno){
   		Edu_loanplace::where('applyno',$applyno)->delete();
   	}

   	public function deleteEduLoanplacelst($applyno){
   		Edu_loanplacelst::where('applyno',$applyno)->delete();
   	}

   	public function deleteEduLoanroom($applyno){
   		Edu_loanroom::where('applyno',$applyno)->delete();
   	}

   	public function deleteEduLoansroom($applyno){
   		Edu_loansroom::where('applyno',$applyno)->delete();
   	}

   	public function deleteT97tb($applyno){
   		T97tb::where('applyno',$applyno)->delete();
   	}
}
