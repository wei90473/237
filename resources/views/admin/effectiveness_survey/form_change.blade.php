@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'effectiveness_survey';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">成效問卷製作表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/effectiveness_survey" class="text-info">成效問卷製作列表</a></li>
                        <li class="active">成效問卷製作表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'method'=>'put', 'url'=>'/admin/effectiveness_survey/change/'.serialize($data), 'id'=>'form']) !!}

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">成效問卷製作表單</h3></div>
                    <div class="card-body pt-4">

                        <!-- 班號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班號<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="class" name="class" placeholder="請輸入班號" value="{{ old('class', (isset($data['class']))? $data['class'] : '') }}" autocomplete="off" maxlength="255" required {{ (isset($data))? 'readonly' : ''}}>
                            </div>
                        </div>

                        <!-- 期別 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">期別<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="term" name="term" placeholder="請輸入期別" value="{{ old('term', (isset($data['term']))? $data['term'] : '') }}" autocomplete="off" maxlength="255" required {{ (isset($data))? 'readonly' : ''}}>
                            </div>
                        </div>

                        <!-- 第幾次調查 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">第幾次調查<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="times" name="times" placeholder="請輸入第幾次調查" value="{{ $data['times'] }}" autocomplete="off" maxlength="255" required {{ (isset($data))? 'readonly' : ''}}>
                            </div>
                        </div>

                        <!-- 舊課程 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">舊課程</label>
                            <div class="col-md-10" id="course_div">
                                <select id="old" name="old" class="select2 form-control select2-single input-max" required>
                                    @foreach($course as $key => $va)
                                        <option value="{{ $va->course }}_{{ $va->idno }}" {{ old('course', (isset($data->course))? $data->course : 1) == $va->course? 'selected' : '' }}>{{ $va->coursename }}({{ $va->cname }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 新課程 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">新課程</label>
                            <div class="col-md-10" id="course_div">
                                <select id="new" name="new" class="select2 form-control select2-single input-max" required>
                                    @foreach($courseNot as $key => $va)

                                        <option value="{{ $va->course }}_{{ $va->idno }}" {{ old('course', (isset($data->course))? $data->course : 1) == $va->course? 'selected' : '' }}>{{ $va->coursename }}({{ $va->cname }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/effectiveness_survey/{{serialize($data)}}/edit">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
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
            url: '/admin/effectiveness_survey/getterm',
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

    // 取得課程
    function termChange() {

        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/effectiveness_survey/getcourse',
            data: { classes: $('#class').val(), term: $('#term').val()},
            success: function(data){
                $('#course_div').html(data);
            },
            error: function() {
                alert('Ajax Error');
            }
        });
    }

    // 初始化
    // classChange();
</script>
@endsection