<?php
namespace App\Services;

use App\Repositories\LibraryExportRepository;

class LibraryExportService
{
    /**
     * EffectivenessSurveyService constructor.
     * @param EffectivenessSurveyRepository $effectivenessSurveyRpository
     */
    public function __construct(LibraryExportRepository $libraryexportrepository)
    {
        $this->ler=$libraryexportrepository;
    }


    public function get_regist_sql($class,$term,$sdate,$edate)
    {
        return $this->ler->get_regist_sql($class,$term,$sdate,$edate);
    }

    public function get_csv_sql($sdate,$edate,$control)
    {
        return $this->ler->get_csv_sql($sdate,$edate,$control);
    }

   

}
