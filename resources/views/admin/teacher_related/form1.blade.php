@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'teacher_related';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座用餐、住宿、派車資料登錄表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/teacher_related" class="text-info">講座用餐、住宿、派車資料登錄列表</a></li>
                        <li class="active">講座用餐、住宿、派車資料登錄表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'method'=>'put', 'url'=>'/admin/teacher_related/'.$ClassWeek_data['edit_id'], 'id'=>'form']) !!}


            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座用餐、住宿、派車資料登錄表單</h3></div>
                    <div class="card-body pt-4">

                        <ul class="list-group col-md-10" >
                            <li class="list-group-item">
                                講座 : {{ $idno_data['cname'] }}<br>
                                需求起訖期間 : {{ $ClassWeek_data['sdate'] }} ~ {{ $ClassWeek_data['edate'] }}<br>
                                <?php if($ClassWeek_data['data_isset'] == 'N'){ ?>
                                <font color="#FF0000">住宿資料尚未儲存</font>
                                <?php } ?>
                            </li>
                        </ul>
                        <input type="hidden" name="type" value="teacher_room">


                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a href="#"  class="nav-link active">住宿</a></li>
                            <li class="nav-item"><a href="/admin/teacher_related/{{ $ClassWeek_data['edit_id'] }}/edit2" class="nav-link">派車</a></li>
                            <li class="nav-item"><a href="/admin/teacher_related/{{ $ClassWeek_data['edit_id'] }}/edit3" class="nav-link">用餐</a></li>
                            <li class="nav-item"><a href="/admin/teacher_related/{{ $ClassWeek_data['edit_id'] }}/edit4" class="nav-link">其他需求</a></li>
                        </ul>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">提前/延後住宿設定：</label>
                            <div class="col-sm-10">
                            </div>
                            <label class="col-sm-1 control-label text-md-right pt-2"></label>
                            <div class="col-sm-11">
                                <input onclick="before_day();" type="checkbox" id="before" name="before" style="min-width:20px; margin-left:5px;" <?=($TeacherByWeek_data['the_day_before']=='Y')?'checked':'';?> value="Y" >
                                提前住宿一天
                            </div>
                            <label class="col-sm-1 control-label text-md-right pt-2"></label>
                            <div class="col-sm-11">
                                <input onclick="after_day();" type="checkbox" id="after" name="after" style="min-width:20px; margin-left:5px;" <?=($TeacherByWeek_data['the_day_after']=='Y')?'checked':'';?> value="Y" >
                                延後住宿一天
                            </div>

                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">住宿日期設定：</label>
                            <div class="col-sm-10">
                            </div>
                            <div id="before_day" >
                            </div>
                            <?php if(isset($RoomDate[$TeacherByWeek_data['before_day']])){ ?>
                            <label id="before_day1" class="col-sm-1 control-label text-md-right pt-2"></label>
                            <div id="before_day2" class="col-sm-11">
                                <input id="check_before" onclick="before_check();" type="checkbox" name="confirm_date[]" style="min-width:20px; margin-left:5px;" <?=($RoomDate[$TeacherByWeek_data['before_day']]['confirm'])=='Y'?'checked':'';?> value="<?=$RoomDate[$TeacherByWeek_data['before_day']]['date'];?>" >
                                <?=substr($RoomDate[$TeacherByWeek_data['before_day']]['date'],0,3);?>/<?=substr($RoomDate[$TeacherByWeek_data['before_day']]['date'],3,2);?>/<?=substr($RoomDate[$TeacherByWeek_data['before_day']]['date'],5,2);?>
                                &ensp;
                                住宿別：
                                <input type="checkbox" name="<?=$RoomDate[$TeacherByWeek_data['before_day']]['date'];?>[]" <?=($RoomDate[$TeacherByWeek_data['before_day']]['morning'])=='Y'?'checked':'';?> value="morning" >
                                早
                                &ensp;
                                <input  type="checkbox" name="<?=$RoomDate[$TeacherByWeek_data['before_day']]['date'];?>[]" <?=($RoomDate[$TeacherByWeek_data['before_day']]['noon'])=='Y'?'checked':'';?> value="noon" >
                                午
                                &ensp;
                                <input  type="checkbox" name="<?=$RoomDate[$TeacherByWeek_data['before_day']]['date'];?>[]" <?=($RoomDate[$TeacherByWeek_data['before_day']]['evening'])=='Y'?'checked':'';?> value="evening" >
                                晚
                            </div>
                            <?php } ?>
                            <?php $checked_id = '0'; ?>
                            <?php foreach($RoomDate as $row){ ?>
                                <?php if($row['date'] != $TeacherByWeek_data['before_day']){ ?>
                                <?php if($row['date'] != $TeacherByWeek_data['after_day']){ ?>
                                <?php if($row['date'] < $TeacherByWeek_data['after_day']){ ?>
                                    <label class="col-sm-1 control-label text-md-right pt-2"></label>
                                    <div class="col-sm-11">
                                        <input id="day_<?=$checked_id;?>" onclick="checked_input('<?=$checked_id;?>');" type="checkbox" name="confirm_date[]" style="min-width:20px; margin-left:5px;" <?=($row['confirm'])=='Y'?'checked':'';?> value="<?=$row['date'];?>" >
                                        <?=substr($row['date'],0,3);?>/<?=substr($row['date'],3,2);?>/<?=substr($row['date'],5,2);?>
                                        &ensp;
                                        住宿別：
                                        <input type="checkbox" name="<?=$row['date'];?>[]" <?=($row['morning'])=='Y'?'checked':'';?> value="morning" >
                                        早
                                        &ensp;
                                        <input id="noon_<?=$checked_id;?>" type="checkbox" name="<?=$row['date'];?>[]" <?=($row['noon'])=='Y'?'checked':'';?> value="noon" >
                                        午
                                        &ensp;
                                        <input id="evening_<?=$checked_id;?>" type="checkbox" name="<?=$row['date'];?>[]" <?=($row['evening'])=='Y'?'checked':'';?> value="evening" >
                                        晚
                                    </div>
                                    <?php $checked_id++; ?>
                                <?php } ?>
                                <?php } ?>
                                <?php } ?>
                            <?php } ?>
                            <div id="after_day" >
                            </div>
                            <?php if(isset($RoomDate[$TeacherByWeek_data['after_day']]) && $RoomDate[$TeacherByWeek_data['after_day']]['only_morning'] == 'Y'){ ?>
                            <label id="after_day5" class="col-sm-1 control-label text-md-right pt-2"></label>
                            <div id="after_day6" class="col-sm-11">
                                <input id="check_after" onclick="after_check();" type="checkbox" name="confirm_date[]" style="min-width:20px; margin-left:5px;" <?=($RoomDate[$TeacherByWeek_data['after_day']]['confirm'])=='Y'?'checked':'';?> value="<?=$RoomDate[$TeacherByWeek_data['after_day']]['date'];?>" >
                                <?=substr($RoomDate[$TeacherByWeek_data['after_day']]['date'],0,3);?>/<?=substr($RoomDate[$TeacherByWeek_data['after_day']]['date'],3,2);?>/<?=substr($RoomDate[$TeacherByWeek_data['after_day']]['date'],5,2);?>
                                &ensp;
                                住宿別：
                                <input type="checkbox" name="<?=$RoomDate[$TeacherByWeek_data['after_day']]['date'];?>[]" <?=($RoomDate[$TeacherByWeek_data['after_day']]['morning'])=='Y'?'checked':'';?> value="morning" >
                                早
                                &ensp;
                                <input  type="checkbox" name="<?=$RoomDate[$TeacherByWeek_data['after_day']]['date'];?>[]" <?=($RoomDate[$TeacherByWeek_data['after_day']]['noon'])=='Y'?'checked':'';?> value="noon" >
                                午
                                &ensp;
                                <input  type="checkbox" name="<?=$RoomDate[$TeacherByWeek_data['after_day']]['date'];?>[]" <?=($RoomDate[$TeacherByWeek_data['after_day']]['evening'])=='Y'?'checked':'';?> value="evening" >
                                晚
                            </div>
                            <?php } ?>
                            <?php if(isset($RoomDate[$TeacherByWeek_data['after_day']]) && $RoomDate[$TeacherByWeek_data['after_day']]['only_morning'] != 'Y'){ ?>
                            <label id="after_day1" class="col-sm-1 control-label text-md-right pt-2"></label>
                            <div id="after_day2" class="col-sm-11">
                                <input id="check_after" onclick="after_check();" type="checkbox" name="confirm_date[]" style="min-width:20px; margin-left:5px;" <?=($RoomDate[$TeacherByWeek_data['after_day']]['confirm'])=='Y'?'checked':'';?> value="<?=$RoomDate[$TeacherByWeek_data['after_day']]['date'];?>" >
                                <?=substr($RoomDate[$TeacherByWeek_data['after_day']]['date'],0,3);?>/<?=substr($RoomDate[$TeacherByWeek_data['after_day']]['date'],3,2);?>/<?=substr($RoomDate[$TeacherByWeek_data['after_day']]['date'],5,2);?>
                                &ensp;
                                住宿別：
                                <input type="checkbox" name="<?=$RoomDate[$TeacherByWeek_data['after_day']]['date'];?>[]" <?=($RoomDate[$TeacherByWeek_data['after_day']]['morning'])=='Y'?'checked':'';?> value="morning" >
                                早
                                &ensp;
                                <input  type="checkbox" name="<?=$RoomDate[$TeacherByWeek_data['after_day']]['date'];?>[]" <?=($RoomDate[$TeacherByWeek_data['after_day']]['noon'])=='Y'?'checked':'';?> value="noon" >
                                午
                                &ensp;
                                <input  type="checkbox" name="<?=$RoomDate[$TeacherByWeek_data['after_day']]['date'];?>[]" <?=($RoomDate[$TeacherByWeek_data['after_day']]['evening'])=='Y'?'checked':'';?> value="evening" >
                                晚
                            </div>
                                <?php $lest_day = date('Ymd',strtotime('+1 day', strtotime(($TeacherByWeek_data['after_day']+19110000))))-19110000; ?>
                                <?php if(isset($RoomDate[$lest_day])){ ?>
                                <label id="after_day3" class="col-sm-1 control-label text-md-right pt-2"></label>
                                <div id="after_day4" class="col-sm-11">
                                    <input id="check_after"  type="checkbox" name="lest_day" style="min-width:20px; margin-left:5px;" <?=($RoomDate[$lest_day]['confirm'])=='Y'?'checked':'';?> value="<?=$RoomDate[$lest_day]['date'];?>" >
                                    <?=substr($RoomDate[$lest_day]['date'],0,3);?>/<?=substr($RoomDate[$lest_day]['date'],3,2);?>/<?=substr($RoomDate[$lest_day]['date'],5,2);?>
                                    &ensp;
                                    住宿別：
                                    <input type="checkbox" name="<?=$RoomDate[$lest_day]['date'];?>[]" <?=($RoomDate[$lest_day]['morning'])=='Y'?'checked':'';?> value="morning" >
                                    早
                                    &ensp;
                                    <input  type="checkbox" name="<?=$RoomDate[$lest_day]['date'];?>[]" <?=($RoomDate[$lest_day]['noon'])=='Y'?'checked':'';?> value="noon" >
                                    午
                                    &ensp;
                                    <input  type="checkbox" name="<?=$RoomDate[$lest_day]['date'];?>[]" <?=($RoomDate[$lest_day]['evening'])=='Y'?'checked':'';?> value="evening" >
                                    晚
                                </div>
                                <?php } ?>
                            <?php } ?>
                            <label class="col-sm-4 control-label text-md-left pt-2"><font color="#FF0000">*當天晚上有住送出後 住宿別會自動增加隔天早上</font></label>
                            <label class="col-sm-8 control-label text-md-right pt-2"></label>
                            <label class="col-sm-6 control-label text-md-left pt-2"><font color="#FF0000">*每次修改住宿日期後 請重新確認或修改用餐資料 六日不供餐</font></label>
                            <label class="col-sm-6 control-label text-md-right pt-2"></label>
                        </div>


                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/teacher_related/detail?class={{ $ClassWeek_data['class'] }}&term={{ $ClassWeek_data['term'] }}&sdate={{ $ClassWeek_data['sdate'] }}">
                            <button type="button" class="btn btn-sm btn-danger"> 取消</button>
                        </a>
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
<script>
    function checked_input(checked_id){
        if($("#day_"+checked_id).prop("checked")) {
            $("#noon_"+checked_id).prop("checked", true);
            $("#evening_"+checked_id).prop("checked", true);
        } else {
            $("#noon_"+checked_id).prop("checked", false);
            $("#evening_"+checked_id).prop("checked", false);
        }

    }
    function before_day(){
        var html = '';
        if($("#before").prop("checked")) {
            html += '<label id="before_day1" class="col-sm-1 control-label text-md-right pt-2"></label>';
            html += '<div id="before_day2" class="col-sm-11">';
            html += '    <input id="check_before" onclick="before_check();" type="checkbox" name="confirm_date[]" style="min-width:20px; margin-left:5px;" checked value="<?=$TeacherByWeek_data['before_day'];?>" >';
            html += '    <?=substr($TeacherByWeek_data['before_day'],0,3);?>/<?=substr($TeacherByWeek_data['before_day'],3,2);?>/<?=substr($TeacherByWeek_data['before_day'],5,2);?>';
            html += '    &ensp;';
            html += '    住宿別：';
            html += '    <input type="checkbox" name="<?=$TeacherByWeek_data['before_day'];?>[]"  value="morning" >';
            html += '    早';
            html += '    &ensp;';
            html += '    <input type="checkbox" name="<?=$TeacherByWeek_data['before_day'];?>[]"  value="noon" >';
            html += '    午';
            html += '    &ensp;';
            html += '    <input type="checkbox" name="<?=$TeacherByWeek_data['before_day'];?>[]" checked value="evening" >';
            html += '    晚';
            html += '</div>';
            $('#before_day').after(html);
        } else {
            $("#before_day1").remove();
            $("#before_day2").remove();
            $("#before").prop("checked", false);
        }
    }
    function before_check(){
        if($("#check_before").prop("checked")) {

        }else{
            $("#before_day1").remove();
            $("#before_day2").remove();
            $("#before").prop("checked", false);
        }
    }
    function after_day(){
        var html = '';
        if($("#after").prop("checked")) {
            $("#after_day5").remove();
            $("#after_day6").remove();
            html += '<label id="after_day1" class="col-sm-1 control-label text-md-right pt-2"></label>';
            html += '<div id="after_day2" class="col-sm-11">';
            html += '    <input id="check_after" onclick="after_check();" type="checkbox" name="confirm_date[]" style="min-width:20px; margin-left:5px;" checked value="<?=$TeacherByWeek_data['after_day'];?>" >';
            html += '    <?=substr($TeacherByWeek_data['after_day'],0,3);?>/<?=substr($TeacherByWeek_data['after_day'],3,2);?>/<?=substr($TeacherByWeek_data['after_day'],5,2);?>';
            html += '    &ensp;';
            html += '    住宿別：';
            html += '    <input type="checkbox" name="<?=$TeacherByWeek_data['after_day'];?>[]"  value="morning" >';
            html += '    早';
            html += '    &ensp;';
            html += '    <input type="checkbox" name="<?=$TeacherByWeek_data['after_day'];?>[]"  value="noon" >';
            html += '    午';
            html += '    &ensp;';
            html += '    <input type="checkbox" name="<?=$TeacherByWeek_data['after_day'];?>[]" checked value="evening" >';
            html += '    晚';
            html += '</div>';
            $('#after_day').after(html);
        } else {
            $("#after_day1").remove();
            $("#after_day2").remove();
            $("#after_day3").remove();
            $("#after_day4").remove();
        }
    }
    function after_check(){
        if($("#check_after").prop("checked")) {

        }else{
            $("#after_day1").remove();
            $("#after_day2").remove();
            $("#after_day3").remove();
            $("#after_day4").remove();
            $("#after").prop("checked", false);
        }
    }
</script>
@endsection