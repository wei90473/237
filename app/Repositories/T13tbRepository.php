<?php
namespace App\Repositories;

use App\Models\T13tb;
use App\Repositories\Repository;
use DateTime;
use DB;

class T13tbRepository extends Repository
{
    public function __construct(T13tb $t13tb)
    {
        $this->model = $t13tb;
    }  
    
    public function getCheckApply($class_info, $check_class)
    {
        return $this->model->selectRaw('*, t01tb.name t01tb_name, t04tb.sdate t04tb_sdate')
                           ->join('t01tb', function($join){
                               $join->on('t01tb.class', '=', 't13tb.class');
                           })
                           ->join('t04tb', function($join){
                                $join->on('t04tb.class', '=', 't13tb.class')
                                     ->on('t04tb.term', '=', 't13tb.term');
                           })                           
                           ->where('status', '=', 1)
                           ->where(function($query) use($class_info, $check_class){
                               $query->where(function($query2) use($class_info, $check_class){
                                       $query2->where('t13tb.class', '=', $class_info['class'])
                                              ->where('t13tb.term', '<>', $class_info['term']);
                                   })->orWhere(function($query3) use($class_info, $check_class){
                                       $query3->where('t13tb.class', '=', $check_class);
                                   });
                           })->whereExists(function($q) use($class_info, $check_class){
                               $q->selectRaw('*')
                                 ->from('t27tb')
                                 ->where('class', '=', $class_info['class'])
                                 ->where('term', '=', $class_info['term'])
                                 ->where(function($query4) use($class_info, $check_class){
                                    $query4->where(function($query5) use($class_info, $check_class){
                                        $query5->where('loginid', '<>', '')
                                                ->where('progress', '=', 0);
                                    })->orWhere(function($query6) use($class_info, $check_class){
                                        $query6->where('loginid', '=', '');
                                    });
                                 })
                                 ->where('idno', '=', DB::raw('t13tb.idno'));
                           })
                           ->get();
    }

    public function get($queryData, $select="*", $paginate = true, $order_bys = [], $with = [])
    {
        if (!empty($with)){
            $t13tb = $this->model->select($select)->with($with);
        }else{
            $t13tb = $this->model->select($select)->with(['m13tb', 'm02tb']);
        }
        

        $t13tb->leftJoin('m02tb', 'm02tb.idno', '=', 't13tb.idno');
        // $t13tb->leftJoin('m17tb', 'm17tb.enrollorg', '=', 'm17tb.idno');
        $t13tb->leftJoin('m13tb', 'm13tb.organ', '=', 't13tb.organ');

        if (!empty($queryData['status'])){
            $t13tb->where('t13tb.status', '=', $queryData['status']);
        }

        if (!empty($queryData['class'])){
            $t13tb->where('t13tb.class', '=', $queryData['class']);
        }

        if (!empty($queryData['term'])){
            $queryData['term'] = str_pad($queryData['term'], 2, '0', STR_PAD_LEFT);
            $t13tb->where('t13tb.term', '=', $queryData['term']);
        }

        if (!empty($queryData['dept'])){
            $t13tb->where('t13tb.dept', 'LIKE', "%{$queryData['dept']}%");
        }

        if (!empty($queryData['email'])){
            $t13tb->where('m02tb.email', 'LIKE', "%{$queryData['email']}%");
        }      
        
        if (!empty($queryData['identity'])){
            $t13tb->where('m02tb.identity', '=', $queryData['identity']);
        }      
  
        if (!empty($queryData['name'])){
            $t13tb->where('m02tb.cname', '=', $queryData['name']);
        }            

        if (!empty($queryData['organ_name'])){
            $t13tb->where('m13tb.lname', 'LIKE', "%{$queryData['organ_name']}%");
        }

        if (empty($order_bys)){
            $t13tb->orderBy('no');
            $t13tb->orderBy('identity');
            $t13tb->orderBy('idno');
        }else{
            foreach ($order_bys as $order_by){
                $t13tb->orderBy($order_by[0], $order_by[1]);
            }
        }

        if ($paginate){
            return $t13tb->paginate(10);            
        }else{
            return $t13tb->get();
        }

    }

