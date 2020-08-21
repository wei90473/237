@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'class_material';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">班別教材資料處理表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/class_material" class="text-info">班別教材資料處理列表</a></li>
                        <li class="active">班別教材資料處理表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/class_material/'.$data->serno, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/class_material/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">班別教材資料處理表單</h3></div>
                    <div class="card-body pt-4">


                        @if(isset($data))
                            {{-- 編輯 --}}

                            <!-- 班別 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">班別<span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control input-max" placeholder="請輸入班別" value="{{ $classData->class }} {{ $classData->name }}" readonly>
                                </div>
                            </div>

                            <!-- 期別 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">期別<span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control input-max" placeholder="請輸入期別" value="{{ $data->term }}" readonly>
                                </div>
                            </div>

                        @else

                            {{-- 新增 --}}
                            <!-- 班號 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">班號<span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select id="class" name="class" class="select2 form-control select2-single input-max" required onchange="classChange()">
                                        @foreach($classList as $key => $va)
                                            <option value="{{ $va->class }}" {{ old('class', (isset($data->class))? $data->class : 1) == $va->class? 'selected' : '' }}>{{ $va->class }} {{ $va->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- 期別 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">期別<span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select id="term" name="term" class="select2 form-control select2-single input-max" required>
                                    </select>
                                </div>
                            </div>
                        @endif

                        <!-- 日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">日期<span class="text-danger">*</span></label>
                            <div class="col-sm-10">

                                <div class="input-group roc-date input-max">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">民國</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-year" maxlength="3" name="date[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($data->date))? mb_substr($data->date, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">年</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-month" maxlength="2" name="date[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($data->date))? mb_substr($data->date, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">月</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-day" maxlength="2" name="date[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($data->date))? mb_substr($data->date, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">日</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- 姓名 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">姓名<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="cname" name="cname" placeholder="請輸入姓名" value="{{ old('cname', (isset($data->cname))? $data->cname : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>


                        <!-- 類別 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">類別<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="type" name="type" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.class_material_type') as $key => $va)
                                        <option value="{{ $key }}" {{ old('type', (isset($data->type))? $data->type : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/class_material">
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
    <script>
        // 取得期別
        function classChange()
        {
            $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "html",
                url: '/admin/class_material/getterm',
                data: { classes: $('#class').val(), selected: ''},
                success: function(data){
                    $('#term').html(data);
                    $("#term").trigger("change");
                },
                error: function() {
                    alert('Ajax Error');
                }
            });
        }

        // 初始化
        {{ ( ! isset($data))? 'classChange();' : '' }}
    </script>
@endsection