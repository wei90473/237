@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

<style>
        /* .item_con {
            display: flex;
            align-items: center;
        } */

        @media print{    
            .no-print, .no-print *
            {
                display: none !important;
            }
        }
        .display_inline {
            display: inline-block;
            margin-right: 5px;
        }
        .halfArea {
            padding: 5px;
            border: 1px solid #d2d6de;
            border-radius: 5px;
        }
        .arrow_con {
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 0px 5px;
        }
        .item_con label {
            cursor: pointer;
        }
        .item_check.active, .item_uncheck.active {
            background-color: #d2f1ff;
        }
        .arrow_rank {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
</style>
    <?php $_menu = 'webbookplace';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show no-print">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">網路預約場地審核處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/webbookplace" class="text-info">網路預約場地審核處理</a></li>
                        <li class="active">網路預約場地審核處理(台北)</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'method'=>'put', 'url'=>'/admin/webbookplaceTaipei/edit/'.$result[0]['meet'].$result[0]['serno'], 'id'=>'formtpa']) !!}
            
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">網路預約場地審核處理</h3></div>
                        <div class="card-body pt-4" id="test">
                            <!-- 申請編號 日期-->
                            <input type="hidden" name="result" value="{{isset($result[0]['result'])?$result[0]['result']:''}}">
                            <div class="form-group row">
                                <label class="col-sm-3 control-label text-md-right pt-2">申請編號:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control input-max"  name="meet" value="{{isset($result[0]['meet'])?$result[0]['meet']:''}}" readonly>
                                </div>
                                <div class="col-md-1">
                                    <input type="text" class="form-control input-max"  name="serno" value="{{isset($result[0]['serno'])?$result[0]['serno']:''}}" readonly>
                                </div >
                                
                                <label class="col-form-label text-md" >申請日期:<span class="text-danger">*</span></label>
                                <div class="col-md-3">
                                    <input type="text" id="sdate3" name="applydate" class="form-control" autocomplete="off" value="{{isset($result[0]['applydate'])?$result[0]['applydate']:''}}" readonly>
                                </div>
                            </div>

                            <!--活動名稱 人數 -->
                            <div class="form-group row">
                                <label class="col-sm-3 control-label text-md-right pt-2"><span class="text-danger">*</span>活動名稱(事由):</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="activity" id="activity" value="{{isset($result[0]['activity'])?$result[0]['activity']:''}}"  {{ ($result[0]['result']!='Y')?'': 'disabled'}}>
                                </div>
                                <label class="control-label text-md-right pt-2">人數</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="cnt" id="cnt" value="{{isset($result[0]['cnt'])?$result[0]['cnt']:''}}">
                                </div>
                            </div>

                            <!--單位類型-->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-md-right"><span class="text-danger">*</span>單位類型:</label>
                                <?php 
                                    $chec1='';$chec2='';$chec3='';$chec4='';
                                    switch($result[0]["type"]){
                                        case 1:
                                            $chec1="checked";
                                            break;
                                        case 2:
                                            $chec2="checked";
                                            break;
                                        case 3:
                                            $chec3="checked";
                                            break;
                                        case 4:
                                            $chec4="checked";
                                            break;
                                        default:
                                            break;
                                    }
                                ?>
                                <div class="col-md-5 mt-2" >
                                    <input type="radio" value="1" {{$chec1}} name="type">政府機關
                                    <input type="radio" value="2" {{$chec2}} name="type">民間單位
                                    <input type="radio" value="3" {{$chec3}} name="type">受政府機關委託
                                    <input type="radio" value="4" {{$chec4}} name="type">個人
                                </div>
                            </div>

                            <!--申請單位 -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-md-right"><span class="text-danger">*</span>申請單位(服務機關):</label>
                                <div class="col-md-5">
                                    <input class="form-control" type="text" name="name" id="name" value="{{isset($result[0]['name'])?$result[0]['name']:''}}"  {{ ($result[0]['result']!='Y')?'': 'disabled'}}>
                                </div>
                            </div>
                            <!--收據抬頭 -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-md-right"><span class="text-danger">*</span>收據抬頭:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="payer" id="payer" value="{{isset($result[0]['payer'])?$result[0]['payer']:''}}"  {{ ($result[0]['result']!='Y')?'': 'disabled'}}>
                                </div>
                                <?php
                                    if($result[0]["payer"]==$result[0]["name"]){
                                        $ck2='checked';
                                    }else{
                                        $ck2='';
                                    }
                                ?>
                                <div class="col-sm-2 pt-2">
                                    <input type="checkbox" onchange="same()" id="ditto" {{$ck2}} {{ ($result[0]['result']!='Y')?'': 'disabled'}}>同上
                                </div>
                                
                            </div>
                            <!-- 單位地址 -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-md-right">單位地址:</label>
                                <div class="col-md-5">
                                    <input class="form-control" type="text" name="address" id="address" value="{{isset($result[0]['address'])?$result[0]['address']:''}}" {{ ($result[0]['result']!='Y')?'': 'disabled'}}>
                                </div>
                            </div>

                            <!-- 聯絡人 -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-md-right "><span class="text-danger">*</span>聯絡人(申請人):</label>
                                <div class="col-md-3 pt-2">
                                    <input class="form-control" type="text" name="liaison" id="liaison" value="{{isset($result[0]['liaison'])?$result[0]['liaison']:''}}" {{ ($result[0]['result']!='Y')?'': 'disabled'}}>
                                </div>
                            </div>
                            <!-- 職稱 -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-md-right">職稱:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="position" id="position" value="{{isset($result[0]['position'])?$result[0]['position']:''}}"  {{ ($result[0]['result']!='Y')?'': 'disabled'}}>
                                </div>
                            </div>
                            <!-- 連絡電話 -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-md-right"><span class="text-danger">*</span>連絡電話:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="telno" id="telno"  value="{{isset($result[0]['telno'])?$result[0]['telno']:''}}"  {{ ($result[0]['result']!='Y')?'': 'disabled'}}>
                                </div>
                            </div>
                            <!-- 傳真 -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-md-right">傳真:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="faxno" id="faxno"  value="{{isset($result[0]['faxno'])?$result[0]['faxno']:''}}"  {{ ($result[0]['result']!='Y')?'': 'disabled'}}>
                                </div>
                            </div>
                            <!-- 行動電話 -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-md-right">行動電話:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="mobiltel" id="mobiltel"  value="{{isset($result[0]['mobiltel'])?$result[0]['mobiltel']:''}}" {{ ($result[0]['result']!='Y')?'': 'disabled'}}>
                                </div>
                            </div>
                            <!-- 電子信箱 -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-md-right"><span class="text-danger">*</span>電子信箱:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="email" id="email"  value="{{isset($result[0]['email'])?$result[0]['email']:''}}">
                                </div>
                            </div>
                            <!--預租借之場地-->
                            <fieldset style="border:groove; padding: inherit;page-break-after:always">
                                <legend>預租借之場地</legend>
                                <div class="form-group row">
                                    <div class="col-md-10">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">日期</th>
                                                    <th class="text-center">時間</th>
                                                    <th class="text-center">場地</th>
                                                    <th class="text-center">功能</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(!empty($place))
                                                    @foreach($place as $k => $row)
                                                    <tr class="text-center">
                                                        <input type="hidden" name="{{'site'.$k}}" value="{{$row['site']}}">
                                                        <input type="hidden" name="{{'date'.$k}}" value="{{$row['date']}}">
                                                        <input type="hidden" name="{{'time'.$k}}" value="{{$row['time']}}">
                                                        <input type="hidden" name="{{'seattype'.$k}}" value="{{$row['seattype']}}">
                                                        <input type="hidden" name="{{'usertype'.$k}}" value="{{$row['usertype']}}">
                                                        <td>{{substr($row["date"],0,3).'/'.substr($row["date"],3,2).'/'.substr($row["date"],5,2).'('.config('app.day_of_week.'.$row["week"]).')' }}</td>
                                                        <td>{{config('app.time.'.$row["time"])}}</td>
                                                        <td>{{$row['name']}}</td>
                                                        @if($result[0]['result']!='Y')
                                                        <td><button class="btn btn-light" type="button" onclick="EditSite({{$k}})">編輯</button></td>
                                                        @else
                                                        <td></td>
                                                        @endif
                                                    </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @if($result[0]['result']!='Y')
                                <button class="btn btn-info no-print" type="button" onclick="Create()">新增場地</button>
                                @endif
                            </fieldset>
                            <br>
                            <!--折扣及費用-->
                            <fieldset style="border:groove; padding: inherit;page-break-after:always">
                                <legend>費用及收據</legend>
                                <!-- 費用 -->
                                <div class="form-group row">
                                    <label class="col-md-2">費用:</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="totalfee" id="totalfee" value="{{isset($result[0]['totalfee'])? number_format ($result[0]['totalfee']):''}}" {{ ($result[0]['result']!='Y')?'': 'disabled'}} >
                                    </div>
                                    <label class="col-sm-2 col-form-label">繳費截止日:</label>
                                     @if($result[0]['result']!='Y')
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="duedate" id="duedate" value="{{isset($result[0]['duedate'])?$result[0]['duedate']:''}}" >
                                    </div>
                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                    @else
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="duedate" id="duedate" value="{{isset($result[0]['duedate'])?$result[0]['duedate']:''}}" disabled>
                                    </div>
                                    @endif
                                </div>
                                <!-- 收據 -->
                                <div class="form-group row">
                                    <label class="col-md-2">收據編號:</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="no" id="no" value="{{isset($result[0]['no'])?$result[0]['no']:''}}" readonly>
                                    </div>
                                    <label class="col-sm-2 col-form-label">回覆日期:</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="replydate" id="replydate" value="{{isset($result[0]['replydate'])?$result[0]['replydate']:''}}" readonly>
                                    </div>
                                </div>
                                <button class="btn btn-info no-print" type="button" onclick="Reply()">回覆情況</button>
                                <a href="mailto:{{$result[0]['email']}}"><button class="btn btn-info no-print" type="button" >email回覆</button></a>
                                <a href="/admin/webbookplaceTaipei/edit/{{$result[0]['meet'].$result[0]['serno']}}/apply_doc" >
                                    <button class="btn btn-info no-print" type="button">列印申請單</button>
                                </a>
                                <a href="/admin/webbookplaceTaipei/edit/{{$result[0]['meet'].$result[0]['serno']}}/export_doc" >
                                    <button class="btn btn-info no-print" type="button">列印回覆單</button>  
                                </a> 
                            </fieldset>
                        </div>
                    </div>
                    <input type="hidden" name="replymk" value="" id="replymk">
                    <textarea style="display:none" class="form-control input-max" rows="5" maxlength="1000" name="replynote"></textarea>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-info no-print"><i class="fa fa-save pr-2"></i>儲存</button>
                        <!-- <button type="button" onclick="deleteClass()" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button> -->
                         
                        <a href="/admin/webbookplaceTaipei">
                            <button type="button" class="btn btn-sm btn-danger no-print"><i class="fa fa-reply"></i> 取消</button>
                        </a>
                    </div>
                </div>
            </div>
            
            {!! Form::close() !!}

        </div>
    </div>
    <!-- 新增場地 modal-->
    <div class="modal fade" id="CreateModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <!-- form start -->
                {!! Form::open([ 'method'=>'PUT', 'url'=>'/admin/webbookplaceTaipei/createSite',  'id'=>'form4']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title">新增類別</h4> -->
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <input type="hidden" name="meet" value="{{isset($result[0]['meet'])?$result[0]['meet']:''}}">
                            <input type="hidden" name="serno" value="{{isset($result[0]['serno'])?$result[0]['serno']:''}}">
                            <!-- 場地 -->
                            <label class="control-label pt-2">場地<span class="text-danger">*</span></label>
                            <div class="col-md-7">
                                <select id="C_site" name="C_site" class="select2 select2-single input-max" required>
                                    @foreach($siteList as $key => $va)
                                    <option value="{{ $va['site'] }}">{{ $va['site'] }} {{ $va['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <!-- 日期 -->
                            <label class="control-label pt-2">日期<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <input type="text" id="C_date" name="C_date" class="form-control" autocomplete="off"  placeholder="請輸入日期" value="" required>
                            </div>
                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker3"><i class="fa fa-calendar"></i></span>
                        </div>
                        <div class="form-group row">
                            <!-- 時段 -->
                            <label class="control-label pt-2">時段<span class="text-danger">*</span></label>
                            <div class="col-md-10 pt-2">
                                @foreach(config('app.time') as $key => $va)
                                    @if($key!='D' && $key !='E')
                                    <input type="radio" name="C_time" value="{{ $key }}" >{{ $va }}
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group row">    
                            <!-- 座位方式 -->
                            <label class="control-label pt-2">座位方式</label>
                            <div class="col-md-5">
                                <select id="C_seattype" name="C_seattype" class="select2 select2-single input-max" >
                                    @foreach(config('app.seattype') as $key => $va)
                                    <option value="{{ $key }}">{{ $key }} {{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 場地使用者 -->
                            <label class="control-label pt-2">場地使用者</label>
                            <div class="col-md-8">
                                <select id="C_usertype" name="C_usertype" class="select2 select2-single input-max" >
                                    @foreach(config('app.usertype') as $key => $va)
                                    <option value="{{ $key }}">{{ $key }} {{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="actionCreate()">新增</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
   	<!-- 修改場地 modal-->
    <div class="modal fade" id="EditModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <!-- form start -->
                {!! Form::open([ 'method'=>'PUT', 'url'=>'/admin/webbookplaceTaipei/updateSite',  'id'=>'form2']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title">新增類別</h4> -->
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <input type="hidden" name="meet" value="{{isset($result[0]['meet'])?$result[0]['meet']:''}}">
                            <input type="hidden" name="serno" value="{{isset($result[0]['serno'])?$result[0]['serno']:''}}">
                            <input type="hidden" name="E_site" value="">
                            <input type="hidden" name="E_date" value="">
                            <input type="hidden" name="E_time" value="">
                            <!-- 場地 -->
                            <label class="control-label pt-2">場地<span class="text-danger">*</span></label>
                            <div class="col-md-7">
                                <select id="site" name="site" class="select2 select2-single input-max" required>
                                    @foreach($siteList as $key => $va)
                                    <option value="{{ $va['site'] }}">{{ $va['site'] }} {{ $va['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm">原申請場地</button>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <!-- 日期 -->
                            <label class="control-label pt-2">日期<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <input type="text" id="date" name="date" class="form-control" autocomplete="off"  placeholder="請輸入日期" value="" required>
                            </div>
                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                        </div>
                        <div class="form-group row">
                            <!-- 時段 -->
                            <label class="control-label pt-2">時段<span class="text-danger">*</span></label>
                            <div class="col-md-10 pt-2">
                                @foreach(config('app.time') as $key => $va)
                                    @if($key!='D' && $key !='E')
                                    <input type="radio" name="time" value="{{ $key }}" >{{ $va }}
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group row">    
                            <!-- 座位方式 -->
                            <label class="control-label pt-2">座位方式</label>
                            <div class="col-md-5">
                                <select id="seattype" name="seattype" class="select2 select2-single input-max" >
                                    @foreach(config('app.seattype') as $key => $va)
                                    <option value="{{ $key }}">{{ $key }} {{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 場地使用者 -->
                            <label class="control-label pt-2">場地使用者</label>
                            <div class="col-md-8">
                                <select id="usertype" name="usertype" class="select2 select2-single input-max" >
                                    @foreach(config('app.usertype') as $key => $va)
                                    <option value="{{ $key }}">{{ $key }} {{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="actionEdit()">儲存</button>
                        <button type="button" class="btn btn-danger" onclick="actionDelete()">刪除</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>
                </div>
                {!! Form::close() !!}
                
                <!-- 刪除 -->
                {!! Form::open([ 'method'=>'delete', 'url'=>'/admin/webbookplaceTaipei/deleteSite',  'id'=>'deleteform']) !!}
                    <input type="hidden" name="meet" value="{{isset($result[0]['meet'])?$result[0]['meet']:''}}">
                    <input type="hidden" name="serno" value="{{isset($result[0]['serno'])?$result[0]['serno']:''}}">
                    <input type="hidden" name="D_site" value="">
                    <input type="hidden" name="D_date" value="">
                    <input type="hidden" name="D_time" value="">
                {!! Form::close() !!}       
            </div>
        </div>
    </div>
    <!-- 回覆情況 modal-->
    <div class="modal fade" id="ReplyModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog" role="document" style="max-width:700px;">
                <!-- form start -->
                {!! Form::open([ 'method'=>'PUT', 'url'=>'/admin/webbookplaceTaipei/reply',  'id'=>'form3']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title">新增類別</h4> -->
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <input type="hidden" name="meet" value="{{isset($result[0]['meet'])?$result[0]['meet']:''}}">
                            <input type="hidden" name="serno" value="{{isset($result[0]['serno'])?$result[0]['serno']:''}}">
                            <!-- 回覆情況 -->
                            <label class="control-label pt-2">回覆情況<span class="text-danger">*</span></label>
                            
                                @foreach(config('app.replymk') as $key => $va)
                                    @if($key%3==1)
                                    <div class="col-md-12">
                                    @endif
                                        <input type="radio" name="R_replymk" value="{{ $key }}" onchange="replynote({{$key}})" {{ old('replymk', (isset($result->replymk))? $result->replymk : '') == $key? 'checked' : '' }} >{{ $va }}
                                    @if($key%3==0)    
                                    </div>
                                    @endif
                                @endforeach
                                @if($key%3!=0)
                                </div>
                                @endif 
                        </div>
                        <!-- 回覆意見 -->
                        <div class="form-group row">
                            <label class="control-label pt-2">回覆意見</label>
                            <div class="col-md-12">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="R_replynote" >{{ old('replynote', (isset($result->replynote))? $result->replynote : '') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="actionReply()">確定</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="doclear()">取消</button>
                    </div>
                    {!! Form::close() !!}       
                </div>
            </div>
        </div>
    </div>  	
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script type="text/javascript">
$(document).ready(function() {
    $("#sdate3").datepicker({
        format: "twy/mm/dd",
        language: 'zh-TW'
    });
    // $('#datepicker5').click(function(){
    //     $("#sdate3").focus();
    // });

    $("#C_date").datepicker({
        format: "twymmdd",
        language: 'zh-TW'
    });
    $('#datepicker3').click(function(){
        $("#C_date").focus();
    });
    $("#date").datepicker({
        format: "twymmdd",
        language: 'zh-TW'
    });
    $('#datepicker2').click(function(){
        $("#date").focus();
    });
    $("#duedate").datepicker({
        format: "twymmdd",
        language: 'zh-TW'
    });
    $('#datepicker1').click(function(){
        $("#duedate").focus();
    });
    /*$( "#is_need_receipt" ).change(function() {
        $("#receiptname").attr("disabled",true);
        //var test=$("#receiptname").attr();
        //console.log(test);
        alert( "Handler for .change() called." );
    });*/
   

    var sdate3 = "<?= isset($result[0]['applydate'])? $result[0]['applydate'] :''?>";
    if(sdate3 != ''){
        var sdate3 = sdate3.substr(0,3)+'/'+sdate3.substr(3,2)+'/'+sdate3.substr(5,2);
    }
    
    $("#sdate3").val(sdate3);   
    actionReply(); 
});

function batch_submit()
{
    $("#batch").val(1);
    $("#form").submit();
}
function deleteClass()
{
    var txt=confirm("是否要刪除?");
    if(txt==true){
        $("#delete").val(1);
        $("#form").submit();
    }
   
}

function printForm(obj)
{
    var html = obj.innerHTML;
    var bodyHtml = document.body.innerHTML;
    var contentStyle = document.querySelector('.content');
    contentStyle.style = "margin-top : 0";
    // var tmpselect = document.querySelector('.tmpselect');
    // tmpselect.style ="width : 250px";

    // $(".printuse").css("margin-top","10%");
    
    // document.body.innerHTML = html;
    window.print();
    document.body.innerHTML = bodyHtml;
    window.location.reload();
}

function same() {
    if( $('#ditto').prop('checked') ){
        var name = $('input[name=name]').val();
        $('input[name=payer]').val(name);
        return false; 
    }else{
        console.log(1);
    }

}
function Create(){
    $('#CreateModal').modal('show');
}
function actionCreate(){
    if( $('#C_site').val()=='' || $('#C_date').val()=='' || $('input[name=C_time]:checked').val()==undefined ){
        alert('請完善資料!');
        return false;
    }else{
        $("#form4").submit();
    }
}
function EditSite(key){
    var site = $('input[name=site'+key+']').val();
    var date = $('input[name=date'+key+']').val();
    var time = $('input[name=time'+key+']').val();
    var seattype = $('input[name=seattype'+key+']').val();
    var usertype = $('input[name=usertype'+key+']').val();
    $("select[name='site']").val(site).trigger("change");
    $('#date').val(date);
    if(time=='A'){
        $("input[name='time']")[0].checked =true;
    }else if(time=='B'){
        $("input[name='time']")[1].checked =true;
    }else if(time=='C'){
        $("input[name='time']")[2].checked =true;
    }else{
        alert('錯誤時段');
        return false;
    }
    $("input[name='time']:radio[value='"+time+"']").attr('checked','true');
    $("select[name='seattype']").val(seattype).trigger("change");
    $("select[name='usertype']").val(usertype).trigger("change");
    $('input[name=E_site]').val(site);
    $('input[name=E_date]').val(date);
    $('input[name=E_time]').val(time);
    $('input[name=D_site]').val(site);
    $('input[name=D_date]').val(date);
    $('input[name=D_time]').val(time);
    $('#EditModal').modal('show');
}
function actionEdit(){

    $("#form2").submit();
}

function actionDelete(){
    $("#deleteform").submit();
}


function Reply(){
    $('#ReplyModal').modal('show');
}

function actionReply(){
    var replymk = $('input[name=R_replymk]:checked').val();
    var replynote = $('textarea[name=R_replynote]').val();
    $('input[name=replymk]').val(replymk);
    $('textarea[name=replynote]').val(replynote);
    // if( replymk ==undefined){
    //     alert('請選擇回覆狀況!');
    //     return false;
    // }else{
    //     $("#form3").submit();
    // }
}
function replynote(lymk){
    var msg = '';
    var totalfee = $('input[name=totalfee]').val();
    if(lymk=='1'){
        msg = '同意釋出場地使用權，將另由本中心福華國際文教會館業務部（聯絡電話83691155轉2108、2109）與貴單位聯絡人洽詢會議場地租借相關事宜。';
    }else if(lymk=='2'){
        msg = '同意租借該場地，費用'+totalfee+'元，請於場地使用一週前至本中心秘書室出納（聯絡電話83691399轉8506劉小姐）繳交完竣，逾期將取消場地保留。';
    }else if(lymk=='3'){
        msg = '同意租借該場地，費用以八折優惠價計算，為'+totalfee+'元，請於場地使用一週前至本中心秘書室出納（聯絡電話83691399轉8506劉小姐）繳交完竣，逾期將取消場地保留。';
    }else if(lymk=='4'){
        msg = '因本中心臨時辦理訓練研習活動，致與租借場地時段衝突，歉難同意租借。煩請洽本中心場地管理人（聯絡電話83691399轉8305許小姐），調整租借場地或時段。';
    }else if(lymk=='5'){
        msg = '因活動性質未符合本中心場地提前釋放原則，煩請於94年10月06日以後逕洽福華文教會館辦理（聯絡電話83691155轉2108、2109）。';
    }else if(lymk=='6'){
        msg = '前開時段將保留為本中心辦理訓練課程使用，並列入本中心使用場次計算。';
    }else if(lymk=='7'){
        msg = '前開時段已保留完竣，請以公文會辦或正式行文本中心。';
    }else{
        msg = '';
    }
    $('textarea[name=R_replynote]').val(msg);
}
function doclear(){
    $('input[name=R_replymk]:checked').val($('input[name=replymk]').val());
    $('textarea[name=R_replynote]').val($('textarea[name=replynote]').val());
}
</script>
@endsection