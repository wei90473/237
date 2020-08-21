@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'sponsor_agent';?>
<style>
    .search-float input{
        min-width: 1px;
    }
</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">研習證明書電子章設定</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">研習證明書電子章設定</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="container">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>研習證明書電子章設定</h3>
                    </div>
                    @if ($action == "edit")
                    {{ Form::model($signature ,['method' => 'put', 'url' => "/admin/signature/{$signature->id}", 'enctype' => "multipart/form-data"]) }}
                    @else
                    {{ Form::open(['method' => 'post', 'url' => '/admin/signature', 'enctype' => "multipart/form-data"]) }}
                    @endif 
                        <div class="card-body">
                            <div>
                                <div class="form-row">      
                                    <div class="form-group col-md-5">
                                        <div class="input-group">
                                            <label class="col-form-label col-sm-4">顯示順序：</label>
                                            {!! Form::text('sort', null, ['class' => 'form-control col-sm-3']) !!}
                                        </div> 
                                    </div>                                     
                                </div>                                   
                                <div class="form-row">      
                                    <div class="form-group col-md-5">
                                        <div class="input-group">
                                            <label class="col-form-label col-sm-4">顯示名稱：</label>
                                            {!! Form::text('name', null, ['class' => 'form-control col-sm-7']) !!}
                                        </div> 
                                    </div>                                     
                                </div>    
                                <div class="form-row">
                                    <div class="form-group col-md-5">
                                        <div class="input-group">
                                            <label class="col-form-label col-sm-4">電子章：</label>
                                            {!! Form::file('signature', ['class' => 'form-control col-sm-7']) !!}
                                        </div> 
                                    </div>  
                                </div>  
                                
                                @if ($action == "edit")
                                    <div>
                                        <img style="width: 50%;" src="/Uploads/signatures/{{ $signature->img_path }}">
                                    </div>
                                @endif 
                                                                                             
                            </div>  
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>儲存</button> 
                            @if($action == "edit")  
                            <button type="button" class="btn btn-danger" onclick="deleteSignature()"><i class="fa fa-trash"></i>刪除</button> 
                            @endif 
                            <a href="/admin/signature">
                                <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                            </a>
                        </div> 
                    {{ Form::close() }}                    
                </div>
                @if($action == "edit")  
                {{ Form::open(['method' => 'delete', 'url' => "/admin/signature/{$signature->id}", "id" => "deleteSignatureForm"]) }}

                {{ Form::close() }}
                @endif 
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')
<script>
    function deleteSignature()
    {
        if (confirm("確定要移除此電子章嗎")){
            $("#deleteSignatureForm").submit();
        }
    }
</script>
@endsection