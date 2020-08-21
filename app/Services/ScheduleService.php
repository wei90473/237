<?php
namespace App\Services;

use App\Repositories\T04tbRepository;
use App\Repositories\M09tbRepository;
use App\Repositories\M14tbRepository;
use App\Repositories\T01tbRepository;
use App\Repositories\M25tbRepository;
use App\Repositories\S02tbRepository;
use App\Repositories\T22tbRepository;
use App\Repositories\T03tbRepository;
use App\Repositories\T36tbRepository;
use App\Repositories\T97tbRepository;
use App\Repositories\T37tbRepository;
use App\Repositories\T47tbRepository;
use App\Repositories\M17tbRepository;
use App\Repositories\T06tbRepository;
use App\Repositories\ClassWeekRepository;
use App\Repositories\T51tbRepository;

use DB;
use App\Helpers\Common;
use App\Helpers\TrainSchedule;
use App\Helpers\Site;
use App\Helpers\TWDateTime;

use Excel;
use DateTime;
use Validator;
use App\Models\Edu_classroom;

use App\Models\T04tb;

class ScheduleService
{
    /**
     * ScheduleService constructor.
     * @param DemandDistributionRepository $demandDistributionRpository
     */
    public function __construct(
        T04tbRepository $t04tbRepository,
        M09tbRepository $m09tbRepository,
        M14tbRepository $m14tbRepository,
        T01tbRepository $t01tbRepository,
        M25tbRepository $m25tbRepository,
        S02tbRepository $s02tbRepository,
        T22tbRepository $t22tbRepository,
        T03tbRepository $t03tbRepository,
        T36tbRepository $t36tbRepository,
        T97tbRepository $t97tbRepository,
        T37tbRepository $t37tbRepository,
        T47tbRepository $t47tbRepository,
        T06tbRepository $t06tbRepository,
        ClassWeekRepository $classWeekRepository,
        T51tbRepository $t51tbRepository,
        M17tbRepository $m17tbRepository
    )
    {
        $this->t04tbRepository = $t04tbRepository;
        $this->m09tbRepository = $m09tbRepository;
        $this->m14tbRepository = $m14tbRepository;
        $this->t01tbRepository = $t01tbRepository;
        $this->m25tbRepository = $m25tbRepository;
        $this->s02tbRepository = $s02tbRepository;
        $this->t22tbRepository = $t22tbRepository;
        $this->t03tbRepository = $t03tbRepository;
        $this->t36tbRepository = $t36tbRepository;
        $this->t97tbRepository = $t97tbRepository;
        $this->t37tbRepository = $t37tbRepository;
        $this->t47tbRepository = $t47tbRepository;
        $this->t06tbRepository = $t06tbRepository;
        $this->classWeekRepository = $classWeekRepository;
        $this->t51tbRepository = $t51tbRepository;
        $this->m17tbRepository = $m17tbRepository;
    }

    /**
     * 取得開班資料列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */

    public function getOpenClassList($queryData = [])
    {
        // 篩選掉洽借班期
        // $queryData['is_type13'] = false;

        return $this->t04tbRepository->getByQueryList($queryData);
    }

    public function getSponsors()
    {
        return $this->m09tbRepository->all();
    }

    public function getClass($class)
    {
        $select = [
            "class",
            "name",
            "type",
            "style",
            "day",
            "time1","time2","time3","time4","time5","time6","time7"
        ];
        $t01tb = $this->t01tbRepository->find($class, $select);
        if ($t01tb){
            return $t01tb;
        }else{
            return false;
        }
    }

    public function getAllT01tbs()
    {
        ini_set('memory_limit', '256M');
        return  $this->t01tbRepository->get(null, false);
    }

    public function getT04tb($class, $term)
    {
        return $this->t04tbRepository->find(compact('class', 'term'));
    }

    public function getClassRooms()
    {
        $class_room = [
            "m14tb" => $this->m14tbRepository->get([], false),
            "m25tb" => Edu_classroom::get()
        ];

        $class_room['m14tb'] = $class_room['m14tb']->map(function($m14tb){
            return [
                'site' => $m14tb->site,
                'name' => $m14tb->name
            ];
        })->toArray();

        $class_room['m25tb'] = $class_room['m25tb']->map(function($m25tb){
            return [
                'site' => $m25tb->roomno,
                'name' => $m25tb->roomname
            ];
        })->toArray();

        return $class_room;
    }

    public function getSections()
    {
        return $this->m09tbRepository->getSections();
    }

    // public function storeSchedule($t04tb, $action)
    // {
    //     $t04tb_info = [
    //         'class' => $t04tb['class'],
    //         'term' => $t04tb['term']
    //     ];

    //     DB::beginTransaction();

    //     try {

    //         $t01tb = $this->t01tbRepository->find($t04tb["class"]);

    //         // 為了計算先轉回西元格式
    //         $t04tb["sdate"] = Common::dateRocToCeFormat($t04tb["sdate"]);
    //         $t04tb["edate"] = Common::dateRocToCeFormat($t04tb["edate"]);

    //         if (!empty($t01tb)){

    //             /*
    //                 若班別為開放自由報名班期 (t01tb.signin='3')，
    //                 設定為不參加聯合派訓 (t04tb.notice='N')。
    //             */
    //             if ($t01tb->signin == 3 && $action == "insert"){
    //                 $t04tb["notice"] = "N";
    //             }

    //             /*
    //                 更新
    //                【t04tb 開班資料檔】
    //                 t04tb.pubsdate 薦送報名開始日期
    //                 t04tb.pubedate 薦送報名結束日期
    //             */

    //             $pubdate_info = $this->getPubDate($t01tb, $t04tb, $action);

    //             // 計算完畢轉回 民國格式
    //             $t04tb["sdate"] = Common::dateCeToRocFormat($t04tb["sdate"]);
    //             $t04tb["edate"] = Common::dateCeToRocFormat($t04tb["edate"]);

    //             if ($pubdate_info != false){
    //                 $t04tb = array_merge($t04tb, $pubdate_info);
    //             }

    //             if ($action == "insert"){
    //                 $this->t04tbRepository->insert($t04tb);
    //             }else{
    //                 $old_t04tb = $this->t04tbRepository->find($t04tb_info);

    //                 if (empty($old_t04tb)){
    //                     return false;
    //                 }

    //                 $this->t04tbRepository->update($t04tb_info, $t04tb);
    //             }

    //             // 為了計算先轉回西元格式
    //             $t04tb["sdate"] = Common::dateRocToCeFormat($t04tb["sdate"]);
    //             $t04tb["edate"] = Common::dateRocToCeFormat($t04tb["edate"]);

    //             if ($action == "insert"){
    //                 // 新增 預設課程 [t06tb] 課程表資料檔
    //                 $this->insertDefaultT06tb($t04tb);

    //                 // [t03tb] 各期參訓單位報名檔】轉至【t51tb] 薦送報名分配檔
    //                 $this->addT51tbFromT03tb($t04tb);

    //                 if ($t04tb["site_branch"] == 2){
    //                     $scheme_dates = $this->getSchemeDate($t04tb["sdate"], $t04tb["edate"]);

    //                     foreach($scheme_dates as $scheme_date){
    //                         $scheme_date['class'] = $t04tb["class"];
    //                         $scheme_date['term'] = $t04tb["term"];
    //                         $this->classWeekRepository->insert($scheme_date);
    //                     }
    //                 }

