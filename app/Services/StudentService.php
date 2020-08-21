<?php
namespace App\Services;

use App\Helpers\Des;
use DateTime;
use DB;

use App\Repositories\M02tbRepository;
use App\Repositories\M22tbRepository;
use App\Repositories\M21tbRepository;
use App\Repositories\M13tbRepository;
use Hash;

class StudentService
{
    /**
     * StudentService constructor.
     * @param M02tbRepository $m02tbRepository
     */
    public function __construct(
        M02tbRepository $m02tbRepository,
        M22tbRepository $m22tbRepository,
        M21tbRepository $m21tbRepository,
        M13tbRepository $m13tbRepository
    )
    {
        $this->m02tbRepository = $m02tbRepository;
        $this->m22tbRepository = $m22tbRepository;
        $this->m21tbRepository = $m21tbRepository;
        $this->m13tbRepository = $m13tbRepository;
    }

    public function getStudents($queryData)
    {
        $students = $this->m02tbRepository->get($queryData, true, ['m17tb']);
        foreach ($students as $student){
            $student->des_idno = Des::encode($student->idno, 'KLKLKL');
        }
        return $students;
    }

    public function getStudent($idno)
    {
        $student = $this->m02tbRepository->find($idno);
        if (empty($student)){
            return null;
        }
        $student->des_idno = Des::encode($student->idno, 'KLKLKL');
        return $student;
    }

    public function storeM02tb($idno, $m02tb_data)
    {
        return $this->m02tbRepository->update(['idno' => $idno], $m02tb_data); 
    }

    public function storeM22tb($info, $m22tb)
    {
        $now = new DateTime();
        $m22tb['account'] = (empty($m22tb['account'])) ? $info['userid'] : $m22tb['account'];
        $m22tb['upddate'] = $now->format("Y-m-d H:i:s");
        $m22tb['status'] = (isset($m22tb['status'])) ? 'Y' : 'N';

        if (empty($m22tb['userpsw'])){
            unset($m22tb['userpsw']);
        }else{
            // $m22tb['userpsw'] = Hash::make($m22tb['userpsw']);
            $m22tb['userpsw'] = md5($m22tb['userpsw']);
        }

        return $this->m22tbRepository->updateOrCreate($info, $m22tb);
    }

    public function updateM22tbUserType2($idno, $status)
    {
        return $this->m22tbRepository->update(['userid' => $idno], ['usertype2' => $status]);
    }

    public function storeM21tb($info, $m21tb)
    {
        // $m21tb['selfid'] = (empty($m21tb['selfid'])) ? $info['userid'] : $m21tb['selfid'];
        $m21tb['status'] = (isset($m21tb['status'])) ? 'Y' : 'N';

        $now = new DateTime();
        $m21tb['crtdate'] = $now->format("Y-m-d H:i:s");

        if (empty($this->m21tbRepository->find($info))){
            $m21tb = array_merge($info, $m21tb);

            if (empty($m21tb['userpsw'])){
                $m21tb['userpsw'] = 'csdi1234';
            }

            $m21tb['userpsw'] = md5($m21tb['userpsw']);
            return $this->m21tbRepository->insert($m21tb);
        }else{

            if (empty($m21tb['userpsw'])){
                unset($m21tb['userpsw']);
            }else{
                $m21tb['userpsw'] = md5($m21tb['userpsw']);
            }     

            return $this->m21tbRepository->update($info, $m21tb);
        }
        
    }

    public function getM13tbs()
    {
        return $this->m13tbRepository->getData();
    }

    public function reset($idno, $resetIdentity, $resetType)
    {
        // dd($idno, $resetIdentity, $resetType);

        DB::beginTransaction();

        try {

            if ($resetIdentity == 'sponsor'){
                if ($resetType == 'password'){
                    // $this->m21tbRepository->update(['userid' => $idno], ['userpsw' => Hash::make('csdi1234')]);
                    $this->m21tbRepository->update(['userid' => $idno], ['userpsw' => md5('csdi1234')]);
                }elseif ($resetType == 'passwordCnt'){
                    $this->m21tbRepository->update(['userid' => $idno], ['pswerrcnt' => 0, 'status' => 'Y']);
                }
            }elseif ($resetIdentity == 'student'){
                $m02tb = $this->m02tbRepository->find($idno);
                if ($resetType == 'password'){
                    if (!empty($m02tb->birth)){
                        // $this->m22tbRepository->update(['userid' => $idno], ['userpsw' => Hash::make($m02tb->birth)]);
                        $this->m22tbRepository->update(['userid' => $idno], ['userpsw' => md5($m02tb->birth)]);
                    }else{
                        return false;
                    }
                }elseif ($resetType == 'passwordCnt'){
                    $this->m22tbRepository->update(['userid' => $idno], ['pswerrcnt' => 0, 'status' => 'Y']);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            // return false;
            var_dump($e->getMessage());
            die;
        }

    }

    public function modifyIdno($idno, $newIdno)
    {
        DB::beginTransaction();

        try {
            $updateData = [$newIdno, $idno];
            // 【m02tb 學員基本資料檔】
            DB::update("UPDATE m02tb SET idno = ? WHERE idno = ?", $updateData);
            // 【t13tb 班別學員資料檔】
            DB::update("UPDATE t13tb SET idno = ? WHERE idno = ?", $updateData);
            // 【t14tb 學員請假資料檔】
            DB::update("UPDATE t14tb SET idno = ? WHERE idno = ?", $updateData);
            // 【t15tb 學員成績資料檔】
            DB::update("UPDATE t15tb SET idno = ? WHERE idno = ?", $updateData);
            // 【t40tb 委辦班別經費支付檔】
            DB::update("UPDATE t40tb SET idno = ? WHERE idno = ?", $updateData);
            // 【t41tb 收據明細檔】
            DB::update("UPDATE t41tb SET idno = ? WHERE idno = ?", $updateData);
            // 【t45tb 委辦班別調期資料檔】
            DB::update("UPDATE t45tb SET idno = ? WHERE idno = ?", $updateData);
            // 【m22tb 網填個人帳號檔】
            DB::update("UPDATE m22tb SET userid = ? WHERE userid = ?", $updateData);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            // return false;
            var_dump($e->getMessage());
            die;
        }        
    }
}