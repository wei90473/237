<?php
//**********************************//
//		縮圖程式庫v2.4L				//
//**********************************//
namespace App\Services;


class ImageService
{
    //上傳的config
    public $upload = array(
        'upload_path'	=> '',              //上傳路徑
        'allowed_types' => 'jpg|png|jpeg|JPG|PNG|JPEG',  //允許上傳的副檔名
        'encrypt_name'  => TRUE             //圖檔上傳後重新命名
    );
    //預設值
    public $preset = array(
        'width_position'=> 0,           //左右位置
        'high_position' => 0,           //上下位置
        'disperse'      => true,        //年月日分資料夾
        'tranparent'    => false,       //是否透明
        'color'         => array(255,255,255),//補色的顏色
        'method'        => 5,           //縮圖方式
        'percentage'    => 20,          //截切超過百分比補色
        'return'        => false,       //回傳值
        'deloriginal'   => false,       //刪除原始圖
        'prefix'        => '',          //縮圖前綴字
        'toosmall'      => 2,           //當圖片小於縮圖尺寸時(單邊)
        'c_toosmall'    => 'original',  //當圖片小於縮圖尺寸時(雙邊)
        'check_size'    => false        //array('width'=>'200', 'high'=>'200')//低於此大小回傳錯誤

    );
    //錯誤訊息
    public $errorMessages;
    //除錯開發模式
    public $debugMode = false;
    //上傳的日期路徑
    public $datePath;

    public function __construct()
    {
        $this->datePath = date('Y').'/'.date('m').'/'.date('d').'/';
    }
    //******************//
    //	縮圖主程式		//
    //******************//
    public function begin($request, $config = NULL)
    {
        $this->request = $request;

        //檢查初始化config
        if ( !$this->checkBaseConfig($config) )
        {
            return $this->errorMessages;
        }

        if ( is_array($_FILES[$this->preset['name']]['name']) )
        {
            //====================批次上傳====================
            //多張上傳,新增變數儲存上傳檔案資料
            $files = $_FILES[$this->preset['name']];
            //計算上傳多少檔案
            $filesCount = count($files['name']);
            //刪除原生變數
            unset($_FILES);
            //新增回傳的變數
            $returnResult = array();
            //迴圈上傳
            for ( $i = 0; $i < $filesCount ; $i ++) {
                //整理圖檔資料
                $_FILES[$this->preset['name']]['name'] 		= $files['name'][$i];
                $_FILES[$this->preset['name']]['type']		= $files['type'][$i];
                $_FILES[$this->preset['name']]['tmp_name']	= $files['tmp_name'][$i];
                $_FILES[$this->preset['name']]['error']		= $files['error'][$i];
                $_FILES[$this->preset['name']]['size']		= $files['size'][$i];
                //上傳
                array_push($returnResult, $this->imageBin());
            }
        }
        else
        {
            //==================== 單張上傳 ====================
            $returnResult = $this->imageBin();
        }
        return $returnResult;
    }

