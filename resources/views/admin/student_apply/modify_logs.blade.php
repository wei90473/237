@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'student_apply';?>
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
                <h4 class="pull-left page-title">學員報名處理</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li>學員報名處理</li>
                    <li class="active">異動紀錄</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員報名處理</h3>
                    </div>

                    <div class="card-body">
                        <!-- <div style="border: 1px solid #000; padding: 10px;margin-bottom:10px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            期別：{{ $t04tb->term }}<br>
                            分班名稱：<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            委訓機關：{{ $t04tb->client }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：
                        </div> -->
                        <div>    
                            <div class="search-float">
                                <div class="float-md mobile-100 row mr-1 mb-3">      
                                    <div class="input-group col-4">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text">班級名稱</label>
                                        </div>
                                        <select id="class" class="form-control" onchange="getTerms(this.value)">
                                            @if(isset($t04tb))
                                                <option value="{{ $t04tb->class }}">{{ $t04tb->class.' '.$t04tb->t01tb->name }}</option>
                                            @endif
                                        </select>
                                    </div>     
                                    <div class="input-group col-2" >
                                        <div class="input-group-prepend">
                                            <label class="input-group-text">期別</label>
                                        </div>
                                        <select class="form-control" name="term" style="flex: 0 0 0;min-width:100px;">
                                            @if(isset($t04tb->t01tb->t04tbs))
                                                @foreach($t04tb->t01tb->t04tbs->pluck('term') as $term)
                                                <option value="{{ $term }}" {{ ($term == $t04tb->term) ? 'selected' : '' }}>{{ $term }}</option>
                                                @endforeach
                                            @endif                                        
                                        </select>
                                        <div style="margin-left:10px;">
                                            <button type="button" onclick="search()" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>                               
                                        </div>
                                    </div>                                                                                                                                                                                                                                                                                           
                                </div>
                            </div>                                                          
                        </div>  

                        <div>
                            換人紀錄         
                            <div class="table-responsive">
                                <table id="data_table" class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>舊學員身分證號</th>
                                            <th>舊學員姓名</th>
                                            <th>舊學員服務機關</th>
                                            <th>新學員身分證號</th>
                                            <th>舊學員姓名</th>
                                            <th>新學員服務機關</th>
                                            <th>操作人員</th>
                                            <th>操作日期</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($t04tb->change_studnet_modify_logs as $modify_log)
                                        <tr>
                                            <td>{{ $modify_log->idno }}</td>
                                            <td>
                                                @if(!empty($modify_log->m02tb))
                                                    {{ $modify_log->m02tb->cname }}
                                                @endif
                                            </td>
                                            <td>{{ $modify_log->student_dept }}</td>
                                            <td>{{ $modify_log->new_idno }}</td>
                                            <td>
                                                @if(!empty($modify_log->new_m02tb))
                                                    {{ $modify_log->new_m02tb->cname }}
                                                @endif
                                            </td>
                                            <td>{{ $modify_log->new_student_dept }}</td>
                                            <td>
                                            @if(!empty($modify_log->modify_user))
                                                {{ $modify_log->modify_user->username }}
                                            @endif
                                            </td>
                                            <td>{{ $modify_log->created_at }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> 
                        </div> 

                        <div style="margin-top:10px;">
                            換期紀錄    
                            <div class="table-responsive">
                                <table id="data_table" class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>學員身分證號</th>
                                            <th>學員姓名</th>
                                            <th>學員服務機關</th>
                                            <th>新期別</th>
                                            <th>操作人員</th>
                                            <th>操作日期</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($t04tb->change_term_modify_logs as $modify_log)
                                        <tr>
                                            <td>{{ $modify_log->idno }}</td>
                                            <td>
                                                @if(!empty($modify_log->m02tb))
                                                    {{ $modify_log->m02tb->cname }}
                                                @endif
                                            </td>
                                            <td>{{ $modify_log->student_dept }}</td>
                                            <td>{{ $modify_log->new_term }}</td>
                                            <td>
                                            @if(!empty($modify_log->modify_user))
                                                {{ $modify_log->modify_user->username }}
                                            @endif
                                            </td>
                                            <td>{{ $modify_log->created_at }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> 
                        </div>

                    </div>
                    <div class="card-footer">
                        <a href="/admin/student_apply/{{ $t04tb->class }}/{{ $t04tb->term }}">
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
    function search()
    {
        if ($("#class").val() == ""){
            alert("請選擇班期");
            return false;
        }

        if ($("select[name=term]").val() == ""){
            alert("請選擇期別");
            return false;
        }

        location.href = "/admin/student_apply/modifylogForAdmin/" + $("#class").val() + "/" + $("select[name=term]").val();
    }

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

    $(function (){
        var page = 1;
        // 初始化階層樹

        $("#class").select2({
            language: 'zh-TW',
            // delay: 250,
            width: '100%',
            // 最多字元限制
            // maximumInputLength: 10,
            // 最少字元才觸發尋找, 0 不指定
            // minimumInputLength: 1,
            // 當找不到可以使用輸入的文字
            // tags: true,
            placeholder: '請輸入名稱...',
            // AJAX 相關操作        
            ajax: {
                delay: 250,
                url: '/admin/field/getData/t01tbs',
                type: 'get',
                dataType: 'json',
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
                    console.log(data);
                    // 一定要返回 results 物件
                    return {
                        results: data,
                        // 可以啟用無線捲軸做分頁
                        pagination: {
                            more: !(data.length < 30)
                        }
                    }
                }
            }
        });

    })

</script>
@endsection