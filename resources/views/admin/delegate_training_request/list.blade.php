@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'delegate_training_request';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">接受委託辦理訓練需求彙總表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">接受委託辦理訓練需求彙總表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>接受委託辦理訓練需求彙總表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form method="post" action="/admin/delegate_training_request/export" id="search_form">
                                        {{ csrf_field() }}
                                            <div class="form-group row">
                                                    <label class="col-sm-1 text-right">年度</label>
                                                    <div class="col-sm-2">
                                                        <select class="select2 form-control select2-single input-max" name="yerly">
                                                            @for ($i = (int)date("Y")-1910 ; $i>=90; $i--)
                                                            <option value={{$i}}>{{$i}}</option>
                                                            @endfor
                                                        </select>
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