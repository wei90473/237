<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Models\Managers;
use Config;
use DB;


class BQTRANS_Controller extends Controller
{
    /**
     * 伺服器測試
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {  
        $data = Managers::first();
        $result['web'] = 1;
        $result['db'] = ($data)? 1 : 0;
        $result['is_demo'] = (config('app.is_demo'))? 1 : 0;
        $result['url'] = Config::get('app.url');
        return response()->json($result);
    }




}
