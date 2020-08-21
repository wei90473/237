@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'site_schedule';?>
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
                    <h4 class="pull-left page-title">洽借場地班期排程處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">洽借場地班期排程處理</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>洽借場地班期排程處理</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="min-width: 1200px;">
                                        <form method="get" id="search_form">
                                                <!-- 年度 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-2">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">年度</span>
                                                    </div>
                                                    <select class="browser-default custom-select" name="yerly" onchange="getdetails()">
                                                    @foreach($queryData['choices'] as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['yerly'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                    @endforeach
                                                    </select>
                                                </div>
                                                <!-- 月份 -->
                                                <div class="input-group col-3">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">月份</span>
                                                    </div>
                                                    <select class="form-control select2" name="month">
                                                        <option value="">請選擇</option>    
                                                    @for($i=1;$i<13;$i++)
                                                        <option value="{{ str_pad($i,2,'0',STR_PAD_LEFT) }}" {{ $queryData['month'] == str_pad($i,2,'0',STR_PAD_LEFT)? 'selected' : '' }}>{{ $i }}</option>
                                                    @endfor
                                                    </select>
                                                </div>
                                                <!-- 班號 -->
                                                <div class="input-group col-2">
                                                     <div class="input-group-prepend">
                                                        <span class="input-group-text">班號</span>
                                                    </div>
                                                    <input type="text" id="class" name="class" class="form-control" autocomplete="off" value="{{ $queryData['class'] }}">
                                                </div>
                                                <!-- 期別 -->
                                                <div class="input-group col-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">期別</span>
                                                    </div>
                                                    <input type="text" id="term" name="term" class="form-control" autocomplete="off" value="{{ $queryData['term'] }}">
                                                </div>
                                                <!-- 班別名稱 -->
                                                <div class="input-group col-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ $queryData['name'] }}">
                                                </div>
                                            </div>
                                            <!-- 辦班院區 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">辦班院區</span>
                                                    </div>
                                                    <select class="form-control select2" name="branch">
                                                       <option value="">全部</option>
                                                        @foreach(config('app.branch') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['branch'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                 <!-- 開訓日期 -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">開訓日期範圍(起)</span>
                                                    </div>
                                                    <input type="text" id="sdate_begin" name="sdate_begin" class="form-control" autocomplete="off" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">開訓日期範圍(迄)</span>
                                                    </div>
                                                    <input type="text" id="sdate_end" name="sdate_end" class="form-control" autocomplete="off" value="">
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


                                            <!-- 上課地點 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">上課地點</span>
                                                    </div>
                                                    <select class="form-control select2" name="sitebranch">
                                                        <option value="">請選擇</option>
                                                        @foreach(config('app.branch') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['sitebranch'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                        <option value="3" {{ $queryData['sitebranch'] == $key? 'selected' : '' }} >外地</option>
                                                    </select>
                                                </div>
                                                
                                            </div>
                                            <!-- 訓練性質 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">訓練性質</span>
                                                    </div>
                                                    <select class="form-control select2" name="traintype">
                                                        <option value="">請選擇</option>
                                                        @foreach(config('app.traintype') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['traintype'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 班務人員** -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班務人員</span>
                                                    </div>
                                                    <?php $list = $base->getSponsor(); ?>
                                                    <select id="sponsor" name="sponsor" class="form-control select2">
                                                        <option value="">請選擇</option>
                                                        @foreach($list as $key => $va)
                                                            <option value="{{ $key }}" {{ old('sponsor', (isset($queryData['sponsor']))? $queryData['sponsor']: '') == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- 結訓日期範圍 -->
                                            <div class="form-group row">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">結訓日期範圍(起)</span>
                                                    </div>
                                                    <input type="text" id="edate_begin" name="edate_begin" class="form-control" autocomplete="off" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker3"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">結訓日期範圍(迄)</span>
                                                    </div>
                                                    <input type="text" id="edate_end" name="edate_end" class="form-control" autocomplete="off" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                            <!-- 在訓期間範圍 -->
                                            <div class="form-group row">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">在訓期間範圍(起)</span>
                                                    </div>
                                                    <input type="text" id="indate_begin" name="indate_begin" class="form-control" autocomplete="off" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">在訓期間範圍(迄)</span>
                                                    </div>
                                                    <input type="text" id="indate_end" name="indate_end" class="form-control" autocomplete="off" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>

 
<!-- 進階/簡易搜尋結束 -->
</footer>
</div>
<!-- 進階/簡易搜尋結束 -->

                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                            <!-- 重設條件 -->
                                            <button class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()" type="button">重設條件</button>
                                            <a href="/admin/site_schedule/add">
                                                <button type="button" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新增排程</button>
                                            </a>
                                            <a href="/admin/site_schedule/details"  id="detailslink">
                                                <button type="button" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-book fa-lg pr-2"></i>排程明細</button>
                                            </a>
                                        </form>
                                    </div>

                                    <div class="float-md-right">
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>功能</th>
                                                    <th width="150">班號</th>
                                                    <th>班別名稱</th>
                                                    <th>期別</th>
                                                    <th>人數</th>
                                                    <th width="70">教室</th>
                                                    <th width="70">開課日期</th>
                                                    <th width="70">結束日期</th>
                                                    <th width="70">班務人員</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($data))        
                                                @foreach($data as $va)  
                                                <tr class="text-center">
                                                    <td>
                                                        <a href="/admin/site_schedule/{{$va->class.$va->term}}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>修改
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->class }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ $va->term }}</td>
                                                    <td>{{ $va->quota }}</td>
                                                    <td>{{ $va->site }}</td>
                                                    <td>{{ $va->sdate }}</td>
                                                    <td>{{ $va->edate }}</td>
                                                    <td>{{ $va->username }}</td>
                                                    <td>
                                                        <a href="/admin/site_schedule/calendar?class={{$va->class.'&term='.$va->term}}">
                                                            <button type="button" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-pencil fa-lg pr-2"></i>調整行事曆</button>
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
        $(document).ready(function() {
            $("#sdate_begin").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker1').click(function(){
                $("#sdate_begin").focus();
            });
            $("#sdate_end").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker2').click(function(){
                $("#sdate_end").focus();
            });
            $("#edate_begin").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker3').click(function(){
                $("#edate_begin").focus();
            });
            $("#edate_end").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker4').click(function(){
                $("#edate_end").focus();
            });
            $("#indate_begin").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker5').click(function(){
                $("#indate_begin").focus();
            });
            $("#indate_end").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker6').click(function(){
                $("#indate_end").focus();
            });
        });
   
        function doClear(){
          var d = new Date();
          var yerly = (d.getFullYear() - 1911);
          document.all.yerly.value = yerly;
          document.all.class.value = "";
          document.all.name.value = "";
          $("select[name=month]").val('').trigger("change");
          document.all.term.value = "";
          $("select[name=sitebranch]").val('').trigger("change");
          $("select[name=branch]").val('').trigger("change");
          $("select[name=sponsor]").val('').trigger("change");
          $("select[name=traintype]").val('').trigger("change");
          document.all.sdate_begin.value = "";
          document.all.sdate_end.value = "";
          document.all.edate_begin.value = "";
          document.all.sdate_end.value = "";
          document.all.indate_begin.value = "";
          document.all.indate_end.value = "";
        }

        function getdetails(){
            var yerly = $('select[name=yerly]').val();
            $('#detailslink').attr('href','/admin/site_schedule/details?yerly='+yerly);
        }
        $(document).ready(function(){
            getdetails();
        })
</script>
@endsection