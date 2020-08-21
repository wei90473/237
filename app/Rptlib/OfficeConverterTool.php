<?php 
namespace App\Rptlib;

use Illuminate\Pagination\LengthAwarePaginator; 
use NcJoes\OfficeConverter\OfficeConverter;

define( 'DS', DIRECTORY_SEPARATOR ); 

class OfficeConverterTool {
   
 
    public function today_filepath() {
        $today = storage_path(date("Y-m-d"));
        if (!file_exists( $today)) {
            mkdir( $today, 0777, true);
        }
        return $today;        
    }

    public function Converter2OtherFileType($source_file,$target_file,$extension) {
       
        if (false !== $pos = strripos($source_file, '.')) {
            $todayfile     = $this->today_filepath();
            $fileName      = substr($source_file, 0, $pos);
            $fileName      = str_replace($todayfile ,"",$fileName);
            if($target_file==''){//不設定新名稱則沿用原來擋案名稱
                $target_file = $fileName;
            }
            $target_file   = $target_file.'.'.$extension;   
            $converter   = new OfficeConverter($source_file); //讀取要轉檔的檔案 
            $converter->convertTo( $target_file);  
            return ($todayfile.DS.$target_file);  
        }else{
            $ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $source_file);
            die('副檔名錯誤'.$ext);
        }
   
    }      

}
