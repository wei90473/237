<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TeachingMaterialService;
use App\Services\User_groupService;
use App\Models\M01tb;
use App\Models\S01tb;
use App\Models\M16tb;
use App\Models\Dbteachingmaterial;
use DB;


class TeachingMaterialController extends Controller
{
    /**
     * TeachingMaterialController constructor.
     * @param TeachingMaterialService $teachingmaterialService
     */
    public function __construct(TeachingMaterialService $teachingmaterialService, User_groupService $user_groupService)
    {
        $this->teachingmaterialService = $teachingmaterialService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('teaching_material', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('teaching_material');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 班別
		if('' == $request->get('class')){
            $queryData['class'] = '';
        }else{
            $queryData['class'] = $request->get('class')=='全部'?'':$request->get('class');
        }


        // 取得關鍵字
        $queryData['keyword'] = $request->get('keyword');
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData);
        // echo "\n</pre>\n";
        // die();
        // 身分證字號
        $queryData['idno'] = $request->get('idno');
        // email
        $queryData['email'] = $request->get('email');
        // 服務機關名稱
        $queryData['dept'] = $request->get('dept');

        // 班別名稱
        $queryData['class_name'] = $request->get('class_name');
        // 班期
        $queryData['term'] = $request->get('term');
        // 專長領域
        $queryData['experience'] = $request->get('experience');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;

        $queryData['search'] = $request->get('search');

        if($queryData['search'] != 'search' ){
        	// 取得關鍵字
	        $queryData2['keyword'] = 'none';
	        // 身分證字號
	        $queryData2['idno'] = 'none';

        	$data = $this->teachingmaterialService->getTeachingMaterialList($queryData2);
        }else{
        	$data = $this->teachingmaterialService->getTeachingMaterialList($queryData);
        }

        // if(null != $request->get('class') || null != $request->get('keyword') || null != $request->get('idno')){
        //     $data = $this->teachingmaterialService->getTeachingMaterialList($queryData);
        //     return view('admin/teachingmaterial/list', compact('data', 'queryData', 'postallist'));
        // }

        return view('admin/teachingmaterial/list', compact('data', 'queryData'));
    }

