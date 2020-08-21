@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'sendtraining_quota';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">名額分配表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">名額分配表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>名額分配表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                        <form id="exportFile" method="post" id="search_form" action="/admin/sendtraining_quota/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                            <!-- 班別 -->
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label text-md-right pt-2">班別</label>
                                                <div class="col-sm-10">
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
                                                <label class="col-sm-2 control-label text-md-right pt-2">期別</label>
                                                    <div class="col-2">
                                                        <select id="startterm" name="startterm" class="select2 form-control select2-single col-2" required>
                                                        <?php foreach ($termArr as $key => $va) { ?>
                                                            <option value='<?=$va->term?>'><?=$va->term?></option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                    <label align="center" class="mr-2 ml-2">~</label>
                                                    <div class="col-2">    
                                                        <select id="endterm" name="endterm" class="select2 form-control select2-single col-2" required>
                                                        <?php foreach ($termArr as $key => $va) { ?>
                                                            <option value='<?=$va->term?>'><?=$va->term?></option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                            </div>
                                            <div id="dvdoctype" class="form-group row  align-items-center">
                                                    <label class="col-2 text-right">請選檔案格式：</label>
                                                    <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                                    <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                            </div>
                                            <div class="form-group row col-9 justify-content-center">
                                                <div>
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
            url:"/admin/sendtraining_quota/getTerms",
            data: { classes: $('#classes').val()},
            success: function(data){
            let dataArr = JSON.parse(data);
            let tempHTML = "";
            for(let i=0; i<dataArr.length; i++) 
            {
                tempHTML += "<option value='"+dataArr[i].term+"'>"+dataArr[i].term+"</option>";                     
            }
            $("#endterm").html(tempHTML);
            $("#startterm").html(tempHTML);	
            },
            error: function() {
                console.log('Ajax Error');
            }
        });
	};

</script>
@endsection