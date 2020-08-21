<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use App\Services\ClassesPeriodService;
use App\Models\ClassesPeriod;
use App\Models\DemandQty;


class ClassesPeriodController extends Controller
{
    /**
     * 顯示頁
     *
     * @param $demand_qty
     */
    public function show($demand_qty)
    {
        return $this->edit($demand_qty);
    }

    /**
     * 編輯頁
     *
     * @param $demand_qty
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($demand_qty)
    {
        $data = ClassesPeriod::first();

        if ( ! $data) {

            return view('admin/errors/error');
        }
        
        return view('admin/demand_distribution/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $demand_qty)
    {
        // 取得資料
        $data['institution_id'] = $request->input('institution_id');
        $data['qty_require'] = $request->input('qty_require');
        $data['qty_quota'] = $request->input('qty_quota');
        $demandDistributionData['total_qty_require'] = $request->input('total_qty_require');
        $demandDistributionData['total_qty_quota'] = $request->input('total_qty_quota');

        $result = array();
        $idAry = array();
        // 格式轉換
        foreach ($data['institution_id'] as $key => $institution_id) {

            $result[] = array(
                'institution_id' => $institution_id,
                'qty_require' => $data['qty_require'][$key],
                'qty_quota' => $data['qty_quota'][$key],
                'demand_qty' => $demand_qty,
            );

            $idAry[] = $institution_id;
        }

        // 儲存資料庫
        foreach ($result as $va) {

            // 確認是否存在
            if ( ! DemandQty::where('demand_qty', $demand_qty)->where('institution_id', $va['institution_id'])->first()) {
                // 不存在時新增
                DemandQty::create($va);

            } else {
                // 存在時更新
                DemandQty::where('demand_qty', $demand_qty)->where('institution_id', $va['institution_id'])->update($va);
            }
        }

        // 刪除不在陣列中的id
        $data = DemandQty::where('demand_qty', $demand_qty)->whereNotIn('institution_id', $idAry)->get();

        foreach($data as $va){
            // 迴圈刪除
            $va->delete();
        }

        // 更新需求分配班級資料
        ClassesPeriod::where('demand_qty', $demand_qty)->update($demandDistributionData);

        return back()->with('result', '1')->with('message', '儲存成功!');
    }
}
