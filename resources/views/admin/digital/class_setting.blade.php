@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'student_grade';?>
<style>
.search-float input{
    min-width:1px;
}
</style>
<div class="content">
    <div class="container-fluid">


        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>數位時數處理</h3>
                    </div>

                    <div class="card-body">
                        <div style="border: 1px solid #000; padding: 10px; margin-bottom:10px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            期別：{{ $t04tb->term }}<br>
                            分班名稱：<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            委訓機關：{{ $t04tb->client }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：
                        </div>
                        <div>
                            <div class="row col-12" style="margin-bottom:10px;">
                                <button class="btn btn-primary" onclick="addMainOption()">新增</button>
                            </div>                            
                        </div>

                        {!! Form::open(['method' => 'put', 'id' => 'digital_class']) !!}
                            @foreach($t04tb->elearn_classes as $elearn_class)
                             <div class="form-group row col-12">
                                <label class="col-form-label">課程代碼：</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control digital_class" name="digital_class[{{ $elearn_class->id }}][code]" value="{{ $elearn_class->code }}">
                                </div>
                                <label class="col-form-label">課程名稱：</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control digital_class" name="digital_class[{{ $elearn_class->id }}][name]" value="{{ $elearn_class->name }}">
                                </div>                            
                            </div>
                            @endforeach
                        {!! Form::close() !!}


                        <!-- include('admin/layouts/list/pagination', ['paginator' => $t27tbs, 'queryData' => $queryData])                    -->
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" onclick="check_digital_class()">保存</button>
                        <a href="/admin/digital/class_list">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
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
    var new_option_num = 0;
    var origin_option_num = 0;
    function addMainOption()
    {
        var digital_class = '<div class="form-group row col-12">' + 
                            '<label class="col-form-label">課程代碼：</label>' + 
                            '<div class="col-sm-2">' + 
                                '<input type="text" class="form-control digital_class" name="new_digital_class[' + new_option_num + '][code]" value="">' + 
                            '</div>' + 
                            '<label class="col-form-label">課程名稱：</label>' + 
                            '<div class="col-sm-2">' + 
                                '<input type="text" class="form-control digital_class" name="new_digital_class[' + new_option_num  + '][name]" value="">' + 
                            '</div>' + 
                          '</div>';
        new_option_num++;
        $('#digital_class').append(digital_class);
    }

    function check_digital_class()
    {
        var new_options = $('.digital_class');
        for(let i=0; i<new_options.length; i++){
            if(new_options[i].value === ''){
                alert('課程代碼或課程名稱不得為空');
                return false;
            }
        }
        $('#digital_class').submit()
    }

</script>
@endsection