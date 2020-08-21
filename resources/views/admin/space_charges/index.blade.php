@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'space_charges';?>


<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">場地收費(南投院區)</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">場地收費(南投院區)</li>
                </ol>
            </div>
        </div>
        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>場地收費(南投院區)</h3>
                    </div>

                    <div class="card-body">
                    	<div class="row">
                            <div class="col-12">
			                    <div class="search-float" style="width:100%;">
			                        <form id="search_form">
			                            <div class="form-row">
			                                <div class="form-group col-md-5">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">申請單位</label>
			                                        </div>
			                                        <input type="text" class="form-control" name="orgname" value="{{ $queryData['orgname'] }}">
			                                    </div>
			                                </div>
			                                <div class="form-group col-md-5">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">申請人姓名</label>
			                                        </div>
			                                        <input type="text" class="form-control" name="applyuser" value="{{ $queryData['applyuser'] }}">
			                                    </div>
			                                </div> 
			                            </div>

			                            <div class="float-md mobile-100 row mr-1 mb-3">
			                                <div class="input-group col-5">
			                                    <div class="input-group-prepend">
			                                        <span class="input-group-text">申請日期(起)</span>
			                                    </div>
			                                    <input type="text" id="sdate" name="start_date" class="form-control" autocomplete="off" value="{{ $queryData['start_date'] }}">
			                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
			                                </div>
			                                <div class="input-group col-5">
			                                    <div class="input-group-prepend">
			                                        <span class="input-group-text">申請日期(訖)</span>
			                                    </div>
			                                    <input type="text" id="edate" name="end_date" class="form-control" autocomplete="off" value="{{ $queryData['end_date'] }}">
			                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
			                                </div>
			                            </div>

			                            <div class="form-row">
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">申請狀態</label>
			                                        </div>

			                                        <select class="custom-select" name="status">
			                                            <option value=""></option>
			                                            @foreach ($applyStatusList as $value => $text)
			                                                <option value="{{ $text['code'] }}" {{ $queryData['status'] == $text['code']? 'selected' : '' }}>{{ $text['name'] }}</option>
			                                            @endforeach
			                                        </select>
			                                    </div>
			                                </div>
			                            </div>
			                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">
			                            <button type="submit" class="btn btn-primary"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
			                        </form>
			                    </div>

			                    <div class="float-md-right">

			                    </div>

			                    <div class="table-responsive">
			                        <table class="table table-bordered mb-0 table-hover">
			                            <thead>
			                            <tr>
			                                <th class="text-center">申請日期</th>
			                                <th class="text-center">申請單位</th>
			                                <th class="text-center">借用明細</th>
			                                <th class="text-right">實付費用</th>
			                                <th class="text-center">收費情形</th>
			                                <th class="text-center">收據</th>
			                            </tr>
			                            </thead>
			                            <tbody>

			                            @foreach($data as $value)
			                                <tr>
			                                    <td class="text-center">{{$value->applydate}}</td>
			                                    <td><a href="/admin/space_charges/ChargesSub1/{{ $value->applyno }}">{{$value->orgname}}</a></td>
			                                    <td class="text-center">{{$value->detail}}</td>
			                                    <td class="text-right">{{$value->fee}}</td>
			                                    <td class="text-center">
			                                    	@if($value->paydate > 0)
			                                    		<a href="/admin/space_charges/EditCharges/{{ $value->applyno }}">已收費</a>
			                                    	@else
			                                    		<a href="/admin/space_charges/EditCharges/{{ $value->applyno }}">未收</a>
			                                    	@endif
			                                    </td>
			                                    <td class="text-center">
			                                  		@if($value->paydate > 0)
			                                  			<a href="/admin/space_charges/PrintReceipt/{{ $value->applyno }}">列印</a>
			                                  		@endif
			                                    </td>
			                                </tr>
			                            @endforeach
			                            </tbody>
			                        </table>
			                    </div>
			                    @if($data)
				                <!-- 分頁 -->
				                @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
				                @endif
			                    
			        		</div>
			        	</div>
			        </div>
                    <!-- 列表頁尾 -->
                    @if($data)
                    @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $("#sdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker1').click(function(){
            $("#sdate").focus();
        });

        $("#edate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker2').click(function(){
            $("#edate").focus();
        });

    });
</script>
@endsection