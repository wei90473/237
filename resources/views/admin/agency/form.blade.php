@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'agency';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">訓練機構資料維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/agency" class="text-info">訓練機構資料維護</a></li>
                        <li class="active">訓練機構資料編輯</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/agency/'.$data->agency, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/agency/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">訓練機構資料編輯</h3></div>
                    <div class="card-body pt-4">

                        <!-- 訓練機構代碼 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">訓練機構代碼<span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="agency" name="agency" placeholder="機構代碼" value="{{ old('agency', (isset($data->agency))? $data->agency : '') }}" autocomplete="off" required maxlength="255" {{ (isset($data))? 'readonly' :'' }}>
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">行政機關代碼<span class="text-danger">*</span></label>
                            <div class="col-sm-6 input-group ">
                                <input type="text" class="form-control input-max" id="enrollorg" name="enrollorg" placeholder="行政機關代碼" value="{{ old('enrollorg', (isset($data->enrollorg))? $data->enrollorg : '') }}" autocomplete="off" required maxlength="255" {{ (isset($data))? 'readonly' :'' }}>
                                @if ( !isset($data) )
                                <button class="btn btn-number" type="button" onclick="getlist()">...</button>
                                @endif
                            </div>
                        </div>

                        <!-- 訓練機構 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">訓練機構<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="name" name="name" placeholder="訓練機構" value="{{ old('name', (isset($data->name))? $data->name : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>
                        <!-- 地址 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">地址<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="address" name="address"  value="{{ old('address', (isset($data->address))? $data->address : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>
                        <!-- 電話 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">電話<span class="text-danger">*</span></label>
                            <div class="input-group col-sm-4">
                                <input type="text" class="form-control input-max" id="telnoa" name="telnoa" placeholder="區域碼" value="{{ old('telnoa', (isset($data->telnoa))? $data->telnoa : '') }}" autocomplete="off"  maxlength="2">
                                <input type="text" class="form-control input-max" id="telnob" name="telnob" placeholder="電話號碼" value="{{ old('telnob', (isset($data->telnob))? $data->telnob : '') }}" autocomplete="off"  maxlength="10" style="
                                width: 88px;">
                                <input type="text" class="form-control input-max" id="telnoc" name="telnoc" placeholder="分機" value="{{ old('telnoc', (isset($data->telnoc))? $data->telnoc : '') }}" autocomplete="off"  maxlength="10">
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">傳真</label>
                            <div class="input-group col-sm-4">
                                <input type="text" class="form-control input-max" id="faxnoa" name="faxnoa" placeholder="區域碼" value="{{ old('faxnoa', (isset($data->faxnoa))? $data->faxnoa : '') }}" autocomplete="off"  maxlength="2">
                                <input type="text" class="form-control input-max" id="faxnob" name="faxnob" placeholder="傳真號碼" value="{{ old('faxnob', (isset($data->faxnob))? $data->faxnob : '') }}" autocomplete="off"  maxlength="10" style="
                                width: 122px;">
                            </div>
                        </div>
                        <!-- 網址 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">網址</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="url" name="url" placeholder="http://" value="{{ old('url', (isset($data->url))? $data->url : '') }}" autocomplete="off"  maxlength="255">
                            </div>
                        </div>
                        <!-- 首長 -->
                        <fieldset style="padding: inherit">
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">首長姓名</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="chief" name="chief"  value="{{ old('chief', (isset($data->chief))? $data->chief : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">職稱</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="cposition" name="cposition" placeholder="職稱" value="{{ old('cposition', (isset($data->cposition))? $data->cposition : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>
                            <!-- 電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">電話</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="ctelnoa" name="ctelnoa" placeholder="區域碼" value="{{ old('ctelnoa', (isset($data->ctelnoa))? $data->ctelnoa : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="ctelnob" name="ctelnob" placeholder="電話號碼" value="{{ old('ctelnob', (isset($data->ctelnob))? $data->ctelnob : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 88px;">
                                    <input type="text" class="form-control input-max" id="ctelnoc" name="ctelnoc" placeholder="分機" value="{{ old('ctelnoc', (isset($data->ctelnoc))? $data->ctelnoc : '') }}" autocomplete="off"  maxlength="10">
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">傳真</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="cfaxnoa" name="cfaxnoa" placeholder="區域碼" value="{{ old('cfaxnoa', (isset($data->cfaxnoa))? $data->cfaxnoa : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="cfaxnob" name="cfaxnob" placeholder="傳真號碼" value="{{ old('cfaxnob', (isset($data->cfaxnob))? $data->cfaxnob : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 122px;">
                                </div>
                            </div>  
                            <!-- 行動電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="cmobiltel" name="cmobiltel" placeholder="行動電話" value="{{ old('cmobiltel', (isset($data->cmobiltel))? $data->cmobiltel : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">E-mail</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="cemail" name="cemail" placeholder="E-mail" value="{{ old('cemail', (isset($data->cemail))? $data->cemail : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>  
                        </fieldset>
                        <br>
                        <!-- 副首長(一) -->
                        <fieldset style="padding: inherit">
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">副首長(一)</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="assistant1" name="assistant1"  value="{{ old('assistant1', (isset($data->assistant1))? $data->assistant1 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">職稱</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="aposition1" name="aposition1" placeholder="職稱" value="{{ old('aposition1', (isset($data->aposition1))? $data->aposition1 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>
                            <!-- 電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">電話</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="atelnoa1" name="atelnoa1" placeholder="區域碼" value="{{ old('atelnoa1', (isset($data->atelnoa1))? $data->atelnoa1 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="atelnob1" name="atelnob1" placeholder="電話號碼" value="{{ old('atelnob1', (isset($data->atelnob1))? $data->atelnob1 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 88px;">
                                    <input type="text" class="form-control input-max" id="atelnoc1" name="atelnoc1" placeholder="分機" value="{{ old('atelnoc1', (isset($data->atelnoc1))? $data->atelnoc1 : '') }}" autocomplete="off"  maxlength="10">
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">傳真</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="afaxnoa1" name="afaxnoa1" placeholder="區域碼" value="{{ old('afaxnoa1', (isset($data->afaxnoa1))? $data->afaxnoa1 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="afaxnob1" name="afaxnob1" placeholder="傳真號碼" value="{{ old('afaxnob1', (isset($data->afaxnob1))? $data->afaxnob1 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 122px;">
                                </div>
                            </div>  
                            <!-- 行動電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="amobiltel1" name="amobiltel1" placeholder="行動電話" value="{{ old('amobiltel1', (isset($data->amobiltel1))? $data->amobiltel1 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">E-mail</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="aemail1" name="aemail1" placeholder="E-mail" value="{{ old('aemail1', (isset($data->aemail1))? $data->aemail1 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>  
                        </fieldset>
                        <br>
                        <!-- 副首長(二) -->
                        <fieldset style="padding: inherit">
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">副首長(二)</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="assistant2" name="assistant2"  value="{{ old('assistant2', (isset($data->assistant2))? $data->assistant2 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">職稱</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="aposition2" name="aposition2" placeholder="職稱" value="{{ old('aposition2', (isset($data->aposition2))? $data->aposition2 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>
                            <!-- 電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">電話</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="atelnoa2" name="atelnoa2" placeholder="區域碼" value="{{ old('atelnoa2', (isset($data->atelnoa2))? $data->atelnoa2 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="atelnob2" name="atelnob2" placeholder="電話號碼" value="{{ old('atelnob2', (isset($data->atelnob2))? $data->atelnob2 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 88px;">
                                    <input type="text" class="form-control input-max" id="atelnoc2" name="atelnoc2" placeholder="分機" value="{{ old('atelnoc2', (isset($data->atelnoc2))? $data->atelnoc2 : '') }}" autocomplete="off"  maxlength="10">
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">傳真</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="afaxnoa2" name="afaxnoa2" placeholder="區域碼" value="{{ old('afaxnoa2', (isset($data->afaxnoa2))? $data->afaxnoa2 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="afaxnob2" name="afaxnob2" placeholder="傳真號碼" value="{{ old('afaxnob2', (isset($data->afaxnob2))? $data->afaxnob2 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 122px;">
                                </div>
                            </div>  
                            <!-- 行動電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="amobiltel2" name="amobiltel2" placeholder="行動電話" value="{{ old('amobiltel2', (isset($data->amobiltel2))? $data->amobiltel2 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">E-mail</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="aemail2" name="aemail2" placeholder="E-mail" value="{{ old('aemail2', (isset($data->aemail2))? $data->aemail2 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>  
                        </fieldset>
                        <br>
                        <!-- 訓練交流業務 -->
                        <fieldset style="padding: inherit">
                            <legend>訓練交流業務</legend>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">聯絡人</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="liaison1" name="liaison1"  value="{{ old('liaison1', (isset($data->liaison1))? $data->liaison1 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">職稱</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lposition1" name="lposition1" placeholder="職稱" value="{{ old('lposition1', (isset($data->lposition1))? $data->lposition1 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>
                            <!-- 電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">電話</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="ltelnoa1" name="ltelnoa1" placeholder="區域碼" value="{{ old('ltelnoa1', (isset($data->ltelnoa1))? $data->ltelnoa1 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="ltelnob1" name="ltelnob1" placeholder="電話號碼" value="{{ old('ltelnob1', (isset($data->ltelnob1))? $data->ltelnob1 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 88px;">
                                    <input type="text" class="form-control input-max" id="ltelnoc1" name="ltelnoc1" placeholder="分機" value="{{ old('ltelnoc1', (isset($data->ltelnoc1))? $data->ltelnoc1 : '') }}" autocomplete="off"  maxlength="10">
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">傳真</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="lfaxnoa1" name="lfaxnoa1" placeholder="區域碼" value="{{ old('lfaxnoa1', (isset($data->lfaxnoa1))? $data->lfaxnoa1 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="lfaxnob1" name="lfaxnob1" placeholder="傳真號碼" value="{{ old('lfaxnob1', (isset($data->lfaxnob1))? $data->lfaxnob1 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 122px;">
                                </div>
                            </div>  
                            <!-- 行動電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lmobiltel1" name="lmobiltel1" placeholder="行動電話" value="{{ old('lmobiltel1', (isset($data->lmobiltel1))? $data->lmobiltel1 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">E-mail</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lemail1" name="lemail1" placeholder="E-mail" value="{{ old('lemail1', (isset($data->lemail1))? $data->lemail1 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>  
                        </fieldset>
                        <br>
                        <!-- 研究發展業務 -->
                        <fieldset style="padding: inherit">
                            <legend>研究發展業務</legend>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">聯絡人</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="liaison2" name="liaison2"  value="{{ old('liaison2', (isset($data->liaison2))? $data->liaison2 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">職稱</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lposition2" name="lposition2" placeholder="職稱" value="{{ old('lposition2', (isset($data->lposition2))? $data->lposition2 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>
                            <!-- 電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">電話</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="ltelnoa2" name="ltelnoa2" placeholder="區域碼" value="{{ old('ltelnoa2', (isset($data->ltelnoa2))? $data->ltelnoa2 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="ltelnob2" name="ltelnob2" placeholder="電話號碼" value="{{ old('ltelnob2', (isset($data->ltelnob2))? $data->ltelnob2 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 88px;">
                                    <input type="text" class="form-control input-max" id="ltelnoc2" name="ltelnoc2" placeholder="分機" value="{{ old('ltelnoc2', (isset($data->ltelnoc2))? $data->ltelnoc2 : '') }}" autocomplete="off"  maxlength="10">
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">傳真</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="lfaxnoa2" name="lfaxnoa2" placeholder="區域碼" value="{{ old('lfaxnoa2', (isset($data->lfaxnoa2))? $data->lfaxnoa2 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="lfaxnob2" name="lfaxnob2" placeholder="傳真號碼" value="{{ old('lfaxnob2', (isset($data->lfaxnob2))? $data->lfaxnob2 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 122px;">
                                </div>
                            </div>  
                            <!-- 行動電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lmobiltel2" name="lmobiltel2" placeholder="行動電話" value="{{ old('lmobiltel2', (isset($data->lmobiltel2))? $data->lmobiltel2 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">E-mail</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lemail2" name="lemail2" placeholder="E-mail" value="{{ old('lemail2', (isset($data->lemail2))? $data->lemail2 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>  
                        </fieldset>
                        <br>
                        <!-- 資訊管理業務 -->
                        <fieldset style="padding: inherit">
                            <legend>資訊管理業務</legend>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">聯絡人</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="liaison3" name="liaison3"  value="{{ old('liaison3', (isset($data->liaison3))? $data->liaison3 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">職稱</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lposition3" name="lposition3" placeholder="職稱" value="{{ old('lposition3', (isset($data->lposition3))? $data->lposition3 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>
                            <!-- 電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">電話</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="ltelnoa3" name="ltelnoa3" placeholder="區域碼" value="{{ old('ltelnoa3', (isset($data->ltelnoa3))? $data->ltelnoa3 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="ltelnob3" name="ltelnob3" placeholder="電話號碼" value="{{ old('ltelnob3', (isset($data->ltelnob3))? $data->ltelnob3 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 88px;">
                                    <input type="text" class="form-control input-max" id="ltelnoc3" name="ltelnoc3" placeholder="分機" value="{{ old('ltelnoc3', (isset($data->ltelnoc3))? $data->ltelnoc3 : '') }}" autocomplete="off"  maxlength="10">
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">傳真</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="lfaxnoa3" name="lfaxnoa3" placeholder="區域碼" value="{{ old('lfaxnoa3', (isset($data->lfaxnoa3))? $data->lfaxnoa3 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="lfaxnob3" name="lfaxnob3" placeholder="傳真號碼" value="{{ old('lfaxnob3', (isset($data->lfaxnob3))? $data->lfaxnob3 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 122px;">
                                </div>
                            </div>  
                            <!-- 行動電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lmobiltel3" name="lmobiltel3" placeholder="行動電話" value="{{ old('lmobiltel3', (isset($data->lmobiltel3))? $data->lmobiltel3 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">E-mail</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lemail3" name="lemail3" placeholder="E-mail" value="{{ old('lemail3', (isset($data->lemail3))? $data->lemail3 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>  
                        </fieldset>
                        <br>
                        <!-- 圖書管理業務 -->
                        <fieldset style="padding: inherit">
                            <legend>圖書管理業務</legend>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">聯絡人</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="liaison4" name="liaison4"  value="{{ old('liaison4', (isset($data->liaison4))? $data->liaison4 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">職稱</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lposition4" name="lposition4" placeholder="職稱" value="{{ old('lposition4', (isset($data->lposition4))? $data->lposition4 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>
                            <!-- 電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">電話</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="ltelnoa4" name="ltelnoa4" placeholder="區域碼" value="{{ old('ltelnoa4', (isset($data->ltelnoa4))? $data->ltelnoa4 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="ltelnob4" name="ltelnob4" placeholder="電話號碼" value="{{ old('ltelnob4', (isset($data->ltelnob4))? $data->ltelnob4 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 88px;">
                                    <input type="text" class="form-control input-max" id="ltelnoc4" name="ltelnoc4" placeholder="分機" value="{{ old('ltelnoc4', (isset($data->ltelnoc4))? $data->ltelnoc4 : '') }}" autocomplete="off"  maxlength="10">
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">傳真</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="lfaxnoa4" name="lfaxnoa4" placeholder="區域碼" value="{{ old('lfaxnoa4', (isset($data->lfaxnoa4))? $data->lfaxnoa4 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="lfaxnob4" name="lfaxnob4" placeholder="傳真號碼" value="{{ old('lfaxnob4', (isset($data->lfaxnob4))? $data->lfaxnob4 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 122px;">
                                </div>
                            </div>  
                            <!-- 行動電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lmobiltel4" name="lmobiltel4" placeholder="行動電話" value="{{ old('lmobiltel4', (isset($data->lmobiltel4))? $data->lmobiltel4 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">E-mail</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lemail4" name="lemail4" placeholder="E-mail" value="{{ old('lemail4', (isset($data->lemail4))? $data->lemail4 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>  
                        </fieldset>
                        <br>
                        <!-- 訓練進修業務 -->
                        <fieldset style="padding: inherit">
                            <legend>訓練進修業務</legend>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">聯絡人</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="liaison5" name="liaison5"  value="{{ old('liaison5', (isset($data->liaison5))? $data->liaison5 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">職稱</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lposition5" name="lposition5" placeholder="職稱" value="{{ old('lposition5', (isset($data->lposition5))? $data->lposition5 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>
                            <!-- 電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">電話</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="ltelnoa5" name="ltelnoa5" placeholder="區域碼" value="{{ old('ltelnoa5', (isset($data->ltelnoa5))? $data->ltelnoa5 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="ltelnob5" name="ltelnob5" placeholder="電話號碼" value="{{ old('ltelnob5', (isset($data->ltelnob5))? $data->ltelnob5 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 88px;">
                                    <input type="text" class="form-control input-max" id="ltelnoc5" name="ltelnoc5" placeholder="分機" value="{{ old('ltelnoc5', (isset($data->ltelnoc5))? $data->ltelnoc5 : '') }}" autocomplete="off"  maxlength="10">
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">傳真</label>
                                <div class="input-group col-sm-4">
                                    <input type="text" class="form-control input-max" id="lfaxnoa5" name="lfaxnoa5" placeholder="區域碼" value="{{ old('lfaxnoa5', (isset($data->lfaxnoa5))? $data->lfaxnoa5 : '') }}" autocomplete="off"  maxlength="2">
                                    <input type="text" class="form-control input-max" id="lfaxnob5" name="lfaxnob5" placeholder="傳真號碼" value="{{ old('lfaxnob5', (isset($data->lfaxnob5))? $data->lfaxnob5 : '') }}" autocomplete="off"  maxlength="10" style="
                                    width: 122px;">
                                </div>
                            </div>  
                            <!-- 行動電話 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lmobiltel5" name="lmobiltel5" placeholder="行動電話" value="{{ old('lmobiltel5', (isset($data->lmobiltel5))? $data->lmobiltel5 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                                <label class="col-sm-2 control-label text-md-right pt-2">E-mail</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-max" id="lemail5" name="lemail5" placeholder="E-mail" value="{{ old('lemail5', (isset($data->lemail5))? $data->lemail5 : '') }}" autocomplete="off"  maxlength="255" >
                                </div>
                            </div>  
                        </fieldset>
                    </div>
                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        @if(isset($data))                    
                            <button type="button" onclick="submitForm('#deleteform')" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button>
                        @endif
                        <a href="/admin/agency">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}
            @if(isset($data))  
            {!! Form::open([ 'method'=>'delete', 'url'=>'/admin/agency/'.$data->agency, 'id'=>'deleteform']) !!}
    
            {!! Form::close() !!}
            @endif
        </div>
    </div>
    <!-- 行政機關代碼 modal -->
    <div class="modal fade bd-example-modal-lg enrollorge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog_120" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong id="popTitle">輸入查詢條件</strong></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="uv_enrollorg" name="uv_enrollorg" value="">
                    <div class="card-body pt-4 text-center">
                        <label for="enrollname">機關名稱</label>
                        <input type="text" class="form-control input-max" id="enrollname" name="enrollname"  value="" autocomplete="off"  maxlength="255">
                        <button type="button" class="btn btn-primary mt-2 mb-0" onclick="search()">查詢</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="tab">
                            <thead>
                            <tr>
                                <th>機關代號</th>
                                <th>機關名稱</th>
                            </tr>
                            </thead>
                            <tbody id="demandlist">               
                            </tbody>            
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="enrollorg()">確定</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
    <!-- 圖片 -->
    @include('admin/layouts/form/image')

@endsection
@section('js')
<script>

    function search(){
        if($('#enrollname').val()==''){
            alert('請輸入機關名稱');
            return false;
        }
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/agency/getenrollname",
            data: { enrollname : $('#enrollname').val() },
            success: function(data){
            let dataArr = JSON.parse(data);
            if(dataArr.status==1){
                alert(dataArr.msg);
                return false;
            }else if(dataArr.status==0){
                let tempHTML = "";
                console.log('length='+dataArr.msg.length);
                for(let i=0; i<dataArr.msg.length; i++) 
                {
                    tempHTML += "<tr onclick=getenrollorg('"+dataArr.msg[i].enrollorg+"')  id='"+dataArr.msg[i].enrollorg+"'>\
                        <td class='text-center classType_item'>"+dataArr.msg[i].enrollorg+"</td>\
                        <td class='text-center classType_item'>"+dataArr.msg[i].enrollname+"</td>\
                    </tr>";
                };
                $("#demandlist").html(tempHTML);                    
            }
            },
            error: function() {
                console.log('Ajax Error');
            }
        });
    }

    function getlist(){
        $(".enrollorge").modal('show');
    }

    function getenrollorg(enrollorge){
        var tab=document.getElementById('tab');
        var rows=tab.rows;
        var rlen=rows.length;
            for (var i = 1; i <rlen; i++) { //所有行清除
                rows[i].style.background='';
            }
        console.log(enrollorge);
        $("#"+enrollorge).css("background-color","#00BBFF");
        $('#uv_enrollorg').val(enrollorge);
    }

    function enrollorg(){
        var enrollorge = $('#uv_enrollorg').val();
        $('#enrollorg').val(enrollorge);
    }
</script>
@endsection