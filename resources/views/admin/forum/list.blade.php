@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'forum';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">人資發展論壇</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">人資發展論壇列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>人資發展論壇</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            <!-- 種類 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">種類</span>
                                                    </div>
                                                    <select class="form-control select2" name="type">
                                                        <option value="1" {{ $queryData['type'] == '1'? 'selected' : '' }}>主題</option>
                                                        <option value="2" {{ $queryData['type'] == '2'? 'selected' : '' }}>回應</option>
                                                    </select>
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
                                    </div>



                                    @if($queryData['type'] != '2')
                                        <!-- 主題的列表 -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                <tr>
                                                    <th class="text-center" width="70">編號</th>
                                                    <th>主題</th>
                                                    <th>日期</th>
                                                    <th>發起者</th>
                                                    <th>內容</th>
                                                    <th class="text-center" width="70">刪除</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @foreach($data as $va)
                                                    <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                    <tr>
                                                        <td class="text-center">{{ $startNo + $loop->iteration }}</td>
                                                        <td>{{ $va->subject }}</td>
                                                        <td>{{ date('Y', strtotime($va->date)) - 1911 }}/{{ date('m/d', strtotime($va->date)) }}</td>
                                                        <td>{{ $va->author }}</td>
                                                        <td>{{ $va->content }}</td>


                                                        <!-- 刪除 -->
                                                        <td class="text-center">
                                                            <span onclick="$('#del_form').attr('action', '/admin/forum/t33/{{ $va->subjectid }}');" data-toggle="modal" data-target="#del_modol" >
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

                                    @else

                                        <!-- 回應的列表 -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                <tr>
                                                    <th class="text-center" width="70">編號</th>
                                                    <th>主題</th>
                                                    <th>日期</th>
                                                    <th>發起者</th>
                                                    <th>內容</th>
                                                    <th class="text-center" width="70">刪除</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @foreach($data as $va)
                                                    <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                    <tr>
                                                        <td class="text-center">{{ $startNo + $loop->iteration }}</td>
                                                        <td>{{ $va->majoridea }}</td>
                                                        <td>{{ date('Y', strtotime($va->date)) - 1911 }}/{{ date('m/d', strtotime($va->date)) }}</td>
                                                        <td>{{ $va->author }}</td>
                                                        <td>{{ $va->content }}</td>


                                                        <!-- 刪除 -->
                                                        <td class="text-center">
                                                            <span onclick="$('#del_form').attr('action', '/admin/forum/t34/{{ $va->articleid }}');" data-toggle="modal" data-target="#del_modol" >
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

                                    @endif

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