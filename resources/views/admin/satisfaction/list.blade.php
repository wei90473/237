@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'satisfaction';?>
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
                    <h4 class="pull-left page-title">課程及講座查詢(滿意度)</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">課程及講座查詢(滿意度)列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>課程及講座查詢(滿意度)</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="min-width: 1000px;">
                                        <form method="get" id="search_form">
                                        <input type="hidden" id="search" name="search" class="form-control" value="search">
                                            <!-- 年度 -->

                                            <div class="float-md mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                        <input type="radio" style="min-width: 30px;" id="year_or_day" name="year_or_day" <?=($queryData['year_or_day']=='1')?'checked':'';?> <?=$queryData['year_or_day'];?> value="1" >
                                                        年度：
                                                        <select type="text" id="yerly1" name="yerly1" class="browser-default custom-select"  value="{{ $queryData['yerly1'] }}" style="min-width: 80px; flex:0 1 auto">
                                                            <option></option>
                                                            @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                                                                <option value="{{$i}}" {{ $queryData['yerly1'] == $i? 'selected' : '' }} >{{$i}}

                                                                </option>
                                                            @endfor
                                                        </select>
                                                        &nbsp;年 ~ &nbsp;
                                                        <select type="text" id="yerly2" name="yerly2" class="browser-default custom-select"  value="{{ $queryData['yerly2'] }}" style="min-width: 80px; flex:0 1 auto">
                                                            <option></option>
                                                            @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                                                                <option value="{{$i}}" {{ $queryData['yerly2'] == $i? 'selected' : '' }} >{{$i}}

                                                                </option>
                                                            @endfor
                                                        </select>
                                                        &nbsp;年&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <input type="radio" style="min-width: 30px;" id="year_or_day" name="year_or_day" <?=($queryData['year_or_day']=='2')?'checked':'';?> value="2" >
                                                        日期區間：

                                                        <input type="text" id="sdate" name="sdate" class="form-control" autocomplete="off" value="{{ $queryData['sdate'] }}">
                                                        <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                        &nbsp; ~ &nbsp;

                                                        <input type="text" id="edate" name="edate" class="form-control" autocomplete="off" value="{{ $queryData['edate'] }}">
                                                        <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>

                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <!-- 班號 -->
                                                <div class="input-group col-6">
                                                     <div class="input-group-prepend">
                                                        <span class="input-group-text">班號</span>
                                                    </div>
                                                    <input type="text" id="class" name="class" class="form-control" autocomplete="off" value="{{ $queryData['class'] }}">
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ $queryData['name'] }}">
                                                </div>
                                            </div>
<!-- 進階/簡易搜尋開始 -->
                                            <div class="panel-group" id="accordion">
                                            <header class="panel-heading">

                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#search"> </a>

                                                </header>
                                                <footer id="search" class="panel-collapse collapse">
