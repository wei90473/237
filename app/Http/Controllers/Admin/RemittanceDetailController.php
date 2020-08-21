<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Services\User_groupService;

class RemittanceDetailController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
      $this->user_groupService = $user_groupService;
      //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('remittance_detail', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    /**
     * 列表頁
     *  getpaidday
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getpaidday();
        $tdateArr=$temp;
        $result="";
        return view('admin/remittance_detail/list',compact('result','tdateArr'));
    }
    public function export(Request $request){
        $RptBasic = new \App\Rptlib\RptBasic();
        $showdate="";
        $date=$request->input('tdate');
        if(strlen($request->input('tdate'))==7)
            $showdate="存款日期：".substr($request->input('tdate'),0,3)."/".substr($request->input('tdate'),3,2)."/".substr($request->input('tdate'),5,2);

        $sql="SELECT
        FBB.編號,
        FBB.姓名,
        FBB.身分證字號,
        FBB.銀行名稱,
        FBB.代號,
        FBB.帳號,
        (CASE WHEN ISNULL(FAA.金額) THEN FBB.金額 ELSE FAA.金額 END) AS 金額,
        (CASE WHEN ISNULL(FAA.補充保險費) THEN FBB.補充保險費 ELSE FAA.補充保險費 END) AS 補充保險費,
        (CASE WHEN ISNULL(FAA.實付金額) THEN FBB.實付金額 ELSE FAA.實付金額 END) AS 實付金額,
        (CASE WHEN ISNULL(FAA.扣繳稅額) THEN FBB.扣繳稅額 ELSE FAA.扣繳稅額 END) AS 扣繳稅額,
        FBB.住址
        #select *
        FROM
        (
        SELECT
        sort AS 編號,
        姓名,
        身分證字號,
        銀行名稱,
        代號,
        帳號,
        金額,
        補充保險費,
        扣繳稅額,
        實付金額,
        住址
        FROM
        (
        SELECT
         AYY.sort AS sort,
         AYY.serno AS serno,
         AYY.paymk AS paymk,
         AYY.transfor AS transfor,
         AYY.class AS class,
         AYY.term AS term,
         AYY.course AS course,
         AYY.type AS type,
         AYY.kind AS kind,
         AYY.date AS date,
         AYY.stime AS stime,
         AYY.姓名 AS 姓名,
         AYY.身分證字號 AS 身分證字號,
         AYY.銀行名稱 AS 銀行名稱,
         AYY.代號 AS 代號,
         AYY.帳號 AS 帳號,
         AYY.金額+cte.金額 AS 金額,
         AYY.補充保險費+cte.補充保險費 AS 補充保險費,
         AYY.扣繳稅額+cte.扣繳稅額 AS 扣繳稅額,
         AYY.實付金額 AS 實付金額,
         AYY.住址 AS 住址
        FROM
        (
        SELECT
         BXX.sort AS sort,
         AXX.serno AS serno,
         AXX.paymk AS paymk,
         AXX.transfor AS transfor,
         BXX.class AS class,
         BXX.term AS term,
         BXX.course AS course,
         BXX.type AS type,
         BXX.kind AS kind,
         BXX.date AS date,
         BXX.stime AS stime,
         AXX.姓名 AS 姓名,
         AXX.身分證字號 AS 身分證字號,
         AXX.銀行名稱 AS 銀行名稱,
         AXX.代號 AS 代號,
         AXX.帳號 AS 帳號,
         BXX.teachtot+BXX.tratot AS 金額,
         BXX.insuretot AS 補充保險費,
         BXX.deductamt AS 扣繳稅額,
         AXX.實付金額 AS 實付金額,
         AXX.住址 AS 住址
        FROM

        (
        SELECT * FROM
        (
        SELECT
        '1' AS paymk,
        A.transfor, /* 轉帳帳戶 1:郵局 2:金融機構 */
        ROW_NUMBER() OVER(PARTITION BY A.idno ORDER BY A.serno) AS serno,
        RTRIM(A.accname) AS 姓名,
        A.idno AS 身分證字號,
        (
         CASE
          WHEN A.transfor = '1' THEN RTRIM(A.post) /* 郵局 */
          WHEN A.transfor = '2' THEN RTRIM(A.bank) /* 金融機構 */
          ELSE ''
         END
        ) AS 銀行名稱,
        (
         CASE
          WHEN A.transfor = '1' THEN RTRIM(A.postcode) /* 郵局 */
          WHEN A.transfor = '2' THEN RTRIM(A.bankcode) /* 金融機構 */
          ELSE ''
         END
        ) AS 代號,
        (
         CASE
          WHEN A.transfor = '1' THEN RTRIM(A.postno) /* 郵局 */
          WHEN A.transfor = '2' THEN RTRIM(A.bankno) /* 金融機構 */
          ELSE ''
         END
        ) AS 帳號,
        CAST(LEFT(A.amt,8) AS int) AS 實付金額,
        REPLACE(REPLACE(RTRIM(B.regaddress),CHAR(10),''),CHAR(13),'') AS 住址
        FROM t11tb A
        INNER JOIN m01tb B
        ON A.idno = B.idno
        WHERE A.date = '".$date."'
        ORDER BY A.serno ) AS AX
        UNION
        SELECT
        '2' AS paymk,
        A.transfor, /* 轉帳帳戶 1:郵局 2:金融機構 */
        ROW_NUMBER() OVER(PARTITION BY A.idno ORDER BY A.serno),
        RTRIM(A.cname) AS 姓名,
        A.idno AS 身分證字號,
        RTRIM(A.transname) AS 銀行名稱,
        RTRIM(A.transcode) AS 代號,
        RTRIM(A.transno) AS 帳號,
        A.amt AS 實付金額,
        REPLACE(REPLACE(RTRIM(B.regaddress),CHAR(10),''),CHAR(13),'') AS 住址
        FROM t93tb A
        INNER JOIN m01tb B
        ON A.idno = B.idno
        WHERE A.date = '".$date."' ) AS AXX

        INNER JOIN

        (
        SELECT
           AA.r,
           BB.sort,
           AA.serno,
           AA.class,     /* 班號 */
           AA.term,      /* 期別 */
           AA.course,    /* 課程編號 */
           AA.idno,      /* 身分證字號 */
           AA.teachtot,  /* 講課酬勞合計 */
           AA.tratot,    /* 交通費合計 */
           AA.insuretot, /* 補充保費合計 */
           AA.deductamt, /* 扣繳稅額合計 */
           AA.totalpay,  /* 實付總計 */
           AA.paymk,     /* 付款方式  1:郵局轉帳 2:支付處付款*/
           AA.type,    /* 類型 1:講座 2:助理 */
           AA.kind,
           AA.date,
           AA.stime
        FROM
        (
           SELECT
           (@rn2:=@rn2 + 1) AS r,
           '' AS sort,
           ROW_NUMBER() OVER(PARTITION BY A.paymk,A.idno ORDER BY A.idno,A.class,A.term,A.course) AS serno,
           A.class,     /* 班號 */
           A.term,      /* 期別 */
           A.course,    /* 課程編號 */
           A.idno,      /* 身分證字號 */
           A.teachtot,  /* 講課酬勞合計 */
           A.tratot,    /* 交通費合計 */
           A.insuretot, /* 補充保費合計 */
           A.deductamt, /* 扣繳稅額合計 */
           A.totalpay,  /* 實付總計 */
           A.paymk,     /* 付款方式  1:郵局轉帳 2:支付處付款*/
           A.type,    /* 類型 1:講座 2:助理 */
           B.kind,
           C.date,
           C.stime
           FROM t09tb A INNER JOIN t04tb B ON A.class = B.class AND A.term = B.term
           INNER JOIN t06tb C ON A.class = C.class AND A.term = C.term AND A.course = C.course, (SELECT @rn2 := 0) AS rnt2
           WHERE paidday = '".$date."' /* 轉帳日期 */
           ORDER BY paymk,idno
        ) AA
        INNER JOIN
        (
           SELECT
           r,
           ROW_NUMBER() OVER(ORDER BY kind,class,term,date,stime,CONCAT(course,type),idno) AS sort
           FROM
           (
               SELECT
               (@rn1:=@rn1 + 1) AS r,
               '' AS sort,
               ROW_NUMBER() OVER(PARTITION BY A.paymk,A.idno ORDER BY A.idno,A.class,A.term,A.course) AS serno,
               A.class,     /* 班號 */
               A.term,      /* 期別 */
               A.course,    /* 課程編號 */
               A.idno,      /* 身分證字號 */
               A.teachtot,  /* 講課酬勞合計 */
               A.tratot,    /* 交通費合計 */
               A.insuretot, /* 補充保費合計 */
               A.deductamt, /* 扣繳稅額合計 */
               A.totalpay,  /* 實付總計 */
               A.paymk,     /* 付款方式  1:郵局轉帳 2:支付處付款*/
               A.type,    /* 類型 1:講座 2:助理 */
               B.kind,
               C.date,
               C.stime
               FROM t09tb A INNER JOIN t04tb B ON A.class = B.class AND A.term = B.term
               INNER JOIN t06tb C ON A.class = C.class AND A.term = C.term AND A.course = C.course, (SELECT @rn1 := 0) AS rnt1
               WHERE paidday = '".$date."' /* 轉帳日期 */
               ORDER BY paymk,idno
           ) tmp1
        ) BB ON AA.r = BB.r
        ) AS BXX ON AXX.paymk = BXX.paymk AND AXX.身分證字號 = BXX.idno AND AXX.serno = BXX.serno
        ) AS AYY

        INNER JOIN
        (SELECT
         BZZ.idno,
         SUM(BZZ.teachtot+BZZ.tratot) AS 金額,
         SUM(BZZ.insuretot) AS 補充保險費,
         SUM(BZZ.deductamt) AS 扣繳稅額
         FROM

         (
        SELECT
           AA.r,
           BB.sort,
           AA.serno,
           AA.class,     /* 班號 */
           AA.term,      /* 期別 */
           AA.course,    /* 課程編號 */
           AA.idno,      /* 身分證字號 */
           AA.teachtot,  /* 講課酬勞合計 */
           AA.tratot,    /* 交通費合計 */
           AA.insuretot, /* 補充保費合計 */
           AA.deductamt, /* 扣繳稅額合計 */
           AA.totalpay,  /* 實付總計 */
           AA.paymk,     /* 付款方式  1:郵局轉帳 2:支付處付款*/
           AA.type,    /* 類型 1:講座 2:助理 */
           AA.kind,
           AA.date,
           AA.stime
        FROM
        (
           SELECT
           (@rn2:=@rn2 + 1) AS r,
           '' AS sort,
           ROW_NUMBER() OVER(PARTITION BY A.paymk,A.idno ORDER BY A.idno,A.class,A.term,A.course) AS serno,
           A.class,     /* 班號 */
           A.term,      /* 期別 */
           A.course,    /* 課程編號 */
           A.idno,      /* 身分證字號 */
           A.teachtot,  /* 講課酬勞合計 */
           A.tratot,    /* 交通費合計 */
           A.insuretot, /* 補充保費合計 */
           A.deductamt, /* 扣繳稅額合計 */
           A.totalpay,  /* 實付總計 */
           A.paymk,     /* 付款方式  1:郵局轉帳 2:支付處付款*/
           A.type,    /* 類型 1:講座 2:助理 */
           B.kind,
           C.date,
           C.stime
           FROM t09tb A INNER JOIN t04tb B ON A.class = B.class AND A.term = B.term
           INNER JOIN t06tb C ON A.class = C.class AND A.term = C.term AND A.course = C.course, (SELECT @rn2 := 0) AS rnt2
           WHERE paidday = '".$date."' /* 轉帳日期 */
           ORDER BY paymk,idno
        ) AA
        INNER JOIN
        (
           SELECT
           r,
           ROW_NUMBER() OVER(ORDER BY kind,class,term,date,stime,CONCAT(course,type),idno) AS sort
           FROM
           (
               SELECT
               (@rn1:=@rn1 + 1) AS r,
               '' AS sort,
               ROW_NUMBER() OVER(PARTITION BY A.paymk,A.idno ORDER BY A.idno,A.class,A.term,A.course) AS serno,
               A.class,     /* 班號 */
               A.term,      /* 期別 */
               A.course,    /* 課程編號 */
               A.idno,      /* 身分證字號 */
               A.teachtot,  /* 講課酬勞合計 */
               A.tratot,    /* 交通費合計 */
               A.insuretot, /* 補充保費合計 */
               A.deductamt, /* 扣繳稅額合計 */
               A.totalpay,  /* 實付總計 */
               A.paymk,     /* 付款方式  1:郵局轉帳 2:支付處付款*/
               A.type,    /* 類型 1:講座 2:助理 */
               B.kind,
               C.date,
               C.stime
               FROM t09tb A INNER JOIN t04tb B ON A.class = B.class AND A.term = B.term
               INNER JOIN t06tb C ON A.class = C.class AND A.term = C.term AND A.course = C.course, (SELECT @rn1 := 0) AS rnt1
               WHERE paidday = '".$date."' /* 轉帳日期 */
               ORDER BY paymk,idno
           ) tmp1
        ) BB ON AA.r = BB.r
        ) AS BZZ


         WHERE NOT EXISTS(
          SELECT
          NULL
          FROM

          (
        SELECT
         BXX.sort AS sort,
         AXX.serno AS serno,
         AXX.paymk AS paymk,
         AXX.transfor AS　transfor,
         BXX.class AS class,
         BXX.term AS term,
         BXX.course AS course,
         BXX.type AS type,
         BXX.kind AS kind,
         BXX.date AS date,
         BXX.stime AS stime,
         AXX.姓名 AS　姓名,
         AXX.身分證字號 AS 身分證字號,
         AXX.銀行名稱 AS 銀行名稱,
         AXX.代號 AS 代號,
         AXX.帳號 AS 帳號,
         BXX.teachtot+BXX.tratot AS金額,
         BXX.insuretot AS 補充保險費,
         BXX.deductamt AS 扣繳稅額,
         AXX.實付金額 AS 實付金額,
         AXX.住址 AS 住址
        FROM

        (
        SELECT * FROM
        (
        SELECT
        '1' AS paymk,
        A.transfor, /* 轉帳帳戶 1:郵局 2:金融機構 */
        ROW_NUMBER() OVER(PARTITION BY A.idno ORDER BY A.serno) AS serno,
        RTRIM(A.accname) AS 姓名,
        A.idno AS 身分證字號,
        (
         CASE
          WHEN A.transfor = '1' THEN RTRIM(A.post) /* 郵局 */
          WHEN A.transfor = '2' THEN RTRIM(A.bank) /* 金融機構 */
          ELSE ''
         END
        ) AS 銀行名稱,
        (
         CASE
          WHEN A.transfor = '1' THEN RTRIM(A.postcode) /* 郵局 */
          WHEN A.transfor = '2' THEN RTRIM(A.bankcode) /* 金融機構 */
          ELSE ''
         END
        ) AS 代號,
        (
         CASE
          WHEN A.transfor = '1' THEN RTRIM(A.postno) /* 郵局 */
          WHEN A.transfor = '2' THEN RTRIM(A.bankno) /* 金融機構 */
          ELSE ''
         END
        ) AS 帳號,
        CAST(LEFT(A.amt,8) AS int) AS 實付金額,
        REPLACE(REPLACE(RTRIM(B.regaddress),CHAR(10),''),CHAR(13),'') AS 住址
        FROM t11tb A
        INNER JOIN m01tb B
        ON A.idno = B.idno
        WHERE A.date = '".$date."'
        ORDER BY A.serno ) AS AX
        UNION
        SELECT
        '2' AS paymk,
        A.transfor, /* 轉帳帳戶 1:郵局 2:金融機構 */
        ROW_NUMBER() OVER(PARTITION BY A.idno ORDER BY A.serno),
        RTRIM(A.cname) AS 姓名,
        A.idno AS 身分證字號,
        RTRIM(A.transname) AS 銀行名稱,
        RTRIM(A.transcode) AS 代號,
        RTRIM(A.transno) AS 帳號,
        A.amt AS 實付金額,
        REPLACE(REPLACE(RTRIM(B.regaddress),CHAR(10),''),CHAR(13),'') AS 住址
        FROM t93tb A
        INNER JOIN m01tb B
        ON A.idno = B.idno
        WHERE A.date = '".$date."' ) AS AXX

        INNER JOIN

        (
        SELECT
           AA.r,
           BB.sort,
           AA.serno,
           AA.class,     /* 班號 */
           AA.term,      /* 期別 */
           AA.course,    /* 課程編號 */
           AA.idno,      /* 身分證字號 */
           AA.teachtot,  /* 講課酬勞合計 */
           AA.tratot,    /* 交通費合計 */
           AA.insuretot, /* 補充保費合計 */
           AA.deductamt, /* 扣繳稅額合計 */
           AA.totalpay,  /* 實付總計 */
           AA.paymk,     /* 付款方式  1:郵局轉帳 2:支付處付款*/
           AA.type,    /* 類型 1:講座 2:助理 */
           AA.kind,
           AA.date,
           AA.stime
        FROM
        (
           SELECT
           (@rn2:=@rn2 + 1) AS r,
           '' AS sort,
           ROW_NUMBER() OVER(PARTITION BY A.paymk,A.idno ORDER BY A.idno,A.class,A.term,A.course) AS serno,
           A.class,     /* 班號 */
           A.term,      /* 期別 */
           A.course,    /* 課程編號 */
           A.idno,      /* 身分證字號 */
           A.teachtot,  /* 講課酬勞合計 */
           A.tratot,    /* 交通費合計 */
           A.insuretot, /* 補充保費合計 */
           A.deductamt, /* 扣繳稅額合計 */
           A.totalpay,  /* 實付總計 */
           A.paymk,     /* 付款方式  1:郵局轉帳 2:支付處付款*/
           A.type,    /* 類型 1:講座 2:助理 */
           B.kind,
           C.date,
           C.stime
           FROM t09tb A INNER JOIN t04tb B ON A.class = B.class AND A.term = B.term
           INNER JOIN t06tb C ON A.class = C.class AND A.term = C.term AND A.course = C.course, (SELECT @rn2 := 0) AS rnt2
           WHERE paidday = '".$date."' /* 轉帳日期 */
           ORDER BY paymk,idno
        ) AA
        INNER JOIN
        (
           SELECT
           r,
           ROW_NUMBER() OVER(ORDER BY kind,class,term,date,stime,CONCAT(course,type),idno) AS sort
           FROM
           (
               SELECT
               (@rn1:=@rn1 + 1) AS r,
               '' AS sort,
               ROW_NUMBER() OVER(PARTITION BY A.paymk,A.idno ORDER BY A.idno,A.class,A.term,A.course) AS serno,
               A.class,     /* 班號 */
               A.term,      /* 期別 */
               A.course,    /* 課程編號 */
               A.idno,      /* 身分證字號 */
               A.teachtot,  /* 講課酬勞合計 */
               A.tratot,    /* 交通費合計 */
               A.insuretot, /* 補充保費合計 */
               A.deductamt, /* 扣繳稅額合計 */
               A.totalpay,  /* 實付總計 */
               A.paymk,     /* 付款方式  1:郵局轉帳 2:支付處付款*/
               A.type,    /* 類型 1:講座 2:助理 */
               B.kind,
               C.date,
               C.stime
               FROM t09tb A INNER JOIN t04tb B ON A.class = B.class AND A.term = B.term
               INNER JOIN t06tb C ON A.class = C.class AND A.term = C.term AND A.course = C.course, (SELECT @rn1 := 0) AS rnt1
               WHERE paidday = '".$date."' /* 轉帳日期 */
               ORDER BY paymk,idno
           ) tmp1
        ) BB ON AA.r = BB.r
        ) AS BXX ON AXX.paymk = BXX.paymk AND AXX.身分證字號 = BXX.idno AND AXX.serno = BXX.serno
        ) AS AZZ

          WHERE AZZ.paymk = BZZ.paymk
          AND AZZ.身分證字號= BZZ.idno
          AND AZZ.serno = BZZ.serno
         )
         GROUP BY BZZ.idno
        ) AS cte
        ON AYY.身分證字號 = cte.idno
        AND AYY.serno = 1 ) AS ZZZ ) AS FAA
        RIGHT JOIN
        (
        SELECT
        sort AS 編號,
        姓名,
        身分證字號,
        銀行名稱,
        代號,
        帳號,
        金額,
        補充保險費,
        扣繳稅額,
        實付金額,
        住址
        FROM
        (
        SELECT
         BXX.sort AS sort,
         AXX.serno AS serno,
         AXX.paymk AS paymk,
         AXX.transfor AS　transfor,
         BXX.class AS class,
         BXX.term AS term,
         BXX.course AS course,
         BXX.type AS type,
         BXX.kind AS kind,
         BXX.date AS date,
         BXX.stime AS stime,
         AXX.姓名 AS 姓名,
         AXX.身分證字號 AS 身分證字號,
         AXX.銀行名稱 AS 銀行名稱,
         AXX.代號 AS 代號,
         AXX.帳號 AS 帳號,
         BXX.teachtot+BXX.tratot AS 金額,
         BXX.insuretot AS 補充保險費,
         BXX.deductamt AS 扣繳稅額,
         AXX.實付金額 AS 實付金額,
         AXX.住址 AS 住址
        FROM

        (
        SELECT * FROM
        (
        SELECT
        '1' AS paymk,
        A.transfor, /* 轉帳帳戶 1:郵局 2:金融機構 */
        ROW_NUMBER() OVER(PARTITION BY A.idno ORDER BY A.serno) AS serno,
        RTRIM(A.accname) AS 姓名,
        A.idno AS 身分證字號,
        (
         CASE
          WHEN A.transfor = '1' THEN RTRIM(A.post) /* 郵局 */
          WHEN A.transfor = '2' THEN RTRIM(A.bank) /* 金融機構 */
          ELSE ''
         END
        ) AS 銀行名稱,
        (
         CASE
          WHEN A.transfor = '1' THEN RTRIM(A.postcode) /* 郵局 */
          WHEN A.transfor = '2' THEN RTRIM(A.bankcode) /* 金融機構 */
          ELSE ''
         END
        ) AS 代號,
        (
         CASE
          WHEN A.transfor = '1' THEN RTRIM(A.postno) /* 郵局 */
          WHEN A.transfor = '2' THEN RTRIM(A.bankno) /* 金融機構 */
          ELSE ''
         END
        ) AS 帳號,
        CAST(LEFT(A.amt,8) AS int) AS 實付金額,
        REPLACE(REPLACE(RTRIM(B.regaddress),CHAR(10),''),CHAR(13),'') AS 住址
        FROM t11tb A
        INNER JOIN m01tb B
        ON A.idno = B.idno
        WHERE A.date = '".$date."'
        ORDER BY A.serno ) AS AX
        UNION
        SELECT
        '2' AS paymk,
        A.transfor, /* 轉帳帳戶 1:郵局 2:金融機構 */
        ROW_NUMBER() OVER(PARTITION BY A.idno ORDER BY A.serno),
        RTRIM(A.cname) AS 姓名,
        A.idno AS 身分證字號,
        RTRIM(A.transname) AS 銀行名稱,
        RTRIM(A.transcode) AS 代號,
        RTRIM(A.transno) AS 帳號,
        A.amt AS 實付金額,
        REPLACE(REPLACE(RTRIM(B.regaddress),CHAR(10),''),CHAR(13),'') AS 住址
        FROM t93tb A
        INNER JOIN m01tb B
        ON A.idno = B.idno
        WHERE A.date = '".$date."' ) AS AXX

        INNER JOIN

        (
        SELECT
           AA.r,
           BB.sort,
           AA.serno,
           AA.class,     /* 班號 */
           AA.term,      /* 期別 */
           AA.course,    /* 課程編號 */
           AA.idno,      /* 身分證字號 */
           AA.teachtot,  /* 講課酬勞合計 */
           AA.tratot,    /* 交通費合計 */
           AA.insuretot, /* 補充保費合計 */
           AA.deductamt, /* 扣繳稅額合計 */
           AA.totalpay,  /* 實付總計 */
           AA.paymk,     /* 付款方式  1:郵局轉帳 2:支付處付款*/
           AA.type,    /* 類型 1:講座 2:助理 */
           AA.kind,
           AA.date,
           AA.stime
        FROM
        (
           SELECT
           (@rn2:=@rn2 + 1) AS r,
           '' AS sort,
           ROW_NUMBER() OVER(PARTITION BY A.paymk,A.idno ORDER BY A.idno,A.class,A.term,A.course) AS serno,
           A.class,     /* 班號 */
           A.term,      /* 期別 */
           A.course,    /* 課程編號 */
           A.idno,      /* 身分證字號 */
           A.teachtot,  /* 講課酬勞合計 */
           A.tratot,    /* 交通費合計 */
           A.insuretot, /* 補充保費合計 */
           A.deductamt, /* 扣繳稅額合計 */
           A.totalpay,  /* 實付總計 */
           A.paymk,     /* 付款方式  1:郵局轉帳 2:支付處付款*/
           A.type,    /* 類型 1:講座 2:助理 */
           B.kind,
           C.date,
           C.stime
           FROM t09tb A INNER JOIN t04tb B ON A.class = B.class AND A.term = B.term
           INNER JOIN t06tb C ON A.class = C.class AND A.term = C.term AND A.course = C.course, (SELECT @rn2 := 0) AS rnt2
           WHERE paidday = '".$date."' /* 轉帳日期 */
           ORDER BY paymk,idno
        ) AA
        INNER JOIN
        (
           SELECT
           r,
           ROW_NUMBER() OVER(ORDER BY kind,class,term,date,stime,CONCAT(course,type),idno) AS sort
           FROM
           (
               SELECT
               (@rn1:=@rn1 + 1) AS r,
               '' AS sort,
               ROW_NUMBER() OVER(PARTITION BY A.paymk,A.idno ORDER BY A.idno,A.class,A.term,A.course) AS serno,
               A.class,     /* 班號 */
               A.term,      /* 期別 */
               A.course,    /* 課程編號 */
               A.idno,      /* 身分證字號 */
               A.teachtot,  /* 講課酬勞合計 */
               A.tratot,    /* 交通費合計 */
               A.insuretot, /* 補充保費合計 */
               A.deductamt, /* 扣繳稅額合計 */
               A.totalpay,  /* 實付總計 */
               A.paymk,     /* 付款方式  1:郵局轉帳 2:支付處付款*/
               A.type,    /* 類型 1:講座 2:助理 */
               B.kind,
               C.date,
               C.stime
               FROM t09tb A INNER JOIN t04tb B ON A.class = B.class AND A.term = B.term
               INNER JOIN t06tb C ON A.class = C.class AND A.term = C.term AND A.course = C.course, (SELECT @rn1 := 0) AS rnt1
               WHERE paidday = '".$date."' /* 轉帳日期 */
               ORDER BY paymk,idno
           ) tmp1
        ) BB ON AA.r = BB.r
        ) AS BXX ON AXX.paymk = BXX.paymk AND AXX.身分證字號 = BXX.idno AND AXX.serno = BXX.serno
         ) AS ZZZ ) AS FBB
         ON FAA.編號=FBB.編號 ORDER BY FBB.編號 ";



        $temp=DB::select($sql);

        if($temp==[]){
            $RptBasic = new \App\Rptlib\RptBasic();
            $temp=$RptBasic->getpaidday();
            $tdateArr=$temp;
            $result ="此條件查無資料，請重新查詢";
            return view('admin/remittance_detail/list',compact('result','tdateArr'));
        }
        $data=json_decode(json_encode($temp), true);
        $datakeys=array_keys((array)$data[0]);

        $sql="SELECT branch FROM t11tb WHERE date = '".$date."' ";
        $temp = json_decode(json_encode(DB::select($sql)), true);
        $branchdata=$temp;
        $title="";
        if($branchdata!=[]){
            if($branchdata[0]["branch"]=="1")
                $title="行政院人事行政總處公務人力發展學院(臺北院區)匯款明細表";
            elseif($branchdata[0]["branch"]=="2")
            $title="行政院人事行政總處公務人力發展學院(南投院區)匯款明細表";
        }

        // EXCEL範本檔案名稱
        $fileName = 'H18';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel，
        $objPHPExcel = IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();

        $objActSheet->setCellValue('A1',$title);
        $objActSheet->setCellValue('K2',$showdate);

        $rowpos=4;
        $rowcnt=0;
        $Gsum=0;
        $Hsum=0;
        $Isum=0;
        $Jsum=0;
        $Gtotal=0;
        $Htotal=0;
        $Itotal=0;
        $Jtotal=0;

        for($i=0;$i<sizeof($data);$i++){
            for($j=0;$j<sizeof($datakeys);$j++){
                $objActSheet->setCellValue($this->getNameFromNumber($j+1).strval($rowpos),$data[$i][$datakeys[$j]]);
            }

            $Gsum+=$data[$i]["金額"];
            $Hsum+=$data[$i]["補充保險費"];
            $Isum+=$data[$i]["扣繳稅額"];
            $Jsum+=$data[$i]["實付金額"];
            $Gtotal+=$data[$i]["金額"];
            $Htotal+=$data[$i]["補充保險費"];
            $Itotal+=$data[$i]["扣繳稅額"];
            $Jtotal+=$data[$i]["實付金額"];
            $rowpos++;
            $rowcnt++;

            if(($i+1)%30==0)
            {
                $objActSheet->setCellValue('F'.strval($rowpos),'小計');
                $objActSheet->setCellValue('G'.strval($rowpos),$Gsum);
                $objActSheet->setCellValue('H'.strval($rowpos),$Hsum);
                $objActSheet->setCellValue('I'.strval($rowpos),$Isum);
                $objActSheet->setCellValue('J'.strval($rowpos),$Jsum);
                $Gsum=0;
                $Hsum=0;
                $Isum=0;
                $Jsum=0;
                $rowpos++;

            }

            if($i==(sizeof($data)-1)){
                $objActSheet->setCellValue('F'.strval($rowpos),'小計');
                $objActSheet->setCellValue('G'.strval($rowpos),$Gsum);
                $objActSheet->setCellValue('H'.strval($rowpos),$Hsum);
                $objActSheet->setCellValue('I'.strval($rowpos),$Isum);
                $objActSheet->setCellValue('J'.strval($rowpos),$Jsum);
                $rowpos++;
                $objActSheet->setCellValue('F'.strval($rowpos),'合計');
                $objActSheet->setCellValue('G'.strval($rowpos),$Gtotal);
                $objActSheet->setCellValue('H'.strval($rowpos),$Htotal);
                $objActSheet->setCellValue('I'.strval($rowpos),$Itotal);
                $objActSheet->setCellValue('J'.strval($rowpos),$Jtotal);
            }

        }

        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];


        $objActSheet->getStyle('A3:K'.strval($rowpos))->applyFromArray($styleArray);

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"匯款明細表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }


}
