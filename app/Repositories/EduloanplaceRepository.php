<?php
namespace App\Repositories;

use App\Models\Edu_loanplace;
use App\Repositories\Repository;
use DB;

class EduloanplaceRepository extends Repository
{
    public function __construct(Edu_loanplace $eduLoanplace)
    {
        $this->model = $eduLoanplace;
    }  

    public function getChargeList($queryData){
    	$model = $this->model->selectRaw('edu_loanplace.id,edu_loanplace.applyno,edu_loanplace.applydate
                                ,edu_loanplace.applyuser,edu_loanplace.orgname,edu_loanplace.title
                                ,edu_loanplace.email,edu_loanplace.tel,edu_loanplace.fax,edu_loanplace.cellphone
                                ,edu_loanplace.applykind,edu_loanplace.num,edu_loanplace.mstay,edu_loanplace.fstay
                                ,edu_loanplace.processdate,edu_loanplace.reason,edu_loanplace.reason2
                                ,edu_loanplace.chief1,edu_loanplace.chief2,edu_loanplace.description
                                ,(edu_loanplace.discount1+edu_loanplace.discount2) AS discount
                                ,edu_loanplace.status,edu_loanplace.receiptno,edu_loanplace.paydate,edu_loanplace.payuser
                                ,edu_classcode.name as statusname
                                ,count(edu_loanplacelst.applyno) as detail
                                ,(sum(edu_loanplacelst.fee)-(edu_loanplace.discount1+edu_loanplace.discount2)) as fee')
							->leftJoin('edu_loanplacelst', 'edu_loanplace.applyno', '=', 'edu_loanplacelst.applyno')
							->leftJoin('edu_classcode', function($join){
									$join->on('edu_loanplace.status', '=', 'edu_classcode.code')
										 ->on('edu_classcode.class', '=', DB::raw('59'));
							  })
							->groupBy('edu_loanplace.applyno')
							->orderBy('edu_loanplace.applyno');

    	if(!empty($queryData['applyuser'])){
    		$model->where('edu_loanplace.applyuser', 'LIKE', '%'.$queryData['applyuser'].'%');
    	}

    	if(!empty($queryData['start_date']) && !empty($queryData['end_date'])){
    		$model->where('edu_loanplace.applydate', '>=', $queryData['start_date']);
    		$model->where('edu_loanplace.applydate', '<=', $queryData['end_date']);
    	} else if(!empty($queryData['start_date'])){
    		$model->where('edu_loanplace.applydate', '=', $queryData['start_date']);
    	}

    	if(!empty($queryData['status'])){
    		$model->where('edu_loanplace.status', '=', $queryData['status']);
    	}

    	if(!empty($queryData['orgname'])){
    		$model->where('edu_loanplace.orgname', 'LIKE', '%'.$queryData['orgname'].'%');
    	}

    	if(empty($queryData['applyuser']) && empty($queryData['start_date']) && empty($queryData['end_date']) && empty($queryData['status']) && empty($queryData['orgname'])){
    		$model->whereIn('edu_loanplace.status', ['N','2']);
    	}

    	$paginate_qty = (isset($queryData['_paginate_qty']) && $queryData['_paginate_qty']) ? $queryData['_paginate_qty'] : 100;
        $data = $model->paginate($paginate_qty);

        return $data;
    }

    public function getForSpacproc($applyno){
        $model = $this->model->selectRaw('edu_loanplace.id,edu_loanplace.confirm_fee,edu_loanplace.applyno,edu_loanplace.applydate
                                ,edu_loanplace.applyuser,edu_loanplace.orgname,edu_loanplace.title
                                ,edu_loanplace.email,edu_loanplace.tel,edu_loanplace.fax,edu_loanplace.cellphone
                                ,edu_loanplace.applykind,edu_loanplace.num,edu_loanplace.mstay,edu_loanplace.fstay
                                ,edu_loanplace.processdate,edu_loanplace.reason,edu_loanplace.reason2
                                ,edu_loanplace.chief1,edu_loanplace.chief2,edu_loanplace.description
                                ,(edu_loanplace.discount1+edu_loanplace.discount2) AS discount
                                ,edu_loanplace.status,edu_loanplace.receiptno,edu_loanplace.paydate,edu_loanplace.payuser
                                ,edu_classcode.name as statusname
                                ,count(edu_loanplacelst.applyno) as detail
                                ,(sum(edu_loanplacelst.fee)-(edu_loanplace.discount1+edu_loanplace.discount2)) as fee')
                            ->leftJoin('edu_loanplacelst', 'edu_loanplace.applyno', '=', 'edu_loanplacelst.applyno')
                            ->leftJoin('edu_classcode', function($join){
                                    $join->on('edu_loanplace.status', '=', 'edu_classcode.code')
                                         ->on('edu_classcode.class', '=', DB::raw('59'));
                              })
                            ->groupBy('edu_loanplace.applyno')
                            ->orderBy('edu_loanplace.applyno');

        $model->where('edu_loanplace.applyno', '=' , $applyno);                   
        $data = $model->get();

        return $data;
    }

    public function getrptSPrptReceipt($applyno){
        $model = $this->model->selectRaw('edu_loanplace.payuser
                                ,(sum(edu_loanplacelst.fee)-(edu_loanplace.discount1+edu_loanplace.discount2)) as fee')
                            ->Join('edu_loanplacelst', 'edu_loanplace.applyno', '=', 'edu_loanplacelst.applyno')
                            ->groupBy('edu_loanplace.applyno');

        $model->where('edu_loanplace.applyno', '=' , $applyno);                   
        $data = $model->get();

        foreach($data as $key => $value){
            date_default_timezone_set("Asia/Taipei");
            $y=date('Y', time())-1911;
            $m=date('m', time());
            $d=date('d', time());
            $date = str_pad($y, 3,'0',STR_PAD_LEFT).str_pad($m, 2,'0',STR_PAD_LEFT).str_pad($d, 2,'0',STR_PAD_LEFT);

            $value['Y']=intval(substr($date,0,3));
            $value['M']=intval(substr($date,3,2));
            $value['D']=intval(substr($date,5,2));

            $fee=strval($value['fee']);
            $len=strlen($fee);
            for ($i=1;$i<=$len;$i++){
                $f='f'.$i;
                $c='c'.$i;
                $value[$f]=substr($fee,$len-$i,1);

                $ch=array('零','壹','貮','參','肆','伍','陸','柒','捌','玖');
                $s=intval($value[$f]);
                $s=strval($s);
                $as=str_split($s);
                $r=array();
                foreach ($as as $j) {
                    $r[]=$ch[intval($j)];
                }
                unset($as);
                $value[$c] = implode($r);         
            }
        }

        return $data;
    }

}