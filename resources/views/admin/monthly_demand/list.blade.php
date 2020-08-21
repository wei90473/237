@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'monthly_demand';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">每月需求表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">每月需求表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>每月需求表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <form id="exportFile" method="post" id="search_form" action="/admin/monthly_demand/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                            <?php $weekpicker =''; ?>
                                            <div class="form-group row align-items-center" >
                                                <label class="col-md-1 text-right">日期：<span class="text-danger"></span></label>
                                                <input type="text" class="form-control  col-md-2 input-max width"  id="yearmonth" name="yearmonth" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="6" required  min="1" autocomplete="off">
                                                <label class="col-md-2">(YYYYMM)<span class="text-danger"></span></label>
                                            </div>
                                            <div id="dvdoctype" class="form-group row  align-items-center">
                                                    <label class="col-2 text-right">請選檔案格式：</label>
                                                    <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                                    <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                            </div>
                                            <div class="form-group row">
                                                <button type="submit" class="btn mobile-100 col-md-4" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                                <label id="download" visible="false"></label>
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
    function getTimes()
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
    }

</script>
@endsection