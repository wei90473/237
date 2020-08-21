@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'site_survey_old';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地問卷處理(96~100)表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/site_survey_old" class="text-info">場地問卷處理(96~100)列表</a></li>
                        <li class="active">場地問卷處理(96~100)表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/site_survey_old/'.$data->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/site_survey_old/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">場地問卷處理(96~100)表單</h3></div>
                    <div class="card-body pt-4">


                        @if( ! isset($data))
                            {{-- 新增 --}}

                            <!-- 年度 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">年度<span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control input-max" placeholder="請輸入年度" id="year" name="year" value="{{ old('year', (isset($data->year))? $data->year : '') }}" autocomplete="off" required maxlength="255" onchange="yearChange();" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                </div>
                            </div>

                            <!-- 第幾次調查 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">第幾次調查<span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select id="times" name="times" class="select2 form-control select2-single input-max" required></select>
                                </div>
                            </div>
                        @else
                            {{-- 編輯 --}}

                            <!-- 年度 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">年度<span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control input-max" placeholder="請輸入年度" value="{{ old('year', (isset($data->year))? $data->year : '') }}" autocomplete="off" required readonly maxlength="255">
                                </div>
                            </div>

                            <!-- 第幾次調查 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">第幾次調查<span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control input-max" placeholder="請輸入第幾次調查" value="{{ old('times', (isset($data->times))? $data->times : '') }}" autocomplete="off" required readonly maxlength="255">
                                </div>
                            </div>

                            <!-- 編號 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">編號<span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control input-max" placeholder="請輸入第幾次調查" value="{{ old('serno', (isset($data->serno))? $data->serno : '') }}" autocomplete="off" required readonly maxlength="255">
                                </div>
                            </div>
                        @endif



                        <!-- 1. -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">1.</label>
                            <div class="col-sm-10">

                                <label class="control-label ">本中心網站提供場地設施介紹資訊及相關輔助說明之完整性：</label>

                                <div class="input-group bootstrap-touchspin number_box">

                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="q1" name="q1" min="0" max="5" placeholder="請輸入問題1" value="{{ old('q1', (isset($data->q1))? $data->q1 : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">2.</label>
                            <div class="col-sm-10">

                                <label class="control-label ">本中心網路預約場地申辦方式及相關輔助說明之便利性：</label>

                                <div class="input-group bootstrap-touchspin number_box">

                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="q2" name="q2" min="0" max="5" placeholder="請輸入問題1" value="{{ old('q2', (isset($data->q2))? $data->q2 : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 3. -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">3.</label>
                            <div class="col-sm-10">

                                <label class="control-label ">本中心網路預約申請之回覆速度：</label>

                                <div class="input-group bootstrap-touchspin number_box">

                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="q3" name="q3" min="0" max="5" placeholder="請輸入問題1" value="{{ old('q3', (isset($data->q3))? $data->q3 : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 4. -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">4.</label>
                            <div class="col-sm-10">

                                <label class="control-label ">福華國際文教會館場地業務接洽人員服務態度、行政效率及回應速度：</label>

                                <div class="input-group bootstrap-touchspin number_box">

                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="q4" name="q4" min="0" max="5" placeholder="請輸入問題1" value="{{ old('q4', (isset($data->q4))? $data->q4 : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 5. -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">5.</label>
                            <div class="col-sm-10">

                                <label class="control-label ">會館場地收費之合理性：</label>

                                <div class="input-group bootstrap-touchspin number_box">

                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="q5" name="q5" min="0" max="5" placeholder="請輸入問題1" value="{{ old('q5', (isset($data->q5))? $data->q5 : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 6. -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">6.</label>
                            <div class="col-sm-10">

                                <label class="control-label ">場地附屬燈光、音響等視聽設備功能：</label>

                                <div class="input-group bootstrap-touchspin number_box">

                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="q6" name="q6" min="0" max="5" placeholder="請輸入問題1" value="{{ old('q6', (isset($data->q6))? $data->q6 : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- ７. -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">7.</label>
                            <div class="col-sm-10">

                                <label class="control-label ">會館場地工作人員的效率及服務態度：</label>

                                <div class="input-group bootstrap-touchspin number_box">

                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                    <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="q7" name="q7" min="0" max="5" placeholder="請輸入問題1" value="{{ old('q7', (isset($data->q7))? $data->q7 : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                    <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                </span>
                                </div>
                            </div>
                        </div>

                        <!-- 8. -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">8.</label>
                            <div class="col-sm-10">

                                <label class="control-label ">會館提供會議附屬服務(如茶點、餐飲等)之品質及收費合理性：</label>

                                <div class="input-group bootstrap-touchspin number_box">

                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                            </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="q8" name="q8" min="0" max="5" placeholder="請輸入問題1" value="{{ old('q8', (isset($data->q8))? $data->q8 : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                            </span>
                                </div>
                            </div>
                        </div>

                        <!-- 9. -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">9.</label>
                            <div class="col-sm-10">

                                <label class="control-label ">場地動線標示及環境清潔等服務：</label>

                                <div class="input-group bootstrap-touchspin number_box">

                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                            </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="q9" name="q9" min="0" max="5" placeholder="請輸入問題1" value="{{ old('q9', (isset($data->q9))? $data->q9 : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                            </span>
                                </div>
                            </div>
                        </div>

                        <!-- 10. -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">10.</label>
                            <div class="col-sm-10">

                                <label class="control-label ">中心及會館整體的服務水準：</label>

                                <div class="input-group bootstrap-touchspin number_box">

                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                            </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="q10" name="q10" min="0" max="5" placeholder="請輸入問題1" value="{{ old('q10', (isset($data->q10))? $data->q10 : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                            </span>
                                </div>
                            </div>
                        </div>


                            <!-- 服務機關性質 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">服務機關性質</label>
                                <div class="col-md-10">
                                    <select id="dept" name="dept" class="select2 form-control select2-single input-max">
                                        @foreach(config('app.site_survey_dept') as $key => $va)
                                            <option value="{{ $key }}" {{ old('dept', (isset($data->dept))? $data->dept : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- 服務機關性質其他說明 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">服務機關性質其他說明</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control input-max" id="extdept" name="extdept" placeholder="請輸入其他" value="{{ old('extdept', (isset($data->extdept))? $data->extdept : '') }}" autocomplete="off" maxlength="255">
                                </div>
                            </div>

                            <!-- 申請使用本中心何種場地 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">申請使用本中心何種場地</label>
                                <div class="col-sm-10">
                                    <div class="checkbox checkbox-primary">
                                        <input id="site1" name="site1" value="1" type="checkbox" {{ (isset($data) && $data->site1)? 'checked' : '' }}>
                                        <label for="site1">
                                            1樓前瞻廳（國際會議廳）
                                        </label>
                                    </div>

                                    <div class="checkbox checkbox-primary">
                                        <input id="site2" name="site2" value="1" type="checkbox" {{ (isset($data) && $data->site2)? 'checked' : '' }}>
                                        <label for="site2">
                                            2樓卓越堂（集會堂）
                                        </label>
                                    </div>

                                    <div class="checkbox checkbox-primary">
                                        <input id="site3" name="site3" value="1" type="checkbox" {{ (isset($data) && $data->site3)? 'checked' : '' }}>
                                        <label for="site3">
                                            14樓貴賓廳
                                        </label>
                                    </div>

                                    <div class="checkbox checkbox-primary">
                                        <input id="site4" name="site4" value="1" type="checkbox" {{ (isset($data) && $data->site4)? 'checked' : '' }}>
                                        <label for="site4">
                                            其他教室
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- 貴單位租借本中心場地次數 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">貴單位租借本中心場地次數</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control input-max" id="applycnt" name="applycnt" placeholder="請輸入其他" value="{{ old('applycnt', (isset($data->applycnt))? $data->applycnt : '') }}" autocomplete="off" maxlength="255">
                                </div>
                            </div>


                            <!-- 租借方式 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">租借方式</label>
                                <div class="col-md-10">
                                    <select id="apply" name="apply" class="select2 form-control select2-single input-max">
                                        <option value="1" {{ old('apply', (isset($data->apply))? $data->apply : 1) == $key? 'selected' : '' }}>網路預約</option>
                                        <option value="2" {{ old('apply', (isset($data->apply))? $data->apply : 2) == $key? 'selected' : '' }}>其他</option>
                                    </select>
                                </div>
                            </div>


                            <!-- 其他租借方式說明 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">其他租借方式說明</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control input-max" id="extapply" name="extapply" placeholder="請輸入其他租借方式說明" value="{{ old('extapply', (isset($data->extapply))? $data->extapply : '') }}" autocomplete="off" maxlength="255">
                                </div>
                            </div>

                            <!-- 填表人職務 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">填表人職務</label>
                                <div class="col-md-10">
                                    <select id="duty" name="duty" class="select2 form-control select2-single input-max">
                                        <option value="1" {{ old('duty', (isset($data->duty))? $data->duty : 1) == $key? 'selected' : '' }}>承辦人</option>
                                        <option value="2" {{ old('duty', (isset($data->duty))? $data->duty : 2) == $key? 'selected' : '' }}>單位主管</option>
                                        <option value="3" {{ old('duty', (isset($data->duty))? $data->duty : 3) == $key? 'selected' : '' }}>機構負責人</option>
                                    </select>
                                </div>
                            </div>

                            <!-- 其他意見及建議 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">其他意見及建議</label>
                                <div class="col-md-10">
                                    <textarea class="form-control input-max" rows="5" name="comment" id="comment" maxlength="255">{{ old('comment', (isset($data->comment))? $data->comment : '') }}</textarea>
                                </div>
                            </div>



                    </div>

                    <div class="card-footer">
                        <!-- <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button> -->
                        <a href="/admin/site_survey_old">
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

    // 取得第幾次調查
    function yearChange() {
        var year = $('#year').val();

        $.ajax({
            type: "get",
            dataType: "html",
            url:"/admin/site_survey_old/gettimes/" + year,
            success: function(data){
                $("#times").html(data);
                $("#times").trigger("change");
            },
            error: function() {
                alert('Ajax Error');
            }
        });
    }
</script>
@endsection