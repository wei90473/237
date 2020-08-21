<?php
namespace App\Services;

use App\Repositories\EntryExportRepository;

class EntryExportService
{
    /**
     * EffectivenessSurveyService constructor.
     * @param EffectivenessSurveyRepository $effectivenessSurveyRpository
     */
    public function __construct(EntryExportRepository $entryexportrepository)
    {
        $this->eer=$entryexportrepository;
    }

    public function select_class($class_info)
    {
        return $this->eer->select_class($class_info);
    }

    public function get_class_info($class_info)
    {
        return $this->eer->get_class_info($class_info);
    }

    public function get_teacher_info($class_info,$sdate,$edate)
    {
        return $this->eer->get_teacher_info($class_info,$sdate,$edate);
    }

    public function lockClassInfo($lock_class)
    {
        return $this->eer->lockClassInfo($lock_class);
    }

    public function get_course_info($sdate,$edate)
    {
        return $this->eer->get_course_info($sdate,$edate);
    }

    public function search($sdate,$edate,$sponsor=null)
    {
        return $this->eer->search($sdate,$edate,$sponsor);
    }

    public function getuser()
    {
        return $this->eer->getuser();
    }

    public function get_regist_sql($class,$term,$sdate,$edate,$control)
    {
        return $this->eer->get_regist_sql($class,$term,$sdate,$edate,$control);
    }

   

}
