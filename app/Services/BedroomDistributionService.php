<?php
namespace App\Services;

use App\Repositories\T04tbRepository;
use App\Repositories\T13tbRepository;
use App\Repositories\EdubedRepository;
use App\Repositories\T06tbRepository;
use App\Repositories\EdustayweeksRepository;
use App\Repositories\EdustayweeksdtRepository;
use DB;

class BedroomDistributionService
{
	/**
     * PunchService constructor.
     * @param 
     */
    public function __construct(T04tbRepository $t04tbRepository,T13tbRepository $t13tbRepository,EdubedRepository $eduBedRepository,T06tbRepository $t06tbRepository,EdustayweeksRepository $edustayweeksRepository,EdustayweeksdtRepository $edustayweeksdtRepository)
    {
        $this->t04tbRepository = $t04tbRepository;
        $this->t13tbRepository = $t13tbRepository;
        $this->eduBedRepository = $eduBedRepository;
        $this->t06tbRepository = $t06tbRepository;
        $this->edustayweeksRepository = $edustayweeksRepository;
        $this->edustayweeksdtRepository = $edustayweeksdtRepository;
    }

    public function checkSameWeek($startdate,$enddate)
    {
        if(strlen($startdate) == 7 && strlen($enddate) == 7){
            $y=intval(substr($startdate,0,3))+1911;
            $m=intval(substr($startdate,3,2));
            $d=intval(substr($startdate,5,2));
            $toUnixtime=mktime(0,0,0,$m,$d,$y);

            $y=intval(substr($enddate,0,3))+1911;
            $m=intval(substr($enddate,3,2));
            $d=intval(substr($enddate,5,2));
            $toUnixtime2=mktime(0,0,0,$m,$d,$y);

            $afweek = date('w',$toUnixtime2);
            $mintime = $toUnixtime2 - $afweek * 3600*24;
            $maxtime = $toUnixtime2 + (7-$afweek)*3600*24;
            if ($toUnixtime >= $mintime && $toUnixtime <= $maxtime){
                return true;
            }
        }

        return false;
    }

    public function getDormClassList($sdate,$edate)
    {
    	return $this->t04tbRepository->getDormClassList($sdate,$edate);
    }

    public function getCourseDate($class,$term)
    {
    	return $this->t06tbRepository->getLongClass($class,$term);
    }

    public function checkLongClass($firstCourseDate,$lastCourseDate)
    {
    	$firstDate = '';
        $lastDate = '';
        $y=intval(substr($firstCourseDate,0,3))+1911;
        $m=intval(substr($firstCourseDate,3,2));
        $d=intval(substr($firstCourseDate,5,2));
        $firstDate = $y.'-'.$m.'-'.$d;
        $firstDate = strtotime($firstDate);

        $y=intval(substr($lastCourseDate,0,3))+1911;
        $m=intval(substr($lastCourseDate,3,2));
        $d=intval(substr($lastCourseDate,5,2));
        $lastDate = $y.'-'.$m.'-'.$d;
        $lastDate = strtotime($lastDate);

        $afweek = date('w',$lastDate);
        $mintime = $lastDate - $afweek * 3600*24;
        $maxtime = $lastDate + (7-$afweek)*3600*24;
        if ( $firstDate >= $mintime && $firstDate <= $maxtime){
            return false;
        } else {
            return true;
        }     
    }

    public function getLongDormStudentInfo($class,$term,$sdate,$edate,$hasBed='')
    {
    	return $this->edustayweeksRepository->getDormStudentInfo($class,$term,$sdate,$edate,$hasBed);
    }

    public function getDormStudentInfo($class,$term,$hasBed='')
    {
    	return $this->t13tbRepository->getDormStudentInfo($class,$term,$hasBed);
    }

    public function getDormStudentNotBedCount($class,$term)
    {
    	return count($this->t13tbRepository->getDormStudent($class,$term,'','N'));
    }

