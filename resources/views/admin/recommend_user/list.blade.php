@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'recommend_user';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">機關個人帳號</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">機關個人帳號列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>機關個人帳號</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            
                                            <div class="form-group row">
                                                <div class="input-group col-6">
                                                    <!-- 薦送機關代碼 -->
                                                    <div class="pull-left mobile-100 mr-1 mb-3">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">薦送機關代碼</span>
                                                            </div>
                                                            <input type="text" id="enrollorg" name="enrollorg" class="form-control" autocomplete="off" value="{{ $queryData['enrollorg'] }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- 薦送機關名稱 -->
                                                <div class="input-group col-6">
                                                    <div class="pull-left mobile-100 mr-1 mb-3">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">薦送機關名稱</span>
                                                            </div>
                                                            <input type="text" id="enrollname" name="enrollname" class="form-control" autocomplete="off" value="{{ $queryData['enrollname'] }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="input-group col-6">
                                                    <!-- 身分證號 -->
                                                    <div class="pull-left mobile-100 mr-1 mb-3">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">身分證號</span>
                                                            </div>
                                                            <input type="text" id="userid" name="userid" class="form-control" autocomplete="off" value="{{ $queryData['userid'] }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="input-group col-6">
                                                    <!-- mail -->
                                                    <div class="pull-left mobile-100 mr-1 mb-3">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">EMAIL</span>
                                                            </div>
                                                            <input type="text" id="email" name="email" class="form-control" autocomplete="off" value="{{ $queryData['email'] }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">
                                            <div class="float-left ">
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                                <!-- <div class="float-md-right">
                                                    <a href="/admin/recommend_user/create">
                                                        <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                                    </a>
                                                </div> -->
                                                <!-- 重設條件 -->
                                                <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0" onclick="doClear()" >重設條件</button>
                                                <!-- 設定聯絡窗口 -->
                                                <a href="/admin/recommend_user/active">
                                                <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0">設定聯絡窗口</button></a>
                                                <a href="/admin/recommend_user/print">
                                                <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0">聯絡名冊列印</button></a>
                                            </div>
                                        </form>
                                    </div>

                                    

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>功能</th>
                                                <th>薦送機關代碼</th>
                                                <th>薦送機關名稱</th>
                                                <th>聯絡單位</th>
                                                <th>姓名</th>
                                                <th>電話</th>
                                                <!-- <th class="text-center" width="70">修改</th>
                                                <th class="text-center" width="70">刪除</th> -->
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if ( isset($data) )    
                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <!--  <td class="text-center">{{ $startNo + $loop->iteration }}</td> -->
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/recommend_user/{{ $va->enrollorg }}/{{ $va->userid }}/edit" data-placement="top" data-toggle="tooltip" >
                                                            <i class="fa fa-pencil">編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->enrollorg }}</td>
                                                    <td>{{ $va->enrollname }}</td>
                                                    <td>{{ $va->section }}</td>
                                                    <td>{{ $va->username }}</td>
                                                    <td>{{ ($va->telnoa)? '('.$va->telnoa.')' : '' }}{{ $va->telnob }}{{ ($va->telnoc)? ' #'.$va->telnoc : ''}}</td>

                                                    

                                                    <!-- 刪除 -->
                                                    <!-- <td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/recommend_user/{{ $va->enrollorg }}/{{ $va->userid }}');" data-toggle="modal" data-target="#del_modol" >
                                                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                                                <i class="fa fa-trash text-danger"></i>
                                                            </span>
                                                        </span>
                                                    </td> -->
                                                </tr>
                                            @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ( isset($data) )
                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if ( isset($data) )
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

<script>
    function doClear(){
      document.all.enrollorg.value = "";
      document.all.enrollname.value = "";
      document.all.email.value = "";
      document.all.userid.value = "";
    }
</script>