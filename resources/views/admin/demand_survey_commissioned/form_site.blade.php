@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'demand_survey_commissioned';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">編輯班別資料</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/class_schedule" class="text-info">編輯班別資料</a></li>
                        <li class="active">編輯班別資料</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
         
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/demand_survey_commissioned/audit_edit/'.$data->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/demand_survey_commissioned/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">編輯班別資料</h3></div>
                    <div class="card-body pt-4">
                        <input type="hidden" name="id" value="{{ $data->id }}">



                        <!-- 班別名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班別名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="class_name" name="class_name" placeholder="請輸入班別名稱(中文)" value="{{ old('class_name', (isset($data->class_name))? $data->class_name : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                        <!-- 參加對象 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">參加對象</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="target" id="target">{{ old('target', (isset($data->target))? $data->target : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 研習目標 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">研習目標</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="object" id="object">{{ old('object', (isset($data->object))? $data->object : '') }}</textarea>
                            </div>
                        </div>



                        <!-- 辦理期數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">辦理期數</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="periods" name="periods" placeholder="請輸入辦理期數" value="{{ old('periods', (isset($data->periods))? $data->periods : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                        <!-- 每期人數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">每期人數</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="periods_people" name="periods_people" placeholder="請輸入每期人數" value="{{ old('periods_people', (isset($data->periods_people))? $data->periods_people : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>


                        <!-- 訓練天數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">訓練天數</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="training_days" name="training_days" placeholder="請輸入訓練天數" value="{{ old('training_days', (isset($data->training_days))? $data->training_days : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                       <!-- 建議辦理時間(起迄) -->

                       <div id="transactDates">
                        <div class="form-row">
                            <button type="button" onclick="insertTransactDate()">新增建議辦理日期</button>      <font color="red">*</font>
                        </div>

                        @if (isset($dataDemandTransact))
                            @foreach ($dataDemandTransact as $key => $transactDate)                           
                                <div class="col-sm-12">
                          
                                        <label>建議辦理日期({{ $key + 1 }})(起) </label>
                                        {{ Form::text("transactDates[{$transactDate->demand_id}][sdate]", $transactDate->sdate, ['autocomplete' => 'off', 'class' => 'datepicker']) }}
                                        <label>(訖)</label>
                                        {{ Form::text("transactDates[{$transactDate->demand_id}][edate]", $transactDate->edate, ['autocomplete' => 'off', 'class' => 'datepicker']) }}

                                        <button onclick="removeTransactDate(this)">X</button>
                               
                                </div>
                            @endforeach
                        @else
                        <div class="col-sm-12">
                                    <label>建議辦理日期(1)(起)<font color="red">*</font></label>
                                    <input type="text" class="datepicker" name="newTransactDates[1][sdate]"> 
                                    <label>(訖)</label>
                                    <input type="text" class="datepicker" name="newTransactDates[1][edate]">
                                    <button onclick="removeTransactDate(this)">X</button>
 
                            </div>             
                        @endif 
                    </div>

                       <!-- 委託機關 -->
                       <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">委託機關</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="entrusting_orga" name="entrusting_orga" placeholder="請輸入委託機關" value="{{ old('entrusting_orga', (isset($data->entrusting_orga))? $data->entrusting_orga : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>


                       <!-- 委託單位-->
                       <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">委託單位</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="entrusting_unit" name="entrusting_unit" placeholder="請輸入委託單位" value="{{ old('entrusting_unit', (isset($data->entrusting_unit))? $data->entrusting_unit : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                       <!-- 承辦人-->
                       <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">承辦人</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="entrusting_contact" name="entrusting_contact" placeholder="請輸入承辦人" value="{{ old('entrusting_contact', (isset($data->entrusting_contact))? $data->entrusting_contact : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                       <!-- 電話-->
                       <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">電話</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="phone" name="phone" placeholder="請輸入電話" value="{{ old('phone', (isset($data->phone))? $data->phone : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                       <!-- email-->
                       <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="email" name="email" placeholder="請輸入email" value="{{ old('email', (isset($data->email))? $data->email : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                       <!-- 使用教室-->
                       <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">特殊教室需求</label>
                            <div class="col-sm-10 row">
 
                            @foreach(config('app.demand_survey_commissioned_room_type') as $key => $va)
                            <div class="col-sm-4  ">
                                    <input type="checkbox" id="classroom_type" name="classroom_type" value="{{ $key }}" {{ old('classroom_type', (isset($data->classroom_type))? $data->classroom_type : '0') == $key? 'checked' : '' }}>{{ $va }}
                            </div>
                            @endforeach           
                    
                            </div> 
                       </div>

                        <!-- 備註 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">備註</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="remarks" id="remarks">{{ old('remarks', (isset($data->remarks))? $data->remarks : '') }}</textarea>
                            </div>
                        </div>


                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-success"><i class="fa fa-save pr-2"></i>刪除</button>
                        <a href="/admin/demand_survey_commissioned/" >
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
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
<script type="text/javascript">
var existTransactDateCount = {{ isset($dataDemandTransactCount) ? $dataDemandTransactCount: 0 }};
var transactDateCount = existTransactDateCount;

$( function() {

    $("input:checkbox").click(function(){ 
        if(5==$(this).val()&& $(this).is(':checked')){
            alert("選擇其他，請填寫備註欄")
        }
           
	}); 


  } );
  $(".datepicker").datepicker({
        format: 'twymmdd', language: 'zh-TW'
    });
function insertTransactDate()
{
    if (transactDateCount + existTransactDateCount > 20){
        alert('建議辦理日期最多20組');
        return false;
    }


    transactDateCount++;

    let html = '' + 
                    '<div class="col-sm-12">' + 
                        '<label >建議辦理日期('+transactDateCount+')(起)</label>' + 
                        '<input type="text" class="datepicker" name="newTransactDates[' + transactDateCount + '][sdate]">' + 
                        '<label >(訖)</label>' +
                        '<input type="text" class="datepicker" name="newTransactDates[' + transactDateCount + '][edate]">' +
                        '<button onclick="removeTransactDate(this)">X</button>' +  
                    '</div>' + 
                '';

    $('#transactDates').append(html);
    $(".datepicker").datepicker({
        format: 'twymmdd', language: 'zh-TW'
    });
}

function removeTransactDate(element, exist)
{
    if (exist){
        if (existTransactDateCount > 0){
            existTransactDateCount--;
        }
    }else{
        if (transactDateCount > 0){
            transactDateCount--;
        }
    }
    
    $(element).parent().remove();
}

</script>
@endsection