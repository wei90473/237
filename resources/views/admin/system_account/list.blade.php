@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'system_account';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">系統帳號維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">系統帳號維護列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>系統帳號維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            <!-- 姓名 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">姓名</span>
                                                        <input type="text" id="keyword" name="keyword" class="form-control" autocomplete="off" value="{{ $queryData['keyword'] }}">
                                                        <input type="hidden" id="search" name="search" class="form-control" autocomplete="off" value="search">

                                                    </div>
                                                </div>

                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">E-mail</span>
                                                        <input type="text" id="email" name="email" class="form-control" autocomplete="off" value="{{ $queryData['email'] }}">

                                                    </div>
                                                 </div>
                                            </div>

                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <div class="float-left">
                                                <!-- 查詢 -->
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                                <!-- 重設條件 -->
                                                <button class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()">重設條件</button>
                                                <a href="/admin/system_account/create">
                                                    <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新增系統帳號</button>
                                                </a>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="80">編輯</th>
                                                <th>帳號</th>
                                                <th>姓名</th>
                                                <th>部門</th>
                                                <th>E-Mail</th>
                                                <th>分機</th>
                                                <th>離職與否</th>
                                                <!-- <th class="text-center" width="70">刪除</th> -->
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <!-- <td class="text-center">{{ $startNo + $loop->iteration }}</td> -->
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/system_account/{{ $va->id }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil">編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->userid }}</td>
                                                    <td>{{ $va->username }}</td>
                                                    <td>{{ $va->section }}</td>
                                                    <td>{{ $va->email }}</td>
                                                    <td>{{ $va->ext }}</td>
                                                    <td>{{ $va->dimission }}</td>

                                                    <!-- 刪除 -->
                                                    <!-- <td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/system_account/{{ $va->serno }}');" data-toggle="modal" data-target="#del_modol" >
                                                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                                                <i class="fa fa-trash text-danger"></i>
                                                            </span>
                                                        </span>
                                                    </td> -->
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

    <script language="javascript">

        function doClear(){
          document.all.keyword.value = "";
          document.all.email.value = "";

        }
    </script>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection