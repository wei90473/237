<?php
namespace App\Repositories;

use App\Models\Edu_classcode;
use App\Models\Edu_loanplacelst;
use App\Models\Edu_unitset;
use App\Models\Edu_holiday;
use App\Models\Edu_loanplace;
use App\Models\Edu_classroomcls;
use App\Models\Edu_loansroom;
use App\Models\Edu_loanroom;
use App\Models\Edu_classterm;
use App\Models\Edu_signup;
use App\Models\Edu_coursedt;
use App\Models\Edu_clsroomfee;
use App\Models\Webbook_parameter;
use App\Models\Edu_bed;
use App\Models\T21tb;
use App\Models\T22tb;
use App\Models\T37tb;
use App\Models\T38tb;
use App\Models\S02tb;
use App\Models\T97tb;
use DB;


class WebBookPlaceRepository
{
    public function getList($condition)
    {
        $query=Edu_loanplace::select("edu_loanplace.id","edu_loanplace.applyno","edu_loanplace.applydate","edu_loanplace.applyuser","edu_loanplace.orgname"
                                    ,"edu_loanplace.title","edu_loanplace.email","edu_loanplace.tel","edu_loanplace.fax","edu_loanplace.cellphone"
                                    ,"edu_loanplace.applykind","edu_loanplace.num","edu_loanplace.mstay","edu_loanplace.fstay","edu_loanplace.processdate"
                                    ,"edu_loanplace.reason","edu_loanplace.reason2","edu_loanplace.chief1","edu_loanplace.chief2","edu_loanplace.description"
                                    ,DB::raw("(edu_loanplace.discount1+edu_loanplace.discount2) AS discount"),"edu_loanplace.status","edu_loanplace.receiptno"
                                    ,"edu_loanplace.paydate","edu_loanplace.payuser","edu_classcode.name as statusname"
                                    ,DB::raw("count(edu_loanplacelst.applyno) as detail")
                                    ,DB::raw("(sum(edu_loanplacelst.fee)-(edu_loanplace.discount1+edu_loanplace.discount2)) as fee"));
        $query->leftJoin("edu_loanplacelst",function($join){
            $join->on("edu_loanplace.applyno","=","edu_loanplacelst.applyno");
        });
        $query->leftJoin("edu_classcode",function($join){
            $join->on("edu_loanplace.status","=","edu_classcode.code");
        });
        if(!empty($condition)){
            if(isset($condition["orgname"])){
                if($condition["orgname"]!=''){
                    $query->where("edu_loanplace.orgname",'like',"%".$condition['orgname']."%");
                }
            }
            if(isset($condition["applyuser"])){
                if($condition["applyuser"]!=''){
                    $query->where("edu_loanplace.applyuser","like","%".$condition['applyuser']."%");
                }
            }
            if(isset($condition["status"])){
                $query->where("edu_loanplace.status",$condition["status"]);
            }else{
                $query->where("edu_loanplace.status",1);
            }
        
            if(isset($condition["date1"]) && isset($condition["date2"])){
                if($condition["date1"]!='' && $condition["date2"]!=''){
                    $query->whereBetween("edu_loanplace.applydate",[$condition['date1'],$condition['date2']]);
                }
            }
        }else{
            $query->where("edu_loanplace.status",1);
        }
        $query->where("edu_classcode.class",'59');
        $query->groupBy("edu_loanplace.applyno");
        $query->groupBy("edu_loanplace.applydate");
        $query->groupBy("edu_loanplace.id");
        //$query->orderBy("edu_loanplace.applydate","desc");
        $result=$query->paginate(10);
        /*$result=$query->get();
        $result=$result->toArray();*/
        return $result;
    }

