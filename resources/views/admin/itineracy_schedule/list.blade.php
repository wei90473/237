@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'itineracy_schedule';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">巡迴研習</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">實施日程表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>實施日程表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="80">功能</th>
                                                <th>年度</th>
                                                <th>期別</th>
                                                <th>巡迴計畫名稱</th>
                                                <th>彙總表列印</th>
                                                <th>空白日程表列印</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($data as $va)
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/itineracy_schedule/edit/{{ $va->yerly.$va->term }}">
                                                            <i class="fa fa-pencil">編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->yerly }}</td>
                                                    <td>{{ $va->term }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td><a href="/admin/itineracy_schedule/print/A{{ $va->yerly.$va->term }}">
                                                            <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">彙總表列印</button>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="/admin/itineracy_schedule/print/B{{ $va->yerly.$va->term }}">
                                                            <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">空白日程表列印</button>
                                                        </a>
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
    

@endsection
