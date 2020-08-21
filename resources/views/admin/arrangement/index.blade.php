@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'arrangement';?>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">課程配當安排</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">課程配當安排</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>課程配當安排</h3>
                    </div>

                    <div class="card-body">
                        <div style="border: 1px solid #FFF; padding: 10px; padding-left: 0px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            期別：{{ $t04tb->term }}<br>
                            分班名稱：<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            委訓機關：{{ $t04tb->client }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：
                        </div>
                        <div style="margin-bottom: 10px;">
                            {!! Form::open([
                                'method' => 'POST',
                                'url'=> "/admin/arrangement/uploadSchedule/{$t04tb->class}",
                                'enctype' => "multipart/form-data",
                                'id' => "upload_schedule"
                            ]) !!}

                            <input type="hidden" name="class" value="{{$t04tb->class}}">
                            <input type="hidden" name="term" value="{{$t04tb->term}}">

                            <input type="file" name="schedule" accept="application/pdf">
                            <button type="button" onclick="isHavePlanmk('{{$t04tb->class}}')" class="btn btn-primary">上傳實施計畫</button>
                            <font>{{ (empty($t04tb->t01tb->planmk)) ? '尚未上傳實施計畫' : '已上傳實施計畫' }}</font>
                            <font color="red" id="check_message"></font>
                            {!! Form::close() !!}

                        </div>

                        <div style="margin-bottom: 10px;">
                            <a href='/admin/unit/{{$t04tb->class}}/{{$t04tb->term}}'>
                                <button class="btn btn-primary">維護單元</button>
                            </a>

                            <a href='/admin/arrangement/create/{{$t04tb->class}}/{{$t04tb->term}}'>
                                <button class="btn btn-primary">新增課程</button>
                            </a>

                        </div>
                        <div>
                            公務人員必讀時數合計： {{ (empty($hours_info[1])) ? 0 : $hours_info[1] }}
                            非公務人員必讀時數合計：{{ (empty($hours_info[0])) ? 0 : $hours_info[0] }}
                            總時數：
                            @if ((int)array_sum($hours_info) != array_sum($hours_info))
                            <font color="red">{{array_sum($hours_info)}}</font>
                            @else
                            {{array_sum($hours_info)}}
                            @endif
                            </div>
                        <div class="table-responsive">
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="100">功能</th>
                                        <th>單元名稱</th>
                                        <th>課程名稱</th>
                                        <th class="text-center" width="30">時數</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($t04tb->t06tbs))
                                        @foreach($t04tb->t06tbs as $t06tb)
                                        <tr>
                                            <td class="text-center">
                                                <a href='/admin/arrangement/edit/{{$t04tb->class}}/{{$t04tb->term}}/{{$t06tb->course}}'>
                                                    <button class="btn btn-primary">編輯</button>
                                                </a>
                                            </td>
                                            <td>
                                                @if(isset($t06tb->t05tb))
                                                    {{ $t06tb->t05tb->name }}
                                                @endif
                                            </td>
                                            <td>{{ $t06tb->name }}</td>
                                            <td class="text-center">{{ $t06tb->hour }}</td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <div class="card-footer">
                    
                    <a href="javascript:history.go(-1)">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('js')
<script>
    function isHavePlanmk(class_no){

        if ($("input[name=schedule]").val() == ""){
            alert("請選擇檔案");
        }else{
            $("#check_message").html('上傳中...');
            $.ajax({
                url: "/admin/arrangement/isHavePlanmk/" + class_no
            }).done(function(response) {
                $("#check_message").html('');
                console.log(response);
                if (response.have_planmk){
                    if(confirm('該班別已上傳實施計畫，是否覆蓋檔案？')){
                        $("#upload_schedule").submit();
                    }
                }else{
                    $("#upload_schedule").submit();
                }
            });
        }

    }



</script>
@endsection