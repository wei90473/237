@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'site_check';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地審核處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">場地審核處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>場地審核處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">

                                                <!-- 開始日期 -->
                                                <div class="pull-left mobile-100 mr-1 mb-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">開始日期</span>
                                                        </div>
                                                        <input type="text" id="start_date" name="start_date" class="form-control" autocomplete="off" value="{{ $queryData['start_date'] }}">
                                                    </div>
                                                </div>

                                                <!-- 結束日期 -->
                                                <div class="pull-left mobile-100 mr-1 mb-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">結束日期</span>
                                                        </div>
                                                        <input type="text" id="end_date" name="end_date" class="form-control" autocomplete="off" value="{{ $queryData['end_date'] }}">
                                                    </div>
                                                </div>

                                                <!-- 下拉選單 -->
                                                <div class="pull-left mobile-100 mr-1 mb-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">下拉選單</span>
                                                        </div>
                                                        <select class="form-control select2" name="status">
                                                            <option value="1" {{ $queryData['status'] == '1'? 'selected' : '' }}>待審核</option>
                                                            <option value="2" {{ $queryData['status'] == '2'? 'selected' : '' }}>已審核</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </form>
                                    </div>

                                    <div class="float-md-right">
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="150">申請編號</th>
                                                <th>審核狀態</th>
                                                <th>申請單位</th>
                                                <th>活動名稱</th>
                                                <th class="text-center" width="70">明細</th>
                                                <th class="text-center" width="70">同意</th>
                                                <th class="text-center" width="70">退回</th>
                                                <th class="text-center" width="70">取消</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)

                                                <tr>
                                                    <td class="text-center">{{ $va->no }}</td>
                                                    <td>{{ $va->status }}</td>
                                                    <td>{{ $va->unit }}</td>
                                                    <td>{{ $va->name }}</td>

                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/site_check/{{ $va->id }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>

                                                    <!-- 同意 -->
                                                    <td class="text-center">
                                                        @if($va->prove == 'W')
                                                        <a href="/admin/site_check/{{ $va->id }}/pass" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        @endif
                                                    </td>

                                                    <!-- 退回 -->
                                                    <td class="text-center">
                                                        @if($va->prove == 'W')
                                                        <a href="/admin/site_check/{{ $va->id }}/return" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        @endif
                                                    </td>

                                                    <!-- 取消 -->
                                                    <td class="text-center">
                                                        @if($va->prove == 'Y' || $va->prove == 'R')
                                                        <a href="/admin/site_check/{{ $va->id }}/cancel" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])

                                </div>
                            </div>
                        </div>

                        <!-- 列表頁尾 -->
                        @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection