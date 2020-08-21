@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'teacher_information';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講師基本資料</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講師基本資料</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講師基本資料</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <form id="exportFile" method="post" id="search_form" action="/admin/teacher_information/export" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                                <!-- 班別 -->
                                                <div  class="form-group row align-items-center justify-content-start">
                                                    <label class="text-md-right">輸入匯出條件(三選一)</label>
                                                </div>
                                                <div class="form-group row align-items-center justify-content-start">
                                                    <div>
                                                        <input type="radio" name="type" id="type1" value="1" checked>
                                                        <label >班別</label>
                                                    </div>
                                                    <div class="col-6 input-group bootstrap-touchspin number_box">
                                                        <select id="classes" name="classes" class="select2 form-control select2-single input-max" onchange="getTerms();">
                                                            <option value="">請選擇</option>
                                                            <?php foreach ($classArr as $key => $va) { ?>
                                                                <option value='<?=$va->class?>'><?=$va->class?>-<?=$va->name?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label>期別</label>            
                                                    </div>
                                                    <div class="col-2 input-group bootstrap-touchspin number_box">
                                                        <select id="term" name="term" class="select2 form-control select2-single input-max" >
                                                            <?php foreach ($termArr as $key => $va) { ?>
                                                                <option value='<?=$va->term?>'><?=$va->term?></option>
                                                            <?php } ?>
                                                        </select>     
                                                    </div>
                                                </div>


                                                <div class="form-group row align-items-center">
                                                    <div>
                                                    <input type="radio" name="type" id="type2" value="2">
                                                    <label >講座姓名：</label>
                                                    </div>
                                                    <input type="text" class="form-control col-2" id="name" name="name" min="2" value="" input-max autocomplete="off" >
                                                </div>

                                                <div class="form-group row align-items-center">
                                                    <div>
                                                        <input type="radio" name="type" id="type3" value="3">
                                                        <label >空白表格：</label>
                                                    </div>
                                                    <div style="border:1px black solid;">
                                                        <label class="pl-2 pr-2" ><input  type="radio" name="formtype" id="formtype1" value="1">講座</label>
                                                            
                                                        <label class="pl-2 pr-2" ><input type="radio" name="formtype" id="formtype2" value="2">助理</label>
                                                        
                                                    </div>            
                                                </div>

                                                <div class="form-group row align-items-center">
                                                    <div>
                                                        <input type="radio" name="type" id="type4" value="4">
                                                        <label >個資授權書</label>
                                                    </div>
                                                </div>
                                                <div id="dvdoctype" class="form-group row  align-items-center">
                                                    <label class="col-2 text-right">請選檔案格式：</label>
                                                    <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                                    <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                                </div>
                                                <div class="form-group row col-4 align-items-center justify-content-center">
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

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script>
    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

    function getTerms()	{
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/teacher_information/getTerms",
            data: { classes: $('#classes').val()},
            success: function(data){
            let dataArr = JSON.parse(data);
            let tempHTML = "";
            for(let i=0; i<dataArr.length; i++) 
            {
                tempHTML += "<option value='"+dataArr[i].term+"'>"+dataArr[i].term+"</option>";                     
            }
            $("#term").html(tempHTML);
            $("#term").html(tempHTML);	
            },
            error: function() {
                console.log('Ajax Error');
            }
        });
	};
</script>
@endsection