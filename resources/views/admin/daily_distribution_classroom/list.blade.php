@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'daily_distribution_classroom';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">教室場地每日分配表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">教室場地每日分配表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>教室場地每日分配表</h3>
                        </div>
                        <div class="card-body">
                            <form id="exportFile" method="post" id="search_form" action="/admin/daily_distribution_classroom/export" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <?php $sdatetw =''; $edatetw =''; ?>
                            <div class="form-group row align-items-center">
                                <label class="col-sm-1">日期<span class="text-danger"></span></label>
                                <input type="text" class="form-control input-max width" value="{{$sdatetw}}" id="sdatetw" name="sdatetw"  id="sdatetw" placeholder="請選擇要查詢的日期" readonly min="1" autocomplete="off" required>
                            </div>
                            <div id="dvdoctype" class="form-group row  align-items-center">
                                <label class="col-2 text-right">請選檔案格式：</label>
                                <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                            </div>
                            <div align="center" class="mr-5" >
                                <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                <label id="download" visible="false"></label>
                            </div>
                            </form>

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
//  call datepicker
    $( function() {
    
        $('#sdatetw').datepicker({
            format: "twy/mm/dd",
        });
    } );
</script>
@endsection