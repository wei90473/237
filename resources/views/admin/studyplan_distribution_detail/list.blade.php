@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'studyplan_distribution_detail';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">報表列印 / 名額分配明細表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">報表列印</li>
                        <li class="active">名額分配明細表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>名額分配明細表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                        <form id="exportFile" method="post" id="search_form" action="/admin/studyplan_distribution_detail/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                            <!-- 年度 -->
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label text-md-right pt-2">年度<span class="text-danger"></span></label>
                                                <div class="col-sm-10">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <!-- 輸入欄位 -->
                                                        <input type="text" class="form-control input-max" id="yerly" name="yerly" min="1" placeholder="請輸入年度" 
                                                        value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" onchange="getTimes();">
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

                                            <div class="form-group row align-items-center">
                                                <label class="col-2 pl-2 pr-2 text-right" > 院區 </label>
                                                <label class="pl-2 pr-2" ><input type="radio" name="area" id="taipei" value="1" checked >台北院區</label>
                                                <label class="pl-2 pr-2" ><input type="radio" name="area" id="nantou" value="2">南投院區</label>
                                                <label class="pl-2 pr-2" ><input type="radio" name="area" id="allarea" value="3" >全部</label>
                                            </div>
                                            
                                            <hr style="border-top: 1px solid #d4d4d4;height: 0px;">
                                            <!-- 班別 -->
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label text-md-right pt-2">班別</label>
                                                <div class="col-sm-10">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <select id="classes" name="classes" class="select2 form-control select2-single input-max">
                                                            <option value="">請選擇</option>
                                                        <?php foreach ($classArr as $key => $va) { ?>
                                                            <option value='<?=$va->class?>'><?=$va->class?>-<?=$va->name?></option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="dvdoctype" class="form-group row  align-items-center">
                                                    <label class="col-2 text-right">請選檔案格式：</label>
                                                    <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                                    <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                            </div>
                                            <div align="center">
                                                <div>
                                                    <button type="button" class="btn mobile-100" onclick="submitSearch()"><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                                    <label id="download"></label>
                                                    
                                                </div>
                                                <label class="control-label text-md-right pt-2" style="font-size: 18px; color:red; ">輸入匯出條件(二選一)</label>
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

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script>
//跳出訊息視窗
    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

    function getTimes(){
        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/studyplan_distribution_detail/gettime',
            data: {yerly: $('#yerly').val()},
            success: function(response){
                let dataArr =JSON.parse(response);
                let tempHTML = "";
                for(let i=0; i<dataArr.length; i++) {
                    tempHTML += "<option value='"+dataArr[i].times+"'>"+dataArr[i].times+"</option>";          
                }
//                $("#classes").prop('selected', false).find('option:first').prop('selected', true);
//                $("#classes").innerText="選擇班別";
                $('#times').html(tempHTML);
            },
            error: function(){
                alert('Ajax Error');
            }
        });   
    }

    function submitSearch() {
        if((($("#yerly").val() == "" || $("#times").val() == "") && $("#classes").val() == "") || 
           ($("#yerly").val() != "" && $("#times").val() != "" && $("#classes").val() != "")) {
            alert("匯出條件(二選一)");
            return;
        }
        $("#exportFile").submit();
    }

</script>
@endsection