@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'teacher_reception';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">計程車安排</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">計程車安排</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>計程車安排</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                        <input type="hidden" id="search" name="search" class="form-control" value="search">
                                            <!-- 日期 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">日期</span>
                                                    </div>
                                                     <input class="date form-control" value="{{$queryData['date']}}" type="text" id="date" name="date">
                                                     <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                    	{!! Form::open([ 'method'=>'post', 'url'=>'/admin/teacher_reception/', 'id'=>'form']) !!}
                                        <input type="hidden"  name="date" value="{{$queryData['date']}}">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>功能</th>
                                                <th>姓名</th>
                                                <th>授課時間</th>
                                                <th>接</th>
                                                <th>送</th>
                                                <th>時間</th>
                                                <th>接送地點</th>
                                                <th>車資</th>
                                                <th>安排車班</th>
                                                <th>呼號</th>
                                                <th>備註</th>
                                            </tr>
                                            </thead>

                                            <tbody>

                                            @foreach($data as $va)
                                            	<input type="hidden" id="update_id" name="update_id[]" value="{{ $va['id'] }}">
                                            	<tr>
	                                                <td class="text-center">
	                                                    <a href="/admin/teacher_reception/{{ $va['t09tb_id'] }}_{{ $va['class_weeks_id'] }}_car_{{ $queryData['date'] }}/edit2" data-placement="top" data-toggle="tooltip" data-original-title="修改">
	                                                        <i class="fa fa-pencil"></i>
	                                                    </a>
	                                                </td>
	                                                <td>{{ $va['name'] }}</td>
	                                                <td>
                                                        @if(!empty($va['class_time']))
	                                                    <?php foreach($va['class_time'] as $time){?>
	                                                    <?=$time;?>
	                                                    <br>
	                                                    <?php } ?>
                                                        @endif
	                                                </td>
	                                                <td>{{ $va['type1'] }}</td>
	                                                <td>{{ $va['type2'] }}</td>
	                                                <td>{{ $va['time'] }}</td>
	                                                <td>{{ $va['location'] }}</td>
	                                                <td>
	                                                	<input type="number"  id="price" name="{{$va['id']}}_price"  value="{{ old('price', (isset($va['price']))? $va['price'] : '0') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
	                                                </td>
	                                                <td>
                                                        <select id="car" name="{{$va['id']}}_car" class="select2 form-control select2-single ">
                                                            <option value="" >請選擇</option>
                                                            <?php if($va['type'] == '1'){ ?>
                                                                <?php foreach(config('app.car_1') as $car_1_key => $car_1_row){ ?>
                                                                <option value="<?=$car_1_key;?>" <?=($va['car'] == $car_1_key)?'selected':'';?>  ><?=$car_1_row;?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                            <?php if($va['type'] == '2'){ ?>
                                                                <?php foreach(config('app.car_A') as $car_A_key => $car_A_row){ ?>
                                                                <option value="<?=$car_A_key;?>" <?=($va['car'] == $car_A_key)?'selected':'';?>  ><?=$car_A_row;?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
	                                                <td>
	                                                	<select id="license_plate" name="{{$va['id']}}_license_plate" class="select2 form-control select2-single ">
		                                                	<option value="" >請選擇</option>
		                                                	<?php foreach($car_data as $car_data_row){ ?>
						                                    <option value="<?=$car_data_row['license_plate'];?>" <?=($va['license_plate'] == $car_data_row['license_plate'])?'selected':'';?>  ><?=$car_data_row['call'];?></option>
						                                    <?php } ?>
					                                    </select>
	                                                </td>
	                                                <td>
	                                                	<input type="text"  id="remark" name="{{$va['id']}}_remark"  value="{{ old('remark', (isset($va['remark']))? $va['remark'] : '') }}" autocomplete="off" maxlength="50" >
	                                                </td>
                                                </tr>
                                            @endforeach
                                            </tbody>

                                        </table>
                                        <div class="float-left">
                                            <!-- 查詢 -->
                                            <a href="/admin/teacher_reception/car_detail?search=search&auto=auto&date={{ $queryData['date'] }}">
                                            	<button type="button" class="btn btn-sm btn-info" >自動安排</button>
                                            </a>
                                            <!-- 重設條件 -->
                                            <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info">儲存</button>
                                            <a href="/admin/teacher_reception/car_detail?search=search&date={{ $queryData['date'] }}">
					                            <button type="button" class="btn btn-sm btn-danger"> 取消 </button>
					                        </a>

                                        </div>

                                        {!! Form::close() !!}
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
$( function() {
    $("#date").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
    });
    $('#datepicker1').click(function(){
        $("#date").focus();
    });

  } );
</script>
@endsection