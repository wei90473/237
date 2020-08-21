@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'each_training_all';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">各類訓練進修研習成果統計彙總表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">各類訓練進修研習成果統計彙總表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>各類訓練進修研習成果統計彙總表</h3>
                        </div>

                        <form method="post" id="search_form" action="/admin/each_training_all/export" enctype="multipart/form-data">
                            {{ csrf_field() }}

                                <div class="form-group row" >
                                    <a href="/admin/each_training_all/edit">
                                        <button type="button" class="btn btn-primary btn-sm ml-3"></i>訓練成果維護</button>
                                    </a> 
                                </div>
                                <div class="form-group row" >
                                    <label class="col-1">起始日期</label>
                                    <input type="text" class="form-control col-1" id="startYear" name="startYear" min="1" 
                                        value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" onchange="yearchange()" >
                                    <p style="display: inline">年</p>

                                    <input type="text" class="form-control col-1" id="startMonth" name="startMonth" min="1"
                                        value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2" onchange="smonthchange()">
                                    <p style="display: inline">月</p>
                                </div> 

                                <div class="form-group row" >
                                    <label class="col-1">結束日期</label>
                                    <input type="text" class="form-control col-1" id="endYear" name="endYear" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3">
                                    <p style="display: inline">年</p>
                                    <input type="text" class="form-control col-1" id="endMonth" name="endMonth" min="1"
                                        value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2" onchange="emonthchange()">
                                    <p style="display: inline">月</p>
                                </div>  
                                       
                                <div class="form-group row align-items-center">
                                    <label class="col-1 pl-2 pr-2 text-right" > 院區 </label>
                                    <label class="pl-2 pr-2" ><input type="radio" name="area" id="taipei" value="1" checked >台北院區</label>
                                    <label class="pl-2 pr-2" ><input type="radio" name="area" id="nantou" value="2">南投院區</label>
                                    <label class="pl-2 pr-2" ><input type="radio" name="area" id="allarea" value="3" >全部</label>
                                </div>
                                <div id="dvdoctype" class="form-group row  align-items-center">
                                    <label class="col-2 text-right">請選檔案格式：</label>
                                    <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                    <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                </div>
                            <div>
                                <label class="col-1"> </label>
                                <button type="submit" class="btn mobile-100 mb-3 mr-1"><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                            </div>
                            <div>
                                <input type="text" id="sMontheday" class="col-1 invisible">
                                <input type="text" id="eMontheday" class="col-1 invisible">
                            </div>
                        </form>   
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

    function yearchange(){
        $('#endYear').val($('#startYear').val());
    }
    function smonthchange(){
        $('#endMonth').val($('#startMonth').val());
    }
    function emonthchange(){
   //     $('#endMonth').val($('#startMonth').val());
   //     $('#eMontheday').val(date('Y-m-t', strtotime(strval((intval($('#etartYear').val())+1911))."-".strval($('#endMonth').val())."-01")));
    }

</script>
@endsection