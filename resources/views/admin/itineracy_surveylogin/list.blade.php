@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'itineracy_surveylogin';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">巡迴研習</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">巡迴研習需求調查登錄</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>巡迴研習需求調查登錄</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <!-- 搜尋條件 -->
                                    <div class="search-float">
                                        <form method="get" id="search_form">
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <!-- 年度 -->
                                                <div class="input-group col-4">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">年度</span>
                                                    </div>
                                                    <select class="browser-default custom-select" name="yerly">
                                                    @foreach($queryData['choices'] as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['yerly'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                    @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{isset($queryData['_sort_field'])?$queryData['_sort_field']:''  }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{isset($queryData['_sort_mode'])?$queryData['_sort_mode']:'' }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{isset($queryData['_paginate_qty'])?$queryData['_paginate_qty']:''  }}">
                                        <div class="float-left">
                                            <!-- 查詢 -->
                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                            <!-- 重設條件 -->
                                            <button type="button" class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()" >重設條件</button>
                                            <!-- 新增 -->
                                            <!-- <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" onclick="Create()"><i class="fa fa-plus fa-lg pr-2"></i>新增</button> -->
                                        </div>
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="80">功能</th>
                                                <th>年度</th>
                                                <th>期別</th>
                                                <th>巡迴計畫名稱</th>
                                                <th>需求調查期間</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($data))
                                            <?php $i = 1; ?>
                                            @foreach($data as $va)

                                                <tr>
                                                    <!-- 修改 -->
                                                    <input type="hidden" name="yerly{{$i}}" value="{{ $va->yerly }}">
                                                    <input type="hidden" name="term{{$i}}" value="{{ $va->term }}">
                                                    <input type="hidden" name="name{{$i}}" value="{{ $va->name }}">
                                                    <input type="hidden" name="surveysdate{{$i}}" value="{{ $va->surveysdate }}">
                                                    <input type="hidden" name="surveyedate{{$i}}" value="{{ $va->surveyedate }}">
                                                    <td class="text-center">
                                                        <a href="#">
                                                            <i class="fa fa-pencil" onclick="Edit({{ $i }}) ">編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->yerly }}</td>
                                                    <td>{{ $va->term }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ substr($va->surveysdate,0,3).'/'.substr($va->surveysdate,-4,2).'/'.substr($va->surveysdate,-2) }}～{{ substr($va->surveyedate,0,3).'/'.substr($va->surveyedate,-4,2).'/'.substr($va->surveyedate,-2) }}</td>
                                                    <td>
                                                        <a href="/admin/itineracy_surveylogin/list/{{ $va->yerly.$va->term }}">
                                                            <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">填報資料</button>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php $i = $i+1; ?>
                                            @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    @if(isset($data))
                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                                    @endif

                                </div>
                            </div>
                        </div>
                        @if(isset($data))
                        <!-- 列表頁尾 -->
                        @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 修改 -->
    <div class="modal fade" id="EditModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <!-- form start -->
                {!! Form::open([ 'method'=>'PUT', 'url'=>'/admin/itineracy_surveylogin/999', 'id'=>'form2']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title">新增類別</h4> -->
                    </div>
                    <div class="modal-body">
                        <!-- 年度 -->
                        <div class="form-group row">
                            <label class="col-sm-5 control-label text-md-right pt-2">年度<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <input type="text" class="form-control number-input-max" id="E_yerly" name="E_yerly" value="" readonly>
                            </div>
                            <!-- 期別 -->
                            <label class="control-label text-md pt-2">期別<span class="text-danger">*</span></label>
                            <div class="col-sm-2 md-left">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="E_term" name="E_term" min="1" max="9" placeholder="請輸入期別" value="" readonly>
                                </div>
                            </div>
                            <!-- 主題數量上限 -->
                            <!-- <label class="control-label text-md pt-2">主題數量上限<span class="text-danger">*</span></label>
                            <div class="col-sm-2 md-left">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <input type="text" class="form-control number-input-max" id="topics" name="topics" min="1" max="99" placeholder="請輸入主題數量上限" value="{{ old('topics', (isset($data['topics']))? $data['topics'] : 1) }}" readonly>
                                </div>
                            </div> -->
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-5 control-label text-md-right pt-2">需求調查日期(起)</label>
                            <div class="col-md-5">
                                <input type="text" id="surveysdate" name="surveysdate" class="form-control" autocomplete="off"  placeholder="請輸入需求調查日期(起)" value="" >
                            </div>
                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-5 control-label text-md-right pt-2">需求調查日期(迄)</label>
                            <div class="col-md-5">
                                <input type="text" id="surveyedate" name="surveyedate" class="form-control" autocomplete="off"  placeholder="請輸入需求調查日期(迄)" value="" >
                            </div>
                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                        </div>
                        <!-- <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">巡迴計畫名稱<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <input type="text" id="name" name="name" class="form-control" autocomplete="off"  placeholder="請輸入計畫名稱" value="{{ old('name', (isset($data['name']))? $data['name'] : '') }}" required>
                            </div>
                        </div> -->
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" >修改</button>
                        <!-- <button type="button" class="btn btn-danger" onclick="actionDelete()">刪除</button> -->
                        <button type="button" class="btn btn-primary" data-dismiss="modal">關閉</button>
                    </div>
                </div>
                {!! Form::close() !!}
                <!-- 刪除 -->

                {!! Form::open([ 'method'=>'delete', 'url'=>'/admin/itineracy_surveylogin/999', 'id'=>'deleteform']) !!}
                <input type="hidden" class="form-control " id="D_code" name="D_code"></input>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection
@section('js')
<script>
    function doClear(){
      var d = new Date();
      var yerly = (d.getFullYear() - 1911);
      document.all.yerly.value = yerly;
    }



    function Edit(code) {
        var yerly = $("input[name=yerly"+code+"]").val();
        var term = $("input[name=term"+code+"]").val();
        var name = $("input[name=name"+code+"]").val();
        var surveysdate = $("input[name=surveysdate"+code+"]").val();
        var surveyedate = $("input[name=surveyedate"+code+"]").val();
        console.log(name);
        $("#E_yerly").val(yerly);
        $("#E_term").val(term);
        $("#surveysdate").val(surveysdate);
        $("#surveyedate").val(surveyedate);
        $("#form2").attr('action', 'http://172.16.10.18/admin/itineracy_surveylogin/'+yerly+term);
        $('#EditModal').modal('show');
    };


    //修改
    function actionEdit(){
            $("#form2").submit();

    }
    //刪除
    function actionDelete(){
        if( $("#D_code").val()!='' && $("#E_name").val()!=''){
            var code = $("#D_code").val();
            $("#deleteform").attr('action', 'http://172.16.10.18/admin/itineracy_surveylogin/'+code);
            $("#deleteform").submit();
        }else{
            alert('請輸入代號名稱 !!');
            return ;
        }
    }

    $(document).ready(function() {
            $("#surveysdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker1').click(function(){
                $("#surveysdate").focus();
            });
            $("#surveyedate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker2').click(function(){
                $("#surveyedate").focus();
            });

     });
</script>
@endsection
