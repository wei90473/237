@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'teaching_material_statistics';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">教材交印處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/teaching_material_statistics" class="text-info">教材印製統計處理</a></li>
                        <li class="active">教材印製統計處理</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/teaching_material_statistics/edit/'.$data['serno'], 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/teaching_material_statistics/edit', 'id'=>'form']) !!}
            @endif
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">教材印製統計編輯</h3></div>
                    <div class="card-body pt-4">
                        <input type="hidden" name="class" value="{{ $queryData->class }}">
                        <input type="hidden" name="QUERY_STRING" value="{{$_SERVER['QUERY_STRING']}}">
                        <input type="hidden" name="term" value="{{ $queryData->term }}">
                        <fieldset style="border:groove; padding: inherit">
                            <div class="form-group row">
                                <label class="col-sm-3 ">班號：{{$queryData->class}}</label>
                                <label class="col-sm-2 ">期別：{{$queryData->term}}</label>
                                <label class="col-sm-4 ">辦班院區：{{ config('app.branch.'.$queryData->branch) }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">訓練班別：{{$queryData->name}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">分班名稱：{{$queryData->branchname}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 ">班別類型：{{ config('app.process.'.$queryData->process) }}</label>
                                <label class="col-sm-4 ">班務人員：{{$queryData->username}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">起迄期間：{{$queryData->sdate}}～{{$queryData->edate}}</label>
                            </div>
                        </fieldset>
                        <div class="form-group row pt-2">
                            <label class="col-md-2 col-form-label text-md-right">院區(主教室)</label>
                            <div class="col-md-4">
                                @if ( isset($data) )
                                <select id="branch" name="branch" class="browser-default custom-select" disabled>
                                @else
                                <select id="branch" name="branch" class="browser-default custom-select" >
                                @endif
                                    @foreach(config('app.branch') as $k => $va)
                                        <option value="{{ $k }}" {{ old('branch', (isset($data->branch))? $data->branch : $queryData->branch) == $k? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label class="control-label pt-2">總價</label>
                            <div class="input-group col-2">
                                <input type="text" id="total" name="total" class="form-control input-max" autocomplete="off" disabled value="{{ old('total', (isset($data['total']))? $data['total'] : '0') }}">
                            </div>
                        </div>
                        <!-- 教材名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">教材名稱</label>
                            <div class="input-group col-6">
                                <input type="text" id="material" name="material" class="form-control input-max" autocomplete="off" value="{{ old('material', (isset($data['material']))? $data['material'] : '') }}">
                            </div>
                            <label class="control-label pt-2">編號</label>
                            <div class="input-group col-2">
                                @if ( isset($data) )
                                <input type="text" id="serno" name="serno" class="form-control input-max" autocomplete="off" value="{{ $data['serno'] }}" readonly>
                                @else
                                <input type="text" id="serno" name="serno" class="form-control input-max" autocomplete="off" value="{{ $queryData->maxserno }}" readonly>
                                @endif
                            </div>
                        </div>
                        <!-- 總份數 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">總份數</label>
                            <div class="col-md-2">
                                <input type="text" id="copy" name="copy" class="form-control input-max" autocomplete="off" value="{{ old('copy', (isset($data['copy']))? $data['copy'] : '') }}">
                            </div>
                            <label class="col-form-label">張數</label>
                            <div class="col-md-2">
                                <input type="text" id="page" name="page" class="form-control input-max" autocomplete="off" value="{{ old('page', (isset($data['page']))? $data['page'] : '') }}">
                                <input type="hidden" name="newtotal" value="">
                            </div>
                            <div class="col-md-4">
                            @foreach(config('app.print') as $key => $va)
                                <input type="radio" name="print" value="1" {{ old('print', (isset($data->print))? $data->print : 1) == $key? 'checked' : '' }}>{{ $va }}
                            @endforeach
                            </div>
                        </div>
                        <!-- 需求 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">預定交貨日期</label>
                            <div class="col-md-2">
                                <input type="text" id="duedate" name="duedate" class="form-control" autocomplete="off" value="{{ old('duedate', isset($data['duedate'])?$data['duedate'] : ''  ) }}">
                            </div>
                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                            <div class="col-md-7">
                                <input type="radio"  name="duetime" value="1" {{ old('duetime', (isset($data->duetime))? $data->duetime : 1) == 1? 'checked' : '' }}>上午
                                <!-- <input type="radio"  name="duetime" value="2" {{ old('duetime', (isset($data->duetime))? $data->duetime : 1) == 2? 'checked' : '' }}>下午 -->
                                <input type="checkbox" id="typing" name="typing" value="Y" {{ old('typing', (isset($data->typing))? $data->typing : '') == 'Y'? 'checked' : '' }}>需要膠裝
                                <input type="checkbox" id="bind" name="bind" value="Y" {{ old('bind', (isset($data->bind))? $data->bind : '') == 'Y'? 'checked' : '' }}>需要裝訂
                                <input type="checkbox" id="punch" name="punch" value="Y" {{ old('punch', (isset($data->punch))? $data->punch : '') == 'Y'? 'checked' : '' }}>需要打孔
                                <!-- <input type="checkbox" id="fast" name="fast" value="Y" {{ old('fast', (isset($data->fast))? $data->fast : '') == 'Y'? 'checked' : '' }}>最速件 -->
                            </div>
                        </div>
                        <!-- 擬開支科目 -->
                        <div class="form-group row" id="Actual_classroom" style="display: flex;">
                            <label class="col-sm-2 control-label text-md-right pt-2">擬開支科目</label>
                            <div class="col-md-5">
                                <select class="browser-default custom-select" name="kind">
                                    <option value="">請選擇</option>
                                    @foreach($kindlist as $key => $va)
                                        <option value="{{ $va['acccode'] }}" {{  old('kind', (isset($data['kind']))? $data['kind']:'') == $va['acccode']? 'selected' : '' }}>{{ $va['accname'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <?php $list = $base->getSponsor(); ?>
                            <label class="control-label pt-2">申請單位</label>
                            <div class="col-md-3">
                                <select class="select2 form-control select2-single input-maxt" name="applicant">
                                    <option value="">請選擇</option>
                                    @foreach($list as $key => $va)
                                        <option value="{{ $key }}" {{ old('applicant', (isset($data['applicant']))? $data['applicant']:'') == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- 其他附註 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">備註</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="extranote" id="extranote" maxlength="255">{{ old('extranote', (isset($data['extranote']))? $data['extranote'] : '') }}</textarea>
                            </div>
                        </div>
                        <!-- 課程清單 -->
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr bgcolor="#99FFFF">
                                    <th>#</th>
                                    <th>項目</th>
                                    <th>單位</th>
                                    <th>合約單價</th>
                                    <th>數量</th>
                                    <th>份數</th>
                                    <th>印製總數</th>
                                    <th>小計</th>
                                    @if(isset($data))
                                    <th>備註</th>
                                    @else
                                    <th style="padding-left: 50px; padding-right: 50px;">備註</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                <?php $quantitytotal = 0; ?>
                                @foreach($datalist as $va)
                                    <tr>
                                        <td>{{ $va['title'] }}</td>
                                        <td>{{ $va['item'] }}</td>
                                        <td>{{ $va['unit'] }}</td>
                                        <td><input type="text" name="price{{$va['sequence']}}" class="form-control input-max" autocomplete="off" value="{{$va['price']}}" disabled></td>

                                        <td>
                                            <input type="text" name="quantity{{$va['sequence']}}" class="form-control input-max" autocomplete="off" value="{{$va['quantity']}}" onchange="Calculation({{$va['sequence']}})"></td>
                                            <input type="hidden" name="hidden{{$va['sequence']}}" value="{{$va['quantity']}}">
                                        <td>
                                            <input type="text" name="copy{{$va['sequence']}}" class="form-control input-max" autocomplete="off" value="{{$va['copy']}}" onchange="Calculation({{$va['sequence']}})"></td>
                                        <td>
                                            <input type="text" name="copyall{{$va['sequence']}}" class="form-control input-max" autocomplete="off" value="{{ number_format($va['quantity']*$va['copy']) }}" disabled></td>
                                        <td>
                                            <input type="text" name="total{{$va['sequence']}}" class="form-control input-max" autocomplete="off" value="{{ round($va['quantity']*$va['copy']*$va['price']) }}" disabled></td>
                                        <td>{{$va['remark']}}</td>
                                        <?php $quantitytotal = $quantitytotal + $va['quantity']; ?>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <!-- <span onclick="actionDelete()" data-toggle="modal" data-target="#del_modol" >
                        <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-save pr-2"></i>刪除</button>
                        </span> -->
                        <a href="/admin/teaching_material_statistics?{{$_SERVER['QUERY_STRING']}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
                        </a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
        $("#duedate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker1').click(function(){
            $("#duedate").focus();
        });

        $("#edate").datepicker( {
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker2').click(function(){
            $("#edate").focus();
        });

    });
    //刪除
    function actionDelete(){
        if (confirm('確定要刪除嗎')){
            $("#deleteform").submit();
        }
    }
    function Calculation(sequence){
        var price = $("input[name=price"+sequence+"]").val();
        var quantity = $("input[name=quantity"+sequence+"]").val();
        var copy = $("input[name=copy"+sequence+"]").val();
        var alltotal = $("input[name=total]").val();
        var oldtotal = $("input[name=total"+sequence+"]").val();
        if(copy == 0){
            copy = $("input[name=copy]").val();
            $("input[name=copy"+sequence+"]").val(copy);
        }
        var copyall = (quantity*copy);
        $("input[name=copyall"+sequence+"]").val(copyall);
        var total = Math.round(price*quantity*copy);
        if(oldtotal == 0){
            alltotal = parseInt(alltotal,10)+parseInt(total,10);
            $("input[name=total]").val(alltotal);
        }else{
            alltotal = parseInt(alltotal,10)-parseInt(oldtotal,10)+parseInt(total,10);
            $("input[name=total]").val(alltotal);
        }
        $("input[name=total"+sequence+"]").val(total);
        var oldquantity = $("input[name=hidden"+sequence+"]").val();

        if(oldquantity != quantity){ //自動計算張數
            var quantitytotal = <?=$quantitytotal?>; //原始加總
            if($("input[name=newtotal]").val()==''){
                var newtotal = 0;
                newtotal = parseInt(quantitytotal,10)+parseInt(quantity,10)- parseInt(oldquantity,10);
            }else{
                var newtotal = $("input[name=newtotal]").val();
                newtotal = parseInt(newtotal,10)+parseInt(quantity,10)- parseInt(oldquantity,10);
            }
            $("input[name=page]").val(newtotal);
            $("input[name=newtotal]").val(newtotal);
            $("input[name=hidden"+sequence+"]").val(quantity);
        }
    }
</script>