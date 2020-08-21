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
                    <li class="active">分配/報名人數</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>分配/報名人數</h3>
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
                                                    <option value="{{$s_t04tb->term}}" {{ ($t04tb->term == $s_t04tb->term) ? 'selected' : '' }} >{{$s_t04tb->term}}</option>
                                                    @endforeach
                                                @endif 
                                            </select>
                                        </div> 
                                        <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>                                                                                                                                                                                                                    
                                    </div>
                                    
                                </div>                                
                            </form>                            
                        </div>    
                        <div class="col-12 row">                  
                            <div class="col-5">
                                @if(!empty($assign_infos))
                                <ul>
                                    <ul id="treeview" class="filetree">
                                        @foreach($assign_infos[""] as $key => $assign_info1)
                                        <li>
                                            <span class="folder classType_item" onclick="choose(this)"
                                            data-enrollorg="{{ $assign_info1->organ }}"
                                            data-enrollname="{{ $assign_info1->m17tb_enrollname }}"
                                            data-share="{{ $assign_info1->share }}"
                                            >{{ "{$assign_info1->m17tb->enrollname} [{$assign_info1->quota}] ({$assign_info1->share})" }}</span>
                                            @if(isset($assign_infos[$assign_info1->m17tb_enrollorg]))
                                                @foreach ($assign_infos[$assign_info1->m17tb_enrollorg] as $assign_info2)
                                                <ul> 
                                                    <li>
                                                        <span class="folder classType_item" onclick="choose(this)"
                                                        data-enrollorg="{{ $assign_info2->organ }}"
                                                        data-enrollname="{{ $assign_info2->m17tb_enrollname }}"
                                                        data-share="{{ $assign_info2->share }}"                                                        
                                                        >{{ $assign_info2->m17tb_enrollname."(".$assign_info2->share.")" }}</span>
                                                        @if(isset($assign_infos[$assign_info2->m17tb_enrollorg]))
                                                            @foreach ($assign_infos[$assign_info2->m17tb_enrollorg] as $assign_info3)                                                        
                                                            <ul> 
                                                                <li>
                                                                    <span class="folder classType_item" onclick="choose(this)"
                                                                    data-enrollorg="{{ $assign_info3->organ }}"
                                                                    data-enrollname="{{ $assign_info3->m17tb_enrollname }}"
                                                                    data-share="{{ $assign_info3->share }}"                                                                        
                                                                    >{{ $assign_info3->m17tb_enrollname."(".$assign_info3->share.")" }}</span>
                                                                    @if(isset($assign_infos[$assign_info3->m17tb_enrollorg]))
                                                                         @foreach ($assign_infos[$assign_info3->m17tb_enrollorg] as $assign_info4)                                                                     
                                                                        <ul> 
                                                                            <li>
                                                                                <span class="folder classType_item" onclick="choose(this)"
                                                                                data-enrollorg="{{ $assign_info4->organ }}"
                                                                                data-enrollname="{{ $assign_info4->m17tb_enrollname }}"
                                                                                data-share="{{ $assign_info4->share }}"                                                                                    
                                                                                >{{ $assign_info4->m17tb_enrollname."(".$assign_info4->share.")" }}</span>
                                                                            </li>
                                                                        </ul> 
                                                                        @endforeach 
                                                                    @endif                                                                                                                                     
                                                                </li>
                                                            </ul>   
                                                            @endforeach 
                                                        @endif                                                                                                                  
                                                    </li>
                                                </ul>   
                                                @endforeach 
                                            @endif                           
                                        </li>
                                        @endforeach
                                    </ul>
                                </ul>
                                @endif 
                            </div>   
                            <div class="col-6">
                                {!! Form::open([ 'method'=>'put', 'url'=>"", 'id'=>'form']) !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="col-form-label">機關代碼</label>
                                            <div class="col-5">
                                                <input class="form-control" name="organ" readOnly="true">
                                            </div>
                                        </div>
                                        <div class="input-group">
                                            <label class="col-form-label">機關名稱</label>
                                            <div class="col-5">
                                                <input class="form-control" name="enrollname" readOnly="true">
                                            </div>
                                        </div>
                                        <div class="input-group">
                                            <label class="col-form-label">可報名人數</label>
                                            <div class="col-5">
                                                <input class="form-control" name="share" readOnly="true" autocomplete="off">
                                            </div>
                                        </div>   
                                        <div class="input-group">
                                            <div class="btndiv">
                                                <button type="button" id="edit" class="btn btn-primary" onclick="actionEdit()">修改</button>
                                            </div>
                                            <div class="btndiv">
                                                <button type="submit" id="save" class="btn btn-info" disabled>儲存</button>
                                            </div>
                                            <div class="btndiv">
                                                <button type="button" id="cancel" class="btn btn-danger" onclick="actionCancel()" disabled>取消</button>
                                            </div>
                                        </div>                                                                                                                       
                                    </div>
                                {!! Form::close() !!}
                            </div>                                
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
var select = null;


$(function (){
    var page = 1;
    // 初始化階層樹
    setTimeout(() => {
        $("#treeview").treeview({
            persist: "location",
            collapsed: true,
            unique: false,
            toggle: function() {
                // console.log("%s was toggled.", $(this).find(">span").text());
            }
        });

    }, 1000); 

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

function choose(span) {
    let t05tb = span.dataset;
    $('.classType_item').css('background-color', '');
    $(span).css('background-color', '#ffe4c4');
    $('input[name=organ]').val(t05tb.enrollorg);
    $('input[name=share]').val(t05tb.share);
    $('input[name=enrollname]').val(t05tb.enrollname);
}

function actionEdit(){
    let enrollorg = $("input[name=organ]").val();
    $("#form").attr('action', '/admin/review_apply/assign/{{ $t04tb->class }}/{{ $t04tb->term }}/' + enrollorg);
    $("#save").attr('disabled', false);
    $("#cancel").attr('disabled', false);
    $("#edit").attr('disabled', true);
    $('input[name=share]').attr('readOnly', false);    
}

function actionCancel(){
    $("#form").attr('action', '');
    $("#save").attr('disabled', true);
    $("#cancel").attr('disabled', true);
    $("#edit").attr('disabled', false);   
    $('input[name=share]').attr('readOnly', true);       
}


</script>
@endsection