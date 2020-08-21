@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<style>
.calendat_selected td{
    background-color: rgb(0, 187, 255);
}
/* .tablesorter-default tr:hover{
    background-color: rgb(0, 187, 255);
} */

.tablesorter-default tbody > tr.hover > td,
.tablesorter-default tbody > tr:hover > td,
.tablesorter-default tbody > tr.even:hover > td,
.tablesorter-default tbody > tr.odd:hover > td {
	background-color: #CCEEFF;
	color: #000;
}

</style>
    <?php $_menu = 'schedule';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">調整行事曆</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/schedule" class="text-info">訓練排程處理</a></li>
                        <li class="active">調整行事曆</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>調整行事曆</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float col-12">
                                        <form method="get" id="search_form">

                                            <!-- 年度 -->
                                            <div class="form-group row">
                                                <div class="form-group col-5">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">班別</span>
                                                        </div>
                                                        <select class="form-control" name="class" onchange="getTerms(this.value)">
                                                            @if (isset($t04tb))
                                                            <option value="{{ $t04tb->t01tb->class }}">{{ $t04tb->t01tb->name }}</option> 
                                                            @else
                                                            <option>請選擇</option>
                                                            @endif 
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- 月份 -->
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">期別</span>
                                                        </div>
                                                        <select class="form-control custom-select" name="term" style="min-width: 80px; flex:0 1 auto">
                                                            <option>請選擇</option>
                                                            @if (isset($t04tb))
                                                                @foreach($t04tb_terms as $term)
                                                                    <option value="{{ $term }}" {{($term == $t04tb->term) ? 'selected' : null }} >{{ $term }}</option>
                                                                @endforeach
                                                            @endif 
                                                        </select>
                                                    </div>
                                                </div>                    
                                                <div class="form-group col-3">
                                                    <div>
                                                        <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                                    </div>
                                                </div>                                
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="table-responsive col-sm-8" style="border: 1px solid #dee2e6;padding: 5px">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr class="text-center" >
                                                <th>上課日期</th>
                                                <th>教室</th>
                                                <th>開課日期</th>
                                                <th>結束日期</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($t04tb))
                                                @foreach($t04tb->t36tbs as $key => $t36tb)
                                                <tr id="tr{{$key}}" class="text-center" 
                                                    data-class="{{ $t36tb->class }}" 
                                                    data-term="{{ $t36tb->term }}" 
                                                    data-date="{{ $t36tb->date }}"
                                                    data-quota="{{ $t04tb->quota }}" 
                                                    data-sponsor="{{ $t04tb->sponsor }}"
                                                    data-section="{{ $t04tb->section }}"
                                                    data-site="{{ $t04tb->site }}"

                                                    onclick="selectRow(this)">
                                                    <td>{{ $t36tb->date }}</td>
                                                    <td>{{ $t36tb->site }}</td>
                                                    <td>{{ $t04tb->sdate }}</td>
                                                    <td>{{ $t04tb->edate }}</td>
                                                </tr> 
                                                @endforeach 
                                            @endif 
                                        </tbody>
                                    </table>
                                </div>
                                <!-- @if (isset($t04tb))
                                    {!! Form::open([ 'method'=>'put', 'url'=>"/admin/schedule/{$t04tb->class}/{$t04tb->term}", 'id'=>'form']) !!}
                                @else -->
                                    
                                <!-- @endif -->
                                
                                <div class="col-sm-4" >
                                @if (isset($t04tb))
                                    {!! Form::open([ 'method'=>'POST', 'url'=>"/admin/calendar/{$t04tb->class}/{$t04tb->term}", 'id'=>'form']) !!}
                                @endif
                                    <input type="hidden" name="_method" value="POST">
                                    <input type="hidden" name="origin_date" value="">

                                    <div style="border: 1px solid #dee2e6;padding: 5px">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label text-md-right">上課日期</label>
                                            <div class="col-md-8">
                                                <div class="input-group">                            
                                                    <input type="text" id="date" name="date" class="form-control input-max" autocomplete="off" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                </div>                                            
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label text-md-right">教室</label>
                                            <div class="col-md-8">
                                                <select class="form-control select2" name="site" disabled>
                                                    <option></option>
                                                    @foreach ($class_rooms['m14tb'] as $m14tb)
                                                    <option value="{{$m14tb['site']}}">{{$m14tb['name']}}</option>
                                                    @endforeach
                                                    @foreach ($class_rooms['m25tb'] as $m25tb)
                                                    <option value="{{$m25tb['site']}}">{{$m25tb['name']}}</option>
                                                    @endforeach                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label text-md-right">人數</label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control input-max" name="quota" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label text-md-right">辦班人員</label>
                                            <div class="col-md-8">
                                                <select class="form-control select2" name="sponsor">
                                                    <option>請選擇</option>
                                                    @foreach($sponsors as $sponsor)
                                                    <option value="{{$sponsor->userid}}">{{$sponsor->username}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label text-md-right">部門</label>
                                            <div class="col-md-8">
                                                <select class="form-control select2" name="section">
                                                    <option>請選擇</option>
                                                    @foreach($sections as $section)
                                                        <option value="{{ $section->section }}">{{ $section->section }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="border: 1px solid #dee2e6;padding: 5px">
                                        <div class="form-group row">
                                            <div class="col-md-6" align="center">
                                                <button type="button" id="insert" class="btn btn-sm btn-info" onclick="actionInsert()"><i class="fa fa-plus pr-2"></i>新增</button>
                                            </div>
                                            <div class="col-md-6" align="center">
                                                <button type="submit" id="save" class="btn btn-sm btn-success" disabled><i class="fa fa-save pr-2"></i>儲存</button>
                                            </div>
                                            <div class="col-md-6" align="center">
                                                <button type="button" id="edit" class="btn btn-sm btn-primary" onclick="actionEdit()">
                                                <i class="fa fa-pencil pr-2"></i>修改
                                                </button>
                                            </div>
                                            <div class="col-md-6" align="center">
                                                <button type="button" id="cancel" class="btn btn-sm btn-warning" onclick="actionCancel()" disabled><i class="fa fa-times pr-2" ></i>取消</button>
                                            </div>
                                            <div class="col-md-6" align="center">
                                                <button type="button" id="delete" class="btn btn-sm btn-danger" onclick="actionDelete()"><i class="fa fa-trash pr-2"></i>刪除</button>
                                            </div>
                                            <div class="col-md-6" align="center">
                                                <a href="/admin/schedule">
                                                    <button type="button" class="btn btn-sm btn-info"><i class="fa fa-reply pr-2"></i>離開</button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                                @if(!empty($t04tb))
                                <div class="col-sm-6 pull-left">資料讀取完畢，共 {{count($t04tb->t36tbs)}} 筆</div>
                                @endif 
                                <div class="col-sm-3">{{ date("Y/m/d") }}</div>
                                <div class="col-sm-3">{{ date("H:i") }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card-footer">
                <a href="/admin/schedule">
                    <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                </a>
            </div>

        </div>
    </div>

    <!-- 刪除確認視窗 -->
    <!-- include('admin/layouts/list/del_modol') -->

@endsection

@section('js')
<script type="text/javascript">
var select = "";
$(document).ready(function() {

    $("#date").datepicker({
        format: "twymmdd",
        language: 'zh-TW'
    });

    $('#datepicker1').click(function(){
        $("#date").focus();
    });
    searchClass();
});

function searchClass(){
    $("select[name=class]").select2({
        language: 'zh-TW',
        width: '100%',
        // 最多字元限制
        maximumInputLength: 10,
        // 最少字元才觸發尋找, 0 不指定
        minimumInputLength: 0,
        // 當找不到可以使用輸入的文字
        // tags: true,
        placeholder: '請輸入名稱...',
        // AJAX 相關操作
        ajax: {
            url: '/admin/field/getData/t01tbs',
            type: 'get',
            // 要送出的資料
            data: function (params){
                console.log(params);
                // 在伺服器會得到一個 POST 'search' 
                return {
                    class_or_name: params.term,
                    page: params.page 
                };
            },
            processResults: function (data, params){

                // 一定要返回 results 物件
                return {
                    results: data,
                    // 可以啟用無線捲軸做分頁
                    pagination: {
                        more: true
                    }
                }
            }
        }
    });    
}

function getTerms(class_no){
    $.ajax({
        url: "/admin/schedule/getTerms/" + class_no
    }).done(function(response) {
        console.log(response);
        var select_term = $("select[name=term]");
        select_term.html("");
        for(var i = 0; i<response.terms.length; i++){
            select_term.append("<option value='"+ response.terms[i] +"'>" + response.terms[i] + "</option>");
        }
    });  
}

function selectRow(row)
{
    $("tbody").find("tr").removeClass('calendat_selected');
    var tr = $("tbody").find("tr");
    select = row.id;
    $("#" + row.id).addClass('calendat_selected');
    // console.log(row.dataset.class);
}

function actionEdit()
{
    if (select != ""){
        $("input[name=_method]").val("PUT");
        var tr = document.getElementById(select);
        $("input[name=origin_date]").val(tr.dataset.date);
        $("input[name=date]").val(tr.dataset.date);
        $("input[name=quota]").val(tr.dataset.quota);
        $("select[name=site]").val(tr.dataset.site).trigger("change");
        $("select[name=sponsor]").val(tr.dataset.sponsor).trigger("change");
        $("select[name=section]").val(tr.dataset.section).trigger("change");
        $("#save").removeAttr("disabled");
        $("#cancel").removeAttr("disabled");
        $("#edit").attr("disabled", true);
        console.log(tr.dataset.class);
    }
}

function actionInsert()
{
    $("tbody").find("tr").css("background-color", "#FFF");
    clearData(); 
    $("#save").removeAttr("disabled");
    $("#cancel").removeAttr("disabled");
    $("#edit").removeAttr("disabled");
    $("#insert").attr("disabled", true);
    select = "";
}

function actionCancel()
{
    $("tbody").find("tr").css("background-color", "#FFF");
    clearData();
    select = "";
    $("#edit").removeAttr("disabled");
    $("#insert").removeAttr("disabled");
    $("#cancel").attr("disabled", true);
    $("#save").attr("disabled", true);
}

function actionDelete()
{
    if (select != ""){
        var tr = document.getElementById(select);
        if (confirm("確定要刪除" + tr.dataset.date +"的資料嗎")){
            $("input[name=_method]").val("DELETE");
            $("input[name=origin_date]").val(tr.dataset.date);
            $("#form").submit();
        }
    }
}

function clearData()
{
    $("input[name=_method]").val("POST");
    $("input[name=date]").val("");
    $("input[name=quota]").val("");
    $("input[name=origin_date]").val("");
    $("select[name=site]").val("").trigger("change");
    $("select[name=sponsor]").val("").trigger("change");
    $("select[name=section]").val("").trigger("change");    
}

</script>

@endsection