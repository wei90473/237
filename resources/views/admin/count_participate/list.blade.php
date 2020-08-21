@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'count_participate';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">各機關參訓人數統計表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">各機關參訓人數統計表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>各機關參訓人數統計表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                <form id="exportFile" method="post" id="search_form" action="/admin/count_participate/export" enctype="multipart/form-data">
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
                                    <div class="form-group row  align-items-center">
                                        <label >班別性質</label>
                                        <div class="col-2">
                                            <select class="select2 form-control select2-single input-max" id="classtype" name="classtype">
                                                <option value="0">請選擇班別性質</option>
                                                    @foreach ($class as $class)
                                                        <option value="{{$class->value}}">{{$class->text}}</option>
                                                    @endforeach
                                            </select>
                                        </div>
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