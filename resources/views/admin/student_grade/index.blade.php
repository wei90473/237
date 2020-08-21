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

                        <div class="table-responsive" style="height:800px;margin-top:10px;">
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>學號</th>
                                        <th>姓名</th>
                                        @foreach($grade_main_options as $grade_main_option)
                                            <th>{{ $grade_main_option->name."( ".$grade_main_option->persent."% )" }}</th>
                                        @endforeach
                                        <th>總分</th>
                                        <th>名次</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($t04tb->t13tbsForFormal as $t13tb)
                                        <tr>
                                            <td>{{ $t13tb->no }}</td>
                                            <td>{{ $t13tb->m02tb->cname }}</td>
                                            @foreach($grade_main_options as $grade_main_option)
                                            <td>
                                                @if(!empty($grades['main_grades'][$t13tb->idno][$grade_main_option->id]->real_grade))
                                                    {{ $grades['main_grades'][$t13tb->idno][$grade_main_option->id]->real_grade }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            @endforeach
                                            <td>
                                                @if(!empty($grades['total_grades'][$t13tb->idno]->final_grade))
                                                    {{ $grades['total_grades'][$t13tb->idno]->final_grade }}
                                                @else
                                                    0
                                                @endif                                            
                                            </td>
                                            <td>
                                                @if(!empty($grades['total_grades'][$t13tb->idno]->rank))
                                                    {{ $grades['total_grades'][$t13tb->idno]->rank }}
                                                @endif                                            
                                            </td>                                            
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>  


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