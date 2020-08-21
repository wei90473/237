<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">人員身分：</label>
            <div class="form-check form-check-inline">
                {{ Form::radio('identity', 1, ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                <label class="form-check-label" for="inlineRadio2">公務人員</label>
            </div>   
            <div class="form-check form-check-inline">
                {{ Form::radio('identity', 2, ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                <label class="form-check-label" for="inlineRadio2">一般民眾</label>
            </div>                                                                                           
        </div>
    </div>                                
</div>

<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">姓名：</label>
            {{ Form::text('cname', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>                             
</div>  

<div class="form-row">
    <div class="form-group col-md-4">
        <div class="input-group">
            <label class="col-form-label">身分證號：</label>
            <div class="col-md-6">
            {{ Form::text('idno', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
            </div>
            <div class="input-group-prepend">
                <button class="btn btn-outline-secondary" type="button" style="zoom: 0.8; height: calc(2.25rem + 2px);margin-top:0px;" onclick="$('#modify_idno').modal('show')">修改身分證字號</button>
            </div>             
        </div>                                         
    </div>                          
</div>
 
<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">姓名：</label>
            {{ Form::text('cname', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>                             
</div>  

<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">性別：</label>
            <div class="form-check form-check-inline">
                {{ Form::radio('sex', 'M', ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                <label class="form-check-label" for="inlineRadio2">男</label>
            </div>   
            <div class="form-check form-check-inline">
                {{ Form::radio('sex', 'F', ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                <label class="form-check-label" for="inlineRadio2">女</label>
            </div>   
        </div>
    </div>       
</div>

<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
        <label class="col-form-label">E-Mail：</label>
        {{ Form::text('email', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>                                                                 
</div>   

<div class="form-row">
    <div class="form-group">
        <div class="form-check-inline">
            <label class="form-check-label">
                {{ Form::checkbox('handicap', "Y", ($student->handicap == "Y"), ['class' => 'form-check-input']) }}
                身心障礙
            </label>
        </div>  
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <div class="input-group">
            <label class="col-form-label">特殊狀況註記：</label>
            {{ Form::textarea('special_situation', null, ['class' => 'form-control', "style" => "height:100px;"]) }}
        </div>                                   
    </div>                                                              
</div> 
                           
<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">更新日期：</label>
            {{ Form::text('updated_at', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
        </div>                                   
    </div>    
</div>                           