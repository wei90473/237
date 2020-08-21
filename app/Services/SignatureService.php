<?php
namespace App\Services;

use DB;
use App\Repositories\SignatureRepository;

class SignatureService
{
    /**
     * SignupService constructor.
     * @param  
     */
    public function __construct(
        SignatureRepository $signatureRepository
    )
    {
        $this->signatureRepository = $signatureRepository;
    }

    public function getSignatures($queryData)
    {
        return $this->signatureRepository->get($queryData);
    }

    public function getSignature($id)
    {
        return $this->signatureRepository->find($id);
    }

    public function storeSignature($signature, $action, $id = null)
    {
        if (empty($signature['sort'])){
            $signature['sort'] = $this->signatureRepository->getFinallySort()->sort + 1;
        }

        if ($action == "create"){
            return $this->signatureRepository->insert($signature);
        }elseif($action == "edit"){
            $origin_signature = $this->signatureRepository->find($id);
            $origin_img_path = $origin_signature->img_path;
            $update = $origin_signature->update($signature);

            if (isset($signature['img_path']) && $update){
                if (!empty($origin_signature)){
                    $origin_file = public_path().'/Uploads/signatures/'.$origin_img_path;
                    if (file_exists($origin_file)) unlink($origin_file);
                }
            }

            return $update;
        }
    }

    public function deleteSignature($id)
    {
        $signature = $this->signatureRepository->find($id);

        if (!empty($signature->img_path)){
            $origin_file = public_path().'/Uploads/signatures/'.$signature->img_path;
            if (file_exists($origin_file)) unlink($origin_file);
        }

        return $signature->delete();
    }
}