@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'lecture_mail';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座聘函</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/lecture_mail" class="text-info">講座聘函</a></li>
                        <li class="active">講座聘函</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'method'=>'post', 'name'=>'form', 'id'=>'form']) !!}

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座聘函通知</h3></div>
                    <div class="card-body pt-4">

                        <ul class="list-group col-md-10" >
                            <li class="list-group-item">
                   
                            </li>
                        </ul>
                        <input type="hidden" name="class" value="{{ $class_data['class'] }}">
                        <input type="hidden" name="term" value="{{ $class_data['term'] }}">

                        <div class="form-group row">
  
                            <label class="col-sm-4 control-label text-md-right pt-4">收件者：</label>
                            <div class="col-sm-7" style="padding-top: 18px;">

                                <?php 
                                if(count($teacher_list)>0){                                
                                foreach ($teacher_list as $key => $va) { ?>
                                    <input name="teacher_mail[]" type="checkbox" value="<?=$va->cname?>~<?=$va->email?>" /> <label><?=$va->cname?></label>
                                <?php } 
                                }else{?>
                                    本班期並無講師資料
                                <?php }?>
                            </div>
                            <label class="col-sm-4 control-label text-md-right pt-4">E-Mail主旨：</label>
                            <div class="col-sm-7" style="padding-top: 5px;">
                                <input type="text" style="width: 360px;" class="form-control" maxlength="30" autocomplete="off" id="title" name="title"  value="{{ old('title', (isset($class_mail_data['title']))? $class_mail_data['title'] : '') }}">
                            </div>
                            <label class="col-sm-4 control-label text-md-right pt-4">E-Mail內容：</label>
                            <div class="col-sm-12">
                            </div>
                            <div class="col-sm-1">
                            </div>
                            <div class="col-sm-10">
                                <textarea id="content" name="content"><?=$class_mail_data['content'];?></textarea>
                            </div>
                            <label class="col-sm-4 control-label text-md-right pt-4">附件：</label>
                            <div class="col-sm-8" style="padding-top: 18px;">
                                <input name="attached[]" type="checkbox" value="1" /> <label>學員名冊</label>
                                <input name="attached[]" type="checkbox" value="2">   <label>課程表</label>
                                <input name="attached[]" type="checkbox" value="3">   <label>個人資料表</label>
                                <input name="attached[]" type="checkbox" value="4">   <label>個資授權書</label>
                                <input name="attached[]" type="checkbox" value="5">   <label>數位材授權書</label>
                            </div>

                        </div>

                    </div>

                    <div class="card-footer">
                    <?php    if(count($teacher_list)>0){    ?>
                        <button type="button" onclick="submitform();" class="btn btn-sm btn-info">寄出</button>

                    <?php  }   ?>
                        <a href="/admin/lecture_mail">
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
    window.document.form.action='/admin/lecture_mail/save_mail';
    submitForm('#form');
}
function submittome(){
    window.document.form.action='/admin/lecture_mail/mail_to_me';
    submitForm('#form');
}

</script>
@endsection