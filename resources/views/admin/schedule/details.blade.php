@extends('admin.layouts.layouts')
@section('content')
    <style>
    .bootstrap-table .fixed-table-container .table thead th {
        vertical-align: middle !important;
    }    
    .back_blue{
        background-color: #5555FF !important;
    }
    .back_pink{
        background-color: #FF3EFF !important;
    }
    table th{background-color: #FFF; color:#000}
    /* table{ word-break:keep-all; } */

    </style>
    <?php $_menu = 'schedule';?>
    <link href="/backend/plugins/bootstrap-fixed-table/css/bootstrap-table.min.css" rel="stylesheet">
    <link href="/backend/plugins/bootstrap-fixed-table/css/bootstrap-table-fixed-columns.min.css" rel="stylesheet">

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">排程明細</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/schedule">訓練排程處理</a></li>
                        <li class="active">排程明細</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>排程明細</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">										
										
                                            <!-- 年度 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">月份</span>
                                                    </div>

                                                    <select class="select2 form-control select2-single input-max" name="yerly">
                                                        @for($year=(int)date('Y')-1911; $year >= 90; $year--)
                                                            <option
                                                            {{ ($queryData['yerly'] == $year) ? 'selected' : '' }}value="{{ $year }}"                                                            
                                                            >{{ $year }}</option>
                                                        @endfor
                                                    </select>
                                                    &nbsp
                                                    <select class="form-control select2-single input-max" name="s_month">
                                                        @for($month=1;$month<=12;$month++)
                                                            <option
                                                            @if($queryData['s_month'] == $month)
                                                            selected
                                                            @endif
                                                            value="{{ $month }}"                                                            
                                                            >{{ $month }}</option>
                                                        @endfor
                                                    </select>
                                                    <label for="">~</label>
                                                    <select class="form-control select2-single input-max" name="e_month">
                                                        @for($month=1;$month<=12;$month++)
                                                            <option
                                                            @if($queryData['e_month'] == $month)
                                                            selected
                                                            @endif
                                                            value="{{ $month }}"
                                                            >{{ $month }}</option>
                                                        @endfor
                                                    </select>
                                                    
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">顯示刻度：</span>
                                                        <input type="radio" name="weekday" class="mt-2" style="min-width:20px; margin-left:5px;" value="week" @if($queryData['weekday'] == "week") checked @endif>週
                                                        <input type="radio" name="weekday" class="mt-2" style="min-width:20px; margin-left:5px;" value="day" @if($queryData['weekday'] == "day") checked @endif>天
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            
                                            {{-- <!-- 排序 -->
                                            <!-- <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}"> -->
                                            <!-- <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">  -->
                                            <!-- 每頁幾筆 -->
                                            {{-- <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}"> --}}

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                            </form>        
                                            <br/>
                                            
                                            {!! Form::open([ 'method'=>'PUT', 'url'=>"/admin/schedule/updateBydetail", "id"=>"edit_form"]) !!}
                                            
                                            <!-- 年度 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別</span>
                                                    </div>
                                                    <select class="select2 form-control select2-single input-max" name="class" onchange="queryT01tb(this.value)">
                                                        <option>請選擇</option>
                                                        @foreach ($t01tbs as $class => $name)
                                                        <option value="{{ $class }}">{{ $class.' '.$name }}</option>
                                                        @endforeach 
                                                    </select>
                                                    
                                                    <div class="input-group-prepend ml-2">
                                                        <span class="input-group-text">期別</span>
                                                    </div>
                                                    <select class="select2 form-control select2-single input-max" name="term" >

                                                    </select>
                                                    
                                                    <div class="input-group-prepend ml-2">
                                                        <span class="input-group-text">開課日期</span>
                                                    </div>
                                                    <!-- <select class="select2 form-control select2-single input-max" name="sdate"></select> -->
                                                    <input type="text" id="sdate" name="sdate" class="form-control input-max" autocomplete="off" value="" onblur="computeEdate()">
                                                    <label for="">~</label>
                                                    <input type="text" id="edate" name="edate" class="form-control input-max" autocomplete="off" value="" readonly>
                                                </div>
                                            </div>
                                            {!! Form::close() !!}
                                        
                                    </div>

                                    <div class="schedule">
                                        <table id="table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" rowspan="3">班別</th>
                                                    <th class="text-center" rowspan="3">
                                                    @for($i=0; $i<20; $i++) &nbsp @endfor
                                                    班號
                                                    @for($i=0; $i<20; $i++) &nbsp @endfor
                                                    </th>
                                                    <th class="text-center" rowspan="3">期數</th>
                                                    <th class="text-center" rowspan="3">人次</th>
                                                    <th class="text-center" colspan="{{ $dates->collapse()->count() }}">一百零九年</th>                             
                                                </tr>
                                                <tr>
                                                    @foreach($dates as $month => $date)
                                                    <th class="text-center" colspan="{{ count($date) }}">{{ $month }}</th>                                                    
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    @foreach($dates as $month => $weeks)
                                                        @foreach ($weeks as $week => $date)
                                                        <th class="text-center">{{ $date }}</th>
                                                        @endforeach
                                                    @endforeach
                                                </tr>                                                
                                            </thead>
                                            <tbody>
                                            <!-- 合計 -->
                                            <tr class="{{ $class }}">
                                                <td>合計</td>
                                                <td class="1"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                @if ($queryData['weekday'] == "week")
                                                    @foreach ($total as $week => $quota)
                                                        <td class="text-center">
                                                        {{ $quota }}
                                                        </td>
                                                    @endforeach
                                                @elseif ($queryData['weekday'] == "day")
                                                    @foreach ($total as $month => $days)
                                                        @foreach($days as $quota)
                                                        <td class="text-center">
                                                        {{ $quota }}
                                                        </td>
                                                        @endforeach 
                                                    @endforeach
                                                @endif 
                                            </tr>                                            
                                            <!-- 合計 -->
                                            @if (!empty($calendars))                                    
                                                @foreach ($calendars as $class => $calendar_info)
                                                    <?php $n_td = 0 ?>
                                                    <tr class="{{ $class }}">
                                                        <td>{{ $class }}</td>
                                                        <td class="1">{{ $calendar_info['name'] }}</td>
                                                        <td class="text-center">{{ $calendar_info['term_num'] }}</td>
                                                        <td class="text-center">{{ $calendar_info['total_quota'] }}</td>
                                                        @if ($queryData['weekday'] == "week")
                                                            @foreach ($calendar_info['calendar'] as $week => $week_info)
                                                                <td class="text-center {{ $class }}_row {{ ($week_info['is_class']) ? 'back_blue' : '' }} {{ (count($week_info['term']) > 1) ? 'back_pink' : '' }} @foreach($week_info['term'] as $term) {{ $class.'_'.$term }} @endforeach" 
                                                                data-class="{{ $class }}"
                                                                data-position = '{{ $n_td++ }}'
                                                                {{ (count($week_info['term']) == 1 && $week_info['is_class']) ? 'data-term='.$week_info['term'][0] : '' }}
                                                                >
                                                                {{ ($week_info['quota'] == 0) ? '' : $week_info['quota'] }}
                                                                </td>
                                                            @endforeach
                                                        @elseif ($queryData['weekday'] == "day")
                                                            @foreach ($calendar_info['calendar'] as $month => $days)
                                                                @foreach($days as $day_info)
                                                                <td class="text-center {{ $class }}_row {{ ($day_info['is_class']) ? 'back_blue' : '' }} {{ (count($day_info['term']) > 1) ? 'back_pink' : '' }} @foreach($day_info['term'] as $term) {{ $class.'_'.$term }} @endforeach " 
                                                                data-class="{{ $class }}"
                                                                {{ (count($day_info['term']) == 1 && $day_info['is_class']) ? 'data-term='.$day_info['term'][0] : '' }}
                                                                data-position = '{{ $n_td++ }}'
                                                                >
                                                                {{ ($day_info['quota'] == 0) ? '' : $day_info['quota'] }}
                                                                </td>
                                                                @endforeach 
                                                            @endforeach
                                                        @endif 
                                                    </tr>
                                                    
                                                @endforeach 
                                            @endif 
                                            </tbody>                                        
                                        </table>
                                    </div>

                                    <div>
                                        <!-- <button type="button" name="edit" class="btn btn-sm btn-info" onclick="actionEdit()">修改</button> -->
                                        <button type="button" name="save" class="btn btn-sm btn-success" onclick="actionsSave()">儲存</button>
                                        <!-- <button type="button" name="cancel" class="btn btn-sm btn-danger" onclick="actionCancel()" disabled>取消</button> -->
                                    </div>

                                    
                                    <!-- 分頁 -->
                                    {{-- @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData]) --}}

                                </div>
                            </div>
                        </div>

                        <!-- 列表頁尾 -->
                        {{-- @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData]) --}}

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script src="/backend/plugins/bootstrap-fixed-table/js/bootstrap-table.min.js"></script>
<script src="/backend/plugins/bootstrap-fixed-table/js/bootstrap-table-fixed-columns.min.js"></script>

