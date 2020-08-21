@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'teachingmethod';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">教學教法資料維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">教學教法資料維護</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>教學教法資料維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <!-- 搜尋條件 -->
                                    <div class="search-float">
                                        <form method="get" id="search_form">
                                            <!-- 教學教法名稱 -->
                                            <div class="float-md mobile-100 row mb-1">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">教學教法名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ $queryData['name'] }}">
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">年度版本</span>
                                                    </div>
                                                    <select class="browser-default custom-select" name="yerly">
                                                        <!-- <option value="" {{ old('yerly', (isset($queryData['yerly']))? $queryData['yerly'] : '') == ''? 'selected' : '' }}>全部</option>
                                                        <option value="109" {{ old('yerly', (isset($queryData['yerly']))? $queryData['yerly'] : '') == '109'? 'selected' : '' }}>108年之後版本</option>
                                                        <option value="107" {{ old('yerly', (isset($queryData['yerly']))? $queryData['yerly'] : '') == '107'? 'selected' : '' }}>107年之前版本</option>
                                                       -->
                                                       <option></option>
                                                        @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                                                            <option value="{{$i}}" {{ $queryData['yerly'] == $i? 'selected' : '' }} >{{$i}}

                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <div class="float-left mr-1 mb-3">
                                                <!-- 查詢 -->
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                                <!-- 重設條件 -->
                                                <button type="button" class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()">重設條件</button>
                                                <!-- 新增教學教法 -->
                                                <a href="/admin/teachingmethod/create">
                                                    <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新增
                                                    教學教法</button>
                                                </a>
                                            </div>    
                                        </form>
                                    </div>

                                    

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">功能</th>
                                                <th>教學教法</th>
                                                <th>版本</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/teachingmethod/{{ $va->id }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="編輯">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ $va->yerly }}</td>
                                                    <!-- 刪除 -->
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
    <script language="javascript">
    function doClear(){
      document.all.name.value = "";
      document.all.yerly.value = "";
    }    
    
    function BatchDelSelected() //刪除用
          {
               if (confirm("確定刪除所選資料 ??")) {
                    //var rows = document.getElementById('table_data').rows;
                    var a = document.getElementsByName('classdel');
                    //var states = “”;
                    var  table = document.getElementById('table_data');

                    for(var i=0;i<a.length;i++)
                    {
                        if(a[i].checked){
                            var row = a[i].parentElement.parentElement.rowIndex;
                            //states = rows[row].cells[1].innerHTML “;”;
                            //  alert(a[i].value);
                            //  alert(rows[row].cells[1].innerHTML);
                        };
                     }
               };
          };
    </script>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection