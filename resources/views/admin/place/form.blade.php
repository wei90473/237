@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'place';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地資料表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/place" class="text-info">場地資料列表</a></li>
                        <li class="active">場地資料表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/place/'.$data->site, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/place/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">場地資料表單</h3></div>
                    <div class="card-body pt-4">

                        <!-- 場地編號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">場地編號<span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control input-max" id="site" name="site" placeholder="請輸入場地編號" value="{{ old('site', (isset($data->site))? $data->site : '') }}" autocomplete="off" required maxlength="3" {{ (isset($data))? 'readonly' : '' }} onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">院區</label>
                                <div class="col-md-3">
                                    <select id="branch" name="branch" class="select2 form-control select2-single input-max" disabled>
                                        @foreach(config('app.branch') as $key => $va)
                                            <option value="{{ $key }}" {{ old('branch', (isset($data->branch))? $data->branch : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                    </select>
                                </div>
                        </div>

                        <!-- 場地名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">場地名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="name" name="name" placeholder="請輸入場地名稱" value="{{ old('name', (isset($data->name))? $data->name : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                        <!-- 場地類型 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">場地類型<span class="text-danger">*</span></label>
                            <div class="col-md-10 pt-2">
                                @foreach(config('app.place_type') as $key => $va)
                                    <input type="radio" name="type" value="{{ $key }}" {{ old('type', (isset($data->type))? $data->type : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>

                        <!-- 是否外借 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">是否外借<span class="text-danger">*</span></label>
                            <div class="col-md-10 pt-2">
                                @foreach(config('app.yesorno') as $key => $va)
                                    <input type="radio" name="open" value="{{ $key }}" {{ old('open', (isset($data->open))? $data->open : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>

                        <!-- 收費類型 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">收費類型<span class="text-danger">*</span></label>
                            <div class="col-md-10 pt-2">
                                @foreach(config('app.place_feetype') as $key => $va)
                                    <input type="radio" name="feetype" value="{{ $key }}" {{ old('feetype', (isset($data->feetype))? $data->feetype : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>

                        <!-- 管理服務費(A時段) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">管理服務費(A時段)</label>
                            <div class="col-sm-10">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <input type="text" class="form-control number-input-max" id="feea" name="feea" placeholder="請輸入管理服務費(A時段)" value="{{ old('feea', (isset($data->feea))? $data->feea : NULL) }}" autocomplete="off" maxlength="5" onkeyup="this.value=this.<span class="input-group-btn">
                                </div>
                            </div>
                        </div>

                        <!-- 管理服務費(B時段) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">管理服務費(B時段)</label>
                            <div class="col-sm-10">


                                <div class="input-group bootstrap-touchspin number_box">
                                    <input type="text" class="form-control number-input-max" id="feeb" name="feeb" placeholder="請輸入管理服務費(B時段)" value="{{ old('feeb', (isset($data->feeb))? $data->feeb : NULL) }}" autocomplete="off" maxlength="5" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                </div>
                            </div>
                        </div>

                        <!-- 管理服務費(C時段) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">管理服務費(C時段)</label>
                            <div class="col-sm-10">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <input type="text" class="form-control number-input-max" id="feec" name="feec" placeholder="請輸入管理服務費(C時段)" value="{{ old('feec', (isset($data->feec))? $data->feec : NULL) }}" autocomplete="off" maxlength="5" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                </div>
                            </div>
                        </div>

                        <!-- 場次限制 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">場次限制</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-max" id="limit" name="limit" placeholder="請輸入場地名稱" value="{{ old('limit', (isset($data->limit))? $data->limit : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 座位類型 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">座位類型<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <select id="seat" name="seat" class="select2 form-control select2-single input-max" required>
                                    @foreach(config('app.place_seat') as $key => $va)
                                        <option value="{{ $key }}" {{ old('seat', (isset($data->seat))? $data->seat : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- 教室門口 -->
                            <label class="col-md-2 col-form-label">教室門口<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <select id="door" name="door" class="select2 form-control select2-single input-max" required>
                                    @foreach(config('app.place_door') as $key => $va)
                                        <option value="{{ $key }}" {{ old('door', (isset($data->door))? $data->door : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/place">
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