    public function getTpaList($condition = []){
        $query = T38tb::select('*');
        // 申請單位
        if ( isset($condition['name']) && $condition['name'] ) {
            
            $query->where('name', 'like', '%'.$condition['name'].'%');
        }

        // 申請單處理狀態
        if ( isset($condition['result']) && $condition['result']!='ALL' ) {

            $query->where('result', $condition['result']);
        }
        // 日期
        if ( isset($condition['sdate']) && $condition['sdate'] &&  isset($condition['edate']) && $condition['edate']) {

            $query->whereBetween('applydate', array($condition['sdate'],$condition['edate']) );
        }
        // 申請編號
        if ( isset($condition['meet']) && $condition['meet'] ) {
            $query->where('meet', $condition['meet']);
        }else{
            $query->where('meet', 'like', 'I%');
        }
        // 申請編號
        if ( isset($condition['serno']) && $condition['serno'] ) {
            
            $query->where('serno', $condition['serno']);
        }

        

        $result = $query->paginate((isset($condition['_paginate_qty']) && $condition['_paginate_qty'])? $condition['_paginate_qty'] : 20);

        return $result;
    }

    public function createSit($queryData = []){
        DB::beginTransaction();
            try{
            $S02 = S02tb::select('weekly','monthly','tmst','tmet','tast','taet','tnst','tnet')->first();
            if(isset($queryData['stime']) && isset($queryData['etime'])){

            }elseif($queryData['time']=='A'){
                $queryData['stime']=$S02['tmst'];
                $queryData['etime']=$S02['tmet'];
            }elseif($queryData['time']=='B'){
                $queryData['stime']=$S02['tast'];
                $queryData['etime']=$S02['taet'];
            }elseif($queryData['time']=='C'){
                $queryData['stime']=$S02['tnst'];
                $queryData['etime']=$S02['tnet'];
            }

            T21tb::create($queryData);
            $data = T38tb::where('meet',$queryData['meet'])->where('serno',$queryData['serno'])->first();
            $sdateVids = (substr($data['date'], 0,-4)+1911).substr($data['date'], -4);
            $affirmVids = date('Ymd',strtotime($sdateVids .' -'.(date('w',strtotime($sdateVids))+8-$S02['weekly']).'Day')); //確認凍結日
            $requestVids = date('Ym',strtotime($sdateVids .' -1 month')).$S02['monthly'];  //需求凍結日
            $queryData['class'] = $queryData['meet'];
            $queryData['term']  = $queryData['serno'];
            unset($queryData['meet'],$queryData['serno']);
            $queryData['cnt']       = $data['cnt'];
            $queryData['reserve']   = $data['sponsor'];
            $queryData['liaison']   = $data['liaison'];
            $queryData['purpose']   = $data['remark'];
            $queryData['fee']       = $data['totalfee'];
            $queryData['request']   = (date('Y',strtotime($requestVids))-1911).date('md',strtotime($requestVids)); //需求凍結日
            $queryData['type'] = '2';
            T37tb::create($queryData);
            unset($queryData['type']);
            $queryData['affirm']    = (date('Y',strtotime($affirmVids))-1911).date('md',strtotime($affirmVids)); //確認凍結日
            $queryData['status']    = 'N';
            $queryData['email']     = $data['email'];
            T22tb::create($queryData);
            DB::commit();
            return TRUE;
        }catch ( Exception $e ){
            DB::rollback();
            return FALSE; 
        }
    }
    public function getSpaceappday()
    {
        $query=Edu_unitset::selectRaw("spaceappday");
        $query->where('id','=','1');
        $result=$query->get();
        $result=$result->toArray();

        return $result[0]['spaceappday'];
    }

    public function checkloanplacelst($applyno,$startdate,$timestart,$croomclsno,$id=0)
    {
        $query=Edu_loanplacelst::selectRaw("count(1) cnt");
        $query->where("applyno",$applyno);
        $query->where("startdate",$startdate);
        $query->where("timestart",$timestart);
        $query->where("croomclsno",$croomclsno);

        if($id > 0){
            $query->where("id",'<>',$id);
        }

        $result=$query->get();
        $result=$result->toArray();
        
        return $result[0]['cnt'];
    }

    public function getClassroom($croomclsno)
    {
        $query = Edu_classroomcls::selectRaw('classroom,croomclsfullname');
        $query->where('croomclsno','=',$croomclsno);
        $result=$query->get();

        return $result;
    }

