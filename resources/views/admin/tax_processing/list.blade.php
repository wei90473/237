@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'tax_processing';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">所得稅申報處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">所得稅申報處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>所得稅申報處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="min-width: 1000px;">
                                        <form method="get" id="search_form">
                                        <input type="hidden" id="search" name="search" class="form-control" value="search">
                                            <!-- 年度 -->

                                            <div class="float-md mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">年度</span>
                                                        </div>
                                                        <select type="text" id="yerly1" name="yerly" class="browser-default custom-select"  value="{{ $queryData['yerly'] }}" style="min-width: 80px; flex:0 1 auto">
                                                            @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                                                                <option value="{{$i}}" {{ $queryData['yerly'] == $i? 'selected' : '' }} >{{$i}}

                                                                </option>
                                                            @endfor
                                                        </select>
                                                </div>
                                            </div>

                                            <!-- 講座姓名 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">講座姓名</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ $queryData['name'] }}">
                                                </div>
                                                <!-- **講座身分證 -->
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">講座身分證</span>
                                                    </div>
                                                    <input type="text" id="idno" name="idno" class="form-control" autocomplete="off" value="{{ $queryData['idno'] }}">
                                                </div>
                                            </div>

                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <div class="float-left">
                                                <!-- 查詢 -->
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                                <!-- 重設條件 -->
                                                <button type="button" class="btn mobile-100 mb-3 mb-md-0" onclick="doClear();">重設條件</button>


                                            </div>

                                        </form>
                                        <div class="float-left">
                                            &nbsp;
                                            <span onclick="$('#tax_form').attr('action', '/admin/tax_processing/taxReturn');" data-toggle="modal" data-target="#tax_modol">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">執行報稅處理</button>
                                            </span>
                                            <span onclick="$('#frmFile_form').attr('action', '/admin/tax_processing/frmFile');" data-toggle="modal" data-target="#frmFile_modol">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">產生磁片</button>
                                            </span>

                                        </div>
                                    </div>


                                    <div class="table-responsive">
                                        <table id="data_table" class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>編號</th>
                                                <th>身分證</th>
                                                <th>講座姓名</th>
                                                <th>所得類別</th>
                                                <th>給付總額</th>
                                                <th>扣繳稅額</th>
                                                <th>給付淨額</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        {{ $va->serno }}
                                                    </td>
                                                    <td class="text-center">{{ $va->idno }}</td>
                                                    <td class="text-center">{{ $va->name }}</td>
                                                    <td>{{ $va->type }}</td>
                                                    <td>{{ intval($va->total) }}</td>
                                                    <td>{{ intval($va->deduct) }}</td>
                                                    <td>{{ intval($va->net) }}</td>

                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
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

    <div id="tax_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content" >
                <div class="card mb-0">
                    <div class="card-header bg-primary">
                        <h3 class="card-title float-left text-white">執行報稅處理</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>

                    <div class="modal-footer py-2 text-center"  >
                        {!! Form::open([ 'method'=>'post', 'url'=>'', 'id'=>'tax_form', 'style'=>'width:100%;' ]) !!}

                        <div class="float-md mobile-100 mr-1 mb-3">
                            <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">年度</span>
                                    </div>
                                    <select type="text" id="yerly" name="yerly" class="browser-default custom-select"  value="{{ $queryData['yerly'] }}" style="min-width: 80px; flex:0 1 auto">
                                        @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                                            <option value="{{$i}}" {{ $queryData['yerly'] == $i? 'selected' : '' }} >{{$i}}

                                            </option>
                                        @endfor
                                    </select>
                            </div>
                        </div>
                        <div class="float-md mobile-100 mr-1 mb-3">
                            <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">重複執行</span>
                                    </div>
                                    <input type="checkbox" id="repeat" name="repeat" style="min-width:20px; margin-left:5px;" value="Y">
                                    (重複執行請選)
                            </div>
                        </div>

                        <button type="button" class="btn mr-2 btn-info" onclick="location.reload();" data-dismiss="modal">取消</button>
                        <button type="button" class="btn mr-3 btn-danger" onclick="submitform();" >確定</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="frmFile_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content" >
                <div class="card mb-0">
                    <div class="card-header bg-primary">
                        <h3 class="card-title float-left text-white">產生磁片</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>

                    <div class="modal-footer py-2 text-center"  >
                        {!! Form::open([ 'method'=>'post', 'url'=>'', 'id'=>'frmFile_form' , 'style'=>'width:100%;' ]) !!}


                        <div class="float-md mobile-100 mr-1 mb-3">
                            <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">報稅年度</span>
                                    </div>
                                    <select type="text" id="yerly" name="yerly" class="browser-default custom-select"  value="{{ $queryData['yerly'] }}" style="min-width: 80px; flex:0 1 auto">
                                        <option></option>
                                        @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                                            <option value="{{$i}}" {{ $queryData['yerly'] == $i? 'selected' : '' }} >{{$i}}

                                            </option>
                                        @endfor
                                    </select>
                            </div>
                        </div>


                        <button type="button" class="btn mr-2 btn-info" onclick="location.reload();" data-dismiss="modal">取消</button>
                        <button type="button" class="btn mr-3 btn-danger" onclick="submitform2();" >確定</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {

        $("#date3").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#transfer_datepicker3').click(function(){
            $("#date3").focus();
        });
        $("#date6").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#transfer_datepicker6').click(function(){
            $("#date6").focus();
        });
        $("#date5").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#transfer_datepicker5').click(function(){
            $("#date5").focus();
        });
    });
    function doClear(){
      var today = new Date();
      var year = today.getFullYear();
      $("#yerly1").val(year-1911);
      document.all.name.value = "";
      document.all.idno.value = "";
    }

    function submitform(){
        if($("#date1").val() == ''){
            alert("上課日期錯誤");
            return;
        }
        if($("#date2").val() == ''){
            alert("上課日期錯誤");
            return;
        }
        if($("#date3").val() == ''){
            alert("轉存日期錯誤");
            return;
        }
        if($("#date1").val()>$("#date2").val()){
            alert("上課期間錯誤");
            return;
        }
        submitForm('#tax_form');
   }
   function submitform2(){

        if($("#date5").val() == ''){
            alert("日期錯誤");
            return;
        }
        submitForm('#frmFile_form');
   }
   function submitform3(){

        if($("#date6").val() == ''){
            alert("日期錯誤");
            return;
        }
        submitForm('#cancel_form');
   }
    </script>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')
@endsection