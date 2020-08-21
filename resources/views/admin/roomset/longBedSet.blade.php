@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'roomset';?>


<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">長期班寢室安排</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">長期班寢室安排</li>
                </ol>
            </div>
        </div>
        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>長期班寢室安排</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="search-float" style="width:100%;">
                                    {{ Form::open(["id" => "search_form", "method" => "put", "url" => "admin/roomset/batchUpdateLongBedInfo"]) }}
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">年度</label>
                                                </div>
                                                <p class="form-control">{{ $data[0]['yerly'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">機關名稱</label>
                                                </div>
                                                <p class="form-control">{{ $data[0]['client'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">班別</label>
                                                </div>
                                                <p class="form-control">{{ $data[0]['name'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">分班名稱</label>
                                                </div>
                                                <p class="form-control">{{ $data[0]['branchname'] }}</p>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">期別</label>
                                                </div>
                                                <p class="form-control">{{ $data[0]['term'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">週別</label>
                                                </div>
                                                <p class="form-control">{{ $data[0]['week'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">調訓鎖定</label>
                                                </div>
                                                <p class="form-control">{{ $data[0]['lock']=='1'?'是':'否' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">住宿日期起</label>
                                                </div>
                                                <p class="form-control">{{ $data[0]['staystartdate'] }}{{$data[0]['staystarttime']}}</p>
                                                <input type="hidden" id='staystartdate' name='staystartdate' value="{{ $data[0]['staystartdate'] }}">
                                                <input type="hidden" id='staystarttime' name='staystarttime' value="{{ $data[0]['staystarttime'] }}">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">住宿日期迄</label>
                                                </div>
                                                <p class="form-control">{{ $data[0]['stayenddate'] }}{{$data[0]['stayendtime']}}</p>
                                                <input type="hidden" id='stayenddate' name='stayenddate' value="{{ $data[0]['stayenddate'] }}">
                                                <input type="hidden" id='stayendtime' name='stayendtime' value="{{ $data[0]['stayendtime'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">已安排住宿數</label>
                                                </div>
                                                <p class="form-control"></p>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">住宿申請數</label>
                                                </div>
                                                <p class="form-control"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">起始安排樓別代碼</label>
                                                </div>
                                                <input class="form-control" id="floorno" name="floorno" value="">
                                                <span class="input-group-addon" style="cursor: pointer;" onclick="qebed(1)">...</span>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">樓別名稱</label>
                                                </div>
                                                <input class="form-control" id="floorname" name="floorname" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">寢室起</label>
                                                </div>
                                                <input class="form-control" id="bedroom1" name="bedroom1" value="">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">寢室迄</label>
                                                </div>
                                                <input class="form-control" id="bedroom2" name="bedroom2" value="">
                                                <span class="input-group-addon" style="cursor: pointer;" onclick="qebed(2)">...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">已安排的重新安排?</label>
                                                </div>
                                                <select class="custom-select" id="resetall" name="resetall">
                                                @foreach(config('app.resetall') as $key => $va)
                                                    <option value="{{ $key }}">{{ $va }}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                            <button type="submit" class="btn btn-sm btn-primary"><i class="fa"></i>安排床位</button>
                                            <a href="/admin/roomset/editLongRoomset/{{ $data[0]['class'] }}/{{ $data[0]['term'] }}">
                                                <button type="button" class="btn btn-sm btn-danger"><i class="fa"></i>回上一層</button>
                                            </a>
                                            <input type="hidden" id="class" name="class" value="{{ $data[0]['class'] }}">
                                            <input type="hidden" id="term" name="term" value="{{ $data[0]['term'] }}">
                                            <input type="hidden" id="sex" name="sex" value="{{ $sex }}">
                                            <input type="hidden" id="week" name="week" value="{{ $data[0]['week'] }}">
                                    </div> 
                                    {{ Form::close() }} 
                                </div>

                                <div class="float-md-right">

                                </div>

                                <div class="table-responsive">
                                {{ Form::open(["id" => "search_form", "method" => "put", "url" => "admin/roomset/updateLongBedset"]) }}
                                    <table class="table table-bordered mb-0 ">
                                        <thead>
                                        <tr>
                                            <th class="text-center">學號</th>
                                            <th class="text-center">姓名</th>
                                            <th class="text-center">床位代碼</th>
                                            <th class="text-center">寢室床位</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($dormStudent as $value => $text)
                                        <?php
                                            $room_alias = '';
                                            if(strlen($text['roomname'])>0){
                                                $n= substr($text['bedno'],-1);
                                                $room_alias = $text['floorname'].' '.$text['roomname'].'室'.'第'.$n.'床';
                                            }

                                            if($text['handicap'] == 'Y'){
                                                $text['cname'] = '*'.$text['cname'];
                                            }
                                        ?>
                                            <tr>
                                                <td class="text-center">{{ $text['no'] }}</td>
                                                <td class="text-center">{{ $text['cname'] }}</td>
                                                <td class="text-center">
                                                    <input name="bedno_{{ $text['idno'] }}" readonly="readonly" value="{{ $text['bedno'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" onclick="selectEmptyBed('{{ $text['idno'] }}')">...</span>
                                                </td>
                                                <td class="text-center">
                                                     <input name="bedname_{{ $text['idno'] }}" readonly="readonly" value="{{ $room_alias }}">
                                                </td>
                                                <input type="hidden" name="floorno_{{ $text['idno'] }}" value="{{ $text['floorno'] }}">
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="class" value="{{ $data[0]['class'] }}">
                                    <input type="hidden" name="term" value="{{ $data[0]['term'] }}">
                                    <input type="hidden" name="sex" value="{{ $sex }}">
                                    <input type="hidden" name="week" value="{{ $data[0]['week'] }}">
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa"></i>修改</button>
                                {{ Form::close() }} 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin/layouts/list/floors')
@include('admin/layouts/list/selectEmptyBed')

@endsection

@section('js')
<script src="/backend/plugins/pagination/pagination.js" charset="UTF-8"></script>
<script>

var globalmode = 0;
function qebed(mode)
{   
    globalmode = mode;
    $("#qebed").modal('show');
}

var studentNo = 0;

function selectEmptyBed(idno)
{   
    studentNo = idno;
    $("#selectEmptyBed").modal('show');
}

function chooseFloor(floorno,floorname,bedroom)
{
    if(globalmode == 1){
        $("input[name='floorno']").val(floorno);
        $("input[name='floorname']").val(floorname);
        $("input[name='bedroom1']").val(bedroom);
    } else if(globalmode == 2){
        $("input[name='bedroom2']").val(bedroom);
    }
}

function chooseBed(bedno,bedname,floorno)
{
    $("input[name='bedno_" + studentNo + "']").val(bedno);
    $("input[name='bedname_" + studentNo + "']").val(bedname);
    $("input[name='floorno_" + studentNo + "']").val(floorno);
}

</script>
@endsection