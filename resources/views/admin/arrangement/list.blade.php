@extends('admin.layouts.layouts')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/form.css') }}" >
    <?php $_menu = 'arrangement';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">課程配當安排</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">課程配當安排列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>課程配當安排</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="margin-bottom: 10px;min-width: 1000px;">
                                        <form method="get" id="search_form">

                                            @include('gerneral.class_list')                                         

                                            <!-- 排序 -->
                                            <!-- <input type="hidden" id="_sort_field" name="_sort_field" value=""> -->
                                            <!-- <input type="hidden" id="_sort_mode" name="_sort_mode" value=""> -->
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">
                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>  
                                            <a href="/admin/schedule/create">
                                                <!-- <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新增訓練排程</button> -->
                                            </a>
                                            
                                            <button type="button" onclick="location.href='/admin/arrangement'" class="btn btn-primary">重設條件</button>
                                        
                                            <button type="button" onclick="location.href='/admin/arrangement/batch_create'" class="btn btn-primary">批次新增</button>
                                        
                                        </form>
                                    </div>

                                    <div class="float-md-right">

                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th width="100">功能</th>
                                                <th class="text-center" width="70">班號</th>
                                                <th>班別名稱</th>
                                                <th>期別</th>
                                                <th>人數</th>
                                                <th>教室</th>
												<th>開課日期</th>
                                                <th>結束日期</th>
                                                <th>班務人員</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $t04tb)
                                                <tr>
                                                    <td><a href="/admin/arrangement/{{ $t04tb->class }}/{{ $t04tb->term }}"><button class="btn btn-primary btn-sm mb-3 mb-md-0">編輯</button><a/></td>
                                                    <td class="text-center">
                                                    @if ($t04tb->t01tb)
                                                        {{ $t04tb->t01tb->class }}
                                                    @endif 
                                                    </td>
                                                    <td>
                                                    @if ($t04tb->t01tb)
                                                        {{ $t04tb->t01tb->name }}
                                                    @endif 
                                                    </td>
                                                    <td>{{ $t04tb->term }}</td>
                                                    <td>{{ $t04tb->quota }} </td>
                                                    <td>
                                                    @if ($t04tb->t01tb)
                                                        {{ config('app.branch')[$t04tb->t01tb->branch] }}
                                                    @endif 
                                                    {{ $t04tb->site }}
                                                    </td>
													<td>{{ $t04tb->sdateformat }}</td>
													<td>{{ $t04tb->edateformat }}</td>
                                                    <td>
                                                        @if($t04tb->m09tb)
                                                            {{ $t04tb->m09tb->username }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>


                                    <!-- Modal1 批次增刪作業 -->
                                    <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">批次增刪作業</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="card-body pt-4 text-center">
                                                        <div class="card-header"><h3 class="card-title">輸入批次作業年度</h3></div>
                                                        <label >年度：</label>
                                                        <input type="text">
                                                        <br/>
                                                    </div>    
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-success ml-auto" data-dismiss="modal">批次新增</button>
                                                    <button type="button" class="btn btn-danger mx-0" data-dismiss="modal">批次刪除</button>
                                                    <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">取消</button>
                                                </div>
                                            </div>
                                        </div>
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

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
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


        $("#graduate_start_date").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });

        $('#datepicker3').click(function(){
            $("#graduate_start_date").focus();
        });

        $("#graduate_end_date").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });

        $('#datepicker4').click(function(){
            $("#graduate_end_date").focus();
        });


        $("#training_start_date").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker5').click(function(){
            $("#training_start_date").focus();
        });

        $("#training_end_date").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker6').click(function(){
            $("#training_end_date").focus();
        });


    });
</script>
@endsection