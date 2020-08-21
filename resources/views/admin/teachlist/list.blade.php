@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'teachlist';?>
<style>
    .panel-heading .accordion-toggle {
        display: inline-block;
        text-align: center;
        color: rgb(255, 255, 255);
        font-size: 16px;
        box-sizing: border-box;
        line-height: 1.8em;
        vertical-align: middle;
        font-family: 微軟正黑體, "Microsoft JhengHei", Arial, Helvetica, sans-serif !important;
        background: rgb(250, 160, 90);
        padding: 5px 20px;
        border-width: initial;
        border-style: none;
        border-color: initial;
        border-image: initial;
        margin: 2px 0px;
        border-radius: 6px;
        margin-bottom: 10px;
    }
    .panel-heading .accordion-toggle:hover {
        background-color: #f79448 !important;
        border: 1px solid #f79448 !important;
            -webkit-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        opacity: 1;
    }
    .panel-heading .accordion-toggle::before {
        background-color: inherit; !important;
    }
    .panel-heading .accordion-toggle.collapsed::before {
        background-color: inherit; !important;
    }
    .search-float input,
    .search-float .select2-selection--single, .search-float select {
        min-width: initial;
    }
</style>
    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">維護教法調查班別</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">維護教法調查班別</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>維護教法調查班別</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="min-width: 1200px;">
                                        <form method="get" id="search_form">
                                        <input type="hidden" id="search" name="search" class="form-control" value="search">
                                            <!-- 年度 -->
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
                                                <!-- 班號 -->
                                                <div class="input-group col-4">
                                                     <div class="input-group-prepend">
                                                        <span class="input-group-text">班號</span>
                                                    </div>
                                                    <input type="text" id="class" name="class" class="form-control" autocomplete="off" value="{{ $queryData['class'] }}">
                                                </div>
                                                <!-- 班別名稱 -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ $queryData['name'] }}">
                                                </div>
                                                </div>
                                            </div>
                                          
                                            <!-- 辦班院區 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
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
                                            </div>
<!-- 進階/簡易搜尋開始 -->
                                            <div class="panel-group" id="accordion">
                                            <header class="panel-heading">

                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#search"> </a>

                                                </header>
                                                <footer id="search" class="panel-collapse collapse">
