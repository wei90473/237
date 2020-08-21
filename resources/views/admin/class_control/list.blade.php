@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'class_control';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">控管辦班處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">控管辦班處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>控管辦班處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">

                                            <!-- 類別 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">類別</span>
                                                    </div>
                                                    <select class="form-control select2" id="type" name="type">
                                                        <option value="1" {{ $queryData['type'] == 1? 'selected' : '' }}>下次需求凍結</option>
                                                        <option value="2" {{ $queryData['type'] == 2? 'selected' : '' }}>下次確認凍結</option>
                                                        <option value="3" {{ $queryData['type'] == 3? 'selected' : '' }}>上次需求凍結</option>
                                                        <option value="4" {{ $queryData['type'] == 4? 'selected' : '' }}>上次確認凍結</option>
                                                    </select>

                                                </div>
                                            </div>

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </form>
                                    </div>

                                    <div class="float-md-right">
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



                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection