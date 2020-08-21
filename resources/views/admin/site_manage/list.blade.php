@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <style>
        .arrow_rank {
            display: flex;
            flex-direction: column;
        }
        .flex {
            display: flex;
        }
        .btnSpace {
            min-width: 70px;
            margin-bottom: 5px;
        }
    </style>

    <?php $_menu = 'site_manage';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">洽借場地班期資料處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">洽借場地班期資料處理</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>洽借場地班期資料處理列表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">

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

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                     <div class="input-group-prepend">
                                                        <span class="input-group-text">班號</span>
                                                    </div>
                                                    <input type="text" id="class" name="class" class="form-control" autocomplete="off" value="{{ $queryData['class'] }}">
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ $queryData['name'] }}">
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">辦班院區</span>
                                                    </div>
                                                    <select class="form-control select2 " name="branch">
                                                       <option value="">全部</option>
                                                        @foreach(config('app.branch') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['branch'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">課程分類</span>
                                                    </div>
                                                    <select class="form-control select2 " name="classtype">
                                                        @foreach(config('app.classtype') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['classtype'] == $key? 'selected' : 'G' }}>{{ $key.$va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <div class="float-left">
                                                <!-- 查詢 -->
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                                <!-- 重設條件 -->
                                                <button class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()" type="button">重設條件</button>
                                                <a href="/admin/site_manage/create">
                                                    <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新洽借場地班期</button>
                                                </a>
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" style=" margin-right:5px;" data-toggle="modal" data-target="#groupwork">批次增刪班別</button>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="float-md-right">
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th width="70">功能</th>
                                                <th width="150">班號</th>
                                                <th width="150">院區</th>
                                                <th width="300">班別名稱</th>
                                                <th width="70">上課方式</th>
                                                <th width="70">訓期</th>
                                                <th width="70">每期人數</th>
                                                <th width="70">訓練總時數</th>
                                               <!--  <th width="70">刪除</th> -->
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($data))
                                                @foreach($data as $va)
                                                    <td class="text-center">
                                                        <a href="/admin/site_manage/{{$va->class}}/edit" data-placement="top" data-toggle="tooltip" data-original-title="編輯">
                                                            <i class="fa fa-pencil">編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->class }}</td>
                                                    <td>{{ config('app.branch.'.$va->branch)}}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ config('app.style.'.$va->style) }}</td>
                                                    <td>{{ $va->period }}{{ config('app.kind.'.$va->kind) }}</td>
                                                    <td>{{ $va->quota }}</td>
                                                    <td>{{ $va->trainhour }}</td>
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

    <form method="POST" action="/admin/site_manage/batchadd" enctype="multipart/form-data" id="form1" name="form1">
    {{ csrf_field() }}
    <!-- 批次新增或刪除 modal -->
    	<div class="modal fade bd-example-modal-lg batchAddDel" id ="groupwork" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    	  	<div class="modal-dialog modal-dialog_80" role="document">
    		    <div class="modal-content">
    		        <div class="modal-header">
    			        <h4 class="modal-title"><strong>批次新增或刪除</strong></h4>
    			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    			          <span aria-hidden="true">&times;</span>
    			        </button>
    		        </div>
    		        <div class="modal-body">
                        <label class="control-label text-md-right">年度</label>
                        <select type="text" id="groupyerly" name="groupyerly" class="browser-default custom-select"  value="{{ $queryData['yerly'] }}" style="min-width: 80px; flex:0 1 auto">
                            @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                                <option value="{{$i}}" {{ $queryData['yerly'] == $i? 'selected' : '' }} >{{$i}}

                                </option>
                            @endfor
                        </select>
    		        </div>
    		        <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="submitForm('#form1');">新增</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="BatchDel();">刪除</button>
    			        <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
    		        </div>
    		    </div>
    		</div>
    	</div>
    </form>
    
    <script>
        // 批次新增或刪除
        function batchAddDel() {
            $(".batchAddDel").modal('show');
        }

        // 排序
        function order() {
            $(".order").modal('show');
        }

        function doClear(){
          var d = new Date();
          var yerly = (d.getFullYear() - 1911);
          document.all.yerly.value = yerly;  
          document.all.class.value = "";
          document.all.name.value = "";
          $("select[name=branch]").val('').trigger("change");
          $("select[name=classtype]").val('G').trigger("change");
        }
        function BatchDel(){
        if($('#yerly').val()==''){
            alert('請輸入年度 !!');
            return ;
        };
        Swal.fire({
            title: '確定要刪除?',
            text: "您將會刪除這個年度的全部資料",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '確定'
        }).then((result) => {
            if (result.value) {
                location.href='/admin/site_manage/batchdel?groupyerly='+$('#groupyerly').val();
            }
        })
    };
    </script>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection