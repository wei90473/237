<?php
namespace App\Repositories;
use Exception;
use DB;

class Repository{
    protected $model;
    public function insert($row)
    {
        DB::connection()->enableQueryLog();

        $insert = $this->model->create($row);
        $sql = DB::getQueryLog();

        DB::connection()->disableQueryLog();
        
        if (checkNeedModifyLog()){
            createModifyLog('I', $this->model->getTable(), null, $row, end($sql));
        }

        return $insert;
    }

    public function delete($params)
    {
        if (!empty($params)){
            if(is_array($params)){
                $model = $this->model->where($params);
            }else{
                $model = $this->model->find($params);
            }
            
            if (checkNeedModifyLog()){
                 DB::connection()->enableQueryLog();
                $beforeData = $model->get();
            }
            
            $model->delete();

            if (checkNeedModifyLog()){
                $sql = DB::getQueryLog();
                DB::connection()->disableQueryLog();                
                createModifyLog('D', $this->model->getTable(), $beforeData->toArray(), null, end($sql));
            }
        }else{
            throw new Exception('delete error');
        }
    }

    public function update($params, $data)
    {

        if (!empty($params)){
            $whereIn = (!empty($params['whereIn'])) ? $params['whereIn'] : null;
            if (!empty($whereIn)){
                unset($params['whereIn']);
            }

            $model = $this->model->where($params);

            if (!empty($whereIn)){
                $model->whereIn($whereIn['field'], $whereIn['data']);
            }
            
            if (checkNeedModifyLog()){
                $beforeDatas = $model->get();
                DB::connection()->enableQueryLog();
            }

            $update = $model->update($data);

            if (checkNeedModifyLog()){
                $sql = DB::getQueryLog();
                DB::connection()->disableQueryLog();                
                foreach ($beforeDatas as $beforeData){
                    $afterData = clone $beforeData;
                    foreach ($data as $key => $value){
                        if (!empty($afterData->$key)){
                            $afterData->$key = $value;
                        }
                    } 

                    createModifyLog('U', $this->model->getTable(), $beforeData->toArray(), $afterData->toArray(), end($sql));               
                }
            }

            return $update;
        }else{
            throw new Exception('update error');
        }
    }

    public function find($params, $select = "*")
    {
        $model = $this->model->select($select);
        
        if (!empty($with)){
            $model->with($with);
        }
        
        if (is_array($params)){
            return $model->where($params)->first();
        }else{
            return $this->model->find($params);
        }
    }

    public function getData($params = [], $select = '*', $with = [], $return_type = 'data')
    {
        $model = $this->model->selectRaw($select);

        if(isset($params['whereIn'])){
            $model->whereIn($params['whereIn']['field'], $params['whereIn']['data']);
            unset($params['whereIn']);
        }

        // if(isset($params['whereNotIn'])){
        //     $model->whereNotIn($params['whereNotIn']['field'], $params['whereNotIn']['data']);
        //     unset($params['whereNotIn']);
        // }

        if (!empty($params)){
            $model->where($params);
        }

        if (!empty($with)){
            $model->with($with);
        }        
        
        if ($return_type == 'count'){
            return $model->count();
        }else{
            return $model->get();            
        }
    }   

    public function updateOrCreate($pk, $data)
    {
        if (!empty($pk)){
            $model = $this->model->where($pk)->get();
            
            if ($model->isEmpty()){
                $data = array_merge($pk, $data);
                return $this->insert($data);
            }else{
                return $this->update($pk, $data);
            }            
        }
    }

    public function getByKeys($key_name, $keys, $other_condition = null)
    {
        $model = $this->model->whereIn($key_name, $keys);

        if ($other_condition !== null){
            $model->where($other_condition);
        }

        return $model->get();                      
    }

    public function deleteByKeys($key_name, $keys)
    {
        return $this->model->whereIn($key_name, $keys)
                           ->delete();
    }

