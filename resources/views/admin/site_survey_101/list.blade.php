@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'site_survey_101';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地問卷及統計表(101)</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">場地問卷及統計表(101)</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>場地問卷及統計表(101)</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float col-12">
                                        <form method="post" id="search_form" action="/admin/site_survey_101/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                            <div class="card">
                                                        <div class="card-header">
                                                            <h4 class="card-title"></i>輸入列印條件</h4>
                                                        </div>

                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    
                                                                    <div class="form-group row">
                                                                        <input class="col-sm-1 pt-2 float-right" type="radio" id="questionnaire" name="radiotype" value="1" checked>
                                                                        <label class="col-sm-2">空白問卷<span class="text-danger"></span></label>    
                                                                    </div>
                                                                    <hr style="border-top: 1px solid #d4d4d4;height: 0px;">
                                                                    <div class="form-group row">
                                                                        <input class="col-sm-1 control-label text-md-right pt-2" type="radio" id="Statistical" name="radiotype" value="2" >
                                                                        <label class="col-sm-1">統計表<span class="text-danger"></span></label>
                                                                    </div>
                                                                    <!-- 年度 -->
                                                                    <div class="form-group row">
                                                                        <label class="col-sm-2 control-label text-md-right pt-2">年度<span class="text-danger"></span></label>
                                                                        <div class="col-sm-10">
                                                                            <div class="input-group bootstrap-touchspin number_box">
                                                                                <!-- 輸入欄位 -->
                                                                                <input type="text" class="form-control input-max" id="yerly" name="yerly" min="1" placeholder="請輸入年度" 
                                                                                value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" onchange="getTimeBySite();">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- 第幾次調查 -->
                                                                    <div class="form-group row">
                                                                        <label class="col-sm-2 control-label text-md-right pt-2">第幾次調查</label>
                                                                        <div class="col-sm-10">
                                                                            <div class="input-group bootstrap-touchspin number_box">
                                                                                <select id="times" name="times" class="select2 form-control select2-single input-max">
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>                                          
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                            </div>
                                            <div id="dvdoctype" class="form-group row  align-items-center">
                                                    <label class="col-2 text-right">請選檔案格式：</label>
                                                    <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                                    <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                            </div>
                                            <div align="center">
                                                <button type="button" class="btn mobile-100" onclick="submitSearch();"><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
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
    </div>

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script>  
    function getTimeBySite()
    {
        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/site_survey_101/getTimeBySite',
            data: {yerly: $('#yerly').val()},
            success: function(response){
                let dataArr =JSON.parse(response);
                let tempHTML = "";
                tempHTML +="<option value=''>請選擇第幾次調查</option>";
                for(let i=0; i<dataArr.length; i++) {
                    tempHTML += "<option value='"+dataArr[i].times+"'>"+dataArr[i].times+"</option>";
                }
                $("#times").html(tempHTML);
            },
            error: function(){
                alert('Ajax Error');
            }
        })
    }  

    function submitSearch() {
        var radiotype = document.querySelector('input[name = radiotype]:checked').value;
        if((radiotype != "2") && ($("#yerly").val() != "" && $("#times").val() != "" )) {
            alert("匯出條件(二選一)");
            return;        
        }
        else {
            if((radiotype == "2") && ($("#yerly").val() == "" || $("#yerly").val() == null || $("#yerly").val() == undefined)) {
                alert("請輸入年度");
                return;
            } else {
                if((radiotype == "2") && ($("#times").val() == "" || $("#times").val() == null || $("#times").val() == undefined )) {                    
                    alert("請選擇第幾次調查");
                    return;            
                } else {
                    $("#search_form").submit();
                }
            }
        }

    }   
</script>
@endsection