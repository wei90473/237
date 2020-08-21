@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'institution';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">機關資料</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">機關資料列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>機關資料</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            <!-- 機關代碼 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">機關代碼</span>
                                                    </div>
                                                    <input type="text" id="organ" name="organ" class="form-control" autocomplete="off" value="{{ $queryData['organ'] }}">
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
                                        <a href="/admin/institution/create">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                        </a>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                
                                                <th class="text-center" width="70">修改</th>
                                                <th>機關代碼<i class="fa fa-sort" data-toggle="sort" data-sort-field="organ"></i></th>
                                                <th>機關名稱(全銜)<i class="fa fa-sort" data-toggle="sort" data-sort-field="lname"></i></th>
                                                <th>類型<i class="fa fa-sort" data-toggle="sort" data-sort-field="type"></i></th>
                                                <th>聯絡單位</th>
                                                <th>聯絡人(一)</th>
                                                <th>電話(一)</th>
                                                <th>地址</th>
                                                <th class="text-center" width="70">刪除</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <tr>

                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/institution/{{ $va->organ }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>

                                                    <td>{{ $va->organ }}</td>
                                                    <td>{{ $va->lname }}</td>
                                                    <td>{{ config('app.institution_type.'.$va->type) }}</td>
                                                    <td>{{ $va->division }}</td>
                                                    <td>{{ $va->sponsor1 }}</td>
                                                    <td>{{ ($va->telnoa1)? '('.$va->telnoa1.')' : '' }}{{ $va->telnob1 }}{{ ($va->telnoc1)? ' #'.$va->telnoc1 : '' }}</td>
                                                    <td>{{ $va->address }}</td>

                                                    <!-- 刪除 -->
                                                    <td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/institution/{{ $va->organ }}');" data-toggle="modal" data-target="#del_modol" >
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