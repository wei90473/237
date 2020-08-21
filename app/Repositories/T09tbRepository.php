<?php
namespace App\Repositories;

use App\Models\T09tb;
use App\Repositories\Repository;
use DateTime;
use DB;

class T09tbRepository extends Repository
{
    public function __construct(T09tb $t09tb)
    {
        $this->model = $t09tb;
    }  
    
    public function getComputeInsurerateInfo($class_info)
    {
        $t09tb = $this->model->select([
                                't09tb.class',
                                't09tb.term',
                                't09tb.course',
                                't09tb.idno',
                                't09tb.type',
                                't09tb.kind',
                                't09tb.insuremk1',
                                't09tb.insuremk2',
                                't09tb.insureamt1',
                                't09tb.lectamt',
                                't09tb.insureamt2',
                                't09tb.noteamt',
                                't09tb.speakamt',
                                't09tb.teachtot',
                                't09tb.deductamt',
                                't09tb.insuretot',
                                't09tb.tratot',
                                't09tb.netpay',
                                't09tb.totalpay',
                                DB::raw('t06tb.date t06tb_date')
                            ])
                           ->join('t06tb', function($join){
                               $join->on('t06tb.class', '=', 't09tb.class')
                                    ->on('t06tb.term', '=', 't09tb.term')
                                    ->on('t06tb.course', '=', 't09tb.course');
                           })
                           ->where('t09tb.class', '=', $class_info['class'])
                           ->where('t09tb.term', '=', $class_info['term'])
                           ->where('t09tb.paidday', '=', '')
                           ->where(function($query){
                                $query->where('t09tb.insuremk1', '=', 'Y')
                                      ->orWhere('t09tb.insuremk2', '=', 'Y');
                           })
                           ->where('t06tb.date', '<>', '')
                           ->orderBy('t09tb.class')
                           ->orderBy('t09tb.term')
                           ->orderBy('t09tb.idno')
                           ->orderBy('t06tb.date');
        return $t09tb->get();
    }

    public function getConclusionInfo($t04tb_info)
    {
        return $this->model->select(DB::raw("
                                            kind, 
                                            sum(lectamt) lectamt, 
                                            sum(lecthr) lecthr, 
                                            sum(noteamt) noteamt, 
                                            sum(speakamt) speakamt,
                                            sum(motoramt) motoramt,
                                            sum(trainamt) trainamt,
                                            sum(planeamt) planeamt,
                                            sum(review_total) review_total,
                                            sum(ship) shipamt
                                            "))
                    ->where($t04tb_info)
                    ->whereRaw(DB::raw("paidday is not null AND paidday <> ''"))
                    ->groupBy('kind')->get();
                    
    }

    // 取得已轉帳資料
    public function getIsPayed($t06tbKey)
    {
        return $this->model->where($t06tbKey)
                           ->where(function($query){
                              $query->where('paidday', '<>', '')
                                    ->orWhereNotNull('paidday');
                           })
                           ->count();
    }    
}