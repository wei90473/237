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
                        <div>

                            {!! Form::open(['method' => 'put', 'enctype' => "multipart/form-data"]) !!}
                            <div class="form-group row">
                                <div class="col-4" style="max-width:300px">
                                    <input type="file" name="import_file" class="form-control" accept=".xls, .xlsx">
                                    <input type="hidden" name="action" value="import">
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-right:10px;">批次匯入</button>
                                <button type="button" class="btn btn-primary" onclick="location.href='/import_example/student_grade/成績處理-母項目匯入範例.zip'">下載匯入範例檔</button>
                            </div>
                            {!! Form::close() !!}
                            <div class="row col-12" style="margin-bottom:10px;">
                                <button class="btn btn-primary" onclick="addMainOption()">新增母項目</button>
                            </div>                            
                        </div>

                        {!! Form::open(['method' => 'put', 'id' => 'main_option']) !!}
                            <input type="hidden" name="action" value="updateOrCreate">
                            @foreach($grade_main_options as $grade_main_option)
                             <div class="form-group row col-12">
                                <label class="col-form-label">母項目名稱：</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control main_option" name="main_option[{{$grade_main_option->id}}][name]" value="{{ $grade_main_option->name }}">
                                </div>
                                <label class="col-form-label">佔項目比(%)：</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control main_option" name="main_option[{{$grade_main_option->id}}][persent]" value="{{ $grade_main_option->persent }}">
                                </div> 
                                <div style="margin-right:10px;">
                                    <a href="/admin/student_grade/main_option/{{$grade_main_option->id}}">
                                        <button type="button" style="font-size: 16px;" class="btn btn-primary">設定子項目</button>                             
                                    </a>
                                </div>
                                <div>
                                    <button type="button" style="font-size: 16px;" class="btn btn-danger" onclick="deleteMainOption(this, '{{ $grade_main_option->name }}')">刪除</button>
                                </div>                                  
                            </div>
                            @endforeach
                        {!! Form::close() !!}


                        <!-- include('admin/layouts/list/pagination', ['paginator' => $t27tbs, 'queryData' => $queryData])                    -->
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" onclick="check_main_option()">保存</button>
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
    var new_option_num = 0;
    var origin_option_num = 0;
    function addMainOption()
    {
        var main_option = '<div class="form-group row col-12">' + 
                            '<label class="col-form-label">母項目名稱：</label>' + 
                            '<div class="col-sm-2">' + 
                                '<input type="text" class="form-control main_option" name="new_main_option[' + new_option_num + '][name]" value="">' + 
                            '</div>' + 
                            '<label class="col-form-label">佔項目比(%)：</label>' + 
                            '<div class="col-sm-2">' + 
                                '<input type="text" class="form-control main_option" name="new_main_option[' + new_option_num  + '][persent]" value="">' + 
                            '</div>' + 
                            '<div>' +
                                '<button type="button" style="font-size: 16px;" class="btn btn-danger" onclick="deleteMainOption(this)">刪除</button>'
                            '</div>' + 
                          '</div>';
        new_option_num++;
        $('#main_option').append(main_option);
    }

    function check_main_option()
    {
        var new_options = $('.main_option');
        for(let i=0; i<new_options.length; i++){
            if(new_options[i].value === ''){
                alert('母項目名稱或佔項目比(%)不得為空');
                return false;
            }
        }
        $('#main_option').submit()
    }

    function deleteMainOption(option_button, name)
    {
        if (confirm('確定要刪除「 ' + name + ' 」嗎? 將會連同成績一起刪除')){  
            option_button.parentElement.parentElement.remove();
        }
    }    
</script>
@endsection