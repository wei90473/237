@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>訓前訓後訓中問卷設定</h3>
                    </div>

                    <div class="card-body">
                        <div style="border: 1px solid #FFF; padding: 10px; padding-left: 0px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            期別：{{ $t04tb->term }}<br>
                            分班名稱：<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            委訓機關：{{ $t04tb->client }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：
                        </div>
                        <div style="padding: 5px 5px 5px 0px;">
                            <button onclick="location.href='/admin/trainQuestSetting/quest/create/{{$t04tb->class}}/{{$t04tb->term}}'" class="btn btn-primary">新增問卷</button>
                        </div>
                        <div class="table-responsive">
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="130">功能</th>
                                        <th width="170">問卷種類</th>
                                        <th>Google問卷網址</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $setting)
                                    <tr>
                                        <td><a href='/admin/trainQuestSetting/quest/edit/{{$setting->id}}'><button class="btn btn-primary"><i class="fa fa-pencil">編輯</i></button></a></td>
                                        <td>{{ $setting->type_name }}</td>
                                        <td><a target="_blank" href="{{$setting->url}}">{{ $setting->url }}</a></td>
                                    </tr>
                                    @endforeach 
                                </tbody>
                            </table>
                            @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                        </div>                        
                    </div>
                    <div class="card-footer">
                        <a href="/admin/trainQuestSetting/">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>                     
                </div>
            </div>
        </div>
    </div>
</div>

@endsection