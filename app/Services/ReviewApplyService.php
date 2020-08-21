<?php
namespace App\Services;

use App\Repositories\T27tbRepository;
use App\Repositories\T04tbRepository;
use App\Repositories\M13tbRepository;
use App\Repositories\M17tbRepository;
use App\Repositories\T01tbRepository;
use App\Repositories\T51tbRepository;
use App\Repositories\T13tbRepository;
use App\Repositories\M02tbRepository;
use App\Repositories\T40tbRepository;

use App\Helpers\Des;
use App\Helpers\Common;
use App\Helpers\SystemParam;

use DB;
use DateTime;
use Validator;

class ReviewApplyService
{
    public function __construct(
        T27tbRepository $t27tbRepository,
        T04tbRepository $t04tbRepository,
        M13tbRepository $m13tbRepository,
        M17tbRepository $m17tbRepository,
        T01tbRepository $t01tbRepository,
        T51tbRepository $t51tbRepository,
        T13tbRepository $t13tbRepository,
        M02tbRepository $m02tbRepository,
        T40tbRepository $t40tbRepository
    )
    {
        $this->t27tbRepository = $t27tbRepository;
        $this->t04tbRepository = $t04tbRepository;
        $this->m13tbRepository = $m13tbRepository;
        $this->m17tbRepository = $m17tbRepository;
        $this->t01tbRepository = $t01tbRepository;
        $this->t51tbRepository = $t51tbRepository;
        $this->t13tbRepository = $t13tbRepository;
        $this->m02tbRepository = $m02tbRepository;
        $this->t40tbRepository = $t40tbRepository;
    }

    /**
     * 取得開班資料列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */

    public function getOpenClassList($queryData = [])
    {
        return $this->t04tbRepository->getByQueryList($queryData);
    }

    public function getT04tb($t04tb_info)
    {
        return $this->t04tbRepository->find($t04tb_info, '*', ['t01tb']);
    }

    public function getT01tbs()
    {
        return $this->t01tbRepository->getData(null, "class, name");
    }
    
    public function getT27tbs($t04tb_info, $queryData)
    {
        $queryData = array_merge($queryData, $t04tb_info);
        $t27tbs = $this->t27tbRepository->get($queryData, false);

        foreach($t27tbs as $t27tb){
            $t27tb->des_idno = Des::encode($t27tb->idno, 'KLKLK');
        }

        return $t27tbs;
    }

    public function getT27tb($t27tb_info)
    {
        $t27tb = $this->t27tbRepository->find($t27tb_info);
        $t27tb->des_idno = Des::encode($t27tb->idno, 'KLKLK');
        return $t27tb;
    }

    public function getM13tbs()
    {
        return $this->m13tbRepository->getData();
    }

    public function getM17tbs()
    {
        return $this->m17tbRepository->getData();
    }    

    public function deleteT27tb($t27tb_info)
    {
        return $this->t27tbRepository->delete($t27tb_info);
    }

    public function storeT27tb($t27tb, $action, $t27tb_info)
    {
        $checkbox = ['dorm', 'extradorm', 'nonlocal', 'handicap', 'chief', 'personnel', 'aborigine'];

        for($i=0;$i<count($checkbox);$i++){
            $t27tb[$checkbox[$i]] = (empty($t27tb[$checkbox[$i]])) ? 'N' : 'Y';
        }
        // $t27tb['birth'] = (string)((int)$t27tb['birth']);
        $t27tb['ecode'] = (string)((int)$t27tb['ecode']);

        $t27tb = array_merge($t27tb, $t27tb_info);

        if ($action == "insert"){
            return $this->t27tbRepository->insert($t27tb);
        }elseif ($action == "update"){
            return $this->t27tbRepository->update($t27tb_info, $t27tb);
        }
    }

    public function storeT51tb($t51tb_data, $action, $t51tb_info=null)
    {
        return $this->t51tbRepository->update($t51tb_info, $t51tb_data);
    }

    public function getAssignData($t04tb_info)
    {
        // $m17tb = $this->m17tbRepository->getByT04tb();
        $t51tbs = $this->t51tbRepository->getByT04tb($t04tb_info);
        // $this->t51tbRepository->get();

        // 主管機關
        // $competent_authoritys = $this->m17tbRepository->getCompetentAuthoritys();
        // $enrollorgs = $competent_authoritys->pluck('organ','enrollorg');
        // dd($t51tbs->groupBy('m17tb_uporgan')[""]->keyBy('m17tb_organ')->pluck('m17tb_enrollname'));
        return $t51tbs->groupBy('m17tb_uporgan');
    }

