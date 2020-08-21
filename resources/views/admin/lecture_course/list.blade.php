@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'lecture_course';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座一覽表-各課程</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座一覽表-各課程</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座一覽表-各課程</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                <form id="exportFile" method="post" id="search_form" action="/admin/lecture_course/export" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                    <?php $sdatetw =''; $edatetw =''; ?>
                                    <div class="form-group row align-items-center">
                                    <!--  datepicker -->
                                        <label class="col-sm-1 text-right">起訖時間</label>
                                        <input type="text" class="form-control col-2 mr=1" value="{{$sdatetw}}" id="sdatetw" name="sdatetw" placeholder="請選擇要查詢的日期" readonly  min="1" autocomplete="off">
                                        <label class="text-center">~</label>
                                        <input type="text" class="form-control col-2" value="{{$edatetw}}" id="edatetw" name="edatetw" placeholder="請選擇要查詢的日期" readonly  min="1" autocomplete="off">
                                    </div>
                                    <div class="form-group row align-items-center">
                                        <label class="col-sm-1 text-right">課程:</label>
                                        <input type="text" class="form-control input-max width"  id="course"" name="course"  autocomplete="off">
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
        format: "twy-mm-dd",
    });
    $('#edatetw').datepicker({
        format: "twy-mm-dd",
    });
} );

</script>
@endsection