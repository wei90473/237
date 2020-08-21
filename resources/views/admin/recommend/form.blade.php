@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'recommend';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">薦送機關維護表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/recommend" class="text-info">薦送機關維護列表</a></li>
                        <li class="active">薦送機關維護表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/recommend/'.$data->enrollorg, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/recommend/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">薦送機關維護表單</h3></div>
                    <div class="card-body pt-4">

                        <!-- 薦送機關代碼 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">薦送機關代碼<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="enrollorg" name="enrollorg" placeholder="請輸入薦送機關代碼" value="{{ old('enrollorg', (isset($data->enrollorg))? $data->enrollorg : '') }}" autocomplete="off" required maxlength="255" {{ (isset($data))? 'readonly' : '' }}>
                            </div>
                        </div>

                        <!-- 薦送機關名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">薦送機關名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="enrollname" name="enrollname" placeholder="請輸入薦送機關名稱" value="{{ old('enrollname', (isset($data->enrollname))? $data->enrollname : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>


                        {{-- 取得主管機關列表 --}}
                        <?php $list = $base->getDBList('M13tb', ['organ', 'lname']);?>

                        <!-- 主管機關 -->
                        <div class="form-group row institution">
                            <label class="col-sm-2 control-label text-md-right pt-2">主管機關<span class="text-danger">*</span></label>

                            <div class="col-sm-10">
                                <select id="organ" name="organ" class="select2 form-control select2-single input-max" onchange="organChange();">
                                    @foreach($list as $va)
                                        <option value="{{ $va->organ }}" {{ old('organ', (isset($data->organ))? $data->organ : '') == $va->organ? 'selected' : '' }}>{{ $va->organ }} {{ $va->lname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 地址 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">地址<span class="text-danger">*</span></label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">郵遞區號</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="zip" name="zip"  value="{{ old('zip', (isset($data->zip))? $data->zip : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">地址</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="255" autocomplete="off" id="address" name="address"  value="{{ old('address', (isset($data->address))? $data->address : '') }}" required>
                                </div>

                            </div>
                        </div>

                        <!-- 層級 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">層級<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="grade" name="grade" class="select2 form-control select2-single input-max" onchange="gradeChange();">
                                    @foreach(config('app.recommend_grade') as $key => $va)
                                        <option value="{{ $key }}" {{ old('grade', (isset($data->grade))? $data->grade : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 上層機關代碼 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">上層機關代碼<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="uporgan" name="uporgan" placeholder="請輸入上層機關代碼" value="{{ old('uporgan', (isset($data->uporgan))? $data->uporgan : '') }}" autocomplete="off" maxlength="255" required>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/recommend">
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
        // 層級切換
        function gradeChange() {

            var grade = $('#grade').val();

            if (grade > 1) {
                $('#uporgan').attr('readonly', false);
            } else {
                $('#uporgan').val($('#organ').val());

                $('#uporgan').attr('readonly', true);
            }
        }

        // 主管機關切換
        function organChange() {

            var grade = $('#grade').val();

            if (grade == 1) {
                $('#uporgan').val($('#organ').val());
            }
        }

        // 層級切換
        gradeChange();
    </script>

@endsection