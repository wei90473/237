
<div class="modal fade" id="t01tb" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">選擇班別</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="search-float">
                    <div class="form-row">
                        <div class="form-group col-4 align-self-center">
                            <div class="input-group">
                                <div class="pull-left input-group-prepend">
                                    <span class="input-group-text">班號</span>
                                </div>
                                <input type="text" name="modalSelectClass" class="form-control">
                            </div>
                        </div>

                        <div class="form-group col-5 align-self-center">
                            <div class="input-group">
                                <div class="pull-left input-group-prepend">
                                    <span class="input-group-text">班別名稱</span>
                                </div>
                                <input type="text" name="modalSelectClassName" class="form-control">
                            </div>
                        </div>  

                        <div class="form-group col-2">
                            <button type="button" class="btn btn-success" onclick="queryt01tb()" >搜尋</button>
                        </div> 
                    </div>
                </div>
                <div id="t01tbSelectContent">
                    
                </div>
            </div>                                                 
            <div class="modal-footer">
                
            </div>                                              
        </div>
    </div>
</div>

<script type="text/javascript">

var select_t01tb = null;

function showt01tbModol(id = null)
{
    select_t01tb = id;
    $("#t01tb").modal('show');
}

function queryt01tb(page = null)
{
    var class_no = $("input[name=modalSelectClass]").val();
    var class_name = $("input[name=modalSelectClassName]").val();

    $.ajax({
        url: "/admin/t01tbSelectModal",
        data: {'class': class_no, 'class_name':class_name, 'page' : page}
    }).done(function(response) {
        $("#t01tbSelectContent").html(response);            
    });    
}

function initT01tbModal(){
    console.log('t01tb');
    $('#t01tbSelectContent').on('click', '.pagination li a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href'),
            page = url.split('page=')[1]
            
        queryt01tb(page);
    });    
}

window.addEventListener("load",function(event) {
    initT01tbModal();
},false);



</script>