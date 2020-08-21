<?php

namespace App\Helpers;

use DateTime;

class TWDateTime extends DateTime{
	public function getTWFormat()
	{
		return ($this->format('Y') - 1911).$this->format('md');
	}

	public function setTWDateTime($dateString)
	{
		if (strlen($dateString) != 7){
			return false;
		}

		$year = (int)substr($dateString, 0, 3) + 1911;
		$month = substr($dateString, 3, 2);
		$day = substr($dateString, 5, 2);

		$this->setDate($year, $month, $day);
		$this->setTime(0, 0, 0);
	}
}
