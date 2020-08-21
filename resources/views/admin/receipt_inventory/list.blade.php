@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'receipt_inventory';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">需求名額統計表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">需求名額統計表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>需求名額統計表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">


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