<!-- 進階/簡易搜尋開始 -->                                           
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">上課地點</span>
                                                    </div>
                                                    <select class="form-control select2" name="sitebranch">
                                                        <option value="">請選擇</option>
                                                        @foreach(config('app.branch') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['sitebranch'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                        <option value="3" {{ $queryData['sitebranch'] == $key? 'selected' : '' }} >外地</option>
                                                    </select>
                                                </div>
                                                <!-- **分班名稱 -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">分班名稱</span>
                                                    </div>
                                                    <input type="text" id="branchname" name="branchname" class="form-control" autocomplete="off" value="{{ $queryData['branchname'] }}">
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別類型</span>
                                                    </div>
                                                    <select class="form-control select2" name="process">
                                                        <option value="">全部</option>
                                                        @foreach(config('app.process') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['process'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 班務人員 -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班務人員</span>
                                                    </div>
                                                    <?php $list = $base->getSponsor(); ?>
                                                    <select id="sponsor" name="sponsor" class="form-control select2 ">
                                                        <option value="">請選擇</option>
                                                        @foreach($list as $key => $va)
                                                            <option value="{{ $key }}" {{ old('sponsor', (isset($queryData['sponsor']))? $queryData['sponsor']: '') == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 訓練性質 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">訓練性質</span>
                                                    </div>
                                                    <select class="form-control select2" name="traintype">
                                                        <option value="">請選擇</option>
                                                        @foreach(config('app.traintype') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['traintype'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 班別性質 -->
                                                <?php $typeList = $base->getSystemCode('K')?>
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別性質</span>
                                                    </div>
                                                    <select class="form-control select2" name="type">
                                                        <option value="">請選擇</option>
                                                        @foreach($typeList as $code => $va)
                                                            <option value="{{ $code }}" {{ $queryData['type'] == $code? 'selected' : '' }}>{{ $va['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- **類別1 -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">類別1</span>
                                                    </div>
                                                    <?php $categoryoneList = $base->getSystemCode('M')?>
                                                    <select id="categoryone" name="categoryone" class="form-control select2">
                                                    <option value="" selected>請選擇</option>
                                                    @foreach($categoryoneList as $code => $va)
                                                        <option value="{{ $va['code'] }}" {{ $queryData['categoryone'] == $va['code']? 'selected' : '' }} >{{ $va['name'] }}</option>
                                                    @endforeach
                                                    </select>
                                                </div>
                                            </div>
<!-- 進階/簡易搜尋結束 -->
                                            </footer>
                                            </div>
<!-- 進階/簡易搜尋結束 -->                                            
                                            <div class="float-left">
                                            <!-- 查詢 -->
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                                <!-- 重設條件 -->
                                                <button class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()" type="button">重設條件</button>
                                            </div>  
                                        </form>
                                    </div>
                                    <!-- 排序 -->
                                    <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                    <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                    <!-- 每頁幾筆 -->
                                    <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">
                                    <div class="float-md-right">
                                    </div>

                                    {!! Form::open([ 'method'=>'put', 'url'=>'/admin/teachlist/edit', 'id'=>'form']) !!}

                                        
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                <tr>
                                                    <th class="text-center" width="80"><input type="checkbox" onclick="checkall(this)" >不納入調查</th>
                                                    <th>班號</th>
                                                    <th>辦班院區</th>
                                                    <th>訓練班別</th>
                                                    <th>分班名稱</th>
                                                    <th>班別類型</th>
                                                    <th>班務人員</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if (isset($data) )
                                                @foreach($data as $va)
                                                    <tr>
                                                        <!-- 修改 -->
                                                        <td class="text-center">
                                                            <input type="hidden" name="{{ $va->class }}" value="{{ $va->teaching=='N'? 'N':'Y'}}">
                                                            <input type="checkbox" name="teaching{{ $va->class }}" value="1" autocomplete="off" {{ $va->teaching=='N'? 'checked':''}} >
                                                        </td>
                                                        <td>{{ $va->class }}</td>
                                                        <td>{{ config('app.branch.'.$va->branch) }}</td>
                                                        <td>{{ $va->name }}</td>
                                                        <td></td>
                                                        <td>{{ config('app.process.'.$va->process) }}</td>
                                                        <td>{{ $va->username }}</td>
                                                    </tr>
                                                @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-primary btn-sm mb-3" onclick="check()"><i class="fa fa-save fa-lg pr-2"></i>儲存</button>
                                        </div>
                                    {!! Form::close() !!}
                                    @if (isset($data) )
                                        <!-- 分頁 -->
                                        @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if (isset($data) )
                            <!-- 列表頁尾 -->
                            @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])
                        @endif
                        

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection
<script>
var checked = 0;
    //全選
    function checkall(e)
    {
        for(i=0; i<$("input[type=checkbox]").length; i++ ){
             $("input[type=checkbox]")[i].checked = e.checked;
        }
    }
    function check(){
        if(confirm('將同步刪除不納入調查班期的講座教學教法資料!是否繼續?')){
            $("#form").submit();
        }
    }
    function doClear(){
      var d = new Date();
      var yerly = (d.getFullYear() - 1911);
      document.all.yerly.value = yerly;
      document.all.class.value = "";
      document.all.name.value = "";
      document.all.branchname.value = "";
      $("select[name=sitebranch]").val('').trigger("change");
      $("select[name=branch]").val('').trigger("change");
      $("select[name=process]").val('').trigger("change");
      $("select[name=sponsor]").val('').trigger("change");
      $("select[name=type]").val('').trigger("change");
      $("select[name=traintype]").val('').trigger("change");
      $("select[name=categoryone]").val('').trigger("change");
    }
</script>