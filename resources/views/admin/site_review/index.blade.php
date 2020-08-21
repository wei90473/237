@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'site_review';?>
<style>

</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">洽借場地班期選員處理</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">洽借場地班期選員處理</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>洽借場地班期選員處理</h3>
                    </div>

                    <div class="card-body">
                        <div style="border: 1px solid #000; padding: 10px;margin-bottom:10px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            辦班院區：{{ $t04tb->t01tb->branch }}<br>
                            期別：{{ $t04tb->term }}<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：
                        </div>
                        <div>
                        <form>     
                            <div class="search-float">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">身分證號：</span>
                                            </div>
                                            <input type="text" name="idno" class="form-control">
                                        </div>                                       
                                    </div>
                                    <div class="form-group col-md-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">姓名：</span>
                                            </div>
                                            <input type="text" name="cname" class="form-control">
                                        </div>  
                                    </div>                                   
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">機關代碼：</span>
                                            </div>
                                            <input type="text" name="enrollid" class="form-control">
                                        </div>                                       
                                    </div>                                 
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">職稱：</span>
                                            </div>
                                            <input type="text" name="position" class="form-control">
                                        </div>  
                                    </div>                                   
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-5">
                                        <div class="form-check-inline">
                                            <label class="form-check-label">選項：</label>
                                            <label class="form-check-label">
                                                <input type="radio" name="prove" class="form-check-input" style="min-width:0px" value="NA" {{ ($queryData['prove'] == "NA")? 'checked' : '' }} >所有(不含備取)
                                            </label>
                                        </div>

                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="prove" class="form-check-input" style="min-width:0px"  value="N" {{ ($queryData['prove'] == "N")? 'checked' : '' }}>未審核
                                            </label>
                                        </div>   

                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="prove" class="form-check-input" style="min-width:0px"  value="X" {{ ($queryData['prove'] == "X")? 'checked' : '' }}>不合格
                                            </label>
                                        </div>  

                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="prove" class="form-check-input" style="min-width:0px"  value="S" {{ ($queryData['prove'] == "S")? 'checked' : '' }}>已轉檔
                                            </label>
                                        </div>  

                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="prove" class="form-check-input" style="min-width:0px"  value="A" {{ ($queryData['prove'] == "A")? 'checked' : '' }}>備取
                                            </label>
                                        </div>  
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
                            <button type="button" class="btn btn-primary" onclick="create_student()">新增</button>       
                            
                            <a href="/admin/site_review/checkCondition/{{ $t04tb->class }}/{{ $t04tb->term }}">
                                <button class="btn btn-primary">檢核資訊</button>       
                            </a>
                            <button class="btn btn-primary" onclick="filterStudent()">篩選學員</button>   
                            <button class="btn btn-primary" type="button" onclick='importStudent()'>讀取檔案</button>
                            {{ Form::open(['method' => 'put', 'url' => "/admin/site_review/filterStudent/{$t04tb->class}/{$t04tb->term}", "id" => "filter_student"]) }}

                            {{ Form::close() }} 
                            <div style="text-align:right">      
                                全部不通過/全部通過 <input type="checkbox" onclick="setAll(this.checked)">                                                                                 
                            </div>                     
                        </div>  

                        <input type="hidden" name="prove" value="S">
                        {{ Form::open(['method' => 'put', 'id' => 'prove_form']) }}
                            <div class="table-responsive" style="height:800px;">
                                <table id="data_table" class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="130">審核狀態</th>
                                            <th class="text-center" width="100">姓名</th>
                                            <th class="text-center">服務機關</th>
                                            <th width="100">職稱</th>
                                            <th>學員分類</th>
                                            <th>費用</th>
                                            <th>資料來源</th>
                                            <th>功能</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($t04tb->t39tbs as $t39tb)
                                        <tr>
                                            <td>
                                                <select name="prove[{{ $t39tb->idno }}]" class="custom-select prove" {{ ($t39tb->prove == 'S') ? 'disabled' : null }}>
                                                    @foreach($t39tb_fields['prove'] as $key => $text)
                                                        @if ($key <> 'S' || $t39tb->prove == 'S')
                                                        <option value="{{ $key }}" {{ ($key == $t39tb->prove) ? 'selected' : '' }} >{{ $text }}</option>
                                                        @endif 
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>{{ $t39tb->cname }}</td>
                                            <td>{{ $t39tb->dept }}</td>
                                            <td>{{ $t39tb->position }}</td>
                                            <td>
                                                @if (!empty($t39tb_fields['race'][$t39tb->race]))
                                                {{ $t39tb_fields['race'][$t39tb->race] }}
                                                @endif 
                                            </td>
                                            <td>{{ $t39tb->fee }}</td>
                                            <td>
                                                @if (!empty($t39tb_fields['source'][$t39tb->source]))
                                                {{ $t39tb_fields['source'][$t39tb->source] }}
                                                @endif 
                                            </td>
                                            <td>
                                                <a href="/admin/site_review/edit/{{ $t39tb->class }}/{{ $t39tb->term }}/{{ $t39tb->des_idno }}">
                                                    <button type="button" class="btn btn-primary">詳細資料</button>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> 
                        {{ Form::close() }}

                        <!-- include('admin/layouts/list/pagination', ['paginator' => $t27tbs, 'queryData' => $queryData])                    -->
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" onclick="prove_submit()"><i class="fa fa-save"></i>保存</button>
                        <a href="/admin/site_review/class_list">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>                     
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 名冊匯入 -->
<div id="create_student" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong id="popTitle">新增學員</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['method' => 'post' , 'url' => "/admin/site_review/create/{$t04tb->class}/{$t04tb->term}/", "enctype" => "multipart/form-data", "onsubmit" => ""]) !!}
                <!-- <input type="hidden" name="cover_insert" value=""> -->
                <div class="search-float">
                    <div class="input-group col-12" style="margin-bottom:10px;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">身分證</label>
                        </div>
                        <input type="text" class="form-control" name="idno">                                              
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">   
                            <button class="btn btn-primary">送出</button>
                        </div>                 
                    </div>
                </div>    
                {!! Form::close() !!}                            
            </div>           
        </div>
    </div>