    public function getBorrowForDropdown()
    {
        $query=Edu_classroomcls::selectRaw('edu_classroomcls.*,edu_classcode.name AS classroomname');
        $query->leftJoin("edu_classcode",function($join){
                $join->on("edu_classroomcls.classroom","=","edu_classcode.code");
                $join->where("edu_classcode.class","=",76);
            });
        $query->orderByRaw('edu_classroomcls.printseq,edu_classroomcls.croomclsname');
        $query->where('borrow','=','1');

        $result=$query->get();

        return $result;
    }

    public function getBookRoomList($id,$mode)
    {
        /*$query=$this->db->select('edu_loanplacelst.*
								 ,edu_loanplace.applykind,edu_loanplace.num,edu_loanplace.mstay,edu_loanplace.fstay
								 ,edu_classroomcls.croomclsname,edu_classroomcls.croomclsfullname,edu_classroomcls.classroom
							     ,ts.name as timestartname,te.name as timeendname')
						->from('edu_loanplacelst')
						->join('edu_loanplace','edu_loanplacelst.applyno=edu_loanplace.applyno','inner')
						->join('edu_classroomcls','edu_loanplacelst.croomclsno=edu_classroomcls.croomclsno','left')
						->join('edu_classcode ts','edu_loanplacelst.timestart=ts.code and ts.class=60','left')
						->join('edu_classcode te','edu_loanplacelst.timeend=te.code and te.class=61','left')
						->where($qo)
						->order_by('edu_loanplacelst.applyno,edu_loanplacelst.croomclsno')
						->get()
                        ->result();*/
                        
        $query=Edu_loanplacelst::select("edu_loanplacelst.*","edu_loanplace.applykind","edu_loanplace.num","edu_loanplace.mstay","edu_loanplace.fstay"
                                        ,"edu_classroomcls.croomclsname","edu_classroomcls.croomclsfullname","edu_classroomcls.classroom"
                                        ,"ts.name as timestartname","te.name as timeendname");
        $query->join("edu_loanplace","edu_loanplacelst.applyno","=","edu_loanplace.applyno");
        $query->leftJoin("edu_classroomcls","edu_loanplacelst.croomclsno","=","edu_classroomcls.croomclsno");
        $query->leftJoin("edu_classcode as ts",function($join){
            $join->on("edu_loanplacelst.timestart","=","ts.code");
            $join->where("ts.class","=",60);
        });
        $query->leftJoin("edu_classcode as te",function($join){
            $join->on("edu_loanplacelst.timeend","=","te.code");
            $join->where("te.class","=",61);
        });
        if($mode=='bed'){
            $query->where("edu_loanplacelst.id",$id);
        }else{
           $query->where("edu_loanplacelst.applyno",$id); 
        }
        
        $result=$query->get();
        //$data=$data->toArray();

		/*$roomquery=$this->db->select('edu_loanplacelst.applyno,edu_loanplacelst.croomclsno,edu_loanplacelst.startdate,edu_loanplacelst.timestart,edu_loanplacelst.enddate,
									  count(edu_loanroom.id) AS room')
							->from('edu_loanplacelst')
							->join('edu_loanroom','edu_loanplacelst.applyno=edu_loanroom.applyno AND edu_loanplacelst.croomclsno=edu_loanroom.croomclsno AND edu_loanplacelst.startdate=edu_loanroom.applydate','INNER')
							->where($qo)
							->group_by('edu_loanplacelst.applyno,edu_loanplacelst.croomclsno,edu_loanplacelst.startdate,edu_loanplacelst.timestart,edu_loanplacelst.enddate')
							->order_by('edu_loanplacelst.applyno,edu_loanplacelst.croomclsno,edu_loanplacelst.startdate,edu_loanplacelst.timestart,edu_loanplacelst.startdate')
							->get()
                            ->result();*/
        $roomquery=Edu_loanplacelst::select("edu_loanplacelst.applyno","edu_loanplacelst.croomclsno","edu_loanplacelst.startdate","edu_loanplacelst.timestart"
                                            ,"edu_loanplacelst.enddate",DB::raw("count(edu_loanroom.id) AS room"));

        $roomquery->join("edu_loanroom",function($join){
            $join->on("edu_loanplacelst.applyno","=","edu_loanroom.applyno");
            $join->on("edu_loanplacelst.croomclsno","=","edu_loanroom.croomclsno");
            $join->on("edu_loanplacelst.startdate","=","edu_loanroom.applydate");
        });
        if($mode=='bed'){
            $query->where("edu_loanplacelst.id",$id);
        }else{
           $query->where("edu_loanplacelst.applyno",$id); 
        }
        $roomquery->groupBy("edu_loanplacelst.applyno","edu_loanplacelst.croomclsno","edu_loanplacelst.startdate","edu_loanplacelst.timestart","edu_loanplacelst.enddate");
        $roomquery->orderBy("edu_loanplacelst.applyno","edu_loanplacelst.croomclsno","edu_loanplacelst.startdate","edu_loanplacelst.timestart","edu_loanplacelst.startdate");

        $room_result=$roomquery->get();
        //$room_result=$room_result->toArray();

		/*$sroomquery=$this->db->select('edu_loanplacelst.applyno,edu_loanplacelst.croomclsno,edu_loanplacelst.startdate,
									   edu_loansroom.sex,count(edu_loansroom.id) AS sroom')
							->from('edu_loanplacelst')
							->join('edu_loansroom','edu_loanplacelst.applyno=edu_loansroom.applyno AND edu_loanplacelst.croomclsno=edu_loansroom.croomclsno AND edu_loanplacelst.startdate=edu_loansroom.startdate','inner')
							->where($qo)
							->group_by('edu_loanplacelst.applyno,edu_loanplacelst.croomclsno,edu_loanplacelst.startdate,edu_loansroom.sex')
							->order_by('edu_loanplacelst.applyno,edu_loanplacelst.croomclsno,edu_loanplacelst.startdate')
							->get()
                            ->result();*/
        $sroomquery=Edu_loanplacelst::select("edu_loanplacelst.applyno","edu_loanplacelst.croomclsno","edu_loanplacelst.startdate"
                                            ,"edu_loansroom.sex",DB::raw("count(edu_loansroom.id) AS sroom"));
        
        $sroomquery->join("edu_loansroom",function($join){
            $join->on("edu_loanplacelst.applyno","=","edu_loansroom.applyno");
            $join->on("edu_loanplacelst.croomclsno","=","edu_loansroom.croomclsno");
            $join->on("edu_loanplacelst.startdate","=","edu_loansroom.startdate");
        });
        if($mode=='bed'){
            $query->where("edu_loanplacelst.id",$id);
        }else{
           $query->where("edu_loanplacelst.applyno",$id); 
        }
        $sroomquery->groupBy("edu_loanplacelst.applyno","edu_loanplacelst.croomclsno","edu_loanplacelst.startdate","edu_loansroom.sex");
        $sroomquery->orderBy("edu_loanplacelst.applyno","edu_loanplacelst.croomclsno","edu_loanplacelst.startdate");
        $sroom_result=$sroomquery->get();
							
		foreach($result as $q){
			$q->setstatus='安排';
			if ($q->classroom==1){
				foreach($room_result as $room){
					if (($room->applyno==$q->applyno) && ($room->croomclsno==$q->croomclsno) && ($room->startdate==$q->startdate) 
						&& ($room->timestart==$q->timestart) && ($room->enddate==$q->enddate)){
						$q->setstatus=$room->room.'間';
						break;
					}
				}				
			}else{
				$mcount=0;
				$fcount=0;
				$s='';
				foreach($sroom_result as $sroom){
					if (($sroom->applyno==$q->applyno) && ($sroom->croomclsno==$q->croomclsno) && ($sroom->startdate==$q->startdate)){
						if ($sroom->sex==1) $mcount=$mcount+$sroom->sroom;
						else $fcount=$fcount+$sroom->sroom;
					}
				}				
				if ($mcount>0) $s='男:'.$mcount.'床';
				if (!empty($s)) $s=$s.'<BR>';
				if ($fcount>0) $s=$s.'女:'.$fcount.'床';
				if (!empty($s)) $q->setstatus=$s;
			}
		}

		return $result;
    }

