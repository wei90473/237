﻿@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'course_teaching_material';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">開班教材清單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">開班教材清單</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>開班教材清單</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <!-- 搜尋 -->
                                    <div class="float-left search-float col-12">
                                        <form method="post" id="search_form" action="/admin/course_teaching_material/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                            <!-- 班別 -->
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label text-md-right pt-2">班別<span class="text-danger">*</span></label>
                                                <div class="col-sm-10">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <select id="class" name="class" class="select2 form-control select2-single input-max" required onchange="getTermByOP();">
                                                        <option value="">請選擇</option>
                                                        <?php foreach ($classArr as $key => $va) { ?>
                                                            <option value='<?=$va->class?>'><?=$va->class?>-<?=$va->name?></option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>                                         
                                            <!-- 期別 -->
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label text-md-right pt-2">期別<span class="text-danger">*</span></label>
                                                <div class="col-sm-10">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <select id="term" name="term" class="select2 form-control select2-single input-max" required >
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
                                                <button type="submit" class="btn mobile-100"><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
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
    //跳出訊息視窗
    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

    function getTermByOP()
    {
        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/course_teaching_material/getTermByOP',
            data: {class: $('#class').val()},
            success: function(response){
                let dataArr =JSON.parse(response);
                let tempHTML = "";
                tempHTML +="<option value=''>請選擇期別</option>";
                for(let i=0; i<dataArr.length; i++) {
                    tempHTML += "<option value='"+dataArr[i].term+"'>"+dataArr[i].term+"</option>";
                }
                $("#term").html(tempHTML);
            },
            error: function(){
                alert('Ajax Error');
            }
        })
    }
</script>
@endsection