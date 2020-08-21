<?php
namespace App\Services;
use App\Repositories\T04tbRepository;
use App\Repositories\T13tbRepository;
use App\Repositories\M13tbRepository;
use App\Repositories\M02tbRepository;
use App\Repositories\M17tbRepository;
use App\Repositories\ApplyModifyLogForAmdinRepository;
use App\Repositories\ApplyModifyLogRepository;
use App\Repositories\CheckChangeT13tbRepository;

use App\Helpers\Des;
use App\Helpers\Common;
use DateTime;
use DB;
use Auth;
use Validator;


class StudentApplyService
{
    /**
     * StudentApplyService constructor.
     * @param SystemCodeRepository $systemCodeRpository
     */
    public function __construct(
        T04tbRepository $t04tbRepository,
        T13tbRepository $t13tbRepository,
        M13tbRepository $m13tbRepository,
        M02tbRepository $m02tbRepository,
        M17tbRepository $m17tbRepository,
        ApplyModifyLogForAmdinRepository $applyModifyLogForAmdinRepository,
        ApplyModifyLogRepository $applyModifyLogRepository,
        CheckChangeT13tbRepository $checkChangeT13tbRepository
    )
    {
        $this->t04tbRepository = $t04tbRepository;
        $this->t13tbRepository = $t13tbRepository;
        $this->m13tbRepository = $m13tbRepository;
        $this->m02tbRepository = $m02tbRepository;
        $this->m17tbRepository = $m17tbRepository;
        $this->applyModifyLogForAmdinRepository = $applyModifyLogForAmdinRepository;
        $this->applyModifyLogRepository = $applyModifyLogRepository;
        $this->checkChangeT13tbRepository = $checkChangeT13tbRepository;
    }

    public function getOpenClassList($queryData)
    {
        $queryData['t13tbNum'] = true;

        $select = [
            't04tb.*',
            't01tb.name as t01tb_name',
            't01tb.branch',
            'm09tb.username as m09tb_username',
            't13tbNumGroup.t13tbNum'
        ];

        return $this->t04tbRepository->getByQueryList($queryData, $select);
    }

    public function getT04tb($t04tb_info)
    {
        return $this->t04tbRepository->find($t04tb_info);
    }

    public function getT13tbsByT04tb($t04tb_info, $queryData = [], $order_by = [])
    {
        $queryData = array_merge($t04tb_info, $queryData);

        $with = [
            'm02tb' => function($relationship){
                $relationship->with(['m13tb', 'm17tb']);
            },
            'm13tb'
        ];

        $t13tbs = $this->t13tbRepository->get($queryData, ['t13tb.*', 'm02tb.email', 'm02tb.sex as m02tb_sex', 'm02tb.enrollid as m02tb_enrollid'], false
        , $order_by, $with);

        foreach ($t13tbs as $t13tb){
            $t13tb->des_idno = Des::encode($t13tb->idno, 'KLKLK');
        }
        return $t13tbs;
    }

    public function getT13tb($t13tb_info)
    {
        $t13tb = $this->t13tbRepository->find($t13tb_info);
        if (!empty($t13tb)){
            $t13tb->des_idno = Des::encode($t13tb->idno, 'KLKLK');
        }
        return $t13tb;
    }

    public function getM13tbs()
    {
        return $this->m13tbRepository->getMainOrgans();
    }

    public function storeT13tb($t04tbKey, $t13tb, $action)
    {
        $t13tb_info = $t04tbKey;
        $t13tb_info['idno'] = $t13tb['idno'];



        $t13tb = array_merge($t13tb_info, $t13tb);

        if ($action == "updateOrCreate"){
            if (empty($this->getT13tb($t13tb_info))){
                return $this->t13tbRepository->insert($t13tb);
            }else{
                return $this->t13tbRepository->update($t13tb_info, $t13tb);
            }
        }else if ($action == "insert"){

        }else if ($action == "update"){
            return $this->t13tbRepository->update($t13tb_info, $t13tb);
        }


    }

