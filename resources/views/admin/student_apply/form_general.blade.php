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
            <label class="col-form-label" style="margin-left:10px;">身分：</label>
            <div style="width:100px;">
                {{ Form::select('type', $t13tb_fileds['type'], null, ['class' => 'form-control']) }}
            </div>  
            <label class="col-form-label" style="margin-left:10px;">結業證號：</label>
            <div>
            {{ config('database.t04tb')['diploma'][$t04tb->diploma] }}
                {{ Form::text('diploma', config('database_fields.t04tb')['diploma'][$t04tb->diploma], ['class' => 'form-control', 'disabled' => 'disabled']) }}
            </div>
            <div>
                {{ Form::text('docno', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
            </div>                                                                                                                         
        </div>                         
    </div>    
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">狀態：</label>
            <div style="width:100px;padding-left:10px;padding-right:10px;">
                {{ Form::select('status', $t13tb_fileds['status'], null, ['class' => 'form-control' , 'onchange' => 'status_change(this.value)']) }}
            </div>                                     
            <label class="col-form-label">退訓日期/時間：</label>
            <div>
                {{ Form::text('dropdate', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
            </div>
            <div>
                {{ Form::text('droptime', null, ['class' => 'form-control', 'disabled' => 'disabled']) }} 
            </div>                                                                    
        </div>    
    </div> 

    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">姓名：</label>
            <div class="col-2">
                {{ Form::text('m02tb[cname]', null, ['class' => 'form-control']) }}
            </div>                                     
            <label class="col-form-label">身分證號：</label>
            <div class="col-2">
                @if ($action == "edit")
                {{ Form::text('m02tb[idno]', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
                @else
                {{ Form::text('m02tb[idno]', null, ['class' => 'form-control']) }}
                @endif 
            </div>            
        </div>              
    </div> 
    
    <div class="form-group">
        <div class="input-group">
                                   
            <label class="col-form-label">出生日期：</label>
            <div class="col-2">
                {{ Form::text('m02tb[birth]', null, ['class' => 'form-control', 'onchange' => 'computeAge()']) }}
            </div>
            
            <div>
                {{ Form::text('age', null, ['class' => 'form-control', 'style' => 'width:50px', 'disabled' => 'disabled']) }}
            </div>
            <label class="col-form-label">歲</label>                                        
        </div>                         
    </div> 
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">性別：</label>
            <div class="form-check form-check-inline">
                {{ Form::radio('m02tb[sex]', 'M', null, ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="inlineRadio1">男</label>
            </div>
            <div class="form-check form-check-inline">
                {{ Form::radio('m02tb[sex]', 'F', null, ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="inlineRadio2">女</label>
            </div>  
            <label class="col-form-label">學號：</label>
            <div>
                {{ Form::text('no', null, ['class' => 'form-control', 'style' => 'width:75px']) }}
            </div> 
            <label class="col-form-label" style="padding-left:10px;">組別：</label>
            <div>
                {{ Form::text('groupno', null, ['class' => 'form-control', 'style' => 'width:50px']) }}
            </div>                                                                                   
        </div>                         
    </div>
    <div class="form-group">
        <div class="input-group">  
            <label class="col-form-label">E-mail：</label>
            <div class="col-2">
                {{ Form::text('m02tb[email]', null, ['class' => 'form-control']) }}
            </div>                                                                                                                          
        </div>                         
    </div> 
    <div class="form-group">
        <div class="input-group">  
            <label class="col-form-label">聯絡電話：</label>
            <div class="col-2">
                {{ Form::text('m02tb[mobiltel]', null, ['class' => 'form-control']) }}
            </div>                                                                                                                          
        </div>                         
    </div>          
    <div class="form-group">
        <div class="input-group">
            <div class="form-check form-check-inline">
                {{ Form::checkbox('m02tb[handicap]', 'Y', (isset($t13tb->m02tb) && $t13tb->m02tb->handicap == "Y"), ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="defaultCheck2">
                    行動不便
                </label>                                        
            </div>
            <div class="form-check form-check-inline">
                {{ Form::checkbox('vegan', 'Y', (isset($t13tb) && $t13tb->vegan == "Y"), ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="defaultCheck2">
                    素食
                </label>
            </div>
        </div>                         
    </div>  
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">特殊狀況註記：</label>
            <div class="col-5">
                {!! Form::textarea('m02tb[special_situation]', null, ['class'=>'form-control', 'rows' => 4, 'cols' => 20]) !!}
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group">
            最後更新時間：{{ $t13tb->upddate }}
        </div>
    </div>
                                
</div>