    //             }

    //             $t04tb_info = [
    //                 'class' => $t04tb['class'],
    //                 'term' => $t04tb['term']
    //             ];
    //            /*
    //                 更新[t51tb]
    //                 t04tb.pubsdate 薦送報名開始日期
    //                 t04tb.pubedate 薦送報名結束日期
    //             */
    //             $this->t51tbRepository->update($t04tb_info, $pubdate_info);

    //             $train_days = $this->checkTrainDateTime($t01tb, ["start" => $t04tb["sdate"], "end" => $t04tb["edate"]]);
    //             $times = (empty($t04tb['time'])) ? "D" : $t04tb['time'];
    //             $times = TrainSchedule::getTime($times);

    //             // 新增/更新 入口網站班別資料檔
    //             $this->storeT47tb($t01tb, $t04tb, $action);

    //             // 預約場地
    //             if ($action == "insert"){
    //                 $this->reserveSite($t04tb, null, $train_days, $times, $action);
    //                 $this->storeT36tb($t04tb, $train_days);
    //             }else if ($action == "update"){

    //                 $old_t04tb->roc_sdate = Common::dateRocToCeFormat($old_t04tb->sdate);
    //                 $old_t04tb->roc_edate = Common::dateRocToCeFormat($old_t04tb->edate);
    //                 // 如果變動主教室 或者起訖日不同
    //                 if (
    //                     ($old_t04tb->roc_sdate != $t04tb['sdate'] && $old_t04tb->roc_edate != $t04tb['edate']) ||
    //                     ($old_t04tb->site != $t04tb['site'] && $old_t04tb->site_branch != $t04tb['site_branch'])
    //                    ){
    //                     // 新增/更新 行事曆
    //                     $this->storeT36tb($t04tb, $train_days);
    //                     $this->reserveSite($t04tb, $old_t04tb->site, $train_days, $times, $action);
    //                 }

    //                 // 如果修改主教室的話，同步至課程表處理的主教室，並清掉課程表處理的實際教室預約資料，以新的主教室重新預約
    //                 if ($old_t04tb->site != $t04tb['site'] && $old_t04tb->site_branch != $t04tb['site_branch']) {
    //                     $this->t06tbRepository->update($t04tb_info, ['site' => $t04tb['site'], 'site_branch' => $t04tb['site_branch']]);
    //                 }

    //             }

    //         }

    //         // DB::rollback();

    //         DB::commit();
    //         // all good
    //         $status = true;
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         $status = false;
    //         var_dump($e->getMessage());
    //         die;
    //         // something went wrong
    //     }
    //     // die;
    //     return $status;
    // }

    public function computeApplyTime($t01tb, $sdate)
    {

        $t47tb = [];
        $origin_sdate = clone $sdate;

        $sub_day = 45;
        if ($t01tb->process == 2){ // 委辦班別
            $sub_day = 60;
        }elseif ($t01tb->type == "13"){ // 游於藝講堂
            $sub_day = 15;
        }

        $sdate->modify("-{$sub_day} day");
        $t47tb['sdate'] = Common::dateCeToRocFormat($sdate->format("Ymd"));

        $sub_day = 15;
        if ($t01tb->process == 2){ // 委辦班別
            $sub_day = 30;
        }elseif ($t01tb->type == "13"){ // 游於藝講堂
            $sub_day = 1;
        }

        $sdate = clone $origin_sdate;
        $sdate->modify("-{$sub_day} day");
        $t47tb['edate'] = Common::dateCeToRocFormat($sdate->format("Ymd"));
        return $t47tb;
    }

    public function getPubDate($t01tb, $t04tb, $action)
    {
        $pubsdate = "";
        $pubedate = "";

        /*
        報名日期:
         IF 班別為開放自由報名班期(t01tb.signin=3)
            報名開始日期公式:開訓當天
            報名結束日期公式:開訓當天下午六時
         Else
            報名開始日期公式:開訓當月之前2個月1日
            報名結束日期公式:開訓當月之前1個月1日下午六時
        */

        // 編輯時如果 不聯合派訓 則不更新
        if ($action == "update" && $t04tb['notice'] == "N"){
            return false;
        }

        if ($t01tb->signin == 3){
            $pubsdate = Common::dateCeToRocFormat($t04tb['sdate']->format('Ymd'));
            $pubedate = Common::dateCeToRocFormat($t04tb['sdate']->format('Ymd'));
        }else{
            $t04tb['sdate']->modify('-2 month');
            $pubsdate = Common::dateCeToRocFormat($t04tb['sdate']->format('Ym01'));
            $t04tb['sdate']->modify('+1 month');
            $pubedate = Common::dateCeToRocFormat($t04tb['sdate']->format('Ym01'));
        }

        return compact(['pubsdate', 'pubedate']);
    }

    // public function validateSchedule($t04tb, $action)
    // {
    //     // 檢查是否已有開課資料
    //     $t04tb_info = [
    //         'class' => $t04tb['class'],
    //         'term' => $t04tb['term']
    //     ];

    //     $exist_t04tb = $this->t04tbRepository->find($t04tb_info);

    //     if (empty($exist_t04tb)){
    //         if($action == "update"){
    //             return [
    //                 "status" => 1,
    //                 "message" => "找不到開班資料!"
    //             ];
    //         }
    //     }else{
    //         if ($action == "insert"){
    //             return [
    //                 "status" => 0,
    //                 "message" => "資料重複(已有此班的開班資料)!"
    //             ];
    //         }
    //     }

    //     $t01tb = $this->t01tbRepository->find($t04tb['class']);

    //     if (empty($t01tb)){
    //         return [
    //             "status" => 3,
    //             "message" => "找不到該班基本資料檔"
    //         ];
    //     }

    //     // 轉換日期格式
    //     $t04tb["sdate"] = Common::dateRocToCeFormat($t04tb["sdate"]);
    //     $t04tb["edate"] = Common::dateRocToCeFormat($t04tb["edate"]);

    //     /*
    //         檢查開課日期 與 結束日期 是否正確
    //         依照 t01tb[班別基本資料檔]上課方式 檢查
    //     */

    //     $train_days = $this->checkTrainDateTime($t01tb, ["start" => $t04tb["sdate"], "end" => $t04tb["edate"]]);

    //     if ($train_days == false){
    //         return [
    //             "status" => 4,
    //             "message" => "開課日期與上課方式不符!上課方式為".config("app.style")[$t01tb->style]
    //         ];
    //     }

    //     // 檢查開課日期是否已過確認凍結日
    //     $check_reuslt = $this->checkOutOfAffirmDate($t04tb["sdate"]);

    //     if ($check_reuslt == false){
    //         return [
    //             "status" => 4,
    //             "message" => "上課日期已過確認凍結日期"
    //         ];
    //     }

    //     // 檢查場地資訊

    //     if ($t04tb["site_branch"] == 1){
    //         $site = $this->m14tbRepository->find($t04tb["site"]);
    //         if (!empty($site)){
    //             // 如果教室為 會議室
    //             if ($site->type == 2){
    //                 $check_reuslt = $this->checkOutOfRequestDate($t04tb["sdate"]);
    //                 if ($check_reuslt == true){
    //                     return [
    //                         "status" => 4,
    //                         "message" => "上課日期過了需求凍結日期，不可預約會議室。"
    //                     ];
    //                 }
    //             }
    //             // 檢查【t22tb 場地預約檔】的場地預約是否已經被預約
    //             if (!empty($t04tb['site'])){
    //                 $check_result = $this->checkSite(
    //                     $train_days,
    //                     $t04tb,
    //                     "D"
    //                 );
    //             }
    //         }
    //     }