    //******************//
    //	上傳並縮圖處理		//
    //******************//
    public function imageBin()
    {
        //檢查是否上傳成功
        if ( ! $this->request->hasFile($this->preset['name']) )
        {
            //上傳失敗返回錯誤訊息
            return '上傳失敗,找不到上傳的檔案';exit;
        }
        else
        {
            //取得檔案
            $file = $this->request->file($this->preset['name']);
            //取得副檔名
            $file_ext = $file->getClientOriginalExtension();
            //檢查副檔名
            if ( ! in_array($file_ext, explode("|", $this->upload['allowed_types'])) )
            {
                return '不允許的副檔名';exit;
            }
            //取得圖片資訊
            $file_img_size = getimagesize($file->getRealPath());
            //重新命名圖片
            $file_name = md5(uniqid(mt_rand())).'.'.$file_ext;
            //移動圖片
            $this->request->file($this->preset['name'])->move($this->upload['upload_path'], $file_name);
            //擷取需要用到的資料
            $fileInfo = array(
                'file_name' => $file_name,
                'file_ext' => '.'.$file_ext,
                'full_path' => $this->upload['upload_path'].$file_name,
                'image_width' => $file_img_size['0'],
                'image_height' => $file_img_size['1'],
                'image_size_str' => $file_img_size['3'],
            );
            //檢查是否低於最小大小
            if ( $this->preset['check_size'] )
            {
                if ( $fileInfo['image_width'] < $this->preset['check_size']['width'] || $fileInfo['image_height'] < $this->preset['check_size']['high']  )
                {
                    //圖片太小回傳錯誤
                    return 'size_error';
                }
            }
            $allImgData = array();
            //迴圈跑各尺寸縮圖
            foreach ( $this->preset['size'] as  $va )
            {
                //取得這個尺寸的config
                $sizeConfig = $this->getSizeConfig($va);

                if ( !$sizeConfig )
                {
                    return $this->errorMessages;
                }
                // 使用6 7另一邊為0
                // 20181108拿掉
                // if ( $sizeConfig['method'] == 6 ){ $va['high'] = 0 ;}
                // if ( $sizeConfig['method'] == 7 ){ $va['width'] = 0 ;}

                //檢查圖片是否小於縮圖尺寸
                if ( $fileInfo['image_width'] < $va['width'] && $fileInfo['image_height'] < $va['high'] && $sizeConfig['method'] != 6 && $sizeConfig['method'] != 7)
                {
                    //長寬都小於縮圖尺寸
                    $sizeConfig['method'] = $this->preset['c_toosmall'];

                }
                else if (  $fileInfo['image_width'] < $va['width'] || $fileInfo['image_height'] < $va['high'] )
                {
                    //長或寬其中一邊小於縮圖尺寸
                    if ( $sizeConfig['method'] == 6 || $sizeConfig['method'] == 7 )
                    {
                        $sizeConfig['method'] = 'original';
                    }
                    else
                    {
                        $sizeConfig['method'] = $this->preset['toosmall'];
                    }
                }

                //debug
                if ( $this->debugMode ){$this->debug( $fileInfo, $sizeConfig , array('width'=>$va['width'], 'high'=>$va['high']) );}

                //縮圖方式
                switch ( $sizeConfig['method'] ) {
                    //尺寸不變
                    case 'original' : $result = $this->caseoriginal( $fileInfo, $sizeConfig , array('width'=>$va['width'], 'high'=>$va['high']) );break;
                    //圖片太小時使用原圖補色
                    case 0 : $result = $this->case0( $fileInfo, $sizeConfig , array('width'=>$va['width'], 'high'=>$va['high']) );break;
                    //截切
                    case 1 : $result = $this->case1( $fileInfo, $sizeConfig , array('width'=>$va['width'], 'high'=>$va['high']) );break;
                    //補色
                    case 2 : $result = $this->case2( $fileInfo, $sizeConfig , array('width'=>$va['width'], 'high'=>$va['high']) );break;
                    //固定某一邊
                    case 3 :
                    case 4 : $result = $this->case34( $fileInfo, $sizeConfig , array('width'=>$va['width'], 'high'=>$va['high']) );break;
                    //百分比
                    case 5 : $result = $this->case5( $fileInfo, $sizeConfig , array('width'=>$va['width'], 'high'=>$va['high']) );break;
                    //6:等比例縮到指定寬,7:等比例縮到指定高
                    case 6 :
                    case 7 : $result = $this->case67( $fileInfo, $sizeConfig , array('width'=>$va['width'], 'high'=>$va['high']) );break;
                }

                //開始縮圖
                $result = $this->imageReSize($result);
                array_push($allImgData, substr($result['newPath'], 1));
            }
            //檢查是否刪除原始圖
            if ( $this->preset['deloriginal'] )
            {
                //刪除前檢查檔案是否存在
                if ( file_exists($fileInfo['full_path']) )
                {
                    unlink($fileInfo['full_path']);
                }
            }
            //回傳值
            $returnData = array(
                'prefix' => $this->preset['prefix'],//前綴字
                'original_all_path' => substr($result['path'], 1),// 原始圖完整路徑
                'original_img' => $fileInfo['file_name'],// 原始圖檔名
                'resize_all_path' => substr($result['newPath'], 1),//縮圖完整路徑
                'resize_path' => substr($result['filePath'], 1),// 縮圖路徑
                'resize_date_path' => $result['datePath'],// 縮圖日期路徑
                'resize_img' => $result['newFileName'],// 縮圖檔名
                'all_size_path' => $allImgData//所有縮圖完整路徑
            );
            if ( is_array($this->preset['return']) )
            {
                //回傳字串,迴圈組成字串
                $returnString = '';
                foreach ( $this->preset['return'] as $va )
                {
                    $returnString .= $returnData[$va];
                }
                return $returnString;
            }
            else
            {
                //回傳陣列
                return $returnData;
            }
        }
    }

