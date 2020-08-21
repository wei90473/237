@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'StudentRoomQuery';?>

<style>
    .panel-heading .accordion-toggle {
        display: inline-block;
        text-align: center;
        color: rgb(255, 255, 255);
        font-size: 16px;
        box-sizing: border-box;
        line-height: 1.8em;
        vertical-align: middle;
        font-family: 微軟正黑體, "Microsoft JhengHei", Arial, Helvetica, sans-serif !important;
        background: rgb(250, 160, 90);
        padding: 5px 20px;
        border-width: initial;
        border-style: none;
        border-color: initial;
        border-image: initial;
        margin: 2px 0px;
        border-radius: 6px;
        margin-bottom: 10px;
    }
    .panel-heading .accordion-toggle:hover {
        background-color: #f79448 !important;
        border: 1px solid #f79448 !important;
            -webkit-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        opacity: 1;
    }
    .panel-heading .accordion-toggle::before {
        background-color: inherit; !important;
    }
    .panel-heading .accordion-toggle.collapsed::before {
        background-color: inherit; !important;
    }
    .search-float input,
    .search-float .select2-selection--single, .search-float select {
        min-width: initial;
    }
</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">學員寢室床位查詢</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">學員寢室床位查詢</li>
                </ol>
            </div>
        </div>
        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員寢室床位查詢</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="search-float" style="width:100%;">
                                    <form id="search_form">
                                        

                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">年度</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="year" name="year" value="{{ $queryData['year'] }}">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">期別</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="period" name="period" value="{{ $queryData['period'] }}">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">班別名稱</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="classname" name="classname" value="{{ $queryData['classname'] }}" >
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="float-md mobile-100 row mr-1 mb-3">
                                            <div class="input-group col-5">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">開訓日期範圍(起)</span>
                                                </div>
                                                <input type="text" id="startdate1" name="startdate1" class="form-control" autocomplete="off" value="{{ $queryData['startdate1'] }}">
                                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                            </div>
                                            <div class="input-group col-5">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">開訓日期範圍(訖)</span>
                                                </div>
                                                <input type="text" id="startdate2" name="startdate2" class="form-control" autocomplete="off" value="{{ $queryData['startdate2'] }}">
                                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
<!-- 進階/簡易搜尋開始 -->
                                            <div class="panel-group" id="accordion">
                                            <header class="panel-heading">

                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#search"> </a>

                                                </header>
                                                <footer id="search" class="panel-collapse collapse">
