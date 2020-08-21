<?php
namespace App\Repositories;

use App\Models\T01tb;//班別基本資料檔
use App\Models\T04tb;//開班資料檔
use App\Models\T09tb;//講座任課資料檔
use App\Models\T13tb;//班別學員資料檔
use App\Models\M01tb;//講座基本資料檔
use App\Models\M02tb;//學員基本資料檔
use App\Models\M13tb;//機關基本資料檔
use Auth;
use DB;


class DataExportRepository
{
    

    //取得課程
    public function select_class($class_info)
    {
        $query=T01tb::select("t01tb.class","t01tb.name","t04tb.sdate","t04tb.edate","t04tb.term");
        $query->join('t04tb',function($join){
            $join->on('t04tb.class','=','t01tb.class');
        });
        //dd($class_info);
        if($class_info['class']!=''){
            $query->where('t01tb.class',$class_info['class']);
        }
        if($class_info['class_name']!=''){
            $query->where('t01tb.name', 'LIKE', "%{$class_info['class_name']}%"); 
        }
        
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);
        //$data=$query->get();
        return $data;
    }

    //取得所有主管機關
    public function master_select()
    {
        $query=M13tb::select('organ','lname');
        $data=$query->get();
        $data=$data->toArray();
        $final_data=[];
        for($i=0;$i<count($data);$i++){
            //$index=implode(" ",$data[$i]);
            $final_data[$data[$i]['organ']]=$data[$i]['lname'];
        }
        //var_dump($data);
        //dd($final_data);
        return $final_data;
    }

    //取得課程講師
    public function getClassTeacher($class_info,$db_select)
    {
       
        $where='';
        for($i=0;$i<count($class_info);$i++){
            $or='or';
            if($i==0){
                $or='';
            }
            $where.=$or."(t09tb.class={$class_info[$i]['class']} and t09tb.term={$class_info[$i]['term']})";
        }
        $query=M01tb::select(DB::raw($db_select));
        $query->join("t09tb",'m01tb.idno','=','t09tb.idno');
        $query->leftJoin("t01tb",'t09tb.class','=','t01tb.class');
        $query->whereRaw($where);
        $query->groupBy('t09tb.class','m01tb.idno','cname','ename','sex','birth');
        $data=$query->get()->toArray();
        //dd($data);
        
        return $data;
        //var_dump($data);
        //die();
    }

    //取得地址條
    public function getAddress($class_info,$db_select)
    {
        $class=[];
        $term=[];
        $name=[];
        for($i=0;$i<count($class_info);$i++){
            $class[$i]=$class_info[$i]['class'];
            $term[$i]=$class_info[$i]['term'];
            $name[$i]=$class_info[$i]['name'];
        }
        $query=M01tb::select(DB::raw($db_select));
        $query->join("t09tb",'m01tb.idno','=','t09tb.idno');
        $query->leftJoin("t01tb",'t09tb.class','=','t01tb.class');
        $query->whereIn('t09tb.class',$class);
        $query->whereIn('t09tb.term',$term);
        $query->whereIn('t01tb.name',$name);
        $query->groupBy('m01tb.idno','cname','ename','sex','birth');
        $data=$query->get();
        $data=$data->toArray();
        return $data;
    }

    public function getAddressStudent($class_info,$db_select,$where=null)
    {
        $class=[];
        $term=[];
        $name=[];
        for($i=0;$i<count($class_info);$i++){
            $class[$i]=$class_info[$i]['class'];
            $term[$i]=$class_info[$i]['term'];
            $name[$i]=$class_info[$i]['name'];
        }
        
        $query=M02tb::select(DB::raw($db_select));
        $query->join("t13tb",'t13tb.idno','=','m02tb.idno');
        $query->leftJoin("m13tb",'m13tb.organ','=','m02tb.organ');
        $query->leftJoin("t01tb",'t13tb.class','=','t01tb.class');
        $query->whereIn('t13tb.class',$class);
        $query->whereIn('t13tb.term',$term);
        if(!empty($where)){
            if(isset($where['master_info'])){
                $query->whereIn('t13tb.organ',$where['master_info']);
            }
            if(isset($where['gov_info'])){
                $query->whereIn('t13tb.rank',$where['gov_info']);
            }
            if(isset($where['dep_info'])){
                $query->whereIn('m02tb.offaddr1',$where['dep_info']);
            }
            if(isset($where['sex'])){
                $query->where('m02tb.sex',$where['sex']);
            }
        }

        $data=$query->get();
        $data=$data->toArray();
        //dd($query->get());
        return $data;
    }
    //取得傳真通知
    public function getFax($class_info,$db_select)
    {
        $class=[];
        $term=[];
        $name=[];
        for($i=0;$i<count($class_info);$i++){
            $class[$i]=$class_info[$i]['class'];
            $term[$i]=$class_info[$i]['term'];
            $name[$i]=$class_info[$i]['name'];
        }
        $query=M01tb::select(DB::raw($db_select));
        $query->join("t09tb",'m01tb.idno','=','t09tb.idno');
        $query->leftJoin("t01tb",'t09tb.class','=','t01tb.class');
        $query->whereIn('t09tb.class',$class);
        $query->whereIn('t09tb.term',$term);
        $query->whereIn('t01tb.name',$name);
        $query->groupBy('m01tb.idno','cname','ename','sex','birth');
        $data=$query->get();
        $data=$data->toArray();
        
        return $data;
    }
    //取得課程學生
    public function getClassStudent($class_info,$db_select,$where=null)
    {

        /*$class=[];
        $term=[];
        $name=[];
        for($i=0;$i<count($class_info);$i++){
            $class[$i]=$class_info[$i]['class'];
            $term[$i]=$class_info[$i]['term'];
            $name[$i]=$class_info[$i]['name'];
        }*/

        $class_where='';
        for($i=0;$i<count($class_info);$i++){
            $or='or';
            if($i==0){
                $or='';
            }
            $class_where.=$or."(t13tb.class={$class_info[$i]['class']} and t13tb.term={$class_info[$i]['term']})";
        }
        
        $query=M02tb::select(DB::raw($db_select));
        $query->join("t13tb",'t13tb.idno','=','m02tb.idno');
        $query->leftJoin("m13tb",'m13tb.organ','=','m02tb.organ');
        $query->leftJoin("t01tb",'t13tb.class','=','t01tb.class');
        //$query->whereIn('t13tb.class',$class);
        //$query->whereIn('t13tb.term',$term);
        $query->whereRaw($class_where);
        if(!empty($where)){
            if(isset($where['master_info'])){
                $query->whereIn('t13tb.organ',$where['master_info']);
            }
            if(isset($where['gov_info'])){
                $query->whereIn('t13tb.rank',$where['gov_info']);
            }
            if(isset($where['dep_info'])){
                $query->whereIn('m02tb.offaddr1',$where['dep_info']);
            }
            if(isset($where['sex'])){
                $query->where('m02tb.sex',$where['sex']);
            }
        }
        //$query->groupBy('m01tb.idno','cname','ename','sex','birth');
        $data=$query->get();
        $data=$data->toArray();

        // dd($data);
        //dd($query->get());
        return $data;
        //var_dump($data);
        //die();
    }

    //取得學生傳真通知
    public function getFaxStudent($class_info,$db_select,$where)
    {
        $class=[];
        $term=[];
        $name=[];
        for($i=0;$i<count($class_info);$i++){
            $class[$i]=$class_info[$i]['class'];
            $term[$i]=$class_info[$i]['term'];
            $name[$i]=$class_info[$i]['name'];
        }
        
        $query=M02tb::select(DB::raw($db_select));
        $query->join("t13tb",'t13tb.idno','=','m02tb.idno');
        $query->leftJoin("m13tb",'m13tb.organ','=','m02tb.organ');
        $query->leftJoin("t01tb",'t13tb.class','=','t01tb.class');
        $query->whereIn('t13tb.class',$class);
        $query->whereIn('t13tb.term',$term);
        if(!empty($where)){
            if(isset($where['master_info'])){
                $query->whereIn('t13tb.organ',$where['master_info']);
            }
            if(isset($where['gov_info'])){
                $query->whereIn('t13tb.rank',$where['gov_info']);
            }
            if(isset($where['dep_info'])){
                $query->whereIn('m02tb.offaddr1',$where['dep_info']);
            }
            if(isset($where['sex'])){
                $query->where('m02tb.sex',$where['sex']);
            }
        }
        //$query->groupBy('m01tb.idno','cname','ename','sex','birth');
        $data=$query->get();
        $data=$data->toArray();
        //dd($query->get());
        return $data;
    }

   
}
