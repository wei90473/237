<?php
namespace App\Repositories;

use App\Models\T22tb;
use App\Models\T23tb;
use DB;


class ClassControlRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassControlList($queryData)
    {
        echo 123;exit;
        // 取得設定的日期
        $monthly = DB::select('select monthly from s02tb');
        $monthly = $monthly[0]->monthly;

        if ($queryData['type'] == '1') {
            // 下次
            $data['startDate'] = date('Y-m-').$monthly;
            $data['endDate'] = date('Y-m-d', strtotime('+1 month', strtotime($data['startDate'])));
        } else {
            // 上次
            $data['endDate'] = date('Y-m-').$monthly;
            $data['startDate'] = date('Y-m-d', strtotime('-1 month', strtotime($data['endDate'])));
        }

        $data = $this->getDateFormat($data);

        $sql = "
            SELECT DISTINCT t22.class, t22.term, t22.request, IFNULL(t01.name,'') AS classname, IFNULL(t38.name,'') AS meetname,
            LTRIM(IFNULL(t04.sdate,'') + IFNULL(t38.sdate,'')) AS sdate, LTRIM(IFNULL(t04.edate,'') + IFNULL(t38.edate,'')) AS edate
            FROM t22tb t22 LEFT JOIN t01tb t01 ON t22.class = t01.class
            LEFT JOIN t38tb t38 ON t22.class + t22.term = t38.meet + t38.serno
            LEFT JOIN t04tb t04 ON RTRIM(t22.class) + t22.term = t04.class + t04.term
             WHERE request BETWEEN '".$data['startDate']."' AND '".$data['endDate']."'
                UNION
              SELECT DISTINCT t23.class, t23.term, t23.request, IFNULL(t01.name,'') AS classname, IFNULL(t38.name,'') AS meetname,
            LTRIM(IFNULL(t04.sdate,'') + IFNULL(t38.sdate,'')) AS sdate, LTRIM(IFNULL(t04.edate,'') + IFNULL(t38.edate,'')) AS edate
            FROM t23tb t23 LEFT JOIN t01tb t01 ON t23.class = t01.class
            LEFT JOIN t38tb t38 ON t23.class + t23.term = t38.meet + t38.serno
            LEFT JOIN t04tb t04 ON RTRIM(t23.class) + t23.term = t04.class + t04.term
             WHERE request BETWEEN '".$data['startDate']."' AND '".$data['endDate']."'   /*  ' 需求凍結的日期範圍 */
             AND t23.type = '3'
             
             ORDER BY sdate, edate, class, term";

        echo $sql;exit;

//        $result = DB::select($sql);



        echo '<pre>';print_r($result);echo '</pre>';exit;

        return $data;
    }

    /**
     * 取得民國格式
     *
     * @param $data
     * @return mixed
     */
    private function getDateFormat($data)
    {
        $result['startDate'] = (date('Y', strtotime($data['startDate'])) - 1911).date('md', strtotime($data['startDate']));
        $result['endDate'] = (date('Y', strtotime($data['endDate'])) - 1911).date('md', strtotime($data['endDate']));

        return $result;
    }
}
