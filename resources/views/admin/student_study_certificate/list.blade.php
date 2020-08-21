@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'student_study_certificate';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">學員研習證書</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">學員研習證書</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員研習證書</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                <form id="exportFile" method="post" id="search_form" action="/admin/student_study_certificate/export" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                    <div class="form-group row align-items-center">
                                        <label class="col-3 control-label text-md-center pt-2">輸入列印條件</label>
                                    </div>
                                    <div class="form-group row align-items-center">
                                        <label class="col-2 control-label text-md-right  mb-1">班別</label>
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
                                    <div class="form-group row align-items-center">
                                        <label class="col-2 control-label text-md-right  mb-1">期別</label>
                                        <div class="col-3">
                                            <div class="input-group bootstrap-touchspin number_box">
                                                <select id="term" name="term" class="select2 form-control select2-single input-max" onchange="getserial();" required>
                                                <?php foreach ($termArr as $key => $va) { ?>
                                                    <option value='<?=$va->term?>'><?=$va->term?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="dvtype" class="form-group row justify-content-left align-items-center">
                                        <label class="col-6 text-right">右列選項請選其一(結業證書字號)：</label>
                                        <input class="mb-2 float-right" type="radio" id="type1" name="type" value="1" checked>
                                        <label class="col-1 text-left " id="label1">本學院</label>
                                        <input class="mb-2 float-right" type="radio" id="type2" name="type" value="2" >
                                        <label class="col-2 text-left" id="label2" >人事總處</label>    
                                    </div>

                                    <div class="form-group row align-items-center">
                                        <label class="col-2 control-label text-md-right mb-1">原用證號</label>
                                        <div class="col-4">
                                            <div class="input-group bootstrap-touchspin number_box">
                                                <input type="text" class="form-control input-max" id="sserial" name="sserial" min="2" placeholder="請先取得證號" value="{{$typev}}" autocomplete="off" readonly required>
                                                <!-- <label class="btn mobile-100 mb-3 ml-2 " onclick="getserial();" ><i class="fa fa-search fa-lg pr-1"></i>取得證號</label> -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row align-items-center">
                                        <label class="col-10 control-label text-md-right text-danger ">若選取證號與原用證號不同，將以本次選擇之證號列印，並複寫原有設定。</label>
                                        <!-- <div class="col-4">
                                            <div class="input-group bootstrap-touchspin number_box">
                                                <input type="text" class="form-control input-max" id="rserial" name="rserial" min="2" placeholder="請先取得證號" value="" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="8" autocomplete="off" required>
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="form-group row align-items-center">
                                    <label class="col-2 control-label text-md-right  mb-1">合作單位電子章
                                        </label> <input type="checkbox" name="signature_type[]" id="signature_type" value="0" />
                                        <div id="signature_type_div" style="display:none">
                                            上方單位：
                                            <select id="signature_type1" name="signature_type1" class="select2 form-control select2-single input-max">
                                            <option value="0">請選擇</option>
  
                                            </select>
                                            </br>
                                            下方單位：
                                            <select id="signature_type2" name="signature_type2" class="select2 form-control select2-single input-max">
                                            <option value="0">請選擇</option>
                                       
                                            </select>
                                        </div>
                                    </div>

                                    <div id="dvdoctype" class="form-group row  align-items-center">
                                        <label class="col-2 text-right">請選檔案格式：</label>
                                        <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                        <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
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
    $('#signature_type').change(function() {
        $('#signature_type_div').toggle();
        getSignature();
    });

    function getSignature()	{
        $.ajax({
            type: 'get',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/student_study_certificate/getSignature",
            success: function(data){
            let dataArr = JSON.parse(data);
            let tempHTML = "";
            for(let i=0; i<dataArr.data.length; i++) 
            {
                tempHTML += "<option value='"+dataArr.data[i].id+"'>"+dataArr.data[i].name+"</option>";                     
            }
            $("#signature_type1").html(tempHTML);
            $("#signature_type2").html(tempHTML);
            },
            error: function() {
                console.log('Ajax Error');
            }
        });
        getserial();
    };

    function getTerms()	{
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/student_study_certificate/getTerms",
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
        getserial();
    };
    
    function getserial(){
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/student_study_certificate/getserial",
            data: { classes: $('#classes').val(),term: $('#term').val()},
            success: function(data){

            let dataArr = JSON.parse(data);
            let tempHTML = "";

            switch (dataArr[0].diploma) {
                case '1':
                    tempHTML="本學院";
                    break;
                case '2':
                    tempHTML="人事總處";
                    break;  
                default:
                    tempHTML="尚無設定證號";
            }
            $("#sserial").val(tempHTML);
            //document.getElementById("sserial").innerHTML = tempHTML;  

            },
            error: function() {
                console.log('Ajax Error');
            }
        });
	};
</script>
@endsection