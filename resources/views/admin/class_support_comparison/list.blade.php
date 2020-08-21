@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'class_support_comparison';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">各班次行政支援成效比較表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">各班次行政支援成效比較表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <form id="exportFile" method="post" id="search_form" action="/admin/class_support_comparison/export" enctype="multipart/form-data">
                        {{ csrf_field() }}
                    <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-list pr-2"></i>各班次行政支援成效比較表</h3>
                            </div>
                    </div>
                    <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"></i>輸入列印條件</h4>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-11">
                                            
                                            <!--  variable declare  --> 
                                            <?php $sdatetw =''; $edatetw =''; ?>

                                            <div class="form-group row align-items-center">
                                                <!--  datepicker -->
                                                <label class="col-2" style=" text-align: right;">訓練期間:<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control col-2" value="{{$sdatetw}}" id="sdatetw" name="sdatetw"  id="sdatetw" placeholder="請選擇要查詢的日期" readonly min="1" autocomplete="off" required>
                                                <label class="col-0">~<span class="text-danger"></span></label>
                                                <input type="text" class="form-control col-2" value="{{$edatetw}}" id="edatetw" name="edatetw"  id="edatetw" placeholder="請選擇要查詢的日期" readonly min="1" autocomplete="off" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                                <div class="row justify-content-around">
                                    <div class="col-2">
                                        <input type="radio" name="area" id="taipei" value="1">台北院區
                                    </div>
                                    <div class="col-2">
                                        <input type="radio" name="area" id="nantou" value="2">南投院區
                                    </div>
                                    <div class="col-2">
                                        <input type="radio" name="area" id="allarea" value="3" checked>全部
                                    </div>
                                </div>
                                <br>
                        </div>

                    </div>
                    <div id="dvdoctype" class="form-group row  align-items-center">
                                <label class="col-2 text-right">請選檔案格式：</label>
                                <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                            </div>
                    <div align="center">
                        <div>
                            <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                            <label id="download"></label>
                        </div>
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
<!--  src of datepicker  --> 
<script src="/backend/assets/js/bootstrap-datepicker.js"></script>
<script>
//  call datepicker
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