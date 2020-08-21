<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassMaterialService;
use App\Models\T10tb;
use DB;


class ClassMaterialController extends Controller
{
    /**
     * ClassMaterialController constructor.
     * @param ClassMaterialService $classMaterialService
     */
    public function __construct(ClassMaterialService $classMaterialService)
    {
        $this->classMaterialService = $classMaterialService;
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
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->classMaterialService->getClassMaterialList($queryData);
        // 已選取的素材
        $selectMaterial = $this->classMaterialService->selectMaterial($queryData);
        // 取得班別列表
        $classList = $this->classMaterialService->getClassList();
        // 取得本班講座
        $teacher = $this->classMaterialService->getTeacher($data);

        return view('admin/class_material/list', compact('data', 'queryData', 'selectMaterial', 'classList', 'teacher'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // 取得POST資料
        $class = $request->input('class');
        $term = $request->input('term');
        $material = $request->input('material');

        if (is_array($material)){
            foreach ($material as $course => $row) {


                // 刪除該課程的教材
                T10tb::where('class', $class)->where('term', $term)->where('course', $course)->delete();

                // 迴圈新增
                foreach ($row as $va) {
                    $ary = array_diff(explode('__', $va), array(null, 'null', '', ' '));

                    $newData = array(
                        'class' => $class,
                        'term' => $term,
                        'course' => $course,
                        'idno' => $ary[0],
                        'handoutno' => $ary[1],
                    );

                    T10tb::create($newData);
                }
            }
        }
        // 日期格式

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

            $data = DB::select('SELECT DISTINCT term FROM t04tb WHERE class = \''.$class.'\'');
        } else {

            $data = DB::select('SELECT DISTINCT term FROM t38tb WHERE meet = \''.$class.'\'');
        }

        $result = '';

        foreach ($data as $va) {
            $result .= '<option value="'.$va->term.'"';
            $result .= ($selected == $va->term)? ' selected>' : '>';
            $result .= $va->term.'</option>';
        }

        return $result;
    }

    /**
     * 取得課程教材
     *
     * @param Request $request
     * @return string
     */
    public function getCourseMaterial(Request $request)
    {
        $s1 = $request->input('s1');
        $s2 = $request->input('s2');

        // 取得身分證字號
        if ($s1) {
            // id搜尋
            $idno = $s1;
        } else {
            // 姓名搜尋身分證字號
            $sql = "SELECT  LEFT(idno,10) AS 身分證字號, dept AS 服務機關, 
            position AS 現職, cname,idno 
            FROM m01tb WHERE cname='".$s2."'";

            $idno = DB::select($sql);

            $idno = (isset($idno[0]->idno))? $idno[0]->idno : false;
        }



        if ($idno) {
            $sql = "             
                SELECT A.class AS t10tb_class,A.term AS t10tb_term,A.handoutno AS t10tb_handoutno, /*教材代碼*/
                C.course AS t06tb_course,C.date AS t06tb_date,C.name AS t06tb_name,B.idno AS t08tb_idno,
                B.cname AS t08tb_cname,D.handout AS m08tb_handout,D.date AS m08tb_date, D.filename AS m08tb_filename
                FROM t10tb A 
                INNER JOIN t08tb B ON A.class=B.class  AND A.term=B.term
                AND A.course=B.course  AND A.idno=B.idno 
                INNER JOIN t06tb C  ON A.class=C.class AND A.term=C.term  AND A.course=C.course 
                INNER JOIN m08tb D ON A.handoutno=D.serno  WHERE B.hire='Y' 
                      
                AND A.idno='".$idno."'
                
                ORDER BY A.class DESC, A.term ASC
            ";

            $data = DB::select($sql);
        } else {
            $data = false;
        }

        $result = '';

        if ($data && is_array($data)) {
            foreach ($data as $key => $va) {
                $checked = ( ! $key)? 'checked' : '';
                $result .= '
                    <tr>
                        <td><input '.$checked.' type="radio" name="selected_material" data-handoutno="'.$va->t10tb_handoutno.'" data-idno="'.$va->t08tb_idno.'" data-handout="'.$va->m08tb_handout.'"></td>
                        <td> '.$va->m08tb_handout.'</td>
                        <td>'.$va->m08tb_date.'</td>
                    </tr>
                ';
            }
        }

        return $result;
    }
}
