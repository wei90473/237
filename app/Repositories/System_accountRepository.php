<?php
namespace App\Repositories;

use App\Models\M09tb;
use App\Models\T04tb;
use App\Models\User_group;


class System_accountRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     *
     * serno: 講座代碼
     * userid: 身分證
     * cname: 姓名
     * dept: 服務機關
     * position: 現職
     * liaison: 聯絡人
     * offtela1: 電話(公1) 區碼
     * offtelb1: 電話(公一)', 'offtelc1: ', 'offfaxa', 'offfaxb: 傳真(公)'
     */
    public function getSystem_accountList($queryData = [])
    {
        $query = M09tb::select('id', 'userid' ,'userpsw', 'password' ,'username' ,'email' ,'ext' ,'siteadm' ,'section' ,'sysadm' ,'signno' ,'agent1' ,'agent2' ,'agent3' ,'deptid' ,'userpwd' ,'dimission' ,'chgpswdate');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['userid', 'userid'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        }

        $query->orderBy('userid', 'desc');

        // 關鍵字(name)
        if ( isset($queryData['keyword']) && $queryData['keyword'] ) {
            $query->where('m09tb.username', 'like', '%'.$queryData['keyword'].'%');
        }

        if ( isset($queryData['email']) && $queryData['email'] ) {
            $query->where('m09tb.email', 'LIKE', '%'.$queryData['email'].'%');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    public function getSections()
    {
        $sections = M09tb::select("section","deptid")
                                ->groupBy("section")
                                ->get();
        return $sections;
    }

    public function getUser_group()
    {
        $group = User_group::select("id","name")
                                ->get()->toArray();
        return $group;
    }

    public function getBy_user_group_id($user_group_id)
    {

        $data = User_group::select("id","name")
                ->where('id', $user_group_id)
                ->get()->toArray();
        return $data[0];
    }

    public function getDelete($userid=null)
    {
        $EditDelete = array(
            'delete' => 'Y',
            'msg' => '',
        );

        $query = T04tb::select('sponsor');
        $data1 = $query->where('sponsor', $userid)->get()->toArray();

        if(!empty($data1)){
            $EditDelete['delete'] = 'N';
            $EditDelete['msg'] = '已被設為辦班人員(t04tb),無法刪除！';
        }

        return $EditDelete;
    }

}
