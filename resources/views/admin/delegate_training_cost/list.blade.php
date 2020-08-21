@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'delegate_training_cost';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">委訓費用明細表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">委訓費用明細表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>委訓費用明細表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form method="get" action="/admin/delegate_training_cost/export" id="search_form">
                                        <div class="form-group row">
                                            <label class="col-sm-1 control-label text-md-right pt-2">班別</label>
                                            <div class="col-6">
                                                <div class="input-group bootstrap-touchspin number_box">
                                                    <select id="class" name="class" class="select2 form-control select2-single input-max" onchange="getTerms();">
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
                                                    <select id="term" name="term" class="select2 form-control select2-single input-max" >
                                                    <?php foreach ($termArr as $key => $va) { ?>
                                                        <option value='<?=$va->term?>'><?=$va->term?></option>
                                                    <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
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
<script>
    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

    function getTerms()	{
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/delegate_training_cost/getTerms",
            data: { classes: $('#class').val()},
            success: function(data){
            let dataArr = JSON.parse(data);
            let tempHTML = "";
            // console.log(dataArr);
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