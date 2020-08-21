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

                        <ul class="list-group col-md-10" >
                            <li class="list-group-item">
                                班號 : {{ $class_data['class'] }}<br>
                                辦班院區 : {{ config('app.branch.'.$class_data['branch']) }}<br>
                                班別名稱 : {{ $class_data['name'] }}<br>
                                期別 : {{ $class_data['term'] }}<br>
                                分班名稱 : {{ $class_data['branchname'] }}<br>
                                班別類型 : {{ config('app.process.'.$class_data['process']) }}<br>
                                受訓期間 : {{ $class_data['sdate'] }} ~ {{ $class_data['edate'] }}<br>
                                班務人員 : {{ $class_data['sponsor'] }}
                            </li>
                        </ul>
                        <input type="hidden" name="class" value="{{ $class_data['class'] }}">
                        <input type="hidden" name="term" value="{{ $class_data['term'] }}">
                        <?php if(isset($data->idkind) && isset($data->course)){ ?>
                        <input type="hidden" name="idkind" value="{{ $data->idkind }}">
                        <input type="hidden" name="course" value="{{ $data->course }}">
                        <?php } ?>
                        <br>

                        <!-- 課程名稱 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">課程名稱<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <select id="course" name="course" required class="select2 form-control select2-single input-max">
                                    <option value=''>請選擇課程名稱</option>
                                    <?php foreach($class_data['course'] as $row){ ?>
                                    <option value='<?=$row->course;?>' {{ old('course', (isset($data->course))? $data->course : 0) == $row->course? 'selected' : '' }}  ><?=$row->name;?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <?php if(!isset($data)){ ?>
                            <label class="col-sm-2 control-label text-md-right pt-2">查詢講師<span class="text-danger"></span></label>
                            <div class="col-sm-3">
                                <select id="teacher_idno" name="teacher_idno" class="form-control select2-single input-max" onchange="teacherChange()" >

                                </select>
                            </div>
                            <?php } ?>

                        </div>

                        <!-- 證號別 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">證號別</label>
                            <div class="col-md-3">
                                <select id="idkind" name="idkind" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.idkind') as $key => $va)
                                        <option value="{{ $key }}" {{ old('idkind', (isset($data->idkind))? $data->idkind : 0) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">姓名<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="cname" name="cname" <?=(isset($data->cname))?'readonly="readonly"':'';?> placeholder="請輸入姓名" value="{{ old('cname', (isset($data->cname))? $data->cname : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                        <!-- 身分證號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">身分證號</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="idno" name="idno" <?=(isset($data->idno))?'readonly="readonly"':'';?>  placeholder="請輸入身分證號" value="{{ old('idno', (isset($data->idno))? $data->idno : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">英文姓名</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="ename" name="ename" placeholder="請輸入英文姓名" value="{{ old('ename', (isset($data->ename))? $data->ename : '') }}" autocomplete="off" maxlength="255">
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

                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡人</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="liaison" name="liaison" placeholder="請輸入聯絡人" value="{{ old('liaison', (isset($data->liaison))? $data->liaison : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 行動電話 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="mobiltel" name="mobiltel" placeholder="請輸入行動電話" value="{{ old('mobiltel', (isset($data->mobiltel))? $data->mobiltel : '') }}" autocomplete="off" maxlength="255">
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">email</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="email" name="email" placeholder="請輸入email" value="{{ old('email', (isset($data->email))? $data->email : '') }}" autocomplete="off" maxlength="255">
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


                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitform();" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/waiting/detail?class={{ $class_data['class'] }}&term={{ $class_data['term'] }}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                        <?php if(isset($data->id)){?>
                        <span onclick="$('#del_form').attr('action', '/admin/waiting/{{ $data->id }}');" data-toggle="modal" data-target="#del_modol" >
                            <button type="button" class="btn btn-sm btn-danger"> 刪除</button>
                        </span>
                        <?php }?>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')
    @include('admin/layouts/list/waiting_del_modol')

@endsection

	@section('js')
    <script type="text/javascript">

        $(function (){
            $("#teacher_idno").select2({
                language: 'zh-TW',
                width: '100%',
                // 最多字元限制
                maximumInputLength: 5,
                // 最少字元才觸發尋找, 0 不指定
                minimumInputLength: 1,
                // 當找不到可以使用輸入的文字
                // tags: true,
                placeholder: '',
                // AJAX 相關操作
                ajax: {
                    url: '/admin/waiting/getIdno',
                    type: 'get',
                    // 要送出的資料
                    data: function (params){
                        // 在伺服器會得到一個 POST 'search'
                        return {
                            search: params.term
                        };
                    },
                    processResults: function (data){
                        console.log(data)

                        // 一定要返回 results 物件
                        return {
                            results: data,
                            // 可以啟用無線捲軸做分頁
                            pagination: {
                                more: true
                            }
                        }
                    }
                }
            });
        })

        function teacherChange () {
            let idno = $('#teacher_idno').val();

            $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json",
                url: '/admin/waiting/getTeacher',
                data: { idno: idno},
                success: function(data){
                    // $('#idkind')[0].selectedIndex = data.idkind;
                    $("#idkind").val(data.idkind).select2()
                    $('#idno').val(data.idno);
                    $('#cname').val(data.cname);
                    $('#ename').val(data.ename);
                    $('#dept').val(data.dept);
                    $('#position').val(data.position);
                    $('#liaison').val(data.liaison);
                    $('#mobiltel').val(data.mobiltel);
                    $('#email').val(data.email);
                    $('#offtela1').val(data.offtela1);
                    $('#offtelb1').val(data.offtelb1);
                    $('#offtelb1').val(data.offtelb1);
                    $('#offtela2').val(data.offtela2);
                    $('#offtelb2').val(data.offtelb2);
                    $('#offtelc2').val(data.offtelc2);
                    $('#homtela').val(data.homtela);
                    $('#homtelb').val(data.homtelb);
                    $('#offfaxa').val(data.offfaxa);
                    $('#offfaxb').val(data.offfaxb);
                    $('#homfaxa').val(data.homfaxa);
                    $('#homfaxb').val(data.homfaxb);
                },
                error: function() {
                    alert('操作錯誤');
                }
            });
        }

        <?php if(isset($data->idkind) && isset($data->course)){ ?>
        $("#idkind").prop('disabled', true);
        $("#course").prop('disabled', true);
        <?php }?>

        function submitform(){

	  		if($("*[name='idkind']").val() == '3' || $("*[name='idkind']").val() == '4' || $("*[name='idkind']").val() == '7'){
                if($("*[name='ename']").val() == ''){
                    alert("國內無地址之外僑、國內有地址之外僑、非居住者，英文姓名為必填");
                return;
                }
	  		}

	        submitForm('#form');
	   }

    </script>
    @endsection