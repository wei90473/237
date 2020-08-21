<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Models\T74tb;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Services\User_groupService;

class EachTrainingAllController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('each_training_all', $user_group_auth)){
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
        return view('admin/each_training_all/list',compact('result'));
    }

    public function edit(Request $request)
    {   
        // 取得年
        $dateData['yerly'] = str_pad($request->get('yerly') ,3,'0',STR_PAD_LEFT);
        if($dateData['yerly']=="000")
            $dateData['yerly'] = "";
        // 取得月
        $dateData['month'] = str_pad($request->get('month') ,2,'0',STR_PAD_LEFT);
        if ( $dateData['month']=="00" ) 
            $dateData['month'] = "";
        if ( (int)$dateData['month']>12 )
            $dateData['month'] = "12";

        $data = [];
        $result="";
        return view('admin/each_training_all/form',compact('result','data','dateData'));
    }

    public function query($yerly,$month)
    {
         // 取得年
         $dateData['yerly'] = str_pad($yerly ,3,'0',STR_PAD_LEFT);
         if($dateData['yerly']=="000")
            $dateData['yerly'] = strval((int)date('Y')-1911);
         // 取得月
         $dateData['month'] = str_pad($month,2,'0',STR_PAD_LEFT);
         if ( $dateData['month']=="00" ) 
            $dateData['month'] = "01";
         if ( (int)$dateData['month']>12 )
            $dateData['month'] = "12";

         $t74tbcons="";
 
         $t74tbcons.="WHERE yerly = '".$dateData['yerly']."' AND mon = '".$dateData['month']."' GROUP BY type) A  LEFT JOIN D12_C B ON A.type = B.traintype ";
 
         if($dateData['yerly']>=100)
             $t74tbcons.="ORDER BY B.seq100 ";
         else
             $t74tbcons.="ORDER BY B.seq99 ";
             
         //查詢【t74tb 訓練成果檔】資料 (自行登打資料)
         $sql="
         SELECT B.traintype_name AS traintype_name, A.* 
         FROM
         (SELECT 
         yerly,mon,
         SUM(termcnt) AS termcnt, 
         SUM(headcnt) AS headcnt, 
         SUM(daycnt) AS daycnt, 
         SUM(hourcnt) AS hourcnt, 
         type
         FROM t74tb ".$t74tbcons;
 
         $temp = json_decode(json_encode(DB::select($sql)), true);
         $data = $temp;



        if($data==[]){
            //設定類別陣列
            $typearr=array('1','2','3','14','A','B','C');
            
            foreach ($typearr as $t) {
                for($i=1;$i<13;$i++){
                    $data=array(
                        "yerly" =>$dateData['yerly'],
                        "mon" => str_pad(strval($i),2,'0',STR_PAD_LEFT),
                        "type" => strval($t),
                        "termcnt" => "0",
                        "headcnt" => "0",
                        "daycnt" => "0",
                        "hourcnt" => "0"
                    );
                    $result=T74tb::firstOrCreate($data);
                }
            }
            $temp = json_decode(json_encode(DB::select($sql)), true);
            $data = $temp;

        }

         $result="";
         return view('admin/each_training_all/form',compact('result','data','dateData'));
    }

    public function save(Request $request)
    {
        $rdata = $request->all();
        $y=$rdata["startYear"];
        $m=$rdata["startMonth"];

        $fieldarr=array('termcnt','headcnt','daycnt','hourcnt');
        $typearr=array('1','2','3','14','A','B','C');
        //更新t74tb
        foreach($typearr as $t ){
            foreach($fieldarr as $f ){
                $udata[$f] = $rdata[$y."_".$m."_".$t."_".$f];
                T74tb::where('yerly', $y)->where('mon', $m)->where('type', $t)->update($udata);
            }
        }

        $dateData['yerly']=$y;
        $dateData['month']=$m;

        $t74tbcons="";

         $t74tbcons.="WHERE yerly = '".$dateData['yerly']."' AND mon = '".$dateData['month']."' GROUP BY type) A  LEFT JOIN D12_C B ON A.type = B.traintype ";
 
         if($dateData['yerly']>=100)
             $t74tbcons.="ORDER BY B.seq100 ";
         else
             $t74tbcons.="ORDER BY B.seq99 ";
        $sql="
        SELECT B.traintype_name AS traintype_name, A.* 
        FROM
        (SELECT 
        yerly,mon,
        SUM(termcnt) AS termcnt, 
        SUM(headcnt) AS headcnt, 
        SUM(daycnt) AS daycnt, 
        SUM(hourcnt) AS hourcnt, 
        type
        FROM t74tb ".$t74tbcons;

        $temp = json_decode(json_encode(DB::select($sql)), true);
        $data = $temp;
        $result="儲存完成。";
        return view('admin/each_training_all/form',compact('result','data','dateData'));


    }

    public function export(Request $request)
    {   
        $RptBasic = new \App\Rptlib\RptBasic();
        $syear=$request->get('startYear');
        $eyear=$request->get('endYear');
        $smonth=$request->get('startMonth');
        $emonth=$request->get('endMonth');
        $ratiobranch=$request->get('area'); // 上課地點 1:臺北院區；2: 南投院區；

        $sMontheday=substr(date('Y-m-t', strtotime(strval($syear+1911)."/"."$smonth"."/01")),8,2);
        $eMontheday=substr(date('Y-m-t', strtotime(strval($eyear+1911)."/"."$emonth"."/01")),8,2);

        $datadate="資料時間：".$syear.".".$smonth.".".$sMontheday."～".$eyear.".".$emonth.".".$eMontheday."止";

        $smonth=str_pad($smonth, 2, "0", STR_PAD_LEFT);
        $emonth=str_pad($emonth, 2, "0", STR_PAD_LEFT);

        if ($emonth=="00")
            $emonth="12";

        $branch="";
        $branchcpation="";
        if($ratiobranch==1){
            $branch="AND B.branch = '1' ";
            $branchcpation="(臺北院區)";
        }elseif ($ratiobranch==2){
            $branch="AND B.branch = '2' ";
            $branchcpation="(南投院區)";
        }

        //shheet 1 系統帶出資訊
        $sql="
        SELECT
        traintype_name,         /* 訓練性質 名稱 */
        col_05 AS '期（課）數',
        col_08 AS '訓練人數',
        col_09 AS '訓練人天數',
        col_10 AS '訓練人時數'
        FROM 
        (
        SELECT
        (@sno := @sno+1) AS serno,
        ROW_NUMBER() OVER( ORDER BY kind, traintype, type ) AS  row_no,
        kind,
        kind_name,
        traintype,  
        traintype_name,
        type,
        type_name, 
        col_05,
        col_06,
        col_07,
        col_08,
        col_09,
        col_10,
        (CASE WHEN type = 'sub_total'  THEN ROUND(col_10/6.0,1) END) AS col_11 
        FROM
        (
        select * from (
        SELECT
        '1' AS kind,                   /* 【分類】 */
        'kind_name' AS kind_name,     /* 【分類】 名稱 */
        traintype,                    /* 訓練性質 */
        traintype_name,               /* 訓練性質 名稱 */
        type,                       /* 班別性質 */
        type_name,                  /* 班別性質 名稱 */
        SUM(cnt_term) AS col_05,     /* 期（課）數 */
        SUM(cnt_sex_m) AS col_06,    /* 男 */
        SUM(cnt_sex_f) AS col_07,    /* 女 */
        SUM(cnt) AS col_08,          /* 訓練人數 */
        SUM(cnt*trainday) AS col_09, /* 訓練人天數 */
        SUM(cnt*classhr) AS col_10   /* 訓練人時數 */
        FROM (
        SELECT
        B.traintype,                /* 訓練性質 */
         (
            CASE B.traintype
            WHEN '1' THEN '中高階公務人員訓練'
            WHEN '2' THEN '人事人員專業訓練'
            WHEN '3' THEN '一般公務人員訓練'
            ELSE ''
            END
        ) AS traintype_name,        /* 訓練性質 名稱 */
        B.type,                      /* 班別性質 */
        RTRIM(C.name) AS type_name, /* 班別性質 名稱 */
        A.class,                     /* 班號 */
        A.term,                      /* 期別 */
        B.trainday,                  /* 訓練總天數 */
        B.classhr,                   /* 實體時數 */
        (
            CASE 
                WHEN B.cntflag='1' THEN COUNT(CASE WHEN status='1' THEN 1 ELSE NULL END) /* 由學員名冊計算 */
                WHEN B.cntflag='2' THEN A.endcnt                                         /* 由承辦人員補登 */
                ELSE 0
            END 
        ) AS cnt, /* 訓練人數 */
        (
            CASE
                WHEN B.cntflag='1' THEN COUNT(CASE WHEN status='1' AND IFNULL(E.sex,'')<>'F' THEN 1 ELSE NULL END) /* 由學員名冊計算 */
                WHEN B.cntflag='2' THEN A.endcnt-ROUND(A.endcnt/2,0)                                               /* 由承辦人員補登 */
                ELSE 0
            END
        ) AS cnt_sex_m, /* 男 */
        (
            CASE
                WHEN B.cntflag='1' THEN COUNT(CASE WHEN status='1' AND E.sex='F' THEN 1 ELSE NULL END) /* 由學員名冊計算 */
                WHEN B.cntflag='2' THEN ROUND(A.endcnt/2,0)                                            /* 由承辦人員補登 */
                ELSE 0
            END
        ) AS cnt_sex_f, /* 女 */
        '1' AS cnt_term   /* 期數 */
        FROM t04tb A  /* 【t04tb 開班資料檔】 */
        INNER JOIN t01tb B  /* 【t01tb 班別基本資料檔】 */
        ON A.class = B.class 
        AND B.type NOT IN ('13','14') /* 不含【研習會議及活動】--> 13:游於藝講堂 14:研習會議 */
        INNER JOIN s01tb C  /* 【s01tb 系統代碼檔】 */
        ON C.type = 'K'   /* k:班別性質 */
        AND C.code <> '12'  /* 其他類 */
        AND B.type = C.code
        LEFT JOIN t13tb D  /* 【t13tb 班別學員資料檔】 */
        ON A.class = D.class
        AND A.term = D.term
        LEFT JOIN m02tb E  /* 【m02tb 學員基本資料檔】 */
        ON D.idno = E.idno
        WHERE  
        ( 
         /* classified 學習性質 1:數位 2:實體 3:混成 */ 
         CASE WHEN B.classified='3' THEN 
            CONCAT(LPAD(CAST((CAST(LEFT(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),4) AS INT)-1911) AS CHAR),3,'0'),
            SUBSTRING(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),6,2),
            RIGHT(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),2)) 
            ELSE A.edate 
         END 
        ) BETWEEN ".$syear.$smonth."01 AND ".$eyear.$emonth."31 
        $branch
        GROUP BY 
        A.class,    /* 班號 */
        A.term,     /* 期別 */
        A.endcnt,   /* 結業人數 */
        B.cntflag,  /* 訓練績效計算方式 1:由學員名冊計算 2:由承辦人員補登 */
        B.trainday, /* 訓練總天數 */
        B.classhr,  /* 實體時數 */
        B.type,     /* 班別性質 */
        C.name,     /* 班別性質 名稱 */
        B.traintype /* 訓練性質 */ 
        
        union
        
        SELECT
        '3' AS　traintype,
        '一般公務人員訓練' AS traintype_name,    /* 訓練性質 名稱 */
        A.type,                                 /* 班別性質 */
        B.name AS type_name,                   /* 班別性質 名稱 */
        A.class,                                /* 班號 */
        '' AS term,                           /* 期別 */
        day AS trainday,                    /* 訓練總天數 */
        hour AS classhr,                    /* 實體時數 */
        A.endcnt AS cnt,                      /* 訓練人數 */
        endcnt-ROUND(endcnt/2,0) AS cnt_sex_m, /* 男 */
        ROUND(endcnt/2,0) AS cnt_sex_f,        /* 女 */
        A.classcnt AS cnt_term                 /* 開班數 */
        FROM t24tb A /*【t24tb 進修訓練資料檔】*/ 
        INNER JOIN s01tb B  /* 【s01tb 系統代碼檔】 */
        ON B.type = 'K'   /* k:班別性質 */
        AND B.code <> '12'  /* 其他類 */
        AND A.type = B.code
        WHERE A.edate BETWEEN ".$syear.$smonth."01 AND ".$eyear.$emonth."31 
        ) AS tmpdata 
        GROUP BY
        traintype,      /* 訓練性質 */
        traintype_name, /* 訓練性質 名稱 */
        type,         /* 班別性質 */
        type_name     /* 班別性質 名稱 */
        ORDER BY 
        traintype, /* 訓練性質 */ 
        type       /* 班別性質 */ ) AS AA
        
        union
        
        select * from (
        SELECT
        '1' AS kind,               /* 【分類】 */
        'kind_name' AS kind_name, /* 【分類】 名稱 */
        traintype,                /* 訓練性質 */
        traintype_name,           /* 訓練性質 名稱 */
        'sub_total' AS type,     /* 班別性質 */
        '小計' AS type_name,      /* 班別性質 名稱 */
        SUM(col_05) AS col_05, /* 期（課）數 */
        SUM(col_06) AS col_06, /* 男 */
        SUM(col_07) AS col_07, /* 女 */
        SUM(col_08) AS col_08, /* 訓練人數 */
        SUM(col_09) AS col_09, /* 訓練人天數 */
        SUM(col_10) AS col_10  /* 訓練人時數 */
        FROM (
        SELECT
        '1' AS kind,                   /* 【分類】 */
        'kind_name' AS kind_name,     /* 【分類】 名稱 */
        traintype,                    /* 訓練性質 */
        traintype_name,               /* 訓練性質 名稱 */
        type,                       /* 班別性質 */
        type_name,                  /* 班別性質 名稱 */
        SUM(cnt_term) AS col_05,     /* 期（課）數 */
        SUM(cnt_sex_m) AS col_06,    /* 男 */
        SUM(cnt_sex_f) AS col_07,    /* 女 */
        SUM(cnt) AS col_08,          /* 訓練人數 */
        SUM(cnt*trainday) AS col_09, /* 訓練人天數 */
        SUM(cnt*classhr) AS col_10   /* 訓練人時數 */
        FROM (
        SELECT
        B.traintype,                /* 訓練性質 */
         (
            CASE B.traintype
            WHEN '1' THEN '中高階公務人員訓練'
            WHEN '2' THEN '人事人員專業訓練'
            WHEN '3' THEN '一般公務人員訓練'
            ELSE ''
            END
        ) AS traintype_name,        /* 訓練性質 名稱 */
        B.type,                      /* 班別性質 */
        RTRIM(C.name) AS type_name, /* 班別性質 名稱 */
        A.class,                     /* 班號 */
        A.term,                      /* 期別 */
        B.trainday,                  /* 訓練總天數 */
        B.classhr,                   /* 實體時數 */
        (
            CASE 
                WHEN B.cntflag='1' THEN COUNT(CASE WHEN status='1' THEN 1 ELSE NULL END) /* 由學員名冊計算 */
                WHEN B.cntflag='2' THEN A.endcnt                                         /* 由承辦人員補登 */
                ELSE 0
            END 
        ) AS cnt, /* 訓練人數 */
        (
            CASE
                WHEN B.cntflag='1' THEN COUNT(CASE WHEN status='1' AND IFNULL(E.sex,'')<>'F' THEN 1 ELSE NULL END) /* 由學員名冊計算 */
                WHEN B.cntflag='2' THEN A.endcnt-ROUND(A.endcnt/2,0)                                               /* 由承辦人員補登 */
                ELSE 0
            END
        ) AS cnt_sex_m, /* 男 */
        (
            CASE
                WHEN B.cntflag='1' THEN COUNT(CASE WHEN status='1' AND E.sex='F' THEN 1 ELSE NULL END) /* 由學員名冊計算 */
                WHEN B.cntflag='2' THEN ROUND(A.endcnt/2,0)                                            /* 由承辦人員補登 */
                ELSE 0
            END
        ) AS cnt_sex_f, /* 女 */
        '1' AS cnt_term   /* 期數 */
        FROM t04tb A  /* 【t04tb 開班資料檔】 */
        INNER JOIN t01tb B  /* 【t01tb 班別基本資料檔】 */
        ON A.class = B.class 
        AND B.type NOT IN ('13','14') /* 不含【研習會議及活動】--> 13:游於藝講堂 14:研習會議 */
        INNER JOIN s01tb C  /* 【s01tb 系統代碼檔】 */
        ON C.type = 'K'   /* k:班別性質 */
        AND C.code <> '12'  /* 其他類 */
        AND B.type = C.code
        LEFT JOIN t13tb D  /* 【t13tb 班別學員資料檔】 */
        ON A.class = D.class
        AND A.term = D.term
        LEFT JOIN m02tb E  /* 【m02tb 學員基本資料檔】 */
        ON D.idno = E.idno
        WHERE  
        ( 
         /* classified 學習性質 1:數位 2:實體 3:混成 */ 
         CASE WHEN B.classified='3' THEN 
            CONCAT(LPAD(CAST((CAST(LEFT(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),4) AS INT)-1911) AS CHAR),3,'0'),
            SUBSTRING(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),6,2),
            RIGHT(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),2)) 
            ELSE A.edate 
         END 
        ) BETWEEN ".$syear.$smonth."01 AND ".$eyear.$emonth."31 
        $branch
        GROUP BY 
        A.class,    /* 班號 */
        A.term,     /* 期別 */
        A.endcnt,   /* 結業人數 */
        B.cntflag,  /* 訓練績效計算方式 1:由學員名冊計算 2:由承辦人員補登 */
        B.trainday, /* 訓練總天數 */
        B.classhr,  /* 實體時數 */
        B.type,     /* 班別性質 */
        C.name,     /* 班別性質 名稱 */
        B.traintype /* 訓練性質 */ 
        
        union
        
        SELECT
        '3' AS　traintype,
        '一般公務人員訓練' AS traintype_name,    /* 訓練性質 名稱 */
        A.type,                                 /* 班別性質 */
        B.name AS type_name,                   /* 班別性質 名稱 */
        A.class,                                /* 班號 */
        '' AS term,                           /* 期別 */
        day AS trainday,                    /* 訓練總天數 */
        hour AS classhr,                    /* 實體時數 */
        A.endcnt AS cnt,                      /* 訓練人數 */
        endcnt-ROUND(endcnt/2,0) AS cnt_sex_m, /* 男 */
        ROUND(endcnt/2,0) AS cnt_sex_f,        /* 女 */
        A.classcnt AS cnt_term                 /* 開班數 */
        FROM t24tb A /*【t24tb 進修訓練資料檔】*/ 
        INNER JOIN s01tb B  /* 【s01tb 系統代碼檔】 */
        ON B.type = 'K'   /* k:班別性質 */
        AND B.code <> '12'  /* 其他類 */
        AND A.type = B.code
        WHERE A.edate BETWEEN ".$syear.$smonth."01 AND ".$eyear.$emonth."31 
        ) AS tmpdata
        GROUP BY
        traintype,      /* 訓練性質 */
        traintype_name, /* 訓練性質 名稱 */
        type,         /* 班別性質 */
        type_name     /* 班別性質 名稱 */
        ORDER BY 
        traintype, /* 訓練性質 */ 
        type       /* 班別性質 */ 
        ) as tmprpt 
        GROUP BY
        traintype,     /* 訓練性質 */
        traintype_name /* 訓練性質 名稱 */) AS BB
        ) AS XX ) AS YY
        WHERE kind = 1
        AND type = 'sub_total'
        ORDER BY row_no";

        $temp = json_decode(json_encode(DB::select($sql)), true); 
        $s1data = $temp;

        //#SQL：查詢 【研習會議及活動】 詳細資料_cdate_style 107.02.01
        $sql="SELECT CONCAT(RTRIM(name),'(',sdate,'～',edate,'共計', CAST(cnt AS char),'人結業)') AS classname_sdate_edate_cnt 
         FROM 
         (
        SELECT B.name,A.sdate,A.edate,
        (
         CASE 
          WHEN B.cntflag='1' THEN COUNT(CASE WHEN C.status='1' THEN 1 ELSE NULL END) /*由學員名冊計算*/
          WHEN B.cntflag='2' THEN A.endcnt                                           /*由承辦人員補登*/
          ELSE 0 
         END
        ) AS cnt,/*訓練人數*/
        B.trainday,/*訓練總天數*/
        B.classhr  /*實體時數*/
        FROM t04tb A INNER JOIN t01tb B ON A.class=B.class LEFT JOIN t13tb C ON A.class=C.class AND A.term=C.term 
        WHERE A.edate BETWEEN ".$syear.$smonth."01 AND ".$eyear.$emonth."31 
        AND B.type IN ('13','14')/*13:游於藝講堂 14:研習會議*/
        ".$branch."
        GROUP BY A.class,A.term,A.sdate,A.edate,B.name,A.endcnt,  B.cntflag,B.trainday,B.classhr
        ORDER BY A.sdate
        ) as tempresult";

        $temp = json_decode(json_encode(DB::select($sql)), true);
        $mdata = $temp;
        
        //SQL：查詢 【「e等公務園」學習網】 詳細資料
        $sql="SELECT  CONCAT(RTRIM(name), '第',CAST(CAST(term AS int) AS char),'期(',  '實體課程',CAST(classhr AS char),'小時，',  '線上課程',CAST(elearnhr AS char),'小時，',  '共計',CAST(cnt AS char), '人結業)') AS classname_sdate_edate_cnt 
        FROM (
        SELECT B.name,A.term,
        (
        CASE 
         WHEN B.cntflag='1' THEN COUNT(CASE WHEN C.status='1' THEN 1 ELSE NULL END) /*由學員名冊計算*/
         WHEN B.cntflag='2' THEN A.endcnt                                           /*由承辦人員補登*/
         ELSE 0 
        END
        ) AS cnt,/*訓練人數*/
        B.trainday,/*訓練總天數*/
        B.elearnhr,/*數位時數*/
        B.classhr  /*實體時數*/
        FROM t04tb A INNER JOIN t01tb B ON A.class=B.class LEFT JOIN t13tb C ON A.class=C.class AND A.term=C.term
        WHERE
        CONCAT(LPAD(CAST((CAST(LEFT(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),4) AS INT)-1911) AS CHAR),3,'0'),
        SUBSTRING(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),6,2),
        RIGHT(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),2)) 
        BETWEEN ".$syear.$smonth."01 AND ".$eyear.$emonth."31 
        AND B.classified='3' /*學習性質 1:數位  2:實體 3:混成 */
        ".$branch."
        GROUP BY A.class,A.term,A.sdate,A.edate,B.name,A.endcnt,B.cntflag,B.classhr,B.trainday,B.elearnhr
        ORDER BY A.sdate
        ) as tempresult";

       $temp = json_decode(json_encode(DB::select($sql)), true);
       $edata = $temp;
      
        //查詢 【研習會議及活動】 合計資料
        $sql="SELECT '研習會議及活動' AS group_name, '13,14' AS 班別性質, COUNT(*) AS 期（課）數, SUM(cnt) AS 訓練人數, SUM(cnt*trainday) AS 訓練人天數, SUM(cnt*classhr) AS 訓練人時數  
        FROM
        (
        SELECT DISTINCT B.name,A.sdate,A.edate,
        (
            CASE 
                WHEN B.cntflag='1' THEN CASE WHEN C.status='1' THEN 1 ELSE NULL END /*由學員名冊計算*/
                WHEN B.cntflag='2' THEN A.endcnt                                    /*由承辦人員補登*/
                ELSE 0 
            END
        ) AS cnt,/*訓練人數*/
        B.trainday,/*訓練總天數*/
        B.classhr  /*實體時數*/
        FROM t04tb A INNER JOIN t01tb B ON A.class=B.class LEFT JOIN t13tb C ON A.class=C.class AND A.term=C.term 
        WHERE A.edate BETWEEN ".$syear.$smonth."01 AND ".$eyear.$emonth."31 
        AND B.type IN ('13','14')/*13:游於藝講堂 14:研習會議*/
        ".$branch."
        GROUP BY A.class,A.term,A.sdate,A.edate,B.name,A.endcnt,B.cntflag,B.trainday,B.classhr,C.status
        ORDER BY A.sdate
        ) as tempresult";     
        
        $temp = json_decode(json_encode(DB::select($sql)), true);
        $msumdata = $temp;

        //查詢 【「e等公務園」學習網】 合計資料
        $sql="SELECT '「e等公務園」學習網' AS group_name,  '3:混成' AS 學習性質, COUNT(*) AS 期（課）數,  SUM(cnt) AS 訓練人數,  SUM(cnt*trainday) AS 訓練人天數,  SUM(cnt*elearnhr) AS 訓練人時數 
        FROM (
        SELECT B.name,A.term,
        (
         CASE 
          WHEN B.cntflag='1' THEN COUNT(CASE WHEN C.status='1' THEN 1 ELSE NULL END) /*由學員名冊計算*/
          WHEN B.cntflag='2' THEN A.endcnt                                           /*由承辦人員補登*/
          ELSE 0 
         END
        ) AS cnt,/*訓練人數*/
        B.trainday,/*訓練總天數*/
        B.elearnhr,/*數位時數*/
        B.classhr  /*實體時數*/
        FROM t04tb A INNER JOIN t01tb B ON A.class=B.class LEFT JOIN t13tb C ON A.class=C.class AND A.term=C.term
        WHERE
        CONCAT(LPAD(CAST((CAST(LEFT(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),4) AS INT)-1911) AS CHAR),3,'0'),
        SUBSTRING(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),6,2),
        RIGHT(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),2)) 
        BETWEEN ".$syear.$smonth."01 AND ".$eyear.$emonth."31 
        AND B.classified='3' /*學習性質 1:數位  2:實體 3:混成 */
        ".$branch."
        GROUP BY A.class,A.term,A.sdate,A.edate,B.name,A.endcnt,B.cntflag,B.classhr,B.trainday,B.elearnhr
        ORDER BY A.sdate
        ) as tempresult";

        $temp = json_decode(json_encode(DB::select($sql)), true);
        $esumdata = $temp;

        $t74tbcons="";

        if($smonth=="")
            $t74tbcons.="WHERE yerly = '".$syear."' AND mon BETWEEN 01 AND 12 GROUP BY type) A  LEFT JOIN D12_C B ON A.type = B.traintype ";
        else
           $t74tbcons.="WHERE yerly BETWEEN ".$syear." AND ".$eyear." AND mon BETWEEN ".$smonth." AND ".$emonth." GROUP BY type) A  LEFT JOIN D12_C B ON A.type = B.traintype ";

        if($syear>=100)
            $t74tbcons.="ORDER BY B.seq100 ";
        else
            $t74tbcons.="ORDER BY B.seq99 ";
            
        //查詢【t74tb 訓練成果檔】資料 (自行登打資料)
        $sql="
        SELECT  B.traintype_name AS 訓練類別, A.*
        FROM
        (SELECT 
        SUM(termcnt) AS '期數(課數)', 
        SUM(headcnt) AS 訓練人數, 
        SUM(daycnt) AS 訓練人天數, 
        SUM(hourcnt) AS 訓練人時數, 
        type
        FROM t74tb ".$t74tbcons;

        $temp = json_decode(json_encode(DB::select($sql)), true);
        $t74data = $temp;


        //part 1 of sheet 3 data
        $sql="SELECT
        '1' AS kind,                   /* 【分類】 */
        'kind_name' AS kind_name,     /* 【分類】 名稱 */
        traintype,                    /* 訓練性質 */
        traintype_name,               /* 訓練性質 名稱 */
        #type,                       /* 班別性質 */
        type_name,                  /* 班別性質 名稱 */
        SUM(cnt_term) AS col_05,     /* 期（課）數 */
        SUM(cnt_sex_m) AS col_06,    /* 男 */
        SUM(cnt_sex_f) AS col_07,    /* 女 */
        SUM(cnt) AS col_08,          /* 訓練人數 */
        SUM(cnt*trainday) AS col_09, /* 訓練人天數 */
        SUM(cnt*classhr) AS col_10   /* 訓練人時數 */
        FROM (
        SELECT
        B.traintype,                /* 訓練性質 */
         (
            CASE B.traintype
            WHEN '1' THEN '中高階公務人員訓練'
            WHEN '2' THEN '人事人員專業訓練'
            WHEN '3' THEN '一般公務人員訓練'
            ELSE ''
            END
        ) AS traintype_name,        /* 訓練性質 名稱 */
        B.type,                      /* 班別性質 */
        RTRIM(C.name) AS type_name, /* 班別性質 名稱 */
        A.class,                     /* 班號 */
        A.term,                      /* 期別 */
        B.trainday,                  /* 訓練總天數 */
        B.classhr,                   /* 實體時數 */
        (
            CASE 
                WHEN B.cntflag='1' THEN COUNT(CASE WHEN status='1' THEN 1 ELSE NULL END) /* 由學員名冊計算 */
                WHEN B.cntflag='2' THEN A.endcnt                                         /* 由承辦人員補登 */
                ELSE 0
            END 
        ) AS cnt, /* 訓練人數 */
        (
            CASE
                WHEN B.cntflag='1' THEN COUNT(CASE WHEN status='1' AND IFNULL(E.sex,'')<>'F' THEN 1 ELSE NULL END) /* 由學員名冊計算 */
                WHEN B.cntflag='2' THEN A.endcnt-ROUND(A.endcnt/2,0)                                               /* 由承辦人員補登 */
                ELSE 0
            END
        ) AS cnt_sex_m, /* 男 */
        (
            CASE
                WHEN B.cntflag='1' THEN COUNT(CASE WHEN status='1' AND E.sex='F' THEN 1 ELSE NULL END) /* 由學員名冊計算 */
                WHEN B.cntflag='2' THEN ROUND(A.endcnt/2,0)                                            /* 由承辦人員補登 */
                ELSE 0
            END
        ) AS cnt_sex_f, /* 女 */
        '1' AS cnt_term   /* 期數 */
        FROM t04tb A  /* 【t04tb 開班資料檔】 */
        INNER JOIN t01tb B  /* 【t01tb 班別基本資料檔】 */
        ON A.class = B.class 
        AND B.type NOT IN ('13','14') /* 不含【研習會議及活動】--> 13:游於藝講堂 14:研習會議 */
        INNER JOIN s01tb C  /* 【s01tb 系統代碼檔】 */
        ON C.type = 'K'   /* k:班別性質 */
        AND C.code <> '12'  /* 其他類 */
        AND B.type = C.code
        LEFT JOIN t13tb D  /* 【t13tb 班別學員資料檔】 */
        ON A.class = D.class
        AND A.term = D.term
        LEFT JOIN m02tb E  /* 【m02tb 學員基本資料檔】 */
        ON D.idno = E.idno
        WHERE  
        ( 
         /* classified 學習性質 1:數位 2:實體 3:混成 */ 
         CASE WHEN B.classified='3' THEN 
            CONCAT(LPAD(CAST((CAST(LEFT(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),4) AS INT)-1911) AS CHAR),3,'0'),
            SUBSTRING(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),6,2),
            RIGHT(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),2)) 
            ELSE A.edate 
         END 
        ) BETWEEN  ".$syear.$smonth."01 AND ".$eyear.$emonth."31 
        AND 1 = (/* 上課地點 1:臺北院區；2: 南投院區； */
            CASE
                WHEN '' = ''                     THEN 1
                WHEN '@branch' = '1' AND B.branch = '1' THEN 1
                WHEN '' = '2' AND B.branch = '2' THEN 1
            END
        )
        GROUP BY 
        A.class,    /* 班號 */
        A.term,     /* 期別 */
        A.endcnt,   /* 結業人數 */
        B.cntflag,  /* 訓練績效計算方式 1:由學員名冊計算 2:由承辦人員補登 */
        B.trainday, /* 訓練總天數 */
        B.classhr,  /* 實體時數 */
        B.type,     /* 班別性質 */
        C.name,     /* 班別性質 名稱 */
        B.traintype /* 訓練性質 */ 
        
        union
        
        SELECT
        '3' AS　traintype,
        '一般公務人員訓練' AS traintype_name,    /* 訓練性質 名稱 */
        A.type,                                 /* 班別性質 */
        B.name AS type_name,                   /* 班別性質 名稱 */
        A.class,                                /* 班號 */
        '' AS term,                           /* 期別 */
        day AS trainday,                    /* 訓練總天數 */
        hour AS classhr,                    /* 實體時數 */
        A.endcnt AS cnt,                      /* 訓練人數 */
        endcnt-ROUND(endcnt/2,0) AS cnt_sex_m, /* 男 */
        ROUND(endcnt/2,0) AS cnt_sex_f,        /* 女 */
        A.classcnt AS cnt_term                 /* 開班數 */
        FROM t24tb A /*【t24tb 進修訓練資料檔】*/ 
        INNER JOIN s01tb B  /* 【s01tb 系統代碼檔】 */
        ON B.type = 'K'   /* k:班別性質 */
        AND B.code <> '12'  /* 其他類 */
        AND A.type = B.code
        WHERE A.edate BETWEEN  ".$syear.$smonth."01 AND ".$eyear.$emonth."31 
        ) AS tmpdata 
        GROUP BY
        traintype,      /* 訓練性質 */
        traintype_name, /* 訓練性質 名稱 */
        type,         /* 班別性質 */
        type_name     /* 班別性質 名稱 */
        ORDER BY 
        traintype, /* 訓練性質 */ 
        type       /* 班別性質 */";

        $temp = json_decode(json_encode(DB::select($sql)), true);
        $s3Adata = $temp;

        $sql="SELECT
        count(*)+(ROW_NUMBER() OVER(ORDER BY type)) AS row_no,
        2 AS kind,                     /* 【分類】 */
        '' as kind_name,                     /* 【分類】 名稱 */
        type_name AS traintype_name, /* 訓練性質 名稱 */
        #type,                         /* 班別性質 */
        type_name,                    /* 班別性質 名稱 */
        SUM(cnt_term) AS col_05,     /* 期（課）數 */
        SUM(cnt_sex_m) AS col_06,    /* 男 */
        SUM(cnt_sex_f) AS col_07,    /* 女 */
        SUM(cnt) AS col_08,          /* 訓練人數 */
        SUM(cnt*trainday) AS col_09, /* 訓練人天數 */
        SUM(cnt*classhr) AS col_10   /* 訓練人時數 */
        FROM 
        (
        SELECT
        B.traintype,                /* 訓練性質 */
         (
            CASE B.traintype
            WHEN '1' THEN '中高階公務人員訓練'
            WHEN '2' THEN '人事人員專業訓練'
            WHEN '3' THEN '一般公務人員訓練'
            ELSE ''
            END
        ) AS traintype_name,        /* 訓練性質 名稱 */
        B.type,                      /* 班別性質 */
        RTRIM(C.name) AS type_name, /* 班別性質 名稱 */
        A.class,                     /* 班號 */
        A.term,                      /* 期別 */
        B.trainday,                  /* 訓練總天數 */
        B.classhr,                   /* 實體時數 */
        (
            CASE 
                WHEN B.cntflag='1' THEN COUNT(CASE WHEN status='1' THEN 1 ELSE NULL END) /* 由學員名冊計算 */
                WHEN B.cntflag='2' THEN A.endcnt                                         /* 由承辦人員補登 */
                ELSE 0
            END 
        ) AS cnt, /* 訓練人數 */
        (
            CASE
                WHEN B.cntflag='1' THEN COUNT(CASE WHEN status='1' AND IFNULL(E.sex,'')<>'F' THEN 1 ELSE NULL END) /* 由學員名冊計算 */
                WHEN B.cntflag='2' THEN A.endcnt-ROUND(A.endcnt/2,0)                                               /* 由承辦人員補登 */
                ELSE 0
            END
        ) AS cnt_sex_m, /* 男 */
        (
            CASE
                WHEN B.cntflag='1' THEN COUNT(CASE WHEN status='1' AND E.sex='F' THEN 1 ELSE NULL END) /* 由學員名冊計算 */
                WHEN B.cntflag='2' THEN ROUND(A.endcnt/2,0)                                            /* 由承辦人員補登 */
                ELSE 0
            END
        ) AS cnt_sex_f, /* 女 */
        '1' AS cnt_term   /* 期數 */
        FROM t04tb A  /* 【t04tb 開班資料檔】 */
        INNER JOIN t01tb B  /* 【t01tb 班別基本資料檔】 */
        ON A.class = B.class 
        AND B.type NOT IN ('13','14') /* 不含【研習會議及活動】--> 13:游於藝講堂 14:研習會議 */
        INNER JOIN s01tb C  /* 【s01tb 系統代碼檔】 */
        ON C.type = 'K'   /* k:班別性質 */
        AND C.code <> '12'  /* 其他類 */
        AND B.type = C.code
        LEFT JOIN t13tb D  /* 【t13tb 班別學員資料檔】 */
        ON A.class = D.class
        AND A.term = D.term
        LEFT JOIN m02tb E  /* 【m02tb 學員基本資料檔】 */
        ON D.idno = E.idno
        WHERE  
        ( 
         /* classified 學習性質 1:數位 2:實體 3:混成 */ 
         CASE WHEN B.classified='3' THEN 
            CONCAT(LPAD(CAST((CAST(LEFT(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),4) AS INT)-1911) AS CHAR),3,'0'),
            SUBSTRING(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),6,2),
            RIGHT(CAST(DATE_ADD(CAST(CONCAT(CAST(LEFT(A.edate,3)*1+1911 as CHAR),'-',CAST(SUBSTRING(A.edate,4,2)*1 as CHAR),'-',RIGHT(A.edate,2)*1) AS DATE), INTERVAL 14 DAY) AS CHAR),2)) 
            ELSE A.edate 
         END 
        ) BETWEEN  ".$syear.$smonth."01 AND ".$eyear.$emonth."31 
        AND 1 = (/* 上課地點 1:臺北院區；2: 南投院區； */
            CASE
                WHEN '' = ''                     THEN 1
                WHEN '@branch' = '1' AND B.branch = '1' THEN 1
                WHEN '' = '2' AND B.branch = '2' THEN 1
            END
        )
        GROUP BY 
        A.class,    /* 班號 */
        A.term,     /* 期別 */
        A.endcnt,   /* 結業人數 */
        B.cntflag,  /* 訓練績效計算方式 1:由學員名冊計算 2:由承辦人員補登 */
        B.trainday, /* 訓練總天數 */
        B.classhr,  /* 實體時數 */
        B.type,     /* 班別性質 */
        C.name,     /* 班別性質 名稱 */
        B.traintype /* 訓練性質 */ 
        
        union
        
        SELECT
        '3' AS　traintype,
        '一般公務人員訓練' AS traintype_name,    /* 訓練性質 名稱 */
        A.type,                                 /* 班別性質 */
        B.name AS type_name,                   /* 班別性質 名稱 */
        A.class,                                /* 班號 */
        '' AS term,                           /* 期別 */
        day AS trainday,                    /* 訓練總天數 */
        hour AS classhr,                    /* 實體時數 */
        A.endcnt AS cnt,                      /* 訓練人數 */
        endcnt-ROUND(endcnt/2,0) AS cnt_sex_m, /* 男 */
        ROUND(endcnt/2,0) AS cnt_sex_f,        /* 女 */
        A.classcnt AS cnt_term                 /* 開班數 */
        FROM t24tb A /*【t24tb 進修訓練資料檔】*/ 
        INNER JOIN s01tb B  /* 【s01tb 系統代碼檔】 */
        ON B.type = 'K'   /* k:班別性質 */
        AND B.code <> '12'  /* 其他類 */
        AND A.type = B.code
        WHERE A.edate BETWEEN  ".$syear.$smonth."01 AND ".$eyear.$emonth."31 
        ) AS tmpdata 
        GROUP BY
        type,     /* 班別性質 */
        type_name /* 班別性質 名稱 */
        ORDER BY 
        type /* 班別性質 */";

        $temp = json_decode(json_encode(DB::select($sql)), true);
        $s3Bdata = $temp;

        //檢查資料完整性    

        if($mdata==[]){
            $result="查無研習會議及活動詳細資料，請重新查詢。";
            return view('admin/each_training_all/list',compact('result'));
        }
        if($edata==[]){
            $result="查無「e等公務園」學習網詳細資料，請重新查詢。";
            return view('admin/each_training_all/list',compact('result'));
        }
        if($msumdata==[]){
            $result="查無研習會議及活動合計資料，請重新查詢。";
            return view('admin/each_training_all/list',compact('result'));
        }
        if($esumdata==[]){
            $result="查無「e等公務園」學習網合計資料，請重新查詢。";
            return view('admin/each_training_all/list',compact('result'));
        }
        if($t74data==[]){
            $result="查自行登打資料資料，請重新查詢。";
            return view('admin/each_training_all/list',compact('result'));
        }
        if($s1data==[]){
            $result="查無系統帶出資料，請重新查詢。";
            return view('admin/each_training_all/list',compact('result'));
        }
        if($s3Adata==[]){
            $result="查無分類統計資料，請重新查詢。";
            return view('admin/each_training_all/list',compact('result'));
        }
        if($s3Bdata==[]){
            $result="查無分類統計資料，請重新查詢。";
            return view('admin/each_training_all/list',compact('result'));
        }


        $mdatakeys=array_keys((array)$mdata[0]);
        $edatakeys=array_keys((array)$edata[0]);
        $msumdatakeys=array_keys((array)$msumdata[0]);
        $esumdatakeys=array_keys((array)$esumdata[0]);        
        $t74datakeys=array_keys((array)$t74data[0]);
        $s1datakeys=array_keys((array)$s1data[0]);
        $s3Adatakeys=array_keys((array)$s3Adata[0]);
        $s3Bdatakeys=array_keys((array)$s3Bdata[0]);

                        
        // 檔案名稱
        $fileName = 'D12';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel，
        $objPHPExcel = IOFactory::load($filePath);

        //填入第一頁資料
        $objSheet = $objPHPExcel->getsheet(0);
           
        $objSheet->setCellValue('A1',$syear."年度各類訓練進修研習成果統計彙總表(系統帶出資訊)".$branchcpation);
        $objSheet->setCellValue('A2',$datadate);

        for($i=0;$i<3;$i++){
           for($j=0;$j<sizeof($s1datakeys)-1;$j++){
                if ($i==0)
                    $objSheet->setCellValue($this->getNameFromNumber($j+4).'4',$s1data[$i][$s1datakeys[$j+1]]);
                if ($i==1)
                    $objSheet->setCellValue($this->getNameFromNumber($j+4).'5',$s1data[$i][$s1datakeys[$j+1]]);
                if ($i==2)
                    $objSheet->setCellValue($this->getNameFromNumber($j+4).'6',$s1data[$i][$s1datakeys[$j+1]]);
           }
        }   

        for($j=0;$j<sizeof($msumdatakeys)-2;$j++){
            $objSheet->setCellValue($this->getNameFromNumber($j+4).'9',$msumdata[0][$msumdatakeys[$j+2]]);
            //公餘進修沒資料先填0
            $objSheet->setCellValue($this->getNameFromNumber($j+4).'11','0');
       }



        //填入第二頁資料

        $objSheet = $objPHPExcel->getsheet(1);
           
        $objSheet->setCellValue('A1',$syear."年度各類訓練進修研習成果統計彙總表(自行登打資訊)");
        $objSheet->setCellValue('A2',$datadate);

        for($i=0;$i<5;$i++){

            for($j=0;$j<sizeof($t74datakeys)-2;$j++){
                    if($i==3){
                        $objSheet->setCellValue($this->getNameFromNumber($j+4).'16',$t74data[$i][$t74datakeys[$j+1]]);
                    }elseif($i==4){
                        $objSheet->setCellValue($this->getNameFromNumber($j+4).'18',$t74data[$i][$t74datakeys[$j+1]]);
                    }elseif($i==5){
                        $objSheet->setCellValue($this->getNameFromNumber($j+4).'19',$t74data[$i][$t74datakeys[$j+1]]);
                    }elseif($i==6){
                        $objSheet->setCellValue($this->getNameFromNumber($j+4).'15',$t74data[$i][$t74datakeys[$j+1]]);
                    }else{
                    $objSheet->setCellValue($this->getNameFromNumber($j+4).strval($i+11),$t74data[$i][$t74datakeys[$j+1]]);
                }
            }
        }  
        

        //填入第三頁資料

        $objSheet = $objPHPExcel->getsheet(2);
           
        $objSheet->setCellValue('A1',$syear."年度各類訓練、班期性質成果明細表".$branchcpation);

        //填第一部份資料
        $rpos=4;
        $tmptype="";
        $tcnt=0;
        $amergefrom="0";
        $amergeto="0";
        $cmergefrom="0";
        $cmergeto="0";
        $cntarr=[];
        for($i=0;$i<sizeof($s3Adata);$i++){
            if($tmptype!=$s3Adata[$i]["traintype_name"]){
                if($i!=0){       
                    $cmergeto++;
                    $objSheet->setCellValue('C'.strval($rpos),$s3Adata[$i]["traintype_name"]);        
                    $objSheet->setCellValue('D'.strval($rpos),"小計");  
                    $objSheet->setCellValue('E'.strval($rpos),"=SUM(E".$cmergefrom.":E".strval($cmergeto-1).")");        
                    $objSheet->setCellValue('F'.strval($rpos),"=SUM(F".$cmergefrom.":F".strval($cmergeto-1).")");    
                    $objSheet->setCellValue('G'.strval($rpos),"=SUM(G".$cmergefrom.":G".strval($cmergeto-1).")");        
                    $objSheet->setCellValue('H'.strval($rpos),"=SUM(F".strval($rpos).":G".strval($rpos).")");  
                    $objSheet->setCellValue('I'.strval($rpos),"=SUM(I".$cmergefrom.":I".strval($cmergeto-1).")");        
                    $objSheet->setCellValue('J'.strval($rpos),"=SUM(J".$cmergefrom.":J".strval($cmergeto-1).")");   
                    $objSheet->setCellValue('K'.strval($cmergefrom),"=J".strval($rpos)."/6");  
                    array_push($cntarr,$rpos); 
                    $rpos++;
                    $objSheet->mergeCells('C'.$cmergefrom.":C".$cmergeto);
                    $objSheet->mergeCells('K'.$cmergefrom.":K".$cmergeto);


                }else{
                    $amergefrom= $rpos;
                    $amergeto= $rpos;
                }
                $tcnt++;
                $tmptype=$s3Adata[$i]["traintype_name"];
                $cmergefrom=$rpos;
                $cmergeto=$rpos;
            }else{
                $cmergeto++;        
            }
            

            $objSheet->setCellValue('C'.strval($rpos),$s3Adata[$i]["traintype_name"]);        
            $objSheet->setCellValue('D'.strval($rpos),$s3Adata[$i]["type_name"]);  
            $objSheet->setCellValue('E'.strval($rpos),$s3Adata[$i]["col_05"]);        
            $objSheet->setCellValue('F'.strval($rpos),$s3Adata[$i]["col_06"]);    
            $objSheet->setCellValue('G'.strval($rpos),$s3Adata[$i]["col_07"]);        
            $objSheet->setCellValue('H'.strval($rpos),"=SUM(F".strval($rpos).":G".strval($rpos).")");  
            $objSheet->setCellValue('I'.strval($rpos),$s3Adata[$i]["col_09"]);        
            $objSheet->setCellValue('J'.strval($rpos),$s3Adata[$i]["col_10"]);   

            $rpos++;

        }  

        $cmergeto++;     
        $objSheet->setCellValue('D'.strval($rpos),"小計");  
        $objSheet->setCellValue('E'.strval($rpos),"=SUM(E".$cmergefrom.":E".strval($cmergeto-1).")");        
        $objSheet->setCellValue('F'.strval($rpos),"=SUM(F".$cmergefrom.":F".strval($cmergeto-1).")");    
        $objSheet->setCellValue('G'.strval($rpos),"=SUM(G".$cmergefrom.":G".strval($cmergeto-1).")");        
        $objSheet->setCellValue('H'.strval($rpos),"=SUM(F".strval($rpos).":G".strval($rpos).")");  
        $objSheet->setCellValue('I'.strval($rpos),"=SUM(I".$cmergefrom.":I".strval($cmergeto-1).")");        
        $objSheet->setCellValue('J'.strval($rpos),"=SUM(J".$cmergefrom.":J".strval($cmergeto-1).")");   
        $objSheet->setCellValue('K'.strval($cmergefrom),"=J".strval($rpos)."/6");  
        array_push($cntarr,$rpos); 
        $rpos++;
        $objSheet->mergeCells('C'.$cmergefrom.":C".$cmergeto);
        $objSheet->mergeCells('K'.$cmergefrom.":K".$cmergeto);


        $objSheet->mergeCells('C'.strval($rpos).":D".strval($rpos));
        $objSheet->setCellValue('C'.strval($rpos),$tcnt."類合計");       
        $objSheet->getStyle('C'.strval($rpos))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_DISTRIBUTED); 

        $sumfields="";
        foreach($cntarr as $v){
            $sumfields.="E".$v.",";
        }
        $sumfields=substr($sumfields,0,-1);

        $objSheet->setCellValue('E'.strval($rpos),"=SUM(".$sumfields.")");        
        str_replace("E","F",$sumfields);
        $objSheet->setCellValue('F'.strval($rpos),"=SUM(".$sumfields.")");   
        str_replace("F","G",$sumfields); 
        $objSheet->setCellValue('G'.strval($rpos),"=SUM(".$sumfields.")");        
        $objSheet->setCellValue('H'.strval($rpos),"=SUM(F".strval($rpos).":G".strval($rpos).")");  
        str_replace("G","I",$sumfields); 
        $objSheet->setCellValue('I'.strval($rpos),"=SUM(".$sumfields.")");      
        str_replace("I","J",$sumfields);   
        $objSheet->setCellValue('J'.strval($rpos),"=SUM(".$sumfields.")");   
        $objSheet->setCellValue('K'.strval($rpos),"=J".strval($rpos)."/6");  
        
        $amergeto=$rpos;
        $objSheet->mergeCells('A'.$amergefrom.":B".$amergeto);
        $objSheet->setCellValue('A'.$amergefrom,$RptBasic->toCHTnum2($tcnt)."大類訓練績效" );

        $rpos++;
        



        //填第二部份資料
       // $rpos++;
       $tmptype="";
       $tcnt=0;
       $amergefrom="0";
       $amergeto="0";
       $cmergefrom="0";
       $cmergeto="0";
       for($i=0;$i<sizeof($s3Bdata);$i++){
           if($tmptype!=$s3Bdata[$i]["traintype_name"]){
               if($i==0){       
                   $amergefrom= $rpos;
                   $amergeto= $rpos;
           }
               $tcnt++;
               $tmptype=$s3Bdata[$i]["traintype_name"];

           }
           
           $objSheet->mergeCells('C'.strval($rpos).":D".strval($rpos));  
           $objSheet->getStyle('C'.strval($rpos))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_DISTRIBUTED); 
           $objSheet->setCellValue('C'.strval($rpos),$s3Bdata[$i]["traintype_name"]);        
           $objSheet->setCellValue('E'.strval($rpos),$s3Bdata[$i]["col_05"]);        
           $objSheet->setCellValue('F'.strval($rpos),$s3Bdata[$i]["col_06"]);    
           $objSheet->setCellValue('G'.strval($rpos),$s3Bdata[$i]["col_07"]);        
           $objSheet->setCellValue('H'.strval($rpos),"=SUM(F".strval($rpos).":G".strval($rpos).")");  
           $objSheet->setCellValue('I'.strval($rpos),$s3Bdata[$i]["col_09"]);        
           $objSheet->setCellValue('J'.strval($rpos),$s3Bdata[$i]["col_10"]);   
           $objSheet->setCellValue('K'.strval($rpos),"=J".strval($rpos)."/6");  
           $rpos++;

       }  

       $amergeto=$rpos;

       $objSheet->mergeCells('C'.strval($rpos).":D".strval($rpos));
       $objSheet->setCellValue('C'.strval($rpos),$tcnt."類合計");       
       $objSheet->getStyle('C'.strval($rpos))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_DISTRIBUTED); 
       $objSheet->setCellValue('E'.strval($rpos),"=SUM(E".$amergefrom.":E".($amergeto-1).")");        
       $objSheet->setCellValue('F'.strval($rpos),"=SUM(F".$amergefrom.":F".($amergeto-1).")");   
       $objSheet->setCellValue('G'.strval($rpos),"=SUM(G".$amergefrom.":G".($amergeto-1).")");        
       $objSheet->setCellValue('H'.strval($rpos),"=SUM(E".strval($rpos).":G".strval($rpos).")");  
       $objSheet->setCellValue('I'.strval($rpos),"=SUM(I".$amergefrom.":I".($amergeto-1).")");      
       $objSheet->setCellValue('J'.strval($rpos),"=SUM(J".$amergefrom.":J".($amergeto-1).")");   
       $objSheet->setCellValue('K'.strval($rpos),"=J".strval($rpos)."/6");  
       

       $objSheet->mergeCells('A'.$amergefrom.":B".$amergeto);
       $objSheet->setCellValue('A'.$amergefrom,$RptBasic->toCHTnum2($tcnt)."大類訓練績效" );

       $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,   
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];


    //apply borders
    $objSheet->getStyle('A4:K'.$rpos)->applyFromArray($styleArray);

    $rpos++;
    $objSheet->mergeCells('B'.strval($rpos).':K'.strval($rpos));    
    $objSheet->setCellValue('A'.strval($rpos),"＊");
    $objSheet->setCellValue('B'.strval($rpos),"研習會議及活動：");

    $rpos++;
        //填入研習會議活動詳細資料
        for($i=0;$i<sizeof($mdata);$i++){
            $objSheet->mergeCells('B'.strval($rpos).':K'.strval($rpos));
            $objSheet->setCellValue('B'.strval($rpos),$mdata[$i][$mdatakeys[0]]);
            $rpos++;
        }         

        //【「e等公務園」學習網】 詳細資料
        for($i=0;$i<sizeof($edata);$i++){
            $objSheet->mergeCells('B'.strval($rpos).':K'.strval($rpos));
            $objSheet->setCellValue('B'.strval($rpos),$edata[$i][$edatakeys[0]]);
            $rpos++;
        }    


        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"各類訓練進修研習成果統計彙總表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
        //export excel

    }


}
