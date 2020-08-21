@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'count_signin';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">報到人數統計表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">報到人數統計表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>報到人數統計表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                <form id="exportFile" method="post" id="search_form" action="/admin/count_signin/export" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                    <div class="form-group row justify-content-left align-items-center">
                                        <label class="col-sm-2 text-left"><span>輸入列印條件</span></label>    
                                    </div>

                                    <?php $sdatetw =''; $edatetw =''; ?>
                                    <div class="form-group row align-items-center">
                                        <!--  datepicker -->
                                        <label class="ml-3 mr-3">起始日期</label>
                                        <input type="text" class="form-control col-sm-2" value="{{$sdatetw}}" id="sdatetw" name="sdatetw"  id="sdatetw" min="1" autocomplete="off" placeholder="請選擇要查詢的日期" readonly>
                                        <label class="ml-2 mr-2">~</label>
                                        <label class="mr-3">結束日期</label>
                                        <input type="text" class="form-control col-sm-2" value="{{$edatetw}}" id="edatetw" name="edatetw"  id="edatetw" min="1" autocomplete="off" placeholder="請選擇要查詢的日期" readonly>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">       
                                                <div class="form-group row  align-items-center">
                                                    <input class="float-right" type="radio" id="outputtype1" name="outputtype" value="1" checked>
                                                    <label class="col-1">訓練班別</label>  
                                                    <label class="mb-2">班別性質</label>
                                                    <div class="col-2 mb-3">
                                                        <select class="select2 form-control select2-single input-max" id="classtype" name="classtype">
                                                            <option value="0">請選擇班別性質</option>
                                                                @foreach ($class as $class)
                                                                    <option value="{{$class->value}}">{{$class->text}}</option>
                                                                @endforeach
                                                    </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row align-items-center">
                                                    <input class="float-right" type="radio" id="outputtype2" name="outputtype" value="2" >
                                                    <label class="col-2">游於藝講堂</label>    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="dvdoctype" class="form-group row  align-items-center">
                                        <label class="col-2 text-right">請選檔案格式：</label>
                                        <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                        <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                    </div>
                                    <div class="form-group row justify-content-center">
                                        <div class="col-6">
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

$( function() {
    
    $('#sdatetw').datepicker({
        format: "twy/mm/dd",
    });
    $('#edatetw').datepicker({
        format: "twy/mm/dd",
    });
} );


</script>
@endsection