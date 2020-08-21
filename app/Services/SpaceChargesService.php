<?php
namespace App\Services;

use App\Repositories\EduclasscodeRepository;
use App\Repositories\EduloanplaceRepository;
use App\Repositories\EduloanplacelstRepository;
use App\Repositories\EduloanroomRepository;
use App\Repositories\EduloansroomRepository;
use DB;

class SpaceChargesService
{
    /**
     * PunchService constructor.
     * @param 
     */
    public function __construct(EduclasscodeRepository $eduClasscodeRepository, EduloanplaceRepository $eduLoanplaceRepository, EduloanplacelstRepository $eduloanPlacelstRepository, EduloanroomRepository $eduLoanRoomRepository, EduloansroomRepository $eduLoansRoomRepository)
    {
        $this->eduClasscodeRepository = $eduClasscodeRepository;
        $this->eduLoanplaceRepository = $eduLoanplaceRepository;
        $this->eduloanPlacelstRepository = $eduloanPlacelstRepository;
        $this->eduLoanRoomRepository = $eduLoanRoomRepository;
        $this->eduLoansRoomRepository = $eduLoansRoomRepository;
    }

    public function getApplyStatus($statusData){
        $applyStatusList = $this->eduClasscodeRepository->getByClass($statusData);

        return $applyStatusList;
    }

    public function getChargeList($queryData){
        $chargeList = $this->eduLoanplaceRepository->getChargeList($queryData);

        return $chargeList;
    }

    public function getForSpacproc($applyno){
        $detail = $this->eduLoanplaceRepository->getForSpacproc($applyno);

        return $detail;
    }

    public function getForSpaceprocessub($applyno,$queryData){
        $data = $this->eduloanPlacelstRepository->getForSpaceprocessub($applyno,$queryData);

        return $data;
    }

    public function getLoanRoom($applyno,$croomclsno){
        $data = $this->eduLoanRoomRepository->getLoanRoom($applyno,$croomclsno);

        return $data;
    }

    public function getLoansRoom($applyno,$croomclsno){
        $data = $this->eduLoansRoomRepository->getLoansRoom($applyno,$croomclsno);

        return $data;
    }

    public function updateLoanplace($queryData,$id){
        return $this->eduLoanplaceRepository->updateOrCreate($id,$queryData);
    }

    public function getrptSPrptReceipt($applyno){
        $data = $this->eduLoanplaceRepository->getrptSPrptReceipt($applyno);

        return $data;
    }
}