    public function check_apply($class_info, $check_class)
    {
        $check_t27tbs = $this->t13tbRepository->getCheckApply($class_info, $check_class);
        return $check_t27tbs;
    }

    public function getRepeatApplyForSdate($queryData)
    {
        return $this->t04tbRepository->getRepeatApplyForSdate($queryData);
    }

    public function getRepeatCount($copy_info)
    {
        $copyed_t04tb = $this->t04tbRepository->find($copy_info['copyed']);
        $copyed_t13tbs = $copyed_t04tb->t13tbs()->where('status', '=', 1)->get()->pluck('idno')->toArray();
    
        $copy_purpose_t04tb = $this->t04tbRepository->find($copy_info['copy_purpose']);
        $copy_purpose_t27tbs = $copy_purpose_t04tb->t27tbs->pluck('idno')->toArray(); 

        return !(count(array_diff($copy_purpose_t27tbs, $copyed_t13tbs)) == count($copy_purpose_t27tbs));
    }

    public function copy_apply($copy_info)
    {
        $t04tb = $this->t04tbRepository->find($copy_info['copy_purpose']);
        $t27tbs = $t04tb->t27tbs->keyBy('idno');

        $new_t27tbs = $this->t13tbRepository->getT2tbCopyInfo($copy_info['copyed'])->keyBy('idno')->toArray();

        DB::beginTransaction();

        try {
            
            if ($copy_info['copy_mode'] == 'clear_and_copy'){
                $this->t13tbRepository->delete($copy_info['copy_purpose']);
            }

            $now = new DateTime();

            foreach ($new_t27tbs as $new_t27tb){
                $new_t27tb['crtdate'] = $now->format('Y-m-d H:i:s');
                $new_t27tb['class'] = $copy_info['copy_purpose']['class'];
                $new_t27tb['term'] = $copy_info['copy_purpose']['term'];
                
                if (isset($t27tbs[$new_t27tb['idno']]) && $copy_info['over_data'] == 1){
                    $t27tbs[$new_t27tb['idno']]->update($new_t27tb);
                }else{
                    $new_t27tb['prove'] = 'N'; 
                    $this->t27tbRepository->insert($new_t27tb);
                }
            }

            // $copy_datas = $this->t27tbRepository->getCopyData($copy_info, $repeat_idnos);
            

            // foreach($copy_datas as $t27tb){
            //     $t27tb->class = $copy_info['copy_purpose']['class'];
            //     $t27tb->term = $copy_info['copy_purpose']['term'];
            //     $t27tb->crtdate = $now->format('Y-m-d H:i:s');
            //     $t27tb->prove = 'N';
            //     $this->t27tbRepository->insert($t27tb->toArray());
            // }
            // dd(DB::getQueryLog());
            DB::commit();
            // DB::rollback();
            // all good
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $status = false;
            var_dump($e->getMessage());
            die;
            // something went wrong
        }      

        return $status;
    }
    

