<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Services\ReportmgService;
use App\Services\User_groupService;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\M13tb;//機關基本資料檔
use File;
use Response;
use ZipArchive;


class ReportmgController extends Controller
{
    public function __construct(ReportmgService $reportmgService, User_groupService $user_groupService)
    {
        $this->rms=$reportmgService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('reportmg', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    public function index(Request $request)
    {
        $data=[];
        $condition=$request->input();
        $data=$this->rms->getMessage($condition);
        return view('admin/reportmg/list',compact('data','condition'));

    }

    public function create(Request $request)
    {
        $condition=$request->input();
        if(!empty($condition)){
            $this->rms->insert($condition);
            return redirect('admin/reportmg')->with('result', '1')->with('message', '新增成功!');
        }
        return view('admin/reportmg/form');
    }

    public function edit(Request $request,$id=null)
    {
        $condition['id']=$id;
        $data=$this->rms->getMessage($condition);
 
        $data[0]['launch'] = $this->dateTo_c($data[0]['launch']);
        $data[0]['discontinue'] = $this->dateTo_c($data[0]['discontinue']);

        $data=$data[0];

        $update=$request->input();
        if(!empty($update)){
            $update['id']=$id;
            $this->rms->update($update);
            return redirect('admin/reportmg')->with('result', '1')->with('message', '修改成功!');
        }
        return view('admin/reportmg/form',compact('data','id'));
    }

    public function delete($id)
    {
        $this->rms->delete($id);
        return redirect('admin/reportmg')->with('result', '1')->with('message', '刪除成功!');
    }

    function dateTo_c($in_date, $in_txt="")
    {

        $ch_date = explode("-", $in_date);
        $ch_date[0] = $ch_date[0]-1911;
        $date = '00.00.00';
        
        
        if ($in_txt=="")
        {
            $date = '000000';
            if ($ch_date[0] > 0 ) $date = $ch_date[0]."".$ch_date[1]."".$ch_date[2];
            
        }
        else
        {
            if ($ch_date[0] > 0 ) $date = $ch_date[0]."$in_txt".$ch_date[1]."$in_txt".$ch_date[2];
        }

        return $date;

    }

}