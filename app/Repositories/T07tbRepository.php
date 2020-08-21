<?php
namespace App\Repositories;

use App\Models\T07tb;
use App\Repositories\Repository;
use DateTime;
use DB;

class T07tbRepository extends Repository
{
    public function __construct(T07tb $t07tb)
    {
        $this->model = $t07tb;
    }  

    public function get($queryData)
    {
        $model = $this->model->select([
            't07tb.*',
            't01tb.name as t01tb_name',
            't01tb.class as t01tb_class',
            't01tb.branch as t01tb_branch',
            't01tb.branchname as t01tb_branchname',
            't01tb.process as t01tb_process',
            't04tb.term as t04tb_term',
            't04tb.quota as t04tb_quota',
            't04tb.site as t04tb_site',
            't04tb.sdate as t04tb_sdate',
            't04tb.edate as t04tb_edate',
            'm09tb.username as m09tb_usename'
        ]);
        $model->join('t04tb', function($join){
            $join->on('t04tb.class', '=', 't07tb.class')
                 ->on('t04tb.term', '=', 't07tb.term');
        });

        $model->join('t01tb', function($join){
            $join->on('t01tb.class', '=', 't04tb.class');
        });

        $model->leftJoin('m09tb', function($join){
            $join->on('m09tb.userid', '=', 't04tb.sponsor');
        });

        $fields = [
            'other_like' => [
                't01tb' => ['name', 'branchname'],
                't04tb' => ['term']
            ],
            'other_equal' => [
                't01tb' => ['yerly', 'branch', 'traintype', 'type'],
                't04tb' => ['class', 'site_branch']
            ],
        ];

        if(!empty($queryData['train_start_date'])){
            $queryData['train_start_date'] = str_pad($queryData['train_start_date'], 7, '0', STR_PAD_LEFT);
            $model->where('sdate', '>=', $queryData['train_start_date'])
                  ->where('sdate', '<=', $queryData['train_end_date']);
        }

        if(!empty($queryData['graduate_start_date'])){
            $queryData['graduate_start_date'] = str_pad($queryData['graduate_start_date'], 7, '0', STR_PAD_LEFT);
            $model->where('edate', '>=', $queryData['graduate_start_date'])
                  ->where('edate', '<=', $queryData['graduate_end_date']);
        }

        if(!empty($queryData['training_start_date'])){
            $queryData['training_start_date'] = str_pad($queryData['training_start_date'], 7, '0', STR_PAD_LEFT);

            $model->where(function($query) use($queryData){
                $query->where(function($query) use($queryData){
                    $query->where('sdate', '>=', $queryData['training_start_date'])
                          ->where('sdate', '<=', $queryData['training_end_date']);
                });                               
                $query->orWhere(function($query) use($queryData){
                    $query->where('edate', '>=', $queryData['training_start_date'])
                          ->where('edate', '<=', $queryData['training_end_date']);       
                });      
            });

        }

        $model = $this->queryField($model, $fields, $queryData);

        return $model->paginate(10);
    }

    public function getByInAndType($infos, $type)
    {
        $model = $this->model->select("*");
        $model->where('type', '=', $type);
        $model->where(function ($query) use($infos){
            foreach($infos as $info){
                $query->orWhere($info);
            }
        });

        return $model->get(); 
    } 
    
