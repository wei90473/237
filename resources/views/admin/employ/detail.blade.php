@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'employ';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座聘任處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座聘任處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座聘任處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            班號 : {{ $class_data['class'] }}<br>
                                            辦班院區 : {{ config('app.branch.'.$class_data['branch']) }}<br>
                                            班別名稱 : {{ $class_data['name'] }}<br>
                                            期別 : {{ $class_data['term'] }}<br>
                                            分班名稱 : {{ $class_data['branchname'] }}<br>
                                            班別類型 : {{ config('app.process.'.$class_data['process']) }}<br>
                                            受訓期間 : {{ $class_data['sdate'] }} ~ {{ $class_data['edate'] }}<br>
                                            班務人員 : {{ $class_data['sponsor'] }}
                                        </li>
                                    </ul>
                                    <br>
                                    <div class="float-left search-float" style="min-width: 1000px;">
                                        <form method="get" id="search_form">
                                        <input type="hidden" id="class" name="class" class="form-control" value="<?=$class_data['class'];?>">
                                        <input type="hidden" id="term" name="term" class="form-control" value="<?=$class_data['term'];?>">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                            <a href="/admin/employ/create?class={{ $class_data['class'] }}&term={{ $class_data['term'] }}">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新增聘任資料</button>
                                            </a>
                                            <a href="/admin/employ/sort?class={{ $class_data['class'] }}&term={{ $class_data['term'] }}">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">設定講座排序</button>
                                            </a>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="data_table" class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">功能</th>
                                                <th>課程名稱</th>
                                                <th>講座姓名</th>
                                                <th>類型</th>
                                                <th>講課酬勞合計</th>
                                                <th>交通費及住宿費合計</th>
                                                <th>扣繳稅額合計</th>
                                                <th>實付總計</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($teacher_list as $row)
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/employ/{{ $row['id'] }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $row['name'] }} ( {{ $row['date'] }} ) </td>
                                                    <td>{{ $row['cname'] }}</td>
                                                    <td><?=($row['type']=='1')?'講師':'助教';?></td>
                                                    <td>{{ $row['teachtot'] }}</td>
                                                    <td>{{ $row['tratot'] }}</td>
                                                    <td>{{ $row['deductamt'] }}</td>
                                                    <td>{{ $row['totalpay'] }}</td>

                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">


    </script>


@endsection