<?php
namespace App\Services;

use App\Repositories\T04tbRepository;
use App\Repositories\T13tbRepository;
use App\Repositories\EdubedRepository;
use App\Repositories\T06tbRepository;
use App\Repositories\EdustayweeksRepository;
use App\Repositories\EdustayweeksdtRepository;
use App\Repositories\SpareroomRepository;
use DB;

class RoomSetService
{
    /**
     * PunchService constructor.
     * @param 
     */
    public function __construct(T04tbRepository $t04tbRepository,T13tbRepository $t13tbRepository,EdubedRepository $eduBedRepository,T06tbRepository $t06tbRepository,EdustayweeksRepository $edustayweeksRepository,EdustayweeksdtRepository $edustayweeksdtRepository,SpareroomRepository $spareroomRepository)
    {
        $this->t04tbRepository = $t04tbRepository;
        $this->t13tbRepository = $t13tbRepository;
        $this->eduBedRepository = $eduBedRepository;
        $this->t06tbRepository = $t06tbRepository;
        $this->edustayweeksRepository = $edustayweeksRepository;
        $this->edustayweeksdtRepository = $edustayweeksdtRepository;
        $this->spareroomRepository = $spareroomRepository;
    }

    public function getListForRoomSet($queryData)
    {
        $data = $this->t04tbRepository->getListForRoomSet($queryData);

        return $data;
    }

    public function getStudentCount($class,$term,$sex='',$dorm='',$hasbed='')
    {
        $data = $this->t13tbRepository->getStudentCount($class,$term,$sex,$dorm,$hasbed);

        return $data;
    }

    public function getLongStudentCount($class,$term,$week,$sex='',$hasbed='')
    {
        $data = $this->edustayweeksdtRepository->getLongStudentCount($class,$term,$week,$sex,$hasbed);

        return $data;
    }

    public function getInfoForEditRoomset($class,$term)
    {
        $data = $this->t04tbRepository->getInfoForEditRoomset($class,$term);

        return $data;
    }

    public function updateRoomset($updateData,$updateKey)
    {
        return $this->t04tbRepository->updateOrCreate($updateKey,$updateData);
    }

    public function getDormStudent($class,$term,$sex='',$reset='')
    {
        $data = $this->t13tbRepository->getDormStudent($class,$term,$sex,$reset);

        return $data;
    }

    public function getLongDormStudent($class,$term,$sex,$week,$reset='')
    {
        $data = $this->t13tbRepository->getLongDormStudent($class,$term,$sex,$week,$reset);

        return $data;
    }

    public function updateBedset($updateData,$updateKey)
    {
        return $this->t13tbRepository->updateOrCreate($updateKey,$updateData);
    }

    public function get_emptybed($startdate,$enddate,$buildno,$bedno_from,$bedno_end,$sex,$onlyOne='')
    {
        return $this->eduBedRepository->get_emptybed($startdate,$enddate,$buildno,$bedno_from,$bedno_end,$sex,$onlyOne);
    }

    public function resetBed($class,$term,$sex='')
    {
        return $this->t13tbRepository->resetBed($class,$term,$sex);
    }

    public function resetLongBed($class,$term,$week,$sex='')
    {
        return $this->edustayweeksdtRepository->resetLongBed($class,$term,$week,$sex);
    }

    public function cancelRoomset($class,$term,$sex)
    {
        return $this->t13tbRepository->cancelRoomset($class,$term,$sex);
    }

    public function getLongClass($class,$term)
    {
        return $this->t06tbRepository->getLongClass($class,$term);
    }

    public function updateStayweeks($updateData,$updateKey)
    {
        return $this->edustayweeksRepository->updateOrCreate($updateKey,$updateData);
    }

    public function getStayweeks($class,$term)
    {
        return $this->edustayweeksRepository->getStayweeks($class,$term);
    }

    public function getInfoFromStayweeks($class,$term,$week)
    {
        return $this->edustayweeksRepository->getInfoFromStayweeks($class,$term,$week);
    }

    public function updateLongBedset($updateData,$updateKey)
    {
        return $this->edustayweeksdtRepository->updateOrCreate($updateKey,$updateData);
    }

    public function getDormDate($class,$term)
    {
        return $this->t04tbRepository->getDormDate($class,$term);
    }

    public function getLongDormDateWeek($class,$term)
    {
        return $this->edustayweeksRepository->getLongDormDateWeek($class,$term);
    }

    public function checkSameWeek($startdate,$enddate)
    {
        if(strlen($startdate) == 7 && strlen($enddate) == 7){
            $y=intval(substr($startdate,0,3))+1911;
            $m=intval(substr($startdate,3,2));
            $d=intval(substr($startdate,5,2));
            $toUnixtime=mktime(0,0,0,$m,$d,$y);

            $y=intval(substr($enddate,0,3))+1911;
            $m=intval(substr($enddate,3,2));
            $d=intval(substr($enddate,5,2));
            $toUnixtime2=mktime(0,0,0,$m,$d,$y);

            $afweek = date('w',$toUnixtime2);
            $mintime = $toUnixtime2 - $afweek * 3600*24;
            $maxtime = $toUnixtime2 + (7-$afweek)*3600*24;
            if ($toUnixtime >= $mintime && $toUnixtime <= $maxtime){
                return true;
            }
        }

        return false;
    }

    public function getWeekMonday($date)
    {
        $y=intval(substr($date,0,3))+1911;
        $m=intval(substr($date,3,2));
        $d=intval(substr($date,5,2));
        $toUnixtime=mktime(0,0,0,$m,$d,$y);
        $w=date('w',$toUnixtime);

        if($w==0){
            $time = $toUnixtime + (-6*86400);
            $y=date('Y', $time)-1911;
            $m=date('m', $time);
            $d=date('d', $time);

            $weekmonday = str_pad($y, 3,'0',STR_PAD_LEFT).str_pad($m, 2,'0',STR_PAD_LEFT).str_pad($d, 2,'0',STR_PAD_LEFT);
        } else {
            $j=$w-1;
            $time = $toUnixtime + (($j*-1)*86400);
            $y=date('Y', $time)-1911;
            $m=date('m', $time);
            $d=date('d', $time);

            $weekmonday = str_pad($y, 3,'0',STR_PAD_LEFT).str_pad($m, 2,'0',STR_PAD_LEFT).str_pad($d, 2,'0',STR_PAD_LEFT);
        }

        return $weekmonday;
    }

    public function createWeeks($class,$term,$courseDate)
    {
        return $this->edustayweeksRepository->createWeeks($class,$term,$courseDate);
    }

    public function updateAuto($updateData,$updateKey)
    {
        return $this->t04tbRepository->updateOrCreate($updateKey,$updateData);
    }

    public function updateLongAuto($updateData,$updateKey)
    {
        return $this->edustayweeksRepository->updateOrCreate($updateKey,$updateData);
    }

    public function checkStayDate($class,$term)
    {
        return $this->t04tbRepository->checkStayDate($class,$term);
    }

    public function updateStayDate($updateKey,$updateData)
    {
        return $this->t04tbRepository->updateOrCreate($updateKey,$updateData);
    }

    public function addSpareroom($insertData)
    {
        return $this->spareroomRepository->addSpareroom($insertData);
    }

    public function getBedroomRange($class,$term,$sex)
    {
        return $this->t13tbRepository->getBedroomRange($class,$term,$sex);
    }

    public function getLongBedroomRange($class,$term,$sex,$week)
    {
        return $this->edustayweeksdtRepository->getLongBedroomRange($class,$term,$sex,$week);
    }

    public function resetBedOfPart($class,$term)
    {
        return $this->t13tbRepository->resetBedOfPart($class,$term);
    }

    public function resetLongBedOfPart($class,$term,$week)
    {
        return $this->edustayweeksdtRepository->resetLongBedOfPart($class,$term,$week);
    }

    public function getSpareroom($class,$term,$sex,$week='')
    {
        return $this->spareroomRepository->getSpareroom($class,$term,$sex,$week);
    }

    public function getSpareroomAll($class,$term,$sex,$week='')
    {
        return $this->spareroomRepository->getSpareroomAll($class,$term,$sex,$week);
    }

