<?php
namespace App\Services;

use App\Models\Important_message;
use App\Repositories\ReportmgRepository;
use DB;
use DateTime;
class ReportmgService
{
    
    public function __construct(ReportmgRepository $reportmgRepository)
    {
        $this->rmr=$reportmgRepository;
    }

    public function getMessage($condition=null)
    {
        return $this->rmr->getMessage($condition);
    }

    public function insert($condition)
    {
        
        if(!isset($condition['opener'])){
            $condition['opener']='off';
        }
        if($condition['position']=='loginbefore'){
            $condition['for']='';
        }else{
            $condition['for']=implode(",",$condition['for']);
        }

         $condition['launch']=$this->dateTo_ad($condition['launch']);
        $condition['discontinue']=$this->dateTo_ad($condition['discontinue']);
        $insert=['title'=>$condition['title'],'position'=>$condition['position'],'launch'=>$condition['launch'],
        'discontinue'=>$condition['discontinue'],'opener'=>$condition['opener'],'content'=>$condition['content'],'for'=>$condition['for']];
        DB::table('important_message')->insert($insert);
    }

    public function update($update)
    {

        $for = '';
        if(!empty($update['for'])){
            $for=implode(",",$update['for']);
        }
        if(empty($update['opener'])){
            $update['opener'] = 'off';
        }
        $update['launch']=$this->dateTo_ad($update['launch']);
        $update['discontinue']=$this->dateTo_ad($update['discontinue']);

        $condition=['title'=>$update['title'],'position'=>$update['position'],'for'=>$for,'opener'=>$update['opener'],'content'=>$update['content'],
                    'launch'=>$update['launch'],'discontinue'=>$update['discontinue']];
        DB::table('important_message')->where('id',$update['id'])->update($condition);
    }

    public function delete($id)
    {
        DB::table('important_message')->where('id',$id)->delete();
    }
    
    //轉西元
    public function dateTo_ad($in_date)
    {

        $cyear = substr($in_date, 0, -4);
        $year = ((int) $cyear )+1911;
        $mon = substr($in_date, -4, 2);
        $day = substr($in_date, -2);

        $date = date("Y-m-d", mktime (0,0,0,$mon ,$day, date($year)));
        return $date;

    }

}
