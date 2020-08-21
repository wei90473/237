@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'recommend';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">薦送機關維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">薦送機關維護列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>薦送機關維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            <!-- 薦送機關代碼 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">薦送機關代碼</span>
                                                    </div>
                                                    <input type="text" id="enrollorg" name="enrollorg" class="form-control" autocomplete="off" value="{{ $queryData['enrollorg'] }}">
                                                </div>
                                            </div>

                                            <!-- 主管機關代號 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">主管機關代號</span>
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
                                        <a href="/admin/recommend/create">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                        </a>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">修改</th>
                                                <th class="text-center">使用狀態</th>
                                                <th>薦送機關代碼</th>
                                                <th>薦送機關名稱</th>
                                                <th>主管機關名稱</th>
                                                <th class="text-center" width="70">刪除</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/recommend/{{ $va->enrollorg }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>                                                    
                                                    <td class="text-center"><button class="btn {{ ($va->status == 'Y') ? 'btn-primary' : 'btn-danger' }}" onclick="changeStatus('{{ $va->enrollorg }}', '{{ ($va->status == 'Y') ? 'N' : 'Y' }}')">{{ ($va->status == 'Y')? '啟用' : '停用' }}</button></td>
                                                    <td>{{ $va->enrollorg }}</td>
                                                    <td>{{ $va->enrollname }}</td>
                                                    <td>{{ $va->lname }}</td>
                                                    <!-- 刪除 -->
                                                    <td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/recommend/{{ $va->enrollorg }}');" data-toggle="modal" data-target="#del_modol" >
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
    {{ Form::open(['method' => 'put', 'url' => '/admin/recommend/', 'id' => 'changeStatusForm']) }}
        {{ Form::hidden('status', null) }}
    {{ Form::close() }}
@endsection


@section('js')
    <script type="text/javascript">
    function changeStatus(enrollorg, status){
        if (confirm('確定要' + ((status == 'Y') ? ' 啟用 ' : ' 停用 ') + enrollorg + ' 啟用狀態嗎 ?')){
            $("input[name=status]").val(status);
            $("#changeStatusForm").attr('action', '/admin/recommend/' + enrollorg);
            $("#changeStatusForm").submit();
        }
    }        
    </script>
@endsection