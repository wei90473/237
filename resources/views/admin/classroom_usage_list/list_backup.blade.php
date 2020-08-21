@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'classroom_usage_list';?>

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
                                <form method="post" action="/admin/classroom_usage_list/export" id="search_form">
                                        {{ csrf_field() }}

                                                <div class="form-group row">
                                                    <label class="col-sm-1 control-label text-md-right pt-2">班別</label>
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
                                                    <label class="col-1 control-label text-md-right pt-2">期別</label>
                                                    <div class="col-3">
                                                        <div class="input-group bootstrap-touchspin number_box">
                                                            <select id="terms" name="terms" class="select2 form-control select2-single input-max" >
                                                            <?php foreach ($termArr as $key => $va) { ?>
                                                                <option value='<?=$va->term?>'><?=$va->term?></option>
                                                            <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php $sdatetw =''; $edatetw =''; ?>
                                                    <div class="form-group row align-items-center">
                                                        <!--  datepicker -->
                                                        <label class="col-1">起始日期<span class="text-danger"></span></label>
                                                        <input type="text" class="form-control col-2 mr=1" value="{{$sdatetw}}" id="sdatetw" name="sdatetw"  id="sdatetw" min="1" readonly autocomplete="off">
                                                        <label class="col-1">結束日期<span class="text-danger"></span></label>
                                                        <input type="text" class="form-control col-2 " value="{{$edatetw}}" id="edatetw" name="edatetw"  id="edatetw" min="1" readonly autocomplete="off">
                                                    </div>
                                                

                                        <div class="form-group row align-items-center">
                                            <label class="col-2 pl-2 pr-2" >  </label>
                                            <label class="pl-2 pr-2" ><input type="radio" name="area" id="taipei" value="1" checked >台北院區</label>
                                            <label class="pl-2 pr-2" ><input type="radio" name="area" id="nantou" value="2">南投院區</label>
                                            <label class="pl-2 pr-2" ><input type="radio" name="area" id="allarea" value="3" >全部</label>
                                                 
                                        </div>

                                        <div class="form-group row">
                                        <label class="col-2 control-label text-md-right pt-2">教室</label>
                                        <div class="col-3">
                                            <div class="input-group bootstrap-touchspin number_box">
                                                <select id="site" name="site" class="select2 form-control select2-single input-max" onchange="setseat();" required>
                                                <option value='0'>請選擇教室</option>
                                                <?php foreach ($siteArr as $key => $va) { ?>
                                                    <option value='<?=$va->site?>'><?=$va->site?></option>
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

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<!--  src of datepicker  --> 
<script src="/backend/assets/js/bootstrap-datepicker.js"></script>

<script>

//  call datepicker
    $( function() {
    
        $('#sdatetw').datepicker({
            format: "twy-mm-dd",
        });
        $('#edatetw').datepicker({
            format: "twy-mm-dd",
        });
    } );

    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

    function getTerms()	{
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/classroom_usage_list/getTerms",
            data: { classes: $('#classes').val()},
            success: function(data){
            let dataArr = JSON.parse(data);
            let tempHTML = "";
            // console.log(dataArr);
            for(let i=0; i<dataArr.length; i++) 
            {
                tempHTML += "<option value='"+dataArr[i].term+"'>"+dataArr[i].term+"</option>";                     
            }
            $("#terms").html(tempHTML);
            },
            error: function() {
                console.log('Ajax Error');
            }
        });
    };

    function getSites()	{
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/classroom_usage_list/getSites",
            data: { classes: $('#classes').val(),term: $('#term').val()},
            success: function(data){
            let dataArr = JSON.parse(data);
            let tempHTML = "";
            for(let i=0; i<dataArr.length; i++) 
            {
                tempHTML += "<option value='"+dataArr[i].site+"'>"+dataArr[i].site+" "+dataArr[i].name+"</option>";                     
            }
            $("#site").html(tempHTML);
            setseat();
            },
            error: function() {
                console.log('Ajax Error');
            }
        });
	};

</script>
@endsection