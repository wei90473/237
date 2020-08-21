<?php
namespace App\Services;

use App\Repositories\RestructuringRepository;
use App\Repositories\RestructuringDetailRepository;
use DB;

class RestructuringService
{
    /**
     * ScheduleService constructor.
     * @param DemandDistributionRepository $demandDistributionRpository
     */
    public function __construct(
        RestructuringRepository $restructuringRepository,
        RestructuringDetailRepository $restructuringDetailRepository
    )
    {
        $this->restructuringRepository = $restructuringRepository;
        $this->restructuringDetailRepository = $restructuringDetailRepository;
    }

    public function getRestructuringList($queryData){
        $restructurings = $this->restructuringRepository->get($queryData);
        $restructurings->map(function($restructuring){
            $created_at = new \DateTime($restructuring->created_at);
            $restructuring->created_at_date = $created_at->format('Y-m-d');
        });

        return $restructurings;
    }

    public function getRestructuring($id){
        $restructuring = $this->restructuringRepository->find($id);
        $restructuring->details = $restructuring->details->groupBy('restructure_type');
        return $restructuring;
    }

    public function createRestructuring($restructuring_detail){
        
        if (count(array_unique($restructuring_detail['new_before_enrollorg'])) <> count($restructuring_detail['new_before_enrollorg'])){
            return [
                'status' => false,
                'message' => '改制前 包含重複的單位'
            ];
        }

        if (count(array_unique($restructuring_detail['new_after_enrollorg'])) <> count($restructuring_detail['new_after_enrollorg'])){
            return [
                'status' => false,
                'message' => '改制後 包含重複的單位'
            ];
        }
        
        DB::beginTransaction();
        try{
            $restructuring = $this->restructuringRepository->insert([]);

            foreach ($restructuring_detail['new_before_enrollorg'] as $enrollorg){
                $detail = [
                    'restructuring_id' => $restructuring->id,
                    'enrollorg' => $enrollorg,
                    'restructure_type' => 'before'
                ];
                $this->restructuringDetailRepository->insert($detail);
            }

            foreach ($restructuring_detail['new_after_enrollorg'] as $enrollorg){
                $detail = [
                    'restructuring_id' => $restructuring->id,
                    'enrollorg' => $enrollorg,
                    'restructure_type' => 'after'
                ];
                $this->restructuringDetailRepository->insert($detail);
            }

            DB::commit();
            return [
                'status' => true,
                'message' => ''
            ];            
        }catch(\Exception $e){
            DB::rollback();
            var_dump($e->getMessage());
            die;
        }
    }

    public function updateRestructuring($id, $restructuring_detail){

        $restructuring = $this->restructuringRepository->find($id);
        $details = $restructuring->details->groupBy('restructure_type')->map(function($type_group){
            return $type_group->pluck('enrollorg', 'enrollorg');
        });

        $before_delete = array_diff(array_keys($details['before']->toArray()), array_keys($restructuring_detail['before_enrollorg']));
        $after_delete = array_diff(array_keys($details['after']->toArray()), array_keys($restructuring_detail['after_enrollorg']));

        $before_exist = array_diff(array_keys($restructuring_detail['before_enrollorg']), $before_delete);
        $after_exist = array_diff(array_keys($restructuring_detail['after_enrollorg']), $after_delete);

        if (empty($restructuring_detail['new_before_enrollorg']) && empty($restructuring_detail['before_enrollorg'])){
            return [
                'status' => false,
                'message' => '改制前不可為空'
            ];            
        }

        if (empty($restructuring_detail['new_after_enrollorg']) && empty($restructuring_detail['after_enrollorg'])){
            return [
                'status' => false,
                'message' => '改制後不可為空'
            ];               
        }

        if (count(array_diff($before_exist, $restructuring_detail['new_before_enrollorg'])) <> count($before_exist)){
            return [
                'status' => false,
                'message' => '改制前 包含重複的單位'
            ];
        }

        if (count(array_diff($after_exist, $restructuring_detail['new_after_enrollorg'])) <> count($after_exist)){
            return [
                'status' => false,
                'message' => '改制後 包含重複的單位'
            ];
        }

        DB::beginTransaction();
        try{

            $this->restructuringDetailRepository->deleteByRestructuringID($id, $before_delete, 'before');
            $this->restructuringDetailRepository->deleteByRestructuringID($id, $after_delete, 'after');

            foreach ($restructuring_detail['before_enrollorg'] as $enrollorg => $new_enrollorg){
                $detail = [
                    'enrollorg' => $new_enrollorg
                ];
                $this->restructuringDetailRepository->update(['restructuring_id' => $id, 'restructure_type' => 'before', 'enrollorg' => $enrollorg], $detail);
            }

            foreach ($restructuring_detail['after_enrollorg'] as $enrollorg => $new_enrollorg){
                $detail = [
                    'enrollorg' => $enrollorg
                ];
                $this->restructuringDetailRepository->update(['restructuring_id' => $id, 'restructure_type' => 'after', 'enrollorg' => $enrollorg], $detail);
            }

            foreach ($restructuring_detail['new_before_enrollorg'] as $enrollorg){
                $detail = [
                    'restructuring_id' => $id,
                    'enrollorg' => $enrollorg,
                    'restructure_type' => 'before'
                ];
                $this->restructuringDetailRepository->insert($detail);
            }

            foreach ($restructuring_detail['new_after_enrollorg'] as $enrollorg){
                $detail = [
                    'restructuring_id' => $id,
                    'enrollorg' => $enrollorg,
                    'restructure_type' => 'after'
                ];
                $this->restructuringDetailRepository->insert($detail);
            }

            DB::commit();

            return [
                'status' => true,
                'message' => ''
            ];             

        }catch(\Exception $e){
            DB::rollback();
            var_dump($e->getMessage());
            die;
        }
    }

    public function deleteRestructuring($id)
    {
        DB::beginTransaction();
        try{        
            $this->restructuringDetailRepository->delete(['restructuring_id' => $id]);
            $this->restructuringRepository->delete(['id' => $id]);
            DB::commit();
            return [
                'status' => true,
                'message' => ''
            ];              
        }catch(\Exception $e){
            DB::rollback();
            var_dump($e->getMessage());
            die;
        }        
    }

    public function checkRestructuringExist($newRestructuring)
    {
        $sqlAfterEnrollorg = join(",", array_map(function($enrollorg){
            return "'{$enrollorg}'";
        },$newRestructuring['new_after_enrollorg']));

        $sqlBeforeEnrollorg = join(",", array_map(function($enrollorg){
            return "'{$enrollorg}'";
        },$newRestructuring['new_before_enrollorg']));       

        $sql = "SELECT * 
                FROM restructuring_detail
                JOIN (
                    SELECT DISTINCT restructuring_id
                    FROM restructuring_detail
                    WHERE (restructure_type = 'after' AND enrollorg in ({$sqlAfterEnrollorg})) OR 
                        (restructure_type = 'before' AND enrollorg in ({$sqlBeforeEnrollorg}))
                ) repeat_detail ON repeat_detail.restructuring_id = restructuring_detail.restructuring_id
        ";

        $details = collect(DB::select($sql))->groupBy('restructuring_id')->map(function($detailGroup){
            return $detailGroup->groupBy('restructure_type');
        });
        
        $result = false;
        foreach($details as $restructuring_id => $detailGroup){
            if ($detailGroup['before']->count() == count($newRestructuring['new_before_enrollorg'])){
                if ($detailGroup['after']->count() == count($newRestructuring['new_after_enrollorg'])){
                    $result = $restructuring_id;
                    break;
                }
            }
        }

        return $result;
    }


}