    public function batchUpdateGroup($class_info, $group, $idnos)
    {
        return $this->model->where($class_info)
                           ->whereIn('idno', $idnos)
                           ->update(['groupno' => $group]);
    }

    public function getByNo($t04tb_info, $nos)
    {
        return $this->model->where($t04tb_info)
                           ->whereIn('no', $nos)
                           ->get();
    }

    public function getByIdnos($t04tb_info, $idnos)
    {
        return $this->model->where($t04tb_info)
                           ->whereIn('idno', $idnos)
                           ->get();
    }    

    public function deleteForIdentity($t04tb_info, $identity)
    {
        return $this->model->join('m02tb', 'm02tb.idno', '=', 't13tb.idno')
                           ->where($t04tb_info)
                           ->where('m02tb.identity', '=', $identity)
                           ->delete();
    }

    public function getT2tbCopyInfo($t04tb_info)
    {
        $copy_field = [
            't13tb.class',
            't13tb.term',
            't13tb.idno',
            'm02tb.cname',
            'm02tb.sex',
            't13tb.organ',
            't13tb.dept',
            't13tb.position',
            't13tb.education',
            'm02tb.offtela1 as offtela',
            'm02tb.offtelb1 as offtelb',
            'm02tb.offtelc1 as offtelc',
            'm02tb.offfaxa',
            'm02tb.offfaxb',
            'm02tb.homtela',
            'm02tb.homtelb',
            'm02tb.mobiltel',
            'm02tb.email',
            't13tb.offemail',
            't13tb.offname',
            't13tb.offtel',
            'm02tb.offzip',
            'm02tb.offaddr1',
            'm02tb.offaddr2',
            'm02tb.send',
            't13tb.rank',
            't13tb.ecode',
            'm02tb.chief',
            'm02tb.personnel',
            'm02tb.aborigine',
            'm02tb.handicap',
            't13tb.dorm',
            't13tb.extradorm',
            't13tb.nonlocal',
            't13tb.vegan',
            'm02tb.enrollid',
            'm02tb.identity'
        ];
        return $this->model->select($copy_field)
                           ->join('m02tb', 'm02tb.idno', '=', 't13tb.idno')
                           ->where($t04tb_info)
                           ->where('status', '=', 1)
                           ->get();
                           
                                
    }

    public function getStudentCount($class,$term,$sex='',$dorm='',$hasbed='')
    {
        $model = $this->model->selectRaw('count(1) as cnt')
                            ->Join('m02tb', 't13tb.idno', '=', 'm02tb.idno');

        $model->where('t13tb.class', '=', $class);

        if(!empty($sex)){
            $model->where('m02tb.sex', '=', $sex);
        }
        
        if(!empty($dorm)){
            $model->whereRaw('(t13tb.dorm = ? or t13tb.extradorm = ?)', [$dorm,$dorm]);
        }

        if(!empty($term)){
            $model->where('t13tb.term', '=', $term);
        }

        if(!empty($hasbed)){
            $model->whereRaw("t13tb.bedno is not null");
        }

        $data = $model->get();

        return $data[0]['cnt'];
    }

    public function getDormSupervisorStudent($class,$term,$sex)
    {   
        $model = $this->model->selectRaw('t13tb.idno, t13tb.no, t13tb.floorno, t13tb.bedno, m02tb.cname, m02tb.handicap, m02tb.sex, edu_floor.floorname, edu_bed.roomname')
                            ->Join('m02tb', 't13tb.idno', '=', 'm02tb.idno')
                            ->leftJoin('edu_floor', 't13tb.floorno', '=', 'edu_floor.floorno')
                            ->leftJoin('edu_bed', 't13tb.bedno', '=', 'edu_bed.bedno');

        $model->where('t13tb.class', '=', $class);
        $model->where('t13tb.term', '=', $term);
        $model->whereRaw("t13tb.status in (1,2)");
        $model->whereRaw('(t13tb.dorm = ? or t13tb.extradorm = ?)', ['Y','Y']);
        $model->whereRaw("t13tb.rank in ('09','10','11','12','13','14')");
        $model->whereRaw('m02tb.chief = "Y"');
        $model->where('m02tb.sex', '=', $sex);
        $model->orderBy('t13tb.rank','desc');

        $data = $model->get();

        return $data;
    }