    //******************//
    //	取得size的config	//
    //******************//
    public function getSizeConfig($va)
    {
        $config = $this->preset;
        if ( count($va) > 2 )
        {
            //特殊參數合法得值
            $valueAry = array('path', 'width_position', 'high_position', 'method', 'tranparent', 'color', 'disperse', 'percentage', 'toosmall');
            foreach ( $va as $key=>$vb )
            {
                if ( in_array($key, $valueAry) )
                {
                    $config[$key] = $vb;

                    if ( $key == 'disperse' )
                    {
                        //年月日變更
                        if ( $vb )
                        {
                            //變更為使用年月日
                            $config['datePath'] = $this->datePath;
                        }
                        else
                        {
                            //變更為不使用年月日
                            $config['datePath'] = '';
                        }
                    }
                    else if ( $key == 'path' )
                    {
                        //路徑變更
                        $config['path'] = $vb;

                    }

                }

            }
            //檢查資料夾
            if ( !is_dir($config['path'].$config['datePath']) )
            {
                //不存在,建立資料夾
                if ( !@mkdir($config['path'].$config['datePath'],0777,true) ){
                    //建立資料夾失敗
                    $this->errorMessages = '建立資料夾失敗,請檢查權限';return FALSE;
                }
            }
        }
        return $config;
    }

    //******************//
    //		縮圖處理		//
    //******************//
    public function imageReSize( $config ){
        //建立一個自訂寬度,預設黑色
        $newImagePic = imagecreatetruecolor( $config['newImagePicWidth'], $config['newImagePicHigh'] );
        if ( $config['tranparent'] )
        {
            //========== 設定為透明色 ==========
            //設定一個顏色
            $color = imagecolorallocate($newImagePic, 255, 255, 255);
            //填滿顏色
            imagefill($newImagePic, 0, 0, $color); // 填滿白色 (背景)
            //將顏色設定為透明
            imagecolortransparent($newImagePic, $color);
        }
        elseif ( $config['color'] != false && isset($config['color'][0]) &&  isset($config['color'][1]) && isset($config['color'][2]) )
        {
            //========== 設定為指定顏色 ==========
            $color = imagecolorallocate($newImagePic, $config['color'][0], $config['color'][1], $config['color'][2]);
            //填滿顏色
            imagefill($newImagePic, 0, 0, $color); // 填滿白色 (背景)
        }
        //判斷副檔名,建立對應的圖像(圖像來源為原始圖片)
        if ( strtolower($config['file_ext']) == ".jpg" )
        {
            $image = imagecreatefromjpeg( $config['path'] );
        }
        elseif ( strtolower($config['file_ext']) == ".png" )
        {
            $image = imagecreatefrompng ( $config['path'] );
        }
        elseif ( strtolower($config['file_ext']) == ".jpeg")
        {
            $image = imagecreatefromjpeg( $config['path'] );
        }
        //將圖像複製到另一個圖像(原始圖像 to 新的圖像)
        imagecopyresampled(
            $newImagePic,	//新目標圖像
            $image,		//來源圖片
            $config['dst_x'],		//目標檔案開始點的 x 座標
            $config['dst_y'],		//目標檔案開始點的 y 座標
            $config['src_x'],		//來源檔案開始點的 x 座標
            $config['src_y'],		//來源檔案開始點的 y 座標
            $config['dst_w'],		//目標檔案的長度
            $config['dst_h'],		//目標檔案的高度
            $config['src_w'],		//來源檔案的長度
            $config['src_h']		//來源檔案的高度
        );
        //檢查是否使用到透明色
        if ( $config['tranparent'] && isset($config['computeColor']) && $config['computeColor'] == true )
        {
            if ( $config['newImagePicWidth'] != $config['dst_w'] || $config['newImagePicHigh'] != $config['dst_h'] )
            {
                $isExportPng = true;
                //$allowedTypesAry = array('.jpg', '.jpeg');
                //將副檔名換成png
                // foreach( $allowedTypesAry as $va )
                // {
                // 	$config['newPath'] = str_replace($va, '.png', $config['newPath']);
                // 	$config['newFileName'] = str_replace($va, '.png', $config['newFileName']);
                // 	$config['originalFileName'] = str_replace($va, '.png', $config['originalFileName']);
                // }
            }
        }
        //判斷副檔名，使用對應函式將檔案存入,使用透明色時輸出png
        if ( isset($isExportPng) && $isExportPng) { imagepng ( $newImagePic, $config['newPath'], (9/$config['quality']) );} else
            if ( strtolower($config['file_ext']) == ".jpg" ) {imagejpeg( $newImagePic, $config['newPath'], $config['quality']);} else
                if ( strtolower($config['file_ext']) == ".png" ) {imagepng ( $newImagePic, $config['newPath'], (9/$config['quality']) );} else
                    if ( strtolower($config['file_ext']) == ".jpeg") {imagejpeg( $newImagePic, $config['newPath'], $config['quality'] );}
        //釋放資源
        imagedestroy( $newImagePic );
        //是否debug
        if ( $this->debugMode ){echo '<b>縮圖時參數:</b><pre>';print_r($config);echo '</pre>';echo '<img src="'.str_replace('./','/',$config['newPath']).'"></br></br></br>';}
        return $config;
    }

