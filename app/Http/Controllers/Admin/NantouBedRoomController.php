<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

use App\Models\Edu_bed;
use App\Models\Edu_floor;

class NantouBedRoomController  extends Controller
{
    public function __construct()
    {

    }
 
    public function index(Request $request)
    {
        $queryData = $request->only('floorno', 'isuse', 'bedno', '_paginate_qty');
        $data = Edu_bed::select("*");

        if (isset($queryData['bedno'])){
            $data->where('bedno', 'LIKE', "%{$queryData['bedno']}%");
        }

        if (isset($queryData['floorno'])){
            $data->where('floorno', '=', $queryData['floorno']);
        }

        if (isset($queryData['isuse'])){
            $data->where('isuse', '=', $queryData['isuse']);
        }
        
        $queryData['_paginate_qty'] = (isset($queryData['_paginate_qty'])) ? $queryData['_paginate_qty'] : 10;

        $floors = Edu_floor::all()->pluck('floorname', 'floorno');
        $data = $data->paginate($queryData['_paginate_qty']);
        return view('admin/nantou_bedroom/index', compact(['data', 'queryData', 'floors']));
    }   

    public function create()
    {        
        $floors = Edu_floor::all();
        $floors = $floors->keyBy('floorno')->map(function($floor){
            return $floor->floorno.'-'.$floor->floorname;
        });
        $action = "create"; 
        return view('admin/nantou_bedroom/form', compact(['classcode', 'action', 'floors']));             
    }

    public function store(Request $request)
    {
        $this->validate($request, ['floorno' => 'required', 'bedseat' => 'required']);
        $newBedroom = $request->only(['floorno', 'bedroom', 'roomname', 'isuse']);

        if (empty($bedroom)){
            $newBedroom['bedno'] = $newBedroom['bedroom'].$request->bedseat;
            Edu_bed::insert($newBedroom);
            return back()->withResult(1)->withMessage('新增成功');            
        }else{
            return back()->withResult(0)->withMessage('代碼重複');            
        }        
    }

    public function edit($id)
    {
        $bedroom = Edu_bed::find($id);

        if (empty($bedroom)){
            return back()->withResult(0)->withMessage('找不到該寢室');
        }

        $floors = Edu_floor::all();
        $floors = $floors->keyBy('floorno')->map(function($floor){
            return $floor->floorno.'-'.$floor->floorname;
        });

        $action = "edit";                            
        return view('admin/nantou_bedroom/form', compact(['bedroom', 'action', 'floors']));                
    }

    public function update(Request $request, $id)
    {
        $newBedroom = $request->only(['floorno', 'bedroom', 'roomname', 'isuse']);
        $bedroom = Edu_bed::find($id);

        if (empty($bedroom)){
            return back()->withResult(0)->withMessage('找不到該寢室');
        }

        $newBedroom['bedno'] = $newBedroom['bedroom'].$request->bedseat;

        $bedroom->fill($newBedroom);
        $bedroom->save();

        return back()->withResult(1)->withMessage('更新成功');
    }


}