<!-- 進階/簡易搜尋開始 -->
                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">機關代碼</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="orgcode" name="orgcode" value="{{ $queryData['orgcode'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" onclick="showM17tbModolForRestructuring()">...</span>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">機關名稱</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="orgname" name="orgname" value="{{ $queryData['orgname'] }}" readonly>
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="form-row">
                                            <!-- <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">班別</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="classno" name="classno" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;">...</span>
                                                </div>
                                            </div> -->
                                            
                                        </div>

                                        

                                        <div class="float-md mobile-100 row mr-1 mb-3">
                                            <div class="input-group col-5">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">結訓日期範圍(起)</span>
                                                </div>
                                                <input type="text" id="enddate1" name="enddate1" class="form-control" autocomplete="off" value="{{ $queryData['enddate1'] }}">
                                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker3"><i class="fa fa-calendar"></i></span>
                                            </div>
                                            <div class="input-group col-5">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">結訓日期範圍(訖)</span>
                                                </div>
                                                <input type="text" id="enddate2" name="enddate2" class="form-control" autocomplete="off" value="{{ $queryData['enddate2'] }}">
                                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>

                                        <div class="float-md mobile-100 row mr-1 mb-3">
	                                        <div class="input-group col-3">
	                                            <div class="input-group-prepend">
	                                                <span class="input-group-text">樓別</span>
	                                            </div>
	                                            <select class="custom-select" id="floorno" name="floorno">
	                                            <option value=""></option>
	                                            @foreach($floorList as $key => $va)
                                                    <option value="{{ $va['floorno'] }}" {{ $queryData['floorno'] == $va['floorno']? 'selected' : '' }}>{{ $va['floorname'] }}</option>
                                                @endforeach
	                                            </select>
	                                        </div>
	                                    </div>

	                                    <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">身分證字號</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="idno" name="idno" value="{{ $queryData['idno'] }}">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">學號</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="studentid" name="studentid" value="{{ $queryData['studentid'] }}">
                                                </div>
                                            </div> 
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">姓名</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ $queryData['name'] }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="float-md mobile-100 row mr-1 mb-3">
                                            <div class="input-group col-5">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">住宿日期範圍(起)</span>
                                                </div>
                                                <input type="text" id="startdate3" name="startdate3" class="form-control" autocomplete="off" value="{{ $queryData['startdate3'] }}">
                                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                            </div>
                                            <div class="input-group col-5">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">住宿日期範圍(訖)</span>
                                                </div>
                                                <input type="text" id="startdate4" name="startdate4" class="form-control" autocomplete="off" value="{{ $queryData['startdate4'] }}">
                                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
<!-- 進階/簡易搜尋結束 -->
                                            </footer>
                                            </div>
<!-- 進階/簡易搜尋結束 -->
                                        <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                    </form>
                                </div>

                                <div class="float-md-right">

                                </div>

                                <div class="table-responsive">
                                	<table class="table table-bordered mb-0 ">
                                        <thead>
                                        <tr>
                                            <th class="text-center">年度</th>
                                            <th class="text-center">機關名稱</th>
                                            <th class="text-center">班別名稱</th>
                                            <th class="text-center">期別</th>
                                            <th class="text-center">開訓日期<br/>結訓日期</th>
                                            <th class="text-center">姓名</th>
                                            <th class="text-center">性別</th>
                                            <th class="text-center">樓別</th>
                                            <th class="text-center">寢室號碼</th>
                                            <th class="text-center">寢室名稱</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($data as $value)
                                        <?php
                                            if($value->sex == 'M'){
                                                $value->sex = '男';
                                            } else if($value->sex == 'F'){
                                                $value->sex = '女';
                                            }
                                        ?>
                                            <tr>
                                                <td class="text-center">{{ $value->yerly }}</td>
                                                <td>{{ $value->client }}</td>
                                               	<td>{{ $value->classname }}</td>
                                                <td class="text-center">{{ $value->term }}</td>
                                                <td class="text-center">{{ $value->sdate }}<br/>{{ $value->edate }}</td>
                                                <td class="text-center">{{ $value->studentname }}</td>
                                                <td class="text-center">{{ $value->sex }}</td>
                                                <td class="text-center">{{ $value->floorname }}</td>
                                                <td class="text-center">{{ $value->bedroom }}</td>
                                                <td class="text-center">{{ $value->roomname }}</td>
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
                     <!-- 列表頁尾 -->
                    @if($data)
                    @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin/layouts/list/enrollorg_modal')

@endsection
@section('js')
<script src="/backend/plugins/pagination/pagination.js" charset="UTF-8"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#startdate1").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker1').click(function(){
            $("#startdate1").focus();
        });

        $("#startdate2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker2').click(function(){
            $("#startdate2").focus();
        });

        $("#enddate1").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker3').click(function(){
            $("#enddate1").focus();
        });

        $("#enddate2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker4').click(function(){
            $("#enddate2").focus();
        });

        $("#startdate3").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker5').click(function(){
            $("#startdate3").focus();
        });

        $("#startdate4").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker6').click(function(){
            $("#startdate4").focus();
        });

    });

    function showM17tbModolForRestructuring(id = null, type, is_new = false)
    {
        $("#m17tb").modal('show');
    }

    function chooseM17tb(enrollorg, enrollname){
        $("input[name='orgcode']").val(enrollorg);
        $("input[name='orgname']").val(enrollname);
    }
</script>
@endsection