@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'signup';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">線上報名設定表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/signup" class="text-info">線上報名設定列表</a></li>
                        <li class="active">線上報名設定表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'signup'=>'put', 'url'=>'/admin/signup/'.$data->id, 'id'=>'form']) !!}

            <input type="hidden" name="class" value="{{ $data->class }}">
            <input type="hidden" name="term" value="{{ $data->term }}">
            <input type="hidden" name="course" value="{{ $data->course }}">
            <input type="hidden" name="idno" value="{{ $data->idno }}">

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">線上報名設定表單</h3></div>
                    <div class="card-body pt-4">

                        <?php $config = (mb_substr($data->class, 0, 3, 'utf-8') > 107)? config('app.signup_signup_new') : config('app.signup_signup_old');?>

                        <!-- 教學方法(一) -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">教學方法(一)</label>
                            <div class="col-md-10">
                                <select id="signup1" name="signup1" class="select2 form-control select2-single input-max">
                                    <option value="">無</option>
                                    @foreach($config as $key => $va)
                                        <option value="{{ $key }}" {{ old('signup1', (isset($data->signup1))? $data->signup1 : NULL) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 教學方法(二) -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">教學方法(二)</label>
                            <div class="col-md-10">
                                <select id="signup2" name="signup2" class="select2 form-control select2-single input-max">
                                    <option value="">無</option>
                                    @foreach($config as $key => $va)
                                        <option value="{{ $key }}" {{ old('signup2', (isset($data->signup2))? $data->signup2 : NULL) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 教學方法(三) -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">教學方法(三)</label>
                            <div class="col-md-10">
                                <select id="signup3" name="signup3" class="select2 form-control select2-single input-max">
                                    <option value="">無</option>
                                    @foreach($config as $key => $va)
                                        <option value="{{ $key }}" {{ old('signup3', (isset($data->signup3))? $data->signup3 : NULL) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 其他方法1 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">其他方法1</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="other1" name="other1" placeholder="請輸入其他方法1" value="{{ old('other1', (isset($data->other1))? $data->other1 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 其他方法2 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">其他方法2</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="other2" name="other2" placeholder="請輸入其他方法2" value="{{ old('other2', (isset($data->other2))? $data->other2 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 其他方法3 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">其他方法3</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="other3" name="other3" placeholder="請輸入其他方法3" value="{{ old('other3', (isset($data->other3))? $data->other3 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 本課程不納入調查 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">本課程不納入調查</label>
                            <div class="col-sm-10">
                                <input type="checkbox" name="mark" value="Y" {{ ($data->mark == 'Y')? 'checked' : '' }}>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/signup">
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
        // 送出前檢查比重
        function verification(result) {
            if ($('#signup1').val() != '') {
                if ($('#signup1').val() == $('#signup2').val() || $('#signup1').val() == $('#signup3').val()) {
                    swal('教學方法比重重複');
                    return false;
                }
            }

            if ($('#signup2').val() != '') {
                if ( $('#signup2').val() == $('#signup3').val()) {
                    swal('教學方法比重重複');
                    return false;
                }
            }

            return result;
        }
    </script>
@endsection