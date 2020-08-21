@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'user_group';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">權限群組維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">權限群組維護列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>權限群組維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">

                                            <div class="float-left">

                                                <a href="/admin/user_group/create">
                                                    <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新增權限群組</button>
                                                </a>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="80">編輯</th>
                                                <th>權限群組</th>
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
                                                        <a href="/admin/user_group/{{ $va->id }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil">編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->name }}</td>

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