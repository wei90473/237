<?php
namespace App\Repositories;

use App\Models\M01tb;
use App\Models\T01tb;
use App\Models\T09tb;
use App\Models\T11tb;
use App\Models\T12tb;
use App\Models\M08tb;
use App\Models\M09tb;
use App\Models\M16tb;
use App\Models\Dbteachingmaterial;


class TeachingMaterialRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     *
     * serno: 講座代碼
     * idno: 身分證
     * cname: 姓名
     * dept: 服務機關
     * position: 現職
     * liaison: 聯絡人
     * offtela1: 電話(公1) 區碼
     * offtelb1: 電話(公一)', 'offtelc1: ', 'offfaxa', 'offfaxb: 傳真(公)'
     */
    public function getTeachingMaterialList($queryData = [])
    {
        $query = M01tb::select('m01tb.serno', 'm01tb.idno', 'm01tb.cname', 'm01tb.dept', 'm01tb.position', 'm01tb.liaison', 'm01tb.offtela1', 'm01tb.offtelb1', 'm01tb.offtelc1', 'm01tb.offfaxa', 'm01tb.offfaxb', 'm01tb.email');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['idno', 'idno'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        }

        $query->orderBy('serno', 'desc');

        // 關鍵字(name)
        if ( isset($queryData['keyword']) && $queryData['keyword'] ) {

            $query->where(function ($query) use ($queryData) {
                $query->where('m01tb.cname', 'like', '%'.$queryData['keyword'].'%')
                    ->orwhere('m01tb.ename', 'like', '%'.$queryData['keyword'].'%');
            });
        }

        // 身分證字號
        if ( isset($queryData['idno']) && $queryData['idno'] ) {

            $query->where('m01tb.idno', 'LIKE', '%'.$queryData['idno'].'%');
        }

        if ( isset($queryData['email']) && $queryData['email'] ) {

            $query->where('m01tb.email', 'LIKE', '%'.$queryData['email'].'%');
        }

        if ( isset($queryData['experience']) && $queryData['experience'] ) {
            $M16tb_query = M16tb::select('m16tb.idno');
            $M16tb_data = $M16tb_query->where('specialty', $queryData['experience'])->distinct()->get()->toArray();
            if(!empty($M16tb_data)){
                $query->whereIn('m01tb.idno', $M16tb_data);
            }
        }

        if ( isset($queryData['dept']) && $queryData['dept'] ) {

            $query->where('m01tb.dept', 'LIKE', '%'.$queryData['dept'].'%');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    public function getDetailList($queryData)
    {
        $query = Dbteachingmaterial::select('*')->where('m01serno', $queryData['serno']);
        if(!empty($queryData['keyword'])){
            $list = $query->where('name', $queryData['keyword'])->get()->toArray();
        }else{
            $list = $query->get()->toArray();
        }

        foreach($list as & $row){
            if(!empty($row['addid'])){
                $query = M09tb::select('username');
                $name_data = $query->where('id', $row['addid'])->get()->toArray();
                $row['addid'] = $name_data[0]['username'];
            }
        }

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($list);
        // echo "\n</pre>\n";
        // die();
        return $list;
    }

    public function getDelete($idno=null)
    {
        $EditDelete = array(
            'delete' => 'Y',
            'msg' => '',
        );

        $query = T09tb::select('idno');
        $data1 = $query->where('idno', $idno)->get()->toArray();

        if(!empty($data1)){
            $EditDelete['delete'] = 'N';
            $EditDelete['msg'] = '資料已存在講座聘任資料(t09tb)中！';
        }

        $query = T11tb::select('idno');
        $data2 = $query->where('idno', $idno)->get()->toArray();

        if(!empty($data2)){
            $EditDelete['delete'] = 'N';
            $EditDelete['msg'] .= '資料已存在轉帳明細檔(t11tb)中！';
        }

        $query = T12tb::select('idno');
        $data3 = $query->where('idno', $idno)->get()->toArray();

        if(!empty($data3)){
            $EditDelete['delete'] = 'N';
            $EditDelete['msg'] .= '資料已存在所得稅明細檔(t12tb)中！';
        }

        $query = M08tb::select('idno');
        $data4 = $query->where('idno', $idno)->get()->toArray();

        if(!empty($data4)){
            $EditDelete['delete'] = 'N';
            $EditDelete['msg'] .= '資料已存在教材基本資料(m08tb)中！';
        }

        $query = M16tb::select('idno');
        $data5 = $query->where('idno', $idno)->get()->toArray();

        if(!empty($data5)){
            $EditDelete['delete'] = 'N';
            $EditDelete['msg'] .= '資料已存在講座專長資料(m16tb)中！';
        }
        return $EditDelete;
    }
}
