@extends('admin.layouts.layouts')
@section('content')
<style>
    .class_label{
        width:120px;
        text-align:center;
    }
</style>
    <?php $_menu = 'place_nantou';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地資料(南投)</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">場地資料(南投)列表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>場地資料(南投)</h3>
                        </div>

                        <div class="card-body">
                            <div style="padding:20px;">
                                


                                {{ Form::open(['method' => 'post', 'url' => "/admin/place_nantou_classroomcls/feeSetting/{$type}/{$croomclsno}"]) }}
                                
                                @if ($classroomcls->classroom == 1)
                                  @if ($type == 'weekdays')
                                  <div class="form-group row">
                                    <label for="colFormLabelSm" class="col-form-label class_label">計費方式</label>
                                    <div class="col-2">
                                     {{ Form::select('feetype', [1 => '每間教室-時段費用', 2 => '每間電腦教室-每小時費用'], $feetype,['class' => "form-control custom-select" , 'onchange' => 'feetypeChange(this.value)']) }}
                                    </div>                                    
                                  </div> 
                                  
                                  <div class="form-group row feetype1" {{ ($feetype == 2) ? 'style=display:none' : null }}>
                                    <label for="colFormLabelSm" class="col-form-label class_label">半日</label>
                                    <div class="col-2">
                                     {{ Form::text('fee[201]', (isset($fees[201])) ? $fees[201]->fee : null , ['class' => "form-control"]) }}
                                    </div>                                    
                                  </div> 

                                  <div class="form-group row feetype1" {{ ($feetype == 2) ? 'style=display:none' : null }}>
                                    <label for="colFormLabelSm" class="col-form-label class_label">全日</label>
                                    <div class="col-2">
                                     {{ Form::text('fee[202]', (isset($fees[202])) ? $fees[202]->fee : null, ['class' => "form-control"]) }}
                                    </div>                                    
                                  </div> 

                                  <div class="form-group row feetype2" {{ ($feetype == 1) ? 'style=display:none' : null }}>
                                    <label for="colFormLabelSm" class="col-form-label class_label">每小時</label>
                                    <div class="col-2">
                                     {{ Form::text('fee[203]', (isset($fees[202]) && ($feetype == 2)) ? $fees[202]->fee : null, ['class' => "form-control"]) }}
                                    </div>                                    
                                  </div> 
                                  @elseif ($type == 'holiday')

                                  <div class="form-group row">
                                    <label for="colFormLabelSm" class="col-form-label class_label">計費方式</label>
                                    <div class="col-2">
                                     {{ Form::select('feetype', [1 => '每間教室-時段費用', 2 => '每間電腦教室-每小時費用'], $feetype,['class' => "form-control custom-select" , 'onchange' => 'feetypeChange(this.value)']) }}
                                    </div>                                    
                                  </div> 
                                  
                                  <div class="form-group row feetype1" {{ ($feetype == 2) ? 'style=display:none' : null }}>
                                    <label for="colFormLabelSm" class="col-form-label class_label">半日</label>
                                    <div class="col-2">
                                     {{ Form::text('fee[201]', (isset($fees[201])) ? $fees[201]->holidayfee : null , ['class' => "form-control"]) }}
                                    </div>                                    
                                  </div> 

                                  <div class="form-group row feetype1" {{ ($feetype == 2) ? 'style=display:none' : null }}>
                                    <label for="colFormLabelSm" class="col-form-label class_label">全日</label>
                                    <div class="col-2">
                                     {{ Form::text('fee[202]', (isset($fees[202])) ? $fees[202]->holidayfee : null, ['class' => "form-control"]) }}
                                    </div>                                    
                                  </div> 

                                  <div class="form-group row feetype2" {{ ($feetype == 1) ? 'style=display:none' : null }}>
                                    <label for="colFormLabelSm" class="col-form-label class_label">每小時</label>
                                    <div class="col-2">
                                     {{ Form::text('fee[203]', (isset($fees[202]) && ($feetype == 2)) ? $fees[202]->holidayfee : null, ['class' => "form-control"]) }}
                                    </div>                                    
                                  </div> 
                                  @elseif ($type == 'time')
                                  <div class="form-group row feetype2">
                                    <div class="col-6">
                                      <table class="table table-bordered mb-0">
                                        <thead>
                                          <th class="text-center">全選<input type="checkbox" name=""></th>
                                          <th class="text-center">時段資訊</th>
                                          <th class="text-center">平日費用</th>
                                          <th class="text-center">假日費用</th>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <td class="text-center"><input type="checkbox" name="" {{ (isset($fees[401])) ? 'checked' : null }}></td>
                                            <td class="text-center">上午(0800~1200)</td>
                                            <td class="text-center"><input type="text" name="fee[401][fee]" class="form-control" value="{{ (isset($fees[401]) ? $fees[401]->fee : null) }}"></td>
                                            <td class="text-center"><input type="text" name="fee[401][holidayfee]" class="form-control" value="{{ (isset($fees[401]) ? $fees[401]->holidayfee : null) }}"></td>
                                          </tr>
                                          <tr>
                                            <td class="text-center"><input type="checkbox" name="" {{ (isset($fees[402])) ? 'checked' : null }}></td>
                                            <td class="text-center">下午(1300~1700)</td>
                                            <td class="text-center"><input type="text" name="fee[402][fee]" class="form-control" value="{{ (isset($fees[401]) ? $fees[402]->fee : null) }}"></td>
                                            <td class="text-center"><input type="text" name="fee[402][holidayfee]" class="form-control" value="{{ (isset($fees[401]) ? $fees[402]->holidayfee : null) }}"></td>
                                          </tr>
                                          <tr>
                                            <td class="text-center"><input type="checkbox" name="" {{ (isset($fees[403])) ? 'checked' : null }}></td>
                                            <td class="text-center">晚上(1800~2200)</td>
                                            <td class="text-center"><input type="text" name="fee[403][fee]" class="form-control" value="{{ (isset($fees[401]) ? $fees[403]->fee : null) }}"></td>
                                            <td class="text-center"><input type="text" name="fee[403][holidayfee]" class="form-control" value="{{ (isset($fees[401]) ? $fees[403]->holidayfee : null) }}"></td>
                                          </tr>                                                                                
                                        </tbody>
                                      </table>
                                    </div>                                    
                                  </div>                                 

                                  @endif               
                                @else
                                  <div class="form-group row">
                                    <label for="colFormLabelSm" class="col-form-label class_label">計費方式</label>
                                    <div class="col-2">
                                     {{ Form::select('feetype', [3 => '每人每日', 5 => '每間每日'], $feetype,['class' => "form-control custom-select" , 'onchange' => 'feetypeChange(this.value)']) }}
                                    </div>                                    
                                  </div> 
                                  
                                  <div class="form-group row feetype1" {{ ($feetype == 2) ? 'style=display:none' : null }}>
                                    <label for="colFormLabelSm" class="col-form-label class_label">半日</label>
                                    <div class="col-2">
                                     {{ Form::text('fee', (isset($fees)) ? $fees->fee : null , ['class' => "form-control"]) }}
                                    </div>                                    
                                  </div> 

                                  <div class="form-group row feetype1" {{ ($feetype == 2) ? 'style=display:none' : null }}>
                                    <label for="colFormLabelSm" class="col-form-label class_label">全日</label>
                                    <div class="col-2">
                                     {{ Form::text('holidayfee', (isset($fees)) ? $fees->holidayfee : null, ['class' => "form-control"]) }}
                                    </div>                                    
                                  </div> 
                                @endif 
                                <div class="form-group row">
                                    <div class="col-12">
                                        <button class="btn btn-primary">儲存</button>
                                        <a href='/admin/place_nantou_classroomcls'><button type="button" class="btn btn-danger">取消</button></a>
                                    </div>
                                </div>
                                {{ Form::close() }}

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('js')
<script type="text/javascript">

  function feetypeChange(type){
    if (type == 1){
      $(".feetype1").css('display', '');
      $(".feetype2").css('display', 'none'); 
    }else if (type == 2){
      $(".feetype1").css('display', 'none');
      $(".feetype2").css('display', '');     
    }
  }


</script>
@endsection