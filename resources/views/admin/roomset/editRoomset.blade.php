@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'roomset';?>


<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">住宿日期時間修改</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">住宿日期時間修改</li>
                </ol>
            </div>
        </div>
        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>住宿日期時間修改</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="search-float" style="width:100%;">
                                    {{ Form::open(["id" => "search_form", "method" => "put", "url" => "admin/roomset/updateRoomset"]) }}
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
                                        <div class="form-group col-md-5">
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
                                    <br/>
                                    <br/>
                                    <div class="form-group">
                                        <div class="form-check-inline">
                                            <label class="input-group-text">鎖調訓</label>

                                            <div class="form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="checkbox" name="lock" class="form-check-input" style="min-width:0px;margin-left: 10px" value="1" {{ $data[0]['lock'] == '1'? 'checked' : '' }}>
                                                </label>
                                            </div>                                                                  
                                        </div>
                                    </div>
                                    <div class="float-md mobile-100 row mr-1 mb-3">
                                        <div class="input-group col-4">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">住宿日期起</span>
                                            </div>
                                            <input type="text" id="staystartdate" name="staystartdate" class="form-control" autocomplete="off" value="{{ $data[0]['staystartdate'] }}">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <div class="input-group col-4">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">時間</span>
                                            </div>
                                            <select class="custom-select" id="staystarttime" name="staystarttime">
                                                @foreach(config('app.staytime') as $key => $va)
                                                    <option value="{{ $key }}" {{ $data[0]['staystarttime'] == $key? 'selected' : '1' }}>{{ $va }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="float-md mobile-100 row mr-1 mb-3">
                                        <div class="input-group col-4">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">住宿日期迄</span>
                                            </div>
                                            <input type="text" id="stayenddate" name="stayenddate" class="form-control" autocomplete="off" value="{{ $data[0]['stayenddate'] }}">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <div class="input-group col-4">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">時間</span>
                                            </div>
                                            <select class="custom-select" id="stayendtime" name="stayendtime">
                                                @foreach(config('app.staytime') as $key => $va)
                                                    <option value="{{ $key }}" {{ $data[0]['stayendtime'] == $key? 'selected' : '1' }}>{{ $va }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                            <button type="submit" class="btn btn-sm btn-primary"><i class="fa"></i>確認</button>
                                            <a href="/admin/roomset">
                                                <button type="button" class="btn btn-sm btn-danger"><i class="fa"></i>取消</button>
                                            </a>
                                            <input type="hidden" id="class" name="class" value="{{ $data[0]['class'] }}">
                                            <input type="hidden" id="term" name="term" value="{{ $data[0]['term'] }}">
                                    </div> 
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



@endsection

@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $("#staystartdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker1').click(function(){
            $("#staystartdate").focus();
        });

        $("#stayenddate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker2').click(function(){
            $("#stayenddate").focus();
        });
    });
</script>
@endsection