@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'site_survey_old';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地問卷處理(96~100)</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">場地問卷處理(96~100)列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>場地問卷處理(96~100)</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            <!-- 年份 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">年份</span>
                                                    </div>
                                                    <input type="text" id="year" name="year" class="form-control" autocomplete="off" value="{{ $queryData['year'] }}">
                                                </div>
                                            </div>

                                            <!-- 調查 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">調查</span>
                                                    </div>
                                                    <input type="text" id="times" name="times" class="form-control" autocomplete="off" value="{{ $queryData['times'] }}">
                                                </div>
                                            </div>


                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </form>
                                    </div>

                                    <div class="float-md-right">
                                        <!-- 新增 -->
                                        <!-- <a href="/admin/site_survey_old/create">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                        </a> -->
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">編號</th>
                                                <th>年度<i class="fa fa-sort" data-toggle="sort" data-sort-field="year"></i></th>
                                                <th>第幾次調查<i class="fa fa-sort" data-toggle="sort" data-sort-field="times"></i></th>
                                                <th>編號<i class="fa fa-sort" data-toggle="sort" data-sort-field="serno"></i></th>
                                                <th class="text-center" width="70">檢視</th>
                                                <!-- <th class="text-center" width="70">刪除</th> -->
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $key => $va)

                                                <tr>
                                                    <td class="text-center">{{ $key + 1 }}</td>
                                                    <td>{{ $va->year }}</td>
                                                    <td>{{ $va->times }}</td>
                                                    <td>{{ $va->serno }}</td>

                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/site_survey_old/{{ $va->id }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>

                                                    <!-- 刪除 -->
                                                    <!-- <td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/site_survey_old/{{ $va->id }}');" data-toggle="modal" data-target="#del_modol" >
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