@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'conference_use_statistics';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">會議場地使用統計表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">會議場地使用統計表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <form method="post" action="/admin/conference_use_statistics/export" id="search_form">
                        {{ csrf_field() }}


                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></i>輸入列印條件(二選一)</h4>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">

                                        <div class="form-group row">
                                        <!-- 月 -->
                                            <input class="col-0 pt-2 float-right" type="radio" id="radiomonth" name="radiotype" value="1" checked onchange="yearchange()">
                                            <label class="col-2">每月統計表:<span class="text-danger"></span></label>
                                            <div class="form-group row">
                                                <label class="col-sm-1">起始日期<span class="text-danger"></span></label>
                                                
                                                    <input type="text" class="form-control col-sm-1" id="startYear" name="startYear" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3"  >
                                                    <p style="display: inline">年</p>

                                                    <input type="text" class="form-control col-sm-1" id="startMonth" name="startMonth" min="1"
                                                        value="1" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2">
                                                    <p style="display: inline">月</p>
                                                    <label class="col-sm-2">(YYYMM)<span class="text-danger"></span></label>
                                                
                                                
                                                <div class="w-100"></div>

                                                <label class="col-sm-1">結束日期<span class="text-danger"></span></label>
                                                
                                                    <input type="text" class="form-control col-sm-1" id="endYear" name="endYear" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3">
                                                    <p style="display: inline">年</p>
                                                    <input type="text" class="form-control col-sm-1" id="endMonth" name="endMonth" min="1"
                                                        value="12" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2">
                                                    <p style="display: inline">月</p>
                                                    <label class="col-sm-3">(YYYMM)<span class="text-danger"></span></label>
                                                 
                                            </div>
                                        </div>
                                        <hr style="border-top: 1px solid #d4d4d4;height: 0px;">    
                                        <!-- 年度 -->
                                        <div class="form-group row">
                                            <input class="col-0 pt-2 float-right" type="radio" id="radioyerly" name="radiotype" value="2"  >
                                            <label class="col-2">年度統計表:<span class="text-danger"></span></label>
                                            
                                             
                                                    <!-- 輸入欄位 -->
                                                    <input type="text" class="form-control col-sm-2" id="yerly" name="yerly" min="1" placeholder="請輸入年度" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3">
                                          
                                            
                                            <label class="col-1">(YYY)<span class="text-danger"></span></label>
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
                            <button type="button" class="btn mobile-100 mb-3 mr-1" onclick="submitSearch();"><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                        </div>
                    </form>
                 </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script>
    function yearchange(){
        $('#startMonth').val("1");
        $('#endMonth').val("12");
    }

    function submitSearch() {
        var radiotype = document.querySelector('input[name = radiotype]:checked').value;

        if((radiotype != "2") && ($("#startYear").val() == "" || $("#startYear").val() == null || $("#startYear").val() == undefined)) {
            alert("請輸入每月統計表的起始年度");
            return;        
        }
        if((radiotype != "2") && ($("#startMonth").val() == "" || $("#startMonth").val() == null || $("#startMonth").val() == undefined)) {
                alert("請輸入每月統計表的起始月份");
                return;        
        } 
        if((radiotype != "2") && ($("#endYear").val() == "" || $("#endYear").val() == null || $("#endYear").val() == undefined)) {
            alert("請輸入每月統計表的結束年度");
            return;        
        }
        if((radiotype != "2") && ($("#endMonth").val() == "" || $("#endMonth").val() == null || $("#endMonth").val() == undefined)) {
            alert("請輸入每月統計表的結束月份");
            return;        
        }         

        if((radiotype == "2") && ($("#yerly").val() == "" || $("#yerly").val() == null || $("#yerly").val() == undefined)) {
            alert("請輸入年度");
            return;
        } 
        $("#search_form").submit();


    }   
</script>
@endsection