    public function getLongDormSupervisorStudent($class,$term,$sex,$week)
    {
        $model = $this->model->selectRaw('t13tb.idno, t13tb.no, edu_stayweeksdt.floorno, edu_stayweeksdt.bedno, m02tb.cname, m02tb.handicap, m02tb.sex, edu_floor.floorname, edu_bed.roomname')
                            ->leftJoin('edu_stayweeksdt', function($join) use($week){
                                $join->on('edu_stayweeksdt.class', '=', 't13tb.class')
                                     ->on('edu_stayweeksdt.term', '=', 't13tb.term')
                                     ->on('edu_stayweeksdt.idno', '=', 't13tb.idno')
                                     ->on('edu_stayweeksdt.week', '=', DB::raw($week));
                            }) 
                            ->Join('m02tb', 't13tb.idno', '=', 'm02tb.idno')
                            ->leftJoin('edu_floor', 'edu_stayweeksdt.floorno', '=', 'edu_floor.floorno')
                            ->leftJoin('edu_bed', 'edu_stayweeksdt.bedno', '=', 'edu_bed.bedno');

        $model->where('t13tb.class', '=', $class);
        $model->where('t13tb.term', '=', $term);
        $model->whereRaw("t13tb.status in (1,2)");
        $model->whereRaw('(t13tb.dorm = ? or t13tb.extradorm = ?)', ['Y','Y']);
        $model->whereRaw("t13tb.rank in ('09','10','11','12','13','14')");
        $model->whereRaw('m02tb.chief = "Y"');
        $model->where('m02tb.sex', '=', $sex);
        $model->orderBy('t13tb.rank','desc');

        $data = $model->get();

        return $data;
    }


    public function getDormStudent($class,$term,$sex='',$reset='')
    {
        $model = $this->model->selectRaw('t13tb.idno, t13tb.no, t13tb.floorno, t13tb.bedno, m02tb.rank, m02tb.chief, m02tb.cname, m02tb.handicap, edu_floor.floorname, edu_bed.roomname')
                            ->Join('m02tb', 't13tb.idno', '=', 'm02tb.idno')
                            ->leftJoin('edu_floor', 't13tb.floorno', '=', 'edu_floor.floorno')
                            ->leftJoin('edu_bed', 't13tb.bedno', '=', 'edu_bed.bedno');

        if($sex == '1'){
            $model->where('m02tb.sex', '=', 'M');
        } else if($sex == '2'){
            $model->where('m02tb.sex', '=', 'F');
        }

        $model->where('t13tb.class', '=', $class);
        $model->where('t13tb.term', '=', $term);
        $model->whereRaw("t13tb.status in (1,2)");

        if($reset == 'N'){
            $model->whereRaw("(t13tb.bedno is null or t13tb.bedno = '')");
        }

        $model->whereRaw('(t13tb.dorm = ? or t13tb.extradorm = ?)', ['Y','Y']);
        $model->orderBy('t13tb.no');
        $data = $model->get();

        return $data;
    }

    public function getLongDormStudent($class,$term,$sex,$week,$reset='')
    {
        $model = $this->model->selectRaw('t13tb.idno, t13tb.no, edu_stayweeksdt.floorno, edu_stayweeksdt.bedno,m02tb.rank, m02tb.chief, m02tb.cname, m02tb.handicap, edu_floor.floorname, edu_bed.roomname')
                            ->leftJoin('edu_stayweeksdt', function($join) use($week){
                                $join->on('edu_stayweeksdt.class', '=', 't13tb.class')
                                     ->on('edu_stayweeksdt.term', '=', 't13tb.term')
                                     ->on('edu_stayweeksdt.idno', '=', 't13tb.idno')
                                     ->on('edu_stayweeksdt.week', '=', DB::raw($week));
                            }) 
                            ->Join('m02tb', 't13tb.idno', '=', 'm02tb.idno')
                            ->leftJoin('edu_floor', 'edu_stayweeksdt.floorno', '=', 'edu_floor.floorno')
                            ->leftJoin('edu_bed', 'edu_stayweeksdt.bedno', '=', 'edu_bed.bedno');

        if($sex == '1'){
            $model->where('m02tb.sex', '=', 'M');
        } else if($sex == '2'){
            $model->where('m02tb.sex', '=', 'F');
        }

        $model->where('t13tb.class', '=', $class);
        $model->where('t13tb.term', '=', $term);
        $model->whereRaw("t13tb.status in (1,2)");

        if($reset == 'N'){
            $model->whereRaw("(edu_stayweeksdt.bedno is null or edu_stayweeksdt.bedno = '')");
        }

        $model->whereRaw('(t13tb.dorm = ? or t13tb.extradorm = ?)', ['Y','Y']);

        $data = $model->get();

        return $data;
    }

