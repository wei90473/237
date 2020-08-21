<div style="padding:10px;padding-left:0px;">  
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">人員身份：公務人員</label>
        </div>                         
    </div>                            
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label"><font color="red">&nbsp*&nbsp</font>身分證號</label>
            <div class="col-2">
                {{ Form::text('idno', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
            </div>
            <label class="col-form-label">姓名</label>
            <div class="col-2">
                {{ Form::text('cname', null, ['class' => 'form-control']) }}
            </div>                                          
            <label class="col-form-label">性別</label>
            <div class="form-check form-check-inline">
                {{ Form::radio('sex', 'M', null, ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="inlineRadio1">男</label>
            </div>
            <div class="form-check form-check-inline">
                {{ Form::radio('sex', 'F', null, ['class' => 'form-check-input']) }}
                <label class="form-check-label" for="inlineRadio2">女</label>
            </div>   
            <label class="col-form-label"><font color="red">&nbsp*&nbsp</font>出生日期</label>
            <div class="col-2">
                {{ Form::text('birth', null, ['class' => 'form-control', 'required' => true]) }}
            </div>                                                                                     
        </div>    
                            
    </div> 

    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label"><font color="red">&nbsp*&nbsp</font>主管機關</label>
            <div class="col-2">
                {{ Form::select('organ', $m13tbs, null, ['class' => 'form-control select2', 'required' => true]) }}
            </div>
            <label class="col-form-label">服務機關</label>
            <div class="col-2">
                <!-- {{ Form::select('loginid', [], null, ['class' => 'form-control select2']) }}   -->
                {{ Form::text('dept', null, ['class' => 'form-control']) }}
            </div>                                                     
        </div>                         
    </div>
    <div style="border:1px solid #000;position:absolute;right:10%;padding:15px;z-index:100">
        <div class="form-check">
            {{ Form::checkbox('dorm', 'Y', (isset($t27tb) && old('dorm',$t27tb->dorm) == 'Y'), ['class' => 'form-check-input']) }}
            <label class="form-check-label" for="defaultCheck1">
                住宿
            </label>
        </div>
        <div class="form-check">
            {{ Form::checkbox('extradorm', 'Y', (isset($t27tb) && old('extradorm',$t27tb->extradorm) == 'Y'), ['class' => 'form-check-input']) }}
            <label class="form-check-label" for="defaultCheck2">
                提前住宿
            </label>
        </div>
        <div class="form-check">
            {{ Form::checkbox('nonlocal', 'Y', (isset($t27tb) && old('nonlocal',$t27tb->nonlocal) == 'Y'), ['class' => 'form-check-input']) }}
            <label class="form-check-label" for="defaultCheck2">
                遠道者
            </label>
        </div>   
        <div class="form-check">
            {{ Form::checkbox('handicap', 'Y', (isset($t27tb) && old('handicap',$t27tb->handicap) == 'Y'), ['class' => 'form-check-input']) }}
            <label class="form-check-label" for="defaultCheck2">
                身心障礙
            </label>
        </div>   
        <div class="form-check">
            {{ Form::checkbox('vegan', 'Y', null, ['class' => 'form-check-input']) }}
            <label class="form-check-label" for="defaultCheck2">
                素食
            </label>
        </div>                                                                                                                                               
    </div> 
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label"><font color="red">&nbsp*&nbsp</font>官職等</label>
                <div class="col-2">
                {{ Form::select('rank', $t27tb_fileds['rank'], null, ['class' => 'form-control select2', 'required' => true]) }}  
            </div>
            <label class="col-form-label">職稱</label>
            <div class="col-2">
                {{ Form::text('position', null, ['class' => 'form-control']) }}
            </div>        
            <label class="col-form-label">E-mail</label>
            <div class="col-2">
                {{ Form::text('email', null, ['class' => 'form-control']) }}
            </div>                                             
        </div>                         
    </div> 
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label"><font color="red">&nbsp*&nbsp</font>最高學歷</label>
            <div class="col-1">
            {{ Form::select('ecode', $t27tb_fileds['ecode'], null, ['class' => 'form-control select2', 'required' => true]) }}  
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
                {{ Form::select('offaddr1', $address, null, ['class' => 'form-control select2']) }}  
            </div>                                         
            <div class="col-2">
                {{ Form::text('offaddr2', null, ['class' => 'form-control']) }}
            </div>

            <label class="col-form-label">ZIP</label>
            <div class="col-1">
                {{ Form::text('offzip', null, ['class' => 'form-control']) }}
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
                {{ Form::select('homaddr1', $address, null, ['class' => 'form-control select2']) }}  
            </div>
            <div class="col-2">
                {{ Form::text('homaddr2', null, ['class' => 'form-control']) }}
            </div> 
            <label class="col-form-label">ZIP</label>
            <div class="col-1">
                {{ Form::text('homzip', null, ['class' => 'form-control']) }}
            </div>                                              
        </div>                         
    </div> 
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">電話(公)</label>
            <div class="col-1">
                {{ Form::text('offtela', null, ['class' => 'form-control']) }}
            </div>
            <div class="col-2">
                {{ Form::text('offtelb', null, ['class' => 'form-control']) }}
            </div>
            <div class="col-1">
                {{ Form::text('offtelc', null, ['class' => 'form-control']) }}
            </div>     
            <label class="col-form-label">傳真(公)</label>
            <div class="col-2">
                {{ Form::text('offfaxa', null, ['class' => 'form-control']) }}
            </div>                                                                                                                                 
        </div>                         
    </div> 
    <div style="border:1px solid #000;position:absolute;right:10%;padding:15px;z-index:100">
        <div class="form-check">
            {{ Form::checkbox('chief', 'Y', (isset($t27tb) && old('chief',$t27tb->chief) == 'Y'), ['class' => 'form-check-input']) }}
            <label class="form-check-label" for="defaultCheck1">
                主管
            </label>
        </div>
        <div class="form-check">
            {{ Form::checkbox('personnel', 'Y', (isset($t27tb) && old('personnel',$t27tb->personnel) == 'Y'), ['class' => 'form-check-input']) }}
            <label class="form-check-label" for="defaultCheck2">
                人事人員
            </label>
        </div>
        <div class="form-check">
            {{ Form::checkbox('aborigine', 'Y', (isset($t27tb) && old('aborigine',$t27tb->aborigine) == 'Y'), ['class' => 'form-check-input']) }}
            <!-- <input class="form-check-input" type="checkbox" value="" id="defaultCheck2"> -->
            <label class="form-check-label" for="defaultCheck2">
                原住民
            </label>
        </div>                                                                                                                                                 
    </div>       
    <div class="form-group">
        <div class="input-group">
            <label class="col-form-label">電話(宅)</label>
            <div class="col-1">
                {{ Form::text('homtela', null, ['class' => 'form-control']) }}  
            </div>                                        
            <div class="col-2">
                {{ Form::text('homtelb', null, ['class' => 'form-control']) }}  
            </div>
            <label class="col-form-label">行動電話</label>
            <div class="col-2">
                {{ Form::text('offfaxb', null, ['class' => 'form-control']) }}  
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
            <label class="col-form-label">人事單位辦公室電話</label>
            <div class="col-2">
            {{ Form::text('offtel', null, ['class' => 'form-control']) }}    
            </div>                     
        </div>                         
    </div> 

</div>                                
                                                  
                    
 
                                        
                