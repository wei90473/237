@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'class_group';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">重覆參訓檢核群組維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/class_group" class="text-info">重覆參訓檢核群組維護</a></li>
                        <li class="active">重覆參訓檢核群組編輯</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/class_group/edit/'.$data['0']['groupid'], 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/class_group/edit', 'id'=>'form']) !!}
            @endif
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">重覆參訓檢核群組編輯</h3></div>
                    <div class="card-body pt-4">
                        <!-- 群組名稱 -->
                        <div class="float-md mobile-100 row mr-1 mb-3 ">
                            <div class="input-group col-6">
                                <div class="pull-left input-group-prepend">
                                    <span class="input-group-text">群組名稱</span>
                                </div>
                                <input type="hidden" id="groupid" name="groupid" class="form-control" autocomplete="off" value="{{ $data['0']['groupid'] }}" >
                                <input type="text" id="class_group" name="class_group" class="form-control" autocomplete="off" value="{{ $data['0']['class_group'] }}" >
                            </div>
                        </div>
                        <button class="btn mobile-100 mb-3 mb-md-0" onclick="newclass()" type="button">新增班別</button>
                        <!-- 課程清單 -->
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr bgcolor="#99FFFF">
                                    <th>功能</th>
                                    <th>班號</th>
                                    <th>班別名稱</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $key =>$va)
                                    @if($key=='0')
                                    @else
                                    <tr>
                                        <!-- 刪除 -->
                                        <td class="text-center">
                                            <span onclick="$('#del_form').attr('action', '/admin/class_group/edit/{{ $va['id'] }}');" data-toggle="modal" data-target="#del_modol" >
                                                <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </span>
                                            </span>
                                        </td>
                                        <td>{{ $va['class'] }}</td>
                                        <td>{{ $va['name'] }}</td>
                                    </tr>
                                     @endif
                                    @endforeach
                                
                               
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/class_group">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
                        </a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            
        </div>
    </div>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

    <!-- 新增 -->
    <div class="modal fade" id="CreateModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <!-- form start -->
                {!! Form::open([ 'method'=>'POST', 'url'=>'/admin/class_group/edit/'.$data['0']['groupid'], 'id'=>'form2']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title">新增類別</h4> -->
                    </div>
                    <div class="modal-body">
                        班號<span class="text-danger">*</span>
                        <select id="class" name="class" >
                           
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="actionStore()">儲存</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">關閉</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

@endsection
@section('js')
<script type="text/javascript">
    $(function (){
        var page = 1;
        // 初始化階層樹
        $("#class").select2({
            language: 'zh-TW',
            width: '100%',
            // 最多字元限制
            maximumInputLength: 10,
            // 最少字元才觸發尋找, 0 不指定
            minimumInputLength: 0,
            // 當找不到可以使用輸入的文字
            // tags: true,
            placeholder: '請輸入名稱...',
            // AJAX 相關操作
            ajax: {
                url: '/admin/field/getData/t01tbs',
                type: 'get',
                // 要送出的資料
                data: function (params){
                    console.log(params);
                    // 在伺服器會得到一個 POST 'search' 
                    return {
                        class_or_name: params.term,
                        page: params.page 
                    };
                },
                processResults: function (data, params){

                    // 一定要返回 results 物件
                    return {
                        results: data,
                        // 可以啟用無線捲軸做分頁
                        pagination: {
                            more: true
                        }
                    }
                }
            }
        });

        $("#check_class").select2({
            language: 'zh-TW',
            width: '100%',
            // 最多字元限制
            maximumInputLength: 10,
            // 最少字元才觸發尋找, 0 不指定
            minimumInputLength: 0,
            // 當找不到可以使用輸入的文字
            // tags: true,
            placeholder: '請輸入名稱...',
            // AJAX 相關操作
            ajax: {
                url: '/admin/field/getData/t01tbs',
                type: 'get',
                // 要送出的資料
                data: function (params){
                    console.log(params);
                    // 在伺服器會得到一個 POST 'search' 
                    return {
                        class_or_name: params.term,
                        page: params.page 
                    };
                },
                processResults: function (data, params){

                    // 一定要返回 results 物件
                    return {
                        results: data,
                        // 可以啟用無線捲軸做分頁
                        pagination: {
                            more: true
                        }
                    }
                }
            }
        });
    })

    function newclass() {
        $("#c_name").val("");
        $('#CreateModal').modal('show');
    };
    //新增
    function actionStore(){
        if( $("#c_code").val()!='' && $("#c_name").val()!=''){
            $("#form2").submit();
        }else{
            alert('請輸入班號 !!');
            return ;
        }
    }


    
</script>
@endsection