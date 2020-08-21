@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')



    <?php $_menu = 'employ';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座聘任處理表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/employ" class="text-info">講座聘任處理列表</a></li>
                        <li class="active">講座聘任處理表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/employ/'.$data->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/employ/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座聘任處理表單</h3></div>
                    <div class="card-body pt-4">
                        <?php if(isset($class_data['class'])){ ?>
                        <input type="hidden" name="class" value="{{ $class_data['class'] }}">
                        <input type="hidden" name="term" value="{{ $class_data['term'] }}">
                        <?php } ?>
                        <?php $classList = $base->getDBList('T01tb', ['class', 'name']);?>
                        <!-- 班號 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">班號<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="class" name="class" class="select2 form-control select2-single input-max" required disabled onchange="classChange()">
                                    @foreach($classList as $key => $va)
                                        <option value="{{ $va->class }}" {{ old('class', (isset($data->class))? $data->class : 1) == $va->class? 'selected' : '' }} {{ old('class', (isset($class_data['class']))? $class_data['class'] : 1) == $va->class? 'selected' : '' }} >{{ $va->class }}{{ $va->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <!-- 期別 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">期別<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="term" name="term" class="select2 form-control select2-single input-max" required {{ old('course', (isset($data->course))? $data->course : 1)? 'disabled' : '' }} onchange="termChange()">
                                    @foreach(config('app.array') as $key2 => $va)
                                        <option value="{{ $key2 }}" {{ old('term', (isset($data->term))? $data->term : 1) == $key2? 'selected' : '' }}  >{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <!-- 課程名稱 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">課程名稱<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="course" name="course" class="select2 form-control select2-single input-max" {{ (isset($data->course))? 'disabled' : '' }} required onchange="courseChange()" >
                                    @foreach(config('app.array') as $key3 => $va)
                                        <option value="{{ $key3 }}" {{ old('course', (isset($data->course))? $data->course : 1) == $key3? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <!-- 姓名 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">姓名<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="idno" name="idno" class="form-control select2-single input-max" {{ (isset($data->idno))? 'disabled' : '' }} required onchange="idnoChange()" >
                                    @if(isset($idnoData))
                                        <option value="{{ $idnoData->idno }}">{{ $idnoData->cname }}{{ $idnoData->ename }}{{ ($idnoData->education)? '('.$idnoData->education.')' : '' }}{{ ' '. $idnoData->dept }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>


                        <!-- 類型 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">類型<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <form>
                                    <fieldset id="type" name="type" class="typeCSS">
                                        @foreach(config('app.employ_type') as $key => $va)
                                            <input id="type" name="type" type="radio" value="{{ $key }}" {{ old('type', (isset($data->type))? $data->type : 0) == $key? 'checked' : '' }} {{ old('type', (!isset($data->type))? 1 : 0) == $key? 'checked' : '' }} onchange="typeChange()" >{{ $va }}
                                        @endforeach
                                    </fieldset>
                                </form>
                            </div>
                        </div>


                        <!-- 分類 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">分類<span class="text-danger">*</span></label>
                            <div class="col-md-10">

                            @foreach(config('app.lecture_kind') as $key => $va)
                                <input id="kind_{{ $key }}" name="kind" type="radio" required value="{{ $key }}" {{ old('kind', (isset($data->kind))? $data->kind : 0) == $key? 'checked' : '' }} {{ old('kind', (!isset($data->kind))? 1 : 0) == $key? 'checked' : '' }} onchange="countCoursePrice()" >{{ $va }}
                            @endforeach


                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right"></label>
                            <div class="col-md-10">
                                <input type="checkbox" id="no_tax" name="no_tax" style="min-width:20px; margin-left:5px;" {{ old('no_tax', (isset($data->no_tax) && $data->no_tax=='Y' )? 'checked':'') }} value="Y" >
                                不扣外國人6%稅額
                            </div>
                        </div>


                        <fieldset style="border:groove; padding: inherit">
                        <legend>講課酬勞</legend>

                        <!-- 授課鐘點時數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">授課鐘點時數</label>
                            <div class="col-sm-2">

                                <input type="text" class="form-control number-input-max" id="lecthr" name="lecthr" min="0" placeholder="請輸入授課鐘點時數" value="{{ old('lecthr', (isset($data->lecthr))? $data->lecthr : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^(\d||/.)]/g,'')" maxlength="255" required onchange="countCoursePrice();">
                                <!-- <div class="input-group bootstrap-touchspin number_box"> -->
                                    <!-- 減 -->
                                    <!-- <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span> -->

                                    <!-- 輸入欄位 -->
                                    <!-- <input type="text" class="form-control number-input-max" id="lecthr" name="lecthr" min="0" placeholder="請輸入授課鐘點時數" value="{{ old('lecthr', (isset($data->lecthr))? $data->lecthr : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="255" required onchange="countCoursePrice();"> -->

                                    <!-- 加 -->
                                    <!-- <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span> -->
                                <!-- </div> -->
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">鐘點費(50)</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control number-input-max" id="lectamt" name="lectamt" min="0" placeholder="請輸入鐘點費" value="{{ old('lectamt', (isset($data->lectamt))? $data->lectamt : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="countPrice()">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">稿費(9B)</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control number-input-max" id="noteamt" name="noteamt" min="0" placeholder="請輸入稿費" value="{{ old('noteamt', (isset($data->noteamt))? $data->noteamt : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="countPrice()">
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">講演費(50)</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control number-input-max" id="speakamt" name="speakamt" min="0" placeholder="請輸入講演費" value="{{ old('speakamt', (isset($data->speakamt))? $data->speakamt : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="countPrice()">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">評閱單價</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control number-input-max" id="review_unit_price" name="review_unit_price" min="0" placeholder="請輸入評閱單價" value="{{ old('review_unit_price', (isset($data->review_unit_price))? $data->review_unit_price : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="countReviewPrice()">
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">評閱數量</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control number-input-max" id="review_quantity" name="review_quantity" min="0" placeholder="請輸入評閱數量" value="{{ old('review_quantity', (isset($data->review_quantity))? $data->review_quantity : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="countReviewPrice()">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">評閱總金額(50)</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control number-input-max" id="review_total" name="review_total" min="0" placeholder="請輸入評閱總金額" value="{{ old('review_total', (isset($data->review_total))? $data->review_total : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="countPrice()">
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">其他薪資所得(50)</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control number-input-max" id="other_salary" name="other_salary" min="0" placeholder="請輸入其他薪資所得" value="{{ old('other_salary', (isset($data->other_salary))? $data->other_salary : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="countPrice()">
                            </div>
                        </div>

                        </fieldset>

                        <fieldset style="border:groove; padding: inherit">
                        <legend>交通費及住宿費</legend>

                        <?php $feeList = $base->getFeeList('S01tb', ['class', 'name']);?>
                        <!-- 火車出發地 -->
                        <!-- <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">火車出發地</label>
                            <div class="col-md-10">
                                <select id="trainstart" name="trainstart" class="select2 form-control select2-single input-max" onchange="$('#trainamt').val($(this).find(':selected').attr('data-fee'));countPrice();">
                                    <option value="" data-fee="0">無</option>
                                    @foreach($feeList as $key => $va)
                                        <option data-fee="{{ $va->fee }}" value="{{ $va->code }}" {{ old('trainstart', (isset($data->trainstart))? $data->trainstart : NULL) == $va->code? 'selected' : '' }}>{{ $va->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> -->



                        <!-- 飛機出發地 -->
                        <!-- <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">飛機出發地</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="planestart" name="planestart" placeholder="請輸入飛機出發地" value="{{ old('planestart', (isset($data->planestart))? $data->planestart : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div> -->

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">飛機高鐵出發地</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="planestart" name="planestart" placeholder="請輸入飛機高鐵出發地" value="{{ old('planestart', (isset($data->planestart))? $data->planestart : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">飛機高鐵到達地</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="plane_d" name="plane_d" placeholder="請輸入飛機高鐵到達地" value="{{ old('plane_d', (isset($data->plane_d))? $data->plane_d : '') }}" autocomplete="off" maxlength="255">
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">飛機高鐵金額</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control number-input-max" id="planeamt" name="planeamt" min="0" placeholder="請輸入飛機高鐵金額" value="{{ old('planeamt', (isset($data->planeamt))? $data->planeamt : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="countPrice()">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">汽車捷運出發地</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="mrt_o" name="mrt_o" placeholder="請輸入汽車捷運出發地" value="{{ old('mrt_o', (isset($data->mrt_o))? $data->mrt_o : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">汽車捷運到達地</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="mrt_d" name="mrt_d" placeholder="請輸入汽車捷運到達地" value="{{ old('mrt_d', (isset($data->mrt_d))? $data->mrt_d : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">汽車捷運金額</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control number-input-max" id="mrtamt" name="mrtamt" min="0" placeholder="請輸入汽車捷運金額" value="{{ old('mrtamt', (isset($data->mrtamt))? $data->mrtamt : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="countPrice()">
                            </div>
                        </div>

                        <!-- 火車車費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">火車出發地</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="train_o" name="train_o" placeholder="請輸入火車出發地" value="{{ old('train_o', (isset($data->train_o))? $data->train_o : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">火車到達地</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="train_d" name="train_d" placeholder="請輸入火車到達地" value="{{ old('train_d', (isset($data->train_d))? $data->train_d : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">火車金額</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control number-input-max" id="trainamt" name="trainamt" min="0" placeholder="請輸入火車金額" value="{{ old('trainamt', (isset($data->trainamt))? $data->trainamt : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="countPrice()">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">船舶出發地</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="ship_o" name="ship_o" placeholder="請輸入船舶出發地" value="{{ old('ship_o', (isset($data->ship_o))? $data->ship_o : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">船舶到達地</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="ship_d" name="ship_d" placeholder="請輸入船舶到達地" value="{{ old('ship_d', (isset($data->ship_d))? $data->ship_d : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">船舶金額</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control number-input-max" id="ship" name="ship" min="0" placeholder="請輸入船舶金額" value="{{ old('ship', (isset($data->ship))? $data->ship : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="countPrice()">
                            </div>
                        </div>

                        <!-- 短程車資 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">短程車資出發地</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="motoramt_o" name="motoramt_o" placeholder="請輸入出發地" value="{{ old('motoramt_o', (isset($data->motoramt_o))? $data->motoramt_o : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">短程車資到達地</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="motoramt_d" name="motoramt_d" placeholder="請輸入到達地" value="{{ old('motoramt_d', (isset($data->motoramt_d))? $data->motoramt_d : '') }}" autocomplete="off" maxlength="255">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">短程車資金額</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control number-input-max" id="motoramt" name="motoramt" min="0" placeholder="請輸入短程車資" value="{{ old('motoramt', (isset($data->motoramt))? $data->motoramt : 300) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="countPrice()">
                            </div>
                        </div>

                        <!-- 其他交通費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">住宿費</label>
                            <div class="col-sm-2">

                                <input type="text" class="form-control number-input-max" id="otheramt" name="otheramt" min="0" placeholder="請輸入其他交通費" value="{{ old('otheramt', (isset($data->otheramt))? $data->otheramt : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="countPrice()">

                            </div>
                        </div>

                        </fieldset>



                        <fieldset style="border:groove; padding: inherit">
                        <legend>合計</legend>
                        <!-- 合計講課酬勞 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">合計講課酬勞</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="teachtot" name="teachtot" value="{{ old('teachtot', (isset($data->teachtot))? $data->teachtot : 0) }}" autocomplete="off" maxlength="255" readonly>
                            </div>
                        </div>

                        <!-- 合計交通費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">合計交通費及住宿費</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="tratot" name="tratot" value="{{ old('tratot', (isset($data->tratot))? $data->tratot : 0) }}" autocomplete="off" maxlength="255" readonly>
                            </div>
                        </div>

                        <!-- 合計扣繳稅額 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">合計扣繳稅額</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="deductamt" name="deductamt" value="{{ old('deductamt', (isset($data->deductamt))? $data->deductamt : 0) }}" autocomplete="off" maxlength="255" readonly>
                            </div>
                        </div>

                        <!-- 合計補充保險費 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">合計補充保險費</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="insuretot" name="insuretot" value="{{ old('insuretot', (isset($data->insuretot))? $data->insuretot : 0) }}" autocomplete="off" maxlength="255" readonly>
                            </div>
                        </div>

                        <!-- 合計實付總計 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">合計實付總計</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="totalpay" name="totalpay" value="{{ old('totalpay', (isset($data->totalpay))? $data->totalpay : 0) }}" autocomplete="off" maxlength="255" readonly>
                            </div>
                        </div>
                        </fieldset>

                        <script>
                            function countPrice() {

                                // 講客酬勞
                                var teachtot = 0;

                                teachtot += parseFloat($('#lectamt').val());
                                teachtot += parseFloat($('#noteamt').val());
                                teachtot += parseFloat($('#speakamt').val());

                                teachtot += parseFloat($('#review_total').val());
                                teachtot += parseFloat($('#other_salary').val());

                                $('#teachtot').val(teachtot);

                                // 交通費
                                var tratot = 0;

                                tratot += parseFloat($('#motoramt').val());
                                tratot += parseFloat($('#trainamt').val());
                                tratot += parseFloat($('#planeamt').val());
                                tratot += parseFloat($('#otheramt').val());
                                tratot += parseFloat($('#ship').val());
                                tratot += parseFloat($('#mrtamt').val());

                                $('#tratot').val(tratot);


                                var totalpay = 0;

                                totalpay += parseFloat($('#teachtot').val());
                                totalpay += parseFloat($('#tratot').val());

                                totalpay = parseFloat(totalpay) - parseFloat($('#deductamt').val()) - parseFloat($('#insuretot').val());

                                $('#totalpay').val(totalpay);

                            }
                        </script>



                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/employ/detail?class={{ $class_data['class'] }}&term={{ $class_data['term'] }}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                        <?php if(isset($data->id)){?>
                        <span onclick="$('#del_form').attr('action', '/admin/employ/{{ $data->id }}');" data-toggle="modal" data-target="#del_modol" >
                            <button type="button" class="btn btn-sm btn-danger"> 刪除</button>
                        </span>
                        <?php }?>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script>

    function idnoChange() {
        let idnoValue = $('#idno').val();

        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "json",
            url: '/admin/employ/getkind',
            data: { idno: idnoValue },
            success: function(data){
                if( data == '1'){
                    $('#kind_1').prop("checked", true);
                }
                if( data == '2'){
                    $('#kind_2').prop("checked", true);
                }
                if( data == '3'){
                    $('#kind_3').prop("checked", true);
                }
                if( data == '4'){
                    $('#kind_4').prop("checked", true);
                }
            },
            error: function() {
                alert('無資料');
            }
        });
    }
    // 取得期別
    function classChange() {
        let classValue = $('#class').val();
        if(classValue.length == 5) {
            classValue = '0' + classValue;
        }

        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/employ/getterm',
            data: { class: classValue, selected: '{{ (isset($data))? $data->term : $class_data['term'] }}'},
            success: function(data){
                $('#term').html(data);
                $("#term").trigger("change");
            },
            error: function() {
                alert('Ajax Error');
            }
        });
    }

    // 取得課程
    @if ( !isset($data) )
    function termChange() {
        let classValue = $('#class').val();
        if(classValue.length == 5) {
            classValue = '0' + classValue;
        }

        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/employ/getcourse',
            data: { course: classValue, term: $('#term').val(), selected: '{{ (isset($data))? $data->course : '' }}'},
            success: function(data){
                $('#course').html(data);
                $("#course").trigger("change");
            },
            error: function() {
                alert('無課程');
            }
        });
    }
    @endif

    @if ( isset($data) )
    function termChange() {
        let classValue = $('#class').val();
        if(classValue.length == 5) {
            classValue = '0' + classValue;
        }

        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/employ/getcourse',
            data: { course: classValue, term: $('#term').val(), selected: '{{ (isset($data))? $data->course : '' }}'},
            success: function(data){
                $('#course').html(data);
            },
            error: function() {
                alert('無課程');
            }
        });
    }
    @endif

    function courseChange() {
        let classValue = $('#class').val();
        if(classValue.length == 5) {
            classValue = '0' + classValue;
        }
        let termValue = $('#term').val();
        let courseValue = $('#course').val();

        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/employ/getlecthr',
            data: { course: courseValue, term: termValue, class: classValue},
            success: function(data){
                @if ( !isset($data) )
                $('#lecthr').val(data);
                @endif

                countCoursePrice();
            },
            error: function() {
                alert('無課程');
            }
        });
    }

    $(document).ready(function(){
        // 初始化
        classChange();

        @if ( isset($data) )
        // setTimeout("lectamt()", 2000);
        @endif
    });

    // @if ( isset($data) )
    // function lectamt() {
    //     $('#lectamt').val( parseFloat({{ $data->lectamt }}) );
    //     countPrice();
    //     // alert('test');
    // }
    // @endif

    // 取得姓名
    $(function (){
        $("#idno").select2({
            language: 'zh-TW',
            width: '100%',
            // 最多字元限制
            maximumInputLength: 5,
            // 最少字元才觸發尋找, 0 不指定
            minimumInputLength: 1,
            // 當找不到可以使用輸入的文字
            // tags: true,
            placeholder: '',
            // AJAX 相關操作
            ajax: {
                url: '/admin/employ/getIdno',
                type: 'get',
                // 要送出的資料
                data: function (params){
                    // 在伺服器會得到一個 POST 'search'
                    return {
                        search: params.term
                    };
                },
                processResults: function (data){
                    console.log(data)

                    // 一定要返回 results 物件
                    return {
                        results: data,
                        // 可以啟用無線捲軸做分頁
                        pagination: {
                            more: true
                        }
                    }
                }
            }
        });
    })

    function typeChange() {
        if($('input[name=type]:checked').val() == '3'){
            $('#kind_4').prop("checked", true);
        }
        countCoursePrice();
    }

    function countReviewPrice() {

        var review_unit_price = parseFloat($('#review_unit_price').val());
        var review_quantity = parseFloat($('#review_quantity').val());

        $('#review_total').val(review_unit_price * review_quantity);

        // 更新合計
        countPrice();
    }

    // 計算講課酬勞
    function countCoursePrice() {
        // 外聘
        var outlectunit = parseFloat('{{ $base->getSystemParameter('outlectunit') }}');
        // 總處
        var burlectunit = parseFloat('{{ $base->getSystemParameter('burlectunit') }}');
        // 內聘
        var inlectunit = parseFloat('{{ $base->getSystemParameter('inlectunit') }}');

        var lecthr = parseFloat($('#lecthr').val());

        if ($('input[name=kind]:checked').val() == '1') {
        	if(lecthr == 0){
        		courseChange();
        		lecthr = parseFloat($('#lecthr').val());
        	}
            $('#lectamt').val(lecthr * outlectunit);
        } else if ($('input[name=kind]:checked').val() == '2') {
        	if(lecthr == 0){
        		courseChange();
        		lecthr = parseFloat($('#lecthr').val());
        	}
            $('#lectamt').val(lecthr * burlectunit);
        } else if ($('input[name=kind]:checked').val() == '3') {
        	if(lecthr == 0){
        		courseChange();
        		lecthr = parseFloat($('#lecthr').val());
        	}
            $('#lectamt').val(lecthr * inlectunit);
        } else if ($('input[name=kind]:checked').val() == '4') {
            $('#lecthr').val('0');
            $('#lectamt').val('0');
        }
        // alert($('input[name=kind]:checked').val());
        if($('input[name=type]:checked').val() == '2'){
            $('#lectamt').val($('#lectamt').val() * 0.5);
        }

        // 更新合計
        countPrice();
    }




</script>
@endsection