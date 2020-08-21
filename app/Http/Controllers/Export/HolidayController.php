<?php

namespace App\Http\Controllers\Export;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;
use DB;



class HolidayController extends Controller
{
    /**
     * 匯出行事曆
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 撈取資料
        $data = DB::select('select * from m12tb');
        // 標題
        $result = array(array('日期', '假日名稱'));

        foreach ($data as $va) {
            $result[] = array(
                $va->date,
                $va->holiday
            );
        }

        $this->export($result);
    }

    /**
     * 匯出
     *
     * @param $data
     */
    public function export($data)
    {
        // 檔案名稱
        $fileName = '國定假日';

        Excel::create($fileName,function ($excel) use ($data){
            $excel->sheet('score', function ($sheet) use ($data){
                $sheet->rows($data);
            });
        })->export('xls');
    }
}
