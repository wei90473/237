<?php
namespace App\Repositories;

use App\Models\T12tb;
use App\Models\T09tb;
use App\Models\M01tb;
use App\Models\S02tb;
use DB ;


class Tax_processingRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTax_processingList($queryData = [])
    {
        $query = T12tb::select('t12tb.serno', 't12tb.idno', 't12tb.name', 't12tb.type', 't12tb.deduct', 't12tb.net', 't12tb.total');


        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('serno', 'asc');

        }
        //year
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {
            $queryData['yerly'] = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT);
            $query->where('t12tb.year', $queryData['yerly']);
        }

        // idno
        if ( isset($queryData['idno']) && $queryData['idno'] ) {
            $query->where('t12tb.idno', $queryData['idno']);
        }

        // name
        if ( isset($queryData['name']) && $queryData['name'] ) {
            $query->where('t12tb.name', 'LIKE', '%'.$queryData['name'].'%');
        }


        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);


        return $data;
    }

    public function TaxExists($queryData = [])
    {
        $query = T12tb::select('t12tb.serno', 't12tb.idno', 't12tb.name', 't12tb.type', 't12tb.deduct', 't12tb.net', 't12tb.total');
        $queryData['yerly'] = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT);
        $query->where('t12tb.year', $queryData['yerly']);
        $check_array = $query->get()->toArray();
        if(!empty($check_array)){
            return true;
        }else{
            return false;
        }

    }

    public function taxReturn($queryData = [])
    {
        setProgid('tax_processing');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG

        $query = T09tb::select('t09tb.idno');
        $query->where('t09tb.class', 'LIKE', $queryData['yerly'].'%');
        $query->groupBy('t09tb.idno');
        $idno_array = $query->get()->toArray();
        $tax_idno_array = array();
        foreach($idno_array as & $idno_row){
            $query = M01tb::select('m01tb.idno', 'm01tb.idkind', 'm01tb.cname', 'm01tb.regaddress');
            $query->where('m01tb.idno', $idno_row['idno']);
            $query->where('m01tb.idkind', '<>', '3');
            $query->where('m01tb.idkind', '<>', '4');
            $query->where('m01tb.idkind', '<>', '7');
            $idno_row_array = $query->get()->toArray();
            if(!empty($idno_row_array)){
                $chk = $this->chk_pid($idno_row['idno']);
                if($chk === true){
                    $tax_idno_array[] = $idno_row_array[0];
                }
            }
        }

        if(empty($tax_idno_array)){
            return false;
        }

        if(isset($queryData['repeat']) && $queryData['repeat'] == 'Y'){
        	T12tb::where('year', str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT))->delete();
        }

        $query = S02tb::select('s02tb.taxorgan', 's02tb.taxcode', 's02tb.companyno');
        $S02tb_data = $query->get()->toArray();
        $S02tb_data = $S02tb_data[0];
        $no = '1';
        $tax_day = (date('Y')-1911).date('md');
        foreach($tax_idno_array as $row){
            $query = T09tb::select('t09tb.idno', DB::raw('SUM(lectamt)+SUM(speakamt) as total'), DB::raw('SUM(deductamt1) as deduct'), DB::raw('SUM(lectamt+speakamt-deductamt1) as net'));
            $query->where('t09tb.idno', $row['idno']);
            $query->where('t09tb.class', 'LIKE', $queryData['yerly'].'%');
            $query->where('t09tb.paidday', '<>', '');
            $query->groupBy('t09tb.idno');
            $query->havingRaw('(SUM(lectamt)+SUM(speakamt))>0');
            $Tax50_array = $query->get()->toArray();
            if(!empty($Tax50_array)){
            	$fields_50 = array(
			        'year' => str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT),    /* 所得年度 */
			        'serno' => str_pad($no,7,'0',STR_PAD_LEFT),     /* 編號 */
			        'taxorgan' => $S02tb_data['taxorgan'],  /* 稽徵機關代號 */
			        'taxcode' => $S02tb_data['taxcode'],   /* 媒體代號 */
			        'incomemk' => '',  /* 註記 */
			        'type' => '50',    /* 格式 */
			        'idno' => $row['idno'],      /* 所得人統一編號 */
			        'idkind' => $row['idkind'],    /* 證號別 */
			        'companyno' => $S02tb_data['companyno'], /* 營利事業統一編號 */
			        'total' => str_pad($Tax50_array[0]['total'],10,'0',STR_PAD_LEFT),     /* 給付總額 */
			        'deduct' => str_pad($Tax50_array[0]['deduct'],10,'0',STR_PAD_LEFT),    /* 扣繳稅額 */
			        'net' => str_pad($Tax50_array[0]['net'],10,'0',STR_PAD_LEFT),       /* 給付淨額 */
			        'identcode' => '', /* 所得人代號/費用 */
			        'spmk' => '#',      /* 分隔註記 */
			        'errormk' => '',   /* 錯誤註記 */
			        'name' => $row['cname'],      /* 所得人中文姓名 */
			        'address' => str_replace(array("\r", "\n", "\r\n", "\n\r"), '', $row['regaddress']), /* 所得人中文地址 */
			        'filedate' => $tax_day,   /* 檔案製作日期 */
            	);
            	T12tb::create($fields_50);

                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('tax_processing')){
                    $nowdata = T12tb::where('idno', $row['idno'])->where('serno', $fields_50['serno'])->where('type', $fields_50['type'])->where('year', $fields_50['year'])->where('filedate', $tax_day)->get()->toarray();
                    createModifyLog('I','T12tb','',$nowdata,end($sql));
                }

                if(checkNeedModifyLog('tax_processing')){
                    $olddata = T09tb::where('t09tb.class', 'LIKE', $queryData['yerly'].'%')->where('t09tb.idno',  $row['idno'])->get()->toarray();
                }

            	$fields = array(
            		'taxedday' => $tax_day,
	        	);
	        	T09tb::where('t09tb.class', 'LIKE', $queryData['yerly'].'%')->where('t09tb.idno',  $row['idno'])->update($fields);
            	$no++;

                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('tax_processing')){
                    $nowdata = T09tb::where('t09tb.class', 'LIKE', $queryData['yerly'].'%')->where('t09tb.idno',  $row['idno'])->get()->toarray();
                    createModifyLog('U','T09tb',$olddata,$nowdata,end($sql));
                }

            }

            $query = T09tb::select('t09tb.idno', DB::raw('SUM(noteamt) as total'), DB::raw('SUM(deductamt2) as deduct'), DB::raw('SUM(noteamt)-SUM(deductamt2) as net'));
            $query->where('t09tb.idno', $row['idno']);
            $query->where('t09tb.class', 'LIKE', $queryData['yerly'].'%');
            $query->where('t09tb.paidday', '<>', '');
            $query->groupBy('t09tb.idno');
            $query->havingRaw('SUM(noteamt)>0');
            $Tax98_array = $query->get()->toArray();
            if(!empty($Tax98_array)){
            	$fields_98 = array(
			        'year' => str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT),    /* 所得年度 */
			        'serno' => str_pad($no,7,'0',STR_PAD_LEFT),     /* 編號 */
			        'taxorgan' => $S02tb_data['taxorgan'],  /* 稽徵機關代號 */
			        'taxcode' => $S02tb_data['taxcode'],   /* 媒體代號 */
			        'incomemk' => '',  /* 註記 */
			        'type' => '9B',    /* 格式 */
			        'idno' => $row['idno'],      /* 所得人統一編號 */
			        'idkind' => $row['idkind'],    /* 證號別 */
			        'companyno' => $S02tb_data['companyno'], /* 營利事業統一編號 */
			        'total' => str_pad($Tax98_array[0]['total'],10,'0',STR_PAD_LEFT),     /* 給付總額 */
			        'deduct' => str_pad($Tax98_array[0]['deduct'],10,'0',STR_PAD_LEFT),    /* 扣繳稅額 */
			        'net' => str_pad($Tax98_array[0]['net'],10,'0',STR_PAD_LEFT),       /* 給付淨額 */
			        'identcode' => '98', /* 所得人代號/費用 */
			        'spmk' => '#',      /* 分隔註記 */
			        'errormk' => '',   /* 錯誤註記 */
			        'name' => $row['cname'],      /* 所得人中文姓名 */
			        'address' => str_replace(array("\r", "\n", "\r\n", "\n\r"), '', $row['regaddress']), /* 所得人中文地址 */
			        'filedate' => $tax_day,   /* 檔案製作日期 */
            	);
            	T12tb::create($fields_98);

                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('tax_processing')){
                    $nowdata = T12tb::where('idno', $row['idno'])->where('serno', $fields_98['serno'])->where('type', $fields_98['type'])->where('year', $fields_98['year'])->where('filedate', $tax_day)->get()->toarray();
                    createModifyLog('I','T12tb','',$nowdata,end($sql));
                }

                if(checkNeedModifyLog('tax_processing')){
                    $olddata = T09tb::where('t09tb.class', 'LIKE', $queryData['yerly'].'%')->where('t09tb.idno',  $row['idno'])->get()->toarray();
                }

            	$fields = array(
            		'taxedday' => $tax_day,
	        	);
            	T09tb::where('t09tb.class', 'LIKE', $queryData['yerly'].'%')->where('t09tb.idno',  $row['idno'])->update($fields);
            	$no++;

                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('tax_processing')){
                    $nowdata = T09tb::where('t09tb.class', 'LIKE', $queryData['yerly'].'%')->where('t09tb.idno',  $row['idno'])->get()->toarray();
                    createModifyLog('U','T09tb',$olddata,$nowdata,end($sql));
                }
            }
        }

        if($no == '1'){
        	return false;
        }else{
        	return true;
        }

    }

    public function getFileName($queryData = []){
    	$query = S02tb::select('s02tb.taxcode');
        $S02tb_data = $query->get()->toArray();
        $S02tb_data = $S02tb_data[0];
        $f_name = $S02tb_data['taxcode'].$queryData['yerly'].".txt";
        return $f_name;
    }

    public function getFile($queryData = []){

        $query = T12tb::select('t12tb.taxorgan', 't12tb.taxcode', 't12tb.serno', 't12tb.incomemk', 't12tb.type', 't12tb.idno', 't12tb.idkind', 't12tb.companyno', 't12tb.total', 't12tb.deduct', 't12tb.net', 't12tb.identcode', 't12tb.spmk', 't12tb.errormk', 't12tb.year', 't12tb.name', 't12tb.address', 't12tb.filedate');
        $queryData['yerly'] = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT);
        $query->where('t12tb.year', $queryData['yerly']);
        $file_data = $query->get()->toArray();

        $text = '';
        foreach($file_data as $row){
            $text .= $row["taxorgan"].$row["taxcode"].$row["serno"];
            if(empty($row["incomemk"])){
            	$text .= ' ';
            }
            $text .= $row["type"].$row["idno"].$row["idkind"].$row["companyno"].$row["total"].$row["deduct"].$row["net"];
            if(empty($row["identcode"])){
            	$text .= '            ';
            }else{
            	$text .= $row["identcode"].'          ';
            }
            $text .= $row["spmk"];
            if(empty($row["errormk"])){
            	$text .= ' ';
            }
            $text .= $row["year"].$row["name"].$row["address"].$row["filedate"]."\n";

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
