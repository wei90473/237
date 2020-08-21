@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'itineracy_schedule';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">巡迴研習</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/itineracy_schedule" class="text-info">實施日程表</a></li>
                        <li><a href="/admin/itineracy_schedule/edit/{{$queryData['yerly'].$queryData['term']}}" class="text-info">編輯日程表</a></li>
                        <li><a href="/admin/itineracy_schedule/edit/city/{{$queryData['yerly'].$queryData['term'].$queryData['city']}}" class="text-info">縣市別</a></li>
                        <li class="active">設定課程</li>
                        <!-- 主題數量上限 -->
                        <input type="hidden" id="topics" name="topics" value="{{ $queryData['topics'] }}">
                    </ol>
                </div>
            </div>
            <!-- 提示訊息 -->
            @include('admin/layouts/alert')
            <!-- form start -->
            <div class="col-md-10 offset-md-1 p-0">
                {!! Form::open([ 'method'=>'PUT', 'url'=>'/admin/itineracy_schedule/edit/city/settingclass/'.$queryData['class']]) !!}
                <div class="card">
                    <div class="card-body pt-4">
                        <!-- 年度 -->
                        <div class="form-group row">
                            <label class="col-sm-1 control-label pt-2">年度<span class="text-danger">*</span></label>
                            <div class="col-md-1">
                                <input type="text" class="form-control number-input-max" name="yerly" value="{{ old('yerly', (isset($queryData['yerly']))? $queryData['yerly'] : 109) }}" readonly>
                            </div>
                            <!-- 期別 -->
                            <label class="col-sm-1 control-label text-md pt-2">期別<span class="text-danger">*</span></label>
                            <div class="col-md-1">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" name="term" min="1" max="9" placeholder="請輸入期別" value="{{ old('term', (isset($queryData['term']))? $queryData['term'] : 1) }}" readonly>
                                </div>
                            </div>
                            <!-- 巡迴計畫名稱 -->
                            <label class="col-sm-2 control-label pt-2">巡迴計畫名稱<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" autocomplete="off"  placeholder="請輸入計畫名稱" value="{{ old('name', (isset($queryData['name']))? $queryData['name'] : '') }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 縣市別 -->
                            <label class="col-sm-1 control-label pt-2">縣市別<span class="text-danger">*</span></label>
                            <input type="hidden" name="city" value="{{ $queryData['city'] }}">
                            <div class="col-md-2">
                                <input type="text" class="form-control" autocomplete="off"  placeholder="請輸入縣市別" value="{{ config('app.city.'.$queryData['city']) }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <button  class="btn btn-sm btn-info"></i>儲存</button>
                                <a href="/admin/itineracy_schedule/edit/city/{{$queryData['yerly'].$queryData['term'].$queryData['city']}}">
                                    <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i>取消</button>
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive pt-2">
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr>
                                    <th class="text-center" width="80"><input type="checkbox" onclick="checkall(this)" >選取 研習方式</th>
                                    <th>單元</th>
                                    <th>類別</th>
                                    <th>課程</th>
                                    <th>期望議題(非必填)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $va)
                                    <tr>
                                        <!-- 修改 -->
                                        <td class="text-center">
                                        <input type="checkbox" name="{{ $va->id }}" value="1" autocomplete="off" onclick="doCheck(this)" {{ $va->serch==1? 'checked':''}} >
                                        </td>
                                        <td>{{ $va->name3 }}</td>
                                        <td>{{ $va->name2 }}</td>
                                        <td>{{ $va->name1 }}</td>
                                        <td><input type="text" name="remake{{ $va->id }}" class="form-control number-input-max" placeholder="" value="{{ $va->remake }}" ></td>
                                    </tr>
                                @endforeach
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button  class="btn btn-sm btn-info">儲存</button>
                        <a href="/admin/itineracy_schedule/edit/city/{{$queryData['yerly'].$queryData['term'].$queryData['city']}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i>取消</button>
                        </a>
                    </div>    
                </div>
                {!! Form::close() !!} 
            </div>
        </div>
    </div>
    

@endsection
@section('js')
<script>
    var checked = 0;
    //全選
    function checkall(e)
    {
        for(i=0; i<$("input[type=checkbox]").length; i++ ){
             $("input[type=checkbox]")[i].checked = e.checked;
        }
    }
    var topics= $("#topics").val(); 
    function doCheck(obj) { 
        obj.checked? checked++ :checked--; 
        if(checked>topics){ 
            obj.checked=false; 
            alert("超過主題數量上限"); 
            checked--; 
        } 
    }
    $(document).ready(function() {
        for(i=0; i<$("input[type=checkbox]").length; i++ ){
            if( $("input[type=checkbox]")[i].checked ){
                checked ++;
            }
        }
        console.log(checked);
    }); 
</script>
@endsection