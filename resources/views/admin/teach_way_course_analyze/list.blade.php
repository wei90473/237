﻿@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'teach_way_course_analyze';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">課程教學教法數目分析</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">課程教學教法數目分析</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>課程教學教法數目分析</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                <form method="post" action="/admin/teach_way_course_analyze/export" id="search_form">
                                        {{ csrf_field() }}
                                                <!--  variable declare  --> 
                                                <?php $sdatetw =''; $edatetw =''; ?>
                                                    <div class="form-group row align-items-center">
                                                        <!--  datepicker -->
                                                        <label class="col-1">起始日期<span class="text-danger"></span></label>
                                                        <input type="text" class="form-control col-2 mr=1" value="{{$queryData['sdate']}}" id="sdate" name="sdate"  id="sdate" min="1" readonly autocomplete="off">
                                                        <label class="col-1">結束日期<span class="text-danger"></span></label>
                                                        <input type="text" class="form-control col-2 " value="{{$queryData['edate']}}" id="edate" name="edate"  id="edate" min="1" readonly autocomplete="off">
                                                    </div>


                                                <div class="form-group row">
                                                    <?php $typeList = $base->getSystemCode('K')?>
                                                    <label class="col-1 text-right">班別性質<span class="text-danger"></span></label>
                                                    <div class="col-3">
                                                        <select class="select2 form-control select2-single input-max" name="type">
                                                            <option value="0">請選擇班別性質</option>
                                                            @foreach($typeList as $code => $va)
                                                                <option value="{{ $code }}" {{ (isset($queryData['type'])?$queryData['type']:''  ) == $code? 'selected' : '' }}>{{ $va['name'] }}</option>
                                                            @endforeach
                                                        </select>
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

    $( function() {
    
         $('#sdate').datepicker({
            format: "twymmdd",
        });
        $('#edate').datepicker({
            format: "twymmdd",
        });
    } );

</script>
@endsection