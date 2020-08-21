@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'itineracy_annual';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">巡迴研習</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/itineracy_annual" class="text-info">年度主題設定</a></li>
                        @if ( isset($data) )
                        <li class="active">年度主題詳細設定</li>
                        @else
                        <li class="active">年度主題設定新增</li>
                        @endif
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')
            <!-- form start -->



            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">{{$queryData['yerly']}}年度主題詳細設定</h3></div>

                    <div class="card-body pt-4">
                        <button type="button" onclick="Create()" class="btn btn-sm btn-info"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                        <a href="/admin/itineracy_annual">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
                        </a>
                        <div class="table-responsive pt-2">
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr>
                                    <th class="text-center" width="80">功能</th>
                                    <th>項次</th>
                                    <th>主題</th>
                                    <th>單元</th>
                                    <th>類別</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($data))
                                @foreach($data as $va)

                                    <?php //$startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                    <tr>
                                        <!-- 修改 -->
                                        <td class="text-center">
                                        <input type="hidden"  name="theme{{ $va->id}}" value="{{$va->type1}}">
                                        <input type="hidden"  name="items{{ $va->id}}" value="{{$va->items}}">
                                        <input type="hidden"  name="unit{{ $va->id}}" value="{{$va->type3}}">
                                        <input type="hidden"  name="category{{ $va->id}}" value="{{$va->type2}}">
                                            <a href="#">
                                                <i class="fa fa-pencil" onclick="Edit({{ $va->id}}) " >編輯</i>
                                            </a>
                                        </td>
                                        <td>{{ $va->items }}</td>
                                        <td>{{ $va->name1 }}</td>
                                        <td>{{ $va->name3 }}</td>
                                        <td>{{ $va->name2 }}</td>
                                    </tr>
                                @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 新增 -->
    <div class="modal fade" id="CreateModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <!-- form start -->
                {!! Form::open([ 'method'=>'POST', 'url'=>'/admin/itineracy_annual/setting', 'id'=>'form']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title">新增類別</h4> -->
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="yerly" name="yerly" value="{{$queryData['yerly']}}">
                        <input type="hidden" id="term" name="term" value="{{$queryData['term']}}">
                        主題編號<span class="text-danger">*</span><select id="theme" name="theme" required class="browser-default custom-select">
                            <option value="0" selected>請選擇</option>
                        @foreach($themelist as $key => $va)
                            <option value="{{$va['code']}}">{{ $va['name'] }}</option>
                        @endforeach
                        </select>
                        項次<span class="text-danger">*</span><input type="text" class="form-control" id="items" name="items" required autocomplete="off" value="{{$queryData['items']}}"></input>
                        單元<select id="unit" name="unit" class="browser-default custom-select">
                            <option value="0">請選擇</option>
                        @foreach($unitlist as $key => $va)
                            <option value="{{$va['code']}}">{{ $va['name'] }}</option>
                        @endforeach
                        </select>
                        類別<select id="category" name="category" class="browser-default custom-select">
                            <option value="0">請選擇</option>
                        @foreach($categorylist as $key => $va)
                            <option value="{{$va['code']}}">{{ $va['name'] }}</option>
                        @endforeach
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
    <!-- 修改 -->
    <div class="modal fade" id="EditModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <!-- form start -->
                {!! Form::open([ 'method'=>'PUT', 'url'=>'/admin/itineracy_annual/setting/999', 'id'=>'form2']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title">新增類別</h4> -->
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="E_id" name="E_id" value="">
                        主題編號<span class="text-danger">*</span><select id="E_theme" name="E_theme" class="browser-default custom-select">
                            <option value="0">請選擇</option>
                        @foreach($themelist as $key => $va)
                            <option value="{{$va['code']}}">{{ $va['name'] }}</option>
                        @endforeach
                        </select>
                        項次<span class="text-danger">*</span><input type="text" class="form-control number-input-max" id="E_items" name="E_items"></input>
                        單元<select id="E_unit" name="E_unit" class="browser-default custom-select">
                            <option value="0">請選擇</option>
                        @foreach($unitlist as $key => $va)
                            <option value="{{$va['code']}}">{{ $va['name'] }}</option>
                        @endforeach
                        </select>
                        類別<select id="E_category" name="E_category" class="browser-default custom-select">
                            <option value="0">請選擇</option>
                        @foreach($categorylist as $key => $va)
                            <option value="{{$va['code']}}">{{ $va['name'] }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="actionEdit()">修改</button>
                        <button type="button" class="btn btn-primary btn-danger" onclick="actionDelete()">刪除</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">關閉</button>
                    </div>
                </div>
                {!! Form::close() !!}
                <!-- 刪除 -->

                {!! Form::open([ 'method'=>'delete', 'url'=>'/admin/itineracy_annual/setting/999', 'id'=>'deleteform']) !!}
                <input type="hidden" class="form-control " id="D_id" name="D_id"></input>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection
@section('js')
<script>
    function Create() {
        $("#theme").val("0");
        $("#unit").val("");
        $("#category").val("");
        $('#CreateModal').modal('show');
    };

    function Edit(id) {
        var theme = $("input[name=theme"+id+"]").val();
        var items = $("input[name=items"+id+"]").val();
        var unit = $("input[name=unit"+id+"]").val();
        var category = $("input[name=category"+id+"]").val();

        console.log(theme);
        $("#E_theme").val(theme);
        $("#E_items").val(items);
        $("#E_unit").val(unit);
        $("#E_category").val(category);
        $("#E_id").val(id);
        $("#D_id").val(id);
        $("#form2").attr('action', '/admin/itineracy_annual/setting/'+id);
        $('#EditModal').modal('show');
    };

    //新增
    function actionStore(){
        if( $("#theme").val()=='0' ){
            alert('請選擇主題 !!');
            return ;
        }else if($("#items").val()==''){
             alert('項次不可為空!!');
            return ;
        }else{
            $("#form").submit();
        }
    }
    //修改
    function actionEdit(){
        if( $("#E_theme").val()==''){
            alert('請選擇主題 !!');
            return ;
        }else if($("#E_items").val()==''){
            alert('項次不可為空!!');
            return ;
        }else{
            $("#form2").submit();
        }
    }
    //刪除
    function actionDelete(){
        if( $("#D_id").val()!='' ){
            var code = $("#D_id").val();
            $("#deleteform").attr('action', '/admin/itineracy_annual/setting/'+code);
            $("#deleteform").submit();
        }else{
            alert('請輸入代號名稱 !!');
            return ;
        }
    }
    //檢查
    function checksigninsubmit(){
        if( $("#edate").val()=='' || $("#sdate").val()=='' ){
            alert('請填寫日期');
            return;
        }else if( $("#edate").val() < $("#sdate").val() ){
            alert('日期錯誤');
            return;
        }
        if( $("#name").val()=='' ){
            alert('請填寫名稱');
            return;
        }

        $("#form").submit();
    }

    $(document).ready(function() {
            $("#sdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker1').click(function(){
                $("#sdate").focus();
            });
            $("#edate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker2').click(function(){
                $("#edate").focus();
            });

     });
</script>
@endsection