    public function insertT13tb($t13tbKey, $newT13tb)
    {
        $t13tb = $this->getT13tb($t13tbKey);
        if (!empty($t13tb)){
            return [
                'status' => 'repeat',
                'message' => '該學員已存在'
            ];
        }

        $newT13tb = array_merge($t13tbKey, $newT13tb);
        return $this->t13tbRepository->insert($newT13tb);
    }

    public function updateT13tb($t13tbKey, $newT13tb)
    {
        $t13tb = $this->getT13tb($t13tbKey);
        if (empty($t13tb)){
            return [
                'status' => 'repeat',
                'message' => '該學員不存在'
            ];
        }

        // $newT13tb = array_merge($t13tbKey, $newT13tb);

        return $this->t13tbRepository->update($t13tbKey, $newT13tb);
    }

    public function setT13tbDefaultValue($newT13tb, $newM02tb, $t04tb)
    {
        $newT13tb['age'] =  Common::computeAge($newM02tb['birth'], $t04tb->yerly);
        $newT13tb['dorm'] = (!empty($newT13tb['dorm'])) ? 'Y': 'N';
        $newT13tb['vegan'] = (!empty($newT13tb['vegan'])) ? 'Y': 'N';
        $newT13tb['nonlocal'] = (!empty($newT13tb['nonlocal'])) ? 'Y': 'N';
        $newT13tb['extradorm'] = (!empty($newT13tb['extradorm'])) ? 'Y': 'N';

        $newT13tb['upddate'] = (new DateTime())->format("Y-m-d H:i:s");
        return $newT13tb;
    }

    public function changeTerm($t13tbKey, $term)
    {
        if (empty($t13tbKey['class']) || empty($t13tbKey['term']) || empty($t13tbKey['idno'])) return false;
        return $this->t13tbRepository->update($t13tbKey, ['term' => $term, 'no' => null]);
    }

    public function changeStudent($t13tbKey, $newT13tb){
        return $this->t13tbRepository->update($t13tbKey, $newT13tb);
    }

    public function storeM02tb($m02tb_info, $m02tb)
    {

        $m02tb['handicap'] = (!empty($m02tb['handicap'])) ? 'Y': 'N';
        $m02tb['chief'] = (!empty($m02tb['chief'])) ? 'Y': 'N';
        $m02tb['personnel'] = (!empty($m02tb['personnel'])) ? 'Y': 'N';
        $m02tb['aborigine'] = (!empty($m02tb['aborigine'])) ? 'Y': 'N';

        return $this->m02tbRepository->updateOrCreate($m02tb_info, $m02tb);

    }

    public function deleteT13tb($t13tb_info)
    {
        return $this->t13tbRepository->delete($t13tb_info);
    }

    public function diffArrangeGroup($t04tb_info, $conditions)
    {
        $students = $this->getT13tbsByT04tb($t04tb_info);
        // dd($students);
        $groups = [$students];

        foreach($conditions as $condition){
            switch ($condition) {
                case 'sex':
                    $groups = $this->arrangeSex($groups);
                    break;
                case 'organ':
                    $groups = $this->arrangeOrgan($groups);
                    break;
                case 'ecode':
                    $groups = $this->arrangeEcode($groups);
                    break;
                case 'age':
                    $groups = $this->arrangeAge($groups);
                    break;
                default:
                    # code...
                    break;
            }

        }
        // dd($groups);
        DB::beginTransaction();
        // DB::enableQueryLog();
        $stNo = 1;
        try {

            foreach($groups as $group => $students){
                $t13tbKey = $t04tb_info;
                foreach ($students as $student){
                    $t13tbKey['idno'] = $student->idno;
                    $this->t13tbRepository->update($t13tbKey, ['no' => str_pad($stNo, 3, '0', STR_PAD_LEFT), 'groupno' => $group + 1]);
                    $stNo++;
                }
            }

            DB::commit();
            // DB::rollback();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
            // return back()->with('result', 0)->with('message', '更新失敗');
            var_dump($e->getMessage());
            die;
        }
    }

