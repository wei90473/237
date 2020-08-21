﻿@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'student_grade';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">學員成績</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">學員成績</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員成績</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                <form id="exportFile" method="post" id="search_form" action="/admin/student_grade/export" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                    <div class="form-group row">
                                        <label class="col-3 control-label text-center pt-2">輸入列印條件</label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">班別</label>
                                        <div class="col-6">
                                             <div class="input-group bootstrap-touchspin number_box">
                                                <select id="classes" name="classes" class="select2 form-control select2-single input-max" onchange="getTerms();">
                                                    <option value="">請選擇</option>
                                                <?php foreach ($classArr as $key => $va) { ?>
                                                    <option value='<?=$va->class?>'><?=$va->class?>-<?=$va->name?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-2 control-label text-md-right pt-2">期別</label>
                                        <div class="col-3">
                                            <div class="input-group bootstrap-touchspin number_box">
                                                <select id="term" name="term" class="select2 form-control select2-single input-max"required>
                                                <?php foreach ($termArr as $key => $va) { ?>
                                                    <option value='<?=$va->term?>'><?=$va->term?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="dvtype" class="form-group row justify-content-left align-items-center">
                                        <label class="col-2 text-right">右列選項請選其一：</label>
                                        <input class="mb-2 float-right" type="radio" id="type1" name="type" value="1" checked>
                                        <label class="col-1 text-left " id="label1">成績明細</label>
                                        <input class="mb-2 float-right" type="radio" id="type2" name="type" value="2" >
                                        <label class="col-1 text-left" id="label2" >成績清冊</label>    
                                    </div>

                                    <div class="form-group row justify-content-center">
                                            <div class="col-6 ">
                                                <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                                <label id="download"></label>
                                            </div> 
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
            url:"/admin/student_grade/getTerms",
            data: { classes: $('#classes').val()},
            success: function(data){
            let dataArr = JSON.parse(data);
            let tempHTML = "";
            for(let i=0; i<dataArr.length; i++) 
            {
                tempHTML += "<option value='"+dataArr[i].term+"'>"+dataArr[i].term+"</option>";                     
            }
            $("#term").html(tempHTML);
            },
            error: function() {
                console.log('Ajax Error');
            }
        });

	};

</script>
@endsection