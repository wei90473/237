@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'yearly_income_detail';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">年度講座所得明細表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">年度講座所得明細表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>年度講座所得明細表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                <form method="post" id="search_form" action="/admin/yearly_income_detail/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                            <!-- 年度 -->
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label text-md-right pt-2">年度<span class="text-danger">*</span></label>
                                                <div class="col-sm-10">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <!-- 輸入欄位 -->
                                                        <input type="text" class="form-control input-max" id="yerly" name="yerly" min="1" placeholder="請輸入年度" 
                                                        value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" required ">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label text-md-right pt-2">講座姓名</label>
                                                <div class="col-sm-10">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <!-- 輸入欄位 -->
                                                        <input type="text" class="form-control input-max" id="tname" name="tname" min="2" placeholder="請輸入講座姓名" 
                                                        autocomplete="off" >
                                                        <label class="btn mobile-100 mb-3 ml-2 " onclick="getidno();" ><i class="fa fa-search fa-lg pr-1"></i>搜尋身分證字號</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="divterm" class="form-group row" style="visibility:visible">
                                                <label class="col-2 control-label text-md-right pt-2">身分證字號</label>
                                                <div class="col-10">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <select id="idno" name="idno" class="select2 form-control select2-single input-max" >
                                                        <option value='0'>請先查詢身分證字號</option>
                                                        <?php foreach ($idnoArr as $key => $va) { ?>
                                                            <option value='<?=$va->idno?>'><?=$va->idno?> <?=$va->sex?> <?=$va->dept?></option>
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
            url:"/admin/yearly_income_detail/getidno",
            data: { tname: $('#tname').val()},
            success: function(data){
            let idnoArr = JSON.parse(data);
            let tempHTML ="";
            if(idnoArr.length==0)
            {
                tempHTML = "<option value='0'>查無此人</option>";
            }

            for(let i=0; i<idnoArr.length; i++) 
            {
                tempHTML += "<option value='"+idnoArr[i].idno+"'>"+idnoArr[i].idno+" "+idnoArr[i].sex+" "+idnoArr[i].dept+"</option>";                     
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