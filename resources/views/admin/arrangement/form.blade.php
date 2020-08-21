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
                    <li class="active">課程配當安排</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="container">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>課程配當安排</h3>
                    </div>
                    @if(isset($t06tb))
                        {!! Form::model($t06tb, array('url' => "/admin/arrangement/{$t04tb->class}/{$t04tb->term}/{$t06tb->course}",'method'=>'put')) !!}                                 
                    @else
                        {!! Form::open(array('url' => "/admin/arrangement/{$t04tb->class}/{$t04tb->term}",'method'=>'post')) !!}
                    @endif
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

                                    <div class="float-md mobile-100 row mr-1 mb-3">
                                        <div class="input-group col-5">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">單元</span>
                                            </div>    
                                            {{ Form::select('unit',array_merge(['' => '請選擇'], $t04tb->t05tbs->pluck('name','unit')->toArray()),null,['class'=> 'browser-default select2']) }}
                                        </div>
                                        <div class="input-group col-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">課程編號</span>
                                            </div>    
                                            {{ Form::text('course',null,  ['class'=>'form-control','readOnly' => 'true']) }}
                                        </div>    
                                        <div class="input-group col-5"></div>                                         
                                        <div class="input-group col-5">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">課程名稱</span>
                                            </div>    
                                            {{ Form::text('name', null, ['class'=>'form-control']) }}
                                        </div>     
                                        <div class="input-group col-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">時數</span>
                                            </div>    
                                            {{ Form::text('hour', null, ['class'=>'form-control']) }}
                                        </div> 
                                        <div class="input-group col-5"></div>    
                                        <div class="input-group col-7">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">課程內容</span>
                                            </div>    
                                            {{ Form::textarea('matter', null, ['class'=>'form-control', 'style="height:100px;"']) }}
                                        </div>   
                                        <div class="input-group col-5"></div>   
                                        <div class="input-group col-6">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" style="display: table;">入口網站班別類別</span>
                                            </div>    
                                            {{ 
                                                Form::text('category', (!isset($t06tb)) ? $t04tb->t01tb->category.' '.$t04tb->t01tb->s03tb->name : null, 
                                                [
                                                    'class'=> 'form-control number-input-max', 
                                                    'id' => "category2",
                                                    'autocomplete' => "off",
                                                    "required" => 'required',
                                                    "readonly" => 'readonly',
                                                    "onkeyup" => "this.value=this.value.replace(/[^\d]/g,'')"
                                                ])
                                            }}

                                            {{ 
                                                Form::hidden('category', (!isset($t06tb)) ? $t04tb->t01tb->category : null, 
                                                [
                                                    'class'=> 'form-control number-input-max', 
                                                    'id' => "category",
                                                    'autocomplete' => "off",
                                                    "required" => 'required',
                                                    "readonly" => 'readonly',
                                                    "onkeyup" => "this.value=this.value.replace(/[^\d]/g,'')"
                                                ])
                                            }}

                                            <button class="btn btn-number" style=" margin: 0;padding: 0 20px;" type="button" onclick="chooseClassType()">...</button>
                                        </div> 
                                        <div class="input-group col-4">                                       
                                            {{ Form::checkbox('is_must_read', 1, (!isset($t06tb)) ? $t04tb->t01tb->is_must_read : null,  ['class'=>'form-check-input', 'style'=> 'position:relative;height:20px;'])}} 
                                            <label>屬於公務人員必讀課程</label>
                                        </div>                                                                                                                 
                                    </div>
                                </div>
                                  
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-info">
                            <i class="fa fa-save pr-2"></i>儲存
                        </button>
                        @if (isset($t06tb))
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteT06tb()">
                                <i class="fa fa-trash pr-2"></i>刪除
                            </button>
                        @endif 
                        
                        <a href="/admin/arrangement/{{$t04tb->class}}/{{$t04tb->term}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>     
                    {!! Form::close() !!}      
                    @if (isset($t06tb))
                        {!! Form::open(['method' => "delete", 'url' => "/admin/arrangement/{$t06tb->class}/{$t06tb->term}/{$t06tb->course}", 'id' => 'delete_t06tb_form']) !!}
                        {!! Form::close() !!}
                    @endif 
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin/layouts/list/class_modol');

@endsection

@section('js')
<script>
    function deleteT06tb(){
        if (confirm('確定要刪除嗎')){
            $("#delete_t06tb_form").submit();
        }
    }
</script>
@endscetion