<script>
    var select = null;
    function actionEdit()
    {
        $("button[name=save]").attr("disabled", false);
        $("button[name=edit]").attr("disabled", true);
        $("button[name=cancel]").attr("disabled", false);       
    }

    function actionsSave()
    {
        $("#edit_form").submit();
    }

    function actionCancel()
    {
        $("button[name=save]").attr("disabled", true);
        $("button[name=edit]").attr("disabled", false);
        $("button[name=cancel]").attr("disabled", true);
        $("input[name=sdate]").val("");
        $("input[name=edate]").val("");
        $("select[name=term]").html("");
        $("select[name=class]").val("").trigger("change");
        clearColor();
    }

    function selectTd(td)
    {
        clearColor();
        td.style.backgroundColor = "#00DD00";
        select = td.dataset;
        actionEdit();
        console.log(select);
    }

    function clearColor()
    {
        select = null;
        $(".datatd").css("background-color","#FFF")
    }

    function queryT01tb(class_no){
        getTerms(class_no);
        $("select[name=term]").trigger('change');
    }


    function getTerms(class_no, start_month, end_month){
        let s_month = '{{ $queryData['s_month'] }}';
        let e_month = '{{ $queryData['e_month'] }}';
        let yerly = '{{ $queryData['yerly'] }}';

        if (class_no !== ""){
            $.ajax({
                url: "/admin/field/getData/t01tb/" + class_no + '?s_month=' + s_month + '&e_month=' + e_month + '&yerly=' + yerly,
                async: false
            }).done(function(t01tb) {
                var select_term = $("select[name=term]");
                select_term.html("");

                t01tb.t04tbs.map(function(t04tb){ 
                    select_term.append("<option value='" + t04tb.term + 
                    "' data-sdate='" + t04tb.sdate + 
                    "' data-edate='" + t04tb.edate + "'>"  + t04tb.term + "</option>");
                });
                
            });  
        }
        
    }    











