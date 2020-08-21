<?php
namespace App\Repositories;

use App\Models\M21tb;
use App\Models\M22tb;
use App\Models\M01tb;
use App\Models\intrust_users;


class Web_simulationRepository
{

    public function getWeb_simulation($queryData = [])
    {
        $data = array();
        $M21tb_data = M21tb::select('userid', 'username')->where('userid', $queryData['idno'])->first();
        if($M21tb_data){
            $data[] = array(
                'name' => $M21tb_data->username,
                'idno' => $M21tb_data->userid,
                'type' => 'admin',
            );
        }

        $M01tb_data = M01tb::select('idno', 'cname')->where('idno', $queryData['idno'])->first();
        if($M01tb_data){
            $data[] = array(
                'name' => $M01tb_data->cname,
                'idno' => $M01tb_data->idno,
                'type' => 'teacher',
            );
        }else{
            $M22tb_type2 = M22tb::select('userid', 'cname')->where('userid', $queryData['idno'])->where('usertype2', 'Y')->first();
            if($M22tb_type2){
                $data[] = array(
                    'name' => $M22tb_type2->cname,
                    'idno' => $M22tb_type2->userid,
                    'type' => 'teacher',
                );
            }
        }

        $M22tb_data = M22tb::select('userid', 'cname')->where('userid', $queryData['idno'])->first();
        if($M22tb_data){
            $data[] = array(
                'name' => $M22tb_data->cname,
                'idno' => $M22tb_data->userid,
                'type' => 'student',
            );
        }

        $intrust = intrust_users::select('idno', 'account')->where('idno', $queryData['idno'])->first();
        if($intrust){
            $data[] = array(
                'name' => $intrust->account,
                'idno' => $intrust->idno,
                'type' => 'IntrustUser',
            );
        }

        return $data;
    }

}
