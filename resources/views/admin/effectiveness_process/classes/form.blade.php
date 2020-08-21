@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'classes';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">班別資料表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/classes" class="text-info">班別資料列表</a></li>
                        <li class="active">班別資料表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/classes/'.$data->class, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/classes/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">班別資料表單</h3></div>
                    <div class="card-body pt-4">

                        @if(!isset($data))
                        <!-- 班號年份 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班號年份<span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="year" name="year" min="1" placeholder="請輸入班號年份" value="{{ old('year', (isset($data->number))? mb_substr($data->number, 0, 3) : date('Y')-1911 ) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required maxlength="3" {{ (isset($data))? 'disabled' : '' }}>

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        <!-- </div> -->

                        <!-- 流水號 -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-sm-2 control-label text-md-right pt-2">流水號<span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="serial_number" name="serial_number" min="1" placeholder="請輸入班號年份" value="{{ old('serial_number', (isset($data->number))? mb_substr($data->number, 3, 3) : $base->getMaxClass() ) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required maxlength="3" onchange="locationChange();" {{ (isset($data))? 'disabled' : '' }}>

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        @else
                            <!-- 班別 -->
                            <!-- <div class="form-group row"> -->
                                <label class="col-sm-2 control-label text-md-right pt-2">班別<span class="text-danger">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" placeholder="請輸入班別名稱(中文)" value="{{ $data->class }}" autocomplete="off" required maxlength="255" readonly>
                                </div>
                            </div>
                        @endif

                        <!-- 班別名稱(中文) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班別名稱(中文)<span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control input-max" id="name" name="name" placeholder="請輸入班別名稱(中文)" value="{{ old('name', (isset($data->name))? $data->name : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        <!-- </div> -->

                        <!-- 班別性質 -->
                        <?php $typeList = $base->getSystemCode('K')?>
                        <!-- <div class="form-group row"> -->
                            <label class="col-md-2 col-form-label text-md-right">班別性質<span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <select id="type" name="type" class="select2 form-control select2-single input-max">
                                    @foreach($typeList as $code => $va)
                                        <option value="{{ $code }}" {{ old('type', (isset($data->type))? $data->type : 1) == $code? 'selected' : '' }}>{{ $va['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 上課地點 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">上課地點<span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <select id="branch" name="branch" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.branch') as $key => $va)
                                        <option value="{{ $key }}" {{ old('branch', (isset($data->branch))? $data->branch : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <!-- </div> -->

                        <!-- 上課方式 -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-md-2 col-form-label text-md-right">上課方式<span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <select id="style" name="style" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.style') as $key => $va)
                                        <option value="{{ $key }}" {{ old('style', (isset($data->style))? $data->style : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <!-- 官等區分 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">官等區分<span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <select id="post" name="post" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.post') as $key => $va)
                                        <option value="{{ $key }}" {{ old('post', (isset($data->post))? $data->post : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <!-- </div> -->

                        <!-- 辦理方式 -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-md-2 col-form-label text-md-right">辦理方式<span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <select id="process" name="process" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.process') as $key => $va)
                                        <option value="{{ $key }}" {{ old('process', (isset($data->process))? $data->process : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 講座審查 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">講座審查<span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <!-- <select id="profchk" name="profchk" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.yorn') as $key => $va)
                                        <option value="{{ $key }}" {{ old('profchk', (isset($data->profchk))? $data->profchk : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
								<input type="checkbox" id="profchk" name="profchk" value="{{ old('profchk', (isset($data->profchk))? $data->profchk : 1) == $key? '1' : '0' }}" 
								checked='{{ $data->profchk}}' >
								 講座審查
							  </input>
                            </div>
                        <!-- </div> -->

                        <!-- 訓練性質 -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-md-2 col-form-label text-md-right">訓練性質<span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <select id="traintype" name="traintype" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.traintype') as $key => $va)
                                        <option value="{{ $key }}" {{ old('traintype', (isset($data->traintype))? $data->traintype : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 是否住宿 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">是否住宿</label>
                            <div class="col-md-4">
                                <!-- <select id="board" name="board" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.board') as $key => $va)
                                        <option value="{{ $key }}" {{ old('board', (isset($data->board))? $data->board : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.board') as $key => $va)
                                    <input type="radio" id="board" name="board" value="{{ $key }}" {{ old('board', (isset($data->board))? $data->board : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        <!-- </div> -->

                        <!-- 委辦班別 -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-md-2 col-form-label text-md-right">委辦班別</label>
                            <div class="col-md-4">
                                <select id="special" name="special" class="select2 form-control select2-single input-max" onchange="signinChange()">
                                    @foreach(config('app.yorn') as $key => $va)
                                        <option value="{{ $key }}" {{ old('special', (isset($data->special))? $data->special : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 追蹤培訓班別 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">追蹤培訓班別</label>
                            <div class="col-md-4">
                                <!-- <select id="trace" name="trace" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.yorn') as $key => $va)
                                        <option value="{{ $key }}" {{ old('trace', (isset($data->trace))? $data->trace : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.yorn') as $key => $va)
                                    <input type="radio" id="trace" name="trace" value="{{ $key }}" {{ old('trace', (isset($data->trace))? $data->trace : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        <!-- </div> -->

                        <!-- 每日上課時數 -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-sm-2 control-label text-md-right pt-2">每日上課時數<span class="text-danger">*</span></label>
                            <div class="col-sm-4">


                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="dayhour" name="dayhour" min="1" placeholder="請輸入每日上課時數" value="{{ old('dayhour', (isset($data->dayhour))? $data->dayhour : 1) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="6" required onchange="countTotalDays();">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 訓期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">訓期<span class="text-danger">*</span></label>
                            <div class="col-sm-4">


                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="period" name="period" min="1" placeholder="請輸入訓期" value="{{ old('period', (isset($data->period))? $data->period : 1) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" maxlength="255" required onchange="kindChange();countTotalDays();">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        <!-- </div> -->

                        <!-- 訓期類別 -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-md-2 col-form-label text-md-right">訓期類別</label>
                            <div class="col-md-4">
                                <!-- <select id="kind" name="kind" class="select2 form-control select2-single input-max" onchange="kindChange();countTotalDays()">
                                    @foreach(config('app.kind') as $key => $va)
                                        <option value="{{ $key }}" {{ old('kind', (isset($data->kind))? $data->kind : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.kind') as $key => $va)
                                    <input type="radio" id="kind" name="kind" value="{{ $key }}" {{ old('kind', (isset($data->kind))? $data->kind : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>

                        <!-- 每期人數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">每期人數<span class="text-danger">*</span></label>
                            <div class="col-sm-4">


                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="quota" name="quota" min="1" placeholder="請輸入每期人數" value="{{ old('quota', (isset($data->quota))? $data->quota : 1) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        <!-- </div> -->

                        <!-- 備取人數 -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-sm-2 control-label text-md-right pt-2">備取人數</label>
                            <div class="col-sm-4">


                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="extraquota" name="extraquota" min="1" placeholder="請輸入備取人數" value="{{ old('extraquota', (isset($data->extraquota))? $data->extraquota : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>



                        <!-- 訓練總天數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">訓練總天數</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control number-input-max" id="trainday" name="trainday" value="{{ old('trainday', (isset($data->trainday))? $data->trainday : NULL) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" readonly>
                            </div>
                        <!-- </div> -->

                        <!-- 學習性質 -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-md-2 col-form-label text-md-right">學習性質</label>
                            <div class="col-md-4">
                                <!-- <select id="classified" name="classified" class="select2 form-control select2-single input-max" onchange="countTotalDays();">
                                    @foreach(config('app.classified') as $key => $va)
                                        <option value="{{ $key }}" {{ old('classified', (isset($data->classified))? $data->classified : 2) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.classified') as $key => $va)
                                    <input type="radio" id="classified" name="classified" value="{{ $key }}" {{ old('classified', (isset($data->classified))? $data->classified : 2) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>

                        <!-- 數位時數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">數位時數</label>
                            <div class="col-sm-4">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="elearnhr" name="elearnhr" min="0" placeholder="請輸入數位時數" value="{{ old('elearnhr', (isset($data->elearnhr))? $data->elearnhr : NULL) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" onchange="hoursChange('digital_hours');">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        <!-- </div> -->

                        <!-- 實體時數 -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-sm-2 control-label text-md-right pt-2">實體時數</label>
                            <div class="col-sm-4">


                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="classhr" name="classhr" min="0" placeholder="請輸入實體時數" value="{{ old('classhr', (isset($data->classhr))? $data->classhr : NULL) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" onchange="hoursChange('entity_hours');">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 訓練總時數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">訓練總時數</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control number-input-max" id="trainhour" name="trainhour" value="{{ old('trainhour', (isset($data->trainhour))? $data->trainhour : NULL) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" readonly>
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

                        <!-- 研習方式 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">研習方式</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="content" id="content" maxlength="255">{{ old('content', (isset($data->content))? $data->content : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 備註 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">備註</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="remark" id="remark">{{ old('remark', (isset($data->remark))? $data->remark : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 班別名稱(英文) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班別名稱(英文)<span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control input-max" id="english" name="english" placeholder="請輸入班別名稱(英文)" value="{{ old('english', (isset($data->english))? $data->english : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        <!-- </div> -->


                        <!-- 訓練績效計算方式 -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-md-2 col-form-label text-md-right">訓練績效計算方式</label>
                            <div class="col-md-4">
                                <!-- <select id="cntflag" name="cntflag" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.cntflag') as $key => $va)
                                        <option value="{{ $key }}" {{ old('cntflag', (isset($data->cntflag))? $data->cntflag : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.cntflag') as $key => $va)
                                    <input type="radio" id="cntflag" name="cntflag" value="{{ $key }}" {{ old('cntflag', (isset($data->cntflag))? $data->cntflag : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>

                        <!-- 報名方式 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">報名方式</label>
                            <div class="col-md-4">
                                <select id="signin" name="signin" class="select2 form-control select2-single input-max" onchange="signinChange();">
                                    @foreach(config('app.signin') as $key => $va)
                                        <option value="{{ $key }}" {{ old('signin', (isset($data->signin))? $data->signin : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <!-- </div> -->

                        <!-- 參訓機關 -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-md-2 col-form-label text-md-right">參訓機關</label>
                            <div class="col-md-4">
                                <select id="orgchk" name="orgchk" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.orgchk') as $key => $va)
                                        <option value="{{ $key }}" {{ old('orgchk', (isset($data->orgchk))? $data->orgchk : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 機關分區 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">機關分區</label>
                            <div class="col-md-4">
                                <select id="areachk" name="areachk" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.areachk') as $key => $va)
                                        <option value="{{ $key }}" {{ old('areachk', (isset($data->areachk))? $data->areachk : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <!-- </div> -->


                        <!-- 職等(第1組) -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-md-2 col-form-label text-md-right">職等(第1組)</label>
                            <div class="col-md-4">
                                <select id="rkchk" name="rkchk[]" class="select2 form-control select2-single input-max" multiple="multiple">
                                    <?php $oldData = old('rkchk', (isset($data->rkchk))? $data->rkchk : array());?>
                                    @foreach(config('app.post') as $key => $va)
                                        <option value="{{ $key }}" {{  in_array($key, $oldData)? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <!-- 主管班(第1組) -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">主管班(第1組)</label>
                            <div class="col-md-4">
                                <select id="chfchk" name="chfchk" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.chfchk') as $key => $va)
                                        <option value="{{ $key }}" {{ old('chfchk', (isset($data->chfchk))? $data->chfchk : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <!-- </div> -->


                        <!-- 人事班(第1組) -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-md-2 col-form-label text-md-right">人事班(第1組)</label>
                            <div class="col-md-4">
                                <select id="perchk" name="perchk" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.perchk') as $key => $va)
                                        <option value="{{ $key }}" {{ old('perchk', (isset($data->perchk))? $data->perchk : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <!-- 職等(第2組) -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">職等(第2組)</label>
                            <div class="col-md-4">
                                <select id="subrkchk" name="subrkchk[]" class="select2 form-control select2-single input-max" multiple="multiple">
                                    <?php $oldData = old('subrkchk', (isset($data->subrkchk))? $data->subrkchk : array());?>
                                    @foreach(config('app.post') as $key => $va)
                                        <option value="{{ $key }}" {{  in_array($key, $oldData)? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <!-- </div> -->


                        <!-- 主管班(第2組) -->
                        <!-- <div class="form-group row"> -->
                            <label class="col-md-2 col-form-label text-md-right">主管班(第2組)</label>
                            <div class="col-md-4">
                                <select id="subchfchk" name="subchfchk" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.chfchk') as $key => $va)
                                        <option value="{{ $key }}" {{ old('subchfchk', (isset($data->subchfchk))? $data->subchfchk : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <!-- 人事班(第2組) -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">人事班(第2組)</label>
                            <div class="col-md-4">
                                <select id="subperchk" name="subperchk" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.perchk') as $key => $va)
                                        <option value="{{ $key }}" {{ old('subperchk', (isset($data->subperchk))? $data->subperchk : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



















                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/classes">
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
    // 訓期類別為天跟周要為整數,天為小數第一位
    function kindChange() {
        // 確保訓期有值
        if ( ! $('#period').val()) {
            $('#period').val(1)
        }

        if ($('#kind').val() == '2') {
            // 時數為小數點第一位
            var period = parseFloat($('#period').val());
            $('#period').val(period.toFixed(1));
        } else {
            // 時數為整數
            $('#period').val(parseInt($('#period').val()));
        }
    }

    // 計算總天數、時數
    function countTotalDays() {
        // 確保訓期有值
        if ( ! $('#period').val()) {
            $('#period').val(1)
        }
        // 確保每日上課時數有值
        if ( ! $('#dayhour').val()) {
            $('#dayhour').val(8)
        }

        // 取得訓期
        var period = parseFloat($('#period').val());
        // 取得每天時數
        var dayhoues = parseInt($('#dayhour').val());

        // 計算總天數
        if ($('#kind').val() == '1') {
            // 以週為單位
            var total_days = period * 5;
        } else if ($('#kind').val() == '2') {
            // 以天為單位
            var total_days = period.toFixed(0) ;
        } else {
            // 以時為單位
            var total_days = period / parseInt($('#dayhour').val());
        }

        // 寫入訓練傯天數
        $('#trainday').val(total_days);

        // 計算總時數
        if ($('#kind').val() == '1') {
            // 以週為單位
            var totalhours = period * 5 * dayhoues;

        } else if ($('#kind').val() == '2') {
            // 以天為單位
            var totalhours = parseInt(period * dayhoues);
        } else {
            // 以時為單位
            var totalhours = period;
        }

        // 寫入訓練總時數
        $('#trainhour').val(totalhours);

        if ($('#classified').val() == '1') {
            // 數位
            $('#elearnhr').val(totalhours);
            $('#elearnhr').attr('disabled', false);
            $('#classhr').val(0);
            $('#classhr').attr('disabled', true);

        } else if($('#learn_type').val() == '2'){
            // 實體
            $('#elearnhr').val(0);
            $('#elearnhr').attr('disabled', true);
            $('#classhr').val(totalhours);
            $('#classhr').attr('disabled', false);
        } else {
            // 混成
            $('#elearnhr').val(0);
            $('#elearnhr').attr('disabled', false);
            $('#classhr').val(totalhours);
            $('#classhr').attr('disabled', false);


        }
    }

    // 實體時數或數位時數不能高於總時數
    function hoursChange(em) {
        var trainhour = parseInt($('#trainhour').val());

        if (parseInt($('#'+em).val()) > trainhour) {
            $('#'+em).val(trainhour);
        }
    }

    // 切換地點
    @if(!isset($data))
        function locationChange() {

            if ($('#serial_number').val()) {
                if (parseInt($('#serial_number').val()) > 500) {

                    $('#branch').val('2').trigger("change");
                    $("#areachk").val("1").trigger("change");
                } else {

                    $('#branch').val('1').trigger("change");
                    $("#areachk").val("3").trigger("change");
                }
            }
        }
        // 初始化
        locationChange();
    @endif

    // 報名方式：若有選取委辦班別，報名方式不能為2或3一定要是1
    function signinChange()
    {
        if ($('#special').val() == 'Y' && $('#signin').val() == '2') {

            swal('若為委辦班別，則報名方式不可為年度臨時增開班期!');
            // 強制為1
            $('#signin').val('1').trigger("change");
        }

        if ($('#special').val() == 'Y' && $('#signin').val() == '3') {

            swal('若為委辦班別，則報名方式不可為開放自由報名班期!');
            // 強制為1
            $('#signin').val('1').trigger("change");
        }
    }

    // 初始化
    kindChange();
    countTotalDays();

</script>
@endsection