</script>


<script>
  var lock = false;
  var $table = $('#table')

  function buildTable($el) {
    $el.bootstrapTable('destroy').bootstrapTable({
      height:  800,
    //   data: data,
      fixedColumns: true,
      fixedNumber: 4,
      onClickRow: function (arg1, e, field) {
        let dataset = e.children()[field].dataset;
        tdClick(dataset);
      }      
    })
  }

  function tdClick(dataset)
  {
    if (typeof(dataset.term) != 'undefined'){
        lock = true;
        $("select[name=class]").val(dataset.class).trigger('change');
        // getTerms(dataset.class);
        $("select[name=term]").val(dataset.term).trigger('change');
        lock = false;
    }      
  }

$(function() {
    buildTable($table);

    // $("select[name=class]").select2({
    //     language: 'zh-TW',
    //     width: '100%',
    //     // 最多字元限制
    //     maximumInputLength: 10,
    //     // 最少字元才觸發尋找, 0 不指定
    //     minimumInputLength: 0,
    //     // 當找不到可以使用輸入的文字
    //     // tags: true,
    //     placeholder: '請輸入名稱...',
    //     // AJAX 相關操作
    //     ajax: {
    //         url: '/admin/field/getData/t01tbs',
    //         type: 'get',
    //         // 要送出的資料
    //         data: function (params){
    //             // 在伺服器會得到一個 POST 'search' 
    //             return {
    //                 yerly: {{ $queryData['yerly'] }},
    //                 class_or_name: params.term,
    //                 page: params.page 
    //             };
    //         },
    //         processResults: function (data, params){
    //             console.log(data);
    //             // 一定要返回 results 物件
    //             return {
    //                 results: data,
    //                 // 可以啟用無線捲軸做分頁
    //                 pagination: {
    //                     more: true
    //                 }
    //             }
    //         }
    //     }
    // });

    $("select[name=term]").on('change', function() {
        let selected = $("option:selected", this);

        if (selected.length > 0){
            showTermInfo(selected);
            moveSdate(selected);
        }
    });


    function showTermInfo(selected){
        $("input[name=sdate]").val(selected.data().sdate);
        $("input[name=edate]").val(selected.data().edate);
    }

    function moveSdate(selected){
        if (lock == false){
            let td_position = $("." + $("select[name=class]").val() + '_' + $("select[name=term]").val());
            let tr_position = $("." + $("select[name=class]").val());

            if (td_position.length > 0){
                td_position = td_position[0].dataset.position;
                let width = 0;
                let tb_row = $(document.getElementsByClassName('fixed-table-body')).find("." + $("select[name=class]").val()  + "_row");
                console.log(td_position);
                for(let i=0; i<td_position; i++){
                    console.log(tb_row[i].getBoundingClientRect().width);
                    width += tb_row[i].getBoundingClientRect().width;
                }
                console.log(width);
                document.getElementsByClassName('fixed-table-body')[0].scrollLeft = width; 
                
            }

            if (tr_position.length > 0){
                tr_position = tr_position[0].dataset.index;
                let height = 0;
                for(let i=0; i<tr_position; i++){
                    height += $(document.getElementsByClassName('fixed-table-body')).find("tr")[i+3].getBoundingClientRect().height;
                }
                document.getElementsByClassName('fixed-table-body')[0].scrollTop = height;    
            } 
        }
    }

});

function computeEdate(){
    $.ajax({
        url: '/admin/schedule/computeAttendClassDate',
        type: 'get',
        data: {
            class: $('select[name=class]').val(), 
            term: $('select[name=term]').val(), 
            sdate: $('input[name=sdate]').val()
        }
    }).done(function(response){
        console.log(response);
        if (response.status == 1){
            $('input[name=edate]').val(response.edate);
        }else{
            alert(response.message);
        }
    });        
}
</script>
@endsection