    //     if (!empty($check_result)){
    //         dd($check_result);
    //     }

    //     // 檢查是否【t36tb 行事曆檔】是否有資料
    //     if ($action == "insert"){
    //         $check_result = $this->t36tbRepository->checkexist($t01tb->class, $t04tb['term']);
    //         if ($check_result){
    //             return [
    //                 "status" => 4,
    //                 "message" => "行事曆檔已有此班資料，請重新輸入!"
    //             ];
    //         }
    //     }

    //     return [
    //         "status" => 5,
    //         "message" => "ok!"
    //     ];
    // }

    /*
        檢查上課時間及結束時間是否合理
    */
    // public function checkTrainDateTime($t01tb, $train_date)
    // {
    //     /*
    //         0 => 星期日, 1 => 星期一 ....以此類推
    //     */
    //     $style = [
    //         1 => [false, true, true, true, true, true, false],
    //         2 => [false, true, false, true, false, true, false],
    //         3 => [false, false, true, false, true, false, false]
    //     ];

    //     $style[4] = [
    //         0 => ($t01tb->time7 == "Y"),
    //         1 => ($t01tb->time1 == "Y"),
    //         2 => ($t01tb->time2 == "Y"),
    //         3 => ($t01tb->time3 == "Y"),
    //         4 => ($t01tb->time4 == "Y"),
    //         5 => ($t01tb->time5 == "Y"),
    //         6 => ($t01tb->time6 == "Y"),
    //     ];

    //     // 是否包含假日
    //     if ($t01tb->holiday == "Y"){
    //         foreach ($style as $key => $type){
    //             $style[$key][0] = true;
    //             $style[$key][6] = true;
    //         }
    //     }

    //     $style = $style[$t01tb->style];

    //     $train_start = new DateTime($train_date["start"]);
    //     $train_end = new DateTime($train_date["end"]);


    //     $day = $t01tb->day;
    //     $days = [];
    //     while($day > 0){
    //         if($style[(int)$train_start->format("w")]){
    //             $day--;
    //             $days[] = $train_start->format("Ymd");
    //         }

    //         if ($day > 0){
    //             $train_start->modify("+1 day");
    //         }
    //     }

    //     if ($train_start->format("Y-m-d") == $train_end->format("Y-m-d")){
    //         return $days;
    //     }else{
    //         return false;
    //     }

    // }
    /*
        檢查有無超過需求凍結日
    */
    // public function checkOutOfAffirmDate($sdate){
    //     $affirm_date = TrainSchedule::getAffirmDate($sdate);
    //     $sdate = new DateTime($sdate);
    //     return $sdate->getTimeStamp() >= $affirm_date->getTimeStamp();
    // }
    // /*
    //     檢查有無超過需求確認日
    // */
    // public function checkOutOfRequestDate($sdate){
    //     $request_date = TrainSchedule::getRequestDate($sdate);
    //     $sdate = new DateTime($sdate);
    //     return $sdate->getTimeStamp() >= $request_date->getTimeStamp();
    // }


    public function checkSite($check_dates, $t04tb, $time_type){
        if ($time_type == "D"){
            $times = array(
                "A" => config("t22tbtimes.times")["A"],
                "B" => config("t22tbtimes.times")["B"]
            );
        }else if ($time_type == "E"){
            $times = array(
                "A" => config("t22tbtimes.times")["A"],
                "B" => config("t22tbtimes.times")["B"],
                "C" => config("t22tbtimes.times")["C"]
            );
        }else{
            $times = [
                $time_type => config("t22tbtimes.times")[$time_type]
            ];
        }

        $reserved_sites = [];

        // if (Site::checkFooHooa($t04tb['site'])) return false; // 福華場地 不預約

        foreach ($check_dates as $check_date){
            $check_date = Common::dateCeToRocFormat($check_date);
            foreach ($times as $time){

                if ($t04tb['site_branch'] == 1){  // 臺北
                    $check_result = $this->t22tbRepository->checkReserved($t04tb['class'], $t04tb['term'], $check_date, $t04tb['site'], $time);
                }else if ($t04tb['site_branch'] == 2){  // 南投
                    $check_result = $this->t97tbRepository->checkReserved($t04tb['class'], $t04tb['term'], $check_date, $t04tb['site'], $time);
                }

                if ($check_result->isEmpty() === false){
                    $reserved_sites[] = $check_result;
                }
            }
        }

        return $reserved_sites;
    }

    public function getT03tbSumQuota($class, $term){
        return $this->t03tbRepository->getSumQuota($class, $term);
    }

    public function getDeatil($queryData)
    {
        return $this->t36tbRepository->getForDetail($queryData)->groupBy('class');
    }

    public function updateT04tbBydetail($t04tb_info, $date)
    {
        $now = new DateTime();
        $t04tb = $this->t04tbRepository->find($t04tb_info);

        $t01tb = $t04tb->t01tb;

        $sdate = new TWDateTime();
        $sdate->setTWDateTime($date["sdate"]);

        $edate = Common::dateRocToCeFormat($date["edate"]);

        $train_days = $this->getTrainDate($t01tb, $sdate);
        if ($train_days == false){
            return [
                "status" => 4,
                "message" => "開課日期與上課方式不符!上課方式為".config("app.style")[$t01tb->style]
            ];
        }

        $affirm_date = TrainSchedule::getAffirmDate(clone $sdate);
        // 檢查開課日期是否已過確認凍結日
        if ($affirm_date->getTimeStamp() < $now->getTimeStamp()){
            return [
                "status" => 3,
                "message" => "上課日期已過確認凍結日期"
            ];
        }

        if ($t04tb->sdate == $date['sdate'] && $t04tb->edate == $date['edate']){
            return true;
        }

        $t04tb = $t04tb->toArray();
        DB::beginTransaction();

        try {
            $this->storeT36tb($t04tb, $train_days);
            $this->t04tbRepository->update($t04tb_info, $date);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;
            return false;
        }

    }

    public function deleteT04tb($class_info)
    {
        return false;
        DB::beginTransaction();
        try {
            $t04tb = $this->t04tbRepository->find($class_info);

            /*
                刪除 [t22tb] 場地預約檔
            */
            $this->t22tbRepository->delete([
                "class" => $t04tb->class,
                "term" => $t04tb->term
            ]);

            /*
                刪除 [t36tb] 場地預約檔
            */
            $this->t36tbRepository->delete([
                "class" => $t04tb->class,
                "term" => $t04tb->term
            ]);

            /*
                刪除 [t97tb] 南部場地預約檔
            */
            $this->t97tbRepository->delete([
                "class" => $t04tb->class,
                "term" => $t04tb->term
            ]);

            /*
                刪除 [t37tb]場地預約歷史紀錄
                1.type = 2 確認資料
                2.t04tb需求凍結日在今天以前 type = 1 需求資料
            */
            if (!empty($t04tb->site)){
                $this->t37tbRepository->clearHistory($t04tb->class, $t04tb->term, $t04tb->site);
            }

            // 刪除行事曆
            $this->t36tbRepository->delete([
                "class" => $t04tb->class,
                "term" => $t04tb->term
            ]);

            $this->t04tbRepository->delete($class_info);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;
            return false;
        }

    }

