@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'review_apply';?>
<style>
.search-float input{
    min-width:1px;
}
</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">報名審核處理</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">報名審核處理</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>報名審核處理</h3>
                    </div>

                    <div class="card-body">
                        <div style="border: 1px solid #FFF; padding: 10px; padding-left: 0px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            期別：{{ $t04tb->term }}<br>
                            分班名稱：<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            委訓機關：{{ $t04tb->client }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：
                        </div>
                        <div>
                        <form>     
                            <div class="search-float">
                                <div class="float-md mobile-100 row mr-1 mb-3">      
                                    <div class="input-group col-3">
                                        <div class="input-group-prepend">
                                        <label class="input-group-text">身分證號</label>
                                        </div>
                                        <input class="form-control" type="text" name="idno" value="{{ $queryData['idno'] }}">
                                    </div>  
                                    <div class="input-group col-2">
                                        <div class="input-group-prepend">
                                        <label class="input-group-text">姓名</label>
                                        </div>
                                        <input class="form-control" type="text" name="name" value="{{ $queryData['name'] }}">
                                    </div>     
                                    <div class="input-group col-5">
                                        <!-- <div class="input-group-prepend"> -->
                                        <label class="input-group-text">選項</label>
                                        <!-- </div> -->
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="prove" value="" checked>
                                            <label class="form-check-label" for="exampleRadios1">
                                                所有
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="prove" value="N"
                                            @if($queryData['prove'] == "N")
                                                checked
                                            @endif 
                                            >
                                            <label class="form-check-label" for="exampleRadios1">
                                                未審核
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="prove" value="S"
                                            @if($queryData['prove'] == "S")
                                                checked
                                            @endif                                             
                                            >
                                            <label class="form-check-label" for="exampleRadios1">
                                                已匯入
                                            </label>
                                        </div>                                                                                
                                    </div>                                                                                                                                                                                                                   
                                </div>
                                <div class="float-md mobile-100 row mr-1 mb-3">
                                    <div class="input-group col-2">
                                        <div class="input-group-prepend">
                                        <label class="input-group-text">服務機關代碼</label>
                                        </div>
                                        <input class="form-control" type="text" name="enrollorg" value="{{ $queryData['enrollorg'] }}">
                                    </div>
                                    <div class="input-group col-3">
                                        <div class="input-group-prepend">
                                        <label class="input-group-text">服務機關名稱</label>
                                        </div>
                                        <input class="form-control" type="text" name="enrollname" value="{{ $queryData['enrollname'] }}">
                                    </div>   
                                    <div class="input-group col-3">
                                        <div class="input-group-prepend">
                                        <label class="input-group-text">主管機關</label>
                                        </div>
                                        <input class="form-control" type="text" name="organ_name" value="{{ $queryData['organ_name'] }}">
                                    </div>                                                                                                              
                                </div>
                                <div class="float-md mobile-100 row mr-1 mb-3">
                                    <div class="input-group col-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text">官職等</label>
                                        </div>
                                        <select class="form-control" name="rank">
                                            <option value="">請選擇</option>
                                            @foreach(config('database_fields.t27tb')['rank'] as $key => $rank)
                                            <option value="{{ $key }}" {{ $key == $queryData['rank'] ? 'selected' : null }} >{{ $rank }}</option>
                                            @endforeach
                                        </select>
                                        <!-- <input class="form-control" type="text" name="rank" value="{{ $queryData['rank'] }}"> -->
                                    </div>  
                                    <div class="input-group col-3">
                                        <div class="input-group-prepend">
                                        <label class="input-group-text">E-mail</label>
                                        </div>
                                        <input class="form-control" type="text" name="email" value="{{ $queryData['email'] }}">
                                    </div>                                                                                                              
                                </div> 
                                <div class="float-md mobile-100 row mr-1 mb-3p">
                                    <div class="input-group col-12">
                                        <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>                               
                                        <a href="/admin/review_apply/{{ $t04tb->class }}/{{ $t04tb->term }}">
                                            <button type="button" class="btn btn-primary">重設條件</button>
                                        </a>
                                    </div>
                                </div>
                                
                            </div>                                
                        </form>                            
                        </div>  
                                              
                        <div style="padding:10px;padding-left:0px;">
                            <div style="padding:10px;padding-left:0px;">
                                {!! Form::open(['method' => 'post', 'url' => "/admin/review_apply/import/{$t04tb->class}/{$t04tb->term}", "enctype" => "multipart/form-data", "onsubmit" => 'return check_import()']) !!}
                                    <!-- <input type="file" name="import_file" style="width:300px;" accept=".xls, .xlsx" > -->
                                    
                                {!! Form::close() !!}
                                <button type="submit" onclick="importApplyData()" class="btn btn-primary">讀取檔案</button>
                            </div>

                            <a href='/admin/review_apply/apply_history?class={{ $t04tb->class }}&term={{ $t04tb->term }}'>
                                <button class="btn btn-primary">報名紀錄</button>
                            </a> 
                            <a href='/admin/review_apply/assign?class={{ $t04tb->class }}&term={{ $t04tb->term }}'>
                                <button class="btn btn-primary">分配/報名人數</button>
                            </a>  
                            <a href='/admin/review_apply/check_apply?class={{ $t04tb->class }}&term={{ $t04tb->term }}'>
                                <button class="btn btn-primary">參訓檢核</button>
                            </a>  
                            <a href='/admin/review_apply/check_repeat_apply?class={{ $t04tb->class }}&term={{ $t04tb->term }}'>
                                <button class="btn btn-primary">重複參訓檢核</button>
                            </a>         
                            <div style="text-align:right">      
                            全部審核 <input type="checkbox" onclick="setAll(this.checked)">                                                                                                 
                            </div>
                        </div>  
                        {!! Form::open(['method' => 'put', 'url' => "/admin/review_apply/review/{$t04tb->class}/{$t04tb->term}"]) !!}           
                        <div class="table-responsive">
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">審核</th>
                                        <!-- <th class="text-center" width="100">審核狀態</th> -->
                                        <th>人員身份</th>
                                        <!-- <th>身分證字號</th> -->
                                        <th>姓名</th>
                                        <th>性別</th>
                                        <th>服務機關</th>
                                        <th>職稱</th>
                                        <th>官職等</th>
                                        <th>E-mail</th>
                                        <th class="text-center">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($t27tbs))
                                        @foreach($t27tbs as $t27tb)
                                        <tr>
                                            <td class="text-center">

                                                <select name="proves[{{$t27tb->idno}}]" class="custom-select prove" {{ ($t27tb->prove == 'S') ? 'disabled' : null }}>
                                                    @if ($t27tb->prove == 'S')
                                                        <option selected>{{ config('database_fields.t27tb.prove')['S'] }}</option>
                                                    @else
                                                        @foreach(config('database_fields.t27tb.prove') as $prove => $proveText)
                                                            @if ($prove != 'S')
                                                            <option value="{{ $prove }}" {{ ($prove == $t27tb->prove) ? 'selected' : null }} >{{ $proveText }}</option>
                                                            @endif 
                                                        @endforeach
                                                    @endif 
                                                </select>
                                                <!-- @if(!empty($t27tb->prove) && $t27tb->prove == 'S')
                                                    <input type="checkbox" style="width:15px;height:15px;" checked disabled>                                                    
                                                @elseif (!empty($t27tb->prove))
                                                    <input type="checkbox" style="width:15px;height:15px;" name="idnos[]" onclick="review(this)" value="{{ $t27tb->des_idno }}" {{ ($t27tb->prove == 'Y') ? 'checked' : '' }}>
                                                @endif                                                                                   -->
                                            </td>
                                            <!-- <td id="{{ $t27tb->des_idno }}">
                                                @if(!empty($t27tb->prove))
                                                {{ config('database_fields.t27tb')['prove'][$t27tb->prove] }}
                                                @endif
                                            </td> -->
                                            <td>
                                            @if (!empty(config('database_fields.t27tb.identity')[$t27tb->identity]))
                                            {{ config('database_fields.t27tb.identity')[$t27tb->identity] }}
                                            @endif 
                                            </td>
                                            <td>{{ $t27tb->cname }}</td>
                                            <td>{{ config('database_fields.t27tb')['sex'][$t27tb->sex] }}</td>
                                            <td>{{ $t27tb->dept }}</td>
                                            <td>{{ $t27tb->position }}</td>
                                            <td>
                                            @if (!empty($t27tb->rank))
                                            {{ config('database_fields.t27tb')['rank'][$t27tb->rank] }}
                                            @endif 
                                            </td>
                                            <td>{{ $t27tb->email }}</td>
                                            <td  class="text-center">
                                                <a href='/admin/review_apply/edit/{{ $t27tb->class }}/{{ $t27tb->term }}/{{ $t27tb->des_idno }}'>
                                                    <button type="button" class="btn btn-primary">詳細資料</button>
                                                </a>                                            
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div> 
                        <div style="margin-top:10px;">
                            <button  class="btn btn-primary"><i class="fa fa-save"></i>保存</button>    
                        </div>
                        {!! Form::close() !!}                
                    </div>
                    <div class="card-footer">

                        <a href="/admin/review_apply/class_list">
                        <button type="button" class="btn btn-sm btn-danger" onclick="location.href=document.referrer"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>                     
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 名冊匯入 -->
<div id="import_apply_data" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong id="popTitle">報名資料匯入</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['method' => 'post' , 'url' => "admin/review_apply/importApplyData/{$t04tb->class}/{$t04tb->term}", "enctype" => "multipart/form-data", "onsubmit" => "return checkImport()"]) !!}
                <div class="search-float">
                    <div class="input-group col-12" style="margin-bottom:10px;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">人員身份</label>
                        </div>
                        <select name="identity" class="form-control custom-select">
                            <option value="1">公務人員</option>
                            <option value="2">一般民眾</option>
                        </select>                                                
                    </div>
                    <!--
                    <div id="version" class="input-group col-12" style="margin-bottom:10px;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">匯入版本</label>
                        </div>
                        <select name="version" class="form-control custom-select">
                            <option value="easy">簡易版</option>
                            <option value="full">完整版</option>
                        </select>                                                
                    </div>                    
                    -->
                    <div class="input-group col-12" style="margin-bottom:10px;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">匯入檔案</label>
                        </div>     
                        <input type="file" name="import_file" class="form-control" style="width:300px;" accept=".xls, .xlsx" >                                       
                    </div>

                    <div class="row">
                        <div class="col-12 text-center">   
                            <button type="button" onclick="downloadExample()" class="btn btn-primary">下載匯入範本</button>
                            <button type="submit" class="btn btn-primary mobile-100 mb-3 mb-md-0">匯入</button>                                                                                                                                                                                                                  
                            <button type="button" data-dismiss="modal" class="btn btn-danger mobile-100 mb-3 mb-md-0">取消</button> 
                        </div>                 
                    </div>
                </div>    
                {!! Form::close() !!}                            
            </div>           
        </div>
    </div>
