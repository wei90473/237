@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'itineracy';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">巡迴研習</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">巡迴研習類別</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>巡迴研習類別</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <!-- 搜尋條件 -->
                                    <div class="search-float">
                                        <form method="get" id="search_form">
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <!-- 代號 -->
                                                <div class="input-group col-4">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">代號</span>
                                                    </div>
                                                    <input type="text" id="code" name="code" class="form-control" autocomplete="off" value="{{ isset($queryData['code'])?$queryData['code']:'' }}">
                                                </div>
                                                <!-- 名稱 -->
                                                <div class="input-group col-6">
                                                     <div class="input-group-prepend">
                                                        <span class="input-group-text">名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ isset($queryData['name'])?$queryData['name']:'' }}">
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
                                            <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" onclick="Create()"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                        </div>
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="80">功能</th>
                                                <th>代號</th>
                                                <th>名稱</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($data))
                                            @foreach($data as $va)
                                                <?php //$startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <input type="hidden" name="code{{$va->code}}" value="{{ $va->name }}">
                                                        <a href="#">
                                                            <i class="fa fa-pencil" onclick="Edit({{ $va->code}}) " >編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->code }}</td>
                                                    <td>{{ $va->name }}</td>
                                                </tr>
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
    <!-- 新增 -->
    <div class="modal fade" id="CreateModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <!-- form start -->
                {!! Form::open([ 'method'=>'POST', 'url'=>'/admin/itineracy/', 'id'=>'form']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title">新增類別</h4> -->
                    </div>
                    <div class="modal-body">
                        代號<span class="text-danger">*</span><input type="text" class="form-control number-input-max" id="c_code" name="c_code"></input>
                        名稱<span class="text-danger">*</span><input type="text" class="form-control number-input-max" id="c_name" name="c_name"></input>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="actionStore()">儲存</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">關閉</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- 修改 -->
    <div class="modal fade" id="EditModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <!-- form start -->
                {!! Form::open([ 'method'=>'PUT', 'url'=>'/admin/itineracy/999', 'id'=>'form2']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title">新增類別</h4> -->
                    </div>
                    <div class="modal-body">
                        代號<span class="text-danger">*</span><input type="text" class="form-control number-input-max" id="E_code" name="E_code"></input>
                        名稱<span class="text-danger">*</span><input type="text" class="form-control number-input-max" id="E_name" name="E_name"></input>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="actionEdit()">修改</button>
                        <button type="button" class="btn btn-primary" onclick="actionDelete()">刪除</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">關閉</button>
                    </div>
                </div>
                {!! Form::close() !!}
                <!-- 刪除 -->

                {!! Form::open([ 'method'=>'delete', 'url'=>'/admin/itineracy/999', 'id'=>'deleteform']) !!}
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
      document.all.code.value = "";
      document.all.c_code.value = "";
      document.all.name.value = "";
      document.all.c_name.value = "";
    }

    function Create() {
        $("#c_code").val("{{ $queryData['max']}}");
        $("#c_name").val("");
        $('#CreateModal').modal('show');
    };

    function Edit(code) {
        var name = $("input[name=code"+code+"]").val();
        console.log(name);
        $("#E_code").val(code);
        $("#E_name").val(name);
        $("#D_code").val(code);
        $("#form2").attr('action', 'http://172.16.10.18/admin/itineracy/'+code);
        $('#EditModal').modal('show');
    };

    //新增
    function actionStore(){
        if( $("#c_code").val()!='' && $("#c_name").val()!=''){
            $("#form").submit();
        }else{
            alert('請輸入代號名稱 !!');
            return ;
        }
    }
    //修改
    function actionEdit(){
        if( $("#E_code").val()!='' && $("#E_name").val()!=''){
            $("#form2").submit();
        }else{
            alert('請輸入代號名稱 !!');
            return ;
        }
    }
    //刪除
    function actionDelete(){
        if( $("#D_code").val()!='' && $("#E_name").val()!=''){
            var code = $("#D_code").val();
            $("#deleteform").attr('action', 'http://172.16.10.18/admin/itineracy/'+code);
            $("#deleteform").submit();
        }else{
            alert('請輸入代號名稱 !!');
            return ;
        }
    }
</script>
@endsection
