@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'student_grade';?>
<style>
.search-float input{
    min-width:1px;
}
</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">成績輸入處理</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">成績輸入處理</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>成績輸入處理</h3>
                    </div>

                    <div class="card-body">
                        <div style="border: 1px solid #000; padding: 10px; margin-bottom:10px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            期別：{{ $t04tb->term }}<br>
                            分班名稱：<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            委訓機關：{{ $t04tb->client }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：
                        </div>
                        <div>
                            <div class="row col-12" style="margin-bottom:10px;">
                                <a href="/admin/student_grade/downloadExportExample/{{ $t04tb->class }}/{{ $t04tb->term }}">
                                    <button class="btn btn-primary">下載本班成績匯入檔</button>
                                </a>
                            </div>
                            {!! Form::open(['method' => 'post', 'url' => "/admin/student_grade/import_grade/{$t04tb->class}/{$t04tb->term}", "enctype" => "multipart/form-data"]) !!}
                            <div class="form-group row">
                                <div class="col-4" style="max-width:300px">
                                    <input type="file" name="import_file" class="form-control" accept=".xls">
                                </div>
                                <button type="submit" class="btn btn-primary">批次匯入</button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="table-responsive" style="height:800px;margin-top:10px;">
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>母項目</th>
                                        <th>比例</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grade_main_options as $grade_main_option)
                                    <tr>
                                        <td>{{ $grade_main_option->name }}</td>
                                        <td>{{ $grade_main_option->persent }}%</td>
                                        <td><a href="/admin/student_grade/input_grade/main_option/{{$grade_main_option->id}}"><button class="btn btn-primary">子項目</button></a></td>
                                    </tr>       
                                    @endforeach                          
                                </tbody>
                            </table>
                        </div> 

                        <!-- include('admin/layouts/list/pagination', ['paginator' => $t27tbs, 'queryData' => $queryData])                    -->
                    </div>
                    <div class="card-footer">
                        <a href="/admin/student_grade/class_list?class={{ $t04tb->class }}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>                     
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>

</script>
@endsection