    public function reserveSite($t04tb, $origin_site, $train_days, $times, $action, $exist_data)
    {
        $t04tb_info = [
            'class' => $t04tb['class'],
            'term' => $t04tb['term']
        ];

        if ($action == "insert"){

        }elseif ($action == "update"){
            /*
                刪除 [t22tb] 場地預約檔 (為了重新產生)
            */
            $this->t22tbRepository->delete([
                "class" => $t04tb['class'],
                "term" => $t04tb['term']
            ]);

            /*
                刪除 [t97tb] 南部場地預約檔 (為了重新產生)
            */
            $this->t97tbRepository->delete([
                "class" => $t04tb['class'],
                "term" => $t04tb['term']
            ]);

            /*
                刪除 [t37tb]場地預約歷史紀錄
                1.type = 2 確認資料
                2.t04tb需求凍結日在今天以前 type = 1 需求資料
            */
            if (!empty($origin_site)){
                $this->t37tbRepository->clearHistory($t04tb['class'], $t04tb['class'], $origin_site);
            }else if (!empty($t04tb['site'])){
                $this->t37tbRepository->clearHistory($t04tb['class'], $t04tb['term'], $t04tb['site']);
            }
        }

        // if (Site::checkFooHooa($t04tb['site'], $t04tb['site_branch'])) return false; // 福華場地 不預約

        $m14tb = $this->m14tbRepository->find($t04tb['site']);

        // 場地預約
        $now = new DateTime();
        if (!empty($t04tb['site'])){
            foreach($train_days as $train_day){
                $request_date = TrainSchedule::getRequestDate(new DateTime($train_day));
                $affirm_date = TrainSchedule::getAffirmDate(new DateTime($train_day));

                $train_day = Common::dateCeToRocFormat($train_day);

                if (!empty($t04tb['site'] && !empty($t04tb["site_branch"]))){
                    foreach($times as $time => $setime){
                        $t22tb = [
                            "site" => $t04tb['site'],
                            "date" => $train_day,
                            "stime" => $setime['stime'],
                            "etime" => $setime['etime'],
                            "time" => $time,
                            "class" => $t04tb['class'],
                            "term" => $t04tb['term'],
                            "request" => Common::dateCeToRocFormat($request_date->format('Ymd')),
                            "affirm" => Common::dateCeToRocFormat($affirm_date->format('Ymd')),
                            "seattype" => "C",
                            "reserve" => $t04tb['sponsor'],
                            "status" => "N",
                            "cnt" => $t04tb['quota'],
                            "usertype" => 1,
                            "upddate" => date("Y-m-d H:i:s")
                        ];

                        if ($t04tb["site_branch"] == 1){
                            // 新增 t22tb
                            $this->t22tbRepository->insert($t22tb);
                            // 新增 t37tb 場地預約歷史檔
                            $today = new DateTime();

                            $t37tb = [
                                "site" => $t04tb['site'],
                                "date" => $train_day,
                                "stime" => $setime['stime'],
                                "etime" => $setime['etime'],
                                "time" => $time,
                                "cnt" => $t04tb['quota'],
                                "reserve" => $t04tb['sponsor'],
                                "class" => $t04tb['class'],
                                "term" => $t04tb['term'],
                                "seattype" => "C",
                                "fee" => TrainSchedule::getSiteFee($m14tb, $time, Common::dateRocToCeFormat($train_day)),
                                "request" => Common::dateCeToRocFormat($affirm_date->format('Ymd')),
                            ];

                            if ($request_date->getTimeStamp() < $now->getTimeStamp()){
                                // 新增需求 場地預約歷史紀錄
                                $t37tb["type"] = "1";
                                $this->t37tbRepository->insert($t37tb);
                            }

                            // 新增確認 場地預約歷史紀錄
                            $t37tb["type"] = "2";
                            $this->t37tbRepository->insert($t37tb);

                        }else if ($t04tb["site_branch"] == 2){
                            // 新增確認 [t97tb]南投場地預約檔
                            $t97tb = [
                                "site" => $t04tb['site'],
                                "date" => $train_day,
                                "stime" => $setime['stime'],
                                "etime" => $setime['etime'],
                                "time" => $time,
                                "class" => $t04tb['class'],
                                "term" => $t04tb['term'],
                            ];
                            $this->t97tbRepository->insert($t97tb);
                        }
                    }
                }
            }
        }
    }


    public function getSchemeDate($begin_date = '', $end_date = '')
    {
        $datediff = strtotime($end_date) - strtotime($begin_date);
        $datediff = floor($datediff/(60*60*24));
        $week_list =array();
        $w = '';
        for($i = 0; $i < $datediff + 1; $i++){
          $week_no = date('W',strtotime($begin_date . ' + ' . $i . 'day'));
          if($week_no != $w){
            if(date('w',strtotime($begin_date . ' + ' . $i . 'day')) != '7' && date('w',strtotime($begin_date . ' + ' . $i . 'day')) != '6'){
                $week_list[$week_no]['sdate'] = date("Y", strtotime($begin_date . ' + ' . $i . 'day'))-1911;
                $week_list[$week_no]['sdate'] .= date("md", strtotime($begin_date . ' + ' . $i . 'day'));
            }

            $w = $week_no;
          }
          if(date('w',strtotime($begin_date . ' + ' . $i . 'day')) == '5'){
            $week_list[$week_no]['edate'] = date("Y", strtotime($begin_date . ' + ' . $i . 'day'))-1911;
            $week_list[$week_no]['edate'] .= date("md", strtotime($begin_date . ' + ' . $i . 'day'));
          }
          if(strtotime(date("Y-m-d", strtotime($begin_date . ' + ' . $i . 'day'))) == strtotime($end_date)){
            $week_list[$week_no]['edate'] = date("Y", strtotime($begin_date . ' + ' . $i . 'day'))-1911;
            $week_list[$week_no]['edate'] .= date("md", strtotime($begin_date . ' + ' . $i . 'day'));
          }
        }

        return $week_list;
    }

    public function storeT47tb($t04tb, $t01tb, $action, $ce_sdate)
    {
        // 更新或新增【t47tb]入口網站班別資料檔
        if ($action == "insert"){
            $t47tb = [
                "class" => $t04tb['class'],
                "term" => $t04tb['term'],
                "degree" => 6,
                "summary" => "",
                "enroll" => "3",
                "validdate" => "",
                "county" => "",
                "site" => "",
                "sdate" => "",
                "edate" => "",
                "credit" => $t01tb->trainhour,
                "unit" => "1",
                "restriction" => "",
                "lodging" => "0",
                "meal" => "0",
                "upload2" => "N",
                "grade" => "N",
                "leave" => "N",
                "file1" => "",
                "file2" => "",
                "file3" => "",
                "file4" => "",
                "file5" => "",
                "remark" => ""
            ];

            if ($t04tb['site_branch'] == 1){
                $t47tb['county'] = '10';
            }else if ($t04tb['site_branch'] == 2){
                $t47tb['county'] = '54';
            }

            if ($t04tb['site_branch'] == 1){
                $t47tb['site'] = '臺北市新生南路3段30號';
            }else if ($t04tb['site_branch'] == 2){
                $t47tb['site'] = '南投市光明路1號';
            }

            $apply_time = $this->computeApplyTime($t01tb, clone $ce_sdate);
            $t47tb['sdate'] = $apply_time['sdate'];
            $t47tb['edate'] = $apply_time['edate'];

            $this->t47tbRepository->insert($t47tb);
        }elseif ($action == "update"){
            $apply_time = $this->computeApplyTime($t01tb, clone $ce_sdate);

            $this->t47tbRepository->update([
                "class" => $t04tb['class'],
                "term" => $t04tb['term']
            ], $apply_time);
        }
    }

