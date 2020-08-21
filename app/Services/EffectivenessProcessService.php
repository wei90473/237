<?php
namespace App\Services;

use App\Repositories\EffectivenessProcessRepository;
use App\Models\T56tb;
use App\Models\T95tb;
use App\Models\T09tb;
use App\Models\T57tb;
use DB;

class EffectivenessProcessService
{
    /**
     * EffectivenessProcessService constructor.
     * @param EffectivenessProcessRepository $effectivenessProcessRpository
     */
    public function __construct(EffectivenessProcessRepository $effectivenessProcessRpository)
    {
        $this->effectivenessProcessRpository = $effectivenessProcessRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getEffectivenessProcessList($queryData = [])
    {
        return $this->effectivenessProcessRpository->getEffectivenessProcessList($queryData);
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList()
    {
        return $this->effectivenessProcessRpository->getClassList();
    }

    public function getClass($queryData)
    {
        return $this->effectivenessProcessRpository->getClass($queryData);
    }
    
    public function cleanOkRate($class_info)
    {
        $data=$this->effectivenessProcessRpository->get_update_t09tb($class_info);
        $data=$data->toArray();
        
        \DB::transaction(function() use ($data){
            $update=['okrate'=>0];
            foreach($data as $temp){
                T09tb::where('class',$temp['class'])->where('term',$temp['term'])->where('course',$temp['course'])->where('idno',$temp['idno'])->update($update);
            }
            
        });
    }
    public function insertOkRate($class_info)
    {
        $data=$this->effectivenessProcessRpository->insertOkRate($class_info);
        $data=$data->toArray();

        \DB::transaction(function() use ($data){
            foreach($data as $temp){
                $update=['okrate'=>$temp['okrate']];
                T09tb::where('class',$temp['class'])->where('term',$temp['term'])->where('course',$temp['course'])->where('idno',$temp['idno'])->update($update);
            }
            
        });

    }
   
    public function delete_insert_t57tb_data($class_info)
    {
        if(isset($class_info['year'])){
            $data=DB::table('t57tb')->where('class','like',$class_info['year'].'%')->get();
        }else{
            $data=DB::table('t57tb')->where('class',$class_info['class'])->where('term',$class_info['term'])->where('times',$class_info['times'])->get();
        }
        if(!empty($data->toArray())){
            \DB::transaction(function () use ($data){
                foreach($data as $temp){
                    DB::table('t57tb')->where('class',$temp->class)->where('term',$temp->term)->where('times',$temp->times)->delete();
                }
            });
        }
    }
    public function insert_t57tb_data($class_info)
    {
        $data=$this->effectivenessProcessRpository->get_insert_t57tb_data($class_info);
        
        if(!empty($data->toArray())){
            \DB::transaction(function () use ($data){
                foreach($data as $temp){
                    $totper=0;
                    if($temp->conper+$temp->attper+$temp->worper+$temp->envper==0){
                        $totper=$temp->teaper;
                    }else{
                        $totper=($temp->conper+$temp->attper)*0.23+$temp->worper*0.1+$temp->teaper*0.44;
                    }
                    
                    $insert=['class'=>$temp->class,'term'=>$temp->term,'times'=>$temp->times,
                     'teaper'=>round($temp->teaper,2) , 'conper'=>round($temp->conper,2) ,'envper'=>round($temp->envper,2),
                     'worper'=>round($temp->worper,2) , 'attper'=>round($temp->attper,2),'totper'=>round($totper,2)];

                    DB::table('t57tb')->insert($insert);
                }
            });
        }
    }

   
    public function getTeacherAndCourse($class_info)
    {
        return $this->effectivenessProcessRpository->getTeacherAndCourse($class_info);
    }


}
