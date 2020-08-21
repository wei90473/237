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


                        <!-- 身分證字號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">身分證字號<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="idno" name="idno" placeholder="請輸入身分證字號" value="{{ old('idno', (isset($data->idno))? $data->idno : '') }}" autocomplete="off" maxlength="255" required>
                            </div>
                        </div>

                        <!-- 證號別 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">證號別</label>
                            <div class="col-md-10">
                                <select id="sex" name="idkind" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.idkind') as $key => $va)
                                        <option value="{{ $key }}" {{ old('idkind', (isset($data->idkind))? $data->idkind : 0) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 姓氏 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">姓氏<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="fname" name="fname" placeholder="請輸入姓氏" value="{{ old('fname', (isset($data->fname))? $data->fname : '') }}" autocomplete="off" maxlength="255" required>
                            </div>
                        </div>

                        <!-- 名字 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">名字<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="lname" name="lname" placeholder="請輸入名字" value="{{ old('lname', (isset($data->cname))? str_replace($data->fname, '', $data->cname) : '') }}" autocomplete="off" maxlength="255" required>
                            </div>
                        </div>

                        <!-- 英文姓名 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">英文姓名</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="ename" name="ename" placeholder="請輸入英文姓名" value="{{ old('ename', (isset($data->ename))? $data->ename : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>


                        <!-- 性別 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">性別</label>
                            <div class="col-md-10">
                                <!-- <select id="sex" name="sex" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.sex') as $key => $va)
                                        <option value="{{ $key }}" {{ old('sex', (isset($data->sex))? $data->sex : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.sex') as $key => $va)
                                    <input type="radio" id="sex" name="sex" value="{{ $key }}" {{ old('sex', (isset($data->sex))? $data->sex : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>

                        <!-- 出生日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">出生日期</label>
                            <div class="col-sm-10">

                                <div class="input-group roc-date input-max">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">民國</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-year" maxlength="3" name="birth[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($data->birth))? mb_substr($data->birth, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">年</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-month" maxlength="2" name="birth[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($data->birth))? mb_substr($data->birth, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">月</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-day" maxlength="2" name="birth[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($data->birth))? mb_substr($data->birth, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">日</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- 國籍 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">國籍</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="citizen" name="citizen" placeholder="請輸入國籍" value="{{ old('citizen', (isset($data->citizen))? $data->citizen : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 護照號碼 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">護照號碼</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="passport" name="passport" placeholder="請輸入護照號碼" value="{{ old('passport', (isset($data->passport))? $data->passport : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 第二類被保險人 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">第二類被保險人</label>
                            <div class="col-md-10">
                                <!-- <select id="insurekind1" name="insurekind1" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.yorn') as $key => $va)
                                        <option value="{{ $key }}" {{ old('insurekind1', (isset($data->insurekind1))? $data->insurekind1 : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.yorn') as $key => $va)
                                    <input type="radio" id="insurekind1" name="insurekind1" value="{{ $key }}" {{ old('insurekind1', (isset($data->insurekind1))? $data->insurekind1 : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>

						<!-- 更新日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">更新日期</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="update_date" name="update_date" value="{{ old('update_date', (isset($data->update_date))? $data->update_date : '') }}" >
                            </div>
                        </div>
						<!-- 上傳個資授權書 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">上傳個資授權書</label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control input-max" id="Certificate" name="Certificate" readonly="readonly" value="{{ old('Certificate', (isset($data->Certificate))? $data->Certificate : '') }}" >
                              <button type="button" OnClick='javascript:$("#upload").click();'class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>選取檔案</button>			
							  <input type="file" class="btn btn-sm btn-info" id="upload" name="upload" style="display:none;" />
							</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座資料維護表單</h3></div>
                    <div class="card-body pt-4">



                        <!-- 服務機關 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">服務機關</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="dept" name="dept" placeholder="請輸入服務機關" value="{{ old('dept', (isset($data->dept))? $data->dept : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 現職 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">現職</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="position" name="position" placeholder="請輸入現職" value="{{ old('position', (isset($data->position))? $data->position : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 分類 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">分類</label>
                            <div class="col-md-10">
                                <select id="kind" name="kind" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.lecture_kind') as $key => $va)
                                        <option value="{{ $key }}" {{ old('kind', (isset($data->kind))? $data->kind : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座資料維護表單</h3></div>
                    <div class="card-body pt-4">

                        <!-- 機關地址 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">郵遞區號索引&查詢
                            </label>
                                <div class="col-sm-4">
                                    <select class="select2 form-control select2-single input-max" name="" id="">

                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="請輸入名稱或代碼進行查詢" class="form-control">
                                    <input type="radio" name="search" value="postName">名稱
                                    <input type="radio" name="search" value="postCode">代碼        
                                    <button>查詢</button>
                                    <label>查詢結果：</label>
                                </div>





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

                                    <input type="text" class="form-control" maxlength="10" autocomplete="off" id="offaddress" name="offaddress"  value="{{ old('offaddress', (isset($data->offaddress))? $data->offaddress : '') }}">
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

                                    <input type="text" class="form-control" maxlength="10" autocomplete="off" id="homaddress" name="homaddress"  value="{{ old('homaddress', (isset($data->homaddress))? $data->homaddress : '') }}">
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

                                    <input type="text" class="form-control" maxlength="10" autocomplete="off" id="regaddress" name="regaddress"  value="{{ old('regaddress', (isset($data->regaddress))? $data->regaddress : '') }}">
                                </div>

                            </div>
                        </div>

                        <!-- 郵寄地址 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">郵寄地址</label>
                            <div class="col-md-10">
                                <!-- <select id="send" name="send" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.lecture_send') as $key => $va)
                                        <option value="{{ $key }}" {{ old('send', (isset($data->send))? $data->send : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.lecture_send') as $key => $va)
                                <input type="radio" id="send" name="send" value="{{ $key }}" {{ old('send', (isset($data->send))? $data->send : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>



                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座資料維護表單</h3></div>
                    <div class="card-body pt-4">



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
                            <div class="col-sm-10">

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
                        </div>

                        <!-- 行動電話 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="mobiltel" name="mobiltel" placeholder="請輸入行動電話" value="{{ old('mobiltel', (isset($data->mobiltel))? $data->mobiltel : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 傳真(公) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">傳真(公)</label>
                            <div class="col-sm-10">

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
                        </div>

                        <!-- 傳真(公) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">傳真(公)</label>
                            <div class="col-sm-10">

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
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="email" name="email" placeholder="請輸入Email" value="{{ old('email', (isset($data->email))? $data->email : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 聯絡人 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡人</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="liaison" name="liaison" placeholder="請輸入聯絡人" value="{{ old('liaison', (isset($data->liaison))? $data->liaison : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座資料維護表單</h3></div>
                    <div class="card-body pt-4">

                        <!-- 最高學歷 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">最高學歷</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="education" name="education" placeholder="請輸入最高學歷" value="{{ old('education', (isset($data->education))? $data->education : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        
                        
                        
                        <
						<!-- 專長領域 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">專長領域</label>
                            <div class="col-md-10">
                                <?php 
                                $str = DB::table('s01tb');
                                $str = $str->select('s01tb.code')->distinct()->where('type','=','B')->get();
                
                                ?>

                                <select class="select2 form-control select2-single input-max" name="experience_area"
                                 >
                                    @foreach ($str as $id=>$json)
                                        @php
                                        $obj = (array)$json
                                        @endphp
                                        @foreach ($obj as $key=>$val)
                                            <option class="seletc2" value={{$val}}>{{$val}}</option>
                                        @endforeach
                                    @endforeach
                                
                                </select>
                                <select class="select2 form-control select2-single input-max" name="experience_area"
                                 >
                                    @foreach ($str as $id=>$json)
                                        @php
                                        $obj = (array)$json
                                        @endphp
                                        @foreach ($obj as $key=>$val)
                                            <option class="seletc2" value={{$val}}>{{$val}}</option>
                                        @endforeach
                                    @endforeach
                                
                                </select>
                                <select class="select2 form-control select2-single input-max" name="experience_area"
                                 >
                                    @foreach ($str as $id=>$json)
                                        @php
                                        $obj = (array)$json
                                        @endphp
                                        @foreach ($obj as $key=>$val)
                                            <option class="seletc2" value={{$val}}>{{$val}}</option>
                                        @endforeach
                                    @endforeach
                                
                                </select>
                                <select class="select2 form-control select2-single input-max" name="experience_area"
                                 >
                                    @foreach ($str as $id=>$json)
                                        @php
                                        $obj = (array)$json
                                        @endphp
                                        @foreach ($obj as $key=>$val)
                                            <option class="seletc2" value={{$val}}>{{$val}}</option>
                                        @endforeach
                                    @endforeach
                                
                                </select>
                                <select class="select2 form-control select2-single input-max" name="experience_area"
                                 >
                                    @foreach ($str as $id=>$json)
                                        @php
                                        $obj = (array)$json
                                        @endphp
                                        @foreach ($obj as $key=>$val)
                                            <option class="seletc2" value={{$val}}>{{$val}}</option>
                                        @endforeach
                                    @endforeach
                                
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


                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座資料維護表單</h3></div>
                    <div class="card-body pt-4">


                        <!-- 可授課程(一) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(一)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major1" name="major1" placeholder="請輸入可授課程(一)" value="{{ old('major1', (isset($data->major1))? $data->major1 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(二) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(二)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major2" name="major2" placeholder="請輸入可授課程(二)" value="{{ old('major2', (isset($data->major2))? $data->major2 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(三) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(三)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major3" name="major3" placeholder="請輸入可授課程(三)" value="{{ old('major3', (isset($data->major3))? $data->major3 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(四) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(四)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major4" name="major4" placeholder="請輸入可授課程(四)" value="{{ old('major4', (isset($data->major4))? $data->major4 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(五) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(五)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major5" name="major5" placeholder="請輸入可授課程(五)" value="{{ old('major5', (isset($data->major5))? $data->major5 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(六) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(六)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major6" name="major6" placeholder="請輸入可授課程(六)" value="{{ old('major6', (isset($data->major6))? $data->major6 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(七) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(七)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major7" name="major7" placeholder="請輸入可授課程(七)" value="{{ old('major7', (isset($data->major7))? $data->major7 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(八) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(八)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major8" name="major8" placeholder="請輸入可授課程(八)" value="{{ old('major8', (isset($data->major8))? $data->major8 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(九) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(九)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major9" name="major9" placeholder="請輸入可授課程(九)" value="{{ old('major9', (isset($data->major9))? $data->major9 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(十) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(十)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major10" name="major10" placeholder="請輸入可授課程(十)" value="{{ old('major10', (isset($data->major10))? $data->major10 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>





                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座資料維護表單</h3></div>
                    <div class="card-body pt-4">


                        <!-- 郵局 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">郵局</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="post" name="post" placeholder="請輸入郵局" value="{{ old('post', (isset($data->post))? $data->post : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 局號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">局號</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="postcode" name="postcode" placeholder="請輸入局號" value="{{ old('postcode', (isset($data->postcode))? $data->postcode : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 郵局帳號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">郵局帳號</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="postno" name="postno" placeholder="請輸入郵局帳號" value="{{ old('postno', (isset($data->postno))? $data->postno : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>



                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座資料維護表單</h3></div>
                    <div class="card-body pt-4">

                        <!-- 銀行代碼 -->
						<?php 
							$list = array(
							'001' => '中央信託', 
							'003' => '交通銀行', 
							'005' => '台灣銀行', 
							'006' => '土地銀行', 
							'007' => '合庫商銀', 
							'008' => '第一銀行', 
							'bank1' => '華南銀行', 
							'bank2' => '彰化銀行', 
							'bank3' => '華僑銀行', 
							'bank4' => '上海銀行', 
							'bank5' => '台北富邦', 
							'bank6' => '國泰世華', 
							'bank7' => '高雄銀行', 
							'bank8' => '兆豐商銀', 
							'bank9' => '農業金庫', 
							'bank10' => '花旗銀行', 
							'bank11' => '運通銀行', 
							'bank12' => '首都銀行', 
							'bank13' => '荷蘭銀行', 
							'bank14' => '中華開發', 
							'bank15' => '臺灣企銀', 
							'bank16' => '台北商銀', 
							'bank17' => '新竹商銀', 
							'bank18' => '台中商銀', 
							'bank19' => '京城商銀', 
							'bank20' => '花蓮企銀', 
							'bank21' => '台東企銀', 
							'bank22' => '東亞銀行', 
							'bank23' => '匯豐銀行', 
							'bank24' => '渣打銀行', 
							'bank25' => '標旗銀行', 
							'bank26' => '台北一信', 
							'bank27' => '華泰銀行', 
							'bank28' => '臺灣新光商銀', 
							'bank29' => '台北五信', 
							'bank30' => '台北九信', 
							'bank31' => '陽信銀行', 
							'bank32' => '基隆一信', 
							'bank33' => '基隆二信', 
							'bank34' => '板信銀行', 
							'bank35' => '淡水一信', 
							'bank36' => '淡水信合社', 
							'bank37' => '宜蘭信合社', 
							'bank38' => '桃園信合社', 
							'bank39' => '新竹一信', 
							'bank40' => '新竹三信', 
							'bank41' => '竹南信合社', 
							'bank42' => '台中二信', 
							'bank43' => '三信銀行', 
							'bank44' => '第七商銀', 
							'bank45' => '彰化一信', 
							'bank46' => '彰化五信', 
							'bank47' => '彰化六信', 
							'bank48' => '彰化十信', 
							'bank48' => '鹿港信合社', 
							'bank50' => '嘉義三信', 
							'bank51' => '嘉義四信', 
							'bank52' => '台南三信', 
							'bank54' => '高雄二信', 
							'bank54' => '高雄三信', 
							'bank55' => '花蓮一信', 
							'bank56' => '花蓮二信', 
							'bank57' => '澎湖一信', 							
							'bank58' => '高雄市農會', 		
							'bank60' => '斗六農會', 
							'bank61' => '台西農會', 
							'bank62' => '大埤農會', 												
							'bank63' => '中華郵政', 
							'bank64' => '聯邦銀行', 
							'bank65' => '中華銀行', 
							'bank66' => '遠東銀行', 
							'bank67' => '復華銀行', 
							'bank68' => '建華銀行', 
							'bank69' => '玉山銀行', 
							'bank70' => '萬泰銀行', 
							'bank71' => '寶華銀行', 
							'bank72' => '台新銀行', 
							'bank73' => '大眾銀行', 
							'bank74' => '日盛銀行', 
							'bank75' => '安泰銀行', 
							'bank76' => '中國信託', 
							'bank77' => '慶豐銀行');



						?>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">銀行代碼</label>
                            <div class="col-sm-10">
                               <select id="bank" name="bank" class="" placeholder="請選擇銀行代碼" >
                                    @foreach($list as $key => $va)
                                        <option value="{{ $va }}" >{{ $va }}</option>
                                    @endforeach	
								</select>
						   </div>
                        </div>

                        <!-- 存摺帳號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">存摺帳號</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="bankno" name="bankno" placeholder="請輸入存摺帳號" value="{{ old('bankno', (isset($data->bankno))? $data->bankno : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 戶名 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">戶名</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="bankaccname" name="bankaccname" placeholder="請輸入戶名" value="{{ old('bankaccname', (isset($data->bankaccname))? $data->bankaccname : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>



                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座資料維護表單</h3></div>
                    <div class="card-body pt-4">

                        <!-- 智庫專家 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">智庫專家</label>
                            <div class="col-md-10">
                                <!-- <select id="expert" name="expert" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.yorn') as $key => $va)
                                        <option value="{{ $key }}" {{ old('expert', (isset($data->expert))? $data->expert : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.yorn') as $key => $va)
                                    <input type="radio" id="expert" name="expert" value="{{ $key }}" {{ old('expert', (isset($data->expert))? $data->expert : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>


                        <!-- 人事總處 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">人事總處</label>
                            <div class="col-md-10">
                                <!-- <select id="publicly" name="publicly" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.yorn') as $key => $va)
                                        <option value="{{ $key }}" {{ old('publicly', (isset($data->publicly))? $data->publicly : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.yorn') as $key => $va)
                                    <input type="radio" iid="publicly" name="publicly" value="{{ $key }}" {{ old('publicly', (isset($data->publicly))? $data->publicly : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>


                        <!-- 公務機關 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">公務機關</label>
                            <div class="col-md-10">
                                <!-- <select id="publish" name="publish" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.yorn') as $key => $va)
                                        <option value="{{ $key }}" {{ old('publish', (isset($data->publish))? $data->publish : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.yorn') as $key => $va)
                                        <input type="radio" id="publish" name="publish" value="{{ $key }}" {{ old('publish', (isset($data->publish))? $data->publish : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>


                        <!-- 轉帳帳戶 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">轉帳帳戶</label>
                            <div class="col-md-10">
                                <!-- <select id="transfor" name="transfor" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.lecture_transfor') as $key => $va)
                                        <option value="{{ $key }}" {{ old('transfor', (isset($data->transfor))? $data->transfor : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.lecture_transfor') as $key => $va)
                                    <input type="radio" id="transfor" name="transfor"  value="{{ $key }}" {{ old('transfor', (isset($data->transfor))? $data->transfor : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>

                        <!-- 入帳通知 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">入帳通知</label>
                            <div class="col-md-10">
                                <!-- <select id="notify" name="notify" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.yorn') as $key => $va)
                                        <option value="{{ $key }}" {{ old('notify', (isset($data->notify))? $data->notify : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.yorn') as $key => $va)
                                    <input type="radio" id="notify" name="notify" value="{{ $key }}" {{ old('notify', (isset($data->notify))? $data->notify : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>						
                        <a href="/admin/lecture">
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
</script>
@endsection