<!-- 進階/簡易搜尋開始 -->
                                            <!-- 班別名稱 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">期別</span>
                                                    </div>
                                                    <input type="text" id="term" name="term" class="form-control" autocomplete="off" value="{{ $queryData['term'] }}">
                                                </div>
                                                <!-- **分班名稱 -->
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">分班名稱</span>
                                                    </div>
                                                    <input type="text" id="class_branch_name" name="class_branch_name" class="form-control" autocomplete="off" value="{{ $queryData['class_branch_name'] }}">
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">課程名稱1</span>
                                                    </div>
                                                    <input type="text" id="class_name_1" name="class_name_1" class="form-control" autocomplete="off" value="{{ $queryData['class_name_1'] }}">
                                                </div>
                                            </div>
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">課程名稱2</span>
                                                    </div>
                                                    <input type="text" id="class_name_2" name="class_name_2" class="form-control" autocomplete="off" value="{{ $queryData['class_name_2'] }}">
                                                </div>
                                            </div>
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">課程名稱3</span>
                                                    </div>
                                                    <input type="text" id="class_name_3" name="class_name_3" class="form-control" autocomplete="off" value="{{ $queryData['class_name_3'] }}">
                                                </div>
                                            </div>
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">姓名</span>
                                                    </div>
                                                    <input type="text" id="teacher" name="teacher" class="form-control" autocomplete="off" value="{{ $queryData['teacher'] }}">
                                                </div>
                                                <!-- **分班名稱 -->
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">身分證字號</span>
                                                    </div>
                                                    <input type="text" id="idno" name="idno" class="form-control" autocomplete="off" value="{{ $queryData['idno'] }}">
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">專長領域</span>
                                                    </div>
                                                    <?php
                                                    $experience_list = DB::table('s01tb')
                                                    ->where('type', '=', 'B')
                                                    ->get();
                                                    ?>

                                                    <select class="select2 form-control select2-single input-max" id="experience" name="experience">
                                                                <option value="">請選擇</option>
                                                        <?php
                                                            foreach($experience_list as $row):
                                                             if(isset($queryData['experience']) && $queryData['experience']==$row->code)
                                                                 echo '<option value="'.$row->code.'" selected>'.$row->name.'</option>';
                                                             else
                                                                echo '<option value="'.$row->code.'">'.$row->name.'</option>';
                                                            endforeach;
                                                        ?>

                                                    </select>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">服務機關名稱</span>
                                                    </div>
                                                    <input type="text" id="dept" name="dept" class="form-control" autocomplete="off" value="{{ $queryData['dept'] }}">
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

                                            <div class="float-left">
                                                <!-- 查詢 -->
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                                <!-- 重設條件 -->
                                                <button type="button" class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()">重設條件</button>

                                                <a href="/admin/satisfaction/export?{{$_SERVER['QUERY_STRING']}}">
                                                    <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">下載execl檔</button>
                                                </a>

                                                <!-- <a href="/admin/satisfaction/exportOdf?{{$_SERVER['QUERY_STRING']}}">
                                                    <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">下載odf檔</button>
                                                </a> -->

                                                <a href="/admin/satisfaction/export2?{{$_SERVER['QUERY_STRING']}}">
                                                    <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">下載平均滿意度execl檔</button>
                                                </a>

                                                <!-- <a href="/admin/satisfaction/exportOdf2?{{$_SERVER['QUERY_STRING']}}">
                                                    <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">下載平均滿意度odf檔</button>
                                                </a> -->

                                            </div>
                                        </form>
                                    </div>


                                    <div class="table-responsive">
                                        <table id="data_table" class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>年度</th>
                                                <th>姓名</th>
                                                <th>服務單位</th>
                                                <th>職稱</th>
                                                <th>班別名稱</th>
                                                <th>分班名稱</th>
                                                <th>課程名稱</th>
                                                <th>時數</th>
                                                <th>滿意度</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td>{{ $va->yerly }}</td>
                                                    <td>{{ $va->cname }}</td>
                                                    <td>{{ $va->dept }}</td>
                                                    <td>{{ $va->position }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ $va->branchname }}</td>
                                                    <td>{{ $va->class_name }}</td>
                                                    <td>{{ $va->hour }}</td>
                                                    <td>{{ $va->okrate }}</td>

                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

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

@endsection

@section('js')
<script>
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

    });
    function doClear(){
      $('input[name="year_or_day"]')[0].checked = true;
      var today = new Date();
      var year = today.getFullYear();
      document.all.yerly1.value = year-1911;
      document.all.yerly2.value = year-1911;
      document.all.sdate.value = "";
      document.all.edate.value = "";
      document.all.class.value = "";
      document.all.name.value = "";
      document.all.class_branch_name.value = "";
      document.all.term.value = "";
      document.all.class_name_1.value = "";
      document.all.class_name_2.value = "";
      document.all.class_name_3.value = "";
      document.all.teacher.value = "";
      document.all.idno.value = "";
      document.all.experience.value = "";
      $("#experience").val('').trigger("change");
      document.all.dept.value = "";

    }
</script>
@endsection