    public function getLongDormStudentNotBedCount($class,$term,$week)
    {
    	return count($this->t13tbRepository->getLongDormStudent($class,$term,'',$week,'N'));
    }

    public function export($sdate,$edate){
    	// 檔案名稱
        $fileName = 'N23';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);

        $year = substr($sdate, 0,3); 
        $sM = substr($sdate, 3,2);
        $sD = substr($sdate, 5,2);
        $eM = substr($edate, 3,2);
        $eD = substr($edate, 5,2);
        $title = $year.'年週別'.$sM.'/'.$sD.'~'.$eM.'/'.$eD;

        $spreadsheet->getActiveSheet()->getCell('A1')->setValue($title);

        $spreadsheet->getActiveSheet()->getCell('A48')->setValue('編號');
        $spreadsheet->getActiveSheet()->getStyle('A48')->getFont()->setSize(20);
        $spreadsheet->getActiveSheet()->getCell('B48')->setValue('班期代碼');
        $spreadsheet->getActiveSheet()->getStyle('B48')->getFont()->setSize(20);
        $spreadsheet->getActiveSheet()->getCell('C48')->setValue('期別');
        $spreadsheet->getActiveSheet()->getStyle('C48')->getFont()->setSize(20);
        $spreadsheet->getActiveSheet()->getCell('D48')->setValue('班期名稱');
        $spreadsheet->getActiveSheet()->getStyle('D48')->getFont()->setSize(20);
        $spreadsheet->getActiveSheet()->getCell('E48')->setValue('未安排人數');
        $spreadsheet->getActiveSheet()->getStyle('E48')->getFont()->setSize(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(60);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $spreadsheet->getActiveSheet()->getRowDimension(48)->setRowHeight(-1);
        $row = 49;
        $classList = $this->getDormClassList($sdate,$edate)->toArray();
        foreach ($classList as $key => $value) {
            $courseDate = $this->getCourseDate($value['class'],$value['term'])->toArray(); 
            if(count($courseDate) > 1){
                $checkLongClass = $this->checkLongClass($courseDate[0]['date'],$courseDate[count($courseDate)-1]['date']);
            } else {
                $checkLongClass = false;
            }

            if($checkLongClass){
                $dormStudentInfo = $this->getLongDormStudentInfo($value['class'],$value['term'],$sdate,$edate,'Y')->toArray();
                if(isset($dormStudentInfo[0]['week'])){
                    $week = $dormStudentInfo[0]['week'];
                } else {
                    $week = 0;
                }
                $notBedStudentCount = $this->getLongDormStudentNotBedCount($value['class'],$value['term'],$week);
            } else {
                $dormStudentInfo = $this->getDormStudentInfo($value['class'],$value['term'],'Y')->toArray();
                $notBedStudentCount = $this->getDormStudentNotBedCount($value['class'],$value['term']);
            }

            foreach ($dormStudentInfo as $key2 => $value2) {
                $location = $this->bedComparison($value2['bedno']);
                if(!empty($location)){
                    $tmpValue = ($key+1).'-'.$value2['student_name'];
                    $spreadsheet->getActiveSheet()->getCell($location)->setValue($tmpValue);
                    $spreadsheet->getActiveSheet()->getStyle($location)->getFont()->setSize(20);
                }
            }

            $tmpALocation = 'A'.$row;
            $tmpBLocation = 'B'.$row;
            $tmpCLocation = 'C'.$row;
            $tmpDLocation = 'D'.$row;
            $tmpELocation = 'E'.$row;
            $spreadsheet->getActiveSheet()->getCell($tmpALocation)->setValue(($key+1));
            $spreadsheet->getActiveSheet()->getStyle($tmpALocation)->getFont()->setSize(20);
            $spreadsheet->getActiveSheet()->getCell($tmpBLocation)->setValue($value['class']);
            $spreadsheet->getActiveSheet()->getStyle($tmpBLocation)->getFont()->setSize(20);
            $spreadsheet->getActiveSheet()->getCell($tmpCLocation)->setValue($value['term']);
            $spreadsheet->getActiveSheet()->getStyle($tmpCLocation)->getFont()->setSize(20);
            $spreadsheet->getActiveSheet()->getCell($tmpDLocation)->setValue($value['classname']);
            $spreadsheet->getActiveSheet()->getStyle($tmpDLocation)->getFont()->setSize(20);
            $spreadsheet->getActiveSheet()->getCell($tmpELocation)->setValue($notBedStudentCount);
            $spreadsheet->getActiveSheet()->getStyle($tmpELocation)->getFont()->setSize(20);
            $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(-1);
            $row++;
        }


        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="寢室分配情形一覽表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        //匯出
        //old code
        // $objWriter = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $objWriter->save('php://output');
    }