    public function get_clsroomfee($croomclsno)
    {
     
        $query=Edu_clsroomfee::select("edu_clsroomfee.*","edu_classcode.param1","edu_classcode.param2");
        $query->leftJoin("edu_classcode",function($join){
            $join->on("edu_clsroomfee.timetype","edu_classcode.code");
            $join->where("edu_classcode.class",62);
        });
        $query->where("edu_clsroomfee.clsroomno",$croomclsno);
        $query->orderBy("feetype");
        $query->orderBy("timetype");
        $data=$query->get();
        return $data;
    }

    public function get_edu_loanplace($applyno)
    {
        $query=Edu_loanplace::select("*",DB::raw("(sum(edu_loanplacelst.fee)-(edu_loanplace.discount1+edu_loanplace.discount2)) as fee"));
        $query->leftJoin("edu_loanplacelst",function($join){
            $join->on("edu_loanplace.applyno","=","edu_loanplacelst.applyno");
        });
        $query->where("edu_loanplace.applyno",$applyno);
        $data=$query->get();
        $data=$data->toArray();
        return $data;
    }

    public function getEduLoanroom($applyno)
    {
        $query=Edu_loanroom::select("*");
        $query->where("applyno",$applyno);
        $result=$query->get();
        $data=$result->toArray();
        return $data;
    }

