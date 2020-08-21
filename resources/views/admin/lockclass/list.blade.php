@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/form.css') }}" >
    <?php $_menu = '';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">班期鎖定查詢</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">班期鎖定查詢</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>班期鎖定查詢</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="float-left search-float" style="min-width: 10px;">
                                        <form method="get" id="search_form">
                                            <!-- 年度 -->

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-3"  style="max-width: 20%;">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">年度</span>
                                                    </div>
                                                    <select type="text" id="yerly" name="t01tb[yerly]" class="browser-default custom-select field_yerly">
                                                        @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                                                            <option value="{{$i}}"
                                                            {{ ( $queryData['t01tb']['yerly'] == $i ) ? 'selected' : '' }}
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
                                                    <input type="text" id="class" name="t01tb[class]" class="field_class form-control" autocomplete="off" value="{{ $queryData['t01tb']['class']  }}" >
                                                </div>
                                            </div>

                                            <!-- 班別名稱 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="t01tb[name]" class="form-control" autocomplete="off" value="{{ $queryData['t01tb']['name']  }}">
                                                </div>
                                                <!-- **分班名稱 -->
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">分班名稱</span>
                                                    </div>
                                                    <input type="text" id="class_branch_name" name="t01tb[branchname]" class="form-control" autocomplete="off" value="{{ $queryData['t01tb']['branchname'] }}">
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">期別</span>
                                                    </div>
                                                    <input type="text" id="term" name="t04tb[term]" class="form-control field_term" autocomplete="off" value="{{ $queryData['t04tb']['term'] }}">
                                                </div>
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">上課地點</span>
                                                    </div>
                                                    <select class="form-control select2" name="t04tb[site_branch]">
                                                        <option value="">請選擇</option>
                                                        @foreach (config('database_fields.m14tb')['branch'] as $branch => $branch_name)
                                                            <option value="{{ $branch }}"
                                                            {{ ($queryData['t04tb']['site_branch'] == $branch) ? 'selected' : '' }}
                                                            >{{ $branch_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- **類別1 -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">辦班院區</span>
                                                    </div>
                                                    <select class="form-control select2 " name="t01tb[branch]">
                                                       <option value="">全部</option>
                                                        @foreach(config('app.branch') as $key => $va)
                                                            <option value="{{ $key }}"
                                                            {{ ($queryData['t01tb']['branch'] == $key) ? 'selected' : '' }}
                                                            >{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別類型</span>
                                                    </div>
                                                    <select class="form-control select2" name="t01tb[process]">
                                                        <option value="">全部</option>
                                                        @foreach(config('app.process') as $key => $va)
                                                            <option value="{{ $key }}"
                                                            {{ ($queryData['t01tb']['process'] == $key) ? 'selected' : '' }}
                                                            >{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">委訓單位</span>
                                                    </div>
                                                    <input type="text" name="t01tb[commission]" class="form-control" autocomplete="off" value="{{ $queryData['t01tb']['commission'] }}">
                                                </div>
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班務人員</span>
                                                    </div>
                                                    <select class="form-control select2" name="t04tb[sponsor]">
                                                        <option value="">請選擇</option>
                                                        @foreach ($sponsors as $sponsor)
                                                            <option value="{{ $sponsor->userid }}"
                                                            {{ ($queryData['t04tb']['sponsor'] == $sponsor->userid) ? 'selected' : '' }}
                                                            >{{ $sponsor->username }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">訓練性質</span>
                                                    </div>
                                                    <select class="form-control select2" name="t01tb[traintype]">
                                                        <option value="">全部</option>
                                                        @foreach(config('app.traintype') as $key => $va)
                                                            <option value="{{ $key }}"
                                                            {{ ($queryData['t01tb']['traintype'] == $key) ? 'selected' : '' }}
                                                            >{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別性質</span>
                                                    </div>
                                                    <select class="form-control select2" name="t01tb[type]">
                                                        <option value="">全部</option>
                                                        @foreach(config('app.class_type') as $key => $va)
                                                            <option value="{{ $key }}"
                                                            {{ ($queryData['t01tb']['type'] == $key) ? 'selected' : '' }}
                                                            >{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">類別1</span>
                                                    </div>
                                                    <select class="form-control select2" name="t01tb[categoryone]">
                                                        <option value="">請選擇</option>
                                                        @foreach($s01tbM as $code => $name)
                                                            <option value="{{ $code }}" {{ ($queryData['t01tb']['categoryone'] == $code) ? 'selected' : null }}>{{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">開訓日期(起)</span>
                                                    </div>
                                                    <input type="text" id="sdate" name="sdate_start" class="form-control" autocomplete="off" value="{{ $queryData['sdate_start'] }}" >
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">開訓日期(訖)</span>
                                                    </div>
                                                    <input type="text" id="edate" name="sdate_end" class="form-control" autocomplete="off" value="{{ $queryData['sdate_end'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">結訓日期(起)</span>
                                                    </div>
                                                    <input type="text" id="graduate_start_date" name="edate_start" class="form-control" autocomplete="off" value="{{ $queryData['edate_start'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker3"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">結訓日期(訖)</span>
                                                    </div>
                                                    <input type="text" id="graduate_end_date" name="edate_end" class="form-control" autocomplete="off" value="{{ $queryData['edate_end'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">在訓期間(起)</span>
                                                    </div>
                                                    <input type="text" id="training_start_date" name="training_start" class="form-control" autocomplete="off" value="{{ $queryData['training_start'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">在訓期間(訖)</span>
                                                    </div>
                                                    <input type="text" id="training_end_date" name="training_end" class="form-control" autocomplete="off" value="{{ $queryData['training_end'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>

                                            <!-- 排序 -->
                                            <!-- <input type="hidden" id="_sort_field" name="_sort_field" value=""> -->
                                            <!-- <input type="hidden" id="_sort_mode" name="_sort_mode" value=""> -->
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">
                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                            <a href="/admin/lockclass" ><button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">重設條件</button></a>
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="5%">功能</th>
                                                <th class="text-center">班號</th>
                                                <th class="text-center">訓練班別</th>
                                                <th class="text-center">期別</th>
                                                <th class="text-center">分班名稱</th>
                                                <th class="text-center">班別類型</th>
                                                <th class="text-center">委訓機關</th>
                                                <th class="text-center">起訖時間</th>
                                                <th class="text-center">班務人員</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($data as $t04tb)
                                                <tr class="text-center">
                                                    <?php
                                                        $dis='';
                                                        if(!empty($sess))
                                                        {
                                                            $dis='disabled';
                                                        }
                                                    ?>
                                                    <!-- <td><button value="{{$t04tb->t01tb->class}}_{{$t04tb->term}}" {{$dis}} onclick="lock_class(this);refresh_page();" class="btn btn-primary btn-sm mb-3 mb-md-0">鎖定</button></td> -->
                                                    <td><button value="{{$t04tb->t01tb->class}}_{{$t04tb->term}}" {{$dis}} onclick="lock_class(this);" class="btn btn-primary btn-sm mb-3 mb-md-0">鎖定</button></td>
                                                    <td class="text-center">
                                                    @if ($t04tb->t01tb)
                                                        {{ $t04tb->t01tb->class }}
                                                    @endif
                                                    </td>
                                                    <td class="text-center">
                                                    @if ($t04tb->t01tb)
                                                        {{ $t04tb->t01tb->name }}
                                                    @endif
                                                    </td>
                                                    <td>{{ $t04tb->term }}</td>
                                                    <td>
                                                    @if ($t04tb->t01tb)
                                                        {{ $t04tb->t01tb->branchname }}
                                                    @endif
                                                    </td>
                                                    <td>
                                                    <?php if ($t04tb->t01tb){
                                                        switch($t04tb->t01tb->process)
                                                        {
                                                            case '1':
                                                                $course_type='自辦班';
                                                                break;
                                                            case '2':
                                                                $course_type='委訓班';
                                                                break;
                                                            case '3':
                                                                $course_type='合作辦理';
                                                                break;
                                                            case '4':
                                                                $course_type='外地班';
                                                                break;
                                                            case '5':
                                                                $course_type='巡迴研習';
                                                                break;
                                                            default:
                                                                $course_type='';
                                                        }
                                                    }
                                                    ?>
                                                    {{$course_type}}
                                                    </td>
                                                    <td>
                                                    {{$t04tb->t01tb->commission}}
                                                    </td>
                                                    <td>{{ $t04tb->sdateformat }}~{{ $t04tb->edateformat }}</td>
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
                                    <!-- <form action="/admin/lockclass/lock" method="post" id="form_lock" target="_blank"> -->
                                    <form action="/admin/lockclass/lock" method="post" id="form_lock" >
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input name="class_lock" id="class_lock" type="hidden">
                                        <input name="term_lock" id="term_lock" type="hidden">
                                    </form>

                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])

                                </div>
                            </div>
                        </div>

                        <!-- 列表頁尾 -->
                        @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])

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

    function lock_class(obj)
    {
        var arr=obj.value.split("_");
        console.log(arr[0]);
        console.log(typeof(arr[0]));
        $("#class_lock").val(arr[0]);
        $("#term_lock").val(arr[1]);
        $("#form_lock").submit();
    }

    function refresh_page()
    {
        window.location.reload();
        setTimeout('refresh_page()',3000);
    }
    //setTimeout('refresh_page()',3000);

    </script>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection