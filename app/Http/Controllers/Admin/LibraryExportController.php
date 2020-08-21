<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Services\LibraryExportService;
use App\Services\User_groupService;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\M13tb;//機關基本資料檔
use File;
use Response;


class LibraryExportController extends Controller
{
    public function __construct(LibraryExportService $libraryexportservice, User_groupService $user_groupService)
    {
        $this->les=$libraryexportservice;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('libraryexport', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }
    public function index()
    {
        return view('admin/libraryexport/list');
    }


    function unicode_encode($strLong)
    {
        $strArr = preg_split('/(?<!^)(?!$)/u', $strLong);//拆分字符串为数组(含中文字符)
        $resUnicode = '';
        foreach ($strArr as $str)
        {
            $bin_str = '';
            $arr = is_array($str) ? $str : str_split($str);//获取字符内部数组表示,此时$arr应类似array(228, 189, 160)
            foreach ($arr as $value)
            {
                $bin_str .= decbin(ord($value));//转成数字再转成二进制字符串,$bin_str应类似111001001011110110100000,如果是汉字"你"
            }
            $bin_str = preg_replace('/^.{4}(.{4}).{2}(.{6}).{2}(.{6})$/', '$1$2$3', $bin_str);//正则截取, $bin_str应类似0100111101100000,如果是汉字"你"
            $unicode = dechex(bindec($bin_str));//返回unicode十六进制
            $_sup = '';
            for ($i = 0; $i < 4 - strlen($unicode); $i++)
            {
                $_sup .= '0';//补位高字节 0
            }
            $str =  '\\u' . $_sup . $unicode; //加上 \u  返回
            $resUnicode .= $str;
        }
        return $resUnicode;
    }

    //Unicode编码转字符串
    function unicode_decode2($str)
    {
        $json = '{"str":"' . $str . '"}';
        $arr = json_decode($json, true);
        if (empty($arr)) return '';
        return $arr['str'];
    }




    public function export(Request $request,$type)
    {
        $date=date('Y-m-d');
        $date=str_replace("-","",$date);
        $temp_year=substr($date,0,4);
        $temp_year=$temp_year-1911;
        $date=$temp_year.substr($date,4,4);
        $sdate=$request->input('final_sdate2');
        $edate=$request->input('final_edate2');

        //產生臺北班別.csv
        if($type==1){
            $control=['class','taipei'];
            $data[1]=$this->les->get_csv_sql($sdate,$edate,$control);

            Excel::create($sdate.'-'.$edate.'台北班別', function ($excel) use ($data) {//第一參數是檔案名稱
                $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱
                    $row=1;//控制列index
                    //插入資料
                    for($b=0;$b<count($data[1]);$b++){
                            unset($data[1][$b]['branch']);
                            $class=implode(",",$data[1][$b]);
                            //$temp=$this->unicode_encode($class);
                            //$temp2 = $this->unicode_decode2($temp);

                            $sheet->cell('A'.$row, function($cell) use($data,$class) {
                                $cell->setValue($class);
                            });

                            $row++;
                    }
                });
            })->export('csv');
        }

        //產生南投班別.csv
        if($type==2){
            $control=['class','nantou'];
            $data[1]=$this->les->get_csv_sql($sdate,$edate,$control);

            Excel::create($sdate.'-'.$edate.'南投班別', function ($excel) use ($data) {//第一參數是檔案名稱
                $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱
                    $row=1;//控制列index
                    //插入資料
                    for($b=0;$b<count($data[1]);$b++){
                            unset($data[1][$b]['branch']);
                            $class=implode(",",$data[1][$b]);

                            $sheet->cell('A'.$row, function($cell) use($data,$class) {
                                $cell->setValue($class);
                            });

                            $row++;
                    }
                });
            })->export('csv');
        }

        //產生台北學員.csv
        if($type==3){
            $control=['student','taipei'];
            $data[1]=$this->les->get_csv_sql($sdate,$edate,$control);
            //dd($data[1]);

            Excel::create($sdate.'-'.$edate.'台北學員', function ($excel) use ($data) {//第一參數是檔案名稱
                $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱
                    $row=1;//控制列index
                    //插入資料
                    for($b=0;$b<count($data[1]);$b++){
                            unset($data[1][$b]['branch']);
                            $info=implode(",",$data[1][$b]);
                            //$temp=$this->unicode_encode($info);
                            //$temp2 = $this->unicode_decode2($temp);
                            //dd($info);
                            $sheet->cell('A'.$row, function($cell) use($data,$info) {
                                $cell->setValue($info);
                            });

                            $row++;
                    }
                });
            })->export('csv');
        }

        //產生南投學員.csv
        if($type==4){
            $control=['student','nantou'];
            $data[1]=$this->les->get_csv_sql($sdate,$edate,$control);
            //dd($data[1]);

            Excel::create($sdate.'-'.$edate.'南投學員', function ($excel) use ($data) {//第一參數是檔案名稱
                $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱
                    $row=1;//控制列index
                    //插入資料
                    for($b=0;$b<count($data[1]);$b++){
                            unset($data[1][$b]['branch']);
                            $info=implode(",",$data[1][$b]);
                            //$temp=$this->unicode_encode($info);
                            //$temp2 = $this->unicode_decode2($temp);
                            //dd($info);
                            $sheet->cell('A'.$row, function($cell) use($data,$info) {
                                $cell->setValue($info);
                            });

                            $row++;
                    }
                });
            })->export('csv');
        }



    }




}

