<script src="/backend/assets/js/jquery.min.js"></script>

<style>
.paginationjs{line-height:1.6;font-family:Marmelad,"Lucida Grande",Arial,"Hiragino Sans GB",Georgia,sans-serif;font-size:14px;box-sizing:initial}.paginationjs:after{display:table;content:" ";clear:both}.paginationjs .paginationjs-pages{float:left}.paginationjs .paginationjs-pages ul{float:left;margin:0;padding:0}.paginationjs .paginationjs-go-button,.paginationjs .paginationjs-go-input,.paginationjs .paginationjs-nav{float:left;margin-left:10px;font-size:14px}.paginationjs .paginationjs-pages li{float:left;border:1px solid #aaa;border-right:none;list-style:none}.paginationjs .paginationjs-pages li>a{min-width:30px;height:28px;line-height:28px;display:block;background:#fff;font-size:14px;color:#333;text-decoration:none;text-align:center}.paginationjs .paginationjs-pages li>a:hover{background:#eee}.paginationjs .paginationjs-pages li.active{border:none}.paginationjs .paginationjs-pages li.active>a{height:30px;line-height:30px;background:#aaa;color:#fff}.paginationjs .paginationjs-pages li.disabled>a{opacity:.3}.paginationjs .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs .paginationjs-pages li:first-child,.paginationjs .paginationjs-pages li:first-child>a{border-radius:3px 0 0 3px}.paginationjs .paginationjs-pages li:last-child{border-right:1px solid #aaa;border-radius:0 3px 3px 0}.paginationjs .paginationjs-pages li:last-child>a{border-radius:0 3px 3px 0}.paginationjs .paginationjs-go-input>input[type=text]{width:30px;height:28px;background:#fff;border-radius:3px;border:1px solid #aaa;padding:0;font-size:14px;text-align:center;vertical-align:baseline;outline:0;box-shadow:none;box-sizing:initial}.paginationjs .paginationjs-go-button>input[type=button]{min-width:40px;height:30px;line-height:28px;background:#fff;border-radius:3px;border:1px solid #aaa;text-align:center;padding:0 8px;font-size:14px;vertical-align:baseline;outline:0;box-shadow:none;color:#333;cursor:pointer;vertical-align:middle\9}.paginationjs.paginationjs-theme-blue .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-blue .paginationjs-pages li{border-color:#289de9}.paginationjs .paginationjs-go-button>input[type=button]:hover{background-color:#f8f8f8}.paginationjs .paginationjs-nav{height:30px;line-height:30px}.paginationjs .paginationjs-go-button,.paginationjs .paginationjs-go-input{margin-left:5px\9}.paginationjs.paginationjs-small{font-size:12px}.paginationjs.paginationjs-small .paginationjs-pages li>a{min-width:26px;height:24px;line-height:24px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-pages li.active>a{height:26px;line-height:26px}.paginationjs.paginationjs-small .paginationjs-go-input{font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-input>input[type=text]{width:26px;height:24px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-button{font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-button>input[type=button]{min-width:30px;height:26px;line-height:24px;padding:0 6px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-nav{height:26px;line-height:26px;font-size:12px}.paginationjs.paginationjs-big{font-size:16px}.paginationjs.paginationjs-big .paginationjs-pages li>a{min-width:36px;height:34px;line-height:34px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-pages li.active>a{height:36px;line-height:36px}.paginationjs.paginationjs-big .paginationjs-go-input{font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-input>input[type=text]{width:36px;height:34px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-button{font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-button>input[type=button]{min-width:50px;height:36px;line-height:34px;padding:0 12px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-nav{height:36px;line-height:36px;font-size:16px}.paginationjs.paginationjs-theme-blue .paginationjs-pages li>a{color:#289de9}.paginationjs.paginationjs-theme-blue .paginationjs-pages li>a:hover{background:#e9f4fc}.paginationjs.paginationjs-theme-blue .paginationjs-pages li.active>a{background:#289de9;color:#fff}.paginationjs.paginationjs-theme-blue .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-blue .paginationjs-go-button>input[type=button]{background:#289de9;border-color:#289de9;color:#fff}.paginationjs.paginationjs-theme-green .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-green .paginationjs-pages li{border-color:#449d44}.paginationjs.paginationjs-theme-blue .paginationjs-go-button>input[type=button]:hover{background-color:#3ca5ea}.paginationjs.paginationjs-theme-green .paginationjs-pages li>a{color:#449d44}.paginationjs.paginationjs-theme-green .paginationjs-pages li>a:hover{background:#ebf4eb}.paginationjs.paginationjs-theme-green .paginationjs-pages li.active>a{background:#449d44;color:#fff}.paginationjs.paginationjs-theme-green .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-green .paginationjs-go-button>input[type=button]{background:#449d44;border-color:#449d44;color:#fff}.paginationjs.paginationjs-theme-yellow .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-yellow .paginationjs-pages li{border-color:#ec971f}.paginationjs.paginationjs-theme-green .paginationjs-go-button>input[type=button]:hover{background-color:#55a555}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li>a{color:#ec971f}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li>a:hover{background:#fdf5e9}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li.active>a{background:#ec971f;color:#fff}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-yellow .paginationjs-go-button>input[type=button]{background:#ec971f;border-color:#ec971f;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-red .paginationjs-pages li{border-color:#c9302c}.paginationjs.paginationjs-theme-yellow .paginationjs-go-button>input[type=button]:hover{background-color:#eea135}.paginationjs.paginationjs-theme-red .paginationjs-pages li>a{color:#c9302c}.paginationjs.paginationjs-theme-red .paginationjs-pages li>a:hover{background:#faeaea}.paginationjs.paginationjs-theme-red .paginationjs-pages li.active>a{background:#c9302c;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-red .paginationjs-go-button>input[type=button]{background:#c9302c;border-color:#c9302c;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-go-button>input[type=button]:hover{background-color:#ce4541}.paginationjs .paginationjs-pages li.paginationjs-next{border-right:1px solid #aaa\9}.paginationjs .paginationjs-go-input>input[type=text]{line-height:28px\9;vertical-align:middle\9}.paginationjs.paginationjs-big .paginationjs-pages li>a{line-height:36px\9}.paginationjs.paginationjs-big .paginationjs-go-input>input[type=text]{height:36px\9;line-height:36px\9}

</style>

<div id="qebed" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog_120 modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong id="popTitle">樓別選擇</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height: 60vh;overflow: auto;">
                <form>     
                    <div class="search-float">
                        <div class="float-md mobile-100 row mr-1 mb-3">      
                            <div class="input-group col-4" style="padding-bottom:0px;">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">樓別</label>
                                </div>
                                <select class="browser-default custom-select" name="floorList" id="floorList">
                                   
                                   
                                </select>
                            </div>   
                            <button type="button" onclick="queryFloors()" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                            <!-- <button type="button" onclick="resetM17tbCondition()" class="btn mobile-100 mb-3 mb-md-0">重設條件</button>                                                                                                                                                                                                                    -->
                        </div>
                        
                    </div>                                
                </form> 
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class='text-center'>功能</th>
                            <th class='text-center'>樓別名稱</th>
                            <th class='text-center'>寢室代碼</th>
                            <th class='text-center'>寢室名稱</th>
                            <th class='text-center'>床位數</th>
                        </tr>
                    </thead>
                    <tbody id="class_tbody">
                        
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

<script>

$(document).ready(function() {
    var staystartdate = document.getElementById('staystartdate').value;
    var stayenddate = document.getElementById('stayenddate').value;
    var staystarttime = document.getElementById('staystarttime').value;
    var stayendtime = document.getElementById('stayendtime').value;
    $.ajax({
        url: "/admin/field/getData/getFloors",
        data: {'staystartdate': staystartdate, 'stayenddate': stayenddate, 'staystarttime': staystarttime, 'stayendtime': stayendtime}
    }).done(function(response) {
        var floor_list = ''
        for (var i=0;i<response.length;i++) {
            floor_list += '<option value=\"'+response[i].floorno+'\">' + response[i].floorname + '(' + response[i].cnt +')' + '</option>';
        }
        $("#floorList").html(floor_list);
        queryFloors();
    });  
});

function queryFloors()
{
    $("#class_tbody").html("<tr><td class='text-center' colspan='5'>搜尋中......</td><tr>");

    var floorno = document.getElementById('floorList').value;
    var staystartdate = document.getElementById('staystartdate').value;
    var stayenddate = document.getElementById('stayenddate').value;
    var staystarttime = document.getElementById('staystarttime').value;
    var stayendtime = document.getElementById('stayendtime').value;
    var sex = document.getElementById('sex').value;

    $.ajax({
        url: "/admin/field/getData/getEmptyBed",
        data: {'floorno': floorno, 'sex': sex, 'staystartdate': staystartdate, 'stayenddate': stayenddate, 'staystarttime': staystarttime, 'stayendtime': stayendtime}
    }).done(function(response) {
        if (response.total > 0){
            console.log(response);
            paginateFloors(response.queryData, response.total);
        }else{
            $("#class_tbody").html("<tr><td class='text-center' colspan='5'>查無資料</td><tr>");            
        }
    });    
}


function paginateFloors(queryData, total) {
    var container = $('#pagination');
    container.pagination({
        dataSource: '/admin/field/getData/getEmptyBed?floorno=' + queryData.floorno + '&staystartdate=' + queryData.staystartdate + '&stayenddate=' + queryData.stayenddate + '&staystarttime=' + queryData.staystarttime + '&stayendtime=' + queryData.stayendtime + '&sex=' + queryData.sex,
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
            window.console && console.log(22, data, pagination);
            var class_html = "";
            for (var i in data) {
                class_html += "<tr>" + 
                                "<td class='text-center'>" + "<button data-dismiss='modal' type='button' onclick='chooseFloor(\"" + data[i].floorno + "\"\,\"" + data[i].floorname + "\"\,\"" + data[i].bedroom + "\")' class='btn btn-primary'>選擇</button>" + "</td>" + 
                                "<td class='text-center'>" + data[i].floorname + "</td>" + 
                                "<td class='text-center'>" + data[i].bedroom + "</td>" + 
                                "<td class='text-center'>" + data[i].roomname + "</td>" + 
                                "<td class='text-center'>" + data[i].cnt + "</td>" + 
                            "</tr>";
            }
            $("#class_tbody").html(class_html);
        }
    })
}



</script>