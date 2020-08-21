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

class ManageMonthlyConferenceController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('manage_monthly_conference', $user_group_auth)){
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
        return view('admin/manage_monthly_conference/list');
    }

    /*
    場地使用管理月報表 CSDIR6140
    參考Tables:
    使用範本:N11.xlsx
    'History:
    '2002/10/16 Update
    'm14tb.timetype 預約類型（1：時段；2：時間）
    '  When m14tb.tiemtype='2'
    '  場次以時數計算 (結束時間 - 開始時間), 不到一小時, 以一小時計
    '例如:會議室
    '2002/10/14 Update
    '條件:
    '1.該月份各場地，【usertype場地使用者】的次數統計。
    '2.日期不可空白
    '3.日期格式是否正確(EX:08801)
    '【各場地】
    'm14tb.type IN ('1','2','5') 場地:1:教室 2:會議室 5:簡報室
    'm14tb.site<>'4XX'
    '【usertype場地使用者】的次數統計
    '【t22tb 場地預約檔】
    'usertype 場地使用者
    '1 本學院自辦訓練及活動-學院
    '2 本學院接洽租借給公務機關
    '3 本學院接洽租借給民間機構
    '4 會館接洽租借給公務機關
    '5 會館接洽租借給民間機構
    '1 本學院自辦訓練及活動-人事局
    '【教室】需作小計
    '1.m14tb.type='1' ->1:教室
    '2.m14tb.site NOT IN ('501','502') ->電腦教室不作統計
    '3.名稱：【一、二、三、四、六樓合計】
    '若次數統計為0，則顯示空白。
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //年
        $startYear = $request->input('startYear');
        //月
        $startMonth = $request->input('startMonth');

        /*
        if($startMonth<10){
            $startMonth='0'.$startMonth;
        }
        */

        //取得t22tb(辦班需求確認檔)的資料
        //教室一至六樓
        $sql="SELECT T.XF,	T.name,
                     T.usertype_1,
                     T.usertype_6,
                     T.usertype_2,
                     T.usertype_3,
                     T.usertype_4,
                     T.usertype_5
                FROM (
              SELECT CONCAT(A.type,A.site) AS SORT,
                     (CASE A.type
                            WHEN '1' THEN
                                (CASE SUBSTRING(A.site,1,1)
                                WHEN '1' THEN '一樓'
                                WHEN '2' THEN '二樓'
                                WHEN '3' THEN '三樓'
                                WHEN '4' THEN '四樓'
                                WHEN '5' THEN '五樓'
                                WHEN '6' THEN '六樓'
                                Else ''
                                End
                                )
                            WHEN '2' THEN
                                (CASE A.site
                                WHEN 'C01' THEN '會議棟一樓'
                                WHEN 'C02' THEN '會議棟二樓'
                                WHEN 'C14' THEN '十四樓'
                                Else ''     END
                                )
                            WHEN '5' THEN '行政棟'+
                                (CASE
                                WHEN SUBSTRING(A.name,2,1)='樓'
                                THEN SUBSTRING(A.name,1,2)
                                Else ''     END    )     ELSE ''
                                END    ) AS XF,
                        A.name,
                        IFNULL(B.usertype_1,'') AS usertype_1,
                        IFNULL(B.usertype_6,'') AS usertype_6,
                        IFNULL(B.usertype_2,'') AS usertype_2,
                        IFNULL(B.usertype_3,'') AS usertype_3,
                        IFNULL(B.usertype_4,'') AS usertype_4,
                        IFNULL(B.usertype_5,'') AS usertype_5
                FROM m14tb A LEFT JOIN
                        (
                        SELECT F.site,
                                        COUNT(CASE WHEN F.usertype='1' THEN 1 ELSE NULL END) AS usertype_1,
                                        COUNT(CASE WHEN F.usertype='2' THEN 1 ELSE NULL END) AS usertype_2,
                                        COUNT(CASE WHEN F.usertype='3' THEN 1 ELSE NULL END) AS usertype_3,
                                        COUNT(CASE WHEN F.usertype='4' THEN 1 ELSE NULL END) AS usertype_4,
                                        COUNT(CASE WHEN F.usertype='5' THEN 1 ELSE NULL END) AS usertype_5,
                                        COUNT(CASE WHEN F.usertype='6' THEN 1 ELSE NULL END) AS usertype_6
                            FROM t22tb F INNER JOIN m14tb G ON F.site =G.site
                            WHERE SUBSTRING(F.date,1,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                AND G.timetype = '1'
                            GROUP BY F.site
                        UNION ALL
                        SELECT F.site,
                                    SUM( CASE WHEN (F.usertype = '1' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '1') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
																			               SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
																			                ,
																                    CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
																		                   SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_1,
                                    SUM( CASE WHEN (F.usertype = '2' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '2') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_2,
                                    SUM( CASE WHEN (F.usertype = '3' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '3') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_3,
                                    SUM( CASE WHEN (F.usertype = '4' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '4') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_4,
                                    SUM( CASE WHEN (F.usertype = '5' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '5') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_5,
                                    SUM( CASE WHEN (F.usertype = '6' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '6') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END ) AS Usertype_6
                            FROM t22tb F INNER JOIN m14tb G ON F.site =G.site
                            WHERE SUBSTRING(F.date,1,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                AND G.timetype = '2'
                            GROUP BY F.site
                        ) B ON A.site = B.site
                WHERE A.type IN ('1','2','5')
                AND A.type = '1'
                ) T
                ORDER BY T.SORT
                ";

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        //會議室
        $sql2="SELECT T.XF,	T.name,
                     T.usertype_1,
                     T.usertype_6,
                     T.usertype_2,
                     T.usertype_3,
                     T.usertype_4,
                     T.usertype_5
                FROM (
              SELECT CONCAT(A.type,A.site) AS SORT,
                     (CASE A.type
                            WHEN '1' THEN
                                (CASE SUBSTRING(A.site,1,1)
                                WHEN '1' THEN '一樓'
                                WHEN '2' THEN '二樓'
                                WHEN '3' THEN '三樓'
                                WHEN '4' THEN '四樓'
                                WHEN '5' THEN '五樓'
                                WHEN '6' THEN '六樓'
                                Else ''
                                End
                                )
                            WHEN '2' THEN
                                (CASE A.site
                                WHEN 'C01' THEN '會議棟一樓'
                                WHEN 'C02' THEN '會議棟二樓'
                                WHEN 'C14' THEN '十四樓'
                                Else ''     END
                                )
                            WHEN '5' THEN '行政棟'+
                                (CASE
                                WHEN SUBSTRING(A.name,2,1)='樓'
                                THEN SUBSTRING(A.name,1,2)
                                Else ''     END    )     ELSE ''
                                END    ) AS XF,
                        A.name,
                        IFNULL(B.usertype_1,'') AS usertype_1,
                        IFNULL(B.usertype_6,'') AS usertype_6,
                        IFNULL(B.usertype_2,'') AS usertype_2,
                        IFNULL(B.usertype_3,'') AS usertype_3,
                        IFNULL(B.usertype_4,'') AS usertype_4,
                        IFNULL(B.usertype_5,'') AS usertype_5
                FROM m14tb A LEFT JOIN
                        (
                        SELECT F.site,
                                        COUNT(CASE WHEN F.usertype='1' THEN 1 ELSE NULL END) AS usertype_1,
                                        COUNT(CASE WHEN F.usertype='2' THEN 1 ELSE NULL END) AS usertype_2,
                                        COUNT(CASE WHEN F.usertype='3' THEN 1 ELSE NULL END) AS usertype_3,
                                        COUNT(CASE WHEN F.usertype='4' THEN 1 ELSE NULL END) AS usertype_4,
                                        COUNT(CASE WHEN F.usertype='5' THEN 1 ELSE NULL END) AS usertype_5,
                                        COUNT(CASE WHEN F.usertype='6' THEN 1 ELSE NULL END) AS usertype_6
                            FROM t22tb F INNER JOIN m14tb G ON F.site =G.site
                            WHERE SUBSTRING(F.date,1,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                AND G.timetype = '1'
                            GROUP BY F.site
                        UNION ALL
                        SELECT F.site,
                                    SUM( CASE WHEN (F.usertype = '1' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '1') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
																			               SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
																			                ,
																                    CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
																		                   SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_1,
                                    SUM( CASE WHEN (F.usertype = '2' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '2') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_2,
                                    SUM( CASE WHEN (F.usertype = '3' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '3') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_3,
                                    SUM( CASE WHEN (F.usertype = '4' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '4') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_4,
                                    SUM( CASE WHEN (F.usertype = '5' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '5') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_5,
                                    SUM( CASE WHEN (F.usertype = '6' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '6') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END ) AS Usertype_6
                            FROM t22tb F INNER JOIN m14tb G ON F.site =G.site
                            WHERE SUBSTRING(F.date,1,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                AND G.timetype = '2'
                            GROUP BY F.site
                        ) B ON A.site = B.site
                WHERE A.type IN ('1','2','5')
                AND A.type = '2'
                AND A.site IN ('C01','C02')
                ) T
                ORDER BY T.SORT
                ";

        $reportlist2 = DB::select($sql2);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist2) != 0) {
            $arraykeys2=array_keys((array)$reportlist2[0]);
        }

        //行政棟十四樓
        $sql3="SELECT T.XF,	T.name,
                     T.usertype_1,
                     T.usertype_6,
                     T.usertype_2,
                     T.usertype_3,
                     T.usertype_4,
                     T.usertype_5
                FROM (
              SELECT CONCAT(A.type,A.site) AS SORT,
                     (CASE A.type
                            WHEN '1' THEN
                                (CASE SUBSTRING(A.site,1,1)
                                WHEN '1' THEN '一樓'
                                WHEN '2' THEN '二樓'
                                WHEN '3' THEN '三樓'
                                WHEN '4' THEN '四樓'
                                WHEN '5' THEN '五樓'
                                WHEN '6' THEN '六樓'
                                Else ''
                                End
                                )
                            WHEN '2' THEN
                                (CASE A.site
                                WHEN 'C01' THEN '會議棟一樓'
                                WHEN 'C02' THEN '會議棟二樓'
                                WHEN 'C14' THEN '十四樓'
                                Else ''     END
                                )
                            WHEN '5' THEN '行政棟'+
                                (CASE
                                WHEN SUBSTRING(A.name,2,1)='樓'
                                THEN SUBSTRING(A.name,1,2)
                                Else ''     END    )     ELSE ''
                                END    ) AS XF,
                        A.name,
                        IFNULL(B.usertype_1,'') AS usertype_1,
                        IFNULL(B.usertype_6,'') AS usertype_6,
                        IFNULL(B.usertype_2,'') AS usertype_2,
                        IFNULL(B.usertype_3,'') AS usertype_3,
                        IFNULL(B.usertype_4,'') AS usertype_4,
                        IFNULL(B.usertype_5,'') AS usertype_5
                FROM m14tb A LEFT JOIN
                        (
                        SELECT F.site,
                                        COUNT(CASE WHEN F.usertype='1' THEN 1 ELSE NULL END) AS usertype_1,
                                        COUNT(CASE WHEN F.usertype='2' THEN 1 ELSE NULL END) AS usertype_2,
                                        COUNT(CASE WHEN F.usertype='3' THEN 1 ELSE NULL END) AS usertype_3,
                                        COUNT(CASE WHEN F.usertype='4' THEN 1 ELSE NULL END) AS usertype_4,
                                        COUNT(CASE WHEN F.usertype='5' THEN 1 ELSE NULL END) AS usertype_5,
                                        COUNT(CASE WHEN F.usertype='6' THEN 1 ELSE NULL END) AS usertype_6
                            FROM t22tb F INNER JOIN m14tb G ON F.site =G.site
                            WHERE SUBSTRING(F.date,1,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                AND G.timetype = '1'
                            GROUP BY F.site
                        UNION ALL
                        SELECT F.site,
                                    SUM( CASE WHEN (F.usertype = '1' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '1') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
																			               SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
																			                ,
																                    CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
																		                   SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_1,
                                    SUM( CASE WHEN (F.usertype = '2' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '2') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_2,
                                    SUM( CASE WHEN (F.usertype = '3' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '3') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_3,
                                    SUM( CASE WHEN (F.usertype = '4' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '4') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_4,
                                    SUM( CASE WHEN (F.usertype = '5' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '5') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END )  AS Usertype_5,
                                    SUM( CASE WHEN (F.usertype = '6' AND SUBSTRING(F.stime,1,2) = SUBSTRING(F.etime,1,2)) THEN 1
                                                        WHEN (F.usertype = '6') THEN
                                                                TIMESTAMPDIFF(Hour, CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.stime,1,2),':',SUBSTRING(F.stime,3,2),':00')
                                                                                                    ,
                                                                                            CONCAT(SUBSTRING(F.Date,1,3)+'1911', '-' ,SUBSTRING(F.Date,4,2), '-' ,SUBSTRING(F.Date,6,2), ' ',
                                                                                                SUBSTRING(F.etime,1,2),':',SUBSTRING(F.etime,3,2),':00'))
                                                        ELSE 0
                                                END ) AS Usertype_6
                            FROM t22tb F INNER JOIN m14tb G ON F.site =G.site
                            WHERE SUBSTRING(F.date,1,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                                AND G.timetype = '2'
                            GROUP BY F.site
                        ) B ON A.site = B.site
                WHERE A.type IN ('1','2','5')
                AND A.type IN ('2','5')
                AND A.site NOT IN ('C01','C02')
                ) T
                ORDER BY T.SORT
                ";

        $reportlist3 = DB::select($sql3);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist3) != 0) {
            $arraykeys3=array_keys((array)$reportlist3[0]);
        }


        // 檔案名稱
        $fileName = 'N11';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&22'.'行政院人事行政總處公務人力發展學院'.substr($startYear,0,3).'年'.$startMonth.'月場地使用管理月報表');

        $reportlist = json_decode(json_encode($reportlist), true);
        $reportlist2 = json_decode(json_encode($reportlist2), true);
        $reportlist3 = json_decode(json_encode($reportlist3), true);

        //教室一至六樓
        //'<<取得t22tb(辦班需求確認檔)的資料
        if(sizeof($reportlist) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //A
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    //C3開始
                    if($i>0 && $reportlist[$j][$arraykeys[$i]]<>'0'){
                        $objActSheet->setCellValue($NameFromNumber.($j+3), $reportlist[$j][$arraykeys[$i]]);
                    }
                }
            }
            //=
            //=IF((SUM(C3:C17)+SUM(C21:C22))>0,(SUM(C3:C17)+SUM(C21:C22)),"")
            $objActSheet->setCellValue('C23', '=IF((SUM(C3:C17)+SUM(C21:C22))>0,(SUM(C3:C17)+SUM(C21:C22)),"")');
            $objActSheet->setCellValue('D23', '=IF((SUM(D3:D17)+SUM(D21:D22))>0,(SUM(D3:D17)+SUM(D21:D22)),"")');
            $objActSheet->setCellValue('E23', '=IF((SUM(E3:E17)+SUM(E21:E22))>0,(SUM(E3:E17)+SUM(E21:E22)),"")');
            $objActSheet->setCellValue('F23', '=IF((SUM(F3:F17)+SUM(F21:F22))>0,(SUM(F3:F17)+SUM(F21:F22)),"")');
            $objActSheet->setCellValue('G23', '=IF((SUM(G3:G17)+SUM(G21:G22))>0,(SUM(G3:G17)+SUM(G21:G22)),"")');
            $objActSheet->setCellValue('H23', '=IF((SUM(H3:H17)+SUM(H21:H22))>0,(SUM(H3:H17)+SUM(H21:H22)),"")');

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

            $objActSheet->getStyle('A3:'.$NameFromNumber.($j+2))->applyFromArray($styleArray);
            */
        }

        //會議室
        if(sizeof($reportlist2) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys2); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //A
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist2); $j++) {
                    //C3開始
                    if($i>0 && $reportlist2[$j][$arraykeys2[$i]]<>'0'){
                        $objActSheet->setCellValue($NameFromNumber.($j+24), $reportlist2[$j][$arraykeys2[$i]]);
                    }
                }
            }
            //=IF(SUM(C23:C25)>0,SUM(C23:C25),"")
            $objActSheet->setCellValue('C26', '=IF(SUM(C23:C25)>0,SUM(C23:C25),"")');
            $objActSheet->setCellValue('D26', '=IF(SUM(D23:D25)>0,SUM(D23:D25),"")');
            $objActSheet->setCellValue('E26', '=IF(SUM(E23:E25)>0,SUM(E23:E25),"")');
            $objActSheet->setCellValue('F26', '=IF(SUM(F23:F25)>0,SUM(F23:F25),"")');
            $objActSheet->setCellValue('G26', '=IF(SUM(G23:G25)>0,SUM(G23:G25),"")');
            $objActSheet->setCellValue('H26', '=IF(SUM(H23:H25)>0,SUM(H23:H25),"")');
        }


        //行政棟十四樓
        if(sizeof($reportlist3) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys3); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //A
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist3); $j++) {
                    //C3開始
                    if($i>0 && $reportlist3[$j][$arraykeys3[$i]]<>'0'){
                        //=SUM(C27:C29)
                        //=C22+C26+C30
                        if($j!=3 && $j!=4){
                            $objActSheet->setCellValue($NameFromNumber.($j+27), $reportlist3[$j][$arraykeys3[$i]]);
                        }
                    }
                }

            }
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"場地使用管理月報表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
