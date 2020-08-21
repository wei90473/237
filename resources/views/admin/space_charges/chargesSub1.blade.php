@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'space_charges';?>


<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">場地借用現況查詢</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">場地借用現況查詢</li>
                </ol>
            </div>
        </div>
        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>場地借用現況查詢</h3>
                    </div>

                    <div class="card-body">
                    	<div class="row">
                            <div class="col-12">
			                    <div class="search-float" style="width:100%;">
			                        <form id="search_form">
			                            <div class="form-row">
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">申請單位</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['orgname'] }}</p>
			                                    </div>
			                                </div>
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">申請人日期</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['applydate'] }}</p>
			                                    </div>
			                                </div> 
			                            </div>

			                            <div class="form-row">
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">申請人職稱</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['title'] }}</p>
			                                    </div>
			                                </div>
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">申請人姓名</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['applyuser'] }}</p>
			                                    </div>
			                                </div> 
			                            </div>

			                            <div class="form-row">
			                                <div class="form-group col-md-6">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">申請人Email</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['email'] }}</p>
			                                    </div>
			                                </div>
			                            </div>

			                            <div class="form-row">
			                                <div class="form-group col-md-6">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">活動人數</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['num'] }}</p>
			                                    </div>
			                                </div>
			                            </div>

			                            <div class="form-row">
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">男住宿人數</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['mstay'] }}</p>
			                                    </div>
			                                </div>
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">女住宿人數</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['fstay'] }}</p>
			                                    </div>
			                                </div> 
			                            </div>

			                            <div class="form-row">
			                                <div class="form-group col-md-6">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">借用事由</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['reason'] }}</p>
			                                    </div>
			                                </div>
			                            </div>

			                            <div class="form-row">
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">電話</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['tel'] }}</p>
			                                    </div>
			                                </div>
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">行動電話</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['cellphone'] }}</p>
			                                    </div>
			                                </div> 
			                            </div>

			                            <div class="form-row">
			                                <div class="form-group col-md-6">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">傳真</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['fax'] }}</p>
			                                    </div>
			                                </div>
			                            </div>

			                            <div class="form-row">
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">處理日期</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['processdate'] }}</p>
			                                    </div>
			                                </div>
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">狀態</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['statusname'] }}</p>
			                                    </div>
			                                </div> 
			                            </div>

			                            <div class="form-row">
			                                <div class="form-group col-md-6">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">無法外借原因</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['reason2'] }}</p>
			                                    </div>
			                                </div>
			                            </div>

			                            <div class="form-row">
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">折扣</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['discount'] }}</p>
			                                    </div>
			                                </div>
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">總經費</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['fee'] }}</p>
			                                    </div>
			                                </div> 
			                            </div>

			                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="">
			                            <a href="/admin/space_charges">
				                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一層</button>
				                        </a>
			                        </form>
			                    </div>

			                    <div class="float-md-right">

			                    </div>

			                    <div class="table-responsive">
			                        <table class="table table-bordered mb-0 table-hover">
			                            <thead>
			                            <tr>
			                                <th class="text-center">借用場地</th>
			                                <th class="text-center">借用日期</th>
			                                <th class="text-center">借用時間</th>
			                                <th class="text-right">借用間數</th>
			                                <th class="text-right">含假日</th>
			                                <th class="text-right">借用費用</th>
			                                <th class="text-center">場地安排</th>
			                            </tr>
			                            </thead>
			                            <tbody>

			                            @foreach($data as $value => $text)
			                                <tr>
			                                    <td class="text-center">{{ $text['croomclsfullname'] }}</td>
			                                    <td class="text-center">{{ $text['startdate'] }}</td>
			                                    <td class="text-center">{{ $text['timestartname'].'～'.$text['timeendname'].'時' }}</td>
			                                    <td class="text-right">{{ $text['placenum'] }}</td>
			                                    <td class="text-right">{{ $text['nday'].$text['hday'] }}</td>
			                                    <td class="text-right">{{ $text['fee'] }}</td>
			                                    <td class="text-center">
			                                  		@if($text['classroom'] == '1')
			                                  		<a href="/admin/space_charges/ChargesSub3/{{ $text['applyno'].'/'.$text['croomclsno'] }}">{!!$text['setstatus'] !!}</a>
			                                  		@else
			                                  		<a href="/admin/space_charges/ChargesSub4/{{ $text['applyno'].'/'.$text['croomclsno'] }}">{!! $text['setstatus'] !!}</a>
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
