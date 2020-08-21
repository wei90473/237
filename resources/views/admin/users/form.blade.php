@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'users';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">個人帳號表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/users" class="text-info">個人帳號列表</a></li>
                        <li class="active">個人帳號表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/users/'.$data->userid, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/users/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">個人帳號表單</h3></div>
                    <div class="card-body pt-4">

                        
                        <!-- 自訂帳號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">自訂帳號</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="selfid" name="selfid" placeholder="請輸入自訂帳號" value="{{ old('selfid', (isset($data->selfid))? $data->selfid : '') }}" autocomplete="off"  maxlength="45">
                            </div>
                        </div>

                        <!-- 身分證號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">身分證號<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="userid" name="userid" placeholder="請輸入身分證號" value="{{ old('userid', (isset($data->userid))? $data->userid : '') }}" autocomplete="off" required maxlength="10" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 姓氏 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">姓氏<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="lname" name="lname" placeholder="請輸入姓氏" value="{{ old('lname', (isset($data->lname))? $data->lname : '') }}" autocomplete="off" required maxlength="45">
                            </div>
                        </div>

                        <!-- 名字 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">名字<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="fname" name="fname" placeholder="請輸入名字" value="{{ old('fname', (isset($data->fname))? $data->fname : '') }}" autocomplete="off" required maxlength="45">
                            </div>
                        </div>

                        <!-- 性別 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">性別<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="sex" name="sex" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.sex') as $key => $va)
                                        <option value="{{ $key }}" {{ old('sex', (isset($data->sex))? $data->sex : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 出生日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">出生日期<span class="text-danger">*</span></label>
                            <div class="col-sm-10">

                                <div class="input-group roc-date input-max">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">民國</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-year" maxlength="3" name="birth[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($data->birth))? mb_substr($data->birth, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">年</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-month" maxlength="2" name="birth[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($data->birth))? mb_substr($data->birth, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">月</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-day" maxlength="2" name="birth[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($data->birth))? mb_substr($data->birth, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">日</span>
                                    </div>
                                </div>

                            </div>
                        </div>



                        {{-- 取得薦送機關列表 --}}
                        <?php $recommendList = $base->getDBList('Recommend', ['recommend_id', 'code', 'name']);?>

                        <!-- 薦送機關代碼 -->
                        {{--<div class="form-group row institution">--}}
                            {{--<label class="col-sm-2 control-label text-md-right pt-2">薦送機關代碼<span class="text-danger">*</span></label>--}}

                            {{--<div class="col-sm-10">--}}
                                {{--<select id="userorg" name="userorg" class="select2 form-control select2-single input-max lecture_text" required onchange="recommendChange();">--}}
                                    {{--<option value="">無</option>--}}
                                    {{--@foreach($recommendList as $va)--}}
                                        {{--<option value="{{ $va->userorg }}" {{ old('userorg', (isset($data->userorg))? $data->userorg : '') == $va->userorg? 'selected' : '' }}>{{ $va->code }}</option>--}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}




                        <!-- 官職等 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">官職等<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="rank" name="rank" class="select2 form-control select2-single input-max lecture_text" required>
                                    <option value="">無</option>
                                    @foreach(config('app.post') as $key => $va)
                                        <option value="{{ $key }}" {{ old('rank', (isset($data->rank))? $data->rank : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <!-- 職稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">職稱</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="position" name="position" placeholder="請輸入職稱" value="{{ old('position', (isset($data->position))? $data->position : '') }}" autocomplete="off" maxlength="45">
                            </div>
                        </div>

                        <!-- 最高學歷 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">最高學歷<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="ecode" name="ecode" class="select2 form-control select2-single input-max lecture_text" required>
                                    <option value="">無</option>
                                    @foreach(config('app.ecode') as $key => $va)
                                        <option value="{{ $key }}" {{ old('ecode', (isset($data->ecode))? $data->ecode : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 畢業學校 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">畢業學校</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="education" name="education" placeholder="請輸入畢業學校" value="{{ old('education', (isset($data->education))? $data->education : '') }}" autocomplete="off"  maxlength="45">
                            </div>
                        </div>

                        <!-- 地址(公) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">地址(公)</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">郵遞區號</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="offzip" name="offzip"  value="{{ old('offzip', (isset($data->offzip))? $data->offzip : '') }}">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">縣市</span>
                                    </div>


                                    <select id="offaddr1" name="offaddr1" class="select2 form-control" style="max-width: 74px;">
                                        @foreach(config('address.county') as $key => $va)
                                            <option value="{{ $key }}" {{ old('offaddr1', (isset($data->offaddr1))? $data->offaddr1 : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                    </select>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">地址</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="10" autocomplete="off" id="offaddr2" name="offaddr2"  value="{{ old('offaddr2', (isset($data->offaddr2))? $data->offaddr2 : '') }}">
                                </div>

                            </div>
                        </div>


                        <!-- 地址(宅) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">地址(宅)</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">郵遞區號</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="homzip" name="homzip"  value="{{ old('homzip', (isset($data->homzip))? $data->homzip : '') }}">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">縣市</span>
                                    </div>


                                    <select id="homaddr1" name="homaddr1" class="select2 form-control" style="max-width: 74px;">
                                        @foreach(config('address.county') as $key => $va)
                                            <option value="{{ $key }}" {{ old('homaddr1', (isset($data->homaddr1))? $data->homaddr1 : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                    </select>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">地址</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="45" autocomplete="off" id="homaddr2" name="homaddr2"  value="{{ old('homaddr2', (isset($data->homaddr2))? $data->homaddr2 : '') }}">
                                </div>

                            </div>
                        </div>

                        <!-- 電話(公) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">電話(公)<span class="text-danger">*</span></label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">區碼</span>
                                    </div>

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="offtela1" name="offtela1"  value="{{ old('offtela1', (isset($data->offtela1))? $data->offtela1 : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="offtelb1" name="offtelb1"  value="{{ old('offtelb1', (isset($data->offtelb1))? $data->offtelb1 : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">分機</span>
                                    </div>

                                    <input type="text" style="max-width:100px;" class="form-control" maxlength="8" autocomplete="off" id="offtelc1" name="offtelc1"  value="{{ old('offtelc1', (isset($data->offtelc1))? $data->offtelc1 : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
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

                                    <input type="text" style="max-width:80px;" class="form-control" maxlength="3" autocomplete="off" id="offfaxa" name="offfaxa"  value="{{ old('offfaxa', (isset($data->offfaxa))? $data->offfaxa : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="10" autocomplete="off" id="offfaxb" name="offfaxb"  value="{{ old('offfaxb', (isset($data->offfaxb))? $data->offfaxb : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
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

                                    <input type="text" style="max-width:80px;" class="form-control" maxlength="3" autocomplete="off" id="homtela" name="homtela"  value="{{ old('homtela', (isset($data->homtela))? $data->homtela : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="10" autocomplete="off" id="homtelb" name="homtelb"  value="{{ old('homtelb', (isset($data->homtelb))? $data->homtelb : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                </div>

                            </div>
                        </div>

                        <!-- 行動電話 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="mobiltel" name="mobiltel" placeholder="請輸入行動電話" value="{{ old('mobiltel', (isset($data->mobiltel))? $data->mobiltel : '') }}" autocomplete="off"  maxlength="45">
                            </div>
                        </div>

                        <!-- 電話(人事總處) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">電話(人事總處)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="dgpatel" name="dgpatel" placeholder="請輸入電話(人事總處)" value="{{ old('dgpatel', (isset($data->dgpatel))? $data->dgpatel : '') }}" autocomplete="off"  maxlength="45">
                            </div>
                        </div>

                        <!-- 電子信箱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">電子信箱<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max lecture_text" id="email" name="email" placeholder="請輸入電子信箱" value="{{ old('email', (isset($data->email))? $data->email : '') }}" autocomplete="off" required maxlength="45">
                            </div>
                        </div>


                        <!-- 身份別 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">身份別<span class="text-danger">*</span></label>
                            <div id="identity" class="col-md-10 pt-1">

                                <input id="usertype1" class="identity" type="checkbox" name="usertype1" value="Y" {{ (isset($data->usertype1) && $data->usertype1 == 'Y')? 'checked' : '' }}>
                                <label for="usertype1" class="font-weight-normal mr-3 pointer identity">學員</label>

                                <input id="usertype2" class="identity" type="checkbox" name="usertype2" value="Y" onchange="lectureChange();" {{ (isset($data->usertype2) && $data->usertype2 == 'Y')? 'checked' : '' }}>
                                <label for="usertype2" class="font-weight-normal mr-3 pointer identity">講座</label>

                                <input id="usertype3" class="identity" type="checkbox" name="usertype3" value="Y" {{ (isset($data->usertype3) && $data->usertype3 == 'Y')? 'checked' : '' }}>
                                <label for="usertype3" class="font-weight-normal mr-3 pointer identity">學院同仁</label>

                            </div>
                        </div>

                        <!-- 人員註記 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">人員註記</label>
                            <div class="col-md-10 pt-1">

                                <input id="chief" type="checkbox" name="chief" value="Y" {{ (isset($data->chief) && $data->chief == 'Y')? 'checked' : '' }}>
                                <label for="chief" class="font-weight-normal mr-3 pointer">主管</label>

                                <input id="personnel" type="checkbox" name="personnel" value="Y" {{ (isset($data->personnel) && $data->personnel == 'Y')? 'checked' : '' }}>
                                <label for="personnel" class="font-weight-normal mr-3 pointer">人事</label>

                                <input id="aborigine" type="checkbox" name="aborigine" value="Y" {{ (isset($data->aborigine) && $data->aborigine == 'Y')? 'checked' : '' }}>
                                <label for="aborigine" class="font-weight-normal mr-3 pointer">原住民</label>

                                <input id="vegan" type="checkbox" name="vegan" value="Y" {{ (isset($data->vegan) && $data->vegan == 'Y')? 'checked' : '' }}>
                                <label for="vegan" class="font-weight-normal mr-3 pointer">素食</label>

                                <input id="handicap" type="checkbox" name="handicap" value="Y" {{ (isset($data->handicap) && $data->handicap == 'Y')? 'checked' : '' }}>
                                <label for="handicap" class="font-weight-normal mr-3 pointer">身心障礙</label>

                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/users">
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
        // 機關代碼
        function recommendChange() {

            var recommend_text = $("#recommend_id option:selected").data('recommend_text');

            if (recommend_text) {

                $('#recommend_text').html(recommend_text);
            } else {
                $('#recommend_text').html('');
            }
        }

        // 機關代碼 初始化
        recommendChange();


        // 講座身份切換(講座身份時:機關代碼.官職等.最高學歷.電話(公)區碼.電話(公).電子信箱為"非"必填)
        function lectureChange() {
            // 取得是否是獎座身份
            var lecture = $("#usertype2").prop("checked");

            if (lecture) {
                // 隱藏必填的*
                $('.lecture_text').parents('.form-group').find('span.text-danger').hide();
                $('.lecture_text').removeAttr('required', false);
                // 刪除紅框跟必填error
                $($('.lecture_text').parents('.form-group').find('.input_error')).removeClass('input_error');
                $('.lecture_text').parent().find('.error').remove();
                $('.lecture_text').parent().parent().find('.error').remove();

            } else {
                // 顯示必填的*
                $('.lecture_text').parents('.form-group').find('span.text-danger').show();
                $('.lecture_text').attr('required', true);

            }
        }

        // 講座身份初始化
        lectureChange();

        // 額外的表單檢查
        function verification() {
            // 檢查身份別至少選一個
            if ( ! $('#usertype1').prop('checked') &&  ! $('#usertype2').prop('checked') &&  ! $('#usertype3').prop('checked')) {

                // 刪除舊的error
                $('#identity').find('.error').remove();
                // 增加新的error
                $('#identity').append('<label class="error">至少要選取一種身份別</label>');

                return false;
            }

            return true;
        }

        // 刪除身份別的error
        $('.identity').change(function() {

            $(this).parents('.form-group').find('.error').remove();
        });
    </script>
@endsection