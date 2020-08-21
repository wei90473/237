@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'review_apply';?>
<style>
.search-float input{
    min-width:1px;
}
.btndiv{
    padding:5px;
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
                    <li>報名審核處理</li>
                    <li class="active">報名紀錄</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>重複參訓檢核</h3>
                    </div>

                    <div class="card-body">
                        <div class="col-12">
                            <form>     
                                <div class="search-float">
                                    <div class="float-md mobile-100 row mr-1 mb-3">      
                                        <div class="input-group col-5">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">班別</label>
                                            </div>
                                            <select id="class" name="class" onchange="getTerms(this.value)" >
                                                @if (isset($t04tb))
                                                    <option value="{{$t04tb->class}}">{{$t04tb->t01tb->name}}</option>
                                                @endif
                                            </select>
                                        </div>  
                                        <div class="input-group col-2">
                                            <div class="input-group-prepend">
                                            <label class="input-group-text">期別</label>
                                            </div>
                                            <select name="term" class="select2">
                                                @if (isset($t04tb->t01tb->t04tbs))
                                                    @foreach($t04tb->t01tb->t04tbs as $s_t04tb)
                                                    <option value="{{$s_t04tb->term}}" {{ ($t04tb->term == $s_t04tb->term) ? 'selected' : null }}>{{$s_t04tb->term}}</option>
                                                    @endforeach
                                                @endif 
                                            </select>
                                        </div> 
                                        <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>                                                                                                                                                                                                                    
                                    
                                    </div>
                                    <div class="float-md mobile-100 row mr-1 mb-3">      
                                        <div class="input-group col-5">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">檢核開課日期區間</label>
                                            </div>
                                            <input type="text" id="sdate" name="sdate_start" class="form-control" autocomplete="off" value="{{ old('sdate_start', $queryData['sdate_start']) }}">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">至</label>
                                            </div>                                            
                                            <input type="text" id="edate" name="sdate_end" class="form-control" autocomplete="off" value="{{ old('sdate_end', $queryData['sdate_end']) }}"">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>                                            
                                        </div>                                          
                                    </div>
                                    
                                </div>                                
                            </form>                            
                        </div>    
                        <div class="table-responsive">                  
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <!-- <th>身分證字號</th> -->
                                        <th>姓名</th>
                                        <th>班號</th>
                                        <th>班期</th>
                                        <th>班別名稱</th>
                                        <th>開課日期</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($repeat_data))
                                        @foreach($repeat_data as $data)
                                        <tr>
                                            <td>{{ $data->name }}</td>
                                            <td>{{ $data->class }}</td>
                                            <td>{{ $data->term }}</td>
                                            <td>{{ $data->class_name }}</td>
                                            <td>{{ $data->sdate}}</td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>                            
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <a href="{{(isset($t04tb)) ? "/admin/review_apply/{$t04tb->class}/{$t04tb->term}" : "/admin/review_apply/class_list" }}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>                     
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>

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
})

function getTerms(class_no){
    $.ajax({
        url: "/admin/schedule/getTerms/" + class_no
    }).done(function(response) {
        console.log(response);
        var select_term = $("select[name=term]");
        select_term.html("");
        for(var i = 0; i<response.terms.length; i++){
            select_term.append("<option value='"+ response.terms[i] +"'>" + response.terms[i] + "</option>");
        }
    });  
}



</script>
@endsection