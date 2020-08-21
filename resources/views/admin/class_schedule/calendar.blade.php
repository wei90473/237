@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'class_schedule';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">課程表處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/class_schedule" class="text-info">課程表處理列表</a></li>
                        <li class="active">課程表</li>
                    </ol>
                </div>
            </div>
            <!-- 提示訊息 -->
            @include('admin/layouts/alert')
            <div class="float-md col-sm-12 ">
                <button type="button" class="btn btn-primary btn-sm mb-3" onclick="changeClassAssign()">課程配當</button>
                <!-- <button type="button" class="btn btn-primary btn-sm mb-3">儲存</button> -->
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <tr>
                            
                                <th class="text-center">時</th>
                                <th class="text-center">分</th>
                            @foreach($date as $key => $value)  
                            
                                <th class="text-center" value="{{$key}}">{{$value['date']}} 週{{$value['week']}}</th>
                                
                            @endforeach
                            </tr>
                            <tr>
                        @for($i=6; $i<=21; $i++) 
                            @if($classdata->Scale == 10)
                                    <td rowspan="6" class="text-center" style="padding:0px;">{{$i}}</td>
                            <!-- 刻度十分 -->
                                @for($j=1; $j<=6; $j++) 
                                        <td class="text-center" style="padding:0px;">{{$j*10}}</td>
                                        @for($k=1; $k<= count($date); $k++)
                                        <?php 
                                            if(isset($calendar[$k][$i][$j*10]['name'])  ){
                                                echo '<td class="text-center" rowspan="'.$calendar[$k][$i][$j*10]['range'].'" style="border:4px #FFD382 groove;">'.$calendar[$k][$i][$j*10]['name'].'</td>';
                                            }elseif( isset($calendar[$k][$i][$j*10]['style'])  ){
                                                echo '<td class="text-center" style="display:none"></td>';
                                            }else{
                                                echo '<td class="text-center" style="padding:0px;"></td>';
                                            } ?>
                                        @endfor
                                    </tr>
                                    <tr>
                                @endfor    
                            <!-- 刻度五分 -->
                            @else
                                <td rowspan="12" class="text-center" style="padding:0px;">{{$i}}</td>
                                @for($j=1; $j<=12; $j++) 
                                        <td class="text-center" style="padding:0px;">{{$j*5}}</td>
                                        @for($k=1; $k<= count($date); $k++)
                                            <?php if(isset($calendar[$k][$i][$j*5]['name'])){
                                                echo '<td class="text-center" rowspan="'.$calendar[$k][$i][$j*5]['range'].'" style="border:4px #FFD382 groove;">'.$calendar[$k][$i][$j*5]['name'].'</td>';
                                            }elseif( isset($calendar[$k][$i][$j*5]['style'])){
                                                echo '<td class="text-center" style="display:none"></td>';
                                            }else{
                                                echo '<td class="text-center" style="padding:0px;"></td>';
                                            } ?>
                                        @endfor
                                    </tr>
                                    <tr>
                                @endfor
                            @endif
                        @endfor
                        <td colspan="{{count($date)+2}}" ></td>
                        </tr>
                        </table>
                    </div><br>
                    <div>
                        <!-- <button type="button" class="btn btn-primary btn-sm">儲存</button> -->
                        <a href="/admin/class_schedule/{{$classdata[0]->class.$classdata[0]->term}}/edit">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 課程配當 modal -->
    <!-- form start -->
    {!! Form::open([ 'method'=>'put', 'url'=>'/admin/class_schedule/calendar/', 'id'=>'form1']) !!}
        <div class="modal fade bd-example-modal-lg classAssign" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document" style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><strong id="popTitle">課程配當</strong></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <input type=hidden id="class" name="class" value="{{$classdata[0]->class}}" >
                        <input type=hidden id="term" name="term" value="{{$classdata[0]->term}}" >
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12" style="height:400px; overflow:auto;">
                            <table class="table table-bordered mb-0">
                                <tr>
                                    <th style="padding:5px;">課程名稱</th>
                                    <th style="padding:5px;">講座</th>
                                    <th style="padding:5px;">日期</th>
                                    <th style="padding:5px;">時間(起)</th>
                                    <th style="padding:5px;">時間(迄)</th>
                                    <th style="padding:5px;">時數</th>
                                </tr>
                                <?php $timelist=''; ?>
                                @foreach($classdata as $key=>$value)
                                <tr>
                                    <td>{{$value['name']}}</td>
                                    <td>{{$value['cname']}}</td>
                                    @if($value['paymoney']==0)
                                        <td>
                                            <input type="text" id="{{'date'.$value['course']}}" name="{{'date'.$value['course']}}" class="form-control" autocomplete="off" value="{{ old('date', (isset($value['date']))? $value['date'] : '') }}"  >
                                        </td>
                                        <td>
                                            @if($classdata->Scale==10)
                                            <select id="{{'stime'.$value['course']}}" name="{{'stime'.$value['course']}}" class="select2 form-control select2-single input-max" >
                                                <option value="">請選擇</option>
                                                @foreach(config('time.start_ten') as $va)
                                                    <option value="{{ $va }}" {{ old('stime', (isset($value['stime']))? $value['stime'] : 1) == $va? 'selected' : '' }}   >{{ $va }}</option>
                                                @endforeach
                                            </select>
                                            @else
                                            <select id="{{'stime'.$value['course']}}" name="{{'stime'.$value['course']}}" class="select2 form-control select2-single input-max" >
                                                <option value="">請選擇</option>
                                                @foreach(config('time.start_five') as $va)
                                                    <option value="{{ $va }}" {{ old('stime', (isset($value['stime']))? $value['stime'] : 1) == $va? 'selected' : '' }} >{{ $va }}</option>
                                                @endforeach
                                            </select>
                                            @endif
                                        </div>
                                        </td>
                                    @else
                                        <td>
                                            <input type="text" class="form-control" autocomplete="off" value="{{ old('date', (isset($value['date']))? $value['date'] : '') }}" disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" autocomplete="off" value="{{ old('stime', (isset($value['stime']))? $value['stime'] : '') }}" disabled>
                                        </div>
                                        </td>
                                    @endif    
                                    <td>{{$value['etime']}}</td>
                                    <td>{{$value['hour']}}</td>
                                </tr>
                                <?php $timelist .='#date'.$value['course'].','; ?>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="submitForm('#form1');">儲存</button>
                        <button type="button" class="btn btn-info" data-dismiss="modal">離開</button>
                    </div>
                </div> 
            </div>
        </div>
    {!! Form::close() !!}
@endsection
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
    function changeClassAssign() {
        $('.classAssign').modal('show');
    }
    
    $(document).ready(function() {
        $(<?= '"'.substr($timelist,0,-1).'"' ?>).datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
    });
</script>

