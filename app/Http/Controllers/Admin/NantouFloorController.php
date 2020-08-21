<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

use App\Models\Edu_floor;
use App\Models\Edu_classroomcls;

class NantouFloorController  extends Controller
{
    public function __construct()
    {

    }
 
    public function index(Request $request)
    {
        $queryData = $request->only(['floorno', 'floorname', '_paginate_qty']);
        $data = Edu_floor::select("*");

        if (isset($queryData['floorno'])){
            $data->where('floorno', 'LIKE', "%{$queryData['floorno']}%");
        }

        if (isset($queryData['floorname'])){
            $data->where('floorname', 'LIKE', "%{$queryData['floorname']}%");
        }
        
        $queryData['_paginate_qty'] = (isset($queryData['_paginate_qty'])) ? $queryData['_paginate_qty'] : 10;

        $data = $data->paginate($queryData['_paginate_qty']);

        $cls = Edu_classroomcls::all();
        $cls = $cls->keyBy('croomclsno')->map(function($cl){
            return $cl->croomclsno.'-'.$cl->croomclsname;
        });

        return view('admin/nantou_floor/index', compact(['data', 'queryData', 'cls']));
    }   

    public function create()
    {        
        $action = "create"; 
        $cls = Edu_classroomcls::all();
        $cls = $cls->keyBy('croomclsno')->map(function($cl){
            return $cl->croomclsno.'-'.$cl->croomclsname;
        });

        return view('admin/nantou_floor/form', compact(['action', 'cls']));             
    }

    public function store(Request $request)
    {
        $this->validate($request, ['floorno' => 'required']);
        $newfloor = $request->only(['floorno', 'floorname', 'croomclsno']);
        $floor = Edu_floor::where('floorno', '=', $newfloor['floorno'])->first();

        if (empty($floor)){
            Edu_floor::insert($newfloor);
            return back()->withResult(1)->withMessage('新增成功');            
        }else{
            return back()->withResult(0)->withMessage('代碼重複');            
        }        
    }

    public function edit($id)
    {
        $floor = Edu_floor::find($id);

        if (empty($floor)){
            return back()->withResult(0)->withMessage('找不到該寢室');
        }

        $cls = Edu_classroomcls::all();
        $cls = $cls->keyBy('croomclsno')->map(function($cl){
            return $cl->croomclsno.'-'.$cl->croomclsname;
        });

        $action = "edit";                            
        return view('admin/nantou_floor/form', compact(['floor', 'action', 'cls']));                
    }

    public function update(Request $request, $id)
    {
        $newfloor = $request->only(['floorname', 'croomclsno']);
        $floor = Edu_floor::find($id);

        if (empty($floor)){
            return back()->withResult(0)->withMessage('找不到該樓別');
        }

        $floor->fill($newfloor);
        $floor->save();

        return back()->withResult(1)->withMessage('更新成功');
    }


}