    public function arrangeSex($groups)
    {
        $new_group = [];
        $group_no = 1;
        foreach($groups as $group){
            foreach($group as $student){
                if ($student->m02tb_sex == "F"){
                    $new_group[$group_no][] = $student;
                }elseif ($student->m02tb_sex == "M"){
                    $new_group[$group_no + 1][] = $student;
                }
            }
            $group_no += 2;
        }
        return array_values($new_group);
    }

    public function arrangeOrgan($groups)
    {
        $new_group = [];
        $group_no = 1;

        // 取得主管機關
        $competent_authoritys = $this->m17tbRepository->getCompetentAuthoritys()->pluck('enrollorg')->toArray();

        foreach($groups as $group){
            foreach($group as $student){
                if (in_array($student->m02tb_enrollid, $competent_authoritys)){
                    $new_group[$group_no][] = $student;
                }else{
                    $new_group[$group_no + 1][] = $student;
                }
            }
            $group_no += 2;
        }

        return array_values($new_group);
    }

    public function arrangeEcode($groups)
    {
        /* 學歷代碼
        1:博士 2:碩士
        3:學士 4:軍警校
        5:專科 6:高中職
        7:其他
        */

        $new_group = [];
        $group_no = 1;
        foreach($groups as $group){
            foreach($group as $student){
                switch ($student->ecode) {
                    case '1':
                        $new_group[$group_no][] = $student;
                        break;
                    case '2':
                        $new_group[$group_no + 1][] = $student;
                        break;
                    case '3':
                        $new_group[$group_no + 2][] = $student;
                        break;
                    default:
                        $new_group[$group_no + 3][] = $student;
                        break;
                }

            }
            $group_no += 4;
        }

        return array_values($new_group);
    }

    public function arrangeAge($groups)
    {
        $new_group = [];
        $group_no = 1;

        foreach($groups as $group){
            foreach($group as $student){
                $age_range = empty($student->age) ? 0 : floor(($student->age-1) / 10);
                $new_group[$group_no + $age_range][] = $student;
            }
            $group_no += 13;
        }

        return array_values($new_group);
    }

    public function randomArrangeGroup($t04tb_info, $group_num)
    {
        $students = $this->getT13tbsByT04tb($t04tb_info)->pluck('idno');
        $students = $students->shuffle()->toArray();
        $student_num = count($students);
        if ($student_num == 0) return false;
        if ($group_num == 0) return false;

        $group_student_num = floor($student_num / $group_num);
        $assign = $group_student_num;
        $new_groups = [];
        $group_no = 1;

        $student_i = 0;
        for($group_no=1; $group_no<=$group_num; $group_no++){
            $stn = $group_student_num;
            while($stn > 0){
                $new_groups[$group_no][] = $students[$student_i];
                $student_i++;
                $stn--;
            }
        }

        // 分配剩餘學員
        $group_no = 1;

        for($i = $student_i; $i<count($students); $i++){
            $new_groups[$group_no][] = $students[$i];
            $group_no++;
        }

        $stno = 0;
        DB::beginTransaction();
        try {

            foreach($new_groups as $group => $idnos){

                foreach ($idnos as $idno){
                    $stno++;
                    $new_stno = str_pad($stno, 3, '0', STR_PAD_LEFT);
                    $student_info = $t04tb_info;
                    $student_info['idno'] = $idno;
                    $this->t13tbRepository->update($student_info, ['no' => $new_stno, 'groupno' => $group]);
                }

            }

            DB::commit();
            // DB::rollback();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
            // return back()->with('result', 0)->with('message', '更新失敗');
            var_dump($e->getMessage());
            die;
        }

    }

    public function arrangeStNo($t04tb_info)
    {
        $order_by = [
            [
                'groupno', 'asc'
            ]
        ];

        $students = $this->getT13tbsByT04tb($t04tb_info, [], $order_by);
        $stno = 0;
        DB::beginTransaction();
        try {

            foreach($students as $student){
                $stno++;
                $new_stno = str_pad($stno, 3, '0', STR_PAD_LEFT);
                $student_info = $t04tb_info;
                $student_info['idno'] = $student->idno;
                $this->t13tbRepository->update($student_info, ['no' => $new_stno]);
            }

            DB::commit();
            // DB::rollback();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
            // return back()->with('result', 0)->with('message', '更新失敗');
            var_dump($e->getMessage());
            die;
        }

    }

