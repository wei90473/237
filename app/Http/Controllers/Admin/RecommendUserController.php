<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\RecommendUserService;
use App\Services\User_groupService;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\M21tb;
use App\Models\M13tb;
use DB ;

class RecommendUserController extends Controller
{
    /**
     * RecommendUserController constructor.
     * @param RecommendUserService $recommendUserService
     */
    public function __construct(RecommendUserService $recommendUserService, User_groupService $user_groupService)
    {
        $this->recommendUserService = $recommendUserService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('recommend_user', $user_group_auth)){
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
        // 身分證號
        $queryData['userid'] = $request->get('userid');
        // 薦送機關代碼
        $queryData['enrollorg'] = $request->get('enrollorg');
        // 薦送機關名稱
        $queryData['enrollname'] = $request->get('enrollname');
        // EMAIL
        $queryData['email'] = $request->get('email');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        if(empty($request->all())) {
            return view('admin/recommend_user/list', compact('queryData'));
        }
        // 取得列表資料
        $data = $this->recommendUserService->getRecommendUserList($queryData);

        return view('admin/recommend_user/list', compact('data', 'queryData'));
    }

    /**
     * 設定聯絡窗口
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function active(Request $request){
        $queryData['enrollorg'] = $request->input('enrollorg');
        if(empty($request->all())) {
            return view('admin/recommend_user/contact_window', compact('queryData'));
        }
        // 取得列表資料
        $data = $this->recommendUserService->getRecommendUserList($queryData);

        return view('admin/recommend_user/contact_window', compact('data', 'queryData'));

    }

    /**
     * 新增處理**
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 取得POST資料
        $data['userid'] = $request->input('userid');
        $data['enrollorg'] = $request->input('enrollorg');
        $data['username'] = $request->input('username');
        $data['section'] = $request->input('section');
        $data['telnoa'] = $request->input('telnoa');
        $data['telnob'] = $request->input('telnob');
        $data['telnoc'] = $request->input('telnoc');
        $data['email'] = $request->input('email');
        $data['keyman'] = $request->input('keyman');

        // 身分證字號跟薦送機關為一組,不能重複
        if (M21tb::where('enrollorg', $data['enrollorg'])->where('userid', $data['userid'])->exists()) {

            return back()->withInput()->with('result', '0')->with('message', '此薦送機關已有該個人帳號！');
        }

        // 新增資料
        $result = M21tb::create($data);

        return redirect('/admin/recommend_user/'.$result->enrollorg.'/'.$result->userid)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $enrollorg
     */
    public function show($enrollorg, $userid)
    {
        return $this->edit($enrollorg, $userid);
    }

