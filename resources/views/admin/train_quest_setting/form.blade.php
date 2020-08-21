@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/form.css') }}" >
<?php $_menu = 'train_quest_setting';?>

<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">訓前訓後訓中問卷設定</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">訓前訓後訓中問卷設定</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        @if (isset($train_quest_setting))
            {!! Form::open([ 'method'=>'put', 'url'=>"/admin/trainQuestSetting/quest/{$train_quest_setting->id}", 'id'=>'form', "enctype"=>"multipart/form-data"]) !!}
        @else
            {!! Form::open([ 'method'=>'post', 'url'=>"/admin/trainQuestSetting/{$t04tb->class}/{$t04tb->term}", 'id'=>'form', "enctype"=>"multipart/form-data"]) !!}
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>訓前訓後訓中問卷設定</h3>
                    </div>

                    <div class="card-body">
                        <div style="border: 1px solid #000; padding: 10px; margin-bottom: 20px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            期別：{{ $t04tb->term }}<br>
                            分班名稱：{{ $t04tb->t01tb->branchname }}<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            委訓機關：{{ $t04tb->client }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：
                        </div>
                        <div class="float-left search-float" style="min-width: 100%; margin-bottom: 20px;">
                            <div class="float-md mobile-100 row mr-1 mb-2">
                                <div class="input-group col-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">問卷種類</span>
                                    </div>
                                    <select class="browser-default custom-select field_ques_type" name="quest_type">
                                        <option value="">請選擇</option>
                                        @foreach(config('app.train_quest_type') as $key => $type)
                                            <option value="{{ $key }}" {{ (isset($train_quest_setting) && $key == $train_quest_setting->type) ? 'selected' : '' }} >{{ $type }}</option>
                                        @endforeach
                                    </select>                                        
                                </div>
                            </div>                                
                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                <div class="input-group col-7">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">google問卷網址</span>
                                    </div>
                                    <input type="text" id="class" name="quest_url" class="field_class form-control" autocomplete="off" value="{{ (isset($train_quest_setting)) ? $train_quest_setting->url : ''}}" >
                                </div>
                            </div>

                            <div style="padding: 5px 5px 5px 0px;">
                                <label>問卷題目檔案：</label>
                                <input type="file" name="question_file" value="點選上傳檔案">
                            </div>
                            <div class="table-responsive">
                                <table id="data_table" class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>檔案名稱</th>
                                            <th width="200">上傳日期</th>
                                            <th class="text-center" width="130">功能</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($train_quest_setting->trainQuestionQuestionnaires))
                                            @foreach($train_quest_setting->trainQuestionQuestionnaires as $questionnaires)
                                                <tr>
                                                    <td><a href="/Uploads/train_questionnaire/question/{{$questionnaires->path}}">{{ $questionnaires->origin_name }}</a></td>
                                                    <td>{{ $questionnaires->created_at }}</td>
                                                    <td style="text-align:center;"><button type="button" class="btn btn-danger" onclick="deleteQuestionnaire({{$questionnaires->id}}, '{{ $questionnaires->origin_name }}')">刪除</button></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div style="padding: 5px 5px 5px 0px;">
                                <label>問卷結果檔案：</label>
                                <input type="file" name="answer_file" value="點選上傳檔案">
                            </div>
                            <div class="table-responsive">
                                <table id="data_table" class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>檔案名稱</th>
                                            <th width="200">上傳日期</th>
                                            <th class="text-center" width="130">功能</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($train_quest_setting->trainAnswerQuestionnaires))
                                            @foreach($train_quest_setting->trainAnswerQuestionnaires as $questionnaires)
                                                <tr>
                                                    <td><a href="/Uploads/train_questionnaire/answer/{{$questionnaires->path}}">{{ $questionnaires->origin_name }}</a></td>
                                                    <td>{{ $questionnaires->created_at }}</td>
                                                    <td style="text-align:center;"><button type="button" class="btn btn-danger" onclick="deleteQuestionnaire({{$questionnaires->id}}, '{{ $questionnaires->origin_name }}')">刪除</button></td>
                                                </tr>
                                            @endforeach    
                                        @endif                                        
                                    </tbody>
                                </table>
                            </div>   
                            
                        </div>                                                                       
                    </div>
                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        @if (isset($train_quest_setting))
                            <button type="button" onclick="deleteSetting({{$train_quest_setting->id}})" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button>
                        @endif
                        <a href="/admin/trainQuestSetting/setting/{{$t04tb->class}}/{{$t04tb->term}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div> 
                    {!! Form::close() !!}

                    {!! Form::open(['method'=>'delete', 'url'=>"", 'id'=>'deleteForm']) !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function deleteQuestionnaire(id, name){
        if (confirm("確定要刪除 " + name + " 嗎？")){
            var delete_form = document.getElementById('deleteForm');
            delete_form.action = "/admin/trainQuestSetting/quest/delete/" + id;
            document.getElementById('deleteForm').submit();
        }
    }

    function deleteSetting(id){
        if (confirm("確定要刪除此問卷嗎？，將會連同問卷檔案一起刪除")){
            var delete_form = document.getElementById('deleteForm');
            delete_form.action = "/admin/trainQuestSetting/delete/" + id;
            document.getElementById('deleteForm').submit();
        }        
    }
</script>

@endsection