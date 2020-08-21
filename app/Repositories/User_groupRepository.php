<?php
namespace App\Repositories;

use App\Models\User_group;
use App\Models\User_group_auth;


class User_groupRepository
{

    public function getUser_groupList($queryData = [])
    {
        $query = User_group::select('id', 'name');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['userid', 'userid'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        }

        $query->orderBy('id', 'asc');


        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    public function getUser_group_auth($user_group_id=null)
    {
        $user_group_auth = User_group_auth::select("menu")
                                ->where("user_group_id", $user_group_id)
                                ->get()->toArray();
        $group_auth = array();
        foreach($user_group_auth as $row){
            $group_auth[] = $row['menu'];
        }

        return $group_auth;
    }

    public function getUser_auth($user_group_id)
    {
        $user_group_id = explode(",",$user_group_id);
        $user_group_auth = array();
        foreach($user_group_id as $row){
            $user_auth = $this->getUser_group_auth($row);
            $user_group_auth[] = $user_auth;
        }
        $return_user_auth = array();
        foreach($user_group_auth as $user_group_auth_row){
            $return_user_auth = array_unique(array_merge($return_user_auth, $user_group_auth_row));
        }
        // dd($return_user_auth);

        return $return_user_auth;
    }

}