    //******************//
    //	 檢查初始化參數	//
    //******************//
    public function checkBaseConfig($config)
    {
        //==========檢查必填區==========
        //檢查檔名
        if ( !isset($config['name']) || !$config['name'] )
        {
            $this->errorMessages = '未設定post name';return FALSE;
        }
        //檢查路徑
        if ( !isset($config['path']) || !$config['path'] || !is_dir($config['path']) )
        {
            $this->errorMessages = '路徑不存在'.$config['path'];return FALSE;
        }
        //檢查縮圖尺寸
        if ( !isset($config['size']) )
        {
            $this->errorMessages = '未設定縮圖尺寸';return FALSE;
        }
        //檢查縮圖尺寸格式
        if ( !isset($config['size']) || !is_array($config['size']) )
        {
            $this->errorMessages = '縮圖尺寸格式錯誤';return FALSE;
        }
        else
        {
            foreach ( $config['size'] as $key=>$va )
            {
                //檢查尺寸
                if ( !isset($va['width']) || !isset($va['high']) ||  !is_numeric($va['width']) || !is_numeric($va['high']) )
                {
                    $this->errorMessages = '縮圖尺寸格式錯誤';return FALSE;
                }
                //檢查路徑
                if ( isset($va['path']) && !is_dir($va['path']) )
                {
                    $this->errorMessages = '路徑錯誤'.$va['path'];return FALSE;
                }
                //確認路徑結尾有"/"
                if ( isset($va['path']) && substr($va['path'], -1) != '/' )
                {
                    $config['size'][$key]['path'] = $va['path'].'/';
                }
            }
        }
        //檢查路徑最後一定要有"/",沒有時補上
        if ( substr( $config['path'], -1 ) != '/' )
        {
            $config['path'] = $config['path'].'/';
        }
        //檢查是否要依照年月日分資料夾
        if ( !isset($config['disperse']) )
        {
            $config['disperse'] = $this->preset['disperse'];
        }
        //執行是否依年月日分資料夾
        if ( $config['disperse'] )
        {
            $config['datePath'] = $this->datePath;
            //上傳原圖路徑
            $this->upload['upload_path'] = $config['path'].$config['datePath'];
        }
        else
        {
            $config['datePath'] = '';
            //上傳原圖路徑
            $this->upload['upload_path'] = $config['path'];
        }
        //檢查資料夾
        if ( !is_dir($config['path'].$config['datePath']) )
        {
            //不存在,建立資料夾
            if ( !@mkdir($config['path'].$config['datePath'],0777,true) ){
                //建立資料夾失敗
                $this->errorMessages = '建立資料夾失敗,請檢查權限';return FALSE;
            }
        }
        //==========預設值==========
        //是否刪除原始圖
        if ( !isset($config['deloriginal']) )
        {
            $config['deloriginal'] = $this->preset['deloriginal'];
        }
        //回傳值
        if ( !isset($config['return']) )
        {
            $config['return'] = $this->preset['return'];
        }

        //位置會存在的合法值
        $positionAry = array('0','1','2');
        //左右位置
        if ( !isset($config['width_position']) || !in_array($config['width_position'], $positionAry) )
        {
            $config['width_position'] = $this->preset['width_position'];
        }
        //上下位置
        if ( !isset($config['high_position']) || !in_array($config['high_position'], $positionAry) )
        {
            $config['high_position'] = $this->preset['high_position'];
        }
        //是否透明
        if ( !isset($config['tranparent']) )
        {
            $config['tranparent'] = $this->preset['tranparent'];
        }
        //顏色
        if ( !isset($config['color']) )
        {
            $config['color'] = $this->preset['color'];
        }
        //縮圖方式會存在的合法值
        $methodAry = array('1','2','3','4','5','6','7');
        //縮圖方式
        if ( !isset($config['method']) || !in_array($config['method'], $methodAry) )
        {
            $config['method'] = $this->preset['method'];
        }
        //當上傳圖片太小時(單邊)
        if ( !isset($config['toosmall']) )
        {
            $config['toosmall'] = $this->preset['toosmall'];
        }
        //當上傳高跟寬都太小時
        if ( !isset($config['c_toosmall']) )
        {
            $config['c_toosmall'] = $this->preset['c_toosmall'];
        }
        //截切超過此值使用補色
        if ( !isset($config['percentage']) || !$config['percentage'] || !is_numeric($config['percentage']) )
        {
            $config['percentage'] = $this->preset['percentage'];
        }
        //最小大小
        if ( !isset($config['check_size']['width']) || !$config['check_size']['width'] || !is_numeric($config['check_size']['width']) || !isset($config['check_size']['high']) || !$config['check_size']['high'] || !is_numeric($config['check_size']['high']) )
        {
            $config['check_size'] = $this->preset['check_size'];
        }
        //縮圖前綴字
        if ( !isset($config['prefix']) )
        {
            $config['prefix'] = $this->preset['prefix'];
        }
        $this->preset = $config;
        return TRUE;
    }

