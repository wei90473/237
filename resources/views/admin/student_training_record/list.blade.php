@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'student_training_record';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">學員歷次受訓紀錄</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">學員歷次受訓紀錄</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員歷次受訓紀錄</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                <form method="post" id="search_form" action="/admin/student_training_record/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                            <div class="form-group row">
                                                <label class="col-2 control-label text-md-right pt-2">學員姓名</label>
                                                <div class="col-6">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <!-- 輸入欄位 -->
                                                        <input type="text" class="form-control input-max" id="sname" name="sname" min="2" placeholder="請輸入學員姓名" 
                                                        value="" autocomplete="off" required>
                                                        <label class="btn mobile-100 mb-3 ml-2 " onclick="getidno();" ><i class="fa fa-search fa-lg pr-1"></i>搜尋身分證字號</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row" style=" margin-top: -1rem;">
                                                <div class="col-2"></div>
                                                <div class="col-10">
                                                    <p style="color: #ef5350; font-size: 14px;">❊輸入姓名後，請點選「搜尋身分證字號」確認查詢人員是否有誤；若有同名同姓，請從身分證字號處點選下拉選單來找尋人員。</p>
                                                </div>
                                            </div>
                                            <div id="divterm" class="form-group row" style="visibility:visible">
                                                <label class="col-2 control-label text-md-right pt-2">身分證字號</label>
                                                <div class="col-10">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <select id="idno" name="idno" class="select2 form-control select2-single input-max" required>
                                                        <option value='0'>請先查詢身分證字號</option>
                                                        <?php foreach ($idnoArr as $key => $va) { ?>
                                                            <option value='<?=$va->idno?>'><?=$va->idno?> <?=$va->dept?> <?=$va->position?></option>
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
                                            <div class="form-group row col-8 justify-content-center" >
                                                <div>
                                                    <button type="submit" class="btn mobile-100"><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
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

    function getidno()	{
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/student_training_record/getidno",
            data: { sname: $('#sname').val()},
            success: function(data){
            let idnoArr = JSON.parse(data);
            let tempHTML ="";
            if(idnoArr.length==0)
            {
                tempHTML = "<option value='0'>查無此人</option>";
            }

            for(let i=0; i<idnoArr.length; i++) 
            {
                tempHTML += "<option value='"+idnoArr[i].idno+"'>"+idnoArr[i].idno+" "+idnoArr[i].dept+" "+idnoArr[i].position+"</option>";                     
            }
            $("#idno").html(tempHTML);
            },
            error: function() {
                console.log('Ajax Error');
            }
        });
	};


</script>
@endsection