<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FileService;


class UploadFileController extends Controller
{
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * 上傳檔案
     *
     * @param Request $request
     * @param $channel
     */
    public function upload(Request $request, $channel)
    {
        $config = config('upfile.'.$channel);

        // 檢查單元
        if ( ! $config) {
            // 單元不存在
            return response()->json(array('success' => 0, 'msg' => '單元不存在'));
        }

        // 檢查權限
        if ( ! $this->fileService->checkPermission($request, $config)) {
            // 權限不足
            return response()->json(array('success' => 0, 'msg' => '權限不足'));
        }

        // 檢查檔案
        if ( ! $request->file($config['name']) || ! $request->file($config['name'])->isValid()) {
            // 請上傳檔案
            return response()->json(array('success' => 0, 'msg' => '請上傳檔案'));
        }

        // 檢查副檔名
        if ( ! $this->fileService->checkExtension($request, $config)) {
            // 不允許的檔案格式
            return response()->json(array('success' => 0, 'msg' => '不允許的檔案格式'));
        }

        // 檢查大小
        if ( ! $this->fileService->checkFileSize($request, $config)) {
            // 不允許的檔案格式
            return response()->json(array('success' => 0, 'msg' => '檔案大小超過 '.config('app.upfile_max_size').'M'));
        }

        // 上傳
        $result = $this->fileService->uploadFile($request, $config);

        // 回傳
        return response()->json($result);
    }

    /**
     * 上傳檔案測試
     */
    public function testUpfile()
    {
        echo '<form action="/admin/upload/file/result" method="POST" enctype="multipart/form-data">';
        echo '<input type="file" name="file">';
        echo '<input type="text" name="_token" value="'.csrf_token().'">';
        echo '<input type="submit" value="送出">';
        echo '</form>';
    }
}
