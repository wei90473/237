<div class="form-row">
    <div class="form-group col-md-4">
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
    <div class="form-group col-md-5">
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
            <label class="col-form-label">姓名：</label>
            {{ Form::text('cname', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">英文姓名：</label>
            {{ Form::text('ename', null, ['class' => 'form-control']) }}
        </div>
    </div>                              
</div>  
<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">出生日期：</label>
            {{ Form::text('birth', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
        </div>                                   
    </div>                            
</div>      

<div class="form-row search-float">
    <div class="form-group col-md-4">
        <div class="input-group">
            <label class="col-form-label">主管機關：</label>
            <div class=" col-md-9">
            {{ Form::select('organ', $m13tbs, null, ['class' => 'select2']) }} 
            </div>
        </div>                  
    </div>
     
    <div class="form-group col-md-6">
        <div class="input-group">
            <label class="col-form-label">服務機關：</label>
            <div class="col-md-3">
            {{ Form::text('enrollid', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'enrollid']) }}
            </div>
            <div class="col-md-6">
            {{ Form::text('m17tb[enrollname]', null, ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'enrollid_name']) }}
            </div>
            <div class="input-group-prepend">
                <button class="btn btn-outline-secondary" type="button" style="zoom: 0.8" onclick="showM17tbModol('enrollid')">...</button>
            </div>
        </div>
    </div>   
                                          
</div>  

<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">官職等：</label>
            {{ Form::select('rank', $m02tb_fields['rank'], null, ['class' => 'form-control']) }}
        </div>                                   
    </div>
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">職稱：</label>
            {{ Form::text('position', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>                                                                 
</div>  
<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">最高學歷：</label>
            {{ Form::select('ecode', $m02tb_fields['ecode'], null, ['class' => 'form-control']) }}
        </div>                                   
    </div>
    <div class="form-group col-md-3">
        <div class="input-group">
        {{ Form::text('education', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>                                                                 
</div> 
<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">機關地址：</label>
            {{ Form::select('offaddr1', array_merge(['' => '請選擇'], config('address.county')) ,null, ['class' => 'form-control']) }}
        </div>                                   
    </div>
    <div class="form-group col-md-3">
        <div class="input-group">
            {{ Form::text('offaddr2', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>          
    <div class="form-group col-md-1">
        <div class="input-group">
        {{ Form::text('offzip', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>                                                       
</div>  
<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">住家地址：</label>
            {{ Form::select('homaddr1', array_merge(['' => '請選擇'], config('address.county')) ,null, ['class' => 'form-control']) }}
        </div>                                   
    </div>
    <div class="form-group col-md-3">
        <div class="input-group">
        {{ Form::text('homaddr2', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>
    <div class="form-group col-md-1">
        <div class="input-group">
        {{ Form::text('homzip', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>                                                                     
</div>
<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">郵遞地址：</label>
            <div class="form-check form-check-inline">
                {{ Form::radio('send', 1, ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                <label class="form-check-label" for="inlineRadio2">機關</label>
            </div>   
            <div class="form-check form-check-inline">
                {{ Form::radio('send', 2, ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                <label class="form-check-label" for="inlineRadio2">住家</label>
            </div> 
        </div>                                   
    </div>
                                                             
</div>  

<div class="form-row">
    <div class="form-group col-md-2">
        <div class="input-group">
            <label class="col-form-label">電話(公一)：</label>
            {{ Form::text('offtela1', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>
    <div class="form-group col-md-3">
        <div class="input-group">
            {{ Form::text('offtelb1', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>                                                                 
    <div class="form-group col-md-1">
        <div class="input-group">
            {{ Form::text('offtelc1', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>     
</div> 

<div class="form-row">
    <div class="form-group col-md-2">
        <div class="input-group">
            <label class="col-form-label">電話(公二)：</label>
            {{ Form::text('offtela2', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>
    <div class="form-group col-md-3">
        <div class="input-group">
            {{ Form::text('offtelb2', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>                                                                 
    <div class="form-group col-md-1">
        <div class="input-group">
            {{ Form::text('offtelb2', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>     
</div> 

<div class="form-row">
    <div class="form-group col-md-2">
        <div class="input-group">
            <label class="col-form-label">傳真(公)：</label>
            {{ Form::text('offfaxa', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>
    <div class="form-group col-md-3">
        <div class="input-group">
            {{ Form::text('offfaxb', null, ['class' => 'form-control']) }}
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
    <div class="form-group col-md-2">
        <div class="input-group">
            <label class="col-form-label">電話(宅)：</label>
            {{ Form::text('homtela', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>
    <div class="form-group col-md-3">
        <div class="input-group">
            {{ Form::text('homtelb', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>    
                                                             
</div> 

<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">行動電話：</label>
            {{ Form::text('mobiltel', null, ['class' => 'form-control']) }}
        </div>                                   
    </div>    
</div>

<div class="form-row">
    <div class="form-group col-md-3">
        <div class="input-group">
            <label class="col-form-label">電話(人事總處)：</label>
            {{ Form::text('dgpatel', null, ['class' => 'form-control', 'disabled' => true]) }}
        </div>                                   
    </div>    
</div>


<div class="form-row">
    <div class="form-group">
        <div class="form-check-inline">
            <label class="form-check-label">人員註記：</label>
            <label class="form-check-label">
                {{ Form::checkbox('chief', "Y", ($student->chief == "Y"),['class' => 'form-check-input']) }}
                主管
            </label>
        </div>

        <div class="form-check-inline">
            <label class="form-check-label">
                {{ Form::checkbox('personnel', "Y", ($student->personnel == "Y"), ['class' => 'form-check-input']) }}
                人事人員
            </label>
        </div>   

        <div class="form-check-inline">
            <label class="form-check-label">
                {{ Form::checkbox('aborigine', "Y", ($student->aborigine == "Y"), ['class' => 'form-check-input']) }}
                原住民
            </label>
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
            <!-- <textarea nameclass="form-control" ></textarea> -->
            {{ Form::textarea('special_situation', null, ['class' => 'form-control', "style" => "height:100px;"]) }}
        </div>                                   
    </div>                                                              
</div> 

<div class="form-row">
    <div class="form-group">
        <div class="form-check-inline">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="m22tb[usertype1]" value="Y" onchange="showM22tb()"
                {{ (isset($student->m22tb) && $student->m22tb->usertype1 == 'Y') ? 'checked' : null }}
                > 
                學員
            </label>
        </div>  

        <div class="form-check-inline">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="m22tb[usertype2]" value="Y" {{ (isset($student->m22tb) && $student->m22tb->usertype2 == 'Y') ? 'checked' : null }}>
                講座
            </label>
        </div>    

        <div class="form-check-inline">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="is_worker" value="Y" onchange="hideOrShowAccount('m21tb', this.checked)" {{ (isset($student->m21tb)) ? 'checked' : null }} >
                訓練承辦人
            </label>
        </div> 

        <div class="form-check-inline">
            <label class="form-check-label">
            <input type="checkbox" class="form-check-input" name="m22tb[usertype3]" value="Y" {{ (isset($student->m22tb) && $student->m22tb->usertype3 == 'Y') ? 'checked' : null }} onchange="showM22tb()">
                學院同仁
            </label>
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

<div id="m22tb" style="border: 1px solid #000; padding:10px;margin-top:10px;max-width: 1000px; {{ (isset($student->m22tb) && ($student->m22tb->usertype1 == 'Y' || $student->m22tb->usertype2 == 'Y' || $student->m22tb->usertype3 == 'Y')) ? '' : 'display:none' }}">
    <div class="form-row">
        <div class="form-group col-md-5">
            <label class="col-form-label col-md"><font color="blue">個人帳號</font></label>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-5">
            <div class="input-group">
                <label class="col-form-label col-md">自訂帳號：</label>
                {{ Form::text('m22tb[selfid]', null, ['class' => 'form-control']) }}
            </div>                                                                                        
        </div>
    </div>    
    <div class="form-row">
        <div class="form-group col-md-5">
            <div class="input-group">
                <label class="col-form-label col-md">密碼：</label>
                {{ Form::password('m22tb[userpsw]', ['class' => 'form-control']) }}
            </div>                                                                                        
        </div>
        
        <div class="form-group">
            <div class="input-group">
                <button type="button" class="btn btn-primary" onclick="resetPassCnt('student', 'password')" {{ ($studentPswIsDefault || empty($student->m22tb)) ? 'disabled' : '' }}>重置密碼</button> 
            </div>                                                                                   
        </div> 

        <div class="form-group col-md-4">
            <div class="input-group">
                <label class="col-form-label col-md-6">密碼錯誤次數：</label>
                <div class="col-md-4">
                {{ Form::text('m22tb[pswerrcnt]', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
                </div>
            </div>                                                                                    
        </div>   

        <div class="form-group">
            <div class="input-group">
                <button type="button" class="btn btn-primary" onclick="resetPassCnt('student', 'passwordCnt')" {{ (empty($student->m22tb)) ? 'disabled' : '' }}>重置密碼次數</button> 
            </div>                                                                                   
        </div>               
    </div>   

    <div class="form-row">
        <div class="form-group col-md-5">
            <div class="input-group">
                <label class="col-form-label col-md">啟用狀態：</label>
                <div class="form-check form-check-inline">
                    {{ Form::radio('m22tb[status]', 'Y', null, ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                    <label class="form-check-label" for="inlineRadio2">啟用</label>
                </div>   
                <div class="form-check form-check-inline">
                    {{ Form::radio('m22tb[status]', 'N', null, ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                    <label class="form-check-label" for="inlineRadio2">停用</label>
                </div>   
            </div>
        </div>                              
    </div>        
</div>

<div id="m21tb"  style="border: 1px solid #000; padding:10px;margin-top:10px;max-width: 1000px;{{ isset($student->m21tb)  ? '' : 'display:none' }}">
    <div class="form-row">
        <div class="form-group col-md-5">
            <label class="col-form-label col-md"><font color="blue">訓練承辦人</font></label>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-5">
            <div class="input-group">
                <label class="col-form-label col-md">機關代碼：</label>
                {{ Form::text('m21tb[enrollorg]', null, ['class' => 'form-control']) }}
            </div>                                                                                        
        </div>
    </div>        
    <div class="form-row">
        <div class="form-group col-md-5">
            <div class="input-group">
                <label class="col-form-label col-md">自訂帳號：</label>
                {{ Form::text('m21tb[selfid]', null, ['class' => 'form-control']) }}
            </div>                                                                                        
        </div>
    </div>    
    <div class="form-row">
        <div class="form-group col-md-5">
            <div class="input-group">
                <label class="col-form-label col-md">密碼：</label>
                {{ Form::password('m21tb[userpsw]', ['class' => 'form-control']) }}
            </div>                                                                                        
        </div>
        <div class="form-group">
            <div class="input-group">
                <button type="button" class="btn btn-primary" onclick="resetPassCnt('sponsor', 'password')" {{ ($m21tbPswIsDefault || empty($student->m21tb)) ? 'disabled' : '' }}>重置密碼</button> 
            </div>                                                                                   
        </div>              
        <div class="form-group col-md-4">
            <div class="input-group">
                <label class="col-form-label col-md-6">密碼錯誤次數：</label>
                <div class="col-md-4">
                {{ Form::text('m21tb[pswerrcnt]', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
                </div>
            </div>                                                                                        
        </div>   
        <div class="form-group">
            <div class="input-group">
                <button type="button" class="btn btn-primary" onclick="resetPassCnt('sponsor', 'passwordCnt')" {{ (empty($student->m21tb)) ? 'disabled' : '' }}>重設密碼次數</button> 
            </div>                                                                                   
        </div>                   
    </div>   

    <div class="form-row">
        <div class="form-group col-md-5">
            <div class="input-group">
                <label class="col-form-label col-md">啟用狀態：</label>
                <div class="form-check form-check-inline">
                    {{ Form::radio('m21tb[status]', 'Y', true, ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                    <label class="form-check-label" for="inlineRadio2">啟用</label>
                </div>   
                <div class="form-check form-check-inline">
                    {{ Form::radio('m21tb[status]', 'N', null, ['class' => 'form-check-input', 'style' => 'min-width: 0px;']) }}
                    <label class="form-check-label" for="inlineRadio2">停用</label>
                </div>   
            </div>
        </div>                              
    </div>  
           
</div>


