@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'waiting';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座擬聘處理表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/waiting" class="text-info">講座擬聘處理列表</a></li>
                        <li class="active">講座擬聘處理表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/waiting/'.$data->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/waiting/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座擬聘處理表單</h3></div>
                    <div class="card-body pt-4">



                        <!-- 班別 -->
                        <?php $list = $base->getDBList('T01tb', ['class', 'name']);?>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">班別<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="class" name="class" class="select2 form-control select2-single input-max" onchange="getTerm()">
                                    <option value="">請選擇</option>
                                    @foreach($list as $key => $va)
                                        <option value="<?php echo strlen($va->class)==5?'0'.$va->class:$va->class;?>" {{ old('class', (isset($data->class))? $data->class : 1) == $va->class? 'selected' : '' }}><?php echo strlen($va->class)==5?'0'.$va->class:$va->class;?>{{ $va->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 期別 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">期別<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select id="term" name="term" class="select2 form-control select2-single input-max" onchange="getCourse()"></select>
                                <!-- <div class="input-group bootstrap-touchspin number_box"> -->
                                    <!-- 減 -->
                                    <!-- <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span> -->

                                    <!-- 輸入欄位 -->
                                    <!-- <input type="text" class="form-control number-input-max" id="term" name="term" min="1" placeholder="請輸入期別" value="{{ old('term', (isset($data->term))? $data->term : NULL) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="255" required> -->

                                    <!-- 加 -->
                                    <!-- <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span> -->
                                <!-- </div> -->
                            </div>
                        </div>

                        <!-- 課程名稱 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">課程名稱<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="course" name="course" class="select2 form-control select2-single input-max"></select>
                            </div>
                        </div>

                        <!-- 遴聘與否 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">遴聘與否</label>
                            <div class="col-md-10" style="display:flex;align-items:center;">
                                <!--<select id="hire" name="hire" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.yorn') as $key => $va)
                                        <option value="{{ $key }}" {{ old('hire', (isset($data->hire))? $data->hire : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>-->
                                @foreach(config('app.yorn') as $key => $va)
                                    <input style="margin-left:5px;" type="radio" id="hire" name="hire" value="{{ $key }}" {{ old('hire', (isset($data->hire))? $data->hire : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>

                        <!-- 姓氏 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">姓氏<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="lname" name="lname" placeholder="請輸入姓氏" value="{{ old('lname', (isset($data->lname))? $data->lname : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                        <!-- 名字 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">名字<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="fname" name="fname" placeholder="請輸入名字" value="{{ old('fname', (isset($data->fname))? $data->fname : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                        <!-- 英文姓名 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">英文姓名</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="ename" name="ename" placeholder="請輸入英文姓名" value="{{ old('ename', (isset($data->ename))? $data->ename : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>


                        <!-- 證號別 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">證號別</label>
                            <div class="col-md-10">
                                <select id="idkind" name="idkind" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.idkind') as $key => $va)
                                        <option value="{{ $key }}" {{ old('idkind', (isset($data->idkind))? $data->idkind : 0) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 身分證號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">身分證號</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="idno" name="idno" placeholder="請輸入身分證號" value="{{ old('idno', (isset($data->idno))? $data->idno : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

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

                        <!-- 聯絡人 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡人</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="liaison" name="liaison" placeholder="請輸入聯絡人" value="{{ old('liaison', (isset($data->liaison))? $data->liaison : '') }}" autocomplete="off" maxlength="255">
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

                        <!-- 傳真(宅) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">傳真(宅)</label>
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

                        <!-- 行動電話 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="mobiltel" name="mobiltel" placeholder="請輸入行動電話" value="{{ old('mobiltel', (isset($data->mobiltel))? $data->mobiltel : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>






                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/waiting">
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

    <script>
        function getTerm()
        {
            let term = <?=isset($data['term'])?$data['term']:'';?>;

            $.ajax({
                type: 'post',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "html",
                url:"/admin/waiting/getterm",
                data: { class: $('#class').val()},
                success: function(data){
                    let dataArr = JSON.parse(data);
                    let tempHTML = "<option value=''>請選擇期別</option>";
                    for(let i=0; i<dataArr.length; i++) {
                        tempHTML += "<option value='"+dataArr[i].term+"' "+(Number(dataArr[i].term)==term?'selected':'')+">"+dataArr[i].term+"</option>";
                    }
                    $("#term").html(tempHTML);

                    getCourse();
                },
                error: function() {
                    console.log('Ajax Error');
                }
            });
        };

        function getCourse()
        {
            let course = <?=isset($data['course'])?$data['course']:'';?>;

            $.ajax({
                type: 'post',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "html",
                url:"/admin/waiting/getcourse",
                data: { class: $('#class').val(), term: $('#term').val()},
                success: function(data){
                    let dataArr = JSON.parse(data);
                    let tempHTML = "<option value=''>請選擇課程名稱</option>";
                    for(let i=0; i<dataArr.length; i++) {
                        tempHTML += "<option value='"+dataArr[i].course+"' "+(Number(dataArr[i].course)==course?'selected':'')+">"+dataArr[i].name+"</option>";
                    }
                    $("#course").html(tempHTML);
                },
                error: function() {
                    console.log('Ajax Error');
                }
            });
        };

        setTimeout(() => {
            if($("#class").val() != '') {
                getTerm();
            }
        }, 1000);
    </script>

@endsection