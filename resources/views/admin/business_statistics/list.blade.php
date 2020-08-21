@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'business_statistics';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">公務統計報表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">公務統計報表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>公務統計報表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form id="exportFile" method="post" id="search_form" action="/admin/business_statistics/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <?php $sdatetw =''; $edatetw =''; ?>
                                        <div class="form-group row align-items-center">
                                            <!--  datepicker -->
                                            <label class="col-1">訓練期間<span class="text-danger"></span></label>
                                            <input type="text" class="form-control col-sm-2 mr=1" value="{{$sdatetw}}" id="sdatetw" name="sdatetw"  id="sdatetw" placeholder="請選擇要查詢的日期" readonly min="1" autocomplete="off" required>
                                            <label >～<span class="text-danger"></span></label>
                                            <input type="text" class="form-control col-sm-2" value="{{$edatetw}}" id="edatetw" name="edatetw"  id="edatetw" placeholder="請選擇要查詢的日期" readonly min="1" autocomplete="off" required>
                                        </div>


                                        <div class="form-group row align-items-center justify-content-left">

                                            <label class="col-1"><span ></span></label>
                                                <input class="mr-1 ml-3" type="checkbox" name="checkrank" id="rank" value="1">學員官等

                                                <input class="mr-1 ml-3" type="checkbox" name="checkage" id="age" value="1">年齡

                                                <input class="mr-1 ml-3" type="checkbox" name="checkedu" id="sex_edu" value="1">性別及學歷

                                                <input class="mr-1 ml-3" type="checkbox" name="checknum" id="nums" value="1">開班數及受訓人次

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