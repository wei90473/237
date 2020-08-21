<?php
namespace App\Repositories;

use App\Models\Edu_stayweeksdt;
use App\Repositories\Repository;
use DateTime;
use DB;

class EdustayweeksdtRepository extends Repository
{
    public function __construct(Edu_stayweeksdt $eduStayweeksdt)
    {
        $this->model = $eduStayweeksdt;
    }  

    public function getLongStudentCount($class,$term,$week,$sex,$hasbed='')
    {
        $model = $this->model->selectRaw('count(1) as cnt')
                             ->Join('m02tb', 'edu_stayweeksdt.idno', '=', 'm02tb.idno');

        $model->where('edu_stayweeksdt.class', '=', $class);
        $model->where('edu_stayweeksdt.term', '=', $term);
        $model->where('edu_stayweeksdt.week', '=', $week);

        if(!empty($sex)){
            $model->where('m02tb.sex', '=', $sex);
        }
        
        if(!empty($hasbed)){
            $model->whereRaw("edu_stayweeksdt.bedno is not null");
        }

        $data = $model->get();

        return $data[0]['cnt'];
    }

    public function resetLongBed($class,$term,$week,$sex='')
    {
      if($sex == '1'){
        $sex = " and m02tb.sex = 'M'";
        $sex_spare = 'M';
      } else if($sex == '2'){
        $sex = " and m02tb.sex = 'F'";
        $sex_spare = 'F';
      } else {
        $sex = '';
        $sex_spare = '';
      }

      $sql = sprintf("update edu_stayweeksdt join m02tb on edu_stayweeksdt.idno = m02tb.idno set edu_stayweeksdt.bedno = null,edu_stayweeksdt.floorno = null,edu_stayweeksdt.bedroom = null where edu_stayweeksdt.class = '%s' and edu_stayweeksdt.term = '%s' and edu_stayweeksdt.week = '%s' %s",$class,$term,$week,$sex);

      DB::update($sql);

      $sql = sprintf("delete from spareroom where class = '%s' and term = '%s' and week = '%s' and sex = '%s'",$class,$term,$week,$sex_spare);
      DB::delete($sql);

      return true;
    }

    public function getLongBedroomRange($class,$term,$sex,$week)
    {
      $model = $this->model->selectRaw('max(edu_stayweeksdt.bedroom) max_bedroom, min(edu_stayweeksdt.bedroom) min_bedroom, edu_stayweeksdt.floorno')
                            ->Join('m02tb', 'edu_stayweeksdt.idno', '=', 'm02tb.idno');

      $model->where('m02tb.sex', '=', $sex);
      $model->where('edu_stayweeksdt.class', '=', $class);
      $model->where('edu_stayweeksdt.term', '=', $term);
      $model->where('edu_stayweeksdt.week', '=', $week);
      $model->where('edu_stayweeksdt.floorno', '!=', '12');

      $data = $model->get();

      return $data;
    }

    public function resetLongBedOfPart($class,$term,$week)
    {
      return $this->model->where('class','=',$class)
                         ->where('term','=',$term)
                         ->where('week','=',$week)
                         ->where('floorno','!=',12)
                         ->update(['floorno' => null,'bedroom' => null,'bedno' => null]);
    }
}