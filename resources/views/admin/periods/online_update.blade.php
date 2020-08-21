@extends('admin.layouts.layouts')
@section('content')

<style>
.btn {
    margin-right: 5px;
    border-color: #dee2e6;
}
</style>


    <?php $_menu = 'periods';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">批次更新線上分配人數</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li>開班期數處理列表</li>
                        <li class="active">批次更新線上分配人數</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>批次更新線上分配人數</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="GET" id="search_form">
                                            
                                            <!-- 班別名稱 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">年度</span>
                                                            <select type="text" id="yerly" name="yerly" class="browser-default custom-select" style="min-width: 60px; flex:0 1 auto">
                                                                @for($year = (int)date("Y") - 1910; $year >= 90; $year--)
                                                                    <option value="{{ $year }}" 
                                                                        @if($queryData['yerly'] == $year) 
                                                                            selected 
                                                                        @elseif ((int)date("Y")-1911 == $year && empty($queryData['yerly']))
                                                                            selected
                                                                        @endif>
                                                                    {{ $year }}</option>
                                                                @endfor
                                                            </select>                                                   
                                                        </div>

                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">第幾次調查</span>
                                                            <input type="text" name="times" class="form-control" style="width: 60px;min-width: 60px; flex:0 1 auto" value="{{ $queryData['times'] }}">
                                                            <!-- <select type="text" id="yerly" name="yerly" class="browser-default custom-select" style="min-width: 60px; flex:0 1 auto">
                                                                <option value="" selected >1</option>
                                                            </select> -->
                                                        </div>
                                               
                                                    </div>     
                                                </div>

                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                                        <input type="button" class="btn btn-primary" value="重設條件" style="min-width:auto;" onclick="window.location='/admin/periods/action/online_update'">           
                                                        <a target="_blank" href="/admin/signup"><button type="button" class="btn btn-primary">線上報名設定</button></a>
                                                    </div>
                                                </div>
                                            </div>

                                        </form>
                                        <div>
                                            <input type="checkbox" style="min-width: unset;width: 20px; height: 20px;" onclick="checkall(this)">全選
                                        </div>
                                    </div>

                                    <div class="float-md-right">

                                    </div>
                                    <form id="t04tb" action="/admin/periods/action/exec_online_update" method="POST">
                                        <div class="table-responsive">
                                            {{ csrf_field() }} 
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                <tr>
                                                    <th>選取</th>
                                                    <th>班號</th>
                                                    <th>班別名稱</th>
                                                    @foreach($terms as $term)
                                                        <th>第{{ $term }}期</th>
                                                    @endforeach
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($datas as $data)     
                                                        <tr>
                                                            <td><input type="checkbox" style="width: 20px; height: 20px;" onclick="checkRow(this, '{{$data->class}}')"
                                                            ></td>
                                                            <td>{{ $data->class }}</td>
                                                            <td>{{ $data->name }}</td>
                                                            @foreach($terms as $term)
                                                            <td>
                                                                @if (isset($is_online_update[$data->class][$term]))
                                                                <input type="checkbox" class="t04tb" style="width: 20px; height: 20px;" name="online_update[{{ $data->class }}][]" value="{{ $term }}"
                                                                    {{ ($is_online_update[$data->class][$term]) ? 'checked disabled' : '' }}
                                                                >   
                                                                @endif                                                          
                                                            </td>                
                                                            @endforeach                                      
                                                        </tr>
                                                    @endforeach 
                                                </tbody>
                                            </table>
                                            <div style="margin-top:10px;">
                                                <input type="hidden" name="exec_update" value="">
                                                <input type="submit" class="btn btn-primary" value="執行線上分配" onclick="return checkAssignOtherOrgan(this)">
                                                <input type="submit" name="exec_remove" class="btn btn-primary" value="放棄重分配">
                                                <!-- <button class="btn mobile-100 mb-3 mb-md-0 btn-primary" onclick="assign()">執行分配</button> -->
                                            </div>
                                        </div>
                                    </form>
                                    <div id="submit_message">
                                    
                                    </div>
                                    <!-- 分頁 -->
                                    @if (!$datas->isEmpty())
                                        @include('admin/layouts/list/pagination', ['paginator' => $datas, 'queryData' => $queryData])   
                                    @endif 

                                </div>
                            </div>
                        </div>

                        <!-- 列表頁尾 -->


                    </div>
                </div>
            </div>
        </div>
    </div>

<script>

    function checkRow(this_check, class_no)
    {
        var checkbox = $("input[name='update[" + class_no + "][]'");
        for(i = 0; i < checkbox.length; i++){
            checkbox[i].checked = this_check.checked;
        }
    }    

    function checkall(e)
    {
        for(i=0; i<$("input[type=checkbox]").length; i++ ){
             $("input[type=checkbox]")[i].checked = e.checked;
        }
    }

    function checkAssignOtherOrgan(btn)
    {     
        var array = $('#t04tb').serializeArray();
        query_string = jQuery.map(array , function(n, i){
            if (n.name == '_token') return null;
            return n.name + '=' + n.value;
        }).join("&");
        $("#submit_message").html("<font color='blue'>檢查是否有機關已往下分配人數中...</font>"); 
        $("input[name=exec_update]").val("exec_update");
        checkAssign(query_string);
        return false;         
    }

    function checkAssign(query_string){
        var status = false;
        $.ajax({
            method: "get",
            url: "/admin/periods/checkAssignOtherOrgan?" + query_string,
            // async: false,
            cache: false,             
        }).done(function( msg ) {
            $("#submit_message").html("");   
            console.log( msg);
            if (msg.message == "") $("#t04tb").submit(); 
            status = confirm(msg.message + "已有機關往下分配人數，是否重次更新線上分配人數？");
            if (status){
                $("#t04tb").submit(); 
            }
        });     
    }
</script>

@endsection