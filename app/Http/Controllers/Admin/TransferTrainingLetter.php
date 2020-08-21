<?php

namespace App\Http\Controllers\Admin;
use App\Services\User_groupService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Rptlib\OfficeConverterTool;
use App\Services\NoticeEmailService;
use DB;
use App\Models\TTL_mail;


class TransferTrainingLetter extends Controller
{
    public function __construct(NoticeEmailService $noticeEmailService,User_groupService $user_groupService)
    {
        $this->noticeEmailService = $noticeEmailService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('transfer_training_letter', $user_group_auth)){
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
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        // $arraykeys=array_keys((array)$temp[0]);
        $queryData['class'] =isset($temp[0]["class"])?$temp[0]["class"]:"0";

        $temp=$RptBasic->getTerms($temp[0]["class"]);
        $termArr=$temp;
        // $arraykeys=array_keys((array)$temp[0]);
        $queryData['term'] = isset($temp[0]->term)?$temp[0]->term:"0";

        $class_data = $this->noticeEmailService->getClass($queryData);
        $result="";
        $subject="";
        $js="";
        return view('admin/transfer_training_letter/list',compact( 'class_data','classArr','termArr' ,'subject','js','result'));
    }

    public function detail(Request $request)
    {

        $queryData['class'] = $request->get('class');
        $queryData['term'] = $request->get('term');
        $subject= $request->get('subject');

        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);

        $temp=$RptBasic->getTerms($temp[0]["class"]);
        $termArr=$temp;

