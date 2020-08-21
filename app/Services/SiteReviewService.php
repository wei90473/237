<?php
namespace App\Services;

use App\Repositories\T04tbRepository;
use App\Repositories\T39tbRepository;
use App\Repositories\T13tbRepository;
use App\Repositories\M02tbRepository;
use DB;

use App\Helpers\Des;

class SiteReviewService
{
    /**
     * PunchService constructor.
     * @param 
     */
    public function __construct(
        T04tbRepository $t04tbRepository,
        T39tbRepository $t39tbRepository,
        T13tbRepository $t13tbRepository,
        M02tbRepository $m02tbRepository
    )
    {
        $this->t04tbRepository = $t04tbRepository;
        $this->t39tbRepository = $t39tbRepository;
        $this->t13tbRepository = $t13tbRepository;
        $this->m02tbRepository = $m02tbRepository;
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getOpenClassList($queryData)
    {
        $select = ['t04tb.*', 't39tbNumGroup.t39tbNum'];
        $queryData['t39tbNum'] = true;
        return $this->t04tbRepository->getByQueryList($queryData, $select);
        // return $this->t04tbRepository->get($queryData, true, ['t04tb.*', 't39tbNumGroup.t39tbNum'], ['t39tbNum' => true]);
    }

    public function getT04tb($t04tb_info)
    {
        return $this->t04tbRepository->find($t04tb_info);
    }    

    public function getT39tbs($t04tb_info, $queryData)
    {
        $queryData = array_merge($t04tb_info, $queryData);
        $t39tbs = $this->t39tbRepository->get($queryData, false);
        foreach ($t39tbs as $t39tb){
            $t39tb->des_idno = Des::encode($t39tb->idno, 'KLKLKL');
        }
        return $t39tbs;
    }

    public function review($t04tb_info, $proves)
    {
        $proves = collect($proves)->groupBy(function($prove){
            return $prove;
        }, true);

        DB::beginTransaction();
        // DB::enableQueryLog();
        try {

            foreach ($proves as $prove => $proveGroup){
                $idnos = $proveGroup->keys();
                if ($prove == 'Y'){
                    $this->shiftT13tb($t04tb_info, $idnos);
                    $this->t39tbRepository->batchUpdateProve($t04tb_info, 'S', $idnos);
                }else{
                    $this->t39tbRepository->batchUpdateProve($t04tb_info, $prove, $idnos);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            // return false;
            // return back()->with('result', 0)->with('message', '更新失敗');
            var_dump($e->getMessage());
            die;
        } 


    }

    public function getT39tb($t39tb_info)
    {
        $t39tb = $this->t39tbRepository->find($t39tb_info);
        $t39tb->des_idno = Des::encode($t39tb->idno, 'KLKLKL');
        return $t39tb;
    }

    public function createT39tb($t04tbKey, $newT39tb, $t39tb)
    {
        if (isset($t39tb)){
            return [
                'status' => 'repeat',
                'message' => '該學員已存在'
            ];
        } 

        $newT39tb = array_merge($t04tbKey, $newT39tb);
        $newT39tb['prove'] = 'N';

        $t39tb = $this->t39tbRepository->insert($newT39tb);

        return [
            'status' => !empty($t39tb),
            'message' => ''
        ];
    }

    public function updateT39tb($t39tb_info, $newT39tb, $t39tb)
    {
        if (isset($t39tb) && $t39tb->prove <> 'S'){
            return [
                'status' => $this->t39tbRepository->update($t39tb_info, $newT39tb),
                'message' => ''
            ];
        }else{
            return [
                'status' => 'prove s',
                'message' => '已轉檔不可修改'
            ];
        }
    }    

    public function createT39tbIdno($t04tb_info)
    {
        $t39tb_max_idno = $this->t39tbRepository->getT39tbMaxIdno($t04tb_info);
        $new_idno = (int)substr($t39tb_max_idno->idno, -2, 2);
        $new_idno = $t04tb_info['class'].$t04tb_info['term'].str_pad($new_idno + 1, 2, '0', STR_PAD_LEFT);

        return $new_idno;
    }

    public function checkIdnoExsit($t04tb_info, $idno)
    {
        $t39tb_info = array_merge($t04tb_info, ['idno' => $idno]);
        $t39tb = $this->t39tbRepository->find($t39tb_info);
        return !empty($t39tb);
    }

    public function checkCondition($t04tb)
    {

        /*
            <<<審核條件一
            同一天,學員不可跨班上課
            A t13tb 班別學員資料檔
            B t36tb 行事曆檔
            C t01tb 班別基本資料檔
        */

        $condition = "SELECT m02tb.idno, m02tb.cname, t13tb.dept, t13tb.class, t13tb.term, t13tb.idno, 'chk1' as `condition`, t01tb.name 
                      FROM t13tb                      
                      JOIN t01tb ON t01tb.class = t13tb.class
                      JOIN m02tb ON m02tb.idno = t13tb.idno
                      JOIN t36tb ON t36tb.class = t13tb.class AND t36tb.term = t13tb.term
                      WHERE t13tb.idno IN (
                          SELECT idno 
                          FROM t39tb
                          WHERE class = ? AND term = ? AND prove IN ('X', 'N')
                      ) 
                      AND t13tb.class LIKE ?
                      AND t13tb.class <> ? 
                      AND t13tb.term <> ?
                      AND t36tb.`date` in (
                          SELECT `date` 
                          FROM t36tb
                          WHERE class = ? AND term = ? 	
                      )
                      ";
                   
        $condition_param[] = $t04tb->class;
        $condition_param[] = $t04tb->term;
        $condition_param[] = substr($t04tb->class, 0, 3).'%';
        $condition_param[] = $t04tb->class;
        $condition_param[] = $t04tb->term;
        $condition_param[] = $t04tb->class;
        $condition_param[] = $t04tb->term;

        /*

            <<<審核條件二
            六個月內 , 學員無故缺課紀錄
            1.t14tb 學員請假資料檔(t14tb.type='5' 缺課)
            2.t13tb 班別學員資料檔(t13tb.status='2' 未報到)

        */

        

        $condition .= "
                      UNION 
                      SELECT m02tb.idno,m02tb.cname, t13tb.dept, t13tb.class, t13tb.term, t13tb.idno, 'chk2' as `condition`, t01tb.name 
                      FROM t13tb 
                      JOIN t01tb ON t01tb.class = t13tb.class
                      JOIN m02tb ON m02tb.idno = t13tb.idno
                      JOIN t04tb ON t04tb.class = t13tb.class AND t04tb.term = t13tb.term
                      WHERE t13tb.status = 2 AND 
                          (
                              t04tb.sdate BETWEEN ? AND ? OR
                              t04tb.edate BETWEEN ? AND ?
                          ) AND 
                          t13tb.idno IN (
                              SELECT idno 
                              FROM t39tb
                              WHERE class = ? AND term = ? AND prove IN ('X', 'N')
                          )
                      UNION 
                      SELECT m02tb.idno,m02tb.cname, t13tb.dept, t14tb.class, t14tb.term, t14tb.idno, 'chk2' as `condition`, t01tb.name 
                      FROM t14tb 
                      JOIN t01tb ON t01tb.class = t14tb.class
                      JOIN m02tb ON m02tb.idno = t14tb.idno
                      JOIN t04tb ON t04tb.class = t14tb.class AND t04tb.term = t14tb.term
                      JOIN t13tb ON t13tb.class = t14tb.class AND t13tb.term = t14tb.term AND t13tb.idno = t14tb.idno
                      WHERE t14tb.`type` = 5 AND 
                          (
                              t04tb.sdate BETWEEN ? AND ? OR
                              t04tb.edate BETWEEN ? AND ?
                          ) AND 
                          t14tb.idno IN (
                              SELECT idno 
                              FROM t39tb
                              WHERE class = ? AND term = ? AND prove IN ('X', 'N')
                          )
                      ";
      
        $condition_param[] = $t04tb->sdate;
        $condition_param[] = $t04tb->edate;
        $condition_param[] = $t04tb->sdate;
        $condition_param[] = $t04tb->edate;
        $condition_param[] = $t04tb->class;
        $condition_param[] = $t04tb->term;


        $condition_param[] = $t04tb->sdate;
        $condition_param[] = $t04tb->edate;
        $condition_param[] = $t04tb->sdate;
        $condition_param[] = $t04tb->edate;
        $condition_param[] = $t04tb->class;
        $condition_param[] = $t04tb->term;

        /* 
        <<<審核條件三
        '同一年度內 , 學員不可重複報名相同的課程(班號相同)
        */
        $condition .= "UNION 
                      SELECT m02tb.idno,m02tb.cname, t13tb.dept , t13tb.class, t13tb.term, t13tb.idno, 'chk3' as `condition`, t01tb.name 
                      FROM t13tb 
                      JOIN t01tb ON t01tb.class = t13tb.class
                      JOIN m02tb ON m02tb.idno = t13tb.idno
                      WHERE t13tb.class = ? AND t13tb.term <> ? AND t13tb.class LIKE ? AND t13tb.idno IN (
                            SELECT idno 
                            FROM t39tb
                            WHERE class = ? AND term = ? AND prove IN ('X', 'N')
                      )";


        $condition_param[] = $t04tb->class;
        $condition_param[] = $t04tb->term;
        $condition_param[] = substr($t04tb->class, 0, 3).'%';
        $condition_param[] = $t04tb->class;
        $condition_param[] = $t04tb->term;
        /*        

        '<<<審核條件四
        '於本班上課期間 , 學員是否同時參加其它班別
        'A t13tb 班別學員資料檔
        'B t36tb 行事曆檔
        'C t01tb 班別基本資料檔

        */
        $condition .= "UNION
                       SELECT m02tb.idno,m02tb.cname, t13tb.dept, t13tb.class, t13tb.term, t13tb.idno, 'chk4' as `condition`, t01tb.name 
                       FROM t13tb
                       JOIN t01tb ON t01tb.class = t13tb.class
                       JOIN m02tb ON m02tb.idno = t13tb.idno
                       JOIN t04tb ON t04tb.class = t13tb.class AND t04tb.term = t13tb.term
                       JOIN t36tb ON t36tb.class = t13tb.class AND t36tb.term = t13tb.term
                       WHERE t13tb.class <> ? AND t13tb.term <> ? AND t13tb.class LIKE ? AND t13tb.idno IN (
                           SELECT idno 
                           FROM t39tb
                           WHERE class = ? AND term = ?	AND prove IN ('X', 'N')
                       ) AND (
                           (t36tb.`date` BETWEEN ? AND ?) OR
                           (t36tb.`date` BETWEEN ? AND ?)
                       )";
                      
        $condition_param[] = $t04tb->class;
        $condition_param[] = $t04tb->term;
        $condition_param[] = substr($t04tb->class, 0, 3).'%';
        $condition_param[] = $t04tb->class;
        $condition_param[] = $t04tb->term;
        $condition_param[] = $t04tb->sdate;
        $condition_param[] = $t04tb->edate;
        $condition_param[] = $t04tb->sdate;
        $condition_param[] = $t04tb->edate;
        // dd($condition_param);
        return DB::select($condition, $condition_param);
        // DB::select()
    }

    public function setCondition($t04tb_info, $check_result)
    {

        DB::beginTransaction();
        // DB::enableQueryLog();
        try {

            foreach ($check_result as $condition => $students){
                $students = $students->pluck('idno');
                $this->t39tbRepository->setCondition($t04tb_info, $students->toArray(), $condition);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
            // return back()->with('result', 0)->with('message', '更新失敗');
            var_dump($e->getMessage());
            die;
        }         
    }

    public function shiftT13tb($classInfo, $idnos)
    {
        
        // 檢查有無此學員報名資料
        $t13tbs = $this->t13tbRepository->getByIdnos($classInfo, $idnos)->keyBy('idno');
        $m02tbs = $this->m02tbRepository->getByIdnos($idnos)->keyBy('idno');
        $t39tbs = $this->t39tbRepository->getByIdnos($classInfo, $idnos)->keyBy('idno');


        $t39tbs = $t39tbs->map(function($t39tb){
            
            $t39tb = collect($t39tb->toArray());
            $newM02tb = $t39tb->only([
                'idno', 
                'cname', 
                'ename', 
                'sex', 
                'birth', 
                'dept', 
                'position', 
                'education',
                'rank',
                'ecode',
                'offfaxa',
                'offfaxb',
                'homtela',
                'homtelb',
                'mobiltel',
                'enrollid'
            ])->toArray();

            $newM02tb['offtela1'] = $t39tb['offtela'];
            $newM02tb['offtelb1'] = $t39tb['offtelb'];
            $newM02tb['offtelc1'] = $t39tb['offtelc'];

            $newT13tb = $t39tb->only([
                'idno',
                'dept',
                'position',
                'education',
                'rank',
                'ecode',
                'race',
                'fee'
            ])->toArray();

            return compact(['newM02tb', 'newT13tb']);
        });
        
        // 有 => 更新, 無 => 新增
        foreach ($t39tbs as $shiftData){
            if (isset($m02tbs[$shiftData['newM02tb']['idno']])){
                $this->m02tbRepository->update(['idno' => $shiftData['newM02tb']['idno']], $shiftData['newM02tb']);
            }else{
                $this->m02tbRepository->insert($shiftData['newM02tb']);
            }

            if (isset($t13tbs[$shiftData['newT13tb']['idno']])){
                $t13tbKey = array_merge($classInfo, ['idno' => $shiftData['newT13tb']['idno']]);
                $this->t13tbRepository->update($t13tbKey, $shiftData['newT13tb']);                
            }else{
                $this->t13tbRepository->insert($shiftData['newT13tb']);
            }
        }

    }

    public function getStudentOrNew($idno)
    {
        $m02tb = $this->m02tbRepository->find($idno);

        if (empty($m02tb)){
            $m02tb = new \App\Models\M02tb;
            $m02tb->idno = $idno;
            $m02tb->sex = 'M';    // 性別
            $m02tb->ecode = 7;    // 最高學歷
            $m02tb->rank = 20;    // 官職等
        }
        
        $m02tb->race = 1;     // 資料來源
        $m02tb->fee = 0;      // 費用
        return $m02tb;
    }

    public function parseImportApplyData($applyDatas)
    {
        $newApplyDatas = []; 
        $fields = [
            "idno",         // 身分證字號
            "cname",        // 名字
            "sex",          // 性別
            "ecode",        // 最高學歷
            "birth",        // 生日
            "education",    // 畢業學校
            "enrollid",     // 學員機關代碼
            "dept",         // 服務機關
            "position",     // 職稱
            "rank",         // 官職等
            "offaddr1",     // 機關縣市
            "offaddr2",     // 機關地址
            "offzip",       // 機關郵遞區號 
            "offtela",      // 機關電話區碼 (電話(公一)區碼)
            "offtelb",      // 機關電話 (電話(公一))
            "offtelc",      // 機關電話分機 (電話(公一)分機)
            "offfaxa",      // 傳真電話(區碼)
            "offfaxb",      // 傳真電話
            "email",        // 學員 email
            "homaddr1",     // 住家縣市
            "homaddr2",     // 住家地址
            "homzip",       // 住家郵遞區號
            "homtela",      // 住家電話(區碼)
            "homtelb",      // 住家電話
            "mobiltel",     // 行動電話
            "dorm",         // 住宿
            "vegan",        // 素食  
            "handicap",     // 行動不便
            "extradorm",    // 提前住宿
            "nonlocal",     // 遠道者
            "offname",      // 人事單位姓名
            "offtel",       // 人事單位電話
            "offemail",     // 人事單位信箱                
            "chief",        // 主管
            "personnel",    // 人事
            "aborigine",    // 原住民
        ];

        $withoutField = [
            "offaddr1",     // 機關縣市
            "offaddr2",     // 機關地址
            "offzip",       // 機關郵遞區號 
            "homaddr1",     // 住家縣市
            "homaddr2",     // 住家地址
            "homzip",       // 住家郵遞區號 
            "dorm",         // 住宿
            "vegan",        // 素食  
            "handicap",     // 行動不便
            "extradorm",    // 提前住宿
            "nonlocal",     // 遠道者 
            "offname",      // 人事單位姓名
            "offtel",       // 人事單位電話
            "offemail",     // 人事單位信箱                
            "chief",        // 主管
            "personnel",    // 人事
            "aborigine",    // 原住民                                  
        ];

        foreach ($applyDatas as $i => $applyData){
            if ($i < 7) continue;
            $newApplyData = [];
            foreach ($fields as $key => $field){
                if (!in_array($field, $withoutField)){
                    $newApplyData[$field] = $applyData[$key];
                }
            }
            $newApplyData['race'] = 1;
            $newApplyData['fee'] = 0;
            $newApplyData['ecode'] = substr($newApplyData['ecode'], 0, 1);
            $newApplyDatas[$i] = $newApplyData;
        }

        return collect($newApplyDatas); 
    }

    public function importT39tbs($t04tbKey, $newT39tbs)
    {
        $t39tbs = $this->t39tbRepository->getByt04tb($t04tbKey)->keyBy('idno');

        try {
            foreach ($newT39tbs as $newT39tb)
            {
                $t39tb = isset($t39tbs[$newT39tb['idno']]) ? $t39tbs[$newT39tb['idno']] : null;

                if (!isset($t39tb)){
                    $result = $this->createT39tb($t04tbKey, $newT39tb, $t39tb);
                }else{
                    $t39tbKey = $t04tbKey;
                    $t39tbKey['idno'] = $newT39tb['idno'];
                    $result = $this->updateT39tb($t39tbKey, $newT39tb, $t39tb);
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


}
