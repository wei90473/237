@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'lecture';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座資料維護表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/lecture" class="text-info">講座資料維護列表</a></li>
                        <li class="active">講座資料維護表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/lecture/'.$data->serno,  'enctype'=>'multipart/form-data','id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/lecture/','enctype'=>'multipart/form-data', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座資料維護表單</h3></div>
                    <div class="card-body pt-4">

                        <!-- 上傳個資授權書 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">上傳個資授權書</label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control input-max" id="Certificate" name="Certificate" readonly="readonly" value="{{ old('Certificate', (isset($data->Certificate))? $data->Certificate : '') }}" >
                              <button type="button" OnClick='javascript:$("#upload").click();'class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>選取檔案</button>
                              <button type="button" onclick="submitform();" class="btn btn-sm btn-info">上傳</button>
                              <?php if(isset($data->Certificate) && !empty($data->Certificate)){ ?>
                              <a target="_blank" href="/Uploads/Authorization/{{ $data->Certificate }}">
                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" >下載個資授權書</button>
                              </a>
                              <input type="hidden" id="old_file" name="old_file" value="{{ $data->Certificate }}" />
                              <?php } ?>
                              <input type="file" class="btn btn-sm btn-info" id="upload" name="upload" style="display:none;" />
                            </div>
                        </div>

                        <!-- 身分證字號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">身分證字號<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="idno" name="idno" <?=(isset($data->idno))?'readonly="readonly"':'';?> placeholder="請輸入身分證字號" value="{{ old('idno', (isset($data->idno))? $data->idno : '') }}" autocomplete="off" maxlength="10" required onchange="checkidno();" >
                                <?php if(isset($data->idno)){ ?>
                                <input type="hidden" id="old_idno" name="old_idno" value="{{ $data->idno }}" />
                                <?php } ?>
                            </div>
                            <!-- 證號別 -->
                            <label class="col-md-2 col-form-label text-md-right">證號別</label>
                            <div class="col-md-3">
                                <select id="idkind" name="idkind" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.idkind') as $key => $va)
                                        <option value="{{ $key }}" {{ old('idkind', (isset($data->idkind))? $data->idkind : 0) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 姓氏 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">姓名<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="cname" name="cname" <?=(isset($data->cname))?'readonly="readonly"':'';?> placeholder="請輸入姓氏" value="{{ old('cname', (isset($data->cname))? $data->cname : '') }}" autocomplete="off" maxlength="255" required>
                                <?php if(isset($data->cname)){ ?>
                                <input type="hidden" id="old_cname" name="old_cname" value="{{ $data->cname }}" />
                                <?php } ?>
                            </div>
                            <!-- 英文姓名 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">英文姓名</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="ename" name="ename" placeholder="請輸入英文姓名" value="{{ old('ename', (isset($data->ename))? $data->ename : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>
                        <?php if(isset($data->idno)){ ?>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2"></label>
                            <div class="col-sm-3">
                                <button type="button" OnClick='editIdno();'class="btn btn-sm btn-info">修改身分證字號及姓名</button>
                            </div>
                        </div>
                        <?php } ?>
                        <!-- 性別 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">性別</label>
                            <div class="col-md-3">
                                <!-- <select id="sex" name="sex" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.sex') as $key => $va)
                                        <option value="{{ $key }}" {{ old('sex', (isset($data->sex))? $data->sex : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.sex') as $key => $va)
                                    <input type="radio" id="sex{{ $key }}" name="sex" value="{{ $key }}"  {{ old('sex', (isset($data->sex))? $data->sex : 'M') == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                            <!-- 出生日期 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">出生日期</label>
                            <div class="col-sm-4">

                                <div class="input-group roc-date input-max">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">民國</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-year" maxlength="3" name="birth[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($data->birth) && !empty($data->birth))? mb_substr($data->birth, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">年</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-month" maxlength="2" name="birth[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($data->birth) && !empty($data->birth))? mb_substr($data->birth, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">月</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-day" maxlength="2" name="birth[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($data->birth) && !empty($data->birth))? mb_substr($data->birth, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">日</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- 國籍 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">國籍</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="citizen" name="citizen" placeholder="請輸入國籍" value="{{ old('citizen', (isset($data->citizen))? $data->citizen : '') }}" autocomplete="off" maxlength="255">
                            </div>
                            <!-- 護照號碼 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">護照號碼</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="passport" name="passport" placeholder="請輸入護照號碼" value="{{ old('passport', (isset($data->passport))? $data->passport : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 第二類被保險人 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">全民健保之第2類被保險人</label>
                            <div class="col-md-3">
                                <!-- <select id="insurekind1" name="insurekind1" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.yorn') as $key => $va)
                                        <option value="{{ $key }}" {{ old('insurekind1', (isset($data->insurekind1))? $data->insurekind1 : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                <input type="radio" id="insurekind1" name="insurekind1" value="Y" {{ old('insurekind1', (isset($data->insurekind1))? $data->insurekind1 : 1) == 'Y'? 'checked' : '' }}>是
                                <input type="radio" id="insurekind1" name="insurekind1" value="N" {{ old('insurekind1', (isset($data->insurekind1))? $data->insurekind1 : 'N') == 'N'? 'checked' : '' }}>否
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">原住民羅馬拼音</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="rname" name="rname"  placeholder="請輸入羅馬拼音" value="{{ old('rname', (isset($data->rname))? $data->rname : '') }}" autocomplete="off" maxlength="50">
                            </div>
                        </div>

                        <!-- 服務機關 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">服務機關</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="dept" name="dept" placeholder="請輸入服務機關" value="{{ old('dept', (isset($data->dept))? $data->dept : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 現職 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">現職</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="position" name="position" placeholder="請輸入現職" value="{{ old('position', (isset($data->position))? $data->position : '') }}" autocomplete="off" maxlength="255">
                            </div>
                            <!-- 分類 -->
                            <label class="col-md-2 col-form-label text-md-right">分類</label>
                            <div class="col-md-3">
                                <select id="kind" name="kind" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.lecture_kind') as $key => $va)
                                        <option value="{{ $key }}" {{ old('kind', (isset($data->kind))? $data->kind : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right"></label>
                            <div class="col-sm-10">
                                <a target="_blank" href="https://www.post.gov.tw/post/internet/Postal/index.jsp?ID=208">
                                    <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">郵遞區號查詢</button>
                                </a>
                            </div>
                        </div>
                        <!-- 機關地址 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">機關地址</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">郵遞區號</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="offzip" name="offzip"  value="{{ old('offzip', (isset($data->offzip))? $data->offzip : '') }}">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">地址</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="30" autocomplete="off" id="offaddress" name="offaddress"  value="{{ old('offaddress', (isset($data->offaddress))? $data->offaddress : '') }}">
                                </div>

                            </div>
                        </div>

                        <!-- 住家地址 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">住家地址</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">郵遞區號</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="homzip" name="homzip"  value="{{ old('homzip', (isset($data->homzip))? $data->homzip : '') }}">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">地址</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="30" autocomplete="off" id="homaddress" name="homaddress"  value="{{ old('homaddress', (isset($data->homaddress))? $data->homaddress : '') }}">
                                </div>

                            </div>
                        </div>

                        <!-- 戶籍地 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">戶籍地</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">郵遞區號</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="regzip" name="regzip"  value="{{ old('regzip', (isset($data->regzip))? $data->regzip : '') }}">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">地址</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="30" autocomplete="off" id="regaddress" name="regaddress"  value="{{ old('regaddress', (isset($data->regaddress))? $data->regaddress : '') }}">
                                </div>

                            </div>
                        </div>

                        <!-- 郵寄地址 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">郵寄地址</label>
                            <div class="col-md-3">
                                <!-- <select id="send" name="send" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.lecture_send') as $key => $va)
                                        <option value="{{ $key }}" {{ old('send', (isset($data->send))? $data->send : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.lecture_send') as $key => $va)
                                <input type="radio" id="send" name="send" value="{{ $key }}" {{ old('send', (isset($data->send))? $data->send : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                            <!-- 更新日期 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">更新日期</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="update_date" readonly="readonly" name="update_date" value="{{ old('update_date', (isset($data->update_date))? $data->update_date : '') }}" >
                            </div>
                        </div>

                        <!-- 電話(公一) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">電話(公一)</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">區碼</span>
                                    </div>

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="offtela1" name="offtela1"  value="{{ old('offtela1', (isset($data->offtela1))? $data->offtela1 : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="offtelb1" name="offtelb1"  value="{{ old('offtelb1', (isset($data->offtelb1))? $data->offtelb1 : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">分機</span>
                                    </div>

                                    <input type="text" style="max-width:100px;" class="form-control" maxlength="8" autocomplete="off" id="offtelc1" name="offtelc1"  value="{{ old('offtelc1', (isset($data->offtelc1))? $data->offtelc1 : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                </div>

                            </div>
                        </div>

                        <!-- 電話(公二) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">電話(公二)</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">區碼</span>
                                    </div>

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="offtela2" name="offtela2"  value="{{ old('offtela2', (isset($data->offtela2))? $data->offtela2 : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="offtelb2" name="offtelb2"  value="{{ old('offtelb2', (isset($data->offtelb2))? $data->offtelb2 : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">分機</span>
                                    </div>

                                    <input type="text" style="max-width:100px;" class="form-control" maxlength="8" autocomplete="off" id="offtelc2" name="offtelc2"  value="{{ old('offtelc2', (isset($data->offtelc2))? $data->offtelc2 : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                </div>

                            </div>
                        </div>

                        <!-- 電話(宅) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">電話(宅)</label>
                            <div class="col-sm-3">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">區碼</span>
                                    </div>

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="homtela" name="homtela"  value="{{ old('homtela', (isset($data->homtela))? $data->homtela : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="homtelb" name="homtelb"  value="{{ old('homtelb', (isset($data->homtelb))? $data->homtelb : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">


                                </div>

                            </div>
                            <!-- 行動電話 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="mobiltel" name="mobiltel" placeholder="請輸入行動電話" value="{{ old('mobiltel', (isset($data->mobiltel))? $data->mobiltel : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 傳真(公) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">傳真(公)</label>
                            <div class="col-sm-3">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">區碼</span>
                                    </div>

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="offfaxa" name="offfaxa"  value="{{ old('offfaxa', (isset($data->offfaxa))? $data->offfaxa : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="offfaxb" name="offfaxb"  value="{{ old('offfaxb', (isset($data->offfaxb))? $data->offfaxb : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">


                                </div>

                            </div>
                            <!-- 傳真(宅) -->
                            <label class="col-sm-2 control-label text-md-right pt-2">傳真(宅)</label>
                            <div class="col-sm-3">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">區碼</span>
                                    </div>

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="homfaxa" name="homfaxa"  value="{{ old('homfaxa', (isset($data->homfaxa))? $data->homfaxa : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>
                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="homfaxb" name="homfaxb"  value="{{ old('homfaxb', (isset($data->homfaxb))? $data->homfaxb : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                </div>

                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">Email</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="email" name="email" placeholder="請輸入Email" value="{{ old('email', (isset($data->email))? $data->email : '') }}" autocomplete="off" maxlength="255">
                            </div>
                            <!-- 聯絡人 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡人</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="liaison" name="liaison" placeholder="請輸入聯絡人" value="{{ old('liaison', (isset($data->liaison))? $data->liaison : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 人事總處 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">人事總處</label>
                            <div class="col-md-3">
                                <input type="radio" id="publicly" name="publicly" value="Y" {{ old('publicly', (isset($data->publicly))? $data->publicly : 1) == 'Y'? 'checked' : '' }}>可
                                <input type="radio" id="publicly" name="publicly" value="N" {{ old('publicly', (isset($data->publicly))? $data->publicly : 'N') == 'N'? 'checked' : '' }}>否
                            </div>
                            <!-- 公務機關 -->
                            <label class="col-md-2 col-form-label text-md-right">公務機關</label>
                            <div class="col-md-3">
                                <input type="radio" id="publish" name="publish" value="Y" {{ old('publish', (isset($data->publish))? $data->publish : 1) == 'Y'? 'checked' : '' }}>可
                                <input type="radio" id="publish" name="publish" value="N" {{ old('publish', (isset($data->publish))? $data->publish : 'N') == 'N'? 'checked' : '' }}>否
                            </div>
                        </div>

                        <!-- 最高學歷 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">最高學歷</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="education" name="education" placeholder="請輸入最高學歷" value="{{ old('education', (isset($data->education))? $data->education : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>
                        <!-- 專長領域 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">專長領域</label>
                            <div class="col-md-10">
                                <?php
                                $experience_list = DB::table('s01tb')
                                ->where('type', '=', 'B')
                                ->get();
                                ?>

                                <select class="select2 form-control select2-single input-max" name="experience1">
                                            <option value="-1">請選擇</option>
                                    <?php
                                        foreach($experience_list as $row):
                                         if(isset($data->experience1) && $data->experience1==$row->code)
                                             echo '<option value="'.$row->code.'" selected>'.$row->code.'  '.$row->name.'</option>';
                                         else
                                            echo '<option value="'.$row->code.'">'.$row->code.'  '.$row->name.'</option>';
                                        endforeach;
                                    ?>

                                </select>
                                <select class="select2 form-control select2-single input-max" name="experience2">
                                            <option value="-1">請選擇</option>
                                    <?php
                                        foreach($experience_list as $row):
                                         if(isset($data->experience2) && $data->experience2==$row->code)
                                             echo '<option value="'.$row->code.'" selected>'.$row->code.'  '.$row->name.'</option>';
                                         else
                                            echo '<option value="'.$row->code.'">'.$row->code.'  '.$row->name.'</option>';
                                        endforeach;
                                    ?>

                                </select>
                                <select class="select2 form-control select2-single input-max" name="experience3">
                                            <option value="-1">請選擇</option>
                                    <?php
                                        foreach($experience_list as $row):
                                         if(isset($data->experience3) && $data->experience3==$row->code)
                                             echo '<option value="'.$row->code.'" selected>'.$row->code.'  '.$row->name.'</option>';
                                         else
                                            echo '<option value="'.$row->code.'">'.$row->code.'  '.$row->name.'</option>';
                                        endforeach;
                                    ?>

                                </select>
                                <select class="select2 form-control select2-single input-max" name="experience4">
                                            <option value="-1">請選擇</option>
                                    <?php
                                        foreach($experience_list as $row):
                                         if(isset($data->experience4) && $data->experience4==$row->code)
                                             echo '<option value="'.$row->code.'" selected>'.$row->code.'  '.$row->name.'</option>';
                                         else
                                            echo '<option value="'.$row->code.'">'.$row->code.'  '.$row->name.'</option>';
                                        endforeach;
                                    ?>

                                </select>
                                <select class="select2 form-control select2-single input-max" name="experience5">
                                            <option value="-1">請選擇</option>
                                    <?php
                                        foreach($experience_list as $row):
                                         if(isset($data->experience5) && $data->experience5==$row->code)
                                             echo '<option value="'.$row->code.'" selected>'.$row->code.'  '.$row->name.'</option>';
                                         else
                                            echo '<option value="'.$row->code.'">'.$row->code.'  '.$row->name.'</option>';
                                        endforeach;
                                    ?>

                                </select>
                                {{-- <textarea class="form-control input-max" rows="5" name="experience_area" id="experience_area" maxlength="255">{{ old('experience_area', (isset($data->experience_area))? $data->experience_area : '') }}</textarea> --}}
                            </div>
                        </div>

                        <!-- 重要經歷 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">重要經歷</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" name="experience" id="experience" maxlength="255">{{ old('experience', (isset($data->experience))? $data->experience : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 重要著作及得獎紀錄 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">重要著作及得獎紀錄</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" name="award" id="award" maxlength="255">{{ old('award', (isset($data->award))? $data->award : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 公部門授課經歷 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">公部門授課經歷</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" name="remark" id="remark" maxlength="255">{{ old('remark', (isset($data->remark))? $data->remark : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 可授課程(一) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(一)</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="major1" name="major1" placeholder="請輸入可授課程(一)" value="{{ old('major1', (isset($data->major1))? $data->major1 : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <!-- 可授課程(二) -->
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(二)</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="major2" name="major2" placeholder="請輸入可授課程(二)" value="{{ old('major2', (isset($data->major2))? $data->major2 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(三) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(三)</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="major3" name="major3" placeholder="請輸入可授課程(三)" value="{{ old('major3', (isset($data->major3))? $data->major3 : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(四)</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="major4" name="major4" placeholder="請輸入可授課程(四)" value="{{ old('major4', (isset($data->major4))? $data->major4 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(五) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(五)</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="major5" name="major5" placeholder="請輸入可授課程(五)" value="{{ old('major5', (isset($data->major5))? $data->major5 : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(六)</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="major6" name="major6" placeholder="請輸入可授課程(六)" value="{{ old('major6', (isset($data->major6))? $data->major6 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(七) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(七)</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="major7" name="major7" placeholder="請輸入可授課程(七)" value="{{ old('major7', (isset($data->major7))? $data->major7 : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(八)</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="major8" name="major8" placeholder="請輸入可授課程(八)" value="{{ old('major8', (isset($data->major8))? $data->major8 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(九) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(九)</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="major9" name="major9" placeholder="請輸入可授課程(九)" value="{{ old('major9', (isset($data->major9))? $data->major9 : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(十)</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="major10" name="major10" placeholder="請輸入可授課程(十)" value="{{ old('major10', (isset($data->major10))? $data->major10 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 轉帳帳戶 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">轉帳帳戶</label>
                            <div class="col-md-3">
                                <!-- <select id="transfor" name="transfor" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.lecture_transfor') as $key => $va)
                                        <option value="{{ $key }}" {{ old('transfor', (isset($data->transfor))? $data->transfor : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.lecture_transfor') as $key => $va)
                                    <input type="radio" id="transfor" name="transfor"  value="{{ $key }}" {{ old('transfor', (isset($data->transfor))? $data->transfor : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                            <!-- 入帳通知 -->
                            <label class="col-md-2 col-form-label text-md-right">入帳通知</label>
                            <div class="col-md-3">
                                <input type="radio" id="notify" name="notify" value="Y" {{ (isset($data))? '' : 'checked' }} {{ old('notify', (isset($data->notify))? $data->notify : 1) == 'Y'? 'checked' : '' }}>是
                                <input type="radio" id="notify" name="notify" value="N" {{ old('notify', (isset($data->notify))? $data->notify : 1) == 'N'? 'checked' : '' }}>否
                            </div>
                        </div>


                        <!-- 郵局 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">郵局</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="post" name="post" placeholder="請輸入郵局" value="{{ old('post', (isset($data->post))? $data->post : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 局號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">局號</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="postcode" name="postcode" placeholder="請輸入局號" value="{{ old('postcode', (isset($data->postcode))? $data->postcode : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 郵局帳號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">郵局帳號</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" onchange="checkPostno();" id="postno" name="postno" placeholder="請輸入郵局帳號" value="{{ old('postno', (isset($data->postno))? $data->postno : '') }}" autocomplete="off" maxlength="255" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">銀行</label>
                            <div class="col-sm-3">
                               <input type="text" class="form-control input-max" id="bank" name="bank" value="{{ old('bank', (isset($data->bank))? $data->bank : '') }}" autocomplete="off" maxlength="255">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal1"><i class="fa fa-plus fa-lg pr-2"></i>查詢銀行代碼</button>
						   </div>
                           <label class="col-md-2 col-form-label text-md-right">銀行代碼</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="bankcode" name="bankcode" value="{{ old('bankcode', (isset($data->bankcode))? $data->bankcode : '') }}" autocomplete="off" maxlength="255">
                           </div>
                        </div>

                        <!-- 存摺帳號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">存摺帳號</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" onchange="checkBankno();" id="bankno" name="bankno" placeholder="請輸入存摺帳號" value="{{ old('bankno', (isset($data->bankno))? $data->bankno : '') }}" autocomplete="off" maxlength="255" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                            <!-- 戶名 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">戶名</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="bankaccname" name="bankaccname" placeholder="請輸入戶名" value="{{ old('bankaccname', (isset($data->bankaccname))? $data->bankaccname : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitform();" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/lecture">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                        <?php if(isset($data->serno)){?>
                        <span onclick="$('#del_form').attr('action', '/admin/lecture/{{ $data->serno }}/from');" data-toggle="modal" data-target="#del_modol" >
                            <button type="button" class="btn btn-sm btn-danger"> 刪除</button>
                        </span>
                        <?php }?>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- Modal1 批次新增 -->
    <div class="modal fade" id="exampleModal1" name="exampleModal1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">查詢銀行代碼</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body pt-4 text-center">
                        <label for="bankname">銀行代碼：</label>
                        <select id="bankname" name="bankname" class="form-control select2" placeholder="請選擇銀行">
                                <option value="-1">請選擇</option>
                            @foreach($list as $key => $va)
                                <option value="{{ $va->銀行代碼 }}" {{ old('bankcode', (isset($data->bankcode))? $data->bankcode==$va->銀行代碼? 'selected' : '' : '') }}> {{ $va->銀行名稱 }}</option>
                            @endforeach
                        </select>
                        <br/>
                    </div>
                </div>
                <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onClick="confirmBank()">確定</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')

@endsection

@section('js')
<script>
  $(document).ready(function () {
	   function readURL(input) {
              if (input.files && input.files[0]) {
                  $('#Certificate').val(input.files[0].name);
				  }
            }

        $("#upload").change(function() {
          readURL(this);
        });
   });
  function submitform(){
  		old_idno = "";
  		<?php if(isset($data->idno)){?>
  		old_idno = '<?=$data->idno;?>';
  		<?php } ?>

        if( $('input[name=transfor]:checked').val() == '1' && $("*[name='postcode']").val().length!=7 && $("*[name='postcode']").val().length!=0){
            alert("請確認局號，局號限制7碼");
            return;
        }
        if($("*[name='idkind']").val() == '3' || $("*[name='idkind']").val() == '4' || $("*[name='idkind']").val() == '7'){
            if($("*[name='ename']").val() == ''){
                alert("國內無地址之外僑、國內有地址之外僑、非居住者，英文姓名為必填");
            return;
            }
        }
        if($("*[name='idno']").val() != old_idno){
            checkRepeat();
        }else{
        	submitForm('#form');
        }
   }
   function checkID( id ) {
    tab = "ABCDEFGHJKLMNPQRSTUVXYWZIO"
    A1 = new Array (1,1,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,3,3,3,3,3,3 );
    A2 = new Array (0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5 );
    Mx = new Array (9,8,7,6,5,4,3,2,1,1);

    if ( id.length != 10 ) return false;
    i = tab.indexOf( id.charAt(0) );
    if ( i == -1 ) return false;
    sum = A1[i] + A2[i]*9;

    for ( i=1; i<10; i++ ) {
        v = parseInt( id.charAt(i) );
        if ( isNaN(v) ) return false;
        sum = sum + v * Mx[i];
    }
    if ( sum % 10 != 0 ) return false;
    return true;
    }
    function confirmBank(){

        $selecttext=$("*[name='bankname']").find(':selected').text();
        //alert($selecttext);
        $("*[name='bankcode']").val($("*[name='bankname']").val());
        $("*[name='bank']").val($selecttext);
    }
    function editIdno(){
    	$("#idno").removeAttr("readOnly");
    	$("#cname").removeAttr("readOnly");
    }
    function checkPostno(){
        var postno_len = "N";
        var postno1 = $("*[name='postno']").val().length;
        var postno2 = $("*[name='postno']").val().length;
        if(postno1==12){
            postno_len = "Y";
        }
        if(postno2==14){
            postno_len = "Y";
        }
        if(postno_len == "N"){
            alert("郵局帳號長度不符，長度正確應為12或14碼！(此訊息僅為提示作用，請再確認帳號長度，如沒問題，請直接儲存即可。)");
            return;
        }
    }
    function checkBankno(){
        var bankno_len = "N";
        var bankno1 = $("*[name='bankno']").val().length;
        var bankno2 = $("*[name='bankno']").val().length;
        if(bankno1==12){
            bankno_len = "Y";
        }
        if(bankno2==14){
            bankno_len = "Y";
        }
        if(bankno_len == "N"){
            alert("存摺帳號長度不符，長度正確應為12或14碼！(此訊息僅為提示作用，請再確認帳號長度，如沒問題，請直接儲存即可。)");
            return;
        }
    }

    function checkidno(){
        let idno = $('#idno').val();

        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "json",
            url: '/admin/lecture/checkidno',
            data: { idno: idno},
            success: function(data){

                if (data.status) {
                    if(data.sex == '1'){
                        $('#sexM').prop("checked", true);
                    }else{
                        $('#sexF').prop("checked", true);
                    }

                } else {
                    if(data.repeat != ''){
                        if (confirm(data.repeat)==true){
                            window.location.replace(data.url);
                        }
                    }else{
                        alert('此身分證字號錯誤，請確認');
                    }

                }

            },
            error: function() {
                alert('操作錯誤');
            }
        });

    }

    function checkRepeat(){
        let idno = $('#idno').val();

        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "json",
            url: '/admin/lecture/checkidno',
            data: { idno: idno},
            success: function(data){

                if (data.status) {
                    submitForm('#form');
                }

                if(data.repeat != ''){
                    if (confirm(data.repeat+' 重覆則不允許儲存')==true){
                        window.location.replace(data.url);
                    }
                }

            },
            error: function() {
                alert('操作錯誤');
            }
        });

    }

</script>
@include('admin/layouts/list/del_modol')
@endsection

