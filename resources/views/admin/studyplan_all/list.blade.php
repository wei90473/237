@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'studyplan_all';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">研習實施計畫總表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">研習實施計畫總表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>研習實施計畫總表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                        <form method="post" id="search_form" action="/admin/studyplan_all/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                            <!-- 年度 -->
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label text-md-right pt-2">年度<span class="text-danger">*</span></label>
                                                <div class="col-sm-10">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <!-- 輸入欄位 -->
                                                        {{-- <input type="text" class="form-control input-max" id="yerly" name="yerly" min="1" placeholder="請輸入年度" 
                                                        value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" required> --}}
                                                        <select type="text" id="yerly" name="yerly" class="browser-default custom-select" style="min-width: 80px; flex:0 1 auto">
                                                            @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                                                                <option value="{{$i}}"
                                                                {{ (  (date("Y") - 1911) == $i ) ? 'selected' : '' }}
                                                                >{{$i}}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
<!-- 
                                            <div class="form-group row align-items-center">
                                                <label class="col-2 pl-2 pr-2 text-right" > 院區 </label>
                                                <label class="pl-2 pr-2" ><input type="radio" name="area" id="taipei" value="1" checked >台北院區</label>
                                                <label class="pl-2 pr-2" ><input type="radio" name="area" id="nantou" value="2">南投院區</label>
                                                <label class="pl-2 pr-2" ><input type="radio" name="area" id="allarea" value="3" >全部</label>
                                            </div> -->
<!-- 
                                            <div id="dvdoctype" class="form-group row  align-items-center">
                                                    <label class="col-2 text-right">請選檔案格式：</label>
                                                    <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                                    <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                            </div> -->
                                            <div align="center">
                                                <button type="submit" class="btn mobile-100"><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                                {{-- <label id="download"></label> --}}
                                                <button type="button" class="btn mobile-100" data-toggle="modal" data-target="#upload_yearplan_file"><i class="fa fa-plus fa-lg pr-1"></i>年度上傳檔案</button>
                                            </div>
                                        </form>


                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

   
<!--  匯入 -->
<div class="modal fade" id="upload_yearplan_file" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        {!! Form::open(['method' => 'POST', 'url' => '/admin/studyplan_all/import', 'enctype' => "multipart/form-data"]) !!}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">年度研習實施計畫總表上傳</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body pt-4 text-center">
                    <div class="card-header"><h3 class="card-title">年度研習實施計畫總表上傳</h3></div>
                

                    <label >上傳年度：</label>
                    <select type="text" id="yerly" name="yerly" class="browser-default custom-select" style="min-width: 80px; flex:0 1 auto">
                        @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                            <option value="{{$i}}"
                            {{ (  (date("Y") - 1911) == $i ) ? 'selected' : '' }}
                            >{{$i}}</option>
                        @endfor
                    </select>
                    <label >上傳檔案：</label>
                    <input type="file" class="form-control" name="import_file">
                    <br/>
                </div>    
            </div>
            <div>
                <div>
                說明：<br>
                請選擇年度，系統將自動以本次上傳之檔案覆蓋該年度研習實施計畫總表
                </div>
            </div>                                                  
            <div class="modal-footer">
                <button type="submit" class="btn btn-success ml-auto">上傳</button>
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">取消</button>
            </div>                                              
        </div>
        {!! Form::close() !!}
    </div>
</div>

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection