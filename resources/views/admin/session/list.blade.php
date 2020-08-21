@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'session';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">會議資料處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">會議資料處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>會議資料處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            <!-- 年度 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-5">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">年度</span>
                                                    </div>
                                                    <select class="form-control select2 " name="yerly">
                                                    @foreach($queryData['choices'] as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['yerly'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                    @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- 會議代號 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-5">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">會議代號</span>
                                                    </div>
                                                    <input type="text" id="meet" name="meet" class="form-control" autocomplete="off" value="{{ $queryData['meet'] }}">
                                                </div>
                                            </div>
                                            <!-- 會議名稱 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-8">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">會議名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ $queryData['name'] }}">
                                                </div>
                                            </div>
                                            <!-- 開始時間 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">開始時間</span>
                                                    </div>
                                                    <input type="text" id="sdate" name="sdate" class="form-control" autocomplete="off" value="{{ $queryData['sdate'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">結束時間</span>
                                                    </div>
                                                    <input type="text" id="edate" name="edate" class="form-control" autocomplete="off" value="{{ $queryData['edate'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>    
                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                             <!-- 重設條件 -->
                                            <button class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()">重設條件</button>
                                            <!-- 新增 -->
                                            <a href="/admin/session/create">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                            </a>

                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">修改</th>
                                                <th>會議代號</th>
                                                <th>編號</th>
                                                <th>會議名稱</th>
                                                <th>開始日期</th>
                                                <th>結束日期</th>
                                                <th>人數</th>
                                                
                                              <!--  <th class="text-center" width="70">刪除</th>-->
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($data))
                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/session/{{ $va->meet.$va->serno }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->meet }}</td>
                                                    <td>{{ $va->serno }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ $base->showDate($va->sdate) }}</td>
                                                    <td>{{ $base->showDate($va->edate) }}</td>
                                                    <td>{{ $va->cnt }}</td>

                                                    

                                                    <!-- 刪除 
                                                    <td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/session/{{ $va->id }}');" data-toggle="modal" data-target="#del_modol" >
                                                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                                                <i class="fa fa-trash text-danger"></i>
                                                            </span>
                                                        </span>
                                                    </td>-->
                                                </tr>
                                            @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                     @if(isset($data))
                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                                    @endif
                                </div>
                            </div>
                        </div>
                         @if(isset($data))
                        <!-- 列表頁尾 -->
                        @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $("#sdate").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker1').click(function(){
            $("#sdate").focus();
        });
        $("#edate").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker2').click(function(){
            $("#edate").focus();
        });
    });
    function doClear(){
      document.all.yerly.value = "";
      document.all.meet.value = "";
      document.all.name.value = "";
      document.all.sdate.value = "";
      document.all.edate.value = "";
    }
    </script>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection