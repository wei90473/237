@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'training_organ';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">訓練機構基本資料表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">訓練機構基本資料表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <form method="post" action="/admin/training_organ/export" id="search_form">
                        {{ csrf_field() }}


                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></i>輸入列印條件</h4>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">

                                        <div class="form-group row">
                                            <input class="col-sm-1 control-label text-md-right pt-2" type="radio" id="dept" name="radiotype" value="1" checked >
                                            <label class="col-sm-2">訓練機構基本資料<span class="text-danger"></span></label>
                                        </div>  
                                        <div class="form-group row">
                                            <input class="col-sm-1 control-label text-md-right pt-2" type="radio" id="chief" name="radiotype" value="2"  >
                                            <label class="col-sm-2">首長基本資料<span class="text-danger"></span></label>
                                        </div>
                                        <div class="form-group row">
                                            <input class="col-sm-1 control-label text-md-right pt-2" type="radio" id="assistant" name="radiotype" value="3"  >
                                            <label class="col-sm-2">副首長基本資料<span class="text-danger"></span></label>
                                        </div>  
                                        <div class="form-group row">
                                            <input class="col-sm-1 control-label text-md-right pt-2" type="radio" id="contact" name="radiotype" value="4"  >
                                            <label class="col-sm-2">聯絡人基本資料<span class="text-danger"></span></label>
                                        </div>                                                                               

                                    </div>
                                </div>
                            </div>

                        </div>
                        <div id="dvdoctype" class="form-group row  align-items-center">
                            <label class="col-2 text-right">請選檔案格式：</label>
                            <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                            <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                        </div>
                        <div align="center">
                            <button type="submit" class="btn mobile-100 mb-3 mr-1"><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                        </div>
                    </form>
                 </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script>
</script>
@endsection