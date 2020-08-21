@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'demand_survey';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">需求調查處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">需求調查列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>需求調查處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="min-width: 1200px;">
                                        <form method="get" id="search_form">
                                            <!-- 年度 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-4">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">年度</span>
                                                    </div>
                                                    <select class="browser-default custom-select" name="yerly">
                                                    @foreach($queryData['choices'] as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['yerly'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                    @endforeach
                                                    </select>
                                                </div>
                                                <!-- 辦班院區 -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">院區</span>
                                                    </div>
                                                    <select class="form-control select2" name="branch">
                                                       <option value="">全部</option>
                                                        @foreach(config('app.branch') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['branch'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 第幾次 -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">第幾次</span>
                                                    </div>
                                                    <input type="text" id="times" name="times" class="form-control" autocomplete="off" value="{{ $queryData['times'] }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                                </div>
                                            </div>
                                            <!--需求調查名稱-->
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">需求調查名稱</span>
                                                    </div>
                                                    <input type="text" id="purpose" name="purpose" class="form-control" autocomplete="off" value="{{ $queryData['purpose'] }}">
                                                </div>
                                            </div>
                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">
                                            <div class="float-md mobile-100 row mr-1">
                                                <div class="input-group col-12">
                                                    <button type="submit" class="btn mobile-100 mb-3 mr-1"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                                    <div class="float-md-right">
                                                        <!-- 重設條件 -->
                                                        <button class="btn mobile-100 mb-3 mr-1" onclick="doClear()"  type="button">重設條件</button>
                                                        <!-- 新增 -->
                                                        <a href="/admin/demand_survey/create">
                                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2" ></i>新增需求調查</button>
                                                        </a>
                                                        <!-- 公告 -->
                                                        <a href="/admin/demand_survey/bulletin_board">
                                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-pencil fa-lg pr-2"></i>公告文字維護</button>
                                                        </a>

                                                        <!-- 公告** -->
                                                        <a href="/admin/demand_survey/form2"  style="display: none;">
                                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>設定</button>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">功能</th>
                                                <th>年度</th>
                                                <th>院區</th>
                                                <th>第幾次調查</th>
                                                <th>需求調查名稱</th>
                                                <th>填表開始日期</th>
                                                <th>填表結束日期</th>
                                                <th></th>
                                                <!--th class="text-center" width="70">刪除</th-->
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($data))
                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/demand_survey/{{ $va->id }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="編輯">
                                                        <button type="submit" class="btn btn-primary mb-3"><i class="fa fa-pencil fa-lg pr-2"></i>編輯</button>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->yerly }}</td>
                                                    <td>{{ config('app.branch.'.$va->branch) }}</td>
                                                    <td>{{ $va->times }}</td>
                                                    <td>{{ $va->purpose }}</td>
                                                    <td>{{ $va->sdate }}</td>
                                                    <td>{{ $va->edate }}</td>
                                                    <td>
                                                        <a href="/admin/demand_survey/form2?id={{ $va->id }}">
                                                            <button type="button" class="btn btn-success mb-3"><i class="fa fa-eye fa-lg pr-2"></i>查看填表資料</button>
                                                        </a>    
                                                    </td>
                                                    <!-- 刪除 -->
                                                    <!--td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/demand_survey/{{ $va->id }}');" data-toggle="modal" data-target="#del_modol" >
                                                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                                                <i class="fa fa-trash text-danger"></i>
                                                            </span>
                                                        </span>
                                                    </td-->
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
    <script language="javascript">
        function doClear(){
          var d = new Date();
          var yerly = (d.getFullYear() - 1911);
          document.all.yerly.value = yerly;
          document.all.times.value = "";
          document.all.purpose.value = "";
          $("select[name=branch]").val('').trigger("change");
          
        }
    </script>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection