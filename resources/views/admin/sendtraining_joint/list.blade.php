@extends('admin.layouts.layouts')
@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/sendtraining_joint.css') }}" >

    <?php $_menu = 'sendtraining_joint';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">聯合派訓通知</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">聯合派訓通知</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>聯合派訓通知</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <div class="col-12 form-group row align-items-right">  
                                        <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" data-toggle="modal" data-target="#setdate" id="setsignupdate" name="setsignupdate" disabled="disabled" >設定報名起訖日期</button>
                                    </div>


                                    <form id="getlist" method="get"  >
                                    {{ csrf_field() }}
                                        <div class="col-md-8 form-group row align-items-center">
                                            <input type="text" class="form-control col-sm-1 mb-3 mr-1 ml-1" id="yerly" name="yerly" min="1" value="{{ $queryData['yerly'] }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" required>
                                            <p style="display: inline">年</p>
                                            <input type="text" class="form-control col-sm-1 mb-3 mr-1 ml-1" id="month" name="month" min="1" value="{{ $queryData['month'] }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2" required>
                                            <p style="display: inline">月</p>
                                            <button type="submit" class="btn mobile-100 mb-3 ml-2"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </div>
                                    </form>  
                                    <form id="exportclass" method="post" action="/admin/sendtraining_joint/{{ $queryData['yerly'] }}/{{ $queryData['month'] }}/exportclass" enctype="multipart/form-data" >
                                        {{ csrf_field() }}   
                                        <div id="dvdoctype" class="form-group row  align-items-center">
                                            <label class="col-2 text-right">請選檔案格式：</label>
                                            <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                            <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                        </div>                
                                        <div class="col-12 form-group row align-items-right">  
                                            <button type="submit" class="btn btn-primary btn-sm mb-3 ml-1"><i class="fas fa-file-export fa-lg pr-1"></i>匯出開班一覽表</button>
                                    </form>
                                    <form id="exportreciever" method="post" action="/admin/sendtraining_joint/{{ $queryData['yerly'] }}/{{ $queryData['month'] }}/exportreciever" enctype="multipart/form-data"  >
                                        {{ csrf_field() }}    
                                            <button type="submit" class="btn btn-primary btn-sm mb-3 ml-3"><i class="fas fa-file-export fa-lg pr-1"></i>聯合派訓受文者清單</button>
                                    </form>    
                                        </div>

                                    <!-- 排序 -->
                                    <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                    <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                    
                                    <!-- 每頁幾筆 -->
                                    <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">
                                    <div class="table-responsive">
                                            <table id="tbdata" class="table table-bordered mb-0" width="2000">
                                                <thead>
                                                <tr>
                                                    <th class="text-center" style="display:none">key</th>
                                                    <th class="text-center" >單位</th>
                                                    <th class="text-center" width="800">班別</th>
                                                    <th class="text-center" >期別</th>
                                                    <th class="text-center" >開課日期</th>
                                                    <th class="text-center">結束日期</th>
                                                    <th class="text-center">報名開始日期</th>
                                                    <th class="text-center">報名截止日期</th>
                                                    <th class="text-center">承辦人員</th>
                                                    <th class="text-center">聯合派訓</td>
                                                    <th class="text-center">名額分配</td>
                                                    <th class="text-center">混成班</td>
                                                    <!--th class="text-center" width="70">刪除</th-->
                                                </tr>
                                                </thead>
                                                <tbody>

                                                <?php if(count($data)==0) { ?>
                                                    <tr>
                                                        <td colspan="12">無資料!  請更改條件搜尋!</td>
                                                    </tr>
                                                <?php } ?>

                                                @foreach($data as $va)
                                                    <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                   
                                                    <tr>
                                                        <td class="text-center" style="display:none">{{ $va->class}}_{{ $va->term}}</td>
                                                        <td class="text-center">{{ $va->單位}}</td>  
                                                        <td class="text-left">{{ $va->班別}}</td>
                                                        <td class="text-center">{{ $va->期別}}</td>
                                                        <td class="text-center">{{ $va->開課日期}}</td>
                                                        <td class="text-center">{{ $va->結束日期}}</td>
                                                        <td class="text-center">{{ $va->報名開始日期}}</td>
                                                        <td class="text-center">{{ $va->報名截止日期}}</td>
                                                        <td class="text-center">{{ $va->承辦人員}}</td>
                                                        <td class="text-center">{{ $va->聯合派訓}}</td>
                                                        <td class="text-center">{{ $va->名額分配}}</td>
                                                        <td class="text-center">{{ $va->混成班}}</td>
                                                        
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                    </div>

                                    <!-- Modal1 批次增刪作業 -->
                                    <div class="modal fade" id="setdate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            {!! Form::open(['method' => 'get', 'url' => '/admin/sendtraining_joint/setdate']) !!}
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" >設定報名起訖日期</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="card-body pt-4 text-center">
                                                        <div class="form-group row" style="font-size: 16px;">
                                                            <input type="text" id="classterm" name="classterm" style="display:none" value="">
                                                            <div class="input-group align-items-center">        
                                                                <label class="mr-2">報名開始時間</label>                    
                                                                <input type="text" id="sdate" name="pubsdate" class="form-control input-max" autocomplete="off" value="">
                                                                <span class="input-group-addon" style="cursor: pointer;height:calc(2.25rem + 2px);" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" style="font-size: 16px;">
                                                            <div class="input-group align-items-center"> 
                                                                <label class="mr-2">報名結束時間</label>      
                                                                <input type="text" id="edate" name="pubedate" class="form-control input-max" autocomplete="off" value="" >
                                                                <span class="input-group-addon" style="cursor: pointer;height:calc(2.25rem + 2px);" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                        </div> 
                                                    </div>    
                                                </div>
                                                <div class="modal-footer justify-content-center">
                                                    <button type="submit" class="btn btn-danger mx-0 justify-self-center" >設定報名起訖日期</button>
                                                    <button type="button" class="btn btn-secondary mr-auto justify-self-center" data-dismiss="modal">取消</button>
                                                </div>
                                            </div>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>

                                     <!-- 分頁 -->
                                     @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])

                                </div>
                            </div>
                        </div>
                        <!-- 列表頁尾 -->
                        @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])
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

    $(document).ready(function() {

        $("#sdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });

        $('#datepicker1').click(function(){
            $("#sdate").focus();
        });

        $("#edate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });

        $('#datepicker2').click(function(){
            $("#edate").focus();
        });

        $("#setsignupdate").attr("disabled", true);

    });

    $("#tbdata tr").click(function(){
        $(this).addClass('selected').siblings().removeClass('selected');    

        $('#classterm').val($(this).find('td:first').html());

        var value=$(this).find('td:eq(6)').html();
        value=value.replace('/','');
        value=value.replace('/','');
        $('#sdate').val(value);

        value=$(this).find('td:eq(7)').html();
        value=value.replace('/','');
        value=value.replace('/','');
        $('#edate').val(value);
        
        $("#setsignupdate").attr("disabled", false);

    });

</script>
@endsection



