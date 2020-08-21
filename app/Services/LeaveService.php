<?php
namespace App\Services;

use App\Repositories\LeaveRepository;
use App\Repositories\T04tbRepository;
use App\Repositories\T13tbRepository;
use App\Repositories\T14tbRepository;
use DB;

use App\Models\T14tb;

class LeaveService
{
    /**
     * LeaveService constructor.
     * @param LeaveRepository $leaveRepository
     */
    public function __construct(
        LeaveRepository $leaveRepository,
        T04tbRepository $t04tbRepository,
        T13tbRepository $t13tbRepository,
        T14tbRepository $t14tbRepository
    )
    {
        $this->leaveRepository = $leaveRepository;
        $this->t04tbRepository = $t04tbRepository;
        $this->t13tbRepository = $t13tbRepository;
        $this->t14tbRepository = $t14tbRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getLeaveList($queryData)
    {
        return $this->leaveRepository->getLeaveList($queryData);
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getOpenClassList($queryData)
    {
        return $this->t04tbRepository->getByQueryList($queryData);
    }

    public function getT04tb($t04tb_info)
    {
        return $this->t04tbRepository->find($t04tb_info);
    }

    public function suspendAllByClass($t04tb_info, $hour)
    {
        $t04tb = $this->t04tbRepository->find($t04tb_info);
        $t13tb_idnos = $this->t13tbRepository->get($t04tb_info, 't13tb.*', false)->pluck('idno');
        $max_sernos = $this->t14tbRepository->getT04tbMaxSerno($t04tb_info, $t13tb_idnos);
        $max_sernos = $max_sernos->pluck('max_serno', 'idno')->toArray();

        DB::beginTransaction();
        try {

            foreach($t13tb_idnos as $idno){

                $serno = "01";

                if (!empty($max_sernos[$idno])){
                    $serno = str_pad((int)$max_sernos[$idno] + 1, 2, '0', STR_PAD_LEFT);
                }

                $t14tb = $t04tb_info;
                $t14tb['idno'] = $idno;
                $t14tb['serno'] = $serno;
                $t14tb['sdate'] = $t04tb->sdate;
                $t14tb['edate'] = $t04tb->edate;
                $t14tb['stime'] = "0000";
                $t14tb['etime'] = "2359";     
                $t14tb['type'] = 6; // 停班課
                $t14tb['hour'] = $hour;
                $this->t14tbRepository->insert($t14tb);
            }

            $this->t13tbRepository->update($t04tb_info, ['status' => 4]);

            DB::commit();

            return true; 

        } catch (\Exception $e) {
            DB::rollback();
            return false;

            var_dump($e->getMessage());
            die;            
        }         

    }
    
    public function createLeave($t04tbKey, $newT14tb)
    {
        // 取得流水號
        $newT14tb['serno'] = T14tb::where($t04tbKey)->where('idno', $newT14tb['idno'])->max('serno') + 1;
        $newT14tb['serno'] = str_pad($newT14tb['serno'],2,'0',STR_PAD_LEFT);
        $newT14tb = array_merge($t04tbKey, $newT14tb);
        return $this->t14tbRepository->insert($newT14tb);
    }

    public function update($t14tbKey, $newT14tb)
    {
        return $this->t14tbRepository->update($t14tbKey, $newT14tb);
    }

    public function suspendPartByClass($t04tb_info, $suspend_infos)
    {
        // $t04tb = $this->t04tbRepository->find($t04tb_info);
        $idnos = array_keys($suspend_infos);
        $max_sernos = $this->t14tbRepository->getT04tbMaxSerno($t04tb_info, $idnos);
        $max_sernos = $max_sernos->pluck('max_serno', 'idno')->toArray();

        DB::beginTransaction();
        try {

            foreach($suspend_infos as $idno => $suspend_info){

                $serno = "01";

                if (!empty($max_sernos[$idno])){
                    $serno = str_pad((int)$max_sernos[$idno] + 1, 2, '0', STR_PAD_LEFT);
                }

                $t14tb = $t04tb_info;
                $t14tb['idno'] = $idno;
                $t14tb['serno'] = $serno;
                $t14tb['sdate'] = $suspend_info['sdate'];
                $t14tb['edate'] = $suspend_info['edate'];
                $t14tb['stime'] = ($suspend_info['stime'] === '') ? '0000' : $suspend_info['stime'];
                $t14tb['etime'] = ($suspend_info['etime'] === '') ? '2359' : $suspend_info['etime'];    
                $t14tb['type'] = 6; // 停班課
                $t14tb['hour'] = $suspend_info['hour'];

                $this->t14tbRepository->insert($t14tb);
            }

            DB::commit();

            return true; 

        } catch (\Exception $e) {
            DB::rollback();
            return false;

            var_dump($e->getMessage());
            die;            
        }          
    }

}
