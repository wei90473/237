<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{

    public $user_group_msg = '無法使用功能!';

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
        //excel 欄位 1 == A, etc
        function getNameFromNumber($num) {
            $numeric = ($num - 1) % 26;
            $letter = chr(65 + $numeric);
            $num2 = intval(($num - 1) / 26);
            if ($num2 > 0) {
                return $this->getNameFromNumber($num2) . $letter;
            } else {
                return $letter;
            }
        }
}