    public function getAutoType($class,$term)
    {
        return $this->t04tbRepository->getAutoType($class,$term);
    }

    public function getLongAutoType($class,$term,$week)
    {
        return $this->edustayweeksRepository->getLongAutoType($class,$term,$week);
    }

    public function autoSetAgain($class,$term,$staystartdate,$stayenddate,$longclass,$week){
        if($longclass == 'N'){
            $auto_type = $this->getAutoType($class,$term)->toArray();
            $maleBedroomRange = $this->getBedroomRange($class,$term,'M')->toArray();
            $femaleBedroomRange = $this->getBedroomRange($class,$term,'F')->toArray();
            if(count($maleBedroomRange[0]) == 3 && count($femaleBedroomRange[0]) == 3){
                $this->resetBedOfPart($class,$term);
                $notBedMaleStudent = $this->getDormStudent($class,$term,'1','N')->toArray();
                $notBedFemaleStudent = $this->getDormStudent($class,$term,'2','N')->toArray();
                $updateKey = array();
                $updateData = array();
                if(count($notBedMaleStudent) > 0){
                    if($auto_type[0]['auto_type'] == 'S'){
                        $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,$maleBedroomRange[0]['floorno'],$maleBedroomRange[0]['min_bedroom'],$maleBedroomRange[0]['max_bedroom'],'M','Y');

                        foreach ($emptyMaleBed as $key => $value) {
                            if (count($notBedMaleStudent)==0) {
                                break;
                            }
                            $signup=array_shift($notBedMaleStudent); 

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $class;
                            $updateKey['term'] = $term;
                            $updateData['bedno'] = $value->bedno;
                            $updateData['bedroom'] = $value->bedroom;
                            $updateData['floorno'] = $value->floorno;

                            $this->updateBedset($updateData,$updateKey);
                        }

                        if(count($notBedMaleStudent) > 0){
                            $emptySpareroom = $this->getSpareroom($class,$term,'M');

                            foreach ($emptySpareroom as $key => $value) {
                                if (count($notBedMaleStudent)==0) {
                                    break;
                                }
                                $signup=array_shift($notBedMaleStudent); 

                                if($signup['handicap'] == 'Y'){
                                    continue;
                                }

                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $class;
                                $updateKey['term'] = $term;
                                $updateData['bedno'] = $value->bedno;
                                $updateData['bedroom'] = $value->bedroom;
                                $updateData['floorno'] = $value->floorno;

                                $this->updateBedset($updateData,$updateKey);
                            }
                        }
                    } else if($auto_type[0]['auto_type'] == 'O'){
                        $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,$maleBedroomRange[0]['floorno'],$maleBedroomRange[0]['min_bedroom'],$maleBedroomRange[0]['max_bedroom'],'M');

                        foreach ($emptyMaleBed as $key => $value) {
                            if (count($notBedMaleStudent)==0) {
                                break;
                            }
                            $signup=array_shift($notBedMaleStudent); 

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $class;
                            $updateKey['term'] = $term;
                            $updateData['bedno'] = $value->bedno;
                            $updateData['bedroom'] = $value->bedroom;
                            $updateData['floorno'] = $value->floorno;

                            $this->updateBedset($updateData,$updateKey);
                        }

                        if(count($notBedMaleStudent) > 0){
                            $emptySpareroom = $this->getSpareroomAll($class,$term,'M');

                            foreach ($emptySpareroom as $key => $value) {
                                if (count($notBedMaleStudent)==0) {
                                    break;
                                }
                                $signup=array_shift($notBedMaleStudent); 

                                if($signup['handicap'] == 'Y'){
                                    continue;
                                }

                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $class;
                                $updateKey['term'] = $term;
                                $updateData['bedno'] = $value->bedno;
                                $updateData['bedroom'] = $value->bedroom;
                                $updateData['floorno'] = $value->floorno;

                                $this->updateBedset($updateData,$updateKey);
                            }
                        }
                    }
                }

                if(count($notBedFemaleStudent) > 0){
                    if($auto_type[0]['auto_type'] == 'S'){
                        $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,$femaleBedroomRange[0]['floorno'],$femaleBedroomRange[0]['min_bedroom'],$femaleBedroomRange[0]['max_bedroom'],'F','Y');

                        foreach ($emptyFemaleBed as $key => $value) {
                            if (count($notBedFemaleStudent)==0) {
                                break;
                            }
                            $signup=array_shift($notBedFemaleStudent); 

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $class;
                            $updateKey['term'] = $term;
                            $updateData['bedno'] = $value->bedno;
                            $updateData['bedroom'] = $value->bedroom;
                            $updateData['floorno'] = $value->floorno;

                            $this->updateBedset($updateData,$updateKey);
                        }

                        if(count($notBedFemaleStudent) > 0){
                            $emptySpareroom = $this->getSpareroom($class,$term,'F');

                            foreach ($emptySpareroom as $key => $value) {
                                if (count($notBedFemaleStudent)==0) {
                                    break;
                                }
                                $signup=array_shift($notBedFemaleStudent); 

                                if($signup['handicap'] == 'Y'){
                                    continue;
                                }

                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $class;
                                $updateKey['term'] = $term;
                                $updateData['bedno'] = $value->bedno;
                                $updateData['bedroom'] = $value->bedroom;
                                $updateData['floorno'] = $value->floorno;

                                $this->updateBedset($updateData,$updateKey);
                            }
                        }
                    } else if($auto_type[0]['auto_type'] == 'O'){
                        $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,$femaleBedroomRange[0]['floorno'],$femaleBedroomRange[0]['min_bedroom'],$femaleBedroomRange[0]['max_bedroom'],'F');

                        foreach ($emptyFemaleBed as $key => $value) {
                            if (count($notBedFemaleStudent)==0) {
                                break;
                            }
                            $signup=array_shift($notBedFemaleStudent); 

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $class;
                            $updateKey['term'] = $term;
                            $updateData['bedno'] = $value->bedno;
                            $updateData['bedroom'] = $value->bedroom;
                            $updateData['floorno'] = $value->floorno;

                            $this->updateBedset($updateData,$updateKey);
                        }

                        if(count($notBedFemaleStudent) > 0){
                            $emptySpareroom = $this->getSpareroomAll($class,$term,'F');

                            foreach ($emptySpareroom as $key => $value) {
                                if (count($notBedFemaleStudent)==0) {
                                    break;
                                }
                                $signup=array_shift($notBedFemaleStudent); 

                                if($signup['handicap'] == 'Y'){
                                    continue;
                                }

                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $class;
                                $updateKey['term'] = $term;
                                $updateData['bedno'] = $value->bedno;
                                $updateData['bedroom'] = $value->bedroom;
                                $updateData['floorno'] = $value->floorno;

                                $this->updateBedset($updateData,$updateKey);
                            }
                        }
                    }
                }
            } else {
                return false;
            }
        } else if($longclass == 'Y'){
            $auto_type = $this->getLongAutoType($class,$term,$week)->toArray();
            $maleBedroomRange = $this->getLongBedroomRange($class,$term,'M',$week)->toArray();
            $femaleBedroomRange = $this->getLongBedroomRange($class,$term,'F',$week)->toArray();
            if(count($maleBedroomRange[0]) == 3 && count($femaleBedroomRange[0]) == 3){
                $this->resetLongBedOfPart($class,$term,$week);
                $notBedMaleStudent = $this->getLongDormStudent($class,$term,'1',$week,'N')->toArray();
                $notBedFemaleStudent = $this->getLongDormStudent($class,$term,'2',$week,'N')->toArray();
                $updateKey = array();
                $updateData = array();
                if(count($notBedMaleStudent) > 0){
                    if($auto_type[0]['auto_type'] == 'S'){
                        $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,$maleBedroomRange[0]['floorno'],$maleBedroomRange[0]['min_bedroom'],$maleBedroomRange[0]['max_bedroom'],'M','Y');

                        foreach ($emptyMaleBed as $key => $value) {
                            if (count($notBedMaleStudent)==0) {
                                break;
                            }
                            $signup=array_shift($notBedMaleStudent); 

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $class;
                            $updateKey['term'] = $term;
                            $updateKey['week'] = $week;
                            $updateData['bedno'] = $value->bedno;
                            $updateData['bedroom'] = $value->bedroom;
                            $updateData['floorno'] = $value->floorno;

                            $this->updateLongBedset($updateData,$updateKey);
                        }

                        if(count($notBedMaleStudent) > 0){
                            $emptySpareroom = $this->getSpareroom($class,$term,'M',$week);

                            foreach ($emptySpareroom as $key => $value) {
                                if (count($notBedMaleStudent)==0) {
                                    break;
                                }
                                $signup=array_shift($notBedMaleStudent); 

                                if($signup['handicap'] == 'Y'){
                                    continue;
                                }

                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $class;
                                $updateKey['term'] = $term;
                                $updateKey['week'] = $week;
                                $updateData['bedno'] = $value->bedno;
                                $updateData['bedroom'] = $value->bedroom;
                                $updateData['floorno'] = $value->floorno;

                                $this->updateLongBedset($updateData,$updateKey);
                            }
                        }
                    } else if($auto_type[0]['auto_type'] == 'O'){
                        $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,$maleBedroomRange[0]['floorno'],$maleBedroomRange[0]['min_bedroom'],$maleBedroomRange[0]['max_bedroom'],'M');

                        foreach ($emptyMaleBed as $key => $value) {
                            if (count($notBedMaleStudent)==0) {
                                break;
                            }
                            $signup=array_shift($notBedMaleStudent); 

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $class;
                            $updateKey['term'] = $term;
                            $updateKey['week'] = $week;
                            $updateData['bedno'] = $value->bedno;
                            $updateData['bedroom'] = $value->bedroom;
                            $updateData['floorno'] = $value->floorno;

                            $this->updateLongBedset($updateData,$updateKey);
                        }

                        if(count($notBedMaleStudent) > 0){
                            $emptySpareroom = $this->getSpareroomAll($class,$term,'M',$week);

                            foreach ($emptySpareroom as $key => $value) {
                                if (count($notBedMaleStudent)==0) {
                                    break;
                                }
                                $signup=array_shift($notBedMaleStudent); 

                                if($signup['handicap'] == 'Y'){
                                    continue;
                                }

                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $class;
                                $updateKey['term'] = $term;
                                $updateKey['week'] = $week;
                                $updateData['bedno'] = $value->bedno;
                                $updateData['bedroom'] = $value->bedroom;
                                $updateData['floorno'] = $value->floorno;

                                $this->updateLongBedset($updateData,$updateKey);
                            }
                        }
                    }
                }

                if(count($notBedFemaleStudent) > 0){
                    if($auto_type[0]['auto_type'] == 'S'){
                        $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,$femaleBedroomRange[0]['floorno'],$femaleBedroomRange[0]['min_bedroom'],$femaleBedroomRange[0]['max_bedroom'],'F','Y');

                        foreach ($emptyFemaleBed as $key => $value) {
                            if (count($notBedFemaleStudent)==0) {
                                break;
                            }
                            $signup=array_shift($notBedFemaleStudent); 

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $class;
                            $updateKey['term'] = $term;
                            $updateKey['week'] = $week;
                            $updateData['bedno'] = $value->bedno;
                            $updateData['bedroom'] = $value->bedroom;
                            $updateData['floorno'] = $value->floorno;

                            $this->updateLongBedset($updateData,$updateKey);
                        }

                        if(count($notBedFemaleStudent) > 0){
                            $emptySpareroom = $this->getSpareroom($class,$term,'F',$week);

                            foreach ($emptySpareroom as $key => $value) {
                                if (count($notBedFemaleStudent)==0) {
                                    break;
                                }
                                $signup=array_shift($notBedFemaleStudent); 

                                if($signup['handicap'] == 'Y'){
                                    continue;
                                }

                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $class;
                                $updateKey['term'] = $term;
                                $updateKey['week'] = $week;
                                $updateData['bedno'] = $value->bedno;
                                $updateData['bedroom'] = $value->bedroom;
                                $updateData['floorno'] = $value->floorno;

                                $this->updateLongBedset($updateData,$updateKey);
                            }
                        }
                    } else if($auto_type[0]['auto_type'] == 'O'){
                        $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,$femaleBedroomRange[0]['floorno'],$femaleBedroomRange[0]['min_bedroom'],$femaleBedroomRange[0]['max_bedroom'],'F');

                        foreach ($emptyFemaleBed as $key => $value) {
                            if (count($notBedFemaleStudent)==0) {
                                break;
                            }
                            $signup=array_shift($notBedFemaleStudent); 

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $class;
                            $updateKey['term'] = $term;
                            $updateKey['week'] = $week;
                            $updateData['bedno'] = $value->bedno;
                            $updateData['bedroom'] = $value->bedroom;
                            $updateData['floorno'] = $value->floorno;

                            $this->updateLongBedset($updateData,$updateKey);
                        }

                        if(count($notBedFemaleStudent) > 0){
                            $emptySpareroom = $this->getSpareroomAll($class,$term,'F',$week);

                            foreach ($emptySpareroom as $key => $value) {
                                if (count($notBedFemaleStudent)==0) {
                                    break;
                                }
                                $signup=array_shift($notBedFemaleStudent); 

                                if($signup['handicap'] == 'Y'){
                                    continue;
                                }

                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $class;
                                $updateKey['term'] = $term;
                                $updateKey['week'] = $week;
                                $updateData['bedno'] = $value->bedno;
                                $updateData['bedroom'] = $value->bedroom;
                                $updateData['floorno'] = $value->floorno;

                                $this->updateLongBedset($updateData,$updateKey);
                            }
                        }
                    }
                }
            } else {
                return false;
            }
        }

        return true;
    }

    public function specificProcess($specific,$courseStartDate,$courseEndDate)
    {
        foreach ($specific as $key => $value) {
            $checkStayDate = $this->checkStayDate($value['class'],$value['term']);
            if(!empty($checkStayDate)){
                $updateKey = array();
                $updateData = array();
                $updateKey['class'] = $value['class'];
                $updateKey['term'] = $value['term'];
                $updateData['staystartdate'] = $checkStayDate[0]->sdate;
                $updateData['stayenddate'] = $checkStayDate[0]->edate;
                $updateData['staystarttime'] = '1';
                $updateData['stayendtime'] = '1';

                $this->updateStayDate($updateKey,$updateData);
                unset($updateKey);
                unset($updateData);
            }
            $updateKey = array();
            $updateData = array();
            if($value['longclass'] == 'N'){
                $maleEndStatus = false;
                $femaleEndStatus = false;
                $this->resetBed($value['class'],$value['term']);
                $maleStudent = $this->getDormStudent($value['class'],$value['term'],'1')->toArray();
                $femaleStudent = $this->getDormStudent($value['class'],$value['term'],'2')->toArray();
                $dormDate = $this->getDormDate($value['class'],$value['term'])->toArray();

                if(!empty($dormDate[0]['staystartdate']) && !empty($dormDate[0]['stayenddate'])){
                    $staystartdate = $dormDate[0]['staystartdate'];
                    $stayenddate = $dormDate[0]['stayenddate'];
                } else {
                    $staystartdate = $dormDate[0]['sdate'];
                    $stayenddate = $dormDate[0]['edate'];
                }
                
                $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2251','2266','');
                $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2271','2287','');

                foreach ($emptyMaleBed as $key2 => $value2) {
                    if (count($maleStudent)==0) {
                        $maleEndStatus = true;
                        break;
                    }
                    $signup=array_shift($maleStudent);

                    //身障人員跳過
                    if($signup['handicap'] == 'Y'){
                        continue;
                    }

                    $updateKey['idno'] = $signup['idno'];
                    $updateKey['class'] = $value['class'];
                    $updateKey['term'] = $value['term'];
                    $updateData['bedno'] = $value2->bedno;
                    $updateData['bedroom'] = $value2->bedroom;
                    $updateData['floorno'] = $value2->floorno;

                    $this->updateBedset($updateData,$updateKey);
                }

                foreach ($emptyFemaleBed as $key2 => $value2) {
                    if (count($femaleStudent)==0) {
                        $femaleEndStatus = true;
                        break;
                    }
                    $signup=array_shift($femaleStudent); 

                    //身障人員跳過
                    if($signup['handicap'] == 'Y'){
                        continue;
                    }

                    $updateKey['idno'] = $signup['idno'];
                    $updateKey['class'] = $value['class'];
                    $updateKey['term'] = $value['term'];
                    $updateData['bedno'] = $value2->bedno;
                    $updateData['bedroom'] = $value2->bedroom;
                    $updateData['floorno'] = $value2->floorno;

                    $this->updateBedset($updateData,$updateKey);
                }

                while(!$maleEndStatus){
                    $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2271','2287','');
                    if(count($emptyFemaleBed) > 0){
                        krsort($emptyFemaleBed);
                        foreach ($emptyFemaleBed as $key2 => $value2) {
                            if (count($maleStudent)==0) {
                                $maleEndStatus = true;
                                break;
                            }
                            $signup=array_shift($maleStudent); 

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $value['class'];
                            $updateKey['term'] = $value['term'];
                            $updateData['bedno'] = $value2->bedno;
                            $updateData['bedroom'] = $value2->bedroom;
                            $updateData['floorno'] = $value2->floorno;

                            $this->updateBedset($updateData,$updateKey);
                        }
                    } else {
                        $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'11','','','','Y');
                        if(count($emptyMaleBed) > 0){
                            foreach ($emptyMaleBed as $key2 => $value2) {
                                if (count($maleStudent)==0) {
                                    $maleEndStatus = true;
                                    break;
                                }
                                $signup=array_shift($maleStudent); 

                                if($signup['handicap'] == 'Y'){
                                    continue;
                                }

                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $value['class'];
                                $updateKey['term'] = $value['term'];
                                $updateData['bedno'] = $value2->bedno;
                                $updateData['bedroom'] = $value2->bedroom;
                                $updateData['floorno'] = $value2->floorno;

                                $this->updateBedset($updateData,$updateKey);
                            }
                        } else {
                            $maleEndStatus = true;
                        }
                    }
                }

                while(!$femaleEndStatus){
                    $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2251','2266','');
                    if(count($emptyMaleBed) > 0){
                        krsort($emptyMaleBed);
                        foreach ($emptyMaleBed as $key2 => $value2) {
                            if (count($femaleStudent)==0) {
                                $femaleEndStatus = true;
                                break;
                            }
                            $signup=array_shift($femaleStudent); 
                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $value['class'];
                            $updateKey['term'] = $value['term'];
                            $updateData['bedno'] = $value2->bedno;
                            $updateData['bedroom'] = $value2->bedroom;
                            $updateData['floorno'] = $value2->floorno;

                            $this->updateBedset($updateData,$updateKey);
                        }
                    } else {
                        $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'79','','','','Y');
                        if(count($emptyFemaleBed) > 0){
                            foreach ($emptyFemaleBed as $key2 => $value2) {
                                if (count($femaleStudent)==0) {
                                    $femaleEndStatus = true;
                                    break;
                                }
                                $signup=array_shift($femaleStudent); 
                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $value['class'];
                                $updateKey['term'] = $value['term'];
                                $updateData['bedno'] = $value2->bedno;
                                $updateData['bedroom'] = $value2->bedroom;
                                $updateData['floorno'] = $value2->floorno;

                                $this->updateBedset($updateData,$updateKey);
                            }
                        } else {
                            $femaleEndStatus = true;
                        }
                    }
                }


                $updateAutoKey = array();
                $updateAutoData = array();
                $updateAutoKey['class'] = $value['class'];
                $updateAutoKey['term'] = $value['term'];
                $updateAutoData['auto'] = 'Y';
                $updateAutoData['auto_type'] = 'S';
                $this->updateAuto($updateAutoData,$updateAutoKey);

                if($maleEndStatus){
                    $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'11','','','','Y');
                    for($i=1;$i<=2;$i++){
                        if(count($emptyMaleBed) > 0){
                            $insertData = array();
                            $spare = array_shift($emptyMaleBed); 

                            $insertData['class'] = $value['class'];
                            $insertData['term'] = $value['term'];
                            $insertData['staystartdate'] = $staystartdate;
                            $insertData['stayenddate'] = $stayenddate;
                            $insertData['week'] = null;
                            $insertData['sex'] = 'M';
                            $insertData['floorno'] = $spare->floorno;
                            $insertData['bedroom'] = $spare->bedroom;
                            $insertData['bedno'] = $spare->bedno;

                            $this->addSpareroom($insertData);
                        }
                    }
                }

                if($femaleEndStatus){
                    $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'79','','','','Y');
                    for($i=1;$i<=2;$i++){
                        if(count($emptyFemaleBed) > 0){
                            $insertData = array();
                            $spare = array_shift($emptyFemaleBed); 

                            $insertData['class'] = $value['class'];
                            $insertData['term'] = $value['term'];
                            $insertData['staystartdate'] = $staystartdate;
                            $insertData['stayenddate'] = $stayenddate;
                            $insertData['week'] = null;
                            $insertData['sex'] = 'F';
                            $insertData['floorno'] = $spare->floorno;
                            $insertData['bedroom'] = $spare->bedroom;
                            $insertData['bedno'] = $spare->bedno;

                            $this->addSpareroom($insertData);
                        }
                    }
                }
            } else if($value['longclass'] == 'Y'){
                $longDormDateWeek = $this->getLongDormDateWeek($value['class'],$value['term'])->toArray();

                if(empty($longDormDateWeek)){
                    $courseDate = $this->getLongClass($value['class'],$value['term'])->toArray();
                    $createWeeks = $this->createWeeks($value['class'],$value['term'],$courseDate);
                }

                $week = 0;
                $weekMondayQuery = $this->getWeekMonday($courseStartDate['courseStartDate']);
                foreach ($longDormDateWeek as $key2 => $value2) {
                    $weekMonday = $this->getWeekMonday($value2['staystartdate']);

                    if($weekMondayQuery == $weekMonday){
                        $staystartdate = $value2['staystartdate'];
                        $stayenddate = $value2['stayenddate'];
                        $week = $value2['week'];
                        break;
                    }
                }

                if($week > 0){
                    $maleEndStatus = false;
                    $femaleEndStatus = false;
                    $this->resetLongBed($value['class'],$value['term'],$week);
                    $maleStudent = $this->getLongDormStudent($value['class'],$value['term'],'1',$week)->toArray();
                    $femaleStudent = $this->getLongDormStudent($value['class'],$value['term'],'2',$week)->toArray();
                    
                    $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2251','2266','');
                    $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2271','2287','');

                    foreach ($emptyMaleBed as $key2 => $value2) {
                        if (count($maleStudent)==0) {
                            $maleEndStatus = true;
                            break;
                        }
                        $signup=array_shift($maleStudent);

                        if($signup['handicap'] == 'Y'){
                            continue;
                        }

                        $updateKey['idno'] = $signup['idno'];
                        $updateKey['class'] = $value['class'];
                        $updateKey['term'] = $value['term'];
                        $updateKey['week'] = $week;
                        $updateData['bedno'] = $value2->bedno;
                        $updateData['bedroom'] = $value2->bedroom;
                        $updateData['floorno'] = $value2->floorno;

                        $this->updateLongBedset($updateData,$updateKey);
                    }

                    foreach ($emptyFemaleBed as $key2 => $value2) {
                        if (count($femaleStudent)==0) {
                            $femaleEndStatus = true;
                            break;
                        }
                        $signup=array_shift($femaleStudent); 

                        if($signup['handicap'] == 'Y'){
                            continue;
                        }

                        $updateKey['idno'] = $signup['idno'];
                        $updateKey['class'] = $value['class'];
                        $updateKey['term'] = $value['term'];
                        $updateKey['week'] = $week;
                        $updateData['bedno'] = $value2->bedno;
                        $updateData['bedroom'] = $value2->bedroom;
                        $updateData['floorno'] = $value2->floorno;

                        $this->updateLongBedset($updateData,$updateKey);
                    }

                    while(!$maleEndStatus){
                        $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2271','2287','');
                        if(count($emptyFemaleBed) > 0){
                            krsort($emptyFemaleBed);
                            foreach ($emptyFemaleBed as $key2 => $value2) {
                                if (count($maleStudent)==0) {
                                    $maleEndStatus = true;
                                    break;
                                }
                                $signup=array_shift($maleStudent); 

                                if($signup['handicap'] == 'Y'){
                                    continue;
                                }

                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $value['class'];
                                $updateKey['term'] = $value['term'];
                                $updateKey['week'] = $week;
                                $updateData['bedno'] = $value2->bedno;
                                $updateData['bedroom'] = $value2->bedroom;
                                $updateData['floorno'] = $value2->floorno;

                                $this->updateLongBedset($updateData,$updateKey);
                            }
                        } else {
                            $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'11','','','','Y');
                            if(count($emptyMaleBed) > 0){
                                foreach ($emptyMaleBed as $key2 => $value2) {
                                    if (count($maleStudent)==0) {
                                        $maleEndStatus = true;
                                        break;
                                    }
                                    $signup=array_shift($maleStudent); 

                                    if($signup['handicap'] == 'Y'){
                                        continue;
                                    }

                                    $updateKey['idno'] = $signup['idno'];
                                    $updateKey['class'] = $value['class'];
                                    $updateKey['term'] = $value['term'];
                                    $updateKey['week'] = $week;
                                    $updateData['bedno'] = $value2->bedno;
                                    $updateData['bedroom'] = $value2->bedroom;
                                    $updateData['floorno'] = $value2->floorno;

                                    $this->updateLongBedset($updateData,$updateKey);
                                }
                            } else {
                                $maleEndStatus = true;
                            }
                        }
                    }

                    while(!$femaleEndStatus){
                        $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2251','2266','');
                        if(count($emptyMaleBed) > 0){
                            krsort($emptyMaleBed);
                            foreach ($emptyMaleBed as $key2 => $value2) {
                                if (count($femaleStudent)==0) {
                                    $femaleEndStatus = true;
                                    break;
                                }
                                $signup=array_shift($femaleStudent); 
                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $value['class'];
                                $updateKey['term'] = $value['term'];
                                $updateKey['week'] = $week;
                                $updateData['bedno'] = $value2->bedno;
                                $updateData['bedroom'] = $value2->bedroom;
                                $updateData['floorno'] = $value2->floorno;

                                $this->updateLongBedset($updateData,$updateKey);
                            }
                        } else {
                            $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'79','','','','Y');
                            if(count($emptyFemaleBed) > 0){
                                foreach ($emptyFemaleBed as $key2 => $value2) {
                                    if (count($femaleStudent)==0) {
                                        $femaleEndStatus = true;
                                        break;
                                    }
                                    $signup=array_shift($femaleStudent); 
                                    $updateKey['idno'] = $signup['idno'];
                                    $updateKey['class'] = $value['class'];
                                    $updateKey['term'] = $value['term'];
                                    $updateKey['week'] = $week;
                                    $updateData['bedno'] = $value2->bedno;
                                    $updateData['bedroom'] = $value2->bedroom;
                                    $updateData['floorno'] = $value2->floorno;

                                    $this->updateLongBedset($updateData,$updateKey);
                                }
                            } else {
                                $femaleEndStatus = true;
                            }
                        }
                    }

                    $updateAutoKey = array();
                    $updateAutoData = array();
                    $updateAutoKey['class'] = $value['class'];
                    $updateAutoKey['term'] = $value['term'];
                    $updateAutoData['auto'] = 'Y';
                    $updateAutoData['auto_type'] = 'S';
                    $this->updateAuto($updateAutoData,$updateAutoKey);

                    $updateAutoKey = array();
                    $updateAutoData = array();
                    $updateAutoKey['class'] = $value['class'];
                    $updateAutoKey['term'] = $value['term'];
                    $updateAutoKey['week'] = $week;
                    $updateAutoData['auto'] = 'Y';
                    $updateAutoData['auto_type'] = 'S';
                    $this->updateLongAuto($updateAutoData,$updateAutoKey);

                    if($maleEndStatus){
                        $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'11','','','','Y');
                        for($i=1;$i<=2;$i++){
                            if(count($emptyMaleBed) > 0){
                                $insertData = array();
                                $spare = array_shift($emptyMaleBed); 

                                $insertData['class'] = $value['class'];
                                $insertData['term'] = $value['term'];
                                $insertData['staystartdate'] = $staystartdate;
                                $insertData['stayenddate'] = $stayenddate;
                                $insertData['week'] = $week;
                                $insertData['sex'] = 'M';
                                $insertData['floorno'] = $spare->floorno;
                                $insertData['bedroom'] = $spare->bedroom;
                                $insertData['bedno'] = $spare->bedno;

                                $this->addSpareroom($insertData);
                            }
                        }
                    }

                    if($femaleEndStatus){
                        $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'79','','','','Y');
                        for($i=1;$i<=2;$i++){
                            if(count($emptyFemaleBed) > 0){
                                $insertData = array();
                                $spare = array_shift($emptyFemaleBed); 

                                $insertData['class'] = $value['class'];
                                $insertData['term'] = $value['term'];
                                $insertData['staystartdate'] = $staystartdate;
                                $insertData['stayenddate'] = $stayenddate;
                                $insertData['week'] = $week;
                                $insertData['sex'] = 'F';
                                $insertData['floorno'] = $spare->floorno;
                                $insertData['bedroom'] = $spare->bedroom;
                                $insertData['bedno'] = $spare->bedno;

                                $this->addSpareroom($insertData);
                            }
                        }
                    }
                }
            }
        }
    }

    public function otherProcess($other,$courseStartDate,$courseEndDate)
    {
        foreach ($other as $key => $value) {
            $checkStayDate = $this->checkStayDate($value['class'],$value['term']);
            if(!empty($checkStayDate)){
                $updateKey = array();
                $updateData = array();
                $updateKey['class'] = $value['class'];
                $updateKey['term'] = $value['term'];
                $updateData['staystartdate'] = $checkStayDate[0]->sdate;
                $updateData['stayenddate'] = $checkStayDate[0]->edate;
                $updateData['staystarttime'] = '1';
                $updateData['stayendtime'] = '1';

                $this->updateStayDate($updateKey,$updateData);
                unset($updateKey);
                unset($updateData);
            }
            $updateKey = array();
            $updateData = array();
            if($value['longclass'] == 'N'){
                $maleSupervisorEndStatus = false;
                $femaleSupervisorEndStatus = false;
                $maleEndStatus = false;
                $femaleEndStatus = false;

                $this->resetBed($value['class'],$value['term']);

                $maleSupervisor = $this->t13tbRepository->getDormSupervisorStudent($value['class'],$value['term'],'M')->toArray();
                $femaleSupervisor = $this->t13tbRepository->getDormSupervisorStudent($value['class'],$value['term'],'F')->toArray();

                $dormDate = $this->getDormDate($value['class'],$value['term'])->toArray();

                if(!empty($dormDate[0]['staystartdate']) && !empty($dormDate[0]['stayenddate'])){
                    $staystartdate = $dormDate[0]['staystartdate'];
                    $stayenddate = $dormDate[0]['stayenddate'];
                } else {
                    $staystartdate = $dormDate[0]['sdate'];
                    $stayenddate = $dormDate[0]['edate'];
                }

                $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2251','2266','');
                $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2271','2287','');

                foreach ($emptyMaleBed as $key2 => $value2) {
                    if (count($maleSupervisor)==0) {
                        $maleSupervisorEndStatus = true;
                        break;
                    }
                    $signup=array_shift($maleSupervisor);

                    if($signup['handicap'] == 'Y'){
                        continue;
                    }

                    $updateKey['idno'] = $signup['idno'];
                    $updateKey['class'] = $value['class'];
                    $updateKey['term'] = $value['term'];
                    $updateData['bedno'] = $value2->bedno;
                    $updateData['bedroom'] = $value2->bedroom;
                    $updateData['floorno'] = $value2->floorno;

                    $this->updateBedset($updateData,$updateKey);
                }

                foreach ($emptyFemaleBed as $key2 => $value2) {
                    if (count($femaleSupervisor)==0) {
                        $femaleSupervisorEndStatus = true;
                        break;
                    }
                    $signup=array_shift($femaleSupervisor); 

                    if($signup['handicap'] == 'Y'){
                        continue;
                    }

                    $updateKey['idno'] = $signup['idno'];
                    $updateKey['class'] = $value['class'];
                    $updateKey['term'] = $value['term'];
                    $updateData['bedno'] = $value2->bedno;
                    $updateData['bedroom'] = $value2->bedroom;
                    $updateData['floorno'] = $value2->floorno;

                    $this->updateBedset($updateData,$updateKey);
                }

                while(!$maleSupervisorEndStatus){
                    $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2271','2287','');
                    if(count($emptyFemaleBed) > 0){
                        krsort($emptyFemaleBed);
                        foreach ($emptyFemaleBed as $key2 => $value2) {
                            if (count($maleSupervisor)==0) {
                                $maleSupervisorEndStatus = true;
                                break;
                            }
                            $signup=array_shift($maleSupervisor); 

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $value['class'];
                            $updateKey['term'] = $value['term'];
                            $updateData['bedno'] = $value2->bedno;
                            $updateData['bedroom'] = $value2->bedroom;
                            $updateData['floorno'] = $value2->floorno;

                            $this->updateBedset($updateData,$updateKey);
                        }
                    } else {
                        $maleSupervisorEndStatus = true;
                    }
                }

                $maleStudent = $this->getDormStudent($value['class'],$value['term'],'1','N')->toArray();
                $femaleStudent = $this->getDormStudent($value['class'],$value['term'],'2','N')->toArray();

                $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'11','','','M');
                $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'79','','','F');

                if(count($maleStudent) > count($emptyMaleBed) || count($femaleStudent) > count($emptyFemaleBed)){
                    $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'13','2302','2318','F');

                    $emptyFemaleBedTwo = array();
                    $emptyFemaleBedThree = array();
                    foreach ($emptyFemaleBed as $key2 => $value2) {
                        $bedroomCount = $this->getBedroomCount($value2->floorno,$value2->bedroom);

                        if($bedroomCount == 2){
                            array_push($emptyFemaleBedTwo, $value2);
                        } else if($bedroomCount == 3){
                            array_push($emptyFemaleBedThree, $value2);
                        }
                    }

                    foreach ($emptyFemaleBedTwo as $key2 => $value2) {
                        if (count($femaleStudent)==0) {
                            $femaleEndStatus = true;
                            break;
                        }

                        $signup=array_shift($femaleStudent);

                        if($signup['handicap'] == 'Y'){
                            continue;
                        }

                        $updateKey['idno'] = $signup['idno'];
                        $updateKey['class'] = $value['class'];
                        $updateKey['term'] = $value['term'];
                        $updateData['bedno'] = $value2->bedno;
                        $updateData['bedroom'] = $value2->bedroom;
                        $updateData['floorno'] = $value2->floorno;

                        $this->updateBedset($updateData,$updateKey);
                    }

                    if(!$femaleEndStatus){
                        foreach ($emptyFemaleBedThree as $key2 => $value2) {
                            if (count($femaleStudent)==0) {
                                $femaleEndStatus = true;
                                break;
                            }

                            $signup=array_shift($femaleStudent);

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $value['class'];
                            $updateKey['term'] = $value['term'];
                            $updateData['bedno'] = $value2->bedno;
                            $updateData['bedroom'] = $value2->bedroom;
                            $updateData['floorno'] = $value2->floorno;

                            $this->updateBedset($updateData,$updateKey);
                        }
                    }

                    $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'13','2302','2318','M');
                
                    $emptyMaleBedTwo = array();
                    $emptyMaleBedThree = array();
                    foreach ($emptyMaleBed as $key2 => $value2) {
                        $bedroomCount = $this->getBedroomCount($value2->floorno,$value2->bedroom);

                        if($bedroomCount == 2){
                            array_push($emptyMaleBedTwo, $value2);
                        } else if($bedroomCount == 3){
                            array_push($emptyMaleBedThree, $value2);
                        }
                    }

                    foreach ($emptyMaleBedTwo as $key2 => $value2) {
                        if (count($maleStudent)==0) {
                            $maleEndStatus = true;
                            break;
                        }

                        $signup=array_shift($maleStudent);

                        if($signup['handicap'] == 'Y'){
                            continue;
                        }

                        $updateKey['idno'] = $signup['idno'];
                        $updateKey['class'] = $value['class'];
                        $updateKey['term'] = $value['term'];
                        $updateData['bedno'] = $value2->bedno;
                        $updateData['bedroom'] = $value2->bedroom;
                        $updateData['floorno'] = $value2->floorno;

                        $this->updateBedset($updateData,$updateKey);
                    }

                    if(!$maleEndStatus){
                        foreach ($emptyMaleBedThree as $key2 => $value2) {
                            if (count($maleStudent)==0) {
                                $maleEndStatus = true;
                                break;
                            }

                            $signup=array_shift($maleStudent);

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $value['class'];
                            $updateKey['term'] = $value['term'];
                            $updateData['bedno'] = $value2->bedno;
                            $updateData['bedroom'] = $value2->bedroom;
                            $updateData['floorno'] = $value2->floorno;

                            $this->updateBedset($updateData,$updateKey);
                        }
                    }

                    if($maleEndStatus){
                        $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'13','2302','2318','M','Y');
                        for($i=1;$i<=2;$i++){
                            if(count($emptyMaleBed) > 0){
                                $insertData = array();
                                $spare = array_shift($emptyMaleBed); 

                                $insertData['class'] = $value['class'];
                                $insertData['term'] = $value['term'];
                                $insertData['staystartdate'] = $staystartdate;
                                $insertData['stayenddate'] = $stayenddate;
                                $insertData['week'] = null;
                                $insertData['sex'] = 'M';
                                $insertData['floorno'] = $spare->floorno;
                                $insertData['bedroom'] = $spare->bedroom;
                                $insertData['bedno'] = $spare->bedno;

                                $this->addSpareroom($insertData);
                            }
                        }
                    }

                    if($femaleEndStatus){
                        $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'13','2302','2318','F','Y');
                        for($i=1;$i<=2;$i++){
                            if(count($emptyFemaleBed) > 0){
                                $insertData = array();
                                $spare = array_shift($emptyFemaleBed); 

                                $insertData['class'] = $value['class'];
                                $insertData['term'] = $value['term'];
                                $insertData['staystartdate'] = $staystartdate;
                                $insertData['stayenddate'] = $stayenddate;
                                $insertData['week'] = null;
                                $insertData['sex'] = 'F';
                                $insertData['floorno'] = $spare->floorno;
                                $insertData['bedroom'] = $spare->bedroom;
                                $insertData['bedno'] = $spare->bedno;

                                $this->addSpareroom($insertData);
                            }
                        }
                    }
                } else {
                    foreach ($emptyMaleBed as $key2 => $value2) {
                        if (count($maleStudent)==0) {
                            $maleEndStatus = true;
                            break;
                        }
                        $signup=array_shift($maleStudent);

                        if($signup['handicap'] == 'Y'){
                            continue;
                        }

                        $updateKey['idno'] = $signup['idno'];
                        $updateKey['class'] = $value['class'];
                        $updateKey['term'] = $value['term'];
                        $updateData['bedno'] = $value2->bedno;
                        $updateData['bedroom'] = $value2->bedroom;
                        $updateData['floorno'] = $value2->floorno;

                        $this->updateBedset($updateData,$updateKey);
                    }

                    foreach ($emptyFemaleBed as $key2 => $value2) {
                        if (count($femaleStudent)==0) {
                            $femaleEndStatus = true;
                            break;
                        }
                        $signup=array_shift($femaleStudent); 

                        if($signup['handicap'] == 'Y'){
                            continue;
                        }

                        $updateKey['idno'] = $signup['idno'];
                        $updateKey['class'] = $value['class'];
                        $updateKey['term'] = $value['term'];
                        $updateData['bedno'] = $value2->bedno;
                        $updateData['bedroom'] = $value2->bedroom;
                        $updateData['floorno'] = $value2->floorno;

                        $this->updateBedset($updateData,$updateKey);
                    }

                    if($maleEndStatus){
                        $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'11','','','M','Y');
                        for($i=1;$i<=2;$i++){
                            if(count($emptyMaleBed) > 0){
                                $insertData = array();
                                $spare = array_shift($emptyMaleBed); 

                                $insertData['class'] = $value['class'];
                                $insertData['term'] = $value['term'];
                                $insertData['staystartdate'] = $staystartdate;
                                $insertData['stayenddate'] = $stayenddate;
                                $insertData['week'] = null;
                                $insertData['sex'] = 'M';
                                $insertData['floorno'] = $spare->floorno;
                                $insertData['bedroom'] = $spare->bedroom;
                                $insertData['bedno'] = $spare->bedno;

                                $this->addSpareroom($insertData);
                            }
                        }
                    }

                    if($femaleEndStatus){
                        $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'79','','','F','Y');
                        for($i=1;$i<=2;$i++){
                            if(count($emptyFemaleBed) > 0){
                                $insertData = array();
                                $spare = array_shift($emptyFemaleBed); 

                                $insertData['class'] = $value['class'];
                                $insertData['term'] = $value['term'];
                                $insertData['staystartdate'] = $staystartdate;
                                $insertData['stayenddate'] = $stayenddate;
                                $insertData['week'] = null;
                                $insertData['sex'] = 'F';
                                $insertData['floorno'] = $spare->floorno;
                                $insertData['bedroom'] = $spare->bedroom;
                                $insertData['bedno'] = $spare->bedno;

                                $this->addSpareroom($insertData);
                            }
                        }
                    }
                }

                $updateAutoKey = array();
                $updateAutoData = array();
                $updateAutoKey['class'] = $value['class'];
                $updateAutoKey['term'] = $value['term'];
                $updateAutoData['auto'] = 'Y';
                $updateAutoData['auto_type'] = 'O';
                $this->updateAuto($updateAutoData,$updateAutoKey);
            } else if($value['longclass'] == 'Y'){
                $maleEndStatus = false;
                $femaleEndStatus = false;
                $longDormDateWeek = $this->getLongDormDateWeek($value['class'],$value['term'])->toArray();

                if(empty($longDormDateWeek)){
                    $courseDate = $this->getLongClass($value['class'],$value['term'])->toArray();
                    $createWeeks = $this->createWeeks($value['class'],$value['term'],$courseDate);
                }

                $week = 0;
                $weekMondayQuery = $this->getWeekMonday($courseStartDate['courseStartDate']);
                foreach ($longDormDateWeek as $key2 => $value2) {
                    $weekMonday = $this->getWeekMonday($value2['staystartdate']);

                    if($weekMondayQuery == $weekMonday){
                        $staystartdate = $value2['staystartdate'];
                        $stayenddate = $value2['stayenddate'];
                        $week = $value2['week'];
                        break;
                    }
                }

                if($week > 0){
                    $maleSupervisorEndStatus = false;
                    $femaleSupervisorEndStatus = false;
                    $maleEndStatus = false;
                    $femaleEndStatus = false;

                    $this->resetLongBed($value['class'],$value['term'],$week);

                    $maleSupervisor = $this->t13tbRepository->getLongDormSupervisorStudent($value['class'],$value['term'],'M',$week)->toArray();
                    $femaleSupervisor = $this->t13tbRepository->getLongDormSupervisorStudent($value['class'],$value['term'],'F',$week)->toArray();

                    $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2251','2266','');
                    $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2271','2287','');

                    foreach ($emptyMaleBed as $key2 => $value2) {
                        if (count($maleSupervisor)==0) {
                            $maleSupervisorEndStatus = true;
                            break;
                        }
                        $signup=array_shift($maleSupervisor);

                        if($signup['handicap'] == 'Y'){
                            continue;
                        }

                        $updateKey['idno'] = $signup['idno'];
                        $updateKey['class'] = $value['class'];
                        $updateKey['term'] = $value['term'];
                        $updateKey['week'] = $week;
                        $updateData['bedno'] = $value2->bedno;
                        $updateData['bedroom'] = $value2->bedroom;
                        $updateData['floorno'] = $value2->floorno;

                        $this->updateLongBedset($updateData,$updateKey);
                    }

                    foreach ($emptyFemaleBed as $key2 => $value2) {
                        if (count($femaleSupervisor)==0) {
                            $femaleSupervisorEndStatus = true;
                            break;
                        }
                        $signup=array_shift($femaleSupervisor); 

                        if($signup['handicap'] == 'Y'){
                            continue;
                        }

                        $updateKey['idno'] = $signup['idno'];
                        $updateKey['class'] = $value['class'];
                        $updateKey['term'] = $value['term'];
                        $updateKey['week'] = $week;
                        $updateData['bedno'] = $value2->bedno;
                        $updateData['bedroom'] = $value2->bedroom;
                        $updateData['floorno'] = $value2->floorno;

                        $this->updateLongBedset($updateData,$updateKey);
                    }

                    while(!$maleSupervisorEndStatus){
                        $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'12','2271','2287','');
                        if(count($emptyFemaleBed) > 0){
                            krsort($emptyFemaleBed);
                            foreach ($emptyFemaleBed as $key2 => $value2) {
                                if (count($maleSupervisor)==0) {
                                    $maleSupervisorEndStatus = true;
                                    break;
                                }
                                $signup=array_shift($maleSupervisor); 

                                if($signup['handicap'] == 'Y'){
                                    continue;
                                }

                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $value['class'];
                                $updateKey['term'] = $value['term'];
                                $updateKey['week'] = $week;
                                $updateData['bedno'] = $value2->bedno;
                                $updateData['bedroom'] = $value2->bedroom;
                                $updateData['floorno'] = $value2->floorno;

                                $this->updateLongBedset($updateData,$updateKey);
                            }
                        } else {
                            $maleSupervisorEndStatus = true;
                        }
                    }

                    $maleStudent = $this->getLongDormStudent($value['class'],$value['term'],'1',$week,'N')->toArray();
                    $femaleStudent = $this->getLongDormStudent($value['class'],$value['term'],'2',$week,'N')->toArray();

                    $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'11','','','M');
                    $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'79','','','F');

                    if(count($maleStudent) > count($emptyMaleBed) || count($femaleStudent) > count($emptyFemaleBed)){
                        $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'13','2302','2318','F');

                        $emptyFemaleBedTwo = array();
                        $emptyFemaleBedThree = array();
                        foreach ($emptyFemaleBed as $key2 => $value2) {
                            $bedroomCount = $this->getBedroomCount($value2->floorno,$value2->bedroom);

                            if($bedroomCount == 2){
                                array_push($emptyFemaleBedTwo, $value2);
                            } else if($bedroomCount == 3){
                                array_push($emptyFemaleBedThree, $value2);
                            }
                        }

                        foreach ($emptyFemaleBedTwo as $key2 => $value2) {
                            if (count($femaleStudent)==0) {
                                $femaleEndStatus = true;
                                break;
                            }

                            $signup=array_shift($femaleStudent);

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $value['class'];
                            $updateKey['term'] = $value['term'];
                            $updateKey['week'] = $week;
                            $updateData['bedno'] = $value2->bedno;
                            $updateData['bedroom'] = $value2->bedroom;
                            $updateData['floorno'] = $value2->floorno;

                            $this->updateLongBedset($updateData,$updateKey);
                        }

                        if(!$femaleEndStatus){
                            foreach ($emptyFemaleBedThree as $key2 => $value2) {
                                if (count($femaleStudent)==0) {
                                    $femaleEndStatus = true;
                                    break;
                                }

                                $signup=array_shift($femaleStudent);

                                if($signup['handicap'] == 'Y'){
                                    continue;
                                }

                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $value['class'];
                                $updateKey['term'] = $value['term'];
                                $updateKey['week'] = $week;
                                $updateData['bedno'] = $value2->bedno;
                                $updateData['bedroom'] = $value2->bedroom;
                                $updateData['floorno'] = $value2->floorno;

                                $this->updateLongBedset($updateData,$updateKey);
                            }
                        }

                        $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'13','2302','2318','M');
                    
                        $emptyMaleBedTwo = array();
                        $emptyMaleBedThree = array();
                        foreach ($emptyMaleBed as $key2 => $value2) {
                            $bedroomCount = $this->getBedroomCount($value2->floorno,$value2->bedroom);

                            if($bedroomCount == 2){
                                array_push($emptyMaleBedTwo, $value2);
                            } else if($bedroomCount == 3){
                                array_push($emptyMaleBedThree, $value2);
                            }
                        }

                        foreach ($emptyMaleBedTwo as $key2 => $value2) {
                            if (count($maleStudent)==0) {
                                $maleEndStatus = true;
                                break;
                            }

                            $signup=array_shift($maleStudent);

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $value['class'];
                            $updateKey['term'] = $value['term'];
                            $updateKey['week'] = $week;
                            $updateData['bedno'] = $value2->bedno;
                            $updateData['bedroom'] = $value2->bedroom;
                            $updateData['floorno'] = $value2->floorno;

                            $this->updateLongBedset($updateData,$updateKey);
                        }

                        if(!$maleEndStatus){
                            foreach ($emptyMaleBedThree as $key2 => $value2) {
                                if (count($maleStudent)==0) {
                                    $maleEndStatus = true;
                                    break;
                                }

                                $signup=array_shift($maleStudent);

                                if($signup['handicap'] == 'Y'){
                                    continue;
                                }

                                $updateKey['idno'] = $signup['idno'];
                                $updateKey['class'] = $value['class'];
                                $updateKey['term'] = $value['term'];
                                $updateKey['week'] = $week;
                                $updateData['bedno'] = $value2->bedno;
                                $updateData['bedroom'] = $value2->bedroom;
                                $updateData['floorno'] = $value2->floorno;

                                $this->updateLongBedset($updateData,$updateKey);
                            }
                        }

                        if($maleEndStatus){
                            $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'13','2302','2318','M','Y');
                            for($i=1;$i<=2;$i++){
                                if(count($emptyMaleBed) > 0){
                                    $insertData = array();
                                    $spare = array_shift($emptyMaleBed); 

                                    $insertData['class'] = $value['class'];
                                    $insertData['term'] = $value['term'];
                                    $insertData['staystartdate'] = $staystartdate;
                                    $insertData['stayenddate'] = $stayenddate;
                                    $insertData['week'] = $week;
                                    $insertData['sex'] = 'M';
                                    $insertData['floorno'] = $spare->floorno;
                                    $insertData['bedroom'] = $spare->bedroom;
                                    $insertData['bedno'] = $spare->bedno;

                                    $this->addSpareroom($insertData);
                                }
                            }
                        }

                        if($femaleEndStatus){
                            $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'13','2302','2318','F','Y');
                            for($i=1;$i<=2;$i++){
                                if(count($emptyFemaleBed) > 0){
                                    $insertData = array();
                                    $spare = array_shift($emptyFemaleBed); 

                                    $insertData['class'] = $value['class'];
                                    $insertData['term'] = $value['term'];
                                    $insertData['staystartdate'] = $staystartdate;
                                    $insertData['stayenddate'] = $stayenddate;
                                    $insertData['week'] = $week;
                                    $insertData['sex'] = 'F';
                                    $insertData['floorno'] = $spare->floorno;
                                    $insertData['bedroom'] = $spare->bedroom;
                                    $insertData['bedno'] = $spare->bedno;

                                    $this->addSpareroom($insertData);
                                }
                            }
                        }
                    } else {
                        foreach ($emptyMaleBed as $key2 => $value2) {
                            if (count($maleStudent)==0) {
                                $maleEndStatus = true;
                                break;
                            }
                            $signup=array_shift($maleStudent);

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $value['class'];
                            $updateKey['term'] = $value['term'];
                            $updateKey['week'] = $week;
                            $updateData['bedno'] = $value2->bedno;
                            $updateData['bedroom'] = $value2->bedroom;
                            $updateData['floorno'] = $value2->floorno;

                            $this->updateLongBedset($updateData,$updateKey);
                        }

                        foreach ($emptyFemaleBed as $key2 => $value2) {
                            if (count($femaleStudent)==0) {
                                $femaleEndStatus = true;
                                break;
                            }
                            $signup=array_shift($femaleStudent); 

                            if($signup['handicap'] == 'Y'){
                                continue;
                            }

                            $updateKey['idno'] = $signup['idno'];
                            $updateKey['class'] = $value['class'];
                            $updateKey['term'] = $value['term'];
                            $updateKey['week'] = $week;
                            $updateData['bedno'] = $value2->bedno;
                            $updateData['bedroom'] = $value2->bedroom;
                            $updateData['floorno'] = $value2->floorno;

                            $this->updateLongBedset($updateData,$updateKey);
                        }

                        if($maleEndStatus){
                            $emptyMaleBed = $this->get_emptybed($staystartdate,$stayenddate,'11','','','M','Y');
                            for($i=1;$i<=2;$i++){
                                if(count($emptyMaleBed) > 0){
                                    $insertData = array();
                                    $spare = array_shift($emptyMaleBed); 

                                    $insertData['class'] = $value['class'];
                                    $insertData['term'] = $value['term'];
                                    $insertData['staystartdate'] = $staystartdate;
                                    $insertData['stayenddate'] = $stayenddate;
                                    $insertData['week'] = $week;
                                    $insertData['sex'] = 'M';
                                    $insertData['floorno'] = $spare->floorno;
                                    $insertData['bedroom'] = $spare->bedroom;
                                    $insertData['bedno'] = $spare->bedno;

                                    $this->addSpareroom($insertData);
                                }
                            }
                        }

                        if($femaleEndStatus){
                            $emptyFemaleBed = $this->get_emptybed($staystartdate,$stayenddate,'79','','','F','Y');
                            for($i=1;$i<=2;$i++){
                                if(count($emptyFemaleBed) > 0){
                                    $insertData = array();
                                    $spare = array_shift($emptyFemaleBed); 

                                    $insertData['class'] = $value['class'];
                                    $insertData['term'] = $value['term'];
                                    $insertData['staystartdate'] = $staystartdate;
                                    $insertData['stayenddate'] = $stayenddate;
                                    $insertData['week'] = $week;
                                    $insertData['sex'] = 'F';
                                    $insertData['floorno'] = $spare->floorno;
                                    $insertData['bedroom'] = $spare->bedroom;
                                    $insertData['bedno'] = $spare->bedno;

                                    $this->addSpareroom($insertData);
                                }
                            }
                        }
                    }

                    $updateAutoKey = array();
                    $updateAutoData = array();
                    $updateAutoKey['class'] = $value['class'];
                    $updateAutoKey['term'] = $value['term'];
                    $updateAutoData['auto'] = 'Y';
                    $updateAutoData['auto_type'] = 'O';
                    $this->updateAuto($updateAutoData,$updateAutoKey);

                    $updateAutoKey = array();
                    $updateAutoData = array();
                    $updateAutoKey['class'] = $value['class'];
                    $updateAutoKey['term'] = $value['term'];
                    $updateAutoKey['week'] = $week;
                    $updateAutoData['auto'] = 'Y';
                    $updateAutoData['auto_type'] = 'O';
                    $this->updateLongAuto($updateAutoData,$updateAutoKey);
                }
            }
        }
    }

    //取房間是幾人房
    public function getBedroomCount($floorno,$bedroom)
    {
        return $this->eduBedRepository->getBedroomCount($floorno,$bedroom);
    }

    public function getClassBedList($class,$term)
    {
        return $this->t13tbRepository->getClassBedList($class,$term);
    }


}