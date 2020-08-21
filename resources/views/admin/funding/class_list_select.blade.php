@extends('admin.layouts.layouts')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/form.css') }}" >
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>
                            @if ($type == "probably")
                                批次新增
                            @elseif ($type == "conclusion")
                                產生結算
                            @endif 
                            </h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="margin-bottom: 10px;">
                                        <form method="get" id="search_form">										
										<?php
										    $today = getdate();	 
											$yerly = isset($queryData['yerly'])? $queryData['yerly'] : $today['year']-1911;
											$month = isset($queryData['month'])? $queryData['month'] : $today['mon'];
										?>

                                            <!-- 年度 -->

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-3"  style="max-width: 20%;">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">年度</span>
                                                    </div>
                                                    <select type="text" id="yerly" name="yerly" class="browser-default custom-select field_yerly"  value="{{ $queryData['yerly'] }}">
                                                        @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                                                            <option value="{{$i}}" 
                                                            @if ($queryData['yerly'] == $i)
                                                                selected
                                                            @elseif ((int)date("Y")-1911 == $i && empty($queryData['yerly']))
                                                                selected
                                                            @endif
                                                            >{{$i}}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <!-- 班號 -->
                                                <div class="input-group col-3">
                                                     <div class="input-group-prepend">
                                                        <span class="input-group-text">班號</span>
                                                    </div>
                                                    <input type="text" id="class" name="class" class="field_class form-control" autocomplete="off" value="{{ $queryData['class'] }}" >
                                                </div>
                                            </div>

                                            <!-- 班別名稱 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="class_name" class="form-control" autocomplete="off" value="{{ $queryData['class_name'] }}">
                                                </div>
                                                <!-- **分班名稱 -->
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">分班名稱</span>
                                                    </div>
                                                    <input type="text" id="class_branch_name" name="class_branch_name" class="form-control" autocomplete="off" value="">
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">期別</span>
                                                    </div>
                                                    <input type="text" id="term" name="term" class="form-control field_term" autocomplete="off" value="{{ $queryData['term'] }}">
                                                </div>
                                                <div class="input-group col-5">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">上課地點</span>
                                                    </div>
                                                    <select class="form-control select2" name="class_location">
                                                        <option value="">請選擇</option>
                                                        <option value="1">臺北院區</option>
                                                        <option value="2">南投院區</option>
                                                        <option value="3">外地</option>
                                                    </select>
                                                </div>
                                                <!-- **類別1 -->
                                                <div class="input-group col-5">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">辦班院區</span>
                                                    </div>
                                                    <select class="form-control select2 " name="branch">
                                                       <option value="">全部</option>
                                                        @foreach(config('app.branch') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['branch'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-5">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別類型</span>
                                                    </div>
                                                    <select class="form-control select2" name="process">
                                                        <option value="">全部</option>
                                                        @foreach(config('app.process') as $key => $va)
                                                            <option value="{{ $key }}" >{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="input-group col-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">委訓單位</span>
                                                    </div>
                                                    <input type="text" name="entrust_train_unit" class="form-control" autocomplete="off" value="{{$queryData['entrust_train_unit']}}">
                                                </div>                                                
                                                <div class="input-group col-5">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班務人員</span>
                                                    </div>
                                                    <select class="form-control select2" name="type">
                                                        <option value="">請選擇</option>
                                                        <option value="1">班務人員1</option>
                                                        <option value="2">班務人員2</option>
                                                        <option value="3">班務人員3</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-5">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">訓練性質</span>
                                                    </div>
                                                    <select class="form-control select2" name="traintype">
                                                        <option value="">全部</option>
                                                        @foreach(config('app.traintype') as $key => $va)
                                                            <option value="{{ $key }}" >{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="input-group col-5">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別性質</span>
                                                    </div>
                                                    <select class="form-control select2" name="class_type">
                                                        <option value="">全部</option>
                                                        @foreach(config('app.class_type') as $key => $va)
                                                            <option value="{{ $key }}" >{{ $va }}</option>
                                                        @endforeach
                                                    </select>                                                    
                                                </div>                                                
                                                <div class="input-group col-4" style=" margin-top: 15px;">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">類別1</span>
                                                    </div>
                                                    <select class="form-control select2" name="type">
                                                        <option value="">請選擇</option>
                                                        <option value="1">班務人員1</option>
                                                        <option value="2">班務人員2</option>
                                                        <option value="3">班務人員3</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">開訓日期(起)</span>
                                                    </div>
                                                    <input type="text" id="sdate" name="train_start_date" class="form-control" autocomplete="off" value="{{ $queryData['train_start_date'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">開訓日期(訖)</span>
                                                    </div>
                                                    <input type="text" id="edate" name="train_end_date" class="form-control" autocomplete="off" value="{{ $queryData['train_end_date'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">結訓日期(起)</span>
                                                    </div>
                                                    <input type="text" id="graduate_start_date" name="graduate_start_date" class="form-control" autocomplete="off" value="{{ $queryData['graduate_start_date'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker3"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">結訓日期(訖)</span>
                                                    </div>
                                                    <input type="text" id="graduate_end_date" name="graduate_end_date" class="form-control" autocomplete="off" value="{{ $queryData['graduate_end_date'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">在訓期間(起)</span>
                                                    </div>
                                                    <input type="text" id="training_start_date" name="training_start_date" class="form-control" autocomplete="off" value="{{ $queryData['graduate_start_date'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">在訓期間(訖)</span>
                                                    </div>
                                                    <input type="text" id="training_end_date" name="training_end_date" class="form-control" autocomplete="off" value="{{ $queryData['graduate_end_date'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>                                           

                                            <!-- 排序 -->
                                            <!-- <input type="hidden" id="_sort_field" name="_sort_field" value=""> -->
                                            <!-- <input type="hidden" id="_sort_mode" name="_sort_mode" value=""> -->
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">
                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>  
                                        </form>
                                    </div>
                                    @if ($type == "probably")
                                        {{ Form::open(['method' => 'post', 'url' => '/admin/funding/batchInsertProbably']) }}
                                    @elseif ($type == "conclusion")
                                        {{ Form::open(['method' => 'post', 'url' => '/admin/funding/batchInsetConclusion']) }}
                                    @endif 
                                    
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
                                                                    <input type="checkbox" name="select[]" value="{{ $t04tb->class.'##'.$t04tb->term }}">
                                                                </td>
                                                                <td>{{ $t04tb->t01tb->class }}</td>                                                        
                                                                <td>
                                                                @if(isset($fields['t01tb']['branch'][$t04tb->t01tb->branch]))
                                                                {{ $fields['t01tb']['branch'][$t04tb->t01tb->branch] }}
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
                                    <div class="form-group row">
                                        <div class="form-group col-md-3">
                                            <button class="btn btn-primary">
                                            @if ($type == "probably")
                                                批次新增
                                            @elseif ($type == "conclusion")
                                                產生結算
                                            @endif                                             
                                            </button>
                                        </div>
                                    </div>
                                    {{ Form::close() }}

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