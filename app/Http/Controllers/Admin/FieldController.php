<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FieldService;

/*
    表單欄位專用 API
*/
class FieldController extends Controller
{
    public function __construct(FieldService $fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function getData(Request $request,$tb)
    {
        switch ($tb) {
            case 't04tbs':
                $queryData = $request->only(['class', 'class_name']);
                $paginate = $this->fieldService->getT04tbs($queryData)->toArray();
                $data = [
                    'status' => '0',
                    'queryData' => $queryData,
                    'data' => $paginate['data'],
                    'total' => $paginate['total']
                ];
                break;
            case 't04tb':
                $queryData = $request->only(['class', 'term']);
                $data['status'] = '0';
                $data['data'] = $this->fieldService->getT04tb($queryData);
            default:
                break;
        }

        return response()->json($data);
    }

    public function m17tbs(Request $request)
    {
        $queryData = $request->only(['enrollorg', 'enrollname']);
        $paginate = $this->fieldService->getM17tbs($queryData)->toArray();

        $data = [
            'status' => '0',
            'queryData' => $queryData,
            'data' => $paginate['data'],
            'total' => $paginate['total']
        ];
        
        return response()->json($data);
    }

    public function AllFloors(Request $request)
    {
        $result = $this->fieldService->getAllFloors()->toArray();

        return $result;
    }

    public function selectEmptyBed(Request $request)
    {
        $queryData = $request->only(['floorno', 'sex', 'staystartdate', 'stayenddate', 'staystarttime', 'stayendtime']);

        $paginate = $this->fieldService->selectEmptyBed($queryData)->toArray();

        $data = [
            'status' => '0',
            'queryData' => $queryData,
            'data' => $paginate['data'],
            'total' => $paginate['total']
        ];

        return response()->json($data);
    }

    public function getFloors(Request $request)
    {
        $queryData = $request->only(['staystartdate', 'stayenddate', 'staystarttime', 'stayendtime']);

        $result = $this->fieldService->getFloors($queryData);

        return $result;
    }

    public function getEmptyBed(Request $request)
    {
        $queryData = $request->only(['floorno', 'sex', 'staystartdate', 'stayenddate', 'staystarttime', 'stayendtime']);

        $paginate = $this->fieldService->getEmptyBed($queryData)->toArray();

        $data = [
            'status' => '0',
            'queryData' => $queryData,
            'data' => $paginate['data'],
            'total' => $paginate['total']
        ];

        return response()->json($data);
    }

    public function t01tbs(Request $request)
    {
        $queryData = $request->only(['class_or_name', 'yerly']);
        $data = $this->fieldService->getT01tbs($queryData)->toArray()['data'];
        return response()->json($data);      
    }

    public function t01tb(Request $request, $class)
    {
        $queryData = $request->only(['yerly', 's_month', 'e_month']);
        $t01tb = $this->fieldService->getT01tb($class, $queryData);
        $t01tb = collect($t01tb->toArray())->only(['class', 'name', 't04tbs']);
        return response()->json($t01tb);      
    }

    public function m14tbs(Request $request)
    {
        $queryData = $request->only(['enrollorg', 'enrollname']);
        $paginate = $this->fieldService->getM14tbs($queryData)->toArray();
        $data = [
            'status' => '0',
            'queryData' => $queryData,
            'data' => $paginate['data'],
            'total' => $paginate['total']
        ];        
        dd($data);
        return response()->json($data);   
    }

}