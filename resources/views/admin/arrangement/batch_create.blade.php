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

.copy_form{
    border:1px solid #000;
    padding:10px;
    margin-bottom:10px;    
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
                    <li class="active">批次新增</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="container">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>批次新增</h3>
                    </div>
                    {!! Form::open(array('url' => "/admin/arrangement/batch_store",'method'=>'post')) !!}
                    <div class="card-body">
              
                        <div class="row">
                            <div class="col-12">
                            原始
                            </div>                        
                            <div class="col-12 copy_form">
                                <div class="form-group row">
                                    <label class="col-form-label col-md-2">班號</label>
                                    <div class="col-md-2">
                                        <input type="text" name="copyed[class]" class="form-control" value="{{ old('copyed.class') }}" readonly>
                                    </div>                            
                                    <button type="button" class="btn btn-primary" style="height:calc(2.25rem + 2px);" onclick="showT04tbModol('copyed')">挑選班期</button>                  
                                </div>    
                                <div class="form-group row">
                                    <label class="col-form-label col-md-2">班別名稱</label>
                                    <div class="col-md-4">
                                        <input type="text" name="copyed[class_name]" class="form-control" value="{{ old('copyed.class_name') }}" readonly>
                                    </div>                                             
                                </div>  
                                <div class="row">
                                    <label class="col-form-label col-md-2">期別</label>
                                    <div class="col-md-2">
                                        <input type="text" name="copyed[term]" class="form-control" value="{{ old('copyed.term') }}" readonly>
                                    </div>                                             
                                </div>                                                                  
                            </div>
                            <!-- <select class="browser-default custom-select"> -->
                            <!-- <option>123</option> -->
                            <!-- </select> -->
                            <div class="col-12">
                            目的
                            </div>
                            <div class="col-12 copy_form">
                                <div class="form-group row">
                                    <label class="col-form-label col-md-2">班號</label>
                                    <div class="col-md-2">
                                        <input type="text" name="copy_purpose[class]" class="form-control" value="{{ old('copy_purpose.class') }}" readonly>
                                    </div>                                             
                                    <button type="button" class="btn btn-primary" style="height:calc(2.25rem + 2px);" onclick="showT04tbModol('copy_purpose')">挑選班期</button>
                                </div>    
                                <div class="form-group row">
                                    <label class="col-form-label col-md-2">班別名稱</label>
                                    <div class="col-md-4">
                                        <input type="text" name="copy_purpose[class_name]" class="form-control" value="{{ old('copy_purpose.class_name') }}" readonly>
                                    </div>                                             
                                </div>  
                                <div class="row">
                                    <label class="col-form-label col-md-2">期別</label>
                                    <div class="col-md-2">
                                        <input type="text" name="copy_purpose[term]" class="form-control" value="{{ old('copy_purpose.term') }}" readonly>
                                    </div>                                             
                                </div>                                                                  
                            </div> 
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="include_class_schedule" value="1" id="defaultCheck1" style="width:20px;height:20px"
                                    {{ (old('include_class_schedule') == 1) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="defaultCheck1">
                                        包含課程表
                                    </label>
                                </div>                            
                            </div>                                                       
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-info">
                            <i class="fa fa-save pr-2"></i>確定
                        </button>
                        <a href="javascript:history.go(-1)">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                        <!-- <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button> -->

                    </div>     
                    {!! Form::close() !!}                 
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin/layouts/list/t04tb_modol')

@endsection

@section('js')
<script src="/backend/plugins/pagination/pagination.js" charset="UTF-8"></script>
<script>

function chooseT04tb(class_no, class_term, class_name)
{
    $("input[name='" + select_t04tb + "[class]']").val(class_no);
    $("input[name='" + select_t04tb + "[term]']").val(class_term);
    $("input[name='" + select_t04tb + "[class_name]']").val(class_name);
}

</script>
@endsection

