@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'teacher_reception';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座接待管理表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/teacher_reception" class="text-info">講座接待管理列表</a></li>
                        <li class="active">講座接待管理表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'method'=>'put', 'url'=>'/admin/teacher_reception/'.$ClassWeek_data['edit_id'], 'id'=>'form']) !!}


            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座接待管理表單</h3></div>
                    <div class="card-body pt-4">

                        <ul class="list-group col-md-10" >
                            <li class="list-group-item">
                                講座 : {{ $idno_data['cname'] }}<br>
                                需求起訖期間 : {{ $ClassWeek_data['sdate'] }} ~ {{ $ClassWeek_data['edate'] }}<br>
                                <?php if($ClassWeek_data['data_isset'] == 'N'){ ?>
                                <font color="#FF0000">派車資料尚未儲存</font>
                                <?php } ?>
                            </li>
                        </ul>
                        <input type="hidden" name="type" value="teacher_car">


                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a href="/admin/teacher_reception/{{ $ClassWeek_data['edit_id'] }}/edit1"  class="nav-link">住宿</a></li>
                            <li class="nav-item"><a href="#" class="nav-link active">派車</a></li>
                            <li class="nav-item"><a href="/admin/teacher_reception/{{ $ClassWeek_data['edit_id'] }}/edit3" class="nav-link">用餐</a></li>
                            <li class="nav-item"><a href="/admin/teacher_reception/{{ $ClassWeek_data['edit_id'] }}/edit4" class="nav-link">其他需求</a></li>
                        </ul>

                        <fieldset style="border:groove; padding: inherit">
                            <legend>接帶</legend>
                            <div class="input-group  col-2">
                                <input type="checkbox" id="come_by_self" name="come_by_self" <?=($TeacherByWeek_data['come_by_self']=='Y')?'checked':'';?> value="Y" >
                                不須接帶
                            </div>
                            <div class="form-group row">
	                            <label class="col-2 control-label text-md-right pt-2">日期：</label>
	                            <div class="input-group  col-2">
	                                <input type="text" id="date1" name="date1" class="form-control" autocomplete="off" value="{{ old('date1', (isset($data['date1']))? $data['date1'] : '') }}">
                                	<span class="input-group-addon" style="cursor: pointer; height:38px" id="datepicker1"><i class="fa fa-calendar"></i></span>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label class="col-2 control-label text-md-right pt-2">上車地點：</label>
	                            <div class="input-group  col-2">
                                    <select id="location1_1" name="location1_1" class="select2 form-control select2-single input-max" onchange="county1Change()">
                                        @foreach(config('app.county') as $key => $va)
                                            <option value="{{ $key }}" {{ old('location1_1', (isset($data['location1_1']))? $data['location1_1'] : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                    </select>
	                            </div>
                                <div class="input-group  col-2">
                                    <select id="location2_1" name="location2_1" class="select2 form-control select2-single input-max">
                                        @foreach(config('app.array') as $key2 => $va)
                                            <option value="{{ $key2 }}" {{ old('location2_1', (isset($data['location2_1']))? $data['location2_1'] : 1) == $key2? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-group  col-5">
                                    <input type="text" class="form-control" maxlength="50" autocomplete="off" id="address_1" name="address_1"  value="{{ old('address_1', (isset($data['address_1']))? $data['address_1'] : '') }}">
                                </div>

	                        </div>

	                        <div class="form-group row">
	                            <label class="col-2 control-label text-md-right pt-2">下車地點：</label>
	                            <div class="input-group  col-2">
                                    <select id="location3_1" name="location3_1" class="select2 form-control select2-single input-max">
                                        @foreach(config('app.school_location') as $key => $va)
                                            <option value="{{ $key }}" {{ old('location3_1', (isset($data['location3_1']))? $data['location3_1'] : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                    </select>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label class="col-2 control-label text-md-right pt-2">時間：</label>
	                            <div class="input-group  col-2">
                                    <input type="time" id="time1" name="time1" class="form-control" placeholder="hrs:mins" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" autocomplete="off" value="{{ old('time1', (isset($data['time1']))? $data['time1'] : '') }}">
	                            </div>
	                        </div>

                        </fieldset>

                        <fieldset style="border:groove; padding: inherit">
                            <legend>送達</legend>
                            <div class="input-group  col-2">
                                <input type="checkbox" id="go_by_self" name="go_by_self" <?=($TeacherByWeek_data['go_by_self']=='Y')?'checked':'';?> value="Y" >
                                不須送達
                            </div>
                            <div class="form-group row">
	                            <label class="col-2 control-label text-md-right pt-2">日期：</label>
	                            <div class="input-group  col-2">
	                                <input type="text" id="date2" name="date2" class="form-control" autocomplete="off" value="{{ old('date2', (isset($data['date2']))? $data['date2'] : '') }}">
                                	<span class="input-group-addon" style="cursor: pointer;  height:38px" id="datepicker2"><i class="fa fa-calendar"></i></span>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label class="col-2 control-label text-md-right pt-2">上車地點：</label>
	                            <div class="input-group  col-2">
                                    <select id="location3_2" name="location3_2" class="select2 form-control select2-single input-max">
                                        @foreach(config('app.school_location') as $key => $va)
                                            <option value="{{ $key }}" {{ old('location3_2', (isset($data['location3_2']))? $data['location3_2'] : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                    </select>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label class="col-2 control-label text-md-right pt-2">下車地點：</label>
	                            <div class="input-group  col-2">
                                    <select id="location1_2" name="location1_2" class="select2 form-control select2-single input-max" onchange="county2Change()">
                                        @foreach(config('app.county') as $key => $va)
                                            <option value="{{ $key }}" {{ old('location1_2', (isset($data['location1_2']))? $data['location1_2'] : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                    </select>
	                            </div>
                                <div class="input-group  col-2">
                                    <select id="location2_2" name="location2_2" class="select2 form-control select2-single input-max">
                                        @foreach(config('app.array') as $key2 => $va)
                                            <option value="{{ $key2 }}" {{ old('location2_2', (isset($data['location2_2']))? $data['location2_2'] : 1) == $key2? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-group  col-5">
                                    <input type="text" class="form-control" maxlength="50" autocomplete="off" id="address_2" name="address_2"  value="{{ old('address_2', (isset($data['address_2']))? $data['address_2'] : '') }}">
                                </div>
	                        </div>

	                        <div class="form-group row">
	                            <label class="col-2 control-label text-md-right pt-2">時間：</label>
	                            <div class="input-group  col-2">
                                    <input type="time" id="time2" name="time2" class="form-control" placeholder="hrs:mins" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" autocomplete="off" value="{{ old('time2', (isset($data['time2']))? $data['time2'] : '') }}">
	                            </div>
	                        </div>

                        </fieldset>

                        <br>

                        <div class="form-group row">
                            <div class="input-group  col-2">
                                <input type="checkbox" id="self" name="self" <?=($TeacherByWeek_data['drive_by_self']=='Y')?'checked':'';?> value="Y" >
                                自來回
                            </div>
                        </div>


                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <?php if(!empty($goBack)){?>
                        <a href="/admin/teacher_reception/{{ $goBack['type'] }}_detail?search=search&date={{ $goBack['date'] }}">
                            <button type="button" class="btn btn-sm btn-danger"> 取消</button>
                        </a>
                        <?php }else{ ?>
                        <a href="/admin/teacher_reception/detail?class={{ $ClassWeek_data['class'] }}&term={{ $ClassWeek_data['term'] }}&sdate={{ $ClassWeek_data['sdate'] }}">
                            <button type="button" class="btn btn-sm btn-danger"> 取消</button>
                        </a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')

@endsection

@section('js')
    <script type="text/javascript">

        $(document).ready(function() {
            $("#date1").datepicker({
                format: "twymmdd",
                language: 'zh-TW'
            });
            $('#datepicker1').click(function(){
                $("#date1").focus();
            });
            $("#date2").datepicker({
                format: "twymmdd",
                language: 'zh-TW'
            });
            $('#datepicker2').click(function(){
                $("#date2").focus();
            });

            county1Change();
            county2Change();

        });

        // 取得地區
        function county1Change() {

            $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "html",
                url: '/admin/teacher_reception/getLocation',
                data: { county: $('#location1_1').val(), selected: '{{ (isset($data))? $data["location2_1"] : '' }}'},
                success: function(data){
                    $('#location2_1').html(data);
                    $("#location2_1").trigger("change");
                },
                error: function() {
                    alert('無地區');
                }
            });
        }

        function county2Change() {

            $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "html",
                url: '/admin/teacher_reception/getLocation',
                data: { county: $('#location1_2').val(), selected: '{{ (isset($data))? $data["location2_2"] : '' }}'},
                success: function(data){
                    $('#location2_2').html(data);
                    $("#location2_2").trigger("change");
                },
                error: function() {
                    alert('無地區');
                }
            });
        }

    </script>
@endsection