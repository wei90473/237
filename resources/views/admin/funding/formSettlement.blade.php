@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'funding';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">經費概(結)算維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/funding" class="text-info">經費概(結)算查詢</a></li>
                        <li class="active">經費概(結)算維護</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {{-- @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/holiday/'.$data->date, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/holiday/', 'id'=>'form']) !!}
            @endif --}}

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">經費概(結)算維護</h3></div>
                    <div class="card-body pt-4">
                        
                        <!-- 概算 & 結算 -->
                        <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2"></label>
                                <div class="col-sm-10">
                                    <input type="radio" name="statistical" value="estimate" checked="checked" disabled>概算
                                    <input type="radio" name="statistical" value="settlement" disabled>結算
    
                                </div>
                        </div>

                        <!-- 班別-->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班別</label>
                            <div class="col-sm-10">
                                <select class="select2 form-control select2-single input-max" disabled>
                                    <option value='0'>無</option>
                                    <option value='1'>1</option>
                                    <!--dropDown-->
                                </select>
                            </div>
                        </div>
                        
                        <!-- 科目-->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">科目</label>
                            <div class="col-sm-10">
                                <select class="select2 form-control select2-single input-max" disabled="disabled">
                                    <option value='0'>無</option>
                                    <option value='1'>1</option>
                                    <!--dropDown-->
                                </select>
                            </div>
                        </div>

                         <!-- 期別-->
                         <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">科目</label>
                            <div class="col-sm-10">
                                <select class="select2 form-control select2-single input-max" disabled="disabled">
                                    <option value='0'>無</option>
                                    <option value='1'>1</option>
                                    <!--dropDown-->
                                </select>
                            </div>
                        </div>

                         <!-- 學員 -->
                         <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">學員</label>
                            <div class="col-sm-10">
                                {{-- 預設值為123 --}}
                                <input class="form-control input-max" type="text" disabled="disabled" value="123" >
                            </div>
                        </div>

                        
                        
                        <!-- 天數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">天數</label>
                            <div class="col-sm-10">
                                    {{-- 預設值為123 --}}
                                <input class="form-control input-max" type="text" disabled="disabled" value="123">
                            </div>
                        </div>
                    </div>


                    <div class="card-header"><h3 class="card-title">鐘點費</h3></div>
                    <div class="card-body mx-auto">

                        <table>
                            <tr>
                                <td></td>
                                <td class="text-center textRed"><label>時數</label></td>
                                <td class="text-center textRed"><label>單價</label></td>
                                <td class="text-center textRed"><label>金額</label></td>
                            </tr>
                            <tr>
                                <td class="pr-2"><label class="textBlue">內聘</label></td>
                                <td><input type="text" class="form-control"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                            </tr>
                            <tr>
                                <td class="pr-2"><label class="textBlue">總處</label></td>
                                <td><input type="text" class="form-control"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                            </tr>
                            <tr>
                                <td class="pr-2"><label class="textBlue">外聘</label></td>
                                <td><input type="text" class="form-control"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                            </tr>
                            <tr>
                                <td class="pr-2"><label class="textBlue">其他</label></td>
                                <td><input type="text" class="form-control"></td>
                                <td><input type="text" class="form-control"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                            </tr>
                        </table>
                    </div>

                    <div class="card-header"><h3 class="card-title">住宿費</h3></div>
                    <div class="card-body mx-auto">

                        <table>
                            <tr>
                                <td></td>
                                <td class="text-center textRed"><label>人*天數</label></td>
                                <td class="text-center textRed"><label>單價</label></td>
                                <td class="text-center textRed"><label>金額</label></td>
                            </tr>
                            <tr>
                                <td class="pr-2"><label class="textBlue">單人房</label></td>
                                <td><input type="text" class="form-control"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                            </tr>
                            <tr>
                                <td class="pr-2"><label class="textBlue">雙人房</label></td>
                                <td><input type="text" class="form-control"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                            </tr>
                            <tr>
                                <td class="pr-2"><label class="textBlue">行政套房</label></td>
                                <td><input type="text" class="form-control"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                            </tr>
                        </table>
                    </div>

                    <div class="card-header">
                        <h3 class="card-title">伙食費(學員總數+1為講座+1班務人員)</h3>
                    </div>
                    <div class="card-body mx-auto">

                        <table>
                            <tr>
                                <td></td>
                                <td class="text-center textRed"><label>人*天數</label></td>
                                <td class="text-center textRed"><label>單價</label></td>
                                <td class="text-center textRed"><label>金額</label></td>
                            </tr>
                            <tr>
                                <td class="pr-2"><label class="textBlue">早餐</label></td>
                                <td><input type="text" class="form-control"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                            </tr>
                            <tr>
                                <td class="pr-2"><label class="textBlue">午餐</label></td>
                                <td><input type="text" class="form-control"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                            </tr>
                            <tr>
                                <td class="pr-2"><label class="textBlue">晚餐</label></td>
                                <td><input type="text" class="form-control"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                                <td><input type="text" class="form-control" disabled="disabled"></td>
                            </tr>
                        </table>
                    </div>

                    <div class="card-header">
                        <h3 class="card-title">交通費</h3>
                    </div>
                        <div class="card-body mx-auto">
                            <table>
                                <tr>
                                    <td></td>
                                    <td class="text-center textRed"><label>人次</label></td>
                                    <td class="text-center textRed"><label>金額</label></td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">汽車</label></td>
                                    <td><input type="text" class="form-control"></td>
                                    <td><input type="text" class="form-control" disabled="disabled"></td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">火車</label></td>
                                    <td><input type="text" class="form-control"></td>
                                    <td><input type="text" class="form-control" disabled="disabled"></td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">飛機</label></td>
                                    <td><input type="text" class="form-control"></td>
                                    <td><input type="text" class="form-control" disabled="disabled"></td>
                                </tr>
                            </table>
                        </div>


                        <div class="card-header"></div>
                            <div class="card-body mx-auto">
                                <table>
                                    <tr>
                                        <td class="text-center textRed"><label>稿費</label></td>
                                        <td class="text-center textRed"><label>演講費</label></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" class="form-control"></td>
                                        <td><input type="text" class="form-control"></td>
                                    </tr>
                                </table>
                            </div>

                        
                        <div class="card-header">
                            <h3 class="card-title">課程規劃費</h3>
                        </div>
                            <div class="card-body mx-auto">
                                <table>
                                    <tr>
                                        <td class="text-center textRed"><label>次數</label></td>
                                        <td class="text-center textRed"><label>單價</label></td>
                                        <td class="text-center textRed"><label>金額</label></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" class="form-control"></td>
                                        <td><input type="text" class="form-control"></td>
                                        <td><input type="text" class="form-control"></td>
                                    </tr>
                                </table>
                            </div>

                        <div class="card-header">
                            <h3 class="card-title">教材講義費（1天2門課程）</h3>
                        </div>
                            <div class="card-body mx-auto">
                               <table>
                                    <tr>
                                        <td class="text-center textRed"><label>金額</label></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" class="form-control"></td>
                                    </tr>
                                </table>
                            </div>

                        <div class="card-header">
                            <h3 class="card-title">文具及其他用品費</h3>
                        </div>
                            <div class="card-body mx-auto">
                               <table>
                                    <tr>
                                        <td class="text-center textRed"><label>人份</label></td>
                                        <td class="text-center textRed"><label>金額</label></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" class="form-control"></td>
                                        <td><input type="text" class="form-control"></td>
                                    </tr>
                                </table>
                            </div>
                        
                        <div class="card-header">
                            <h3 class="card-title">場地租借費</h3>
                        </div>
                            <div class="card-body mx-auto">
                               <table>
                                    <tr>
                                        <td class="text-center textRed"><label>次數</label></td>
                                        <td class="text-center textRed"><label>單價</label></td>
                                        <td class="text-center textRed"><label>金額</label></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" class="form-control"></td>
                                        <td><input type="text" class="form-control"></td>
                                        <td><input type="text" class="form-control" disabled="disabled"></td>
                                    </tr>
                                </table>
                            </div>

                        <div class="card-header">
                            <h3 class="card-title">院外教學</h3>
                        </div>
                            <div class="card-body mx-auto">
                                <input type="radio" name="numberOfDays" value="1" checked="checked">1天
                                <input type="radio" name="numberOfDays" value="2">2天
                               <table>
                                    <tr>
                                        <td></td>
                                        <td class="text-center textRed"><label>人數</label></td>
                                        <td class="text-center textRed"><label>金額</label></td>
                                    </tr>
                                    <tr>
                                        <td class="pr-2"><label class="textBlue">保險費</label></td>
                                        <td><input type="text" class="form-control"></td>
                                        <td><input type="text" class="form-control" disabled="disabled"></td>
                                    </tr>
                                    <tr>
                                        <td class="pr-2"><label class="textBlue">活動費</label></td>
                                        <td><input type="text" class="form-control" ></td>
                                        <td><input type="text" class="form-control" disabled="disabled"></td>
                                    </tr>
                                    <tr>
                                        <td class="pr-2"><label class="textBlue">租車費</label></td>
                                        <td></td>
                                        <td><input type="text" class="form-control"></td>
                                    </tr>
                                </table>
                            </div>

                        <div class="card-header">
                            <h3 class="card-title">其他雜支</h3>
                        </div>
                            <div class="card-body mx-auto">
                               <table>
                                    <tr>
                                        <td></td>
                                        <td class="text-center textRed"><label>數量</label></td>
                                        <td class="text-center textRed"><label>金額</label></td>
                                    </tr>
                                    <tr>
                                        <td class="pr-2"><label class="textBlue">茶品費</label></td>
                                        <td><input type="text" class="form-control"></td>
                                        <td><input type="text" class="form-control" disabled="disabled"></td>
                                    </tr>
                                    <tr>
                                        <td class="pr-2"><label class="textBlue">獎品費</label></td>
                                        <td><input type="text" class="form-control" ></td>
                                        <td><input type="text" class="form-control" disabled="disabled"></td>
                                    </tr>
                                    <tr>
                                        <td class="pr-2"><label class="textBlue">慶生活動</label></td>
                                        <td><input type="text" class="form-control"></td>
                                        <td><input type="text" class="form-control" disabled="disabled"></td>
                                    </tr>
                                    <tr>
                                        <td class="pr-2"><label class="textBlue">聯誼活動</label></td>
                                        <td><input type="text" class="form-control"></td>
                                        <td><input type="text" class="form-control" disabled="disabled"></td>
                                    </tr>
                                    <tr>
                                        <td class="pr-2"><label class="textBlue">場地布置</label></td>
                                        <td><input type="text" class="form-control"></td>
                                        <td><input type="text" class="form-control" disabled="disabled"></td>
                                    </tr>
                                    <tr>
                                        <td class="pr-2"><label class="textBlue">加菜金</label></td>
                                        <td><input type="text" class="form-control"></td>
                                        <td><input type="text" class="form-control" disabled="disabled"></td>
                                    </tr>
                                    <tr>
                                        <td class="pr-2"><label class="textBlue">其他一</label></td>
                                        <td></td>
                                        <td><input type="text" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td class="pr-2"><label class="textBlue">其他二</label></td>
                                        <td></td>
                                        <td><input type="text" class="form-control"></td>
                                    </tr>
                                </table>
                            </div>

                        <div class="card-header">
                            <h3 class="card-title">合計</h3>
                        </div>
                            <div class="card-body mx-auto">
                               <table>
                                    <tr>
                                        <td class="text-center textRed"><label>金額</label></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" class="form-control" disabled="disabled"></td>
                                    </tr>
                                </table>
                            </div>

                    <div class="card-footer">
                        <a href="/admin/funding">
                        <!--onclick="submitForm('#form');"-->
                        <button type="button"  class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        </a>
                        <a href="/admin/funding">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>

            {{-- {!! Form::close() !!} --}}

        </div>
    </div>


    
    <!-- 圖片 -->
    @include('admin/layouts/form/image')

@endsection