@extends('admin.layouts.layouts')
@section('content')
    <?php $_menu = 'funding';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">經費概(結)處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">經費概(結)處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>經費概(結)處理</h3>
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
                                            <a href="/admin/funding/selectProbably"><button type="button" class="btn btn-primary"></i>批次新增</button></a>
                                            <a href="/admin/funding/selectConclusion"><button type="button" class="btn btn-primary"></i>產生結算</button></a>
                                            <button type="button" class="btn btn-primary" onclick="showUpdateunit()">更新單價</button>
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
                                                <th>辦班院區</th>
                                                <th>訓練班別</th>
                                                <th>期別</th>
                                                <th>分班名稱</th>
                                                <th>班別類型</th>
												<th>起訖期間</th>
                                                <th>班務人員</th>
                                                <th>種類</th>
                                                <th>合計</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($data))
                                                    @foreach($data as $t07tb)
                                                        <tr>
                                                            <td>
                                                                <a href="/admin/funding/edit/{{ $t07tb->t01tb_class }}/{{ $t07tb->t04tb_term }}/{{ $t07tb->type }}">
                                                                    <button class="btn btn-primary">編輯</button>
                                                                </a>
                                                            </td>
                                                            <td>{{ $t07tb->t01tb_class }}</td>                                                        
                                                            <td>
                                                            @if (isset($fields['t01tb']['branch'][$t07tb->t01tb_branch]))
                                                            {{ $fields['t01tb']['branch'][$t07tb->t01tb_branch] }}
                                                            @else
                                                            {{ $t07tb->t01tb_branch }}
                                                            @endif 
                                                            </td>
                                                            <td>{{ $t07tb->t01tb_name }}</td>
                                                            <td>{{ $t07tb->t04tb_term }}</td>
                                                            <td>{{ $t07tb->t01tb_branchname }}</td>
                                                            <td>
                                                                @if (isset($fields['t01tb']['process'][$t07tb->t01tb_process]))
                                                                {{ $fields['t01tb']['process'][$t07tb->t01tb_process] }}
                                                                @endif 
                                                            </td>
                                                            <td>{{ \App\Helpers\Common::addDateSlash($t07tb->t04tb_sdate).' ~ '.\App\Helpers\Common::addDateSlash($t07tb->t04tb_edate) }}</td>
                                                            <td>{{ $t07tb->m09tb_usename }}</td>
                                                            <td>
                                                                @if (isset($fields['t07tb']['type'][$t07tb->type]))
                                                                {{ $fields['t07tb']['type'][$t07tb->type] }}
                                                                @endif 
                                                            </td>
                                                            <td>{{ $t07tb->totalamt }}</td>
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
    <!-- include('admin/layouts/list/del_modol') -->

<!-- 更新單價 modal -->
<div id="updateunit" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content p-0 b-0">
            <div class="card mb-0">
                <div class="card-header">
                    <h3 class="card-title float-left">更新單價</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/funding/updateUnitPrice', 'onsubmit' => 'return checkUpdateunit()']) !!}
                <div class="card-body">
                    <label>開課日期(起)</label>
                    <input type="text" name="updateSdate" class="form-control" autocomplete="off" required>
                    <label>開課日期(迄)</label>
                    <input type="text" name="updateEdate" class="form-control" autocomplete="off" required>
                </div>
                <div class="modal-footer py-2">
                    <button type="submit" class="btn mr-3 btn-primary">確定</button>
                    <button type="button" class="btn mr-2 btn-danger pull-left" data-dismiss="modal">取消</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<!-- -->


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

        $("input[name=updateSdate]").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });     

        $("input[name=updateEdate]").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });           
    });

    function showUpdateunit()
    {
        $("#updateunit").modal('show');
    }

    function checkUpdateunit()
    {
        if ($("input[name=updateSdate]").val() > $("input[name=updateEdate]").val()){
            alert('始束日期應大於或等於開始日期!');
        }else{
            if (confirm('確定要更新' + $("input[name=updateSdate]").val() + '~' + $("input[name=updateEdate]").val() + '區間的單價嗎')){
                return true;
            }
        }
        return false;
    }
</script>
@endsection