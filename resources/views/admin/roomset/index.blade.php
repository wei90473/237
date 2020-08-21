@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'roomset';?>
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
                <h4 class="pull-left page-title">寢室床位安排(南投院區)</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">寢室床位安排(南投院區)</li>
                </ol>
            </div>
        </div>
        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>寢室床位安排(南投院區)</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="search-float" style="width:100%;">
                                    <form id="search_form">
                                        <!-- <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">機關代碼</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="orgcode" name="orgcode" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;">...</span>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">機關名稱</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="orgname" name="orgname" value="" readonly>
                                                </div>
                                            </div> 
                                        </div> -->

                                        <div class="form-row">
                                            <!-- 年度 -->
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">年度</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="year" name="year" value="{{ $queryData['year'] }}">
                                                </div>
                                            </div>
                                            <!-- 班號 -->
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">班號</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="class" name="class" value="{{ $queryData['class'] }}">
                                                </div>
                                            </div>
                                            <!-- 期別 -->
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">期別</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="period" name="period" value="{{ $queryData['period'] }}">
                                                </div>
                                            </div>
                                            <!-- 班別名稱 -->
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">班別名稱</label>
                                                    </div>
                                                    <input type="text" class="form-control" id="classname" name="classname" value="{{ $queryData['classname'] }}">
                                                </div>
                                            </div> 
                                        </div>

                                        <div class="float-md mobile-100 row mr-1 mb-3">
                                            <!-- 開訓日期 -->
                                            <div class="input-group col-5">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">開訓日期範圍(起)</span>
                                                </div>
                                                <input type="text" id="sdate1" name="sdate1" class="form-control" autocomplete="off" value="{{ $queryData['sdate1'] }}">
                                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                            </div>
                                            <div class="input-group col-5">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">開訓日期範圍(訖)</span>
                                                </div>
                                                <input type="text" id="sdate2" name="sdate2" class="form-control" autocomplete="off" value="{{ $queryData['sdate2'] }}">
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
                                            <!-- 班別類型 -->
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">班別類型</label>
                                                    </div>

                                                    <select class="custom-select" name="process">
                                                        <option value=""></option>
                                                        @foreach(config('app.process') as $key => $va)
                                                            <option  value="{{ $key }}" {{ $queryData['process'] == $key? 'selected' : '' }}>{{ $key.$va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        

                                        <div class="float-md mobile-100 row mr-1 mb-3">
                                            <!-- 結訓日期 -->
                                            <div class="input-group col-5">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">結訓日期範圍(起)</span>
                                                </div>
                                                <input type="text" id="edate1" name="edate1" class="form-control" autocomplete="off" value="{{ $queryData['edate1'] }}">
                                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker3"><i class="fa fa-calendar"></i></span>
                                            </div>
                                            <div class="input-group col-5">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">結訓日期範圍(訖)</span>
                                                </div>
                                                <input type="text" id="edate2" name="edate2" class="form-control" autocomplete="off" value="{{ $queryData['edate2'] }}">
                                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>

                                        <div class="float-md mobile-100 row mr-1 mb-3">
                                            <div class="input-group col-5">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">在訓日期範圍(起)</span>
                                                </div>
                                                <input type="text" id="courseStartDate" name="courseStartDate" class="form-control" autocomplete="off" value="{{ $queryData['courseStartDate'] }}">
                                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                            </div>
                                            <div class="input-group col-5">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">在訓日期範圍(訖)</span>
                                                </div>
                                                <input type="text" id="courseEndDate" name="courseEndDate" class="form-control" autocomplete="off" value="{{ $queryData['courseEndDate'] }}">
                                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
<!-- 進階/簡易搜尋結束 -->
                                            </footer>
                                            </div>
<!-- 進階/簡易搜尋結束 -->                                        
                                        <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">
                                        <!-- <input type="hidden" id="auto" name="auto" value=""> -->
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                        @if(!empty($queryData['courseStartDate']) || !empty($queryData['courseEndDate']))
                                        <button type="submit" class="btn btn-primary" name="auto" value="1">自動安排</button>
                                        @endif
                                    
                                </div>

                                <div class="float-md-right">

                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0 table-hover">
                                        <thead>
                                        <tr>
                                            @if(!empty($queryData['courseStartDate']) || !empty($queryData['courseEndDate']))
                                            <th class="text-center" style="vertical-align: middle">優先文昌樓</th>
                                            @endif
                                            <th class="text-center" style="vertical-align: middle">年度</th>
                                            <th class="text-center" style="vertical-align: middle">機關名稱</th>
                                            <th class="text-center" style="vertical-align: middle">班號</th>
                                            <th class="text-center" style="vertical-align: middle">班別名稱</th>
                                            <th class="text-center" style="vertical-align: middle">分班名稱</th>
                                            <th class="text-center" style="vertical-align: middle">期別</th>
                                            <th class="text-center" style="vertical-align: middle">開班類別</th>
                                            <th class="text-center" style="vertical-align: middle">開訓日期<br/>結訓日期</th>
                                            <th class="text-center" style="vertical-align: middle">住宿日期<br/>時間起迄</th>
                                            <th class="text-center" style="vertical-align: middle">冊列數<br/>(男)</th>
                                            <th class="text-center" style="vertical-align: middle">冊列數<br/>(女)</th>
                                            <th class="text-center" style="vertical-align: middle">住宿數<br/>(男)</th>
                                            <th class="text-center" style="vertical-align: middle">住宿數<br/>(女)</th>
                                            <th class="text-center" style="vertical-align: middle">辦班人員</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($data as $value)
                                            <tr>
                                                @if(!empty($queryData['courseStartDate']) || !empty($queryData['courseEndDate']))
                                                    @if($value->auto == 'Y' && $value->longclass == 'Y')
                                                        <td class="text-center" style="width: 5%"></td>
                                                    @elseif($value->auto == 'Y')
                                                        <td class="text-center" style="width: 5%"><a href="/admin/roomset/autoSetAgain/{{ $value->class }}/{{ $value->term }}/{{ !empty($value->staystartdate)?$value->staystartdate:$value->sdate }}/{{ !empty($value->stayenddate)?$value->stayenddate:$value->edate }}/{{$value->longclass}}/0">再次自動安排</a></td>
                                                    @else
                                                        <td class="text-center" style="width: 5%"><input type="text" name="autoClass_{{ $value->class }}_{{ $value->term }}_{{ $value->longclass }}" value="" style="width: 30%"></td>
                                                    @endif
                                                @endif
                                                <td class="text-center">{{ $value->yerly }}</td>
                                                <td>{{ $value->client }}</td>
                                                <td>{{ $value->class }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-center">{{ $value->branchname }}</td>
                                                <td class="text-center">{{ $value->term }}</td>
                                                <td class="text-center">{{ config('app.process.'.$value->process) }}</td>
                                                <td class="text-center">{{ $value->sdate }}<br/>{{ $value->edate }}</td>
                                                @if($value->longclass == 'Y')
                                                <td class="text-center"><a href="/admin/roomset/editLongRoomset/{{ $value->class }}/{{ $value->term }}">長期班住宿設定</a></td>
                                                <td class="text-center">{{ $value->totalMaleCount }}</td>
                                                <td class="text-center">{{ $value->totalFemaleCount }}</td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                @else
                                                <td class="text-center"><a href="/admin/roomset/editRoomset/{{ $value->class }}/{{ $value->term }}">{{ !empty($value->staystartdate)?$value->staystartdate:$value->sdate }}{{ $value->staystarttime==3?'晚':($value->staystarttime==2?'中':'早') }}<br/>{{ !empty($value->stayenddate)?$value->stayenddate:$value->edate }}{{ $value->stayendtime==3?'晚':($value->stayendtime==2?'中':'早') }}</a></td>
                                                <td class="text-center">{{ $value->totalMaleCount }}</td>
                                                <td class="text-center">{{ $value->totalFemaleCount }}</td>
                                                <td class="text-center"><a href="/admin/roomset/bedSet/{{ $value->class }}/{{ $value->term }}/1">{{ $value->hasBedMaleCount }}/{{ $value->dormMaleCount }}</a><br/><a href="/admin/roomset/cancelRoomset/{{ $value->class }}/{{ $value->term }}/1">取消</a></td>
                                                <td class="text-center"><a href="/admin/roomset/bedSet/{{ $value->class }}/{{ $value->term }}/2">{{ $value->hasBedFemaleCount }}/{{ $value->dormFemaleCount }}</a><br/><a href="/admin/roomset/cancelRoomset/{{ $value->class }}/{{ $value->term }}/2">取消</a></td>
                                                @endif
                                                <td class="text-center">{{ $value->username }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    </form>
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

@endsection

@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $("#sdate1").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker1').click(function(){
            $("#sdate1").focus();
        });

        $("#sdate2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker2').click(function(){
            $("#sdate2").focus();
        });

        $("#edate1").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker3').click(function(){
            $("#edate1").focus();
        });

        $("#edate2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker4').click(function(){
            $("#edate2").focus();
        });

        $("#courseStartDate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker5').click(function(){
            $("#courseStartDate").focus();
        });

        $("#courseEndDate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker6').click(function(){
            $("#courseEndDate").focus();
        });

    });

    function autoFun(){
        var obj = document.getElementById('search_form');
        document.getElementById('auto').value = 1;
        obj.submit();
    }
</script>
@endsection

<?php 
if(isset($queryData['auto']) && $queryData['auto'] == '2'){
    $autoArray = array();
    $autoArray['sdate'] = $queryData['courseStartDate'];
    $autoArray['edate'] = $queryData['courseEndDate'];
    
    echo '<script src="/backend/assets/js/jquery.min.js"></script>';
    echo '<script>';
    echo '$( document ).ready(function() {';
   
    echo 'window.open("'.route('autoExport', $autoArray).'","_blank");';
    echo '});';
    echo '</script>';
}


?>