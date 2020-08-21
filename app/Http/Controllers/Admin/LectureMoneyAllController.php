<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Services\User_groupService;

class LectureMoneyAllController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_money_all', $user_group_auth)){
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
        $result="";
        return view('admin/lecture_money_all/list',compact('result'));
    }

    /*
    講座費用請領總表 CSDIR3101
    參考Tables:
    使用範本:H10.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //1:台北院區, 2:南投院區
        $area = $request->input('area');
        //"109/03/08 ~ 109/03/14"
        $weekpicker = $request->input('weekpicker');
        // $sdate = str_replace('/','',substr( $weekpicker,0,9));
        // $edate = str_replace('/','',substr( $weekpicker,12,9));
        $sdate="";
        $edate="";

        $tflag="";
        if($weekpicker!=""){
            try {
                $ttemp=explode(" ",$weekpicker);
                $sdatetmp=explode("/",$ttemp[0]);
                $edatetmp=explode("/",$ttemp[2]);
                $sdate=$sdatetmp[0].$sdatetmp[1].$sdatetmp[2];
                $edate=$edatetmp[0].$edatetmp[1].$edatetmp[2];
                $tflag="1";
                // Validate the value...
            } catch (\Exception $e) {
                    $ttemp="error";
            }

            if($ttemp=="error" || $sdate=="NaNundefinedundefined" )
            {
                $result = "日期格式錯誤，請重新輸入。";
                return view('admin/lecture_money_all/list',compact('result'));
            }
        }

        //1:一般班, 2:代收款班, 3:全部
        $type = $request->input('type');

        /*
        Branch As String     ' 上課地點 1:臺北院區；2:南投院區；
        BranchName As String ' 上課地點 1:臺北院區；2:南投院區；
            ' 【t04tb 開班資料檔】
        ' t04tb.kind 開支科目
        ' kind 開支科目 char 2 (‘’)
        ' 01  在職訓練短期研習班
        ' 02  在職訓練中期研習班
        ' 03  在職訓練長期研習班
        ' 04  國家策略及女性領導者研究班
        ' 05  游於藝講堂
        ' 06  訓練輔導研究行政維持
        ' 07  在職進修專業課程
        ' 08  人力資源研究發展
        ' 09  一般行政 (基本行政工作維持)
        ' 10 代收款
        ' 11 其他
        */

        //Get_Total_Data 取得【經費類合計數】金額
        //一般, 排除 讀取【代收款】的開支科目代碼 10
            //2020/06/11 新需求修改公式
                //#=>國內旅費=飛機高鐵planeamt+汽車捷運mrtamt+火車trainamt+船舶ship+住宿費otheramt
                //#=>演講費=稿費noteamt+演講費speakamt+評閱費review_total
                //#新增=>其他=其他薪資所得other_salary(新增的欄位，加在演講費與總計之間)
                //但總計,與 實付金額 沒有對映修改
        /*    
        $sqlTotal="SELECT SUM(A.lectamt),
                            SUM(A.motoramt),
                            SUM(A.trainamt+A.planeamt+A.otheramt),
                            SUM(A.noteamt+A.speakamt),
                            SUM(A.teachtot+A.tratot),
                            SUM(A.insuretot),
                            SUM(A.deductamt),
                            SUM(A.totalpay)
                    FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                INNER JOIN t01tb D ON A.class=D.class
                                LEFT JOIN m09tb F ON B.sponsor=F.userid
                                INNER JOIN m01tb G ON A.idno = G.idno
                                            LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                    WHERE A.totalpay > 0
                    AND D.branch = '".$area."'
                    AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                    AND B.kind <> '10' AND B.kind <> '14'
                    ";
                    */
        $sqlTotal="SELECT SUM(A.lectamt),
                            SUM(A.motoramt),
                            SUM(IFNULL(A.planeamt+A.mrtamt+A.trainamt+A.ship+A.otheramt,0)),
                            SUM(A.noteamt+A.speakamt+A.review_total),
                            SUM(A.other_salary),
                            SUM(A.teachtot+A.tratot),
                            SUM(A.insuretot),
                            SUM(A.deductamt),
                            SUM(A.totalpay)
                    FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                INNER JOIN t01tb D ON A.class=D.class
                                LEFT JOIN m09tb F ON B.sponsor=F.userid
                                INNER JOIN m01tb G ON A.idno = G.idno
                                            LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                    WHERE A.totalpay > 0
                    AND D.branch = '".$area."'
                    AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                    AND B.kind <> '10' AND B.kind <> '14'
                    ";            
        $reportlistTotal = DB::select($sqlTotal);
        //$dataArrTotal=json_decode(json_encode(DB::select($sqlTotal)), true);
        //取出全部項目
        if(sizeof($reportlistTotal) != 0) {
            $arraykeysTotal=array_keys((array)$reportlistTotal[0]);
        }

        //Get_Total_Data 取得【經費類合計數】金額
        //【代收款】的開支科目代碼 10
            //2020/06/11 新需求修改公式
                //#=>國內旅費=飛機高鐵planeamt+汽車捷運mrtamt+火車trainamt+船舶ship+住宿費otheramt
                //#=>演講費=稿費noteamt+演講費speakamt+評閱費review_total
                //#新增=>其他=其他薪資所得other_salary(新增的欄位，加在演講費與總計之間)
                //但總計,與 實付金額 沒有對映修改
        /*
        $sqlTotal10="SELECT SUM(A.lectamt),
                            SUM(A.motoramt),
                            SUM(A.trainamt+A.planeamt+A.otheramt),
                            SUM(A.noteamt+A.speakamt),
                            SUM(A.teachtot+A.tratot),
                            SUM(A.insuretot),
                            SUM(A.deductamt),
                            SUM(A.totalpay)
                    FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                INNER JOIN t01tb D ON A.class=D.class
                                LEFT JOIN m09tb F ON B.sponsor=F.userid
                                INNER JOIN m01tb G ON A.idno = G.idno
                                            LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                    WHERE A.totalpay > 0
                    AND D.branch = '".$area."'
                    AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                    AND (B.kind = '10' OR B.kind = '14')
                    ";
                    */
        $sqlTotal10="SELECT SUM(A.lectamt),
                            SUM(A.motoramt),
                            SUM(IFNULL(A.planeamt+A.mrtamt+A.trainamt+A.ship+A.otheramt,0)),
                            SUM(A.noteamt+A.speakamt+A.review_total),
                            SUM(A.other_salary),
                            SUM(A.teachtot+A.tratot),
                            SUM(A.insuretot),
                            SUM(A.deductamt),
                            SUM(A.totalpay)
                    FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                INNER JOIN t01tb D ON A.class=D.class
                                LEFT JOIN m09tb F ON B.sponsor=F.userid
                                INNER JOIN m01tb G ON A.idno = G.idno
                                            LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                    WHERE A.totalpay > 0
                    AND D.branch = '".$area."'
                    AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                    AND (B.kind = '10' OR B.kind = '14')
                    ";                    
        $reportlistTotal10 = DB::select($sqlTotal10);
        //$dataArrTotal10=json_decode(json_encode(DB::select($sqlTotal10)), true);
        //取出全部項目
        if(sizeof($reportlistTotal10) != 0) {
            $arraykeysTotal10=array_keys((array)$reportlistTotal10[0]);
        }

        //小計 一般, 不含【代收款】的開支科目代碼 10
            //2020/06/11 新需求修改公式
                //#=>國內旅費=飛機高鐵planeamt+汽車捷運mrtamt+火車trainamt+船舶ship+住宿費otheramt
                //#=>演講費=稿費noteamt+演講費speakamt+評閱費review_total
                //#新增=>其他=其他薪資所得other_salary(新增的欄位，加在演講費與總計之間)
                //但總計,與 實付金額 沒有對映修改        
        /*
        $sqlSubTotal="SELECT CONCAT(RTRIM(IFNULL(DD.accname,'')),'小計') AS accname,
                                '' AS classname,
                                    '' AS term,
                                '' AS sdate,
                                '' AS edate,
                                SUM(A.lectamt),
                                SUM(A.motoramt),
                                SUM(A.trainamt+A.planeamt+A.otheramt),
                                SUM(A.noteamt+A.speakamt),
                                SUM(A.teachtot+A.tratot),
                                SUM(A.insuretot),
                                SUM(A.deductamt),
                                SUM(A.totalpay),
                                    B.kind
                        FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                    INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                    INNER JOIN t01tb D ON A.class=D.class
                                    LEFT JOIN m09tb F ON B.sponsor=F.userid
                                    INNER JOIN m01tb G ON A.idno = G.idno
                                                LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                        WHERE A.totalpay > 0
                        AND D.branch = '".$area."'
                        AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                        AND B.kind <> '10' AND B.kind <> '14'
                        GROUP BY DD.accname
                        ORDER BY B.kind
                    ";
                    */
        $sqlSubTotal="SELECT CONCAT(RTRIM(IFNULL(DD.accname,'')),'小計') AS accname,
                                '' AS classname,
                                    '' AS term,
                                '' AS sdate,
                                '' AS edate,
                                SUM(A.lectamt),
                                SUM(A.motoramt),
                                SUM(IFNULL(A.planeamt+A.mrtamt+A.trainamt+A.ship+A.otheramt,0)),
                                SUM(A.noteamt+A.speakamt+A.review_total),
                                SUM(A.other_salary),
                                SUM(A.teachtot+A.tratot),
                                SUM(A.insuretot),
                                SUM(A.deductamt),
                                SUM(A.totalpay),
                                    B.kind
                        FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                    INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                    INNER JOIN t01tb D ON A.class=D.class
                                    LEFT JOIN m09tb F ON B.sponsor=F.userid
                                    INNER JOIN m01tb G ON A.idno = G.idno
                                                LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                        WHERE A.totalpay > 0
                        AND D.branch = '".$area."'
                        AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                        AND B.kind <> '10' AND B.kind <> '14'
                        GROUP BY DD.accname
                        ORDER BY B.kind
                        ";

        $reportlistSubTotal = DB::select($sqlSubTotal);
        //$dataArrSubTotal=json_decode(json_encode(DB::select($sqlSubTotal)), true);
        //取出全部項目
        if(sizeof($reportlistSubTotal) != 0) {
            $arraykeysSubTotal=array_keys((array)$reportlistSubTotal[0]);
        }

        //代收款合計數 【代收款】的開支科目代碼 10
            //2020/06/11 新需求修改公式
                //#=>國內旅費=飛機高鐵planeamt+汽車捷運mrtamt+火車trainamt+船舶ship+住宿費otheramt
                //#=>演講費=稿費noteamt+演講費speakamt+評閱費review_total
                //#新增=>其他=其他薪資所得other_salary(新增的欄位，加在演講費與總計之間)
                //但總計,與 實付金額 沒有對映修改    
        /*
        $sqlSubTotal10="SELECT CONCAT(RTRIM(IFNULL(DD.accname,'')),'代收款合計數') AS accname,
                                '' AS classname,
                                    '' AS term,
                                '' AS sdate,
                                '' AS edate,
                                SUM(A.lectamt),
                                SUM(A.motoramt),
                                SUM(A.trainamt+A.planeamt+A.otheramt),
                                SUM(A.noteamt+A.speakamt),
                                SUM(A.teachtot+A.tratot),
                                SUM(A.insuretot),
                                SUM(A.deductamt),
                                SUM(A.totalpay),
                                    B.kind
                        FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                    INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                    INNER JOIN t01tb D ON A.class=D.class
                                    LEFT JOIN m09tb F ON B.sponsor=F.userid
                                    INNER JOIN m01tb G ON A.idno = G.idno
                                                LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                        WHERE A.totalpay > 0
                        AND D.branch = '".$area."'
                        AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                        AND (B.kind = '10' OR B.kind = '14')
                        GROUP BY DD.accname
                        ORDER BY B.kind
                    ";
                    */
                    
        $sqlSubTotal10="SELECT CONCAT(RTRIM(IFNULL(DD.accname,'')),'代收款合計數') AS accname,
                                '' AS classname,
                                    '' AS term,
                                '' AS sdate,
                                '' AS edate,
                                SUM(A.lectamt),
                                SUM(A.motoramt),
                                SUM(IFNULL(A.planeamt+A.mrtamt+A.trainamt+A.ship+A.otheramt,0)),
                                SUM(A.noteamt+A.speakamt+A.review_total),
                                SUM(A.other_salary),
                                SUM(A.teachtot+A.tratot),
                                SUM(A.insuretot),
                                SUM(A.deductamt),
                                SUM(A.totalpay),
                                    B.kind
                        FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                    INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                    INNER JOIN t01tb D ON A.class=D.class
                                    LEFT JOIN m09tb F ON B.sponsor=F.userid
                                    INNER JOIN m01tb G ON A.idno = G.idno
                                                LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                        WHERE A.totalpay > 0
                        AND D.branch = '".$area."'
                        AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                        AND (B.kind = '10' OR B.kind = '14')
                        GROUP BY DD.accname
                        ORDER BY B.kind
                        ";

        $reportlistSubTotal10 = DB::select($sqlSubTotal10);
        //$dataArrSubTotal10=json_decode(json_encode(DB::select($sqlSubTotal10)), true);
        //取出全部項目
        if(sizeof($reportlistSubTotal10) != 0) {
            $arraykeysSubTotal10=array_keys((array)$reportlistSubTotal10[0]);
        }

        //秘書室
        //1:一般班, 2:代收款班, 3:全部
            //2020/06/09新需求
                //需求內容：請在秘書室的頁籤，表格最右邊新增一欄「講座」，列出有費用的講座姓名。
                //,G.cname
            //2020/06/11 新需求修改公式
                //#=>國內旅費=飛機高鐵planeamt+汽車捷運mrtamt+火車trainamt+船舶ship+住宿費otheramt
                //#=>演講費=稿費noteamt+演講費speakamt+評閱費review_total
                //#新增=>其他=其他薪資所得other_salary(新增的欄位，加在演講費與總計之間)
                //但總計,與 實付金額 沒有對映修改  
        $sqlPara=" ";
        if($type=='1'){
            $sqlPara=" AND B.kind <> '10' AND B.kind <> '14'
                     GROUP BY A.class,A.term,D.name,B.sdate,B.edate,F.username, DD.accname
                     ORDER BY B.kind, A.class,A.term";
        }elseif($type=='2'){
            $sqlPara=" AND (B.kind = '10' OR B.kind = '14')
                     GROUP BY A.class,A.term,D.name,B.sdate,B.edate,F.username, DD.accname
                     ORDER BY B.kind, A.class,A.term";
        }else{
            $sqlPara=" GROUP BY A.class,A.term,D.name,B.sdate,B.edate,F.username, DD.accname
                       ORDER BY B.kind, A.class,A.term";
        }
        /*
        $sql="SELECT RTRIM(IFNULL(DD.accname,'')) AS accname,
                        CONCAT(RTRIM(D.name),
							(CASE WHEN week(B.edate+'19110000') <> week(B.sdate+'19110000') AND
							           week(B.sdate+'19110000')  - week(CONCAT(SUBSTRING(B.sdate,1,5)+'191100','01')) > 1 THEN
								CONCAT('(第',week(B.sdate+'19110000')-week(CONCAT(SUBSTRING(B.sdate,1,5)+'191100','01')),'週)')
							ELSE '' END)) AS classname,
                        (CASE A.term
                             WHEN '01' THEN '1'
                             WHEN '02' THEN '2'
                             WHEN '03' THEN '3'
                             WHEN '04' THEN '4'
                             WHEN '05' THEN '5'
                             WHEN '06' THEN '6'
                             WHEN '07' THEN '7'
                             WHEN '08' THEN '8'
                             WHEN '09' THEN '9'
                             ELSE A.term END
                        ) AS term,
                        SUM(A.lectamt),
                        SUM(A.motoramt),
                        SUM(A.trainamt+A.planeamt+A.otheramt),
                        SUM(A.noteamt+A.speakamt),
                        SUM(A.teachtot+A.tratot),
                        SUM(A.insuretot),
                        SUM(A.deductamt),
                        SUM(A.totalpay),
                        '".substr( $weekpicker,0,9)."'
                        ,
                        A.class,
                        RTRIM(F.username) 
                        ,A.term AS classterm                       
                FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                            INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                            INNER JOIN t01tb D ON A.class=D.class
                            LEFT JOIN m09tb F ON B.sponsor=F.userid
                            INNER JOIN m01tb G ON A.idno = G.idno
                                        LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                WHERE A.totalpay > 0
                AND D.branch = '".$area."'
                AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                ".$sqlPara;
                */
        $sql="SELECT RTRIM(IFNULL(DD.accname,'')) AS accname,
                        CONCAT(RTRIM(D.name),
                            (CASE   WHEN week(B.edate+'19110000') <> week(B.sdate+'19110000') AND
                                         week(B.sdate+'19110000') - week('".$sdate."'+'19110000') + 1  > 0 THEN
                                         CONCAT('(第',week(B.sdate+'19110000') - week('".$sdate."'+'19110000') + 1 ,'週)')
									WHEN week(B.edate+'19110000') <> week(B.sdate+'19110000') AND
                                         week('".$sdate."'+'19110000')  - week(B.sdate+'19110000') + 1 > 0 THEN
                                         CONCAT('(第',week('".$sdate."'+'19110000')  - week(B.sdate+'19110000') + 1 ,'週)')                                
                                    ELSE '' END)) AS classname,
                        (CASE A.term
                            WHEN '01' THEN '1'
                            WHEN '02' THEN '2'
                            WHEN '03' THEN '3'
                            WHEN '04' THEN '4'
                            WHEN '05' THEN '5'
                            WHEN '06' THEN '6'
                            WHEN '07' THEN '7'
                            WHEN '08' THEN '8'
                            WHEN '09' THEN '9'
                            ELSE A.term END
                        ) AS term,
                        SUM(A.lectamt),
                        SUM(A.motoramt),
                        SUM(IFNULL(A.planeamt+A.mrtamt+A.trainamt+A.ship+A.otheramt,0)),
                        SUM(A.noteamt+A.speakamt+A.review_total),
                        SUM(A.other_salary),
                        SUM(A.teachtot+A.tratot),
                        SUM(A.insuretot),
                        SUM(A.deductamt),
                        SUM(A.totalpay),
                        '".substr( $weekpicker,0,9)."'
                        ,
                        A.class,
                        RTRIM(F.username) 
                        ,A.term AS classterm                       
                FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                            INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                            INNER JOIN t01tb D ON A.class=D.class
                            LEFT JOIN m09tb F ON B.sponsor=F.userid
                            INNER JOIN m01tb G ON A.idno = G.idno
                                        LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                WHERE A.totalpay > 0
                AND D.branch = '".$area."'
                AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                ".$sqlPara;
        $reportlist = DB::select($sql);

        //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'H10';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();

        $reportlist = json_decode(json_encode($reportlist), true);
        $reportlistTotal = json_decode(json_encode($reportlistTotal), true);
        $reportlistTotal10 = json_decode(json_encode($reportlistTotal10), true);
        $reportlistSubTotal = json_decode(json_encode($reportlistSubTotal), true);
        $reportlistSubTotal10 = json_decode(json_encode($reportlistSubTotal10), true);

        $AreaName='';
        if($area=='1'){
            $AreaName='臺北院區';
        }else{
            $AreaName='南投院區';
        }

        //一般班
        $objActSheet = $objPHPExcel->getSheet(0);
        //Title: 行政院人事行政總處公務人力發展學院(XX院區)XXX年XX月XX日至XX月XX日各研習班按日按件計資酬金（講座鐘點費）及交通費清冊總表
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&14'.'行政院人事行政總處公務人力發展學院('.$AreaName.')'.substr($sdate,0,3).'年'.substr($sdate,3,2).'月'.substr($sdate,5,2).'日至'.substr($edate,3,2).'月'.substr($edate,5,2).'日各研習班按日按件計資酬金（講座鐘點費）及交通費清冊總表');
       //1:一般班, 2:代收款班, 3:全部
        if($type=='1' || $type=='3'){
           //total
            if(sizeof($reportlistTotal) != 0) {
                for ($j=0; $j < sizeof($reportlistTotal); $j++) {
                    for ($i=0; $i < sizeof($arraykeysTotal); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+6); //A
                        $objActSheet->setCellValue($NameFromNumber.($j+3), $reportlistTotal[$j][$arraykeysTotal[$i]]);
                    }
                }
            }
   
            //subtotal
            if(sizeof($reportlistSubTotal) != 0) {
                $k=0;
                for ($j=0; $j < sizeof($reportlistSubTotal); $j++) {
                    for ($i=0; $i < sizeof($arraykeysSubTotal); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+1); //A
                        //if($i!=13){
                        if($i!=14){
                            $objActSheet->setCellValue($NameFromNumber.($j+4+$k), $reportlistSubTotal[$j][$arraykeysSubTotal[$i]]);
                            $startline=($j+4+$k);
                        }
                    }

                    //明細
                        //2020/06/11 新需求修改公式
                            //#=>國內旅費=飛機高鐵planeamt+汽車捷運mrtamt+火車trainamt+船舶ship+住宿費otheramt
                            //#=>演講費=稿費noteamt+演講費speakamt+評閱費review_total
                            //#新增=>其他=其他薪資所得other_salary(新增的欄位，加在演講費與總計之間)
                            //但總計,與 實付金額 沒有對映修改      
                    /*               
                    $sqlFee="SELECT RTRIM(IFNULL(DD.accname,'')) AS accname,
                                    CONCAT(RTRIM(D.name),
                                    (CASE WHEN week(B.edate+'19110000') <> week(B.sdate+'19110000') AND
                                            week(B.sdate+'19110000')  - week(CONCAT(SUBSTRING(B.sdate,1,5)+'191100','01')) > 1 THEN
                                        CONCAT('(第',week(B.sdate+'19110000')-week(CONCAT(SUBSTRING(B.sdate,1,5)+'191100','01')),'週)')
                                    ELSE '' END)) AS classname,
                                    (CASE A.term
                                        WHEN '01' THEN '1'
                                        WHEN '02' THEN '2'
                                        WHEN '03' THEN '3'
                                        WHEN '04' THEN '4'
                                        WHEN '05' THEN '5'
                                        WHEN '06' THEN '6'
                                        WHEN '07' THEN '7'
                                        WHEN '08' THEN '8'
                                        WHEN '09' THEN '9'
                                        ELSE A.term END
                                    ) AS term,
                                    B.sdate,
                                    B.edate,
                                    SUM(A.lectamt),
                                    SUM(A.motoramt),
                                    SUM(A.trainamt+A.planeamt+A.otheramt),
                                    SUM(A.noteamt+A.speakamt),
                                    SUM(A.teachtot+A.tratot),
                                    SUM(A.insuretot),
                                    SUM(A.deductamt),
                                    SUM(A.totalpay),
                                        B.kind
                            FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                        INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                        INNER JOIN t01tb D ON A.class=D.class
                                        LEFT JOIN m09tb F ON B.sponsor=F.userid
                                        INNER JOIN m01tb G ON A.idno = G.idno
                                                    LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                            WHERE A.totalpay > 0
                            AND D.branch = '".$area."'
                            AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                            AND B.kind = '".$reportlistSubTotal[$j][$arraykeysSubTotal[13]]."'
                            GROUP BY A.class,A.term,D.name,B.sdate,B.edate,F.username, DD.accname
                            ORDER BY B.kind, A.class,A.term
                                        ";
                                        */
                    $sqlFee="SELECT RTRIM(IFNULL(DD.accname,'')) AS accname,
                                    CONCAT(RTRIM(D.name),
                                    (CASE   WHEN week(B.edate+'19110000') <> week(B.sdate+'19110000') AND
                                                 week(B.sdate+'19110000') - week('".$sdate."'+'19110000') + 1  > 0 THEN
                                                 CONCAT('(第',week(B.sdate+'19110000') - week('".$sdate."'+'19110000') + 1 ,'週)')
									        WHEN week(B.edate+'19110000') <> week(B.sdate+'19110000') AND
                                                 week('".$sdate."'+'19110000')  - week(B.sdate+'19110000') + 1 > 0 THEN
                                                 CONCAT('(第',week('".$sdate."'+'19110000')  - week(B.sdate+'19110000') + 1 ,'週)')                                
                                            ELSE '' END)) AS classname,                                    
                                    (CASE A.term
                                        WHEN '01' THEN '1'
                                        WHEN '02' THEN '2'
                                        WHEN '03' THEN '3'
                                        WHEN '04' THEN '4'
                                        WHEN '05' THEN '5'
                                        WHEN '06' THEN '6'
                                        WHEN '07' THEN '7'
                                        WHEN '08' THEN '8'
                                        WHEN '09' THEN '9'
                                        ELSE A.term END
                                    ) AS term,
                                    B.sdate,
                                    B.edate,
                                    SUM(A.lectamt),
                                    SUM(A.motoramt),
                                    SUM(IFNULL(A.planeamt+A.mrtamt+A.trainamt+A.ship+A.otheramt,0)),
                                    SUM(A.noteamt+A.speakamt+A.review_total),
                                    SUM(A.other_salary),
                                    SUM(A.teachtot+A.tratot),
                                    SUM(A.insuretot),
                                    SUM(A.deductamt),
                                    SUM(A.totalpay),
                                        B.kind
                            FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                        INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                        INNER JOIN t01tb D ON A.class=D.class
                                        LEFT JOIN m09tb F ON B.sponsor=F.userid
                                        INNER JOIN m01tb G ON A.idno = G.idno
                                                    LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                            WHERE A.totalpay > 0
                            AND D.branch = '".$area."'
                            AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                            AND B.kind = '".$reportlistSubTotal[$j][$arraykeysSubTotal[14]]."'
                            GROUP BY A.class,A.term,D.name,B.sdate,B.edate,F.username, DD.accname
                            ORDER BY B.kind, A.class,A.term
                                        ";

                    $reportlistFee = DB::select($sqlFee);
                    //$dataArrFee=json_decode(json_encode(DB::select($sqlFee)), true);
                    if(sizeof($reportlistFee) != 0) {
                        $arraykeysFee=array_keys((array)$reportlistFee[0]);
                    }
                    $reportlistFee = json_decode(json_encode($reportlistFee), true);
                    //明細
                    if(sizeof($reportlistFee) != 0) {
                     
                        for ($d=0; $d < sizeof($reportlistFee); $d++) {
                            $k++;
                            for ($f=0; $f < sizeof($arraykeysFee); $f++) {
                                $NameFromNumber=$this->getNameFromNumber($f+1); //A
                                //if($f!=13 && $f!=0){
                                if($f!=14 && $f!=0){    
                                    $objActSheet->setCellValue($NameFromNumber.($j+4+$k),$reportlistFee[$d][$arraykeysFee[$f]]);
                                }
                            }
                        }
                        $arrayStyleOutline = [
                            'borders' => [
                                    'outline' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => '000000'],
                                ],
                            ],
                        ];
                        // dd(sizeof($arraykeysFee));
                        for($l=0;$l<sizeof($arraykeysFee);$l++) {
                            $NameFromNumber=$this->getNameFromNumber($l+1); //A
                            
                            if($l==1){
                                $objActSheet->getStyle('A'.$startline.':B'.($j+4+$k))->applyFromArray($arrayStyleOutline);
                            }elseif($l>=1){
                                $objActSheet->getStyle($NameFromNumber.$startline.':'.$NameFromNumber.($j+4+$k))->applyFromArray($arrayStyleOutline);
                            }
                        }

                    }

                }
            }
        }

        //代收款
        $objActSheet = $objPHPExcel->getSheet(1);
        //Title: 行政院人事行政總處公務人力發展學院(XX院區)XXX年XX月XX日至XX月XX日各研習班按日按件計資酬金（講座鐘點費）及交通費清冊總表
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&14'.'行政院人事行政總處公務人力發展學院('.$AreaName.')'.substr($sdate,0,3).'年'.substr($sdate,3,2).'月'.substr($sdate,5,2).'日至'.substr($edate,3,2).'月'.substr($edate,5,2).'日各研習班按日按件計資酬金（講座鐘點費）及交通費清冊總表');
       //1:一般班, 2:代收款班, 3:全部
        if($type=='2' || $type=='3'){
         
            //total
            if(sizeof($reportlistTotal10) != 0) {
                for ($j=0; $j < sizeof($reportlistTotal10); $j++) {
                    for ($i=0; $i < sizeof($arraykeysTotal10); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+6); //A
                        $objActSheet->setCellValue($NameFromNumber.($j+3), $reportlistTotal10[$j][$arraykeysTotal10[$i]]);
                    }
                }
            }
        
            //subtotal
            if(sizeof($reportlistSubTotal10) != 0) {
                $k=0;
                for ($j=0; $j < sizeof($reportlistSubTotal10); $j++) {
                    for ($i=0; $i < sizeof($arraykeysSubTotal10); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+1); //A
                        //if($i!=13){
                        if($i!=14){
                            $objActSheet->setCellValue($NameFromNumber.($j+4+$k), $reportlistSubTotal10[$j][$arraykeysSubTotal10[$i]]);
                            $startline=($j+4+$k);
                        }
                    }

                    //明細
                        //2020/06/11 新需求修改公式
                            //#=>國內旅費=飛機高鐵planeamt+汽車捷運mrtamt+火車trainamt+船舶ship+住宿費otheramt
                            //#=>演講費=稿費noteamt+演講費speakamt+評閱費review_total
                            //#新增=>其他=其他薪資所得other_salary(新增的欄位，加在演講費與總計之間)
                            //但總計,與 實付金額 沒有對映修改    
                    /*
                    $sqlFee2="SELECT RTRIM(IFNULL(DD.accname,'')) AS accname,
                                    CONCAT(RTRIM(D.name),
                                    (CASE WHEN week(B.edate+'19110000') <> week(B.sdate+'19110000') AND
                                            week(B.sdate+'19110000')  - week(CONCAT(SUBSTRING(B.sdate,1,5)+'191100','01')) > 1 THEN
                                        CONCAT('(第',week(B.sdate+'19110000')-week(CONCAT(SUBSTRING(B.sdate,1,5)+'191100','01')),'週)')
                                    ELSE '' END)) AS classname,
                                    (CASE A.term
                                        WHEN '01' THEN '1'
                                        WHEN '02' THEN '2'
                                        WHEN '03' THEN '3'
                                        WHEN '04' THEN '4'
                                        WHEN '05' THEN '5'
                                        WHEN '06' THEN '6'
                                        WHEN '07' THEN '7'
                                        WHEN '08' THEN '8'
                                        WHEN '09' THEN '9'
                                        ELSE A.term END
                                    ) AS term,
                                    B.sdate,
                                    B.edate,
                                    SUM(A.lectamt),
                                    SUM(A.motoramt),
                                    SUM(A.trainamt+A.planeamt+A.otheramt),
                                    SUM(A.noteamt+A.speakamt),
                                    SUM(A.teachtot+A.tratot),
                                    SUM(A.insuretot),
                                    SUM(A.deductamt),
                                    SUM(A.totalpay),
                                        B.kind
                            FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                        INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                        INNER JOIN t01tb D ON A.class=D.class
                                        LEFT JOIN m09tb F ON B.sponsor=F.userid
                                        INNER JOIN m01tb G ON A.idno = G.idno
                                                    LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                            WHERE A.totalpay > 0
                            AND D.branch = '".$area."'
                            AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                            AND B.kind = '".$reportlistSubTotal10[$j][$arraykeysSubTotal10[13]]."'
                            GROUP BY A.class,A.term,D.name,B.sdate,B.edate,F.username, DD.accname
                            ORDER BY B.kind, A.class,A.term
                                        ";
                                        */
                    $sqlFee2="SELECT RTRIM(IFNULL(DD.accname,'')) AS accname,
                                        CONCAT(RTRIM(D.name),
                                        (CASE   WHEN week(B.edate+'19110000') <> week(B.sdate+'19110000') AND
                                                 week(B.sdate+'19110000') - week('".$sdate."'+'19110000') + 1  > 0 THEN
                                                 CONCAT('(第',week(B.sdate+'19110000') - week('".$sdate."'+'19110000') + 1 ,'週)')
									        WHEN week(B.edate+'19110000') <> week(B.sdate+'19110000') AND
                                                 week('".$sdate."'+'19110000')  - week(B.sdate+'19110000') + 1 > 0 THEN
                                                 CONCAT('(第',week('".$sdate."'+'19110000')  - week(B.sdate+'19110000') + 1 ,'週)')                                
                                            ELSE '' END)) AS classname,                                          
                                        (CASE A.term
                                            WHEN '01' THEN '1'
                                            WHEN '02' THEN '2'
                                            WHEN '03' THEN '3'
                                            WHEN '04' THEN '4'
                                            WHEN '05' THEN '5'
                                            WHEN '06' THEN '6'
                                            WHEN '07' THEN '7'
                                            WHEN '08' THEN '8'
                                            WHEN '09' THEN '9'
                                            ELSE A.term END
                                        ) AS term,
                                        B.sdate,
                                        B.edate,
                                        SUM(A.lectamt),
                                        SUM(A.motoramt),
                                        SUM(IFNULL(A.planeamt+A.mrtamt+A.trainamt+A.ship+A.otheramt,0)),
                                        SUM(A.noteamt+A.speakamt+A.review_total),
                                        SUM(A.other_salary),
                                        SUM(A.teachtot+A.tratot),
                                        SUM(A.insuretot),
                                        SUM(A.deductamt),
                                        SUM(A.totalpay),
                                            B.kind
                                FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                            INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                            INNER JOIN t01tb D ON A.class=D.class
                                            LEFT JOIN m09tb F ON B.sponsor=F.userid
                                            INNER JOIN m01tb G ON A.idno = G.idno
                                                        LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                                WHERE A.totalpay > 0
                                AND D.branch = '".$area."'
                                AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                                AND B.kind = '".$reportlistSubTotal10[$j][$arraykeysSubTotal10[14]]."'
                                GROUP BY A.class,A.term,D.name,B.sdate,B.edate,F.username, DD.accname
                                ORDER BY B.kind, A.class,A.term
                                            ";

                    $reportlistFee2 = DB::select($sqlFee2);
                    //$dataArrFee=json_decode(json_encode(DB::select($sqlFee)), true);
                    if(sizeof($reportlistFee2) != 0) {
                        $arraykeysFee2=array_keys((array)$reportlistFee2[0]);
                    }
                    $reportlistFee2 = json_decode(json_encode($reportlistFee2), true);
                    //明細
                    if(sizeof($reportlistFee2) != 0) {
                        for ($d=0; $d < sizeof($reportlistFee2); $d++) {
                            $k++;
                            for ($f=0; $f < sizeof($arraykeysFee2); $f++) {
                                $NameFromNumber=$this->getNameFromNumber($f+1); //A
                                //if($f!=13 && $f!=0){
                                if($f!=14 && $f!=0){    
                                    $objActSheet->setCellValue($NameFromNumber.($j+4+$k),$reportlistFee2[$d][$arraykeysFee2[$f]]);
                                }
                            }
                        }
                        $arrayStyleOutline = [
                            'borders' => [
                                    'outline' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => '000000'],
                                ],
                            ],
                        ];
                        for($l=0;$l<sizeof($arraykeysFee2);$l++) {
                            $NameFromNumber=$this->getNameFromNumber($l+1); //A
                            if($l==1){
                                $objActSheet->getStyle('A'.$startline.':B'.($j+4+$k))->applyFromArray($arrayStyleOutline);
                            }elseif($l>=1){
                                $objActSheet->getStyle($NameFromNumber.$startline.':'.$NameFromNumber.($j+4+$k))->applyFromArray($arrayStyleOutline);
                            }
                        }
                    }

                }
            }
        }
     
        //秘書室
        $objActSheet = $objPHPExcel->getSheet(2);
        //Title:.PageSetup.CenterHeader = "&""標楷體,標準""&14" & " " & "講座費用請領總表 (XX院區)"
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&14'.'講座費用請領總表('.$AreaName.')');
        if(sizeof($reportlist) != 0) {
            for ($j=0; $j < sizeof($reportlist); $j++) {
                for ($i=0; $i < sizeof($arraykeys); $i++) {
                    //excel 欄位 1 == A, etc
                    $NameFromNumber=$this->getNameFromNumber($i+1); //A
                    //if($i!=14){
                    if($i!=15){    
                        $objActSheet->setCellValue($NameFromNumber.($j+2), $reportlist[$j][$arraykeys[$i]]);
                    }

                    //講座
                    //if($i==12){
                    if($i==13){    
                        //dd($reportlist[$j][$arraykeys[$i]]);                        
                        $sqlParaT=" ";
                        if($type=='1'){
                            $sqlParaT=" AND B.kind <> '10' AND B.kind <> '14'
                                    GROUP BY A.class,A.term, G.cname";
                        }elseif($type=='2'){
                            $sqlParaT=" AND (B.kind = '10' OR B.kind = '14')
                                    GROUP BY A.class,A.term, G.cname";
                        }else{
                            $sqlParaT=" GROUP BY A.class,A.term, G.cname";
                        }
                        /*
                        $sqlT="SELECT G.cname                       
                                FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                            INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                            INNER JOIN t01tb D ON A.class=D.class
                                            LEFT JOIN m09tb F ON B.sponsor=F.userid
                                            INNER JOIN m01tb G ON A.idno = G.idno
                                                        LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                                WHERE A.totalpay > 0
                                AND D.branch = '".$area."'
                                AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                                AND A.class = '".$reportlist[$j][$arraykeys[$i]]."'
                                AND A.term = '".$reportlist[$j][$arraykeys[14]]."'
                                ".$sqlParaT;
                                */
                        $sqlT="SELECT G.cname                       
                                FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                            INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                            INNER JOIN t01tb D ON A.class=D.class
                                            LEFT JOIN m09tb F ON B.sponsor=F.userid
                                            INNER JOIN m01tb G ON A.idno = G.idno
                                                        LEFT JOIN s06tb DD ON LEFT(C.class,3) = DD.yerly AND B.kind = DD.acccode
                                WHERE A.totalpay > 0
                                AND D.branch = '".$area."'
                                AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                                AND A.class = '".$reportlist[$j][$arraykeys[$i]]."'
                                AND A.term = '".$reportlist[$j][$arraykeys[15]]."'
                                ".$sqlParaT;       
                           
                        $reportlistT = DB::select($sqlT);
                        //$dataArrFee=json_decode(json_encode(DB::select($sqlFee)), true);
                        if(sizeof($reportlistT) != 0) {
                            $arraykeysT=array_keys((array)$reportlistT[0]);
                        }
                        $reportlistT = json_decode(json_encode($reportlistT), true);
                        
                        //明細
                        if(sizeof($reportlistT) != 0) {  
                            $cname = '';
                            for ($d=0; $d < sizeof($reportlistT); $d++) {
                                $cname = $cname.$reportlistT[$d][$arraykeysT[0]].',';
                            }                            
                            //$objActSheet->setCellValue('O'.($j+2), substr_replace($cname,'',-1) );
                            $objActSheet->setCellValue('P'.($j+2), substr_replace($cname,'',-1) );
                        }   
                    }
                }
            }

            $arrayStyle = [
                'borders' => [
                        //只有外框           'outline' => [
                        'allBorders'=> [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];
            //$objActSheet->getStyle('A2:N'.($j+1))->applyFromArray($arrayStyle);
            //$objActSheet->getStyle('A2:O'.($j+1))->applyFromArray($arrayStyle);
            $objActSheet->getStyle('A2:P'.($j+1))->applyFromArray($arrayStyle);
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"講座費用請領總表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
        
    }
}
