<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Services\EntryExportService;
use App\Services\ArrangementService;
use App\Services\User_groupService;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\M13tb;//機關基本資料檔
use File;
use Response;
use View;
use Session;


class EntryExportController extends Controller
{
    public function __construct(EntryExportService $entryexportservice,ArrangementService $arrangementService, User_groupService $user_groupService)
    {
        $this->ees=$entryexportservice;
        $this->as=$arrangementService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('entryexport', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }
    public function index()
    {
        $user=$this->ees->getuser();//辦班人員
       // $user[0]=['PHOEBE200'=>'PHOEBE200'];
        //dd($user);
        $lock_class=Session::get('lock_class');
        $data=$this->ees->lockClassInfo($lock_class);
        // var_dump($data);
        return view('admin/entryexport/list',compact('user','lock_class','data'));
    }

    public function select_class($date)
    {
        //dd($date);
        $temp=explode("_",$date);
        $class_info['final_sdate']=$temp[0];
        $class_info['final_edate']=$temp[1];
        $not_select=[];
        $select=[];
        $select=$this->ees->select_class($class_info);


        $savefield='final_course';
        return view('admin/entryexport/set_column',compact('not_select','select','savefield'));
    }

    public function export(Request $request,$type)
    {
        $date=date('Y-m-d');
        $date=str_replace("-","",$date);
        $temp_year=substr($date,0,4);
        $temp_year=$temp_year-1911;
        $date=$temp_year.substr($date,4,4);
        //產生class.csv
        if($type==1){
            $data[0]=['課程代碼','期別','課程名稱','學位學分','課程類別代碼','課程簡介','允許報名方式','上線開始日期','上課縣市','上課地點','招生人數','報名開始時間','報名結束時間'
                        ,'學習費用','認證時數(學分)','認證計算單位','限制上課對象機關代碼','必選住宿','必選餐點','資格條件','其他說明','學習性質','數位時數'
                        ,'實體時數','講師','科目','開課起始日期','開課起始時間','開課結束日期','開課結束時間'];

            if($request->input('final_course')!=''){
                $class_info_temp=explode(",",$request->input('final_course'));
                for($i=0;$i<count($class_info_temp);$i++){
                    if($class_info_temp[$i]!=''){
                        $temp=explode("_",$class_info_temp[$i]);
                        $class_info[$i]['class']=$temp[0];
                        $class_info[$i]['term']=$temp[1];
                    }
                }
                for($j=0;$j<count($class_info);$j++){
                    $data[1][$j]=$this->ees->get_class_info($class_info[$j]);
                }

            }else{
                $class_info_temp['final_sdate']=$request->input('final_sdate');
                $class_info_temp['final_edate']=$request->input('final_edate');
                $class_info=$this->ees->select_class($class_info_temp);
                for($j=0;$j<count($class_info);$j++){
                    $data[1][$j]=$this->ees->get_class_info($class_info[$j]);
                }
            }

            for($k=0;$k<count($class_info);$k++){
                $update=['file1'=>$date];
                DB::table('t47tb')->where('class',$class_info[$k]['class'])->where('term',$class_info[$k]['term'])->update($update);
            }
            Excel::create('class', function ($excel) use ($data) {//第一參數是檔案名稱
                $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱
                    $sheet->row(1,$data[0]);//插入excel欄位
                    $row=2;//控制列index
                    //插入資料
                    for($b=0;$b<count($data[1]);$b++){
                        $sheet->cell('A'.$row, function($cell) use($data,$b) {
                            $data[1][$b]['class']='P'.$data[1][$b]['class'];
                            $cell->setValue($data[1][$b]['class']);
                        });
                        $sheet->cell('B'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['term']);
                        });
                        $sheet->cell('C'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['name']);
                        });
                        $sheet->cell('D'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['degree']);
                        });
                        $sheet->cell('E'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['category']);
                        });
                        //if($data[1][$b]['type']=='13'){
                            $info=$data[1][$b]['object']."詳細課程資訊，請至本學院網站查閱<a href=http://www.hrd.gov.tw target=_blank>www.hrd.gov.tw</a>";
                        //}
                        $sheet->cell('F'.$row, function($cell) use($data,$b,$info) {
                            $cell->setValue($info);
                        });
                        $sheet->cell('G'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['enroll']);
                        });
                        $sheet->cell('H'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['validdate']);
                        });
                        $sheet->cell('I'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['county']);
                        });
                        $sheet->cell('J'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['site']);
                        });
                        $sheet->cell('K'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['quota']);
                        });
                        $sheet->cell('L'.$row, function($cell) use($data,$b) {
                            $data[1][$b]['sdate']=substr($data[1][$b]['sdate'],0,3).'-'.substr($data[1][$b]['sdate'],3,2).'-'.substr($data[1][$b]['sdate'],5,2);
                            $cell->setValue($data[1][$b]['sdate']);
                        });
                        $sheet->cell('M'.$row, function($cell) use($data,$b) {
                            $data[1][$b]['edate']=substr($data[1][$b]['edate'],0,3).'-'.substr($data[1][$b]['edate'],3,2).'-'.substr($data[1][$b]['edate'],5,2);
                            $cell->setValue($data[1][$b]['edate']);
                        });
                        if($data[1][$b]['type']=='13'){
                            $fee=$data[1][$b]['fee'];
                        }else{
                            $fee=0;
                        }
                        $sheet->cell('N'.$row, function($cell) use($data,$b,$fee) {
                            $cell->setValue($fee);
                        });
                        $sheet->cell('O'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['trainhour']);
                        });
                        $sheet->cell('P'.$row, function($cell) use($data,$b) {
                            $cell->setValue('1');
                        });
                        $sheet->cell('Q'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['restriction']);
                        });
                        $sheet->cell('R'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['lodging']);
                        });
                        $sheet->cell('S'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['meal']);
                        });
                        $sheet->cell('T'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['target']);
                        });
                        if($data[1][$b]['type']=='13'){
                            $un="報名方式說明，請至本學院網站查閱<a href=http://www.hrd.gov.tw target=_blank>www.hrd.gov.tw</a><br>";
                        }else{
                            $un="";
                        }
                        $sheet->cell('U'.$row, function($cell) use($data,$b,$un) {
                            $cell->setValue($un);
                        });
                        $sheet->cell('V'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['classified']);
                        });
                        $sheet->cell('W'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['elearnhr']);
                        });
                        $sheet->cell('X'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['classhr']);
                        });
                        $sheet->cell('Y'.$row, function($cell) use($data,$b) {
                            $cell->setValue("");
                        });
                        $sheet->cell('Z'.$row, function($cell) use($data,$b) {
                            $cell->setValue("");
                        });
                        $sheet->cell('AA'.$row, function($cell) use($data,$b) {
                            $data[1][$b]['sdate']=substr($data[1][$b]['sdate'],0,3).'-'.substr($data[1][$b]['sdate'],3,2).'-'.substr($data[1][$b]['sdate'],5,2);
                            $cell->setValue($data[1][$b]['sdate']);
                        });
                        if($data[1][$b]['trainday']==1.5){
                            $starttime='13:40:00';
                        }else{
                            $starttime='09:10:00';
                        }
                        $sheet->cell('AB'.$row, function($cell) use($data,$b,$starttime) {
                            $cell->setValue($starttime);
                        });
                        $sheet->cell('AC'.$row, function($cell) use($data,$b) {
                            $data[1][$b]['edate']=substr($data[1][$b]['edate'],0,3).'-'.substr($data[1][$b]['edate'],3,2).'-'.substr($data[1][$b]['edate'],5,2);
                            $cell->setValue($data[1][$b]['edate']);
                        });
                        $endtime='16:30:00';
                        $sheet->cell('AD'.$row, function($cell) use($data,$b,$endtime) {
                            $cell->setValue($endtime);
                        });



                        $row++;
                    }
                });
            })->export('csv');


        }

        //產生teacher.csv
        if($type==2){
            $data[0]=['講師代碼','身分證字號','姓名','性別','服務機關名稱','職稱','聯絡電話(一)','聯絡電話(二)','E-Mail','專長代碼1','專長代碼2','專長代碼3','專長代碼4','專長代碼5','可授課程1'
            ,'可授課程2','可授課程3','可授課程4','可授課程5','可授課程6','可授課程7','可授課程8','可授課程9','可授課程10','是否對外公開'];
            $class_info_temp['final_sdate']=$request->input('final_sdate');
            $class_info_temp['final_edate']=$request->input('final_edate');
            if($request->input('final_course')!=''){
                $class_info_temp=explode(",",$request->input('final_course'));
                for($i=0;$i<count($class_info_temp);$i++){
                    if($class_info_temp[$i]!=''){
                        $temp=explode("_",$class_info_temp[$i]);
                        $class_info[$i]['class']=$temp[0];
                        $class_info[$i]['term']=$temp[1];
                    }
                }
                for($j=0;$j<count($class_info);$j++){
                    $data[1][$j]=$this->ees->get_teacher_info($class_info[$j],$class_info_temp['final_sdate'],$class_info_temp['final_edate']);
                }

            }else{
                $class_info=$this->ees->select_class($class_info_temp);
                for($j=0;$j<count($class_info);$j++){
                    $data[1][$j]=$this->ees->get_teacher_info($class_info[$j],$class_info_temp['final_sdate'],$class_info_temp['final_edate']);
                }
            }
            $temp_final=[];
            $c=0;
            for($z=0;$z<count($data[1]);$z++){
                for($h=0;$h<count($data[1][$z]);$h++){
                    $temp_final[$c]=$data[1][$z][$h];
                    $c++;
                }
            }
            for($k=0;$k<count($class_info);$k++){
                $update=['file2'=>$date];
                DB::table('t47tb')->where('class',$class_info[$k]['class'])->where('term',$class_info[$k]['term'])->update($update);
            }
            $data[1]=$temp_final;
            // dd($data[1]);
            Excel::create('teacher', function ($excel) use ($data) {//第一參數是檔案名稱
                $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱

                    $sheet->row(1,$data[0]);//插入excel欄位
                    $row=2;
                     //插入資料
                     for($b=0;$b<count($data[1]);$b++){
                        $sheet->cell('A'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['serno']);
                        });
                        $sheet->cell('B'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['idno']);
                        });
                        $sheet->cell('C'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['cname']);
                        });
                        $sheet->cell('D'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['sex']);
                        });
                        $sheet->cell('E'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['dept']);
                        });

                        $sheet->cell('F'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['position']);
                        });
                        $sheet->cell('G'.$row, function($cell) use($data,$b) {
                            $phone1=$data[1][$b]['offtela1'].$data[1][$b]['offtelb1'];
                            $cell->setValue($phone1);
                        });
                        $sheet->cell('H'.$row, function($cell) use($data,$b) {
                            $phone2=$data[1][$b]['offtela2'].$data[1][$b]['offtelb2'];
                            $cell->setValue($phone2);
                        });
                        $sheet->cell('I'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['email']);
                        });
                        $sheet->cell('J'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['special1']);
                        });
                        $sheet->cell('K'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['special2']);
                        });
                        $sheet->cell('L'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['special3']);
                        });
                        $sheet->cell('M'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['special4']);
                        });
                        $sheet->cell('N'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['special5']);
                        });

                        $sheet->cell('O'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['major1']);
                        });
                        $sheet->cell('P'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['major2']);
                        });
                        $sheet->cell('Q'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['major3']);
                        });
                        $sheet->cell('R'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['major4']);
                        });
                        $sheet->cell('S'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['major5']);
                        });
                        $sheet->cell('T'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['major6']);
                        });
                        $sheet->cell('U'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['major7']);
                        });

                        $sheet->cell('V'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['major8']);
                            //$cell->setValue("");
                        });
                        $sheet->cell('W'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['major9']);
                        });
                        $sheet->cell('X'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['major10']);
                        });
                        $sheet->cell('Y'.$row, function($cell) use($data,$b) {
                            $cell->setValue("0");
                        });

                        $row++;
                    }

                });
            })->export('csv');
        }

        //產生course.csv
        if($type==3){
            $data[0]=['科目代碼','科目名稱','科目說明'];
            $class_info_temp['final_sdate']=$request->input('final_sdate');
            $class_info_temp['final_edate']=$request->input('final_edate');
            $class_info=$this->ees->select_class($class_info_temp);
            $data[1]=$this->ees->get_course_info($class_info_temp['final_sdate'],$class_info_temp['final_edate']);
            for($k=0;$k<count($class_info);$k++){
                $update=['file3'=>$date];
                DB::table('t47tb')->where('class',$class_info[$k]['class'])->where('term',$class_info[$k]['term'])->update($update);
            }
            Excel::create('course', function ($excel) use ($data) {//第一參數是檔案名稱
                $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱
                    $sheet->row(1,$data[0]);//插入excel欄位
                    $row=2;
                    //插入資料
                    for($b=0;$b<count($data[1]);$b++){

                        $sheet->cell('A'.$row, function($cell) use($data,$b) {
                            $num='P'.$data[1][$b]['class'].$data[1][$b]['term'].$data[1][$b]['course'];
                            $cell->setValue($num);
                        });
                        $sheet->cell('B'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['name']);
                        });
                        $sheet->cell('C'.$row, function($cell) use($data,$b) {
                            $cell->setValue("");
                        });


                        $row++;
                    }


                });
            })->export('csv');
        }

        //匯出記錄查詢的列印
        if($type==4){
            $sdate=$request->input('final_sdate');
            $edate=$request->input('final_edate');
            $data[0]=['班別','期別','開始日期','結束日期','課程表是否公告','是否上傳班別','是否上傳成績','班別轉出日期','講座轉出日期','課程轉出日期','班別課程講師轉出日期','成績轉出日期'];
            $data[1]=$this->ees->search($sdate,$edate);

            Excel::create('record', function ($excel) use ($data) {//第一參數是檔案名稱
                $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱
                    $ascii=65;
                    for($a=0;$a<count($data[0]);$a++){
                        $sheet->setWidth(chr($ascii),15);
                        $ascii=$ascii+1;
                    }
                    $sheet->row(1,$data[0]);//插入excel欄位
                    $row=2;
                    //插入資料
                    for($b=0;$b<count($data[1]);$b++){

                        $sheet->cell('A'.$row, function($cell) use($data,$b) {
                            $num=$data[1][$b]['name'].'('.$data[1][$b]['class'].')';
                            $cell->setValue($num);
                        });

                        $sheet->cell('B'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['term']);
                        });
                        $sheet->cell('C'.$row, function($cell) use($data,$b) {
                            $sdate=substr($data[1][$b]['sdate'],0,3).'/'.substr($data[1][$b]['sdate'],3,2).'/'.substr($data[1][$b]['sdate'],5,2);
                            $cell->setValue($sdate);
                        });
                        $sheet->cell('D'.$row, function($cell) use($data,$b) {
                            $edate=substr($data[1][$b]['edate'],0,3).'/'.substr($data[1][$b]['edate'],3,2).'/'.substr($data[1][$b]['edate'],5,2);
                            $cell->setValue($edate);
                        });

                        $sheet->cell('E'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['publish2']=='Y'){
                                $pub='是';
                            }else{
                                $pub='否';
                            }
                            $cell->setValue($pub);
                        });

                        $sheet->cell('F'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['upload1']=='Y'){
                                $upload1='是';
                            }else{
                                $upload1='否';
                            }
                            $cell->setValue($upload1);
                        });

                        $sheet->cell('G'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['upload2']=='Y'){
                                $upload2='是';
                            }else{
                                $upload2='否';
                            }
                            $cell->setValue($upload2);
                        });

                        $sheet->cell('H'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['file1']!=null&&$data[1][$b]['file1']!=''){
                                $file1 = substr($data[1][$b]['file1'],0,3).'/'.substr($data[1][$b]['file1'],3,2).'/'.substr($data[1][$b]['file1'],5,2);;
                            }else{
                                $file1=' ';
                            }
                            $cell->setValue($file1);
                        });

                        $sheet->cell('I'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['file2']!=null&&$data[1][$b]['file2']!=''){
                                $file2 = substr($data[1][$b]['file2'],0,3).'/'.substr($data[1][$b]['file2'],3,2).'/'.substr($data[1][$b]['file2'],5,2);;
                            }else{
                                $file2=' ';
                            }
                            $cell->setValue($file2);
                        });

                        $sheet->cell('J'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['file3']!=null&&$data[1][$b]['file3']!=''){
                                $file3 = substr($data[1][$b]['file3'],0,3).'/'.substr($data[1][$b]['file3'],3,2).'/'.substr($data[1][$b]['file3'],5,2);;
                            }else{
                                $file3=' ';
                            }
                            $cell->setValue($file3);
                        });

                        $sheet->cell('K'.$row, function($cell) use($data,$b) {

                            $cell->setValue(" ");
                        });

                        $sheet->cell('L'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['file5']!=null&&$data[1][$b]['file5']!=''){
                                $file5 = substr($data[1][$b]['file5'],0,3).'/'.substr($data[1][$b]['file5'],3,2).'/'.substr($data[1][$b]['file5'],5,2);;
                            }else{
                                $file5=' ';
                            }
                            $cell->setValue($file5);
                        });

                        $row++;
                    }


                });
            })->export('xls');
        }
        //認證時數資料匯出-列印
        if($type==5){
            $data[0]=[];
            $data[0]=['班別','期別','結束日期','是否上傳','轉出日期'];
            $sdate=$request->input('final_sdate2');
            $edate=$request->input('final_edate2');
            $sponsor=$request->input('sponsor');
            $data[1]=$this->ees->search($sdate,$edate,$sponsor);
            //dd($data[0]);
            Excel::create('print_time', function ($excel) use ($data) {//第一參數是檔案名稱
                $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱
                    $ascii=65;
                    for($a=0;$a<count($data[0]);$a++){
                        $sheet->setWidth(chr($ascii),15);
                        $ascii=$ascii+1;
                    }
                    $sheet->row(1,$data[0]);//插入excel欄位
                    $row=2;
                    //插入資料
                    for($b=0;$b<count($data[1]);$b++){

                        $sheet->cell('A'.$row, function($cell) use($data,$b) {
                            $num=$data[1][$b]['name'].'('.$data[1][$b]['class'].')';
                            $cell->setValue($num);
                        });

                        $sheet->cell('B'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['term']);
                        });

                        $sheet->cell('C'.$row, function($cell) use($data,$b) {
                            $edate=substr($data[1][$b]['edate'],0,3).'/'.substr($data[1][$b]['edate'],3,2).'/'.substr($data[1][$b]['edate'],5,2);
                            $cell->setValue($edate);
                        });

                        $sheet->cell('D'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['upload1']=='Y'){
                                $upload1='是';
                            }else{
                                $upload1='否';
                            }
                            $cell->setValue($upload1);
                        });

                        $sheet->cell('E'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['file5']!=null&&$data[1][$b]['file5']!=''){
                                $file5 = substr($data[1][$b]['file5'],0,3).'/'.substr($data[1][$b]['file5'],3,2).'/'.substr($data[1][$b]['file5'],5,2);;
                            }else{
                                $file5=' ';
                            }
                            $cell->setValue($file5);
                        });

                        $row++;
                    }
                });
            })->export('xls');
        }

        //認證實數資料匯出-匯出實數資料(regist1.csv)
        if($type==6){
            $sdate=$request->input('final_sdate2');
            $edate=$request->input('final_edate2');
            $class=$request->input('class2');
            $term=$request->input('term2');
            $data[0]=['課程代碼','期別','身分證字號','姓名','通過','訓練成績','訓練總數','證件字號','出勤上課狀況','生日','學習性質','數位時數','實體時數','實際上課起始日期',
                      '實際上課起始時間','實際上課結束日期','實際上課結束時間'];
            $control='regist1';
            //$term='01';
            $data[1]=$this->ees->get_regist_sql($class,$term,$sdate,$edate,$control);
            if(empty($data[1])){
                return redirect('admin/entryexport')->with("message","沒資料!");
            }

            $score=$request->input("score");
            $sickkk=$request->input("sickkk");
            $data[3]=[$score,$sickkk];

            $update=['file5'=>$date];
            DB::table('t47tb')->where('class',$class)->where('term',$term)->update($update);

            Excel::create('regist1', function ($excel) use ($data) {//第一參數是檔案名稱
                $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱

                    $sheet->row(1,$data[0]);//插入excel欄位
                    $row=2;
                    //插入資料
                    for($b=0;$b<count($data[1]);$b++){

                        $sheet->cell('A'.$row, function($cell) use($data,$b) {
                            $num='P'.$data[1][$b]['class'];
                            $cell->setValue($num);
                        });

                        $sheet->cell('B'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['term']);
                        });

                        $sheet->cell('C'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['idno']);
                        });

                        $sheet->cell('D'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['cname']);
                        });

                        $sheet->cell('E'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['elearning']=='Y' && $data[1][$b]['status']==1){
                                $pass=1;
                            }else{
                                $pass=0;
                            }
                            $cell->setValue($pass);
                        });

                        $sheet->cell('F'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['grade']=='Y' && data[3][1]!=null){
                                $grade=$data[1][$b]['totsrc'];
                            }else{
                                $grade='';
                            }
                            $cell->setValue($grade);
                        });

                        $sheet->cell('G'.$row, function($cell) use($data,$b) {
                            $learn=0;
                            if($data[1][$b]['classified']!=2&&$data[1][$b]['status']==1&&$data[1][$b]['elearning']=='Y'){
                                $learn=$data[1][$b]['elearnhr'];
                            }
                            $time=0;
                            if($data[1][$b]['hour']==null){
                                $data[1][$b]['hour']=0;
                            }
                            $time=$data[1][$b]['classhr']-$data[1][$b]['hour'];

                            if($data[1][$b]['classified']==1){
                                $total_time= $learn;
                            }else if($data[1][$b]['classified']==1){
                                $total_time= $time;
                            }else{
                                $total_time=$learn+$time;
                            }

                            $cell->setValue($total_time);
                        });

                        $sheet->cell('H'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['diploma']==2 && $data[1][$b]['status']==1 && $data[1][$b]['docno']!=null && $data[1][$b]['docno']!=''){
                                $diploma='局人發研字第'.$data[1][$b]['docno'].'號';
                            }else if($data[1][$b]['status']==1 && $data[1][$b]['docno']!=null && $data[1][$b]['docno']!=''){
                                $diploma='人教習字第'.$data[1][$b]['docno'].'號';
                            }else{
                                $diploma='';
                            }
                            $cell->setValue($diploma);
                        });

                        $sheet->cell('I'.$row, function($cell) use($data,$b) {
                            switch($data[1][$b]['type']){
                                case 1:
                                    $status=$data[1][$b]['leave_sdate'].'~'.$data[1][$b]['leave_edate'].' '.' '.$data[1][$b]['leave_stime'].'~'.$data[1][$b]['leave_etime'].' '.' '.'事假'.' '.'共'.$data[1][$b]['leave_total_hour'].'小時';
                                    break;
                                case 2:
                                    $status=$data[1][$b]['leave_sdate'].'~'.$data[1][$b]['leave_edate'].' '.' '.$data[1][$b]['leave_stime'].'~'.$data[1][$b]['leave_etime'].' '.' '.'病假'.' '.'共'.$data[1][$b]['leave_total_hour'].'小時';

                                    break;
                                case 3:
                                    $status=$data[1][$b]['leave_sdate'].'~'.$data[1][$b]['leave_edate'].' '.' '.$data[1][$b]['leave_stime'].'~'.$data[1][$b]['leave_etime'].' '.' '.'喪假'.' '.'共'.$data[1][$b]['leave_total_hour'].'小時';
                                    break;
                                case 4:
                                    $status=$data[1][$b]['leave_sdate'].'~'.$data[1][$b]['leave_edate'].' '.' '.$data[1][$b]['leave_stime'].'~'.$data[1][$b]['leave_etime'].' '.' '.'請假'.' '.'共'.$data[1][$b]['leave_total_hour'].'小時';
                                    break;
                                case 5:
                                    $status=$data[1][$b]['leave_sdate'].'~'.$data[1][$b]['leave_edate'].' '.' '.$data[1][$b]['leave_stime'].'~'.$data[1][$b]['leave_etime'].' '.' '.'缺課'.' '.'共'.$data[1][$b]['leave_total_hour'].'小時';
                                    break;
                                default:
                                    $status="全勤";
                                    break;
                            }

                            if($data[3][0]==null){
                                $status='';
                            }
                            $cell->setValue($status);
                        });

                        $sheet->cell('J'.$row, function($cell) use($data,$b) {
                            $cell->setValue("");
                        });

                        $sheet->cell('K'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['classified']);
                        });

                        $sheet->cell('L'.$row, function($cell) use($data,$b) {
                            $learn=0;
                            if($data[1][$b]['classified']!=2&&$data[1][$b]['status']==1&&$data[1][$b]['elearning']=='Y'){
                                $learn=$data[1][$b]['elearnhr'];
                            }
                            $cell->setValue($learn);
                        });

                        $sheet->cell('M'.$row, function($cell) use($data,$b) {
                            $time=0;
                            if($data[1][$b]['classified']!=1&&$data[1][$b]['status']==1&&$data[1][$b]['elearning']=='Y'){
                                if($data[1][$b]['hour']==null){
                                    $data[1][$b]['hour']=0;
                                }
                                $time=$data[1][$b]['classhr']-$data[1][$b]['hour'];
                            }
                            $cell->setValue($time);
                        });

                        $sheet->cell('N'.$row, function($cell) use($data,$b) {
                            $sdate=substr($data[1][$b]['sdate'],0,3).'-'.substr($data[1][$b]['sdate'],3,2).'-'.substr($data[1][$b]['sdate'],5,2);
                            $cell->setValue($sdate);
                        });


                        $sheet->cell('O'.$row, function($cell) use($data,$b) {
                            $stime='09:10:00';
                            if($data[1][$b]['trainday']==1.5){
                                $stime='13:40:00';
                            }
                            $cell->setValue($stime);
                        });

                        $sheet->cell('P'.$row, function($cell) use($data,$b) {
                            $edate=substr($data[1][$b]['edate'],0,3).'-'.substr($data[1][$b]['edate'],3,2).'-'.substr($data[1][$b]['edate'],5,2);
                            $cell->setValue($edate);
                        });

                        $sheet->cell('Q'.$row, function($cell) use($data,$b) {
                            $edate=substr($data[1][$b]['edate'],0,3).'/'.substr($data[1][$b]['edate'],3,2).'/'.substr($data[1][$b]['edate'],5,2);
                            $cell->setValue("16:30:00");
                        });

                        $row++;
                    }
                });
            })->export('csv');
        }

        //認證實數資料匯出-匯出實數資料(regist2.csv)
        if($type==7){
            $sdate=$request->input('final_sdate2');
            $edate=$request->input('final_edate2');
            $class=$request->input('class2');
            $term=$request->input('term2');
            dd($request->all());
            $data=[];
            $data[0]=['身分證字號','名稱','開課起始日期','開課起始時間','開課結束日期','開課結束時間','姓名','學位學分','課程類別代碼','上課縣市','期別',
                      '訓練總數','訓練總數單位','訓練成績','證件字號','出勤上課狀況','生日','學習性質','數位時數','實體時數','課程代碼','實際上課起始日期',
                      '實際上課起始時間','實際上課結束日期','實際上課結束時間'];
            $control='regist2';
            $data[1] = $this->ees->get_regist_sql($class,$term,$sdate,$edate,$control);
            if(empty($data[1])){
                return redirect('admin/entryexport')->with("message","沒資料!");
            }
            $score=$request->input("score");
            $sickkk=$request->input("sickkk");
            $data[3]=[$score,$sickkk];
            $update=['file5'=>$date];
            DB::table('t47tb')->where('class',$class)->where('term',$term)->update($update);

            Excel::create('regist2', function ($excel) use ($data) {//第一參數是檔案名稱
                $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱

                    $sheet->row(1,$data[0]);//插入excel欄位
                    $row=2;
                    //插入資料
                    for($b=0;$b<count($data[1]);$b++){
                    if($data[1][$b]['course_hour']!=0){
                        $sheet->cell('A'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['idno']);
                        });

                        $sheet->cell('B'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['name']);
                        });

                        $sheet->cell('C'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['date']);
                        });

                        $sheet->cell('D'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['stime']);
                        });

                        $sheet->cell('E'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['date']);
                        });

                        $sheet->cell('F'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['etime']);
                        });

                        $sheet->cell('G'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['cname']);
                        });

                        $sheet->cell('H'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['degree']);
                        });

                        $sheet->cell('I'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['category']);
                        });

                        $sheet->cell('J'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['county']);
                        });

                        $sheet->cell('K'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['term']);
                        });

                        $sheet->cell('L'.$row, function($cell) use($data,$b) {
                            $learn=0;
                            if($data[1][$b]['classified']!=2&&$data[1][$b]['status']==1&&$data[1][$b]['elearning']=='Y'){
                                $learn=$data[1][$b]['elearnhr'];
                            }
                            $time=0;
                            if($data[1][$b]['hour']==null){
                                $data[1][$b]['hour']=0;
                            }
                            $time=$data[1][$b]['classhr']-$data[1][$b]['hour'];

                            if($data[1][$b]['classified']==1){
                                $total_time= $learn;
                            }else if($data[1][$b]['classified']==1){
                                $total_time= $time;
                            }else{
                                $total_time=$learn+$time;
                            }

                            $cell->setValue($total_time);
                        });

                        $sheet->cell('M'.$row, function($cell) use($data,$b) {
                            $cell->setValue("1");
                        });

                        $sheet->cell('N'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['grade']=='Y' && data[3][1]!=null){
                                $grade=$data[1][$b]['totsrc'];
                            }else{
                                $grade='';
                            }
                            $cell->setValue($grade);
                        });


                        $sheet->cell('O'.$row, function($cell) use($data,$b) {
                            if($data[1][$b]['diploma']==2 && $data[1][$b]['status']==1 && $data[1][$b]['docno']!=null && $data[1][$b]['docno']!=''){
                                $diploma='局人發研字第'.$data[1][$b]['docno'].'號';
                            }else if($data[1][$b]['status']==1 && $data[1][$b]['docno']!=null && $data[1][$b]['docno']!=''){
                                $diploma='人教習字第'.$data[1][$b]['docno'].'號';
                            }else{
                                $diploma='';
                            }
                            $cell->setValue($diploma);
                        });

                        $sheet->cell('P'.$row, function($cell) use($data,$b) {
                            switch($data[1][$b]['type']){
                                case 1:
                                    $status=$data[1][$b]['leave_sdate'].'~'.$data[1][$b]['leave_edate'].' '.' '.$data[1][$b]['leave_stime'].'~'.$data[1][$b]['leave_etime'].' '.' '.'事假'.' '.'共'.$data[1][$b]['leave_total_hour'].'小時';
                                    break;
                                case 2:
                                    $status=$data[1][$b]['leave_sdate'].'~'.$data[1][$b]['leave_edate'].' '.' '.$data[1][$b]['leave_stime'].'~'.$data[1][$b]['leave_etime'].' '.' '.'病假'.' '.'共'.$data[1][$b]['leave_total_hour'].'小時';

                                    break;
                                case 3:
                                    $status=$data[1][$b]['leave_sdate'].'~'.$data[1][$b]['leave_edate'].' '.' '.$data[1][$b]['leave_stime'].'~'.$data[1][$b]['leave_etime'].' '.' '.'喪假'.' '.'共'.$data[1][$b]['leave_total_hour'].'小時';
                                    break;
                                case 4:
                                    $status=$data[1][$b]['leave_sdate'].'~'.$data[1][$b]['leave_edate'].' '.' '.$data[1][$b]['leave_stime'].'~'.$data[1][$b]['leave_etime'].' '.' '.'請假'.' '.'共'.$data[1][$b]['leave_total_hour'].'小時';
                                    break;
                                case 5:
                                    $status=$data[1][$b]['leave_sdate'].'~'.$data[1][$b]['leave_edate'].' '.' '.$data[1][$b]['leave_stime'].'~'.$data[1][$b]['leave_etime'].' '.' '.'缺課'.' '.'共'.$data[1][$b]['leave_total_hour'].'小時';
                                    break;
                                default:
                                    $status="全勤";
                                    break;
                            }

                            if($data[3][0]==null){
                                $status='';
                            }
                            $cell->setValue($status);
                            $cell->setValue($status);
                        });

                        $sheet->cell('Q'.$row, function($cell) use($data,$b) {
                            $cell->setValue("");
                        });

                        $sheet->cell('R'.$row, function($cell) use($data,$b) {
                            $cell->setValue($data[1][$b]['classified']);
                        });

                        $sheet->cell('S'.$row, function($cell) use($data,$b) {
                            $learn=0;
                            if($data[1][$b]['classified']!=2&&$data[1][$b]['status']==1&&$data[1][$b]['elearning']=='Y'){
                                $learn=$data[1][$b]['elearnhr'];
                            }
                            $cell->setValue($learn);
                        });

                        $sheet->cell('T'.$row, function($cell) use($data,$b) {
                            $time=0;
                            if($data[1][$b]['classified']!=1&&$data[1][$b]['status']==1&&$data[1][$b]['elearning']=='Y'){
                                if($data[1][$b]['hour']==null){
                                    $data[1][$b]['hour']=0;
                                }
                                $time=$data[1][$b]['classhr']-$data[1][$b]['hour'];
                            }
                            $cell->setValue($time);
                        });

                        $sheet->cell('U'.$row, function($cell) use($data,$b) {
                            $class='P'.$data[1][$b]['class'].$data[1][$b]['course'];
                            $cell->setValue($class);
                        });

                        $sheet->cell('V'.$row, function($cell) use($data,$b) {
                            $sdate=substr($data[1][$b]['sdate'],0,3).'-'.substr($data[1][$b]['sdate'],3,2).'-'.substr($data[1][$b]['sdate'],5,2);
                            $cell->setValue($sdate);
                        });


                        $sheet->cell('W'.$row, function($cell) use($data,$b) {
                            $stime='09:10:00';
                            if($data[1][$b]['trainday']==1.5){
                                $stime='13:40:00';
                            }
                            $cell->setValue($stime);
                        });

                        $sheet->cell('X'.$row, function($cell) use($data,$b) {
                            $edate=substr($data[1][$b]['edate'],0,3).'-'.substr($data[1][$b]['edate'],3,2).'-'.substr($data[1][$b]['edate'],5,2);
                            $cell->setValue($edate);
                        });

                        $sheet->cell('Y'.$row, function($cell) use($data,$b) {
                            $edate=substr($data[1][$b]['edate'],0,3).'/'.substr($data[1][$b]['edate'],3,2).'/'.substr($data[1][$b]['edate'],5,2);
                            $cell->setValue("16:30:00");
                        });

                        $row++;
                    }
                    }
                });
            })->export('csv');

        }

    }

    public function search(Request $request,$type)
    {
        if($type==1){
            $sdate=$request->input('final_sdate');
            $edate=$request->input('final_edate');
            $sponsor=$request->input("sponsor");
            $data=$this->ees->search($sdate,$edate,$sponsor);
            //$data=['sponsor'=>$sponsor];
            return $data;
        }
        if($type==2){
            $sdate=$request->input('final_sdate');
            $edate=$request->input('final_edate');
            $data=$this->ees->search($sdate,$edate);
            return $data;
        }
        //dd($request->input());
    }

}

