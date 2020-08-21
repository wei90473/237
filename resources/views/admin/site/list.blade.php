@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'site';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">洽借場地班期公告</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">洽借場地班期公告列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>洽借場地班期公告</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            <!-- 班別名稱 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別名稱</span>
                                                    </div>
                                                    <input type="text" id="keyword" name="keyword" class="form-control" autocomplete="off" value="{{ $queryData['keyword'] }}">
                                                </div>
                                            </div>

                                            <!-- 年度 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">年度</span>
                                                    </div>
                                                    <input type="text" id="year" name="year" class="form-control" autocomplete="off" value="{{ $queryData['year'] }}" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
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
                                        網頁公告年度 {{ $year->nightsyear }} ~ {{ $year->nighteyear }}年<i class="fa fa-pencil pl-2 pointer" style="color:rgb(0, 123, 255);"  data-toggle="modal" data-target="#year_modol"></i>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">編號</th>
                                                <th>是否公告</th>
                                                <th>報名開始日期</th>
                                                <th>報名結束日期</th>
                                                <th>班號</th>
                                                <th>班別名稱</th>
                                                <th>期別</th>
                                                <th class="text-center" width="70">網頁公告</th>
                                                <th class="text-center" width="70">報名公告</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <td class="text-center">{{ $startNo + $loop->iteration }}</td>
                                                    <td>{{ $va->publish }}</td>
                                                    <td>{{ $base->showDate($va->pubsdate) }}</td>
                                                    <td>{{ $base->showDate($va->pubedate) }}</td>
                                                    <td>{{ $va->class }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ $va->term }}</td>

                                                    <!-- 是否公告 -->
                                                    <td class="text-center">
                                                        <i class="fa fa-pencil pointer" onclick="$('#class').val('{{ $va->class }}')" style="color:rgb(0, 123, 255);" data-toggle="modal" data-target="#publish_modol"></i>
                                                    </td>

                                                    <!-- 報名公告 -->
                                                    <td class="text-center">
                                                        <i onclick="
                                                            $('#date_class').val('{{ $va->class }}');
                                                            $('#pubsdate_year').val('{{ mb_substr($va->pubsdate, 0, 3) }}');
                                                            $('#pubsdate_month').val('{{ mb_substr($va->pubsdate, 3, 2) }}');
                                                            $('#pubsdate_day').val('{{ mb_substr($va->pubsdate, 5, 2) }}');
                                                            $('#pubedate_year').val('{{ mb_substr($va->pubedate, 0, 3) }}');
                                                            $('#pubedate_month').val('{{ mb_substr($va->pubedate, 3, 2) }}');
                                                            $('#pubedate_day').val('{{ mb_substr($va->pubedate, 5, 2) }}');"
                                                            class="fa fa-pencil pointer" style="color:rgb(0, 123, 255);" data-toggle="modal" data-target="#date_modol"></i>
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

    <!-- 網頁公告年度視窗 -->
    <div id="year_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content p-0 b-0">
                <div class="card mb-0">
                    <div class="card-header bg-danger">
                        <h3 class="card-title float-left text-white">警告</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    {!! Form::open([ 'method'=>'post', 'url'=>'/admin/site/year', 'id'=>'year_form' ]) !!}
                        <div class="card-body">
                            <p class="mb-0">網頁公告年度：</p>
                            <input type="text" class="form-control" name="nightsyear" value="{{ $year->nightsyear }}" style="width:50px;" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            至
                            <input type="text" class="form-control" name="nighteyear" value="{{ $year->nighteyear }}" style="width:50px;" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                        </div>
                        <div class="modal-footer py-2">
                            <button type="button" class="btn mr-2 btn-info pull-left" data-dismiss="modal">取消</button>
                            <button type="submit" class="btn mr-3 btn-danger">確定</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <!-- 是否公告 -->
    <div id="publish_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content p-0 b-0">
                <div class="card mb-0">
                    <div class="card-header bg-danger">
                        <h3 class="card-title float-left text-white">警告</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    {!! Form::open([ 'method'=>'post', 'url' => '/admin/site/publish', 'id'=>'publish_form' ]) !!}
                        <div class="card-body">
                            <p class="mb-0">是否公告：</p>
                            <select class="form-control mt-3" name="publish">
                                <option value="Y">Y</option>
                                <option value="N">N</option>
                            </select>
                        </div>

                        <input type="hidden" id="class" name="class" vlaue="">

                        <div class="modal-footer py-2">
                            <button type="button" class="btn mr-2 btn-info pull-left" data-dismiss="modal">取消</button>
                            <button type="submit" class="btn mr-3 btn-danger">確定</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <!-- 是否公告 -->
    <div id="date_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content p-0 b-0">
                <div class="card mb-0">
                    <div class="card-header bg-danger">
                        <h3 class="card-title float-left text-white">警告</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    {!! Form::open([ 'method'=>'post', 'url' => '/admin/site/date', 'id'=>'date_form' ]) !!}
                        <div class="card-body">
                            <!-- 報名開始日期 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">報名開始日期<span class="text-danger">*</span></label>
                                <div class="col-sm-10">

                                    <div class="input-group roc-date input-max">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">民國</span>
                                        </div>

                                        <input type="text" class="form-control roc-date-year" maxlength="3" id="pubsdate_year" name="pubsdate[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($data->pubsdate))? mb_substr($data->pubsdate, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                        <div class="input-group-prepend">
                                            <span class="input-group-text">年</span>
                                        </div>

                                        <input type="text" class="form-control roc-date-month" maxlength="2" id="pubsdate_month" name="pubsdate[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($data->pubsdate))? mb_substr($data->pubsdate, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                        <div class="input-group-prepend">
                                            <span class="input-group-text">月</span>
                                        </div>

                                        <input type="text" class="form-control roc-date-day" maxlength="2" id="pubsdate_day" name="pubsdate[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($data->pubsdate))? mb_substr($data->pubsdate, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                        <div class="input-group-prepend">
                                            <span class="input-group-text">日</span>
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <!-- 報名結束日期 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">報名結束日期<span class="text-danger">*</span></label>
                                <div class="col-sm-10">

                                    <div class="input-group roc-date input-max">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">民國</span>
                                        </div>

                                        <input type="text" class="form-control roc-date-year" maxlength="3" id="pubedate_year" name="pubedate[year]" placeholder="請輸入年份" autocomplete="off" value="" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                        <div class="input-group-prepend">
                                            <span class="input-group-text">年</span>
                                        </div>

                                        <input type="text" class="form-control roc-date-month" maxlength="2" id="pubedate_month" name="pubedate[month]" placeholder="請輸入月份" autocomplete="off" value="" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                        <div class="input-group-prepend">
                                            <span class="input-group-text">月</span>
                                        </div>

                                        <input type="text" class="form-control roc-date-day" maxlength="2" id="pubedate_day" name="pubedate[day]" placeholder="請輸入日期" autocomplete="off" value="" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                        <div class="input-group-prepend">
                                            <span class="input-group-text">日</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="date_class" name="class" vlaue="">

                        <div class="modal-footer py-2">
                            <button type="button" class="btn mr-2 btn-info pull-left" data-dismiss="modal">取消</button>
                            <button type="button" class="btn mr-3 btn-danger" onclick="submitForm('#date_form');">確定</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

@endsection