@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'system_parameter';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">系統參數維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li class="active">系統參數維護</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'method'=>'put', 'url'=>'/admin/system_parameter/', 'id'=>'form']) !!}

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">登入設定</h3></div>
                    <div class="card-body pt-4">

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">登入鎖定次數</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="Logins" name="Logins" placeholder="請輸入外聘" value="{{ old('Logins', (isset($data->Logins))? $data->Logins : '') }}" autocomplete="off"  maxlength="2" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座鐘點費及交通費</h3></div>
                    <div class="card-body pt-4">
                        <!-- 外聘鐘點費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">外聘鐘點費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="outlectunit" name="outlectunit" placeholder="請輸入外聘" value="{{ old('outlectunit', (isset($data->outlectunit))? $data->outlectunit : '') }}" autocomplete="off"  maxlength="6" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                            <!-- 總處鐘點費 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">總處鐘點費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="burlectunit" name="burlectunit" placeholder="請輸入外聘" value="{{ old('burlectunit', (isset($data->burlectunit))? $data->burlectunit : '') }}" autocomplete="off"  maxlength="6" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 內聘鐘點費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">內聘鐘點費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="inlectunit" name="inlectunit" placeholder="請輸入外聘" value="{{ old('inlectunit', (isset($data->inlectunit))? $data->inlectunit : '') }}" autocomplete="off"  maxlength="6" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                            <!-- 汽車交通費 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">汽車交通費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="motorunit" name="motorunit" placeholder="請輸入外聘" value="{{ old('motorunit', (isset($data->motorunit))? $data->motorunit : '') }}" autocomplete="off"  maxlength="6" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">住宿用餐費用</h3></div>
                    <div class="card-body pt-4">
                        <!-- 單人房費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">單人房費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="sinunit" name="sinunit" placeholder="請輸入外聘" value="{{ old('sinunit', (isset($data->sinunit))? $data->sinunit : '') }}" autocomplete="off"  maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                            <!-- 行政套房費 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">行政套房費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="vipunit" name="vipunit" placeholder="請輸入外聘" value="{{ old('vipunit', (isset($data->vipunit))? $data->vipunit : '') }}" autocomplete="off"  maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 雙人單床房費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">雙人單床房費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="doneunit" name="doneunit" placeholder="請輸入外聘" value="{{ old('doneunit', (isset($data->doneunit))? $data->doneunit : '') }}" autocomplete="off"  maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                            <!-- 雙人雙床房費 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">雙人雙床房費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="dtwounit" name="dtwounit" placeholder="請輸入外聘" value="{{ old('dtwounit', (isset($data->dtwounit))? $data->dtwounit : '') }}" autocomplete="off"  maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <div class="form-group row">
                            <!-- 早餐費用 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">早餐費用</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="meaunit" name="meaunit" placeholder="請輸入外聘" value="{{ old('meaunit', (isset($data->meaunit))? $data->meaunit : '') }}" autocomplete="off"  maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                            <!-- 午餐費用 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">午餐費用</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="lununit" name="lununit" placeholder="請輸入外聘" value="{{ old('lununit', (isset($data->lununit))? $data->lununit : '') }}" autocomplete="off"  maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>
                        <div class="form-group row">
                             <!-- 晚餐費用 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">晚餐費用</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="dinunit" name="dinunit" placeholder="請輸入外聘" value="{{ old('dinunit', (isset($data->dinunit))? $data->dinunit : '') }}" autocomplete="off"  maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>
                        <!-- 自助餐午餐費用 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">自助餐午餐費用</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="buffer1" name="buffer1" placeholder="請輸入外聘" value="{{ old('buffer1', (isset($data->buffer1))? $data->buffer1 : '') }}" autocomplete="off"  maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                            <!-- 自助餐晚餐費用 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">自助餐晚餐費用</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="buffer2" name="buffer2" placeholder="請輸入外聘" value="{{ old('buffer2', (isset($data->buffer2))? $data->buffer2 : '') }}" autocomplete="off"  maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">文具及其他用品費</h3></div>
                    <div class="card-body pt-4">
                        <!-- 教材費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">教材費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="docunit" name="docunit" placeholder="請輸入外聘" value="{{ old('docunit', (isset($data->docunit))? $data->docunit : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                            <!-- 短期班文具費 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">短期班文具費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="spenunit" name="spenunit" placeholder="請輸入外聘" value="{{ old('spenunit', (isset($data->spenunit))? $data->spenunit : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>
                        <!-- 中期班文具費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">中期班文具費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="mpenunit" name="mpenunit" placeholder="請輸入外聘" value="{{ old('mpenunit', (isset($data->mpenunit))? $data->mpenunit : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                            <!-- 長期班文具費 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">長期班文具費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="lpenunit" name="lpenunit" placeholder="請輸入外聘" value="{{ old('lpenunit', (isset($data->lpenunit))? $data->lpenunit : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">院外教學</h3></div>
                    <div class="card-body pt-4">


                        <!-- 保險費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">保險費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="insunit" name="insunit" placeholder="請輸入外聘" value="{{ old('insunit', (isset($data->insunit))? $data->insunit : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 活動費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">活動費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="actunit" name="actunit" placeholder="請輸入外聘" value="{{ old('actunit', (isset($data->actunit))? $data->actunit : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 租車費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">租車費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="carunit" name="carunit" placeholder="請輸入外聘" value="{{ old('carunit', (isset($data->carunit))? $data->carunit : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">其他雜支</h3></div>
                    <div class="card-body pt-4">

                        <!-- 茶點費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">茶點費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="teaunit" name="teaunit" placeholder="請輸入外聘" value="{{ old('teaunit', (isset($data->teaunit))? $data->teaunit : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 獎品費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">獎品費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="prizeunit" name="prizeunit" placeholder="請輸入外聘" value="{{ old('prizeunit', (isset($data->prizeunit))? $data->prizeunit : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 慶生活動費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">慶生活動費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="birthunit" name="birthunit" placeholder="請輸入外聘" value="{{ old('birthunit', (isset($data->birthunit))? $data->birthunit : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 聯誼活動費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯誼活動費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="unionunit" name="unionunit" placeholder="請輸入外聘" value="{{ old('unionunit', (isset($data->unionunit))? $data->unionunit : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 場地佈置費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">場地佈置費</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="setunit" name="setunit" placeholder="請輸入外聘" value="{{ old('setunit', (isset($data->setunit))? $data->setunit : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 加菜金 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">加菜金</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="dishunit" name="dishunit" placeholder="請輸入外聘" value="{{ old('dishunit', (isset($data->dishunit))? $data->dishunit : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">委辦班別</h3></div>
                    <div class="card-body pt-4">

                        <!-- 供膳宿費用 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">供膳宿費用</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="board1" name="board1" placeholder="請輸入外聘" value="{{ old('board1', (isset($data->board1))? $data->board1 : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 供三餐不供宿費用 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">供三餐不供宿費用</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="board2" name="board2" placeholder="請輸入外聘" value="{{ old('board2', (isset($data->board2))? $data->board2 : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 供午餐不供宿費用 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">供午餐不供宿費用</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="board3" name="board3" placeholder="請輸入外聘" value="{{ old('board3', (isset($data->board3))? $data->board3 : '') }}" autocomplete="off" maxlength="7" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">單位基本資料</h3></div>
                    <div class="card-body pt-4">


                        <!-- 單位名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">單位名稱</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="csdiname" name="csdiname" placeholder="請輸入外聘" value="{{ old('csdiname', (isset($data->csdiname))? $data->csdiname : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 負責人姓名 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">負責人姓名</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="csdiboss" name="csdiboss" placeholder="請輸入外聘" value="{{ old('csdiboss', (isset($data->csdiboss))? $data->csdiboss : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 單位地址 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">單位地址</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="csdiaddress" name="csdiaddress" placeholder="請輸入外聘" value="{{ old('csdiaddress', (isset($data->csdiaddress))? $data->csdiaddress : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">郵局劃撥轉帳</h3></div>
                    <div class="card-body pt-4">


                        <!-- 轉帳主管 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">轉帳主管</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="postboss" name="postboss" placeholder="請輸入外聘" value="{{ old('postboss', (isset($data->postboss))? $data->postboss : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 轉帳經辦 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">轉帳經辦</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="postname" name="postname" placeholder="請輸入外聘" value="{{ old('postname', (isset($data->postname))? $data->postname : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 公司電話 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">公司電話</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="posttelno" name="posttelno" placeholder="請輸入外聘" value="{{ old('posttelno', (isset($data->posttelno))? $data->posttelno : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 傳真機號碼 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">傳真機號碼</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="postfaxno" name="postfaxno" placeholder="請輸入外聘" value="{{ old('postfaxno', (isset($data->postfaxno))? $data->postfaxno : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 受託局號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">受託局號</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="offno" name="offno" placeholder="請輸入外聘" value="{{ old('offno', (isset($data->offno))? $data->offno : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 受託局名 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">受託局名</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="post" name="post" placeholder="請輸入外聘" value="{{ old('post', (isset($data->post))? $data->post : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 劃撥帳號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">劃撥帳號</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="girono" name="girono" placeholder="請輸入外聘" value="{{ old('girono', (isset($data->girono))? $data->girono : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 媒體管制號碼 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">媒體管制號碼</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="control" name="control" placeholder="請輸入外聘" value="{{ old('control', (isset($data->control))? $data->control : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">綜合所得稅</h3></div>
                    <div class="card-body pt-4">


                        <!-- 統一編號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">統一編號</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="companyno" name="companyno" placeholder="請輸入外聘" value="{{ old('companyno', (isset($data->companyno))? $data->companyno : '') }}" autocomplete="off"  maxlength="8" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 稽征機關代號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">稽征機關代號</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="taxorgan" name="taxorgan" placeholder="請輸入外聘" value="{{ old('taxorgan', (isset($data->taxorgan))? $data->taxorgan : '') }}" autocomplete="off"  maxlength="6">
                            </div>
                        </div>

                        <!-- 媒體代號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">媒體代號</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="taxcode" name="taxcode" placeholder="請輸入外聘" value="{{ old('taxcode', (isset($data->taxcode))? $data->taxcode : '') }}" autocomplete="off"  maxlength="6">
                            </div>
                        </div>

                        <!-- 營利事業稅籍編號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">營利事業稅籍編號</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="taxno" name="taxno" placeholder="請輸入外聘" value="{{ old('taxno', (isset($data->taxno))? $data->taxno : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 房屋稅籍編號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">房屋稅籍編號</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="houseno" name="houseno" placeholder="請輸入外聘" value="{{ old('houseno', (isset($data->houseno))? $data->houseno : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 報稅聯絡人姓名 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">報稅聯絡人姓名</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="taxname" name="taxname" placeholder="請輸入外聘" value="{{ old('taxname', (isset($data->taxname))? $data->taxname : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 報稅聯絡電話 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">報稅聯絡電話</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="taxtelno" name="taxtelno" placeholder="請輸入外聘" value="{{ old('taxtelno', (isset($data->taxtelno))? $data->taxtelno : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 報稅聯絡人E-MAIL -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">報稅聯絡人E-MAIL</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="taxemail" name="taxemail" placeholder="請輸入外聘" value="{{ old('taxemail', (isset($data->taxemail))? $data->taxemail : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>

                        <!-- 本國人扣繳稅率 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">本國人扣繳稅率</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="deductrate1" name="deductrate1" placeholder="請輸入外聘" value="{{ old('deductrate1', (isset($data->deductrate1))? $data->deductrate1 : '') }}" autocomplete="off"  maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 外國人扣繳稅率 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">外國人扣繳稅率</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="deductrate2" name="deductrate2" placeholder="請輸入外聘" value="{{ old('deductrate2', (isset($data->deductrate2))? $data->deductrate2 : '') }}" autocomplete="off"  maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')

@endsection