<?php
namespace App\Repositories;

use App\Models\T08tb;
use App\Models\T09tb;
use App\Models\T06tb;
use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\M09tb;
use App\Models\M01tb;
use App\Models\S02tb;
use App\Models\T92tb;
use App\Models\T11tb;
use DB ;

class Transfer_processingRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTransfer_processingList($queryData = [])
    {
        $class_query = T01tb::select('t01tb.class');

        $class_query->join('t04tb', function($join)
        {
            $join->on('t04tb.class', '=', 't01tb.class');
        });

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [])) {

                $class_query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            // $class_query->orderBy('paidday', 'asc');
            // $class_query->orderBy('date', 'asc');
            // $class_query->orderBy('t04tb.sdate', 'asc');
        }
        //year
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {
            $queryData['yerly'] = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT);
            $class_query->where('t01tb.yerly', $queryData['yerly']);
        }

        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            $class_query->where('t01tb.class', 'LIKE', '%'.$queryData['class'].'%');
        }

        // 班別
        if ( isset($queryData['name']) && $queryData['name'] ) {
            $class_query->where('t01tb.name', 'LIKE', '%'.$queryData['name'].'%');
        }

        if ( isset($queryData['class_branch_name']) && $queryData['class_branch_name'] ) {
            $class_query->where('t01tb.branchname', 'LIKE', '%'.$queryData['class_branch_name'].'%');
        }

        if ( isset($queryData['branch']) && $queryData['branch'] ) {
            $class_query->where('t01tb.branch', 'LIKE', '%'.$queryData['branch'].'%');
        }

        if ( isset($queryData['process']) && $queryData['process'] ) {
            $class_query->where('t01tb.process', 'LIKE', '%'.$queryData['process'].'%');
        }

        if ( isset($queryData['traintype']) && $queryData['traintype'] ) {
            $class_query->where('t01tb.traintype', 'LIKE', '%'.$queryData['traintype'].'%');
        }

        if ( isset($queryData['categoryone']) && $queryData['categoryone'] ) {
            $class_query->where('t01tb.categoryone', $queryData['categoryone']);
        }

        if ( isset($queryData['sponsor']) && $queryData['sponsor'] ) {
            $class_query->where('t04tb.sponsor', '=', $queryData['sponsor']);
        }

        if ( isset($queryData['type']) && $queryData['type'] ) {
            $class_query->where('t01tb.type', 'LIKE', '%'.$queryData['type'].'%');
        }

        if ( isset($queryData['sitebranch']) && $queryData['sitebranch'] ) {
            $class_query->where('t04tb.site_branch', $queryData['sitebranch']);
        }

        if ( isset($queryData['sdate']) && $queryData['sdate'] ) {
            $queryData['sdate'] = str_pad($queryData['sdate'],7,'0',STR_PAD_LEFT);
            $class_query->where('t04tb.sdate', '>=', $queryData['sdate']);
        }
        if ( isset($queryData['edate']) && $queryData['edate'] ) {
            $queryData['edate'] = str_pad($queryData['edate'],7,'0',STR_PAD_LEFT);
            $class_query->where('t04tb.sdate', '<=', $queryData['edate']);
        }

        if ( isset($queryData['sdate2']) && $queryData['sdate2'] ) {
            $queryData['sdate2'] = str_pad($queryData['sdate2'],7,'0',STR_PAD_LEFT);
            $class_query->where('t04tb.edate', '>=', $queryData['sdate2']);
        }
        if ( isset($queryData['edate2']) && $queryData['edate2'] ) {
            $queryData['edate2'] = str_pad($queryData['edate2'],7,'0',STR_PAD_LEFT);
            $class_query->where('t04tb.edate', '<=', $queryData['edate2']);
        }

        if(isset($queryData['sdate3']) && $queryData['sdate3'] && isset($queryData['edate3']) && $queryData['edate3'] ){
            $queryData['sdate3'] = str_pad($queryData['sdate3'],7,'0',STR_PAD_LEFT);
            $queryData['edate3'] = str_pad($queryData['edate3'],7,'0',STR_PAD_LEFT);

            $class_query->leftJoin('t06tb', function($join)
            {
                $join->on('t04tb.class', '=', 't06tb.class')
                ->on('t04tb.term', '=', 't06tb.term');
            });

            // //
            // $class_no = T06tb::select('class')
            // ->where('date', '>=', $queryData['sdate3'])
            // ->where('date', '<=', $queryData['edate3'])
            // ->get();
            // $class_no_in = array();
            // foreach ($class_no as $row) {
            //     $class_no_in[] = $row->class;
            // }
            // $class_query->whereIn('t01tb.class', $class_no_in);
            // //
            $class_query->where('t06tb.date', '>=', $queryData['sdate3']);
            $class_query->where('t06tb.date', '<=', $queryData['edate3']);
            $class_query->distinct();
        }else{
            if ( isset($queryData['sdate3']) && $queryData['sdate3'] ) {
                $class_query->leftJoin('t06tb', function($join)
                {
                    $join->on('t04tb.class', '=', 't06tb.class')
                    ->on('t04tb.term', '=', 't06tb.term');
                });
                $queryData['sdate3'] = str_pad($queryData['sdate3'],7,'0',STR_PAD_LEFT);
                // //
                // $class_no = T06tb::select('class')
                // ->where('date', '>=', $queryData['sdate3'])
                // ->get();
                // $class_no_in = array();
                // foreach ($class_no as $row) {
                //     $class_no_in[] = $row->class;
                // }
                // $class_query->whereIn('t01tb.class', $class_no_in);
                // //
                $class_query->where('t06tb.date', '>=', $queryData['sdate3']);
                $class_query->distinct();
            }
            if ( isset($queryData['edate3']) && $queryData['edate3'] ) {
                $class_query->leftJoin('t06tb', function($join)
                {
                    $join->on('t04tb.class', '=', 't06tb.class')
                    ->on('t04tb.term', '=', 't06tb.term');
                });
                $queryData['edate3'] = str_pad($queryData['edate3'],7,'0',STR_PAD_LEFT);
                // //
                // $class_no = T06tb::select('class')
                // ->where('date', '<=', $queryData['edate3'])
                // ->get();
                // $class_no_in = array();
                // foreach ($class_no as $row) {
                //     $class_no_in[] = $row->class;
                // }
                // $class_query->whereIn('t01tb.class', $class_no_in);
                // //
                $class_query->where('t06tb.date', '<=', $queryData['edate3']);
                $class_query->distinct();
            }
        }
        $class_no = $class_query->get();

        $class_no_in = array();
        foreach ($class_no as $row) {
            $class_no_in[] = $row->class;
        }

  //       echo '<pre style="text-align:left;">' . "\n";
		// print_r($class_no_in);
		// echo "\n</pre>\n";

        $query = T09tb::select('*');

        $query->Join('t06tb', function($join)
            {
                $join->on('t09tb.class', '=', 't06tb.class')
                ->on('t09tb.term', '=', 't06tb.term')
                ->on('t09tb.course', '=', 't06tb.course');
            });

        $query->Join('t04tb', function($join)
            {
                $join->on('t09tb.class', '=', 't04tb.class')
                ->on('t09tb.term', '=', 't04tb.term');
            });
        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {

            $query->where('t09tb.term', 'LIKE', '%'.$queryData['term'].'%');
        }

        if ( isset($queryData['sdate']) && $queryData['sdate'] ) {
            $queryData['sdate'] = str_pad($queryData['sdate'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.sdate', '>=', $queryData['sdate']);
        }
        if ( isset($queryData['edate']) && $queryData['edate'] ) {
            $queryData['edate'] = str_pad($queryData['edate'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.sdate', '<=', $queryData['edate']);
        }

        if ( isset($queryData['sdate2']) && $queryData['sdate2'] ) {
            $queryData['sdate2'] = str_pad($queryData['sdate2'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.edate', '>=', $queryData['sdate2']);
        }
        if ( isset($queryData['edate2']) && $queryData['edate2'] ) {
            $queryData['edate2'] = str_pad($queryData['edate2'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.edate', '<=', $queryData['edate2']);
        }

        if ( isset($queryData['sdate3']) && $queryData['sdate3'] ) {

                $queryData['sdate3'] = str_pad($queryData['sdate3'],7,'0',STR_PAD_LEFT);

                $query->where('t06tb.date', '>=', $queryData['sdate3']);
            }
            if ( isset($queryData['edate3']) && $queryData['edate3'] ) {

                $queryData['edate3'] = str_pad($queryData['edate3'],7,'0',STR_PAD_LEFT);

                $query->where('t06tb.date', '<=', $queryData['edate3']);
            }

        $query->whereIn('t09tb.class', $class_no_in);
        $query->where('t09tb.totalpay', '<>', '0');

        if(isset($queryData['paid']) && $queryData['paid']){
            if($queryData['paid'] == '1'){
                $query->where('t09tb.paidday', '<>', '');
            }
            if($queryData['paid'] == '2'){
                $query->where('t09tb.paidday', '=', '');
            }
        }

        $query->orderBy('paidday', 'asc');
        $query->orderBy('date', 'asc');

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        // dd($data);

        foreach($data as & $va){

            // $query = T06tb::select('t06tb.date');
            // $T06tb_data = $query->where('class', $va->class)->where('term', $va->term)->where('course', $va->course)->get()->toArray();

            // if(isset($T06tb_data[0]['date'])){
            //     $va->date = $T06tb_data[0]['date'];
            // }else{
            //     $va->date = '';
            // }

            $query = T01tb::select('t01tb.name');
            $T01tb_data = $query->where('class', $va->class)->get()->toArray();

            if(isset($T01tb_data[0]['name'])){
                $va->name = $T01tb_data[0]['name'];
            }else{
                $va->name = '';
            }

            $query = M01tb::select('m01tb.cname', 'm01tb.transfor', 'm01tb.postno', 'm01tb.bankno');
            $M01tb_data = $query->where('idno', $va->idno)->get()->toArray();

            if(isset($M01tb_data[0]['cname'])){
                $va->cname = $M01tb_data[0]['cname'];
            }else{
                $va->cname = '';
            }

            if(isset($M01tb_data[0]['transfor'])){
                if($M01tb_data[0]['transfor'] == '1'){
                    $va->postno_bankno = $M01tb_data[0]['postno'];
                    $va->transfor_name = '郵局';
                }
                if($M01tb_data[0]['transfor'] == '2'){
                    $va->postno_bankno = $M01tb_data[0]['bankno'];
                    $va->transfor_name = '金融機構';
                }
            }

        }

        return $data;
    }

    public function getClass($queryData = [])
    {
        $query = T01tb::select('t01tb.class', 't01tb.name', 't01tb.branch', 't01tb.branchname', 't01tb.process');
        $results = $query->where('class', $queryData['class'])->get()->toArray();
        $class_data = $results[0];
        $query = T04tb::select('t04tb.term', 't04tb.sdate', 't04tb.edate', 't04tb.sponsor');
        $results = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();
        $class_data['term'] = $results[0]['term'];
        $class_data['sdate'] = $results[0]['sdate'];
        $class_data['edate'] = $results[0]['edate'];
        $class_data['sponsor'] = $results[0]['sponsor'];
        if(!empty($class_data['sponsor'])){
            $query = M09tb::select('m09tb.username');
            $results = $query->where('userid', $class_data['sponsor'])->get()->toArray();
            $class_data['sponsor'] = $results[0]['username'];
            //123
        }
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($class_data);
        // echo "\n</pre>\n";
        // die();

        return $class_data;
    }

    public function getSponsor(){
        $query = T04tb::select('t04tb.sponsor', 'm09tb.username');
        $query->join('m09tb', function($join)
        {
            $join->on('m09tb.userid', '=', 't04tb.sponsor');
        });
        $results = $query->where('sponsor', '<>', '')->distinct()->get()->toArray();
        $sponsor = array();
        foreach($results as $row){
            $sponsor[$row['sponsor']] = $row['username'];
        }

        return $sponsor;
    }

    public function getT04tbKind($queryData = []){

        $query = T06tb::select('t09tb.class', 't09tb.term');
        $query->join('t09tb', function($join)
        {
            $join->on('t06tb.class', '=', 't09tb.class')
            ->on('t06tb.term', '=', 't09tb.term')
            ->on('t06tb.course', '=', 't09tb.course');
        });

        $query->where('t06tb.date', '>=', $queryData['date1']);
        $query->where('t06tb.date', '<=', $queryData['date2']);
        $query->where('t09tb.totalpay', '>', '0');
        $query->groupBy('t09tb.class');
        $query->groupBy('t09tb.term');
        $class_data = $query->get()->toArray();
        $class_name_data = array();
        foreach($class_data as $row){
            $query = T04tb::select('t04tb.class', 't01tb.name');
            $query->join('t01tb', function($join)
            {
                $join->on('t04tb.class', '=', 't01tb.class');
            });
            $query->where('t04tb.class', $row['class']);
            $query->where('t04tb.term', $row['term']);
            $query->where('t04tb.kind', '');
            $query->where('t01tb.branch', $queryData['branch']);
            $row_data = $query->get()->toArray();
            if(!empty($row_data)){
                $row['name'] = $row_data[0]['name'];
                $class_name_data[] = $row;
            }
        }

        // dd($class_name_data);
        return $class_name_data;

    }

    public function checkIdno($queryData = []){
        // idno 身分證字號錯誤 若講師為外國人時，就不檢查身分證字號(idkind  = 3, 4, 7)
        // 帳號空白 transfor = '1' AND postno = '' THEN 1 郵局
        // 帳號空白 transfor = '2' AND bankno = '' THEN 1 金融機構
        // 戶籍地址空白 regaddress = ''
        $query = T06tb::select('t09tb.idno');
        $query->join('t09tb', function($join)
        {
            $join->on('t06tb.class', '=', 't09tb.class')
            ->on('t06tb.term', '=', 't09tb.term')
            ->on('t06tb.course', '=', 't09tb.course');
        });

        $query->join('t01tb', function($join)
        {
            $join->on('t06tb.class', '=', 't01tb.class');
        });

        $query->where('t06tb.date', '>=', $queryData['date1']);
        $query->where('t06tb.date', '<=', $queryData['date2']);
        $query->where('t01tb.branch', $queryData['branch']);
        $query->where('t09tb.totalpay', '<>', '0');
        $query->groupBy('t09tb.idno');
        $check_array = $query->get()->toArray();
        $check_data = array();
        foreach($check_array as $row){
            $query = M01tb::select('m01tb.idno', 'm01tb.cname', 'm01tb.transfor', 'm01tb.postno', 'm01tb.bankno', 'm01tb.regaddress', 'm01tb.idkind');
            $query->where('idno', $row['idno']);
            $row_data = $query->get()->toArray();
            if(!empty($row_data)){
                $error_msg = '';
                if(!in_array($row_data[0]['idkind'], array('3', '4', '7'))){
                    $chk = $this->chk_pid($row_data[0]['idno']);
                    if($chk === false){
                        $error_msg .= '身分證字號錯誤..';
                    }
                }
                if($row_data[0]['transfor'] == '1'){
                    if(empty($row_data[0]['postno'])){
                        $error_msg .= '帳號空白..';
                    }
                }else{
                    if(empty($row_data[0]['bankno'])){
                        $error_msg .= '帳號空白..';
                    }
                }
                if(empty($row_data[0]['regaddress'])){
                    $error_msg .= '戶籍地址空白..';
                }
                if($error_msg != ''){
                    $row_data[0]['error_msg'] = $error_msg;
                    $check_data[] = $row_data[0];
                }
            }
        }

        // dd($class_name_data);
        return $check_data;

    }

    public function TransferExists($queryData = []){
    	$T11tb_data = T11tb::select('t11tb.date')->where('t11tb.date', $queryData['date3'])->get()->toArray();
    	if(!empty($T11tb_data)){
    		return true;
    	}
    	$T09tb_data = T09tb::select('t09tb.paidday')->where('t09tb.paidday', $queryData['date3'])->get()->toArray();
    	if(!empty($T09tb_data)){
    		return true;
    	}

    	$query = T06tb::select('t09tb.idno');
        $query->join('t09tb', function($join)
        {
            $join->on('t06tb.class', '=', 't09tb.class')
            ->on('t06tb.term', '=', 't09tb.term')
            ->on('t06tb.course', '=', 't09tb.course');
        });

        $query->where('t06tb.date', '>=', $queryData['date1']);
        $query->where('t06tb.date', '<=', $queryData['date2']);
        $query->where('t09tb.paidday', '=', $queryData['date3']);
        $T06tb_data = $query->get()->toArray();
        if(!empty($T06tb_data)){
    		return true;
    	}

    	$query = T06tb::select('t09tb.idno', 't09tb.class', 't09tb.term');
        $query->join('t09tb', function($join)
        {
            $join->on('t06tb.class', '=', 't09tb.class')
            ->on('t06tb.term', '=', 't09tb.term')
            ->on('t06tb.course', '=', 't09tb.course');
        });
        $query->join('t01tb', function($join)
        {
            $join->on('t06tb.class', '=', 't01tb.class');
        });

        $query->where('t06tb.date', '>=', $queryData['date1']);
        $query->where('t06tb.date', '<=', $queryData['date2']);
        $query->where('t09tb.paidday', '<>', '');
        $query->where('t01tb.branch', $queryData['branch']);
        $T06tb_data = $query->get()->toArray();
        if($queryData['clss_kind'] == '1' && !empty($T06tb_data)){
        	return true;
        }
        foreach($T06tb_data as & $row){
        	$class_year = substr($row['class'], 0,3);
        	$query = T04tb::select('s06tb.yerly', 's06tb.acccode', 's06tb.accname');
            $query->join('s06tb', function($join)
            {
                $join->on('s06tb.acccode', '=', 't04tb.kind');
            });
            $query->where('t04tb.class', $row['class']);
            $query->where('t04tb.term', $row['term']);
            $query->where('s06tb.yerly', $class_year);
            $row_data1 = $query->get()->toArray();
            $row['yerly'] = $row_data1[0]['yerly'];
            $row['acccode'] = $row_data1[0]['acccode'];
            $row['accname'] = $row_data1[0]['accname'];

            if($queryData['clss_kind'] == '3'){
            	if($row['accname'] == '代收款'){
            		return true;
            	}
            }
            if($queryData['clss_kind'] == '2'){
            	if($row['accname'] != '代收款'){
            		return true;
            	}
            }
        }

    	return false;
    }

    public function doTransfer($queryData = []){

        setProgid('transfer_processing');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG

        $query = T06tb::select('t06tb.class', 't06tb.term', 't06tb.course', 't06tb.date', 't09tb.idno', 't09tb.kind', 't09tb.lectamt', 't09tb.noteamt', 't09tb.speakamt', 't09tb.insureamt1', 't09tb.insureamt2', 't09tb.totalpay', 't09tb.paidday', 't09tb.id');

        $query->join('t09tb', function($join)
        {
            $join->on('t06tb.class', '=', 't09tb.class')
            ->on('t06tb.term', '=', 't09tb.term')
            ->on('t06tb.course', '=', 't09tb.course');
        });

        $query->join('t01tb', function($join)
        {
            $join->on('t06tb.class', '=', 't01tb.class');
        });

        $query->where('t06tb.date', '>=', $queryData['date1']);
        $query->where('t06tb.date', '<=', $queryData['date2']);
        $query->where('t01tb.branch', $queryData['branch']);
        $query->where('t09tb.totalpay', '>', '0');
        $query->orderBy('t09tb.idno', 'asc');
        $query->orderBy('t06tb.class', 'asc');
        $query->orderBy('t06tb.term', 'asc');
        $query->orderBy('t06tb.date', 'asc');

        $doTransfer_array = $query->get()->toArray();

        if(empty($doTransfer_array)){
        	return false;
        }

        $Transfer_data = array();

        foreach($doTransfer_array as $key => $row){
        	$class_year = substr($row['class'], 0,3);
        	// dd($class_year);
        	$Transfer_data[$key] = $row;
        	$Transfer_data[$key]['paymk'] = '1';
        	$query = T04tb::select('s06tb.yerly', 's06tb.acccode', 's06tb.accname');
            $query->join('s06tb', function($join)
            {
                $join->on('s06tb.acccode', '=', 't04tb.kind');
            });
            $query->where('t04tb.class', $row['class']);
            $query->where('t04tb.term', $row['term']);
            $query->where('s06tb.yerly', $class_year);
            $row_data1 = $query->get()->toArray();
            $Transfer_data[$key]['yerly'] = $row_data1[0]['yerly'];
            $Transfer_data[$key]['acccode'] = $row_data1[0]['acccode'];
            $Transfer_data[$key]['accname'] = $row_data1[0]['accname'];

            if($queryData['clss_kind'] == '2'){
            	if($Transfer_data[$key]['accname'] == '代收款'){
            		unset($Transfer_data[$key]);
            	}
            }
            if($queryData['clss_kind'] == '3'){
            	if($Transfer_data[$key]['accname'] != '代收款'){
            		unset($Transfer_data[$key]);
            	}
            }
        }

        $S02tb_data = S02tb::select('s02tb.offno', 's02tb.girono')->get()->toArray();

        $T11tb_data = array();
        $Transfer_idno_data = array();
        $Transfer_no = '1';

        $t92tb_no = '1';

        foreach($Transfer_data as $row_data){

        	$fields = array(
        		'paidday' => $queryData['date3'],
        		'paymk' => '1',
        	);
        	T09tb::where('id', $row_data['id'])->update($fields);

        	$M01_data = M01tb::select('m01tb.cname')->where('m01tb.idno', $row_data['idno'])->get()->toArray();
        	$t92tb_50 = array();
        	$t92tb_9B = array();

        	if(in_array($row_data['kind'], array('1', '2', '4'))){
        		$t92tb_50['extamt'] = $row_data['lectamt']+$row_data['speakamt'];
        		$t92tb_50['intamt'] = '0';
        	}else{
        		$t92tb_50['intamt'] = $row_data['lectamt']+$row_data['speakamt'];
        		$t92tb_50['extamt'] = '0';
        	}

        	if($t92tb_50['intamt'] > '0' || $t92tb_50['extamt'] > '0'){

        		$t92tb_50['paidday'] = $queryData['date3'];
	        	$t92tb_50['name'] = $M01_data[0]['cname'];
	        	$t92tb_50['idno'] = $row_data['idno'];
	        	$t92tb_50['type'] = '50';
	        	$t92tb_50['deduct'] = $row_data['insureamt1'];
	        	$t92tb_50['class'] = $row_data['class'];
	        	$t92tb_50['term'] = $row_data['term'];
	        	$t92tb_50['course'] = $row_data['course'];
	        	$t92tb_50['accname'] = $row_data['accname'];
	        	$t92tb_50['serno'] = str_pad($t92tb_no,7,'0',STR_PAD_LEFT);
	        	T92tb::create($t92tb_50);

                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('transfer_processing')){
                    $nowdata = T92tb::where('idno', $t92tb_50['idno'])->where('class', $t92tb_50['class'])->where('term', $t92tb_50['term'])->where('course', $t92tb_50['course'])->where('serno', $t92tb_50['serno'])->get()->toarray();
                    createModifyLog('I','T92tb','',$nowdata,end($sql));
                }

	        	$t92tb_no++;
        	}

        	if(in_array($row_data['kind'], array('1', '2', '4'))){
        		$t92tb_9B['extamt'] = $row_data['noteamt'];
        		$t92tb_9B['intamt'] = '0';
        	}else{
        		$t92tb_9B['intamt'] = $row_data['noteamt'];
        		$t92tb_9B['extamt'] = '0';
        	}

        	if($t92tb_9B['intamt'] > '0' || $t92tb_9B['extamt'] > '0'){

        		$t92tb_9B['paidday'] = $queryData['date3'];
	        	$t92tb_9B['name'] = $M01_data[0]['cname'];
	        	$t92tb_9B['idno'] = $row_data['idno'];
	        	$t92tb_9B['type'] = '9B';
	        	$t92tb_9B['deduct'] = $row_data['insureamt1'];
	        	$t92tb_9B['class'] = $row_data['class'];
	        	$t92tb_9B['term'] = $row_data['term'];
	        	$t92tb_9B['course'] = $row_data['course'];
	        	$t92tb_9B['accname'] = $row_data['accname'];
	        	$t92tb_9B['serno'] = str_pad($t92tb_no,7,'0',STR_PAD_LEFT);
	        	T92tb::create($t92tb_9B);

                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('transfer_processing')){
                    $nowdata = T92tb::where('idno', $t92tb_9B['idno'])->where('class', $t92tb_9B['class'])->where('term', $t92tb_9B['term'])->where('course', $t92tb_9B['course'])->where('serno', $t92tb_9B['serno'])->get()->toarray();
                    createModifyLog('I','T92tb','',$nowdata,end($sql));
                }

	        	$t92tb_no++;
        	}

        	if(in_array($row_data['idno'], $Transfer_idno_data)){
        		$T11tb_data[$row_data['idno']]['amt'] = intval($T11tb_data[$row_data['idno']]['amt']) + intval($row_data['totalpay'].'00');
        		$T11tb_data[$row_data['idno']]['amt'] = str_pad($T11tb_data[$row_data['idno']]['amt'] ,10,'0',STR_PAD_LEFT);
        	}else{
        		$Transfer_idno_data[] = $row_data['idno'];
        		$M01tb_data = M01tb::select('m01tb.postcode', 'm01tb.postno', 'm01tb.idkind', 'm01tb.transfor', 'm01tb.bank', 'm01tb.bankcode', 'm01tb.bankno', 'm01tb.post', 'm01tb.cname', 'm01tb.bankaccname')->where('m01tb.idno', $row_data['idno'])->get()->toArray();
        		$T11tb_data[$row_data['idno']]['date'] = $queryData['date3'];
        		$T11tb_data[$row_data['idno']]['serno'] = str_pad($Transfer_no,6,'0',STR_PAD_LEFT);
        		$T11tb_data[$row_data['idno']]['postcode'] = $M01tb_data[0]['postcode'];
        		$T11tb_data[$row_data['idno']]['postno'] = $M01tb_data[0]['postno'];
        		if(in_array($M01tb_data[0]['idkind'], array('3', '4', '7')) && $M01tb_data[0]['transfor'] == '2' ){
        			$T11tb_data[$row_data['idno']]['accname'] = $M01tb_data[0]['bankaccname'];
        		}else{
        			$T11tb_data[$row_data['idno']]['accname'] = $M01tb_data[0]['cname'];
        		}
        		$T11tb_data[$row_data['idno']]['amt'] = str_pad($row_data['totalpay'].'00',10,'0',STR_PAD_LEFT);
        		$T11tb_data[$row_data['idno']]['idno'] = $row_data['idno'];
        		$T11tb_data[$row_data['idno']]['cardno'] = '033';
        		$T11tb_data[$row_data['idno']]['offno'] = $S02tb_data[0]['offno'];
        		$T11tb_data[$row_data['idno']]['girono'] = $S02tb_data[0]['girono'];
        		$T11tb_data[$row_data['idno']]['transfor'] = $M01tb_data[0]['transfor'];
        		$T11tb_data[$row_data['idno']]['bank'] = $M01tb_data[0]['bank'];
        		$T11tb_data[$row_data['idno']]['bankcode'] = $M01tb_data[0]['bankcode'];
        		$T11tb_data[$row_data['idno']]['bankno'] = $M01tb_data[0]['bankno'];
        		$T11tb_data[$row_data['idno']]['post'] = $M01tb_data[0]['post'];
        		$T11tb_data[$row_data['idno']]['branch'] = $queryData['branch'];
        		$Transfer_no ++;
        	}


        }

        foreach($T11tb_data as $T11tb_row){
        	T11tb::create($T11tb_row);

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('transfer_processing')){
                $nowdata = T11tb::where('idno', $T11tb_row['idno'])->where('date', $T11tb_row['date'])->where('serno', $T11tb_row['serno'])->get()->toarray();
                createModifyLog('I','T11tb','',$nowdata,end($sql));
            }

        }

        return true;
    }

    public function doCancel($queryData = []){

        setProgid('transfer_processing');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG

        $query = T09tb::select('t09tb.class', 't09tb.term', 't09tb.course', 't09tb.idno', 't09tb.paidday', 't09tb.id');

        $query->where('t09tb.paidday', '=', $queryData['date6']);

        $doCancel_array = $query->get()->toArray();

        if(empty($doCancel_array)){
            return false;
        }else{

            if(checkNeedModifyLog('transfer_processing')){
                $olddata = T11tb::where('date', $queryData['date6'])->get()->toarray();
            }

            T11tb::where('date', $queryData['date6'])->delete();

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('transfer_processing')){
                createModifyLog('D','T11tb',$olddata,'',end($sql));
            }

            if(checkNeedModifyLog('transfer_processing')){
                $olddata = T92tb::where('paidday', $queryData['date6'])->get()->toarray();
            }

            T92tb::where('paidday', $queryData['date6'])->delete();

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('transfer_processing')){
                createModifyLog('D','T92tb',$olddata,'',end($sql));
            }

            $fields = array(
                'paidday' => '',
                'paymk' => '',
            );

            if(checkNeedModifyLog('transfer_processing')){
                $olddata = T09tb::where('paidday', $queryData['date6'])->get()->toarray();
            }

            T09tb::where('paidday', $queryData['date6'])->update($fields);

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('transfer_processing')){
                $nowdata = T09tb::where('paidday', $queryData['date6'])->get()->toarray();
                createModifyLog('U','T09tb',$olddata,$nowdata,end($sql));
            }

        }

        return true;
    }

    public function getFile($queryData = []){

        $query = T11tb::select('t11tb.cardno', 't11tb.offno', 't11tb.postno', 't11tb.girono', 't11tb.date', 't11tb.postcode', 't11tb.idno', 't11tb.amt');
        $query->where('t11tb.date', '=', $queryData['date5']);
        $query->where('t11tb.transfor', '=', '1');
        $file_data = $query->get()->toArray();

        $text = '';
        foreach($file_data as $row){
            $text .= $row['cardno'].$row['offno'].'      '.$row['girono'].$row['date'].$row['postcode'].$row['postno'].$row['idno'].$row['amt']."               \n";
        }
        // dd($text);
        return $text;

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

}
