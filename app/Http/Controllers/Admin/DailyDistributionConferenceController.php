<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DailyDistributionConferenceController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('daily_distribution_conference', $user_group_auth)){
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
        return view('admin/daily_distribution_conference/list');
    }

    /*
    會議場地每日分配表 CSDIR6050
    參考Tables:
    使用範本:N5.xlsx
    'History:
    '2002/10/15 Update
    '1.場地加入【簡報室】
    'm14tb.type  場地類型 1:教室 2:會議室3:KTV 4:宿舍5:簡報室
    'm14tb.type IN ('2','5')
    '
    '2.【t38tb 會議基本資料檔】t38tb.meet  會議代號 char  7  ('')
    '第一碼定義的修改:
    '  T 訓練業務、M 行政會議、R 場地釋放
    '
    '3.【t22tb 場地預約檔】
    '  t22tb.time   時段 A:早 B:午 C:晚 D:其他 E:第一場 F:第二場
    '  若t22tb.time='D'，將場地轉成早上、下午、晚間的格式。
    '
    '時間界線:
    '  早上 0830~1200
    '  下午 1300~1630
    '  晚間 1800~2130
    '
    '例如:
    '  t22tb.stime='0800' t22tb.etime='1400'
    '  -->該筆資料分成早上、下午各有一筆。
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //日期
        $sdatetw = $request->input('sdatetw');
        //取得 會議場地每日分配表
        /*分為3個時間區域,使用Union ALL 串接
        '  早上 0830~1200
        '  下午 1300~1630
        '  晚間 1800~2130
        */
        $sql="SELECT T.time_type,
                        CASE WHEN T.site_name = '人才衡鑑研發室講堂' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '人才衡鑑研發室講堂',
                        CASE WHEN T.site_name = '前瞻廳' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '前瞻廳',
                        CASE WHEN T.site_name = '卓越堂' THEN
                                    CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '卓越堂',
                        CASE WHEN T.site_name = '14樓貴賓廳' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '14樓貴賓廳',
                        CASE WHEN T.site_name = '二樓簡報室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '二樓簡報室',
                        CASE WHEN T.site_name = '二樓貴賓室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '二樓貴賓室',
                        CASE WHEN T.site_name = '三樓會議室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '三樓會議室',
                        CASE WHEN T.site_name = '南投院區第一會議室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區第一會議室',
                        CASE WHEN T.site_name = '南投院區第二會議室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區第二會議室',
                        CASE WHEN T.site_name = '人才衡鑑研發室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '人才衡鑑研發室',
                        CASE WHEN T.site_name = '南投院區612研討室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區612研討室',
                        CASE WHEN T.site_name = '南投院區702教室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區702教室',
                        CASE WHEN T.site_name = '南投院區703教室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區703教室',
                        CASE WHEN T.site_name = '南投院區教材教法室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區教材教法室'
                FROM (SELECT B.name AS site_name,
                                            '上午' AS time_type,
                                            CASE
                                                WHEN IFNULL(C.name,'')<>'' THEN C.name
                                                ELSE IFNULL((SELECT name FROM t38tb WHERE meet=A.class  AND serno=A.term),'')
                                            END  AS name,
                                            CASE
                                                WHEN IFNULL(C.name,'')<>'' THEN A.term ELSE ''
                                                END  AS term,
                                        A.cnt,
                                        A.site
                                FROM t22tb A
                                        INNER JOIN m14tb B ON A.site=B.site
                                        LEFT JOIN t01tb C ON A.class=C.class
                                WHERE B.type IN ('2','5')
                                    AND A.date= REPLACE('".$sdatetw."','/','')
                                    AND ( A.stime BETWEEN '0830' AND '1200' OR
                                                A.etime BETWEEN '0830' AND '1200' )
                                ) T

                UNION ALL
                SELECT T.time_type,
                        CASE WHEN T.site_name = '人才衡鑑研發室講堂' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '人才衡鑑研發室講堂',
                        CASE WHEN T.site_name = '前瞻廳' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '前瞻廳',
                        CASE WHEN T.site_name = '卓越堂' THEN
                                    CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '卓越堂',
                        CASE WHEN T.site_name = '14樓貴賓廳' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '14樓貴賓廳',
                        CASE WHEN T.site_name = '二樓簡報室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '二樓簡報室',
                        CASE WHEN T.site_name = '二樓貴賓室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '二樓貴賓室',
                        CASE WHEN T.site_name = '三樓會議室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '三樓會議室',
                        CASE WHEN T.site_name = '南投院區第一會議室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區第一會議室',
                        CASE WHEN T.site_name = '南投院區第二會議室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區第二會議室',
                        CASE WHEN T.site_name = '人才衡鑑研發室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '人才衡鑑研發室',
                        CASE WHEN T.site_name = '南投院區612研討室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區612研討室',
                        CASE WHEN T.site_name = '南投院區702教室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區702教室',
                        CASE WHEN T.site_name = '南投院區703教室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區703教室',
                        CASE WHEN T.site_name = '南投院區教材教法室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區教材教法室'
                FROM (SELECT B.name AS site_name,
                                            '下午' AS time_type,
                                            CASE
                                                WHEN IFNULL(C.name,'')<>'' THEN C.name
                                                ELSE IFNULL((SELECT name FROM t38tb WHERE meet=A.class  AND serno=A.term),'')
                                            END  AS name,
                                            CASE
                                                WHEN IFNULL(C.name,'')<>'' THEN A.term ELSE ''
                                                END  AS term,
                                        A.cnt,
                                        A.site
                                FROM t22tb A
                                        INNER JOIN m14tb B ON
                                        A.site=B.site
                                        LEFT JOIN t01tb C
                                        ON A.class=C.class
                                WHERE B.type IN ('2','5')
                                    AND A.date= REPLACE('".$sdatetw."','/','')
                                    AND ( A.stime BETWEEN '1300' AND '1630' OR
                                                A.etime BETWEEN '1300' AND '1630' )
                                ) T
                UNION ALL
                SELECT T.time_type,
                        CASE WHEN T.site_name = '人才衡鑑研發室講堂' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '人才衡鑑研發室講堂',
                        CASE WHEN T.site_name = '前瞻廳' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '前瞻廳',
                        CASE WHEN T.site_name = '卓越堂' THEN
                                    CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '卓越堂',
                        CASE WHEN T.site_name = '14樓貴賓廳' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '14樓貴賓廳',
                        CASE WHEN T.site_name = '二樓簡報室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '二樓簡報室',
                        CASE WHEN T.site_name = '二樓貴賓室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '二樓貴賓室',
                        CASE WHEN T.site_name = '三樓會議室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '三樓會議室',
                        CASE WHEN T.site_name = '南投院區第一會議室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區第一會議室',
                        CASE WHEN T.site_name = '南投院區第二會議室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區第二會議室',
                        CASE WHEN T.site_name = '人才衡鑑研發室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '人才衡鑑研發室',
                        CASE WHEN T.site_name = '南投院區612研討室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區612研討室',
                        CASE WHEN T.site_name = '南投院區702教室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區702教室',
                        CASE WHEN T.site_name = '南投院區703教室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區703教室',
                        CASE WHEN T.site_name = '南投院區教材教法室' THEN
                                CONCAT(T.name,
                                            CASE WHEN T.term IS NULL OR T.term  ='' THEN '' ELSE CONCAT('\n','第',T.term, '期') END,
                                                CASE WHEN T.cnt > 0 THEN CONCAT('\n',T.cnt,'人') ELSE '' END)
                            ELSE '' END AS '南投院區教材教法室'
                FROM (SELECT B.name AS site_name,
                                            '晚間' AS time_type,
                                            CASE
                                                WHEN IFNULL(C.name,'')<>'' THEN C.name
                                                ELSE IFNULL((SELECT name FROM t38tb WHERE meet=A.class  AND serno=A.term),'')
                                            END  AS name,
                                            CASE
                                                WHEN IFNULL(C.name,'')<>'' THEN A.term ELSE ''
                                                END  AS term,
                                        A.cnt,
                                        A.site
                                FROM t22tb A
                                        INNER JOIN m14tb B ON
                                        A.site=B.site
                                        LEFT JOIN t01tb C
                                        ON A.class=C.class
                                WHERE B.type IN ('2','5')
                                    AND A.date= REPLACE('".$sdatetw."','/','')
                                    AND ( A.stime BETWEEN '1800' AND '2130' OR
                                                A.etime BETWEEN '1800' AND '2130' )
                                ) T
                                UNION ALL
                                SELECT '晚間' AS site_name,
                                            '' AS COL1,  '' AS COL2,  '' AS COL3,  '' AS COL4, '' AS COL5,
                                            '' AS COL6,  '' AS COL7,  '' AS COL8,  '' AS COL9, '' AS COL10,
                                            '' AS COL11, '' AS COL12, '' AS COL13, '' AS COL14
                                FROM DUAL
                                        ";

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'N5';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setCellValue('A1', '日期：'.$sdatetw.'');

        $reportlist = json_decode(json_encode($reportlist), true);

        if(sizeof($reportlist) != 0) {
            //資料by迴圈
            //$linenum=3;
            $tempCol1='';
            $tempCol2='';
            $tempCol3='';
            $tempCol4='';
            $tempCol5='';
            $tempCol6='';
            $tempCol7='';
            $tempCol8='';
            $tempCol9='';
            $tempCol10='';
            $tempCol11='';
            $tempCol12='';
            $tempCol13='';
            $tempCol14='';
            $tempCol15='';
            for ($j=0; $j < sizeof($reportlist); $j++) {
                //項目數量迴圈
                //for ($i=0; $i < sizeof($arraykeys); $i++) {
                    //excel 欄位 1 == A, etc
                    //$NameFromNumber=$this->getNameFromNumber($i+1); //A
                    if($reportlist[$j][$arraykeys[0]]=='上午'){
                        $linenum='3';
                    }elseif ($reportlist[$j][$arraykeys[0]]=='下午'){
                        $linenum='4';
                    }elseif($reportlist[$j][$arraykeys[0]]=='晚間'){
                        $linenum='5';
                    }else{
                        $linenum='3';
                    }

                    if($reportlist[$j][$arraykeys[0]]!=$tempCol1 || $j==0){
                        //4開始
                        //$objActSheet->setCellValue($NameFromNumber.($j+4), $reportlist[$j][$arraykeys[$i]]);
                        //if($i==1){

                        //} else {

                        //}
                        //高 230
                        //$objActSheet->getRowDimension($j+4)->setRowHeight(230);
                        //$objActSheet->getRowDimension($linenum)->setRowHeight(230);
                        $tempCol1=$reportlist[$j][$arraykeys[0]];
                        $tempCol2=$reportlist[$j][$arraykeys[1]];
                        $tempCol3=$reportlist[$j][$arraykeys[2]];
                        $tempCol4=$reportlist[$j][$arraykeys[3]];
                        $tempCol5=$reportlist[$j][$arraykeys[4]];
                        $tempCol6=$reportlist[$j][$arraykeys[5]];
                        $tempCol7=$reportlist[$j][$arraykeys[6]];
                        $tempCol8=$reportlist[$j][$arraykeys[7]];
                        $tempCol9=$reportlist[$j][$arraykeys[8]];
                        $tempCol10=$reportlist[$j][$arraykeys[9]];
                        $tempCol11=$reportlist[$j][$arraykeys[10]];
                        $tempCol12=$reportlist[$j][$arraykeys[11]];
                        $tempCol13=$reportlist[$j][$arraykeys[12]];
                        $tempCol14=$reportlist[$j][$arraykeys[13]];
                        $tempCol15=$reportlist[$j][$arraykeys[14]];

                        //newline
                        //$linenum++;
                    } else {
                        //內容換行
                        if($reportlist[$j][$arraykeys[1]]!='')
                            $tempCol2=$tempCol2.$reportlist[$j][$arraykeys[1]].PHP_EOL;
                        if($reportlist[$j][$arraykeys[2]]!='')
                            $tempCol3=$tempCol3.$reportlist[$j][$arraykeys[2]].PHP_EOL;
                        if($reportlist[$j][$arraykeys[3]]!='')
                            $tempCol4=$tempCol4.$reportlist[$j][$arraykeys[3]].PHP_EOL;
                        if($reportlist[$j][$arraykeys[4]]!='')
                            $tempCol5=$tempCol5.$reportlist[$j][$arraykeys[4]].PHP_EOL;
                        if($reportlist[$j][$arraykeys[5]]!='')
                            $tempCol6=$tempCol6.$reportlist[$j][$arraykeys[5]].PHP_EOL;
                        if($reportlist[$j][$arraykeys[6]]!='')
                            $tempCol7=$tempCol7.$reportlist[$j][$arraykeys[6]].PHP_EOL;
                        if($reportlist[$j][$arraykeys[7]]!='')
                            $tempCol8=$tempCol8.$reportlist[$j][$arraykeys[7]].PHP_EOL;
                        if($reportlist[$j][$arraykeys[8]]!='')
                            $tempCol9=$tempCol9.$reportlist[$j][$arraykeys[8]].PHP_EOL;
                        if($reportlist[$j][$arraykeys[9]]!='')
                            $tempCol10=$tempCol10.$reportlist[$j][$arraykeys[9]].PHP_EOL;
                        if($reportlist[$j][$arraykeys[10]]!='')
                            $tempCol11=$tempCol11.$reportlist[$j][$arraykeys[10]].PHP_EOL;
                        if($reportlist[$j][$arraykeys[11]]!='')
                            $tempCol12=$tempCol12.$reportlist[$j][$arraykeys[11]].PHP_EOL;
                        if($reportlist[$j][$arraykeys[12]]!='')
                            $tempCol13=$tempCol13.$reportlist[$j][$arraykeys[12]].PHP_EOL;
                        if($reportlist[$j][$arraykeys[13]]!='')
                            $tempCol14=$tempCol14.$reportlist[$j][$arraykeys[13]].PHP_EOL;
                        if($reportlist[$j][$arraykeys[14]]!='')
                            $tempCol15=$tempCol15.$reportlist[$j][$arraykeys[14]].PHP_EOL;

                            $objActSheet->setCellValue('A'.($linenum), $reportlist[$j][$arraykeys[0]]);
                            $objActSheet->setCellValue('B'.($linenum), $tempCol2);
                            $objActSheet->setCellValue('C'.($linenum), $tempCol3);
                            $objActSheet->setCellValue('D'.($linenum), $tempCol4);
                            $objActSheet->setCellValue('E'.($linenum), $tempCol5);
                            $objActSheet->setCellValue('F'.($linenum), $tempCol6);
                            $objActSheet->setCellValue('G'.($linenum), $tempCol7);
                            $objActSheet->setCellValue('H'.($linenum), $tempCol8);
                            $objActSheet->setCellValue('I'.($linenum), $tempCol9);
                            $objActSheet->setCellValue('J'.($linenum), $tempCol10);
                            $objActSheet->setCellValue('K'.($linenum), $tempCol11);
                            $objActSheet->setCellValue('L'.($linenum), $tempCol12);
                            $objActSheet->setCellValue('M'.($linenum), $tempCol13);
                            $objActSheet->setCellValue('N'.($linenum), $tempCol14);
                            $objActSheet->setCellValue('O'.($linenum), $tempCol15);
                            //自動換行功能啓用
                            $objActSheet->getStyle('B'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->getStyle('C'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->getStyle('D'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->getStyle('E'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->getStyle('F'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->getStyle('G'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->getStyle('H'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->getStyle('I'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->getStyle('J'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->getStyle('K'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->getStyle('L'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->getStyle('M'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->getStyle('N'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->getStyle('O'.($linenum))->getAlignment()->setWrapText(true);
                    }
                //}

            }

            /*
            $styleArray = [
                'borders' => [
            //只有外框           'outline' => [
                        'allBorders'=> [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $objActSheet->getStyle('A4:'.$NameFromNumber.($j+3))->applyFromArray($styleArray);
            */
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"會議場地每日分配表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }

}
