@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'class_distribution';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">班次分配表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">班次分配表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>班次分配表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                <div class="card-body">
                            <div class="card-header">
                                <h4 class="card-title"></i>下列兩區條件請選其一</h4>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <form method="post" action="/admin/class_distribution/export" id="search_form">
                                    {{ csrf_field() }}

                                        <div class="card">
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <input class="col-sm-1 pt-2 float-right" type="radio" id="cons1"" name="cons" value="1" checked>
                                                    <label class="col-sm-2">第一區條件</label>    
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right">年度</label>
                                                    <div class="col-sm-2">
                                                        <select class="select2 form-control select2-single input-max" name="selectYear">
                                                            @for ($i = (int)date("Y")-1910 ; $i>=90; $i--)
                                                            <option value={{$i}}>{{$i}}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <label class="col-sm-1 text-right">月份</label>
                                                    <div class="col-sm-2">
                                                        <select class="select2 form-control select2-single input-max" name="selectMonth">
                                                            @for ($i = 1; $i<=12; $i++)
                                                                <option value={{$i}}>{{$i}}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-body">

                                                <div class="form-group row">
                                                    <input class="col-sm-1 pt-2 float-right" type="radio" id="cons2" name="cons" value="2">
                                                    <label class="col-sm-2">第二區條件</label>    
                                                </div>

                                                <!--  variable declare  --> 
                                                <?php $sdatetw =''; $edatetw =''; ?>
                                                    <div class="form-group row align-items-center">
                                                        <!--  datepicker -->
                                                        <label class="col-2">上課期間起始日期<span class="text-danger"></span></label>
                                                        <input type="text" class="form-control col-2 mr=1" value="{{$sdatetw}}" id="sdatetw" name="sdatetw"  id="sdatetw" min="1" readonly autocomplete="off">
                                                        <label class="col-2">結束日期<span class="text-danger"></span></label>
                                                        <input type="text" class="form-control col-2 " value="{{$edatetw}}" id="edatetw" name="edatetw"  id="edatetw" min="1" readonly autocomplete="off">
                                                    </div>
                                                </div>
                                        </div>
                                        <div id="dvdoctype" class="form-group row  align-items-center">
                                            <label class="col-2 text-right">請選檔案格式：</label>
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