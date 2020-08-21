@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'punch';?>
<style>
.form-group{
    min-width: 250px;
}
</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">學員刷卡處理</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">學員刷卡處理</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員刷卡處理</h3>
                    </div>

                    <div class="card-body">
                        <div style="border: 1px solid #000; padding: 10px;margin-bottom:10px;">
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
                                <div class="form-row">
                                    <div class="form-group col-sm-4"> 
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">上課日期：</label>
                                            </div>
                                            <input type="text" class="form-control" name="dated" id="dated"  value="{{ $queryData['dated'] }}"> 
                                            <span class="input-group-addon" style="cursor: pointer;" id="dated_datepicker"><i class="fa fa-calendar"></i></span>    
                                        </div>                              
                                    </div>
                                    <div class="form-group col-sm-2"> 
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">學號：</label>
                                            </div>
                                            <input type="text" class="form-control" name="no" value="{{ $queryData['no'] }}">    
                                        </div>                              
                                    </div>          
                                    <div class="form-group col-sm-2"> 
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">姓名：</label>
                                            </div>
                                            <input type="text" class="form-control" name="cname" value="{{ $queryData['cname'] }}">    
                                        </div>                              
                                    </div>                                                                  
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12"> 
                                        <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>                               
                                        <a href="/admin/punch/{{ $t04tb->class }}/{{ $t04tb->term }}">
                                            <button type="button" class="btn btn-primary">重設條件</button>
                                        </a>              
                                    </div>                       
                                </div>
                            </div>                                
                        </form>                            
                        </div>                          

                        <input type="hidden" name="prove" value="S">
                        <div class="table-responsive">
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>學號</th>
                                        <th>姓名</th>
                                        <th>刷卡日期</th>
                                        <th>上午</th>
                                        <th>下午</th>
                                        <th>報到方式</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($t84tbs as $user_t84tbs)
                                        @foreach ($user_t84tbs as $user_dated_t84tbs)
                                        <tr>
                                            <td>{{ $user_dated_t84tbs->t13tb_no }}</td>
                                            <td>{{ $user_dated_t84tbs->m02tb_cname }}</td>
                                            <td>{{ $user_dated_t84tbs->dated }}</td>
                                            <td>{{ $user_dated_t84tbs->status['A']['timed'] }}</td>
                                            <td>{{ $user_dated_t84tbs->status['B']['timed'] }}</td>
                                            <td></td>
                                        </tr>
                                        @endforeach
                                    @endforeach --}}

                                    @foreach ($t84tbs as $t84tb)
                                        <tr>
                                            <td>{{ $t84tb->t13tb_no }}</td>
                                            <td>{{ $t84tb->m02tb_cname }}</td>
                                            <td>{{ $t84tb->dated }}</td>
                                            <td>
                                            @if (isset($t84tb->atimed))
                                            <font color="{{ ($t84tb->atimed > '093000') ? 'red' : null }}">{{ substr($t84tb->atimed, 0, 2).':'.substr($t84tb->atimed, 2, 2) }}</font>
                                            @endif 
                                            </td>
                                            <td>
                                            @if (isset($t84tb->btimed))
                                            <font color="{{ ($t84tb->btimed > '140000') ? 'red' : null }}">{{ substr($t84tb->btimed, 0, 2).':'.substr($t84tb->btimed, 2, 2) }}</font>
                                            @endif 
                                            </td>
                                            <td></td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>               
                    </div>
                    <div class="card-footer">
                        <a href="/admin/punch/class_list">
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
    $("#dated").datepicker({
        format: "twymmdd",
        language: 'zh-TW'
    });

    $('#dated_datepicker').click(function(){
        $("#dated").focus();
    });
</script>
@endsection