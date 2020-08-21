@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'arrangement';?>

<style>
.input-group{
    padding-bottom:10px;
    /* flex-wrap: nowrap; */
}

.search-float input{
    min-width:60px;
}
</style>

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

        <div class="container">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>單元編輯</h3>
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
                        <div class="row">
                            <div class="col-12">
                                <div class="float-left search-float" style="margin-bottom: 10px;">
                                @if(isset($t05tb))
                                {!! Form::model($t05tb, array('url' => "/admin/unit/{$t04tb->class}/{$t04tb->term}/{$t05tb->unit}",'method'=>'put')) !!}                                 
                                @else
                                {!! Form::open(array('url' => "/admin/unit/{$t04tb->class}/{$t04tb->term}",'method'=>'post')) !!}
                                @endif
                                    <div class="float-md mobile-100 row mr-1 mb-3">
                                        <!-- <div class="input-group col-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">單元編號</span>
                                            </div>    
                                            {{ Form::text('course',null,  ['class'=>'form-control','readOnly' => 'true']) }}
                                        </div>     -->
                                        <!-- <div class="input-group col-10"></div>                                          -->
                                        <div class="input-group col-7">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">單元名稱</span>
                                            </div>    
                                            {{ Form::text('name', null, ['class'=>'form-control']) }}
                                        </div>     
                                        <div class="input-group col-5"></div>    
                                        <div class="input-group col-7">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">備註</span>
                                            </div>    
                                            {{ Form::textarea('remark', null, ['class'=>'form-control', 'style="height:100px;"']) }}
                                        </div>   
                                        <div class="input-group col-5"></div>                                                                                                                 
                                    </div>
                                </div>
                                  
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-info">
                            <i class="fa fa-save pr-2"></i>儲存
                        </button>
                        @if (isset($t05tb))
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteUnit()">
                            <i class="fa fa-trash pr-2"></i>刪除
                        </button>          
                        @endif              
                        <a href="/admin/unit/{{$t04tb->class}}/{{$t04tb->term}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>     
                    {!! Form::close() !!}  
                    @if (isset($t05tb))
                    {!! Form::open(['method' => 'delete', 'url' => "/admin/unit/{$t05tb->class}/{$t05tb->term}/{$t05tb->unit}", 'id' => 'delete_unit_form']) !!} 
                    {!! Form::close() !!}              
                    @endif 
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')
<script>
    function deleteUnit()
    {
        if (confirm('確定要刪除嗎？')){
            $("#delete_unit_form").submit();
        }
    }
</script>
@endsection
