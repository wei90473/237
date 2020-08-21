@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'class_group';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">重覆參訓檢核群組維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">重覆參訓檢核群組維護</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>重覆參訓檢核群組維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="search-float">
                                        <form method="get" id="search_form">
                                            <!-- 群組名稱 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-4">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">群組名稱</span>
                                                    </div>
                                                    <input type="text" id="class_group" name="class_group" class="form-control" autocomplete="off" value="{{ $queryData['class_group'] }}">
                                                </div>
                                            
                                                <!-- 班號 -->
                                                <div class="input-group col-4">
                                                     <div class="input-group-prepend">
                                                        <span class="input-group-text">班號</span>
                                                    </div>
                                                    <input type="text" id="class" name="class" class="form-control" autocomplete="off" value="{{ $queryData['class'] }}">
                                                </div>
                                           
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ $queryData['name'] }}">
                                                </div>
                                                <!-- **分班名稱 -->
                                                <!-- <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">分班名稱</span>
                                                    </div>
                                                    <input type="text" id="branchname" name="branchname" class="form-control" autocomplete="off" value="">
                                                </div> -->
                                            </div>
                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                            <!-- 重設條件 -->
                                            <button class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()" type="button">重設條件</button>
                                            <!-- 新增群組 -->
                                            <button class="btn mobile-100 mb-3 mb-md-0" onclick="newgroup()" type="button">新增群組</button>
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">功能</th>
                                                <th>群組名稱</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($data))        
                                            @foreach($data as $va)
                                                <tr>
                                                <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/class_group/edit/{{ $va->groupid }}" data-placement="top" data-toggle="tooltip" data-original-title="編輯">
                                                            <i class="fa fa-pencil">編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->class_group }}</td>
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
                {!! Form::open([ 'method'=>'POST', 'url'=>'/admin/class_group', 'id'=>'form']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title">新增類別</h4> -->
                    </div>
                    <div class="modal-body">
                        群組名稱<span class="text-danger">*</span><input type="text" class="form-control number-input-max" id="c_name" name="c_name"></input>
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
@endsection

@section('js')
    <script>
        function doClear(){
          document.all.class_group.value = "";
          document.all.class.value = "";
          document.all.name.value = "";
          document.all.branchname.value = "";
        }

        function newgroup() {
            $("#c_name").val("");
            $('#CreateModal').modal('show');
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


        
        

    </script>
@endsection