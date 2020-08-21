@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'review_apply';?>
<style>
input{
    /* min-width:1px; */
}
</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">報名審核處理</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">報名審核處理</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
              
            <div class="col-12">
            {!! Form::model($t27tb, array('url' => "/admin/review_apply/{$t27tb->class}/{$t27tb->term}/{$t27tb->des_idno}",'method'=>'put', 'id' => 'edit_form')) !!}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>報名審核處理</h3>
                    </div>

                    <div class="card-body">
                        <div style="border: 1px solid #000; padding: 10px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            期別：{{ $t04tb->term }}<br>
                            分班名稱：<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            委訓機關：{{ $t04tb->client }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：
                        </div>
                        <div>
                        
                            @if ($t27tb->identity == 1)
                                @include('admin/review_apply/form_government')
                            @elseif ($t27tb->identity == 2)
                                @include('admin/review_apply/form_general')
                            @endif 
                         
                    </div>
                </div>

                <div class="card-footer">
                    @if((isset($t27tb) && $t27tb->prove != 'S') || empty($t27tb))
                    <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-save"></i> 儲存</button>
                    @endif 
                    
                    <button type="button" class="btn btn-sm btn-danger" onclick="$('#deleteForm').submit()"><i class="fa fa-trash"></i>刪除</button> 
                    <a href="/admin/review_apply/{{ $t04tb->class }}/{{ $t04tb->term }}">
                        <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                    </a>
                </div>  
            {!! Form::close() !!} 

            {!! Form::open(['id' => 'deleteForm', 'method' => 'delete', 'url' => "/admin/review_apply/{$t27tb->class}/{$t27tb->term}/{$t27tb->des_idno}", 'onsubmit' => 'return confirm("確定要刪除此報名資料嗎？")' ]) !!}
            {!! Form::close() !!}                       
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    function save(){
        $('#edit_form').submit();
    }
</script>
@endsection 