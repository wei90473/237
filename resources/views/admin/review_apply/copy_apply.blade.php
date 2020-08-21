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
.paginationjs{line-height:1.6;font-family:Marmelad,"Lucida Grande",Arial,"Hiragino Sans GB",Georgia,sans-serif;font-size:14px;box-sizing:initial}.paginationjs:after{display:table;content:" ";clear:both}.paginationjs .paginationjs-pages{float:left}.paginationjs .paginationjs-pages ul{float:left;margin:0;padding:0}.paginationjs .paginationjs-go-button,.paginationjs .paginationjs-go-input,.paginationjs .paginationjs-nav{float:left;margin-left:10px;font-size:14px}.paginationjs .paginationjs-pages li{float:left;border:1px solid #aaa;border-right:none;list-style:none}.paginationjs .paginationjs-pages li>a{min-width:30px;height:28px;line-height:28px;display:block;background:#fff;font-size:14px;color:#333;text-decoration:none;text-align:center}.paginationjs .paginationjs-pages li>a:hover{background:#eee}.paginationjs .paginationjs-pages li.active{border:none}.paginationjs .paginationjs-pages li.active>a{height:30px;line-height:30px;background:#aaa;color:#fff}.paginationjs .paginationjs-pages li.disabled>a{opacity:.3}.paginationjs .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs .paginationjs-pages li:first-child,.paginationjs .paginationjs-pages li:first-child>a{border-radius:3px 0 0 3px}.paginationjs .paginationjs-pages li:last-child{border-right:1px solid #aaa;border-radius:0 3px 3px 0}.paginationjs .paginationjs-pages li:last-child>a{border-radius:0 3px 3px 0}.paginationjs .paginationjs-go-input>input[type=text]{width:30px;height:28px;background:#fff;border-radius:3px;border:1px solid #aaa;padding:0;font-size:14px;text-align:center;vertical-align:baseline;outline:0;box-shadow:none;box-sizing:initial}.paginationjs .paginationjs-go-button>input[type=button]{min-width:40px;height:30px;line-height:28px;background:#fff;border-radius:3px;border:1px solid #aaa;text-align:center;padding:0 8px;font-size:14px;vertical-align:baseline;outline:0;box-shadow:none;color:#333;cursor:pointer;vertical-align:middle\9}.paginationjs.paginationjs-theme-blue .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-blue .paginationjs-pages li{border-color:#289de9}.paginationjs .paginationjs-go-button>input[type=button]:hover{background-color:#f8f8f8}.paginationjs .paginationjs-nav{height:30px;line-height:30px}.paginationjs .paginationjs-go-button,.paginationjs .paginationjs-go-input{margin-left:5px\9}.paginationjs.paginationjs-small{font-size:12px}.paginationjs.paginationjs-small .paginationjs-pages li>a{min-width:26px;height:24px;line-height:24px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-pages li.active>a{height:26px;line-height:26px}.paginationjs.paginationjs-small .paginationjs-go-input{font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-input>input[type=text]{width:26px;height:24px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-button{font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-button>input[type=button]{min-width:30px;height:26px;line-height:24px;padding:0 6px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-nav{height:26px;line-height:26px;font-size:12px}.paginationjs.paginationjs-big{font-size:16px}.paginationjs.paginationjs-big .paginationjs-pages li>a{min-width:36px;height:34px;line-height:34px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-pages li.active>a{height:36px;line-height:36px}.paginationjs.paginationjs-big .paginationjs-go-input{font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-input>input[type=text]{width:36px;height:34px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-button{font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-button>input[type=button]{min-width:50px;height:36px;line-height:34px;padding:0 12px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-nav{height:36px;line-height:36px;font-size:16px}.paginationjs.paginationjs-theme-blue .paginationjs-pages li>a{color:#289de9}.paginationjs.paginationjs-theme-blue .paginationjs-pages li>a:hover{background:#e9f4fc}.paginationjs.paginationjs-theme-blue .paginationjs-pages li.active>a{background:#289de9;color:#fff}.paginationjs.paginationjs-theme-blue .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-blue .paginationjs-go-button>input[type=button]{background:#289de9;border-color:#289de9;color:#fff}.paginationjs.paginationjs-theme-green .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-green .paginationjs-pages li{border-color:#449d44}.paginationjs.paginationjs-theme-blue .paginationjs-go-button>input[type=button]:hover{background-color:#3ca5ea}.paginationjs.paginationjs-theme-green .paginationjs-pages li>a{color:#449d44}.paginationjs.paginationjs-theme-green .paginationjs-pages li>a:hover{background:#ebf4eb}.paginationjs.paginationjs-theme-green .paginationjs-pages li.active>a{background:#449d44;color:#fff}.paginationjs.paginationjs-theme-green .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-green .paginationjs-go-button>input[type=button]{background:#449d44;border-color:#449d44;color:#fff}.paginationjs.paginationjs-theme-yellow .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-yellow .paginationjs-pages li{border-color:#ec971f}.paginationjs.paginationjs-theme-green .paginationjs-go-button>input[type=button]:hover{background-color:#55a555}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li>a{color:#ec971f}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li>a:hover{background:#fdf5e9}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li.active>a{background:#ec971f;color:#fff}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-yellow .paginationjs-go-button>input[type=button]{background:#ec971f;border-color:#ec971f;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-red .paginationjs-pages li{border-color:#c9302c}.paginationjs.paginationjs-theme-yellow .paginationjs-go-button>input[type=button]:hover{background-color:#eea135}.paginationjs.paginationjs-theme-red .paginationjs-pages li>a{color:#c9302c}.paginationjs.paginationjs-theme-red .paginationjs-pages li>a:hover{background:#faeaea}.paginationjs.paginationjs-theme-red .paginationjs-pages li.active>a{background:#c9302c;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-red .paginationjs-go-button>input[type=button]{background:#c9302c;border-color:#c9302c;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-go-button>input[type=button]:hover{background-color:#ce4541}.paginationjs .paginationjs-pages li.paginationjs-next{border-right:1px solid #aaa\9}.paginationjs .paginationjs-go-input>input[type=text]{line-height:28px\9;vertical-align:middle\9}.paginationjs.paginationjs-big .paginationjs-pages li>a{line-height:36px\9}.paginationjs.paginationjs-big .paginationjs-go-input>input[type=text]{height:36px\9;line-height:36px\9}

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
                    <li class="active">報名人員複製</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>報名人員複製</h3>
                    </div>

                    <div class="card-body">
                        <div class="col-12">
                            
                            {!! Form::open(['method' => 'post', 'url' => '/admin/review_apply/copy_apply', 'onsubmit' => "return check_copy()"]) !!}

                                <div class="search-float">
                                    <!-- <div class="float-md mobile-100 row mr-1 mb-3">      

                                    </div> -->
                                    <div style="width:50%;border:1px solid #000;margin-bottom:10px;padding:10px;">      
                                        <div id="copyed">
                                            訓練班別：<br>
                                            期別：<br>
                                            分班名稱：<br>
                                            班別類型：<br>
                                            委訓機關：<br>
                                            起訖期間：<br>
                                            班務人員：
                                        </div>  
                                        <button type="button" class="btn btn-primary"  onclick="choose('copyed')">挑選班期</button>                                         
                                        <input type="hidden" name="copyed[class]">
                                        <input type="hidden" name="copyed[term]">
                                    </div>  
                                    <div><font color="red">上面班期複製到</font></div>
                                    <div style="width:50%;border:1px solid #000;padding:10px;">    
                                        <div id="copy_purpose">
                                            訓練班別：<br>
                                            期別：<br>
                                            分班名稱：<br>
                                            班別類型：<br>
                                            委訓機關：<br>
                                            起訖期間：<br>
                                            班務人員：
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="choose('copy_purpose')">挑選班期</button>                                       
                                        <input type="hidden" name="copy_purpose[class]">
                                        <input type="hidden" name="copy_purpose[term]">                                      
                                    </div> 
                                    <div class="float-md mobile-100 row mr-1 mb-3" style="padding:10px;">    
                                        <div class="input-group">
                                            <label class="col-form-label">複製模式</label>
                                            <div class="col-2">
                                                <select class="form-control" name="copy_mode">
                                                    <option value="insert">新增</option>
                                                    <option value="clear_and_copy">清空後複製</option>
                                                </select>
                                                <input type="hidden" name="over_data" value="">
                                            </div>
                                        </div>                                     
                                        <div class="input-group">
                                            <button type="submit" class="btn btn-primary">確定複製</button>
                                        </div>
                                    </div>
                                </div>                                               
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



<!--  入口網站班級類別設定 -->
<!-- 班別類別 modal -->
<div class="modal fade bd-example-modal-lg classType" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog_120 modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong id="popTitle">班期選擇</strong></h4>
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
                                    <label class="input-group-text">班號</label>
                                </div>
                                <input type="text" name="class" class="form-control">
                            </div>  
                            <div class="input-group col-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">班別名稱</label>
                                </div>
                                <input type="text" name="class_name" class="form-control">
                            </div> 
                            <button type="button" onclick="queryClass()" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                            <button type="button" onclick="resetCondition()" class="btn mobile-100 mb-3 mb-md-0">重設條件</button>                                                                                                                                                                                                                   
                        </div>
                        
                    </div>                                
                </form> 
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class='text-center'>功能</th>
                            <th class='text-center'>班號</th>
                            <th>班別名稱</th>
                            <th class='text-center'>期別</th>
                            <th>開課起訖日</th>
                        </tr>
                    </thead>
                    <tbody id="class_tbody">
                        <!-- <tr>
                            <td><button type="button">選擇</button></td>
                            <td>109001A</td>
                            <td>WebHR種籽教師認證班</td>
                            <td>1</td>
                            <td>109/10/01~109/10/07</td>
                        </tr> -->
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
var choose_id;

function choose(id)
{
    choose_id = id;
    $(".classType").modal('show');
}

function queryClass()
{
    $("#class_tbody").html("<tr><td class='text-center' colspan='5'>搜尋中......</td><tr>");

    var class_no = $("input[name=class]").val();
    var class_name = $("input[name=class_name]").val();

    $.ajax({
        url: "/admin/field/getData/t04tbs?getTotal=1",
        data: {'class': class_no, 'class_name':class_name}
    }).done(function(response) {
        if (response.total > 0){
            console.log(response);
            paginate(response.queryData, response.total);
        }else{
            $("#class_tbody").html("<tr><td class='text-center' colspan='5'>查無資料</td><tr>");            
        }
    });
}

function chooseClass(class_no, term){
    data = getClassInfo(class_no, term);
}

function getClassInfo(class_no, term){
    var class_info;
    $("#" + choose_id).html("Loading...");
    $.ajax({
        url: "/admin/field/getData/t04tb",
        data: {'class': class_no, 'term':term}
    }).done(function(response) {
        var class_html = "";
        let username = (typeof(response.data.t04tb.m09tb.username) == 'undefined') ? '' : response.data.t04tb.m09tb.username; 
        console.log(response);
        class_html += "訓練班別：" + response.data.t04tb.t01tb.name + 
                      "<br>期別：" + response.data.t04tb.term +
                      "<br>分班名稱：" + 
                      "<br>班別類型：" + response.data.t04tb.t01tb.s01tb.name +
                      "<br>委訓機關：" + response.data.t04tb.client +
                      "<br>起訖期間：" + response.data.t04tb.sdateformat + '~' + response.data.t04tb.edateformat + 
                      "<br>班務人員：" + username;
        console.log(choose_id);
        $("#" + choose_id).html(class_html);
        $("input[name='" + choose_id + "[class]']").val(response.data.t04tb.class);
        $("input[name='" + choose_id + "[term]']").val(response.data.t04tb.term);
    }); 

    return class_info; 
}

function resetCondition(){
    $("input[name=class]").val("");
    $("input[name=class_name]").val("");
}

function check_copy(){
    console.log('check_copy');
    let check_list = ['copy_purpose', 'copyed'];
    
    for(var i in check_list){
        if($("input[name='" + check_list[i] + "[class]']").val() == '' || $("input[name='" + check_list[i] + "[term]']").val() == ''){
            alert('請選擇班期');
            return false;
        }
    }

    if($("select[name=copy_mode]").val() == 'insert'){
        console.log('insert');
        check_repeat();
    }else{
        $("input[name=over_data]").val(2);
    }

    return true;
}

function check_repeat()
{
    var copy_purpose = [];
    copy_purpose.class = $("input[name='copy_purpose[class]']").val();
    copy_purpose.term = $("input[name='copy_purpose[term]']").val();

    var copyed = [];
    copyed.class = $("input[name='copyed[class]']").val();
    copyed.term = $("input[name='copyed[term]']").val();    

    $.ajax({
        url: "/admin/review_apply/check_copy_repeat?" + 
             "copy_purpose[class]=" + copy_purpose.class + 
             "&copy_purpose[term]=" + copy_purpose.term + 
             "&copyed[class]=" + copyed.class + 
             "&copyed[term]=" + copyed.term,
        data: {},
        async: false
    }).done(function(response) {
        console.log(response);
        
        if(response.is_repeat){
            if(confirm('複製的名單中有重複的人員，是否覆蓋？')){
                $("input[name=over_data]").val(1);
            }else{
                $("input[name=over_data]").val(2);
            }
        }else{
            $("input[name=over_data]").val(2);
        }

    }); 
}


/*
            'copy_purpose_class': $("input[name='copy_purpose[class]']").val(),
            'copy_purpose_term': $("input[name='copy_purpose[term]']").val(), 
            'copyed_class': $("input[name='copyed[class]']").val(),
            'copyed_term': $("input[name='copyed[term]']").val()

*/

function paginate(queryData, total) {

    var container = $('#pagination');
    console.log(container);
    container.pagination({
      dataSource: '/admin/field/getData/t04tbs?class=' + queryData.class + '&class_name=' + queryData.class_name,
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
          $("#class_tbody").html("<tr><td class='text-center' colspan='5'>搜尋中......</td><tr>");
        }
      },
      callback: function(data, pagination) {
        // window.console && console.log(22, data, pagination);
        var class_html = "";
        for (var i in data) {
            class_html += "<tr>" + 
                            "<td class='text-center'>" + "<button data-dismiss='modal' type='button' onclick='chooseClass(\"" + data[i].t01tb.class + '","' + data[i].term  + "\")' class='btn btn-primary'>選擇</button>" + "</td>" + 
                            "<td class='text-center'>" + data[i].t01tb.class + "</td>" + 
                            "<td>" + data[i].t01tb.name + "</td>" + 
                            "<td class='text-center'>" + data[i].term + "</td>" + 
                            "<td>" + data[i].sdate + '~' + data[i].edate + "</td>" + 
                          "</tr>";
        }
        $("#class_tbody").html(class_html);
      }
    })
}
</script>
@endsection