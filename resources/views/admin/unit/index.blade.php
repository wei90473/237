@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'arrangement';?>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">課程配當安排</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li>課程配當安排</li>
                    <li class="active">單元維護</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>單元維護</h3>
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
                        <div style="margin-bottom: 10px;">
                            <a href='/admin/unit/create/{{ $t04tb->class }}/{{ $t04tb->term }}'>
                                <button class="btn btn-primary">新增單元</button>
                            </a>                            
                        </div>                        
                        <div class="table-responsive">
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="100">功能</th>
                                        <th>單元編號</th>
                                        <th>單元名稱</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($t04tb->t05tbs))
                                        @foreach($t04tb->t05tbs as $t05tb)
                                        <tr>
                                            <td class="text-center">
                                                <a href='/admin/unit/edit/{{ $t04tb->class }}/{{ $t04tb->term }}/{{ $t05tb->unit }}'>
                                                    <button class="btn btn-primary">編輯</button>
                                                </a>
                                            </td>
                                            <td>{{ $t05tb->unit }}</td>
                                            <td>{{ $t05tb->name }}</td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        
                        </div>                        
                    </div>
                    <div class="card-footer">
                        <a href="/admin/arrangement/{{ $t04tb->class }}/{{ $t04tb->term }}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>                     
                </div>
            </div>
        </div>
    </div>
</div>

@endsection