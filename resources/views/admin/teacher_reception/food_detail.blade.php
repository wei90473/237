@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'teacher_reception';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">用餐安排</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">用餐安排</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>用餐安排</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                        <input type="hidden" id="search" name="search" class="form-control" value="search">
                                            <!-- 日期 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">日期</span>
                                                    </div>
                                                     <input class="date form-control" value="{{$queryData['date']}}" type="text" id="date" name="date">
                                                     <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>功能</th>
                                                <th>姓名</th>
                                                <th>授課時間</th>
                                                <th>早</th>
                                                <th>午</th>
                                                <th>晚</th>
                                                <th>住宿別</th>
                                                <th>單位</th>
                                                <th>職稱</th>
                                                <th>班期</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)

                                                <tr>
                                                    <td class="text-center">
                                                        <a href="/admin/teacher_reception/{{ $va->t09tb_id }}_{{ $va->class_weeks_id }}_food_{{ $queryData['date'] }}/edit3" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>
                                                        <?php foreach($va->time as $time){?>
                                                        <?=$time;?>
                                                        <br>
                                                        <?php } ?>
                                                    </td>
                                                    <td>{{ $va->breakfast }}</td>
                                                    <td>{{ $va->lunch }}</td>
                                                    <td>{{ $va->dinner }}</td>
                                                    <td>{{ $va->room }}</td>
                                                    <td>{{ $va->dept }}</td>
                                                    <td>{{ $va->position }}</td>
                                                    <td>{{ $va->class_name }}</td>
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

@endsection
@section('js')
<script>
$( function() {
    $("#date").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
    });
    $('#datepicker1').click(function(){
        $("#date").focus();
    });

  } );
</script>
@endsection