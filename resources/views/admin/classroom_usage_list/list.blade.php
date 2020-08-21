@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'SiteGlance';?>

<style type="text/css">
    
    .queryLabel{
        width: 150px;
    }
</style>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">教室使用一覽表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">教室使用一覽表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>教室使用一覽表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                <form method="get" action="/admin/classroom_usage_list/export" id="search_form">
                                    <div class="form-group row">
                                        <label class="control-label text-md-right queryLabel">查詢方式</label>
                                        <div class="col-2">
                                            <label class="mr-3">
                                                <input type="radio" name="queryType" value="class" onchange="queryTypeChange()" checked>依班級
                                            </label>
                                            <label>
                                                <input type="radio" name="queryType" value="date" onchange="queryTypeChange()">依起迄日期
                                            </label>  
                                        </div>
                                    </div> 
                                    <div class="form-group row">
                                        <label class="control-label text-md-right pt-2 queryLabel">班別</label>
                                        <div class="col-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control queryClass" name="class" value="" readOnly required> 
                                                <button class="btn queryClass" type="button" style="zoom: 0.8;height: calc(2.25rem + 11px);margin-top:0px;border-radius: 0px;" onclick="$('#t01tb').modal('show')">...</button>
                                            </div>         
                                        </div>
                                        <label class="control-label text-md-right pt-2 queryLabel">班別名稱</label>
                                        <div class="col-4">
                                            <div class="input-group">
                                                <!-- <input type='text' class="form-control" name="classText" disabled> -->
                                                <input type="text" class="form-control" name="class_name" value="" disabled> 
                                            </div>         
                                        </div>                                        
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label text-md-right pt-2 queryLabel">期別</label>
                                        <div class="col-1">
                                            <!-- <input type="text" name="term" class="form-control queryClass"> -->
                                            <select name="term" class="custom-select queryClass" required>
                                                
                                            </select>
                                        </div>
<!--                                         <div class="col-1"></div>                                       
                                        <label class="control-label text-md-right pt-2 queryLabel">分班名稱</label>
                                        <div class="col-4">
                                            <input type="text" name="commission" class="form-control queryClass">
                                        </div>   -->                                      
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label text-md-right pt-2 queryLabel">在訓期間</label>
                                        <div class="input-group col-2">
                                            <input type="text" id="trainingSdate" name="trainingSdate" class="form-control queryDate" autocomplete="off" value="" required>
                                            <span class="input-group-addon" style="cursor: pointer;height: calc(2.25rem + 2px)" id="sdateDatepicker"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <div>
                                            <label class="col-sm-1 control-label text-md-right pt-2">至</label>
                                        </div>
                                        <div class="input-group col-2">
                                            <input type="text" id="trainingEdate" name="trainingEdate" class="form-control queryDate" autocomplete="off" value="" required>
                                            <span class="input-group-addon" style="cursor: pointer;height: calc(2.25rem + 2px)" id="edateDatepicker"><i class="fa fa-calendar"></i></span>
                                        </div>                                       
                                    </div>  


                                    <div class="form-group row">
                                        <label class="control-label text-md-right pt-2 queryLabel">辦班院區</label>
                                        <div class="col-2">
                                            <div class="input-group">
                                                <!-- <input type='text' class="form-control" name=""> -->
                                                <select class="custom-select" name="branch" onchange="changeSiteBranch()">
                                                    <option value="1">臺北院區</option>
                                                    <option value="2">南投院區</option>
                                                </select>
                                            </div>         
                                        </div>                                
                                    </div> 

                                    <div class="form-group row">
                                        <label class="control-label text-md-right pt-2 queryLabel">教室</label>

                                        <div class="col-2">
                                            <div class="input-group">
                                                <select class="custom-select" name="site">
