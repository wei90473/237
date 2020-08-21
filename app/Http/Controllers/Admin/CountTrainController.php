<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;

class CountTrainController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('count_train', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
                //取得班別
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclass();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = '';
                return view('admin/count_train/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    /*
    訓練人數統計表 CSDIR4130
    參考Tables:
    使用範本:J16A.xlsx, J16B.xlsx
    '主要Table:
    't13tb (班別學員)
    't13tb.ecode 學歷代碼 char  1  ('3')
      '1:博士
      '2:碩士
      '3:學士
      '4:軍警校
      '5:專科
      '6:高中職
      '7:其他
    't13tb.race  學員分類 char  1  (‘1’)
      '1:現職
      '2:退休
      '3:里民  (游於藝)
    't01tb 班別基本資料檔
    'm13tb (機關基本資料)
      'M13tb.type  類型  char  1  ('')
      '1: 中央暨所屬單位
      '2: 台北市所屬單位
      '3: 高雄市所屬單位
      '4: 縣市政府所屬單 位
      '5: 其他單位
    'm02tb (學員基本資料)
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //班別
        $classes = $request->input('classes');
        //期別
        $term = $request->input('term');

        //取得type, 後續判斷 是否為游於藝講堂
        $sqlType = "SELECT type
                    FROM t01tb WHERE class= '".$classes."'
                    ";
        $reportlistType = DB::select($sqlType);
        $dataArrType = json_decode(json_encode($reportlistType), true);

        //取得 CLASSNAME
        $sqlCLASSNAME = "SELECT CONCAT(t01tb.name,'第',t04tb.term,'期') AS CLASSNAME
                FROM t04tb INNER JOIN t01tb ON t01tb.class = t04tb.class
                WHERE t04tb.class='".$classes."'
                AND t04tb.term = '".$term."'
                ";
        $reportlistC = DB::select($sqlCLASSNAME);
        $dataArrC = json_decode(json_encode($reportlistC), true);

        /* 取得 訓練人數統計表
            't13tb.ecode 學歷代碼 char  1  ('3')
            '1:博士
            '2:碩士
            '3:學士
            '4:軍警校
            '5:專科
            '6:高中職
            '7:其他

            't13tb.race  學員分類 char  1  (‘1’)
            '1:現職
            '2:退休
            '3:里民  (游於藝)

            't01tb 班別基本資料檔
            'm13tb (機關基本資料)
            'M13tb.type  類型  char  1  ('')
            '1: 中央暨所屬單位
            '2: 台北市所屬單位
            '3: 高雄市所屬單位
            '4: 縣市政府所屬單 位
            '5: 其他單位
        */
        $sql = "SELECT COUNT(CASE B.sex WHEN 'M' THEN 1 ELSE NULL END) AS SEX_M,
                        COUNT(CASE B.sex WHEN 'F' THEN 1 ELSE NULL END) AS SEX_F,
                        COUNT(B.sex) AS SEX,
                        COUNT(CASE WHEN A.age >=20 AND A.age <= 24 THEN 1 ELSE NULL END) AGE20_4,
                        COUNT(CASE WHEN A.age >=25 AND A.age <= 29 THEN 1 ELSE NULL END) AGE25_9,
                        COUNT(CASE WHEN A.age >=30 AND A.age <= 34 THEN 1 ELSE NULL END) AGE30_4,
                        COUNT(CASE WHEN A.age >=35 AND A.age <= 39 THEN 1 ELSE NULL END) AGE35_9,
                        COUNT(CASE WHEN A.age >=40 AND A.age <= 44 THEN 1 ELSE NULL END) AGE40_4,
                        COUNT(CASE WHEN A.age >=45 AND A.age <= 49 THEN 1 ELSE NULL END) AGE45_9,
                        COUNT(CASE WHEN A.age >=50  THEN 1 ELSE NULL END) AGE50,
                        ROUND(AVG(A.age),1) AVG_AGE,
                        COUNT(CASE C.type WHEN '1' THEN 1 ELSE NULL END) AS TYPE_1,
                        COUNT(CASE C.type WHEN '2' THEN 1 ELSE NULL END) AS TYPE_2,
                        COUNT(CASE C.type WHEN '3' THEN 1 ELSE NULL END) AS TYPE_3,
                        COUNT(CASE C.type WHEN '4' THEN 1 ELSE NULL END) AS TYPE_4,
                        COUNT(CASE C.type WHEN '5' THEN 1 ELSE NULL END) AS TYPE_5,
                        COUNT(C.TYPE) AS COUNT_TYPE,
                        COUNT(CASE A.race WHEN '1' THEN 1 ELSE NULL END) AS RACE_1,
                        COUNT(CASE A.race WHEN '2' THEN 1 ELSE NULL END) AS RACE_2,
                        COUNT(CASE A.race WHEN '3' THEN 1 ELSE NULL END) AS RACE_3,
                        COUNT(A.RACE) AS RACE,
                        COUNT(CASE A.ecode WHEN '1' THEN 1 ELSE NULL END) AS ECODE_1,
                        COUNT(CASE A.ecode WHEN '2' THEN 1 ELSE NULL END) AS ECODE_2,
                        COUNT(CASE A.ecode WHEN '3' THEN 1 ELSE NULL END) AS ECODE_3,
                        COUNT(CASE A.ecode WHEN '4' THEN 1 ELSE NULL END) AS ECODE_4,
                        COUNT(CASE A.ecode WHEN '5' THEN 1 ELSE NULL END) AS ECODE_5,
                        COUNT(CASE A.ecode WHEN '6' THEN 1 ELSE NULL END) AS ECODE_6,
                        COUNT(CASE A.ecode WHEN '7' THEN 1 ELSE NULL END) AS ECODE_7,
                        COUNT(A.ecode) AS ECODE
                FROM t13tb A LEFT OUTER JOIN m02tb B ON A.idno = B.idno
                        LEFT OUTER JOIN m13tb C ON A.organ = C.organ
                WHERE A.status='1'
                AND A.class= '".$classes."'
                AND A.term= '".$term."'
                ";
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        //13是否為游於藝講堂, 為不同欄位
        if ($dataArrType[0]['type'] == '13') {
          $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J16B').'.docx');
          $templateProcessor->setValue('RACE_1', ltrim($dataArr[0]['RACE_1'],'0'));
          $templateProcessor->setValue('RACE_2', ltrim($dataArr[0]['RACE_2'],'0'));
          $templateProcessor->setValue('RACE_3', ltrim($dataArr[0]['RACE_3'],'0'));
          $templateProcessor->setValue('RACE', $dataArr[0]['RACE']);
        } else {
          $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J16A').'.docx');
          $templateProcessor->setValue('TYPE_1', ltrim($dataArr[0]['TYPE_1'],'0'));
          $templateProcessor->setValue('TYPE_2', ltrim($dataArr[0]['TYPE_2'],'0'));
          $templateProcessor->setValue('TYPE_3', ltrim($dataArr[0]['TYPE_3'],'0'));
          $templateProcessor->setValue('TYPE_4', ltrim($dataArr[0]['TYPE_4'],'0'));
          $templateProcessor->setValue('TYPE_5', ltrim($dataArr[0]['TYPE_5'],'0'));
          $templateProcessor->setValue('COUNT_TYPE', $dataArr[0]['COUNT_TYPE']);
        }

        $templateProcessor->setValue('CLASSNAME', $dataArrC[0]['CLASSNAME']);
        $templateProcessor->setValue('SEX_M', ltrim($dataArr[0]['SEX_M'],'0'));
        $templateProcessor->setValue('SEX_F', ltrim($dataArr[0]['SEX_F'],'0'));
        $templateProcessor->setValue('SEX', ltrim($dataArr[0]['SEX'],'0'));
        $templateProcessor->setValue('AGE20_4', ltrim($dataArr[0]['AGE20_4'],'0'));
        $templateProcessor->setValue('AGE25_9', ltrim($dataArr[0]['AGE25_9'],'0'));
        $templateProcessor->setValue('AGE30_4', ltrim($dataArr[0]['AGE30_4'],'0'));
        $templateProcessor->setValue('AGE35_9', ltrim($dataArr[0]['AGE35_9'],'0'));
        $templateProcessor->setValue('AGE40_4', ltrim($dataArr[0]['AGE40_4'],'0'));
        $templateProcessor->setValue('AGE45_9', ltrim($dataArr[0]['AGE45_9'],'0'));
        $templateProcessor->setValue('AGE50', ltrim($dataArr[0]['AGE50'],'0'));
        $templateProcessor->setValue('AVG_AGE', $dataArr[0]['AVG_AGE']);
        $templateProcessor->setValue('ECODE_1', ltrim($dataArr[0]['ECODE_1'],'0'));
        $templateProcessor->setValue('ECODE_2', ltrim($dataArr[0]['ECODE_2'],'0'));
        $templateProcessor->setValue('ECODE_3', ltrim($dataArr[0]['ECODE_3'],'0'));
        $templateProcessor->setValue('ECODE_4', ltrim($dataArr[0]['ECODE_4'],'0'));
        $templateProcessor->setValue('ECODE_5', ltrim($dataArr[0]['ECODE_5'],'0'));
        $templateProcessor->setValue('ECODE_6', ltrim($dataArr[0]['ECODE_6'],'0'));
        $templateProcessor->setValue('ECODE_7', ltrim($dataArr[0]['ECODE_7'],'0'));
        $templateProcessor->setValue('ECODE', $dataArr[0]['ECODE']);

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"訓練人數統計表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
