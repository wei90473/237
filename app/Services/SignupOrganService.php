<?php
namespace App\Services;

use App\Repositories\T04tbRepository;
use App\Repositories\M13tbRepository;
use App\Repositories\M17tbRepository;
use App\Repositories\OnlineApplyOrganRepository;
use DB;

class SignupOrganService
{
    /**
     * SignupService constructor.
     * @param  
     */
    public function __construct(
        T04tbRepository $t04tbRepository,
        M13tbRepository $m13tbRepository,
        M17tbRepository $m17tbRepository,
        OnlineApplyOrganRepository $onlineApplyOrganRepository
    )
    {
        $this->t04tbRepository = $t04tbRepository;
        $this->m13tbRepository = $m13tbRepository;
        $this->m17tbRepository = $m17tbRepository;
        $this->onlineApplyOrganRepository = $onlineApplyOrganRepository;
    }

    public function getT04tb($class_info)
    {
        return $this->t04tbRepository->find($class_info);
    }

    public function getCompetentAuthoritys()
    {
        return $this->m17tbRepository->getCompetentAuthoritys()->pluck('enroll_full_name', 'enrollorg');
    }

    public function storeOnlineApplyOrgan($online_apply_organ, $action, $id = null)
    {
        $online_apply_organ['open_belong_apply'] = empty($online_apply_organ['open_belong_apply']) ? 0 : 1;

        if ($action == 'insert'){
            return $this->onlineApplyOrganRepository->insert($online_apply_organ);
        }elseif ($action == "update"){
            return $this->onlineApplyOrganRepository->update(['id' => $id], $online_apply_organ);
        }
         
    }

    public function getOnlineApplyOrgan($id)
    {
        return $this->onlineApplyOrganRepository->find($id);
    }

}