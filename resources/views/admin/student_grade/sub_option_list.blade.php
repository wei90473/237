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
                        {!! Form::open(['method' => 'put', 'id' => 'grade_form']) !!}
                        <div class="table-responsive" style="height:800px;margin-top:10px;">
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>學號</th>
                                        <th>姓名</th>
                                        @foreach($grade_sub_options as $grade_sub_option)
                                            <th width="100">{{ $grade_sub_option->name }}</th>
                                        @endforeach 
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($t04tb->t13tbs as $t13tb)
                                        <tr>
                                            <td>{{ $t13tb->no }}</td>
                                            <td>{{ $t13tb->m02tb->cname }}</td>
                                            @foreach($grade_sub_options as $sub_option)
                                            <td>
                                                <input type="text" name="grade[{{ $sub_option->id }}][{{ $t13tb->idno }}]" class="form-control" 
                                                        value="{{ (empty($sub_option->student_grades[$t13tb->idno])) ? '': $sub_option->student_grades[$t13tb->idno]->grade }}">
                                            </td>
                                            @endforeach                                                 
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>  
                        {!! Form::close() !!}

                        <!-- include('admin/layouts/list/pagination', ['paginator' => $t27tbs, 'queryData' => $queryData])                    -->
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" onclick="check_sub_option()">保存</button>
                        <a href="/admin/student_grade/input_grade/{{$t04tb->class}}/{{$t04tb->term}}">
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
    function check_sub_option()
    {
        $('#grade_form').submit();
    }
</script>
@endsection