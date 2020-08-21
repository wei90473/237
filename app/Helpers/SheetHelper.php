<?php
namespace App\Helpers;

use PhpOffice\PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use NcJoes\OfficeConverter\OfficeConverter;


class SheetHelper{
	public function getTempPath()
	{
		return storage_path('reportTemp/');
	}

	public function getTempFileName()
	{
		return time().rand(1, 100000);
	}

	public function exportXlsx($spreadSheet, $outputFileName, $saveInServer = false)
	{
        ob_end_clean();
        ob_start();

		$objWriter = IOFactory::createWriter($spreadSheet, "Xlsx");
		if ($saveInServer){
			$filePath = $this->getTempPath().$this->getTempFileName().'.xlsx';
			$objWriter->save($filePath);
			return $filePath;
		}else{
	        // Redirect output to a client’s web browser (Excel2007)
	        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	        // 設定下載 Excel 的檔案名稱
	        header('Content-Disposition: attachment;filename="'.$outputFileName.'.xlsx"');    
	        header('Cache-Control: max-age=0');
	        // If you're serving to IE 9, then the following may be needed
	        header('Cache-Control: max-age=1');
	        // If you're serving to IE over SSL, then the following may be needed
	        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	        header ('Pragma: public'); // HT	
	        
	        $objWriter->save('php://output');	
	        exit;	
		}

	}

	public function convertToOds($sourceFilePath, $outputFileName)
	{
        $converter = new OfficeConverter($sourceFilePath);    // 讀取要轉檔的檔案
        $sourceFilePath = pathinfo($sourceFilePath);          // 解析來源檔案路徑 
        $odsFileName = $sourceFilePath['filename'].'.ods';    // 轉換後的檔案名稱
        $converter->convertTo($odsFileName);                  // 轉換

        $odsFilePath = $sourceFilePath['dirname'].'/'.$odsFileName;
        $file_size = filesize($odsFilePath);

        ob_end_clean();
        ob_start();

        header('Pragma: public');
        header('Expires: 0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . $file_size);
        header('Content-Disposition: attachment; filename="'.$outputFileName.'.ods";');
        header('Content-Transfer-Encoding: binary');

        readfile($odsFilePath); 
        exit;
	}
}
