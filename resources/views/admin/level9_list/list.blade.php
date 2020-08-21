@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'level9_list';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">薦任9職等主管名單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">薦任9職等主管名單</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>薦任9職等主管名單</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                <form method="post" action="/admin/level9_list/export" id="search_form">
                                        {{ csrf_field() }}
                                                <!--  variable declare  --> 
                                                <?php $sdatetw =''; $edatetw =''; ?>
                                                    <div class="form-group row align-items-center">
                                                        <!--  datepicker -->
                                                        <label class="col-2">起始日期<span class="text-danger"></span></label>
                                                        <input type="text" class="form-control col-sm-2 mr=1" value="{{$sdatetw}}" id="sdatetw" name="sdatetw"  id="sdatetw" min="1" readonly autocomplete="off">
                                                        <label class="col-2">結束日期<span class="text-danger"></span></label>
                                                        <input type="text" class="form-control col-sm-2 " value="{{$edatetw}}" id="edatetw" name="edatetw"  id="edatetw" min="1" readonly autocomplete="off">
                                                    </div>
                                                
                                                <div id="dvdoctype" class="form-group row  align-items-center">
                                                    <label class="col-3 text-right">請選檔案格式：</label>
                                                    <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                                    <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
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
            format: "twy/mm/dd",
        });
        $('#edatetw').datepicker({
            format: "twy/mm/dd",
        });
    } );

    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

</script>
@endsection