</div>
<!-- 名冊匯入 -->

<!-- 名冊匯入 -->
<div id="import_student" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong id="popTitle">名冊匯入</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['method' => 'post' , 'url' => "admin/site_view/importApplyData/{$t04tb->class}/{$t04tb->term}", "enctype" => "multipart/form-data", "onsubmit" => "return checkImport()"]) !!}
 
                <div class="search-float">                  
                    <div class="input-group col-12" style="margin-bottom:10px;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">匯入檔案</label>
                        </div>     
                        <input type="file" name="import_file" class="form-control" style="width:300px;" accept=".xls, .xlsx" >                                       
                    </div>

                    <div class="row">
                        <div class="col-12 text-center">   
                            <button type="button" onclick="downloadExample()" class="btn btn-primary">下載匯入範本</button>
                            <button type="submit" name="publish" value="Y" class="btn btn-primary mobile-100 mb-3 mb-md-0">匯入</button>                        <button type="button" data-dismiss="modal" class="btn btn-danger mobile-100 mb-3 mb-md-0">取消</button> 
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

function prove_submit()
{
    $("#prove_form").submit();
}

function importStudent()
{
    $("#import_student").modal('show');
}

function checkImport()
{
    if($("input[name=import_file]").val() == "")
    {
        alert("請選擇匯入檔案後，在進行匯入");
        return false;
    }

    return true;
}

function filterStudent()
{
    if (confirm('確定要篩選學員？')){
        $('#filter_student').submit();
    } 
}

function setAll(status)
{
    status = (status) ? 'Y' : 'N';
    $(".prove:enabled").val(status)
}

function create_student()
{
    $("#create_student").modal('show');
}

function downloadExample()
{
    location.href="/import_example/site_review/洽借場地班期選員處理-學員報名表.zip?" + Math.random();
}

</script>
@endsection