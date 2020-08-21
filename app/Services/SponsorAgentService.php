<?php
namespace App\Services;

use App\Repositories\SponsorAgentRepository;
use App\Repositories\M09tbRepository;

class SponsorAgentService
{
    /**
     * StudentService constructor.
     * @param M02tbRepository $m02tbRepository
     */
    public function __construct(
        SponsorAgentRepository $sponsorAgentRepository,
        M09tbRepository $m09tbRepository
    )
    {
        $this->sponsorAgentRepository = $sponsorAgentRepository;
        $this->m09tbRepository = $m09tbRepository;
    }

    public function getM09tbs($queryData = null, $paginate)
    {
        return $this->m09tbRepository->get($queryData, "*", $paginate);
    }

    public function getM09tb($userid)
    {
        return $this->m09tbRepository->find(['userid' => $userid]);
    }

    public function addAgent($userid, $agent_userid)
    {
        $agent = compact(['userid', 'agent_userid']);
        return $this->sponsorAgentRepository->insert($agent);
    }

    public function getSponsorAgent($userid, $agent_userid)
    {
        $agent = compact(['userid', 'agent_userid']);
        return $this->sponsorAgentRepository->find($agent);
    }

    public function deleteSponsorAgent($id)
    {
        return $this->sponsorAgentRepository->delete($id);
    }
}