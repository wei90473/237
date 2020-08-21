@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')
<style>\
input{
    min-width: 60px;
}
.pr-2{
    white-space:nowrap; 
}
label{
    margin-bottom: 0px;
}
</style>
    <?php $_menu = 'funding';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">經費概(結)算維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/funding/class_list" class="text-info">經費概(結)算查詢</a></li>
                        <li class="active">經費概(結)算維護</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {{ Form::model($t07tb, ['method' => 'PUT', 'url' => "/admin/funding/{$t07tb->class}/{$t07tb->term}/{$t07tb->type}"]) }}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">經費概(結)算維護</h3></div>
                    <div class="card-body pt-4">
                        <div style="border: 1px solid #000; padding: 10px;margin-bottom:10px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            期別：{{ $t04tb->term }}<br>
                            分班名稱：{{ $t04tb->t01tb->branchname }}<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            委訓機關：{{ $t04tb->client }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：{{ $t04tb->m09tb->username }}
                        </div>                       
                        <!-- 概算 & 結算 -->
                        <div class="form-group row">
                            <div class="form-group col-md-6">
                                <label class="col-form-label col-md-3">種類：{{ ($t07tb->type == 1) ? '概算' : '結算'}}</label>
                                <label class="col-form-label col-md-3">學員：{{ $t04tb->t13tbs()->count() }} </label>
                                <label class="col-form-label col-md-3">天數：{{ $t04tb->t01tb->day }}</label>
                            </div>                        
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <label class="col-form-label"> 科目：</label>
                                    <select class="custom-select" name="kind" {{ (empty($t07tb->kind)) ? null : 'disabled' }}>
                                    <option value=""></option>
                                    @foreach ($t04tb->t01tb->s06tbs->pluck('accname', 'acccode') as $acccode => $accname)
                                        <option value="{{ $acccode }}" {{ ($acccode === $t07tb->kind) ? 'selected' : null }}>{{ $accname }}</option>
                                    @endforeach
                                    </select>
                                    <!-- {{ Form::select('kind', collect(['' => ''])->merge($t04tb->t01tb->s06tbs->pluck('accname', 'acccode')), null, ['class' => 'custom-select', 'disabled' => (empty($t07tb->kind)) ? null : true]) }} -->
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <label class="col-form-label"> 合計金額：</label>
                                    {{ Form::text('totalamt', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
                                </div>
                            </div>                            
                        </div>
                    </div>

                    <div class="card-header">
                        <div class=" row">
                            <div class="col-6">
                                <h3 class="card-title">鐘點費</h3>
                            </div>
                            <div class="col-6">
                                <h3 class="card-title">住宿費</h3>
                            </div>
                        </div>    
                    </div>


                    <div class="row">
                        <div class="card-body col-6" style="padding: 20px 10% 20px 10%;">
                            <table>
                                <tr>
                                    <td></td>
                                    <td class="text-center textRed"><label>時數</label></td>
                                    <td class="text-center textRed"><label>單價</label></td>
                                    <td class="text-center textRed"><label>金額</label></td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">內聘</label></td>
                                    <td>{{ Form::text('inlecthr', null, ['class' => 'form-control', 'disabled' => ($t07tb->type == 2) ? 'disabled' : null, 'onchange' => "computeAmt('inlecthr', 'inlectunit', 'inlectamt')"]) }}</td>
                                    <td>{{ Form::text('inlectunit', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}</td>
                                    <td>{{ Form::text('inlectamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">總處</label></td>
                                    <td>{{ Form::text('burlecthr', null, ['class' => 'form-control', 'disabled' => ($t07tb->type == 2) ? 'disabled' : null, 'onchange' => "computeAmt('burlecthr', 'burlectunit', 'burlectamt')"]) }}</td>
                                    <td>{{ Form::text('burlectunit', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}</td>
                                    <td>{{ Form::text('burlectamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">外聘</label></td>
                                    <td>{{ Form::text('outlecthr', null, ['class' => 'form-control', 'disabled' => ($t07tb->type == 2) ? 'disabled' : null, 'onchange' => "computeAmt('outlecthr', 'outlectunit', 'outlectamt')"]) }}</td>
                                    <td>{{ Form::text('outlectunit', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}</td>
                                    <td>{{ Form::text('outlectamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">其他</label></td>
                                    <td>{{ Form::text('othlecthr', null, ['class' => 'form-control', 'disabled' => ($t07tb->type == 2) ? 'disabled' : null, 'onchange' => "computeAmt('othlecthr', 'othlectunit', 'othlectamt')"]) }}</td>
                                    <td>{{ Form::text('othlectunit', null, ['class' => 'form-control', 'disabled' => ($t07tb->type == 2) ? 'disabled' : null]) }}</td>
                                    <td>{{ Form::text('othlectamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-body col-6" style="padding: 20px 10% 20px 10%;">
                            <table>
                                <tr>
                                    <td></td>
                                    <td class="text-center textRed"><label>人*天數</label></td>
                                    <td class="text-center textRed"><label>單價</label></td>
                                    <td class="text-center textRed"><label>金額</label></td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">單人房</label></td>
                                    <td>{{ Form::text('sincnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('sincnt', 'sinunit', 'sinamt')"]) }}</td>
                                    <td>{{ Form::text('sinunit', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}</td>
                                    <td>{{ Form::text('sinamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">雙人房</label></td>
                                    <td>{{ Form::text('donecnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('donecnt', 'doneunit', 'doneamt')"]) }}</td>
                                    <td>{{ Form::text('doneunit', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}</td>
                                    <td>{{ Form::text('doneamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">行政套房</label></td>
                                    <td>{{ Form::text('vipcnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('vipcnt', 'vipunit', 'vipamt')"]) }}</td>
                                    <td>{{ Form::text('vipunit', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}</td>
                                    <td>{{ Form::text('vipamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                            </table>
                        </div>                        
                    </div>

                    <div class="card-header">
                        <div class=" row">
                            <div class="col-6">
                                <h3 class="card-title">伙食費(學員總數 + 1位講座 + 班務人員)</h3>
                            </div>
                            <div class="col-6">
                                <h3 class="card-title">交通費</h3>
                            </div>
                        </div>    
                    </div>
                    <div class="row">
                        <div class="card-body col-6"  style="padding: 20px 10% 20px 10%;">
                            <table>
                                <tr>
                                    <td></td>
                                    <td class="text-center textRed"><label>人*天數</label></td>
                                    <td class="text-center textRed"><label>單價</label></td>
                                    <td class="text-center textRed"><label>金額</label></td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">早餐</label></td>
                                    <td>{{ Form::text('meacnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('meacnt', 'meaunit', 'meaamt')"]) }}</td>
                                    <td>{{ Form::text('meaunit', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}</td>
                                    <td>{{ Form::text('meaamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">午餐</label></td>
                                    <td>{{ Form::text('luncnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('luncnt', 'lununit', 'lunamt')"]) }}</td>
                                    <td>{{ Form::text('lununit', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}</td>
                                    <td>{{ Form::text('lunamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">晚餐</label></td>
                                    <td>{{ Form::text('dincnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('dincnt', 'dinunit', 'dinamt')"]) }}</td>
                                    <td>{{ Form::text('dinunit', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}</td>
                                    <td>{{ Form::text('dinamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-body col-6"  style="padding: 20px 10% 20px 10%;">
                            <table>
                                <tr>
                                    <td></td>
                                    <td class="text-center textRed"><label>人次</label></td>
                                    <td class="text-center textRed"><label>金額</label></td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">短程車資</label></td>
                                    <td>
                                        {{ Form::text('motorcnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('motorcnt', 'motorunit', 'motoramt')"]) }}
                                        {{ Form::hidden('motorunit', null) }}
                                    </td>
                                    <td>{{ Form::text('motoramt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">火車</label></td>
                                    <td></td>
                                    <td>{{ Form::text('trainamt', null, ['class' => 'form-control amt']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">飛機高鐵</label></td>
                                    <td></td>
                                    <td>{{ Form::text('planeamt', null, ['class' => 'form-control amt']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">汽車捷運</label></td>
                                    <td></td>
                                    <td>{{ Form::text('mrtamt', null, ['class' => 'form-control amt']) }}</td>
                                </tr> 
                                <tr>
                                    <td class="pr-2"><label class="textBlue">船舶</label></td>
                                    <td></td>
                                    <td>{{ Form::text('shipamt', null, ['class' => 'form-control amt']) }}</td>
                                </tr>                                                                 
                            </table>
                        </div>                        
                    </div>

                    <div class="card-header">
                        <div class=" row">
                            <div class="col-6">
                                <h3 class="card-title"></h3>
                            </div>
                            <div class="col-6">
                                <h3 class="card-title">課程規劃費</h3>
                            </div>
                        </div>    
                    </div>
                    <div class="row">
                        <div class="card-body col-6"  style="padding: 20px 10% 20px 10%;">
                            <table>
                                <tr>
                                    <td class="text-center textRed"><label>稿費</label></td>
                                    <td class="text-center textRed"><label>演講費</label></td>
                                    <td class="text-center textRed"><label>評閱費</label></td>
                                </tr>
                                <tr>
                                    <td>{{ Form::text('noteamt', null, ['class' => 'form-control amt', 'disabled' => true]) }}</td>
                                    <td>{{ Form::text('speakamt', null, ['class' => 'form-control amt', 'disabled' => true]) }}</td>
                                    <td>{{ Form::text('review_total', null, ['class' => 'form-control amt', 'disabled' => true]) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-body col-6"  style="padding: 20px 10% 20px 10%;">
                            <table>
                                <tr>
                                    <td class="text-center textRed"><label>次數</label></td>
                                    <td class="text-center textRed"><label>單價</label></td>
                                    <td class="text-center textRed"><label>金額</label></td>
                                </tr>
                                <tr>
                                    <td>{{ Form::text('drawcnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('drawcnt', 'drawunit', 'drawamt')"]) }}</td>
                                    <td>{{ Form::text('drawunit', null, ['class' => 'form-control', 'onchange' => "computeAmt('drawcnt', 'drawunit', 'drawamt')"]) }}</td>
                                    <td>{{ Form::text('drawamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                            </table>
                        </div>                        
                    </div>

                    <div class="card-header">
                        <div class=" row">
                            <div class="col-6">
                                <h3 class="card-title">教材講義費（1天2門課程)</h3>
                            </div>
                            <div class="col-6">
                                <h3 class="card-title">文具及其他用品費</h3>
                            </div>
                        </div>    
                    </div>

                    <div class="row">
                        <div class="card-body col-6"  style="padding: 20px 10% 20px 10%;">
                            <table>
                                <tr>
                                    
                                    <td class="text-center textRed">
                                        @if ($t07tb->type != 2)
                                            <label>人份</label>
                                        @endif 
                                    </td>
                                    <td class="text-center textRed"><label>金額</label></td>
                                </tr>
                                <tr>
                                    <td>
                                        @if ($t07tb->type != 2)
                                        {{ Form::text('doccnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('doccnt', 'docunit', 'docamt')"]) }}
                                        {{ Form::hidden('docunit') }}
                                        @endif 
                                    </td>
                                    <td>{{ Form::text('docamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-body col-6"  style="padding: 20px 10% 20px 10%;">
                            <table>
                                <tr>
                                    <td class="text-center textRed"><label>人份</label></td>
                                    <td class="text-center textRed"><label>金額</label></td>
                                </tr>
                                <tr>
                                    <td>
                                        {{ Form::text('pencnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('pencnt', 'penunit', 'penamt')"]) }}
                                        {{ Form::hidden('penunit') }}
                                    </td>
                                    <td>{{ Form::text('penamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                            </table>
                        </div>                        
                    </div>

                    <div class="card-header">
                        <div class=" row">
                            <div class="col-6">
                                <h3 class="card-title">場地租借費</h3>
                            </div>
                            <div class="col-6">
                                <h3 class="card-title">院外教學</h3>
                            </div>
                        </div>    
                    </div>
                    <div class="row">
                        <div class="card-body col-6"  style="padding: 20px 10% 20px 10%;">
                            <table>
                                <tr>
                                    <td class="text-center textRed"><label>次數</label></td>
                                    <td class="text-center textRed"><label>單價</label></td>
                                    <td class="text-center textRed"><label>金額</label></td>
                                </tr>
                                <tr>
                                    <td>{{ Form::text('placecnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('placecnt', 'placeunit', 'placeamt')"]) }}</td>
                                    <td>{{ Form::text('placeunit', null, ['class' => 'form-control', 'onchange' => "computeAmt('placecnt', 'placeunit', 'placeamt')"]) }}</td>
                                    <td>{{ Form::text('placeamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-body col-6"  style="padding: 20px 10% 20px 10%;">
                            {{ Form::radio('daytype', 1 ,null, ['onchange' => 'computeOutSide()']) }} 1天
                            {{ Form::radio('daytype', 2 ,null, ['onchange' => 'computeOutSide()']) }} 2天
                            <table>
                                <tr>
                                    <td></td>
                                    <td class="text-center textRed"><label>人數</label></td>
                                    <td class="text-center textRed"><label>金額</label></td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">保險費</label></td>
                                    <td>
                                        {{ Form::text('inscnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('inscnt', 'insunit', 'insamt')"]) }}
                                        {{ Form::hidden('insunit', null) }}
                                    </td>
                                    <td>{{ Form::text('insamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">活動費</label></td>
                                    <td>
                                        {{ Form::text('actcnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('actcnt', 'actunit', 'actamt')"]) }}
                                        {{ Form::hidden('actunit', null, ['class' => 'form-control']) }}
                                    </td>
                                    <td>{{ Form::text('actamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">租車費</label></td>
                                    <td></td>
                                    <td>{{ Form::text('caramt', null, ['class' => 'form-control amt']) }}</td>
                                </tr>
                            </table>
                        </div>                        
                    </div>

                    <div class="card-header">
                        <div class=" row">
                            <div class="col-6">
                                <h3 class="card-title">其他雜支</h3>
                            </div>
                            <div class="col-6">
                                <h3 class="card-title"></h3>
                            </div>
                        </div>    
                    </div>
                    <div class="row">
                        <div class="card-body col-6"  style="padding: 20px 10% 20px 10%;">
                            <table>
                                <tr>
                                    <td></td>
                                    <td class="text-center textRed"><label>數量</label></td>
                                    <td class="text-center textRed"><label>金額</label></td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">茶點費</label></td>
                                    <td>
                                        {{ Form::text('teacnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('teacnt', 'teaunit', 'teaamt')"]) }}
                                        {{ Form::hidden('teaunit', null) }}
                                    </td>
                                    <td>{{ Form::text('teaamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">獎品費</label></td>
                                    <td>
                                        {{ Form::text('prizecnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('prizecnt', 'prizeunit', 'prizeamt')"]) }}
                                        {{ Form::hidden('prizeunit', null) }}
                                    </td>
                                    <td>{{ Form::text('prizeamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">慶生活動</label></td>
                                    <td>
                                        {{ Form::text('birthcnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('birthcnt', 'birthunit', 'birthamt')"]) }}
                                        {{ Form::hidden('birthunit', null) }}
                                    </td>
                                    <td>{{ Form::text('birthamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">聯誼活動</label></td>
                                    <td>
                                        {{ Form::text('unioncnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('unioncnt', 'unionunit', 'unionamt')"]) }}
                                        {{ Form::hidden('unionunit', null) }}
                                    </td>
                                    <td>{{ Form::text('unionamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">場地布置</label></td>
                                    <td>
                                        {{ Form::text('setcnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('setcnt', 'setunit', 'setamt')"]) }}
                                        {{ Form::hidden('setunit', null) }}
                                    </td>
                                    <td>{{ Form::text('setamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-body col-6"  style="padding: 20px 10% 20px 10%;">
                            <table>
                                <tr>
                                    <td></td>
                                    <td class="text-center textRed"><label>數量</label></td>
                                    <td class="text-center textRed"><label>金額</label></td>
                                </tr>                            
                                <tr>
                                    <td class="pr-2"><label class="textBlue">加菜金</label></td>
                                    <td>
                                        {{ Form::text('dishcnt', null, ['class' => 'form-control', 'onchange' => "computeAmt('dishcnt', 'dishunit', 'dishamt')"]) }}
                                        {{ Form::hidden('dishunit', null) }}
                                    </td>
                                    <td>{{ Form::text('dishamt', null, ['class' => 'form-control amt', 'disabled' => 'disabled']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">其他一</label></td>
                                    <td></td>
                                    <td>{{ Form::text('otheramt1', null, ['class' => 'form-control amt']) }}</td>
                                </tr>
                                <tr>
                                    <td class="pr-2"><label class="textBlue">其他二</label></td>
                                    <td></td>
                                    <td>{{ Form::text('otheramt2', null, ['class' => 'form-control amt']) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="/admin/funding">
                            <button type="submit"  class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteFunding()"><i class="fa fa-trash pr-2"></i>刪除</button>
                        <a href="/admin/funding/class_list">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>

            

        </div>
    </div>
    {!! Form::close() !!} 
    {!! Form::open(['method' => 'delete', 'url' => "/admin/funding/{$t07tb->class}/{$t07tb->term}/{$t07tb->type}", 'id' => 'deleteForm']) !!}

    {!! Form::close() !!}
        
    
    
    <!-- 圖片 -->
    <!-- include('admin/layouts/form/image') -->
<script type="text/javascript">
    function deleteFunding()
    {
        if (confirm('確定要刪除嗎?')){
            $('#deleteForm').submit();
        }
    }  

    function computeAmt(cnt_name, unit_name, amt_name)
    {
        var cnt = $('input[name=' + cnt_name + ']').val();
        var unit = $('input[name=' + unit_name + ']').val();
        var outSideDay = $('input[name=daytype]:checked').val();
        var outSiteCnt = ['inscnt', 'actcnt', 'caramt'];

        var amt = cnt * unit;
        console.log(outSiteCnt.indexOf(cnt_name));
        if (outSiteCnt.indexOf(cnt_name) != -1){
            if (outSideDay == undefined){
                outSideDay = 0;
            }

            amt = amt * outSideDay;
        }

        $('input[name=' + amt_name + ']').val(amt);
        computeTotal();
    }  
    // 計算院外教學
    function computeOutSide()
    {
        computeAmt('inscnt', 'insunit', 'insamt');
        computeAmt('actcnt', 'actunit', 'actamt');
        var outSideDay = $('input[name=daytype]:checked').val();
        if (outSideDay == undefined){
            outSideDay = 0;
        }       

        if (outSideDay == 1){
            outSideDay = 0.5;
        }else{
            outSideDay = 2;
        }

        $('input[name=caramt]').val($('input[name=caramt]').val() * outSideDay);
        computeTotal();
    }

    function computeTotal()
    {
        var amts = $('.amt');
        var total = 0;
        for(var i=0; i<amts.length; i++)
        {
            if (amts[i].value !== undefined){
                total += parseInt(amts[i].value);
            }
        }
        $("input[name=totalamt]").val(total);
        console.log(total);
    }
</script>

@endsection