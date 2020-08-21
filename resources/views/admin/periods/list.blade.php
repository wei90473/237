@extends('admin.layouts.layouts')
@section('content')

<style>
.btn {
    margin-right: 5px;
    border-color: #dee2e6;
}
</style>


    <?php $_menu = 'periods';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">開班期數處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">開班期數處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>開班期數處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">

                                            <!-- 班別名稱 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">年度</span>
                                                        </div>
                                                        <select type="text" id="yerly" name="yerly" class="browser-default custom-select"  value="{{ $queryData['yerly'] }}" style="min-width: 80px; flex:0 1 auto">
                                                            @for($i = (int)date("Y")-1910; $i >= 90; $i--)
                                                                <option value="{{ str_pad($i, 3, '0', STR_PAD_LEFT) }}" 
                                                                @if($i == $queryData['yerly']) 
                                                                    selected 
                                                                @elseif ((int)date("Y")-1911 == $i && empty($queryData['yerly']))
                                                                    selected
                                                                @endif 
                                                                >{{$i}}</option>
                                                            @endfor
                                                        </select>

                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">辦班院區</span>
                                                        </div>
                                                        <select type="text" id="branch" name="branch" class="browser-default custom-select"  value="{{ $queryData['branch'] }}" style="min-width: 120px; flex:0 1 auto">
                                                            <option value="1" @if(1 == $queryData['branch']) selected @endif >臺北院區</option>
                                                            <option value="2" @if(2 == $queryData['branch']) selected @endif >南投院區</option>
                                                        </select>                                                  
                                                    </div>     
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">班號</span>
                                                        </div>
                                                        <input type="text" id="class" name="class" class="form-control" autocomplete="off" value="{{ $queryData['class'] }}" style="min-width: 120px; flex:0 1 auto">

                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">班別名稱</span>
                                                        </div>
                                                        <input type="text" name="class_name" class="form-control" autocomplete="off" value="{{ $queryData['class_name'] }}" style="min-width: 500px; flex:0 1 auto">                                                    
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                                        <input type="button" class="btn btn-primary" value="重設條件" style="min-width:auto;" onclick="window.location='/admin/periods'">           
                                                        <input type="button" class="btn btn-primary" value="批次線上更新分配人數" onclick="window.location='/admin/periods/action/online_update'">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">
                                        </form>
                                    </div>

                                    <div class="float-md-right">

                                    </div>

                                    <div class="table-responsive">
                                    <form action="/admin/periods/action/assign" method="POST" onsubmit="return check_assign_submit()">
                                        {{ csrf_field() }} 
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>功能</th>
                                                <th>班號</th>
                                                <th>班別名稱</th>
                                                <th>總分配名額</th>
                                                <th>已分配期別</th>
                                                <th>累積已分配人數</th>
                                                <th>分配人數</th>
                                                <th>開始期別</th>
                                                <th>結束期別</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <tr>
                                                     <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/periods/{{ $va->class }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>                                                
                                                    <td>
                                                        <input type="hidden" name="class[]" value="{{ $va->class }}">
                                                        {{ $va->class }}
                                                    </td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>
                                                        {{ $va->t02tbs->sum('quota') }} 
                                                    </td>
                                                    <td>
                                                        {{ join(', ', $va->t03tbs->pluck('term')->unique()->toArray()) }}
                                                    </td>
                                                    <td>
                                                        {{ $va->t03tbs->pluck('quota')->sum() }}
                                                    </td>
                                                    <td>
                                                        <input type="text" name="assign[{{ $va->class }}][assign_num]" class="form-control" autocomplete="off" value="" style="max-width: 70px; flex:0 1 auto">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="assign[{{ $va->class }}][start_term]" class="form-control" autocomplete="off" value="" style="max-width: 70px; flex:0 1 auto">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="assign[{{ $va->class }}][end_term]" class="form-control" autocomplete="off" value="" style="max-width: 70px; flex:0 1 auto">
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <div style="margin-top:10px;">
                                            <button class="btn mobile-100 mb-3 mb-md-0 btn-primary">執行分配</button>
                                        </div>
                                    </div>
                                    </form>
                                    @if($data)
                                        <!-- 分頁 -->
                                        @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                                    @endif 

                                </div>
                            </div>
                        </div>
                        @if($data)
                            <!-- 列表頁尾 -->
                            @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])
                        @endif 

                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    function check_assign_submit(){
        return confirm("確定要執行嗎？");
    }
</script>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection