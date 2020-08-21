<?php
namespace App\Services;

use App\Repositories\FaqRepository;
use App\Http\Controllers\Controller;


class FileService extends Controller
{
    /**
     * 檢查權限
     *
     * @return bool
     */
    public function checkPermission($request, $config)
    {
        // 檢查權限..
    }

    /**
     * 檢查副檔名
     *
     * @param $request
     * @param $config
     * @return bool
     */
    public function checkExtension($request, $config)
    {
        // 取得副檔名
        $extension = $request->file($config['name'])->getClientOriginalExtension();

        // 檢查副檔名是否允許
        return ( ! in_array($extension, config('app.extension_pass')))? false : true;
    }

    /**
     * 檢查檔案大小
     *
     * @param $request
     * @param $config
     * @return bool
     */
    public function checkFileSize($request, $config)
    {
        // 取得檔案大小
        $fileSize = $request->file($config['name'])->getSize();

        // 檢查是否超過
        return ($fileSize > (config('app.upfile_max_size') * 1048576))? false : true;
    }

    /**
     * 上傳檔案
     *
     * @param $request
     * @param $config
     * @return array
     */
    public function uploadFile($request, $config)
    {
        $file = $request->file('file');

        // 檔案大小
        $size = $file->getSize();
        // 取得原始檔名
        $original_name = str_replace('.'.$file->getClientOriginalExtension(), '', $file->getClientOriginalName());
        // 儲存的路徑
        $destinationPath = base_path() . $config['path'];
        // 取得副檔名
        $extension = $file->getClientOriginalExtension();
        // 新檔名
        $fileName = md5(uniqid(mt_rand())) . '.' . $extension;
        // 移動到墓地路徑
        $file->move($destinationPath, $fileName);

        $result = [
            'success' => 1,
            'data' => [
                'name' => $original_name,
                'extension' => $extension,
                'path' => $config['path'] . $fileName,
                'size' => $size
            ]
        ];

        return $result;
    }
}
