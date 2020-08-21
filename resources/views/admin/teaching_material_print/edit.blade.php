@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'teaching_material_print';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">教材交印處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/teaching_material_print" class="text-info">教材交印資料處理</a></li>
                        <li class="active">教材交印資料處理</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/teaching_material_print/edit/'.$queryData->class.$queryData->term.$queryData->course, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/teaching_material_print/edit', 'id'=>'form']) !!}
            @endif
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    @if(isset($data))
                    <div class="card-header"><h3 class="card-title">教材交印資料編輯</h3></div>
                    @else
                    <div class="card-header"><h3 class="card-title">教材交印資料新增</h3></div>
                    @endif
                    <div class="card-body pt-4">
                        <input type="hidden" name="class" value="{{ $queryData->class }}">
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
                                <select id="branch" name="branch" class="browser-default custom-select" onchange="getlist()">
                                @endif
                                    @foreach(config('app.branch') as $k => $va)
                                        <option value="{{ $k }}" {{ old('branch', (isset($data->branch))? $data->branch : $queryData->branch) == $k? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- 教材名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">教材名稱<span class="text-danger">*</span></label>
                            <div class="input-group col-6">
                                <input type="text" id="material" name="material" class="form-control input-max" autocomplete="off" value="{{ old('material', (isset($data['material']))? $data['material'] : '') }}" placeholder="教材名稱" required>
                                <button type="button" class="btn btn-light" onclick="materialpuls()">...</button>
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
                                <input type="text" id="page" name="page" class="form-control input-max" autocomplete="off" value="{{ old('page', (isset($data['page']))? $data['page'] : '') }}" readonly>
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
                                <input type="text" id="duedate" name="duedate" class="form-control" autocomplete="off" 
                                value="{{ old('duedate', ( isset($data['duedate'])?$data['duedate'] : (date('Y',strtotime('+3day'))-1911).date('md',strtotime('+3day')  ))) }}">
                            </div>
                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                            <div class="col-md-7">
                                <input type="radio"  name="duetime" value="1" {{ old('duetime', (isset($data->duetime))? $data->duetime : 1) == 1? 'checked' : '' }}>上午
                                <!-- <input type="radio"  name="duetime" value="2" {{ old('duetime', (isset($data->duetime))? $data->duetime : 1) == 2? 'checked' : '' }}>下午 -->
                                <input type="checkbox" id="typing" name="typing" value="Y" {{ old('typing', (isset($data->typing))? $data->typing : '') == 'Y'? 'checked' : '' }}>需要膠裝
                                <input type="checkbox" id="bind" name="bind" value="Y" {{ old('bind', (isset($data->bind))? $data->bind : '') == 'Y'? 'checked' : '' }}>需要裝訂
                                <input type="checkbox" id="punch" name="punch" value="Y" {{ old('punch', (isset($data->punch))? $data->punch : '') == 'Y'? 'checked' : '' }}>需要打孔
                                <input type="checkbox" id="fast" name="fast" value="Y" {{ old('fast', (isset($data->fast))? $data->fast : '') == 'Y'? 'checked' : '' }}>最速件
                            </div>
                        </div>
                        <!-- 擬開支科目 -->
                        <div class="form-group row" id="Actual_classroom" style="display: flex;">
                            <label class="col-sm-2 control-label text-md-right pt-2">擬開支科目<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <select class="select2 form-control select2-single input-max"  name="kind" id="kind"  onchange="getacccode()" required>
                                    <option value="">請選擇</option>
                                    @foreach($kindlist as $key => $va)
                                        <option value="{{ $va['acccode'] }}" {{  old('kind', (isset($data['kind']))? $data['kind']:'') == $va['acccode']? 'selected' : '' }} >{{ $va['accname'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <?php $list = $base->getSponsor(); ?>
                            <label class="control-label pt-2">申請單位<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <select class="select2 form-control select2-single input-max" name="applicant" id="applicant" required>
                                    <option value="">請選擇</option>
                                    @foreach($list as $key => $va)
                                        <option value="{{ $key }}" {{ old('applicant', (isset($data['applicant']))? $data['applicant']:'') == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- 委辦單位 -->
                        <div class="form-group row" id="kind_client">
                            <label class="col-md-2 col-form-label text-md-right">委辦單位</label>
                            <div class="col-md-3">
                                <input type="text" id="client" name="client" class="form-control input-max" autocomplete="off" value="{{ old('client', (isset($data['client']))? $data['client'] : '') }}" >
                            </div>
                            <label class="col-md-2 col-form-label text-md-right">發票</label>
                            <div class="col-md-3">
                                <select class="select2 form-control select2-single input-max" name="invoice" id="invoice" required>
                                    <option value="1" {{ old('invoice', (isset($data['invoice']))? $data['invoice']:'') == $key? 'selected' : '' }}>發票送回原單位</option>
                                    <option value="2" {{ old('invoice', (isset($data['invoice']))? $data['invoice']:'') == $key? 'selected' : '' }}>發票留存本學院</option>
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
                                @if(isset($data))
                                    @foreach($datalist as $va)
                                    <tr style="display: table-row;">
                                        <td>{{ $va['title'] }}</td>
                                        <td>{{ $va['item'] }}</td>
                                        <td>{{ $va['unit'] }}</td>
                                        <td>{{ $va['price'] }}</td>
                                        @if(isset($data))
                                        <td>{{ $va['quantity'] }}</td>
                                        <td>{{ $va['copy'] }}</td>
                                        <td>{{ number_format($va['quantity']*$va['copy']) }}</td>
                                        <td>{{ ($va['quantity']*$va['copy']*$va['price']) }}</td>
                                        @else
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        @endif
                                        <td><input type="text" name="remark{{$va['sequence']}}" class="form-control input-max" autocomplete="off" value="{{$va['remark']}}"></td>
                                    </tr>
                                    @endforeach
                                @else
                                    @foreach($datalist[1] as $va)
                                    <tr name="Taipei" style="display: table-row;">
                                        <td>{{ $va['title'] }}</td>
                                        <td>{{ $va['item'] }}</td>
                                        <td>{{ $va['unit'] }}</td>
                                        <td>{{ $va['price'] }}</td>
                                        @if(isset($data))
                                        <td>{{ $va['quantity'] }}</td>
                                        <td>{{ $va['copy'] }}</td>
                                        <td>{{ number_format($va['quantity']*$va['copy']) }}</td>
                                        <td>{{ ($va['quantity']*$va['copy']*$va['price']) }}</td>
                                        @else
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        @endif
                                        <td><input type="text" name="remark{{$va['sequence']}}" class="form-control input-max" autocomplete="off" value="{{$va['remark']}}"></td>
                                    </tr>
                                    @endforeach
                                    @foreach($datalist[2] as $va)
                                    <tr name="Nantou" style="display: none;">
                                        <td>{{ $va['title'] }}</td>
                                        <td>{{ $va['item'] }}</td>
                                        <td>{{ $va['unit'] }}</td>
                                        <td>{{ $va['price'] }}</td>
                                        @if(isset($data))
                                        <td>{{ $va['quantity'] }}</td>
                                        <td>{{ $va['copy'] }}</td>
                                        <td>{{ number_format($va['quantity']*$va['copy']) }}</td>
                                        <td>{{ ($va['quantity']*$va['copy']*$va['price']) }}</td>
                                        @else
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        @endif
                                        <td><input type="text" name="remark{{$va['sequence']}}" class="form-control input-max" autocomplete="off" value="{{$va['remark']}}"></td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        @if(isset($data))
                        <span onclick="actionDelete()" data-toggle="modal" data-target="#del_modol" >
                        <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-save pr-2"></i>刪除</button>
                        </span>
                        @endif
                        <a href="/admin/teaching_material_print/list/{{$queryData->class.$queryData->term}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
                        </a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            @if(isset($data))
                {!! Form::open([ 'method'=>'delete', 'url'=> '/admin/teaching_material_print/edit/'.$queryData->class.$queryData->term.$data['serno'], 'id'=>'deleteform']) !!}
                <input type="hidden" class="form-control " id="D_id" name="D_id"></input>
                {!! Form::close() !!}  
            @endif
        </div>
    </div>
    <!-- 教材名稱 modal -->
    <div class="modal fade bd-example-modal-lg materialpuls" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog_120" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong id="popTitle">教材名稱</strong></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="uv_term_course" name="uv_term_course" value="">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="tab">
                            <thead>
                            <tr>
                                <th>課程名稱</th>
                                <th>期別</th>
                                <th>課程編號</th>
                            </tr>
                            </thead>
                            <tbody>
                                @if($material!='')
                                @foreach($material as $key => $va)
                                <?php  echo '<tr onclick="getmaterial(\''.$va['term'].$va['course'].'\')"  id="'.$va['term'].$va['course'].'">' ?>
                                    <td>{{ $va['name'] }}</td>
                                    <td>{{ $va['term'] }}</td>
                                    <td>{{ $va['course'] }}</td>
                                </tr>  
                                @endforeach  
                                @endif           
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
@endsection
@section('js')
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
        getacccode();
        getlist();
    });    
    //刪除
    function actionDelete(){
        if (confirm('確定要刪除嗎')){
            $("#deleteform").submit();
        }
    }

    //教材名稱清單
    function materialpuls(){
        var check = <?=$material[0]['term']?>;
        if (check==''){
            alert('無課程資料');
            return false;
        }

        $(".materialpuls").modal('show');
    }
    function getmaterial(term_course){
        var name = $("#"+term_course).find('td').eq(0).text().trim();
        var tab=document.getElementById('tab');
        var rows=tab.rows;
        var rlen=rows.length;
            for (var i = 1; i <rlen; i++) { //所有行清除
                rows[i].style.background='';
            }
        $("#"+term_course).css("background-color","#00BBFF");
        $('#uv_term_course').val(name);
    }

    function enrollorg(){
        var material = $('#uv_term_course').val();
        $('#material').val(material);
    }

    function getlist(){
        var branch = $('#branch').val();
        if(branch=='1'){
            $('tr[name=Taipei]').css('display','table-row');
            $('tr[name=Nantou]').css('display','none');
        }else{
            $('tr[name=Nantou]').css('display','table-row');
            $('tr[name=Taipei]').css('display','none');
        }
    }

    function getacccode(){
        var kind = $('select[name=kind]').val();
        var html = '<option value="">不套用流程</option>';
        if(kind=='14'){
            $('#kind_client').show();
        }else{
            $('#kind_client').hide();
        }
    }

</script>
@endsection