    public function details(Request $request , $serno)
    {

        $M01tb_data = M01tb::select('cname')->where('serno', $serno)->first();

        $queryData['cname'] = $M01tb_data->cname;

        $queryData['keyword'] = $request->get('keyword');
        $queryData['serno'] = $serno;

        $list = $this->teachingmaterialService->getDetailList($queryData);

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r(auth()->user()->id);
        // echo "\n</pre>\n";
        // die();

        return view('admin/teachingmaterial/detail', compact('data', 'list', 'serno', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $serno = $request->get('serno');
        return view('admin/teachingmaterial/form', compact('data', 'list', 'serno'));
    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();

		if ($request->hasFile('upload1')) {
			$file = $request->file('upload1');  //獲取UploadFile例項
			if ( $file->isValid()) { //判斷檔案是否有效
				$filename = $file->getClientOriginalName(); //檔案原名稱
				$extension = $file->getClientOriginalExtension(); //副檔名
                $filename = $data['name'].'_file_'.time().'.'.$extension;    //重新命名
				$file->move(public_path()."/Uploads/teachingmaterial/", $filename);
                $data['filename'] = $filename;
			};
		};
        if ($request->hasFile('upload2')) {
            $file = $request->file('upload2');  //獲取UploadFile例項
            if ( $file->isValid()) { //判斷檔案是否有效
                $filename = $file->getClientOriginalName(); //檔案原名稱
                $extension = $file->getClientOriginalExtension(); //副檔名
                $filename = $data['name'].'_COA_'.time().'.'.$extension;    //重新命名
                $file->move(public_path()."/Uploads/teachingmaterial/", $filename);
                $data['COA'] = $filename;
            };
        };
		unset($data['upload1']);
        unset($data['upload2']);


        $data['addid'] = auth()->user()->id;

        $data['addday'] = (date('Y')-1911).'/'.date('m/d');
        if(!isset($data['online'])){
            $data['online'] = 'N';
        }
        unset($data['_method'], $data['_token']);
        // dd($data);
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($data);
        // echo "\n</pre>\n";
        // die();
        //新增
        $result = Dbteachingmaterial::create($data);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('teaching_material')){
          $nowdata = Dbteachingmaterial::where('m01serno', $data['m01serno'])->where('filename', $data['filename'])->get()->toarray();
          createModifyLog('I','Dbteachingmaterial','',$nowdata,end($sql));
        }

        return redirect('/admin/teaching_material/details/'.$data['m01serno'])->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $id
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * 編輯頁
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = Dbteachingmaterial::find($id);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/teachingmaterial/form', compact('data','list'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        $old_data = Dbteachingmaterial::where('id', $id)->first();

		if ($request->hasFile('upload1')) {
			$file = $request->file('upload1');  //獲取UploadFile例項
			if ( $file->isValid()) { //判斷檔案是否有效
				$filename = $file->getClientOriginalName(); //檔案原名稱
				$extension = $file->getClientOriginalExtension(); //副檔名
				$filename = $data['name'].'_file_'.time().'.'.$extension;    //重新命名
				$file->move(public_path()."/Uploads/teachingmaterial/", $filename);
                $data['filename'] = $filename;
                $data['addid'] = auth()->user()->id;
                $data['addday'] = (date('Y')-1911).'/'.date('m/d');
                if(!empty($old_data->filename) && $filename != $old_data->filename){
                    if(file_exists(public_path()."/Uploads/teachingmaterial/".$old_data->filename)){
                        unlink(public_path()."/Uploads/teachingmaterial/".$old_data->filename);
                    }
                }
			}
		}
        if ($request->hasFile('upload2')) {
            $file = $request->file('upload2');  //獲取UploadFile例項
            if ( $file->isValid()) { //判斷檔案是否有效
                $filename = $file->getClientOriginalName(); //檔案原名稱
                $extension = $file->getClientOriginalExtension(); //副檔名
                $filename = $data['name'].'_COA_'.time().'.'.$extension;    //重新命名
                $file->move(public_path()."/Uploads/teachingmaterial/", $filename);
                $data['COA'] = $filename;
                $data['addid'] = auth()->user()->id;
                $data['addday'] = (date('Y')-1911).'/'.date('m/d');
                if(!empty($old_data->COA) && $filename != $old_data->COA){
                    if(file_exists(public_path()."/Uploads/teachingmaterial/".$old_data->COA)){
                        unlink(public_path()."/Uploads/teachingmaterial/".$old_data->COA);
                    }
                }
            }
        }

		unset($data['upload1']);
        unset($data['upload2']);

        if(!isset($data['online'])){
            $data['online'] = 'N';
        }
        // dd($data);
        unset($data['_method'], $data['_token']);

        if(checkNeedModifyLog('teaching_material')){
            $olddata = Dbteachingmaterial::where('id', $id)->get()->toarray();
        }

        //更新
        Dbteachingmaterial::where('id', $id)->update($data);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('teaching_material')){
            $nowdata = Dbteachingmaterial::where('id', $id)->get()->toarray();
            createModifyLog('U','Dbteachingmaterial',$olddata,$nowdata,end($sql));
        }

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    /**
     * 刪除處理
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($id) {

            $data = Dbteachingmaterial::find($id);
            $getDelete = $this->teachingmaterialService->getDelete($data->idno);
            if($getDelete['delete'] == 'Y'){
                Dbteachingmaterial::find($id)->delete();
                return back()->with('result', '1')->with('message', '刪除成功!');
            }else{
                return back()->with('result', '0')->with('message', $getDelete['msg']);
            }

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    public function destroy_from($id)
    {
        if ($id) {

            $data = Dbteachingmaterial::find($id);

            if(!empty($data['filename']) && file_exists('/var/www/html/csdi/public/Uploads/teachingmaterial/'.$data['filename'])){
                unlink('/var/www/html/csdi/public/Uploads/teachingmaterial/'.$data['filename']);
            }

            if(!empty($data['COA']) && file_exists('/var/www/html/csdi/public/Uploads/teachingmaterial/'.$data['COA'])){
                unlink('/var/www/html/csdi/public/Uploads/teachingmaterial/'.$data['COA']);
            }

            if(checkNeedModifyLog('teaching_material')){
                $olddata = Dbteachingmaterial::where('id', $id)->get()->toarray();
            }

            Dbteachingmaterial::find($id)->delete();

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('teaching_material')){
                createModifyLog('D','Dbteachingmaterial',$olddata,'',end($sql));
            }

            return redirect('/admin/teaching_material/details/'.$data->m01serno)->with('result', '1')->with('message', '刪除成功!');

        } else {

            return redirect('/admin/teaching_material')->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
