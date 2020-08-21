@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'itineracy_annual';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">巡迴研習</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">年度主題設定</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>年度主題設定</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <!-- 搜尋條件 -->
                                    <div class="search-float">
                                        <form method="get" id="search_form">
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <!-- 年度 -->
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
                                                <!-- 名稱 -->
                                                <div class="input-group col-6">
                                                     <div class="input-group-prepend">
                                                        <span class="input-group-text">名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ isset($queryData['name'])?$queryData['name']:'' }}">
                                                </div>
                                            </div>
                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{isset($queryData['_sort_field'])?$queryData['_sort_field']:''  }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{isset($queryData['_sort_mode'])?$queryData['_sort_mode']:'' }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{isset($queryData['_paginate_qty'])?$queryData['_paginate_qty']:''  }}">
                                        <div class="float-left">
                                            <!-- 查詢 -->
                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                            <!-- 重設條件 -->
                                            <button type="button" class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()" >重設條件</button>
                                            <!-- 新增 -->
                                            <a href="/admin/itineracy_annual/create">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                            </a>
                                        </div>    
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="80">功能</th>
                                                <th>年度</th>
                                                <th>期別</th>
                                                <th>巡迴計畫名稱</th>
                                                <th>匯總表公告期間</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($data))
                                            @foreach($data as $va)
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/itineracy_annual/edit/{{ $va->yerly.$va->term }}">
                                                            <i class="fa fa-pencil">編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->yerly }}</td>
                                                    <td>{{ $va->term }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ substr($va->sdate,0,3).'/'.substr($va->sdate,-4,2).'/'.substr($va->sdate,-2) }}～{{ substr($va->edate,0,3).'/'.substr($va->edate,-4,2).'/'.substr($va->edate,-2) }}</td>
                                                    <td>
                                                        <a href="/admin/itineracy_annual/setting/{{ $va->yerly.$va->term }}">
                                                            <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">年度主題設定</button>
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
@section('js')
<script>
    function doClear(){
      var d = new Date();
      var yerly = (d.getFullYear() - 1911);
      document.all.yerly.value = yerly;
      document.all.name.value = "";
    }
    

</script>
@endsection