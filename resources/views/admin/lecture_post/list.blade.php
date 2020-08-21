@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'lecture_post';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座郵寄名條</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座郵寄名條
                        </li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座郵寄名條
                            </h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                <form id="exportFile" method="post" id="search_form" action="/admin/lecture_post/export" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group row justify-content-left align-items-center">
                                    <input class="float-right" type="radio" id="condition1" name="condition" value="1" checked>
                                    <label class="col-sm-1 text-danger text-left"><span class="text-danger">依班期</span></label>    
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-body">
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
                                                <label class="col-sm-2 control-label text-md-right pt-2">期別</label>
                                                <div class="col-3">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <select id="term" name="term" class="select2 form-control select2-single input-max" required>
                                                        <option value='0'>請選擇期別</option>
                                                        <?php foreach ($termArr as $key => $va) { ?>
                                                            <option value='<?=$va->term?>'><?=$va->term?></option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row justify-content-center align-items-center">
                                                <input class="pt-2 float-right" type="radio" id="outputtype1" name="outputtype"" value="1" checked>
                                                <label class="col-1">機關<span class="text-danger"></span></label>    
                                                <input class=" pt-2 float-right" type="radio" id="outputtype2" name="outputtype" value="2" >
                                                <label class="col-1">住家<span class="text-danger"></span></label>    
                                                <input class="pt-2 float-right" type="radio" id="outputtype3" name="outputtype" value="3" >
                                                <label class="col-6">郵寄<span class="text-danger"></span></label>    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-left align-items-center">
                                    <input class="float-right" type="radio" id="condition2" name="condition" value="2" >
                                    <label class="col-sm-2 text-danger text-left">依資料期間</span></label>    
                                </div>    
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-body">
                                            <?php $sdatetw =''; $edatetw =''; ?>
                                            <div class="form-group row align-items-center">
                                                <!--  datepicker -->
                                                <label class="col-sm-1">起始日期<span class="text-danger"></span></label>
                                                <input type="text" class="form-control col-2 mr=1" value="{{$sdatetw}}" id="sdatetw" name="sdatetw" placeholder="請選擇要查詢的日期" readonly  id="sdatetw" min="1" autocomplete="off">
                                                <label class="col-sm-1">結束日期<span class="text-danger"></span></label>
                                                <input type="text" class="form-control col-2" value="{{$edatetw}}" id="edatetw" name="edatetw" placeholder="請選擇要查詢的日期" readonly  id="edatetw" min="1" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="form-group row align-items-center">
                                        <label class="col-2 text-right pl-2 pr-2" > 請選則院區：</label>
                                        <label class="pl-2 pr-2" ><input type="radio" name="area" id="taipei" value="1" checked >台北院區</label>
                                        <label class="pl-2 pr-2" ><input type="radio" name="area" id="nantou" value="2">南投院區</label>
                                        <label class="pl-2 pr-2" ><input type="radio" name="area" id="allarea" value="3" >全部</label>
                                                    
                                    </div>
                                    <div id="dvdoctype" class="form-group row  align-items-center">
                                            <label class="col-2 text-right">請選檔案格式：</label>
                                            <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                            <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                    </div>
                                    <div class="form-group row justify-content-center">
                                        <div class="col-6">
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
<!--  src of datepicker  --> 
<script src="/backend/assets/js/bootstrap-datepicker.js"></script>
<script>
     if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

$( function() {
    
    $('#sdatetw').datepicker({
        format: "twy-mm-dd",
    });
    $('#edatetw').datepicker({
        format: "twy-mm-dd",
    });
} );

function getTerms()	{
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/lecture_signature/getTerms",
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