<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CertificationService;
use App\Models\T47tb;
use DB;


class CertificationController extends Controller
{
    /**
     * CertificationController constructor.
     * @param CertificationService $certificationService
     */
    public function __construct(CertificationService $certificationService)
    {
        $this->certificationService = $certificationService;
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 取得班別
        $queryData['class'] = $request->get('class');
        // 取得期別
        $queryData['term'] = $request->get('term');
        // 取得列表資料
        $data = $this->certificationService->getCertificationList($queryData);
        $data = (isset($data[0]))? $data[0] : array();
        // 取得課程列表
        $classList = $this->certificationService->getClassList();

        return view('admin/certification/list', compact('data', 'queryData', 'classList'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $data['upload2'] = $request->input('upload2');
        $data['grade'] = $request->input('grade')? $request->input('grade') : 'N';
        $data['leave'] = $request->input('leave')? $request->input('leave') : 'N';

        $class = $request->input('class');
        $term = $request->input('term');

        T47tb::where('class', $class)->where('term', $term)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');
    }

    /**
     * 取得期別
     *
     * @param $class
     * @return string
     */
    public function getTerm(Request $request)
    {
        $class = $request->input('classes');

        $selected = $request->input('selected');

        if (is_numeric( mb_substr($class, 0, 1))) {

            $data = DB::select('SELECT DISTINCT term FROM t04tb WHERE class = \''.$class.'\' ORDER BY `term`');
        } else {

            $data = DB::select('SELECT DISTINCT term FROM t38tb WHERE meet = \''.$class.'\' ORDER BY `term`');
        }

        $result = '';

        foreach ($data as $va) {
            $result .= '<option value="'.$va->term.'"';
            $result .= ($selected == $va->term)? ' selected>' : '>';
            $result .= $va->term.'</option>';
        }

        return $result;
    }
}
