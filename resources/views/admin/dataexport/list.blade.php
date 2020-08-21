@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'dataexport';?>
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
                                            <!--<span style="margin-right:1%">
                                                <a href="/admin/dataexport/select_class" >
                                                    <button type="button" class="btn btn-sm btn-primary"onclick="window.open('/admin/dataexport/select_class', 'mywin',
                                                    'left=20,top=400,width=1000,height=500,toolbar=1,resizable=0'); return false;" >新增班期</button>
                                                </a>
                                            </span>-->

                                            <button type="button" onclick="wo();" class="btn btn-sm btn-primary mr-2">新增班期</button>
                                            <button type="button" onclick="return send_email();" class="btn btn-sm btn-primary">訊息傳遞</button>
                                            
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
                                                <input type='hidden' id="link" value="/admin/dataexport/set_column/teacher">
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
                                        <input type="hidden" id="output_info" name="output_info" value="m01tb.idno_身分證號,cname_中文姓名,ename_英文姓名,sex_性別,birth_出生日期,dept_服務機關,position_現職,offaddress_機關地址,offzip_地郵遞區號(公),homaddress_住家地址,homzip_郵遞區號(宅),regaddress_戶籍地址,regzip_郵遞區號(戶),offtel1_電話(公一),offtel2_電話(公二),homtel_電話(宅),mobiltel_行動電話,offfax_傳真(公),homfax_傳真(宅),email_E-mail,excel">

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

<script src="/backend/plugins/pagination/pagination.js" charset="UTF-8"></script>

<script type="text/javascript">

    var selectDefault = {};

    selectDefault.student = 'no_學號,m02tb.idno_身分證號,cname_中文姓名,ename_英文姓名,sex_性別,birth_出生日期,m13tb.lname_主管機關,m02tb.dept_服務機關,t13tb.rank_官職,m02tb.position_職稱,m02tb.education_學歷,offaddr_機關地址,offzip_郵遞區號(公),homaddr_住家地址,homzip_郵遞區號(宅),offtel1_電話(公一),offtel2_電話(公二),offfax_傳真(公),m02tb.email_E-mail,homtel_電話(宅),mobiltel_行動電話,handicap_身心障礙,m02tb.offemail_人事單位Email,m13tb.email as m13tbEmail_機關電子信箱,t13tb.age_年齡,excel';

    selectDefault.teacher = 'm01tb.idno_身分證號,cname_中文姓名,ename_英文姓名,sex_性別,birth_出生日期,dept_服務機關,position_現職,offaddress_機關地址,offzip_地郵遞區號(公),homaddress_住家地址,homzip_郵遞區號(宅),regaddress_戶籍地址,regzip_郵遞區號(戶),offtel1_電話(公一),offtel2_電話(公二),homtel_電話(宅),mobiltel_行動電話,offfax_傳真(公),homfax_傳真(宅),email_E-mail,excel';

    $(document).ready(function() {
        var datatype=$('#datatype').val();
        $("#datatype2").val(datatype);

        $("#student_condition").hide();
        $('input[type=radio][name=datatype]').change(function() {
            clear_condition();
            if (this.value == 'teacher') {
                $("#link").val("/admin/dataexport/set_column/teacher");
                $("#output_info").val(selectDefault.teacher);
                $("#student_condition").hide();
                $("#datatype2").val(this.value);
            }
            else if (this.value == 'student') {
                $("#link").val("/admin/dataexport/set_column/student");
                $("#output_info").val(selectDefault.student);
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
   
    function wo()
    {
        //var iHeight=500;
        //var iWidth=1200;
        var iHeight=(window.screen.availHeight)*0.4;
        var iWidth=(window.screen.availWidth)*0.6;
        var iTop = (window.screen.availHeight - 30 - iHeight) / 2; 
        var iLeft = (window.screen.availWidth - 10 - iWidth) / 2; 
        window.open('/admin/dataexport/select_class', 'test', 'height=' + iHeight + ',innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no'); 
    }

    function send_email()
    {
        
        var type_send=$("#datatype").val();
        var class_send=$("#class_info").val();
        var temp2='';
        class_send_arr=class_send.split(",");
        if(class_send==''){
            alert("請先選擇班期");
        }else{
            for(var i=0;i<class_send_arr.length;i++){
                if(class_send_arr[i].length){
                    temp=class_send_arr[i].split("_");
                    temp2+=temp[0]+'_'+temp[2]+',';
                }
            }
            var cond=type_send+','+temp2;
            window.open("/admin/dataexport/send/"+cond);
        }
        
        
    }

    function set_column_2(type)
    {
        var iHeight=(window.screen.availHeight)*0.6;
        var iWidth=(window.screen.availWidth)*0.3;
        var iTop = (window.screen.availHeight - 30 - iHeight) / 2; 
        var iLeft = (window.screen.availWidth - 10 - iWidth) / 2; 
        if(type==1){
            var col=$("#output_info").val();
            var select_link=$("#link").val();
            //window.open("/admin/dataexport/set_column/teacher/"+col,"set_column", 'height=' + iHeight + ',innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');
            window.open(select_link+'/'+col,"set_column", 'height=' + iHeight + ',innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');

        }

        if(type=="master"){
            var mas=$("#master_info").val();
            window.open("/admin/dataexport/set_column/master_select/"+mas,"set_column", 'height=' + iHeight + ',innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');
        }

        if(type=="gov_select"){
            var gov=$("#gov_info").val();
            window.open("/admin/dataexport/set_column/gov_select/"+gov,"set_column", 'height=' + iHeight + ',innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');
        }

        if(type=="dep_select"){
            var dep=$("#dep_info").val();
            window.open("/admin/dataexport/set_column/dep_select/"+dep,"set_column", 'height=' + iHeight + ',innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');
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
        n=class_info.search(class_info_stirng);
        
        if(n==-1){
            class_info += class_info_stirng;//紀錄新增班期選取的班期
            class_info +=',';
        }
        //console.log(class_info_stirng);
        console.log(class_info);
        
        $("#class_info").val(class_info);
        $("#class_info2").val(class_info);
        //var class_info_array=class_info_stirng.split(",");
        var class_info_array=class_info.split(",");

        for(var i=0;i<class_info_array.length;i++){
            if(class_info_array[i]!=''){
                var class_detail=class_info_array[i].split("_");
                
                tableData+='<tr class="text-center">';
                tableData+='<td>'+j+'</td>';
                tableData+='<td>'+class_detail[0]+'</td>';
                tableData+='<td>'+class_detail[1]+'</td>';
                tableData+='<td>'+class_detail[2]+'</td>';
                tableData+='<td><button type="button" class="btn btn-light"  onclick="deleteRow(this)">刪除</button></td>'
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


