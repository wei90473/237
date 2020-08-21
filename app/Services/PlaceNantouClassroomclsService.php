<?php
namespace App\Services;

use App\Models\Edu_classroom;
use App\Repositories\EduClassRoomclsRepository;
use App\Repositories\EduclassroomFeeRepository;

use DB;

class PlaceNantouClassroomclsService
{
	public function __construct(EduClassRoomclsRepository $eduClassRoomclsRepository, EduclassroomFeeRepository $educlassroomFeeRepository)
	{
		$this->eduClassRoomclsRepository = $eduClassRoomclsRepository;
		$this->educlassroomFeeRepository = $educlassroomFeeRepository;
	}

	public function getClassRoomlsList($queryData)
	{
		$with = ['fees' => function($query){
			$query->with('feetypeCode');
		}];
		return $this->eduClassRoomclsRepository->getList($queryData, true, '*', $with);
	}

	public function getClassRoomcls($croomclsno)
	{
		return $this->eduClassRoomclsRepository->find(['croomclsno' => $croomclsno]);
	}

	public function createClassRoomcls($newClassrooomcls)
	{
		try {
			$classroomcls = $this->eduClassRoomclsRepository->find(['croomclsno' => $newClassrooomcls['croomclsno']]);
			if (isset($classroomcls)){
				return 1;
			}
			$this->eduClassRoomclsRepository->insert($newClassrooomcls);
			return true;
		} catch (Exception $e) {
			return false;
		}
		
	}

	public function updateClassRoomcls($classroomcls, $newClassrooomcls)
	{
		DB::beginTransaction();
		try {

			if ((int)$newClassrooomcls['classroom'] != (int)$classroomcls->classroom){
				$this->clearFeeSetting($classroomcls->croomclsno);
			}
			
			$this->eduClassRoomclsRepository->update(['croomclsno' => $classroomcls->croomclsno], $newClassrooomcls);
			DB::commit();
			return true;
		} catch (Exception $e) {
			DB::rollback();
			return false;
		}
		
	}

	public function getFeeByDay($croomclsno)
	{
		return $this->educlassroomFeeRepository->getFeeByDay($croomclsno);
	}

	public function getFeeByTime($croomclsno)
	{
		return $this->educlassroomFeeRepository->getFeeByTime($croomclsno);
	}

	public function getFee($clsroomno)
	{
		return $this->educlassroomFeeRepository->find(['clsroomno' => $clsroomno]);
	}

	public function clearFeeSetting($croomclsno)
	{
		$this->educlassroomFeeRepository->delete(['clsroomno' => $croomclsno]);
	}

	public function insertFeeSetting($classroomcls, $setting)
	{

        DB::beginTransaction();

        try {
        	$orginfee = $classroomcls->fees->keyBy('timetype');

        	$this->clearFeeSetting($classroomcls->croomclsno);
			
			switch ($setting['feetype']) {
				case '1':
					// 每間教室時段費用
					foreach ($setting['fee'] as $timetype => $fee){
						if ($setting['type'] == 'weekdays'){
							$feeData =  [
								'fee' => $fee,
								'holidayfee' => (isset($orginfee[$timetype])) ? $orginfee[$timetype]->holidayfee : 0								
							];
						}elseif ($setting['type'] == 'holiday'){
							$feeData =  [
								'fee' => (isset($orginfee[$timetype])) ? $orginfee[$timetype]->fee : 0,
								'holidayfee' => $fee								
							];
						}

						$newFee = [
							'clsroomno' => $classroomcls->croomclsno,
							'feetype' => $setting['feetype'],
							'timetype' => $timetype,
						];

						$newFee = array_merge($newFee, $feeData);
						$this->educlassroomFeeRepository->insert($newFee);
					}
					break;
				case '2':
					// 每間電腦教室-每小時費用
					$newFee = [
						'clsroomno' => $classroomcls->croomclsno,
						'feetype' => $setting['feetype'],
						'timetype' => 203,
						'fee' => $setting['fee'][203],
						'holidayfee' => $setting['fee'][203]
					];
					$this->educlassroomFeeRepository->insert($newFee);
					break;
				case '4':
					// 早上下午晚上 時段費用
					foreach ($setting['fee'] as $timetype => $fee){
						$newFee = [
							'clsroomno' => $classroomcls->croomclsno,
							'feetype' => $setting['feetype'],
							'timetype' => $timetype,
							'fee' => $fee['fee'],
							'holidayfee' => $fee['holidayfee']									
						];
						$this->educlassroomFeeRepository->insert($newFee);
					}
					break;										
				default:
					# code...
					break;
			}

			if ($setting['feetype'] == 3 || $setting['feetype'] == 5){
				$newFee = [
					'clsroomno' => $classroomcls->croomclsno,
					'feetype' => $setting['feetype'],
					'timetype' => '000',
					'fee' => $setting['fee'],
					'holidayfee' => $setting['holidayfee']									
				];
				$this->educlassroomFeeRepository->insert($newFee);
			}

            // DB::rollback();

            DB::commit();
            // all good
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            // return false;
            var_dump($e->getMessage());
            die;
            // something went wrong
        }

	}

	public function insertFeeSettingByTime($croomclsno)
	{
		
	}	
}