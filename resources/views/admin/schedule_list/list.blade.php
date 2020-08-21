@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'schedule_list';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">行事表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">行事表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>行事表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <form id="exportFile" method="post" id="search_form" action="/admin/schedule_list/export" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <!-- 年度 --><!-- 月份 -->
                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right">年度</label>
                                            <div class="col-sm-2">
                                                <select class="select2 form-control select2-single input-max" name="selectMonth">
                                                    @for ($i = (int)date("Y")-1910; $i>=90; $i--)
                                                       <option value={{$i}}>{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <label class="col-sm-1 text-right">月份</label>
                                            <div class="col-sm-2">
                                                <select class="select2 form-control select2-single input-max" name="selectMonth">
                                                    @for ($i = 1; $i<=12; $i++)
                                                        <option value={{$i}}>{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
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


</script>
@endsection