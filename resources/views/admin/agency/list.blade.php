@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'agency';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">訓練機構資料維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">訓練機構資料維護</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>訓練機構資料維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            <!-- 訓練機構代碼 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">訓練機構代碼</span>
                                                    </div>
                                                    <input type="text" id="agency" name="agency" class="form-control" autocomplete="off" value="{{ $queryData['agency'] }}">
                                                </div>
                                            </div>

                                            <!-- 訓練機構名稱 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">訓練機構名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ $queryData['name'] }}">
                                                </div>
                                            </div>

                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                            <!-- 重設條件 -->
                                            <button type="button" class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()" >重設條件</button>
                                        </form>
                                    </div>

                                    <div class="float-md-right">
                                        <!-- 新增 -->
                                        <a href="/admin/agency/create">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                        </a>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>訓練機構代碼</th>
                                                <th>訓練機構</th>
                                                <th>電話</th>
                                                <th>傳真</th>
                                                <th>地址</th>
                                                <th>修改</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($data))
                                            @foreach($data as $va)
                                                <tr>
                                                    <td>{{ $va->agency }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ $va->telnoa.$va->telnob.($va->telnoc !=''?'#'.$va->telnoc : '') }}</td>
                                                    <td>{{ $va->faxnoa.'-'.$va->faxnob }}</td>
                                                    <td>{{ $va->address }}</td>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/agency/{{ $va->agency }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
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

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

<script type="text/javascript">
    
    function doClear(){
      document.all.agency.value = "";
      document.all.name.value = "";
    }
</script>