    public function review($class_info, $prove){
        $shift_result = [];

        DB::beginTransaction();
        try {

            $t04tb =  $this->t04tbRepository->find($class_info);
            $success = [];
            $fail = [];

            foreach($prove as $idno => $prove){
                if ($prove == 'Y'){
                    $shift_result[$idno] = $this->shiftToT13tb($t04tb, $idno);
                    if($shift_result[$idno]['status']){
                        $success[] = $idno;
                    }else{
                        $fail[] = $idno;
                    }
                }
            }
            
            if (!empty($success)){
                $this->t27tbRepository->review($class_info, $success, 'S');
            }

            if (!empty($fail)){
                $this->t27tbRepository->review($class_info, $fail, 'N');
            }

            DB::commit();
            // DB::rollback();
            return [
                'status' => true, 
                'result' => $shift_result
            ];
        }catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;                
            return false;

        }                 

    }

    public function shiftToT13tb($t04tb, $idno)
    {

        $class_info = [
            'class' => $t04tb->class,
            'term' => $t04tb->term
        ];
        
        $validate = $this->t27tbValidate($class_info, $idno);

        if ($validate['status'] == false){
            return $validate;
        }
        // 取得該學員[m02tb]
        
        $t01tb = $t04tb->t01tb;
        $t27tb = $validate['t27tb'];
        $m02tb = $validate['m02tb'];
        $t13tb = $validate['t13tb'];
        // $t40tb = $validate['t40tb'];

        $new_t13tb = [
            'class' => $t27tb->class,                               // 班號
            'term' => $t27tb->term,                                 // 期別
            'idno' => $t27tb->idno,                                 // 身分證字號
            'age' => Common::computeAge($t27tb->birth, substr($t27tb->class, 0, 3)),             // 年齡
            'organ' => $t27tb->organ,                               // 機關代碼
            'dept' => $t27tb->dept,                                 // 單位名稱
            'position' => $t27tb->position,                         // 職稱
            'rank' => $t27tb->rank,                                 // 官職等
            'ecode' => $t27tb->ecode,                               // 學歷代碼
            'education' => $t27tb->education,                       // 學歷
            'dorm' => $t27tb->dorm,                                 // 受訓期間住宿
            'extradorm' => $t27tb->extradorm,                       // 提前住宿
            'nonlocal' => $t27tb->nonlocal,                         // 遠道者
            'vegan' => $t27tb->vegan,                               // 素食
            'fee' => 0
        ]; 
        
        if($t01tb->type == '13'){ //班別性質(游於藝講堂)
            $new_t13tb['fee'] = 0;
        }else{
            if($t01tb->process == 2){
                if (!empty($t27tb->mboard)){
                    $mboard_field_name = 'board'.$t27tb->mboard;
                    $new_t13tb['fee'] = SystemParam::get()->$mboard_field_name;
                }
            }else{
                $new_t13tb['fee'] = 0;
            }
        }

        $t27tb_info = array_merge(['idno' => $idno], $class_info);

        if (empty($t13tb)){
            $this->t13tbRepository->insert($new_t13tb);
        }else{
            $this->t13tbRepository->update($t27tb_info, $new_t13tb);
        }

        $new_m02tb = [
            'idno' => $t27tb->idno,
            'lname' => $t27tb->lname,
            'fname' => $t27tb->fname,
            'cname' => $t27tb->lname.$t27tb->fname,
            'sex' => $t27tb->sex,
            'birth' => $t27tb->birth,
            'organ' => $t27tb->organ,
            'dept' => $t27tb->dept,
            'position' => $t27tb->position,
            'education' => $t27tb->education,
            'offtela1' => $t27tb->offtela,
            'offtelb1' => $t27tb->offtelb,
            'offtelc1' => $t27tb->offtelc,
            'offfaxa' => $t27tb->offfaxa,
            'offfaxb' => $t27tb->offfaxb,
            'homtela' => $t27tb->homtela,
            'homtelb' => $t27tb->homtelb,
            'mobiltel' => $t27tb->mobiltel,
            'email' => $t27tb->email,
            'offemail' => $t27tb->offemail,
            'offzip' => $t27tb->offzip,
            'homzip' => $t27tb->homzip,
            'offaddr1' => $t27tb->offaddr1,
            'offaddr2' => $t27tb->offaddr2,
            'homaddr1' => $t27tb->homaddr1,
            'homaddr2' => $t27tb->homaddr2,
            'send' => $t27tb->send,
            'handicap' => $t27tb->handicap,
            'chief' => $t27tb->chief,
            'personnel' => $t27tb->personnel,
            'aborigine' => $t27tb->aborigine,
            'datadate' => new DateTime(),
            'enrollid' => $t27tb->enrollid,
            'rank' => $t27tb->rank,
            'ecode' => $t27tb->ecode
        ];

        $new_m02tb['datadate'] = ((int)($new_m02tb['datadate']->format('Y'))-1911).$new_m02tb['datadate']->format('md');

        if (empty($m02tb)){
            $this->m02tbRepository->insert($new_m02tb);
        }else{
            $this->m02tbRepository->update(['idno' => $idno], $new_m02tb);          
        }

        $new_t40tb = [
            'class' => $t27tb->class,
            'term' => $t27tb->term,
            'idno' => $t27tb->idno,
            'morgan' => $t27tb->morgan,
            'zip' => $t27tb->zip,
            'maddress' => $t27tb->maddress,
            'msponsor' => $t27tb->msponsor,
            'mposition' => $t27tb->mposition,
            'mtelnoa' => $t27tb->mtelnoa,
            'mtelnob' => $t27tb->mtelnob,
            'mtelnoc' => $t27tb->mtelnoc,
            'mfaxnoa' => $t27tb->mfaxnoa,
            'mfaxnob' => $t27tb->mfaxnob,
            'memail' => $t27tb->memail,
            'mboard' => $t27tb->mboard
        ];

        if (empty($t40tb)){
            $this->t40tbRepository->insert($new_t40tb);
        }else{
            $this->t40tbRepository->update($t27tb_info, $new_t40tb);  
        }   

        return ['status' => true, 'message' => "轉檔成功"];
    }        

    public function t27tbValidate($class_info, $idno)
    {
        $t27tb_info = $class_info;
        $t27tb_info['idno'] = $idno;
        
        $t27tb = $this->t27tbRepository->find($t27tb_info);

        if (empty($t27tb)){
            return ['status' => false, 'message' => "找不到該報名資料"];
        }

        $m02tb = $this->m02tbRepository->find(['idno' => $idno]);
        if (!empty($m02tb)){
            // 判斷姓名是否相同
            if ($m02tb->cname != $t27tb->cname){
                return ['status' => false, 'message' => "【m02tb 學員基本資料檔】中已有{$m02tb->cname}與其身分證號相同"];
            }
        }

        $t13tb = $this->t13tbRepository->find($t27tb_info);
        // if (!empty($t13tb)){
        //     if (!empty($t13tb->serno)){
        //         return ['status' => false, 'message' => "【t13tb 班別學員資料檔】中已有【{$m02tb->lname}{$m02tb->fname}】收據編號：{$t13tb->serno}"];
        //     }
        // }

        //【 t40tb 委辦班別經費支付檔】
        // $t40tb = $this->t40tbRepository->find($t27tb_info);
        // if (!empty($t40tb)){
        //     if(!empty($t40tb->oldterm)){
        //         return ['status' => false, 'message' => "【{$m02tb->lname}{$m02tb->fname}】作過【調期】!"];
        //     }
        // }      

        return [
            'status' => true,
            'm02tb' => $m02tb,
            't13tb' => $t13tb,
            // 't40tb' => $t40tb,
            't27tb' => $t27tb
        ];  
    }

    public function t27tbsValidate($t04tb_info, $idnos, $apply_datas){
        
        $condition = [
            'field' => 'idno',
            'data' => $idnos
        ];

        $t27tb_info = $t04tb_info;
        $t27tb_info['whereIn'] = $condition;

        $m02tbs = $this->m02tbRepository->getData(['whereIn' => $condition])->keyBy('idno')->toArray();
        $t13tbs = $this->t13tbRepository->getData($t27tb_info)->keyBy('idno')->toArray();
        $t40tbs = $this->t40tbRepository->getData($t27tb_info)->keyBy('idno')->toArray();
        $errors = [];
        foreach ($idnos as $idno){

            $m02tb = (empty($m02tbs[$idno])) ? null : $m02tbs[$idno];
            
            $errors[$idno] = "";

            if (!empty($m02tb)){
                // 判斷姓名是否相同
                if ($m02tb->lname.$m02tb->fname != $apply_datas[$indo]['fullname']){
                    $errors[$idno] .= "【m02tb 學員基本資料檔】中已有{$m02tb->lname}{$m02tb->fname}與其身分證號相同;";
                }
            }
    
            $t13tb = (empty($t13tbs[$idno])) ? null : $t13tbs[$idno] ;
            if (!empty($t13tb)){
                if (!empty($t13tb->serno)){
                    $errors[$idno] .= "【t13tb 班別學員資料檔】中已有【{$m02tb->lname}{$m02tb->fname}】收據編號：{$t13tb->serno};";
                }
            }
    
            //【 t40tb 委辦班別經費支付檔】
            $t40tb = (empty($t13tbs[$idno])) ? null : $t13tbs[$idno] ;
            if (!empty($t40tb)){
                if(!empty($t40tb->oldterm)){
                    $errors[$idno] .= "【{$m02tb->lname}{$m02tb->fname}】作過【調期】!;";
                }
            }    

            if (empty($errors[$idno])){
                unset($errors[$idno]);
            }
            
        }

        return $errors;
    }

    public function importFileTransFormat($apply_datas, $identity)
    {
        $format_datas = [];
        $format = [];

        if ($identity == 1){
            $format = array_keys($this->getGovernmentEmployeeFormat());
        }elseif ($identity == 2){
            $format = array_keys($this->getGeneralPeopleFormat());
        }  

        foreach($apply_datas as $key =>$datas)
        {
            if ($key < 7 || empty(array_filter($datas))) continue;
            
            foreach ($datas as $index => $data){
                if (isset($format[$index])){
                    $data = trim($data);
                    if ($format[$index] == "ecode"){
                        $data = (int)($data);
                    }

                    $format_datas[$key][$format[$index]] = $data;
                }
            }
            $format_datas[$key]['identity'] = $identity;
        }
        
        return $format_datas;
    
    }

    /*
        取得匯入報名資料公務人員格式
    */
    public function getGovernmentEmployeeFormat()
    {
        // MT = 兩者都有
        return [
            "idno" => 'MT',            // 身分證字號
            "cname" => 'm02tb',        // 名字
            "sex" => 'm02tb',          // 性別
            "ecode" => 'MT',           // 最高學歷
            "birth" => 'm02tb',
            "education" => 'MT',       // 畢業學校
            "enrollid" => 'm02tb',     // 學員機關代碼
            "dept" => 'MT',            // 服務機關
            "position" => 'MT',        // 職稱
            "rank" => 'MT',            // 官職等
            "offaddr1" => 'm02tb',     // 機關縣市
            "offaddr2" => 'm02tb',     // 機關地址
            "offzip" => 'm02tb',       // 機關郵遞區號 
            "offtela" => 'm02tb',     // 機關電話區碼 (電話(公一)區碼)
            "offtelb" => 'm02tb',     // 機關電話 (電話(公一))
            "offtelc" => 'm02tb',     // 機關電話分機 (電話(公一)分機)
            "offfaxa" => 'm02tb',      // 傳真電話(區碼)
            "offfaxb" => 'm02tb',      // 傳真電話
            "email" => 'm02tb',        // 學員 email
            "homaddr1" => 'm02tb',     // 住家縣市
            "homaddr2" => 'm02tb',     // 住家地址
            "homzip" => 'm02tb',       // 住家郵遞區號
            "homtela" => 'm02tb',      // 住家電話(區碼)
            "homtelb" => 'm02tb',      // 住家電話
            "mobiltel" => 'm02tb',     // 行動電話
            "dorm" => 't27tb',         // 住宿
            "vegan" => 't27tb',        // 素食  
            "handicap" => 'm02tb',     // 行動不便
            "extradorm" => 't27tb',    // 提前住宿
            "nonlocal" => 't27tb',     // 遠道者
            "offname" => 't27tb',      // 人事單位姓名
            "offtel" => 't27tb',       // 人事單位電話
            "offemail" => 't27tb',     // 人事單位信箱                
            "chief" => 'm02tb',        // 主管
            "personnel" => 'm02tb',    // 人事
            "aborigine" => 'm02tb',    // 原住民
            "identity" => 'm02tb'
        ];
    }

    public function getGeneralPeopleFormat()
    {
        return [
            "idno" => 'MT',            // 身分證字號
            "cname" => 'm02tb',        // 姓名
            "sex" => 'm02tb',          // 性別          
            "mobiltel" => 'm02tb',     // 聯絡電話            
            "email" => 'm02tb',        // 學員 email
            "vegan" => 't13tb',        // 素食  
            "handicap" => 'm02tb',     // 行動不便            
            "identity" => 'm02tb'
        ];
    }

    public function validateImport($apply_datas, $identity)
    {
        // 驗證有無重複身分證 
        $idnos = collect($apply_datas)->pluck('idno')->toArray(); 
        $idno_repeat = array_unique(array_diff_assoc($idnos, array_unique($idnos)));
        $error_message = "";
        if (!empty($idno_repeat)){
            foreach($idno_repeat as $key => $idno){
                $repeat_key = array_search($idno, $idnos);
                $error_message .= "<a style='color:red'>第 ".($key+8)." 筆學員資料身分證與第 ".($repeat_key+8)." 筆學員資料重複</a><br>";
            }
            return $error_message;
        }
        
        // 驗證資料是否正確
        if ($identity == 1){
            $errors = $this->validateGovernmentEmployeeFormat($apply_datas);
        }elseif ($identity == 2){
            $errors = $this->validateGeneralPeopleFormat($apply_datas);
        }  

        foreach ($errors as $key => $error){
            if (!empty($error->toArray())){
                $error_message .= "<a style='color:blue'>第".($key+1)."筆學員資料</a><br>";
                foreach ($error->toArray() as $error_row)
                {
                    $error_message .= join("<br>", $error_row)."<br>";
                }
            }
        }

        return $error_message;
    }

    public function validateGovernmentEmployeeFormat($apply_datas)
    {
        $errors = [];

        $idnos = collect($apply_datas)->pluck('idno')->filter();
        $m02tbs = $this->m02tbRepository->getByIdnos($idnos)->keyBy('idno');

        $t13tb_fields = config('database_fields.t27tb');
        $ecodes = join(",", array_keys($t13tb_fields['ecode']));

        $enrollorgs = collect($apply_datas)->pluck('enrollid');
        
        $m17tbs = $this->m17tbRepository->getByEnrollorgs($enrollorgs)->keyBy('enrollorg');

        foreach ($apply_datas as $key => $apply_data){

            $validator = Validator::make($apply_data, [
                'idno' => 'required',
                'cname' => 'required',
                'sex' => 'required|in:F,M',
                'ecode' => "required|in:$ecodes",
                'enrollid' => 'required',
                'position' => 'required',
                'rank' => 'required'
            ],[
                'idno.required' => '身分證 不可為空',
                'cname.required' => '姓名 不可為空',
                'sex.required' => '性別 不可為空',
                'enrollid.required' => '服務機關代碼 不可為空',
                'position.required' => '職稱 不可為空',
                'rank.required' => '官職等 不可為空',
                'ecode.in' => '最高學歷 異常',
                'sex.in' => '性別 異常'
            ]);

            if (!isset($m17tbs[$apply_data['enrollid']])){
                $validator->errors()->add('exsit', '服務機關代碼 不存在');
            }       

            if(isset($m02tbs[$apply_data['idno']]) && $m02tbs[$apply_data['idno']]->cname != $apply_data['cname']){
                $validator->errors()->add('cname', '學員身分證已存在但姓名不同');
            }

            $errors[$key] = $validator->errors();         
        }

        return $errors;
    }   
    
    public function validateGeneralPeopleFormat($apply_datas)
    {
        $idnos = collect($apply_datas)->pluck('idno')->filter();
        $m02tbs = $this->m02tbRepository->getByIdnos($idnos)->keyBy('idno');
        foreach ($apply_datas as $key => $apply_data){

            $validator = Validator::make($apply_data, [
                'idno' => 'required',
                'cname' => 'required',
                'sex' => 'required',
                'vegan' => '|in:Y,N,',
                "handicap" => '|in:Y,N,'
            ],[
                "idno.required" => "身分證 不可為空",
                'cname.required' => "姓名 不可為空",
                'sex.required' => '性別 不可為空',
                'vegan.in' => '素食 異常',
                "handicap.in" => '行動不便 異常'                        
            ]);

            if (isset($m02tbs[$apply_data['idno']]) && $m02tbs[$apply_data['idno']]->identity <> $apply_data['identity']){
                $validator->errors()->add('identity', '學員身份錯誤');
            }

            $errors[$key] = $validator->errors();
            
        }
        return $errors;
    }    

    public function splitApplyData($apply_datas, $identity, $version)
    {
        $new_apply_datas = [];
        if ($identity == 1){
            $format = $this->getGovernmentEmployeeFormat($version);
        }elseif ($identity == 2){
            $format = $this->getGeneralPeopleFormat();
        }             

        foreach ($apply_datas as $key => $apply_data){
            foreach ($apply_data as $field => $value){
                // $format[$field] = 屬於哪張資料表的資料
                if ($format[$field] == "MT"){
                    $new_apply_datas[$key]['m02tb'][$field] = $value;
                    $new_apply_datas[$key]['t13tb'][$field] = $value;
                }else{
                    $new_apply_datas[$key][$format[$field]][$field] = $value;
                }
                
            }
        }
        return $new_apply_datas;
    }    

    public function importApplyData($t04tb_info, $apply_datas)
    {
        $idnos = collect($apply_datas)->pluck('idno')->unique();
        $exsit_t27tbs = $this->t27tbRepository->getByIdnos($t04tb_info, $idnos)->keyBy('idno');
        $exsit_m02tbs = $this->m02tbRepository->getByIdnos($idnos)->keyBy('idno');

        DB::beginTransaction();

        try {

            foreach ($apply_datas as $apply_data)
            {
                if (empty($exsit_t27tbs[$apply_data['idno']])){
                    $apply_data = array_merge($t04tb_info, $apply_data);
                    $apply_data['prove'] = 'N';
                    $this->t27tbRepository->insert($apply_data);
                }else{
                    $t27tb_info = $t04tb_info;
                    $t27tb_info['idno'] = $apply_data['idno'];                
                    $this->t27tbRepository->update($t27tb_info, $apply_data);
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

    public function getT01tb($class){
        return $this->t01tbRepository->find($class);
    }

}