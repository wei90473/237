<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Model

use App\Models\T04tb;
use App\Models\T01tb;
use App\Models\M01tb;

/*
    選擇 Modal
*/
class SelectModalController extends Controller
{
    public function __construct()
    {
        
    }

    public function t01tb(Request $request)
    {
        $queryData = $request->only(['class', 'class_name']);
        $t01tbs = T01tb::select('*')->with('t04tbs');

        if (isset($queryData['class'])){
            $t01tbs->where('class', 'LIKE', "%{$queryData['class']}%");
        }

        if (isset($queryData['class_name'])){
            $t01tbs->where('name', 'LIKE', "%{$queryData['class_name']}%");
        }

        $t01tbs = $t01tbs->paginate(10);
  
        return view('admin.selectModal.t01tb.table', compact('t01tbs'));
    }

    public function t04tb()
    {
        $t04tbs = T04tb::join('t01tb', 't01tb.class', '=', 't04tb.class')
                       ->selectRaw('t04tb.class, t04tb.term, t01tb.name as class_name')
                       ->paginate(10);
        return view('admin.selectModal.t04tb.table', compact('t04tbs'));
    }

    public function teacher()
    {
        $teachers = M01tb::paginate(10);
        return view('admin.selectModal.teacher.table', compact('teachers'));
    }
}