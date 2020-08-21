<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SpaceChargesService;
use App\Services\User_groupService;
use DB;
use Excel;

class SpaceChargesController extends Controller
{
    public function __construct(SpaceChargesService $spaceChargesService, User_groupService $user_groupService)
    {
        $this->spaceChargesService = $spaceChargesService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('space_charges', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    public function index(Request $request)
    {
        $queryData = $request->only([
            'orgname',              // 申請單位
            'applyuser',            // 申請人姓名
            'start_date',           // 申請日期(起)
            'end_date',             // 申請日期(訖)
            'status',               // 申請狀態
            '_paginate_qty',        // 分頁資料數量
        ]);

        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 100;

        $data = $this->spaceChargesService->getChargeList($queryData);

        $statusData['class'] = 59;
        $applyStatusList = $this->spaceChargesService->getApplyStatus($statusData);

        return view("/admin/space_charges/index",compact(['applyStatusList','queryData','data']));
    }

    public function ChargesSub1($applyno=0,Request $request){
        if ($applyno == 0){
            return back()->with('result', 1)->with('message', '查無資料');
        }

        $queryData = $request->only([
            '_paginate_qty',        // 分頁資料數量
        ]);

        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 100;

        $detail = $this->spaceChargesService->getForSpacproc($applyno);
        $data = $this->spaceChargesService->getForSpaceprocessub($applyno,$queryData);

        return view("/admin/space_charges/chargesSub1",compact(['queryData','data','detail']));
    }

    public function ChargesSub3($applyno=0,$croomclsno){
        if ($applyno == 0){
            return back()->with('result', 1)->with('message', '查無資料');
        }

        $data = $this->spaceChargesService->getLoanRoom($applyno,$croomclsno);

        return view("/admin/space_charges/chargesSub3",compact(['data','applyno']));
    }

    public function ChargesSub4($applyno=0,$croomclsno){
        if ($applyno == 0){
            return back()->with('result', 1)->with('message', '查無資料');
        }

        $data = $this->spaceChargesService->getLoansRoom($applyno,$croomclsno);

        return view("/admin/space_charges/chargesSub4",compact(['data','applyno']));
    }

    public function EditCharges($applyno=0){
        if ($applyno == 0){
            return back()->with('result', 1)->with('message', '查無資料');
        }

        $detail = $this->spaceChargesService->getForSpacproc($applyno);

        if(empty($detail[0]['paydate'])){
            date_default_timezone_set("Asia/Taipei");
            $y=date('Y', time())-1911;
            $m=date('m', time());
            $d=date('d', time());

            $detail[0]['payuser'] = $detail[0]['applyuser'];
            $detail[0]['paydate'] = str_pad($y, 3,'0',STR_PAD_LEFT).str_pad($m, 2,'0',STR_PAD_LEFT).str_pad($d, 2,'0',STR_PAD_LEFT);
        }

        return view("/admin/space_charges/editCharges",compact(['detail']));
    }

    public function update(Request $request){
        $rules = array(
            'payuser' => 'required',
            'paydate' => 'required',
        );

        $messages = array(
            'payuser.required' => '請填寫繳款人',
            'paydate.required' => '請填寫繳費日期'
        );

        $this->validate($request, $rules, $messages);

        $queryData = $request->only([
                        'applyno',
                        'payuser',
                        'paydate',
                    ]);
        $id = $request->only('id');
        $mode = $request->only('mode');

        if($mode['mode'] == 1){
            $queryData['status'] = 'T';
            $queryData['locked'] = '1';
        } else if($mode['mode'] == -1){
            $queryData['payuser'] = '';
            $queryData['paydate'] = '';
            $queryData['status'] = 'F';
        }

        try {
            $this->spaceChargesService->updateLoanplace($queryData,$id);

            return redirect('/admin/space_charges')->with('result', '1')->with('message', '更新成功');
        } catch (\Exception $e) {
            $status = false;
            // return back()->with('result', 0)->with('message', '更新失敗');
            var_dump($e->getMessage());
            die;
        }
    }

    public function PrintReceipt($applyno=0)
    {
        if ($applyno == 0){
            return back()->with('result', 1)->with('message', '查無資料');
        }

        $data = $this->spaceChargesService->getrptSPrptReceipt($applyno);

        $filename = date("YmdHis",time()).rand(2222,3332);

        Excel::load('storage/rpt/SPrptReceipt_1.xls',function($excel) use($data,$filename){
            $excel->sheet(0)->setCellValue('C4', $data[0]['Y']);
            $excel->sheet(0)->setCellValue('C19', $data[0]['Y']);
            $excel->sheet(0)->setCellValue('C34', $data[0]['Y']);

            $excel->sheet(0)->setCellValue('E4', $data[0]['M']);
            $excel->sheet(0)->setCellValue('E19', $data[0]['M']);
            $excel->sheet(0)->setCellValue('E34', $data[0]['M']);

            $excel->sheet(0)->setCellValue('I4', $data[0]['D']);
            $excel->sheet(0)->setCellValue('I19', $data[0]['D']);
            $excel->sheet(0)->setCellValue('I34', $data[0]['D']);

            $excel->sheet(0)->setCellValue('A7', $data[0]['payuser']);
            $excel->sheet(0)->setCellValue('A22', $data[0]['payuser']);
            $excel->sheet(0)->setCellValue('A37', $data[0]['payuser']);

            if(isset($data[0]['f9'])){
                $excel->sheet(0)->setCellValue('D7', $data[0]['f9']);
                $excel->sheet(0)->setCellValue('D22', $data[0]['f9']);
                $excel->sheet(0)->setCellValue('D37', $data[0]['f9']);
            } else {
                $excel->sheet(0)->setCellValue('D7', '');
                $excel->sheet(0)->setCellValue('D22', '');
                $excel->sheet(0)->setCellValue('D37', '');
            }

            if(isset($data[0]['f8'])){
                $excel->sheet(0)->setCellValue('E7', $data[0]['f8']);
                $excel->sheet(0)->setCellValue('E22', $data[0]['f8']);
                $excel->sheet(0)->setCellValue('E37', $data[0]['f8']);
            } else {
                $excel->sheet(0)->setCellValue('E7', '');
                $excel->sheet(0)->setCellValue('E22', '');
                $excel->sheet(0)->setCellValue('E37', '');
            }

            if(isset($data[0]['f7'])){
                $excel->sheet(0)->setCellValue('F7', $data[0]['f7']);
                $excel->sheet(0)->setCellValue('F22', $data[0]['f7']);
                $excel->sheet(0)->setCellValue('F37', $data[0]['f7']);
            } else {
                $excel->sheet(0)->setCellValue('F7', '');
                $excel->sheet(0)->setCellValue('F22', '');
                $excel->sheet(0)->setCellValue('F37', '');
            }

            if(isset($data[0]['f6'])){
                $excel->sheet(0)->setCellValue('H7', $data[0]['f6']);
                $excel->sheet(0)->setCellValue('H22', $data[0]['f6']);
                $excel->sheet(0)->setCellValue('H37', $data[0]['f6']);
            } else {
                $excel->sheet(0)->setCellValue('H7', '');
                $excel->sheet(0)->setCellValue('H22', '');
                $excel->sheet(0)->setCellValue('H37', '');
            }

            if(isset($data[0]['f5'])){
                $excel->sheet(0)->setCellValue('J7', $data[0]['f5']);
                $excel->sheet(0)->setCellValue('J22', $data[0]['f5']);
                $excel->sheet(0)->setCellValue('J37', $data[0]['f5']);
            } else {
                $excel->sheet(0)->setCellValue('J7', '');
                $excel->sheet(0)->setCellValue('J22', '');
                $excel->sheet(0)->setCellValue('J37', '');
            }

            if(isset($data[0]['f4'])){
                $excel->sheet(0)->setCellValue('K7', $data[0]['f4']);
                $excel->sheet(0)->setCellValue('K22', $data[0]['f4']);
                $excel->sheet(0)->setCellValue('K37', $data[0]['f4']);
            } else {
                $excel->sheet(0)->setCellValue('K7', '');
                $excel->sheet(0)->setCellValue('K22', '');
                $excel->sheet(0)->setCellValue('K37', '');
            }

            if(isset($data[0]['f3'])){
                $excel->sheet(0)->setCellValue('M7', $data[0]['f3']);
                $excel->sheet(0)->setCellValue('M22', $data[0]['f3']);
                $excel->sheet(0)->setCellValue('M37', $data[0]['f3']);
            } else {
                $excel->sheet(0)->setCellValue('M7', '');
                $excel->sheet(0)->setCellValue('M22', '');
                $excel->sheet(0)->setCellValue('M37', '');
            }

            if(isset($data[0]['f2'])){
                $excel->sheet(0)->setCellValue('N7', $data[0]['f2']);
                $excel->sheet(0)->setCellValue('N22', $data[0]['f2']);
                $excel->sheet(0)->setCellValue('N37', $data[0]['f2']);
            } else {
                $excel->sheet(0)->setCellValue('N7', '');
                $excel->sheet(0)->setCellValue('N22', '');
                $excel->sheet(0)->setCellValue('N37', '');
            }

            if(isset($data[0]['f1'])){
                $excel->sheet(0)->setCellValue('O7', $data[0]['f1']);
                $excel->sheet(0)->setCellValue('O22', $data[0]['f1']);
                $excel->sheet(0)->setCellValue('O37', $data[0]['f1']);
            } else {
                $excel->sheet(0)->setCellValue('O7', '');
                $excel->sheet(0)->setCellValue('O22', '');
                $excel->sheet(0)->setCellValue('O37', '');
            }

            if(isset($data[0]['c5'])){
                $excel->sheet(0)->setCellValue('B10', $data[0]['c5']);
                $excel->sheet(0)->setCellValue('B25', $data[0]['c5']);
                $excel->sheet(0)->setCellValue('B40', $data[0]['c5']);
            } else {
                $excel->sheet(0)->setCellValue('B10', '');
                $excel->sheet(0)->setCellValue('B25', '');
                $excel->sheet(0)->setCellValue('B40', '');
            }

            if(isset($data[0]['c4'])){
                $excel->sheet(0)->setCellValue('E10', $data[0]['c4']);
                $excel->sheet(0)->setCellValue('E25', $data[0]['c4']);
                $excel->sheet(0)->setCellValue('E40', $data[0]['c4']);
            } else {
                $excel->sheet(0)->setCellValue('E10', '');
                $excel->sheet(0)->setCellValue('E25', '');
                $excel->sheet(0)->setCellValue('E40', '');
            }

            if(isset($data[0]['c3'])){
                $excel->sheet(0)->setCellValue('J10', $data[0]['c3']);
                $excel->sheet(0)->setCellValue('J25', $data[0]['c3']);
                $excel->sheet(0)->setCellValue('J40', $data[0]['c3']);
            } else {
                $excel->sheet(0)->setCellValue('J10', '');
                $excel->sheet(0)->setCellValue('J25', '');
                $excel->sheet(0)->setCellValue('J40', '');
            }

            if(isset($data[0]['c2'])){
                $excel->sheet(0)->setCellValue('N10', $data[0]['c2']);
                $excel->sheet(0)->setCellValue('N25', $data[0]['c2']);
                $excel->sheet(0)->setCellValue('N40', $data[0]['c2']);
            } else {
                $excel->sheet(0)->setCellValue('N10', '');
                $excel->sheet(0)->setCellValue('N25', '');
                $excel->sheet(0)->setCellValue('N40', '');
            }

            if(isset($data[0]['c1'])){
                $excel->sheet(0)->setCellValue('Q10', $data[0]['c1']);
                $excel->sheet(0)->setCellValue('Q25', $data[0]['c1']);
                $excel->sheet(0)->setCellValue('Q40', $data[0]['c1']);
            } else {
                $excel->sheet(0)->setCellValue('Q10', '');
                $excel->sheet(0)->setCellValue('Q25', '');
                $excel->sheet(0)->setCellValue('Q40', '');
            }

            $excel->sheet(0)->cell('A1:U45', function($cell) {
                $cell->setAlignment('center');
                $cell->setFontFamily('標楷體');
            });

        })->setFilename($filename)->download('xls');
    }
}

?>