    public function getByQueryList($queryData)
    {
        $select = [
            't07tb.*',
            't01tb.name as t01tb_name',
            't01tb.class as t01tb_class',
            't01tb.branch as t01tb_branch',
            't01tb.branchname as t01tb_branchname',
            't01tb.process as t01tb_process',
            't04tb.term as t04tb_term',
            't04tb.quota as t04tb_quota',
            't04tb.site as t04tb_site',
            't04tb.sdate as t04tb_sdate',
            't04tb.edate as t04tb_edate',
            'm09tb.username as m09tb_usename'
        ];

        $model = $this->model->select($select)
                             ->join('t04tb', function($query){
                                 $query->on('t04tb.class', '=', 't07tb.class')
                                       ->on('t04tb.term', '=', 't07tb.term');
                             })        
                             ->join('t01tb', 't01tb.class', '=', 't04tb.class')
                             ->leftJoin('m09tb', 'm09tb.userid', '=', 't04tb.sponsor');
        $likes = [
            't01tb' => ['class', 'name', 'commission']
        ];

        $equals = [
            't01tb' => ['branch', 'site_branch', 'process', 'categoryone', 'traintype', 'type', 'categoryone'],
            't04tb' => ['term', 'sponsor'],
        ];

        $fields = compact(['likes', 'equals']);

        $model = $this->queryForList($model, $fields, $queryData);

        if (!empty($queryData['t01tb']['yerly'])){
            $queryData['t01tb']['yerly'] = str_pad($queryData['t01tb']['yerly'], 3, '0', STR_PAD_LEFT);            
            $model->where('t01tb.yerly', '=', $queryData['t01tb']['yerly']);
        }

        if (!empty($queryData['t04tb']['month'])){
            $monthDate = str_pad($queryData['t01tb']['yerly'], 3, '0', STR_PAD_LEFT) + 1911;
            $monthDate .= str_pad($queryData['t04tb']['month'], 2, '0', STR_PAD_LEFT);
            $monthDate .= '01';

            $model->where(function($query) use($monthDate){
                $monthSDate = new DateTime($monthDate);
                $monthSDate = ($monthSDate->format('Y')-1911).$monthSDate->format('md');
                $monthEDate = new DateTime($monthDate);
                $monthEDate->modify('+1 month -1 day');
                $monthEDate = ($monthEDate->format('Y')-1911).$monthEDate->format('md');
                
                $query->where(function($query) use($monthSDate, $monthEDate){
                    $query->where('sdate', '>=', $monthSDate)
                          ->where('sdate', '<=', $monthEDate);
                });

                $query->orWhere(function($query) use($monthSDate, $monthEDate){
                    $query->where('edate', '>=', $monthSDate)
                          ->where('edate', '<=', $monthEDate);
                });
            });            
        }

        if (!empty($queryData['sdate_start']) && !empty($queryData['sdate_end'])){
            $model->where(function($query) use($queryData){
                $query->where('sdate', '>=', $queryData['sdate_start'])
                      ->where('sdate', '<=', $queryData['sdate_end']);
            });
        }

        if (!empty($queryData['edate_start']) && !empty($queryData['edate_end'])){
            $model->where(function($query) use($queryData){
                $query->where('edate', '>=', $queryData['edate_start'])
                      ->where('edate', '<=', $queryData['edate_end']);
            });
        }
        
        if (!empty($queryData['training_start']) && !empty($queryData['training_end'])){
            $model->whereExists(function($query) use($queryData){
                $query->select('*')
                      ->from('t06tb')
                      ->whereRaw('t04tb.class = t06tb.class AND t04tb.term = t06tb.term')
                      ->where('t06tb.date', '>=', $queryData['training_start'])
                      ->where('t06tb.date', '<=', $queryData['training_end']);
            });

        }        
        // 是否洽借班期
        if (isset($queryData['is_type13'])){
            if ($queryData['is_type13']){
                $model->where('type', '=', 13);
            }else{
                $model->where('type', '<>', 13);
            }
        }

        $queryData['_paginate_qty'] = (isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'] !== "") ? $queryData['_paginate_qty'] : 10;

        return $model->paginate($queryData['_paginate_qty']);        
    }

    public function getByT04tbSEdate($sdate, $edate)
    {
        $t07tbs = $this->model->join('t04tb', function($join){
            $join->on('t04tb.class', '=', 't07tb.class')
                 ->on('t04tb.term', '=', 't07tb.term');
        })->select('t07tb.*');

        $t07tbs->where(function($query) use($sdate, $edate){
            $query->where(function($query1) use($sdate, $edate){
                $query1->where('sdate', '>=', $sdate)
                       ->where('sdate', '<=', $edate);
            });
            $query->where(function($query2) use($sdate, $edate){
                $query2->where('edate', '>=', $sdate)
                       ->where('edate', '<=', $edate);
            });            
        });

        return $t07tbs->get();
    }
}