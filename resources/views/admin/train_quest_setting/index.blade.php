@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/form.css') }}" >
    <?php $_menu = 'train_quest_setting';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">訓前訓後訓中問卷設定</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">訓前訓後訓中問卷設定</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>訓前訓後訓中問卷設定</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="min-width: 1000px; margin-bottom: 20px;">
                                        <form method="get" id="search_form">
                                            @include('gerneral.class_list')

                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <button class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                            <input type="button" class="btn btn-primary" value="重設條件" style="min-width:auto;" onclick="window.location='/admin/trainQuestSetting'">
                                        </form>
                                        
                                    </div>


                                    <div class="table-responsive">
                                        <table id="data_table" class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="130">功能</th>
                                                    <th>訓練班別</th>
                                                    <th>期別</th>
                                                    <th>分班名稱</th>
                                                    <th>班別類型</th>
                                                    <th>委訓機關</th>
                                                    <th>起訖期間</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $t04tb)
                                                <tr>
                                                    <td><button onclick="location.href='/admin/trainQuestSetting/setting/{{$t04tb->class}}/{{$t04tb->term}}'" class="btn btn-primary"><i class="fa fa-pencil">編輯</i></button></td>
                                                    <td>
                                                        @if (isset($t04tb->t01tb))
                                                        {{ $t04tb->t01tb->name }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $t04tb->term }}</td>
                                                    <td><!-- {{ $t04tb->term }} --></td>
                                                    <td>
                                                        @if (isset($t04tb->t01tb->s01tb))
                                                            {{ $t04tb->t01tb->s01tb->name }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (isset($t04tb->t01tb))
                                                            {{ $t04tb->t01tb->commission }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}</td>
                                                </tr>
                                                @endforeach 
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($data)
                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                                    @endif

                                </div>
                            </div>
                        </div>
                        @if($data)
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
        $("#sdate2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker3').click(function(){
            $("#sdate2").focus();
        });
        $("#edate2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker4').click(function(){
            $("#edate2").focus();
        });
        $("#sdate3").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker5').click(function(){
            $("#sdate3").focus();
        });
        $("#edate3").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker6').click(function(){
            $("#edate3").focus();
        });
    });
    function doClear(){
      document.all.yerly.value = "";
      document.all.meet.value = "";
      document.all.name.value = "";
      document.all.sdate.value = "";
      document.all.edate.value = "";
      document.all.sdate2.value = "";
      document.all.edate2.value = "";
      document.all.sdate3.value = "";
      document.all.edate3.value = "";
    }
    </script>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')
@endsection