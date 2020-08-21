@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'site_using_overview';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地借用概況表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">場地借用概況表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>場地借用概況表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <form id="exportFile" method="post" id="search_form" action="/admin/site_using_overview/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <!-- 年度 --><!-- 月份 -->
                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right">年度</label>
                                            <div class="col-sm-2">
                                                <select class="select2 form-control select2-single input-max" name="year">
                                                    @for ($i = (int)date("Y")-1910; $i>=90; $i--)
                                                       <option value={{$i}}>{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            
                                        </div>
                                         

                                        <div class="form-group row col-6 align-items-center justify-content-center">
                                            <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                            
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