    public function get_webbook_parameter($userid)
    {
        $query=Webbook_parameter::select("*");
        $query->where("cre_user",$userid);
        $data=$query->get();
        $data=$data->toArray();
        return $data;
    }

    public function get_webbook_parameter_by_id($ids)
    {
        $query=Webbook_parameter::select("email");
        $query->whereIn("id",$ids);
        $data=$query->get();
        $data=$data->toArray();
        return $data;
    }

    public function delete_loansroom($applyno,$croomclsno,$startdate)
    {
        $del = Edu_loansroom::where('applyno','=',$applyno);
        $del->where('croomclsno','=',$croomclsno);
        $del->where('startdate','=',$startdate);

        if($del->delete()){
            return true;
        }
        
        return false;
    }

    public function delete_loanroom($applyno,$croomclsno,$startdate,$timestart,$timeend){
        $del = Edu_loanroom::where('applyno','=',$applyno);
        $del->where('croomclsno','=',$croomclsno);
        $del->where('applydate','=',$startdate);
        $del->where('starttime','=',$timestart);
        $del->where('endtime','=',$timeend);

        if($del->delete()){
            return true;
        }
        
        return false;
    }

    public function get_edu_loanplacelst($applyno)
    {
        $query=Edu_loanplacelst::select("*");
        $query->where("applyno",$applyno);
        $result=$query->get();
        $result=$result->toArray();
        for($i=0;$i<count($result);$i++){
            $result[$i]['loansroom_count']=$this->getCountLoansRoom($result[$i]["applyno"],$result[$i]["croomclsno"]);
            $result[$i]['loanroom_count']=$this->getCountLoanRoom($result[$i]["applyno"],$result[$i]["croomclsno"]);
            $croomclsfullname = $this->getClassroom($result[$i]["croomclsno"]);
            $result[$i]['croomclsfullname'] = $croomclsfullname[0]['croomclsfullname'];
        }
        return $result;
    }

    public function get_Tpa_loanplacelst($condition =[] ){
        $query = T21tb::select('*');
        $query->join('m14tb','t21tb.site','m14tb.site');
        // 申請編號
        if ( isset($condition['meet']) && $condition['meet'] ) {
            $query->where('meet', $condition['meet']);
        }else{
            $query->where('meet', 'like', 'I%');
        }
        // 申請編號
        if ( isset($condition['serno']) && $condition['serno'] ) {
            
            $query->where('serno', $condition['serno']);
        }
        $query->orderby('date');
        return $query->get()->toArray();
    }

    public function getCountLoansRoom($applyno,$croomclsno,$startdate='')
    {
        $query=Edu_loansroom::select(DB::raw("count(1) as loansroom_count"));
        $query->where("applyno",$applyno);
        $query->where("croomclsno",$croomclsno);

        if(!empty($startdate)){
            $query->where("startdate",$startdate);
        }

        $data=$query->get();
        $data=$data->toArray();
        return $data[0]["loansroom_count"];
    }

