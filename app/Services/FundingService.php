<?php
namespace App\Services;

use App\Repositories\T04tbRepository;
use App\Repositories\T07tbRepository;
use App\Repositories\T09tbRepository;
use App\Repositories\T23tbRepository;
use App\Repositories\T49tbRepository;
use App\Services\Term_processService;

use App\Helpers\Des;
use App\Helpers\Common;

use DateTime;
use DB;
use Auth;
use Validator;
use App\Helpers\SystemParam;

class FundingService
{
    /**
     * ForumService constructor.
     * @param FundingRepository $fundingRpository
     */
    public function __construct(
        T04tbRepository $t04tbRepository,
        T07tbRepository $t07tbRepository,
        T09tbRepository $t09tbRepository,
        T23tbRepository $t23tbRepository,
        T49tbRepository $t49tbRepository,
        Term_processService $term_processService
    )
    {
        $this->t04tbRepository = $t04tbRepository;
        $this->t07tbRepository = $t07tbRepository;
        $this->t09tbRepository = $t09tbRepository;
        $this->t23tbRepository = $t23tbRepository;
        $this->t49tbRepository = $t49tbRepository;
        $this->term_processService = $term_processService;
    }

    public function getT04tbs($queryData)
    {
        return $this->t04tbRepository->get($queryData);
    }

    public function getT07tbs($queryData)
    {
        return $this->t07tbRepository->getByQueryList($queryData);
    }

    public function getT07tb($t07tb_info, $t07tb_fields = "*")
    {
        return $this->t07tbRepository->find($t07tb_info, $t07tb_fields);
    }

    public function updateT07tb($t07tb, $new_t07tb)
    {
        
        if (!empty($t07tb->kind)){
            unset($new_t07tb['kind']);
        }

        $tmp_new_t07tb = $new_t07tb;
        $new_t07tb = clone $t07tb;
        $new_t07tb->fill($tmp_new_t07tb);
        $new_t07tb = $new_t07tb->toArray();
        $new_t07tb = $this->computeAmt($new_t07tb, ($t07tb->type == 2));

        $t07tb_info = collect($new_t07tb)->only(['class', 'term', 'type'])->toArray();

        foreach ($new_t07tb as $key => $value){
            if ($t07tb->$key == $value){
                unset($new_t07tb[$key]);
            }
        }

        if (empty($new_t07tb)){
            return true;
        }else{
            return $this->t07tbRepository->update($t07tb_info, $new_t07tb);
        }

    }

    public function computeAmt($t07tb, $is_conclusion = false){

        $amts = [
            'inlectamt' => ['inlecthr', 'inlectunit'],          // 內聘鐘點費金額
            'burlectamt' => ['outlecthr', 'burlectunit'],       // 總處鐘點費金額
            'outlectamt' => ['outlecthr', 'outlectunit'],       // 外聘鐘點費金額
            'othlectamt' => ['othlecthr', 'othlectunit'],       // 其他鐘點費金額
            'motoramt' => ['motorcnt', 'motorunit'],            // 汽車金額
            'drawamt' => ['drawcnt', 'drawunit'],               // 課程規劃費
            'vipamt' => ['vipcnt', 'vipunit'],                  // 行政套房住宿金額
            'doneamt' => ['donecnt', 'doneunit'],               // 單人房金額
            'sinamt' => ['sincnt', 'sinunit'],                  // 雙人房金額
            'meaamt' => ['meacnt', 'meaunit'],                  // 早餐金額
            'lunamt' => ['luncnt', 'lununit'],                  // 午餐金額
            'dinamt' => ['dincnt', 'dinunit'],                  // 晚餐金額
            'docamt' => ['doccnt', 'docunit'],                  // 教材金額
            'penamt' => ['pencnt', 'penunit'],                  // 文具金額
            'insamt' => ['inscnt', 'insunit'],                  // 保險費金額
            'actamt' => ['actcnt', 'actunit'],                  // 活動費金額
            'placeamt' => ['placecnt', 'placeunit'],            // 場地租借金額
            'teaamt' => ['teacnt', 'teaunit'],                  // 茶水費金額
            'prizeamt' => ['prizecnt', 'prizeunit'],            // 獎品費金額
            'birthamt' => ['birthcnt', 'birthunit'],            // 慶生活動費金額
            'unionamt' => ['unioncnt', 'unionunit'],            // 聯誼活動金額
            'setamt' => ['setcnt', 'setunit'],                  // 場地佈置費金額
            'dishamt' => ['dishcnt', 'dishunit'],               // 加菜金金額
        ];

        if ($is_conclusion){
            // 結算不能更改鐘點費
            unset($amts['inlectamt']);
            unset($amts['burlectamt']);
            unset($amts['outlectamt']);
            unset($amts['othlectamt']);
        }

        foreach ($amts as $amt => $cnt_unit){
            if (isset($t07tb[$cnt_unit[0]]) && isset($t07tb[$cnt_unit[1]])){
                $t07tb[$amt] = (int)$t07tb[$cnt_unit[0]] * (int)$t07tb[$cnt_unit[1]];
            }
        }

        $t07tb['insamt'] = $t07tb['insamt'] * $t07tb['daytype'];
        $t07tb['actamt'] = $t07tb['actamt'] * $t07tb['daytype'];

        return $t07tb;
    }