    // 預設課程
    public function insertDefaultT06tb($t04tb){
        $t06tb = [
            'class' => $t04tb["class"],
            'term' => $t04tb["term"],
            'unit' => '',
            'hour' => 0,
            'course' => '01',
            'name' => '報到'
        ];

        $this->t06tbRepository->insert($t06tb);

        $t06tb = [
            'class' => $t04tb["class"],
            'term' => $t04tb["term"],
            'unit' => '',
            'hour' => 0,
            'course' => '02',
            'name' => '班務介紹'
        ];

        $this->t06tbRepository->insert($t06tb);
    }

    public function storeT36tb($t04tb, $train_days)
    {
        $t04tb_info = [
            'class' => $t04tb['class'],
            'term' => $t04tb['term']
        ];
        $t36tbs = $this->t36tbRepository->getByT04tb($t04tb_info)->keyBy('date')->toArray();

        foreach($train_days as &$train_day){
            $train_day = Common::dateCeToRocFormat($train_day);
            unset($train_day);
        }
        $delete = array_diff(array_keys($t36tbs), $train_days);

        $this->t36tbRepository->deleteByDate($t04tb_info, $delete);

        foreach($train_days as $train_day){
            // 新增[t36tb]行事曆檔
            $t36tb_info = [
                "class" => $t04tb['class'],
                "term" => $t04tb['term'],
                "date" => $train_day,
            ];

            $t36tb = [
                'site' => (empty($t04tb['site'])) ? '' : $t04tb['site'],
                'site_branch' => $t04tb['site_branch']
            ];

            if (isset($t36tbs[$train_day])){
                $this->t36tbRepository->update($t36tb_info, $t36tb);
            }else{
                $t36tb = array_merge($t36tb, $t36tb_info);
                $this->t36tbRepository->insert($t36tb);
            }

        }

    }

    public function addT51tbFromT03tb($new_t04tb, $exist_data)
    {
        $t04tb_info = [
            'class' => $new_t04tb['class'],
            'term' => $new_t04tb['term']
        ];

        $exist_data['t03tbs'] = $exist_data['t03tbs']->pluck('quota', 'organ');
        $exist_data['t51tbs'] = $exist_data['t51tbs']->pluck('quota', 'organ');

        $this->t51tbRepository->update($t04tb_info, ['quota' => 0]);

        if (isset($exist_data['t03tbs'])){
            foreach ($exist_data['t03tbs'] as $organ => $quota){
                $t51tb_info = $t04tb_info;
                if (isset($exist_data['grade1_m17tb'][$organ])){
                    $t51tb_info['organ'] = $exist_data['grade1_m17tb'][$organ];

                    if (isset($t51tbs[$t51tb_info['organ']])){
                        $this->t51tbRepository->update($t51tb_info, ['quota' => $quota]);
                    }else{
                        $t51tb_info['quota'] = $quota;
                        $this->t51tbRepository->insert($t51tb_info);
                    }
                }
            }
        }
        // $this->t03tbRepository->update(compact(['class', 'term']), ['is_online_update' => 1]);

        // dd($t03tbs, $t51tbs);
    }

    public function batchInsert($yerly)
    {
        // 檢查 各期參訓單位報名檔t03tb是否有資料，但不包括 游於藝班級 t01tb.type='13'
        $t03tbs_count = $this->t03tbRepository->getByYerlyCount($yerly);

        if ($t03tbs_count < 1){
            return [
                'status' => false,
                'message' => "各期參訓單位報名檔無 {$yerly} 年度的資料，無法執行批次新增"
            ];
        }

        // 檢查 t04tb開班資料檔是否有資料，但不包括 游於藝班級 t01tb.type='13'

        $t04tbs_count = $this->t04tbRepository->getByYerlyCount($yerly);

        if ($t04tbs_count){
            return [
                'status' => false,
                'message' => "已有 {$yerly} 年度的開班資料，無法執行批次新增"
            ];
        }

        DB::beginTransaction();

        try {
            // $this->t04tbRepository->insertFromT03tb($yerly);
            $t04tbInfos = $this->t03tbRepository->getScheduleBatchInsertData($yerly);

            foreach ($t04tbInfos as $t04tbInfo){
                $this->t04tbRepository->insert($t04tbInfo->toArray());
            }

            $t01tbs = $this->t01tbRepository->getINt03tb($yerly);

            foreach ($t01tbs as $t01tb){
                $t47tb = [
                    'class' => $t01tb->class,
                    'term' => $t01tb->term,
                    "degree" => 6,
                    "enroll" => "3",
                    "credit" => $t01tb->trainhour,
                    "unit" => "1",
                    "lodging" => "0",
                    "meal" => "0",
                    "upload2" => "N",
                    "grade" => "N",
                    "leave" => "N",                    
                ];

                $this->t47tbRepository->insert($t47tb);
            }


            DB::commit();
            return [
                'status' => true,
                'message' => "批次新增成功"
            ];
        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;            
            return [
                'status' => false,
                'message' => ""
            ];

        }

    }

    public function batchDelete($yerly)
    {
        DB::beginTransaction();

        try {
            $this->t04tbRepository->deleteByYerly($yerly);

            DB::commit();
            return [
                'status' => true,
                'message' => "批次刪除成功"
            ];
        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;            
            return [
                'status' => false,
                'message' => "批次刪除失敗"
            ];
            
            // return false;
            var_dump($e->getMessage());
            die;
        }


    }

