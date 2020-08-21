@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'special_class_fee';?>
<style>
input{
    min-width:1px;
}
</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">委訓班費用處理</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">委訓班費用處理</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>委訓班費用處理</h3>
                    </div>
                    {!! Form::model($t04tb->specailClassFee, ['method' => 'put', 'url' => "admin/special_class_fee/{$t04tb->class}/{$t04tb->term}"]) !!}
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
                        <div style="margin-top:10px;">
                        
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group ">
                                        <label class="col-form-label col-12 text-center">數量</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">天數</label>
                                    </div>
                                </div> 
                                <div class="form-group col-md-2 text-center">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">單價</label>
                                    </div>
                                </div> 
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">預算</label>
                                    </div>
                                </div>                                                                                                                                 
                            </div>                        
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">A-業務費</label>
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        {!! Form::text('service_fee_quantity', null, ['class' => 'form-control', 'disabled' => ($t04tb->t13tbs->count() < 40)]) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="input-group">
                                        {!! Form::text('service_fee_days', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        {!! Form::text('service_fee_unit_price', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        {!! Form::text('service_fee_budget', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                                                                                          
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">B-鐘點費</label>
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        {!! Form::text('hourly_fee_quantity', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="input-group">

                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">

                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        {!! Form::text('hourly_fee_budget', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                                                                                          
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">B-1 外聘</label>
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        {!! Form::text('oh_hourly_fee_quantity', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="input-group">

                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('oh_hourly_fee_unit_price', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('oh_hourly_fee_budget', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                                                                                          
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">B-2 外聘隸屬</label>
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('ohbe_hourly_fee_quantity', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="input-group">

                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('ohbe_hourly_fee_unit_price', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('ohbe_hourly_fee_budget', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                                                                                          
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">B-3 內聘</label>
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('ih_hourly_fee_quantity', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="input-group">

                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('ih_hourly_fee_unit_price', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('ih_hourly_fee_budget', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                                                                                          
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">B-4 助教</label>
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('ass_hourly_fee_quantity', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="input-group">

                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('ass_hourly_fee_unit_price', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('ass_hourly_fee_budget', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                                                                                          
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">C-業務支出</label>
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('business_pay_quantity', null, ($t04tb->t01tb->quota < 40) ? ['class' => 'form-control'] : ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="input-group">

                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('business_pay_unit_price', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('business_pay_budget', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                                                                                          
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">D-伙食費</label>
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('food_expenses_quantity', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="input-group">
                                    {!! Form::text('food_expenses_days', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('food_expenses_unit_price', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('food_expenses_budget', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                                                                                          
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">E-額外項</label>
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="input-group">

                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        {!! Form::text('extra_budget', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                                                                                          
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">E-1 租車</label>
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('rent_car_quantity', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="input-group">

                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('rent_car_unit_price', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('rent_car_budget', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                                                                                          
                            </div>   

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">E-2 保險</label>
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('insurance_quantity', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="input-group">

                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('insurance_unit_price', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('insurance_budget', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                                                                                          
                            </div>  

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        <label class="col-form-label" style="margin:auto">E-3 獎品</label>
                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('reward_quantity', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <div class="input-group">

                                    </div>
                                </div>                            
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('reward_unit_price', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                    {!! Form::text('reward_budget', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>                                                                                          
                            </div>                                                                                                                                                                                                                                                                                                                   
                                                                                                        
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-save"></i> 儲存</button>
                        @if($action == "edit")
                            
                        @endif 
                        <a href="/admin/special_class_fee/class_list">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>  
                    {!! Form::close() !!} 
                    {!! Form::open(['id' => 'deleteForm', 'method' => 'delete', 'url' => "", 'onsubmit' => 'return confirm("確定要刪除此報名資料嗎？")' ]) !!}
                               
                    {!! Form::close() !!}                                        
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')
<script>

</script>
@endsection