    //**********************************//
    //			太小時維持原本大小			//
    //**********************************//
    public function caseoriginal( $fileInfo, $sizeConfig ,$newSize )
    {
        //取得預設值
        $result = $this->getDefaultValue($fileInfo, $sizeConfig ,$newSize);
        $result['newImagePicWidth'] = $fileInfo['image_width'] ;//目標檔案的寬度
        $result['newImagePicHigh'] = $fileInfo['image_height'] ;//目標檔案的高度
        $result['dst_w'] = $fileInfo['image_width'] ;//目標檔案的寬度
        $result['dst_h'] = $fileInfo['image_height'] ;//目標檔案的高度

        return $result;
    }

    //**********************************//
    //			太小時原尺寸補色			//
    //**********************************//
    public function case0( $fileInfo, $sizeConfig ,$newSize )
    {
        //取得預設值
        $result = $this->getDefaultValue($fileInfo, $sizeConfig ,$newSize);
        //是補色
        $result['computeColor'] = true;
        $result['dst_w'] = $fileInfo['image_width'] ;//目標檔案的寬度
        $result['dst_h'] = $fileInfo['image_height'] ;//目標檔案的高度
        //========== 高 ==========
        //置中
        if ( $sizeConfig['width_position'] == 0 ) {$result['dst_y'] = ($newSize['high'] - $result['dst_h']) / 2;}
        //補上邊
        if ( $sizeConfig['width_position'] == 2 ){$result['dst_y'] = $newSize['high'] - $result['dst_h'] + 1;}
        //預設補下
        //========== 寬 ==========
        //置中
        if ( $sizeConfig['width_position'] == 0 ) {$result['dst_x'] = ($newSize['width'] - $result['dst_w']) / 2;}
        //補右邊
        if ( $sizeConfig['width_position'] == 2 ){$result['dst_x'] = $newSize['width'] - $result['dst_w'] + 1;}
        //預設補左
        return $result;
    }