<!--                                                     <option value="">請選擇</option>
                                                    @foreach ($classrooms['m14tb'] as $m14tb)
                                                        <option value="{{ $m14tb['site'] }}">{{ $m14tb['name'] }}</option>
                                                    @endforeach -->
                                                </select>
                                            </div>         
                                        </div>                                        
                                    </div> 
                                    <div class="form-group row">
                                        <label class="control-label text-md-right pt-2 queryLabel">講座</label>
                                        <div class="col-2">
                                            <div class="input-group">
                                                <input type='text' class="form-control" name="teacher_name" disabled>
                                                <input type='hidden' name="teacher_id">
                                                <button class="btn" type="button" style="zoom: 0.8;height: calc(2.25rem + 11px);margin-top:0px;border-radius: 0px;" onclick="showTeacherModol()">...</button>
                                            </div>         
                                        </div>
                                    </div> 
                                    <div class="form-group row">
                                        <label class="control-label text-md-right pt-2 queryLabel">課程名稱</label>
                                        <div class="col-2">
                                            <div class="input-group">
                                                <input type='text' class="form-control" name="course_name">
                                            </div>         
                                        </div>
                                    </div> 

                                    <div class="form-group row">
                                        <label class="control-label text-md-right queryLabel">請選檔案格式</label>
                                        <div class="col-2">
                                            <label class="mr-3">
                                                <input type="radio" name="doctype" value="xlsx" checked="">MS-DOC
                                            </label>
                                            <label>
                                                <input type="radio" name="doctype" value="ods">ODF
                                            </label>  
                                        </div>
                                    </div> 

                                    <div class="form-group row col-6 align-items-center justify-content-center">
                                        <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                        <label id="download"></label>
                                    </div>
                                </form>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@include('admin/selectModal/t01tb/modal')
@include('admin/selectModal/teacher/modal')

@endsection

@section('js')

<script>
var sites = JSON.parse('{!! json_encode($classrooms) !!}');
$(document).ready(function() {
    $("#trainingSdate").datepicker({
        format: "twymmdd",
        language: 'zh-TW'
    });
    $('#sdateDatepicker').click(function(){
        $("#trainingSdate").focus();
    });

    $("#trainingEdate").datepicker({
        format: "twymmdd",
        language: 'zh-TW'
    });
    $('#edateDatepicker').click(function(){
        $("#trainingEdate").focus();
    });    

    queryTypeChange();
    changeSiteBranch();
});

function chooseT01tb(class_no, class_name, terms){
    terms = terms.split(',');
    $("input[name=class]").val(class_no);
    $("input[name=class_name]").val(class_name);
    // $("input[name=classText]").val(class_no + ' ' + class_name);

    if (terms.length == 0){
        alert('找不到該班期別');
    }else{
        let termsHtml = '<option></option>';
        for(let i=0; i<terms.length; i++){
            termsHtml += "<option>" + terms[i] + "</option>";
        }

        $("select[name=term]").html(termsHtml);
        $("select[name=term]").attr('disabled', false);         
    }


}

function chooseTeacher(idno, name)
{
    $("input[name=teacher_id]").val(idno);
    $("input[name=teacher_name]").val(name);
}

function changeSiteBranch()
{
    branch = $("select[name=branch]").val();
    var html = '<option value="">請選擇</option>';
    if (branch == 1){
        changesites = sites.m14tb;
    }else if (branch == 2){
        changesites = sites.m25tb;
    }else{
        changesites = [];
    }

    for(let i=0; i<changesites.length; i++){
        html += '<option>' + changesites[i].name + '</option>';
    }

    $("select[name=site]").html(html);

}

function queryTypeChange()
{
    let type = $('input[name=queryType]:checked').val();
    if (type == 'class'){
        $(".queryDate").attr('disabled', true);
        $(".queryDate").val('');
        $(".queryClass").attr('disabled', false);
    }else if (type == 'date'){
        $("input[name=class_name]").val('');
        $(".queryClass").attr('disabled', true);
        $(".queryClass").val('');
        $(".queryDate").attr('disabled', false);
    }
}
</script>
@endsection
