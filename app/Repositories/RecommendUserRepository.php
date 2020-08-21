<?php
namespace App\Repositories;

use App\Models\M21tb;


class RecommendUserRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getRecommendUserList($queryData = [])
    {
        $query = M21tb::select('m17tb.enrollname', 'm21tb.enrollorg', 'm21tb.section', 'm21tb.username', 'm21tb.telnoa', 'm21tb.telnob', 'm21tb.telnoc', 'm21tb.userid','m21tb.email','m21tb.keyman','m21tb.selfid','m21tb.birthday');

        $query->join('m17tb', 'm17tb.enrollorg', '=', 'm21tb.enrollorg', 'left');

        // 薦送機關代碼
        if ( isset($queryData['enrollorg']) && $queryData['enrollorg'] ) {

            $query->where('m21tb.enrollorg', 'LIKE', '%'.$queryData['enrollorg'].'%');
        }

        // 薦送機關名稱
        if ( isset($queryData['enrollname']) && $queryData['enrollname'] ) {

            $query->where('m17tb.enrollname', 'LIKE', '%'.$queryData['enrollname'].'%');
        }

        // 身分證號
        if ( isset($queryData['userid']) && $queryData['userid'] ) {

            $query->where('m21tb.userid', $queryData['userid']);
        }

        // EMAIL
        if ( isset($queryData['email']) && $queryData['email'] ) {

            $query->where('m21tb.email', 'LIKE', '%'.$queryData['email'].'%');
        }
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