        $js="$('#classes').val('".$queryData['class']."').change();$('#terms').val('".$queryData['term']."').change();$('#subject').val('".$subject."');";
        $js.='document.getElementById("dvmail").style.visibility="visible";';
        $class_data = $this->noticeEmailService->getClass($queryData);
        $result="";
        return view('admin/transfer_training_letter/list',compact( 'class_data','classArr','termArr' ,'subject','js','result'));

    }

    public function getTerms(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $termArr=$RptBasic->getTerms($request->input('classes'));
        return $termArr;
    }

    public function getContent(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $processArr=$RptBasic->getProcess($request->input('classes'));

        if($processArr[0]->process == '2' || $processArr[0]->process == '4'){
            return '1';
        } else {
            $branch=$RptBasic->getBranch($request->input('classes'),$request->input('term'));
            if($branch[0]->site_branch == '1'){
                return '2';  
            } else if($branch[0]->site_branch == '2'){
                return '3';
            }
        }
        
        return '0';
    }

    // public function detail(Request $request)  SHOULD migrate into index
    // {
    //     $queryData['class'] = $request->get('class');
    //     $queryData['term'] = $request->get('term');
    //     $class_mail_data = array();
    //     $class_mail_data['subject'] = '問卷填答通知';
    //     $class_mail_data['editor'] = '';

    //     $class_data = $this->noticeEmailService->getClass($queryData);
    //     $mail_data = $this->noticeEmailService->getTTLMailData($queryData);
    //     if(!empty($mail_data)){
    //         if(!empty($mail_data['subject'])){
    //             $class_mail_data['subject'] = $mail_data['subject'];
    //         }
    //         if(!empty($mail_data['editor'])){
    //             $class_mail_data['editor'] = $mail_data['editor'];
    //         }
    //         if(!empty($mail_data['date'])){
    //             $class_mail_data['date'] = (date("Y",strtotime($mail_data['date']))-1911).date("/m/d H:i",strtotime($mail_data['date']));
    //         }
    //     }

    //     return view('admin/notice_email/detail', compact('data', 'class_data', 'class_mail_data'));
    // }

    public function list(Request $request, $id)
    {
        $class_data = explode("_",$id);
        $queryData['class'] = $class_data[0];
        $queryData['term'] = $class_data[1];
        $queryData['subject'] = $class_data[2];
        $data = $this->noticeEmailService->getTTLStudentMail($queryData);
        return view('admin/transfer_training_letter/select_mail', compact('data', 'class_data', 'queryData'));
    }

    public function savelist(Request $request)
    {
        //先 只要有上次寄送日期 不要cc
        $data = $request->all();
        $queryData['class'] = $data['class'];
        $queryData['term'] = $data['term'];
        $queryData['subject'] = $data['subject'];

        if(isset($data['checkbox'])){
            foreach($data['checkbox'] as $key => $row){
                if(empty($row)){
                    unset($data['checkbox'][$key]);
                }
            }
            $data['checkbox'] = implode(",",$data['checkbox']);

            $mail_data = $this->noticeEmailService->getTTLMailData($queryData);
            if(!empty($mail_data)){
                $fields = array(
                    'mail_list' => $data['checkbox'],
                );
                //更新
                TTL_mail::where('class', $queryData['class'])->where('term', $queryData['term'])->update($fields);
            }else{
                $fields = array(
                    'class' => $data['class'],
                    'term' => $data['term'],
                    'mail_list' => $data['checkbox'],
                );
                $result = TTL_mail::create($fields);
            }
           
            return redirect('/admin/transfer_training_letter/detail?class='.$queryData['class'].'&term='.$queryData['term'].'&subject='.$queryData['subject'])->with('result', '1')->with('message', '收件者選擇成功!');
        }else{
            return redirect('/admin/transfer_training_letter/detail?class='.$queryData['class'].'&term='.$queryData['term'].'&subject='.$queryData['subject'])->with('result', '1')->with('message', '未選擇收件者!');
        }

    }

    // sample code
    // public function save_mail(Request $request)
    // {

    //     $data = $request->all();

    //     $queryData['class'] = $data['classes'];
    //     $queryData['term'] = $data['terms'];
    //     $mail_data = $this->noticeEmailService->getTTLMailData($queryData);
    //     if(!empty($mail_data)){
    //         $fields = array(
    //             'subject' => $data['subject'],
    //             'editor' => $data['editor'],
    //             'date' => date('Y-m-d H:i:s'),
    //         );

    //         TTL_mail::where('class', $queryData['class'])->where('term', $queryData['term'])->update($fields);
    //     }else{
    //         $fields = array(
    //             'class' => $data['classes'],
    //             'term' => $data['terms'],
    //             'subject' => $data['subject'],
    //             'editor' => $data['editor'],
    //             'date' => date('Y-m-d H:i:s'),
    //         );
    //         $result = TTL_mail::create($fields);
    //     }

    //     if(!empty($mail_data['mail_list'])){
    //         $mail = explode(",",$mail_data['mail_list']);
    //         $mail = array('peter19841115@hotmail.com', 'clairec4305@gmail.com');

    //         Mail::send("admin/transfer_training_letter/send", $data, function ($message) use ($mail,$data){
    //                  $message->from('fet@hrd.gov.tw', 'CSDI自動寄信通知');
    //             $message->subject($data['subject']);
    //             $message->to($mail);
    //         });

    //         return back()->with('result', '1')->with('message', '寄送成功!');
    //     }else{
    //         return back()->with('result', '0')->with('message', '尚未選擇收件者!');
    //     }


    // }
    // public function mail_to_me(Request $request)
    // {

    //     $data = $request->all();

    //     $mail = auth()->user()->email;

    //     Mail::send("admin/transfer_training_letter/send", $data, function ($message) use ($mail,$data){
    //              $message->from('fet@hrd.gov.tw', 'CSDI自動寄信通知');
    //         $message->subject($data['subject']);
    //         $message->to($mail);
    //     });

    //     $queryData['class'] = $data['classes'];
    //     $queryData['term'] = $data['terms'];
    //     $mail_data = $this->noticeEmailService->getTTLMailData($queryData);
    //     if(!empty($mail_data)){
    //         $fields = array(
    //             'subject' => $data['subject'],
    //             'editor' => $data['editor'],
    //         );

    //         TTL_mail::where('class', $queryData['class'])->where('term', $queryData['term'])->update($fields);
    //     }else{
    //         $fields = array(
    //             'class' => $data['classes'],
    //             'term' => $data['terms'],
    //             'subject' => $data['subject'],
    //             'editor' => $data['editor'],
    //         );
    //         $result = TTL_mail::create($fields);
    //     }

    //     return back()->with('result', '1')->with('message', '寄送成功!');

    // }
 
    public function mail(Request $request)
    {
        $data = $request->all();

        $queryData['class'] = $data['classes'];
        $queryData['term'] = $data['terms'];

        $mail_data = $this->noticeEmailService->getTTLMailData($queryData);
        if(!empty($mail_data)){
            $fields = array(
                'subject' => $data['subject'],
                'editor' => $data['editor'],
                'date' => date('Y-m-d H:i:s'),
            );

            TTL_mail::where('class', $queryData['class'])->where('term', $queryData['term'])->update($fields);
        }else{
            $fields = array(
                'class' => $data['classes'],
                'term' => $data['terms'],
                'subject' => $data['subject'],
                'editor' => $data['editor'],
                'date' => date('Y-m-d H:i:s'),
            );
            $result = TTL_mail::create($fields);
        }
        //附件
        // 1、學員名冊，抓「學員名冊」功能的參訓人員名冊(已序號)。
        // 2、課程表，抓「課程表」功能的依班期&單週。
        // 3、停車卡。 (附件檔案 固定格式直接赴檔案就可以)


        $data['file_1'] ='';
        $data['file_2'] ='';
        $data['file_3'] ='';
        if(isset($data['attached'])){
         
            foreach($data['attached'] as $attacedid){
                switch ($attacedid) {
                    case 1://學員名冊
                        $data['file_1'] = $this->student_list($queryData['class'],$queryData['term']);
                        break;
                    case 2://課程表
                        $data['file_2'] = $this->course_schedule_list($queryData['class'],$queryData['term']);
                        break;
                    case 3://停車卡
                        $data['file_3'] = '../example/parkingcard.pdf';
                        break;
                  
                }
            }
            
        }

       
        if(!empty($mail_data['mail_list'])){
            $mail = explode(",",$mail_data['mail_list']);
            $mail = array('hws0106@gmail.com');

            Mail::send("admin/transfer_training_letter/send", $data, function ($message) use ($mail,$data){
                $message->from('fet@hrd.gov.tw', 'CSDI自動寄信通知');
                $message->subject($data['subject']);
                $message->to($mail);
                if($data['file_1']!=''){
                    $message->attach($data['file_1'],['as'=>'學員名冊.pdf']);
                }
                if($data['file_2']!=''){
                    $message->attach($data['file_2'],['as'=>'課程表.pdf']);
                } 
                if($data['file_3']!=''){
                    $message->attach('../example/parkingcard.pdf',['as'=>'停車卡.pdf']);
       
                }
            });

            return back()->with('result', '1')->with('message', '郵件寄送完成!');
        }else{
            return back()->with('result', '0')->with('message', '尚未選擇收件者!');
        }

    }

    public function mailtome(Request $request)
    {

        $data = $request->all();
        //附件
        // 1、學員名冊，抓「學員名冊」功能的參訓人員名冊(已序號)。
        // 2、課程表，抓「課程表」功能的依班期&單週。
        // 3、停車卡。 (附件檔案 固定格式直接赴檔案就可以)
      
        $data['file_1'] ='';
        $data['file_2'] ='';
        $data['file_3'] ='';
        if(isset($data['attached'])){
         
            foreach($data['attached'] as $attacedid){
                switch ($attacedid) {
                    case 1://學員名冊
                        $data['file_1'] = $this->student_list($queryData['class'],$queryData['term']);
                        break;
                    case 2://課程表
                        $data['file_2'] = $this->course_schedule_list($queryData['class'],$queryData['term']);
                        break;
                    case 3://停車卡
                        $data['file_3'] = '../example/parkingcard.pdf';
                        break;
                  
                }
            }
            
        }

        $mail = auth()->user()->email;
        $mail = array('csditest3322@gmail.com');

        Mail::send("admin/transfer_training_letter/send", $data, function ($message) use ($mail,$data){
            $message->from('fet@hrd.gov.tw', 'CSDI自動寄信通知');
            $message->subject($data['subject']);
            $message->to($mail);
            if($data['file_1']!=''){
                $message->attach($data['file_1'],['as'=>'學員名冊.pdf']);
            }
            if($data['file_2']!=''){
                $message->attach($data['file_2'],['as'=>'課程表.pdf']);
            } 
            if($data['file_3']!=''){
                $message->attach('../example/parkingcard.pdf',['as'=>'停車卡.pdf']);
   
            }
        });

        $queryData['class'] = $data['classes'];
        $queryData['term'] = $data['terms'];
        $mail_data = $this->noticeEmailService->getTTLMailData($queryData);
        if(!empty($mail_data)){
            $fields = array(
                'subject' => $data['subject'],
                'editor' => $data['editor'],
            );

            TTL_mail::where('class', $queryData['class'])->where('term', $queryData['term'])->update($fields);
        }else{
            $fields = array(
                'class' => $data['classes'],
                'term' => $data['terms'],
                'subject' => $data['subject'],
                'editor' => $data['editor'],
            );
            $result = TTL_mail::create($fields);
        }

        return back()->with('result', '1')->with('message', '已將範本寄送到您的信箱!');

    }

    /* 學員名冊 */
    public function student_list($classes,$term){

            //1:參訓人員名冊(已序號), 2:參訓人員名冊(未含序號), 3:結訓人員名冊 , 4:最新學員名冊
            $outputtype = 4;
    
            //1:組別選項 checked value:1
            $checkteam = '1';
            //是否依組別分頁: Y是, N否
            $grouptype = 'N';
            //2:學歷  checked value:1
            $checkedu = 1;
            //3:出生日期 checked value:1
            $checkbirth = 1;
            
            //取得Title
            $sqlTitle=" SELECT DISTINCT A.class,
                                CONCAT(RTRIM(A.name), '第',
                                    (CASE B.term
                                                WHEN '01' THEN '1'
                                                WHEN '02' THEN '2'
                                                WHEN '03' THEN '3'
                                                WHEN '04' THEN '4'
                                                WHEN '05' THEN '5'
                                                WHEN '06' THEN '6'
                                                WHEN '07' THEN '7'
                                                WHEN '08' THEN '8'
                                                WHEN '09' THEN '9'
                                    ELSE B.term END),'期') AS classname,
                                B.sdate, B.edate , A.type, A.special
                        FROM t01tb A LEFT JOIN t04tb B ON A.class = B.class
                        WHERE EXISTS (SELECT * FROM t04tb C WHERE A.class=C.class)
                        AND B.class= '".$classes."'
                        AND B.term= '".$term."'
                        ";
            $reportlistTitle = DB::select($sqlTitle);
            $dataArrTitle=json_decode(json_encode(DB::select($sqlTitle)), true);
            //取出全部項目
            //if(sizeof($reportlistTitle) != 0) {
            //    $arraykeysTitle=array_keys((array)$reportlistTitle[0]);
            //}
    
            $sqlPara="";
            if($outputtype=='1'){
                $sqlPara=$sqlPara." AND A.no<>'' ";
            }
            if($outputtype=='3' or $outputtype=='4'){
                $sqlPara=$sqlPara." AND A.status='1' ";
            }
            if($checkteam=='1' && $grouptype=='Y'){
                $sqlPara=$sqlPara." ORDER BY A.groupno,A.no ";
            }else{
                $sqlPara=$sqlPara." ORDER BY A.no ";
            }
    
            //取得 學員名冊
            $sql="SELECT A.no,
                            B.dept,
                            CONCAT(SUBSTRING(B.cname,1,1),'O',SUBSTRING(B.cname,-1)) AS cname,
                            B.position,
                                CONCAT(SUBSTRING(B.birth,1,3),'/',SUBSTRING(B.birth,4,2),'/',SUBSTRING(B.birth,6,2)) AS birth,
                                (CASE B.sex WHEN 'F' THEN '女' WHEN 'M' THEN '男' ELSE '' END) AS sex,
                            B.education,
                                CONCAT((CASE A.extradorm WHEN 'Y' THEN '*' WHEN 'N' THEN '' ELSE '' END),
                                            (CASE A.dorm WHEN 'Y' THEN '是' WHEN 'N' THEN ''  ELSE '' END)) AS dorm,
                            (CASE vegan WHEN 'Y' THEN '是' WHEN 'N' THEN '' ELSE '' END) AS vegan,
                                (CASE C.mboard WHEN '1' THEN '三餐' WHEN '2' THEN '三餐' WHEN '3' THEN '午餐' ELSE '' END) AS mboard,
                            A.groupno,
                                (CASE race WHEN '1' THEN '現職' WHEN '2' THEN '退休' WHEN '3' THEN '里民' ELSE '' END) AS race
                    FROM t13tb A LEFT JOIN m02tb B ON A.idno=B.idno
                                LEFT JOIN t40tb C ON A.class=C.class AND A.term=C.term AND A.idno=C.idno
                    WHERE A.class= '".$classes."'
                        AND A.term= '".$term."'
                        ".$sqlPara;
            $reportlist = DB::select($sql);
            $dataArr=json_decode(json_encode(DB::select($sql)), true);
            //取出全部項目
            if(sizeof($reportlist) != 0) {
                $arraykeys=array_keys((array)$reportlist[0]);
            }
    
            // 檔案名稱
            $fileName = 'J2';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel
            $objPHPExcel = IOFactory::load($filePath);
            $excelReader = IOFactory::createReaderForFile($filePath);
            $excelReader->setReadDataOnly(false);
            $objPHPExcel = $excelReader->load($filePath);
            $objActSheet = $objPHPExcel->getActiveSheet();
            $objActSheet = $objPHPExcel->getSheet(0);
    
            $reportlist = json_decode(json_encode($reportlist), true);
            $reportlistTitle = json_decode(json_encode($reportlistTitle), true);
    
            $filenameC ='';
            if($outputtype=='1'){
                $filenameC ='參訓人員名冊';
            }elseif($outputtype=='2'){
                $filenameC ='參訓人員名冊';
            }elseif($outputtype=='3'){
                $filenameC ='結訓人員名冊';
            }elseif($outputtype=='4'){
                $filenameC ='最新學員名冊';
            }
    
            //分頁分組顯示
            if($checkteam=='1' && $grouptype=='Y'){
                $groupno='';
                //分頁
                $pageline=0;
                //$startborder=3;
    
                if(sizeof($reportlist) != 0) {
                    for ($j=0; $j < sizeof($reportlist); $j++) {
                        if($j==0){
                            $groupno=$reportlist[$j][$arraykeys[10]];
                            //簡報表達技巧基礎研習班第1期參訓人員名冊
                            $objActSheet->setCellValue('A1', $dataArrTitle[0]['classname'].$filenameC);
                            $objActSheet->mergeCells('A1:L1');
                            //107.05.09 至 107.05.09　(第2組)
                            if($reportlist[$j][$arraykeys[10]]<>''){
                                $objActSheet->setCellValue('A2', substr($dataArrTitle[0]['sdate'],0,3).'.'.substr($dataArrTitle[0]['sdate'],3,2).'.'.substr($dataArrTitle[0]['sdate'],5,2).' 至 '.substr($dataArrTitle[0]['edate'],0,3).'.'.substr($dataArrTitle[0]['edate'],3,2).'.'.substr($dataArrTitle[0]['edate'],5,2).'　(第'.$reportlist[$j][$arraykeys[10]].'組)');
                            }else{
                                $objActSheet->setCellValue('A2', substr($dataArrTitle[0]['sdate'],0,3).'.'.substr($dataArrTitle[0]['sdate'],3,2).'.'.substr($dataArrTitle[0]['sdate'],5,2).' 至 '.substr($dataArrTitle[0]['edate'],0,3).'.'.substr($dataArrTitle[0]['edate'],3,2).'.'.substr($dataArrTitle[0]['edate'],5,2));
                            }
                            $objActSheet->mergeCells('A2:L2');
                            $startborder=3;
                        }
                        if($reportlist[$j][$arraykeys[10]]<>$groupno && $reportlist[$j][$arraykeys[10]]<>''){
                            //簡報表達技巧基礎研習班第1期參訓人員名冊
                            $objActSheet->setCellValue('A'.($j+4+$pageline), $dataArrTitle[0]['classname'].$filenameC);
                            $objActSheet->mergeCells('A'.($j+4+$pageline).':L'.($j+4+$pageline));
                            $objActSheet->getRowDimension($j+4+$pageline)->setRowHeight(30);  //高 30
                            $objActSheet->getStyle('A'.($j+4+$pageline).':L'.($j+4+$pageline))->getFont()->setSize(14);
                            //107.05.09 至 107.05.09　(第2組)
                            if($reportlist[$j][$arraykeys[10]]<>''){
                                $objActSheet->setCellValue('A'.($j+4+$pageline+1), substr($dataArrTitle[0]['sdate'],0,3).'.'.substr($dataArrTitle[0]['sdate'],3,2).'.'.substr($dataArrTitle[0]['sdate'],5,2).' 至 '.substr($dataArrTitle[0]['edate'],0,3).'.'.substr($dataArrTitle[0]['edate'],3,2).'.'.substr($dataArrTitle[0]['edate'],5,2).'　(第'.$reportlist[$j][$arraykeys[10]].'組)');
                            }else{
                                $objActSheet->setCellValue('A'.($j+4+$pageline+1), substr($dataArrTitle[0]['sdate'],0,3).'.'.substr($dataArrTitle[0]['sdate'],3,2).'.'.substr($dataArrTitle[0]['sdate'],5,2).' 至 '.substr($dataArrTitle[0]['edate'],0,3).'.'.substr($dataArrTitle[0]['edate'],3,2).'.'.substr($dataArrTitle[0]['edate'],5,2));
                            }
                            $objActSheet->mergeCells('A'.($j+4+$pageline+1).':L'.($j+4+$pageline+1));
                            $objActSheet->getRowDimension($j+4+$pageline+1)->setRowHeight(20); //高 20
                            $objActSheet->getStyle('A'.($j+4+$pageline+1).':L'.($j+4+$pageline+1))->getFont()->setSize(12);
                            //項目列
                            $objActSheet->setCellValue('A'.($j+4+$pageline+2),'學號');
                            $objActSheet->setCellValue('B'.($j+4+$pageline+2),'服　務　機　關');
                            $objActSheet->setCellValue('C'.($j+4+$pageline+2),'姓名');
                            $objActSheet->setCellValue('D'.($j+4+$pageline+2),'職稱');
                            $objActSheet->setCellValue('E'.($j+4+$pageline+2),'出生日期');
                            $objActSheet->setCellValue('F'.($j+4+$pageline+2),'性別');
                            $objActSheet->setCellValue('G'.($j+4+$pageline+2),'學        歷');
                            $objActSheet->setCellValue('H'.($j+4+$pageline+2),'住宿');
                            $objActSheet->setCellValue('I'.($j+4+$pageline+2),'素食');
                            $objActSheet->setCellValue('J'.($j+4+$pageline+2),'膳食');
                            $objActSheet->setCellValue('K'.($j+4+$pageline+2),'組別');
                            $objActSheet->setCellValue('L'.($j+4+$pageline+2),'學員分類');
                            $styleCenter = array(
                                'alignment' => array(
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                )
                            );
                            $objActSheet->getStyle('A'.($j+4+$pageline+2).':L'.($j+4+$pageline+2))->applyFromArray($styleCenter);
                            $arrayStyle = [
                                'borders' => [
                            //只有外框           'outline' => [
                                        'allBorders'=> [
                                        'borderStyle' => Border::BORDER_THIN,
                                        'color' => ['rgb' => '000000'],
                                    ],
                                ],
                            ];
                            $objActSheet->getStyle('A'.($j+4+$pageline+2).':L'.($j+4+$pageline+2))->applyFromArray($arrayStyle);
                            //$startborder=($j+4+$pageline+2);
                            $pageline=$pageline+3;
                        }
                        for ($i=0; $i < sizeof($arraykeys); $i++) {
                            //excel 欄位 1 == A, etc
                            $NameFromNumber=$this->getNameFromNumber($i+1);
                            if($i==4){
                                $objActSheet->setCellValue($NameFromNumber.($j+4+$pageline), ltrim($reportlist[$j][$arraykeys[$i]],'0'));
                            }else{
                                $objActSheet->setCellValue($NameFromNumber.($j+4+$pageline), $reportlist[$j][$arraykeys[$i]]);
                            }
                            $arrayStyle = [
                                'borders' => [
                            //只有外框           'outline' => [
                                        'allBorders'=> [
                                        'borderStyle' => Border::BORDER_THIN,
                                        'color' => ['rgb' => '000000'],
                                    ],
                                ],
                            ];
                            $objActSheet->getStyle($NameFromNumber.($j+4+$pageline))->applyFromArray($arrayStyle);
                        }
    
                        $groupno=$reportlist[$j][$arraykeys[10]];
                    }
                }
            }else{
                if(sizeof($reportlist) != 0) {
                    for ($j=0; $j < sizeof($reportlist); $j++) {
                        for ($i=0; $i < sizeof($arraykeys); $i++) {
                            //excel 欄位 1 == A, etc
                            $NameFromNumber=$this->getNameFromNumber($i+1);
                            if($i==4){
                                $objActSheet->setCellValue($NameFromNumber.($j+4), ltrim($reportlist[$j][$arraykeys[$i]],'0'));
                            }else{
                                $objActSheet->setCellValue($NameFromNumber.($j+4), $reportlist[$j][$arraykeys[$i]]);
                            }
                        }
                    }
                    $arrayStyle = [
                        'borders' => [
                    //只有外框           'outline' => [
                                'allBorders'=> [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ];
                    $objActSheet->getStyle('A4:L'.($j+3))->applyFromArray($arrayStyle);
                }
            }
    
            /*
            '訓練班別與委辦班別(t01tb. special= Y)與
            '游於藝講堂班別(t01tb.type <> 13)之格式不同。
            'PS: 游於藝講堂班別不可加印「組別」。
            */
            if($dataArrTitle[0]['special']=='Y'){
                $objActSheet->getColumnDimension('L')->setVisible(false); //學員分類
                //if($checkteam!='1'){
                    $objActSheet->getColumnDimension('K')->setVisible(false); //組別
                // }
            }elseif($dataArrTitle[0]['special']=='13'){
                $objActSheet->getColumnDimension('H')->setVisible(false); //住宿
                $objActSheet->getColumnDimension('I')->setVisible(false); //素食
                $objActSheet->getColumnDimension('J')->setVisible(false); //膳食
                //if($checkteam!='1'){
                    $objActSheet->getColumnDimension('K')->setVisible(false); //組別
                //}
            }else{
                $objActSheet->getColumnDimension('J')->setVisible(false); //膳食
                $objActSheet->getColumnDimension('L')->setVisible(false); //學員分類
                if($checkteam!='1'){
                    $objActSheet->getColumnDimension('K')->setVisible(false); //組別
                }elseif($grouptype=='Y'){
                    $objActSheet->getColumnDimension('K')->setVisible(false); //組別
                }
            }
    
            if($checkbirth!='1'){
                $objActSheet->getColumnDimension('E')->setVisible(false); //出生日期
            }
    
            if($checkedu!='1'){
                $objActSheet->getColumnDimension('G')->setVisible(false); //學歷
            }
    
            if(!($checkteam=='1' && $grouptype=='Y')){
                //簡報表達技巧基礎研習班第1期參訓人員名冊
                $objActSheet->setCellValue('A1', $dataArrTitle[0]['classname'].$filenameC);
                $objActSheet->mergeCells('A1:L1');
                //107.01.17 至 107.01.19
                $objActSheet->setCellValue('A2', substr($dataArrTitle[0]['sdate'],0,3).'.'.substr($dataArrTitle[0]['sdate'],3,2).'.'.substr($dataArrTitle[0]['sdate'],5,2).' 至 '.substr($dataArrTitle[0]['edate'],0,3).'.'.substr($dataArrTitle[0]['edate'],3,2).'.'.substr($dataArrTitle[0]['edate'],5,2));
                $objActSheet->mergeCells('A2:L2');
            }
    
            // 設定下載 Excel 的檔案名稱
    
            if($checkteam=='1'){
                $filenameC =$filenameC.'-含組別';
            }
            if($checkteam=='1' && $grouptype=='Y'){
                $filenameC =$filenameC.'-依組別分頁';
            }
    
            if($checkteam=='1' && $checkedu=='1'){
                $filenameC =$filenameC.'、學歷';
            }elseif($checkedu=='1'){
                $filenameC =$filenameC.'-學歷';
            }
    
            if($checkteam=='1' && $checkedu=='1' && $checkbirth=='1'){
                $filenameC =$filenameC.'、出生日期';
            }elseif($checkteam=='1' && $checkbirth=='1'){
                $filenameC =$filenameC.'、出生日期';
            }elseif($checkedu=='1' && $checkbirth=='1'){
                $filenameC =$filenameC.'、出生日期';
            }elseif($checkbirth=='1'){
                $filenameC =$filenameC.'-出生日期';
            }

            $officeConverterTool = new OfficeConverterTool();
            $today_filepath  =  $officeConverterTool->today_filepath();
    
            $r_filename = $today_filepath.DS.time().'.xlsx';
            $outfilename =''; //不設定新名稱則沿用原來擋案名稱
            $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
            $objWriter->save($r_filename); //先把檔案存起來
            $new_file = $officeConverterTool->Converter2OtherFileType($r_filename,$outfilename,'pdf');
            return $new_file;
    }
    
    /* 課程表 */
    public function course_schedule_list($class,$term){
        
                // $weekpicker=$request->input('weekpicker');
                // $cardselect=$request->input('cardselect');
                $cardselect=1;
                $weektype=1;
                $area=3;
                //$outputname="課程表";依班期-單週 雙週  依整週 台北院區 南投院區 全部
                $outputfile="課程表";
                $weekarray=array("日","一","二","三","四","五","六");
                $tdate="";
                $tcnt=0;
                $sdate="";
                $edate="";
        
                $sql="SELECT
                (
                    CASE B.branch
                    WHEN '1' THEN CONCAT(IFNULL(RTRIM(C.name),''),'(臺北院區)')
                    WHEN '2' THEN CONCAT(IFNULL(RTRIM(D.name),''),'(南投院區)')
                    END
                ) as roomname
                FROM t04tb A
                INNER JOIN t01tb B ON A.class = B.class
                LEFT JOIN m14tb C  ON A.site = C.site
                LEFT JOIN m25tb D ON A.site = D.site
                WHERE A.class = '".$class."' AND A.term = '".$term."'";
                $temp=DB::select("$sql");
        
                if($temp==[])
                {
                    // $result="查無資料";
                    // $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                    // FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                    // ORDER BY t04tb.class DESC");
                    // $classArr=$temp;
                    // $temp=json_decode(json_encode($temp), true);
                    // $arraykeys=array_keys((array)$temp[0]);
                    // $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                    // $termArr=$temp;
        
                     return '';
                }
        
                $temp=json_decode(json_encode($temp), true);
                $roomname=$temp[0]["roomname"];
        
                if($cardselect=="1"){   //依班期
        
                    $sql="select type from t01tb where class='".$class."'";
                    $temp=DB::select("$sql");
                    $temp=json_decode(json_encode($temp), true);
                    $ctype=$temp[0]["type"];
        
                    if($ctype=="13"){  //游於藝
                        $outputfile.="-游於藝";
                        $sql="select distinct * from (
                        select IFNULL(t01tb.name,'') as classname,  #系列主題
                        IFNULL(t01tb.object,'') as object,  #課程目標
                        IFNULL(t01tb.target,'') as target,  #對象
                        IFNULL(t01tb.quota,0) as quota,  #人數
                        IFNULL(t04tb.fee,0) as fee,  #費用
                        t06tb.course as course,  #課程編號
                        t06tb.name as coursename,  #課程名稱
                        t06tb.date as date,  #日期
                        t06tb.stime as stime,  #開始時間
                        t06tb.etime as etime,  #結束時間
                        t06tb.matter as matter,  #課程內容
                        CONCAT(IFNULL(RTRIM(m14tb.name),''),'(臺北院區)') as classroom,  #上課地點
                        IFNULL(t04tb.lineup,'') as lineup,  #教師人數1.表一人2.表多人
                        IFNULL(t04tb.remark,'') as remark,  #備註
                        IFNULL(t08tb.cname,'') as teacher  #講座姓名
                        from t06tb
                        left outer join t01tb on t01tb.class=t06tb.class
                        left outer join t04tb on t04tb.class=t06tb.class and t04tb.term=t06tb.term
                        left outer join m14tb on m14tb.site=t04tb.site
                        left outer join t08tb on t06tb.course=t08tb.course
                        and t06tb.class=t08tb.class
                        and t06tb.term=t08tb.term
                        and t08tb.hire='Y'
                        left outer join t09tb on t08tb.idno=t09tb.idno
                        and t08tb.class=t09tb.class
                        and t08tb.term=t09tb.term
                        and t08tb.course=t09tb.course
                        where t06tb.class='".$class."'
                        and t06tb.term='".$term."'
                        and t06tb.date<>''
                        and t08tb.idkind<>'1'
        
                        #英文姓名
                        union all
        
                        select IFNULL(t01tb.name,'') as classname,   #系列主題
                        IFNULL(t01tb.object,'') as object,  #課程目標
                        IFNULL(t01tb.target,'') as target,  #對象
                        IFNULL(t01tb.quota,0) as quota,  #人數
                        IFNULL(t04tb.fee,0) as fee,  #費用
                        t06tb.course as course,  #課程編號
                        t06tb.name as coursename,  #課程名稱
                        t06tb.date as date,  #日期
                        t06tb.stime as stime,  #開始時間
                        t06tb.etime as etime,  #結束時間
                        t06tb.matter as matter,  #課程內容
                        CONCAT(IFNULL(RTRIM(m14tb.name),''),'(臺北院區)') as classroom,  #上課地點
                        IFNULL(t04tb.lineup,'') as lineup,  #教師人數1.表一人2.表多人
                        IFNULL(t04tb.remark,'') as remark,  #備註
                        IFNULL(t08tb.ename,'') as teacher  #講座姓名
                        from t06tb
                        left outer join t01tb on t01tb.class=t06tb.class
                        left outer join t04tb on t04tb.class=t06tb.class
                        and t04tb.term=t06tb.term
                        left outer join m14tb  on m14tb.site=t04tb.site
                        left outer join t08tb on t06tb.course=t08tb.course
                        and t06tb.class=t08tb.class
                        and t06tb.term=t08tb.term
                        and t08tb.hire='Y'
                        and t08tb.idkind='1'
                        left outer join t09tb on t08tb.idno=t09tb.idno
                        and t08tb.class=t09tb.class
                        and t08tb.term=t09tb.term
                        and t08tb.course=t09tb.course
                        where t06tb.class='".$class."'
                        and t06tb.term='".$term."'
                        and t06tb.date<>''
                        and t08tb.idkind='1'
        
                        #無聘任資料
                        union all
        
                        select IFNULL(t01tb.name,'') as classname,   #系列主題
                        IFNULL(t01tb.object,'') as object,  #課程目標
                        IFNULL(t01tb.target,'') as target,  #對象
                        IFNULL(t01tb.quota,0) as quota,  #人數
                        IFNULL(t04tb.fee,0) as fee,  #費用
                        t06tb.course as course,  #課程編號
                        t06tb.name as coursename,  #課程名稱
                        t06tb.date as date,  #日期
                        t06tb.stime as stime,  #開始時間
                        t06tb.etime as etime,  #結束時間
                        t06tb.matter as matter,  #課程內容
                        CONCAT(IFNULL(RTRIM(m14tb.name),''),'(臺北院區)') as classroom,  #上課地點
                        IFNULL(t04tb.lineup,'') as lineup,  #教師人數1.表一人2.表多人
                        IFNULL(t04tb.remark,'') as remark,  #備註
                        '' as teacher  #講座姓名
                        from t06tb
                        left outer join t01tb on t01tb.class=t06tb.class
                        left outer join t04tb on t04tb.class=t06tb.class
                        and t04tb.term=t06tb.term
                        left outer join m14tb  on m14tb.site=t04tb.site
                        where t06tb.class='".$class."'
                        and t06tb.term='".$term."'
                        and t06tb.date<>''
                        and t06tb.course not in (select course from t08tb where class='".$class."' and term='".$term."')
                        ) AS AA
                        order by AA.date,AA.stime";
        
                        $temp = DB::select($sql);
        
                        if($temp==[])
                        {
                            // $result="查無資料";
                            // $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                            // FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                            // ORDER BY t04tb.class DESC");
                            // $classArr=$temp;
                            // $temp=json_decode(json_encode($temp), true);
                            // $arraykeys=array_keys((array)$temp[0]);
                            // $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                            // $termArr=$temp;
        
                             return '';
                        }
        
                        $temp=json_decode(json_encode($temp), true);
                        $lineup=$temp[0]["lineup"];
                        $data=$temp;
        
                        if($lineup=="1"){//單一講師 F7C1.docx
        
                        // 讀檔案
                        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F7C1').'.docx');
                        ini_set('pcre.backtrack_limit', 999999999);
                        $templateProcessor->setValue('classname',$data[0]["classname"]);
                        $templateProcessor->setValue('object',$data[0]["object"]);
                        $templateProcessor->setValue('target',$data[0]["target"]);
                        $templateProcessor->setValue('quota',$data[0]["quota"]);
                        $templateProcessor->setValue('fee',$data[0]["fee"]);
                        $templateProcessor->setValue('classroom',$data[0]["classroom"]);
                        $templateProcessor->setValue('stime',strval((int)substr($data[0]["stime"],0,2)).':'.substr($data[0]["stime"],2,2));
                        $templateProcessor->setValue('etime',strval((int)substr($data[0]["etime"],0,2)).':'.substr($data[0]["etime"],2,2));
                        $templateProcessor->setValue('teacher',$data[0]["teacher"]);
                        $templateProcessor->cloneRow('c', sizeof($data));
        
        
                        for($i=0;$i<sizeof($data);$i++){
                            $dnow=strval((int)substr($data[$i]["date"],0,3)+1911)."-".substr($data[$i]["date"],3,2)."-".substr($data[$i]["date"],5,2);
                            $dnowc=substr($data[$i]["date"],0,3)."-".substr($data[$i]["date"],3,2)."-".substr($data[$i]["date"],5,2);
                            if($tdate!=$data[$i]["date"]){
                                $tdate=$data[$i]["date"];
                                $tcnt=1;
                            }else{
                                $tcnt++;
                            }
                            $templateProcessor->setValue('c#'.strval($i+1),$tcnt);
                            $templateProcessor->setValue('date#'.strval($i+1),$dnowc);
                            $templateProcessor->setValue('w#'.strval($i+1),$weekarray[date("w",strtotime($dnow))]);
                            $templateProcessor->setValue('course#'.strval($i+1),$data[$i]["coursename"]);
                            $templateProcessor->setValue('matter#'.strval($i+1),$data[$i]["matter"]);
                        }
        
                        }else{//多講師 F7C2.docx
                            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F7C2').'.docx');
                            $templateProcessor->setValue('classname',$data[0]["classname"]);
                            $templateProcessor->setValue('object',$data[0]["object"]);
                            $templateProcessor->setValue('target',$data[0]["target"]);
                            $templateProcessor->setValue('quota',$data[0]["quota"]);
                            $templateProcessor->setValue('fee',$data[0]["fee"]);
                            $templateProcessor->setValue('classroom',$data[0]["classroom"]);
                            $templateProcessor->setValue('stime',strval((int)substr($data[0]["stime"],0,2)).':'.substr($data[0]["stime"],2,2));
                            $templateProcessor->setValue('etime',strval((int)substr($data[0]["etime"],0,2)).':'.substr($data[0]["etime"],2,2));
                            $templateProcessor->cloneRow('c', sizeof($data));
        
                            for($i=0;$i<sizeof($data);$i++){
                                $dnow=strval((int)substr($data[$i]["date"],0,3)+1911)."-".substr($data[$i]["date"],3,2)."-".substr($data[$i]["date"],5,2);
                                $dnowc=substr($data[$i]["date"],0,3)."-".substr($data[$i]["date"],3,2)."-".substr($data[$i]["date"],5,2);
                                if($tdate!=$data[$i]["date"]){
                                    $tdate=$data[$i]["date"];
                                    $tcnt=1;
                                }else{
                                    $tcnt++;
                                }
                                $templateProcessor->setValue('c#'.strval($i+1),$tcnt);
                                $templateProcessor->setValue('date#'.strval($i+1),$dnowc);
                                $templateProcessor->setValue('w#'.strval($i+1),$weekarray[date("w",strtotime($dnow))]);
                                $templateProcessor->setValue('course#'.strval($i+1),$data[$i]["coursename"]);
                                $templateProcessor->setValue('matter#'.strval($i+1),$data[$i]["matter"]);
                                $templateProcessor->setValue('teacher#'.strval($i+1),$data[$i]["teacher"]);
                            }
                        }
        
                    }else{//非游於藝
                        $sdate=""; //取消日期條件
                        $sql="SELECT
                        (
                            CASE B.branch
                            WHEN '1' THEN CONCAT(IFNULL(RTRIM(C.name),''),'(臺北院區)')
                            WHEN '2' THEN CONCAT(IFNULL(RTRIM(D.name),''),'(南投院區)')
                            END
                        ) as roomname
                        FROM t04tb A
                        INNER JOIN t01tb B ON A.class = B.class
                        LEFT JOIN m14tb C  ON A.site = C.site
                        LEFT JOIN m25tb D ON A.site = D.site
                        WHERE A.class = '".$class."' AND A.term = '".$term."'";
        
                        $temp = DB::select($sql);
                        $temp=json_decode(json_encode($temp), true);
                        $roomname=$temp[0]["roomname"];
        
                        $sql="SELECT
                        D.type,
                        A.date,
                        CONCAT((CASE A.stime WHEN '' THEN '' ELSE CONCAT(SUBSTRING(A.stime,1,2),':', SUBSTRING(A.stime,3,2)) END) ,
                               (CASE A.etime WHEN '' THEN '' ELSE  CONCAT('-',SUBSTRING(A.etime,1,2),':', SUBSTRING(A.etime,3,2)) END)) AS classtime,
                        A.course as course,
                        IFNULL(A.name,'') AS classname ,
                        (CASE IFNULL(B.cname,'') WHEN '' THEN '' ELSE CONCAT(RTRIM(B.cname),'講座') END) AS name ,
                        C.remark,
                        '".$roomname."' AS roomname
                        FROM t06tb A
                        LEFT JOIN t08tb B ON A.course = B.course AND A.class = B.class AND A.term = B.term
                        LEFT JOIN t04tb C ON A.class = C.class AND A.term = C.term
                        INNER JOIN t09tb D ON B.class = D.class AND B.term = D.term AND B.course = D.course AND B.idno = D.idno
                        WHERE A.class = '".$class."'
                        AND A.term = '".$term."'
                        AND B.idkind <> '1' /* 證號別 1：事業團體 */
                        AND B.hire = 'Y'
                        AND 1 = (
                            CASE
                            WHEN '".$sdate."' = '' THEN 1
                            WHEN A.date BETWEEN '".$sdate."' AND '".$edate."' THEN 1
                            END
                        )
                        UNION
                        SELECT
                        D.type,
                        A.date,
                        CONCAT((CASE A.stime WHEN '' THEN '' ELSE CONCAT(SUBSTRING(A.stime,1,2),':',SUBSTRING(A.stime,3,2)) END),
                               (CASE A.etime WHEN '' THEN '' ELSE CONCAT('-',SUBSTRING(A.etime,1,2),':',SUBSTRING(A.etime,3,2)) END)) AS classtime ,
                        A.course,
                        IFNULL(A.name,'') AS classname,
                        IFNULL(B.ename,'') AS name,
                        C.remark ,
                        '".$roomname."' AS roomname
                        FROM t06tb A LEFT JOIN t08tb B ON A.course=B.course AND A.class=B.class AND A.term=B.term
                        LEFT JOIN t04tb C ON A.class=C.class AND A.term=C.term
                        INNER JOIN t09tb D ON B.class = D.class AND B.term = D.term AND B.course = D.course AND B.idno = D.idno
                        WHERE A.class = '".$class."'
                        AND A.term = '".$term."'
                        AND B.idkind = '1' /* 證號別 1：事業團體 */
                        AND B.hire='Y'
                        AND 1 = (
                            CASE
                            WHEN '".$sdate."' = '' THEN 1
                            WHEN A.date BETWEEN '".$sdate."' AND '".$edate."' THEN 1
                            END
                        )
                        UNION
                        SELECT
                        '3' AS 'type',
                        A.date,
                        CONCAT((CASE A.stime WHEN '' THEN '' ELSE CONCAT(SUBSTRING(A.stime,1,2),':',SUBSTRING(A.stime,3,2)) END),
                               (CASE A.etime WHEN '' THEN '' ELSE CONCAT('-' ,SUBSTRING(A.etime,1,2),':',SUBSTRING(A.etime,3,2)) END)) AS classtime ,
                        A.course,
                        IFNULL(A.name,'') AS classname,
                        '' AS name,
                        B.remark ,
                        '".$roomname."' AS roomname
                        FROM t06tb A
                        LEFT JOIN t04tb B
                        ON A.class=B.class
                        AND A.term=B.term
                        WHERE A.class = '".$class."'
                        AND A.term='".$term."'
                        AND 1 = (
                            CASE
                            WHEN '".$sdate."' = '' THEN 1
                            WHEN A.date BETWEEN '".$sdate."' AND '".$edate."' THEN 1
                            END
                        ) ORDER BY date,course,type";
        
                        $temp = DB::select($sql);
        
                        if($temp==[])
                        {
                            // $result="查無資料";
                            // $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                            // FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                            // ORDER BY t04tb.class DESC");
                            // $classArr=$temp;
                            // $temp=json_decode(json_encode($temp), true);
                            // $arraykeys=array_keys((array)$temp[0]);
                            // $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                            // $termArr=$temp;
                            // $result = '';
                             return '';
                        }
        
                        $temp=json_decode(json_encode($temp), true);
                        $data=$temp;
        
                        $temp=DB::select("SELECT name FROM  t01tb WHERE class='".$class."'");
                        $temp=json_decode(json_encode($temp), true);
                        $classnamet=$temp[0]["name"];
        
                        if($weektype=="1"){ //單週
                            $outputfile.="-依班期-單週";
                            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F7B').'.docx');
                            $templateProcessor->setValue('class',$class);
                            $templateProcessor->setValue('classname',$classnamet);
                            $templateProcessor->setValue('term',strval((int)$term));
                            $templateProcessor->setValue('roomname',$roomname);
        
                            $cntarr=[];
                            $wcnt=-1;
                            $wtemp="";
                            $ctmp="";
                            $itemp="";
                            for($i=0;$i<sizeof($data);$i++){
                                if( $ctmp!=$data[$i]["course"]){ //過濾空值
                                    $ctmp=$data[$i]["course"];
                                    $itemp=$i ;
                                }elseif(isset($data[$i]["name"])){
                                    if(trim($data[$i]["name"])!=""){
                                        if($data[$i]["type"]=="2")
                                            $data[$itemp]["name"]=$data[$itemp]["name"]."<w:br />助教：".str_replace("講座", "", $data[$i]["name"]);
                                        else
                                            $data[$itemp]["name"]=$data[$itemp]["name"]."<w:br />".$data[$i]["name"];
                                    }
                                }
                            }
        
                            $ctmp="";
                            foreach($data as $k => $v){
                                if( $ctmp!=$v["course"]) //拿掉空值
                                    $ctmp=$v["course"];
                                else
                                    unset($data[$k]);
                            }
        
                            foreach($data as $v){//建立每週天數，以此為基礎填入多週報表值
                                    $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                    if($wtemp!=date("W",strtotime($dnow))){
                                        $wtemp=date("W",strtotime($dnow));
        
                                        array_push($cntarr,array(
                                                "week"=>$wtemp,
                                                "rcnt"=>1
                                            ));
        
                                        $wcnt++;
                                    }else{
                                        $cntarr[$wcnt]["rcnt"]++;
                                    }
                            }
        
                            $templateProcessor->cloneBlock('b',sizeof($cntarr), true, true);
        
                            for($i=0;$i<sizeof($cntarr);$i++){
                                $templateProcessor->cloneRow('date#'.strval($i+1), (int)$cntarr[$i]["rcnt"]);
                                if($i<(sizeof($cntarr)-1))
                                    $templateProcessor->setValue('pagebreak#'.strval($i+1), '</w:t></w:r>'.'<w:r><w:br w:type="page"/></w:r>'.'<w:r><w:t>');
                                else
                                    $templateProcessor->setValue('pagebreak#'.strval($i+1), '');
                            }
                            $wpos=0;
                            $rpos=0;
                            $cntarrcnt=0;
                            $tmpname="";
                            $ctmp="";
                            $cname="";
                            if(sizeof($cntarr)==1){
                                $i=0;
                                foreach($data as $v){
        
                                        if($i==0)
                                            $templateProcessor->setValue('remark#'.strval($i+1),$v["remark"]);
        
                                        $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                        $dnowc=strval((int)substr($v["date"],3,2))."月".strval((int)substr($v["date"],5,2))."日";
                                        $templateProcessor->setValue('date#1#'.strval($i+1),$dnowc);
                                        $templateProcessor->setValue('wdate#1#'.strval($i+1),$weekarray[date("w",strtotime($dnow))]);
                                        $templateProcessor->setValue('time#1#'.strval($i+1),$v["classtime"]);
                                        $templateProcessor->setValue('course#1#'.strval($i+1),$v["classname"]);
                                        $templateProcessor->setValue('lec#1#'.strval($i+1),$v["name"]);
        
                                    $i++;
                                }
        
                            }else{
        
                                foreach($data as $v){
                                    if($wpos==0)
                                        $templateProcessor->setValue('remark#'.strval($wpos+1),$v["remark"]);
                                    if($rpos<$cntarr[$wpos]["rcnt"])
                                    {
                                        $rpos++;
                                    }else{
        
                                        $wpos++;
                                        $rpos=1;
                                        $templateProcessor->setValue('remark#'.strval($wpos+1),$v["remark"]);
                                    }
        
                                    $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                    $dnowc=substr($v["date"],0,3)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                    $templateProcessor->setValue('date#'.strval($wpos+1).'#'.$rpos,$dnow);
                                    $templateProcessor->setValue('wdate#'.strval($wpos+1).'#'.$rpos,$weekarray[date("w",strtotime($dnow))]);
                                    $templateProcessor->setValue('time#'.strval($wpos+1).'#'.$rpos,$v["classtime"]);
                                    $templateProcessor->setValue('course#'.strval($wpos+1).'#'.$rpos,$v["classname"]);
                                    $templateProcessor->setValue('lec#'.strval($wpos+1).'#'.$rpos,$v["name"]);
        
                                }
                            }
        
                        }else{ //雙週
                            $outputfile.="-依班期-雙週";
                            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F7A').'.docx');
                            $templateProcessor->setValue('class',$class);
                            $templateProcessor->setValue('classname',$classnamet);
                            $templateProcessor->setValue('term',strval((int)$term));
                            $templateProcessor->setValue('roomname',$roomname);
                            $titlew="";
                            $cntarr=[];
                            $wcnt=-1;
                            $wtemp="";
                            $ctmp="";
                            $itemp="";
                            for($i=0;$i<sizeof($data);$i++){
                                if( $ctmp!=$data[$i]["course"]){ //過濾空值
                                    $ctmp=$data[$i]["course"];
                                    $itemp=$i ;
                                }elseif(isset($data[$i]["name"])){
                                    if(trim($data[$i]["name"])!=""){
                                        if($data[$i]["type"]=="2")
                                            $data[$itemp]["name"]=$data[$itemp]["name"]."<w:br />助教：".str_replace("講座", "", $data[$i]["name"]);
                                        else
                                            $data[$itemp]["name"]=$data[$itemp]["name"]."<w:br />".$data[$i]["name"];
                                    }
                                }
                            }
        
                            $ctmp="";
                            foreach($data as $k => $v){
                                if( $ctmp!=$v["course"]) //拿掉空值
                                    $ctmp=$v["course"];
                                else
                                    unset($data[$k]);
                            }
        
                            foreach($data as $v){//建立每週天數，以此為基礎填入多週報表值
                                $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                if($wtemp!=date("W",strtotime($dnow))){
                                    $wtemp=date("W",strtotime($dnow));
                                    $wcnt++;
                                    array_push($cntarr,array(
                                            "week"=>$wcnt,
                                            "rcnt"=>1
                                        ));
                                }else{
                                    $cntarr[$wcnt]["rcnt"]++;
                                }
                            }
        
                            $templateProcessor->cloneBlock('b',sizeof($cntarr), true, true);
        
                            for($i=0;$i<sizeof($cntarr);$i++){
                                $templateProcessor->cloneRow('w#'.strval($i+1), (int)$cntarr[$i]["rcnt"]);
                                $templateProcessor->setValue('titlew#'.strval($i+1),strval($i*2+1)."、".strval($i*2+2));
                                if($i<(sizeof($cntarr)-1))
                                    $templateProcessor->setValue('pagebreak#'.strval($i+1), '</w:t></w:r>'.'<w:r><w:br w:type="page"/></w:r>'.'<w:r><w:t>');
                                else
                                    $templateProcessor->setValue('pagebreak#'.strval($i+1), '');
                            }
                            $wpos=0;
                            $rpos=0;
                            $cntarrcnt=0;
                            $tmpname="";
                            $ctmp="";
                            $cname="";
                            $tmpdate="";
                            if(sizeof($cntarr)==1){
                                $i=0;
                                foreach($data as $v){
                                        $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                        $dnowc=strval((int)substr($v["date"],3,2))."月".strval((int)substr($v["date"],5,2))."日";
                                        if($i==0){
                                            $templateProcessor->setValue('remark#'.strval($i+1),$v["remark"]);
                                            $templateProcessor->setValue('w#1#'.strval($i+1),"第".strval($wpos+1)."週");
                                        }else{
                                            $templateProcessor->setValue('w#1#'.strval($i+1),"");
                                        }
        
                                            $templateProcessor->setValue('date#1#'.strval($i+1),$dnowc);
                                            $templateProcessor->setValue('wdate#1#'.strval($i+1),$weekarray[date("w",strtotime($dnow))]);
        
                                        $templateProcessor->setValue('time#1#'.strval($i+1),$v["classtime"]);
                                        $templateProcessor->setValue('course#1#'.strval($i+1),$v["classname"]);
                                        $templateProcessor->setValue('lec#1#'.strval($i+1), $v["name"]);
        
                                    $i++;
                                }
                            }else{
                                $tmpwpos=-1;
                                $rpos=0;
                                $wpos=0;
                                foreach($data as $v){
        
                                    if($rpos<$cntarr[$wpos]["rcnt"])
                                    {
                                        $rpos++;
                                    }else{
                                        $wpos++;
                                        $rpos=1;
                                    }
        
                                    $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                    $dnowc=substr($v["date"],0,3)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
        
                                    if($tmpwpos!=$wpos){
                                        $templateProcessor->setValue('remark#'.strval($wpos+1),$v["remark"]);
                                        $templateProcessor->setValue('w#'.strval($wpos+1).'#'.$rpos,"第".strval($wpos+1)."週");
                                        $tmpwpos=$wpos;
                                    }
                                    else{
                                        $templateProcessor->setValue('w#'.strval($wpos+1).'#'.$rpos,"");
                                    }
                                    $templateProcessor->setValue('date#'.strval($wpos+1).'#'.$rpos,$dnowc);
                                    $templateProcessor->setValue('wdate#'.strval($wpos+1).'#'.$rpos,$weekarray[date("w",strtotime($dnow))]);
                                    $templateProcessor->setValue('time#'.strval($wpos+1).'#'.$rpos,$v["classtime"]);
                                    $templateProcessor->setValue('course#'.strval($wpos+1).'#'.$rpos,$v["classname"]);
                                    $templateProcessor->setValue('lec#'.strval($wpos+1).'#'.$rpos,$v["name"]);
        
                                }
        
                            }
                        }
        
                    }
                }else{  //依整週
        
                        $classcnt=0;
                        $weekpicker=$request->input('weekpicker');
                        $tflag="";
                        if($weekpicker!=""){
                            try {
                                $ttemp=explode(" ",$weekpicker);
                                $sdatetmp=explode("/",$ttemp[0]);
                                $edatetmp=explode("/",$ttemp[2]);
                                $sdate=$sdatetmp[0].$sdatetmp[1].$sdatetmp[2];
                                $edate=$edatetmp[0].$edatetmp[1].$edatetmp[2];
                                $tflag="1";
                                // Validate the value...
                            } catch (\Exception $e) {
                                $ttemp="error";
                        }
        
        
                            if($ttemp=="error" || $sdate=="NaNundefinedundefined" )
                            {
                                $RptBasic = new \App\Rptlib\RptBasic();
                                $temp=$RptBasic->getclass();
                                $classArr=$temp;
                                $temp=json_decode(json_encode($temp), true);
                                $arraykeys=array_keys((array)$temp[0]);
                                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                                $termArr=$temp;
                                $result = "日期格式錯誤，請重新輸入。";
                                // return view('admin/lecture_signature/list',compact('classArr','termArr' ,'result'));
                            }
                        }
        
        
                    if($area=="1"){         //台北院區
                        $outputfile.="-依整週-台北院區";
                    }elseif($area=="2"){    //南投院區
                        $outputfile.="-依整週-南投院區";
                    }else{                  //全部
                        $area="";
                        $outputfile.="-依整週-全部";
                    }
        
                    $sql="SELECT
                        A.class,A.term,
                        CONCAT(CAST(A.class AS char),RTRIM(B.name)) AS class_name
                        FROM t06tb A
                        INNER JOIN t01tb B
                        ON A.class = B.class
                        WHERE A.date BETWEEN '".$sdate."' AND '".$edate."'
                        AND '".$area."' = (CASE WHEN '".$area."' = '' THEN '' ELSE B.branch END) /* @branch 上課地點 1:臺北院區 2:南投院區 */
                        GROUP BY A.class,A.term,B.name
                        ORDER BY A.class,A.term";
        
                        $temp = DB::select($sql);
        
                        if($temp==[])
                        {
                            // $result="查無資料";
                            // $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                            // FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                            // ORDER BY t04tb.class DESC");
                            // $classArr=$temp;
                            // $temp=json_decode(json_encode($temp), true);
                            // $arraykeys=array_keys((array)$temp[0]);
                            // $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                            // $termArr=$temp;
                            // $result = '';
                            return '';
                        }
        
                        $temp=json_decode(json_encode($temp), true);
                        $weekdata=$temp;
                        $wsql="";
                        for($i=0;$i<sizeof($weekdata);$i++){
        
        
                            $sql="SELECT
                            (
                                CASE B.branch
                                WHEN '1' THEN CONCAT(IFNULL(RTRIM(C.name),''),'(臺北院區)')
                                WHEN '2' THEN CONCAT(IFNULL(RTRIM(D.name),''),'(南投院區)')
                                END
                            ) as roomname
                            FROM t04tb A
                            INNER JOIN t01tb B ON A.class = B.class
                            LEFT JOIN m14tb C  ON A.site = C.site
                            LEFT JOIN m25tb D ON A.site = D.site
                            WHERE A.class = '".$weekdata[$i]["class"]."' AND A.term = '".$weekdata[$i]["term"]."'";
        
                            $temp = DB::select($sql);
                            if(sizeof($temp)==0)
                                continue;
                            $classcnt++;
                            $temp=json_decode(json_encode($temp), true);
                            $roomname=$temp[0]["roomname"];
        
        
                            //取消日期條件
                            $sql="SELECT
                            A.class,A.term,E.name AS classtermname,
                            D.type,
                            A.date,
                            CONCAT((CASE A.stime WHEN '' THEN '' ELSE CONCAT(SUBSTRING(A.stime,1,2),':', SUBSTRING(A.stime,3,2)) END) ,
                                (CASE A.etime WHEN '' THEN '' ELSE  CONCAT('-',SUBSTRING(A.etime,1,2),':', SUBSTRING(A.etime,3,2)) END)) AS classtime,
                            A.course as course,
                            IFNULL(A.name,'') AS classname ,
                            (CASE IFNULL(B.cname,'') WHEN '' THEN '' ELSE CONCAT(RTRIM(B.cname),'講座') END) AS name ,
                            C.remark,
                            '".$roomname."' AS roomname
                            FROM t06tb A
                            LEFT JOIN t08tb B ON A.course = B.course AND A.class = B.class AND A.term = B.term
                            LEFT JOIN t04tb C ON A.class = C.class AND A.term = C.term
                            INNER JOIN t09tb D ON B.class = D.class AND B.term = D.term AND B.course = D.course AND B.idno = D.idno
                            LEFT JOIN t01tb E ON A.class = E.class
                            WHERE A.class = '".$weekdata[$i]["class"]."'
                            AND A.term = '".$weekdata[$i]["term"]."'
                            AND B.idkind <> '1' /* 證號別 1：事業團體 */
                            AND B.hire = 'Y'
                            UNION
                            SELECT
                            A.class,A.term,E.name AS classtermname,
                            D.type,
                            A.date,
                            CONCAT((CASE A.stime WHEN '' THEN '' ELSE CONCAT(SUBSTRING(A.stime,1,2),':',SUBSTRING(A.stime,3,2)) END),
                                (CASE A.etime WHEN '' THEN '' ELSE CONCAT('-',SUBSTRING(A.etime,1,2),':',SUBSTRING(A.etime,3,2)) END)) AS classtime ,
                            A.course,
                            IFNULL(A.name,'') AS classname,
                            IFNULL(B.ename,'') AS name,
                            C.remark ,
                            '".$roomname."' AS roomname
                            FROM t06tb A LEFT JOIN t08tb B ON A.course=B.course AND A.class=B.class AND A.term=B.term
                            LEFT JOIN t04tb C ON A.class=C.class AND A.term=C.term
                            INNER JOIN t09tb D ON B.class = D.class AND B.term = D.term AND B.course = D.course AND B.idno = D.idno
                            LEFT JOIN t01tb E ON A.class = E.class
                            WHERE A.class = '".$weekdata[$i]["class"]."'
                            AND A.term = '".$weekdata[$i]["term"]."'
                            AND B.idkind = '1' /* 證號別 1：事業團體 */
                            AND B.hire='Y'
                            UNION
                            SELECT
                            A.class,A.term,C.name AS classtermname,
                            '3' AS 'type',
                            A.date,
                            CONCAT((CASE A.stime WHEN '' THEN '' ELSE CONCAT(SUBSTRING(A.stime,1,2),':',SUBSTRING(A.stime,3,2)) END),
                                (CASE A.etime WHEN '' THEN '' ELSE CONCAT('-' ,SUBSTRING(A.etime,1,2),':',SUBSTRING(A.etime,3,2)) END)) AS classtime ,
                            A.course,
                            IFNULL(A.name,'') AS classname,
                            '' AS name,
                            B.remark ,
                            '".$roomname."' AS roomname
                            FROM t06tb A
                            LEFT JOIN t04tb B ON A.class=B.class AND A.term=B.term
                            LEFT JOIN t01tb C ON A.class = C.class
                            WHERE A.class = '".$weekdata[$i]["class"]."'
                            AND A.term='".$weekdata[$i]["term"]."'
                            ORDER BY date,course,type";
        
                            if($i==sizeof($weekdata)-1)
                                $wsql.="SELECT * FROM ( ".$sql." ) AS A".$i;
                            else
                                $wsql.="SELECT * FROM ( ".$sql." ) AS A".$i." UNION ";
        
                        }
        
                        $temp = DB::select($wsql);
        
                        if($temp==[])
                        {
                            // $result="查無資料";
                            // $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                            // FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                            // ORDER BY t04tb.class DESC");
                            // $classArr=$temp;
                            // $temp=json_decode(json_encode($temp), true);
                            // $arraykeys=array_keys((array)$temp[0]);
                            // $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                            // $termArr=$temp;
                            // $result = '';
                            return '';
                        }
        
                        $temp=json_decode(json_encode($temp), true);
                        $data=$temp;
        
        
                        $cntarr=[];
                        $wcnt=-1;
                        $wtemp="";
                        $ctmp="";
                        $classtmp="";
                        $termtmp="";
                        $itemp="";
                        for($i=0;$i<sizeof($data);$i++){
                            if( $ctmp!=$data[$i]["course"]){ //過濾空值
        
                                if($i==0)
                                {
                                    $classtmp=$data[$i]["class"];
                                    $termtmp=$data[$i]["term"];
                                    $ctmp=$data[$i]["course"];
                                    $itemp=$i ;
                                }else{
                                    if($classtmp==$data[$i]["class"] && $termtmp=$data[$i]["term"]){
                                        $ctmp=$data[$i]["course"];
                                        $itemp=$i ;
                                    }else{
                                        $classtmp=$data[$i]["class"];
                                        $termtmp=$data[$i]["term"];
                                        $ctmp=$data[$i]["course"];
                                        $itemp=$i ;
                                    }
                                }
                            }elseif(isset($data[$i]["name"]) && $classtmp==$data[$i]["class"] && $termtmp=$data[$i]["term"]){
                                if(trim($data[$i]["name"])!=""){
                                    if($data[$i]["type"]=="2")
                                        $data[$itemp]["name"]=$data[$itemp]["name"]."<w:br />助教：".str_replace("講座", "", $data[$i]["name"]);
                                    else
                                        $data[$itemp]["name"]=$data[$itemp]["name"]."<w:br />".$data[$i]["name"];
                                }
                            }
                        }
        
                        $ctmp="";
                        $classtmp="";
                        $termtmp="";
                        foreach($data as $k => $v){
                            if($k==0)
                                {
                                    $classtmp=$v["class"];
                                    $termtmp=$v["term"];
                                    $ctmp=$v["course"];
                                }else{
                                    if($classtmp==$v["class"] && $termtmp=$v["term"]){
                                        if( $ctmp!=$v["course"]) //拿掉空值
                                            $ctmp=$v["course"];
                                        else
                                            unset($data[$k]);
                                    }else{
                                        $classtmp=$v["class"];
                                        $termtmp=$v["term"];
                                        $ctmp=$v["course"];
                                    }
                                }
                        }
        
                        $cntarr=[];
                        $ctcnt=-1;
                        $classtmp="";
                        $termtmp="";
                        foreach($data as $v){//建立班期筆數，以此為基礎填入報表值
                                $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                if( $classtmp!=$v["class"] || $termtmp!=$v["term"] ){
                                    $classtmp=$v["class"];
                                    $termtmp=$v["term"];
                                    array_push($cntarr,array(
                                            "ct"=>$classtmp.$termtmp,
                                            "rcnt"=>1
                                        ));
        
                                    $ctcnt++;
                                }else{
                                    $cntarr[$ctcnt]["rcnt"]++;
                                }
                        }
        
                        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F7B').'.docx');
                        $templateProcessor->setValue('class', '');
                        $templateProcessor->cloneBlock('b',sizeof($cntarr), true, true);
        
                        for($i=0;$i<sizeof($cntarr);$i++){
                            $templateProcessor->cloneRow('date#'.strval($i+1), (int)$cntarr[$i]["rcnt"]);
                            if($i<(sizeof($cntarr)-1))
                                $templateProcessor->setValue('pagebreak#'.strval($i+1), '</w:t></w:r>'.'<w:r><w:br w:type="page"/></w:r>'.'<w:r><w:t>');
                            else
                                $templateProcessor->setValue('pagebreak#'.strval($i+1), '');
                        }
                        $ctpos=0;
                        $rpos=0;
                        $cntarrcnt=0;
                        $tmpname="";
                        $ctmp="";
                        $cname="";
                        if(sizeof($cntarr)==1){
                            $i=0;
                            foreach($data as $v){
        
                                    if($i=0){
                                        $templateProcessor->setValue('remark#'.strval($i+1),$v["remark"]);
                                        $templateProcessor->setValue('class#'.strval($i+1),$v["class"]);
                                        $templateProcessor->setValue('classname#'.strval($i+1),$v["classtermname"]);
                                        $templateProcessor->setValue('term#'.strval($i+1),strval((int)$v["term"]));
                                        $templateProcessor->setValue('roomname#'.strval($i+1),$v["roomname"]);
        
                                    }
                                    $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                    $dnowc=strval((int)substr($v["date"],3,2))."月".strval((int)substr($v["date"],5,2))."日";
                                    $templateProcessor->setValue('date#1#'.strval($i+1),$dnowc);
                                    $templateProcessor->setValue('wdate#1#'.strval($i+1),$weekarray[date("w",strtotime($dnow))]);
                                    $templateProcessor->setValue('time#1#'.strval($i+1),$v["classtime"]);
                                    $templateProcessor->setValue('course#1#'.strval($i+1),$v["classname"]);
                                    $templateProcessor->setValue('lec#1#'.strval($i+1),$v["name"]);
        
                                $i++;
                            }
        
                        }else{
        
                            foreach($data as $v){
                                if($ctpos==0)
                                    $templateProcessor->setValue('remark#'.strval($ctpos+1),$v["remark"]);
                                    $templateProcessor->setValue('class#'.strval($ctpos+1),$class);
                                    $templateProcessor->setValue('classname#'.strval($ctpos+1),$v["classtermname"]);
                                    $templateProcessor->setValue('term#'.strval($ctpos+1),strval((int)$v["term"]));
                                    $templateProcessor->setValue('roomname#'.strval($ctpos+1),$v["roomname"]);
                                if($rpos<$cntarr[$ctpos]["rcnt"])
                                {
                                    $rpos++;
                                }else{
        
                                    $ctpos++;
                                    $rpos=1;
                                    $templateProcessor->setValue('remark#'.strval($ctpos+1),$v["remark"]);
                                }
                                $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                $dnowc=substr($v["date"],0,3)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                $templateProcessor->setValue('date#'.strval($ctpos+1).'#'.$rpos,$dnow);
                                $templateProcessor->setValue('wdate#'.strval($ctpos+1).'#'.$rpos,$weekarray[date("w",strtotime($dnow))]);
                                $templateProcessor->setValue('time#'.strval($ctpos+1).'#'.$rpos,$v["classtime"]);
                                $templateProcessor->setValue('course#'.strval($ctpos+1).'#'.$rpos,$v["classname"]);
                                $templateProcessor->setValue('lec#'.strval($ctpos+1).'#'.$rpos,$v["name"]);
                            }
                        }
                }
        
    
                $officeConverterTool = new OfficeConverterTool();
                $today_filepath  =  $officeConverterTool->today_filepath();
                $r_filename = $today_filepath.DS.$class.$term.time().'.xlsx';
                $outfilename =''; //不設定新名稱則沿用原來擋案名稱
                $templateProcessor->saveAs($r_filename); //先把檔案存起來
                $new_file = $officeConverterTool->Converter2OtherFileType($r_filename,$outfilename,'pdf');
                return $new_file;
    
    
            
    }


}