    //**********************************//
    //			最短邊縮小後截切			//
    //**********************************//
    public function case1( $fileInfo, $sizeConfig ,$newSize ){
        //取得預設值
        $result = $this->getDefaultValue($fileInfo, $sizeConfig ,$newSize);
        //計算是比較長還是比較寬
        $countWidth = $fileInfo['image_width'] / $newSize['width'];
        $countHigh = $fileInfo['image_height'] / $newSize['high'];
        if ( $countWidth > $countHigh ) {
            //====================圖片較 寬 截圖計算====================
            //計算出這個比例下寬應該多少才不會超出
            $result['src_w'] = $countHigh * $newSize['width'];
            //計算多出來幾公分
            $surplus = $fileInfo['image_width'] - $result['src_w'];
            //置中
            if ( $sizeConfig['width_position'] == 0 ) {$result['src_x'] = ($surplus / 2) ;}
            //截右邊
            if ( $sizeConfig['width_position'] == 2 ){$result['src_x'] = $surplus;}
            //預設靠左
        } else if ( $countWidth < $countHigh ){
            //====================圖片較 高 截圖計算====================
            //計算出這個比例下高應該多少才不會超過
            $result['src_h'] = $countWidth * $newSize['high'];
            //計算多出來多少
            $surplus = $fileInfo['image_height'] - $result['src_h'];
            //置中
            if ( $sizeConfig['high_position'] == 0 ) {$result['src_y'] = ($surplus / 2) ;}
            //截下面
            if ( $sizeConfig['high_position'] == 2 ){$result['src_y'] = $surplus;}
            //預設截上
        }
        return $result;
    }


    //**********************************//
    //			計算補色縮圖所需的資料		//
    //**********************************//
    public function case2( $fileInfo, $sizeConfig ,$newSize ){
        //取得預設值
        $result = $this->getDefaultValue($fileInfo, $sizeConfig ,$newSize);
        //是補色
        $result['computeColor'] = true;
        //計算是比較長還是比較寬
        $countWidth = $fileInfo['image_width'] / $newSize['width'];
        $countHigh = $fileInfo['image_height'] / $newSize['high'];
        if ( $countWidth > $countHigh ) {
            //====================圖片較 寬 補高計算====================
            //計算等比例高縮到多少才不會讓寬超過
            $result['dst_h'] = $fileInfo['image_height'] / $countWidth ;
            //置中
            if ( $sizeConfig['high_position'] == 0 ) {$result['dst_y'] = ($newSize['high'] - $result['dst_h']) / 2;}
            //補上邊
            if ( $sizeConfig['high_position'] == 2 ){$result['dst_y'] = $newSize['high'] - $result['dst_h'] + 1;}
            //預設補下
        } else if ( $countWidth < $countHigh ){
            //====================圖片較 高 補寬計算====================
            //計算等比例寬縮到多少才不會讓高超過
            $result['dst_w'] = $fileInfo['image_width'] / $countHigh ;
            //置中
            if ( $sizeConfig['width_position'] == 0 ) {$result['dst_x'] = ($newSize['width'] - $result['dst_w']) / 2;}
            //補右邊
            if ( $sizeConfig['width_position'] == 2 ){$result['dst_x'] = $newSize['width'] - $result['dst_w'] + 1;}
            //預設補左
        }
        return $result;
    }


    //**********************************//
    //			計算固定某一邊				//
    //**********************************//
    public function case34( $fileInfo, $sizeConfig ,$newSize ){
        //取得預設值
        $result = $this->getDefaultValue($fileInfo, $sizeConfig ,$newSize);
        //判斷是固定寬還是固定高
        if ( $sizeConfig['method'] == '3' ){
            //====================固定高====================
            //計算高縮小的比例
            $countHigh = $fileInfo['image_height'] / $newSize['high'];
            //取得同比例寬為多少
            $countWidth = $fileInfo['image_width'] / $countHigh;
            if ( $countWidth > $newSize['width'] ){
                //截切寬
                //計算多出多少
                $surplus = $countHigh * ($countWidth - $newSize['width']);
                $result['src_w'] = $fileInfo['image_width'] - $surplus;
                //置中
                if ( $sizeConfig['width_position'] == 0 ) {$result['src_x'] = ($surplus / 2);}
                //截左邊
                if ( $sizeConfig['width_position'] == 2 ){$result['src_x'] = $surplus;}
                //預設截右邊
            } else if ( $countWidth < $newSize['width'] ){
                //補色
                //目標內縮圖的寬
                $result['dst_w'] = $countWidth;
                //置中
                if ( $sizeConfig['width_position'] == 0 ) {$result['dst_x'] = ($newSize['width'] - $result['dst_w']) / 2;}
                //補上邊
                if ( $sizeConfig['width_position'] == 2 ){$result['dst_x'] = $newSize['width'] - $result['dst_w'] + 1;}
                //預設補下
            }
        } else if ( $sizeConfig['method'] == '4' ){
            //====================固定寬====================
            //計算寬縮小的比例
            $countWidth = $fileInfo['image_width'] / $newSize['width'];
            //取得同比例高為多少
            $countHigh = $fileInfo['image_height'] / $countWidth;
            if ( $countHigh > $newSize['high'] ){
                //截切高
                //計算多出多少
                $surplus = $countWidth * ($countHigh - $newSize['high']);
                $result['src_h'] = $fileInfo['image_height'] - $surplus;
                //置中
                if ( $sizeConfig['high_position'] == 0 ) {$result['src_y'] = ($surplus / 2) ;}
                if ( $sizeConfig['high_position'] == 2 ){$result['src_y'] = $surplus;}
                //預設截上
            } if( $countHigh < $newSize['high'] ){
                //補色
                //目標內縮圖的高
                $result['dst_h'] = $countHigh;
                //置中
                if ( $sizeConfig['high_position'] == 0 ) {$result['dst_y'] = ($newSize['high'] - $result['dst_h']) / 2;}
                //補上邊
                if ( $sizeConfig['high_position'] == 2 ){$result['dst_y'] = $newSize['high'] - $result['dst_h'] + 1;}
                //預設補下
            }
        }
        return $result;
    }


