@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'class_status';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">開辦班次概況表(含住宿)</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">開辦班次概況表(含住宿)</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>開辦班次概況表(含住宿)</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                <form method="post" action="/admin/class_status/export" id="search_form">
                                        {{ csrf_field() }}
                                                <!--  variable declare  -->
                                                <?php $sdatetw =''; $edatetw =''; ?>
                                                    <div class="form-group row align-items-center">
                                                        <!--  datepicker -->
                                                        <label class="col-1">開訓日期<span class="text-danger"></span></label>
                                                        <input type="text" class="form-control col-2 mr=1" value="{{$queryData['sdatetw']}}" id="sdatetw" name="sdatetw"  id="sdatetw" min="1" readonly autocomplete="off">
                                                        <label class="col-1">結束日期<span class="text-danger"></span></label>
                                                        <input type="text" class="form-control col-2 " value="{{$queryData['edatetw']}}" id="edatetw" name="edatetw"  id="edatetw" min="1" readonly autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row col-6 align-items-center justify-content-center">
                                            <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                            <label id="download"></label>
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
            format: "twy-mm-dd",
        });
        $('#edatetw').datepicker({
            format: "twy-mm-dd",
        });
    } );

    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

</script>
@endsection