    public function import($import_datas)
    {
        $new_t04tbs = $this->splitImportData($import_datas);
        $error_new_t04tbs = collect();
        $grade1_m17tb = $this->m17tbRepository->getGrade1Organ()->pluck('enrollorg', 'organ');

        $new_t04tbs_keys = $new_t04tbs->groupBy('class')
                                      ->map(function($class_group){
                                          return $class_group->pluck('term');
                                      });

        $t01tbs = $this->t01tbRepository->getByKeys('class', $new_t04tbs_keys->keys())
                                        ->keyBy('class');

        $t04tbs = $this->t04tbRepository->getByT04tbs($new_t04tbs_keys)
                                        ->groupBy('class')->map(function($class_group){
                                            return $class_group->groupBy('term');
                                        });

        // 篩選有問題的開班資料
        $new_t04tbs = $new_t04tbs->map(function($new_t04tb, $index) use(&$error_new_t04tbs, $t01tbs, $t04tbs){
            $isValidate = true;

            $validator = Validator::make($new_t04tb, [
                'class' => 'required',
                'term' => 'required',
                'sdate' => 'required'
            ],[
                'class.required' => '未輸入班號',
                'term.required' => '未輸入班級',
                'sdate.required' => '未輸入開班日期'
            ]);
            $errors = $validator->errors();
            if (!isset($t01tbs[$new_t04tb['class']])){
                $errors->add('error', '該班級不存在班級基本資料');
                $isValidate = false;
            }

            if (isset($t04tbs[$new_t04tb['class']][$new_t04tb['term']])){
                $errors->add('error', '該班級已存在');
                $isValidate = false;
            }

            $error_new_t04tbs[$index] = $errors;

            if (!$validator->fails() && $isValidate) {
                return $new_t04tb;
            }else{
                return null;
            }

        })->filter();

        if ($new_t04tbs->isEmpty()) return $error_new_t04tbs;


        $t03tbs = $this->t03tbRepository->getByT04tbs($new_t04tbs_keys)
                                        ->groupBy('class')->map(function($class_group){
                                            return $class_group->groupBy('term');
                                        });

        $quotas = $t03tbs->map(function($terms, $class) use(&$new_t04tbs){
            return $terms->map(function($t03tbs, $term) use($class, $new_t04tbs){
                return $t03tbs->sum('quota');
            });
        });

        $t51tbs = $this->t03tbRepository->getByT04tbs($new_t04tbs_keys)
                                        ->groupBy('class')->map(function($class_group){
                                            return $class_group->groupBy('term');
                                        });

        $check_result = [];

        DB::beginTransaction();

        try {

            foreach ($new_t04tbs as $index => $new_t04tb){
                $new_t04tb['quota'] = (isset($quotas[$new_t04tb['class']][$new_t04tb['term']])) ? $quotas[$new_t04tb['class']][$new_t04tb['term']] : 0;

                $exist_data = [];
                $exist_data['t04tb'] = (isset($t04tbs[$new_t04tb['class']][$new_t04tb['term']])) ? $t04tbs[$new_t04tb['class']][$new_t04tb['term']] : null;


                $exist_data['t01tb'] = (isset($t01tbs[$new_t04tb['class']])) ? $t01tbs[$new_t04tb['class']] : collect();
                $exist_data['t03tbs'] = (isset($t03tbs[$new_t04tb['class']][$new_t04tb['term']])) ? $t03tbs[$new_t04tb['class']][$new_t04tb['term']] : collect();
                $exist_data['grade1_m17tb'] = $grade1_m17tb;
                $exist_data['t51tbs'] = (isset($t51tbs[$new_t04tb['class']][$new_t04tb['term']])) ? $t51tbs[$new_t04tb['class']][$new_t04tb['term']] : collect();

                $result = $this->validateT04tb($new_t04tb, $exist_data);

                if ($result['status'] == 0){
                    $exist_data = array_merge($exist_data, $result['data']);
                    $this->storeT04tb($new_t04tb, $exist_data, "insert");
                }else{
                    $error_new_t04tbs[$index]->add('error', $result['message']);
                }


            }

            DB::commit();
            return $error_new_t04tbs;
            // DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;
        }
    }

    public function createT04tbImportExample()
    {
        $sponsors = $this->getSponsors();
        $class_rooms = $this->getClassRooms();

        // $class_rooms = [
        //     "m14tb" => $this->m14tbRepository->get([], false)->pluck("name", "site")->toArray(),
        //     "m25tb" => $this->m25tbRepository->get([], false)->pluck("name", "site")->toArray(),
        // ];

        $sections = $this->getSections();

        Excel::create('訓練排程匯入檔', function ($excel) use($sponsors, $class_rooms, $sections){

            $excel->sheet( '訓練排程資料', function ($sheet){
                $sheet->row(1,
                    ['班號', '期別', '開課日期', '主教室(台北)', '主教室(南投)', '主教室外地班', '辦班人員', '部門']
                );
            } );

            $excel->sheet( '選單資料', function ($sheet) use($sponsors, $class_rooms, $sections){
                $sheet->row(1, [ '教室(臺北)', '教室(南投)', '辦班人員', '部門' ]);

                $index = 2;
                foreach ($class_rooms['m14tb'] as $m14tb){
                    $sheet->SetCellValue( "A{$index}", $m14tb['name'].'  ['.$m14tb['site'].']');
                    $index++;
                }

                $index = 2;
                foreach ($class_rooms['m25tb'] as $m25tb){
                    $sheet->SetCellValue( "B{$index}", $m25tb['name'].'  ['.$m25tb['site'].']');
                    $index++;
                }

                $index = 2;
                foreach ($sponsors as $sponsor){
                    $sheet->SetCellValue( "C{$index}", $sponsor->username.'  ['.$sponsor->userid.']');
                    $index++;
                }

                $index = 2;
                foreach ($sections as $section){
                    $sheet->SetCellValue( "D{$index}", $section->section);
                    $index++;
                }
            } );

            $excel->setActiveSheetIndex(1);
            $sheet = $excel->getActiveSheet();

            $excel->addNamedRange(
                new \PHPExcel_NamedRange(
                    'room_Taipei',  $sheet, "A2:A".(count($class_rooms['m14tb']) + 1)
                )
            );

            $excel->addNamedRange(
                new \PHPExcel_NamedRange(
                    'room_Nantou',  $sheet, "B2:B".(count($class_rooms['m25tb']) + 1)
                )
            );

            $excel->addNamedRange(
                new \PHPExcel_NamedRange(
                    'sponsors',  $sheet, "C2:C".($sponsors->count() + 1)
                )
            );

            $excel->addNamedRange(
                new \PHPExcel_NamedRange(
                    'sections',  $sheet, "D2:D".($sections->count() + 1)
                )
            );

            $excel->setActiveSheetIndex(0);
            $sheet = $excel->getActiveSheet();

            for($i=2; $i<=11; $i++){
                $objValidation = $sheet->getCell("D{$i}")->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_STOP);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('資料錯誤，請勿任意修改');
                // $objValidation->setPromptTitle('Pick from list');
                // $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('room_Taipei'); //note this!
            }

            for($i=2; $i<=11; $i++){
                $objValidation = $sheet->getCell("E{$i}")->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_STOP);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('資料錯誤，請勿任意修改');
                // $objValidation->setPromptTitle('Pick from list');
                // $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('room_Nantou'); //note this!
            }

            for($i=2; $i<=11; $i++){
                $objValidation = $sheet->getCell("G{$i}")->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_STOP);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('資料錯誤，請勿任意修改');
                // $objValidation->setPromptTitle('Pick from list');
                // $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('sponsors'); //note this!
            }