    public function storeT13tbStno($t04tb_info, $stnos)
    {

        $t13tbs = $this->t13tbRepository->get($t04tb_info, ['t13tb.*'], false);
        $idnos = $t13tbs->pluck('idno', 'idno')->toArray();

        foreach($stnos as $idno => $stno){
            if (empty($idnos[$idno])){
                return [
                    'status' => false,
                    'message' => '資料異常'
                ];
            }
        }

        if (count($t13tbs) != count($stnos)){
            return [
                'status' => false,
                'message' => '資料異常'
            ];
        }

        $stnos = array_map(function($stno){
            return str_pad($stno, 3, '0', STR_PAD_LEFT);
        }, $stnos);

        // 檢查學號是否重複
        if (count(array_unique(array_filter($stnos))) != count(array_filter($stnos))){
            $tmp = [];

            foreach($stnos as $idno => $stno){
                if (empty($tmp[$stno])){
                    $tmp[$stno] = $idno;
                }else{
                    return [
                        'status' => false,
                        'message' => "{$stno} 學號重複"
                    ];
                }
            }
        }

        try {

            foreach($stnos as $idno => $stno){
                $student_info = $t04tb_info;
                $student_info['idno'] = $idno;
                $this->t13tbRepository->update($student_info, ['no' => $stno]);
            }

            DB::commit();

            return [
                'status' => true,
                'message' => ""
            ];
        } catch (\Exception $e) {
            DB::rollback();

            return [
                'status' => false,
                'message' => ""
            ];

            var_dump($e->getMessage());
            die;
        }

    }

