@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'signup';?>
<style>
.paginationjs{line-height:1.6;font-family:Marmelad,"Lucida Grande",Arial,"Hiragino Sans GB",Georgia,sans-serif;font-size:14px;box-sizing:initial}.paginationjs:after{display:table;content:" ";clear:both}.paginationjs .paginationjs-pages{float:left}.paginationjs .paginationjs-pages ul{float:left;margin:0;padding:0}.paginationjs .paginationjs-go-button,.paginationjs .paginationjs-go-input,.paginationjs .paginationjs-nav{float:left;margin-left:10px;font-size:14px}.paginationjs .paginationjs-pages li{float:left;border:1px solid #aaa;border-right:none;list-style:none}.paginationjs .paginationjs-pages li>a{min-width:30px;height:28px;line-height:28px;display:block;background:#fff;font-size:14px;color:#333;text-decoration:none;text-align:center}.paginationjs .paginationjs-pages li>a:hover{background:#eee}.paginationjs .paginationjs-pages li.active{border:none}.paginationjs .paginationjs-pages li.active>a{height:30px;line-height:30px;background:#aaa;color:#fff}.paginationjs .paginationjs-pages li.disabled>a{opacity:.3}.paginationjs .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs .paginationjs-pages li:first-child,.paginationjs .paginationjs-pages li:first-child>a{border-radius:3px 0 0 3px}.paginationjs .paginationjs-pages li:last-child{border-right:1px solid #aaa;border-radius:0 3px 3px 0}.paginationjs .paginationjs-pages li:last-child>a{border-radius:0 3px 3px 0}.paginationjs .paginationjs-go-input>input[type=text]{width:30px;height:28px;background:#fff;border-radius:3px;border:1px solid #aaa;padding:0;font-size:14px;text-align:center;vertical-align:baseline;outline:0;box-shadow:none;box-sizing:initial}.paginationjs .paginationjs-go-button>input[type=button]{min-width:40px;height:30px;line-height:28px;background:#fff;border-radius:3px;border:1px solid #aaa;text-align:center;padding:0 8px;font-size:14px;vertical-align:baseline;outline:0;box-shadow:none;color:#333;cursor:pointer;vertical-align:middle\9}.paginationjs.paginationjs-theme-blue .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-blue .paginationjs-pages li{border-color:#289de9}.paginationjs .paginationjs-go-button>input[type=button]:hover{background-color:#f8f8f8}.paginationjs .paginationjs-nav{height:30px;line-height:30px}.paginationjs .paginationjs-go-button,.paginationjs .paginationjs-go-input{margin-left:5px\9}.paginationjs.paginationjs-small{font-size:12px}.paginationjs.paginationjs-small .paginationjs-pages li>a{min-width:26px;height:24px;line-height:24px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-pages li.active>a{height:26px;line-height:26px}.paginationjs.paginationjs-small .paginationjs-go-input{font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-input>input[type=text]{width:26px;height:24px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-button{font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-button>input[type=button]{min-width:30px;height:26px;line-height:24px;padding:0 6px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-nav{height:26px;line-height:26px;font-size:12px}.paginationjs.paginationjs-big{font-size:16px}.paginationjs.paginationjs-big .paginationjs-pages li>a{min-width:36px;height:34px;line-height:34px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-pages li.active>a{height:36px;line-height:36px}.paginationjs.paginationjs-big .paginationjs-go-input{font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-input>input[type=text]{width:36px;height:34px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-button{font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-button>input[type=button]{min-width:50px;height:36px;line-height:34px;padding:0 12px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-nav{height:36px;line-height:36px;font-size:16px}.paginationjs.paginationjs-theme-blue .paginationjs-pages li>a{color:#289de9}.paginationjs.paginationjs-theme-blue .paginationjs-pages li>a:hover{background:#e9f4fc}.paginationjs.paginationjs-theme-blue .paginationjs-pages li.active>a{background:#289de9;color:#fff}.paginationjs.paginationjs-theme-blue .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-blue .paginationjs-go-button>input[type=button]{background:#289de9;border-color:#289de9;color:#fff}.paginationjs.paginationjs-theme-green .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-green .paginationjs-pages li{border-color:#449d44}.paginationjs.paginationjs-theme-blue .paginationjs-go-button>input[type=button]:hover{background-color:#3ca5ea}.paginationjs.paginationjs-theme-green .paginationjs-pages li>a{color:#449d44}.paginationjs.paginationjs-theme-green .paginationjs-pages li>a:hover{background:#ebf4eb}.paginationjs.paginationjs-theme-green .paginationjs-pages li.active>a{background:#449d44;color:#fff}.paginationjs.paginationjs-theme-green .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-green .paginationjs-go-button>input[type=button]{background:#449d44;border-color:#449d44;color:#fff}.paginationjs.paginationjs-theme-yellow .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-yellow .paginationjs-pages li{border-color:#ec971f}.paginationjs.paginationjs-theme-green .paginationjs-go-button>input[type=button]:hover{background-color:#55a555}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li>a{color:#ec971f}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li>a:hover{background:#fdf5e9}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li.active>a{background:#ec971f;color:#fff}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-yellow .paginationjs-go-button>input[type=button]{background:#ec971f;border-color:#ec971f;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-red .paginationjs-pages li{border-color:#c9302c}.paginationjs.paginationjs-theme-yellow .paginationjs-go-button>input[type=button]:hover{background-color:#eea135}.paginationjs.paginationjs-theme-red .paginationjs-pages li>a{color:#c9302c}.paginationjs.paginationjs-theme-red .paginationjs-pages li>a:hover{background:#faeaea}.paginationjs.paginationjs-theme-red .paginationjs-pages li.active>a{background:#c9302c;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-red .paginationjs-go-button>input[type=button]{background:#c9302c;border-color:#c9302c;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-go-button>input[type=button]:hover{background-color:#ce4541}.paginationjs .paginationjs-pages li.paginationjs-next{border-right:1px solid #aaa\9}.paginationjs .paginationjs-go-input>input[type=text]{line-height:28px\9;vertical-align:middle\9}.paginationjs.paginationjs-big .paginationjs-pages li>a{line-height:36px\9}.paginationjs.paginationjs-big .paginationjs-go-input>input[type=text]{height:36px\9;line-height:36px\9}

</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">機關維護</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li>線上報名設定列表</li>
                    <li class="active">機關維護</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="container">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>機關維護</h3>
                    </div>

                    <div class="card-body">              
                        <div class="row">
                            <div class="col-12">
                                <div class="col-12">
                                    @if(isset($online_apply_organ))
                                    {!! Form::model($online_apply_organ, array('url' => "/admin/signup_organ/{$online_apply_organ->id}",'method'=>'put')) !!}                                 
                                    @else
                                    {!! Form::open(array('url' => "/admin/signup_organ/{$t04tb->class}/{$t04tb->term}",'method'=>'post')) !!}
                                    @endif
                                    <div class="form-group row">
                                        <label class="col-form-label">機關</label>
                                        <div class="input-group col-md-5">
                                            <!--  Form::select('organ', $m17tbs, null, ['class'=> 'browser-default select2'])  --> 
                                            {{ Form::text('enrollorg', null, ['class'=>'form-control', 'readOnly' => 'readOnly']) }}
                                            @if (isset($online_apply_organ) && !empty($online_apply_organ->m17tb))
                                            {{ Form::text('enrollname', $online_apply_organ->m17tb->enrollname, ['class'=>'form-control', 'readOnly' => 'readOnly']) }}
                                            @else
                                            {{ Form::text('enrollname', null, ['class'=>'form-control', 'readOnly' => 'readOnly']) }}
                                            @endif 
                                        </div>                  
                                        <button type="button" class="btn btn-primary" style="height:calc(2.25rem + 2px);" onclick="showOrgan()">挑選機關</button>                            
                                    </div>  
                                    <div class="form-group row">
                                        <label class="col-form-label">正取名額</label>
                                        <div class="col-md-2">
                                            {{ Form::text('officially_enroll', null, ['class'=>'form-control']) }}
                                        </div>
                                        <label class="col-form-label">候補名額</label>
                                        <div class="col-md-2">
                                            {{ Form::text('secondary_enroll', null, ['class'=>'form-control']) }}
                                        </div>                                                
                                    </div> 

                                    <!-- <div class="form-group row"> -->
                                        <!-- <label class="col-form-label">是否開放所屬報名</label> -->
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                {{ Form::checkbox('open_belong_apply', 1 , null, ['class'=>'form-check-input']) }}
                                                <!-- open_belong_apply -->
                                                是否開放所屬報名
                                            </label>
                                        </div>
                                    <!-- </div> -->
                                </div>
                                  
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-info">
                            <i class="fa fa-save pr-2"></i>儲存
                        </button>
                        <a href="/admin/signup/edit/{{$t04tb->class}}/{{$t04tb->term}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>     
                    {!! Form::close() !!}                 
                </div>
            </div>
        </div>
    </div>
</div>

<!--  挑選機關選單 -->

<div class="modal fade bd-example-modal-lg choose_organ" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog_120 modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong id="popTitle">機關選擇</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height: 60vh;overflow: auto;">
                <form>     
                    <div class="search-float">
                        <div class="float-md mobile-100 row mr-1 mb-3">      
                            <div class="input-group col-4">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">機關代碼</label>
                                </div>
                                <input type="text" name="query_enrollorg" class="form-control">
                            </div>  
                            <div class="input-group col-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">機關名稱</label>
                                </div>
                                <input type="text" name="query_enrollname" class="form-control">
                            </div> 
                            <button type="button" onclick="queryOrgan()" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                            <button type="button" onclick="resetCondition()" class="btn mobile-100 mb-3 mb-md-0">重設條件</button>                                                                                                                                                                                                                   
                        </div>
                        
                    </div>                                
                </form> 
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class='text-center'>功能</th>
                            <th class='text-center'>機關代碼</th>
                            <th class='text-center'>機關名稱</th>
                        </tr>
                    </thead>
                    <tbody id="organ_tbody">

                    </tbody>
                </table>
            </div>
            <div id="wrapper">
                <section>
                    <div id="pagination"></div>
                </section>
            </div>            
        </div>
    </div>
</div>




@endsection


@section('js')
<script src="/backend/plugins/pagination/pagination.js" charset="UTF-8"></script>
<script>
function showOrgan()
{
    $(".choose_organ").modal('show');
}

function queryOrgan()
{
    $("#organ_tbody").html("<tr><td class='text-center' colspan='5'>搜尋中......</td><tr>");

    var enrollorg = $("input[name=query_enrollorg]").val();
    var enrollname = $("input[name=query_enrollname]").val();

    $.ajax({
        url: "/admin/field/getData/m17tbs",
        data: {'enrollorg': enrollorg, 'enrollname':enrollname}
    }).done(function(response) {
        console.log(response);
        if (response.total > 0){
            paginate(response.queryData, response.total);
        }else{
            $("#organ_tbody").html("<tr><td class='text-center' colspan='5'>查無資料</td><tr>");      
            $('#pagination').html("");      
        }
    });
}

function paginate(queryData, total) {
    var container = $('#pagination');

    container.pagination({
        dataSource: '/admin/field/getData/m17tbs?enrollorg=' + queryData.enrollorg + '&enrollname=' + queryData.enrollname,
        locator: 'data',
        totalNumber: total,
        pageSize: 10,
        showpages: true,
        showPrevious: true,
        showNext: true,
        showNavigator: true,
        showFirstOnEllipsisShow: true,
        showLastOnEllipsisShow: true,
        ajax: {
            beforeSend: function() {
            $("#organ_tbody").html("<tr><td class='text-center' colspan='5'>搜尋中......</td><tr>");
            }
        },
        callback: function(data, pagination) {
            // window.console && console.log(22, data, pagination);
            var organ_html = "";
            for (var i in data) {
                organ_html += "<tr>" + 
                                "<td class='text-center'>" + "<button data-dismiss='modal' type='button' onclick='chooseOrgan(\"" + data[i].enrollorg + '","' + data[i].enrollname  + "\")' class='btn btn-primary'>選擇</button>" + "</td>" + 
                                "<td class='text-center'>" + data[i].enrollorg + "</td>" + 
                                "<td>" + data[i].enrollname + "</td>" + 
                            "</tr>";
            }
            $("#organ_tbody").html(organ_html);
        }
    })
}

function chooseOrgan(enrollorg, enrollname)
{
    $("input[name=enrollorg]").val(enrollorg);
    $("input[name=enrollname]").val(enrollname);
}

</script>
@endsection