            for($i=2; $i<=11; $i++){
                $objValidation = $sheet->getCell("H{$i}")->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_STOP);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('資料錯誤，請勿任意修改');
                // $objValidation->setPromptTitle('Pick from list');
                // $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('sections'); //note this!
            }
            // dd($excel->getSheet(0));


         } )->export( 'xlsx' );
    }

    /*
        解析匯入檔
    */
    public function splitImportData($import_datas)
    {
        $import_format = $this->getImportDataFormat();
        // 移除標題列
        unset($import_datas[0]);
        $new_t04tb = [];

        foreach ($import_datas as $index => $data){

            if (empty(array_filter($data))){
                continue;
            }

            $t04tb = [];
            foreach ($import_format as $key => $format){
                $t04tb[$format] = $data[$key];
            }

            $t04tb['class'] = (gettype($t04tb['class']) == "double") ? (string)(int)$t04tb['class'] : $t04tb['class'];
            $t04tb['term'] = (empty($t04tb['term'])) ? null : str_pad((string)(int)$t04tb['term'], 2, "0", STR_PAD_LEFT);
            $t04tb['sdate'] = str_pad((string)(int)$t04tb['sdate'], 7, "0", STR_PAD_LEFT);

            $t04tb['sponsor'] = preg_replace("/\].*/", "", $t04tb['sponsor'], 1);
            $t04tb['sponsor'] = preg_replace("/.*\[/", "", $t04tb['sponsor'], 1);

            $t04tb['section'] = preg_replace("/\].*/", "", $t04tb['section'], 1);
            $t04tb['section'] = preg_replace("/.*\[/", "", $t04tb['section'], 1);

            if (!empty($t04tb['site_taipei'])){
                $t04tb['site'] = preg_replace("/\].*/", "", $t04tb['site_taipei'], 1);
                $t04tb['site'] = preg_replace("/.*\[/", "", $t04tb['site'], 1);
                $t04tb['site_branch'] = 1;
            }elseif (!empty($t04tb['site_nantou'])){
                $t04tb['site'] = preg_replace("/\].*/", "", $t04tb['site_nantou'], 1);
                $t04tb['site'] = preg_replace("/.*\[/", "", $t04tb['site'], 1);
                $t04tb['site_branch'] = 2;
            }elseif (!empty($t04tb['location'])){
                $t04tb['site_branch'] = 3;
                $t04tb['site'] = null;
            }else{
                $t04tb['site'] = null;
                $t04tb['site_branch'] = null;
            }

            unset($t04tb['site_taipei']);
            unset($t04tb['site_nantou']);

            $new_t04tb[$index] = $t04tb;
        }

        return collect($new_t04tb);
    }

    public function getImportDataFormat()
    {
        return [
            'class', 'term', 'sdate', 'site_taipei', 'site_nantou', 'location', 'sponsor', 'section'
        ];
    }

    public function getExistData($new_t04tbs)
    {

        $new_t04tbs_keys = $new_t04tbs->groupBy('class')
                                      ->map(function($class_group){
                                          return $class_group->pluck('term');
                                      });

        $t01tbs = $this->t01tbRepository->getByKeys('class', $new_t04tbs_keys->keys())
                                        ->keyBy('class');

        $t04tbs = $this->t04tbRepository->getByT04tbs($new_t04tbs_keys)
                                        ->groupBy('class')
                                        ->map(function($class_group){
                                           return $class_group->keyBy('term');
                                        });

        $t03tbs = $this->t03tbRepository->getByT04tbs($new_t04tbs_keys)
                                        ->groupBy('class')->map(function($class_group){
                                            return $class_group->groupBy('term');
                                        });

        $quotas = $t03tbs->map(function($terms, $class) use(&$new_t04tbs){
            return $terms->map(function($t03tbs, $term) use($class, $new_t04tbs){
                return $t03tbs->sum('quota');
            });
        });

        $grade1_m17tb = $this->m17tbRepository->getGrade1Organ()->pluck('enrollorg', 'organ');

        return compact(['t01tbs', 't04tbs', 't03tbs', 'quotas', 'grade1_m17tb']);
    }

    public function validateT04tb($new_t04tb, $exist_data)
    {
        $now = new DateTime();
        if (empty($exist_data['t01tb'])){
            return [
                "status" => 1,
                "message" => "找不到該班基本資料檔"
            ];
        }

        // 轉換日期格式
        $ce_sdate = new DateTime(Common::dateRocToCeFormat($new_t04tb["sdate"]));

        /*
            檢查開課日期 與 結束日期 是否正確
            依照 t01tb[班別基本資料檔]上課方式 檢查
        */

        // 取得訓練日期
        $train_days = $this->getTrainDate($exist_data['t01tb'], clone $ce_sdate);

        if ($train_days == false){
            return [
                "status" => 2,
                "message" => "開課日期與上課方式不符!上課方式為".config("app.style")[$exist_data['t01tb']->style]
            ];
        }

        $affirm_date = TrainSchedule::getAffirmDate(clone $ce_sdate);

        
        // 假設有更動開課日期、教室、人數 允許修改辦班人員、部門及帶班輔導員
        if (
            !(isset($exist_data['t04tb']) && 
            $exist_data['t04tb']->sdate == $new_t04tb['sdate'] && 
            $exist_data['t04tb']->site_branch == $new_t04tb['site_branch'] &&
            $exist_data['t04tb']->site == $new_t04tb['site'] &&
            $exist_data['t04tb']->quota == $new_t04tb['quota'])
        ){
            // 檢查開課日期是否已過確認凍結日
            if ($affirm_date->getTimeStamp() < $now->getTimeStamp()){
                return [
                    "status" => 3,
                    "message" => "上課日期已過確認凍結日期"
                ];
            }            
        }


        $request_date = TrainSchedule::getRequestDate(clone $ce_sdate);
        // 檢查場地資訊

        if ($new_t04tb["site_branch"] == 1){
            $site = $this->m14tbRepository->find($new_t04tb["site"]);
            if (!empty($site)){
                // 如果教室為 會議室
                if ($site->type == 2){
                    if ($request_date->getTimeStamp() < $now->getTimeStamp()){
                        return [
                            "status" => 4,
                            "message" => "上課日期過了需求凍結日期，不可預約會議室。"
                        ];
                    }
                }

                $times = (empty($new_t04tb['times'])) ? 'D' : $new_t04tb['times'];

                // 檢查【t22tb 場地預約檔】的場地預約是否已經被預約
                if (!empty($new_t04tb['site'])){
                    $check_result = $this->checkSite($train_days, $new_t04tb, $times);
                }
            }
        }

        if (!empty($check_result)){
            return [
                "status" => 5,
                "message" => "場地已被預約"
            ];
        }

        // 檢查是否【t36tb 行事曆檔】是否有資料

        return [
            "status" => 0,
            "message" => "ok!",
            "data" => compact(['train_days', 'times', 'affirm_date', 'request_date', 'ce_sdate'])
        ];
    }

    public function getTrainDate($t01tb, $sdate)
    {
        /*
            0 => 星期日, 1 => 星期一 ....以此類推
        */
        $style = [
            1 => [false, true, true, true, true, true, false],
            2 => [false, true, false, true, false, true, false],
            3 => [false, false, true, false, true, false, false]
        ];

        $style[4] = [
            0 => ($t01tb->time7 == "Y"),
            1 => ($t01tb->time1 == "Y"),
            2 => ($t01tb->time2 == "Y"),
            3 => ($t01tb->time3 == "Y"),
            4 => ($t01tb->time4 == "Y"),
            5 => ($t01tb->time5 == "Y"),
            6 => ($t01tb->time6 == "Y"),
        ];

        // 是否包含假日
        if ($t01tb->holiday == "Y"){
            foreach ($style as $key => $type){
                $style[$key][0] = true;
                $style[$key][6] = true;
            }
        }

        $style = $style[$t01tb->style];

        $day = $t01tb->day;
        $days = [];
        $is_first_day = true;

        while($day > 0){
            if($style[(int)$sdate->format("w")]){
                $is_first_day = false;
                $day--;
                $days[] = $sdate->format("Ymd");
            }elseif ($is_first_day){
                return false;
            }
            $sdate->modify("+1 day");
        }

        return $days;
    }

    public function storeT04tb($new_t04tb, $exist_data, $action)
    {
            $t04tb_info = [
                'class' => $new_t04tb['class'],
                'term' => $new_t04tb['term']
            ];

            // 為了計算先轉回西元格式
            $new_t04tb["edate"] = $exist_data['train_days'][count($exist_data['train_days']) - 1];

            $ce_edate = new DateTime($new_t04tb["edate"]);
            $new_t04tb["edate"] = Common::dateCeToRocFormat($new_t04tb["edate"]);

            /*
                若班別為開放自由報名班期 (t01tb.signin='3')，
                設定為不參加聯合派訓 (t04tb.notice='N')。
            */
            if ($exist_data['t01tb']->signin == 3 && $action == "insert"){
                $new_t04tb["notice"] = "N";
            }elseif ($action == "update"){
                $new_t04tb["notice"] = $exist_data['t04tb']->notice;
            }else{
                $new_t04tb["notice"] = null;
            }
            // dd($exist_data['t04tb']->sdate);
            // dd($new_t04tb['sdate']);

            /*
                更新
                【t04tb 開班資料檔】
                t04tb.pubsdate 薦送報名開始日期
                t04tb.pubedate 薦送報名結束日期
            */
            // dd($new_t04tb);
            $pubdate_info = $this->getPubDate($exist_data['t01tb'], ['sdate' => clone $exist_data['ce_sdate'], 'notice' => $new_t04tb['notice']], $action);

            // 計算完畢轉回 民國格式

            if ($pubdate_info != false){
                $new_t04tb = array_merge($new_t04tb, $pubdate_info);
            }

            $remarkDefault = config('database_fields.t04tb.remarkDefault.branch')[$exist_data['t01tb']->branch];

            // dd(str_replace("{@counselorInfo}", "test", $remarkDefault));
            $counselorTel = ($exist_data['t01tb']->branch == 1) ? '02-83691399' : '049-2332131';

            $new_t04tb['counselor'] = empty($new_t04tb['counselor']) ? '' : $new_t04tb['counselor'];

            $newCounselor = $this->m09tbRepository->find(['userid' => $new_t04tb['counselor']]);

            if (empty($newCounselor)){
                $new_t04tb['remark'] = str_replace("{@counselorInfo}", '', $remarkDefault);
            }else{
                if ($exist_data['t01tb']->branch == 1){
                    $new_t04tb['remark'] = str_replace("{@counselorInfo}", '（三）帶班輔導員： '.$newCounselor->username." {$counselorTel} 分機 ".$newCounselor->ext.'。', $remarkDefault);
                }else{
                    $new_t04tb['remark'] = str_replace("{@counselorInfo}", '、帶班輔導員： '.$newCounselor->username." {$counselorTel} 分機 ".$newCounselor->ext, $remarkDefault);
                }
            }

            if ($action == "insert"){
                $this->t04tbRepository->insert($new_t04tb);
            }else{

                if (empty($exist_data['t04tb'])){
                    return false;
                }

                if ($exist_data['t04tb']->counselor == $new_t04tb['counselor']){
                    unset($new_t04tb['remark']);
                }

                $this->t04tbRepository->update($t04tb_info, $new_t04tb);
            }

            if ($action == "insert"){
                // 新增 預設課程 [t06tb] 課程表資料檔
                $this->insertDefaultT06tb($new_t04tb);
                // [t03tb] 各期參訓單位報名檔】轉至【t51tb] 薦送報名分配檔
                $this->addT51tbFromT03tb($new_t04tb, $exist_data);

                if ($new_t04tb["site_branch"] == 2){
                    $scheme_dates = $this->getSchemeDate($exist_data['ce_sdate']->format('Ymd'), $ce_edate->format('Ymd'));

                    foreach($scheme_dates as $scheme_date){
                        $scheme_date['class'] = $new_t04tb["class"];
                        $scheme_date['term'] = $new_t04tb["term"];
                        $this->classWeekRepository->insert($scheme_date);
                    }
                }

            }else{
                if ($new_t04tb["site_branch"] == 2){

                    $queryData['class'] = $new_t04tb["class"];
                    $queryData['term'] = $new_t04tb["term"];

                    if($exist_data['t04tb']->sdate != $new_t04tb['sdate']){

                        $this->classWeekRepository->deleteWeek($queryData);

                        $scheme_dates = $this->getSchemeDate($exist_data['ce_sdate']->format('Ymd'), $ce_edate->format('Ymd'));

                        foreach($scheme_dates as $scheme_date){
                            $scheme_date['class'] = $new_t04tb["class"];
                            $scheme_date['term'] = $new_t04tb["term"];
                            $this->classWeekRepository->insert($scheme_date);
                        }

                    }else if($exist_data['t04tb']->sdate == $new_t04tb['sdate']){
                        $classWeekExist = $this->classWeekRepository->get_exist($queryData);
                        if($classWeekExist === true){
                            $scheme_dates = $this->getSchemeDate($exist_data['ce_sdate']->format('Ymd'), $ce_edate->format('Ymd'));

                            foreach($scheme_dates as $scheme_date){
                                $scheme_date['class'] = $new_t04tb["class"];
                                $scheme_date['term'] = $new_t04tb["term"];
                                $this->classWeekRepository->insert($scheme_date);
                            }
                        }
                    }

                }
            }

            $t04tb_info = [
                'class' => $new_t04tb['class'],
                'term' => $new_t04tb['term']
            ];

            /*
                更新[t51tb]
                t04tb.pubsdate 薦送報名開始日期
                t04tb.pubedate 薦送報名結束日期
            */

            if ($pubdate_info !== false){
                $this->t51tbRepository->update($t04tb_info, $pubdate_info);
            }


            $times = (empty($new_t04tb['time'])) ? "D" : $new_t04tb['time'];
            $times = TrainSchedule::getTime($times);


            // 新增/更新 入口網站班別資料檔
            $this->storeT47tb($new_t04tb, $exist_data['t01tb'], $action, $exist_data['ce_sdate']);
            // 預約場地
            if ($action == "insert"){
                $this->reserveSite($new_t04tb, null, $exist_data['train_days'], $times, $action, $exist_data);
                $this->storeT36tb($new_t04tb, $exist_data['train_days']);
            }else if ($action == "update"){

                // 如果變動主教室 或者起訖日不同
                if (
                    ($exist_data['t04tb']->sdate != $new_t04tb['sdate'] || $exist_data['t04tb']->edate != $new_t04tb['edate']) ||
                    ($exist_data['t04tb']->site != $new_t04tb['site'] || $exist_data['t04tb']->site_branch != $new_t04tb['site_branch'])
                    ){
                    // 新增/更新 行事曆
                    $this->storeT36tb($new_t04tb, $exist_data['train_days']);
                    $this->reserveSite($new_t04tb, $exist_data['t04tb']->site, $exist_data['train_days'], $times, $action, $exist_data);
                }

                // 如果修改主教室的話，同步至課程表處理的主教室，並清掉課程表處理的實際教室預約資料，以新的主教室重新預約
                if ($exist_data['t04tb']->site != $new_t04tb['site'] && $exist_data['t04tb']->site_branch != $new_t04tb['site_branch']) {
                    $this->t06tbRepository->update($t04tb_info, ['site' => $new_t04tb['site'], 'branch' => $new_t04tb['site_branch']]);
                }

            }

    }
}
