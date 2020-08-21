@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'dataexport';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">資料匯出處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">資料匯出作業</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>資料匯出處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="float-left search-float" style="min-width: 1000px;">
                                    </div>
                                    <form method="post" action="/admin/dataexport" id="form">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="col-md-12 row">
                                            <span>
                                                <label class="label">資料類型:</label>
                                                <input type="radio" name='datatype' id="datatype" value="teacher" checked>講座資料
                                                <input type="radio" name='datatype' id="datatype" value="student">學員資料
                                            </span>
                                        </div>

                                        <div class="col-md-12 row" style="margin-bottom:1%">
                                            <span style="margin-right:1%">
                                                <a href="/admin/dataexport/select_class" >
                                                    <button type="button" class="btn btn-sm btn-primary"onclick="window.open('/admin/dataexport/select_class', 'mywin',
                                                    'left=20,top=20,width=1000,height=1000,toolbar=1,resizable=0'); return false;" >新增班期</button>
                                                </a>
                                            </span>

                                            
                                            <button type="button" onclick="send_email();" class="btn btn-sm btn-primary">訊息傳遞</button>
                                            
                                        </div>
                                        
                                        <!--班期資料-->
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0" id="myTable">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="5%"></th>
                                                        <th class="text-center">班號</th>
                                                        <th class="text-center">班別</th>
                                                        <th class="text-center">期別</th>
                                                        <th class="text-center"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody1">
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div class="col-md-12" style="margin-top:1%">
                                            <span style="margin-top:1%">
                                                <label class="label">匯出選項</label>
                                                <input type="radio" name="exporttype" id="exporttype" value="basic_info" checked>基本資料
                                                
                                                <span style="margin-right:1%">
                                                    <button type="button" id="set" onclick="set_column_2(1);" class="btn btn-sm btn-primary">設定欄位</button>
                                                </span>
                                                
                                                <input type="radio" name="exporttype" id="exporttype" value="address">地址條
                                                <input type="radio" name="exporttype" id="exporttype" value="fax">傳真通知
                                            </span>
                                        </div>

                                        <div class="col-md-12" id="student_condition">
                                            <label>學員其他條件:</label>
                                            <div class="col-md-12" style="margin-bottom:1%">
                                                <input type="checkbox" id="master" name="master">主管機關
                                                
                                                <button type="button" class="btn btn-sm btn-primary" id="master_select" onclick="set_column_2('master')" disabled>挑選</button>
                                            </div>
                                            <div class="col-md-12" style="margin-bottom:1%">
                                                <input type="checkbox" id="gov" name="gov">官職等
                                                
                                                <button type="button" class="btn btn-sm btn-primary" id="gov_select" onclick="set_column_2('gov_select')" disabled>挑選</button>
                                            </div>
                                            <div class="col-md-12" style="margin-bottom:1%">
                                                <input type="checkbox" id="dep" name="dep">機關縣市
                                                
                                                <button type="button" class="btn btn-sm btn-primary" id="dep_select" onclick="set_column_2('dep_select')" disabled>挑選</button>

                                            </div>
                                            <div class="col-md-12">
                                                <input type="checkbox" id="sexual">性別
                                                <input type="radio" id="sex1" name="sex" value="M" disabled>男
                                                <input type="radio" id="sex2" name="sex" value="F" disabled>女
                                            </div>
                                        </div>

                                        <div class="col-md-12 row" style="margin-top:1%">
                                            <button type="button" id="butt" class="btn btn-primary mobile-100 mb-3 mb-md-0" onclick="return check();">資料匯出</button>
                                        </div>

                                        

                                        <input type="hidden" id="class_info" name="class_info">
                                        <input type="hidden" id="output_info" name="output_info">
                                        <input type="hidden" id="master_info" name="master_info">
                                        <input type="hidden" id="gov_info" name="gov_info">
                                        <input type="hidden" id="dep_info" name="dep_info">
                                    </form>
                                    <form id="test" action="" method="post" target="_blank">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" id="datatype2" name="datatype">
                                        <input type="hidden" id="exporttype2" name="exporttype">
                                        <input type="hidden" id="class_info2" name="class_info">
                                        <input type="hidden" id="output_info2" name="output_info">
                                        <input type="hidden" id="master_info2" name="master_info">
                                        <input type="hidden" id="gov_info2" name="gov_info">
                                        <input type="hidden" id="dep_info2" name="dep_info">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var datatype=$('#datatype').val();
        $("#datatype2").val(datatype);


        $("#student_condition").hide();
        $('input[type=radio][name=datatype]').change(function() {
            clear_condition();
            if (this.value == 'teacher') {
                $("#set_column").attr("href", "/admin/dataexport/set_column/teacher");
                $("#student_condition").hide();
                $("#datatype2").val(this.value);
            }
            else if (this.value == 'student') {
                $("#set_column").attr("href", "/admin/dataexport/set_column/student");
                $("#student_condition").show();
                $("#datatype2").val(this.value);
                
            }

        });

        $('input[type=radio][name=exporttype]').change(function() {
            if (this.value == 'fax') {
                $("#exporttype2").val(this.value);
                $("#test").attr("action", "/admin/dataexport/fax/2");

                $( "#butt" ).click(function() {
                    $("#test").submit();
                });
            }else{
                $("#test").attr("action", " ");
            }
        });
           

        $("#gov").change(function(){
            if(this.checked){
                $('#gov_select').attr('disabled',false);
            }else{
                $('#gov_select').attr('disabled',true);
            }
        });

        $("#dep").change(function(){
            if(this.checked){
                $('#dep_select').attr('disabled',false);
            }else{
                $('#dep_select').attr('disabled',true);
            }
        });

        $("#master").change(function(){
            if(this.checked){
                $('#master_select').attr('disabled',false);
            }else{
                $('#master_select').attr('disabled',true);
            }
        });

        $("#sexual").change(function(){
            if(this.checked){
                $('#sex1').attr('disabled',false);
                $('#sex2').attr('disabled',false);
            }else{
                $('#sex1').attr('disabled',true);
                $('#sex2').attr('disabled',true);
            }
        });
        

        $('input[type=radio][name=exporttype]').change(function() {
            if (this.value == 'basic_info') {
                $('#set').attr('disabled',false);
            }
            else {
                $('#set').attr('disabled','disabled');
            }
        });
    });
    function choose(id)
    {
        choose_id = id;
        $(".classType").modal('show');
    }

    function send_email()
    {
        var type_send=$("#datatype").val();
        var class_send=$("#class_info").val();
        class_send_arr=class_send.split(",");
        //console.log(class_send_arr);
        var temp2='';
        for(var i=0;i<class_send_arr.length;i++){
            if(class_send_arr[i].length){
                temp=class_send_arr[i].split("_");
                temp2+=temp[0]+'_'+temp[2]+',';
            }
        }
        //console.log(temp2);
        
        var cond=type_send+','+temp2;
        window.open("/admin/dataexport/send/"+cond,"set_column",'left=20,top=20,width=1000,height=1000,toolbar=1,resizable=0');
    }

    function set_column_2(type)
    {
        if(type==1){
            var col=$("#output_info").val();
            window.open("/admin/dataexport/set_column/teacher/"+col,"set_column",'left=20,top=20,width=1000,height=1000,toolbar=1,resizable=0');
        }

        if(type=="master"){
            var mas=$("#master_info").val();
            window.open("/admin/dataexport/set_column/master_select/"+mas,"set_column",'left=20,top=20,width=1000,height=1000,toolbar=1,resizable=0');
        }

        if(type=="gov_select"){
            var gov=$("#gov_info").val();
            window.open("/admin/dataexport/set_column/gov_select/"+gov,"set_column",'left=20,top=20,width=1000,height=1000,toolbar=1,resizable=0');
        }

        if(type=="dep_select"){
            var dep=$("#dep_info").val();
            window.open("/admin/dataexport/set_column/dep_select/"+dep,"set_column",'left=20,top=20,width=1000,height=1000,toolbar=1,resizable=0');
        }
        
    }

    function clear_condition()
    {
        $empty='';
        $("#output_info").val($empty);
        $("#master_info").val($empty);
        $("#gov_info").val($empty);
        $("#dep_info").val($empty);
        $("#datatype2").val($empty);
        $("#exporttype2").val($empty);
        $("#class_info2").val($empty);
        $("#output_info2").val($empty);
        $("#master_info2").val($empty);
        $("#gov_info2").val($empty);
        $("#dep_info2").val($empty);
    }
    

    
    var class_info='';
    
    function select_class(savefield)
    {
        var tableData='';
        var j=1;
        var class_info_stirng=$("#class_info").val();
        class_info += class_info_stirng;//紀錄新增班期選取的班期
        class_info +=',';
        
        
        $("#class_info").val(class_info);
        $("#class_info2").val(class_info);
        //var class_info_array=class_info_stirng.split(",");
        var class_info_array=class_info.split(",");

        for(var i=0;i<class_info_array.length;i++){
            if(class_info_array[i]!=''){
                var class_detail=class_info_array[i].split("_");
                //console.log(class_info_array[i])
                tableData+='<tr class="text-center">';
                tableData+='<td>'+j+'</td>';
                tableData+='<td>'+class_detail[0]+'</td>';
                tableData+='<td>'+class_detail[1]+'</td>';
                tableData+='<td>'+class_detail[2]+'</td>';
                tableData+='<td><input type="button" value="delete" onclick="deleteRow(this)"></td>'
                tableData+='</tr>';
                j++;
            }
        }
        $("#tbody1").html(tableData);
    }
    var classinfo='';
    function select_output(savefield_2)
    {
        var output = $("#"+savefield_2).val();
        //console.log(output);
        $("#"+savefield_2+'2').val(output);
    }

    function deleteRow(r)
    {
        var i = r.parentNode.parentNode.rowIndex;
        var condition1=document.getElementById("myTable").rows[i].cells[1].innerHTML;
        var condition2=document.getElementById("myTable").rows[i].cells[2].innerHTML;
        var condition3=document.getElementById("myTable").rows[i].cells[3].innerHTML;

        var final_condition=condition1+'_'+condition2+'_'+condition3;
        var input_text=$("#class_info").val();
        //console.log(input_text);
        //console.log(final_condition);
        var final_input_text=input_text.replace(final_condition,"");
        class_info=class_info.replace(final_condition,"");//清除新增班期的紀錄
        $("#class_info").val(final_input_text);
        document.getElementById("myTable").deleteRow(i);
        adjust_table();
    }
    function adjust_table()
    {
        var rowCount = $('#myTable tr').length-1;
        for(i=1;i<=rowCount;i++){
            document.getElementById("myTable").rows[i].cells[0].innerHTML=i;
        }
        //tableData=$("#tbody1").prop("outerHtml");
        //$("#tbody1").html(tableData);
        //j=1;
    }

    function check()
    {
        var class_info=$("#class_info").val();
        var output_info=$("#output_info").val();
        var exporttype=$("#exporttype").val();
        if(exporttype.value='basic_info'){
            if(class_info.length==0){
                alert("請選擇班期")
                return false;
            }
            if(output_info.length==0){
                alert("請選擇欄位")
                return false;
            }
            $("#form").attr("target","_blank");
            $("#form").submit();
            
        }
    }

</script>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

