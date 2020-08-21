<?php
namespace App\Helpers;

use Session;

class History
{
    public static function record()
    {
        if (request()->method() == 'GET'){
            $history = Session::get('history');

            if (!is_array($history)){
                $history = [];
            }

            $history[request()->path()] = request()->getQueryString();
            Session::put('history', $history);
        }
    }
    
    public static function getHistory($path)
    {
        $history = Session::get('history');
        if (isset($history[$path])){
            return $history[$path];
        }else{
            return null;
        }
    }
}