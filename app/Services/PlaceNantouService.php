<?php
namespace App\Services;

use App\Models\Edu_classroom;
use App\Repositories\EduClassRoomRepository;

class PlaceNantouService
{
	public function __construct(EduClassRoomRepository $eduClassRoomRepository)
	{
		$this->eduClassRoomRepository = $eduClassRoomRepository;
	}

	public function getClassRoomList($queryData)
	{
		return $this->eduClassRoomRepository->getList($queryData, true, '*', ['classroomcls', 'location']);
	}

	public function getClassRoom($roomno)
	{
		return $this->eduClassRoomRepository->find(['roomno' => $roomno]);
	}

	public function createClassRoom($newClassrooom)
	{
		try {
			$classroom = $this->eduClassRoomRepository->find(['roomno' => $newClassrooom['roomno']]);
			if (isset($classroom)){
				return 1;
			}
			$this->eduClassRoomRepository->insert($newClassrooom);
			return true;
		} catch (Exception $e) {
			return false;
		}
		
	}

	public function updateClassRoom($roomno, $newClassrooom)
	{
		try {
			$this->eduClassRoomRepository->update(['roomno' => $roomno], $newClassrooom);
			return true;
		} catch (Exception $e) {
			return false;
		}
		
	}

}