@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'dining_table';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">用餐人數概況表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">用餐人數概況表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <form id="exportFile" method="post" id="search_form" action="/admin/dining_table/export" enctype="multipart/form-data">
                        {{ csrf_field() }}
                    <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-list pr-2"></i>用餐人數概況表</h3>
                            </div>
                    </div>

                    <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></i>輸入列印條件</h4>
                            </div>                    
                            <div class="card-body">
                                <div class="row justify-content-around">
                                    <div class="col-2">
                                        <input type="radio" name="area" id="taipei" value="1" checked>台北院區
                                    </div>
                                    <div class="col-2">
                                        <input type="radio" name="area" id="nantou" value="2">南投院區
                                    </div>
                                </div>
                            </div>

                    </div>

                    <div class="card">


                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-11">
                                            
                                            <!--  variable declare  --> 
                                            <?php $sdatetw =''; $edatetw =''; ?>

                                            <div class="form-group row align-items-center">
                                                <!--  datepicker -->
                                                <label class="col-sm-1">期間<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control col-2" value="{{$sdatetw}}" id="sdatetw" name="sdatetw"  id="sdatetw" min="1" autocomplete="off" placeholder="請選擇要查詢的日期" readonly required onchange="dateChange()">
                                                <label class="col-sm-0">～<span class="text-danger"></span></label>
                                                <input type="text" class="form-control col-2" value="{{$edatetw}}" id="edatetw" name="edatetw"  id="edatetw" min="1" autocomplete="off" placeholder="請選擇要查詢的日期" readonly required>
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
<script src="/backend/assets/js/bootstrap-datepicker.js"></script>
<script>
    function dateChange(){
        $('#edatetw').val($('#sdatetw').val());
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