<?php

namespace App\Presenters;

use Session;
use App\Models\S01tb;
use App\Models\Classes;
use App\Models\DemandDistribution;
use App\Models\T01tb;
use App\Models\S02tb;
use DB;


class BasePresenter
{
    /**
     * 取得日期(民國格式)
     *
     * @param string $date
     * @return array
     */
    function getDateData($date = '')
    {
        $result = array();

        if ($date && $date != 'NaN-NaN-NaN') {
            $result['year'] = str_pad(date('Y', strtotime($date)) - 1911, 3, '0', STR_PAD_LEFT);
            $result['month'] = str_pad(date('m', strtotime($date)), 2, '0', STR_PAD_LEFT);
            $result['day'] = str_pad(date('d', strtotime($date)), 2, '0', STR_PAD_LEFT);
        } else {

            $result['year'] = '';
            $result['month'] = '';
            $result['day'] = '';
        }

        return $result;
    }

    /**
     * 取得資料庫列表
     *
     * @param $model
     * @param bool $select
     * @return mixed
     */
    public function getDBList($model, $select = false)
    {
        $model = 'App\Models\\'.$model;

        $model = new $model;

        if ($select) {

            $model = $model->select($select);
        }

        return $model->get();
    }

    /**
     * 取得未被用過的班別
     *
     * @param $classesId
     * @return mixed
     */
    public function getDemandSurveyClasses($yerly, $times)
    {
        $query = T01tb::select('class', 'name', 'yerly', 'times');

        $query->where(function ($query) use ($yerly, $times) {

            $query->where('yerly', '');

            $query->orwhere(function ($query) use ($yerly, $times) {

                $query->where('yerly', $yerly);
                $query->where('times', $times);
            });

        });

        return $query->get();
    }

    /**
     * 取得系統代號
     *
     * @param $type
     * @return array
     */
    public function getSystemCode($type)
    {
        $data = S01tb::where('type', $type)->get();

        $result = array();

        foreach ($data as $va) {

            $result[$va->code]['type'] = $va->type;
            $result[$va->code]['code'] = $va->code;
            $result[$va->code]['name'] = $va->name;
            $result[$va->code]['fee'] = $va->fee;
            $result[$va->code]['serno'] = $va->serno;
            $result[$va->code]['category'] = $va->category;
        }

        return $result;
    }

    /**
     * 取得班別編號流水號
     *
     * @return int|string
     */
    public function getMaxClass()
    {
        $data = T01tb::where('type', '!=', '13')->max('class');

        return isset($data)? mb_substr($data, 3, 3) + 1 : 1;
    }

    /**
     * 執行撈取的sql
     *
     * @param $sql
     * @return mixed
     */
    public function SQL($sql)
    {
        $result = DB::select($sql);

        return $result;
    }

    /**
     * 將民國日期加入斜線
     *
     * @param $date
     * @return string
     */
    public function showDate($date)
    {
        return ($date)? mb_substr($date, 0, 3).'/'.mb_substr($date, 3, 2).'/'.mb_substr($date, 5, 2) : '';
    }

    /**
     * 取得系統參數
     *
     * @param $field
     * @return mixed
     */
    public function getSystemParameter($field)
    {
        $data = S02tb::select($field)->first();

        return $data[$field];
    }

    /**
     * 取得火車列表
     *
     * @return mixed
     */
    public function getFeeList()
    {
        $result = S01tb::where('type', 'E')->get();

        return $result;
    }
}
