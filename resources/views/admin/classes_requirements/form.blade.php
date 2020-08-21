@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'classes_requirements';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地管理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/classes_requirements" class="text-info">辦班需求(確認)處理</a></li>
                        <li class="active">辦班需求(確認)編輯</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'method'=>'put', 'url'=>'/admin/classes_requirements/edit/acccode/'.$queryData->class.$queryData->term, 'id'=>'form']) !!}
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">辦班需求(確認)編輯</h3></div>
                    <div class="card-body pt-4">
                        <fieldset style="border:groove; padding: inherit">
                            <input type="hidden" name="class" value="{{ $queryData->class }}">
                            <input type="hidden" name="term" value="{{ $queryData->term }}">
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
                                <label class="col-sm-10 ">委訓機關：{{$queryData->commission}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">起迄期間：{{$queryData->sdate}}～{{$queryData->edate}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 ">班別類型：{{ config('app.process.'.$queryData->process) }}</label>
                                <label class="col-sm-4 ">班務人員：{{$queryData->username}}</label>
                            </div>
                        </fieldset>
                        <!-- 開支科目 -->
                        <div class="float-md mobile-100 row br-1 pt-1">
                            <div class="input-group col-6">
                                <span class="input-group-text">開支科目</span>
                                <select class="browser-default custom-select" name="acccode">
                                    <option value="" >請選擇</option>
                                    @foreach($Expenditure as $key => $va)
                                        <option  value="{{ $va['acccode'] }}" {{ (isset($queryData->kind)?$queryData->kind:'' ) == $va['acccode']? 'selected' : '' }}>{{ $va['accname'] }}</option>
                                    @endforeach
                                </select>
                                <!-- 更新開支科目 -->
                                <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                     
                            </div>
                        </div>
                        <div class="float-md-left pt-1">
                            <button type="button" class="btn btn-primary btn-sm mb-3" data-toggle="modal" data-target="#creat">新增</button>
                            <!-- <button type="button" class="btn btn-primary btn-sm mb-3" style=" margin-right:10px;" data-toggle="modal" data-target="#group" >批次增刪</button> -->
                            @if($queryData->branch =='1')
                                <button type="button" class="btn btn-primary btn-sm mb-3" data-toggle="modal" onclick="groupstore()">批次新增</button>
                                <button type="button" class="btn btn-danger btn-sm mb-3" data-toggle="modal" onclick="groupdestroy()">批次刪除</button>
                                <button type="button" class="btn btn-primary btn-sm mb-3" data-toggle="modal" onclick="groupupdate()">更新人數</button>
                            @elseif($queryData->branch=='2')
                                <button type="button" class="btn btn-primary btn-sm mb-3" data-toggle="modal" data-target="#stopcook" >新增止伙資料</button>
                                <button type="button" class="btn btn-primary btn-sm mb-3" data-toggle="modal" data-target="#stopcooklist">止伙明細</button>
                            @endif
                        </div>
                        <!-- 清單 -->
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                @if($queryData->branch =='1')    
                                <tr>
                                    <th class="text-center" width="140">功能</th>
                                    <th>日期</th>
                                    <th>單人房</th>
                                    <th>雙人單床房</th>
                                    <th>雙人雙床房</th>
                                    <th>愛心房</th>
                                    <th>早餐</th>
                                    <th>早素</th>
                                    <th>午餐</th>
                                    <th>午素</th>
                                    <th>晚餐</th>
                                    <th>晚素</th>
                                    <th>訂席桌餐餐種</th>
                                    <th>訂席桌餐人數</th>
                                    <th>訂席桌餐素食</th>
                                    <th>訂席桌餐單價</th>
                                    <th>自助餐餐種</th>
                                    <th>自助餐人數</th>
                                    <th>自助餐素食</th>
                                    <th>茶點人數</th>
                                    <th>茶點單價</th>
                                    <th>茶點時間</th>
                                    <th>其他餐點</th>
                                    <th>場租</th>
                                </tr>
                                @elseif($queryData->branch=='2')
                                <tr>
                                    <th class="text-center" width="140">功能</th>
                                    <th>日期</th>
                                    <th>報名人數(葷)</th>
                                    <th>報名人數(素)</th>
                                    <th>報到人數(葷)</th>
                                    <th>報到人數(素)</th>
                                    <th>業務輔導員人數(葷)</th>
                                    <th>業務輔導員人數(素)</th>
                                    <th>晚餐不用餐人數</th>
                                    <th>提前住宿人數</th>
                                    <th>退訓人數(葷)</th>
                                    <th>退訓人數(素)</th>
                                    <th>結訓餐盒</th>
                                    <th>兩週班以上周一用早餐人數</th>
                                </tr>
                                @else
                                @endif
                                </thead>
                                <tbody>
                                @if(isset($data))
                                <?php $now = (date('Y',strtotime('now'))-1911).date('md',strtotime('now'));?>
                                    @if($queryData->branch =='1')  
                                    @foreach($data as $key => $va)
                                        <tr id="tr{{$key}}" class="text-center" 
                                            data-date="{{ $va->date }}" 
                                            data-sincnt="{{ $va->sincnt }}" 
                                            data-donecnt="{{ $va->donecnt }}" 
                                            data-dtwocnt="{{ $va->dtwocnt }}"
                                            data-lovecnt="{{ $va->lovecnt }}"
                                            data-meacnt="{{ $va->meacnt }}" 
                                            data-meavegan="{{ $va->meavegan }}"
                                            data-luncnt="{{ $va->luncnt }}"
                                            data-lunvegan="{{ $va->lunvegan }}"
                                            data-dincnt="{{ $va->dincnt }}"
                                            data-dinvegan="{{ $va->dinvegan }}"
                                            data-tabtype="{{ $va->tabtype }}"
                                            data-tabcnt="{{ $va->tabcnt }}"
                                            data-tabvegan="{{ $va->tabvegan }}"
                                            data-tabunit="{{ $va->tabunit }}" 
                                            data-buftype="{{ $va->buftype }}" 
                                            data-bufcnt="{{ $va->bufcnt }}" 
                                            data-bufvegan="{{ $va->bufvegan }}" 
                                            data-teacnt="{{ $va->teacnt }}" 
                                            data-teaunit="{{ $va->teaunit }}" 
                                            data-teatime="{{ $va->teatime }}" 
                                            data-otheramt="{{ $va->otheramt }}" 
                                            data-siteamt="{{ $va->siteamt }}" 
                                               > 
                                            <!-- <td>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="copy({{$key}})">複製</button>
                                            </td> -->
                                            
                                            <td class="text-center" width="140">
                                                @if( $va->date >= $now || $authority==1)
                                                <button type="button" class="btn btn-sm btn-info" onclick="editT({{$key}})">編輯</button>
                                                <button type="button" class="btn btn-sm btn-info" onclick="copyT({{$key}})">複製</button>
                                                @endif
                                            </td>
                                            @if( $va->date >= $now)
                                            <td>{{ $va->date }}</td>
                                            @else
                                            <td ><font color="red">{{ $va->date }}</font></td>
                                            @endif
                                            <td>{{ is_null($va->sincnt)?0:$va->sincnt }}</td>
                                            <td>{{ is_null($va->donecnt)?0:$va->donecnt }}</td>
                                            <td>{{ is_null($va->dtwocnt)?0:$va->dtwocnt }}</td>
                                            <td>{{ is_null($va->lovecnt)?0:$va->lovecnt }}</td>
                                            <td>{{ is_null($va->meacnt)?0:$va->meacnt }}</td>
                                            <td>{{ is_null($va->meavegan)?0:$va->meavegan }}</td>
                                            <td>{{ is_null($va->luncnt)?0:$va->luncnt }}</td>
                                            <td>{{ is_null($va->lunvegan)?0:$va->lunvegan }}</td>
                                            <td>{{ is_null($va->dincnt)?0:$va->dincnt }}</td>
                                            <td>{{ is_null($va->dinvegan)?0:$va->dinvegan }}</td>
                                            <td>{{ $va->tabtype==1? '1 午餐': ($va->tabtype==2? '2 晚餐' : '') }}</td>
                                            <td>{{ is_null($va->tabcnt)?0:$va->tabcnt }}</td>
                                            <td>{{ is_null($va->tabvegan)?0:$va->tabvegan }}</td>
                                            <td>{{ is_null($va->tabunit)?0:$va->tabunit }}</td>
                                            <td>{{ $va->buftype==1? '1 午餐': ($va->buftype==2? '2 晚餐' : '')  }}</td>
                                            <td>{{ is_null($va->bufcnt)?0:$va->bufcnt }}</td>
                                            <td>{{ is_null($va->bufvegan)?0:$va->bufvegan }}</td>
                                            <td>{{ is_null($va->teacnt)?0:$va->teacnt }}</td>
                                            <td>{{ is_null($va->teaunit)?0:$va->teaunit }}</td>
                                            <td>{{ is_null($va->teatime)?'':$va->teatime }}</td>
                                            <td>{{ is_null($va->otheramt)?0:$va->otheramt }}</td>
                                            <td>{{ is_null($va->siteamt)?0:$va->siteamt }}</td>
                                        </tr>
                                    @endforeach
                                    @elseif($queryData->branch=='2')
                                    @foreach($data as $key => $va)
                                        <tr id="tr{{$key}}" class="text-center" 
                                            data-date="{{ $va->date }}" 
                                            data-signupcnt="{{ $va->signupcnt }}" 
                                            data-signupvegan="{{ $va->signupvegan }}" 
                                            data-checkincnt="{{ $va->checkincnt }}"
                                            data-checkinvegan="{{ $va->checkinvegan }}" 
                                            data-counselorcnt="{{ $va->counselorcnt }}"
                                            data-counselorvegan="{{ $va->counselorvegan }}"
                                            data-nodincnt="{{ $va->nodincnt }}"
                                            data-earlystaycnt="{{ $va->earlystaycnt }}"
                                            data-cropoutcnt="{{ $va->cropoutcnt }}"
                                            data-cropoutvegan="{{ $va->cropoutvegan }}"
                                            data-cropoutvegan="{{ $va->cropoutvegan }}"
                                            data-endbento="{{ $va->endbento }}"
                                            data-blfcnt="{{ $va->blfcnt }}"    >
                                            <!-- 編輯 -->
                                            <td>
                                                @if( $va->date >= $now || $authority==1)
                                                <button type="button" class="btn btn-sm btn-info" onclick="editN({{$key}})">編輯</button>
                                                <button type="button" class="btn btn-sm btn-info" onclick="copyN({{$key}})">複製</button>
                                                @endif
                                            </td>
                                            @if( $va->date >= $now)
                                            <td>{{ $va->date }}</td>
                                            @else
                                            <td ><font color="red">{{ $va->date }}</font></td>
                                            @endif
                                            <td>{{ is_null($va->signupcnt)?0:$va->signupcnt }}</td>
                                            <td>{{ is_null($va->signupvegan)?0:$va->signupvegan }}</td>
                                            <td>{{ is_null($va->checkincnt)?0:$va->checkincnt }}</td>
                                            <td>{{ is_null($va->checkinvegan)?0:$va->checkinvegan }}</td>
                                            <td>{{ is_null($va->counselorcnt)?0:$va->counselorcnt }}</td>
                                            <td>{{ is_null($va->counselorvegan)?0:$va->counselorvegan }}</td>
                                            <td>{{ is_null($va->nodincnt)?0:$va->nodincnt }}</td>
                                            <td>{{ is_null($va->earlystaycnt)?0:$va->earlystaycnt }}</td>
                                            <td>{{ is_null($va->cropoutcnt)?0:$va->cropoutcnt }}</td>
                                            <td>{{ is_null($va->cropoutvegan)?0:$va->cropoutvegan }}</td>
                                            <td>{{ $va->endbento=='Y'? '是': ($va->endbento=='N'? '否' : '')  }}</td>
                                            <td>{{ is_null($va->blfcnt)?0:$va->blfcnt }}</td>
                                        </tr>
                                    @endforeach
                                    @else
                                    @endif
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        <!-- 更新開支科目 -->
                        <!-- <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button> -->
                        <!-- <a href="/admin/classes_requirements"> -->
                            <button type="button" class="btn btn-sm btn-danger" onclick="history.go(-1)" ><i class="fa fa-reply"></i> 回上一頁</button>
                        <!-- </a> -->
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')
    <!-- 止伙 -->
    <div class="modal fade" id="stopcook" role="dialog">
        {!! Form::open([ 'method'=>'post', 'url'=>'/admin/classes_requirements/edit/stopcook', 'id'=>'form3']) !!}
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog"  role="document"  style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">新增止伙</h4>
                    </div>
                    <div class="modal-body">
                        <div class="input-group pt-1 col-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text">班號：</span>
                            </div>
                            <input type="text" name="class" class="form-control" value="{{ $queryData->class }}" readonly>
                            <div class="input-group-prepend">
                                <span class="input-group-text">期別：</span>
                            </div>
                            <input type="text" name="term" class="form-control" value="{{ $queryData->term }}" readonly>
                        </div>
                        <div class="input-group pt-1 col-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text">班別名稱：</span>
                            </div>
                            <input type="text"  class="form-control" value="{{ $queryData->name }}" readonly>
                            <div class="input-group-prepend">
                                <span class="input-group-text">分班名稱：</span>
                            </div>
                            <input type="text"  class="form-control" value="{{$queryData->branchname}}" readonly>
                        </div>
                        <!-- 止伙日期 -->
                        <div class="input-group pt-1 col-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text">止伙日期</span>
                            </div>
                            <input type="text" id="stopdate" name="stopdate" class="form-control" autocomplete="off" value="{{date('Y',strtotime('+1day'))-1911).date('md',strtotime('+1day')}}">
                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                        </div>
                        
                        <div class="input-group pt-1 col-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text">餐別</span>
                            </div>
                            <select name="cooktype" class="browser-default custom-select" >
                                <option value="1">1 早餐</option>
                                <option value="2">2 午餐</option>
                                <option value="3">3 晚餐</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal"  onclick="submitForm('#form3');">儲存</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- 止伙明細 -->
    <div class="modal fade" id="stopcooklist" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog"  role="document"  style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">止伙明細</h4>
                    </div>
                    <div class="modal-body">
                        <div class="input-group pt-1 col-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text">班號：</span>
                            </div>
                            <input type="text" name="class" class="form-control" value="{{ $queryData->class }}" readonly>
                            <div class="input-group-prepend">
                                <span class="input-group-text">期別：</span>
                            </div>
                            <input type="text" name="term" class="form-control" value="{{ $queryData->term }}" readonly>
                        </div>
                        <div class="input-group pt-1 col-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text">班別名稱：</span>
                            </div>
                            <input type="text"  class="form-control" value="{{ $queryData->name }}" readonly>
                            <div class="input-group-prepend">
                                <span class="input-group-text">分班名稱：</span>
                            </div>
                            <input type="text"  class="form-control" value="{{$queryData->branchname}}" readonly>
                        </div>
                        </br>
                        <!-- 止伙日期 -->
                        <div class="input-group pt-1 col-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">止伙日期</th>
                                        <th class="text-center">餐別</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($stopcooklist))
                                        @foreach($stopcooklist as $drow)
                                        <tr class="text-center">
                                            <td>{{substr($drow['stopdate'],0,3)}}/{{substr($drow['stopdate'],3,2)}}/{{substr($drow['stopdate'],5,2)}}</td>
                                            <td>{{$drow['cooktype']=='1'?'早餐':($drow['cooktype']=='2'? '午餐':'晚餐')}}</td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 新增 -->
    <div class="modal fade" id="creat" role="dialog">
        {!! Form::open([ 'method'=>'post', 'url'=>'/admin/classes_requirements/edit', 'id'=>'form1']) !!}
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog"  role="document"  style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">新增</h4>
                    </div>
                    <input type="hidden" name="class" value="{{ $queryData->class }}">
                    <input type="hidden" name="term" value="{{ $queryData->term }}">
                    <input type="hidden" name="branch" value="{{ $queryData->branch }}">
                    <div class="modal-body">
                        <!-- 開訓日期範圍 -->
                        <div class="input-group pt-1 col-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text">請選擇上課日期</span>
                            </div>
                            <input type="text" id="date" name="date" class="form-control" autocomplete="off" value="">
                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                        </div>
                        @if($queryData->branch =='1')  
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">單人房</span>
                                </div>
                                <input type="text" name="sincnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">雙人單床房</span>
                                </div>
                                <input type="text" name="donecnt" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">雙人雙床房</span>
                                </div>
                                <input type="text" name="dtwocnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">愛心房</span>
                                </div>
                                <input type="text" name="lovecnt" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">早餐</span>
                                </div>
                                <input type="text" name="meacnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">早素</span>
                                </div>
                                <input type="text" name="meavegan" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">午餐</span>
                                </div>
                                <input type="text" name="luncnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">午素</span>
                                </div>
                                <input type="text" name="lunvegan" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">晚餐</span>
                                </div>
                                <input type="text" name="dincnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">晚素</span>
                                </div>
                                <input type="text" name="dinvegan" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">訂席桌餐餐種</span>
                                </div>
                                <select name="tabtype" class="browser-default custom-select" >
                                    <option value="">請選擇</option>
                                    <option value="1">1 午餐</option>
                                    <option value="2">2 晚餐</option>
                                </select>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">訂席桌餐人數</span>
                                </div>
                                <input type="text" name="tabcnt" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">訂席桌餐(素)</span>
                                </div>
                                <input type="text" name="tabvegan" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">訂席桌餐單價</span>
                                </div>
                                <input type="text" name="tabunit" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">自助餐餐種</span>
                                </div>
                                <select name="buftype" class="browser-default custom-select" >
                                    <option value="">請選擇</option>
                                    <option value="1">1 午餐</option>
                                    <option value="2">2 晚餐</option>
                                </select>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">自助餐人數</span>
                                </div>
                                <input type="text" name="bufcnt" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">自助餐(素)</span>
                                </div>
                                <input type="text" name="bufvegan" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">茶點人數</span>
                                </div>
                                <input type="text" name="teacnt" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">茶點單價</span>
                                </div>
                                <input type="text" name="teaunit" class="form-control col-sm-2" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">茶點時間</span>
                                </div>
                                <input type="text" name="teatime" class="form-control" value="">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">其他餐點</span>
                                </div>
                                <input type="text" name="otheramt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">場租</span>
                                </div>
                                <input type="text" name="siteamt" class="form-control" value="0">
                            </div>
                        @elseif($queryData->branch=='2')
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">報名人數(葷)</span>
                                </div>
                                <input type="text" name="signupcnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">報名人數(素)</span>
                                </div>
                                <input type="text" name="signupvegan" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">報到人數(葷)</span>
                                </div>
                                <input type="text" name="checkincnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">報到人數(素)</span>
                                </div>
                                <input type="text" name="checkinvegan" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">業務輔導員人數(葷)</span>
                                </div>
                                <input type="text" name="counselorcnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">業務輔導員人數(素)</span>
                                </div>
                                <input type="text" name="counselorvegan" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">晚餐不用餐人數</span>
                                </div>
                                <input type="text" name="nodincnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">提前住宿人數</span>
                                </div>
                                <input type="text" name="earlystaycnt" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">退訓人數(葷)</span>
                                </div>
                                <input type="text" name="cropoutcnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">退訓人數(素)</span>
                                </div>
                                <input type="text" name="cropoutvegan" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">結訓餐盒</span>
                                </div>
                                <input type="hidden" name="endbento" class="form-control" value="0">
                                <select name="endbento" class="browser-default custom-select" >
                                    <option value="">請選擇</option>
                                    <option value="Y">是</option>
                                    <option value="N">否</option>
                                </select>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">兩週班以上周一用早餐人數</span>
                                </div>
                                <input type="text" name="blfcnt" class="form-control" value="0">
                            </div>    
                        @else
                        @endif

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="checkcreat();" >確認</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- 修改 -->
    <div class="modal fade" id="edit" role="dialog">
        {!! Form::open([ 'method'=>'put', 'url'=>'/admin/classes_requirements/update/9999', 'id'=>'form4']) !!}
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog"  role="document"  style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">編輯</h4>
                    </div>
                    <input type="hidden" name="class" value="{{ $queryData->class }}">
                    <input type="hidden" name="term" value="{{ $queryData->term }}">
                    <input type="hidden" name="branch" value="{{ $queryData->branch }}">
                    <div class="modal-body">
                        <!-- 開訓日期範圍 -->
                        <div class="input-group pt-1 col-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text">上課日期</span>
                            </div>
                            <input type="text" name="date" class="form-control" autocomplete="off" value="" readonly>
                            <select name="date" class="browser-default custom-select" >
                                @foreach($data as $key => $va)
                                    <option value="{{ $va->date }}">{{ $va->date }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($queryData->branch =='1')  
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">單人房</span>
                                </div>
                                <input type="text" name="sincnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">雙人單床房</span>
                                </div>
                                <input type="text" name="donecnt" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">雙人雙床房</span>
                                </div>
                                <input type="text" name="dtwocnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">愛心房</span>
                                </div>
                                <input type="text" name="lovecnt" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">早餐</span>
                                </div>
                                <input type="text" name="meacnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">早素</span>
                                </div>
                                <input type="text" name="meavegan" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">午餐</span>
                                </div>
                                <input type="text" name="luncnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">午素</span>
                                </div>
                                <input type="text" name="lunvegan" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">晚餐</span>
                                </div>
                                <input type="text" name="dincnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">晚素</span>
                                </div>
                                <input type="text" name="dinvegan" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">訂席桌餐餐種</span>
                                </div>
                                <input type="hidden" name="tabtype" class="form-control" autocomplete="off" value="" disabled>
                                <select name="tabtype" class="browser-default custom-select" >
                                    <option value="">請選擇</option>
                                    <option value="1">1 午餐</option>
                                    <option value="2">2 晚餐</option>
                                </select>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">訂席桌餐人數</span>
                                </div>
                                <input type="text" name="tabcnt" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">訂席桌餐(素)</span>
                                </div>
                                <input type="text" name="tabvegan" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">訂席桌餐單價</span>
                                </div>
                                <input type="text" name="tabunit" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">自助餐餐種</span>
                                </div>
                                <input type="hidden" name="buftype" class="form-control" autocomplete="off" value="" disabled>
                                <select name="buftype" class="browser-default custom-select" >
                                    <option value="">請選擇</option>
                                    <option value="1">1 午餐</option>
                                    <option value="2">2 晚餐</option>
                                </select>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">自助餐人數</span>
                                </div>
                                <input type="text" name="bufcnt" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">自助餐(素)</span>
                                </div>
                                <input type="text" name="bufvegan" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">茶點人數</span>
                                </div>
                                <input type="text" name="teacnt" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">茶點單價</span>
                                </div>
                                <input type="text" name="teaunit" class="form-control col-sm-2" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">茶點時間</span>
                                </div>
                                <input type="text" name="teatime" class="form-control" value="">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">其他餐點</span>
                                </div>
                                <input type="text" name="otheramt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">場租</span>
                                </div>
                                <input type="text" name="siteamt" class="form-control" value="0">
                            </div>
                        @elseif($queryData->branch=='2')
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">報名人數(葷)</span>
                                </div>
                                <input type="text" name="signupcnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">報名人數(素)</span>
                                </div>
                                <input type="text" name="signupvegan" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">報到人數(葷)</span>
                                </div>
                                <input type="text" name="checkincnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">報到人數(素)</span>
                                </div>
                                <input type="text" name="checkinvegan" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">業務輔導員人數(葷)</span>
                                </div>
                                <input type="text" name="counselorcnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">業務輔導員人數(素)</span>
                                </div>
                                <input type="text" name="counselorvegan" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">晚餐不用餐人數</span>
                                </div>
                                <input type="text" name="nodincnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">提前住宿人數</span>
                                </div>
                                <input type="text" name="earlystaycnt" class="form-control" value="0">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">報到人數(葷)</span>
                                </div>
                                <input type="text" name="cropoutcnt" class="form-control" value="0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">報到人數(素)</span>
                                </div>
                                <input type="text" name="cropoutvegan" class="form-control" value="0" readonly="">
                            </div>
                            <div class="input-group pt-1 col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">結訓餐盒</span>
                                </div>
                                <input type="hidden" name="endbento" class="form-control" value="0">
                                <select name="endbento" class="browser-default custom-select" >
                                    <option value="">請選擇</option>
                                    <option value="Y">是</option>
                                    <option value="N">否</option>
                                </select>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">兩週班以上周一用早餐人數</span>
                                </div>
                                <input type="text" name="blfcnt" class="form-control" value="0">
                            </div>    
                        @else
                        @endif

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"  id="btn_updata" data-dismiss="modal" onclick="checkupdata();" >確認</button>
                        <button type="button" class="btn btn-primary"  id="btn_copy" data-dismiss="modal" onclick="checkupdata();" >貼上資料</button>
                        <button type="button" class="btn btn-danger" id="btn_dle" data-dismiss="modal" onclick="destroy()">刪除</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- 批次增刪 -->
    <div class="modal fade" id="group" role="dialog">
        {!! Form::open([ 'method'=>'post', 'url'=>'/admin/classes_requirements/edit/group', 'id'=>'form2']) !!}
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">批次增刪</h4>
                    </div>
                    <div class="modal-body">
                        <!-- 開訓日期範圍 -->
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">班別/會議</span>
                            </div>
                            <select class="browser-default custom-select" name="groupclass" onchange="getTerms(this.value)">
                                <option value="" >請選擇</option>
                                @if(isset($list))
                                    @foreach($list as $k => $v)
                                        <option value="{{ $v['class'] }}" {{ (isset($queryData['class'])?$queryData['class']:'' ) == $v['class']? 'selected' : '' }}>{{ $v['class'].$v['branchcode'].'  '.$v['name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <br>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">期別/編號</span>
                            </div>
                            <input type="hidden" name="groupterm" value="">
                            <!-- <select class="browser-default custom-select" name="groupterm">
                                <option value="" >請選擇</option>
                            </select> -->
                        </div>
                        <br>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">開支科目</span>
                            </div>
                            <select class="browser-default custom-select" name="groupacccode" required>
                                <option value="" >請選擇</option>
                                @foreach($Expenditure as $key => $va)
                                    <option  value="{{ $va['acccode'] }}" >{{ $va['accname'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="groupstore()">週確認新增</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="groupdestroy()">批次刪除</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>

                </div>
            </div>
        </div>
        {!! Form::close() !!}
        <!-- 刪除 -->
        {!! Form::open([ 'method'=>'delete', 'url'=>'/admin/classes_requirements/edit/group/999', 'id'=>'deleteform']) !!}
        <input type="hidden" class="form-control " id="D_code" name="D_code"></input>
        {!! Form::close() !!}
    </div>
    <!-- 複製** -->
    <div class="modal fade" id="copy" role="dialog">
        {!! Form::open([ 'method'=>'post', 'url'=>'/admin/classes_requirements/edit/copy', 'id'=>'form5']) !!}
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog"  role="document"  style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">複製</h4>
                    </div>
                    <input type="hidden" name="class" value="{{ $queryData->class }}">
                    <input type="hidden" name="term" value="{{ $queryData->term }}">
                    <input type="hidden" name="branch" value="{{ $queryData->branch }}">
                    <div class="modal-body">
                        <div class="input-group pt-1 col-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text">複製日期：</span>
                            </div>
                            <input type="text" name="fromdate" class="form-control" value="" readonly>
                        </div>
                        <div class="input-group pt-1 col-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text">目標日期：</span>
                            </div>
                            <input type="text" id="targetdate" name="targetdate" class="form-control" autocomplete="off" value="">
                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker3"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="copy()">貼上資料</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
            $("#date").datepicker({
                format: "twymmdd",
                language: 'zh-TW'
            });
            $('#datepicker1').click(function(){
                $("#date").focus();
            });

            $("#stopdate").datepicker({
                format: "twymmdd",
                language: 'zh-TW'
            });
            $('#datepicker2').click(function(){
                $("#stopdate").focus();
            });

            $("#targetdate").datepicker({
                format: "twymmdd",
                language: 'zh-TW'
            });
            $('#datepicker3').click(function(){
                $("#targetdate").focus();
            });
        var _class = $("input[name=class]").val();    
        // getTerms(_class);   
    });
    //取得編號
    // function getTerms(class_no){

    //     if(class_no==null){
    //         return false;
    //     }
    //     $.ajax({
    //         url: "/admin/schedule/getTerms/" + class_no
    //     }).done(function(response) {
    //         console.log(response);
    //         var select_term = $("select[name=groupterm]");
    //         select_term.html("");
    //         for(var i = 0; i<response.terms.length; i++){
    //             select_term.append("<option value='"+ response.terms[i] +"'>" + response.terms[i] + "</option>");
    //         }
    //     });
    // }

    //刪除
    function destroy(){
        var _class = $("input[name=class]").val();
        var _term = $("input[name=term]").val();
        var _date = $("input[name=D_code]").val();
        if( _date !='' ){
            if (confirm("確定要刪除" + _date +"的資料嗎")){
                $("#deleteform").attr('action', '/admin/classes_requirements/edit/'+_class+_term+_date);
                $("#deleteform").submit();
            }
        }else{
            alert('請選擇刪除班別!!');
            return ;
        }
    }
    // 編輯_台北
    function editT(key){
        var tr = document.getElementById('tr'+key);
        var authority = <?=$authority?>;
        var now = <?=$now?>;
        var dt = new Date();
        console.log(tr.dataset);
        $("select[name=date]").val(tr.dataset.date).trigger("change").attr('disabled','').hide();
        $("input[name=date]").val(tr.dataset.date).show().removeAttr('disabled'); 
        $("input[name=D_code]").val(tr.dataset.date);
        $("input[name=sincnt]").val(tr.dataset.sincnt).removeAttr('readonly'); 
        $("input[name=donecnt]").val(tr.dataset.donecnt).removeAttr('readonly'); 
        $("input[name=dtwocnt]").val(tr.dataset.dtwocnt).removeAttr('readonly'); 
        $("input[name=lovecnt]").val(tr.dataset.lovecnt).removeAttr('readonly');
        $("input[name=dincnt]").val(tr.dataset.dincnt).removeAttr('readonly'); 
        $("input[name=dinvegan]").val(tr.dataset.dinvegan).removeAttr('readonly'); 
        if(authority ==1 || tr.dataset.date > now){
            $("input[name=meacnt]").val(tr.dataset.meacnt).removeAttr('readonly'); 
            $("input[name=meavegan]").val(tr.dataset.meavegan).removeAttr('readonly'); 
            $("input[name=luncnt]").val(tr.dataset.luncnt).removeAttr('readonly'); 
            $("input[name=lunvegan]").val(tr.dataset.lunvegan).removeAttr('readonly'); 
            $("select[name=tabtype]").val(tr.dataset.tabtype).trigger("change").removeAttr('disabled');
            $("input[name=tabtype]").val(tr.dataset.tabtype).attr('disabled','');
            $("input[name=tabcnt]").val(tr.dataset.tabcnt).removeAttr('readonly'); 
            $("input[name=tabvegan]").val(tr.dataset.tabvegan).removeAttr('readonly'); 
            $("input[name=tabunit]").val(tr.dataset.tabunit).removeAttr('readonly'); 
            $("select[name=buftype]").val(tr.dataset.buftype).trigger("change").removeAttr('disabled');
            $("input[name=buftype]").val(tr.dataset.buftype).attr('disabled','');
            $("input[name=bufcnt]").val(tr.dataset.bufcnt).removeAttr('readonly'); 
            $("input[name=bufvegan]").val(tr.dataset.bufvegan).removeAttr('readonly'); 
            $("input[name=teacnt]").val(tr.dataset.teacnt).removeAttr('readonly'); 
            $("input[name=teaunit]").val(tr.dataset.teaunit).removeAttr('readonly'); 
            $("input[name=teatime]").val(tr.dataset.teatime).removeAttr('readonly'); 
            $("input[name=otheramt]").val(tr.dataset.otheramt).removeAttr('readonly'); 
            $("input[name=siteamt]").val(tr.dataset.siteamt).removeAttr('readonly'); 
        }else if( tr.dataset.date == now){
            var DeadLineHours1 = '9';  //D1 0940
            var DeadLineMinutes1 = '40';
            var DeadLineHours2 = '14';  //D2 1400
            var NowHours = dt.getHours();
            var NowMinutes = dt.getMinutes();
            $("input[name=meacnt]").val(tr.dataset.meacnt).attr('readonly',''); 
            $("input[name=meavegan]").val(tr.dataset.meavegan).attr('readonly',''); 
            $("input[name=teacnt]").val(tr.dataset.teacnt).attr('readonly',''); 
            $("input[name=teaunit]").val(tr.dataset.teaunit).attr('readonly',''); 
            $("input[name=teatime]").val(tr.dataset.teatime).attr('readonly',''); 
            $("input[name=otheramt]").val(tr.dataset.otheramt).attr('readonly',''); 
            $("input[name=siteamt]").val(tr.dataset.siteamt).attr('readonly',''); 
            if(NowHours >= DeadLineHours2){ /*超過D2 */
                alert('日期:'+tr.dataset.date+'已過日異動時間，無法編輯');
                return false;
            }else if(NowHours == DeadLineHours1 && NowMinutes > DeadLineMinutes1){ /*超過D1 */
                $("input[name=luncnt]").val(tr.dataset.luncnt).attr('readonly',''); 
                $("input[name=lunvegan]").val(tr.dataset.lunvegan).attr('readonly',''); 
                $("select[name=tabtype]").val(tr.dataset.tabtype).trigger("change").attr('disabled',''); 
                $("input[name=tabtype]").val(tr.dataset.tabtype).removeAttr('disabled');
                $("input[name=tabcnt]").val(tr.dataset.tabcnt).attr('readonly',''); 
                $("input[name=tabvegan]").val(tr.dataset.tabvegan).attr('readonly',''); 
                $("input[name=tabunit]").val(tr.dataset.tabunit).attr('readonly',''); 
                $("select[name=buftype]").val(tr.dataset.buftype).trigger("change").attr('disabled',''); 
                $("input[name=buftype]").val(tr.dataset.buftype).removeAttr('disabled');
                $("input[name=bufcnt]").val(tr.dataset.bufcnt).attr('readonly',''); 
                $("input[name=bufvegan]").val(tr.dataset.bufvegan).attr('readonly',''); 
            }else if(NowHours > DeadLineHours1 ){ /*超過D1 */
                $("input[name=luncnt]").val(tr.dataset.luncnt).attr('readonly',''); 
                $("input[name=lunvegan]").val(tr.dataset.lunvegan).attr('readonly',''); 
                $("select[name=tabtype]").val(tr.dataset.tabtype).trigger("change").attr('disabled',''); 
                $("input[name=tabtype]").val(tr.dataset.tabtype).removeAttr('disabled');
                $("input[name=tabcnt]").val(tr.dataset.tabcnt).attr('readonly',''); 
                $("input[name=tabvegan]").val(tr.dataset.tabvegan).attr('readonly',''); 
                $("input[name=tabunit]").val(tr.dataset.tabunit).attr('readonly',''); 
                $("select[name=buftype]").val(tr.dataset.buftype).trigger("change").attr('disabled',''); 
                $("input[name=buftype]").val(tr.dataset.buftype).removeAttr('disabled');
                $("input[name=bufcnt]").val(tr.dataset.bufcnt).attr('readonly',''); 
                $("input[name=bufvegan]").val(tr.dataset.bufvegan).attr('readonly',''); 
            }
        }else if(tr.dataset.date < now){
            alert('已過日異動時間，無法修改');
            return false;
        }
        $("#btn_copy").hide();
        $("#btn_updata").show();
        $("#btn_dle").show();
        $('#edit').modal('show');
    }
    // 複製_台北
    function copyT(key){
        var tr = document.getElementById('tr'+key);
        console.log(tr.dataset);
        $("select[name=date]").val(tr.dataset.date).trigger("change").removeAttr('disabled').show();
        $("input[name=date]").val(tr.dataset.date).attr('disabled','').hide();
        $("input[name=D_code]").val(tr.dataset.date);
        $("input[name=sincnt]").val(tr.dataset.sincnt).attr('readonly',''); 
        $("input[name=donecnt]").val(tr.dataset.donecnt).attr('readonly',''); 
        $("input[name=dtwocnt]").val(tr.dataset.dtwocnt).attr('readonly',''); 
        $("input[name=lovecnt]").val(tr.dataset.lovecnt).attr('readonly',''); 
        $("input[name=meacnt]").val(tr.dataset.meacnt).attr('readonly',''); 
        $("input[name=meavegan]").val(tr.dataset.meavegan).attr('readonly',''); 
        $("input[name=luncnt]").val(tr.dataset.luncnt).attr('readonly',''); 
        $("input[name=lunvegan]").val(tr.dataset.lunvegan).attr('readonly',''); 
        $("input[name=dincnt]").val(tr.dataset.dincnt).attr('readonly',''); 
        $("input[name=dinvegan]").val(tr.dataset.dinvegan).attr('readonly',''); 
        $("select[name=tabtype]").val(tr.dataset.tabtype).trigger("change").attr('disabled',''); 
        $("input[name=tabtype]").val(tr.dataset.tabtype).removeAttr('disabled');
        $("input[name=tabcnt]").val(tr.dataset.tabcnt).attr('readonly',''); 
        $("input[name=tabvegan]").val(tr.dataset.tabvegan).attr('readonly',''); 
        $("input[name=tabunit]").val(tr.dataset.tabunit).attr('readonly',''); 
        $("select[name=buftype]").val(tr.dataset.buftype).trigger("change").attr('disabled',''); 
        $("input[name=buftype]").val(tr.dataset.buftype).removeAttr('disabled');
        $("input[name=bufcnt]").val(tr.dataset.bufcnt).attr('readonly',''); 
        $("input[name=bufvegan]").val(tr.dataset.bufvegan).attr('readonly',''); 
        $("input[name=teacnt]").val(tr.dataset.teacnt).attr('readonly',''); 
        $("input[name=teaunit]").val(tr.dataset.teaunit).attr('readonly',''); 
        $("input[name=teatime]").val(tr.dataset.teatime).attr('readonly',''); 
        $("input[name=otheramt]").val(tr.dataset.otheramt).attr('readonly',''); 
        $("input[name=siteamt]").val(tr.dataset.siteamt).attr('readonly',''); 
        
        $("#btn_dle").hide();
        $("#btn_updata").hide();
        $("#btn_copy").show();
        $('#edit').modal('show');
    }

    // 編輯_南投
    function editN(key){
        var tr = document.getElementById('tr'+key);
        console.log(tr.dataset);
        $("select[name=date]").val(tr.dataset.date).trigger("change").attr('disabled','').hide();
        $("input[name=date]").val(tr.dataset.date).show().removeAttr('disabled'); 
        $("input[name=D_code]").val(tr.dataset.date);
        $("input[name=signupcnt]").val(tr.dataset.signupcnt).removeAttr('readonly'); 
        $("input[name=signupvegan]").val(tr.dataset.signupvegan).removeAttr('readonly'); 
        $("input[name=checkincnt]").val(tr.dataset.checkincnt).removeAttr('readonly'); 
        $("input[name=checkinvegan]").val(tr.dataset.checkinvegan).removeAttr('readonly'); 
        $("input[name=counselorcnt]").val(tr.dataset.counselorcnt).removeAttr('readonly'); 
        $("input[name=counselorvegan]").val(tr.dataset.counselorvegan).removeAttr('readonly'); 
        $("input[name=nodincnt]").val(tr.dataset.nodincnt).removeAttr('readonly'); 
        $("input[name=earlystaycnt]").val(tr.dataset.earlystaycnt).removeAttr('readonly'); 
        $("input[name=cropoutcnt]").val(tr.dataset.cropoutcnt).removeAttr('readonly'); 
        $("input[name=cropoutvegan]").val(tr.dataset.cropoutvegan).removeAttr('readonly'); 
        $("select[name=endbento]").val(tr.dataset.endbento).trigger("change").removeAttr('disabled');
        $("input[name=endbento]").val(tr.dataset.endbento).attr('disabled','');
        $("input[name=blfcnt]").val(tr.dataset.blfcnt).removeAttr('readonly'); 

        $("#btn_copy").hide();
        $("#btn_updata").show();
        $("#btn_dle").show();
        $('#edit').modal('show');
    }

    // 複製_南投
    function copyN(key){
        var tr = document.getElementById('tr'+key);
        console.log(tr.dataset);
        $("select[name=date]").val(tr.dataset.date).trigger("change").removeAttr('disabled').show();
        $("input[name=date]").val(tr.dataset.date).attr('disabled','').hide();
        $("input[name=D_code]").val(tr.dataset.date);
        $("input[name=signupcnt]").val(tr.dataset.signupcnt).attr('readonly','');
        $("input[name=signupvegan]").val(tr.dataset.signupvegan).attr('readonly','');
        $("input[name=checkincnt]").val(tr.dataset.checkincnt).attr('readonly','');
        $("input[name=checkinvegan]").val(tr.dataset.checkinvegan).attr('readonly','');
        $("input[name=counselorcnt]").val(tr.dataset.counselorcnt).attr('readonly','');
        $("input[name=counselorvegan]").val(tr.dataset.counselorvegan).attr('readonly','');
        $("input[name=nodincnt]").val(tr.dataset.nodincnt).attr('readonly','');
        $("input[name=earlystaycnt]").val(tr.dataset.earlystaycnt).attr('readonly','');
        $("input[name=cropoutcnt]").val(tr.dataset.cropoutcnt).attr('readonly','');
        $("input[name=cropoutvegan]").val(tr.dataset.cropoutvegan).attr('readonly','');
        $("select[name=endbento]").val(tr.dataset.endbento).trigger("change").attr('disabled',''); 
        $("input[name=endbento]").val(tr.dataset.endbento).removeAttr('disabled');
        $("input[name=blfcnt]").val(tr.dataset.blfcnt).attr('readonly','');

        $("#btn_dle").hide();
        $("#btn_updata").hide();
        $("#btn_copy").show();
        $('#edit').modal('show');
    }
    // 執行新增
    function checkcreat (){
        if( $("select[name=date]").val()!=''  ){
            $("#form1").submit();
        }else{
            alert('請選擇上課日期!!');
            return ;
        }
    }
    // 執行編輯
    function checkupdata(){
        var _date = $("select[name=date]").val();
        if( _date!=''  ){
            $("#form4").attr('action', '/admin/classes_requirements/update/'+_date);
            $("#form4").submit();
        }else{
            alert('請選擇上課日期!!');
            return ;
        }
    }
    // function group(){
    //     $('#group').modal('show');
    // }
    // 批次刪除
    function groupdestroy(){
        if( $("#class").val()!='' ){

            var code =  $("input[name=class]").val()+ $("input[name=term]").val();
            if (confirm("確定要刪除" + code +"的資料嗎")){
                $("#deleteform").attr('action', '/admin/classes_requirements/edit/group/'+code);
                $("#deleteform").submit();
            }
        }else{
            alert('請選擇刪除班別!!');
            return ;
        }
    }
    // 週確認新增 ->批次新增
    function groupstore(){
        $("select[name=groupclass]").val($("input[name=class]").val());
        $("input[name=groupterm]").val($("input[name=term]").val());
        $("select[name=groupacccode]").val($("select[name=acccode]").val());
        if( $("select[name=acccode]").val()!='' ){
            $("#form2").submit();
        }else{
            alert('請選擇開支科目!!');
            return ;
        }
    }
    // 更新人數
    function groupupdate(){
        if( $("#class").val()!='' ){
            var code =  $("input[name=class]").val()+ $("input[name=term]").val();
            if (confirm("執行此功能會依據最新的學員人數重新計算各項膳宿數量，當天(含)之後的資料會被清除並重新計算，是否確認執行？")){
                $("#form").attr('action', '/admin/classes_requirements/edit/groupupdate/'+code);
                $("#form").submit();
            }
        }else{
            alert('查無資料!!');
            return ;
        }
    }


</script>
