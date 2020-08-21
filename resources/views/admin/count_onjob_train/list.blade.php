@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'count_onjob_train';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">在職訓練人數統計表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">在職訓練人數統計表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>在職訓練人數統計表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                <form id="exportFile" method="post" id="search_form" action="/admin/count_onjob_train/export" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                    <div class="form-group row justify-content-left align-items-center">
                                        <label class="col-2 text-left"><span>輸入列印條件</span></label>    
                                    </div>

                                    <div class="form-group row align-items-center">
                                        <!--  datepicker -->
                                        <label class="mb-2 mr-2">起始日期</label>
                                            <input type="text" class="form-control col-1 mb-2 mr-1" id="syear" name="syear" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3">
                                            <p style="display: inline">年</p>
                                            <input type="text" class="form-control col-1 mb-2 mr-1 ml-1" id="smonth" name="smonth" min="1"
                                                value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2">
                                            <p style="display: inline">月</p>
                                    </div>
                                    <div class="form-group row align-items-center">    
                                        <label class="mb-2 mr-2 text-right">結束日期</label>
                                            <input type="text" class="form-control col-1 mb-2 mr-1" id="eyear" name="eyear" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3">
                                            <p style="display: inline">年</p>
                                            <input type="text" class="form-control col-1 mb-2 mr-1 ml-1" id="emonth" name="emonth" min="1"
                                                value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2">
                                            <p style="display: inline">月</p>
                                       
                                    </div>
                                    <div class="form-group row justify-content-left align-items-center">
                                        <label class="col-2 text-left"><span>下列選項請點選其一</span></label>    
                                    </div>
                                    <div class="form-group row justify-content-left align-items-center">
                                        <input class="pt-2 float-right" type="radio" id="outputtype1" name="outputtype" value="1" checked>
                                        <label class="col-2">訓練班別<span class="text-danger"></span></label>    
                                        <input class=" pt-2 float-right" type="radio" id="outputtype2" name="outputtype" value="2" >
                                        <label class="col-2">游於藝講堂<span class="text-danger"></span></label>    
                                        <input class="pt-2 float-right" type="radio" id="outputtype3" name="outputtype" value="3" >
                                        <label class="col-6">學歷性別及年齡性別<span class="text-danger"></span></label>    
                                    </div>
                                    <div class="form-group row align-items-center">
                                    <label >訓練性質</label>
                                            <div class="col-2">
                                                <select class="select2 form-control select2-single input-max" id="traintype" name="training">
                                                    <option value="0">請選擇訓練性質</option>
                                                    <option value="1">1 中高階公務人員訓練</option>
                                                    <option value="2">2 人事人員專業訓練</option>
                                                    <option value="3">3 一般公務人員訓練</option>
                                                </select>
                                            </div>   
                                        </div>
                                    <div class="form-group row  align-items-center">
                                        <label >班別性質</label>
                                        <div class="col-2">
                                            <select class="select2 form-control select2-single input-max" id="classtype" name="classtype">
                                                <option value="0">請選擇班別性質</option>
                                                    @foreach ($class as $class)
                                                        <option value="{{$class->value}}">{{$class->value}} {{$class->text}}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row justify-content-left align-items-center">
                                        <label class="col-2 text-left"><span>下列選項請點選其一</span></label>    
                                    </div>
                                    <div class="form-group row justify-content-left align-items-center">
                                        <input class="pt-2 float-right" type="radio" id="statics1" name="statics" value="1" checked>
                                        <label class="col-2">依班期個別統計<span class="text-danger"></span></label>    
                                        <input class=" pt-2 float-right" type="radio" id="statics2" name="statics" value="2" >
                                        <label class="col-2">依班號合併統計<span class="text-danger"></span></label>    
                                    </div>
                                    <div id="dvdoctype" class="form-group row  align-items-center">
                                        <label class="col-2 text-right">請選檔案格式：</label>
                                        <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                        <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                    </div>
                                    <div class="form-group row justify-content-center">
                                        <div class="col-10">
                                            <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                            <label id="download"></label>
                                        </div>
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

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<!--  src of datepicker  --> 
<script src="/backend/assets/js/bootstrap-datepicker.js"></script>
<script>
     if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

</script>
@endsection