<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Services\User_groupService;

class ClassDistribution extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('class_distribution', $user_group_auth)){
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
        $result="";
        return view('admin/class_distribution/list',compact('result'));
    }


    public function export(Request $request)
    {
        $sdatetw=explode("-",$request->input('sdatetw'));
        $edatetw=explode("-",$request->input('edatetw'));
        $sdate="";
        $edate="";

        $year=str_pad($request->input('selectYear'), 3, "0", STR_PAD_LEFT);
        $month=str_pad($request->input('selectMonth'), 2, "0", STR_PAD_LEFT);

        $cons=$request->input('cons');

        $caption='';

        if($cons=='1'){
            $caption.='行政院人事行政總處公務人力發展學院'. $year.'年'.$month.'月班次分配表';
            $sdate=$year.$month.'01';
            $edate=$year.$month.'31';

        }else{
            $sdate=$sdatetw[0].$sdatetw[1]. $sdatetw[2];
            $edate=$edatetw[0].$edatetw[1]. $edatetw[2];
            $caption.='行政院人事行政總處公務人力發展學院'. $sdatetw[0].'年'.$sdatetw[1].'月'. $sdatetw[2].'日~'
                       .$edatetw[0].'年'.$edatetw[1]. '月'.$edatetw[2].'日班次分配表';
        }


        $sql="select
        CONCAT(B.class,B.term) as classcode,
        if(ifnull(B.client,'')='','行政院人事行政總處公務人力發展學院',B.client) as client,
        CONCAT(A.name,'第',CAST(CAST(B.term as int) as char),'期') as classnameterm,
        CONCAT('起',cast(cast(substr(B.sdate,1,3) as int)as char),'.',substr(B.sdate,4,2),'.',substr(B.sdate,6,2),
        '迄',cast(cast(substr(B.edate,1,3) as int)as char),'.',substr(B.edate,4,2),'.',substr(B.edate,6,2)) as date,
        CONCAT(A.period,
        case when A.kind='1' then '週'
             when A.kind='2' then '天'
             when A.kind='3' then '小時'
        end
        ) as period,
        A.day,
        B.quota,
        C.username,
        case when B.site between 901 and 906 then '外地班' else '' end as outside

        from  t01tb A
        left join t04tb B on A.class=B.class
        left join m09tb C on B.sponsor=C.userid
        where (B.sdate between ".$sdate." and ".$edate.") or (B.edate between ".$sdate." and ".$edate.")
        order by A.class,B.term";

        $data=json_decode(json_encode(DB::select($sql)), true);

        if($data==[]){
            $result="查無資料，請重新設定搜尋條件。";
            return view('admin/class_distribution/list',compact('result'));
        }

        $datakey=array_keys((array)$data[0]);

        // 檔案名稱
        $fileName = 'D6';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);

        $objSheet = $objPHPExcel->getsheet(0);
        $objSheet->setCellValue('A1', $caption);
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        for($i=0;$i<sizeof($data);$i++){
            for($j=0;$j<sizeof($datakey);$j++){
                $pos=$j;
                if ($j==sizeof($datakey)-1)
                    $pos++;

                $objSheet->setCellValue($this->getNameFromNumber($pos+1).($i+4),$data[$i][$datakey[$j]]);

            }
        }

        //apply borders
        $objSheet->getStyle('A4:H'.(sizeof($data)+3))->applyFromArray($styleArray);


        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"班次分配表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