    //**********************************//
    //			計算百分比是否使用縮圖		//
    //**********************************//
    public function case5( $fileInfo, $sizeConfig ,$newSize ){
        //寬縮小的比例
        $countWidth = $fileInfo['image_width'] / $newSize['width'];
        //高縮小的比例
        $countHigh = $fileInfo['image_height'] / $newSize['high'];
        //比較寬跟高哪個縮小的比例多
        if ( $countWidth > $countHigh ) {
            //計算出這個比例下寬應該多少才不會超出
            $surplus = $fileInfo['image_width'] - ($countHigh * $newSize['width']);
            //計算截切比例
            $percentage = $surplus / $fileInfo['image_width'];
        } else if ( $countWidth < $countHigh ){
            //計算出這個比例下高應該多少才不會超過
            $surplus = $fileInfo['image_height'] - ($countWidth * $newSize['high']);
            //計算截切比例
            $percentage = $surplus / $fileInfo['image_height'];
        } else {
            //等比例
            $percentage = 0;
        }
        //debug
        if($this->debugMode){ echo '截切百分之',$percentage * 100,'<br>'; }
        //判斷有沒有超過百分比
        if ( ($percentage * 100) > $sizeConfig['percentage'] ){
            //超過使用補色
            return $this->case2( $fileInfo, $sizeConfig ,$newSize );
        } else {
            //未超過截切
            return $this->case1( $fileInfo, $sizeConfig ,$newSize );
        }
    }


    //**************************************//
    //			等比例縮至指定寬或高			//
    //**************************************//
    public function case67( $fileInfo, $sizeConfig ,$newSize ){
        //取得預設值
        $result = $this->getDefaultValue($fileInfo, $sizeConfig ,$newSize);
        //判斷是固定寬還是固定高
        if ( $sizeConfig['method'] == '7' ){
            //====================固定高====================
            //計算高縮小的比例
            $countHigh = $fileInfo['image_height'] / $newSize['high'];
            //取得同比例寬為多少
            $countWidth = $fileInfo['image_width'] / $countHigh;

            $result['newImagePicHigh'] = $newSize['high'];
            $result['newImagePicWidth'] = $countWidth;
            $result['dst_w'] = $countWidth;

        } else if ( $sizeConfig['method'] == '6' ){
            //====================固定寬====================
            //計算寬縮小的比例
            $countWidth = $fileInfo['image_width'] / $newSize['width'];
            //取得同比例高為多少
            $countHigh = $fileInfo['image_height'] / $countWidth;

            $result['newImagePicHigh'] = $countHigh;
            $result['newImagePicWidth'] = $newSize['width'];
            $result['dst_h'] = $countHigh;
        }
        return $result;
    }


