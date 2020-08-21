@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'remittance_detail';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">匯款明細表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">匯款明細表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>匯款明細表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                <form id="exportFile" method="post" id="search_form" action="/admin/remittance_detail/export" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label class="col-1 text-right">轉存日期</label>
                                            <div class="col-3">
                                                <div class="input-group bootstrap-touchspin number_box">
                                                    <select id="tdate" name="tdate" class="select2 form-control select2-single input-max" required>
                                                     <?php foreach ($tdateArr as $key => $va) { ?>
                                                        <option value='<?=$va->date?>'><?=$va->sdate?></option>
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
                                    <div class="form-group row justify-content-center">
                                        <div class="col-9">
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

</script>
@endsection