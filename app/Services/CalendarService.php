<?php
namespace App\Services;

use DB;
use App\Helpers\Common;
use App\Helpers\TrainSchedule;
use DateTime;

use App\Repositories\T36tbRepository;
use App\Repositories\T04tbRepository;

class CalendarService
{
    /**
     * ScheduleService constructor.
     * @param DemandDistributionRepository $demandDistributionRpository
     */
    public function __construct(
        T36tbRepository $t36tbRepository,
        T04tbRepository $t04tbRepository
    )
    {
        $this->t36tbRepository = $t36tbRepository;
        $this->t04tbRepository = $t04tbRepository;
    }

    public function getCalendar($class, $term)
    {
        $this->t36tbRepository->getData(['class' => $class, 'term' => $term]);
    }

    public function storeCalendar($data, $action)
    {

        DB::beginTransaction();
        try {
            $newT36tb = $data['orgin'];
            $newT36tb['date'] = $data['t36tb']['date'];

            if (!empty($this->t36tbRepository->find($newT36tb)) && $newT36tb['date'] <> $data['orgin']['date']){
                return 1;
            }

            if ($action == "update"){
                $this->t36tbRepository->update($data['orgin'], $data['t36tb']);
                $this->t04tbRepository->update([
                    "class" => $data['orgin']['class'],
                    "term" => $data['orgin']['term']
                ], $data['t04tb']);
            }elseif ($action == "insert"){
                $this->t36tbRepository->insert($data['t36tb']);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;            
            return false;
        }
    }

    public function deleteCalendar($data){
        return $this->t36tbRepository->delete($data);
    }

}