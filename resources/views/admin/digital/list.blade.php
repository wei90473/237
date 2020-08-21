@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'digital';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">數位時數處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">數位時數處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>數位時數處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <div class="float-md-left">
                                        @if(empty($elearn_classes->toArray()))
                                            無數位課程需要完成
                                        @endif
                                        <!-- 編輯 -->
                                        <!-- <button onclick="$('.show').hide();$('.edit').show();" type="button" class="btn btn-primary btn-sm mb-3 show"><i class="fa fa-pencil fa-lg pr-2"></i>編輯</button> -->

                                        <!-- 儲存 -->
                                        

                                    </div>

                                    {!! Form::open([ 'method'=>'put', 'url'=>"/admin/digital/student/{$t04tb->class}/{$t04tb->term}", 'id'=>'form']) !!}

                                    @foreach($elearn_classes as $elearn_class)
                                        <div style="margin-top:10px;">
                                        <font>課程代碼：{{$elearn_class->code}}  課程名稱：{{$elearn_class->name}}</font>
                                        </div>
                                        <div class="table-responsive" style="height:400px; border:1px solid #000; padding:5px;">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                <tr>
                                                    <th class="text-center" width="70">學號</th>
                                                    <th>姓名</th>
                                                    <th>是否完成數位學習</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @if(!empty($t13tbs))
                                                        @foreach($t13tbs as $t13tb)
                                                        <tr>
                                                            <td>{{$t13tb->no}}</td>
                                                            <td>{{$t13tb->m02tb->cname}}</td>
                                                            <td>
                                                                <input type="checkbox" name="elearn_status[{{ $elearn_class->id }}][{{ $t13tb->idno }}]" style="width:20px; height:20px;" value="Y" {{ (!empty($elearn_historys[$elearn_class->id][$t13tb->idno]) && $elearn_historys[$elearn_class->id][$t13tb->idno] == "Y") ? 'checked' : '' }} >
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach
                                    {!! Form::close() !!}

                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button onclick="$('#form').submit()" type="button" class="btn btn-primary btn-sm"><i class="fa fa-save fa-lg pr-2"></i>儲存</button>
                            <a href="/admin/digital/class_list">
                                <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                            </a>                            
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection


@section('js')
    <script>

    </script>

@endsection