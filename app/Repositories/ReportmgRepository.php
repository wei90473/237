<?php
namespace App\Repositories;

use App\Models\Important_message;
use Auth;
use DB;


class ReportmgRepository
{
    public function getMessage($condition=null)
    {
        $query=Important_message::select('*');
        if(!empty($condition)){
            if(isset($condition['title'])){
                $query->where('title','like','%'.$condition['title'].'%');
            }
            if(isset($condition['position'])){
                if($condition['position']=='loginbefore' || $condition['position']=='loginafter'){
                   $query->where('position',$condition['position']); 
                }
            }
            if(isset($condition['id'])){
                $query->where('id','like',$condition['id']);
            }
        }
        $data=$query->get();
        $data=$data->toArray();
        return $data;
    }
    
   
}