</div>
<!-- 名冊匯入 -->

@endsection




@section('js')
<script>
    // var prove_text = JSON.parse('{!! json_encode(config("database_fields.t27tb")["prove"]) !!}');

    var class_no = '{{ $t04tb->class }}';
    var term = '{{ $t04tb->term }}';

    // function review(checkbox){
    //     let prove = (checkbox.checked) ? 'Y' : 'N';
    //     $("input[name='idnos[]']").attr("disabled", true);

    //     $.ajax({
    //         method: "POST",
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },            
    //         url: "/admin/review_apply/review/" + class_no + '/' + term,
    //         data: {
    //             _method: 'PUT',
    //             idnos: [checkbox.value],
    //             prove: prove
    //         },
    //     }).done(function(response) {
    //         if(response.result == 1){
    //             console.log("review success");
    //             $("input[name='idnos[]']").attr("disabled", false);
    //             $("#" + checkbox.value).html(prove_text[prove]);
    //         }else{
    //             alert('審核失敗');
    //             console.log("review fail");
    //         }
    //     }); 

    // }
/*
    function askIsOver()
    {

        if ($("input[name=import_file]").val() !== ""){

            $.ajax({
                url: "/admin/review_apply/check_is_over",
                data: {
                    class : class_no,
                    term : term
                },
            }).done(function(response) {
                if(response.ask_over){
                    if(confirm('已有報名資料選擇覆蓋(Y)或者附加(N)？')){
                        $("input[name=over]").val("Y");
                        $("#import_form").submit();
                    }else{
                        if (confirm('匯入名單的資料是否覆蓋？')){
                            $("input[name=over]").val("N");
                            $("#import_form").submit();
                        }
                    }
                }else{
                    alert('審核失敗');
                    console.log("review fail");
                }
            });
        }else{
            alert('請選擇檔案');
        }
    }
*/
    function checkImport()
    {
        if ($("input[name=import_file]").val() == ""){
            alert("請選擇匯入檔案");
            return false;
        }
        return true;
    }

    function importApplyData()
    {
        $("#import_apply_data").modal('show');
    }

    function downloadExample()
    {
        if ($("select[name=identity]").val() == 1){
            location.href = "/import_example/review_apply/審核報名處理-學員報名表(公務人員版).zip";
        }else if ($("select[name=identity]").val() == 2){
            location.href = "/import_example/review_apply/審核報名處理-學員報名表(一般民眾).zip";
        }else{
            alert("請選擇身份");
        }
    }
    
    function setAll(status)
    {
        status = (status) ? 'Y' : 'N';
        $(".prove:enabled").val(status)
    }
</script>
@endsection