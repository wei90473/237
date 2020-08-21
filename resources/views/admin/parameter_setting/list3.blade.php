@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'parameter_setting';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座服務參數維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座服務參數維護列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座服務參數維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <ul class="nav nav-tabs">
                                        <li class="nav-item"><a href="/admin/parameter_setting_1"  class="nav-link">計程車呼號維護</a></li>
                                        <li class="nav-item"><a href="/admin/parameter_setting_2" class="nav-link">講座寢室維護</a></li>
                                        <li class="nav-item"><a href="#" class="nav-link active">接送地點車資維護</a></li>
                                    </ul>

                                     <br>

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="min-width: 1000px;">
                                        <form method="get" id="search_form">
                                        <input type="hidden" id="search" name="search" class="form-control" value="search">

                                            <div class="float-md mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">縣市</span>
                                                        </div>
                                                        <select type="text" id="county" name="county" class="browser-default custom-select"  value="{{ $queryData['county'] }}" style="min-width: 120px; flex:0 1 auto">
                                                                @foreach(config('app.county') as $key => $va)
                                                                    <option value="{{ $key }}" {{ old('county', (isset($queryData['county']))? $queryData['county'] : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                                                @endforeach
                                                        </select>
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
                                                <a href="/admin/parameter_setting_3/create">
                                                    <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新增接送地點</button>
                                                </a>

                                            </div>
                                        </form>
                                    </div>


                                    <div class="table-responsive">
                                        <table id="data_table" class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">編輯</th>
                                                <th>縣市</th>
                                                <th>接送地點</th>
                                                <th>契約車資</th>

                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/parameter_setting_3/{{ $va->id }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                    <td>{{ config('app.county.'.$va->county) }}</td>
                                                    <td>{{ $va->area }}</td>
                                                    <td>{{ $va->fare }}</td>

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