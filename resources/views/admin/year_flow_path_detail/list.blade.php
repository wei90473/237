@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'year_flow_path_detail';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">年度流路明細表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">年度流路明細表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>年度流路明細表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                <form id="exportFile" method="post" id="search_form" action="/admin/year_flow_path_detail/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label class="col-sm-1">起始日期<span class="text-danger"></span></label>
                                          
                                                <input type="text" class="form-control col-1" id="startYear" name="startYear" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3">
                                                <p style="display: inline">年</p>

                                                <input type="text" class="form-control col-1" id="startMonth" name="startMonth" min="1"
                                                    value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2">
                                                <p style="display: inline">月</p>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-1">結束日期<span class="text-danger"></span></label>
                                                <input type="text" class="form-control col-1" id="endYear" name="endYear" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3">
                                                <p style="display: inline">年</p>
                                                <input type="text" class="form-control col-1" id="endMonth" name="endMonth" min="1"
                                                    value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2">
                                                <p style="display: inline">月</p>

                                        </div>

                                        <div class="form-group row align-items-center">
                                            <label class="col-2 pl-2 pr-2" >  </label>
                                            <label class="pl-2 pr-2" ><input type="radio" name="area" id="taipei" value="1" checked >台北院區</label>
                                            <label class="pl-2 pr-2" ><input type="radio" name="area" id="nantou" value="2">南投院區</label>
                                            <label class="pl-2 pr-2" ><input type="radio" name="area" id="allarea" value="3" >全部</label>
                                                 
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

    //跳出訊息視窗
    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }


    function yearchange(){
        $('#startYear').val($('#yerly').val());
        $('#endYear').val($('#yerly').val());
        $('#startMonth').val("1");
        $('#endMonth').val("12");
    }
   

</script>
@endsection