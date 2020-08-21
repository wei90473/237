@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'training_survey';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">訓後問卷製作表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/training_survey" class="text-info">訓後問卷製作列表</a></li>
                        <li class="active">訓後問卷製作表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/training_survey/'.$data->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/training_survey/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">訓後問卷製作表單</h3></div>
                    <div class="card-body pt-4">


                        @if(isset($data))
                            {{-- 編輯 --}}
                            <!-- 班號 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">班號<span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control input-max" id="class" name="class" placeholder="請輸入班號" value="{{ $classData->class }} {{ $classData->name }}" autocomplete="off" required maxlength="255" readonly>
                                </div>
                            </div>

                            <!-- 期別 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">期別<span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control input-max" id="term" name="term" placeholder="請輸入期別" value="{{ old('term', (isset($data->term))? $data->term : '') }}" autocomplete="off" required maxlength="255" readonly>
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
                                    <select id="term" name="term" class="select2 form-control select2-single input-max" required onchange="getCourse();">
                                    </select>
                                </div>
                            </div>
                        @endif

                        <!-- 發出卷數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">發出卷數<span class="text-danger">*</span></label>
                            <div class="col-sm-10">


                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="copy" name="copy" min="1" placeholder="請輸入發出卷數" value="{{ old('copy', (isset($data->copy))? $data->copy : NULL) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 選取課程 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">選取課程</label>
                            <div class="col-md-10" id="course_div">

                            @if(isset($data))
                                <?php $key = 1;?>

                                    <!-- 已選取的課程 -->
                                    @foreach($course as $va)
                                        <div class="checkbox checkbox-primary">
                                            <input id="course{{ $key }}" name="course[]" value="{{ $va->course }}" type="checkbox" checked>
                                            <label for="course{{ $key }}">
                                                {{ $base->showDate($va->classdate) }} {{ $va->classname }} {{ $va->cname }}
                                            </label>
                                            <i class="fa fa-arrow-up pointer text-secondary" onclick="prev(this);"></i>
                                            <i class="fa fa-arrow-down pointer text-secondary" onclick="next(this);"></i>
                                        </div>
                                        <?php $key ++;?>
                                    @endforeach

                                    <!-- 未選取的課程 -->
                                    @foreach($courseNotSelect as $va)
                                        <div class="checkbox checkbox-primary">
                                            <input id="course{{ $key }}" name="course[]" value="{{ $va->course }}" type="checkbox">
                                            <label for="course{{ $key }}">
                                                {{ $base->showDate($va->date) }} {{ $va->coursename }} {{ $va->cname }}
                                            </label>
                                            <i class="fa fa-arrow-up pointer text-secondary" onclick="prev(this);"></i>
                                            <i class="fa fa-arrow-down pointer text-secondary" onclick="next(this);"></i>
                                        </div>
                                        <?php $key ++;?>
                                    @endforeach
                                @endif

                            </div>
                        </div>



                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/training_survey">
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
                url: '/admin/training_survey/getterm',
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
        classChange();

        // 取得課程
        function getCourse()
        {
            $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "html",
                url: '/admin/training_survey/getcourse',
                data: { classes: $('#class').val(), term: $('#term').val()},
                success: function(data){
                    $('#course_div').html(data);
                },
                error: function() {
                    alert('Ajax Error');
                }
            });
        }
    </script>

    <script>
        // 往上移動
        function prev(e) {
            // 取得自己
            e = $(e).parent();
            // 取得前一個元素
            var prev = $(e).prev();
            // 檢查是否有上一個元素
            if (prev.length) {
                // 刪除自己
                $(e).remove();
                // 將自己新增在前一個元素前
                $(prev).before(e)
            }
        }

        // 往下移動
        function next(e) {
            // 取得自己
            e = $(e).parent();
            // 取得下一個元素
            var prev = $(e).next();
            // 檢查是否有下一個元素
            if (next.length) {
                // 刪除自己
                $(e).remove();
                // 將自己新增在前一個元素前
                $(prev).after(e)
            }
        }
    </script>

@endsection