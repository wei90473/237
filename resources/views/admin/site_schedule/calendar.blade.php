@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'site_schedule';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">調整行事曆</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/site_schedule" class="text-info">洽借場地班期排程處理</a></li>
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
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">

                                                <!-- 年度 -->
                                                <div class="pull-left mobile-100 mr-1 mb-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">班別</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                            <select class="form-control select2 " name="class" id="class" onchange="getTerms(this.value)">
                                                                @foreach($list as $va)
                                                                    <option value="{{ $va['class'] }}"  {{ $queryData['class'] == $va['class']? 'selected' : '' }}>{{ $va['class'].'_'. $va['name'] }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 期別 -->
                                                <div class="pull-left mobile-100 mr-2 mb-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">期別</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                        <select class="form-control select2 " name="term" id="term">
                                                            @for($i=1;$i<9;$i++)
                                                                <option value="{{ str_pad($i,2,'0',STR_PAD_LEFT) }}" {{ $queryData['term'] == str_pad($i,2,'0',STR_PAD_LEFT)? 'selected' : '' }}>{{ str_pad($i,2,'0',STR_PAD_LEFT) }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    </div>
                                                </div>

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="table-responsive col-sm-8" style="border: 1px solid #dee2e6;padding: 5px">
                                    <table class="table table-bordered mb-0" id="tab">
                                        <thead>
                                            <tr class="text-center">
                                                <th>上課日期</th>
                                                <th>教室</th>
                                                <th>開課日期</th>
                                                <th>結束日期</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            @if(isset($calendarlist))
                                            @foreach($calendarlist as $value)
                                            <tr class="text-center" onclick="getclass({{$value->date}})" id="{{$value->date}}">
                                                <td>{{$value->date}}</td>
                                                <td>{{$value->site}}</td>
                                                <td>{{$value->sdate}}</td>
                                                <td>{{$value->edate}}</td>
                                            </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-4" >
                                    {!! Form::open([ 'method'=>'POST', 'url'=>"/admin/site_schedule/calendar/".$queryData['class'].$queryData['term'], 'id'=>'form']) !!}
                                    <input type="hidden" name="_method" value="POST">
                                    <input type="hidden" name="class" value="{{$queryData['class']}}">
                                    <input type="hidden" name="term" value="{{$queryData['term']}}">
                                    <input type="hidden" name="site" value="{{isset($calendarlist)?$calendarlist[0]->site :''}}">
                                    <input type="hidden" id="select" name="select" value="">
                                    <div style="border: 1px solid #dee2e6;padding: 5px">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label text-md-right">上課日期</label>
                                            <div class="col-md-8">
                                                <input type="text" id="date" name="date" class="form-control" autocomplete="off" value="">
                                                <!-- <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span> -->
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label text-md-right">人數</label>
                                            <div class="col-md-8">
                                                <input type="text" id="quota" name="quota" class="form-control" autocomplete="off" value="{{  old('quota', (isset($data[$queryData['class'].$queryData['term']]['quota']))? $data[$queryData['class'].$queryData['term']]['quota'] : '0') }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label text-md-right">班務人員</label>
                                            <div class="col-md-8">
                                                <?php $list = $base->getSponsor(); ?>
                                                <select id="sponsor" name="sponsor" class="select2 form-control select2-single input-max">
                                                    <option value="">請選擇</option>
                                                    @foreach($list as $key => $va)
                                                        <option value="{{ $key }}" {{ old('sponsor', (isset($data[$queryData['class'].$queryData['term']]['sponsor']))? $data[$queryData['class'].$queryData['term']]['sponsor']: '') == $key? 'selected' : '' }}>{{ $va }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label text-md-right">費用</label>
                                            <div class="col-md-8">
                                                <input type="text" id="fee" name="fee" class="form-control" autocomplete="off" value="{{  old('fee', (isset($data[$queryData['class'].$queryData['term']]['fee']))? $data[$queryData['class'].$queryData['term']]['fee']: '0') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div style="border: 1px solid #dee2e6;padding: 5px">
                                        <div class="form-group row" id="lineup" style="display: flex;">
                                            <label class="col-md-4 col-form-label text-md-right">師資陣容</label>
                                            <div class="col-md-8">
                                                @foreach(config('app.lineup') as $key => $va)
                                                    <input type="radio" name="lineup" value="{{ $key }}" {{ old('lineup', (isset($data[$queryData['class'].$queryData['term']]['lineup']))? $data[$queryData['class'].$queryData['term']]['lineup']: '') == $key? 'Checked' : '' }}>{{ $va }}
                                                @endforeach
                                            </div>
                                        </div>
                                    </div> -->
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
                                            <!-- <div class="col-md-6" align="center">
                                                <a href="/admin/schedule">
                                                    <button type="button" class="btn btn-sm btn-info"><i class="fa fa-reply pr-2"></i>離開</button>
                                                </a>
                                            </div> -->
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>

                                <div class="col-sm-6 pull-left">資料讀取完畢，共{{isset($calendarlist)? count($calendarlist):'0' }}筆</div>
                                <!-- <div class="col-sm-3">2019/11/26</div> -->
                                <!-- <div class="col-sm-3">上午 10:35</div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="/admin/site_schedule">
                    <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                </a>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
var date = "";
$(document).ready(function() {
    $("#date").datepicker({   
        format: "twymmdd",
        language: 'zh-TW'
    });
});

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
function getclass(day){
    var tab=document.getElementById('tab');
        var rows=tab.rows;
        var rlen=rows.length;
            for (var i = 1; i <rlen; i++) { //所有行清除
                rows[i].style.background='';
            }
    date = day;
    // console.log(day);
    $("#date").val(day);
    $("#"+day).css("background-color","#00BBFF");
}

function actionInsert(){ //新增
    $("tbody").find("tr").css("background-color", "#FFF");
    clearData(); 
    $("#save").removeAttr("disabled");
    $("#cancel").removeAttr("disabled");
    $("#edit").attr("disabled", true);
    $("#delete").attr("disabled", true);
    $("#quota").attr("disabled", true);
    $("#fee").attr("disabled", true);
    $("#sponsor").attr("disabled", true);
    //$("#lineup").css("display", "none");
    $("#form").attr("action","/admin/site_schedule/calendar");
    date = "";
}

function actionEdit(){
    
    if (date != ""){
        var selclass = $("input[name=class]").val();
        var term = $("input[name=term]").val();
        console.log(date);
        $("input[name=_method]").val("PUT");
        $("#select").removeAttr("disabled");
        $("input[name=select]").val(date);
        $("#save").removeAttr("disabled");
        $("#cancel").removeAttr("disabled");
        $("#insert").attr("disabled", true);
        $("#delete").attr("disabled", true);
        $("#form").attr("action","/admin/site_schedule/calendar/"+selclass+term);
    }else{
        alert('請選擇修改班期');
    }
}

function actionCancel(){
    $("tbody").find("tr").css("background-color", "#FFF");
    clearData();
    date = "";
    $("#select").attr("disabled", true);
    $("#edit").removeAttr("disabled");
    $("#insert").removeAttr("disabled");
    $("#delete").removeAttr("disabled");
    $("#cancel").attr("disabled", true);
    $("#save").attr("disabled", true);
    $("#quota").removeAttr("disabled");
    $("#fee").removeAttr("disabled");
    $("#sponsor").removeAttr("disabled");
   // $("#lineup").css("display", "flex");
}

function actionDelete(){
    if (date != ""){
        if (confirm("確定要刪除" + date +"的資料嗎")){
            $("input[name=_method]").val("DELETE");
            $("input[name=date]").val(date);
            
            $("#form").submit();
        }
    }else{
        alert('請選擇刪除班期');
    }
}

function clearData(){
    $("input[name=_method]").val("POST");
    $("input[name=date]").val("");
    $("input[name=quota]").val("");
    $("select[name=sponsor]").val("").trigger("change");
    $("select[name=fee]").val("").trigger("change"); 
   // $("select[name=lineup]").val("").trigger("change");
}

</script>