    //******************//
    //		縮圖預設值	//
    //******************//
    public function getDefaultValue( $fileInfo, $sizeConfig ,$newSize)
    {
        //預設同比例縮圖
        $result['file_ext'] = $fileInfo['file_ext'];//副檔名
        $result['quality'] = (isset($sizeConfig['quality']) && is_numeric($sizeConfig['quality']))? $sizeConfig['quality'] : 90 ;//縮圖品質
        $result['dst_x'] = 0 ;//目標檔案開始點的 x 座標
        $result['dst_y'] = 0 ;//目標檔案開始點的 y 座標
        $result['src_x'] = 0 ;//來源檔案開始點的 x 座標
        $result['src_y'] = 0 ;//來源檔案開始點的 y 座標
        $result['dst_w'] = $newSize['width'] ;//目標檔案的寬度
        $result['dst_h'] = $newSize['high'] ;//目標檔案的高度
        $result['src_w'] = $fileInfo['image_width'] ;//來源檔案的寬度
        $result['src_h'] = $fileInfo['image_height'] ;//來源檔案的高度
        $result['newImagePicWidth'] = $newSize['width'];//縮圖後的寬
        $result['newImagePicHigh'] = $newSize['high'];//縮圖後的高
        //來源檔案
        $result['path'] = $fileInfo['full_path'];
        //新檔案的名稱
        $result['newFileName'] = $sizeConfig['prefix'].str_replace( '.', '_'.$newSize['width'].$newSize['high'].'.', $fileInfo['file_name'] );
        //目標檔案
        $result['newPath'] = $sizeConfig['path'].$sizeConfig['datePath'].$result['newFileName'];
        //年月日資料夾
        $result['datePath'] =  $sizeConfig['datePath'];
        //是否使用透明色
        $result['tranparent'] = $sizeConfig['tranparent'];
        //原圖名稱
        $result['originalFileName'] = $fileInfo['file_name'];
        //路徑
        $result['filePath'] = $sizeConfig['path'];
        //是否使用特定顏色
        $result['color'] = $sizeConfig['color'];
        return $result;
    }


    //**********************************//
    //				debug				//
    //**********************************//
    public function debug( $fileInfo, $uploadImgConfig ,$newSize )
    {
        echo '<style>html{color:red;}b{color:#888888;}</style>';
        echo '<b>縮圖方式:</b>';
        echo ($uploadImgConfig['method'] == 0)?'小於目的尺寸,不放大':'';
        echo ($uploadImgConfig['method'] == 1)?'一律使用截切方式':'';
        echo ($uploadImgConfig['method'] == 2)?'一律使用補色方式':'';
        echo ($uploadImgConfig['method'] == 3)?'固定高,寬截切或補色':'';
        echo ($uploadImgConfig['method'] == 4)?'固定寬,高截切或補色':'';
        echo ($uploadImgConfig['method'] == 5)?'百分比':'';
        echo ($uploadImgConfig['method'] == 6)?'等比例將寬縮至指定寬':'';
        echo ($uploadImgConfig['method'] == 7)?'等比例將高縮至指定高':'';
        echo '</br>';
        echo '<b>原圖大小</b>'.$fileInfo['image_size_str'].'</br>';
        echo '<b>縮圖大小參數</b>'.$newSize['width'].'X'.$newSize['high'].'</br>';
        echo '<b>路徑:</b>'.$uploadImgConfig['path'].'</br>';
        echo '<b>是否分資料夾:</b>';
        echo ($uploadImgConfig['disperse'])?'是':'否';
        echo '</br><b>左右切割方向:</b>';
        echo ($uploadImgConfig['width_position'] == 0)?'置中':'';
        echo ($uploadImgConfig['width_position'] == 1)?'左邊':'';
        echo ($uploadImgConfig['width_position'] == 2)?'右邊':'';
        echo '</br><b>上下切割方向:</b>';
        echo ($uploadImgConfig['high_position'] == 0)?'置中':'';
        echo ($uploadImgConfig['high_position'] == 1)?'上面':'';
        echo ($uploadImgConfig['high_position'] == 2)?'下面':'';
        echo '</br>';
        echo '<b>是否轉換透明色:</b>';
        echo ($uploadImgConfig['tranparent'])?'是':'否';
        echo '</br><b>截切百分比:</b>';
        echo $uploadImgConfig['percentage'].'</br>';
        echo '</br><b>顏色</b>';
        echo '<pre>';
        print_r($uploadImgConfig['color']);
        echo '</pre>';
    }
}
?>
