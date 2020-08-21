<?php
namespace App\Repositories;

use App\Models\T04tb;
use App\Repositories\Repository;
use DateTime;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;



class T04tbRepository extends Repository
{
    public function __construct(T04tb $t04tb)
    {
        $this->model = $t04tb;
    }

    function get($queryData, $paginate = true, $select = '*', $otherData = []){
        $t04tbs = $this->model->select("*")->with(['m09tb']);

        if (is_array($select)){
            if(!empty($select['t04tb'])){
                $t04tbs->select($select['t04tb']);
            }

            if(!empty($select['t01tb'])){
                $t04tbs->with(['t01tb' => function($wq) use($select){
                    $wq->select($select['t01tb']);
                }]);
            }
        }else{
            $t04tbs->select($select)->with(['t01tb']);
        }

        $t01tb_field = [
            'class_name', 'branch', 'branchname', 'commission', 'process', 'type'
        ];

        $queryData = array_filter($queryData);

        $exist_t01tb_field = array_diff($t01tb_field, array_keys($queryData));
        $exist_t01tb_field = (count($exist_t01tb_field) != count($t01tb_field));

        $t04tbs->whereHas('t01tb', function ($t01tb_query) use($queryData){

            if (isset($queryData['class_name'])){
                $t01tb_query->where('name', 'LIKE', "%{$queryData['class_name']}%");
            }

            if (isset($queryData['branch'])){
                $t01tb_query->where('branch', '=', $queryData['branch']);
            }

            if (isset($queryData['branchname'])){
                $t01tb_query->where('branchname', '=', $queryData['branchname']);
            }

            if (isset($queryData['process'])){
                $t01tb_query->where('process', '=', $queryData['process']);
            }

            if (isset($queryData['type'])){
                $t01tb_query->where('type', '=', $queryData['type']);
            }

            // if (isset($queryData['notType'])){
            //     $t01tb_query->where('type', '<>', $queryData['notType']);
            // }

        });

        if (isset($queryData['yerly'])){
            $queryData['yerly'] = str_pad($queryData['yerly'], 3, '0', STR_PAD_LEFT);
            $t04tbs->where('class', 'LIKE', "{$queryData['yerly']}%");
        }

        if (isset($queryData['class'])){
            $t04tbs->where('class', 'LIKE', "%{$queryData['class']}%");
        }

        if (isset($queryData['term'])){
            $queryData['term'] = str_pad($queryData['term'], 2, '0', STR_PAD_LEFT);
            $t04tbs->where('term', '=', $queryData['term']);
        }

        if (isset($queryData['entrust_train_unit'])){
            $t04tbs->where('client', 'LIKE', "%{$queryData['entrust_train_unit']}%");
        }

        if(isset($queryData['train_start_date'])){
            $queryData['train_start_date'] = str_pad($queryData['train_start_date'], 7, '0', STR_PAD_LEFT);
            $t04tbs->where('sdate', '>=', $queryData['train_start_date'])
                   ->where('sdate', '<=', $queryData['train_end_date']);
        }

        if(isset($queryData['graduate_start_date'])){
            $queryData['graduate_start_date'] = str_pad($queryData['graduate_start_date'], 7, '0', STR_PAD_LEFT);
            $t04tbs->where('edate', '>=', $queryData['graduate_start_date'])
                   ->where('edate', '<=', $queryData['graduate_end_date']);
        }

        if(isset($queryData['training_start_date'])){
            $queryData['training_start_date'] = str_pad($queryData['training_start_date'], 7, '0', STR_PAD_LEFT);

            $t04tbs->where(function($query) use($queryData){
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

        if (isset($otherData['t13tbNum'])){
            $t13tbNumSql = "SELECT class as groupClass, term as groupTerm, COUNT(*) as t13tbNum
                            FROM t13tb
                            GROUP BY class, term";
            $t04tbs->leftJoin(DB::raw("($t13tbNumSql) as t13tbNumGroup"), function($join){
                $join->on('t13tbNumGroup.groupClass', '=', 't04tb.class')
                     ->on('t13tbNumGroup.groupTerm', '=', 't04tb.term');
            });
        }

        if (isset($otherData['t39tbNum'])){
            $t39tbNumSql = "SELECT class as groupClass, term as groupTerm, COUNT(*) as t39tbNum
                            FROM t39tb
                            GROUP BY class, term";
            $t04tbs->leftJoin(DB::raw("($t39tbNumSql) as t39tbNumGroup"), function($join){
                $join->on('t39tbNumGroup.groupClass', '=', 't04tb.class')
                     ->on('t39tbNumGroup.groupTerm', '=', 't04tb.term');
            });
        }

        $t04tbs->orderBy('class', 'desc')->orderBy('term', 'desc');

        if ($paginate){
            $paginate_qty = (isset($queryData['_paginate_qty']) && $queryData['_paginate_qty']) ? $queryData['_paginate_qty'] : 10;
            $data = $t04tbs->paginate($paginate_qty);
        }else{
            $data = $t04tbs->get();
        }

        return $data;
    }

    function _find($class, $term)
    {
        return $this->model->where('class', '=', $class)->with(['t01tb'])
                        ->where('term', '=', $term)
                        ->first();
    }

    function _update($class, $term, $t04tb)
    {
        return $this->model->where("class", "=", $class)
                           ->where("term", "=", $term)
                           ->update($t04tb);
    }

    function getByYerlyCount($yerly, $is_type13 = false)
    {
        $model = $this->model->join('t01tb', 't01tb.class', '=', 't04tb.class')
                             ->where('t04tb.class', 'LIKE', "{$yerly}%");

        if ($is_type13 == false){
            $model->where('t01tb.type', '<>', 13);
        }

        return $model->count();
    }

    function insertFromT03tb($yerly, $is_type13 = false)
    {
        $type13Sql = "";
        if ($is_type13){
            $type13Sql .= " AND t01tb.type <> 13";
        }

        $sql = "INSERT INTO t04tb(class, term, quota)
                SELECT t03tb.class, t03tb.term, SUM(t03tb.quota)
                FROM t03tb
                JOIN t01tb ON t01tb.class = t03tb.class
                WHERE t01tb.yerly = ? $is_type13
                GROUP BY t03tb.class, t03tb.term";

        DB::insert($sql, [$yerly]);

        $t47tbFiledValue = [
            "t03tb.class" => null,
            "t03tb.term" => null,
            "degree" => 6,
            "enroll" => "3",
            "credit" => 't01tb.trainhour',
            "unit" => "1",
            "lodging" => "0",
            "meal" => "0",
            "upload2" => "'N'",
            "grade" => "'N'",
            "`leave`" => "'N'",
        ];

        $t47tbFiledValue = array_map(function($value, $filed){
            if (!isset($value)){
                return $filed;
            }

            return $value.' as '.$filed;
        }, $t47tbFiledValue, array_keys($t47tbFiledValue));

        $t47tbFiledValue = join(', ', $t47tbFiledValue);

        $t47tbFiled = [
            "class",
            "term",
            "degree",
            "enroll",
            "credit",
            "unit",
            "lodging",
            "meal",
            "upload2",
            "grade",
            "`leave`",
        ];
        $t47tbFiled = join(', ', $t47tbFiled);

        $sql = "INSERT INTO t47tb({$t47tbFiled})
                SELECT {$t47tbFiledValue}
                FROM (
                    SELECT t03tb.class, t03tb.term
                    FROM t03tb
                    JOIN t01tb ON t01tb.class = t03tb.class
                    WHERE t01tb.yerly = ? $is_type13
                    GROUP BY t03tb.class, t03tb.term
                ) as t03tb
                JOIN t01tb ON t01tb.class = t03tb.class";

        DB::insert($sql, [$yerly]);
    }

    function deleteByYerly($yerly, $is_type13 = false)
    {
        $model = $this->model->join('t01tb', 't01tb.class', '=', 't04tb.class')
                             ->where('t01tb.yerly', '=', $yerly);

        $type13Sql = "";
        if ($is_type13 == false){
            $type13Sql .= " AND t01tb.type <> 13";
            $model->where('t01tb.type', '<>', 13);
        }

        $this->execWithModifyLog($model, 'delete');

        DB::delete("DELETE t47tb
                   FROM t47tb
                   JOIN t01tb ON t01tb.class = t47tb.class
                   WHERE t01tb.yerly = ? {$type13Sql}", [$yerly]);

    }

    public function getRepeatApplyForSdate($queryData)
    {
        // return $this->model->selectRaw("t13tb.idno, RTRIM(m02tb.cname), t04tb.class, t04tb.term, RTRIM(t01tb.name), t04tb.sdate")
        //                    ->join('t01tb', 't01tb.class', '=', 't04tb.class')
        //                    ->join('t13tb', function($join){
        //                        $join->on('t13tb.class', '=', 't04tb.class')
        //                             ->on('t13tb.term', '=', 't04tb.term')
        //                             ->on('t13tb.status', '<>', DB::raw('2'));
        //                    })
        //                    ->join('m02tb', 'm02tb.idno', '=', 't13tb.idno')
        //                    ->join('t27tb', function($join) use($queryData){
        //                        $join->on('t27tb.class', '=', DB::raw($queryData['class']))
        //                             ->on('t27tb.term', '=', DB::raw($queryData['term']))
        //                             ->where(function($query){
        //                                 $query->where(function($query2){
        //                                             $query2->where('t27tb.loginid', '<>', '')
        //                                                    ->where('t27tb.progress', '=', '0');
        //                                          })
        //                                          ->orWhere(function($query3){
        //                                             $query3->where('t27tb.loginid', '=', '')
        //                                                    ->where('t27tb.progress', '=', '');
        //                                          });
        //                             })
        //                             ->where('t27tb.idno', '=', 't13tb.idno');
        //                    })
        //                    ->whereBetween('t04tb.sdate',['1080320', '1080620'])
        //                    ->where(function($query4) use($queryData){
        //                        $query4->where('t04tb.class', '<>', $queryData['class'])
        //                               ->where('t04tb.term', '<>', $queryData['term']);
        //                    })
        //                    ->orderBy('t13tb.idno','t13tb.class','t13tb.term')
        //                    ->get();


        return DB::select("SELECT
                                C.idno,
                                RTRIM(D.cname) name,
                                A.class,
                                A.term,
                                RTRIM(B.name) class_name,
                                A.sdate
                            FROM t04tb A
                            INNER JOIN t01tb B
                                    ON A.class = B.class
                            INNER JOIN t13tb C
                                    ON A.class = C.class
                                    AND A.term = C.term
                                    AND C.status <> '2'
                            INNER JOIN m02tb D
                                    ON C.idno = D.idno
                            INNER JOIN t27tb ON t27tb.class = ?
                                    AND t27tb.term = ?
                                    AND ((t27tb.loginid <> '' AND t27tb.progress = '0') OR (t27tb.loginid = ''AND t27tb.progress = ''))
                                    AND t27tb.idno = C.idno
                            WHERE A.sdate BETWEEN ? AND ?
                                    AND NOT (A.class = ? AND A.term = ?)
                                    ORDER BY C.idno,C.class,C.term ",
                            [
                                $queryData['class'], $queryData['term'], $queryData['sdate_start'], $queryData['sdate_end'], $queryData['class'], $queryData['term']
                            ]);
    }

    public function checkStayDate($class,$term)
    {
        return DB::select("SELECT
                                sdate,edate
                            FROM t04tb
                            WHERE
                                class = ? AND term = ?
                            AND (staystartdate is null or stayenddate is null or staystarttime is null or stayendtime is null)",
                            [$class, $term]);
    }

    public function getT04tbAndModifyinfo($t04tb_info)
    {
        $t04tb = $this->model->where($t04tb_info)->first();
        if (!empty($t04tb)){
            $t04tb->change_studnet_modify_logs = $t04tb->applyModifyLogForAmdin()
                                                       ->where(['modify_type' => 1])
                                                       ->with(['m02tb', 'new_m02tb'])->get();

            $t04tb->change_term_modify_logs = $t04tb->applyModifyLogForAmdin()
                                                    ->where(['modify_type' => 2])
                                                    ->with(['m02tb', 'new_m02tb'])->get();
        }

        return $t04tb;
    }

    public function getByIn($t04tb_infos)
    {
        $model = $this->model->select("*")->with(['t01tb']);

        foreach($t04tb_infos as $t04tb_info){
            $model->orWhere($t04tb_info);
        }

        return $model->get();
    }

    public function _getByIn($classes)
    {
        $model = $this->model->select("*");
        foreach ($classes as $class => $terms){
            $model->orWhere(function($query) use($class, $terms){
                $query->where('class', '=', $class)
                      ->WhereIn('term', $terms);
            });
        }
        return $model->get();
    }

    public function getByQueryList($queryData, $select=null)
    {
        if ($select == null){
            $select = [
                't04tb.*',
                't01tb.name as t01tb_name',
                'm09tb.username as m09tb_username',
                't01tb.branch'
            ];
        }

        $model = $this->model->select($select)
                             ->join('t01tb', 't01tb.class', '=', 't04tb.class')
                             ->leftJoin('m09tb', 'm09tb.userid', '=', 't04tb.sponsor');
        $likes = [
            't01tb' => ['class', 'name', 'commission']
        ];

        $equals = [
            't01tb' => ['branch', 'site_branch', 'process', 'categoryone', 'traintype', 'type'],
            't04tb' => ['term', 'sponsor'],
        ];

        $fields = compact(['likes', 'equals']);

        $queryData['t04tb']['term'] = (!empty($queryData['t04tb']['term'])) ? str_pad($queryData['t04tb']['term'], 2, '0', STR_PAD_LEFT) : null;

        $model = $this->queryForList($model, $fields, $queryData);

        if (!empty($queryData['t01tb']['yerly'])){
            $queryData['t01tb']['yerly'] = str_pad($queryData['t01tb']['yerly'], 3, '0', STR_PAD_LEFT);
            $model->where('t01tb.yerly', '=', $queryData['t01tb']['yerly']);
        }

        if (isset($queryData['t13tbNum'])){
            $t13tbNumSql = "SELECT class as groupClass, term as groupTerm, COUNT(*) as t13tbNum
                            FROM t13tb
                            GROUP BY class, term";
            $model->leftJoin(DB::raw("($t13tbNumSql) as t13tbNumGroup"), function($join){
                $join->on('t13tbNumGroup.groupClass', '=', 't04tb.class')
                     ->on('t13tbNumGroup.groupTerm', '=', 't04tb.term');
            });
        }

        if (isset($queryData['t39tbNum'])){
            $t13tbNumSql = "SELECT class as groupClass, term as groupTerm, COUNT(*) as t39tbNum
                            FROM t39tb
                            GROUP BY class, term";
            $model->leftJoin(DB::raw("($t13tbNumSql) as t39tbNumGroup"), function($join){
                $join->on('t39tbNumGroup.groupClass', '=', 't04tb.class')
                     ->on('t39tbNumGroup.groupTerm', '=', 't04tb.term');
            });
        }

        if (isset($queryData['special_class_fee'])){
            $model->leftJoin('special_class_fee', function($join){
                $join->on('t04tb.class', '=', 'special_class_fee.class')
                     ->on('t04tb.term', '=', 'special_class_fee.term');
            });
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

        if (!empty($queryData['sdate_start']) || !empty($queryData['sdate_end'])){
            $model->where(function($query) use($queryData){
                if (!empty($queryData['sdate_start'])){
                    $query->where('sdate', '>=', $queryData['sdate_start']);
                }
                if (!empty($queryData['sdate_end'])){
                    $query->where('sdate', '<=', $queryData['sdate_end']);
                }
            });
        }

        if (!empty($queryData['edate_start']) || !empty($queryData['edate_end'])){
            $model->where(function($query) use($queryData){
                if (!empty($queryData['edate_start'])){
                    $query->where('edate', '>=', $queryData['edate_start']);
                }
                if (!empty($queryData['edate_end'])){
                    $query->where('edate', '<=', $queryData['edate_end']);
                }
            });
        }

        if (!empty($queryData['training_start']) || !empty($queryData['training_end'])){
            // $model->where(function($query) use($queryData){
            //     $query->where(function($query1) use($queryData){
            //         $query1->where('sdate', '>=', $queryData['training_start'])
            //                ->where('sdate', '<=', $queryData['training_end']);
            //     });
            //     $query->orWhere(function($query2) use($queryData){
            //         $query2->where('edate', '>=', $queryData['training_start'])
            //                ->where('edate', '<=', $queryData['training_end']);
            //     });
            // });

            $model->whereExists(function($query) use($queryData){
                $query->select('*')
                      ->from('t06tb')
                      ->whereRaw('t04tb.class = t06tb.class AND t04tb.term = t06tb.term');

                if (!empty($queryData['training_start'])){
                    $query->where('t06tb.date', '>=', $queryData['training_start']);
                }

                if (!empty($queryData['training_end'])){
                    $query->where('t06tb.date', '<=', $queryData['training_end']);
                }
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

    public function getByT04tbs($t04tb_keys)
    {
        $model = $this->model->select(["*"]);

        foreach ($t04tb_keys as $class => $terms){
            foreach ($terms as $term){
                $model->orWhere(function($query) use($class, $term){
                    $query->where(compact(['class', 'term']));
                });
            }
        }

        return $model->get();
    }

    public function getListForRoomSet($queryData)
    {
        $model = $this->model->selectRaw('t04tb.class,
                                          t01tb.yerly,
                                          t04tb.client,
                                          t01tb.name,
                                          t01tb.branchname,
                                          t04tb.term,
                                          t01tb.process,
                                          t04tb.sdate,
                                          t04tb.edate,
                                          t04tb.staystartdate,
                                          t04tb.stayenddate,
                                          t04tb.staystarttime,
                                          t04tb.stayendtime,
                                          t04tb.auto,
                                          m09tb.username')
                            ->Join('t01tb', 't04tb.class', '=', 't01tb.class');

        if(!empty($queryData['courseStartDate']) || !empty($queryData['courseEndDate'])){
            $model->join('t06tb', function($join){
                       $join->on('t06tb.class', '=', 't04tb.class')
                            ->on('t06tb.term', '=', 't04tb.term');
                   });
            $model->groupBy('t04tb.class','t04tb.term');
        }

        $model->leftJoin('m09tb', 't04tb.sponsor', '=', 'm09tb.userid');

        $model->where('t04tb.site_branch', '=', DB::raw('2'));
        $model->whereIn('t01tb.board', ['Y','N']);

        if(!empty($queryData['year'])){
            $model->where('t01tb.yerly', '=', $queryData['year']);
        }

        if(!empty($queryData['period'])){
            $model->where('t04tb.term', '=', $queryData['period']);
        }

        if(!empty($queryData['class'])){
            $model->where('t04tb.class', '=', $queryData['class']);
        }

        if(!empty($queryData['classname'])){
            $model->where('t01tb.name', '=', $queryData['classname']);
        }

        if(!empty($queryData['process'])){
            $model->where('t01tb.process', '=', $queryData['process']);
        }

        if(!empty($queryData['sdate1']) && !empty($queryData['sdate2'])){
            $model->where('t04tb.sdate', '>=', $queryData['sdate1']);
            $model->where('t04tb.sdate', '<=', $queryData['sdate2']);
        } else if(!empty($queryData['sdate1'])){
            $model->where('t04tb.sdate', '=', $queryData['sdate1']);
        }

        if(!empty($queryData['edate1']) && !empty($queryData['edate2'])){
            $model->where('t04tb.edate', '>=', $queryData['edate1']);
            $model->where('t04tb.edate', '<=', $queryData['edate2']);
        } else if(!empty($queryData['edate1'])){
            $model->where('t04tb.edate', '=', $queryData['edate1']);
        }

        if(!empty($queryData['courseStartDate']) && !empty($queryData['courseEndDate'])){
            $model->where('t06tb.date', '>=', $queryData['courseStartDate']);
            $model->where('t06tb.date', '<=', $queryData['courseEndDate']);
        } else if(!empty($queryData['courseStartDate'])){
            $model->where('t06tb.date', '=', $queryData['courseStartDate']);
        }

        $paginate_qty = $queryData['_paginate_qty'];
        $data = $model->paginate($paginate_qty);

        return $data;
    }

    public function getInfoForEditRoomset($class,$term)
    {
        $model = $this->model->selectRaw('t04tb.class,
                                          t01tb.yerly,
                                          t04tb.client,
                                          t01tb.name,
                                          t01tb.branchname,
                                          t04tb.term,
                                          t04tb.lock,
                                          t04tb.sdate,
                                          t04tb.edate,
                                          t04tb.staystartdate,
                                          t04tb.stayenddate,
                                          t04tb.staystarttime,
                                          t04tb.stayendtime')
                            ->Join('t01tb', 't04tb.class', '=', 't01tb.class');

        if(!empty($term)){
            $model->where('t04tb.term', '=', $term);
        }

        if(!empty($class)){
            $model->where('t04tb.class', '=', $class);
        }

        $data = $model->get();

        return $data;
    }

    public function getModifyLogBySponsor($userid, $queryData= [])
    {
        $query = $this->model->select([
            'apply_modify_logs.id',
            't01tb.name',
            't04tb.class',
            't04tb.term',
            'm02tb.cname',
            'newM02tb.cname as new_cname',
            'm17tb.enrollname',
            'newM17tb.enrollname as new_enrollname',
            'apply_modify_logs.type',
            'apply_modify_logs.status',
            'apply_modify_logs.idno',
            'apply_modify_logs.new_idno'
        ]);
        $query->join('t01tb', 't01tb.class', '=', 't04tb.class')
              ->join('apply_modify_logs', function($join){
                  $join->on('apply_modify_logs.class', '=', 't04tb.class')
                       ->on('apply_modify_logs.term', '=', 't04tb.term');
              })
              ->join('m02tb', 'm02tb.idno', '=', 'apply_modify_logs.idno')
              ->join('m17tb', 'm17tb.enrollorg', '=', 'm02tb.enrollid')
              ->leftJoin('m02tb as newM02tb', 'newM02tb.idno', '=', 'apply_modify_logs.new_idno')
              ->leftJoin('m17tb as newM17tb', 'newM17tb.enrollorg', '=', 'newM02tb.enrollid')
              ->where('t04tb.sponsor', '=', $userid);

        if (!empty($queryData['yerly'])){
            $queryData['yerly'] = str_pad($queryData['yerly'], 3, '0', STR_PAD_LEFT);
            $query->where('t01tb.yerly', '=', $queryData['yerly']);
        }

        if (!empty($queryData['class'])){
            $query->where('t01tb.class', '=', $queryData['class']);
        }

        if (!empty($queryData['class_name'])){
            $query->where('t01tb.name', 'LIKE', "%{$queryData['class_name']}%");
        }

        if (!empty($queryData['term'])){
            $queryData['term'] = str_pad($queryData['term'], 2, '0', STR_PAD_LEFT);
            $query->where('t04tb.term', '=', $queryData['term']);
        }

        if (!empty($queryData['type'])){
            $query->where('apply_modify_logs.type', '=', $queryData['type']);
        }

        return $query->paginate(10);
    }

    public function getBySponsor($userid)
    {
        $query = $this->model->where('sponsor', '=',  $userid)->orderBy('class', 'desc')->whereHas('t01tb')->with(['t01tb']);
        return $query->get();
    }

    public function ForStudentRoomBedSet($queryData)
    {
        $fields="t01tb.yerly,t04tb.client,t04tb.class,t01tb.name as classname
                            ,t04tb.term,t04tb.sdate,t04tb.edate
                            ,t13tb.idno, m02tb.cname as studentname, m02tb.sex, edu_floor.floorname, edu_bed.bedroom,edu_bed.roomname";

        $sqlwhere1="";
        $sqlwhere2="";
        $sqlwhere3="";

        if (!empty($queryData['year'])) {
            $var=$queryData['year'];
            $sqlwhere1.=" and t01tb.yerly='{$var}'";
        }

        if (!empty($queryData['orgname'])) {
            $var=$queryData['orgname'];
            $sqlwhere1.=" and t04tb.client='{$var}'";
        }

        if (!empty($queryData['classname'])) {
            $var=$queryData['classname'];
            $sqlwhere1.=" and t01tb.name like '%{$var}%'";
        }

        if (!empty($queryData['period'])) {
            $var=$queryData['period'];
            $sqlwhere1.=" and t04tb.term='{$var}'";
        }

        if (!empty($queryData['floorno'])) {
            $var=$queryData['floorno'];
            $sqlwhere1.=" and edu_floor.floorno='{$var}'";
        }

        if (!empty($queryData['idno'])) {
            $var=$queryData['idno'];
            $sqlwhere1.=" and t13tb.idno='{$var}'";
        }

        if (!empty($queryData['studentid'])) {
            $var=$queryData['studentid'];
            $sqlwhere1.=" and t13tb.no='{$var}'";
        }

        if (!empty($queryData['name'])) {
            $var=$queryData['name'];
            $sqlwhere1.=" and m02tb.cname='{$var}'";
        }

        if (!empty($queryData['startdate1']) and !empty($queryData['startdate2'])) {
            $var=$queryData['startdate1'];
            $sqlwhere1.=" and t04tb.sdate >= '{$var}'";
            $var=$queryData['startdate2'];
            $sqlwhere1.=" and t04tb.sdate <= '{$var}'";
        }else if (!empty($queryData['startdate1'])){
            $var=$queryData['startdate1'];
            $sqlwhere1.=" and t04tb.sdate = '{$var}'";
        }

        if (!empty($queryData['enddate1']) and !empty($queryData['enddate2'])) {
            $var=$queryData['enddate1'];
            $sqlwhere1.=" and t04tb.edate >= '{$var}'";
            $var=$queryData['enddate2'];
            $sqlwhere1.=" and t04tb.edate <= '{$var}'";
        }else if (!empty($queryData['enddate1'])){
            $var=$queryData['enddate1'];
            $sqlwhere1.=" and t04tb.edate = '{$var}'";
        }

        $sqlwhere2=$sqlwhere1;

        if (!empty($queryData['startdate3'])){
            $staydate1=$queryData['startdate3'];
            $staydate2=$queryData['startdate3'];
            if (!empty($queryData['startdate4'])){
                $staydate2=$queryData['startdate4'];
            }

            //如果沒有班別的條件
            if (empty($sqlwhere2)){
                $sqlwhere3="((edu_loansroom.startdate between '{$staydate1}' and '{$staydate2}')
                    or (edu_loansroom.enddate between '{$staydate1}' and '{$staydate2}')
                    or (edu_loansroom.startdate<'{$staydate1}' and edu_loansroom.enddate>'{$staydate2}')
                    or (edu_loansroom.startdate>'{$staydate1}' and edu_loansroom.enddate<'{$staydate2}'))";

                $sqlwhere3.=" and bedno <> ''";
            }

            $sqlwhere1.=" and ((t04tb.staystartdate between '{$staydate1}' and '{$staydate2}')
                            or (t04tb.stayenddate between '{$staydate1}' and '{$staydate2}')
                            or (t04tb.staystartdate<'{$staydate1}' and t04tb.stayenddate>'{$staydate2}')
                            or (t04tb.staystartdate>'{$staydate1}' and t04tb.stayenddate<'{$staydate2}'))";

            $sqlwhere2.=" and ((CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime) between '{$staydate1}' and '{$staydate2}')
                    or (CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime) between '{$staydate1}' and '{$staydate2}')
                    or (CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime)<'{$staydate1}' and CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime)>'{$staydate2}')
                    or (CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime)>'{$staydate1}' and CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime)<'{$staydate2}'))";
        }

        $sqlwhere1="where t13tb.bedno <> ''".$sqlwhere1;
        $sqlwhere2="where edu_stayweeksdt.bedno <> ''".$sqlwhere2;

        $sql1="select {$fields} from t04tb inner join t01tb on t04tb.class=t01tb.class
                inner join t13tb on t04tb.class=t13tb.class and t04tb.term = t13tb.term
                inner join m02tb on t13tb.idno=m02tb.idno
                inner join edu_bed on t13tb.bedno=edu_bed.bedno
                inner join edu_floor on edu_bed.floorno = edu_floor.floorno
                $sqlwhere1";

        $sql2=" select t01tb.yerly,t04tb.client,t04tb.class,t01tb.name as classname
                       ,t04tb.term,edu_stayweeks.staystartdate as sdate,edu_stayweeks.stayenddate as edate
                       ,edu_stayweeksdt.idno, m02tb.cname as studentname, m02tb.sex
                       ,edu_floor.floorname, edu_bed.bedroom,edu_bed.roomname
                from t04tb
                inner join t01tb on t04tb.class=t01tb.class
                INNER JOIN t13tb ON t04tb.class = t13tb.class AND t04tb.term = t13tb.term
                INNER JOIN m02tb ON t13tb.idno = m02tb.idno
                inner join edu_stayweeks on t04tb.class=edu_stayweeks.class and t04tb.term=edu_stayweeks.term
                inner join edu_stayweeksdt on edu_stayweeks.class=edu_stayweeksdt.class and edu_stayweeks.term=edu_stayweeksdt.term and edu_stayweeks.week=edu_stayweeksdt.week and t13tb.idno = edu_stayweeksdt.idno
                inner join edu_bed on edu_stayweeksdt.bedno=edu_bed.bedno
                inner join edu_floor on edu_bed.floorno = edu_floor.floorno
                $sqlwhere2";
        $sql="select * from (($sql1) union ($sql2)) q1 order by sdate,bedroom";

        $queryData['_paginate_qty'] = (isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'] !== "") ? $queryData['_paginate_qty'] : 10;

        $query = DB::select( DB::raw($sql) );
        $page = Paginator::resolveCurrentPage("page");
        $perPage = $queryData['_paginate_qty']; //實際每頁筆數
        $offset = ($page * $perPage) - $perPage;

        $query2 = collect(array_slice($query, $offset, $perPage, true))->values();

        $data = new LengthAwarePaginator($query2, count($query), $perPage, $page, ['path' =>  Paginator::resolveCurrentPath()]);

        return $data;
    }

    public function getDormDate($class,$term)
    {
        $model = $this->model->selectRaw('sdate,
                                          edate,
                                          staystartdate,
                                          stayenddate,
                                          staystarttime,
                                          stayendtime');

        $model->where('class','=',$class);
        $model->where('term','=',$term);
        $data = $model->get();

        return $data;
    }

    public function getDormClassList($sdate,$edate)
    {
        $model = $this->model->selectRaw('t04tb.class,
                                          t01tb.yerly,
                                          t01tb.name as classname,
                                          t04tb.term,
                                          t04tb.sdate,
                                          t04tb.edate,
                                          t04tb.staystartdate,
                                          t04tb.stayenddate')
                            ->Join('t01tb', 't04tb.class', '=', 't01tb.class');


        $model->where('t04tb.site_branch', '=', DB::raw('2'));
        $model->whereIn('t01tb.board', ['Y','N']);


        if(!empty($sdate) && !empty($edate)){
            $where = sprintf("(( t04tb.sdate BETWEEN '%s' AND '%s' )
                                OR ( t04tb.staystartdate BETWEEN '%s' AND '%s' ))",$sdate,$edate,$sdate,$edate);
            $model->whereRaw($where);

        }

        $data = $model->get();

        return $data;
    }

    public function getAutoType($class,$term)
    {
        $model = $this->model->selectRaw('auto_type');
        $model->where('class',$class);
        $model->where('term',$term);

        $data = $model->get();

        return $data;
    }
}