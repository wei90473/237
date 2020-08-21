<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Services\DataExportService;
use App\Services\User_groupService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Models\M13tb;//機關基本資料檔
use File;
use Response;
use ZipArchive;


class DataExportController extends Controller
{
    public function __construct(DataExportService $dataexportservice, User_groupService $user_groupService)
    {
        $this->des=$dataexportservice;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('dataexport', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }
    public function index(Request $request,$type=null)
    {
        return view('admin/dataexport/list',compact('data'));
    }

    //無傳真電話
    public function fax(Request $request,$type)
    {
        $class_info=explode(",",$request->input('class_info'));//課程資訊
        for($i=0;$i<count($class_info);$i++){
            $class_info_condition_temp=explode("_",$class_info[$i]);
            if(isset($class_info_condition_temp[0])&&$class_info_condition_temp[0]!=''){
               $class_info_condition[$i]['class']=$class_info_condition_temp[0];
            }
            if(isset($class_info_condition_temp[1])){
                $class_info_condition[$i]['name']=$class_info_condition_temp[1];
             }
            if(isset($class_info_condition_temp[2])){
                $class_info_condition[$i]['term']=$class_info_condition_temp[2];
             }
        }
        if($request->input("datatype")=='student'){
            $where=[];
            if($request->input("master")=='on'){
                $temp1=explode(",",$request->input("master_info"));
                for($g=0;$g<count($temp1);$g++){
                    $master_temp=explode("_",$temp1[$g]);
                    if($master_temp[0]!=''){
                       $where['master_info'][$g]=$master_temp[0];
                    }
                }
                //var_dump($master_temp);
            }
            if($request->input("gov")=='on'){
                $temp2=explode(",",$request->input("gov_info"));

                for($h=0;$h<count($temp2);$h++){
                    $gov_temp=explode("_",$temp2[$h]);
                    if($gov_temp[0]!=''){
                       $where['gov_info'][$h]=$gov_temp[0];
                    }
                }
            }
            if($request->input("dep")=='on'){
                $temp3=explode(",",$request->input("dep_info"));
                for($k=0;$k<count($temp3);$k++){
                    $dep_temp=explode("_",$temp3[$k]);
                    if($dep_temp[0]!=''){
                       $where['dep_info'][$k]=$dep_temp[0];
                    }
                }
            }
            if($request->input("sex")){
                $where['sex']=$request->input("sex");
            }
            $db_select='offfaxa,offfaxb,t01tb.class,t13tb.term,cname,t13tb.dept,offtela1,offtelb1,offtelc1,t01tb.name,(RTRIM(offaddr1)+offaddr2) as offaddress';
            $data[1]=$this->des->getFaxStudent($class_info_condition,$db_select,$where);

            for($e=0;$e<count($data[1]);$e++){
                if($data[1][$e]['offfaxa']!=''&&$data[1][$e]['offfaxb']!=''){
                    $data[2][$e]=$data[1][$e];//有傳真號碼
                }else{
                    $data[3][$e]=$data[1][$e];//無傳真號碼
                }
            }

            if(!empty($data[3])){
                $data[3]=array_values($data[3]);

                $content='';
                for($f=0;$f<count($data[3]);$f++){
                    $content.=$data[3][$f]['class']."　"."　".$data[3][$f]['name']."　"."　"."　"."　"."　"."　"."　"."　"."　"."　".$data[3][$f]['term']." "." "." "." "." ".$data[3][$f]['cname']."　"."　"."　"."　"."　"."　".$data[3][$f]['offtela1'].$data[3][$f]['offtelb1']."\r\n";
                }
                $filename="無傳真清單檔案.txt";
                $headers = [
                'Content-type' => 'text/plain',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                ];

                return Response::make($content, 200, $headers);
            }

        }

        if($request->input("datatype")=='teacher'){
            $db_select='offfaxa,offfaxb,t09tb.class,t09tb.term,cname,dept,offtela1,offtelb1,offtelc1,cname,offaddress,t01tb.name';
            $data[1]=$this->des->getFax($class_info_condition,$db_select);
            for($e=0;$e<count($data[1]);$e++){
                if($data[1][$e]['offfaxa']!=''&&$data[1][$e]['offfaxb']!=''){
                    $data[2][$e]=$data[1][$e];//有傳真號碼
                }else{
                    $data[3][$e]=$data[1][$e];//無傳真號碼
                }
            }

            if(!empty($data[3])){
                $data[3]=array_values($data[3]);

                $content='';
                for($f=0;$f<count($data[3]);$f++){
                    $content.=$data[3][$f]['class']."　"."　".$data[3][$f]['name']."　"."　"."　"."　"."　"."　"."　"."　"."　"."　".$data[3][$f]['term']." "." "." "." "." ".$data[3][$f]['cname']."　"."　"."　"."　"."　"."　".$data[3][$f]['offtela1'].$data[3][$f]['offtelb1']."\r\n";
                }
                $filename="無傳真清單檔案.txt";
                $headers = [
                'Content-type' => 'text/plain',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                ];

                return Response::make($content, 200, $headers);
            }
        }

    }

    //新增班期
    public function select_class(Request $request)
    {

        $class_info=[];
        $savefield='class_info';
        $class_info['class']=$request->input('class');
        $class_info['class_name']=$request->input('class_name');
        $data=$this->des->select_class($class_info);
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 10;

        return view('admin/dataexport/select_class',compact('data','savefield','queryData'));
    }
    //設定欄位、學員其他條件
    public function set_column($type,$cond=null)
    {
        $course[0] = new \stdClass();

        switch($type)
        {
            case 'master_select':
                $not_select=$this->des->master_select();
                $savefield='master_info';
                break;
            case 'teacher':
                $not_select=['m01tb.idno'=>'身分證號','cname'=>'中文姓名','ename'=>'英文姓名','sex'=>'性別','birth'=>'出生日期','dept'=>'服務機關','position'=>'現職',
                         'offaddress'=>'機關地址','offzip'=>'地郵遞區號(公)','homaddress'=>'住家地址'
                            ,'homzip'=>'郵遞區號(宅)','regaddress'=>'戶籍地址','regzip'=>'郵遞區號(戶)','offtel1'=>'電話(公一)',
                            'offtel2'=>'電話(公二)','homtel'=>'電話(宅)','mobiltel'=>'行動電話','offfax'=>'傳真(公)','homfax'=>'傳真(宅)','email'=>'E-mail'];
                $savefield='output_info';
                break;
            case 'student':
                $not_select=['no'=>'學號','m02tb.idno'=>'身分證號','cname'=>'中文姓名','ename'=>'英文姓名','sex'=>'性別','birth'=>'出生日期',
                         'm13tb.lname'=>'主管機關','m02tb.dept'=>'服務機關','t13tb.rank'=>'官職','m02tb.position'=>'職稱','m02tb.education'=>'學歷',
                         'offaddr'=>'機關地址','offzip'=>'郵遞區號(公)','homaddr'=>'住家地址','homzip'=>'郵遞區號(宅)'
                            ,'offtel1'=>'電話(公一)','offtel2'=>'電話(公二)','offfax'=>'傳真(公)','m02tb.email'=>'E-mail','homtel'=>'電話(宅)',
                            'mobiltel'=>'行動電話','handicap'=>'身心障礙','m02tb.offemail'=>'人事單位Email','m13tb.email as m13tbEmail'=>'機關電子信箱','t13tb.age'=>'年齡'];
                $savefield='output_info';
                break;
            case 'dep_select':
                $not_select=['臺北市'=>'臺北市' ,'基隆市'=>'基隆市','新北市'=>'新北市','宜蘭縣'=>'宜蘭縣','新竹市'=>'新竹市' ,'新竹縣'=>'新竹縣',
                             '桃園市'=>'桃園市', '苗栗縣'=>'苗栗縣','臺中市'=>'臺中市','彰化縣'=>'彰化縣','南投縣'=>'南投縣','嘉義市'=>'嘉義市',
                             '嘉義縣'=>'嘉義縣','雲林縣'=>'雲林縣','臺南市'=>'臺南市','高雄市'=>'高雄市','澎湖縣'=>'澎湖縣','屏東縣'=>'屏東縣',
                             '臺東縣'=>'臺東縣','花蓮縣'=>'花蓮縣','金門縣'=>'金門縣','連江縣'=>'連江縣'];
                $savefield='dep_info';
                break;

            case 'gov_select':
                $not_select=['01'=>'委任第一職等','02'=>'委任第二職等','03'=>'委任第三職等','04'=>'委任第四職等','05'=>'委任第五職等','06'=>'薦任第六職等','07'=>'薦任第七職等',
                             '08'=>'薦任第八職等','09'=>'薦任第九職等','10'=>'簡任第十職等','11'=>'簡任第十一職等','12'=>'簡任第十二職等','13'=>'簡任第十三職等','14'=>'簡任第十四職等',
                             '15'=>'特任','16'=>'依法任派用人員','17'=>'聘僱人員','18'=>'約聘人員','19'=>'軍職人員','20'=>'其他'];
                $savefield='gov_info';
                break;
            default:
                break;
        }
        //dd($not_select);
        //var_dump($type);

        $temp=explode(",",$cond);
        for($i=0;$i<count($temp);$i++){
            $temp2[$i]=explode("_",$temp[$i]);
        }
        $radio='';
        $radio=$temp2[count($temp2)-1][0];
        //var_dump($temp2[count($temp2)-1][0]);
        unset($temp2[count($temp2)-1]);
        $select=[];
        for($j=0;$j<count($temp2);$j++){
            $select[$temp2[$j][0]]=$temp2[$j][1];
        }
        $not_select=array_diff($not_select,$select);
        //var_dump($select);
        //var_dump($not_select);
        //var_dump($not_select);
        //$select=[];
        return view('admin/dataexport/set_column',compact('not_select','select','savefield','radio'));
    }

    public function export(Request $request)
    {
        $data = [];
        $output_info = explode(",",$request->input("output_info"));//接收需要篩選的欄位
        $select_column = [];//SQL select欄位
        $excel_column = [];//Excel 欄位
        $excel_or_txt = $output_info[count($output_info)-1];//輸出Excel或是txt
        unset($output_info[count($output_info)-1]);

        for($j=0;$j<count($output_info);$j++){
            $temp=explode("_",$output_info[$j]);
            if(isset($temp[0])){
                array_push($select_column,$temp[0]);//處理要select的欄位
            }
            if(isset($temp[1])){
                array_push($excel_column,$temp[1]);//excel的欄位
            }
        }

        $select_column = array_values($select_column);

        $db_select = implode(",",$select_column);
        
        $db_select = str_replace('offfax', 'CONCAT(offfaxa, \'-\', offfaxb)', $db_select);
        $db_select = str_replace('offtel1', 'CONCAT(offtela1, \'-\', offtelb1, \'#\', offtelc1)', $db_select);
        $db_select = str_replace('offtel2', 'CONCAT(offtela2, \'-\', offtelb2, \'#\', offtelc2)', $db_select);
        
        $db_select = str_replace('homtel', 'CONCAT(homtela, \'-\', homtelb)', $db_select);
        $db_select = str_replace('homfax', 'CONCAT(homfaxa, \'-\', homfaxb)', $db_select);

        if($request->input("datatype")=='student'){
            $db_select = str_replace('homaddr', 'CONCAT(homaddr1, homaddr2)', $db_select);
            $db_select = str_replace('offaddr', 'CONCAT(offaddr1, offaddr2)', $db_select);
        }

        // dd($db_select);

        // dd($db_select);

        $class_info = explode(",",$request->input('class_info'));//課程資訊

        for($i=0;$i<count($class_info);$i++){
            $class_info_condition_temp=explode("_",$class_info[$i]);
            if(isset($class_info_condition_temp[0])&&$class_info_condition_temp[0]!=''){
                $class_info_condition[$i]['class']=$class_info_condition_temp[0];
            }
            if(isset($class_info_condition_temp[1])){
                $class_info_condition[$i]['name']=$class_info_condition_temp[1];
            }
            if(isset($class_info_condition_temp[2])){
                $class_info_condition[$i]['term']=$class_info_condition_temp[2];
            }
        }

        $class_info_condition = array_values($class_info_condition);
        //輸出基本資料
        if($request->input("exporttype") == 'basic_info'){

            $data[0]=$excel_column;
            if($request->input("datatype") == 'teacher'){

                $data[1]=$this->des->getClassTeacher($class_info_condition,$db_select);//課程資料
                $filename="Teacher";
            }

            if($request->input("datatype")=='student'){

                $where=[];
                if($request->input("master")=='on'){
                    $temp1=explode(",",$request->input("master_info"));
                    for($g=0;$g<count($temp1);$g++){
                        $master_temp=explode("_",$temp1[$g]);
                        if($master_temp[0]!=''){
                           $where['master_info'][$g]=$master_temp[0];
                        }
                    }
                }

                if($request->input("gov")=='on'){
                    $temp2=explode(",",$request->input("gov_info"));

                    for($h=0;$h<count($temp2);$h++){
                        $gov_temp=explode("_",$temp2[$h]);
                        if($gov_temp[0]!=''){
                           $where['gov_info'][$h]=$gov_temp[0];
                        }
                    }
                }

                if($request->input("dep")=='on'){
                    $temp3=explode(",",$request->input("dep_info"));
                    for($k=0;$k<count($temp3);$k++){
                        $dep_temp=explode("_",$temp3[$k]);
                        if($dep_temp[0]!=''){
                           $where['dep_info'][$k]=$dep_temp[0];
                        }
                    }
                }
                if($request->input("sex")){
                    $where['sex']=$request->input("sex");
                }

                $data[1]=$this->des->getClassStudent($class_info_condition,$db_select,$where);//課程資料
                $filename="Student";
            }

            $data[1] = $this->dataFormat($data[1]);
            // dd($data[1]);
            if (empty($data[1])) return back()->with('result', 0)->with('message', '無資料');
            //產生excel
            if($excel_or_txt=='excel'){
                // dd($data[1], $data[0]);
                Excel::create($filename, function ($excel) use ($data) {//第一參數是檔案名稱
                    $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱
                        $ascii=65;
                        for($a=65;$a<count($data[0])+65;$a++){
                            $sheet->setWidth(chr($ascii),15);
                            $ascii=$ascii+1;
                        }
                        $sheet->row(1,$data[0]);//插入excel欄位
                        $row=2;//控制列index
                        //插入資料
                        for($b=0;$b<count($data[1]);$b++){
                            if(isset($data[1][$b]['birth'])){
                                if($data[1][$b]['birth']!=''){
                                    $data[1][$b]['birth']=substr($data[1][$b]['birth'],0,3).'-'.substr($data[1][$b]['birth'],3,2).'-'.substr($data[1][$b]['birth'],5,2);
                                }
                            }

                            $sheet->row($row,$data[1][$b]);
                            $row++;
                        }
                    });
                })->export('xls');
            }
            //產生txt
            if($excel_or_txt=='txt'){
                $content='';
                for($c=0;$c<count($data[0]);$c++){
                    $content.=$data[0][$c]." "." "." "." "." "." "." "." ";
                }
                $content.="\r\n";
                for($c=0;$c<count($data[0]);$c++){
                    $content.="================";
                }
                $content.="\r\n";
                for($d=0;$d<count($data[1]);$d++){
                    foreach($data[1][$d] as $temp){
                        $content.=$temp." "." "." "." "." "." "." "." ";
                    }
                    $content.="\r\n";
                }


                $filename.=".txt";
                $headers = [
                'Content-type' => 'text/plain',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                ];

                return Response::make($content, 200, $headers);

            }


        }

