@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'changetraining_error';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">班期調派訓異常統計表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">班期調派訓異常統計表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <form id="exportFile" method="post" id="search_form" action="/admin/changetraining_error/export" enctype="multipart/form-data">
                        {{ csrf_field() }}
                    <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-list pr-2"></i>班期調派訓異常統計表</h3>
                            </div>
                    </div>
                    <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"></i>兩區條件請選其一 (每次僅會套用一區的條件)</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">

                                            <div class="form-group row align-items-center">
                                                <input class="col-1 pt-2 float-right" type="radio" id="card1" name="cardselect" value="1" checked>
                                                <label class="col-2">第一區條件<span class="text-danger"></span></label>    
                                            </div>
                                            <!--  variable declare  --> 
                                            <?php $sdatetw =''; $edatetw =''; ?>
                                            <div class="form-group row align-items-center">
                                                <!--  datepicker -->
                                                <label class="col-1">起始日期<span class="text-danger"></span></label>
                                                <input type="text" class="form-control col-2 mr=1" value="{{$sdatetw}}" id="sdatetw" name="sdatetw"  id="sdatetw" min="1" readonly autocomplete="off">
                                                <label class="col-1">結束日期<span class="text-danger"></span></label>
                                                <input type="text" class="form-control col-2" value="{{$edatetw}}" id="edatetw" name="edatetw"  id="edatetw" min="1" readonly autocomplete="off">
                                            </div>
                                            <div class="form-group row align-items-center">

                                                <label class="col-sm-1">班別性質<span class="text-danger"></span></label>
                                                <div class="col-sm-3 input-max width">
                                                    <select class="select2 form-control select2-single " id="classtype" name="classtype">
                                                        <option value="0">請選擇班別性質</option>
                                                        @foreach ($class as $class)
                                                            <option value="{{$class->value}}">{{$class->value}}{{$class->text}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row align-items-center">
                                                <input class="col-sm-1 pt-2 float-right" type="radio" id="outputtype1" name="outputtype" value="1" checked>
                                                <label class="col-sm-1">異常統計<span class="text-danger"></span></label>    
                                                <input class="col-sm-1 pt-2 float-right" type="radio" id="outputtype2" name="outputtype" value="2" >
                                                <label class="col-sm-1">實到統計<span class="text-danger"></span></label>    
                                                <input class="col-sm-1 pt-2 float-right" type="radio" id="outputtype3" name="outputtype" value="3" >
                                                <label class="col-sm-3">各機關請假及未到訓統計<span class="text-danger"></span></label>    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    </div>
                    <div class="card">
                            <div class="card-header">
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <input class="col-sm-1 pt-2 float-right" type="radio" id="card2" name="cardselect" value="2"">
                                            <label class="col-sm-2">第二區條件<span class="text-danger"></span></label>    
                                        </div>
                                                <!-- 班別 -->
                                                <div class="form-group row">
                                                    <label class="col-sm-1">班別</label>
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
                                                <!-- 期別 -->                        
                                                <div class="form-group row">
                                                    <label class="col-sm-1">期別</label>
                                                    <div class="col-sm-2">
                                                        <div class="input-group bootstrap-touchspin number_box">
                                                        <select id="terms" name="terms" class="select2 form-control select2-single input-max" >
                                                            <?php foreach ($termArr as $key => $va) { ?>
                                                                <option value='<?=$va->term?>'><?=$va->term?></option>
                                                            <?php } ?>
                                                            </select>
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
            url:"/admin/changetraining_error/getTerms",
            data: { classes: $('#classes').val()},
            success: function(data){
            let dataArr = JSON.parse(data);
            let tempHTML = "";
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
    

</script>
@endsection