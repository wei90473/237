<?php
namespace App\Repositories;

use App\Models\Term_process;
use App\Models\Class_process_job;
use App\Models\Class_process;
use App\Models\SponsorAgent;
use App\Models\T04tb;
use App\Models\T01tb;
use App\Models\M09tb;
use DB;

class Term_processRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getList($queryData = [])
    {
        $query = T01tb::select('t04tb.sponsor', 't04tb.sdate', 't04tb.edate', 't04tb.term', 't01tb.class', 't01tb.name' , 't01tb.class_process');

        $query->join('t04tb', function($join)
        {
            $join->on('t04tb.class', '=', 't01tb.class');
        });

        $query->orderBy('class', 'asc');
        $query->orderBy('term', 'asc');

        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {
            $queryData['yerly'] = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT);
            $query->where('t01tb.yerly', $queryData['yerly']);
        }

        if ( isset($queryData['sponsor']) && $queryData['sponsor'] ) {
            $query->where('t04tb.sponsor', '=', $queryData['sponsor']);
        }

        if ( isset($queryData['process_complete']) && $queryData['process_complete'] ) {
            $query->where('t04tb.process_complete', '=', $queryData['process_complete']);
        }

        $query->whereNotNull('t01tb.class_process');
        $query->where("t01tb.class_process", '!=', '0');

        $data = $query->get()->toArray();

        if(!empty($data)){
            $data = $this->getJob($data, $queryData);
        }

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($data);
        // echo "\n</pre>\n";
        // die();

        return $data;
    }

    public function getJob($data, $queryData)
    {
        // deadline 1:無期限  2:開課前  3:結訓後
        // deadline_type 1 開課前 deadline_day_1 天
        // deadline_type 2 開課前 deadline_day_2 上週星期
        // deadline_type 3 開課前 deadline_day_3 號
        // deadline_type 4 結訓後 deadline_day_4 天
        // deadline_type 5 結訓後 deadline_day_5 上週星期
        // deadline_type 6 結訓後 deadline_day_6 號

        foreach($data as & $row){
            $query = Class_process_job::select('id', 'class_process_id', 'name', 'job', 'type', 'deadline', 'deadline_type', 'deadline_day', 'freeze', 'file');
            $query->where('class_process_id', '=', $row['class_process']);
            $query->orderBy('type', 'asc');
            $job_data = $query->get()->toArray();
            if(!empty($job_data)){
                foreach($job_data as $key => $job_row){

                    $job_data[$key]['url'] = $this->getUrl($job_row['job'], $row['class'], $row['term'], $row['sdate']);
                    $job_data[$key]['deadline'] = $this->getDeadline($job_row['deadline'], $job_row['deadline_type'], $job_row['deadline_day'], $row['sdate'], $row['edate']);
                    $query = Term_process::select('complete');
                    $query->where('class_process_job_id', '=', $job_row['id']);
                    $query->where('class_process_id', '=', $job_row['class_process_id']);
                    $query->where('class', '=', $row['class']);
                    $query->where('term', '=', $row['term']);
                    $complete_data = $query->get()->toArray();
                    if(!empty($complete_data)){
                        if($complete_data[0]['complete'] == 'Y'){
                            $job_data[$key]['complete'] = 'Y';
                        }else{
                            $job_data[$key]['complete'] = 'N';
                        }
                    }else{
                        $job_data[$key]['complete'] = 'N';
                    }
                    if(isset($queryData['job_complete']) && $queryData['job_complete'] == 'N' && $job_data[$key]['complete'] == 'Y'){
                        unset($job_data[$key]);
                    }
                }
            }
            $row['job_data'] = $job_data;
        }

        return $data;
    }

    public function getDeadline($deadline, $deadline_type, $deadline_day, $sdate, $edate)
    {

    	if($deadline == '1'){
            $deadline = '無期限';
        }
        if($deadline == '2'){
            $deadline = $sdate + 19110000;
            if($deadline_type == '1'){
                $deadline = (date("Y",strtotime($deadline))-1911).date("/m/d",strtotime("-{$deadline_day} day",strtotime($deadline)));
            }
            if($deadline_type == '2'){
                if($deadline_day == '1'){
                    $deadline = (date("Y",strtotime($deadline))-1911).date("/m/d",strtotime("-1 monday",strtotime($deadline)));
                }
                if($deadline_day == '2'){
                    $deadline = (date("Y",strtotime($deadline))-1911).date("/m/d",strtotime("-1 tuesday",strtotime($deadline)));
                }
                if($deadline_day == '3'){
                    $deadline = (date("Y",strtotime($deadline))-1911).date("/m/d",strtotime("-1 wednesday",strtotime($deadline)));
                }
                if($deadline_day == '4'){
                    $deadline = (date("Y",strtotime($deadline))-1911).date("/m/d",strtotime("-1 thursday",strtotime($deadline)));
                }
                if($deadline_day == '5'){
                    $deadline = (date("Y",strtotime($deadline))-1911).date("/m/d",strtotime("-1 friday",strtotime($deadline)));
                }
            }
            if($deadline_type == '3'){
                $deadline = (date("Y",strtotime($deadline))-1911).date("/m",strtotime("-1 month",strtotime($deadline)))."/".str_pad($deadline_day,2,'0',STR_PAD_LEFT);
            }
        }
        if($deadline == '3'){
            $deadline = $edate + 19110000;
            if($deadline_type == '4'){
                $deadline = (date("Y",strtotime($deadline))-1911).date("/m/d",strtotime("+{$deadline_day} day",strtotime($deadline)));
            }
            if($deadline_type == '5'){
                if($deadline_day == '1'){
                    $deadline = (date("Y",strtotime($deadline))-1911).date("/m/d",strtotime("+2 monday",strtotime($deadline)));
                }
                if($deadline_day == '2'){
                    $deadline = (date("Y",strtotime($deadline))-1911).date("/m/d",strtotime("+2 tuesday",strtotime($deadline)));
                }
                if($deadline_day == '3'){
                    $deadline = (date("Y",strtotime($deadline))-1911).date("/m/d",strtotime("+2 wednesday",strtotime($deadline)));
                }
                if($deadline_day == '4'){
                    $deadline = (date("Y",strtotime($deadline))-1911).date("/m/d",strtotime("+2 thursday",strtotime($deadline)));
                }
                if($deadline_day == '5'){
                    $deadline = (date("Y",strtotime($deadline))-1911).date("/m/d",strtotime("+2 friday",strtotime($deadline)));
                }
            }
            if($deadline_type == '6'){
                $deadline = (date("Y",strtotime($deadline))-1911).date("/m",strtotime("+1 month",strtotime($deadline)))."/".str_pad($deadline_day,2,'0',STR_PAD_LEFT);
            }
        }
        return $deadline;
    }

    public function getUrl($job, $class, $term, $sdate)
    {

        $url = '';

        switch ($job) {

            case'unit' :
                 $url = "/admin/unit/{$class}/{$term}";
                 break;

            case'arrangement_class' :
                 $url = "/admin/arrangement/{$class}/{$term}";
                 break;

            case'arrangement_upload' :
                 $url = "/admin/arrangement/{$class}/{$term}";
                 break;

            case'class_schedule' :
                 $url = "/admin/class_schedule/{$class}{$term}/edit";
                 break;

            case'siteedit' :
                 $url = "/admin/class_schedule/siteedit/{$class}{$term}";
                 break;

            case'publishedit' :
                 $url = "/admin/class_schedule/{$class}{$term}/edit";
                 break;

            case'teaching_material' :
                 $url = '/admin/teaching_material';
                 break;

            case'method' :
                 $url = "/admin/method/{$class}{$term}/edit";
                 break;

            case'funding_edit_type1' :
                 $url = '/admin/funding/class_list';
                 break;

            case'funding_edit_type2' :
                 $url = '/admin/funding/class_list';
                 break;

            case'signup_edit_type1' :
                 $url = "/admin/signup/edit/{$class}/{$term}";
                 break;

            case'signup_edit_type2' :
                 $url = "/admin/signup/edit/{$class}/{$term}";
                 break;

            case'importApplyData' :
                 $url = "/admin/review_apply/{$class}/{$term}";
                 break;

            case'review_apply' :
                 $url = "/admin/review_apply/{$class}/{$term}";
                 break;

            case'assign' :
                 $url = "/admin/review_apply/assign?class={$class}&term={$term}";
                 break;

            case'special_class_fee' :
                 $url = "/admin/special_class_fee/edit/{$class}/{$term}";
                 break;

            case'transfer_training_letter' :
                 $url = "/admin/transfer_training_letter";
                 break;

            case'waiting' :
                 $url = "/admin/waiting/detail?class={$class}&term={$term}";
                 break;

            case'lecture' :
                 $url = '/admin/lecture';
                 break;

            case'lecture_upload' :
                 $url = '/admin/lecture';
                 break;

            case'employ' :
                 $url = "/admin/employ/detail?class={$class}&term={$term}";
                 break;

            case'teacher_related' :
                 $url = "/admin/teacher_related/detail?class={$class}&term={$term}&sdate={$sdate}";
                 break;

            case'teacher_related_url' :
                 $url = "/admin/teacher_related/detail?class={$class}&term={$term}&sdate={$sdate}";
                 break;

            case'lecture_mail' :
                 $url = "/admin/lecture_mail";
                 break;

            case'modify_manage' :
                 $url = '/admin/student_apply/modify_manage';
                 break;

            case'student_apply' :
                 $url = "/admin/student_apply/{$class}/{$term}";
                 break;

            case'arrange_group' :
                 $url = "/admin/student_apply/arrange_group/{$class}/{$term}";
                 break;

            case'arrange_stno' :
                 $url = "/admin/student_apply/arrange_stno/{$class}/{$term}";
                 break;

            case'leave' :
                 $url = "/admin/leave/{$class}/{$term}";
                 break;

            case'punch' :
                 $url = "/admin/punch/{$class}/{$term}";
                 break;

            case'student_grade_setting' :
                 $url = "/admin/student_grade/setting/{$class}/{$term}";
                 break;

            case'student_grade_input_grade' :
                 $url = "/admin/student_grade/input_grade/{$class}/{$term}";
                 break;

            case'digital_class_setting' :
                 $url = "/admin/digital/class_setting/{$class}/{$term}";
                 break;

            case'digital_student' :
                 $url = "/admin/digital/student/{$class}/{$term}";
                 break;

            case'trainQuestSetting' :
                 $url = "/admin/trainQuestSetting/setting/{$class}/{$term}";
                 break;

            case'notice_emai' :
                 $url = "/admin/notice_emai/detail?class={$class}&term={$term}";
                 break;

            case'effectiveness_survey' :
                 $url = "admin/effectiveness_survey?search=search&yerly=".substr($class, 0, 3)."&class={$class}&term={$term}";
                 break;

            case'classes_requirements' :
                 $url = "/admin/classes_requirements/edit/{$class}{$term}";
                 break;

            case'teaching_material_print' :
                 $url = "/admin/teaching_material_print/list/{$class}{$term}";
                 break;

            case'entryexport' :
                 $url = '/admin/entryexport';
                 break;

        }

        return $url;
    }

    public function getFreeze($job, $class, $term)
    {
    	$freeze = 'N';
    	$query = T04tb::select('t04tb.sponsor', 't04tb.sdate', 't04tb.edate', 't04tb.term', 't01tb.class', 't01tb.name' , 't01tb.class_process');
    	$query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 't04tb.class');
        });
    	if ( isset($class) ) {
            $query->where('t04tb.class', '=', $class);
        }
        if ( isset($term) ) {
            $query->where('t04tb.term', '=', $term);
        }
        $query->whereNotNull('t01tb.class_process');

        $data = $query->get()->toArray();

        if(!empty($data)){
        	if(!empty($data[0]['class_process'])){
        		$query = Class_process_job::select('deadline', 'deadline_type', 'deadline_day', 'freeze');
        		$query->where('class_process_id', '=', $data[0]['class_process']);
        		$query->where('job', '=', $job);
        		$query->where('freeze', '=', 'Y');
        		$job_data = $query->get()->toArray();
        	}
        	if(!empty($job_data) && $data[0]['sdate'] && $data[0]['edate']){
        		$deadline = $this->getDeadline2($job_data[0]['deadline'], $job_data[0]['deadline_type'], $job_data[0]['deadline_day'], $data[0]['sdate'], $data[0]['edate']);
        		$today = date('Y-m-d');
        		if(strtotime($today)>strtotime($deadline)){
        			$sponsor = auth()->user()->userid;
                    // $sponsor = 'NIMA';
        			$query = SponsorAgent::select('agent_userid');
	        		$query->where('userid', '=', $data[0]['sponsor']);
	        		$sponsor_data = $query->get()->toArray();
	        		if($sponsor == $data[0]['sponsor']){
	        			$freeze = 'Y';
	        		}
	        		foreach($sponsor_data as $sponsor_row){
	        			if($sponsor == $sponsor_row['agent_userid']){
	        				$freeze = 'Y';
	        			}
	        		}
        		}
        	}
        }
    	return $freeze;
    }

    public function getDeadline2($deadline, $deadline_type, $deadline_day, $sdate, $edate)
    {

    	if($deadline == '1'){
            $deadline = '無期限';
        }
        if($deadline == '2'){
            $deadline = $sdate + 19110000;
            if($deadline_type == '1'){
                $deadline = date("Y/m/d",strtotime("-{$deadline_day} day",strtotime($deadline)));
            }
            if($deadline_type == '2'){
                if($deadline_day == '1'){
                    $deadline = date("Y/m/d",strtotime("-1 monday",strtotime($deadline)));
                }
                if($deadline_day == '2'){
                    $deadline = date("Y/m/d",strtotime("-1 tuesday",strtotime($deadline)));
                }
                if($deadline_day == '3'){
                    $deadline = date("Y/m/d",strtotime("-1 wednesday",strtotime($deadline)));
                }
                if($deadline_day == '4'){
                    $deadline = date("Y/m/d",strtotime("-1 thursday",strtotime($deadline)));
                }
                if($deadline_day == '5'){
                    $deadline = date("Y/m/d",strtotime("-1 friday",strtotime($deadline)));
                }
            }
            if($deadline_type == '3'){
                $deadline = date("Y/m",strtotime("-1 month",strtotime($deadline)))."/".str_pad($deadline_day,2,'0',STR_PAD_LEFT);
            }
        }
        if($deadline == '3'){
            $deadline = $edate + 19110000;
            if($deadline_type == '4'){
                $deadline = date("Y/m/d",strtotime("+{$deadline_day} day",strtotime($deadline)));
            }
            if($deadline_type == '5'){
                if($deadline_day == '1'){
                    $deadline = date("Y/m/d",strtotime("+2 monday",strtotime($deadline)));
                }
                if($deadline_day == '2'){
                    $deadline = date("Y/m/d",strtotime("+2 tuesday",strtotime($deadline)));
                }
                if($deadline_day == '3'){
                    $deadline = date("Y/m/d",strtotime("+2 wednesday",strtotime($deadline)));
                }
                if($deadline_day == '4'){
                    $deadline = date("Y/m/d",strtotime("+2 thursday",strtotime($deadline)));
                }
                if($deadline_day == '5'){
                    $deadline = date("Y/m/d",strtotime("+2 friday",strtotime($deadline)));
                }
            }
            if($deadline_type == '6'){
                $deadline = date("Y/m",strtotime("+1 month",strtotime($deadline)))."/".str_pad($deadline_day,2,'0',STR_PAD_LEFT);
            }
        }
        return $deadline;
    }

    public function getProcess_non_complete()
    {
        // $sponsor = 'NIMA';
        $sponsor = auth()->user()->userid;
        $query = T01tb::select('t01tb.class','t04tb.term','t04tb.sponsor','t04tb.process_complete');
        $query->join('t04tb', function($join)
        {
            $join->on('t04tb.class', '=', 't01tb.class');
        });
        $query->whereNotNull('t01tb.class_process');
        $query->where("t01tb.class_process", '!=', '0');
        $query->where("t04tb.sponsor", $sponsor);
        $query->where("t04tb.process_complete", "N");
        $data = $query->get()->toArray();

        return $data;
    }

    public function getMail()
    {
        $today = (date("Y",strtotime('now'))-1911).date("md",strtotime('now'));
        $edate = (date("Y",strtotime('now'))-1911).date("m",strtotime("-1 month",strtotime('now')))."01";
        $sdate = (date("Y",strtotime('now'))-1911).date("mt",strtotime("+1 month",strtotime('now')));

        $query = T04tb::select('t04tb.sponsor', 't04tb.sdate', 't04tb.edate', 't04tb.term', 't01tb.class', 't01tb.name' , 't01tb.class_process');
        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 't04tb.class');
        });
        $query->whereBetween('t04tb.sdate', array($today, $sdate));
        $query->whereNotNull('t01tb.class_process');
        $query->where('t01tb.class_process', '!=', '0');
        $sdate_data = $query->get()->toArray();
        $mail_data = array();
        foreach($sdate_data as $sdate_row){
            $query = Class_process_job::select('name', 'deadline_type', 'deadline_day');
            $query->where('class_process_id', '=', $sdate_row['class_process']);
            $query->where('email', '=', 'Y');
            $query->where('deadline', '=', '2');
            $sjob_data = $query->get()->toArray();
            foreach($sjob_data as $sjob_row){
                $deadline = $this->getDeadline2('2', $sjob_row['deadline_type'], $sjob_row['deadline_day'], $sdate_row['sdate'], $sdate_row['edate']);
                if($deadline == date('Y/m/d')){
                    $query = M09tb::select('userid', 'email');
                    $query->where('userid', '=', $sdate_row['sponsor']);
                    $M09tb_data = $query->get()->toArray();
                    if(!empty($M09tb_data)){
                        $mail_to = $M09tb_data[0]['email'];
                        $mail_to = 'peter19841115@hotmail.com';
                        if($sjob_row['deadline_type'] == '1'){
                            $deadline_day = $sjob_row['deadline_day'].'天';
                        }
                        if($sjob_row['deadline_type'] == '2'){
                            $deadline_day = '上週星期'.config('app.day_of_week.'.$sjob_row['deadline_day']);
                        }
                        if($sjob_row['deadline_type'] == '3'){
                            $deadline_day = '上月'.$sjob_row['deadline_day'].'號';
                        }
                        $mail_row = array(
                            'mail_to' => $mail_to,
                            'title' => $sdate_row['class'].'第'.$sdate_row['term']."期班務流程通知",
                            'content' => $sdate_row['class'].'第'.$sdate_row['term'].'期,班務流程  '.$sjob_row['name'].'  開課前'.$deadline_day.'通知',
                        );
                        $mail_data[] = $mail_row;
                        // dd(date('Y/m/d'));
                    }
                }
                // dd($deadline);
            }
            // dd($sjob_data);
        }

        // dd($sdate_data);

        $query = T04tb::select('t04tb.sponsor', 't04tb.sdate', 't04tb.edate', 't04tb.term', 't01tb.class', 't01tb.name' , 't01tb.class_process');
        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 't04tb.class');
        });
        $query->whereBetween('t04tb.edate', array($edate, $today));
        $query->whereNotNull('t01tb.class_process');
        $query->where('t01tb.class_process', '!=', '0');
        $edate_data = $query->get()->toArray();
        foreach($edate_data as $edate_row){
            $query = Class_process_job::select('name', 'deadline_type', 'deadline_day');
            $query->where('class_process_id', '=', $edate_row['class_process']);
            $query->where('email', '=', 'Y');
            $query->where('deadline', '=', '3');
            $ejob_data = $query->get()->toArray();
            foreach($ejob_data as $ejob_row){
                $deadline = $this->getDeadline2('3', $ejob_row['deadline_type'], $ejob_row['deadline_day'], $edate_row['sdate'], $edate_row['edate']);
                if($deadline == date('Y/m/d')){
                    $query = M09tb::select('userid', 'email');
                    $query->where('userid', '=', $edate_row['sponsor']);
                    $M09tb_data = $query->get()->toArray();
                    if(!empty($M09tb_data)){
                        $mail_to = $M09tb_data[0]['email'];
                        $mail_to = 'peter19841115@hotmail.com';
                        if($ejob_row['deadline_type'] == '4'){
                            $deadline_day = $ejob_row['deadline_day'].'天';
                        }
                        if($ejob_row['deadline_type'] == '5'){
                            $deadline_day = '下週星期'.config('app.day_of_week.'.$ejob_row['deadline_day']);
                        }
                        if($ejob_row['deadline_type'] == '6'){
                            $deadline_day = '下月'.$ejob_row['deadline_day'].'號';
                        }
                        $mail_row = array(
                            'mail_to' => $mail_to,
                            'title' => $edate_row['class'].'第'.$edate_row['term']."期班務流程通知",
                            'content' => $edate_row['class'].'第'.$edate_row['term'].'期,班務流程  '.$ejob_row['name'].'  結訓後'.$deadline_day.'通知',
                        );
                        $mail_data[] = $mail_row;
                    }
                }
                // dd($deadline);
            }
            // dd($ejob_data);
        }
        // dd($mail_data);

        return $mail_data;
    }

    public function getExport($queryData = [])
    {
        $query = T01tb::select('t04tb.site', 't04tb.site_branch', 't04tb.sponsor', 't04tb.sdate', 't04tb.edate', 't04tb.term', 't01tb.class', 't01tb.name' , 't01tb.class_process');

        $query->join('t04tb', function($join)
        {
            $join->on('t04tb.class', '=', 't01tb.class');
        });

        $query->orderBy('class', 'asc');
        $query->orderBy('term', 'asc');

        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {
            $queryData['yerly'] = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT);
            $query->where('t01tb.yerly', $queryData['yerly']);
        }

        if ( isset($queryData['sponsor']) && $queryData['sponsor'] ) {
            $query->where('t04tb.sponsor', '=', $queryData['sponsor']);
        }

        if ( isset($queryData['process_complete']) && $queryData['process_complete'] ) {
            $query->where('t04tb.process_complete', '=', $queryData['process_complete']);
        }

        $query->whereNotNull('t01tb.class_process');
        $query->where("t01tb.class_process", '!=', '0');

        $data = $query->get()->toArray();

        if(!empty($data)){
            $data = $this->getJob($data, $queryData);
        }
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($data);
        // echo "\n</pre>\n";
        // die();
        // dd($data);

        return $data;
    }

}
