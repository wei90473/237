@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'teacher_related';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座用餐、住宿、派車資料登錄</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座用餐、住宿、派車資料登錄列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座用餐、住宿、派車資料登錄</h3>
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
                                            需求起訖期間 : {{ $class_data['sdate'] }} ~ {{ $class_data['edate'] }}<br>
                                            班務人員 : {{ $class_data['sponsor'] }}
                                        </li>
                                    </ul>
                                    <br>
                                    <div class="float-left search-float" style="min-width: 1000px;">

                                    </div>

                                    <div class="table-responsive">
                                        <table id="data_table" class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">功能</th>
                                                <th>班務人員確認</th>
                                                <th>講座姓名</th>
                                                <th>狀態</th>
                                                <th>填寫網址連結</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($teacher_list as $row)
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/teacher_related/{{ $row['id'] }}_{{ $class_data['class_weeks_id'] }}/edit1" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span onclick="$('#confirm_form').attr('action', '/admin/teacher_related/changeConfirm/<?=$row['id'];?>');" data-toggle="modal" data-target="#confirm_modol">
                                                        <input type="checkbox" id="confirm" name="confirm" style="min-width:20px; margin-left:5px;" <?=($row['confirm']=='Y')?'checked':'';?> value="" >
                                                        </span>
                                                    </td>
                                                    <td>{{ $row['cname'] }}</td>
                                                    <td>
                                                        <?php if($row['id_isset'] == 'Y'){?>
                                                        講座已填寫
                                                        <?php }else{ ?>
                                                        講座未填寫
                                                        <?php } ?>
                                                    </td>
                                                    <td><b id="copythis_<?=$row['id'];?>">{{env('WEB_URL')}}Teacher/teacher_login/<?=base64_encode($class_data['class'].'_'.$class_data['term'].'_'.$row['id']);?></b>
                                                        <button class="btn btn-secondary" onclick="CopyTextToClipboard('copythis_<?=$row['id'];?>')">複製</button>
                                                    </td>

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

        function CopyTextToClipboard(id) {

            var TextRange = document.createRange();
            TextRange.selectNode(document.getElementById(id));
            sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(TextRange);
            document.execCommand("copy");
            alert("複製完成！")

        }

    </script>

    <div id="confirm_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content p-0 b-0">
                <div class="card mb-0">
                    <div class="card-header bg-warning">
                        <h3 class="card-title float-left text-white">班務人員確認</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">確認修改？</p>
                    </div>
                    <div class="modal-footer py-2">
                        {!! Form::open([ 'method'=>'post', 'url'=>'', 'id'=>'confirm_form' ]) !!}
                        <input type="hidden" id="class_weeks_id" name="class_weeks_id" class="form-control" value="<?=$class_data['class_weeks_id'];?>">
                        <button type="button" class="btn mr-2 btn-info pull-left" onclick="location.reload();" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn mr-3 btn-danger">確定</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection