@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'YearlyChannelController';?>

<style type="text/css">
    .queryLabel{
        width: 150px;
    }
</style>

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

                                <form method="get" action="/admin/yearly_channel/export" id="search_form">

                                   <div class="form-group row">
                                        <label class="control-label text-md-right pt-2 queryLabel">期間</label>
                                        <div class="input-group col-2">
                                            <input type="text" id="sdate" name="sdate" class="form-control" autocomplete="off" value="" required>
                                            <span class="input-group-addon" style="cursor: pointer;height: calc(2.25rem + 2px)" id="sdateDatepicker"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <div>
                                            <label class="col-sm-1 control-label text-md-right pt-2">至</label>
                                        </div>
                                        <div class="input-group col-2">
                                            <input type="text" id="edate" name="edate" class="form-control" autocomplete="off" value="" required>
                                            <span class="input-group-addon" style="cursor: pointer;height: calc(2.25rem + 2px)" id="edateDatepicker"><i class="fa fa-calendar"></i></span>
                                        </div>                                       
                                    </div>  

                                    <div class="form-group row">
                                        <label class="control-label text-md-right pt-2 queryLabel">院區</label>
                                        <div class="col-2">
                                            <div class="input-group">
                                                <select class="custom-select" name="branch" onchange="changeSiteBranch(this.value)" required>
                                                    <option value="1">臺北院區</option>
                                                    <option value="2">南投院區</option>
                                                </select>
                                            </div>         
                                        </div>                                  
                                    </div> 
                                    <div class="form-group row">
                                        <label class="control-label text-md-right pt-2 queryLabel">床位</label>
                                        <div class="col-2">
                                            <div class="input-group">
                                                <input type="number" name="bedQuantity" class="form-control" value="256">
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
@endsection

@section('js')
<script>

$(document).ready(function() {
    $("#sdate").datepicker({
        format: "twymmdd",
        language: 'zh-TW'
    });
    $('#sdateDatepicker').click(function(){
        $("#sdate").focus();
    });

    $("#edate").datepicker({
        format: "twymmdd",
        language: 'zh-TW'
    });
    $('#edateDatepicker').click(function(){
        $("#edate").focus();
    });    
});

</script>
@endsection
