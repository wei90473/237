<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

use App\Models\Edu_classcode;

class PlaceNantouLocationController  extends Controller
{
    public function __construct()
    {

    }
 
    public function index(Request $request)
    {
        $queryData = $request->only('name', 'code', '_paginate_qty');
        $data = Edu_classcode::where('deleted', '=', 0)
                             ->where('class', '=', '49');

        if (isset($queryData['code'])){
            $data->where('code', 'LIKE', "%{$queryData['code']}%");
        }

        if (isset($queryData['name'])){
            $data->where('name', 'LIKE', "%{$queryData['name']}%");
        }

        $queryData['_paginate_qty'] = (isset($queryData['_paginate_qty'])) ? $queryData['_paginate_qty'] : 10;
        $data = $data->paginate($queryData['_paginate_qty']);
        return view('admin/place_nantou_location/index', compact(['data', 'queryData']));
    }   

    public function create()
    {
        $action = "create"; 
        return view('admin/place_nantou_location/form', compact(['classcode', 'action']));             
    }

    public function store(Request $request)
    {
        $this->validate($request, ['code' => 'required', 'name' => 'required']);
        $newClasscode = $request->only(['code', 'name']);
        
        $classcode = Edu_classcode::where('deleted', '=', 0)
                                  ->where('class', '=', '49')
                                  ->where('code', '=', $newClasscode['code'])
                                  ->first();

        if (empty($classcode)){
            $newClasscode['deleted'] = 0;
            $newClasscode['class'] = 49;
            Edu_classcode::insert($newClasscode);
            return back()->withResult(1)->withMessage('新增成功');            
        }else{
            return back()->withResult(0)->withMessage('代碼重複');            
        }        
    }

    public function edit($code)
    {
        $classcode = Edu_classcode::where('deleted', '=', 0)
                             ->where('class', '=', '49')
                             ->where('code', '=', $code)
                             ->first();

        if (empty($classcode)){
            return back()->withResult(0)->withMessage('找不到該代碼');
        }

        $action = "edit";                            
        return view('admin/place_nantou_location/form', compact(['classcode', 'action']));                
    }

    public function update(Request $request, $code)
    {
        $newClasscode = $request->only(['name']);
        $classcode = Edu_classcode::where('deleted', '=', 0)
                                  ->where('class', '=', '49')
                                  ->where('code', '=', $code)
                                  ->first();

        if (empty($classcode)){
            return back()->withResult(0)->withMessage('找不到該代碼');
        }

        Edu_classcode::where('deleted', '=', 0)
                     ->where('class', '=', '49')
                     ->where('code', '=', $code)
                     ->update($newClasscode);

        return back()->withResult(1)->withMessage('更新成功');
    }


}
