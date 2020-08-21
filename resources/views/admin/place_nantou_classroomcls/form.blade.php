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
                                @if ($action == 'create')
                                    {{ Form::open(['method' => 'post', 'url' => "/admin/place_nantou_classroomcls"]) }}
                                @elseif ($action == "edit")
                                    {{ Form::model($classroomcls, ['method' => 'put', 'url' => "/admin/place_nantou_classroomcls/{$classroomcls->croomclsno}"]) }}
                                @endif 
                                
                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">場地代碼</label>
                                  <div class="col-2">
                                   {{ Form::text('croomclsno', null, ['class' => "form-control", 'disabled' => ($action == 'edit') ? true : null]) }}
                                  </div>

                                  <label for="colFormLabelSm" class="col-form-label class_label">場地簡稱</label>
                                  <div class="col-2">
                                   {{ Form::text('croomclsname', null, ['class' => "form-control"]) }}
                                  </div>                                     
                                </div>

                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">場地全名</label>
                                  <div class="col-2">
                                   {{ Form::text('croomclsfullname', null, ['class' => "form-control"]) }}
                                  </div>

                                  <label for="colFormLabelSm" class="col-form-label class_label">開放外借</label>
                                  <div class="col-1">
                                   {{ Form::select('borrow', [ 0 => '否', 1 => '是'], null, ['class' => "form-control custom-select"]) }}
                                  </div>                                     
                                </div>

                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">場地型態</label>
                                  <div class="col-1">
                                    {{ Form::select('classroom', [ 0 => '寢室', 1 => '教室'], null, ['class' => "form-control custom-select"]) }}
                                  </div>                                 
                                </div>

                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">場地使用說明</label>
                                  <div class="col-5">
                                   {{ Form::textarea('description', null, ['class' => "form-control", 'style' => "height:75px;"]) }}
                                  </div>                                    
                                </div>  

                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">場地簡介URL</label>
                                  <div class="col-5">
                                   {{ Form::text('link', null, ['class' => "form-control"]) }}
                                  </div>                                    
                                </div>  
                                @if ($action == "edit")
                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">平日計費</label>
                                  <div class="col-5">
                                      @if ($classroomcls->classroom == 1)
                                        @if (isset($classroomcls->fees[201]))
                                        <div>
                                          <a href="/admin/place_nantou_classroomcls/feeSetting/weekdays/{{ $classroomcls->croomclsno }}">
                                          {{ config('database_fields.edu_classroomcls')[$classroomcls->fees[201]->timetype].' '.$classroomcls->fees[201]->fee.' 元' }}
                                          @if (isset($classroomcls->fees[201]->feetypeCode))
                                          {{ ' / '.$classroomcls->fees[201]->feetypeCode->name }}
                                          @endif                                         
                                          </a>
                                        </div>
                                        @endif 

                                        @if (isset($classroomcls->fees[202]))
                                        <div>
                                          <a href="/admin/place_nantou_classroomcls/feeSetting/weekdays/{{ $classroomcls->croomclsno }}">
                                          {{ config('database_fields.edu_classroomcls')[$classroomcls->fees[202]->timetype].' '.$classroomcls->fees[202]->fee.' 元' }}
                                          @if (isset($classroomcls->fees[202]->feetypeCode))
                                          {{ ' / '.$classroomcls->fees[202]->feetypeCode->name }}
                                          @endif                                         
                                          </a>
                                        </div>  
                                        @endif 

                                        @if (!isset($classroomcls->fees[202]) && !isset($classroomcls->fees[201]))                               
                                          <a href="/admin/place_nantou_classroomcls/feeSetting/weekdays/{{ $classroomcls->croomclsno }}">未設定</a>
                                        @endif                                        
                                      @elseif ($classroomcls->classroom == 0)
                                        @if (isset($classroomcls->fees))
                                        <div>
                                          <a href="/admin/place_nantou_classroomcls/feeSetting/weekdays/{{ $classroomcls->croomclsno }}">
                                          {{ config('database_fields.edu_classroomcls')[$classroomcls->fees->timetype].' '.$classroomcls->fees->fee.' 元' }}
                                          @if (isset($classroomcls->fees->feetypeCode))
                                          {{ ' / '.$classroomcls->fees->feetypeCode->name }}
                                          @endif                                         
                                          </a>
                                        </div>
                                        @else
                                        <a href="/admin/place_nantou_classroomcls/feeSetting/holiday/{{ $classroomcls->croomclsno }}">未設定</a>  
                                        @endif 
                                      @endif 

 

                                  </div>                                    
                                </div>

                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">假日計費</label>
                                  <div class="col-5">
                                    @if ($classroomcls->classroom == 1)
                                      @if (isset($classroomcls->fees[201]))
                                      <div>
                                        <a href="/admin/place_nantou_classroomcls/feeSetting/holiday/{{ $classroomcls->croomclsno }}">
                                        {{ config('database_fields.edu_classroomcls')[$classroomcls->fees[201]->timetype].' '.$classroomcls->fees[201]->holidayfee.' 元' }}
                                        @if (isset($classroomcls->fees[201]->feetypeCode))
                                        {{ ' / '.$classroomcls->fees[201]->feetypeCode->name }}
                                        @endif                                         
                                        </a>
                                      </div> 
                                      @endif 

                                      @if (isset($classroomcls->fees[202]))
                                      <div>
                                        <a href="/admin/place_nantou_classroomcls/feeSetting/holiday/{{ $classroomcls->croomclsno }}">
                                        {{ config('database_fields.edu_classroomcls')[$classroomcls->fees[202]->timetype].' '.$classroomcls->fees[202]->holidayfee.' 元' }}
                                        @if (isset($classroomcls->fees[202]->feetypeCode))
                                        {{ ' / '.$classroomcls->fees[202]->feetypeCode->name }}
                                        @endif                                         
                                        </a>
                                      </div> 
                                      @endif 

                                      @if (!isset($classroomcls->fees[202]) && !isset($classroomcls->fees[201]))                               
                                        <a href="/admin/place_nantou_classroomcls/feeSetting/holiday/{{ $classroomcls->croomclsno }}">未設定</a>
                                      @endif       

                                    @elseif ($classroomcls->classroom == 0)
                                      @if (isset($classroomcls->fees))
                                        <div>
                                          <a href="/admin/place_nantou_classroomcls/feeSetting/weekdays/{{ $classroomcls->croomclsno }}">
                                          {{ config('database_fields.edu_classroomcls')[$classroomcls->fees->timetype].' '.$classroomcls->fees->fee.' 元' }}
                                          @if (isset($classroomcls->fees->feetypeCode))
                                          {{ ' / '.$classroomcls->fees->feetypeCode->name }}
                                          @endif                                         
                                          </a>
                                        </div>
                                      @else
                                        <a href="/admin/place_nantou_classroomcls/feeSetting/holiday/{{ $classroomcls->croomclsno }}">未設定</a>
                                      @endif 

                                    @endif 


                                  </div>                                    
                                </div>
                                  @if ($classroomcls->classroom == 1)
                                  <div class="form-group row">
                                    <label for="colFormLabelSm" class="col-form-label class_label">平日計費(新)</label>
                                    <div class="col-5">
                                        
                                        @if (isset($classroomcls->fees[401]))
                                        <a href="/admin/place_nantou_classroomcls/feeSetting/time/{{ $classroomcls->croomclsno }}">
                                          {{ config('database_fields.edu_classroomcls')[$classroomcls->fees[401]->timetype].' '.$classroomcls->fees[401]->fee.' 元' }}
                                          @if (isset($classroomcls->fees[401]->feetypeCode))
                                          {{ ' / '.$classroomcls->fees[401]->feetypeCode->name }}
                                          @endif
                                          <br>
                                        </a>
                                        @endif 

                                        @if (isset($classroomcls->fees[402]))
                                        <a href="/admin/place_nantou_classroomcls/feeSetting/time/{{ $classroomcls->croomclsno }}">
                                          {{ config('database_fields.edu_classroomcls')[$classroomcls->fees[402]->timetype].' '.$classroomcls->fees[402]->fee.' 元' }}
                                          @if (isset($classroomcls->fees[402]->feetypeCode))
                                          {{ ' / '.$classroomcls->fees[402]->feetypeCode->name }}
                                          @endif
                                          <br>
                                        </a>
                                        @endif 

                                        @if (isset($classroomcls->fees[403]))
                                        <a href="/admin/place_nantou_classroomcls/feeSetting/time/{{ $classroomcls->croomclsno }}">
                                          {{ config('database_fields.edu_classroomcls')[$classroomcls->fees[403]->timetype].' '.$classroomcls->fees[403]->fee.' 元' }}
                                          @if (isset($classroomcls->fees[403]->feetypeCode))
                                          {{ ' / '.$classroomcls->fees[403]->feetypeCode->name }}
                                          @endif
                                          <br>
                                        </a>
                                        @endif 

                                        @if (!isset($classroomcls->fees[401]) && !isset($classroomcls->fees[402]) && !isset($classroomcls->fees[403]))                               
                                          <a href="/admin/place_nantou_classroomcls/feeSetting/time/{{ $classroomcls->croomclsno }}">未設定</a>
                                        @endif                                         
                                    </div>                                    
                                  </div>

                                  <div class="form-group row">
                                    <label for="colFormLabelSm" class="col-form-label class_label">假日計費(新)</label>
                                    <div class="col-5">
                                        @if (isset($classroomcls->fees[401]))
                                        <a href="/admin/place_nantou_classroomcls/feeSetting/time/{{ $classroomcls->croomclsno }}">
                                          {{ config('database_fields.edu_classroomcls')[$classroomcls->fees[401]->timetype].' '.$classroomcls->fees[401]->holidayfee.' 元' }}
                                          @if (isset($classroomcls->fees[401]->feetypeCode))
                                          {{ ' / '.$classroomcls->fees[401]->feetypeCode->name }}
                                          @endif
                                          <br>
                                        </a>
                                        @endif 

                                        @if (isset($classroomcls->fees[402]))
                                        <a href="/admin/place_nantou_classroomcls/feeSetting/time/{{ $classroomcls->croomclsno }}">
                                          {{ config('database_fields.edu_classroomcls')[$classroomcls->fees[402]->timetype].' '.$classroomcls->fees[402]->holidayfee.' 元' }}
                                          @if (isset($classroomcls->fees[402]->feetypeCode))
                                          {{ ' / '.$classroomcls->fees[402]->feetypeCode->name }}
                                          @endif
                                          <br>
                                        </a>
                                        @endif 

                                        @if (isset($classroomcls->fees[403]))
                                        <a href="/admin/place_nantou_classroomcls/feeSetting/time/{{ $classroomcls->croomclsno }}">
                                          {{ config('database_fields.edu_classroomcls')[$classroomcls->fees[403]->timetype].' '.$classroomcls->fees[403]->holidayfee.' 元' }}
                                          @if (isset($classroomcls->fees[403]->feetypeCode))
                                          {{ ' / '.$classroomcls->fees[403]->feetypeCode->name }}
                                          @endif
                                          <br>
                                        </a>
                                        @endif 

                                        @if (!isset($classroomcls->fees[401]) && !isset($classroomcls->fees[402]) && !isset($classroomcls->fees[403]))                               
                                          <a href="/admin/place_nantou_classroomcls/feeSetting/time/{{ $classroomcls->croomclsno }}">未設定</a>
                                        @endif                                   
                                    </div>                                    
                                  </div>  
                                  @endif                               
                                @endif 
                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">容納人數</label>
                                  <div class="col-5">
                                   {{ Form::textarea('summary2', null, ['class' => "form-control", 'style' => "height:75px;"]) }}
                                  </div>                                    
                                </div> 

                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">對外公告費用說明</label>
                                  <div class="col-5">
                                   {{ Form::textarea('summary1', null, ['class' => "form-control", 'style' => "height:75px;"]) }}
                                  </div>                                    
                                </div> 

                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">外借注意事項</label>
                                  <div class="col-5">
                                   {{ Form::text('note', null, ['class' => "form-control"]) }}
                                  </div>                                    
                                </div> 

                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">報表順序</label>
                                  <div class="col-1">
                                   {{ Form::text('printseq', null, ['class' => "form-control"]) }}
                                  </div>                                    
                                </div>                                 

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