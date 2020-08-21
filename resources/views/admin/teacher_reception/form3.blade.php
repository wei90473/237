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
                                <font color="#FF0000">用餐資料尚未儲存</font>
                                <?php } ?>
                            </li>
                        </ul>
                        <input type="hidden" name="type" value="teacher_food">

                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a href="/admin/teacher_reception/{{ $ClassWeek_data['edit_id'] }}/edit1"  class="nav-link">住宿</a></li>
                            <li class="nav-item"><a href="/admin/teacher_reception/{{ $ClassWeek_data['edit_id'] }}/edit2" class="nav-link">派車</a></li>
                            <li class="nav-item"><a href="#" class="nav-link active">用餐</a></li>
                            <li class="nav-item"><a href="/admin/teacher_reception/{{ $ClassWeek_data['edit_id'] }}/edit4" class="nav-link">其他需求</a></li>
                        </ul>
                        <?php foreach($data as $key => $row){ ?>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">日期：<?=substr($key, 0, 3).'/'.substr($key, 3, 2).'/'.substr($key, 5, 2);?>(<?=$row['week_day'];?>)</label>
                            <div class="col-sm-10">
                            </div>
                            <label class="col-sm-1 control-label text-md-right pt-2"></label>
                            <div class="col-sm-2">
                                <input type="checkbox" name="<?=$key;?>_breakfast" style="min-width:20px; margin-left:5px;" <?=($row['breakfast'])=='Y'?'checked':'';?> value="Y" >
                                早餐

                                <input type="radio" id="<?=$key;?>_breakfasttype" name="<?=$key;?>_breakfasttype" <?=($row['breakfast_type']=='1')?'checked':'';?> value="1">
                                <label for="male">葷食</label>

                                <input type="radio" id="<?=$key;?>_breakfasttype" name="<?=$key;?>_breakfasttype" <?=($row['breakfast_type']=='2')?'checked':'';?> value="2">
                                <label for="female">素食</label>
                            </div>
                            <div class="col-1">用餐種類：</div>
                            <div class="input-group  col-2" style="margin-bottom: 8px;" >
                                <select id="<?=$key;?>_breakfasttype2" name="<?=$key;?>_breakfasttype2" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.food_type') as $key1 => $va)
                                        <option value="{{ $key1 }}" {{ old('breakfast_type2', (isset($row['breakfast_type2']))? $row['breakfast_type2'] : '0') == $key1? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                            </div>
                            <label class="col-sm-1 control-label text-md-right pt-2"></label>
                            <div class="col-sm-2">
                                <input type="checkbox" name="<?=$key;?>_lunch" style="min-width:20px; margin-left:5px;" <?=($row['lunch'])=='Y'?'checked':'';?> value="Y" >
                                午餐

                                <input type="radio" id="<?=$key;?>_lunchtype" name="<?=$key;?>_lunchtype" <?=($row['lunch_type']=='1')?'checked':'';?> value="1">
                                <label for="male">葷食</label>

                                <input type="radio" id="<?=$key;?>_lunchtype" name="<?=$key;?>_lunchtype" <?=($row['lunch_type']=='2')?'checked':'';?> value="2">
                                <label for="female">素食</label>

                            </div>
                            <div class="col-1">用餐種類：</div>
                            <div class="input-group  col-2" style="margin-bottom: 8px;" >
                                <select id="<?=$key;?>_lunchtype2" name="<?=$key;?>_lunchtype2" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.food_type') as $key2 => $va)
                                        <option value="{{ $key2 }}" {{ old('lunch_type2', (isset($row['lunch_type2']))? $row['lunch_type2'] : '0') == $key2? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                            </div>
                            <label class="col-sm-1 control-label text-md-right pt-2"></label>
                            <div class="col-sm-2">
                                <input type="checkbox" name="<?=$key;?>_dinner" style="min-width:20px; margin-left:5px;" <?=($row['dinner'])=='Y'?'checked':'';?> value="Y" >
                                晚餐

                                <input type="radio" id="<?=$key;?>_dinnertype" name="<?=$key;?>_dinnertype" <?=($row['dinner_type']=='1')?'checked':'';?> value="1">
                                <label for="male">葷食</label>

                                <input type="radio" id="<?=$key;?>_dinnertype" name="<?=$key;?>_dinnertype" <?=($row['dinner_type']=='2')=='2'?'checked':'';?> value="2">
                                <label for="female">素食</label>

                            </div>
                            <div class="col-1">用餐種類：</div>
                            <div class="input-group  col-2" style="margin-bottom: 8px;" >
                                <select id="<?=$key;?>_dinnertype2" name="<?=$key;?>_dinnertype2" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.food_type') as $key3 => $va)
                                        <option value="{{ $key3 }}" {{ old('dinner_type2', (isset($row['dinner_type2']))? $row['dinner_type2'] : '0') == $key3? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                            </div>
                        </div>
                        <?php } ?>



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