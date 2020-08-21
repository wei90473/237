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
            <label class="col-form-label">人員身份：<font color="blue">公務人員</font></label>
            <label class="col-form-label" style="margin-left:10px;">身分：</label>
            <div style="width:100px;">
                {{ Form::select('type', $t13tb_fileds['type'], null, ['class' => 'form-control']) }}
            </div>  
            <label class="col-form-label" style="margin-left:10px;">結業證號：</label>
            <div>
                @if(!empty($t04tb->diploma))
                    {{ Form::text('diploma', config('database_fields.t04tb')['diploma'][$t04tb->diploma], ['class' => 'form-control', 'disabled' => 'disabled']) }}
                @endif 
            </div>
            <div>
                {{ Form::text('docno', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
            </div>                                                                                                                         
        </div>                         
    </div>    
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label" >狀態：</label>
            <div style="width:100px;padding-left:10px;padding-right:10px;">
                {{ Form::select('status', $t13tb_fileds['status'], null, ['class' => 'form-control' , 'onchange' => 'status_change(this.value)']) }}
            </div>                                     
            <label class="col-form-label">退訓日期/時間：</label>
            <div>
                {{ Form::text('dropdate', null, ['class' => 'form-control', 'disabled' => (isset($t13tb) && $t13tb->status == 3) ? null : 'disabled']) }}
            </div>
            <div>
                {{ Form::text('droptime', null, ['class' => 'form-control', 'disabled' => (isset($t13tb) && $t13tb->status == 3) ? null : 'disabled']) }} 
            </div>                                        
            <label class="col-form-label" style="margin-left:10px;">認證與否</label>
            <div class="form-check form-check-inline">
                {{ Form::radio('authorize', 'Y', (empty($t13tb->authorize)) ? true : null , ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="inlineRadio1">是</label>
            </div>
            <div class="form-check form-check-inline">
                {{ Form::radio('authorize', 'N', null, ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="inlineRadio2">否</label>
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
                @if($action == 'create' || $action == 'change_student')
                    {{ Form::text('m02tb[idno]', (isset($new_idno)) ? $new_idno : null, ['class' => 'form-control', 'readonly' => 'readonly']) }}
                @elseif($action == 'edit')
                    {{ Form::text('m02tb[idno]', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
                @endif 
            </div>

            <label class="col-form-label">未到訓通知：</label>
            <div class="form-check form-check-inline">
                {{ Form::radio('not_present_notification', '1', null, ['class' => 'form-check-input', 'disabled' => (isset($t13tb) && $t13tb->status != 2) ? true : null ]) }}
                <label class="form-check-label" for="inlineRadio1">是</label>
            </div>
            <div class="form-check form-check-inline">
                {{ Form::radio('not_present_notification', '2', true, ['class' => 'form-check-input', 'disabled' => (isset($t13tb) && $t13tb->status != 2) ? true : null ]) }}
                <label class="form-check-label" for="inlineRadio1">否</label>
            </div>                           
        </div>    
                            
    </div> 
    
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">英文姓名</label>
            <div class="col-2">
                {{ Form::text('m02tb[ename]', null, ['class' => 'form-control']) }}
            </div> 

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
            @if($action == "change_student")
                {{ Form::text('no', null, ['class' => 'form-control', 'style' => 'width:75px', 'disabled' => 'disabled']) }}
            @else
                {{ Form::text('no', null, ['class' => 'form-control', 'style' => 'width:75px']) }}
            @endif                
            </div> 
            
            <label class="col-form-label" style="padding-left:10px;">組別：</label>
            <div>
                {{ Form::text('groupno', null, ['class' => 'form-control', 'style' => 'width:50px']) }}
            </div>                                                                                   
        </div>                         
    </div>

    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">主管機關：</label>
            <div class="col-2">
                {{ Form::select('organ', $m13tbs, null, ['class' => 'form-control select2']) }}
            </div>
            <label class="col-form-label">服務機關：</label>
            <div class="col-2">
                {{ Form::text('dept', null, ['class' => 'form-control']) }}
            </div>                                        
        </div>                         
    </div>
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">官職等</label>
            <div class="col-2">
                {{ Form::select('rank', $t13tb_fileds['rank'], null, ['class' => 'form-control select2']) }}  
            </div>
            <label class="col-form-label">職稱</label>
            <div class="col-2">
                {{ Form::text('position', null, ['class' => 'form-control']) }}
            </div>                                        
        </div>                         
    </div> 
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">最高學歷</label>
            <div class="col-1">
            {{ Form::select('ecode', $t13tb_fileds['ecode'], null, ['class' => 'form-control select2']) }}  
            </div>                                        
            <div class="col-2">
                {{ Form::text('education', null, ['class' => 'form-control']) }}
            </div>
        </div>                         
    </div> 
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">機關地址</label>
            <div class="col-1">
                {{ Form::select('m02tb[offaddr1]', $address, null, ['class' => 'form-control select2']) }}  
            </div>                                         
            <div class="col-2">
                {{ Form::text('m02tb[offaddr2]', null, ['class' => 'form-control']) }}
            </div>

            <label class="col-form-label">ZIP</label>
            <div style="width:100px;padding-left:10px;padding-right:10px">
                {{ Form::text('m02tb[offzip]', null, ['class' => 'form-control']) }}
            </div>    
            <a href='https://www.post.gov.tw/post/internet/Postal/index.jsp?ID=208' target="_blank">
                <button type="button" class='btn btn-primary'>郵遞區號</button>
            </a>
        </div>                         
    </div> 

    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">住家地址</label>
            <div class="col-1">
                {{ Form::select('m02tb[homaddr1]', $address, null, ['class' => 'form-control select2']) }}  
            </div>
            <div class="col-2">
                {{ Form::text('m02tb[homaddr2]', null, ['class' => 'form-control']) }}
            </div> 
            <label class="col-form-label">ZIP</label>
            <div style="width:100px;padding-left:10px;padding-right:10px">
                {{ Form::text('m02tb[homzip]', null, ['class' => 'form-control']) }}
            </div>
            <label class="col-form-label">郵寄地址：</label>
            <div class="form-check form-check-inline">
                {{ Form::radio('m02tb[send]', 1, null, ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="inlineRadio1">機關</label>
            </div>
            <div class="form-check form-check-inline">
                {{ Form::radio('m02tb[send]', 2, null, ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="inlineRadio2">住家</label>
            </div>                                                                                       
        </div>                         
    </div> 
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">電話(公一)</label>
            <div class="col-1">
                {{ Form::text('m02tb[offtela1]', null, ['class' => 'form-control']) }}
            </div>
            <div class="col-2">
                {{ Form::text('m02tb[offtelb1]', null, ['class' => 'form-control']) }}
            </div>
            <div class="col-1">
                {{ Form::text('m02tb[offtelc1]', null, ['class' => 'form-control']) }}
            </div>                                                                                                                          
        </div>                         
    </div> 
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">電話(公二)：</label>
            <div class="col-1">
                {{ Form::text('m02tb[offtela2]', null, ['class' => 'form-control']) }}
            </div>
            <div class="col-2">
                {{ Form::text('m02tb[offtelb2]', null, ['class' => 'form-control']) }}
            </div>
            <div class="col-1">
                {{ Form::text('m02tb[offtelc2]', null, ['class' => 'form-control']) }}
            </div>   
            <label class="col-form-label">E-mail：</label>
            <div class="col-2">
                {{ Form::text('m02tb[email]', null, ['class' => 'form-control']) }}
            </div>                                                                                                                          
        </div>                         
    </div> 
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">傳真(公)：</label>
            <div class="col-1">
                {{ Form::text('m02tb[offfaxa]', null, ['class' => 'form-control']) }}
            </div>
            <div class="col-2">
                {{ Form::text('m02tb[offfaxb]', null, ['class' => 'form-control']) }}
            </div>            
            <label class="col-form-label">行動電話：</label>
            <div class="col-2">
                {{ Form::text('m02tb[mobiltel]', null, ['class' => 'form-control']) }}  
            </div>                                        
        </div>                         
    </div> 

    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">電話(宅)</label>
            <div class="col-1">
                {{ Form::text('m02tb[homtela]', null, ['class' => 'form-control']) }}  
            </div>                                        
            <div class="col-2">
                {{ Form::text('m02tb[homtelb]', null, ['class' => 'form-control']) }}  
            </div>
        </div>                         
    </div>      
    
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">人事單位姓名</label>
            <div class="col-2">
            {{ Form::text('offname', null, ['class' => 'form-control']) }}  
            </div>
            <label class="col-form-label">人事單位E-mail</label>
            <div class="col-2">
            {{ Form::text('offemail', null, ['class' => 'form-control']) }}  
            </div>                                        
        </div>                         
    </div> 
            
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">人事單位辦公室電話</label>
            <div class="col-2">
            {{ Form::text('offtel', null, ['class' => 'form-control']) }}    
            </div>
        </div>                         
    </div>                                                                                                                                                                                                                                                               
    <div class="form-group">
        <div class="input-group">
            <div class="form-check form-check-inline">
                {{ Form::checkbox('dorm', 'Y', (isset($t13tb) && $t13tb->dorm == "Y"), ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="defaultCheck1">
                    住宿
                </label>
            </div>
            <div class="form-check form-check-inline">
                {{ Form::checkbox('extradorm', 'Y', (isset($t13tb) && $t13tb->extradorm == "Y"), ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="defaultCheck2">
                    提前住宿
                </label>
            </div>
            <div class="form-check form-check-inline">
                {{ Form::checkbox('nonlocal', 'Y', (isset($t13tb) && $t13tb->nonlocal == "Y"), ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="defaultCheck2">
                    遠道者
                </label>
            </div>
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
            <label class="col-form-label">人員註記：</label>
            <div class="form-check form-check-inline">
                {{ Form::checkbox('m02tb[chief]', 'Y', (isset($t13tb->m02tb) && $t13tb->m02tb->chief == "Y"), ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="defaultCheck1">
                    主管
                </label>
            </div>
            <div class="form-check form-check-inline">
                {{ Form::checkbox('m02tb[personnel]', 'Y', (isset($t13tb->m02tb) && $t13tb->m02tb->personnel == "Y"), ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="defaultCheck2">
                    人事人員
                </label>                                        
            </div>
            <div class="form-check form-check-inline">
                {{ Form::checkbox('m02tb[aborigine]', 'Y', (isset($t13tb->m02tb) && $t13tb->m02tb->aborigine == "Y"), ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="defaultCheck2">
                    原住民
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
            @if(isset($t13tb))
                最後更新時間：{{ $t13tb->upddate }} 
            @endif 
        </div>
    </div>
                                
</div>

<script>

</script>