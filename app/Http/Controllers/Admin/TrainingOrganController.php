<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;

class TrainingOrganController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('training_organ', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin/training_organ/list');
    }

    /*
    訓練機構基本資料表 CSDIR7020
    參考Tables:
    使用範本:Q1A.xlsx/Q1B.xlsx/Q1C.xlsx/Q1D.xlsx
            Q1A:訓練機構基本資料, Q1B:首長基本資料, Q1C:副首長基本資料, Q1D:聯絡人基本資料
    有四支轉Word, 是固定選項
    1. 訓練機構-首長基本資料
    2. 訓練機構-訓練機構基本資料
    3. 訓練機構-副首長基本資料
    4. 訓練機構-聯絡人基本資料
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //1:訓練機構基本資料, 2:首長基本資料, 3:副首長基本資料, 4:聯絡人基本資料
        $radiotype = $request->input('radiotype');

        //取得訓練機構基本資料表
        //1:訓練機構基本資料, 2:首長基本資料, 3:副首長基本資料, 4:聯絡人基本資料
        if($radiotype=='2'){
            $sql = "SELECT agency,
                            NAME AS dept,
                            chief as name, cposition as position,
                                                CONCAT(
                            rtrim( case  ctelnoa  when '' then  ctelnoa  else CONCAT('(' , rtrim(ctelnoa) , ')')   end )
                            ,
                                                rtrim( case  when  length(rtrim(ctelnob)) = 8  then
                                                        CONCAT(substring(ctelnob,1,4) , '-' , substring(ctelnob,5,4))  else ctelnob end )
                            ,
                                                rtrim( case  ctelnoc  when '' then  ctelnoc  else CONCAT('轉' , ctelnoc)  end)
                                        ) as telno,
                                                CONCAT(
                            rtrim( case  cfaxnoa  when '' then  cfaxnoa  else CONCAT('(' , rtrim(cfaxnoa) , ')')   end )
                            ,
                                                rtrim( case  when  length(rtrim(cfaxnob)) = 8   then
                                                CONCAT(substring(cfaxnob,1,4) , '-' , substring(cfaxnob,5,4))  else cfaxnob end )
                                        ) as faxno,
                                                cemail as email,cmobiltel as mobiltel
                        FROM m07tb
                        ORDER BY agency";
        } elseif($radiotype=='3'){
            $sql = "SELECT agency,
                            NAME AS dept,
                            assistant1 as name,aposition1 as position,
                                            CONCAT(
                            rtrim( case  atelnoa1  when '' then  atelnoa1  else CONCAT('(' , rtrim(atelnoa1) ,')')   end )
                            ,
                                            rtrim( case  when  length(rtrim(atelnob1)) = 8  then
                                                CONCAT(substring(atelnob1,1,4) , '-' , substring(atelnob1,5,4))  else atelnob1 end )
                            ,
                                            rtrim( case  atelnoc1  when '' then  atelnoc1  else CONCAT('轉',atelnoc1)  end)
                                            ) as telno,
                                            CONCAT(
                            rtrim( case  afaxnoa1  when '' then  afaxnoa1  else CONCAT('(', rtrim(afaxnoa1) , ')')   end )
                                            ,
                            rtrim( case  when  length(rtrim(afaxnob1)) = 8   then
                                                    CONCAT(substring(afaxnob1,1,4) , '-' , substring(afaxnob1,5,4))  else afaxnob1 end )
                                        ) as faxno
                            ,aemail1 as email ,amobiltel1 as mobiltel
                    From m07tb
                    Union All
                    SELECT agency,
                            name as dept,
                                            assistant2 as name,aposition2 as position,
                                            CONCAT(
                            rtrim( case  atelnoa2  when '' then  atelnoa2  else CONCAT('(' , rtrim(atelnoa2) , ')')  end )
                            ,
                                            rtrim( case  when  length(rtrim(atelnob2)) = 8  then
                                                    CONCAT(substring(atelnob2,1,4) , '-' , substring(atelnob2,5,4))  else atelnob2 end )
                            ,
                                            rtrim( case  atelnoc2  when '' then  atelnoc2  else CONCAT('轉' , atelnoc2)  end)
                                        ) as telno,
                                            CONCAT(
                            rtrim( case  afaxnoa2  when '' then  afaxnoa2  else CONCAT('(' , rtrim(afaxnoa2) , ')')   end )
                                            ,
                            rtrim( case  when  length(rtrim(afaxnob2)) = 8   then
                                                        CONCAT(substring(afaxnob2,1,4) , '-' , substring(afaxnob2,5,4))  else afaxnob2 end )
                                        ) as faxno
                            ,aemail2 as email,amobiltel2 as mobiltel
                    FROM m07tb
                    ORDER BY agency";
        } elseif($radiotype=='4'){
            $sql = "SELECT agency,
                            NAME AS dept,
                            '1.訓練交流業務' as type,
                            liaison1 as name,lposition1 as position,
                                            CONCAT(
                            rtrim( case  ltelnoa1  when '' then  ltelnoa1  else CONCAT('(' , rtrim(ltelnoa1) ,')')   end )
                            ,
                                            rtrim( case  when  length(rtrim(ltelnob1)) = 8  then
                                                    CONCAT(substring(ltelnob1,1,4) , '-' , substring(ltelnob1,5,4))  else ltelnob1 end )
                            ,
                                            rtrim( case  ltelnoc1  when '' then ltelnoc1  else CONCAT('轉' , ltelnoc1)  end)
                                        ) as telno,
                                            CONCAT(
                            rtrim( case  lfaxnoa1  when '' then lfaxnoa1  else CONCAT('(' ,rtrim(lfaxnoa1),')')   end )
                                            ,
                            rtrim( case  when  length(rtrim(lfaxnob1)) = 8  then
                                                        CONCAT(substring(lfaxnob1,1,4) , '-' , substring(lfaxnob1,5,4))  else lfaxnob1 end )
                                            ) as faxno
                            ,lemail1 as email ,lmobiltel1 as mobiltel
                    From m07tb
                    Union All
                    select agency, name as dept,
                            '2.研究發展業務' as type,
                            liaison2 as name,lposition2 as position,
                                            CONCAT(
                            rtrim( case  ltelnoa2  when '' then ltelnoa2  else CONCAT('(' , rtrim(ltelnoa2) ,')' )  end )
                            ,
                                            rtrim( case  when  length(rtrim(ltelnob2)) = 8  then
                                                    CONCAT(substring(ltelnob2,1,4) , '-' , substring(ltelnob2,5,4))  else ltelnob2 end )
                            ,
                                            rtrim( case  ltelnoc2  when '' then ltelnoc2  else CONCAT('轉' ,ltelnoc2)  end)
                                    ) as telno,
                                            CONCAT(
                            rtrim( case  lfaxnoa2  when '' then lfaxnoa2  else CONCAT('(' ,rtrim(lfaxnoa2),')')   end )
                                            ,
                            rtrim( case  when  length(rtrim(lfaxnob2)) = 8   then
                                                        CONCAT(substring(lfaxnob2,1,4) , '-' , substring(lfaxnob2,5,4))  else lfaxnob2 end )
                                    ) as faxno
                            ,lemail2 as email ,lmobiltel2 as mobiltel
                        From m07tb
                    Union All
                    select agency,name as dept,
                            '3.資訊管理業務' as type,
                            liaison3 as name,lposition3 as position,
                                                    CONCAT(
                            rtrim( case  ltelnoa3  when '' then  ltelnoa3  else CONCAT('(' , rtrim(ltelnoa3) ,')')   end )
                            ,
                                                    rtrim( case  when  length(rtrim(ltelnob3)) = 8  then
                                                            CONCAT(substring(ltelnob3,1,4) , '-' , substring(ltelnob3,5,4))  else ltelnob3 end )
                            ,
                                                    rtrim( case  ltelnoc3  when '' then  ltelnoc3  else CONCAT('轉' , ltelnoc3)  end)
                                            ) as telno,
                                                    CONCAT(
                            rtrim( case  lfaxnoa3  when '' then lfaxnoa3  else CONCAT('(' , rtrim(lfaxnoa3) , ')')   end )
                                                    ,
                            rtrim( case  when  length(rtrim(lfaxnob3)) = 8   then
                                                                CONCAT(substring(lfaxnob3,1,4) , '-' , substring(lfaxnob3,5,4))  else lfaxnob3 end )
                                                    ) as faxno
                            ,lemail3 as email ,lmobiltel3 as mobiltel
                        From m07tb
                    Union All
                    select agency,name as dept,
                            '4.圖書管理業務' as type,
                            liaison4 as name,lposition4 as position,
                                                    CONCAT(
                            rtrim( case  ltelnoa4 when '' then  ltelnoa4  else CONCAT('(' , rtrim(ltelnoa4) ,')')   end )
                            ,
                                                    rtrim( case  when  length(rtrim(ltelnob4)) = 8  then
                                                            CONCAT(substring(ltelnob4,1,4) , '-' , substring(ltelnob4,5,4))  else ltelnob4 end )
                            ,
                                                    rtrim( case  ltelnoc4  when '' then ltelnoc4  else CONCAT('轉',ltelnoc4)  end)
                                            ) as telno,
                                                    CONCAT(
                            rtrim( case  lfaxnoa4  when '' then lfaxnoa4  else CONCAT('(' ,rtrim(lfaxnoa4), ')')   end )
                                                    ,
                            rtrim( case  when  length(rtrim(lfaxnob4)) = 8   then
                                                                CONCAT(substring(lfaxnob4,1,4) , '-' , substring(lfaxnob4,5,4))  else lfaxnob4 end )
                                        ) as faxno
                            ,lemail4 as email ,lmobiltel4 as mobiltel
                        From m07tb
                    Union All
                    select agency,name as dept,
                            '5.訓練進修業務' as type,
                            liaison5 as name,lposition5 as position,
                                                    CONCAT(
                            rtrim( case  ltelnoa5 when '' then  ltelnoa5  else CONCAT('(' , rtrim(ltelnoa5) , ')')   end )
                            ,
                                                    rtrim( case  when  length(rtrim(ltelnob5)) = 8  then
                                                            CONCAT(substring(ltelnob5,1,4) , '-' , substring(ltelnob5,5,4))  else ltelnob5 end )
                            ,
                                                    rtrim( case  ltelnoc5  when '' then ltelnoc5  else CONCAT('轉' , ltelnoc5)  end)
                                            ) as telno,
                                                    CONCAT(
                            rtrim( case  lfaxnoa5  when '' then lfaxnoa5  else CONCAT('(' , rtrim(lfaxnoa5), ')')   end )
                                                    ,
                            rtrim( case  when  length(rtrim(lfaxnob5)) = 8   then
                                                                CONCAT(substring(lfaxnob5,1,4) , '-' , substring(lfaxnob5,5,4))  else lfaxnob5 end )
                                                    ) as faxno
                            ,lemail5 as email ,lmobiltel5 as mobiltel
                    FROM m07tb
                    ORDER BY 1, 3 ";
        } else {
            $sql = "SELECT agency,
                            NAME AS dept,
                            address,
                            CONCAT(rtrim(CASE telnoa
                                            WHEN '' THEN
                                            telnoa
                                            ELSE
                                            CONCAT('(', rtrim(telnoa), ')')
                                            END),
                                    rtrim(CASE

                                            WHEN LENGTH(rtrim(telnob)) = 8 THEN
                                            CONCAT(substring(telnob, 1, 4), '-', substring(telnob, 5, 4))
                                            ELSE
                                            telnob
                                            END),
                                    rtrim(CASE telnoc
                                            WHEN '' THEN
                                            telnoc
                                            ELSE
                                            CONCAT('轉', telnoc)
                                            END)) AS telno,
                            CONCAT(rtrim(CASE faxnoa
                                            WHEN '' THEN
                                            faxnoa
                                            ELSE
                                            CONCAT('(', rtrim(faxnoa), ')')
                                            END),
                                    rtrim(CASE
                                            WHEN LENGTH(rtrim(faxnob)) = 8 THEN
                                            CONCAT(substring(faxnob, 1, 4), '-', substring(faxnob, 5, 4))
                                            ELSE
                                            faxnob
                                            END)) AS faxno,
                            url AS email
                        FROM m07tb
                        ORDER BY agency, 'type' ";
        }


        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        // 讀檔案
        //Q1A:訓練機構基本資料, Q1B:首長基本資料, Q1C:副首長基本資料, Q1D:聯絡人基本資料
        //1:訓練機構基本資料, 2:首長基本資料, 3:副首長基本資料, 4:聯絡人基本資料
        if($radiotype=='2'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'Q1B').'.docx');
        } elseif($radiotype=='3'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'Q1C').'.docx');
        } elseif($radiotype=='4'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'Q1D').'.docx');
        } else {
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'Q1A').'.docx');
        }

        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        //各個範本所需欄位有所不同,分別填入
        if($radiotype=='2'){
            // 要放的資料筆數，先建 列
            $templateProcessor->cloneRow('dept', sizeof($dataArr));
            // 每列要放的資料(#1：第一列、以此類推)
            for($i=0; $i<sizeof($dataArr); $i++) {
                $templateProcessor->setValue('dept#'.($i+1), $dataArr[$i]['dept']);
                $templateProcessor->setValue('name#'.($i+1), $dataArr[$i]['name']);
                $templateProcessor->setValue('position#'.($i+1), $dataArr[$i]['position']);
                $templateProcessor->setValue('telno#'.($i+1), $dataArr[$i]['telno']);
                $templateProcessor->setValue('faxno#'.($i+1), $dataArr[$i]['faxno']);
                $templateProcessor->setValue('email#'.($i+1), $dataArr[$i]['email']);
                $templateProcessor->setValue('mobiltel#'.($i+1), $dataArr[$i]['mobiltel']);
            }
        } elseif($radiotype=='3'){
            // 要放的資料筆數，先建 列
            //$templateProcessor->cloneRow('dept', sizeof($dataArr));
            $templateProcessor->cloneRow('dept', (sizeof($dataArr)/2));
            // 每列要放的資料(#1：第一列、以此類推)
            $A=1;
            $B=1;
            for($i=0; $i<sizeof($dataArr); $i++) {
                //有合併儲存儲,故分二區域分別填入
                if(($i%2)==0){
                    $templateProcessor->setValue('dept#'.($A), $dataArr[$i]['dept']);
                    $templateProcessor->setValue('nameA#'.($A), $dataArr[$i]['name']);
                    $templateProcessor->setValue('positionA#'.($A), $dataArr[$i]['position']);
                    $templateProcessor->setValue('telnoA#'.($A), $dataArr[$i]['telno']);
                    $templateProcessor->setValue('faxnoA#'.($A), $dataArr[$i]['faxno']);
                    $templateProcessor->setValue('emailA#'.($A), $dataArr[$i]['email']);
                    $templateProcessor->setValue('mobilA#'.($A), $dataArr[$i]['mobiltel']);
                    $A++;
                }
                if(($i%2)==1){
                    $templateProcessor->setValue('nameB#'.($B), $dataArr[$i]['name']);
                    $templateProcessor->setValue('positionB#'.($B), $dataArr[$i]['position']);
                    $templateProcessor->setValue('telnoB#'.($B), $dataArr[$i]['telno']);
                    $templateProcessor->setValue('faxnoB#'.($B), $dataArr[$i]['faxno']);
                    $templateProcessor->setValue('emailB#'.($B), $dataArr[$i]['email']);
                    $templateProcessor->setValue('mobilB#'.($B), $dataArr[$i]['mobiltel']);
                    $B++;
                }
            }

           // $templateProcessor->cloneRow('name', sizeof($dataArr));
           /*
            for($i=0; $i<sizeof($dataArr); $i++) {
                $templateProcessor->setValue('name#'.($i+1), $dataArr[$i]['name']);
                $templateProcessor->setValue('position#'.($i+1), $dataArr[$i]['position']);
                $templateProcessor->setValue('telno#'.($i+1), $dataArr[$i]['telno']);
                $templateProcessor->setValue('faxno#'.($i+1), $dataArr[$i]['faxno']);
                $templateProcessor->setValue('email#'.($i+1), $dataArr[$i]['email']);
                $templateProcessor->setValue('mobiltel#'.($i+1), $dataArr[$i]['mobiltel']);
            }
            */

        } elseif($radiotype=='4'){
            // 要放的資料筆數，先建 列
            //$templateProcessor->cloneRow('dept', sizeof($dataArr));
            $templateProcessor->cloneRow('dept', (sizeof($dataArr)/5));
            // 每列要放的資料(#1：第一列、以此類推)
            $A=1;
            $B=1;
            $C=1;
            $D=1;
            $E=1;
            for($i=0; $i<sizeof($dataArr); $i++) {
                //有合併儲存儲,故分5區域分別填入
                if(($i%5)==0){
                    $templateProcessor->setValue('dept#'.($A), $dataArr[$i]['dept']);
                    $templateProcessor->setValue('typeA#'.($A), substr($dataArr[$i]['type'],2));
                    $templateProcessor->setValue('nameA#'.($A), $dataArr[$i]['name']);
                    $templateProcessor->setValue('positionA#'.($A), $dataArr[$i]['position']);
                    $templateProcessor->setValue('telnoA#'.($A), $dataArr[$i]['telno']);
                    $templateProcessor->setValue('faxnoA#'.($A), $dataArr[$i]['faxno']);
                    $templateProcessor->setValue('emailA#'.($A), $dataArr[$i]['email']);
                    $templateProcessor->setValue('mobilA#'.($A), $dataArr[$i]['mobiltel']);
                    $A++;
                }
                if(($i%5)==1){
                    $templateProcessor->setValue('typeB#'.($B),  substr($dataArr[$i]['type'],2));
                    $templateProcessor->setValue('nameB#'.($B), $dataArr[$i]['name']);
                    $templateProcessor->setValue('positionB#'.($B), $dataArr[$i]['position']);
                    $templateProcessor->setValue('telnoB#'.($B), $dataArr[$i]['telno']);
                    $templateProcessor->setValue('faxnoB#'.($B), $dataArr[$i]['faxno']);
                    $templateProcessor->setValue('emailB#'.($B), $dataArr[$i]['email']);
                    $templateProcessor->setValue('mobilB#'.($B), $dataArr[$i]['mobiltel']);
                    $B++;
                }
                if(($i%5)==2){
                    $templateProcessor->setValue('typeC#'.($C),  substr($dataArr[$i]['type'],2));
                    $templateProcessor->setValue('nameC#'.($C), $dataArr[$i]['name']);
                    $templateProcessor->setValue('positionC#'.($C), $dataArr[$i]['position']);
                    $templateProcessor->setValue('telnoC#'.($C), $dataArr[$i]['telno']);
                    $templateProcessor->setValue('faxnoC#'.($C), $dataArr[$i]['faxno']);
                    $templateProcessor->setValue('emailC#'.($C), $dataArr[$i]['email']);
                    $templateProcessor->setValue('mobilC#'.($C), $dataArr[$i]['mobiltel']);
                    $C++;
                }
                if(($i%5)==3){
                    $templateProcessor->setValue('typeD#'.($D),  substr($dataArr[$i]['type'],2));
                    $templateProcessor->setValue('nameD#'.($D), $dataArr[$i]['name']);
                    $templateProcessor->setValue('positionD#'.($D), $dataArr[$i]['position']);
                    $templateProcessor->setValue('telnoD#'.($D), $dataArr[$i]['telno']);
                    $templateProcessor->setValue('faxnoD#'.($D), $dataArr[$i]['faxno']);
                    $templateProcessor->setValue('emailD#'.($D), $dataArr[$i]['email']);
                    $templateProcessor->setValue('mobilD#'.($D), $dataArr[$i]['mobiltel']);
                    $D++;
                }
                if(($i%5)==4){
                    $templateProcessor->setValue('typeE#'.($E),  substr($dataArr[$i]['type'],2));
                    $templateProcessor->setValue('nameE#'.($E), $dataArr[$i]['name']);
                    $templateProcessor->setValue('positionE#'.($E), $dataArr[$i]['position']);
                    $templateProcessor->setValue('telnoE#'.($E), $dataArr[$i]['telno']);
                    $templateProcessor->setValue('faxnoE#'.($E), $dataArr[$i]['faxno']);
                    $templateProcessor->setValue('emailE#'.($E), $dataArr[$i]['email']);
                    $templateProcessor->setValue('mobilE#'.($E), $dataArr[$i]['mobiltel']);
                    $E++;
                }
            }
            /*
            for($i=0; $i<sizeof($dataArr); $i++) {
                $templateProcessor->setValue('type#'.($i+1), $dataArr[$i]['type']);
                $templateProcessor->setValue('name#'.($i+1), $dataArr[$i]['name']);
                $templateProcessor->setValue('position#'.($i+1), $dataArr[$i]['position']);
                $templateProcessor->setValue('telno#'.($i+1), $dataArr[$i]['telno']);
                $templateProcessor->setValue('faxno#'.($i+1), $dataArr[$i]['faxno']);
                $templateProcessor->setValue('email#'.($i+1), $dataArr[$i]['email']);
                $templateProcessor->setValue('mobiltel#'.($i+1), $dataArr[$i]['mobiltel']);
            }
            */
        } else {
            // 要放的資料筆數，先建 列
            $templateProcessor->cloneRow('dept', sizeof($dataArr));
            // 每列要放的資料(#1：第一列、以此類推)
            for($i=0; $i<sizeof($dataArr); $i++) {
                $templateProcessor->setValue('dept#'.($i+1), $dataArr[$i]['dept']);
                $templateProcessor->setValue('address#'.($i+1), $dataArr[$i]['address']);
                $templateProcessor->setValue('telno#'.($i+1), $dataArr[$i]['telno']);
                $templateProcessor->setValue('faxno#'.($i+1), $dataArr[$i]['faxno']);
                $templateProcessor->setValue('email#'.($i+1), $dataArr[$i]['email']);
            }
        }

        $outputname="";
        //1:訓練機構基本資料, 2:首長基本資料, 3:副首長基本資料, 4:聯絡人基本資料
        if($radiotype=='2'){
            $outputname="訓練機構基本資料表-首長基本資料";
        } elseif($radiotype=='3'){
            $outputname="訓練機構基本資料表-副首長基本資料";
        } elseif($radiotype=='4'){
            $outputname="訓練機構基本資料表-聯絡人基本資料";
        } else {
            $outputname="訓練機構基本資料表-訓練機構基本資料";
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$outputname);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }

}
