@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'student_apply';?>
<style>
input{
    /* min-width:1px; */
}
</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">學員報名處理</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">學員報名處理</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員報名處理</h3>
                    </div>

                    {!! Form::model($t13tb, array('url' => "/admin/student_apply/changeStudent/{$t04tb->class}/{$t04tb->term}/{$des_idno}",'method'=>'put')) !!}    
                    <div class="card-body">
                        <div style="border: 1px solid #000; padding: 10px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            期別：{{ $t04tb->term }}<br>
                            分班名稱：<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            委訓機關：{{ $t04tb->client }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：
                        </div>

                        <div>
                            @include('admin.student_apply.form_government')
                        </div>                                                                                   
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-save"></i> 儲存</button>
                        <a href="/admin/student_apply/{{ $t04tb->class }}/{{ $t04tb->term }}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>  
                    {!! Form::close() !!}                                      
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>

    computeAge();
    function computeAge(){
        let class_year = {{ (int)substr($t04tb->class, 0, 3) }};
        let birth = $("input[name='m02tb[birth]']").val();
        if (birth.length == 7){
            let year = parseInt(birth.substr(0,3));
            let age = class_year - year;
            $("input[name=age").val(age);
        }
    }

    function showChangeStudentModol()
    {
        $("#change_student").modal('show');
    }

    function status_change(status)
    {
        console.log(status);
        if (status == 3){
            $("input[name=not_present_notification]").attr('disabled', false);
        }else{
            $("input[name=not_present_notification]").attr('disabled', true);            
        }
    }    

</script>
@endsection