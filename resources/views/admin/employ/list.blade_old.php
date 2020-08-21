@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'employ';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座聘任處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座聘任處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座聘任處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            <!-- 課程名稱 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">課程名稱</span>
                                                    </div>
													<?php $list = $base->getDBList('T01tb', ['class', 'name']);?>
                                                     <select class="form-control select2" name="keyword" id="keyword">
													    <option value="empty"></option>
                                                        <option value="全部">全部</option>
                                                        @foreach($list as $key => $va)
                                                            <option value="{{ $va->class }}" {{ $queryData['keyword'] == $va->class? 'selected' : '' }}>{{ $va->class }}{{ $va->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 期別 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">期別</span>
                                                    </div>
                                                    <input type="text" id="term" name="term" class="form-control" autocomplete="off" value="{{ $queryData['term'] }}">
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

                                    <div class="float-md-right">
                                        <!-- 新增 -->
                                        <a href="/admin/employ/create">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                        </a>
									  <!-- 講課酬勞查詢 -->
                                        <a href="/admin/employ/payroll">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>講課酬勞查詢</button>
                                        </a>
                                       
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">編號</th>
                                                <th>課程名稱</th>
                                                <th>期別</th>
                                                <th>姓名</th>
                                                <th>類型</th>
                                                <th>講客酬勞合計</th>
                                                <th>交通費合計</th>
                                                <th>扣繳稅額合計</th>
                                                <th>實付總計</th>
                                                <th class="text-center" width="70">修改</th>
                                                <th class="text-center" width="70">刪除</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <td class="text-center">{{ $startNo + $loop->iteration }}</td>
                                                    <td>{{ $va->class }} {{ $va->class_name }}</td>
                                                    <td>{{ $va->term }}</td>
                                                    <td>{{ $va->cname }}{{ $va->ename }}</td>
                                                    <td>{{ config('app.employ_type.'.$va->type) }}</td>
                                                    <td>{{ $va->teachtot }}</td>
                                                    <td>{{ $va->tratot }}</td>
                                                    <td>{{ $va->deductamt }}</td>
                                                    <td>{{ $va->totalpay }}</td>

                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/employ/{{ $va->id }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>

                                                    <!-- 刪除 -->
                                                    <td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/employ/{{ $va->id }}');" data-toggle="modal" data-target="#del_modol" >
                                                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                                                <i class="fa fa-trash text-danger"></i>
                                                            </span>
                                                        </span>
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