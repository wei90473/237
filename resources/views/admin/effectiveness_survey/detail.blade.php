@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'effectiveness_survey';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">成效問卷製作</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">成效問卷製作列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>成效問卷製作</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!--顯示班期資料-->
                                    <div class="float-left search-float" style="min-width: 1000px;">
                                        <form method="get" id="search_form">
                                            <input type="hidden" id="search" name="search" class="form-control" value="search">
                                            <!-- 年度 -->

                                            <!-- 班號 -->
                                            <div class="float-md mobile-100 row ml-1 mr-1 mb-3 ">
                                                <label>班號:{{ $class_basic_info[0]->class}}</label>
                                            </div>

                                            <?php
                                                $branch = '南投';
                                                if ($class_basic_info[0]->branch == '1') {
                                                    $branch = '臺北';
                                                }
                                            ?>
                                            <!-- 辦班院區 -->
                                            <div class="float-md mobile-100 row ml-1 mr-1 mb-3 ">
                                                <label>辦班院區:{{ $branch}}</label>
                                            </div>

                                            <!-- 班別名稱 -->
                                            <div class="float-md mobile-100 row ml-1 mr-1 mb-3">
                                                <label>班別名稱:{{ $class_basic_info[0]->name}}</label>
                                            </div>

                                            <div class="float-md mobile-100 row ml-1 mr-1 mb-3">
                                                <label>期別:{{ $class_basic_info[0]->term}}</label>
                                            </div>

                                            <!-- **分班名稱 -->
                                            <div class="float-md mobile-100 row ml-1 mr-1 mb-3">
                                                <label>分班名稱:{{ $class_basic_info[0]->branchname}}</label>
                                            </div>
                                            <?php
                                                $course_type = '';
                                                switch ($class_basic_info[0]->process) {
                                                    case '1':
                                                        $course_type = '自辦班';
                                                        break;
                                                    case '2':
                                                        $course_type = '委訓班';
                                                        break;
                                                    case '3':
                                                        $course_type = '合作辦理';
                                                        break;
                                                    case '4':
                                                        $course_type = '外地班';
                                                        break;
                                                    case '5':
                                                        $course_type = '巡迴研習';
                                                        break;
                                                    default:
                                                        $course_type = '';
                                                }
                                            ?>
                                            <div class="float-md mobile-100 row ml-1 mr-1 mb-3">
                                                
                                                <label>班別類型:{{ $course_type}}</label>
                                            </div>

                                            <div class="float-md mobile-100 row ml-1 mr-1 mb-3">
                                                <label>委訓機關:{{ $class_basic_info[0]->client}}</label>
                                            </div>
                                            
                                            <?php 
                                                $date=$class_basic_info[0]->sdate.'~'.$class_basic_info[0]->edate;
                                            ?>
                                            <div class="float-md mobile-100 row ml-1 mr-1 mb-3">
                                                
                                                <label>起訖期間:{{$date}}</label>
                                            </div>

                                            <div class="float-md mobile-100 row ml-1 mr-1 mb-3">
                                               
                                                <label>班務人員:{{$class_basic_info[0]->sp_name}}</label>
                                            </div>

                                        </form>
                                    </div>
                                    
                                    
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <?php 
                                                $arr=['class'=>$class_basic_info[0]->class,
                                                      'term'=>$class_basic_info[0]->term];
                                                
                                            ?>
                                            <!--新增-->
                                            <thead>
                                                <div class="float-md-left">
                                                    <a href="/admin/effectiveness_survey/create/{{serialize($arr)}}">
                                                        <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                                    </a>
                                                </div>
                                            </thead>
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="5%">功能</th>
                                                    <th class="text-center">第幾次調查</th>
                                                    <th class="text-center">發出卷數</th>
                                                    <th class="text-center">填表期間</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $results)
                                                <tr class="text-center">
                                                    <?php 
                                                        $arr=['class'=>$results->class,
                                                              'term' =>$results->term,
                                                              'times'=>$results->times,]
                                                    ?>
                                                    <td>
                                                        <a href="/admin/effectiveness_survey/{{serialize($arr)}}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>編輯
                                                        </a>
                                                    </td>
                                                    <td>{{$results->times}}</td>
                                                    <td>{{$results->copy}}</td>
                                                    <?php 
                                                        $date=$results->fillsdate.'~'.$results->filledate;
                                                    ?>
                                                    <td>{{$date}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection