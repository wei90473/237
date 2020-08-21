@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'web_simulation';?>
<style>
    .search-float input{
        min-width: 1px;
    }
</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">角色模擬</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">角色模擬</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="container">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>角色模擬</h3>
                    </div>

                    <div class="card-body">
                        <div>
                            {{ Form::open(['method' => 'post']) }}
                                <div class="search-float">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">身分證</label>
                                                </div>
                                                <input class="form-control" type="text" name="idno" value="{{ $queryData['idno'] }}">
                                            </div>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                                <a href="/admin/role_simulate">
                                                    <button type="button" class="btn btn-primary">重設條件</button>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            {{ Form::close() }}
                        </div>

                        <input type="hidden" name="prove" value="S">
                        <div class="table-responsive" style="height:800px;">
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">
                                            @foreach($data as $row)

                                                @if($row['type'] ==  'admin')
                                                <button class="btn btn-primary" onclick="simulate('{{ $row['idno'] }}', '{{ $row['type'] }}')">模擬訓練承辦人</button>
                                                @endif

                                                @if($row['type'] ==  'teacher')
                                                <button class="btn btn-primary" onclick="simulate('{{ $row['idno'] }}', '{{ $row['type'] }}')">模擬講師</button>
                                                @endif

                                                @if($row['type'] ==  'student')
                                                <button class="btn btn-primary" onclick="simulate('{{ $row['idno'] }}', '{{ $row['type'] }}')">模擬學員</button>
                                                @endif

                                                @if($row['type'] ==  'IntrustUser')
                                                <button class="btn btn-primary" onclick="simulate('{{ $row['idno'] }}', '{{ $row['type'] }}')">模擬委訓單位承辦人</button>
                                                @endif

                                            @endforeach
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    {{ Form::open(['method' => 'post', 'url' => '/admin/web_simulation/simulate', 'id' => 'simulate_form']) }}
                        <input type="hidden" name="simulate_user_id" value="">
                        <input type="hidden" name="simulate_user_type" value="">
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')
<script>
    function simulate(user_id, type)
    {
        $("input[name=simulate_user_id]").val(user_id);
        $("input[name=simulate_user_type]").val(type);
        $("#simulate_form").submit();
    }
</script>
@endsection