    public function splitClassString($selects)
    {

        $selects = collect($selects)->map(function($select){
            $select = explode( '##', $select);

            if (count($select) != 2) return null;

            return [
                'class' => $select[0],
                'term' => $select[1]
            ];
        });

        return $selects->filter()->toArray();
    }

    public function batchInsertProbably($select_class)
    {

        $t04tbs = $this->t04tbRepository->getByIn($select_class);
        $t07tbs = $this->t07tbRepository->getByInAndType($select_class, 1);
        $t07tbs = $t07tbs->map(function($t07tb){
            return [
                'class_term' => $t07tb->class.'_'.$t07tb->term,
                'class' => $t07tb->class,
                'term' => $t07tb->term,
                'type' => $t07tb->type
            ];
        });

        $t07tbs = $t07tbs->keyBy('class_term');

        DB::beginTransaction();
        $system = SystemParam::get();

        try {

            foreach ($t04tbs as $t04tb){
                //班務流程凍結
                $freeze = $this->term_processService->getFreeze('arrangement_class', $t04tb->class, $t04tb->term);
                if($freeze == 'Y'){
                    continue;
                }
                if (empty($t07tbs[$t04tb->class.'_'.$t04tb->term])){
                    $new_t07tb = $this->createProbably($t04tb, $system);
                    $new_t07tb = $this->computeAmt($new_t07tb);
                    $this->t07tbRepository->insert($new_t07tb);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;
            return false;
        }

    }

    public function createProbably($t04tb, $system)
    {

        $t07tb = [
            'type' => 1,
            'class' => $t04tb->class,
            'term' => $t04tb->term,
            'inlecthr' => 0,
            'inlectamt' => 0,
            'burlectamt' => 0,
            'outlecthr' => 0,
            'outlectamt' => 0,
            'othlecthr' => 0,
            'othlectunit' => 0,
            'othlectamt' => 0,
            'trainamt' => 0,
            'planeamt' => 0,
            'noteamt' => 0,
            'speakamt' => 0,
            'drawcnt' => 0,
            'drawunit' => 0,
            'drawamt' => 0,
            'vipcnt' => 0,
            'vipamt' => 0,
            'donecnt' => 0,
            'doneamt' => 0,
            'placecnt' => 0,
            'placeunit' => 0,
            'placeamt' => 0,
            'otheramt1' => 0,
            'otheramt2' => 0,
            'mrtamt' => 0,
            'review_total' => 0,
            'donecnt' => 0,
            'sincnt' => 0,
            'meacnt' => 0,
            'luncnt' => 0,
            'dincnt' => 0,
            'daytype' => 1,
            'shipamt' => 0
        ];

        $t04tb_info = ['class' => $t04tb->class, 'term' => $t04tb->term];

        /*
            鐘點費
        */

        /*
            計算單人房人數
            [t13tb]
            台北縣政府名額 3764100
            台北市政府名額 3790000
            基隆市政府名額名額 3765700
            中央機關名額 m13tb.type='1' (中央暨所屬單位)
            單人房人數 = [班期人數(t04tb.quota) - (中央機關名額 * 0.5 + 台北縣政府名額 + 台北市政府名額 + 基隆市政府名額名額)] * 訓期(天)
        */

        $central_quota = $t04tb->t03tb()
                               ->join('m13tb', 'm13tb.organ', '=', 't03tb.organ')
                               ->where('m13tb.type', '=', 1)
                               ->sum('quota');

        $taipei_area_quota = $t04tb->t03tb()->whereIn('organ', ['3764100', '3790000', '3765700'])->sum('quota');
        $t07tb['sincnt'] = ($t04tb->quota - ($central_quota * 0.5 + $taipei_area_quota)) * $t04tb->t01tb->period;

        /*
          計算伙食費
          (1)午餐及早餐=(班期人數+2)*訓期(天)
          (2)晚餐人數=(班期人數*(1/2)+2)*(訓期(天)-1)
        */

        $t07tb['meacnt'] = ($t04tb->quota + 2) * $t04tb->t01tb->day;
        $t07tb['luncnt'] = ($t04tb->quota + 2) * $t04tb->t01tb->day;
        $t07tb['dincnt'] = ($t04tb->t01tb->day == 0) ? 0 : (($t04tb->quota * 0.5) + 2) * ($t04tb->t01tb->day - 1);

        // 汽車人次 = 2 * 訓期(天)
        $t07tb['motorcnt'] = $t04tb->t01tb->day * 2;

        // 教材人份＝(班期人數+5)*2*訓期(天)
        $t07tb['doccnt'] = ($t04tb->quota + 5) * 2 * $t04tb->t01tb->day;

        // 文具及其他用品費(人份)=文具人份=班期人數=(t04tb.quota)
        $t07tb['pencnt'] = $t04tb->quota;

        $sum_t09tb = $this->t09tbRepository->getConclusionInfo($t04tb_info);
        $sum_t09tb = $sum_t09tb->keyBy('kind'); 

        // 稿費、演講費
        $t07tb['noteamt'] = $sum_t09tb->pluck('noteamt')->sum();
        $t07tb['speakamt'] = $sum_t09tb->pluck('speakamt')->sum();
        $t07tb['review_total'] = $sum_t09tb->pluck('review_total')->sum();

        /*
           校外教學
           校外教學《6<=訓期(天)<=20》->1天 《訓期(天)>=21》->2天
           (1)保險費人數=班期人數+2
           (2)活動費人數=班期人數
           (3)租車費金額=12000
        */

        $t07tb['daytype'] = 0;
        if ($t04tb->t01tb->day >= 6 && $t04tb->t01tb->day <=20){
            $t07tb['daytype'] = 1;
        }else if($t04tb->t01tb->day >= 21) {
            $t07tb['daytype'] = 2;
        }

        // 保險費人數=班期人數+2
        $t07tb['inscnt'] = $t04tb->quota + 2;

        // 活動費人數 = 班期人數
        $t07tb['actcnt'] = $t04tb->quota;

        // 租車費金額 = 12000
        $t07tb['caramt'] = 12000;

        /*
         <<其他雜支
          (1)茶點費、慶生活動、聯誼活動、場地佈置、加菜金的數量
            《6<=訓期(天)<=20》->1
            《   訓期(天)>=21》->2
        */
        $t07tb['teacnt'] = $t07tb['birthcnt'] = $t07tb['unioncnt'] = $t07tb['setcnt'] = $t07tb['dishcnt'] = 0;

        if ($t04tb->t01tb->day >= 6 && $t04tb->t01tb->day <=20){
            $t07tb['teacnt'] = $t07tb['birthcnt'] = $t07tb['unioncnt'] = $t07tb['setcnt'] = $t07tb['dishcnt'] = 1;
        }else if($t04tb->t01tb->day >= 21) {
            $t07tb['teacnt'] = $t07tb['birthcnt'] = $t07tb['unioncnt'] = $t07tb['setcnt'] = $t07tb['dishcnt'] = 2;
        }

        /*
          (2)獎品費數量
            《   訓期(天)>=5 》->1
            《6<=訓期(天)<=20》->3
            《   訓期(天)>=21》->5
        */
        $t07tb['prizecnt'] = 0;
        if ($t04tb->t01tb->day >= 5){
            $t07tb['prizecnt'] = 1;
        }else if ($t04tb->t01tb->day >= 6 && $t04tb->t01tb->day <=20){
            $t07tb['prizecnt'] = 3;
        }else if($t04tb->t01tb->day >= 21) {
            $t07tb['prizecnt'] = 5;
        }

        if ($t04tb->t01tb->day < 5){
            $t07tb['penunit'] = $system->spenunit;  // 短期班文具費
        }else if ($t04tb->t01tb->day > 10){
            $t07tb['penunit'] = $system->lpenunit;  // 長期班文具費
        }else{
            $t07tb['penunit'] = $system->mpenunit;  // 中期班文具費
        }

        $t07tb['inlectunit'] = $system->inlectunit;     // 內聘鐘點費
        $t07tb['burlectunit'] = $system->burlectunit;   // 本局鐘點費
        $t07tb['outlectunit'] = $system->outlectunit;   // 外聘鐘點費
        $t07tb['sinunit'] = $system->sinunit;           // 單人房費
        $t07tb['doneunit'] = $system->doneunit;         // 雙人單床房費
        $t07tb['vipunit'] = $system->vipunit;           // 行政套房費
        $t07tb['meaunit'] = $system->meaunit;           // 早餐費用
        $t07tb['lununit'] = $system->lununit;           // 午餐費用
        $t07tb['dinunit'] = $system->dinunit;           // 晚餐費用
        $t07tb['motorunit'] = $system->motorunit;       // 汽車交通費
        $t07tb['docunit'] = $system->docunit;           // 教材費
        $t07tb['insunit'] = $system->insunit;           // 保險費
        $t07tb['actunit'] = $system->actunit;           // 活動費
        $t07tb['teaunit'] = $system->teaunit;           // 茶點費
        $t07tb['prizeunit'] = $system->prizeunit;       // 獎品費
        $t07tb['birthunit'] = $system->birthunit;       // 慶生活動費
        $t07tb['unionunit'] = $system->unionunit;       // 聯誼活動費
        $t07tb['setunit'] = $system->setunit;           // 場地佈置費
        $t07tb['dishunit'] = $system->dishunit;         // 加菜金

        return $t07tb;
    }

    public function batchInsertConclusion($select_class)
    {
        $t04tbs = $this->t04tbRepository->getByIn($select_class);
        $t07tbs = $this->t07tbRepository->getByInAndType($select_class, 2);
        $probablys = $this->t07tbRepository->getByInAndType($select_class, 1); // 概算

        $t07tbs = $t07tbs->groupBy('class')->map(function($t07tbGroup){
            return $t07tbGroup->keyBy('term');
        });

        $probablys = $probablys->groupBy('class')->map(function($t07tbGroup){
            return $t07tbGroup->keyBy('term');
        });

        DB::beginTransaction();
        $system = SystemParam::get();

        try {

            foreach ($t04tbs as $t04tb){
                //班務流程凍結
                $freeze = $this->term_processService->getFreeze('funding_edit_type2', $t04tb->class, $t04tb->term);
                
                if($freeze == 'Y'){
                    continue;
                }

                $probablyKind = isset($probablys[$t04tb->class][$t04tb->term]) ? $probablys[$t04tb->class][$t04tb->term]->kind : null;

                if (empty($t07tbs[$t04tb->class][$t04tb->term])){
                    $new_t07tb = $this->createConclusion($t04tb, $system, $probablyKind);
                    $new_t07tb = $this->computeAmt($new_t07tb);

                    $this->t07tbRepository->insert($new_t07tb);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;
            return false;
        }
    }
    /*
        結算
    */
    public function createConclusion($t04tb, $system, $kind)
    {
        /*
            鐘點費
            從 t09tb [講座任課資料檔] 講座聘任處理 讀取
        */
        $t04tb_info = ['class' => $t04tb->class, 'term' => $t04tb->term];
        /*
        kind
        1:外聘 2:總處 3:內聘 4:其他
        */
        $sum_t09tb = $this->t09tbRepository->getConclusionInfo($t04tb_info);
        $sum_t09tb = $sum_t09tb->keyBy('kind'); 

        $t07tb = [
            'type' => 2,
            'class' => $t04tb->class,
            'term' => $t04tb->term,
            'kind' => $kind,
            'inlecthr' => 0,
            'inlectamt' => 0,
            'burlectamt' => 0,
            'outlecthr' => 0,
            'outlectamt' => 0,
            'othlecthr' => 0,
            'othlectunit' => 0,
            'othlectamt' => 0,
            'trainamt' => 0,
            'planeamt' => 0,
            'noteamt' => 0,
            'speakamt' => 0,
            'drawcnt' => 0,
            'drawunit' => 0,
            'drawamt' => 0,
            'vipcnt' => 0,
            'vipamt' => 0,
            'donecnt' => 0,
            'doneamt' => 0,
            'placecnt' => 0,
            'placeunit' => 0,
            'placeamt' => 0,
            'otheramt1' => 0,
            'otheramt2' => 0,
            'mrtamt' => 0,
            'review_total' => 0,
            'donecnt' => 0,
            'sincnt' => 0,
            'meacnt' => 0,
            'luncnt' => 0,
            'dincnt' => 0,
            'daytype' => 1,
            'shipamt' => 0                       
        ];

        // 時數
        $t07tb['outlecthr'] = (isset($sum_t09tb[1])) ? $sum_t09tb[1]->lecthr : 0;        
        $t07tb['burlecthr'] = (isset($sum_t09tb[2])) ? $sum_t09tb[2]->lecthr : 0;
        $t07tb['inlecthr'] = (isset($sum_t09tb[3])) ? $sum_t09tb[3]->lecthr : 0;
        $t07tb['othlecthr'] = (isset($sum_t09tb[4])) ? $sum_t09tb[4]->lecthr : 0;

        // 金額
        $t07tb['outlectamt'] = (isset($sum_t09tb[1])) ? $sum_t09tb[1]->lectamt : 0;        
        $t07tb['burlectamt'] = (isset($sum_t09tb[2])) ? $sum_t09tb[2]->lectamt : 0;
        $t07tb['inlectamt'] = (isset($sum_t09tb[3])) ? $sum_t09tb[3]->lectamt : 0;
        $t07tb['othlectamt'] = (isset($sum_t09tb[4])) ? $sum_t09tb[4]->lectamt : 0;

        // 其他鐘點費單價
        $t07tb['othlectunit'] = ($t07tb['othlecthr'] == 0) ? 0 : $t07tb['othlectamt'] / $t07tb['othlecthr'];

        // 稿費、演講費
        $t07tb['noteamt'] = $sum_t09tb->pluck('noteamt')->sum();
        $t07tb['speakamt'] = $sum_t09tb->pluck('speakamt')->sum();
        $t07tb['review_total'] = $sum_t09tb->pluck('review_total')->sum();

        // 伙食費人數
        $t23tb = $this->t23tbRepository->getConclusionInfo($t04tb_info);

        // 交通費
        // $t07tb['motoramt'] = $sum_t09tb->pluck('motoramt')->sum();
        $t07tb['trainamt'] = $sum_t09tb->pluck('trainamt')->sum();
        $t07tb['planeamt'] = $sum_t09tb->pluck('planeamt')->sum();
        $t07tb['shipamt'] = $sum_t09tb->pluck('shipamt')->sum();

        $t07tb["sincnt"] = (int)$t23tb->sincnt;      // 單人房
        $t07tb["donecnt"] = (int)$t23tb->donecnt;    // 雙人房
        $t07tb["meacnt"] = (int)$t23tb->meacnt;      // 早餐
        $t07tb["luncnt"] = (int)$t23tb->luncnt;      // 午餐
        $t07tb["dincnt"] = (int)$t23tb->dincnt;      // 晚餐

        // 教材費 (已付費)
        $sum_t49tb = $this->t49tbRepository->getConclusionInfo($t04tb_info);
        $t07tb["docamt"] = $sum_t49tb->docamt;


        // 單價
        if ($t04tb->t01tb->day < 5){
            $t07tb['penunit'] = $system->spenunit;  // 短期班文具費
        }else if ($t04tb->t01tb->day > 10){
            $t07tb['penunit'] = $system->lpenunit;  // 長期班文具費
        }else{
            $t07tb['penunit'] = $system->mpenunit;  // 中期班文具費
        }

        $t07tb['inlectunit'] = $system->inlectunit;     // 內聘鐘點費
        $t07tb['burlectunit'] = $system->burlectunit;   // 本局鐘點費
        $t07tb['outlectunit'] = $system->outlectunit;   // 外聘鐘點費
        $t07tb['sinunit'] = $system->sinunit;           // 單人房費
        $t07tb['doneunit'] = $system->doneunit;         // 雙人單床房費
        $t07tb['vipunit'] = $system->vipunit;           // 行政套房費
        $t07tb['meaunit'] = $system->meaunit;           // 早餐費用
        $t07tb['lununit'] = $system->lununit;           // 午餐費用
        $t07tb['dinunit'] = $system->dinunit;           // 晚餐費用
        $t07tb['motorunit'] = $system->motorunit;       // 汽車交通費
        $t07tb['docunit'] = $system->docunit;           // 教材費
        $t07tb['insunit'] = $system->insunit;           // 保險費
        $t07tb['actunit'] = $system->actunit;           // 活動費
        $t07tb['teaunit'] = $system->teaunit;           // 茶點費
        $t07tb['prizeunit'] = $system->prizeunit;       // 獎品費
        $t07tb['birthunit'] = $system->birthunit;       // 慶生活動費
        $t07tb['unionunit'] = $system->unionunit;       // 聯誼活動費
        $t07tb['setunit'] = $system->setunit;           // 場地佈置費
        $t07tb['dishunit'] = $system->dishunit;         // 加菜金

        return $t07tb;
    }

    public function updateUnitPrice($queryData)
    {
        if (empty($queryData['updateSdate'])){
            return false;
        }

        $queryData['updateSdate'] = str_pad($queryData['updateSdate'], 7, '0', STR_PAD_LEFT);
       
        if (empty($queryData['updateEdate'])){
            return false;
        }
        $queryData['updateEdate'] = str_pad($queryData['updateEdate'], 7, '0', STR_PAD_LEFT);

        $units = ['inlectunit', 'burlectunit', 'outlectunit', 'sinunit', 'doneunit', 'vipunit', 'meaunit', 'lununit', 'dinunit', 'motorunit', 'docunit', 'insunit', 'actunit', 'teaunit', 'prizeunit', 'birthunit', 'unionunit', 'setunit', 'dishunit'];

        $system = \App\Models\S02tb::select($units)->first();

        $t07tbs = $this->t07tbRepository->getByT04tbSEdate($queryData['updateSdate'], $queryData['updateEdate']);

        DB::beginTransaction();

        try {

            foreach ($t07tbs as $t07tb){
                $t07tb->fill($system->toArray());
                $t07tb = $this->computeAmt($t07tb->toArray());
                $t07tbKey = collect($t07tb)->only(['class', 'term', 'type'])->toArray();

                unset($t07tb['updated_at']);
                $this->t07tbRepository->update($t07tbKey, $t07tb);
                
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;
            return false;
        }
    }

    public function deleteT07tb($class, $term, $type)
    {
        
        DB::beginTransaction();
        try {
            $t07tbKey = compact(['class', 'term', 'type']);
            $this->t07tbRepository->delete($t07tbKey);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;
            return false;
        }

    }
}
