@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'waiting';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座擬聘處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座擬聘處理列表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座擬聘處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            班號 : {{ $class_data['class'] }}<br>
                                            辦班院區 : {{ config('app.branch.'.$class_data['branch']) }}<br>
                                            班別名稱 : {{ $class_data['name'] }}<br>
                                            期別 : {{ $class_data['term'] }}<br>
                                            分班名稱 : {{ $class_data['branchname'] }}<br>
                                            班別類型 : {{ config('app.process.'.$class_data['process']) }}<br>
                                            受訓期間 : {{ $class_data['sdate'] }} ~ {{ $class_data['edate'] }}<br>
                                            班務人員 : {{ $class_data['sponsor'] }}
                                        </li>
                                    </ul>
                                    <br>
                                    <div class="float-left search-float" style="min-width: 1000px;">
                                        <form method="get" id="search_form">
                                        <input type="hidden" id="class" name="class" class="form-control" value="<?=$class_data['class'];?>">
                                        <input type="hidden" id="term" name="term" class="form-control" value="<?=$class_data['term'];?>">
                                            <div class="pull-left mobile-100 mr-1 mb-2">
                                                <div class="input-group">
                                                    <div class="input-group-prepend" style="display:flex; align-items:center;">
                                                        <span class="input-group-text">篩選</span>
                                                        <input type="radio" id="hire" name="hire" style="min-width:20px; margin-left:5px;" value="Y" <?=($class_data['hire']=='Y')?'checked':'';?> >聘用
                                                        <input type="radio" id="hire" name="hire" style="min-width:20px; margin-left:5px;" value="N" <?=($class_data['hire']=='N')?'checked':'';?> >未遴聘
                                                        <input type="radio" id="hire" name="hire" style="min-width:20px; margin-left:5px;" value="" <?=($class_data['hire']=='')?'checked':'';?> >全部
                                                    </div>
                                                </div>
                                            </div>


                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                            <a href="/admin/waiting/create?class={{ $class_data['class'] }}&term={{ $class_data['term'] }}">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新增擬聘資料</button>
                                            </a>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="data_table" class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">功能</th>
                                                <th>遴聘與否</th>
                                                <th>支付鐘點費</th>
                                                <th>課程名稱</th>
                                                <th>講座姓名</th>
                                                <th>服務機關</th>
                                                <th>現職</th>
                                                <th>E-Mail</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($teacher_list as $row)
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/waiting/{{ $row['id'] }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                    <td >
                                                        <span onclick="$('#hire_form').attr('action', '/admin/waiting/changehire/<?=$row['id'];?>');" data-toggle="modal" data-target="#hire_modol">
                                                        <input type="checkbox" id="hire" name="hire" style="min-width:20px; margin-left:5px;" <?=($row['hire']=='Y')?'checked':'';?> value="" >
                                                        </span>
                                                    </td>
                                                    <td >
                                                        <?php if($row['hire']!='N' && $row['lecthr']=='0'){ ?>
                                                        <span onclick="$('#hire_type_form').attr('action', '/admin/waiting/changepay/<?=$row['id'];?>');" data-toggle="modal" data-target="#hire_type_modol">
                                                        <?php } ?>
                                                            <input type="radio" id="hire<?=$row['id'];?>" name="hire<?=$row['id'];?>" <?=($row['hire']=='Y' && $row['lecthr']!='0')?'checked':'';?> style="min-width:20px; margin-left:5px;" value="Y" <?=($row['hire']=='N')?'disabled':'';?> >
                                                        </span>
                                                        有料
                                                        <?php if($row['hire']!='N' && $row['lecthr']!='0'){ ?>
                                                        <span onclick="$('#hire_type_form').attr('action', '/admin/waiting/changenotpay/<?=$row['id'];?>');" data-toggle="modal" data-target="#hire_type_modol">
                                                        <?php } ?>
                                                            <input type="radio" id="hire<?=$row['id'];?>" name="hire<?=$row['id'];?>" <?=($row['hire']=='Y' && $row['lecthr']=='0')?'checked':'';?> style="min-width:20px; margin-left:5px;" value="N" <?=($row['hire']=='N')?'disabled':'';?> >
                                                        </span>
                                                        無料
                                                    </td>
                                                    <td>{{ $row['name'] }} ( {{ $class_data['term'] }}/{{ $row['course'] }} ) </td>
                                                    <td>{{ $row['cname'] }}</td>
                                                    <td>{{ $row['dept'] }}</td>
                                                    <td>{{ $row['position'] }}</td>
                                                    <td>{{ $row['email'] }}</td>

                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">


    </script>

    <div id="hire_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content p-0 b-0">
                <div class="card mb-0">
                    <div class="card-header bg-warning">
                        <h3 class="card-title float-left text-white">遴聘與否</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">確定要修改嗎？</p>
                    </div>
                    <div class="modal-footer py-2">
                        {!! Form::open([ 'method'=>'post', 'url'=>'', 'id'=>'hire_form' ]) !!}
                        <input type="hidden" id="class" name="class" class="form-control" value="<?=$class_data['class'];?>">
                        <input type="hidden" id="term" name="term" class="form-control" value="<?=$class_data['term'];?>">
                        <button type="button" class="btn mr-2 btn-info pull-left" onclick="location.reload();" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn mr-3 btn-danger">確定</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="hire_type_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content p-0 b-0">
                <div class="card mb-0">
                    <div class="card-header bg-warning">
                        <h3 class="card-title float-left text-white">有料無料</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">確定要修改嗎？</p>
                    </div>
                    <div class="modal-footer py-2">
                        {!! Form::open([ 'method'=>'post', 'url'=>'', 'id'=>'hire_type_form' ]) !!}
                        <input type="hidden" id="class" name="class" class="form-control" value="<?=$class_data['class'];?>">
                        <input type="hidden" id="term" name="term" class="form-control" value="<?=$class_data['term'];?>">
                        <button type="button" class="btn mr-2 btn-info pull-left" onclick="location.reload();" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn mr-3 btn-danger">確定</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection