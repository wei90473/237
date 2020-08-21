<?php
namespace App\Services;

use App\Repositories\T01tbRepository;
use App\Repositories\T13tbRepository;
use App\Repositories\EdubedRepository;
use App\Repositories\T06tbRepository;
use App\Repositories\EdustayweeksRepository;
use App\Repositories\EdustayweeksdtRepository;
use DB;

class StayDistributionDutyListService
{
	/**
     * PunchService constructor.
     * @param 
     */
    public function __construct(T01tbRepository $t01tbRepository,T13tbRepository $t13tbRepository,EdubedRepository $eduBedRepository,T06tbRepository $t06tbRepository,EdustayweeksRepository $edustayweeksRepository,EdustayweeksdtRepository $edustayweeksdtRepository)
    {
        $this->t01tbRepository = $t01tbRepository;
        $this->t13tbRepository = $t13tbRepository;
        $this->eduBedRepository = $eduBedRepository;
        $this->t06tbRepository = $t06tbRepository;
        $this->edustayweeksRepository = $edustayweeksRepository;
        $this->edustayweeksdtRepository = $edustayweeksdtRepository;
    }

    public function getrptPPrptSub8($sdate,$edate)
    {
    	$query = $this->t01tbRepository->getrptPPrptSub8($sdate,$edate);
    	$querynum = $this->eduBedRepository->getBedInfo();

    	$r=array();		
		$oldid='';
		$oldfloorno='';
		$oldroomname='';
		$oldbedno='';
		
		foreach($query as $q){
			$tmp_roomname = explode('-',$q->roomname); 
			$q->roomname = $tmp_roomname['0'];
			if ($q->yerly.$q->classno.$q->period.$q->week != $oldid){
				if (!empty($oldid)){//結尾
					if (($item->mbed>'') and ($oldroomname != $lastroomname))
					   $item->mbed=$item->mbed.'﹣'.$oldroomname;
					if (!empty($oldbedno)) $item->mbed=$item->mbed.$oldbedno;//加上迄房號
					if (($item->fbed>'') and ($foldroomname != $flastroomname))
					   $item->fbed=$item->fbed.'-'.$foldroomname;
					if (!empty($foldbedno)) $item->fbed=$item->fbed.$foldbedno;
					if (!empty($mcheckroom)) $item->mbed=$item->mbed.$mcheckroom;//寢室檢查
					if (!empty($fcheckroom)) $item->fbed=$item->fbed.$fcheckroom;//寢室檢查							
					if (!empty($mlastfloor)){
						$item->mbed=$item->mbed.chr(10).'◎'.$mlastfloor;
					}
					if (!empty($flastfloor)){
						$item->fbed=$item->fbed.chr(10).'◎'.$flastfloor;
					}
				}
																				
				$item=(object)[];
				$item->classno=$q->classno;
				$item->classname=$q->classname;
				if ($q->week > 0) $item->classname=$item->classname.'(第'.$q->week.'週)';
				$item->period=$q->period;
				$item->rosternum=$q->rosternum;
				$item->startdate=substr($q->startdate,3,2).'.'.substr($q->startdate,5,2);
				$item->enddate=substr($q->enddate,3,2).'.'.substr($q->enddate,5,2);;
				$item->trainingday=$q->trainingday;
				$item->counselorname=$q->counselorname;
				//住宿人數合計
				$item->stayreqcount=0;//$q->stayreqcount;
				$item->staymreqcount=0;//$q->staymreqcount;
				$item->stayfreqcount=0;//$q->stayfreqcount;
				
				$item->fbed='';
				$item->mbed='';
                $r[]=$item;		
						
				$oldid=$q->yerly.$q->classno.$q->period.$q->week;
				$oldfloorno='';
				$oldroomno=0;
		        $oldroomname='';
		        $oldbedno='';
				
				$foldfloorno='';
				$foldroomno=0;
		        $foldroomname='';
		        $foldbedno='';
				
				//備用寢室-記錄最後一樓名
				$mlastfloor='';
				$flastfloor='';
				$mcheckroom='';
				$fcheckroom='';
				
				$bednum=0;//床位數
				$fbednum=0;
			}
			
			$newfloor=$q->floorname.' '.$q->roomname;
			if ($q->sex=='M'){
				if ($q->stay=='Y'){//住宿人數合計
					$item->staymreqcount=$item->staymreqcount+1;
				    $item->stayreqcount=$item->stayreqcount+1;
				}
				
				if ($oldfloorno != $q->floorno){//樓不同
					$mlastfloor=mb_substr($q->floorname,0,1,"UTF-8");//記錄最後一樓名為備用寢室
					if ($item->mbed > ''){//結尾
						if ($oldroomname != $lastroomname)
						   $item->mbed=$item->mbed.'﹣'.$oldroomname;
						if (!empty($oldbedno)) $item->mbed=$item->mbed.$oldbedno;//加上迄房號
						if (!empty($mcheckroom)) $item->mbed=$item->mbed.$mcheckroom;//寢室檢查
						if (!empty($item->mbed)) $item->mbed=$item->mbed.chr(10);//加上迄房 
					}
					$item->mbed=$item->mbed.$newfloor;
					$oldfloorno=$q->floorno;
					$oldroomno=$q->bedroom;
					$oldroomname=$q->roomname;
					
					$bednum=$this->getBedNum($querynum,$q->floorno,$q->bedroom);
					if ($bednum==1) $oldbedno='';//寢室只開放一床
					else $oldbedno='(1)';//'('.substr($q->bedno,4,1).')';//(1)
					$lastroomname=$q->roomname;
					//寢室檢查
					// if ($this->checkbedroom($q->year,$q->id,$q->enddate,$oldfloorno,'s')) $mcheckroom=$mcheckroom.'※';
					// if ($this->checkbedroom($q->year,$q->id,$q->startdate,$oldfloorno,'e')) $mcheckroom=$mcheckroom.'*';
				}
				else{//樓同
					if ($oldroomname != $q->roomname){//房不同
					    //echo $oldroomno.'-'.$q->bedroom;
					    if ($oldroomno+1 != $q->bedroom){//不連續
					       if ($oldroomname != $lastroomname)
						      $item->mbed=$item->mbed.'-'.$oldroomname;
						   if (!empty($oldbedno)) $item->mbed=$item->mbed.$oldbedno;
						   if (!empty($item->mbed)) $item->mbed=$item->mbed.'、';//chr(10);//加上迄房 7.20
					       $item->mbed=$item->mbed.$q->roomname;//$newfloor;//7.20
					       $lastroomname=$q->roomname;	
					    }
						else{//連續,但床號不連續時
							
						}
						$oldroomno=$q->bedroom;
						$oldroomname=$q->roomname;
						$bednum=$this->getBedNum($querynum,$q->floorno,$q->bedroom);
						if ($bednum==1) $oldbedno='';//寢室只開放一床
						else $oldbedno='(1)';//'('.substr($q->bedno,4,1).')';//$q->bedno;
					}
					else {//房同
						$bno=intval(substr($oldbedno,1,1))+1;
						if ($bno==$bednum)
							$oldbedno=''; //床位排滿
						else $oldbedno='('.$bno.')';
					}
				}			
			}
			else{
				if ($q->stay=='Y'){//住宿人數合計
					$item->stayfreqcount=$item->stayfreqcount+1;
				    $item->stayreqcount=$item->stayreqcount+1;
				}
								
				if ($foldfloorno != $q->floorno){//樓不同
					$flastfloor=mb_substr($q->floorname,0,1,"UTF-8");;//記錄第一樓名為備用寢室
					if ($item->fbed > ''){//結尾
						if ($foldroomname != $flastroomname)
						   $item->fbed=$item->fbed.'﹣'.$foldroomname;
						if (!empty($foldbedno)) $item->fbed=$item->fbed.$foldbedno;//加上迄房號
						if (!empty($fcheckroom)) $item->fbed=$item->fbed.$fcheckroom;//寢室檢查					   
						if (!empty($item->fbed)) $item->fbed=$item->fbed.chr(10);//加上迄房
					}
					$item->fbed=$item->fbed.$newfloor;
					$foldfloorno=$q->floorno;
					$foldroomno=$q->bedroom;
					$foldroomname=$q->roomname;
					$fbednum=$this->getBedNum($querynum,$q->floorno,$q->bedroom);
					if ($fbednum==1) $foldbedno='';//寢室只開放一床
					else $foldbedno='(1)';//'('.substr($q->bedno,4,1).')';//$q->bedno;
					$flastroomname=$q->roomname;
					// if ($this->checkbedroom($q->year,$q->id,$q->enddate,$foldfloorno,'s')) $fcheckroom=$fcheckroom.'※';
					// if ($this->checkbedroom($q->year,$q->id,$q->startdate,$foldfloorno,'e')) $fcheckroom=$fcheckroom.'*';
				}
				else{//樓同
					if ($foldroomname != $q->roomname){//房不同
					    //echo $oldroomno.'-'.$q->bedroom;
					    if ($foldroomno+1 != $q->bedroom){//不連續
					       if ($foldroomname != $flastroomname)
						      $item->fbed=$item->fbed.'-'.$foldroomname;
						   if (!empty($foldbedno)) $item->fbed=$item->fbed.$foldbedno;
						   if (!empty($item->fbed)) $item->fbed=$item->fbed.'、';//.chr(10);//加上迄房 7.20
					       $item->fbed=$item->fbed.$q->roomname;//$newfloor; 7.20
					       $flastroomname=$q->roomname;	
					    }
						else{//連續
							
						}
						$foldroomno=$q->bedroom;
						$foldroomname=$q->roomname;						
						$fbednum=$this->getBedNum($querynum,$q->floorno,$q->bedroom);
						if ($fbednum==1) $foldbedno='';//寢室只開放一床
						else $foldbedno='(1)';//'('.substr($q->bedno,4,1).')';//$q->bedno;

					}
					else {//房同
						$bno=intval(substr($foldbedno,1,1))+1;
						if ($bno==$fbednum)
							$foldbedno=''; //床位排滿
						else $foldbedno='('.$bno.')';
					}
				}
				
			}

		}

		if ($oldid > ''){//結尾
			if (($item->mbed>'') and ($oldroomname != $lastroomname))
				$item->mbed=$item->mbed.'﹣'.$oldroomname;
			if (!empty($oldbedno)) $item->mbed=$item->mbed.$oldbedno;//加上迄房號
			if (($item->fbed>'') and ($foldroomname != $flastroomname))
				$item->fbed=$item->fbed.'-'.$foldroomname;
			if (!empty($oldbedno)) $item->fbed=$item->fbed.$foldbedno;
			if (!empty($mcheckroom)) $item->mbed=$item->mbed.$mcheckroom;//寢室檢查
			if (!empty($fcheckroom)) $item->fbed=$item->fbed.$fcheckroom;//寢室檢查
			if (!empty($mlastfloor)){
				$item->mbed=$item->mbed.chr(10).'◎'.$mlastfloor;
			}
			if (!empty($flastfloor)){
						$item->fbed=$item->fbed.chr(10).'◎'.$flastfloor;
			}
		}
		
		//增加4列空白
		// for ($i=1;$i<=4;$i++){
		// 	$item=(object)[];
		// 	$item->classname='';
		// 	$r[]=$item;
		// }		

    	return $r;
    }

    public function getBedInfo()
    {
    	return $this->eduBedRepository->getBedInfo();
    }

    public function getBedNum($query,$floorno,$bedroom)
    {
    	$num=0;
		foreach ($query as $q){
			if (($q->floorno==$floorno) and ($q->bedroom==$bedroom)){
				$num=$q->bednum;
				break;
			}
		}
		return $num;
    }
}

?>