        //輸出地址條
        if($request->input("exporttype")=='address'){
            if($request->input("datatype")=='teacher'){
                $data[0] = ['郵遞區號(公)', '服務機關', '機關地址', '姓名', '職稱'];
                $db_select = 'offzip,m01tb.dept,offaddress,cname,position';
                $data[1] = $this->des->getAddress($class_info_condition,$db_select);
                $filename = "講座地址條";
            }

            if($request->input("datatype")=='student'){
                $data[0]=['學號','身分證號','中文姓名','英文姓名','性別','出生日期','主管機關'];
                $where=[];
                if($request->input("master")=='on'){
                    $temp1=explode(",",$request->input("master_info"));
                    for($g=0;$g<count($temp1);$g++){
                        $master_temp=explode("_",$temp1[$g]);
                        if($master_temp[0]!=''){
                           $where['master_info'][$g]=$master_temp[0];
                        }
                    }
                }
                if($request->input("gov")=='on'){
                    $temp2=explode(",",$request->input("gov_info"));

                    for($h=0;$h<count($temp2);$h++){
                        $gov_temp=explode("_",$temp2[$h]);
                        if($gov_temp[0]!=''){
                           $where['gov_info'][$h]=$gov_temp[0];
                        }
                    }
                }
                if($request->input("dep")=='on'){
                    $temp3=explode(",",$request->input("dep_info"));
                    for($k=0;$k<count($temp3);$k++){
                        $dep_temp=explode("_",$temp3[$k]);
                        if($dep_temp[0]!=''){
                           $where['dep_info'][$k]=$dep_temp[0];
                        }
                    }
                }
                if($request->input("sex")){
                    $where['sex']=$request->input("sex");
                }
                $db_select='no,m02tb.idno,m02tb.cname,m02tb.ename,m02tb.sex,m02tb.birth,m02tb.dept';
                $data[1]=$this->des->getAddressStudent($class_info_condition,$db_select,$where);
                $filename="學生地址條";
            }

            Excel::create($filename, function ($excel) use ($data) {//第一參數是檔案名稱
                $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱
                    $ascii=65;
                    for($a=0;$a<count($data[0]);$a++){
                        $sheet->setWidth(chr($ascii),15);
                        $ascii=$ascii+1;
                    }
                    $sheet->row(1,$data[0]);//插入excel欄位
                    $row=2;//控制列index
                    //插入資料
                    for($b=0;$b<count($data[1]);$b++){
                        $sheet->row($row,$data[1][$b]);
                        $row++;
                    }
                });
            })->export('xls');
        }

        //輸出傳真通知
        if($request->input("exporttype")=='fax'){

            if($request->input("datatype")=='student'){
                $where=[];
                if($request->input("master")=='on'){
                    $temp1=explode(",",$request->input("master_info"));
                    for($g=0;$g<count($temp1);$g++){
                        $master_temp=explode("_",$temp1[$g]);
                        if($master_temp[0]!=''){
                           $where['master_info'][$g]=$master_temp[0];
                        }
                    }
                    //var_dump($master_temp);
                }
                if($request->input("gov")=='on'){
                    $temp2=explode(",",$request->input("gov_info"));

                    for($h=0;$h<count($temp2);$h++){
                        $gov_temp=explode("_",$temp2[$h]);
                        if($gov_temp[0]!=''){
                           $where['gov_info'][$h]=$gov_temp[0];
                        }
                    }
                }
                if($request->input("dep")=='on'){
                    $temp3=explode(",",$request->input("dep_info"));
                    for($k=0;$k<count($temp3);$k++){
                        $dep_temp=explode("_",$temp3[$k]);
                        if($dep_temp[0]!=''){
                           $where['dep_info'][$k]=$dep_temp[0];
                        }
                    }
                }
                if($request->input("sex")){
                    $where['sex']=$request->input("sex");
                }
                $db_select='offfaxa,offfaxb,t01tb.class,t13tb.term,cname,t13tb.dept,offtela1,offtelb1,offtelc1,t01tb.name,(RTRIM(offaddr1)+offaddr2) as offaddress';
                $data[1]=$this->des->getFaxStudent($class_info_condition,$db_select,$where);

                for($e=0;$e<count($data[1]);$e++){
                    if($data[1][$e]['offfaxa']!=''&&$data[1][$e]['offfaxb']!=''){
                        $data[2][$e]=$data[1][$e];//有傳真號碼
                    }else{
                        $data[3][$e]=$data[1][$e];//無傳真號碼
                    }
                }

                if(!empty($data[2])){
                    $data[2]=array_values($data[2]);
                    $content='';
                    for($f=0;$f<count($data[2]);$f++){
                        $content.=$data[2][$f]['offfaxa'].$data[2][$f]['offfaxb']."##".$data[2][$f]['cname']." "." "." "." "." ".$data[2][$f]['dept']."\r\n";
                    }


                    $filename="電話簿檔案.txt";
                    $headers = [
                        'Content-type' => 'text/plain',
                        'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                    ];

                    return Response::make($content, 200, $headers);
                }

                return redirect()->back();
            }

            if($request->input("datatype")=='teacher'){
                $db_select='offfaxa,offfaxb,t09tb.class,t09tb.term,cname,dept,offtela1,offtelb1,offtelc1,name,offaddress';
                $data[1]=$this->des->getFax($class_info_condition,$db_select);
                for($e=0;$e<count($data[1]);$e++){
                    if($data[1][$e]['offfaxa']!=''&&$data[1][$e]['offfaxb']!=''){
                        $data[2][$e]=$data[1][$e];//有傳真號碼
                    }else{
                        $data[3][$e]=$data[1][$e];//無傳真號碼
                    }
                }

                if(!empty($data[2])){
                    $data[2]=array_values($data[2]);
                    $content='';
                    for($f=0;$f<count($data[2]);$f++){
                        $content.=$data[2][$f]['offfaxa'].$data[2][$f]['offfaxb']."##".$data[2][$f]['cname']." "." "." "." "." ".$data[2][$f]['dept']."\r\n";
                    }


                    $filename="電話簿檔案.txt";
                    $headers = [
                        'Content-type' => 'text/plain',
                        'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                    ];

                    return Response::make($content, 200, $headers);
                }

                return redirect()->back();
            }

        }




    }

    public function send(Request $request,$cond=null)
    {


            $class_info=explode(",",$cond);//課程資訊

            for($i=0;$i<count($class_info);$i++){
                $class_info_condition_temp=explode("_",$class_info[$i]);
                if(isset($class_info_condition_temp[0])&&$class_info_condition_temp[0]!=''){
                    $class_info_condition[$i]['class']=$class_info_condition_temp[0];
                }
                if(isset($class_info_condition_temp[1])){
                    $class_info_condition[$i]['term']=$class_info_condition_temp[1];
                }
            }
            $datatype=$class_info_condition[0];
            unset($class_info_condition[0]);
            $class_info_condition=array_values($class_info_condition);
            if($datatype=='teacher'){
                $db_select="m01tb.email";
                $mail=$this->des->getClassTeacher($class_info_condition,$db_select);
            }else{
                $db_select="m02tb.email";
                $mail=$this->des->getClassStudent($class_info_condition,$db_select);
            }


            $url = $_SERVER['REQUEST_URI'];
            $url = urldecode($url);
            //dd($mail);
        if($request->input()){
            $data=[];
            $data=['title'=>$request->input('title'),'content'=>$request->input('content')];



            $class_info=explode(",",$cond);//課程資訊

            for($i=0;$i<count($class_info);$i++){
                $class_info_condition_temp=explode("_",$class_info[$i]);
                if(isset($class_info_condition_temp[0])&&$class_info_condition_temp[0]!=''){
                    $class_info_condition[$i]['class']=$class_info_condition_temp[0];
                }
                if(isset($class_info_condition_temp[1])){
                    $class_info_condition[$i]['term']=$class_info_condition_temp[1];
                }
            }

            $datatype=$class_info[0];
            unset($class_info_condition[0]);
            $class_info_condition=array_values($class_info_condition);
            if($datatype=='teacher'){
                $db_select="m01tb.email";
                $mail=$this->des->getClassTeacher($class_info_condition,$db_select);
            }else{
                $db_select="m02tb.email";
                $mail=$this->des->getClassStudent($class_info_condition,$db_select);
            }

            Mail::send("admin/dataexport/send", $data, function ($message) use ($mail,$data){
                for($i=0;$i<count($mail);$i++){
                    $message->from('csdi.send@gmail.com', 'tobytest');
                    $message->subject($data['title']);
                    $message->to($mail[$i]['email']);
                }
            });
            return back()->with("message","寄送成功!");
        }else{

            return view("admin/dataexport/mail",compact('url'));
        }

    }

    private function dataFormat($datas){
        $m02tbFields = config('database_fields.m02tb');

        foreach ($datas as $key => $data){
            if (isset($data['sex'])){
                $datas[$key]['sex'] = (isset($m02tbFields['sex'][$data['sex']])) ? $m02tbFields['sex'][$data['sex']] : $data['sex'];
            }

            if (isset($data['rank'])){
                $datas[$key]['rank'] = (isset($m02tbFields['rank'][$data['rank']])) ? $m02tbFields['rank'][$data['rank']] : $data['rank'];
            }

        }
        return $datas;
    }

}