    public function bedComparison($bedno)
    {
    	$bed = array();
    	//英雄館2樓
    	$bed['21231'] = 'F6';  
		$bed['21232'] = 'G6';
		$bed['21241'] = 'H6';
		$bed['21242'] = 'I6';
		$bed['21251'] = 'J6';
		$bed['21252'] = 'K6';
		$bed['21261'] = 'L6';
		$bed['21262'] = 'M6';
		$bed['21271'] = 'N6';
		$bed['21272'] = 'O6';
		$bed['21281'] = 'P6';
		$bed['21282'] = 'Q6';
		$bed['21291'] = 'R6';
		$bed['21292'] = 'S6';
		$bed['21301'] = 'T6';
		$bed['21302'] = 'U6';
		$bed['21311'] = 'V6';
		$bed['21312'] = 'W6';
		$bed['21321'] = 'X6';
		$bed['21322'] = 'Y6';
		$bed['21331'] = 'Z6';
		$bed['21332'] = 'AA6';
		$bed['21341'] = 'AB6';
		$bed['21342'] = 'AC6';
		$bed['21351'] = 'AD6';
		$bed['21352'] = 'AE6';
		$bed['21361'] = 'AF6';
		$bed['21362'] = 'AG6';
		$bed['21371'] = 'AH6';
		$bed['21372'] = 'AI6';
		$bed['21381'] = 'AJ6';
		$bed['21382'] = 'AK6';
		$bed['21391'] = 'AL6';
		$bed['21392'] = 'AM6';
		$bed['21401'] = 'AN6';
		$bed['21402'] = 'AO6';
		$bed['21411'] = 'AP6';
		$bed['21412'] = 'AQ6';
		$bed['21421'] = 'AR6';
		$bed['21422'] = 'AS6';
		$bed['21431'] = 'AT6';
		$bed['21432'] = 'AU6';
		$bed['21441'] = 'AV6';
		$bed['21442'] = 'AW6';
		$bed['21451'] = 'AX6';
		$bed['21452'] = 'AY6';
		$bed['21461'] = 'AZ6';
		$bed['21462'] = 'BA6';

		//英雄館1樓
		$bed['21011'] = 'F12';
		$bed['21012'] = 'G12';
		$bed['21021'] = 'H12';
		$bed['21022'] = 'I12';
		$bed['21031'] = 'J12';
		$bed['21032'] = 'K12';
		$bed['21041'] = 'L12';
		$bed['21042'] = 'M12';
		$bed['21051'] = 'N12';
		$bed['21052'] = 'O12';
		$bed['21061'] = 'P12';
		$bed['21062'] = 'Q12';
		$bed['21071'] = 'R12';
		$bed['21072'] = 'S12';
		$bed['21081'] = 'T12';
		$bed['21082'] = 'U12';
		$bed['21091'] = 'V12';
		$bed['21092'] = 'W12';
		$bed['21101'] = 'X12';
		$bed['21102'] = 'Y12';
		$bed['21111'] = 'Z12';
		$bed['21112'] = 'AA12';
		$bed['21121'] = 'AB12';
		$bed['21122'] = 'AC12';
		$bed['21131'] = 'AD12';
		$bed['21132'] = 'AE12';
		$bed['21141'] = 'AF12';
		$bed['21142'] = 'AG12';
		$bed['21151'] = 'AH12';
		$bed['21152'] = 'AI12';
		$bed['21161'] = 'AJ12';
		$bed['21162'] = 'AK12';
		$bed['21171'] = 'AL12';
		$bed['21172'] = 'AM12';
		$bed['21181'] = 'AN12';
		$bed['21182'] = 'AO12';
		$bed['21191'] = 'AP12';
		$bed['21192'] = 'AQ12';
		$bed['21201'] = 'AR12';
		$bed['21202'] = 'AS12';
		$bed['21211'] = 'AT12';
		$bed['21212'] = 'AU12';
		$bed['21221'] = 'AV12';
		$bed['21222'] = 'AW12';

		//文昌樓2樓
		$bed['22711'] = 'F18';
		$bed['22721'] = 'H18';
		$bed['22731'] = 'J18';
		$bed['22741'] = 'L18';
		$bed['22751'] = 'N18';
		$bed['22761'] = 'P18';
		$bed['22771'] = 'R18';
		$bed['22781'] = 'T18';
		$bed['22791'] = 'V18';
		$bed['22801'] = 'X18';
		$bed['22811'] = 'Z18';
		$bed['22821'] = 'AB18';
		$bed['22831'] = 'AD18';
		$bed['22841'] = 'AF18';
		$bed['22851'] = 'AH18';
		$bed['22861'] = 'AJ18';
		$bed['22871'] = 'AL18';

		//文昌樓1樓
		$bed['22511'] = 'F24';
		$bed['22521'] = 'H24';
		$bed['22531'] = 'J24';
		$bed['22541'] = 'L24';
		$bed['22551'] = 'N24';
		$bed['22561'] = 'P24';
		$bed['22571'] = 'R24';
		$bed['22581'] = 'T24';
		$bed['22591'] = 'V24';
		$bed['22601'] = 'X24';
		$bed['22611'] = 'Z24';
		$bed['22621'] = 'AB24';
		$bed['22631'] = 'AD24';
		$bed['22641'] = 'AF24';
		$bed['22651'] = 'AH24';
		$bed['22661'] = 'AJ24';

		//詠晴園2樓
		$bed['24221'] = 'F30';
		$bed['24222'] = 'G30';
		$bed['24231'] = 'H30';
		$bed['24232'] = 'I30';
		$bed['24241'] = 'J30';
		$bed['24242'] = 'K30';
		$bed['24251'] = 'L30';
		$bed['24252'] = 'M30';
		$bed['24261'] = 'N30';
		$bed['24262'] = 'O30';
		$bed['24271'] = 'P30';
		$bed['24272'] = 'Q30';
		$bed['24281'] = 'R30';
		$bed['24282'] = 'S30';
		$bed['24291'] = 'T30';
		$bed['24292'] = 'U30';
		$bed['24301'] = 'V30';
		$bed['24302'] = 'W30';
		$bed['24311'] = 'X30';
		$bed['24312'] = 'Y30';
		$bed['24321'] = 'Z30';
		$bed['24322'] = 'AA30';
		$bed['24331'] = 'AB30';
		$bed['24332'] = 'AC30';
		$bed['24371'] = 'AJ30';
		$bed['24372'] = 'AK30';
		$bed['24381'] = 'AL30';
		$bed['24382'] = 'AM30';
		$bed['24391'] = 'AN30';
		$bed['24392'] = 'AO30';
		$bed['24401'] = 'AP30';
		$bed['24402'] = 'AQ30';
		$bed['24411'] = 'AR30';
		$bed['24412'] = 'AS30';
		$bed['24421'] = 'AT30';
		$bed['24422'] = 'AU30';
		$bed['24431'] = 'AV30';
		$bed['24432'] = 'AW30';
		$bed['24451'] = 'AZ30';
		$bed['24452'] = 'BA30';

		//詠晴園1樓
		$bed['24011'] = 'F36';
		$bed['24012'] = 'G36';
		$bed['24021'] = 'H36';
		$bed['24022'] = 'I36';
		$bed['24031'] = 'J36';
		$bed['24032'] = 'K36';
		$bed['24041'] = 'L36';
		$bed['24042'] = 'M36';
		$bed['24051'] = 'N36';
		$bed['24052'] = 'O36';
		$bed['24061'] = 'P36';
		$bed['24062'] = 'Q36';
		$bed['24071'] = 'R36';
		$bed['24072'] = 'S36';
		$bed['24081'] = 'T36';
		$bed['24082'] = 'U36';
		$bed['24091'] = 'V36';
		$bed['24092'] = 'W36';
		$bed['24101'] = 'X36';
		$bed['24102'] = 'Y36';
		$bed['24111'] = 'Z36';
		$bed['24112'] = 'AA36';
		$bed['24121'] = 'AB36';
		$bed['24122'] = 'AC36';
		$bed['24131'] = 'AD36';
		$bed['24132'] = 'AE36';
		$bed['24141'] = 'AF36';
		$bed['24142'] = 'AG36';
		$bed['24151'] = 'AH36';
		$bed['24152'] = 'AI36';
		$bed['24161'] = 'AJ36';
		$bed['24162'] = 'AK36';
		$bed['24171'] = 'AL36';
		$bed['24172'] = 'AM36';
		$bed['24181'] = 'AN36';
		$bed['24182'] = 'AO36';
		$bed['24191'] = 'AP36';
		$bed['24192'] = 'AQ36';
		$bed['24201'] = 'AR36';
		$bed['24202'] = 'AS36';
		$bed['24211'] = 'AT36';
		$bed['24212'] = 'AU36';

		//名人巷
		$bed['23021'] = 'H42';
		$bed['23022'] = 'I42';
		$bed['23023'] = 'J42';
		$bed['23031'] = 'K42';
		$bed['23032'] = 'L42';
		$bed['23033'] = 'M42';
		$bed['23041'] = 'N42';
		$bed['23042'] = 'O42';
		$bed['23043'] = 'P42';
		$bed['23051'] = 'Q42';
		$bed['23052'] = 'R42';
		$bed['23053'] = 'S42';
		$bed['23061'] = 'T42';
		$bed['23062'] = 'U42';
		$bed['23063'] = 'V42';
		$bed['23071'] = 'W42';
		$bed['23072'] = 'X42';
		$bed['23073'] = 'Y42';
		$bed['23081'] = 'Z42';
		$bed['23082'] = 'AA42';
		$bed['23083'] = 'AB42';
		$bed['23091'] = 'AC42';
		$bed['23092'] = 'AD42';
		$bed['23093'] = 'AE42';
		$bed['23101'] = 'AF42';
		$bed['23102'] = 'AG42';
		$bed['23103'] = 'AH42';
		$bed['23111'] = 'AI42';
		$bed['23112'] = 'AJ42';
		$bed['23113'] = 'AK42';
		$bed['23121'] = 'AL42';
		$bed['23122'] = 'AM42';
		$bed['23123'] = 'AN42';
		$bed['23131'] = 'AO42';
		$bed['23132'] = 'AP42';
		$bed['23133'] = 'AQ42';
		$bed['23141'] = 'AR42';
		$bed['23142'] = 'AS42';
		$bed['23143'] = 'AT42';
		$bed['23151'] = 'AU42';
		$bed['23152'] = 'AV42';
		$bed['23153'] = 'AW42';
		$bed['23161'] = 'AX42';
		$bed['23162'] = 'AY42';
		$bed['23163'] = 'AZ42';
		$bed['23171'] = 'BA42';
		$bed['23172'] = 'BB42';
		$bed['23173'] = 'BC42';
		$bed['23181'] = 'BD42';
		$bed['23182'] = 'BE42';
		$bed['23183'] = 'BF42';

		if(isset($bed[$bedno])){
			return $bed[$bedno];
		}

		return '';
    }
}

?>