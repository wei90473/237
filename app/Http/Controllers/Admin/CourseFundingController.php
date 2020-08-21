<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CourseFundingController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('course_funding', $user_group_auth)){
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

        $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class ORDER BY t04tb.class DESC");
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=DB::select("SELECT  DISTINCT term FROM t04tb WHERE class ='".$temp[0][$arraykeys[0]]."' ORDER By term");
        $termArr=$temp;
        $result = '';

        return view('admin/course_funding/list',compact('classArr','termArr' ,'result'));
    }

    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT  DISTINCT term FROM t04tb WHERE class ='".$request->input('classes')."' ORDER By term");
        $termArr= $temp;
        return $termArr;
    }

    public function export(Request $request)
    {
        $classes=$request->get('classes');
        $terms=$request->get('terms');
        $counttype=$request->get('counttype');
        if($terms!="0"){
            $sql="SELECT sdate, edate, quota FROM t04tb WHERE class='".$classes."' AND term='".$terms."'";
            $temp=json_decode(json_encode(DB::select($sql)), true);
            $classdata=$temp;
        }
        $sql="SELECT RTRIM(name) AS name,period,  kind,  day  FROM t01tb WHERE class='".$classes."'";
        $temp=json_decode(json_encode(DB::select($sql)), true);
        $basic=$temp;
        $sql="SELECT DISTINCT accname FROM s06tb WHERE yerly='".substr($classes,0,3)."' AND acccode IN
             (SELECT kind FROM t07tb  WHERE class='".$classes."')";
        $temp=json_decode(json_encode(DB::select($sql)), true);
        $accname="";
        for($i=0;$i<sizeof($temp);$i++)
        {
            if($i!=(sizeof($temp)-1))
                $accname.=$temp[$i]["accname"]."、";
            else
                $accname.=$temp[$i]["accname"];
        }

        $conditionA="";
        $conditionB="";
        $caption="";
        $subject="";
        $period="";
        $periodunit="";
        //1:週 2:天 3:小時
        switch ($basic[0]["kind"]) {
            case "1":
                $periodunit="週";
              break;
            case "2":
                $periodunit="天";
              break;
            case "3":
                $periodunit="小時";
              break;
          }

        //prepare content and condition
        if($terms==0){
            $period="本班每期訓練計".$basic[0]["period"]."天(".$basic[0]["day"]."日)";
        }else{
            $caption=$basic[0]["name"]."第".strval((int)$terms)."期程經費概算表";
            $period="本班次於".substr($classdata[0]["sdate"],0,3)."年".substr($classdata[0]["sdate"],3,2)."月".substr($classdata[0]["sdate"],5,2).
            "日至".substr($classdata[0]["edate"],0,3)."年".substr($classdata[0]["edate"],3,2)."月".substr($classdata[0]["edate"],5,2).
            "日訓練計".$basic[0]["period"].$periodunit."(".$basic[0]["day"]."日)預計".$classdata[0]["quota"]."人參訓";
        }
        $subject="擬於".$accname."之項下開支";


        if($counttype==1)
        {
            $caption=$basic[0]["name"]."課程經費概算表";
            // fetch rough accounting data
            if($terms==0){
                $sql="SELECT SUM(inlectamt) AS 內聘鐘點費金額, SUM(burlectamt) AS 本局鐘點費金額, SUM(outlectamt) AS 外聘鐘點費金額, SUM(othlectamt) AS 其他鐘點費金額,
                SUM(motoramt) AS 汽車金額,SUM(trainamt) AS 火車金額,SUM(planeamt) AS 飛機金額,SUM(noteamt) AS 稿費金額,SUM(speakamt) AS 講演費金額,SUM(drawamt) AS 課程規劃金額,
                SUM(sinamt) AS 單人房金額,SUM(doneamt) AS 雙人房金額,SUM(vipamt) AS 行政套房住宿金額,SUM(meaamt) AS 早餐金額,SUM(lunamt) AS 午餐金額,
                SUM(dinamt) AS 晚餐金額,SUM(docamt) AS 教材金額,SUM(penamt) AS 文具金額,SUM(penamt) AS 文具金額,SUM(penamt) AS 文具金額,SUM(insamt) AS 保險費金額,
                SUM(actamt) AS 活動費金額,SUM(caramt) AS 租車費金額,SUM(placeamt) AS 場地租借金額,SUM(teaamt) AS 茶水費金額,SUM(prizeamt) AS 獎品費金額,
                SUM(birthamt) AS 慶生活動費金額,SUM(unionamt) AS 聯誼活動金額,SUM(setamt) AS 場地佈置費金額,SUM(dishamt) AS 加菜金金額,
                SUM(otheramt1) AS 其他一金額,SUM(otheramt2) AS 其他二金額
                FROM t07tb WHERE class='".$classes."' GROUP BY class";
            }else{

            $sql="SELECT inlectamt AS 內聘鐘點費金額, burlectamt AS 本局鐘點費金額, outlectamt AS 外聘鐘點費金額, othlectamt AS 其他鐘點費金額,
            motoramt AS 汽車金額,trainamt AS 火車金額,planeamt AS 飛機金額,noteamt AS 稿費金額,speakamt AS 講演費金額,drawamt AS 課程規劃金額,
            sinamt AS 單人房金額,doneamt AS 雙人房金額,vipamt AS 行政套房住宿金額,meaamt AS 早餐金額,lunamt AS 午餐金額,
            dinamt AS 晚餐金額,docamt AS 教材金額,penamt AS 文具金額,penamt AS 文具金額,penamt AS 文具金額,insamt AS 保險費金額,
            actamt AS 活動費金額,caramt AS 租車費金額,placeamt AS 場地租借金額,teaamt AS 茶水費金額,prizeamt AS 獎品費金額,
            birthamt AS 慶生活動費金額,unionamt AS 聯誼活動金額,setamt AS 場地佈置費金額,dishamt AS 加菜金金額,
            otheramt1 AS 其他一金額,otheramt2 AS 其他二金額
            FROM t07tb
            WHERE class='".$classes."' AND term='".$terms."' /*概算*/";
            }

            $temp=json_decode(json_encode(DB::select($sql)), true);

            if($temp==[]){
                $RptBasic = new \App\Rptlib\RptBasic();
                $tmp=$RptBasic->getclassEx();
                $classArr=$tmp;
                $tmp=json_decode(json_encode($tmp), true);
                $arraykeys=array_keys((array)$tmp[0]);
                $tmp=$RptBasic->getTerms($tmp[0][$arraykeys[0]]);
                $termArr=$tmp;
                $result = '查無此班期的概算資料。';

                return view('admin/course_funding/list',compact('classArr','termArr' ,'result'));
            }

            $rough=$temp;
            $roughkey=array_keys((array)$rough[0]);

            $sql="SELECT
            A.inlectunit AS 內聘鐘點費單價,A.burlectunit AS 本局鐘點費單價,A.outlectunit AS 外聘鐘點費單價,B.othlectunit AS 其他鐘點費單價,
            A.motorunit AS 汽車單價,'' AS empty1 ,'' AS empty2 ,'' AS empty3,'' AS empty4,B.drawunit AS 課程規劃單價,A.sinunit AS 單人房單價,
            A.doneunit AS 雙人房單價,A.vipunit AS 行政套房住宿單價,A.meaunit AS 早餐單價,A.lununit AS 午餐單價,A.dinunit AS 晚餐單價,
            A.docunit AS 教材單價,A.spenunit AS 短期文具單價,A.mpenunit AS 中期文具單價,A.lpenunit AS 長期文具單價,A.insunit AS 保險費單價,
            A.actunit AS 活動費單價,'' AS empty5,B.placeunit AS 場地租借單價,A.teaunit AS 茶水費單價,A.prizeunit AS 獎品費單價,
            A.birthunit AS 慶生活動費單價,A.unionunit AS 聯誼活動單價,A.setunit AS 場地佈置費單價,A.dishunit AS 加菜金單價
            FROM s02tb A INNER JOIN t07tb B ON 1=1
            WHERE class='".$classes."' ORDER BY B.term DESC LIMIT 1";
            $temp=json_decode(json_encode(DB::select($sql)), true);
            $runitprice=$temp;
            $runitpricekey=array_keys((array)$runitprice[0]);

            // 檔案名稱
            $fileName = 'F13';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel，
            $objPHPExcel = IOFactory::load($filePath);

            $objSheet = $objPHPExcel->getsheet(0);

            //set captions
            $objSheet->setCellValue('A1',$caption);
            $objSheet->setCellValue('A2',$period);
            $objSheet->setCellValue('A3',$subject);
            $objSheet->getHeaderFooter()->setOddFooter("&L&B ".$classes);
            //set accounting data
            for($j=0;$j<sizeof($roughkey);$j++){
                $objSheet->setCellValue('G'.strval($j+5),$rough[0][$roughkey[$j]]);
                if($runitprice[0][$runitpricekey[$j]]!="")
                    $objSheet->setCellValue('F'.strval($j+5),$runitprice[0][$runitpricekey[$j]]);
            }


        }else{
            $caption=$basic[0]["name"]."課程經費結算表";
            //取得【t07tb 經費概（結）算資料】金額的資料
            if($terms==0){
                $conditionA="WHERE class='".$classes."' ";
                $conditionB="WHERE  class='".$classes."' GROUP BY class";
            }else{
                $conditionA="WHERE class='".$classes."' AND term='".$terms."'";
                $conditionB=$conditionA;
            }


            $sql="SELECT
            SUM(trainamt) AS 火車金額,
            SUM(planeamt) AS 飛機金額,
            SUM(noteamt) AS 稿酬,
            SUM(speakamt) AS 講演費,
            SUM(docamt) AS 教材金額,
            SUM(caramt) AS 租車費金額,
            SUM(otheramt1) AS 其他一金額,
            SUM(otheramt2) AS 其他二金額
            FROM t07tb ".$conditionB;

            $temp=json_decode(json_encode(DB::select($sql)), true);

            if($temp==[]){
                $RptBasic = new \App\Rptlib\RptBasic();
                $tmp=$RptBasic->getclassEx();
                $classArr=$tmp;
                $tmp=json_decode(json_encode($tmp), true);
                $arraykeys=array_keys((array)$tmp[0]);
                $tmp=$RptBasic->getTerms($tmp[0][$arraykeys[0]]);
                $termArr=$tmp;
                $result = '查無此班期的結算資料。';

                return view('admin/course_funding/list',compact('classArr','termArr' ,'result'));
            }

            $count=$temp;
            $countkey=array_keys((array)$count[0]);


            //取得【t07tb 經費概（結）算資料】單價的資料
            $sql="SELECT
            inlectunit AS 內聘鐘點費單價,
            burlectunit AS 本局鐘點費單價,
            outlectunit AS 外聘鐘點費單價,
            othlectunit AS 其他鐘點費單價,
            motorunit AS 汽車單價,
            '' AS empty1,
            '' AS empty2,
            '' AS empty3,
            '' AS empty4,
            drawunit AS 課程規劃單價,
            sinunit AS 單人房單價,
            doneunit AS 雙人房單價,
            vipunit AS 行政套房住宿單價,
            meaunit AS 早餐單價,
            lununit AS 午餐單價,
            dinunit AS 晚餐單價,
            docunit AS 教材單價,
            penunit AS 文具單價,
            penunit AS 文具單價,
            penunit AS 文具單價,
            insunit AS 保險費單價,
            actunit AS 活動費單價,
            '' AS empty5,
            placeunit AS 場地租借單價,
            teaunit AS 茶水費單價,
            prizeunit AS 獎品費單價,
            birthunit AS 慶生活動費單價,
            unionunit AS 聯誼活動單價,
            setunit AS 場地佈置費單價,
            dishunit AS 加菜金單價
            FROM t07tb ".$conditionA;

            $temp=json_decode(json_encode(DB::select($sql)), true);
            $cunitprice=$temp;
            $cunitpricekey=array_keys((array)$cunitprice[0]);

            //取得【t07tb 經費概（結）算資料】數量的資料
            $sql="SELECT
            SUM(inlecthr) AS 內聘鐘點費時數,
            SUM(burlecthr) AS 本局鐘點費時數,
            SUM(outlecthr) AS 外聘鐘點費時數,
            SUM(othlecthr) AS 其他鐘點費時數,
            SUM(motorcnt) AS 汽車人次,
            '' AS empty1,
            '' AS empty2,
            '' AS empty3,
            '' AS empty4,
            SUM(drawcnt) AS 課程規劃次數,
            SUM(sincnt) AS 單人房人數,
            SUM(donecnt) AS 雙人房人數,
            SUM(vipcnt) AS 行政套房住宿人數,
            SUM(meacnt) AS 早餐人數,
            SUM(luncnt) AS 午餐人數,
            SUM(dincnt) AS 晚餐人數,
            SUM(doccnt) AS 教材人份,
            SUM(pencnt) AS 文具人份,
            SUM(pencnt) AS 文具人份,
            SUM(pencnt) AS 文具人份,
            SUM(inscnt) AS 保險費人數,
            SUM(actcnt) AS 活動費人數,
            '' AS empty5,
            SUM(placecnt) AS 場地租借場次,
            SUM(teacnt) AS 茶水費場次,
            SUM(prizecnt) AS 獎品費班次,
            SUM(birthcnt) AS 慶生活動費班次,
            SUM(unioncnt) AS 聯誼活動班次,
            SUM(setcnt) AS 場地佈置費班次,
            SUM(dishcnt ) AS 加菜金次數,
            '' AS 其他一金額,
            '' AS 其他二金額
            FROM t07tb ".$conditionB;

            $temp=json_decode(json_encode(DB::select($sql)), true);
            $number=$temp;
            $numberkey=array_keys((array)$number[0]);

            // 檔案名稱
            $fileName = 'F13';
             //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel，
            $objPHPExcel = IOFactory::load($filePath);

            $objSheet = $objPHPExcel->getsheet(0);

            //set captions
            $objSheet->setCellValue('A1',$caption);
             $objSheet->setCellValue('A2',$period);
            $objSheet->setCellValue('A3',$subject);
            $objSheet->getHeaderFooter()->setOddFooter("&L&B ".$classes);
            //set accounting data
            for($j=0;$j<sizeof($countkey);$j++){
                if($j<5){
                    $objSheet->setCellValue('G'.strval($j+10),$count[0][$countkey[$j]]);
                }
                if($j==5){
                    $objSheet->setCellValue('G'.strval($j+22),$count[0][$countkey[$j]]);
                }
                if($j>5 && $j<8){
                    $objSheet->setCellValue('G'.strval($j+29),$count[0][$countkey[$j]]);
                }

                if($cunitprice[0][$cunitpricekey[$j]]!=""){
                    $objSheet->setCellValue('D'.strval($j+5),$number[0][$numberkey[$j]]);
                    $objSheet->setCellValue('F'.strval($j+5),$cunitprice[0][$cunitpricekey[$j]]);

                }
            }

        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"經費概(結)算表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
       
    }
}
