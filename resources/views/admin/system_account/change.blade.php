@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'lecture';?>

    <div class="content">
        <div class="container-fluid">

            <!-- �������D -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">���y��ƺ��@���</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">����</a></li>
                        <li><a href="/admin/lecture" class="text-info">���y��ƺ��@�C��</a></li>
                        <li class="active">���y��ƺ��@���</li>
                    </ol>
                </div>
            </div>

            <!-- ���ܰT�� -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/lecture/'.$data->serno,  'enctype'=>'multipart/form-data','id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/lecture/','enctype'=>'multipart/form-data', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">���y��ƺ��@���</h3></div>
                    <div class="card-body pt-4">


                        <!-- �����Ҧr�� -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�����Ҧr��<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="idno" name="idno" placeholder="�п�J�����Ҧr��" value="{{ old('idno', (isset($data->idno))? $data->idno : '') }}" autocomplete="off" maxlength="255" required>
                            </div>
                        </div>

                        <!-- �Ҹ��O -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">�Ҹ��O</label>
                            <div class="col-md-10">
                                <select id="sex" name="idkind" class="select2 form-control select2-single input-max" disabled="disabled">
                                    @foreach(config('app.idkind') as $key => $va)
                                        <option value="{{ $key }}" {{ old('idkind', (isset($data->idkind))? $data->idkind : 0) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- �m�� -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�m��<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="fname" name="fname" placeholder="�п�J�m��" value="{{ old('fname', (isset($data->fname))? $data->fname : '') }}" autocomplete="off" maxlength="255" required>
                            </div>
                        </div>

                        <!-- �W�r -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�W�r<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="lname" name="lname" placeholder="�п�J�W�r" value="{{ old('lname', (isset($data->cname))? str_replace($data->fname, '', $data->cname) : '') }}" autocomplete="off" maxlength="255" required>
                            </div>
                        </div>

                        <!-- �^��m�W -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�^��m�W</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="ename" name="ename" placeholder="�п�J�^��m�W" value="{{ old('ename', (isset($data->ename))? $data->ename : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>


                        <!-- �ʧO -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">�ʧO</label>
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

                        <!-- �X�ͤ�� -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�X�ͤ��</label>
                            <div class="col-sm-10">

                                <div class="input-group roc-date input-max">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">����</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-year" maxlength="3" name="birth[year]" placeholder="�п�J�~��" disabled="disabled" autocomplete="off" value="{{ (isset($data->birth))? mb_substr($data->birth, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">�~</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-month" maxlength="2" name="birth[month]" placeholder="�п�J���" disabled="disabled" autocomplete="off" value="{{ (isset($data->birth))? mb_substr($data->birth, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">��</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-day" maxlength="2" name="birth[day]" placeholder="�п�J���" disabled="disabled" autocomplete="off" value="{{ (isset($data->birth))? mb_substr($data->birth, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">��</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ���y -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">���y</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="citizen" name="citizen" placeholder="�п�J���y" value="{{ old('citizen', (isset($data->citizen))? $data->citizen : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �@�Ӹ��X -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�@�Ӹ��X</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="passport" name="passport" placeholder="�п�J�@�Ӹ��X" value="{{ old('passport', (isset($data->passport))? $data->passport : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �ĤG���Q�O�I�H -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">�ĤG���Q�O�I�H</label>
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

						<!-- ��s��� -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">��s���</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="update_date" name="update_date" value="{{ old('update_date', (isset($data->update_date))? $data->update_date : '') }} " disabled="disabled">
                            </div>
                        </div>
						<!-- �W�ǭӸ���v�� -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�W�ǭӸ���v��</label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control input-max" id="Certificate" name="Certificate" readonly="readonly" value="{{ old('Certificate', (isset($data->Certificate))? $data->Certificate : '') }}" disabled="disabled">
                              <button type="button" OnClick='javascript:$("#upload").click();'class="btn btn-sm btn-info" disabled="disabled"><i class="fa fa-save pr-2"></i>����ɮ�</button>			
							  <input type="file" class="btn btn-sm btn-info" id="upload" name="upload" style="display:none;" disabled="disabled"/>
							</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">���y��ƺ��@���</h3></div>
                    <div class="card-body pt-4">



                        <!-- �A�Ⱦ��� -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�A�Ⱦ���</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="dept" name="dept" placeholder="�п�J�A�Ⱦ���" value="{{ old('dept', (isset($data->dept))? $data->dept : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �{¾ -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�{¾</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="position" name="position" placeholder="�п�J�{¾" value="{{ old('position', (isset($data->position))? $data->position : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- ���� -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">����</label>
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
                    <div class="card-header"><h3 class="card-title">���y��ƺ��@���</h3></div>
                    <div class="card-body pt-4">

                        <!-- �����a�} -->
                        <div class="form-group row">
                            <!-- <label class="col-sm-2 control-label text-md-right pt-2">�l���ϸ�����&�d��
                            </label>
                                <div class="col-sm-4">
                                    <select class="select2 form-control select2-single input-max" name="" id="">

                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="�п�J�W�٩ΥN�X�i��d��" class="form-control">
                                    <input type="radio" name="search" value="postName">�W��
                                    <input type="radio" name="search" value="postCode">�N�X        
                                    <button>�d��</button>
                                    <label>�d�ߵ��G�G</label>
                                </div> -->





                            <label class="col-sm-2 control-label text-md-right pt-2">�����a�}</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">�l���ϸ�</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="offzip" name="offzip"  value="{{ old('offzip', (isset($data->offzip))? $data->offzip : '') }}" disabled="disabled">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">�a�}</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="10" autocomplete="off" id="offaddress" name="offaddress"  value="{{ old('offaddress', (isset($data->offaddress))? $data->offaddress : '') }}" disabled="disabled">
                                </div>

                            </div>
                        </div>

                        <!-- ��a�a�} -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">��a�a�}</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">�l���ϸ�</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="homzip" name="homzip"  value="{{ old('homzip', (isset($data->homzip))? $data->homzip : '') }}" disabled="disabled">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">�a�}</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="10" autocomplete="off" id="homaddress" name="homaddress"  value="{{ old('homaddress', (isset($data->homaddress))? $data->homaddress : '') }}" disabled="disabled">
                                </div>

                            </div>
                        </div>

                        <!-- ���y�a -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">���y�a</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">�l���ϸ�</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="regzip" name="regzip"  value="{{ old('regzip', (isset($data->regzip))? $data->regzip : '') }}" disabled="disabled">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">�a�}</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="10" autocomplete="off" id="regaddress" name="regaddress"  value="{{ old('regaddress', (isset($data->regaddress))? $data->regaddress : '') }}" disabled="disabled">
                                </div>

                            </div>
                        </div>

                        <!-- �l�H�a�} -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">�l�H�a�}</label>
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
                    <div class="card-header"><h3 class="card-title">���y��ƺ��@���</h3></div>
                    <div class="card-body pt-4">



                        <!-- �q��(���@) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�q��(���@)</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">�ϽX</span>
                                    </div>

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="offtela1" name="offtela1"  value="{{ old('offtela1', (isset($data->offtela1))? $data->offtela1 : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">���X</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="offtelb1" name="offtelb1"  value="{{ old('offtelb1', (isset($data->offtelb1))? $data->offtelb1 : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">����</span>
                                    </div>

                                    <input type="text" style="max-width:100px;" class="form-control" maxlength="8" autocomplete="off" id="offtelc1" name="offtelc1"  value="{{ old('offtelc1', (isset($data->offtelc1))? $data->offtelc1 : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                </div>

                            </div>
                        </div>

                        <!-- �q��(���G) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�q��(���G)</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">�ϽX</span>
                                    </div>

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="offtela2" name="offtela2"  value="{{ old('offtela2', (isset($data->offtela2))? $data->offtela2 : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">���X</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="offtelb2" name="offtelb2"  value="{{ old('offtelb2', (isset($data->offtelb2))? $data->offtelb2 : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">����</span>
                                    </div>

                                    <input type="text" style="max-width:100px;" class="form-control" maxlength="8" autocomplete="off" id="offtelc2" name="offtelc2"  value="{{ old('offtelc2', (isset($data->offtelc2))? $data->offtelc2 : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                </div>

                            </div>
                        </div>

                        <!-- �q��(�v) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�q��(�v)</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">�ϽX</span>
                                    </div>

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="homtela" name="homtela"  value="{{ old('homtela', (isset($data->homtela))? $data->homtela : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">���X</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="homtelb" name="homtelb"  value="{{ old('homtelb', (isset($data->homtelb))? $data->homtelb : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">


                                </div>

                            </div>
                        </div>

                        <!-- ��ʹq�� -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">��ʹq��</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="mobiltel" name="mobiltel" placeholder="�п�J��ʹq��" value="{{ old('mobiltel', (isset($data->mobiltel))? $data->mobiltel : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �ǯu(��) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�ǯu(��)</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">�ϽX</span>
                                    </div>

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="offfaxa" name="offfaxa"  value="{{ old('offfaxa', (isset($data->offfaxa))? $data->offfaxa : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">���X</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="offfaxb" name="offfaxb"  value="{{ old('offfaxb', (isset($data->offfaxb))? $data->offfaxb : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">


                                </div>

                            </div>
                        </div>

                        <!-- �ǯu(��) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�ǯu(��)</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">�ϽX</span>
                                    </div>

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="homfaxa" name="homfaxa"  value="{{ old('homfaxa', (isset($data->homfaxa))? $data->homfaxa : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">���X</span>
                                    </div>
                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="homfaxb" name="homfaxb"  value="{{ old('homfaxb', (isset($data->homfaxb))? $data->homfaxb : '') }}" disabled="disabled" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                </div>

                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="email" name="email" placeholder="�п�JEmail" value="{{ old('email', (isset($data->email))? $data->email : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �p���H -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�p���H</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="liaison" name="liaison" placeholder="�п�J�p���H" value="{{ old('liaison', (isset($data->liaison))? $data->liaison : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">���y��ƺ��@���</h3></div>
                    <div class="card-body pt-4">

                        <!-- �̰��Ǿ� -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�̰��Ǿ�</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="education" name="education" placeholder="�п�J�̰��Ǿ�" value="{{ old('education', (isset($data->education))? $data->education : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

						<!-- �M����� -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">�M�����</label>
                            <div class="col-md-10">
                                <?php 
                                $str = DB::table('s01tb');
                                $str = $str->select('s01tb.code')->distinct()->where('type','=','B')->get();
                                ?>

                                <select class="select2 form-control select2-single input-max" name="experience_area" disabled="disabled">
                                            <option value="-1">�п��</option>
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
                                            <option value="-1">�п��</option>
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
                                            <option value="-1">�п��</option>
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
                                            <option value="-1">�п��</option>
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
                                            <option value="-1">�п��</option>
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

                        <!-- ���n�g�� -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">���n�g��</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" name="experience" id="experience" maxlength="255" disabled="disabled">{{ old('experience', (isset($data->experience))? $data->experience : '') }}</textarea>
                            </div>
                        </div>

                        <!-- ���n�ۧ@�αo������ -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">���n�ۧ@�αo������</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" name="award" id="award" maxlength="255" disabled="disabled">{{ old('award', (isset($data->award))? $data->award : '') }}</textarea>
                            </div>
                        </div>

                        <!-- �������½Ҹg�� -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">�������½Ҹg��</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" name="remark" id="remark" maxlength="255" disabled="disabled">{{ old('remark', (isset($data->remark))? $data->remark : '') }}</textarea>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">�i�½ҵ{</h3></div>
                    <div class="card-body pt-4">


                        <!-- �i�½ҵ{(�@) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�i�½ҵ{(�@)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major1" name="major1" placeholder="�п�J�i�½ҵ{(�@)" value="{{ old('major1', (isset($data->major1))? $data->major1 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �i�½ҵ{(�G) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�i�½ҵ{(�G)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major2" name="major2" placeholder="�п�J�i�½ҵ{(�G)" value="{{ old('major2', (isset($data->major2))? $data->major2 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �i�½ҵ{(�T) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�i�½ҵ{(�T)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major3" name="major3" placeholder="�п�J�i�½ҵ{(�T)" value="{{ old('major3', (isset($data->major3))? $data->major3 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �i�½ҵ{(�|) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�i�½ҵ{(�|)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major4" name="major4" placeholder="�п�J�i�½ҵ{(�|)" value="{{ old('major4', (isset($data->major4))? $data->major4 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �i�½ҵ{(��) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�i�½ҵ{(��)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major5" name="major5" placeholder="�п�J�i�½ҵ{(��)" value="{{ old('major5', (isset($data->major5))? $data->major5 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �i�½ҵ{(��) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�i�½ҵ{(��)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major6" name="major6" placeholder="�п�J�i�½ҵ{(��)" value="{{ old('major6', (isset($data->major6))? $data->major6 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �i�½ҵ{(�C) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�i�½ҵ{(�C)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major7" name="major7" placeholder="�п�J�i�½ҵ{(�C)" value="{{ old('major7', (isset($data->major7))? $data->major7 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �i�½ҵ{(�K) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�i�½ҵ{(�K)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major8" name="major8" placeholder="�п�J�i�½ҵ{(�K)" value="{{ old('major8', (isset($data->major8))? $data->major8 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �i�½ҵ{(�E) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�i�½ҵ{(�E)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major9" name="major9" placeholder="�п�J�i�½ҵ{(�E)" value="{{ old('major9', (isset($data->major9))? $data->major9 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �i�½ҵ{(�Q) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�i�½ҵ{(�Q)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="major10" name="major10" placeholder="�п�J�i�½ҵ{(�Q)" value="{{ old('major10', (isset($data->major10))? $data->major10 : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>





                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">�l��</h3></div>
                    <div class="card-body pt-4">


                        <!-- �l�� -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�l��</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="post" name="post" placeholder="�п�J�l��" value="{{ old('post', (isset($data->post))? $data->post : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- ���� -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">����</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="postcode" name="postcode" placeholder="�п�J����" value="{{ old('postcode', (isset($data->postcode))? $data->postcode : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- �l���b�� -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�l���b��</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="postno" name="postno" placeholder="�п�J�l���b��" value="{{ old('postno', (isset($data->postno))? $data->postno : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>



                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">���ľ��c</h3></div>
                    <div class="card-body pt-4">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">�Ȧ�</label>
                            <div class="col-sm-10">
                                <div class="input-group roc-date input-max">
                                    <select id="bankcode" name="bankcode" class="form-control select2" placeholder="�п�ܻȦ�">
                                            <option value="-1">�п��</option>
                                        @foreach($list as $key => $va)
                                            <option value="{{ $va->�Ȧ�N�X }}" {{ old('bankcode', (isset($data->bankcode))? $data->bankcode==$va->�Ȧ�N�X? 'selected' : '' : '') }}>{{ $va->�Ȧ�W�� }}</option>
                                        @endforeach	
                                    </select>
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal1"><i class="fa fa-plus fa-lg pr-2"></i>�d�߻Ȧ�N�X</button>
                                </div>
						   </div>
                        </div>

                        <!-- �s�P�b�� -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">�s�P�b��</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="bankno" name="bankno" placeholder="�п�J�s�P�b��" value="{{ old('bankno', (isset($data->bankno))? $data->bankno : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- ��W -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">��W</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="bankaccname" name="bankaccname" placeholder="�п�J��W" value="{{ old('bankaccname', (isset($data->bankaccname))? $data->bankaccname : '') }}" disabled="disabled" autocomplete="off" maxlength="255">
                            </div>
                        </div>



                    </div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">���y��ƺ��@���</h3></div>
                    <div class="card-body pt-4">

                        <!-- ���w�M�a -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">���w�M�a</label>
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


                        <!-- �H���`�B -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">�H���`�B</label>
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


                        <!-- ���Ⱦ��� -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">���Ⱦ���</label>
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


                        <!-- ��b�b�� -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">��b�b��</label>
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

                        <!-- �J�b�q�� -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">�J�b�q��</label>
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
                        <button type="button" onclick="submitform()" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>�x�s</button>						
                        <a href="/admin/lecture">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> �^�C��</button>
                        </a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
    <!-- Modal1 �妸�s�W -->
    <div class="modal fade" id="exampleModal1" name="exampleModal1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">�d�߻Ȧ�N�X</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body pt-4 text-center">
                        <label for="bankname">�Ȧ�N�X�G</label>
                        <select id="bankname" name="bankname" class="form-control select2" placeholder="�п�ܻȦ�">
                                <option value="-1">�п��</option>
                            @foreach($list as $key => $va)
                                <option value="{{ $va->�Ȧ�N�X }}" {{ old('bankcode', (isset($data->bankcode))? $data->bankcode==$va->�Ȧ�N�X? 'selected' : '' : '') }}>{{ $va->�Ȧ�N�X }} {{ $va->�Ȧ�W�� }}</option>
                            @endforeach	
                        </select>
                        <br/>
                    </div>
                </div>
                <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onClick="confirmBank()">�T�w</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">����</button>
                </div>
            </div>
        </div>
    </div>
    <!-- �Ϥ� -->
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
            alert("�нT�{�����Ҧr���O�_���~");
            return;
        }
        if($("*[name='postcode']").val().length!=7){
            alert("�нT�{�����A��������7�X");
            return;
        }
        if($("*[name='postno']").val().length!=7){
            alert("�нT�{�l���b���A�l���b������7�X");
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