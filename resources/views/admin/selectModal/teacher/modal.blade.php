
<div class="modal fade" id="selectTeacher" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">選擇講座</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="search-float">
                    <div class="form-row">
                        <div class="form-group col-5 align-self-center">
                            <div class="input-group">
                                <div class="pull-left input-group-prepend">
                                    <span class="input-group-text">身分證號</span>
                                </div>
                                <input type="text" name="modalSelectIdno" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-5 align-self-center">
                            <div class="input-group">
                                <div class="pull-left input-group-prepend">
                                    <span class="input-group-text">姓名</span>
                                </div>
                                <input type="text" name="modalSelectCname" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="form-row">
                        <div class="form-group col-5 align-self-center">
                            <div class="input-group">
                                <div class="pull-left input-group-prepend">
                                    <span class="input-group-text">服務單位</span>
                                </div>
                                <input type="text" name="modalSelectDept" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="form-row">
                        <div class="form-group col-2">
                            <button type="button" class="btn btn-success" onclick="queryTeacher()" >搜尋</button>
                        </div> 
                    </div>
                </div>
                <div id="teacherSelectContent">
                    
                </div>
            </div>                                                 
            <div class="modal-footer">
                
            </div>                                              
        </div>
    </div>
</div>

<script type="text/javascript">

var select_teacher = null;

function showTeacherModol(id = null)
{
    select_teacher = id;
    $("#selectTeacher").modal('show');
}

function queryTeacher(page = null)
{
    var idno = $("input[name=modalSelectIdno]").val();
    var cname = $("input[name=modalSelectCname]").val();
    var dept = $("input[name=modalSelectDept]").val();
    $.ajax({
        url: "/admin/teacherSelectModal",
        data: {'idno': idno, 'cname':cname, 'dept':dept, 'page' : page}
    }).done(function(response) {
        $("#teacherSelectContent").html(response);            
    });    
}


function initTeacherModal()
{
    console.log('teacher');
    $('#teacherSelectContent').on('click', '.pagination li a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href'),
            page = url.split('page=')[1]
            
        queryTeacher(page);
    });    
}

window.addEventListener("load",function(event) {
    initTeacherModal();
},false);



</script>