    /**
     * 編輯頁
     *
     * @param $enrollorg
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($enrollorg, $userid){
        $queryData['enrollorg'] =$enrollorg;
        $queryData['userid'] =$userid;
        $data = $this->recommendUserService->getRecommendUserList($queryData);

        $data = $data[0];
        if(substr($data['userid'], 1,1)=='1'){
            $data->sex =  'M';
        }elseif(substr($data['userid'], 1,1)=='2'){
            $data->sex =  'F';
        }else{
            $data->sex =  '';
        }
        if ( ! $data) {

            return view('admin/errors/error');
        }
        return view('admin/recommend_user/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $enrollorg, $userid)
    {
        // 取得POST資料
        $data = $request->all();

        if ( ! $data) {
            return view('admin/errors/error');
        }
        DB::beginTransaction();
        try{
            //更新
            if($userid=='active'){
                unset($data['_method'],$data['_token']);
                if(count($data)>1) return back()->with('result', '0')->with('message', '錯誤!該薦送機關聯絡窗口只能有一筆!');

                $olddata = M21tb::select('enrollorg','userid','keyman')->where('enrollorg', $enrollorg)->get()->toarray();
                foreach ($olddata as $key => $value) {
                    if(isset($data['keyman'.$value['userid']]) ) {
                        M21tb::where('enrollorg', $enrollorg)->where('userid', $value['userid'])->update(array('keyman'=>'Y'));
                    }else{
                        M21tb::where('enrollorg', $enrollorg)->where('userid', $value['userid'])->update(array('keyman'=>'N'));
                    }
                }
            }else{
                M21tb::where('enrollorg', $enrollorg)->where('userid', $userid)->update(array('section' =>$data['section'],
                                                                                          'birthday'=>$data['birthday'],
                                                                                          'email'   =>$data['email'],
                                                                                          'username'=>$data['username'] ));
            }
            DB::commit();
            return back()->with('result', '1')->with('message', '儲存成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '更新失敗，請稍後再試!');
        }
    }

    /**
     * 刪除處理**
     *
     * @param $enrollorg
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($enrollorg, $userid)
    {
        if ($enrollorg && $userid) {

            M21tb::where('enrollorg', $enrollorg)->where('userid', $userid)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
    // 列印聯絡名冊
    public function print(){
        $data[2] =['調訓機關承辦人聯絡人名冊'] ;
        $data[0]=['機關名稱','聯絡人','電話','傳真','電子信箱'];
        $data[1] = M13tb::select(DB::raw("lname, RTRIM(sponsor1) AS sponsor1,CONCAT(RTRIM(CASE telnoa1 WHEN '' THEN telnoa1 ELSE CONCAT('(',RTRIM(telnoa1),')') END),RTRIM(CASE WHEN LENGTH(RTRIM(telnob1)) = 8 THEN CONCAT(SUBSTRING(telnob1,1,4),'-',SUBSTRING(telnob1,5,4)) ELSE telnob1 END),RTRIM(CASE telnoc1 WHEN '' THEN telnoc1 ELSE CONCAT('轉',telnoc1) END)) AS telno1, CONCAT(RTRIM(CASE faxnoa1 WHEN '' THEN faxnoa1 ELSE CONCAT('(',RTRIM(faxnoa1),')') END),RTRIM(CASE WHEN LENGTH(RTRIM(faxnob1)) = 8 THEN CONCAT(SUBSTRING(faxnob1,1,4),'-',SUBSTRING(faxnob1,5,4)) ELSE faxnob1 END))AS faxno1,RTRIM(email) AS email "))->where('kind', 'Y')->where('expdate','')->get()->toarray();
        Excel::create('調訓機關承辦人聯絡人名冊', function ($excel) use ($data) {//第一參數是檔案名稱
            $excel->sheet('data', function ($sheet) use ($data) {//第一個參數是sheet名稱
                $sheet->row(1,$data[2]);//插入excel欄位
                $ascii=65;
                for($a=0;$a<count($data[0]);$a++){
                    $sheet->setWidth(chr($ascii),15);
                    $ascii=$ascii+1;
                }
                $sheet->row(3,$data[0]);
                $row=4;
                //插入資料
                for($b=0;$b<count($data[1]);$b++){
                    $sheet->cell('A'.$row, function($cell) use($data,$b) {
                        $num=$data[1][$b]['lname'];
                        $cell->setValue($num);
                    });
                    $sheet->cell('B'.$row, function($cell) use($data,$b) {
                        $num=$data[1][$b]['sponsor1'];
                        $cell->setValue($num);
                    });
                    $sheet->cell('C'.$row, function($cell) use($data,$b) {
                        $num=$data[1][$b]['telno1'];
                        $cell->setValue($num);
                    });
                    $sheet->cell('D'.$row, function($cell) use($data,$b) {
                        $num=$data[1][$b]['faxno1'];
                        $cell->setValue($num);
                    });
                    $sheet->cell('E'.$row, function($cell) use($data,$b) {
                        $num=$data[1][$b]['email'];
                        $cell->setValue($num);
                    });
                    $row++;
                }
            });
        })->export('xls');
    }
}
