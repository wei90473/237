@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'changetraining_plan';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">實施計畫</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">實施計畫</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>實施計畫</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                <div class="float-left search-float col-12">
                                        <form id="exportFile" method="post" id="search_form" action="/admin/changetraining_plan/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                            <!-- 班別 -->
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label text-md-right pt-2">班別</label>
                                                <div class="col-sm-10">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <select id="classes" name="classes" class="select2 form-control select2-single input-max" ">
                                                            <option value="">請選擇</option>
                                                        <?php foreach ($classArr as $key => $va) { ?>
                                                            <option value='<?=$va->class?>'><?=$va->class?>-<?=$va->name?></option>
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
                                            <div align="center">
                                                <div>
                                                    <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                                    <label id="download"></label>
                                                    <!-- <button type="button" class="btn mobile-100" data-toggle="modal" data-target="#upload_changetraining_plan_file"><i class="fa fa-plus fa-lg pr-1"></i>班別上傳實施計畫</button> -->
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
        </div>
    </div>
   
<!--  匯入 -->
<!-- <div class="modal fade" id="upload_changetraining_plan_file" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        {!! Form::open(['method' => 'POST', 'url' => '/admin/changetraining_plan/import', 'enctype' => "multipart/form-data"]) !!}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">班別上傳實施計畫</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body pt-4 text-center">
                    <div class="card-header"><h3 class="card-title">班別上傳實施計畫</h3></div>
                

                    <label >上傳班別：</label>
                    <select id="classes" name="classes" class="select2 form-control select2-single input-max" ">
                        <option value="">請選擇</option>
                        <?php foreach ($classArr as $key => $va) { ?>
                            <option value='<?=$va->class?>'><?=$va->class?>-<?=$va->name?></option>
                        <?php } ?>
                    </select>
                    <label >上傳檔案：</label>
                    <input type="file" class="form-control" name="import_file">
                    <br/>
                </div>    
            </div>
            <div>
                <div>
                說明：<br>
                請選擇班別，系統將自動以本次上傳之檔案覆蓋該班實施計畫檔案
                </div>
            </div>                                                  
            <div class="modal-footer">
                <button type="submit" class="btn btn-success ml-auto">上傳</button>
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">取消</button>
            </div>                                              
        </div>
        {!! Form::close() !!}
    </div>
</div> -->

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script>

    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

 /*    function getTimes()
    {
        console.log("IM here");
        $('#download').val("報表下載中");
        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/demand_quota_report/gettime',
            data: {yerly: $('#yerly').val()},
            success: function(response){
                console.log(response);
                let dataArr =JSON.parse(response);
                let tempHTML = "";
                for(let i=0; i<dataArr.length; i++) 
                {
                    tempHTML += "<option value='"+dataArr[i].times+"'>"+dataArr[i].times+"</option>";
                    
                };
                console.log(tempHTML);
                $('#download').val("");
                // console.log(abc);
                $('#times').html(tempHTML);
            },
            error: function(){
                alert('Ajax Error');
            }
        })
    } */

</script>
@endsection