    public function queryField($model, $fields, $queryData)
    {

        $other_like = (isset($fields['other_like'])) ? $fields['other_like'] : [];

        foreach ($other_like as $table => $like){
            foreach ($like as $field){
                if (!empty($queryData[$field])){
                    $model->where($table.'.'.$field, 'LIKE', "%{$queryData[$field]}%");
                }
            }
        }

        $other_equal = (isset($fields['other_equal'])) ? $fields['other_equal'] : [];

        foreach ($other_equal as $table => $equal){
            foreach ($equal as $field){
                if (!empty($queryData[$field])){
                    $model->where($table.'.'.$field, '=', $queryData[$field]);
                }
            }
        }

        $equal = (isset($fields['equal'])) ? $fields['equal'] : [];

        foreach ($equal as $field){
            if (!empty($queryData[$field])){
                $model->where($field, '=', $queryData[$field]);
            }
        }

        $like = (isset($fields['like'])) ? $fields['like'] : [];

        foreach ($like as $field){
            if (!empty($queryData[$field])){
                $model->where($field, 'LIKE', '%'.$queryData[$field].'%');
            }
        }

        $YN = (isset($fields['YN'])) ? $fields['YN'] : [];

        foreach ($YN as $field){
            if (!empty($queryData[$field])){
                $model->where($field, '=', 'Y');
            }
        }


        $other_not_in = (isset($fields['other_not_in'])) ? $fields['other_not_in'] : [];

        foreach ($other_not_in as $table => $not_in){
            foreach ($not_in as $field){
                if (!empty($queryData[$field])){
                    $model->whereNotIn($table.'.'.$field, $queryData[$field]);
                }
            }
        }               
        /*
        $other_period = (isset($fields['other_period'])) ? $fields['other_period'] : [];

        foreach ($other_period as $table => $period){
            foreach ($period as $field => $field_se){
                if(!empty($queryData[$field])){
                    // $queryData['train_start_date'] = str_pad($queryData['train_start_date'], 7, '0', STR_PAD_LEFT);
                    $model->where($table.'.'.$field, '>=', $queryData[$field_se[0]])
                          ->where($field.'.'.$field, '<=', $queryData[$field_se[1]]);
                }
            }
        }

        $period = (isset($fields['period'])) ? $fields['period'] : [];

        foreach ($period as $field){
            if(!empty($queryData[$field])){
                // $queryData['train_start_date'] = str_pad($queryData['train_start_date'], 7, '0', STR_PAD_LEFT);
                $model->where($field, '>=', $queryData[$field[0]])
                      ->where($field, '<=', $queryData[$field[1]]);
            }
        }

        */

        return $model;
    }
    
    public function all(){
        return $this->model->get();
    }


    /*
        列表查詢(新)
    */
    public function queryForList($model, $fields, $queryData)
    {
        
        $likes = (isset($fields['likes'])) ? $fields['likes'] : [];

        foreach ($likes as $table => $like){
            foreach ($like as $field){
                if (isset($queryData[$table][$field]) && $queryData[$table][$field] !== ""){
                    $model->where($table.'.'.$field, 'LIKE', "%{$queryData[$table][$field]}%");
                }
            }
        }

        $equals = (isset($fields['equals'])) ? $fields['equals'] : [];

        foreach ($equals as $table => $equal){
            foreach ($equal as $field){
                if (isset($queryData[$table][$field]) && $queryData[$table][$field] !== ""){
                    $model->where($table.'.'.$field, '=', $queryData[$table][$field]);
                }
            }
        }

        $not_equals = (isset($fields['not_equals'])) ? $fields['not_equals'] : [];

        foreach ($not_equals as $table => $not_equal){
            foreach ($not_equal as $field){
                if (isset($queryData[$table][$field]) && $queryData[$table][$field] !== ""){
                    $model->where($table.'.'.$field, '=', $queryData[$table][$field]);
                }
            }
        }

        $not_ins = (isset($fields['not_in'])) ? $fields['not_in'] : [];

        foreach ($not_ins as $table => $not_in){
            foreach ($not_in as $field){
                if (isset($queryData[$table][$field]) && $queryData[$table][$field] !== ""){
                    $model->whereNotIn($table.'.'.$field, $queryData[$table][$field]);
                }
            }
        }               

        $ins = (isset($fields['ins'])) ? $fields['ins'] : [];

        foreach ($ins as $table => $in){
            foreach ($in as $field){
                if (isset($queryData[$table][$field]) && $queryData[$table][$field] !== ""){
                    $model->whereNotIn($table.'.'.$field, $queryData[$table][$field]);
                }
            }
        } 

        return $model;        
    }

    public function execWithModifyLog($query, $action, $data = null)
    {
        if (checkNeedModifyLog()){
            $beforeDatas = $query->selectRaw($this->model->getTable().'.*')->get();
            $sql = DB::getQueryLog();
            DB::connection()->disableQueryLog();   
        }

        if ($action == "delete"){
            
            $query->delete();

            if (checkNeedModifyLog()){
                $sql = DB::getQueryLog();
                DB::connection()->disableQueryLog(); 

                foreach ($beforeDatas as $beforeData){               
                    createModifyLog('D', $this->model->getTable(), $beforeData->toArray(), null, end($sql));
                }
            }

        }elseif ($action == "update" && !empty($data)){

            $query->update($data);

            if (checkNeedModifyLog()){
                $sql = DB::getQueryLog();
                DB::connection()->disableQueryLog();                
                foreach ($beforeDatas as $beforeData){
                    $afterData = clone $beforeData;
                    foreach ($data as $key => $value){
                        if (!empty($afterData->$key)){
                            $afterData->$key = $value;
                        }
                    } 

                    createModifyLog('U', $this->model->getTable(), $beforeData->toArray(), $afterData->toArray(), end($sql));               
                }
            }

        }
    }
}