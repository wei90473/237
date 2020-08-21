@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'employ';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講講課酬勞查詢</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講課酬勞查詢</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講課酬勞查詢</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                        <input type="hidden" id="search" name="search" class="form-control" value="search">
                                            <!-- 開始日期 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">開始日期</span>
                                                    </div>
                                                     <input class="date form-control" value="{{$queryData['sdate']}}" type="text" id="sdate" name="sdate">
                                                </div>
                                            </div>

												<!-- 結束日期 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">結束日期</span>
                                                    </div>
                                                     <input class="date form-control" value="{{$queryData['edate']}}"  type="text" id="edate" name="edate">
                                                </div>
                                            </div>

                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>姓名</th>
                                                <th>講客酬勞合計</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)

                                                <tr>
                                                    <td>{{ $va['name'] }}</td>
                                                    <td>{{ $va['total'] }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- 分頁 -->


                                </div>
                            </div>
                        </div>

                        <!-- 列表頁尾 -->


                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
<script>
$( function() {
    $("#sdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
    });

    $("#edate").datepicker({
        format: "twymmdd",
        language: 'zh-TW'
    });
  } );
</script>
@endsection