    public function getCountLoanRoom($applyno,$croomclsno,$applydate='')
    {
        $query=Edu_loanroom::select(DB::raw("count(1) as loanroom_count"));
        $query->where("applyno",$applyno);
        $query->where("croomclsno",$croomclsno);

        if(!empty($applydate)){
            $query->where("applydate",$applydate);
        }

        $data=$query->get();
        $data=$data->toArray();
        return $data[0]["loanroom_count"];
    }

    public function get_edu_loanplace_by_applyno($applyno)
    {
        $query=Edu_loanplace::select("*");
        $query->where("applyno",$applyno);
        $result=$query->get();
        $result=$result->toArray();
        return $result;
    }

    public function get_edu_loanplacelst_by_id($id)
    {
        $query=Edu_loanplacelst::select("*");
        $query->where("id",$id);
        $result=$query->get();
        $result=$result->toArray();
        return $result;
    }
    
    public function get_room_bed($applyno,$croomclsno,$sdate,$edate)
    {
        if (empty($edate)){
            $edate=$sdate;
        }
		//--取出這個分類的寢室
        $query=Edu_classroomcls::select("edu_classroomcls.croomclsno","edu_classroomcls.croomclsname","edu_classroomcls.croomclsfullname"
                                        ,"edu_floor.floorno","edu_floor.floorname","edu_floor.stayflag","edu_bed.bedroom","edu_bed.roomname"
                                        ,DB::raw("COUNT(edu_bed.id) AS bedamount"));
        $query->join("edu_floor",'edu_classroomcls.croomclsno','=','edu_floor.croomclsno');
        $query->join("edu_bed",function($join){
            $join->on('edu_floor.floorno','edu_bed.floorno');
            $join->where('edu_bed.isuse','1');
        });
        $query->where("edu_classroomcls.croomclsno",$croomclsno);
        $query->groupBy("edu_floor.floorno","edu_bed.bedroom");
        $query->orderBy("floorname","asc");
        $query->orderBy("bedroom","asc");
        $data_query=$query->get();
        
		//--取出這張申請單,這個分類的寢室				
        $roomquery=Edu_loansroom::select("edu_loansroom.startdate","edu_loansroom.enddate","edu_loansroom.sex"
                                ,"edu_loansroom.bedroom","edu_loansroom.floorno","edu_loansroom.bedno");
        $roomquery->where("edu_loansroom.applyno",$applyno)->where("edu_loansroom.croomclsno",$croomclsno)->where("edu_loansroom.startdate",$sdate);
        $roomquery->orderBy("bedroom");
        $data_roomquery=$roomquery->get();

        $roomquery2=Edu_loansroom::select("edu_loansroom.bedroom","edu_loansroom.floorno","edu_loansroom.bedno");
        $roomquery2->where("edu_loansroom.croomclsno",$croomclsno);
        $roomquery2->whereRaw(" (edu_loansroom.startdate BETWEEN {$sdate} AND {$edate} OR edu_loansroom.enddate BETWEEN {$sdate} AND {$edate})");
        $data_roomquery2=$roomquery2->get();                   

        $coursequery=Edu_classterm::select('edu_signup.bedno','edu_bed.bedroom','edu_bed.floorno','edu_classterm.staystartdate','edu_classterm.stayenddate');
        $coursequery->leftJoin("edu_signup",function($join){
            $join->on("edu_classterm.id","=","edu_signup.classterm_id");
            $join->where(DB::raw("edu_signup.bedno is not null"));
        });
        $coursequery->leftJoin("edu_bed","edu_signup.bedno","edu_bed.bedno");
        $coursequery->whereRaw(" (edu_classterm.staystartdate BETWEEN {$sdate} AND {$edate} OR edu_classterm.stayenddate BETWEEN {$sdate} AND {$edate})");
        $data_coursequery=$coursequery->get();
        		
		foreach($data_query as $q){
			$q->usestatus=0;
			$q->usestatusname='可以外借';
			foreach($data_roomquery as $room){
				if (($room->floorno==$q->floorno) && ($room->bedroom==$q->bedroom)){//選取
					$q->sel=$q->bedroom;
					$q->sex=$room->sex;
					break;
				}
			}			
			if (empty($q->sel)){
				foreach($data_roomquery2 as $room2){
					if (($room2->floorno==$q->floorno) && ($room2->bedroom==$q->bedroom)){
						$q->usestatus=1;
						$q->usestatusname='已外借';
						break;	
					}
				}
			}
			if (empty($q->sel)){
				foreach($data_coursequery as $course){
					if (($course->floorno==$q->floorno) && ($course->bedroom==$q->bedroom)){
						$enddate2=substr($course->stayenddate,0,7);//住宿結束日期
						$endtime2=substr($course->stayenddate,7,1);//住宿結束時間起						
						if ((substr($sdate,0,7)==$enddate2) && ($endtime2=='1')){//102.9.16 結束日期，下午可借
							
						}else{
							$q->usestatus=1;
							$q->usestatusname='已外借';
							break;
						}
					}
				}
			}		 
		}
		return $data_query;
    }