    public function resetBed($class,$term,$sex='')
    {
        if($sex == '1'){
            $sex = " and m02tb.sex = 'M'";
        } else if($sex == '2'){
            $sex = " and m02tb.sex = 'F'";
        } else {
            $sex = '';
        }

        $sql = sprintf("update t13tb join m02tb on t13tb.idno = m02tb.idno set t13tb.bedno = null,t13tb.floorno = null,t13tb.bedroom = null where t13tb.class = '%s' and t13tb.term = '%s' %s",$class,$term,$sex);
       
        DB::update($sql);

        return true;
    }

    public function cancelRoomset($class,$term,$sex)
    {
        if($sex == '1'){
            $sex = 'M';
        } else if($sex == '2'){
            $sex = 'F';
        }

        $sql = sprintf("update t13tb join m02tb on t13tb.idno = m02tb.idno set t13tb.bedno = null,t13tb.floorno = null,t13tb.bedroom = null where t13tb.status = '2' and t13tb.class = '%s' and t13tb.term = '%s' and m02tb.sex = '%s'",$class,$term,$sex);
        
        DB::update($sql);

        $sql = sprintf("delete from spareroom where class = '%s' and term = '%s' and sex = '%s'",$class,$term,$sex);
        DB::delete($sql);

        return true;
    }

    public function getClassBedList($class,$term)
    {
        $model = $this->model->selectRaw('floorno,bedroom,bedno');
        $model->where('class','=',$class);
        $model->where('term','=',$term);
        $model->whereRaw("bedno is not null");
        $model->orderByRaw('floorno,bedroom,bedno');

        $data = $model->get();

        return $data;
    }

    public function getDormStudentInfo($class,$term,$hasBed='')
    {
        $model = $this->model->selectRaw('m02tb.cname as student_name,t13tb.floorno,t13tb.bedroom,t13tb.bedno')
                            ->Join('m02tb', 't13tb.idno', '=', 'm02tb.idno');
    
        $model->where('t13tb.class', '=', $class);
        $model->where('t13tb.term', '=', $term);
        
        if($hasBed == 'Y'){
            $model->whereRaw("t13tb.bedno is not null");
        } else if($hasBed == 'N') {
            $model->whereRaw("t13tb.bedno is null");
        }

        $model->whereRaw('(t13tb.dorm = ? or t13tb.extradorm = ?)', ['Y','Y']);

        $data = $model->get();

        return $data;
    }

    public function getBedroomRange($class,$term,$sex)
    {
        $model = $this->model->selectRaw('max(t13tb.bedroom) max_bedroom, min(t13tb.bedroom) min_bedroom, t13tb.floorno')
                            ->Join('m02tb', 't13tb.idno', '=', 'm02tb.idno');

        $model->where('m02tb.sex', '=', $sex);
        $model->where('t13tb.class', '=', $class);
        $model->where('t13tb.term', '=', $term);
        $model->where('t13tb.floorno', '!=', '12');

        $data = $model->get();

        return $data;
    }

    public function resetBedOfPart($class,$term)
    {
        return $this->model->where('class','=',$class)
                           ->where('term','=',$term)
                           ->where('floorno','!=',12)
                           ->update(['floorno' => null,'bedroom' => null,'bedno' => null]);
    }
}