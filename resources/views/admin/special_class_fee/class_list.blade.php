@extends('admin.layouts.layouts')
@section('content')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/form.css') }}" > -->
    <?php $_menu = 'special_class_fee';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">委訓班費用處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">委訓班費用處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>
                            委訓班費用處理
                            </h3>
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
                                        </form>
                                    </div>

                                    
                                    <div class="form-group row col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                <tr>
                                                    <th class="text-center" width="100">功能</th>
                                                    <th class="text-center" width="70">班號</th>
                                                    <th>辦班院區</th>
                                                    <th>訓練班別</th>
                                                    <th>期別</th>
                                                    <th>分班名稱</th>
                                                    <th>班別類型</th>
                                                    <th>起訖期間</th>
                                                    <th>班務人員</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @if (isset($data))
                                                        @foreach($data as $t04tb)
                                                            <tr>
                                                                <td class="text-center">
                                                                    @if (($t04tb->id == null))
                                                                        <button class="btn btn-primary" onclick="computeFee('{{ $t04tb->class }}', '{{ $t04tb->term }}')">計算</button>
                                                                    @else
                                                                        <a href="/admin/special_class_fee/edit/{{ $t04tb->class }}/{{ $t04tb->term }}">
                                                                            <button class="btn btn-primary">編輯</button>
                                                                        </a>                                                                    
                                                                    @endif 
                                                                </td>
                                                                <td>{{ $t04tb->t01tb->class }}</td>                                                        
                                                                <td>
                                                                @if(isset($fields['t01tb']['branch'][$t04tb->t01tb->branch]))
                                                                {{ $fields['t01tb']['branch'][$t04tb->t01tb->branch] }}
                                                                @else
                                                                {{ $t04tb->t01tb->branch }}
                                                                @endif 
                                                                </td>
                                                                <td>{{ $t04tb->t01tb->name }}</td>
                                                                <td>{{ $t04tb->term }}</td>
                                                                <td>{{ $t04tb->t01tb->branchname }}</td>
                                                                <td>
                                                                    @if (isset($fields['t01tb']['process'][$t04tb->t01tb->process]))
                                                                    {{ $fields['t01tb']['process'][$t04tb->t01tb->process] }}
                                                                    @endif 
                                                                </td>
                                                                <td>{{ \App\Helpers\Common::addDateSlash($t04tb->sdate).' ~ '.\App\Helpers\Common::addDateSlash($t04tb->edate) }}</td>
                                                                <td>
                                                                @if (isset($t04tb->m09tb))
                                                                {{ $t04tb->m09tb->username }}
                                                                @endif 
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif 
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    @if(!empty($data))
                                        <!-- 分頁 -->
                                        @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if(!empty($data))
                            <!-- 列表頁尾 -->
                            @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{ Form::open(['id' => 'computeForm', 'method' => 'post', 'url' => '/admin/special_class_fee/computeFee']) }}
        <input type="hidden" name="computeClassNo" value="">
        <input type="hidden" name="computeTerm" value="">
    {{ Form::close() }}

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

    function computeFee(classNo, term)
    {
        if (confirm('此班級尚未計算費用 確定要計算嗎?')){
            $('input[name=computeClassNo]').val(classNo);
            $('input[name=computeTerm]').val(term);
            $('#computeForm').submit();            
        }
    }

</script>
@endsection