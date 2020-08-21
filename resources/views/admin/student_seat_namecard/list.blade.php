@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'student_seat_namecard';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">學員座位名牌卡</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">學員座位名牌卡</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員座位名牌卡</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                <form id="exportFile" method="post" id="search_form" action="/admin/student_seat_namecard/export" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">輸入列印條件</label>
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
                                                <select id="term" name="term" class="select2 form-control select2-single input-max" required>
                                                <?php foreach ($termArr as $key => $va) { ?>
                                                    <option value='<?=$va->term?>'><?=$va->term?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row justify-content-center">
                                        <input class="mb-2 float-right" type="radio" id="outputtype1" name="outputtype" value="1" checked>
                                        <label class="col-6 text-left">單位職稱姓名(大)</label>
                                    </div>
                                    <div class="form-group row justify-content-center">
                                        <input class="mb-2 float-right" type="radio" id="outputtype2" name="outputtype" value="2" >
                                        <label class="col-6 text-left">姓名職稱(大)</label>       
                                    </div>
                                    <div class="form-group row  justify-content-center">                    
                                        <input class="mb-2 float-right" type="radio" id="outputtype3" name="outputtype"" value="3" >
                                        <label class="col-6 text-left">姓名(大)</label>   
                                    </div>        
                                    <div class="form-group row  justify-content-center">
                                        <input class="mb-2 float-right" type="radio" id="outputtype4" name="outputtype" value="4" >
                                        <label class="col-6 text-left">學號姓名(小)</label>    
                                    </div>
                                    <div class="form-group row justify-content-center">
                                        <input class="mb-2 float-right" type="radio" id="outputtype5" name="outputtype" value="5" >
                                        <label class="col-6 text-left">班期姓名單位(大)</label>
                                    </div>
                                    <div class="form-group row justify-content-center">
                                        <input class="mb-2 float-right" type="radio" id="outputtype6" name="outputtype" value="6" >
                                        <label class="col-6 text-left">桌牌(學院)</label>       
                                    </div>
                                    <div class="form-group row justify-content-center">
                                        <input class="mb-2 float-right" type="radio" id="outputtype7" name="outputtype" value="7" >
                                        <label class="col-6 text-left">桌牌(人事總處)</label>       
                                    </div>
                                    <div class="form-group row  justify-content-center">                    
                                        <input class="mb-2 float-right" type="radio" id="outputtype8" name="outputtype"" value="8" >
                                        <label class="col-6 text-left">桌牌</label>   
                                    </div>        
                                    <div class="form-group row  justify-content-center">
                                        <input class="mb-4 float-right" type="radio" id="outputtype9" name="outputtype" value="9" >
                                        <label class="col-6 text-left">桌牌(A4)</label>    
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

    function getTerms()	{
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/student_seat_namecard/getTerms",
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