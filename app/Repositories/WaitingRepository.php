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
use DB ;

class WaitingRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getWaitingList($queryData = [])
    {
        $query = T01tb::select('t04tb.sponsor', 't04tb.sdate', 't04tb.edate', 't04tb.term', 't01tb.class', 't01tb.name', 't01tb.branch' , 't01tb.process' , 't01tb.branchname');

        $query->join('t04tb', function($join)
        {
            $join->on('t04tb.class', '=', 't01tb.class');
        });

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('class', 'asc');
            $query->orderBy('term', 'asc');
            $query->orderBy('t04tb.sdate', 'asc');
        }
        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {
            $queryData['yerly'] = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT);
            $query->where('t01tb.yerly', $queryData['yerly']);
        }
        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            $query->where('t01tb.class', 'LIKE', '%'.$queryData['class'].'%');
        }
        // 班別
        if ( isset($queryData['name']) && $queryData['name'] ) {
            $query->where('t01tb.name', 'LIKE', '%'.$queryData['name'].'%');
        }
        // 分班名稱
        if ( isset($queryData['branchname']) && $queryData['branchname'] ) {
            $query->where('t01tb.branchname', 'LIKE', '%'.$queryData['branchname'].'%');
        }
        // 辦班院區
        if ( isset($queryData['branch']) && $queryData['branch'] ) {
            $query->where('t01tb.branch', 'LIKE', '%'.$queryData['branch'].'%');
        }
        // 班別類型
        if ( isset($queryData['process']) && $queryData['process'] ) {
            $query->where('t01tb.process', 'LIKE', '%'.$queryData['process'].'%');
        }
        // 訓練性質
        if ( isset($queryData['traintype']) && $queryData['traintype'] ) {
            $query->where('t01tb.traintype', 'LIKE', '%'.$queryData['traintype'].'%');
        }
        // 類別1
        if ( isset($queryData['categoryone']) && $queryData['categoryone'] ) {
            $query->where('t01tb.categoryone', $queryData['categoryone']);
        }
        // 班務人員
        if ( isset($queryData['sponsor']) && $queryData['sponsor'] ) {
            $query->where('t04tb.sponsor', '=', $queryData['sponsor']);
        }
        // 班別性質
        if ( isset($queryData['type']) && $queryData['type'] ) {
            $query->where('t01tb.type', $queryData['type']);
        }
        // 上課地點
        if ( isset($queryData['sitebranch']) && $queryData['sitebranch'] ) {
            $query->where('t04tb.site_branch', $queryData['sitebranch']);
        }
        // 開訓日期
        if ( isset($queryData['sdate']) && $queryData['sdate'] ) {
            $queryData['sdate'] = str_pad($queryData['sdate'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.sdate', '>=', $queryData['sdate']);
        }
        if ( isset($queryData['edate']) && $queryData['edate'] ) {
            $queryData['edate'] = str_pad($queryData['edate'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.sdate', '<=', $queryData['edate']);
        }
        // 結訓日期
        if ( isset($queryData['sdate2']) && $queryData['sdate2'] ) {
            $queryData['sdate2'] = str_pad($queryData['sdate2'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.edate', '>=', $queryData['sdate2']);
        }
        if ( isset($queryData['edate2']) && $queryData['edate2'] ) {
            $queryData['edate2'] = str_pad($queryData['edate2'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.edate', '<=', $queryData['edate2']);
        }
        // 在訓日期
        if(isset($queryData['sdate3']) && $queryData['sdate3'] && isset($queryData['edate3']) && $queryData['edate3'] ){
            $queryData['sdate3'] = str_pad($queryData['sdate3'],7,'0',STR_PAD_LEFT);
            $queryData['edate3'] = str_pad($queryData['edate3'],7,'0',STR_PAD_LEFT);

            $query->leftJoin('t06tb', function($join)
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
            // $query->whereIn('t01tb.class', $class_no_in);
            // //
            $query->where('t06tb.date', '>=', $queryData['sdate3']);
            $query->where('t06tb.date', '<=', $queryData['edate3']);
            $query->distinct();
        }else{
            if ( isset($queryData['sdate3']) && $queryData['sdate3'] ) {
                $query->leftJoin('t06tb', function($join)
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
                // $query->whereIn('t01tb.class', $class_no_in);
                // //
                $query->where('t06tb.date', '>=', $queryData['sdate3']);
                $query->distinct();
            }
            if ( isset($queryData['edate3']) && $queryData['edate3'] ) {
                $query->leftJoin('t06tb', function($join)
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
                // $query->whereIn('t01tb.class', $class_no_in);
                // //
                $query->where('t06tb.date', '<=', $queryData['edate3']);
                $query->distinct();
            }
        }

        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {

            // $queryData['term'] = str_pad($queryData['term'] ,2,'0',STR_PAD_LEFT);

            $query->where('t04tb.term', $queryData['term']);
        }

        $query->groupBy('t04tb.class', 't04tb.term');

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

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

    public function getDetailList($queryData = [])
    {
        $query = T08tb::select('t08tb.id', 't08tb.idno', 't08tb.cname', 't08tb.course', 't08tb.dept', 't08tb.position', 't08tb.liaison', 't08tb.hire', 't08tb.email');
        if(!empty($queryData['hire'])){
            $list = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->where('hire', $queryData['hire'])->get()->toArray();
        }else{
            $list = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();
        }

        foreach($list as $key => $row){
            $query = T06tb::select('t06tb.name', 't06tb.date');
            $class_name = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->where('course', $row['course'])->get()->toArray();

            if(isset($class_name[0]['name'])){
                $list[$key]['name'] = $class_name[0]['name'];
            }else{
                $list[$key]['name'] = '';
            }

            if(isset($class_name[0]['date'])){
                $list[$key]['date'] = substr($class_name[0]['date'], '3', '2').'/'.substr($class_name[0]['date'], '5', '2');
            }else{
                $list[$key]['date'] = '';
            }

            $query = T09tb::select('t09tb.lecthr');
            $t09data = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->where('course', $row['course'])->where('idno', $row['idno'])->get()->toArray();

            if(isset($t09data[0]['lecthr'])){
                $list[$key]['lecthr'] = $t09data[0]['lecthr'];
            }else{
                $list[$key]['lecthr'] = '';
            }

            // echo '<pre style="text-align:left;">' . "\n";
            // print_r($t09data);
            // echo "\n</pre>\n";


        }

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($list);
        // echo "\n</pre>\n";
        // die();
        return $list;
    }

    public function getById($id=NULL)
    {
        $query = T08tb::select('*');
        $data = $query->where('id', $id)->get()->toArray();
        $data = $data[0];
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($data);
        // echo "\n</pre>\n";
        // die();
        return $data;
    }

    public function getProfchk($class=NULL)
    {
        $query = T01tb::select('t01tb.profchk');
        $data = $query->where('class', $class)->get()->toArray();
        $data = $data[0];

        return $data;
    }

    public function getHourById($id=NULL)
    {
        $hour = '0';
        $query = T08tb::select('*');
        $data = $query->where('id', $id)->get()->toArray();
        $data = $data[0];

        $year = substr($data['class'], 0, 3);

        $class_no = T01tb::select('t08tb.class', 't08tb.term', 't08tb.course')
                    ->join('t08tb', function($join)
                    {
                        $join->on('t08tb.class', '=', 't01tb.class');
                    })
                    ->where('t01tb.class', 'like', $year.'%')
                    ->where('t08tb.hire', '=', 'Y')
                    ->where('t01tb.profchk', '=', 'Y')
                    ->where('t08tb.idno', '=', $data['idno'])
                    ->get();

        foreach ($class_no as $key => $row) {
            $class_no_in[$key]['class'] = $row->class;
            $class_no_in[$key]['term'] = $row->term;
            $class_no_in[$key]['course'] = $row->course;
        }

        if(!empty($class_no_in)){
            foreach($class_no_in as $row){
                $p_hour = T06tb::select('t06tb.hour')
                            ->where('t06tb.class', '=', $row['class'])
                            ->where('t06tb.term', '=', $row['term'])
                            ->where('t06tb.course', '=', $row['course'])
                            ->get()->toArray();

                $hour = $hour + $p_hour[0]['hour'];
            }
        }

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($hour);
        // echo "\n</pre>\n";

        if($data['hire'] != 'Y'){
            $p_hour = T06tb::select('t06tb.hour')
                    ->where('t06tb.class', '=', $data['class'])
                    ->where('t06tb.term', '=', $data['term'])
                    ->where('t06tb.course', '=', $data['course'])
                    ->get()->toArray();
            $hour = $hour + $p_hour[0]['hour'];
        }

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($hour);
        // echo "\n</pre>\n";
        // die();

        return $hour;
    }

    public function getEditDelete($id=NULL)
    {
        $EditDelete = array(
            'EditorDelete' => 'N',
            'paidday' => '',
        );

        $query = T08tb::select('*');
        $data = $query->where('id', $id)->get()->toArray();
        $data = $data[0];

        $paidday = T09tb::select('t09tb.paidday')
                ->where('t09tb.class', '=', $data['class'])
                ->where('t09tb.term', '=', $data['term'])
                ->where('t09tb.course', '=', $data['course'])
                ->where('t09tb.idno', '=', $data['idno'])
                ->get()->toArray();

        if(empty($paidday)){
            $EditDelete['EditorDelete'] = 'Y';
        }else{
            if($paidday[0]['paidday'] == ''){
                $EditDelete['EditorDelete'] = 'Y';
            }else{
                $EditDelete['paidday'] = $paidday[0]['paidday'];
            }
        }

        return $EditDelete;
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
        $sponsor[''] = '';

        return $sponsor;
    }

    public function WhenMark($id=NULL){
        $query = T08tb::select('*');
        $data = $query->where('id', $id)->get()->toArray();
        $data = $data[0];

        $query = M01tb::select('kind', 'idkind', 'insurekind1', 'insurekind2');
        $data2 = $query->where('idno', $data['idno'])->get()->toArray();
        $data2 = $data2[0];

        if(empty($data2['kind'])){
            $data2['kind'] = '1';
        }

        $query = S02tb::select('motorunit', 'outlectunit', 'burlectunit', 'inlectunit', 'insurerate');
        $data3 = $query->distinct()->get()->toArray();
        $data3 = $data3[0];

        if($data3['motorunit'] > 0){
            $motoramt = $data3['motorunit'];
        }else{
            $motoramt = '0';
        }

        $query = T06tb::select('hour', 'date');
        $data4 = $query->where('class', $data['class'])
                 ->where('term', $data['term'])
                 ->where('course', $data['course'])
                 ->get()->toArray();
        $data4 = $data4[0];

        $query = T01tb::select('type');
        $data5 = $query->where('class', $data['class'])
                 ->get()->toArray();
        $data5 = $data5[0];

        if($data5['type'] == '13'){
            $motoramt = '0';
        }

        $fields = array(
            'class' => $data['class'],
            'term' => $data['term'],
            'course' => $data['course'],
            'idno' => $data['idno'],
            'type' => '1',
            'kind' => $data2['kind'],
            'lecthr' => $data4['hour'],
            'motoramt' => $motoramt,
            'trainamt' => '0',
            'planeamt' => '0',
            'otheramt' => '0',
        );

        if($fields['type'] == '1'){
            $sngA = 1;
        }else{
            $sngA = 0.5;
        }

        if(empty($data4['date']) || $data4['date'] > '1070131' ){

            switch ($fields['kind']) {
                case '1':
                    $fields['lectamt'] = $fields['lecthr'] * $data3['outlectunit'] * $sngA;
                    break;

                case '2':
                    $fields['lectamt'] = $fields['lecthr'] * $data3['burlectunit'] * $sngA;
                    break;

                case '3':
                    $fields['lectamt'] = $fields['lecthr'] * $data3['inlectunit'] * $sngA;
                    break;

                case '4':
                    $fields['lectamt'] = '0';
                    break;
            }

        }else{

            switch ($fields['kind']) {
                case '1':
                    $fields['lectamt'] = $fields['lecthr'] * 1600 * $sngA;
                    break;

                case '2':
                    $fields['lectamt'] = $fields['lecthr'] * 1200 * $sngA;
                    break;

                case '3':
                    $fields['lectamt'] = $fields['lecthr'] * 800 * $sngA;
                    break;

                case '4':
                    $fields['lectamt'] = '0';
                    break;
            }

        }

        $fields['noteamt'] = '0';
        $fields['speakamt'] = '0';
        $fields['teachtot'] = $fields['lectamt'] + $fields['noteamt'] + $fields['speakamt'];
        //(交通費合計tratot)=motoramt+trainamt+planeamt+otheramt
        $fields['trainamt'] = '0';
        $fields['planeamt'] = '0';
        $fields['otheramt'] = '0';
        $fields['mrtamt'] = '0';
        $fields['ship'] = '0';
        $fields['tratot'] = $fields['motoramt'] + $fields['trainamt'] + $fields['planeamt'] + $fields['otheramt'] + $fields['mrtamt'] + $fields['ship'];

        $return_array = $this->CalculateTheAmount($data2['idkind'] , $fields['lectamt'] , $fields['noteamt'] , $fields['speakamt']);
        $fields = array_merge($fields, $return_array);

        if(empty($data4['date'])){
            $query = T04tb::select('t04tb.sdate');
            $results = $query->where('class', $data['class'])->where('term', $data['term'])->get()->toArray();
            $Computedate = $results[0]['sdate'];
        }else{
            $Computedate = $data4['date'];
        }

        $ComputeInsuretot_array = $this->ComputeInsuretot($fields['kind'] , $Computedate , $data3['insurerate'] , $data2['insurekind1'] , $data2['insurekind2'], $fields['lectamt'] , $fields['noteamt'] , $fields['speakamt']);

        $fields = array_merge($fields, $ComputeInsuretot_array);

        $fields['netpay'] = $fields['teachtot']-$fields['deductamt']-$fields['insuretot'];

        $fields['totalpay'] = $fields['netpay'] + $fields['tratot'];

        setProgid('waiting');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG

        T09tb::create($fields);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('waiting')){
            $nowdata = T09tb::where('idno', $data['idno'])->where('class', $data['class'])->where('term', $data['term'])->where('course', $data['course'])->get()->toarray();
            createModifyLog('I','T09tb','',$nowdata,end($sql));
        }

    }

    public function pay($id=NULL){
        $query = T08tb::select('*');
        $data = $query->where('id', $id)->get()->toArray();
        $data = $data[0];

        $query = M01tb::select('kind', 'idkind', 'insurekind1', 'insurekind2');
        $data2 = $query->where('idno', $data['idno'])->get()->toArray();
        $data2 = $data2[0];

        if(empty($data2['kind'])){
            $data2['kind'] = '1';
        }

        $query = S02tb::select('motorunit', 'outlectunit', 'burlectunit', 'inlectunit', 'insurerate');
        $data3 = $query->distinct()->get()->toArray();
        $data3 = $data3[0];

        if($data3['motorunit'] > 0){
            $motoramt = $data3['motorunit'];
        }else{
            $motoramt = '0';
        }

        $query = T06tb::select('hour', 'date');
        $data4 = $query->where('class', $data['class'])
                 ->where('term', $data['term'])
                 ->where('course', $data['course'])
                 ->get()->toArray();
        $data4 = $data4[0];

        $query = T01tb::select('type');
        $data5 = $query->where('class', $data['class'])
                 ->get()->toArray();
        $data5 = $data5[0];

        if($data5['type'] == '13'){
            $motoramt = '0';
        }

        $fields = array(
            'kind' => $data2['kind'],
            'motoramt' => $motoramt,
            'lecthr' => $data4['hour'],
        );

        $t09type = T09tb::select('t09tb.type', 't09tb.noteamt', 't09tb.speakamt', 't09tb.trainamt', 't09tb.planeamt', 't09tb.otheramt', 't09tb.ship', 't09tb.mrtamt')
                ->where('t09tb.class', '=', $data['class'])
                ->where('t09tb.term', '=', $data['term'])
                ->where('t09tb.course', '=', $data['course'])
                ->where('t09tb.idno', '=', $data['idno'])
                ->get()->toArray();

        if($t09type[0]['type'] == '1'){
            $sngA = 1;
        }else{
            $sngA = 0.5;
        }

        if(empty($data4['date']) || $data4['date'] > '1070131' ){

            switch ($fields['kind']) {
                case '1':
                    $fields['lectamt'] = $fields['lecthr'] * $data3['outlectunit'] * $sngA;
                    break;

                case '2':
                    $fields['lectamt'] = $fields['lecthr'] * $data3['burlectunit'] * $sngA;
                    break;

                case '3':
                    $fields['lectamt'] = $fields['lecthr'] * $data3['inlectunit'] * $sngA;
                    break;

                case '4':
                    $fields['lectamt'] = '0';
                    break;
            }

        }else{

            switch ($fields['kind']) {
                case '1':
                    $fields['lectamt'] = $fields['lecthr'] * 1600 * $sngA;
                    break;

                case '2':
                    $fields['lectamt'] = $fields['lecthr'] * 1200 * $sngA;
                    break;

                case '3':
                    $fields['lectamt'] = $fields['lecthr'] * 800 * $sngA;
                    break;

                case '4':
                    $fields['lectamt'] = '0';
                    break;
            }

        }

        if(!empty($t09type[0]['noteamt'])){
            $fields['noteamt'] = $t09type[0]['noteamt'];
        }else{
            $fields['noteamt'] = '0';
        }

        foreach($t09type[0] as $key => $row){
            if(!empty($t09type[0][$key])){
                $fields[$key] = $row;
            }else{
                $fields[$key] = '0';
            }
        }

        $fields['teachtot'] = $fields['lectamt'] + $fields['noteamt'] + $fields['speakamt'];
        $fields['tratot'] = $fields['motoramt'] + $fields['trainamt'] + $fields['planeamt'] + $fields['otheramt'] + $fields['mrtamt'] + $fields['ship'];

        $return_array = $this->CalculateTheAmount($data2['idkind'] , $fields['lectamt'] , $fields['noteamt'] , $fields['speakamt']);

        $fields = array_merge($fields, $return_array);

        if(empty($data4['date'])){
            $query = T04tb::select('t04tb.sdate');
            $results = $query->where('class', $data['class'])->where('term', $data['term'])->get()->toArray();
            // dd($results);
            $Computedate = $results[0]['sdate'];
        }else{
            $Computedate = $data4['date'];
        }
        // dd($data3);
        $ComputeInsuretot_array = $this->ComputeInsuretot($fields['kind'] , $Computedate , $data3['insurerate'] , $data2['insurekind1'] , $data2['insurekind2'], $fields['lectamt'] , $fields['noteamt'] , $fields['speakamt']);

        $fields = array_merge($fields, $ComputeInsuretot_array);

        $fields['netpay'] = $fields['teachtot']-$fields['deductamt']-$fields['insuretot'];

        $fields['totalpay'] = $fields['netpay'] + $fields['tratot'];

        if(isset($data['idno']) && isset($data['class']) && isset($data['term']) && isset($data['course'])){

            setProgid('waiting');
            DB::connection()->enableQueryLog(); //啟動SQL_LOG
            if(checkNeedModifyLog('waiting')){
                $olddata = T09tb::where('idno', $data['idno'])->where('class', $data['class'])->where('term', $data['term'])->where('course', $data['course'])->get()->toarray();
            }

            T09tb::where('idno', $data['idno'])
            ->where('class', $data['class'])
            ->where('term', $data['term'])
            ->where('course', $data['course'])
            ->update($fields);

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('waiting')){
                $nowdata = T09tb::where('idno', $data['idno'])->where('class', $data['class'])->where('term', $data['term'])->where('course', $data['course'])->get()->toarray();
                createModifyLog('U','T09tb',$olddata,$nowdata,end($sql));
            }

        }

    }

    public function notpay($id=NULL){
        $query = T08tb::select('*');
        $data = $query->where('id', $id)->get()->toArray();
        $data = $data[0];

        $fields = array(
            'kind' => '4',
            'motoramt' => '0',
            'lecthr' => '0',
            'lectamt' => '0',
            'noteamt' => '0',
            'speakamt' => '0',
            'teachtot' => '0',
            'trainamt' => '0',
            'planeamt' => '0',
            'otheramt' => '0',
            'mrtamt' => '0',
            'ship' => '0',
            'deductamt' => '0',
            'insureamt1' => '0',
            'insureamt2' => '0',
            'insuretot' => '0',
            'netpay' => '0',
            'totalpay' => '0',
        );

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($data);
        // echo "\n</pre>\n";
        // die();

        if(isset($data['idno']) && isset($data['class']) && isset($data['term']) && isset($data['course'])){

            setProgid('waiting');
            DB::connection()->enableQueryLog(); //啟動SQL_LOG
            if(checkNeedModifyLog('waiting')){
                $olddata = T09tb::where('idno', $data['idno'])->where('class', $data['class'])->where('term', $data['term'])->where('course', $data['course'])->get()->toarray();
            }

            T09tb::where('idno', $data['idno'])
            ->where('class', $data['class'])
            ->where('term', $data['term'])
            ->where('course', $data['course'])
            ->update($fields);

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('waiting')){
                $nowdata = T09tb::where('idno', $data['idno'])->where('class', $data['class'])->where('term', $data['term'])->where('course', $data['course'])->get()->toarray();
                createModifyLog('U','T09tb',$olddata,$nowdata,end($sql));
            }

        }

    }

    public function MarkDelete($id=NULL){
        $query = T08tb::select('*');
        $data = $query->where('id', $id)->get()->toArray();
        $data = $data[0];

        setProgid('waiting');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        if(checkNeedModifyLog('waiting')){
            $olddata = T09tb::where('idno', $data['idno'])->where('class', $data['class'])->where('term', $data['term'])->where('course', $data['course'])->get()->toarray();
        }

        T09tb::where('class', $data['class'])
        ->where('term', $data['term'])
        ->where('course', $data['course'])
        ->where('idno', $data['idno'])
        ->delete();

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('waiting')){
            createModifyLog('D','T09tb',$olddata,'',end($sql));
        }
    }

    public function CalculateTheAmount($idkind , $lectamt , $noteamt , $speakamt){
        // 證號別(m01tb.idkind)
        // 3,4,7 【s02tb.deductrate2 外國人扣繳稅率】
        // 其它　【s02tb.deductrate1 本國人扣繳稅率】
        //
        // 【deductamt 扣繳稅額】：deductamt1 + deductamt2
        // 講座為外國人:
        // 50稅額：(lectamt + speakamt) * s02tb.deductrate2 / 100
        // 9B稅額：IIF(noteamt > 5000, noteamt * 0.2, 0)
        //
        // 講座為本國人:
        // 50稅額：(lectamt + speakamt) * s02tb.deductrate1 / 100
        // 9B稅額：IIF(noteamt > 5000, noteamt * 0.1, 0)
        $query = S02tb::select('motorunit', 'outlectunit', 'burlectunit', 'inlectunit', 'deductrate1', 'deductrate2');
        $data3 = $query->distinct()->get()->toArray();
        $data3 = $data3[0];

        if(in_array($idkind, array('3', '4', '7'))){

            $deductamt1 = ($lectamt + $speakamt) * ($data3['deductrate2'] / 100);
            if($noteamt > 5000){
                $deductamt2 = $noteamt * 0.2;
            }else{
                $deductamt2 = 0;
            }

        }else{
            $deductamt1 = ($lectamt + $speakamt) * ($data3['deductrate1'] / 100);
            if($noteamt > 5000){
                $deductamt2 = $noteamt * 0.1;
            }else{
                $deductamt2 = 0;
            }

        }

        $deductamt = $deductamt1 + $deductamt2;

        $fields = array(
            'deductamt' => $deductamt,
            'deductamt1' => $deductamt1,
            'deductamt2' => $deductamt2,
        );

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($fields);
        // echo "\n</pre>\n";
        // die();
        return $fields;
    }

    public function ComputeInsuretot($kind , $date , $insurerate , $insurekind1 , $insurekind2 , $lectamt , $noteamt , $speakamt){
        $fields = array(
            'insuremk1' => 'Y',
            'insuremk2' => 'Y',
            'insureamt1' => '0',
            'insureamt2' => '0',
        );

        if(in_array($kind, array('1', '2', '4'))){
            if($insurekind1 == 'Y'){
                $fields['insuremk1'] = 'N';
            }else{
                $fields['insuremk1'] = 'Y';
            }

            if($insurekind2 == 'Y'){
                $fields['insuremk2'] = 'N';
            }else{
                $fields['insuremk2'] = 'Y';
            }
        }

        if(in_array($kind, array('3'))){
            $fields['insuremk1'] = 'N';
            $fields['insuremk2'] = 'N';
        }
        if($fields['insuremk1'] == 'Y'){

            if($date >= "1030901" && $date <= "1040630"){
                if(($lectamt + $speakamt) > 19273){
                    $fields['insureamt1'] = ($lectamt + $speakamt) * $insurerate;
                }
            }else if($date >= "1040701"){
                if(($lectamt + $speakamt) >= 20008){
                    $fields['insureamt1'] = ($lectamt + $speakamt) * $insurerate;
                }
            }else{
                if(($lectamt + $speakamt) > 5000){
                    $fields['insureamt1'] = ($lectamt + $speakamt) * $insurerate;
                }
            }
        }
        if($fields['insuremk2'] == 'Y'){
            if($date >= "1050101"){
                if($noteamt >= 20000){
                    $fields['insureamt2'] = $noteamt * $insurerate;
                }
            }else{
                if($noteamt > 5000){
                    $fields['insureamt2'] = $noteamt * $insurerate;
                }
            }
        }

        $fields['insuretot'] = $fields['insureamt1'] + $fields['insureamt2'];

        return $fields;
    }

}
