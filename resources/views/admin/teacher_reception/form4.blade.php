@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'teacher_reception';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座接待管理表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/teacher_reception" class="text-info">講座接待管理列表</a></li>
                        <li class="active">講座接待管理表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'method'=>'put', 'url'=>'/admin/teacher_reception/'.$ClassWeek_data['edit_id'], 'id'=>'form']) !!}


            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座接待管理表單</h3></div>
                    <div class="card-body pt-4">

                        <ul class="list-group col-md-10" >
                            <li class="list-group-item">
                                講座 : {{ $idno_data['cname'] }}<br>
                                需求起訖期間 : {{ $ClassWeek_data['sdate'] }} ~ {{ $ClassWeek_data['edate'] }}<br>
                            </li>
                        </ul>
                        <input type="hidden" name="type" value="teacher_other">

                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a href="/admin/teacher_reception/{{ $ClassWeek_data['edit_id'] }}/edit1"  class="nav-link">住宿</a></li>
                            <li class="nav-item"><a href="/admin/teacher_reception/{{ $ClassWeek_data['edit_id'] }}/edit2" class="nav-link">派車</a></li>
                            <li class="nav-item"><a href="/admin/teacher_reception/{{ $ClassWeek_data['edit_id'] }}/edit3" class="nav-link">用餐</a></li>
                            <li class="nav-item"><a href="#" class="nav-link active">其他需求</a></li>
                        </ul>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-left pt-2">其他授課相關需求：</label>
                            <div class="col-sm-10">
                            </div>
                            <div class="col-sm-12">
                                <textarea id="demand" rows="6" name="demand" cols="60"><?=$TeacherByWeek_data['demand'];?></textarea>
                            </div>

                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <?php if(!empty($goBack)){?>
                        <a href="/admin/teacher_reception/{{ $goBack['type'] }}_detail?search=search&date={{ $goBack['date'] }}">
                            <button type="button" class="btn btn-sm btn-danger"> 取消</button>
                        </a>
                        <?php }else{ ?>
                        <a href="/admin/teacher_reception/detail?class={{ $ClassWeek_data['class'] }}&term={{ $ClassWeek_data['term'] }}&sdate={{ $ClassWeek_data['sdate'] }}">
                            <button type="button" class="btn btn-sm btn-danger"> 取消</button>
                        </a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')

@endsection