    public function get_room_bed2($applyno,$croomclsno,$applydate,$stime,$etime)
    {
        if (empty($etime)){
            $etime=$stime;
        }

        $query=Edu_classroomcls::select("edu_classroomcls.croomclsno","edu_classroomcls.croomclsname","edu_classroomcls.croomclsfullname"
                                ,"edu_classroom.roomno","edu_classroom.roomname","edu_classroom.fullname","edu_classroom.num");
        $query->leftJoin("edu_classroom","edu_classroomcls.croomclsno","edu_classroom.roomcla");
        $query->where("edu_classroomcls.croomclsno",$croomclsno);
        $query->orderBy("edu_classroom.roomname");
        $data_query=$query->get();

        $roomquery=Edu_loanroom::select("edu_loanroom.classroomno","edu_loanroom.applydate","edu_loanroom.starttime","edu_loanroom.endtime");
        $roomquery->where("edu_loanroom.applyno",$applyno);
        $roomquery->where("edu_loanroom.croomclsno",$croomclsno);
        $roomquery->where("edu_loanroom.applydate",$applydate);
        $roomquery->orderBy("classroomno");
        $data_roomquery=$roomquery->get();

        $roomquery2=T97tb::select("site as classroomno");
        $roomquery2->where("croomclsno",$croomclsno);
        $roomquery2->where("date",$applydate);
        $roomquery2->whereRaw(" (stime BETWEEN {$stime} AND {$etime} OR etime BETWEEN {$stime} AND {$etime})");
        $roomquery2->orderBy("site");
        $data_roomquery2=$roomquery2->get();

        // $coursequery=Edu_coursedt::select("edu_coursedt.classroom");
        // $coursequery->where("edu_coursedt.classdate",$applydate);          
        // $coursequery->whereRaw(" (edu_coursedt.starttime BETWEEN {$stime} AND {$etime} OR edu_coursedt.endtime BETWEEN {$stime} AND {$etime})");
        // $coursequery->orderBy("classroom");
        // $data_coursequery=$coursequery->get();
		foreach($data_query as $q){
			$q->usestatus=0;
			$q->usestatusname='可以外借';
			foreach($data_roomquery as $room){
				if ($room->classroomno==$q->roomno){//選取
					$q->sel=$q->roomno;
					break;
				}
			}
			if (empty($q->sel)){
				foreach($data_roomquery2 as $room2){
					if ($room2->classroomno==$q->roomno){
						$q->usestatus=1;
						$q->usestatusname='已外借';//'有課程';
						break;
					}
				}
			}
        }
       
		return $data_query;
    }



    public function getArg($id = null)
    {
        $query=Edu_classcode::select("*");
        $query->where("class","64");
        //dd($id);
        // var_dump($id);
        if($id!=null){
            $query->where('id',$id);
        }
        $data=$query->get();
        $data=$data->toArray();
        return $data;
    }

    public function getHoliday($params)
    {
        $query=Edu_holiday::select("*");
        $query->whereBetween('holiday',array($params['sdate'],$params['edate']));
        $data=$query->get();
        $data=$data->toArray();
        if($params["mode"]=='workdate'){
            return count($data);
        }else{
            return $data;
        }
    }


}
