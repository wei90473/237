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
                                </div>
                                <input type="hidden" name="action" value="import">
                                <button type="submit" class="btn btn-primary" style="margin-right:10px;">批次匯入</button>
                                <button type="button" class="btn btn-primary" onclick="location.href='/import_example/student_grade/成績處理-母項目匯入範例.zip'">下載匯入範例檔</button>
                            </div>
                            {!! Form::close() !!}
                            <div class="row col-12" style="margin-bottom:10px;">
                                <div style="margin-right:10px;">母項目名稱：{{$main_option->name}}</div>
                                <button class="btn btn-primary" onclick="addMainOption()">新增子項目</button>
                            </div>   
                            <div class="row col-12" style="margin-bottom:10px;">
                                子項目最多五個，總佔比加總須為100
                            </div>                                                      
                        </div>

                        {!! Form::open(['method' => 'put', 'id' => 'sub_option']) !!}
                            <input type="hidden" name="action" value="updateOrCreate">
                            @foreach($grade_sub_options as $grade_sub_option)
                             <div class="form-group row col-12">
                                <label class="col-form-label">子項目名稱：</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control sub_option" name="sub_option[{{$grade_sub_option->id}}][name]" value="{{$grade_sub_option->name}}">
                                </div>
                                <label class="col-form-label">佔項目比(%)：</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control sub_option persent" name="sub_option[{{$grade_sub_option->id}}][persent]" value="{{$grade_sub_option->persent}}">
                                </div> 
                                <div>
                                    <button type="button" style="font-size: 16px;" class="btn btn-danger" onclick="deleteSubOption(this, '{{ $grade_sub_option->name }}')">刪除</button>
                                </div>                                 
                            </div>
                            @endforeach
                        {!! Form::close() !!}

                        <!-- include('admin/layouts/list/pagination', ['paginator' => $t27tbs, 'queryData' => $queryData])                    -->
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" onclick="checkSubOption()">保存</button>
                        <a href="/admin/student_grade/setting/{{$main_option->class}}/{{$main_option->term}}">
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
        var sub_option = '<div class="form-group row col-12">' + 
                            '<label class="col-form-label">子項目名稱：</label>' + 
                            '<div class="col-sm-2">' + 
                                '<input type="text" class="form-control sub_option" name="new_sub_option[' + new_option_num + '][name]" value="">' + 
                            '</div>' + 
                            '<label class="col-form-label">佔項目比(%)：</label>' + 
                            '<div class="col-sm-2">' + 
                                '<input type="text" class="form-control sub_option persent" name="new_sub_option[' + new_option_num  + '][persent]" value="">' + 
                            '</div>' + 
                            '<div>' +
                                '<button type="button" style="font-size: 16px;" class="btn btn-danger" onclick="deleteSubOption(this)">刪除</button>'
                            '</div>' +
                          '</div>';
        new_option_num++;
        $('#sub_option').append(sub_option);
    }

    function checkSubOption()
    {
        var new_options = $('.sub_option');
        for(let i=0; i<new_options.length; i++){
            if(new_options[i].value === ''){
                alert('子項目名稱或佔項目比(%)不得為空');
                return false;
            }
        }

        let persents = $(".persent");
        let total = 0;
        for(let i=0; i<persents.length; i++){
            total += parseInt(persents[i].value);
        }

        if (total != 100){
            alert("子項目佔項目比未滿或超過100%");
            return false;
        }

        $('#sub_option').submit();

    }

    function deleteSubOption(option_button, name)
    {
        if (confirm('確定要刪除「 ' + name + ' 」嗎? 將會連同成績一起刪除')){  
            option_button.parentElement.parentElement.remove();
        }
    }
</script>
@endsection