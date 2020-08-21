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
                                <select id="sex" name="idkind" class="select2 form-control select2-single input-max" disabled="disabled">
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
                                <input type="text" class="form-control input-max" id="ename" name="ename" placeholder="請輸入英文姓名" value="{{ old('ename', (isset($data->ename))? $data->ename : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
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
                                    <input type="radio" id="sex" name="sex" value="{{ $key }}" {{ old('sex', (isset($data->sex))? $data->sex : 1) == $key? 'checked' : '' }} disabled="disabled">{{ $va }}
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

                                    <input type="text" class="form-control roc-date-year" maxlength="3" name="birth[year]" placeholder="請輸入年份" disabled="disabled" autocomplete="off" value="{{ (isset($data->birth))? mb_substr($data->birth, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">年</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-month" maxlength="2" name="birth[month]" placeholder="請輸入月份" disabled="disabled" autocomplete="off" value="{{ (isset($data->birth))? mb_substr($data->birth, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">月</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-day" maxlength="2" name="birth[day]" placeholder="請輸入日期" disabled="disabled" autocomplete="off" value="{{ (isset($data->birth))? mb_substr($data->birth, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

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
                                <input type="text" class="form-control input-max" id="citizen" name="citizen" placeholder="請輸入國籍" value="{{ old('citizen', (isset($data->citizen))? $data->citizen : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 護照號碼 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">護照號碼</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="passport" name="passport" placeholder="請輸入護照號碼" value="{{ old('passport', (isset($data->passport))? $data->passport : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
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
                                    <input type="radio" id="insurekind1" name="insurekind1" value="{{ $key }}" {{ old('insurekind1', (isset($data->insurekind1))? $data->insurekind1 : 1) == $key? 'checked' : '' }} disabled="disabled">{{ $va }}
                                @endforeach
                            </div>
                        </div>

						<!-- 更新日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">更新日期</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="update_date" name="update_date" value="{{ old('update_date', (isset($data->update_date))? $data->update_date : '') }} " disabled="disabled">
                            </div>
                        </div>
						<!-- 上傳個資授權書 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">上傳個資授權書</label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control input-max" id="Certificate" name="Certificate" readonly="readonly" value="{{ old('Certificate', (isset($data->Certificate))? $data->Certificate : '') }}" disabled="disabled">
                              <button type="button" OnClick='javascript:$("#upload").click();'class="btn btn-sm btn-info" disabled="disabled"><i class="fa fa-save pr-2"></i>選取檔案</button>			
							  <input type="file" class="btn btn-sm btn-info" id="upload" name="upload" style="display:none;" disabled="disabled"/>
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
                                <input type="text" class="form-control input-max" id="dept" name="dept" placeholder="請輸入服務機關" value="{{ old('dept', (isset($data->dept))? $data->dept : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 現職 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">現職</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="position" name="position" placeholder="請輸入現職" value="{{ old('position', (isset($data->position))? $data->position : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 分類 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">分類</label>
                            <div class="col-md-10">
                                <select id="kind" name="kind" class="select2 form-control select2-single input-max" disabled="disabled">
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
                            <!-- <label class="col-sm-2 control-label text-md-right pt-2">郵遞區號索引&查詢
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
                                </div> -->





                            <label class="col-sm-2 control-label text-md-right pt-2">機關地址</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">郵遞區號</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="offzip" name="offzip"  value="{{ old('offzip', (isset($data->offzip))? $data->offzip : '') }}" disabled="disabled">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">地址</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="10" autocomplete="off" id="offaddress" name="offaddress"  value="{{ old('offaddress', (isset($data->offaddress))? $data->offaddress : '') }}" disabled="disabled">
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

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="homzip" name="homzip"  value="{{ old('homzip', (isset($data->homzip))? $data->homzip : '') }}" disabled="disabled">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">地址</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="10" autocomplete="off" id="homaddress" name="homaddress"  value="{{ old('homaddress', (isset($data->homaddress))? $data->homaddress : '') }}" disabled="disabled">
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

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="regzip" name="regzip"  value="{{ old('regzip', (isset($data->regzip))? $data->regzip : '') }}" disabled="disabled">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">地址</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="10" autocomplete="off" id="regaddress" name="regaddress"  value="{{ old('regaddress', (isset($data->regaddress))? $data->regaddress : '') }}" disabled="disabled">
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
                                <input type="radio" id="send" name="send" value="{{ $key }}" {{ old('send', (isset($data->send))? $data->send : 1) == $key? 'checked' : '' }} disabled="disabled">{{ $va }}
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

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="offtela1" name="offtela1"  value="{{ old('offtela1', (isset($data->offtela1))? $data->offtela1 : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="offtelb1" name="offtelb1"  value="{{ old('offtelb1', (isset($data->offtelb1))? $data->offtelb1 : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">分機</span>
                                    </div>

                                    <input type="text" style="max-width:100px;" class="form-control" maxlength="8" autocomplete="off" id="offtelc1" name="offtelc1"  value="{{ old('offtelc1', (isset($data->offtelc1))? $data->offtelc1 : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
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

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="offtela2" name="offtela2"  value="{{ old('offtela2', (isset($data->offtela2))? $data->offtela2 : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="offtelb2" name="offtelb2"  value="{{ old('offtelb2', (isset($data->offtelb2))? $data->offtelb2 : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">分機</span>
                                    </div>

                                    <input type="text" style="max-width:100px;" class="form-control" maxlength="8" autocomplete="off" id="offtelc2" name="offtelc2"  value="{{ old('offtelc2', (isset($data->offtelc2))? $data->offtelc2 : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
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

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="homtela" name="homtela"  value="{{ old('homtela', (isset($data->homtela))? $data->homtela : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="homtelb" name="homtelb"  value="{{ old('homtelb', (isset($data->homtelb))? $data->homtelb : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">


                                </div>

                            </div>
                        </div>

                        <!-- 行動電話 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="mobiltel" name="mobiltel" placeholder="請輸入行動電話" value="{{ old('mobiltel', (isset($data->mobiltel))? $data->mobiltel : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
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

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="offfaxa" name="offfaxa"  value="{{ old('offfaxa', (isset($data->offfaxa))? $data->offfaxa : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="offfaxb" name="offfaxb"  value="{{ old('offfaxb', (isset($data->offfaxb))? $data->offfaxb : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">


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

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="homfaxa" name="homfaxa"  value="{{ old('homfaxa', (isset($data->homfaxa))? $data->homfaxa : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>
                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="homfaxb" name="homfaxb"  value="{{ old('homfaxb', (isset($data->homfaxb))? $data->homfaxb : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                </div>

                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="email" name="email" placeholder="請輸入Email" value="{{ old('email', (isset($data->email))? $data->email : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 聯絡人 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡人</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="liaison" name="liaison" placeholder="請輸入聯絡人" value="{{ old('liaison', (isset($data->liaison))? $data->liaison : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
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
                                <input type="text" class="form-control input-max" id="education" name="education" placeholder="請輸入最高學歷" value="{{ old('education', (isset($data->education))? $data->education : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

						<!-- 專長領域 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">專長領域</label>
                            <div class="col-md-10">
                                <?php 
                                $str = DB::table('s01tb');
                                $str = $str->select('s01tb.code')->distinct()->where('type','=','B')->get();
                                ?>

                                <select class="select2 form-control select2-single input-max" name="experience_area" disabled="disabled">
                                            <option value="-1">請選擇</option>
                                    @foreach ($str as $id=>$json)
                                        @php
                                        $obj = (array)$json
                                        @endphp
                                        @foreach ($obj as $key=>$val)
                                            <option class="seletc2" value={{$val}}>{{$val}}</option>
                                        @endforeach
                                    @endforeach
                                
                                </select>
                                <select class="select2 form-control select2-single input-max" name="experience_area" disabled="disabled">
                                            <option value="-1">請選擇</option>
                                    @foreach ($str as $id=>$json)
                                        @php
                                        $obj = (array)$json
                                        @endphp
                                        @foreach ($obj as $key=>$val)
                                            <option class="seletc2" value={{$val}}>{{$val}}</option>
                                        @endforeach
                                    @endforeach
                                
                                </select>
                                <select class="select2 form-control select2-single input-max" name="experience_area" disabled="disabled">
                                            <option value="-1">請選擇</option>
                                    @foreach ($str as $id=>$json)
                                        @php
                                        $obj = (array)$json
                                        @endphp
                                        @foreach ($obj as $key=>$val)
                                            <option class="seletc2" value={{$val}}>{{$val}}</option>
                                        @endforeach
                                    @endforeach
                                
                                </select>
                                <select class="select2 form-control select2-single input-max" name="experience_area" disabled="disabled">
                                            <option value="-1">請選擇</option>
                                    @foreach ($str as $id=>$json)
                                        @php
                                        $obj = (array)$json
                                        @endphp
                                        @foreach ($obj as $key=>$val)
                                            <option class="seletc2" value={{$val}}>{{$val}}</option>
                                        @endforeach
                                    @endforeach
                                
                                </select>
                                <select class="select2 form-control select2-single input-max" name="experience_area" disabled="disabled">
                                            <option value="-1">請選擇</option>
                                    @foreach ($str as $id=>$json)
                                        @php
                                        $obj = (array)$json
                                        @endphp
                                        @foreach ($obj as $key=>$val)
                                            <option class="seletc2" value={{$val}}>{{$val}}</option>
                                        @endforeach
                                    @endforeach
                                
                                </select>
                                {{-- <textarea class="form-control input-max" rows="5" name="experience_area" id="experience_area" maxlength="255" disabled="disabled">{{ old('experience_area', (isset($data->experience_area))? $data->experience_area : '') }}</textarea> --}}
                            </div>
                        </div>

                        <!-- 重要經歷 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">重要經歷</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" name="experience" id="experience" maxlength="255" disabled="disabled">{{ old('experience', (isset($data->experience))? $data->experience : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 重要著作及得獎紀錄 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">重要著作及得獎紀錄</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" name="award" id="award" maxlength="255" disabled="disabled">{{ old('award', (isset($data->award))? $data->award : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 公部門授課經歷 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">公部門授課經歷</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" name="remark" id="remark" maxlength="255" disabled="disabled">{{ old('remark', (isset($data->remark))? $data->remark : '') }}</textarea>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">可授課程</h3></div>
                    <div class="card-body pt-4">


                        <!-- 可授課程(一) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(一)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major1" name="major1" placeholder="請輸入可授課程(一)" value="{{ old('major1', (isset($data->major1))? $data->major1 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(二) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(二)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major2" name="major2" placeholder="請輸入可授課程(二)" value="{{ old('major2', (isset($data->major2))? $data->major2 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(三) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(三)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major3" name="major3" placeholder="請輸入可授課程(三)" value="{{ old('major3', (isset($data->major3))? $data->major3 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(四) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(四)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major4" name="major4" placeholder="請輸入可授課程(四)" value="{{ old('major4', (isset($data->major4))? $data->major4 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(五) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(五)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major5" name="major5" placeholder="請輸入可授課程(五)" value="{{ old('major5', (isset($data->major5))? $data->major5 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(六) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(六)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major6" name="major6" placeholder="請輸入可授課程(六)" value="{{ old('major6', (isset($data->major6))? $data->major6 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(七) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(七)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major7" name="major7" placeholder="請輸入可授課程(七)" value="{{ old('major7', (isset($data->major7))? $data->major7 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(八) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(八)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major8" name="major8" placeholder="請輸入可授課程(八)" value="{{ old('major8', (isset($data->major8))? $data->major8 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(九) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(九)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major9" name="major9" placeholder="請輸入可授課程(九)" value="{{ old('major9', (isset($data->major9))? $data->major9 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 可授課程(十) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">可授課程(十)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major10" name="major10" placeholder="請輸入可授課程(十)" value="{{ old('major10', (isset($data->major10))? $data->major10 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>





                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">郵局</h3></div>
                    <div class="card-body pt-4">


                        <!-- 郵局 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">郵局</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="post" name="post" placeholder="請輸入郵局" value="{{ old('post', (isset($data->post))? $data->post : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 局號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">局號</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="postcode" name="postcode" placeholder="請輸入局號" value="{{ old('postcode', (isset($data->postcode))? $data->postcode : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 郵局帳號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">郵局帳號</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="postno" name="postno" placeholder="請輸入郵局帳號" value="{{ old('postno', (isset($data->postno))? $data->postno : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>



                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">金融機構</h3></div>
                    <div class="card-body pt-4">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">銀行</label>
                            <div class="col-sm-10">
                                <div class="input-group roc-date input-max">
                                    <select id="bankcode" name="bankcode" class="form-control select2" placeholder="請選擇銀行">
                                            <option value="-1">請選擇</option>
                                        @foreach($list as $key => $va)
                                            <option value="{{ $va->銀行代碼 }}" {{ old('bankcode', (isset($data->bankcode))? $data->bankcode==$va->銀行代碼? 'selected' : '' : '') }}>{{ $va->銀行名稱 }}</option>
                                        @endforeach	
                                    </select>
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal1"><i class="fa fa-plus fa-lg pr-2"></i>查詢銀行代碼</button>
                                </div>
						   </div>
                        </div>

                        <!-- 存摺帳號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">存摺帳號</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="bankno" name="bankno" placeholder="請輸入存摺帳號" value="{{ old('bankno', (isset($data->bankno))? $data->bankno : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 戶名 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">戶名</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="bankaccname" name="bankaccname" placeholder="請輸入戶名" value="{{ old('bankaccname', (isset($data->bankaccname))? $data->bankaccname : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
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
                                    <input type="radio" id="expert" name="expert" value="{{ $key }}" {{ old('expert', (isset($data->expert))? $data->expert : 1) == $key? 'checked' : '' }} disabled="disabled">{{ $va }}
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
                                    <input type="radio" iid="publicly" name="publicly" value="{{ $key }}" {{ old('publicly', (isset($data->publicly))? $data->publicly : 1) == $key? 'checked' : '' }} disabled="disabled">{{ $va }}
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
                                        <input type="radio" id="publish" name="publish" value="{{ $key }}" {{ old('publish', (isset($data->publish))? $data->publish : 1) == $key? 'checked' : '' }} disabled="disabled">{{ $va }}
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
                                    <input type="radio" id="transfor" name="transfor"  value="{{ $key }}" {{ old('transfor', (isset($data->transfor))? $data->transfor : 1) == $key? 'checked' : '' }} disabled="disabled">{{ $va }}
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
                                    <input type="radio" id="notify" name="notify" value="{{ $key }}" {{ old('notify', (isset($data->notify))? $data->notify : 1) == $key? 'checked' : '' }} disabled="disabled">{{ $va }}
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitform()" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>						
                        <a href="/admin/lecture">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
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
                                <option value="{{ $va->銀行代碼 }}" {{ old('bankcode', (isset($data->bankcode))? $data->bankcode==$va->銀行代碼? 'selected' : '' : '') }}>{{ $va->銀行代碼 }} {{ $va->銀行名稱 }}</option>
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
        if(checkID($("*[name='idno']").val())==false){
            alert("請確認身分證字號是否有誤");
            return;
        }
        if($("*[name='postcode']").val().length!=7){
            alert("請確認局號，局號限制7碼");
            return;
        }
        if($("*[name='postno']").val().length!=7){
            alert("請確認郵局帳號，郵局帳號限制7碼");
            return;
        }
        submitForm('#form');
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
        $("*[name='bankcode']").val($("*[name='bankname']").val()).trigger("change");
    }
</script>
@endsection