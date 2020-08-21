@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'training_process';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">訓後問卷處理表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/training_process" class="text-info">訓後問卷處理列表</a></li>
                        <li class="active">訓後問卷處理表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/training_process/'.$data->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/training_process/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">訓後問卷處理表單</h3></div>
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

                        <!-- 編號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">編號<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="serno" name="serno" placeholder="請輸入編號" value="{{ old('serno', (isset($data->serno))? $data->serno : '') }}" autocomplete="off" maxlength="255" readonly>
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



                        <!-- 加註說明 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">加註說明</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" name="comment" id="comment" maxlength="255">{{ old('comment', (isset($data->comment))? $data->comment : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 增加課程 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">增加課程</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="addcourse" name="addcourse" placeholder="請輸入增加課程" value="{{ old('addcourse', (isset($data->addcourse))? $data->addcourse : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 刪除課程 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">刪除課程</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="delcourse" name="delcourse" placeholder="請輸入刪除課程" value="{{ old('delcourse', (isset($data->delcourse))? $data->delcourse : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 整體評價 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">整體評價</label>
                            <div class="col-sm-10">


                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="wholeval" name="wholeval" min="1" max="100" placeholder="請輸入整體評價" value="{{ old('wholeval', (isset($data->wholeval))? $data->wholeval : 0) }}" autocomplete="off" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>


                        <!-- 介紹其他同仁 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">介紹其他同仁</label>
                            <div class="col-md-10">
                                <select id="willing" name="willing" class="select2 form-control select2-single input-max">
                                    <option value="1" {{ old('willing', (isset($data->willing))? $data->willing : '') == 1? 'selected' : '' }}>願意</option>
                                    <option value="2" {{ old('willing', (isset($data->willing))? $data->willing : '') == 2? 'selected' : '' }}>不願意</option>
                                </select>
                            </div>
                        </div>

                        <!-- 其他建議 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">其他建議</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="othercom" name="othercom" placeholder="請輸入其他建議" value="{{ old('othercom', (isset($data->othercom))? $data->othercom : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>



                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr>
                                    <th class="text-center" width="70">題次</th>
                                    <th>課程名稱</th>
                                    <th>對工作的助益程度</th>
                                </tr>
                                </thead>
                                <tbody id="course_div">

                                    @foreach($list as $va)
                                        <tr>
                                            <td>{{ $va->no }}</td>
                                            <td>{{ $va->coursename }}</td>
                                            <td><input name="ans[{{ $va->id }}]" value="{{ $va->ans }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="1"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>



                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/training_process">
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
                url: '/admin/training_process/getcourse',
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

@endsection