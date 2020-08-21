<div style="padding:10px;padding-left:0px;">  
    @if(isset($student))
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label"><font color="blue">被換學員：{{ $student->cname }}</font></label>                                                                                                                      
        </div>                         
    </div> 
    @endif   
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">人員身份：<font color="blue">一般民眾</font></label>                                                                                                                        
        </div>                         
    </div>    

    <div class="form-group">
        <div class="input-group">                                  
            <label class="col-form-label">身分證號：</label>
            <div class="col-2">
                @if ($action == "edit")
                {{ Form::text('idno', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
                @else
                {{ Form::text('idno', null, ['class' => 'form-control']) }}
                @endif 
            </div>            
        </div>              
    </div> 

    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">姓名：</label>
            <div class="col-2">
                {{ Form::text('cname', null, ['class' => 'form-control']) }}
            </div>                                               
        </div>              
    </div> 

    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">性別：</label>
            <div class="form-check form-check-inline">
                {{ Form::radio('sex', 'M', null, ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="inlineRadio1">男</label>
            </div>
            <div class="form-check form-check-inline">
                {{ Form::radio('sex', 'F', null, ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="inlineRadio2">女</label>
            </div>                                                                                  
        </div>                         
    </div>
    <div class="form-group">
        <div class="input-group">  
            <label class="col-form-label">E-mail：</label>
            <div class="col-2">
                {{ Form::text('email', null, ['class' => 'form-control']) }}
            </div>                                                                                                                          
        </div>                         
    </div> 
    <div class="form-group">
        <div class="input-group">  
            <label class="col-form-label">聯絡電話：</label>
            <div class="col-2">
                {{ Form::text('mobiltel', null, ['class' => 'form-control']) }}
            </div>                                                                                                                          
        </div>                         
    </div>          
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">特殊狀況：</label>
            <div class="form-check form-check-inline">
                {{ Form::checkbox('handicap', 'Y', (isset($t27tb) && $t27tb->handicap == "Y"), ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="defaultCheck2">
                    行動不便
                </label>                                        
            </div>
            <div class="form-check form-check-inline">
                {{ Form::checkbox('vegan', 'Y', (isset($t27tb) && $t27tb->vegan == "Y"), ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="defaultCheck2">
                    素食
                </label>
            </div>
        </div>                         
    </div>                     
</div>