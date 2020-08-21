@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'roomset';?>


<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">長期班住宿設定</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">長期班住宿設定</li>
                </ol>
            </div>
        </div>
        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>長期班住宿設定</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="search-float" style="width:100%;">
                                    {{ Form::open(["id" => "search_form", "method" => "put", "url" => "admin/roomset/updateLongRoomset"]) }}
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
                                        <div class="form-group col-md-6">
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
                                        <div class="input-group col-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">開課日期</span>
                                            </div>
                                            <select class="custom-select" multiple="multiple" id="lstClassdates" style="height:120px;width:200px">
                                                @foreach($courseDate as $key => $va)
                                                    <option value="{{ $va['date'] }}">{{ $va['date'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer">
                                            <button type="submit" class="btn btn-sm btn-primary"><i class="fa"></i>確認</button>
                                            <a href="/admin/roomset">
                                                <button type="button" class="btn btn-sm btn-danger"><i class="fa"></i>回上一層</button>
                                            </a>
                                            <input type="hidden" id="class" name="class" value="{{ $data[0]['class'] }}">
                                            <input type="hidden" id="term" name="term" value="{{ $data[0]['term'] }}">
                                    </div> 

                                    <div class="float-md-right">

                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0 ">
                                            <thead>
                                            <tr>
                                                <th class="text-center">再次自動安排</th>
                                                <th class="text-center">週別</th>
                                                <th class="text-center">起始日期(起)</th>
                                                <th class="text-center">時間</th>
                                                <th class="text-center">起始日期(讫)</th>
                                                <th class="text-center">時間</th>
                                                <th class="text-center">計算洗滌費</th>
                                                <th class="text-center">住宿數(男)</th>
                                                <th class="text-center">住宿數(女)</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                $i=0;
                                            ?>
                                            @foreach($dt as $value => $text)
                                                <tr>
                                                    @if($text['auto'] == 'Y')
                                                    <td class="text-center">
                                                        <a href="/admin/roomset/autoSetAgain/{{ $text['class'] }}/{{ $text['term'] }}/{{ $text['staystartdate'] }}/{{ $text['stayenddate'] }}/Y/{{ $text['week'] }}">再次自動安排</a>
                                                    </td>
                                                    @else
                                                    <td class="text-center">
                                                    </td>
                                                    @endif
                                                    <td class="text-center">
                                                        {{ $text['week'] }}
                                                        <input type="hidden" name="weeks[]" value="{{ $text['week'] }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <input name="sdates[]" value="{{ $text['staystartdate'] }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <select class="custom-select" id="stimes" name="stimes[]">
                                                            @foreach(config('app.staytime') as $key => $va)
                                                                <option value="{{ $key }}" {{ $text['staystarttime'] == $key? 'selected' : '1' }}>{{ $va }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <input name="edates[]" value="{{ $text['stayenddate'] }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <select class="custom-select" id="etimes" name="etimes[]">
                                                            @foreach(config('app.staytime') as $key => $va)
                                                                <option value="{{ $key }}" {{ $text['stayendtime'] == $key? 'selected' : '1' }}>{{ $va }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="washings[{{$i}}]" value="1" {{ $text['washing'] == '1'? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="/admin/roomset/longBedSet/{{ $data[0]['class'] }}/{{ $data[0]['term'] }}/{{ $text['week'] }}/1">{{ $text['hasBedMaleCount'] }}/{{ $amount['dormMaleCount'] }}</a><br/><a href="/admin/roomset/cancelLongRoomset/{{ $data[0]['class'] }}/{{ $data[0]['term'] }}/{{ $text['week'] }}/1">取消</a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="/admin/roomset/longBedSet/{{ $data[0]['class'] }}/{{ $data[0]['term'] }}/{{ $text['week'] }}/2">{{ $text['hasBedFemaleCount'] }}/{{ $amount['dormFemaleCount'] }}</a><br/><a href="/admin/roomset/cancelLongRoomset/{{ $data[0]['class'] }}/{{ $data[0]['term'] }}/{{ $text['week'] }}/2">取消</a>
                                                    </td>
                                                </tr>
                                            <?php
                                                $i++;
                                            ?>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <input type="hidden" name="class" value="{{ $data[0]['class'] }}">
                                        <input type="hidden" name="term" value="{{ $data[0]['term'] }}">
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