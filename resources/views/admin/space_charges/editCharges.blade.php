@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'space_charges';?>


<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">場地收費</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">場地收費</li>
                </ol>
            </div>
        </div>
        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>場地收費</h3>
                    </div>

                    <div class="card-body">
                    	<div class="row">
                            <div class="col-12">
			                    <div class="search-float" style="width:100%;">
			                    	{{ Form::open(["id" => "search_form", "method" => "put", "url" => "admin/space_charges/update"]) }}
			                       
			                        	<input type="hidden" name="id" value="{{ $detail[0]['id'] }}"></input>
			                        	<input type="hidden" name="applyno" value="{{ $detail[0]['applyno'] }}"></input>
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
			                                            <label class="input-group-text">申請日期</label>
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
			                                            <label class="input-group-text">聯絡電話</label>
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
			                                            <label class="input-group-text">機關首長</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['chief1'] }}</p>
			                                    </div>
			                                </div>
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">單位主管</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['chief2'] }}</p>
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
			                                <div class="form-group col-md-6">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">備註</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['description'] }}</p>
			                                    </div>
			                                </div>
			                            </div>

			                            <div class="form-row">
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">金額確認</label>
			                                        </div>
			                                        <p class="form-control">{{ $detail[0]['confirm_fee'] }}</p>
			                                    </div>
			                                </div>
			                            </div>

			                            <br/>
			       
			                            <div class="form-row">
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text" style="background-color: red;color: white">繳款人</label>
			                                        </div>
			                                        <input type="text" class="form-control" name="payuser" value="{{ $detail[0]['payuser'] }}"></input>
			                                    </div>
			                                </div>
			                                <div class="form-group col-md-3">
			                                    <div class="input-group">
			                                        <div class="input-group-prepend">
			                                            <label class="input-group-text">繳費日期</label>
			                                        </div>
			                                        <input type="text" id="paydate" name="paydate" class="form-control" autocomplete="off" value="{{ $detail[0]['paydate'] }}">
			                                    	<span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
			                                    </div>
			                                </div> 
			                            </div>

			                            <div class="card-footer">
					                        <button type="button" class="btn btn-sm btn-primary" onclick="sendFun(1)"><i class="fa"></i>確認</button>
					                        <button type="button" class="btn btn-sm btn-primary" onclick="sendFun(-1)"><i class="fa"></i>撤銷</button>
					                        <a href="/admin/space_charges">
					                            <button type="button" class="btn btn-sm btn-danger"><i class="fa"></i>取消</button>
					                        </a>
					                        <input type="hidden" id="mode" name="mode" value=""></input>
					                    </div> 
			                        {{ Form::close() }} 
			                    </div>
			        		</div>
			        	</div>
			        </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
	$(document).ready(function() {
        $("#paydate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker1').click(function(){
            $("#paydate").focus();
        });
    });

    function sendFun(status)
    {
    	var obj = document.getElementById('search_form');
        if(status == 1){
        	document.getElementById('mode').value = 1;
        } else if(status == -1) {
        	document.getElementById('mode').value = -1;
        }

        obj.submit();
    }
</script>
@endsection