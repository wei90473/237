@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'student_apply';?>
<style>
    .margin_top_bottom10{
        margin-top: 10px;
        margin-bottom: 10px;
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
                    
                    <div class="card-body">
                        <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">換員及補報審核</a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">換員設定</a>
                        </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active margin_top_bottom10" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                @include('admin.student_apply.modify_manage_check')
                            </div>
                            <div class="tab-pane fade margin_top_bottom10" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                @include('admin.student_apply.change_people')
                            </div>
                        </div>                                                                            
                    </div>
                    <div class="card-footer">

                    </div>  
                                   
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
function stopChange(classNo, term, isStopChange)
{
    isStopChange = (isStopChange) ? 'Y' : 'N';
    $.ajax({
        method: 'POST',
        url: '/admin/student_apply/stopChange',
        data: {
            class: classNo,
            term: term,
            _method: 'PUT',
            isStopChange: isStopChange,
            _token: $('meta[name=csrf-token]').attr('content')
        }
    }).done(function(response){

    });
}
</script>
@endsection