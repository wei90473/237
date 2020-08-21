@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'lecture_reception_list';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座接待一覽表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座接待一覽表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座接待一覽表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form method="post" action="/admin/lecture_reception_list/export" id="search_form">
                                            {{ csrf_field() }}
                                                    <!--  variable declare  -->
                                                    <?php $sdatetw =''; $edatetw =''; ?>
                                                        <div class="form-group row">
                                                            <!--  datepicker -->
                                                            <label class="col-1">日期<span class="text-danger"></span></label>
                                                            <div class="col-2">
                                                                <input type="text" class="form-control" value="{{$sdatetw}}" id="sdatetw" name="sdatetw"  id="sdatetw" min="1" readonly autocomplete="off">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <!--  datepicker -->
                                                            <label class="col-1">版本<span class="text-danger"></span></label>
                                                            <div class="col-2">
                                                                <div class="input-group bootstrap-touchspin number_box">
                                                                <select id="type" name="type" class="select2 form-control select2-single input-max" >
                                                                    <option value="1">一般性</option>
                                                                    <option value="2">機敏性</option>
                                                                </select>
                                                                </div>
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
        // $('#edatetw').datepicker({
        //     format: "twy-mm-dd",
        // });
    } );

    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }


</script>
@endsection