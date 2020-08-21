<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Auth;


class UploadImageController extends Controller
{
    /**
     * 縮圖程式
     *
     * @param Request $request
     * @param $channel
     */
    public function upload(Request $request, $channel)
    {
        set_time_limit(900);

        $config = config('upimg.'.$channel);

        if ( ! $config) {

            return response()->json(['success' => false, 'msg' => '單元不存在']);
        }

        $imageService = new ImageService();

        $data = $imageService->begin($request, $config);

        if (isset($data['resize_all_path']) && $data['resize_all_path']) {

            $result = ['success' => true, 'img' => $data['resize_all_path']];
        } else {

            $result = ['success' => false, 'msg' => $data];
        }

        return response()->json($result);
    }

    /**
     * 縮圖測試
     */
    public function testUpimg()
    {
        echo '<form action="/admin/upload/image/news" method="POST" enctype="multipart/form-data">';
        echo '<input type="file" name="image">';
        echo '<input type="text" name="_token" value="'.csrf_token().'">';
        echo '<input type="submit" value="送出">';
        echo '</form>';
    }
}
