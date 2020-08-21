@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'site_review';?>
<style>

</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">洽借場地班期選員處理</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">洽借場地班期選員處理</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>洽借場地班期選員處理</h3>
                    </div>
                    @if (isset($m02tb))
                        {{ Form::model($m02tb, ["method" => "post", "url" => "/admin/site_review/{$t04tb->class}/{$t04tb->term}", "id" => 't39tb_form']) }}
                    @elseif (isset($t39tb))
                        {{ Form::model($t39tb, ["method" => "put", "url" => "/admin/site_review/{$t39tb->class}/{$t39tb->term}/{$t39tb->des_idno}", "id" => 't39tb_form']) }}
                    @endif 
                    <div class="card-body">
                        <div style="border: 1px solid #000; padding: 10px;margin-bottom:10px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            辦班院區：{{ $t04tb->t01tb->branch }}<br>
                            期別：{{ $t04tb->term }}<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：{{ $t04tb->m09tb->cname }}
                        </div>
                        <div style="max-width:900px;">
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <div class="input-group">
                                        <label class="col-form-label"><font color="red">*</font>身分證號：</label>
                                        {{ Form::text('idno', null, ['class' => 'form-control', 'disabled' => 'readonly'] ) }}
                                    </div>                                   
                                </div>  
                                <div class="form-group col-md-5">
                                    <div class="input-group">
                                        <label class="col-form-label">性別：</label>
                                        <div class="form-check form-check-inline">
                                            {{ Form::radio('sex', 'M', null, ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                                            <label class="form-check-label" for="inlineRadio2">男</label>
                                        </div>   
                                        <div class="form-check form-check-inline">
                                            {{ Form::radio('sex', 'F', null, ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                                            <label class="form-check-label" for="inlineRadio2">女</label>
                                        </div>   
                                    </div>                                 
                                </div>                                                                                                                           
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <div class="input-group">
                                        <label class="col-form-label"><font color="red">*</font>姓名：</label>
                                        {{ Form::text('cname', null, ['class' => 'form-control', 'required' => 'required']) }}
                                        <div class="invalid-feedback">
                                            請輸入姓名
                                        </div>                                        
                                    </div>                                   
                                </div>  
                                <div class="form-group col-md-5">
                                    <div class="input-group">
                                        <label class="col-form-label">學員分類：</label>
                                        <div class="form-check form-check-inline">
                                            {{ Form::radio('race', 1, null, ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                                            <label class="form-check-label" for="inlineRadio2">現職</label>
                                        </div>   
                                        <div class="form-check form-check-inline">
                                            {{ Form::radio('race', 2, null, ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                                            <label class="form-check-label" for="inlineRadio2">現休</label>
                                        </div>  
                                        <div class="form-check form-check-inline">
                                            {{ Form::radio('race', 3, null, ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                                            <label class="form-check-label" for="inlineRadio2">里民</label>
                                        </div>                                       
                                    </div>                                 
                                </div>
                                <div class="form-group col-md-4">
                                    <div class="input-group">
                                        <label class="col-form-label"><font color="red"></font>資料來源：</label>
                                        {{ Form::select('source', ['' => '', 1 => '網頁', 2 => 'E-Mail', 3 => '傳真', 4 => '其他'], (isset($t39tb)) ? null : 3, ['class' => 'form-control custom-select', 'required' => 'required']) }}
                                        <div class="invalid-feedback">
                                            請輸入姓名
                                        </div>                                        
                                    </div>  
                                </div>                                                                                               
                            </div>   

                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <div class="input-group">
                                        <label class="col-form-label"><font color="red">*</font>生日：</label>
                                        {{ Form::text('birth', null, ['class' => 'form-control', 'required' => 'required']) }}
                                    </div>                                 
                                </div>

                                <div class="form-group col-md-5">
                                    <div class="input-group">
                                        <label class="col-form-label">費用：</label>
                                        {{ Form::number('fee', null, ['class' => 'form-control']) }}
                                    </div>                                 
                                </div>                            
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <div class="input-group">
                                        <label class="col-form-label">報名時間：</label>
                                        {{ Form::text('logdate', (isset($t39tb)) ? null : ((int)$now->format("Y") - 1911).$now->format("md"), ['class' => 'form-control']) }}
                                    </div>                                                                     
                                </div>

                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        {{ Form::text('logtime', (isset($t39tb)) ? null : $now->format("Hi"), ['class' => 'form-control']) }}
                                    </div>                                 
                                </div>

                                <div class="form-group col-md-5">
                                    <div class="input-group">
                                        <label class="col-form-label">服務機關：</label>
                                        {{ Form::text('dept', null, ['class' => 'form-control']) }}
                                    </div>                                 
                                </div>                            
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <div class="input-group">
                                        <label class="col-form-label">官職等：</label>
                                        {{ Form::select('rank', $m02tb_fields['rank'] , null, ['class' => 'form-control']) }}
                                    </div>                                 
                                </div>

                                <div class="form-group col-md-5">
                                    <div class="input-group">
                                        <label class="col-form-label">職稱：</label>
                                        {{ Form::text('position', null, ['class' => 'form-control']) }}
                                    </div>                                 
                                </div>                            
                            </div>                                                                   

                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <div class="input-group">
                                        <label class="col-form-label">最高學歷：</label>
                                        {{ Form::select('ecode', $m02tb_fields['ecode'] ,null, ['class' => 'form-control']) }}
                                    </div>                                 
                                </div>

                                <div class="form-group col-md-5">
                                    <div class="input-group">
                                        {{ Form::text('education', null, ['class' => 'form-control']) }}
                                    </div>                                 
                                </div>                            
                            </div>   

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <div class="input-group">
                                        <label class="col-form-label">電話(公一)：</label>
                                        {{ Form::text('offtela', (isset($m02tb) ? $m02tb->offtela1 : null), ['class' => 'form-control']) }}
                                    </div>                                   
                                </div>
                                <div class="form-group col-md-4">
                                    <div class="input-group">
                                        {{ Form::text('offtelb', (isset($m02tb) ? $m02tb->offtelb1 : null), ['class' => 'form-control']) }}
                                    </div>                                   
                                </div>                                                                 
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        {{ Form::text('offtelc', (isset($m02tb) ? $m02tb->offtelc1 : null), ['class' => 'form-control']) }}
                                    </div>                                   
                                </div>     
                            </div> 

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <div class="input-group">
                                        <label class="col-form-label">傳真(公)：</label>
                                        {{ Form::text('offfaxa', null, ['class' => 'form-control']) }}
                                    </div>                                   
                                </div>
                                <div class="form-group col-md-4">
                                    <div class="input-group">
                                        {{ Form::text('offfaxb', null, ['class' => 'form-control']) }}
                                    </div>                                   
                                </div>                                                                    
                            </div> 

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                    <label class="col-form-label">E-Mail：</label>
                                    {{ Form::text('email', null, ['class' => 'form-control']) }}
                                    </div>                                   
                                </div>                                                                 
                            </div>   
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <div class="input-group">
                                        <label class="col-form-label">電話(宅)：</label>
                                        {{ Form::text('homtela', null, ['class' => 'form-control']) }}
                                    </div>                                   
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        {{ Form::text('homtelb', null, ['class' => 'form-control']) }}
                                    </div>                                   
                                </div>                                          
                            </div> 

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <div class="input-group">
                                        <label class="col-form-label">行動電話：</label>
                                        {{ Form::text('mobiltel', null, ['class' => 'form-control']) }}
                                    </div>                                   
                                </div>    
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <label class="col-form-label">備註：</label>
                                        {{ Form::text('extranote', null, ['class' => 'form-control']) }}
                                    </div>                                   
                                </div>    
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <label class="col-form-label">不受理原因：</label>
                                        {{ Form::text('reject', null, ['class' => 'form-control']) }}
                                    </div>                                   
                                </div>    
                            </div>       

                            <div class="form-row">
                                <div class="form-group">
                                    <div class="form-check-inline">
                                    <label class="form-check-label">檢核條件：</label>
                                        <label class="form-check-label">
                                            {{ Form::checkbox('chk1', "Y", (isset($t39tb) && $t39tb->chk1 == "Y"), ['class' => 'form-check-input', 'disabled' => true]) }}
                                            同日跨班上課
                                        </label>
                                    </div>  

                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            {{ Form::checkbox('chk2', "Y", (isset($t39tb) && $t39tb->chk2 == "Y"), ['class' => 'form-check-input', 'disabled' => true]) }}
                                            六個月內曾缺課
                                        </label>
                                    </div>    

                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            {{ Form::checkbox('chk3', "Y", (isset($t39tb) && $t39tb->chk3 == "Y"), ['class' => 'form-check-input', 'disabled' => true]) }}
                                            重複報名相同課程
                                        </label>
                                    </div> 

                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            {{ Form::checkbox('chk4', "Y", (isset($t39tb) && $t39tb->chk4 == "Y"), ['class' => 'form-check-input', 'disabled' => true]) }}
                                            同時參加其他課程
                                        </label>
                                    </div>                                                                                                                  
                                </div>
                            </div>                                                  
                        </div>

                    </div>
                    <div class="card-footer">
                        @if ((isset($t39tb) && $t39tb->prove <> 'S') || !isset($t39tb))
                        <button class="btn btn-sm btn-primary"><i class="fa fa-save"></i>儲存</button>
                        @endif 
                        <a href="/admin/site_review/{{ $t04tb->class }}/{{ $t04tb->term }}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div> 
                    {{ Form::close() }}                    
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')
<script>


    // Example starter JavaScript for disabling form submissions if there are invalid fields
    // (function() {
    // 'use strict';
    // window.addEventListener('load', function() {
    //     // Fetch all the forms we want to apply custom Bootstrap validation styles to
    //     var forms = document.getElementsByClassName('needs-validation');
    //     // Loop over them and prevent submission
    //     var validation = Array.prototype.filter.call(forms, function(form) {
    //         form.addEventListener('submit', function(event) {
    //             if (form.checkValidity() === false) {
    //                 event.preventDefault();
    //                 event.stopPropagation();
    //             }
    //             form.classList.add('was-validated');
    //         }, false);
    //     });
    // }, false);
    // })();



    function hideOrShowAccount(id, status)
    {
        status = (status == true) ? '' : 'none';
        $('#' + id).css('display', status);
    }

    
</script>
@endsection