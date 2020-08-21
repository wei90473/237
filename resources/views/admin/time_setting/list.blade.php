@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'time_setting';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">時段設定</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">時段設定</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>時段設定</h3>
                        </div>
                        <input type="hidden" name="code" id="code" value="">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="search-float">
                                        <div class="float-left">
                                            <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0" id="taipaiBT" onclick="getbranch(1)">台北院區</button>
                                            <button type="button" class="btn mobile-100 mb-3 mb-md-0" id="nantouBT" onclick="getbranch(2)">南投院區</button>
                                        </div>    
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="taipai" style=" display: inline-table;">
                                            <thead>
                                            <tr>
                                                <th class="text-center">功能</th>
                                                <th>時段</th>
                                                <th>時間</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="#">
                                                            <i class="fa fa-pencil" onclick="Edit(1)" >編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>上午</td>
                                                    <td>{{ substr($data->tmst,0,2).'：'.substr($data->tmst,-2).' - '.substr($data->tmet,0,2).'：'.substr($data->tmet,-2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="#">
                                                            <i class="fa fa-pencil" onclick="Edit(2)" >編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>下午</td>
                                                    <td>{{ substr($data->tast,0,2).'：'.substr($data->tast,-2).' - '.substr($data->taet,0,2).'：'.substr($data->taet,-2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="#">
                                                            <i class="fa fa-pencil" onclick="Edit(3)" >編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>晚上</td>
                                                    <td>{{ substr($data->tnst,0,2).'：'.substr($data->tnst,-2).' - '.substr($data->tnet,0,2).'：'.substr($data->tnet,-2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered mb-0" id="nantou" style="display: none">
                                            <thead>
                                            <tr>
                                                <th class="text-center">功能</th>
                                                <th>時段</th>
                                                <th>時間</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="#">
                                                            <i class="fa fa-pencil" onclick="Edit(4)" >編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>上午</td>
                                                    <td>{{ substr($data->nmst,0,2).'：'.substr($data->nmst,-2).' - '.substr($data->nmet,0,2).'：'.substr($data->nmet,-2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="#">
                                                            <i class="fa fa-pencil" onclick="Edit(5)" >編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>下午</td>
                                                    <td>{{ substr($data->nast,0,2).'：'.substr($data->nast,-2).' - '.substr($data->naet,0,2).'：'.substr($data->naet,-2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="#">
                                                            <i class="fa fa-pencil" onclick="Edit(6)" >編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>晚上</td>
                                                    <td>{{ substr($data->nnst,0,2).'：'.substr($data->nnst,-2).' - '.substr($data->nnet,0,2).'：'.substr($data->nnet,-2) }}</td>
                                                </tr>
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
    <!-- 修改 -->
    <div class="modal fade" id="EditModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <!-- form start -->
                {!! Form::open([ 'method'=>'PUT', 'url'=>'/admin/time_setting', 'id'=>'form1']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">時段設定</h4>
                    </div>
                    <div class="modal-body" id="edit1"  style="display: block">
                        <div class="form-group row">
                            <label class="control-label pt-2">時段：上午</label>
                        </div>
                        <div class="form-group row">
                            <label class="control-label pt-2">時間(起)：</label><span class="text-danger">*</span>
                            <div class="col-md-5">
                                <input type="text" class="form-control number-input-max" id="tmst" name="tmst" value="{{$data['tmst']}}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="4"></input>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label pt-2">時間(迄)：</label><span class="text-danger">*</span>
                            <div class="col-md-5">
                                <input type="text" class="form-control number-input-max" id="tmet" name="tmet" value="{{$data['tmet']}}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="4"></input>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body" id="edit2"  style="display: none">
                        <div class="form-group row">
                            <label class="control-label pt-2">時段：下午</label>
                        </div>
                        <div class="form-group row">
                            <label class="control-label pt-2">時間(起)：</label><span class="text-danger">*</span>
                            <div class="col-md-5">
                                <input type="text" class="form-control number-input-max" id="tast" name="tast" value="{{$data['tast']}}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="4"></input>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label pt-2">時間(迄)：</label><span class="text-danger">*</span>
                            <div class="col-md-5">
                                <input type="text" class="form-control number-input-max" id="taet" name="taet" value="{{$data['taet']}}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="4"></input>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body" id="edit3"  style="display: none">
                        <div class="form-group row">
                            <label class="control-label pt-2">時段：晚上</label>
                        </div>
                        <div class="form-group row">
                            <label class="control-label pt-2">時間(起)：</label><span class="text-danger">*</span>
                            <div class="col-md-5">
                                <input type="text" class="form-control number-input-max" id="tnst" name="tnst" value="{{$data['tnst']}}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="4"></input>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label pt-2">時間(迄)：</label><span class="text-danger">*</span>
                            <div class="col-md-5">
                                <input type="text" class="form-control number-input-max" id="tnet" name="tnet" value="{{$data['tnet']}}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="4"></input>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body" id="edit4"  style="display: block">
                        <div class="form-group row">
                            <label class="control-label pt-2">時段：上午</label>
                        </div>
                        <div class="form-group row">
                            <label class="control-label pt-2">時間(起)：</label><span class="text-danger">*</span>
                            <div class="col-md-5">
                                <input type="text" class="form-control number-input-max" id="nmst" name="nmst" value="{{$data['nmst']}}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="4"></input>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label pt-2">時間(迄)：</label><span class="text-danger">*</span>
                            <div class="col-md-5">
                                <input type="text" class="form-control number-input-max" id="nmet" name="nmet" value="{{$data['nmet']}}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="4"></input>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body" id="edit5"  style="display: none">
                        <div class="form-group row">
                            <label class="control-label pt-2">時段：下午</label>
                        </div>
                        <div class="form-group row">
                            <label class="control-label pt-2">時間(起)：</label><span class="text-danger">*</span>
                            <div class="col-md-5">
                                <input type="text" class="form-control number-input-max" id="nast" name="nast" value="{{$data['nast']}}"onkeyup="this.value=this.value.replace(/[^\d]/g,'')"  maxlength="4"></input>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label pt-2">時間(迄)：</label><span class="text-danger">*</span>
                            <div class="col-md-5">
                                <input type="text" class="form-control number-input-max" id="naet" name="naet" value="{{$data['naet']}}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="4"></input>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body" id="edit6"  style="display: none">
                        <div class="form-group row">
                            <label class="control-label pt-2">時段：晚上</label>
                        </div>
                        <div class="form-group row">
                            <label class="control-label pt-2">時間(起)：</label><span class="text-danger">*</span>
                            <div class="col-md-5">
                                <input type="text" class="form-control number-input-max" id="nnst" name="nnst" value="{{$data['nnst']}}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="4"></input>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label pt-2">時間(迄)：</label><span class="text-danger">*</span>
                            <div class="col-md-5">
                                <input type="text" class="form-control number-input-max" id="nnet" name="nnet" value="{{$data['nnet']}}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="4"></input>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="actionEdit()">儲存</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">關閉</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    
    

@endsection
@section('js')
<script>
    
    
    function getbranch(n) {
        if(n=='1'){
            $("#nantouBT").removeClass('btn-primary');
            $("#taipaiBT").addClass('btn-primary');
            $("#taipai").css('display','inline-table');
            $("#nantou").css('display','none');
        }else if(n=='2'){
            $("#taipaiBT").removeClass('btn-primary');
            $("#nantouBT").addClass('btn-primary');
            $("#taipai").css('display','none');
            $("#nantou").css('display','inline-table');
        }else{
            alert('error');
            return false;
        }
    };
  
    function Edit(code) {
        $('#code').val(code);
        for (var i = 6; i >= 1; i--) {
            if(i == code){
                $("#edit"+i).css('display','block');
            }else{
                $("#edit"+i).css('display','none');
            }
        }
        $('#EditModal').modal('show');
    };

    //修改
    function actionEdit(){
        var code = $('#code').val();
        for (var i = 1; i < 7; i++) {
            if(i == code){
                var stime = i*2+1;
                var etime = i*2+2;
                if($('input')[stime].value >= $('input')[etime].value ){
                    alert('日期格式錯誤');
                    return false;
                } 
            }
        }
        for (var i = 1; i < 7; i++) {
            if(i != code){
                $("#edit"+i).html('');
            }
        }
        $("#form1").submit();
        
    }
    
</script>
@endsection