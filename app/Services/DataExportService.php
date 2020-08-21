<?php
namespace App\Services;

use App\Repositories\DataExportRepository;

class DataExportService
{
    /**
     * EffectivenessSurveyService constructor.
     * @param EffectivenessSurveyRepository $effectivenessSurveyRpository
     */
    public function __construct(DataExportRepository $dataexportrepository)
    {
        $this->der=$dataexportrepository;
    }

    public function select_class($class_info)
    {
        return $this->der->select_class($class_info);
    }

    public function master_select()
    {
        return $this->der->master_select();
    }

    public function getClassTeacher($class_info,$db_select)
    {
        return $this->der->getClassTeacher($class_info,$db_select);
    }

    public function getAddress($class_info,$db_select)
    {
        return $this->der->getAddress($class_info,$db_select);
    }
    public function getFax($class_info,$db_select,$where)
    {
        return $this->der->getFax($class_info,$db_select,$where);
    }

    public function getClassStudent($class_info,$db_select,$where=null)
    {
        return $this->der->getClassStudent($class_info,$db_select,$where);
    }
    public function getAddressStudent($class_info,$db_select,$where=null)
    {
        return $this->der->getAddressStudent($class_info,$db_select,$where);
    }
    public function getFaxStudent($class_info,$db_select,$where)
    {
        return $this->der->getFaxStudent($class_info,$db_select,$where);
    }

}
