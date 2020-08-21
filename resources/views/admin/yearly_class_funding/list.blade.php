@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'yearly_class_funding';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">年度班期費用統計表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">年度班期費用統計表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>年度班期費用統計表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form id="exportFile" method="post" id="search_form" action="/admin/yearly_class_funding/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                    <div class="form-group row align-items-center">
                                        <input type="text" class="form-control col-sm-1 mb-2 mr-1" id="Year" name="Year" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3">
                                        <p style="display: inline">年</p>
                                        <input type="text" class="form-control col-sm-1 mb-2 mr-1 ml-1" id="Month" name="Month" min="1"
                                            value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2">
                                        <p style="display: inline">月</p>
                                    </div>  
                                    <div id="dvdoctype" class="form-group row ">
                                        <label class="text-right">請選檔案格式：</label>
                                        <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                        <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                    </div>

                                    <div align="left">
                                        <div>
                                            <button type="submit" class="btn mobile-100 col-2" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                            <label id="msg"" ></label>
                                            <label id="download" visible="false"></label>
                                        </div>
                                        <label class="control-label text-md-right ml-3" style="font-size: 12px; color:red; ">月份條件空白時輸出整年的資料</label>
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
//跳出訊息視窗
if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }   

</script>
@endsection