@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'leave';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">學員請假處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">學員請假處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員請假處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div style="border: 1px solid #000; padding: 10px;margin-bottom:10px; ">
                                        訓練班別：{{ $t04tb->t01tb->name }}<br>
                                        期別：{{ $t04tb->term }}<br>
                                        分班名稱：<br>
                                        班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                                        委訓機關：{{ $t04tb->client }}<br>
                                        起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                                        班務人員：
                                    </div>                                
                                    <div>
                                        <!-- 新增 -->
                                        <a href="/admin/leave/create/{{$t04tb->class}}/{{$t04tb->term}}">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                        </a>
                                        <a href="/admin/leave/suspendClasses/{{$t04tb->class}}/{{$t04tb->term}}">
                                            <button type="button" class="btn btn-primary btn-sm mb-3">停班課處理</button>
                                        </a>                                        
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>

                                                <th>學號</th>
                                                <th>姓名</th>
                                                <th>假別</th>
                                                <th>時數</th>
                                                <th>起始日期</th>
                                                <th>結束日期</th>
                                                <th>起始時間</th>
                                                <th>結束時間</th>
                                                <th class="text-center" width="70">修改</th>
                                                <th class="text-center" width="70">刪除</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            
                                            @foreach($data as $va)

                                                <tr>

                                                    <td>{{ $va->no }}</td>
                                                    <td>{{ $va->cname }}</td>
                                                    <td>{{ config('database_fields.t14tb')['type'][$va->type] }}</td>
                                                    <td>{{ $va->hour }}</td>
                                                    <td>{{ $base->showDate($va->sdate) }}</td>
                                                    <td>{{ $base->showDate($va->edate) }}</td>
                                                    <td>{{ $va->stime }}</td>
                                                    <td>{{ $va->etime }}</td>

                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/leave/{{ $va->id }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>

                                                    <!-- 刪除 -->
                                                    <td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/leave/{{ $va->id }}');" data-toggle="modal" data-target="#del_modol" >
                                                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                                                <i class="fa fa-trash text-danger"></i>
                                                            </span>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <div>
                                            <a href="/admin/leave/class_list?{{$listHistory}}"><button class="btn btn-danger">回上一頁</button></a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- 列表頁尾 -->

                        <div class="card-footer">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
    <script>

    </script>
@endsection