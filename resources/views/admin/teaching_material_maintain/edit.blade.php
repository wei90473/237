@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'teaching_material_maintain';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">教材交印處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li class="active">教材交印參數處理</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">教材交印參數處理</h3></div>
                    <div class="card-body pt-4">
                        <form method="get" id="search_form">
                            <!-- 院區 -->
                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                <div class="input-group col-5">
                                    <div class="pull-left input-group-prepend">
                                        <span class="input-group-text">院區</span>
                                    </div>
                                    <select id="branch" name="branch" class="browser-default custom-select" >
                                        @foreach(config('app.branch') as $k => $va)
                                            <option value="{{ $k }}" {{ old('branch', (isset($queryData->branch))? $queryData->branch : '') == $k? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                            </div>
                        </form>
                        {!! Form::open([ 'method'=>'put', 'url'=>'/admin/teaching_material_maintain/edit/'.$queryData->branch, 'id'=>'form']) !!}
                        <div class="float-left">
                            <button type="button" class="btn btn-sm btn-info" onclick="submitForm('#form');" ><i class="fa fa-save pr-2"></i>儲存</button>
                            <!-- 排序 -->
                            <button type="button" class="btn btn-sm btn-info" onclick="ChangeSort();">項目排序</button>
                            <!-- 新增 -->
                            <button type="button" class="btn btn-sm btn-info" onclick="creat();">新增項目</button>
                            <!-- 刪除 -->
                            <button type="button" class="btn btn-sm btn-danger" onclick="del();">刪除項目</button>
                        </div>
                        <!-- 課程清單 -->
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>項目</th>
                                    <th>單位</th>
                                    <th>合約單價</th>
                                    <th>備註</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($datalist as $va)
                                    <tr><input type="hidden" name="serno[]" value="{{ $va['serno'] }}"> 
                                        <input type="hidden" name="type[]" value="{{ $va['type'] }}">  
                                        <td>{{$va['title']}}</td>
                                        <td><input type="text" name="item[]" class="form-control input-max" style="width: 450px;" autocomplete="off" value="{{$va['item']}}" ></td>
                                        @if($va['type']=='C')
                                        <td><input type="text" name="unit[]" class="form-control input-max" style="width: 70px;" autocomplete="off" value="{{$va['unit']}}" ></td>
                                        @else
                                        <td><input type="text" name="unit[]" class="form-control input-max" style="width: 70px;" autocomplete="off" value="" ></td>
                                        @endif
                                        @if($va['type']=='C')
                                        <td><input type="text" name="price[]" class="form-control input-max" style="width: 70px;" autocomplete="off" value="{{$va['price']}}" ></td>
                                        <td><input type="text" name="remark[]" class="form-control input-max" autocomplete="off" value="{{$va['remark']}}" ></td>
                                        @else
                                        <td><input type="text" name="price[]" class="form-control input-max" style="width: 70px;" autocomplete="off" value="" readonly></td>
                                        <td><input type="text" name="remark[]" class="form-control input-max" autocomplete="off" value="" readonly ></td>
                                        @endif
                                        
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {!! Form::close() !!}
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-sm btn-info" onclick="submitForm('#form');" ><i class="fa fa-save pr-2"></i>儲存</button>
                        <!-- 排序 -->
                        <button type="button" class="btn btn-sm btn-info" onclick="ChangeSort();">項目排序</button>
                        <!-- 新增 -->
                        <button type="button" class="btn btn-sm btn-info" onclick="creat();">新增項目</button>
                        <!-- 刪除 -->
                        <button type="button" class="btn btn-sm btn-danger" onclick="del();">刪除項目</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 資料排序 modal -->
    <div class="modal fade" id="SortModal" role="dialog" css="width:300px;">
        {!! Form::open([ 'method'=>'put', 'url'=>'/admin/teaching_material_maintain/changesort/'.$queryData->branch, 'id'=>'form2']) !!}
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
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
                                    <th>項目</th>
                                    <th>排序</th>                                             
                                </tr>
                            </thead>
                            <tbody>
                            @if(isset($datalist)) 
                            <?php $rank= 1; ?>   
                            @foreach($datalist as $va)

                                <tr draggable="true">                                               
                                    <td>{{$va['item']}}</td>
                                    <td>
                                        <a href="#" class="up">上</a> <a href="#" class="down">下</a>
                                    </td>
                                     <input type="hidden" name="{{$va['serno']}}" value="{{ $va['sequence']==''?$rank:$va['sequence'] }}">
                                </tr>
                            <?php $rank= $rank+1; ?>      
                            @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="submitForm('#form2');">儲存</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>

                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- 新增 modal -->
    <div class="modal fade" id="CreatModal" role="dialog">
        {!! Form::open([ 'method'=>'post', 'url'=>'/admin/teaching_material_maintain/creat/'.$queryData->branch, 'id'=>'form3']) !!}
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <div class="modal-content">
                 <!-- form start -->  
                    <div class="modal-header">
                        <h4 class="modal-title">新增</h4>
                    </div>
                    <div class="modal-body">
                        <label>項目<span class="text-danger">*</span><input type="text" class="form-control number-input-max" name="item" required></input></label>
                        <label>單位<input type="text" class="form-control number-input-max" name="unit"></input></label>
                        <label>合約單價<input type="text" class="form-control number-input-max" name="price" disabled></input></label>
                        <label>備註<input type="text" class="form-control number-input-max" name="remark" disabled></input></label>
                        <label>項目類型<span class="text-danger">*</span></label>
                        <label><input type="radio" name="type" value="A" checked onclick="selecttype(1)">A-大項</label>
                        <label><input type="radio" name="type" value="B" onclick="selecttype(2)">B-中項</label>
                        <label><input type="radio" name="type" value="C" onclick="selecttype(3)">C-細項</label>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="submitForm('#form3');">儲存</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- 刪除 modal -->
    <div class="modal fade" id="DelModal" role="dialog">
        {!! Form::open([ 'method'=>'delete', 'url'=>'/admin/teaching_material_maintain/edit/'.$queryData->branch, 'id'=>'form4']) !!}
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <div class="modal-content">
                 <!-- form start -->  
                    <div class="modal-header">
                        <h4 class="modal-title">刪除</h4>
                    </div>
                    <div class="modal-body">
                        <select class="browser-default custom-select" name="serno">
                            <option value="select">請選擇刪除項目</option>
                            @foreach($datalist as  $va)
                                <option value="{{ $va['serno'] }}">{{ $va['item'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="submitForm('#form4');">刪除</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript">
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
    function selecttype(type){
        if(type==3){
            $("input[name=price]").removeAttr("disabled");
            $("input[name=remark]").removeAttr("disabled");
        }else{
            $("input[name=price]").attr('disabled', true);
            $("input[name=remark]").attr('disabled', true);
        }
    }    
    //排序  
    function ChangeSort()
    {
        $('#SortModal').modal('show');
    };
    
    function creat()
    {
        $('#CreatModal').modal('show');
    };
    
    function del()
    {
        $('#DelModal').modal('show');
    };
</script>