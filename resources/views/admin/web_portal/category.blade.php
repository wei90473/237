@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'web_portal';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">入口網站代碼維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/web_portal" class="text-info">入口網站代碼維護</a></li>
                        <li class="active">班別類別/專長代碼維護</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">班別類別/專長代碼維護</h3></div>
                    <div class="card-body pt-4">
                    <fieldset style="border:groove; padding: inherit">
                        <button type="button" class="btn mobile-100 mb-3 mb-md-0" onclick="Creat()">新增</button>
                        <button type="button" class="btn mobile-100 mb-3 mb-md-0" onclick="ChangeSort()">排序</button>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr>
                                    <th class="text-center" width="80">功能</th>
                                    <th></th>
                                    <th></th>
                                    <th>班別類別/專長代碼</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($category as $key => $va)
                                    <tr >
                                        <!-- 修改 -->
                                        <td id="td{{$key}}" class="text-center" 
                                                    data-serno="{{ $va['serno'] }}" 
                                                    data-alias="{{ $va['alias'] }}" 
                                                    data-name="{{ $va['name'] }}"
                                                    data-category="{{ $va['category'] }}" onclick="Edit(this)">
                                            <a href="#" data-placement="top" data-toggle="tooltip" data-original-title="編輯">
                                                <i class="fa fa-pencil">編輯</i>
                                            </a>
                                        </td>
                                        <td>
                                            @for($i=1;$i< $va['indent'];$i++)
                                            <?='..'?>
                                            @endfor
                                        </td>
                                        <td>{{ $va['category']==''?$va['name']: $va['name'].'('.$va['category'].')'}}</td>
                                        <td>{{ $va['alias']=='Y'?'是':'否'}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </fieldset>
                    <div class="card-footer">
                        <a href="/admin/web_portal">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上頁</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')
    <!-- 資料排序 modal -->
    <div class="modal fade SortModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        {!! Form::open([ 'method'=>'put', 'url'=>'/admin/web_portal/rank', 'id'=>'form2']) !!}
            <div class="modal-dialog" role="document" style="max-width:900px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">排序</h4>
                    </div>
                    <div class="modal-body" height="200px;">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="checkrank()">儲存</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                         <table class="table table-bordered mb-0" height="200px;" id="table_sort" name="table_sort">
                            <thead>
                            <tr>
                                <th>階層</th>
                                <th>班別名稱(中文)</th>    
                                <th>排序</th>                                             
                                </tr>
                            </thead>
                            <tbody>
                            <?php $sequence= 1; ?>   
                            @foreach($category as $va)
                                <tr draggable="true"> 
                                    <input type="hidden" name="serno[]" value="{{ $va['serno'] }}">
                                    <td>
                                        <input type="text" name="indent[]" value="{{ $va['indent'] }}">
                                    </td>
                                    <td>{{ $va['category']==''?$va['name']: $va['name'].'('.$va['category'].')' }}</td>
                                    <td>
                                        <a href="#" class="up">上</a> <a href="#" class="down">下</a>
                                    </td>
                                    <input type="hidden" name="sequence[]" value="{{ $va['sequence']==''?$sequence:$va['sequence'] }}">
                                </tr>
                            <?php $sequence= $sequence+1; ?>      
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="checkrank()">儲存</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>

                </div>
            </div>
        <!-- </div> -->
        {!! Form::close() !!}
    </div>
    <!-- 新增 modal -->
    <div class="modal fade CreatModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        {!! Form::open([ 'method'=>'post', 'url'=>'/admin/web_portal/category', 'id'=>'form3']) !!}
            <div class="modal-dialog" role="document" style="max-width:500px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">新增</h4>
                    </div>
                    <div class="modal-body" >
                        <div class="form-group row">
                            <!-- 名稱 -->
                            <label class="control-label pt-2">名稱<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <input type="text" id="C_name" name="name" class="form-control" autocomplete="off"  placeholder="請輸入名稱" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 名稱 -->
                            <label class="control-label pt-2">班別類別/專長代碼<span class="text-danger">*</span></label>
                            <div class="pt-2" onchange="alias()">
                                <input type="radio" name="alias" value="Y" checked>是
                                <input type="radio" name="alias" value="N">否
                            </div>
                            <div class="col-md-5">
                                <input type="text" id="C_category" name="category" class="form-control" autocomplete="off"  placeholder="請輸入代碼" value="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="actionCreat()">儲存</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>

                </div>
            </div>
        <!-- </div> -->
        {!! Form::close() !!}
    </div>
    <!-- 修改 modal -->
    <div class="modal fade EditModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        {!! Form::open([ 'method'=>'put', 'url'=>'/admin/web_portal/category/999', 'id'=>'form4']) !!}
            <div class="modal-dialog" role="document" style="max-width:500px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">修改</h4>
                    </div>
                    <input type="hidden" class="form-control " id="E_serno" name="E_serno"></input>

                    <div class="modal-body" >
                        <div class="form-group row">
                            <!-- 名稱 -->
                            <label class="control-label pt-2">名稱<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <input type="text" id="E_name" name="E_name" class="form-control" autocomplete="off"  placeholder="請輸入名稱" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 名稱 -->
                            <label class="control-label pt-2">班別類別/專長代碼<span class="text-danger">*</span></label>
                            <div class="pt-2" onchange="alias()">
                                <input type="radio" name="E_alias" value="Y" checked>是
                                <input type="radio" name="E_alias" value="N">否
                            </div>
                            <div class="col-md-5">
                                <input type="text" id="E_category" name="E_category" class="form-control" autocomplete="off"  placeholder="請輸入代碼" value="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="actionEdit()">儲存</button>
                        <button type="button" class="btn btn-danger" onclick="actionDelete()">刪除</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>

                </div>
            </div>
        <!-- </div> -->
        {!! Form::close() !!}
    </div>
    <!-- 刪除 -->
    {!! Form::open([ 'method'=>'delete', 'url'=>'/admin/web_portal/category/999', 'id'=>'deleteform']) !!}
    <input type="hidden" class="form-control " id="D_serno" name="D_serno"></input>
    {!! Form::close() !!}
    

@endsection
@section('js')
<script>

    function alias(){
        if($('input[name=alias]')[0].checked){
            $("#C_category").removeAttr('disabled');
        }else{
            $("#C_category").attr('disabled','true');
        }
    }
    //新增  
    function Creat()
    {
        $('.CreatModal').modal('show');
    };
    function actionCreat(){
        if( $("#C_name").val()!='' && $('input[name=alias]').val()!='' ){
            $('#form3').submit();
        }else{
            alert('請填寫名稱 !!');
            return ;
        }
        
    }

    //修改
    function Edit(row){
        select = row.id;
        var td = document.getElementById(select);
        console.log(td.dataset);
        $("#E_serno").val(td.dataset.serno);
        $("#D_serno").val(td.dataset.serno)
        $("input[name='E_alias']:radio[value='"+td.dataset.alias+"']").prop('checked','true');
        $("input[name=E_name]").val(td.dataset.name);
        $("input[name=E_category]").val(td.dataset.category);
        $("#form4").attr('action', 'http://172.16.10.18/admin/web_portal/category/'+td.dataset.serno);
        $('.EditModal').modal('show');
    };
    
    function actionEdit(){
        if( $("#E_name").val()!='' && $('input[name=Ealias]').val()!='' ){
            $('#form4').submit();
        }else{
            alert('請填寫名稱 !!');
            return ;
        }
    }
    //刪除
    function actionDelete(){
        if( $("#D_serno").val()!=''){
            var serno = $("#D_serno").val();
            $("#deleteform").attr('action', 'http://172.16.10.18/admin/web_portal/category/'+serno);
            $("#deleteform").submit();
        }else{
            alert('查無資料 !!');
            return ;
        }
    }
    //排序  
    function ChangeSort()
    {
        $('.SortModal').modal('show');
    };
    function checkrank(){
        $("#form2").submit();
    }
    $(document).ready(function(){
        $(".up,.down").click(function(){
            var row = $(this).parents("tr:first");
            if ($(this).is(".up")) {
                var classes = $(this).parent().prev().prev().html();
                var change = $(this).parent().parent().prev().children().html();
                row.insertBefore(row.prev());
                
                if(change){
                    var classesval = $("input[name="+classes+"]").val();
                    classesval = parseInt(classesval) -1;
                    $("input[name="+classes+"]").val(classesval);

                    var changeval = $("input[name="+change+"]").val();
                    changeval = parseInt(changeval) +1;
                    $("input[name="+change+"]").val(changeval);
                }
            } else {
                var classes = $(this).parent().prev().prev().html();
                var change = $(this).parent().parent().next().children().html();
                row.insertAfter(row.next());
                if(change){
                    var classesval = $("input[name="+classes+"]").val();
                    classesval = parseInt(classesval) +1;
                    $("input[name="+classes+"]").val(classesval);

                    var changeval = $("input[name="+change+"]").val();
                    changeval = parseInt(changeval) -1;
                    $("input[name="+change+"]").val(changeval);
                }
            }
        });
    });


</script>
@endsection