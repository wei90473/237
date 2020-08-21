@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'space_charges';?>


<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">場地借用現況查詢﹣可借用場地</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">場地借用現況查詢﹣可借用場地</li>
                </ol>
            </div>
        </div>
        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>場地借用現況查詢﹣可借用場地</h3>
                    </div>
                    <div class="card-body">
                    	<div class="row">
		                    <div class="table-responsive">
		                        <table class="table table-bordered mb-0 table-hover">
		                            <thead>
		                            <tr>
		                                <th class="text-center">場地名稱</th>
		                                <th class="text-center">地點</th>
		                                <th class="text-center">容納數</th>
		                            </tr>
		                            </thead>
		                            <tbody>
		                            @foreach($data as $value => $text)
		                                <tr>
		                                    <td>{{ $text['croomclsfullname'] }}</td>
		                                    <td>{{ $text['fullname'] }}</td>
		                                    <td>{{ $text['num'] }}</td>
		                                </tr>
		                            @endforeach
		                            </tbody>
		                        </table>
		                    </div>
		                    <a href="/admin/space_charges/ChargesSub1/{{ $applyno }}">
	                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一層</button>
	                        </a>
			        	</div>
			        </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