    public function storeT13tbGroup($t04tb_info, $groups)
    {

        try {
            foreach($groups as $idno => $group){
                $student_info = $t04tb_info;
                $student_info['idno'] = $idno;
                $this->t13tbRepository->update($student_info, ['groupno' => $group]);
            }

            DB::commit();

            return [
                'status' => true,
                'message' => ""
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

    public function getM02tb($idno)
    {
        return $this->m02tbRepository->find($idno);
    }

    public function insertApplyModifyLogForAmdin($t04tb_info, $modify_type, $modify_info)
    {
        $modify_log = array_merge($t04tb_info, $modify_info);
        $modify_log['modify_type'] = $modify_type;
        $modify_log['modify_user_id'] = Auth::user()->id;

        return $this->applyModifyLogForAmdinRepository->insert($modify_log);
    }

    public function checkChangeStudent($t04tb_info, $new_idno, $idno)
    {
        $old_student = $this->getM02tb($idno);
        $new_student = $this->getM02tb($new_idno);

        // 身份相同才可以換員
        if (!empty($new_student)){
            if ($old_student->identity != $new_student->identity){
                return [
                    'status' => false,
                    'message' => '換員的對象身份不符'
                ];
            }
        }

        $t13tb_info = array_merge($t04tb_info, ['idno' => $new_idno]);
        $new_t13tb = $this->getT13tb($t13tb_info);

        if (!empty($new_t13tb)){
            return [
                'status' => false,
                'message' => '該學員已報名'
            ];
        }

        $t13tb_info = array_merge($t04tb_info, ['idno' => $idno]);
        $old_t13tb = $this->getT13tb($t13tb_info);

        if (empty($old_t13tb)){
            return [
                'status' => false,
                'message' => '被換學員不存在'
            ];
        }

        return [
            'status' => true,
            't13tb' => $old_t13tb
        ];
    }

    public function getT04tbAndModifyinfo($t04tb_info)
    {
        return $this->t04tbRepository->getT04tbAndModifyinfo($t04tb_info);
    }

    public function updateT04tbPublish($t04tb_info, $publish)
    {
        return $this->t04tbRepository->update($t04tb_info, ['publish1' => $publish]);
    }

    public function importFileTransFormat($apply_datas, $identity, $version)
    {
        $format_datas = [];
        $format = [];

        if ($identity == 1){
            $format = array_keys($this->getGovernmentEmployeeFormat($version));
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
        取得匯入名冊公務人員格式
    */
    public function getGovernmentEmployeeFormat($version)
    {
        if ($version == "full"){
            // 完整版
            // MT = 兩者都有
            return [
                "idno" => 'MT',            // 身分證字號
                "cname" => 'm02tb',        // 名字
                "sex" => 'm02tb',          // 性別
                "ecode" => 'MT',           // 最高學歷
                "birth" => 'm02tb',        // 生日
                "education" => 'MT',       // 畢業學校
                "enrollid" => 'm02tb',     // 學員機關代碼
                "dept" => 'MT',            // 服務機關
                "position" => 'MT',        // 職稱
                "rank" => 'MT',            // 官職等
                "offaddr1" => 'm02tb',     // 機關縣市
                "offaddr2" => 'm02tb',     // 機關地址
                "offzip" => 'm02tb',       // 機關郵遞區號
                "offtela1" => 'm02tb',     // 機關電話區碼 (電話(公一)區碼)
                "offtelb1" => 'm02tb',     // 機關電話 (電話(公一))
                "offtelc1" => 'm02tb',     // 機關電話分機 (電話(公一)分機)
                "offfaxa" => 'm02tb',      // 傳真電話(區碼)
                "offfaxb" => 'm02tb',      // 傳真電話
                "email" => 'm02tb',        // 學員 email
                "homaddr1" => 'm02tb',     // 住家縣市
                "homaddr2" => 'm02tb',     // 住家地址
                "homzip" => 'm02tb',       // 住家郵遞區號
                "homtela" => 'm02tb',      // 住家電話(區碼)
                "homtelb" => 'm02tb',      // 住家電話
                "mobiltel" => 'm02tb',     // 行動電話
                "dorm" => 't13tb',         // 住宿
                "vegan" => 't13tb',        // 素食
                "handicap" => 'm02tb',     // 行動不便
                "extradorm" => 't13tb',    // 提前住宿
                "nonlocal" => 't13tb',     // 遠道者
                "offname" => 't13tb',      // 人事單位姓名
                "offtel" => 't13tb',       // 人事單位電話
                "offemail" => 't13tb',     // 人事單位信箱
                "chief" => 'm02tb',        // 主管
                "personnel" => 'm02tb',    // 人事
                "aborigine" => 'm02tb',    // 原住民
                "identity" => 'm02tb'
            ];
        }elseif ($version == "easy"){
            // 簡易版
            return [
                "idno" => 'MT',            // 身分證字號
                "cname" => 'm02tb',        // 名字
                "dorm" => 't13tb',         // 住宿
                "vegan" => 't13tb',        // 素食
                "handicap" => 'm02tb',     // 行動不便
                "extradorm" => 't13tb',    // 提前住宿
                "nonlocal" => 't13tb',     // 遠道者
                "chief" => 'm02tb',        // 主管
                "personnel" => 'm02tb',    // 人事
                "aborigine" => 'm02tb',    // 原住民
                "identity" => 'm02tb',
            ];
        }
    }

    public function validateGovernmentEmployeeFormat($apply_datas, $version)
    {
        $errors = [];

        $idnos = collect($apply_datas)->pluck('idno')->filter();
        $m02tbs = $this->m02tbRepository->getByIdnos($idnos)->keyBy('idno');

        if ($version == "full"){
            $t13tb_fields = config('database_fields.t13tb');
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

                if (empty($m17tbs[$apply_data['enrollid']])){
                    $validator->errors()->add('exsit', '服務機關代碼 不存在');
                }

                if(isset($m02tbs[$apply_data['idno']]) && $m02tbs[$apply_data['idno']]->cname != $apply_data['cname']){
                    $validator->errors()->add('cname', '學員身分證已存在但姓名不同');
                }

                $errors[$key] = $validator->errors();
            }
        }else{

            foreach ($apply_datas as $key => $apply_data){

                $validator = Validator::make($apply_data, [
                    'idno' => 'required',
                    'cname' => 'required',
                ],[
                    "idno.required" => "身分證 不可為空",
                    'cname.required' => "姓名 不可為空"
                ]);

                if (empty($m02tbs[$apply_data['idno']])){
                    $validator->errors()->add('exsit', '學員 不存在，請使用完整版匯入');
                }else if($m02tbs[$apply_data['idno']]->cname != $apply_data['cname']){
                    $validator->errors()->add('cname', '學員身分證已存在但姓名不同');
                }

                $errors[$key] = $validator->errors();
            }
        }

        return $errors;
    }

    public function getGeneralPeopleFormat()
    {
        return [
            "idno" => 'MT',            // 身分證字號
            "cname" => 'm02tb',        // 姓名
            "sex" => 'm02tb',          // 性別
            "birth" => 'm02tb',        // 生日
            "mobiltel" => 'm02tb',     // 聯絡電話
            "email" => 'm02tb',        // 學員 email
            "vegan" => 't13tb',        // 素食
            "handicap" => 'm02tb',     // 行動不便
            "identity" => 'm02tb'
        ];
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

    public function validateImport($apply_datas, $identity, $version)
    {
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


        if ($identity == 1){
            $errors = $this->validateGovernmentEmployeeFormat($apply_datas, $version);
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
        // dd($new_apply_datas);
        return $new_apply_datas;
    }

    public function importApplyData($t04tb_info, $apply_datas, $version)
    {
        $t04tb = $this->t04tbRepository->find($t04tb_info);
        $idnos = collect($apply_datas)->pluck('m02tb.idno')->unique();
        $exsit_t13tbs = $this->t13tbRepository->getByIdnos($t04tb_info, $idnos)->keyBy('idno');
        $exsit_m02tbs = $this->m02tbRepository->getByIdnos($idnos)->keyBy('idno');

        $apply_datas = array_map(function($apply_data){
            $apply_data['t13tb']['dorm'] = !empty($apply_data['t13tb']['dorm']) ? $apply_data['t13tb']['dorm'] : 'N';
            $apply_data['t13tb']['vegan'] = !empty($apply_data['t13tb']['vegan']) ? $apply_data['t13tb']['vegan'] : 'N';
            $apply_data['t13tb']['extradorm'] = !empty($apply_data['t13tb']['extradorm']) ? $apply_data['t13tb']['extradorm'] : 'N';
            $apply_data['t13tb']['nonlocal'] = !empty($apply_data['t13tb']['nonlocal']) ? $apply_data['t13tb']['nonlocal'] : 'N';
            $apply_data['t13tb']['status'] = 1;

            return $apply_data;
        }, $apply_datas);

        DB::beginTransaction();

        try {

            foreach ($apply_datas as $apply_data)
            {
                $m02tb = isset($exsit_m02tbs[$apply_data['m02tb']['idno']]) ? $exsit_m02tbs[$apply_data['m02tb']['idno']] : null;
                $birthDay = ($version == 'easy') ? $m02tb->birth : $apply_data['m02tb']['birth'];
                $apply_data['t13tb']['age'] = Common::computeAge($birthDay, $t04tb->yerly);

                // 簡易版自動帶入該身分證的學員基本資料
                if ($version == 'easy'){
                    $m02tb = collect($m02tb->toArray())->only(['organ', 'dept', 'rank', 'position', 'ecode', 'education'])->toArray();
                    $apply_data['t13tb'] = array_merge($apply_data['t13tb'], $m02tb);

                    $this->m02tbRepository->update(['idno' => $apply_data['m02tb']['idno']], $apply_data['m02tb']);
                }elseif ($version == "full"){
                    if (empty($exsit_m02tbs[$apply_data['m02tb']['idno']])){
                        $this->m02tbRepository->insert($apply_data['m02tb']);
                    }else{
                        $this->m02tbRepository->update(['idno' => $apply_data['m02tb']['idno']], $apply_data['m02tb']);
                    }
                }

                if (empty($exsit_t13tbs[$apply_data['t13tb']['idno']])){
                    $apply_data['t13tb'] = array_merge($t04tb_info, $apply_data['t13tb']);
                    $this->t13tbRepository->insert($apply_data['t13tb']);
                }else{
                    $t13tb_info = $t04tb_info;
                    $t13tb_info['idno'] = $apply_data['t13tb']['idno'];
                    $this->t13tbRepository->update($t13tb_info, $apply_data['t13tb']);
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

    public function getModifyLogBySponsor($userid, $queryData)
    {
        return $this->t04tbRepository->getModifyLogBySponsor($userid, $queryData);
    }

    public function getT04tbBySponsor($userid)
    {
        return $this->t04tbRepository->getBySponsor($userid);
    }

    public function stopChange($t04tbKey, $is_stop_change)
    {
        return $this->t04tbRepository->update($t04tbKey, ['is_stop_change' => $is_stop_change]);
    }

    public function getModifyLogByIds($ids)
    {
        return $this->applyModifyLogRepository->getNotReviewByid($ids);
    }

    public function reviewModify($status)
    {
        $modifyLogs = $this->applyModifyLogRepository->getNotReviewByid($status->keys());

        DB::beginTransaction();

        try {
            foreach ($modifyLogs as $modifyLog){
                if (isset($status[$modifyLog->id])){
                    if ($status[$modifyLog->id] !== $modifyLog->status){
                        $modifyLog->status = $status[$modifyLog->id];
                        if ($modifyLog->status == 'Y'){
                            if ($modifyLog->type == 1){
                                // 換員
                                $this->execChangeStudent($modifyLog);
                            }elseif ($modifyLog->type == 2){
                                // 補報
                                $this->fetchApply($modifyLog);
                            }elseif ($modifyLog->type == 4){
                                // 取消
                                $this->execCancel($modifyLog);
                            }
                        }else{
                            $modifyLog->save();
                        }
                    }
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

    public function execChangeStudent($modifylog)
    {
        $t13tbKey = [
            'class' => $modifylog->class,
            'term' => $modifylog->term,
            'idno' => $modifylog->idno
        ];
        $this->t13tbRepository->delete($t13tbKey);
        $newT13tb = $this->checkChangeT13tbRepository->find(['id' => $modifylog->t13tb_check_id])->toArray();
        unset($newT13tb['id']);
        unset($newT13tb['created_at']);
        unset($newT13tb['updated_at']);
        unset($newT13tb['handicap']);
        $newT13tb['upddate'] = (new DateTime())->format("Y-m-d H:i:s");
        if (isset($newT13tb)){
            $t13tbKey = [
                'class' => $modifylog->class,
                'term' => $modifylog->term,
                'idno' => $modifylog->new_idno
            ];
            $check = $this->t13tbRepository->find($t13tbKey);
            if (empty($check)){
                $this->t13tbRepository->insert($newT13tb);
                $modifylog->save();
            }
        }
    }

    public function execCancel($modifylog)
    {
        $t13tbKey = [
            'class' => $modifylog->class,
            'term' => $modifylog->term,
            'idno' => $modifylog->idno
        ];

        $this->t13tbRepository->delete($t13tbKey);
        $modifylog->save();
    }

    public function fetchApply($modifylog)
    {
        $newT13tb = $this->checkChangeT13tbRepository->find(['id' => $modifylog->t13tb_check_id])->toArray();
        unset($newT13tb['id']);
        unset($newT13tb['created_at']);
        unset($newT13tb['updated_at']);
        unset($newT13tb['handicap']);
        $newT13tb['upddate'] = (new DateTime())->format("Y-m-d H:i:s");
        if (isset($newT13tb)){
            $t13tbKey = [
                'class' => $modifylog->class,
                'term' => $modifylog->term,
                'idno' => $modifylog->new_idno
            ];
            $check = $this->t13tbRepository->find($t13tbKey);
            if (empty($check)){
                $modifylog->save();
                $this->t13tbRepository->insert($newT13tb);
            }
        }

    }

}
