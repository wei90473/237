@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'notice_emai';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">E-Mail線上問卷填答通知</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/notice_emai" class="text-info">E-Mail線上問卷填答通知列表</a></li>
                        <li class="active">E-Mail線上問卷填答通知</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'method'=>'post', 'name'=>'form', 'id'=>'form']) !!}

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">E-Mail線上問卷填答通知</h3></div>
                    <div class="card-body pt-4">

                        <ul class="list-group col-md-10" >
                            <li class="list-group-item">
                                班號 : {{ $class_data['class'] }}<br>
                                辦班院區 : {{ config('app.branch.'.$class_data['branch']) }}<br>
                                班別名稱 : {{ $class_data['name'] }}<br>
                                期別 : {{ $class_data['term'] }}<br>
                                分班名稱 : {{ $class_data['branchname'] }}<br>
                                班別類型 : {{ config('app.process.'.$class_data['process']) }}<br>
                                委訓機關 : {{ $class_data['commission'] }}<br>
                                受訓期間 : {{ $class_data['sdate'] }} ~ {{ $class_data['edate'] }}<br>
                                班務人員 : {{ $class_data['sponsor'] }}
                            </li>
                        </ul>
                        <input type="hidden" name="class" value="{{ $class_data['class'] }}">
                        <input type="hidden" name="term" value="{{ $class_data['term'] }}">

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">上次寄送時間：</label>
                            <div class="col-sm-10" style="padding-top: 8px;">
                                <?php if(isset($class_mail_data) && isset($class_mail_data['date'])){ ?>
                                <?=$class_mail_data['date'];?>
                                <?php }else{ ?>
                                尚未寄送
                                <?php } ?>
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">收件者：</label>
                            <div class="col-sm-10" style="padding-top: 3px;">
                                <a href="/admin/notice_emai/list/{{ $class_data['class'] }}_{{ $class_data['term'] }}">
                                    <button type="button" class="btn btn-info"> 挑選收件者</button>
                                </a>
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">E-Mail主旨：</label>
                            <div class="col-sm-10" style="padding-top: 3px;">
                                <input type="text" style="width: 500px;" class="form-control" maxlength="50" autocomplete="off" id="title" name="title"  value="{{ old('title', (isset($class_mail_data['title']))? $class_mail_data['title'] : '') }}">
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">E-Mail內容：</label>
                            <div class="col-sm-12">
                            </div>
                            <div class="col-sm-1">
                            </div>
                            <div class="col-sm-8">
                                <textarea id="content" name="content"><?=$class_mail_data['content'];?></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitform();" class="btn btn-sm btn-info">寄出</button>
                        <a href="/admin/notice_emai">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                        <button type="button" onclick="submittome();" class="btn btn-sm btn-info">寄送範本給我</button>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection

@section('js')
<script src="/backend/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
CKEDITOR.replace('content', { height: '300px'});
function submitform(){
    window.document.form.action='/admin/notice_emai/save_mail';
    submitForm('#form');
}
function submittome(){
    window.document.form.action='/admin/notice_emai/mail_to_me';
    submitForm('#form');
}

</script>
@endsection