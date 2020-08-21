<?php $_menu = (isset($_menu))? $_menu : false;?>
<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <div id="sidebar-menu">
            <ul>
            {{-- 年度資料 --}}
                @inject('metrics', 'App\Services\User_groupService')
                <?php $user_group_id = Auth::user()->user_group_id;
                      $user_menu = $metrics->getUser_auth($user_group_id);
                ?>
                @if( in_array('menu-1', $user_menu) )
                <?php $menuUnit = ['classes', 'demand_survey', 'demand_distribution', 'periods', 'schedule', 'site_manage',
                    'site_schedule', 'demand_measure_report', 'demand_quota_report', 'studyplan_all'
                    , 'studyplan_periods', 'studyplan_distribution_all', 'studyplan_distribution_detail', 'studyplan_quota_all', 'training_performance'
                    , 'each_training_all', 'same_assessment', 'business_statistics', 'restructuring','class_group'];?>
                <li class="has_sub">
                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 年度資料 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                    <ul class="list-unstyled">

                        <!-- 班別資料處理 -->
                        @if( in_array('classes', $user_menu) )
                        <li class="{{ @active('classes') }}">
                            <a href="/admin/classes" class="waves-effect"><span> 班別資料處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('demand_survey', $user_menu) )
                        <!-- 需求調查處理 -->
                        <li class="{{ @active('demand_survey') }}">
                            <a href="/admin/demand_survey" class="waves-effect"><span> 需求調查處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('demand_survey_commissioned', $user_menu) )
                        <!-- 委訓班需求調查處理 -->
                        <li class="{{ @active('demand_survey_commissioned') }}">
                            <a href="/admin/demand_survey_commissioned" class="waves-effect"><span> 委訓班需求調查</span></a>
                        </li>
                        @endif

                        @if( in_array('demand_distribution', $user_menu) )
                        <!-- 需求分配 -->
                        <li class="{{ @active('demand_distribution') }}">
                            <a href="/admin/demand_distribution" class="waves-effect"><span> 需求分配處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('periods', $user_menu) )
                        <!-- 開班期數 -->
                        <li class="{{ @active('periods') }}">
                            <a href="/admin/periods" class="waves-effect"><span> 開班期數處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('schedule', $user_menu) )
					    <!-- 開班期數 -->
                        <li class="{{ @active('schedule') }}">
                            <a href="/admin/schedule" class="waves-effect"><span> 訓練排程處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('site_manage', $user_menu) )
                        <!-- 洽借場地班期資料處理 -->
                        <li class="{{ @active('site_manage') }}">
                            <a href="/admin/site_manage" class="waves-effect"><span> 洽借場地班期資料處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('site_schedule', $user_menu) )
                        <!-- 洽借場地班期排程處理 -->
                        <li class="{{ @active('site_schedule') }}">
                            <a href="/admin/site_schedule" class="waves-effect"><span> 洽借場地班期排程處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('restructuring', $user_menu) )
                        <!-- 組織改制對照表維護 -->
                        <li class="{{ @active('restructuring') }}">
                            <a href="/admin/restructuring" class="waves-effect"><span> 組織改制對照表維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('performance', $user_menu) )
                        <!-- 訓練績效處理 -->
                        <li class="{{ @active('performance') }}">
                            <a href="/admin/performance" class="waves-effect"><span> 訓練績效處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('class_group', $user_menu) )
                        <!-- 重覆參訓檢核群組維護 -->
                        <li class="{{ @active('class_group') }}">
                            <a href="/admin/class_group" class="waves-effect"><span> 重覆參訓檢核群組維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('menu-1-1', $user_menu) )
                        <?php $menuUnit = ['demand_measure_report', 'demand_quota_report', 'training_performance', 'each_training_all', 'same_assessment', 'business_statistics'
                    ,'studyplan_all', 'studyplan_periods', 'studyplan_distribution_all', 'studyplan_distribution_detail', 'studyplan_quota_all', 'yearly_channel'];?>
                        <!--報表列印-->
                        <li class="has_sub">
                            <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 報表列印 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                            <ul class="list-unstyled">

                            	@if( in_array('demand_measure_report', $user_menu) )
                                <!-- 需求調查表 -->
                                <li class="{{ @active('demand_measure_report') }}">
                                    <a href="/admin/demand_measure_report" class="waves-effect"><span> 需求調查表 </span></a>
                                </li>
                                @endif

                                @if( in_array('demand_quota_report', $user_menu) )
                                <!-- 需求名額統計表 -->
                                <li class="{{ @active('demand_quota_report') }}">
                                    <a href="/admin/demand_quota_report" class="waves-effect"><span> 需求名額統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('studyplan_all', $user_menu) )
                                <!-- 總表 -->
                                <li class="{{ @active('studyplan_all') }}">
                                    <a href="/admin/studyplan_all" class="waves-effect"><span> 研習實施計畫總表 </span></a>
                                </li>
                                @endif

                                @if( in_array('schedule_list', $user_menu) )
                                <!-- D4 行事表 -->
                                <li class="{{ @active('schedule_list') }}">
                                    <a href="/admin/schedule_list" class="waves-effect"><span> 行事表 </span></a>
                                </li>
                                @endif

                                @if( in_array('training_period_list', $user_menu) )
                                <!-- D5 訓期一覽表  -->
                                <li class="{{ @active('training_period_list') }}">
                                    <a href="/admin/training_period_list" class="waves-effect"><span> 訓期一覽表 </span></a>
                                </li>
                                @endif

                                @if( in_array('class_distribution', $user_menu) )
                                <!-- D6 班次分配表 -->
                                <li class="{{ @active('class_distribution') }}">
                                    <a href="/admin/class_distribution" class="waves-effect"><span> 班次分配表 </span></a>
                                </li>
                                @endif

                                @if( in_array('studyplan_distribution_all', $user_menu) )
                                <!-- 名額分配總表 -->
                                <li class="{{ @active('studyplan_distribution_all') }}">
                                    <a href="/admin/studyplan_distribution_all" class="waves-effect"><span> 名額分配總表 </span></a>
                                </li>
                                @endif

                                @if( in_array('studyplan_distribution_detail', $user_menu) )
                                <!-- 名額分配明細表 -->
                                <li class="{{ @active('studyplan_distribution_detail') }}">
                                    <a href="/admin/studyplan_distribution_detail" class="waves-effect"><span> 名額分配明細表 </span></a>
                                </li>
                                @endif

                                @if( in_array('studyplan_quota_all', $user_menu) )
                                <!-- 名額彙總表 -->
                                <li class="{{ @active('studyplan_quota_all') }}">
                                    <a href="/admin/studyplan_quota_all" class="waves-effect"><span> 名額彙總表 </span></a>
                                </li>
                                @endif

                                @if( in_array('training_performance', $user_menu) )
                                <!-- 訓練績效報表 -->
                                <li class="{{ @active('training_performance') }}">
                                    <a href="/admin/training_performance" class="waves-effect"><span> 訓練績效報表 </span></a>
                                </li>
                                @endif

                                @if( in_array('YearlyChannelController', $user_menu) )
                                <!-- D10 年度流路明細表 -->
                                <li class="{{ @active('YearlyChannelController') }}">
                                    <a href="/admin/yearly_channel" class="waves-effect"><span> 年度流路明細表 </span></a>
                                </li>
                                @endif

                                @if( in_array('each_training_all', $user_menu) )
                                <!-- 各類訓練進修研習成果統計彙總表 -->
                                <li class="{{ @active('each_training_all') }}">
                                    <a href="/admin/each_training_all" class="waves-effect"><span> 各類訓練進修研習成果統計彙總表 </span></a>
                                </li>
                                @endif

                                @if( in_array('business_statistics', $user_menu) )
	                            <!-- 公務統計報表 -->
                                <li class="{{ @active('business_statistics') }}">
                                    <a href="/admin/business_statistics" class="waves-effect"><span> 公務統計報表 </span></a>
                                </li>
                                @endif

                                @if( in_array('delegate_class_term_list', $user_menu) )
	                            <!-- D14 接受委訓班期訓期一覽表 -->
                                <li class="{{ @active('delegate_class_term_list') }}">
                                    <a href="/admin/delegate_class_term_list" class="waves-effect"><span> 接受委訓班期訓期一覽表 </span></a>
                                </li>
                                @endif

                                @if( in_array('delegate_training_request', $user_menu) )
	                            <!-- D15 接受委託辦理訓練需求彙總表 -->
                                <li class="{{ @active('delegate_training_request') }}">
                                    <a href="/admin/delegate_training_request" class="waves-effect"><span> 接受委託辦理訓練需求彙總表 </span></a>
                                </li>
                                @endif

                                <!-- @if( in_array('studyplan_periods', $user_menu) )

                                <li class="{{ @active('studyplan_periods') }}">
                                    <a href="/admin/studyplan_periods" class="waves-effect"><span> 訓期一覽表 </span></a>
                                </li>
                                @endif -->

                                @if( in_array('same_assessment', $user_menu) )
                                <!-- 共同考核項目報表 -->
                                <li class="{{ @active('same_assessment') }}">
                                    <a href="/admin/same_assessment" class="waves-effect"><span> 共同考核項目報表 </span></a>
                                </li>
                                @endif

                            </ul>
                        </li>
                        @endif

                    </ul>
                </li>
                @endif

                {{-- 訓練班務 --}}
                @if( in_array('menu-2', $user_menu) )
                <?php $menuUnit = ['arrangement', 'class_schedule', 'method', 'signup', 'review_apply', 'special_class_fee', 'sponsor_agent', 'funding', 'teachlist','teachingmethod', 'teaching_material', 'term_process'];?>
                <li class="has_sub">
                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 訓練班務 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                    <ul class="list-unstyled">

                    	@if( in_array('term_process', $user_menu) )
                        <!-- 班務流程 -->
                        <li class="{{ @active('term_process') }}">
                            <a href="/admin/term_process" class="waves-effect"><span> 班務流程 </span></a>
                        </li>
                        @endif

                        @if( in_array('arrangement', $user_menu) )
                        <!-- 課程配當安排 -->
                        <li class="{{ @active('arrangement') }}">
                            <a href="/admin/arrangement" class="waves-effect"><span> 課程配當安排 </span></a>
                        </li>
                        @endif

                        @if( in_array('class_schedule', $user_menu) )
                        <!-- 課程表處理 -->
                        <li class="{{ @active('class_schedule') }}">
                            <a href="/admin/class_schedule" class="waves-effect"><span> 課程表處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('teaching_material', $user_menu) )
                        <!-- 講座授課及教材資料登錄 -->
                        <li class="{{ @active('teaching_material') }}">
                            <a href="/admin/teaching_material" class="waves-effect"><span> 講座授課及教材資料登錄 </span></a>
                        </li>
                        @endif

                        @if( in_array('method', $user_menu) )
                        <!-- 教學教法處理 -->
                        <li class="{{ @active('method') }}">
                            <a href="/admin/method" class="waves-effect"><span> 教學教法處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('funding', $user_menu) )
                        <!-- 經費概(結)算處理 -->
                        <li class="{{ @active('funding') }}">
                            <a href="/admin/funding/class_list" class="waves-effect"><span>經費概(結)算處理</span></a>
                        </li>
                        @endif

                        @if( in_array('signup', $user_menu) )
                        <!-- 線上報名設定 -->
                        <li class="{{ @active('signup') }}">
                            <a href="/admin/signup" class="waves-effect"><span> 線上報名設定 </span></a>
                        </li>
                        @endif

                        @if( in_array('review_apply', $user_menu) )
                        <!-- 報名審核處理 -->
                        <li class="{{ @active('review_apply') }}">
                            <a href="/admin/review_apply/class_list" class="waves-effect"><span> 報名審核處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('special_class_fee', $user_menu) )
                        <!-- 委訓班費用處理 -->
                        <li class="{{ @active('special_class_fee') }}">
                            <a href="/admin/special_class_fee/class_list" class="waves-effect"><span> 委訓班費用處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('web_simulation', $user_menu) )
                        <li class="{{ @active('web_simulation') }}">
                            <a href="/admin/web_simulation" class="waves-effect"><span> 模擬前台角色 </span></a>
                        </li>
                        @endif

                        @if( in_array('sponsor_agent', $user_menu) )
                        <!-- 班期管理代理人員維護 -->
                        <li class="{{ @active('sponsor_agent') }}">
                            <a href="/admin/sponsor_agent" class="waves-effect"><span> 班期管理代理人員維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('teachlist', $user_menu) )
                        <!-- 維護教法調查班別 -->
                        <li class="{{ @active('teachlist') }}">
                            <a href="/admin/teachlist" class="waves-effect"><span>維護教法調查班別</span></a>
                        </li>
                        @endif

                        @if( in_array('teachingmethod', $user_menu) )
                        <!-- 教學教法資料維護 -->
                        <li class="{{ @active('teachingmethod') }}">
                            <a href="/admin/teachingmethod" class="waves-effect"><span> 教學教法資料維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('menu-2-1', $user_menu) )
                        <?php $menuUnit = ['course_assignment','course_schedule','course_funding','yearly_class_funding'
                    ,'changetraining_error','complex_report','process_deadline','class_daily_report','teachway_all'];?>
                        <!--報表列印-->
                        <li class="has_sub">
                            <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 報表列印 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                            <ul class="list-unstyled">

                            	@if( in_array('sendtraining_joint', $user_menu) )
	                            <!-- 聯合派訓通知 -->
                                <li class="{{ @active('sendtraining_joint') }}">
                                    <a href="/admin/sendtraining_joint" class="waves-effect"><span> 聯合派訓通知 </span></a>
                                </li>
                                @endif

                                @if( in_array('sendtraining_quota', $user_menu) )
	                            <!-- 名額分配表 -->
                                <li class="{{ @active('sendtraining_quota') }}">
                                    <a href="/admin/sendtraining_quota" class="waves-effect"><span> 名額分配表 </span></a>
                                </li>
                                @endif

                                @if( in_array('transfer_training_letter', $user_menu) )
	                            <!-- F3 調訓函 -->
                                <li class="{{ @active('transfer_training_letter') }}">
                                    <a href="/admin/transfer_training_letter" class="waves-effect"><span> 調訓函 </span></a>
                                </li>
                                @endif

                                @if( in_array('changetraining_plan', $user_menu) )
	                            <!-- 實施計畫 -->
                                <li class="{{ @active('changetraining_plan') }}">
                                    <a href="/admin/changetraining_plan" class="waves-effect"><span> 實施計畫 </span></a>
                                </li>
                                @endif

                                @if( in_array('changetraining_error', $user_menu) )
                                <!-- 班期調派訓異常統計表 -->
                                <li class="{{ @active('changetraining_error') }}">
                                    <a href="/admin/changetraining_error" class="waves-effect"><span> 班期調派訓異常統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('course_assignment', $user_menu) )
                                <!-- 課程配當 -->
                                <li class="{{ @active('course_assignment') }}">
                                    <a href="/admin/course_assignment" class="waves-effect"><span> 課程配當 </span></a>
                                </li>
                                @endif

                                @if( in_array('course_schedule', $user_menu) )
                                <!-- 課程表 -->
                                <li class="{{ @active('course_schedule') }}">
                                    <a href="/admin/course_schedule" class="waves-effect"><span> 課程表 </span></a>
                                </li>
                                @endif

                                @if( in_array('class_daily_report', $user_menu) )
                                <!-- 班期日報表 -->
                                <li class="{{ @active('class_daily_report') }}">
                                    <a href="/admin/class_daily_report" class="waves-effect"><span> 班期日報表 </span></a>
                                </li>
                                @endif

                                @if( in_array('class_term_plan', $user_menu) )
                                <!-- F9 班期計畫表 -->
                                <li class="{{ @active('class_term_plan') }}">
                                    <a href="/admin/class_term_plan" class="waves-effect"><span> 班期計畫表 </span></a>
                                </li>
                                @endif

                                @if( in_array('lecture_course_list', $user_menu) )
                                <!-- F10 講座期間授課課表 -->
                                <li class="{{ @active('lecture_course_list') }}">
                                    <a href="/admin/lecture_course_list" class="waves-effect"><span> 講座期間授課課表 </span></a>
                                </li>
                                @endif

                                @if( in_array('classroom_usage_list', $user_menu) )
                                <!-- F11 教室使用一覽表 -->
                                <li class="{{ @active('classroom_usage_list') }}">
                                    <a href="/admin/classroom_usage_list" class="waves-effect"><span> 教室使用一覽表 </span></a>
                                </li>
                                @endif

                                <!-- F12 辦理流程期限表 -->
                                @if( in_array('process_timetable', $user_menu) )
                                <li class="{{ @active('process_timetable') }}">
                                    <a href="/admin/process_timetable" class="waves-effect"><span> 辦理流程期限表 </span></a>
                                </li>
                                @endif

                                @if( in_array('course_funding', $user_menu) )
                                <!-- 課程經費概(結)算表 -->
                                <li class="{{ @active('course_funding') }}">
                                    <a href="/admin/course_funding" class="waves-effect"><span> 課程經費概(結)算表 </span></a>
                                </li>
                                @endif

                                @if( in_array('yearly_class_funding', $user_menu) )
                                <!-- 年度班期費用統計表 -->
                                <li class="{{ @active('yearly_class_funding') }}">
                                    <a href="/admin/yearly_class_funding" class="waves-effect"><span> 年度班期費用統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('delegate_training_cost', $user_menu) )
                                <!-- F15 委訓費用明細表 -->
                                <li class="{{ @active('delegate_training_cost') }}">
                                    <a href="/admin/delegate_training_cost" class="waves-effect"><span> 委訓費用明細表 </span></a>
                                </li>
                                @endif

                                @if( in_array('delegate_training_cost_quota', $user_menu) )
                                <!-- F16 委訓經費各單位分配額度表 -->
                                <li class="{{ @active('delegate_training_cost_quota') }}">
                                    <a href="/admin/delegate_training_cost_quota" class="waves-effect"><span> 委訓經費各單位分配額度表 </span></a>
                                </li>
                                @endif

                                @if( in_array('teachway_all', $user_menu) )
                                <!-- 教學方法運用彙整表 -->
                                <li class="{{ @active('teachway_all') }}">
                                    <a href="/admin/teachway_all" class="waves-effect"><span> 教學方法運用彙整表 </span></a>
                                </li>
                                @endif

                                @if( in_array('teach_way_statics', $user_menu) )
                                <!-- F18 教法運用統計圖表 -->
                                <li class="{{ @active('teach_way_statics') }}">
                                    <a href="/admin/teach_way_statics" class="waves-effect"><span> 教法運用統計圖表 </span></a>
                                </li>
                                @endif

                                @if( in_array('teach_way_satisfaction', $user_menu) )
                                <!-- F19 班別性質教法運用滿意度統計表 -->
                                <li class="{{ @active('teach_way_satisfaction') }}">
                                    <a href="/admin/teach_way_satisfaction" class="waves-effect"><span> 班別性質教法運用滿意度統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('teach_way_course_analyze', $user_menu) )
                                <!-- F20 課程教學教法數目分析 -->
                                <li class="{{ @active('teach_way_course_analyze') }}">
                                    <a href="/admin/teach_way_course_analyze" class="waves-effect"><span> 課程教學教法數目分析 </span></a>
                                </li>
                                @endif

                                @if( in_array('teach_way_calss_analyze', $user_menu) )
                                <!-- F21 班別性質與教法數目分析表 -->
                                <li class="{{ @active('teach_way_calss_analyze') }}">
                                    <a href="/admin/teach_way_calss_analyze" class="waves-effect"><span> 班別性質與教法數目分析表 </span></a>
                                </li>
                                @endif

                            </ul>
                        </li>
                        @endif

                    </ul>
                </li>
                @endif

                @if( in_array('menu-3', $user_menu) )
                {{-- 講師資料 --}}
                <?php $menuUnit = ['waiting', 'transfer_processing', 'lecture', 'employ', 'tax_processing', 'teacher_related', 'parameter_setting', 'teacher_reception', 'satisfaction'];?>
                <li class="has_sub">
                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 講師資料 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                    <ul class="list-unstyled">

                    	@if( in_array('waiting', $user_menu) )
                        <!-- 講座擬聘處理 -->
                        <li class="{{ @active('waiting') }}">
                            <a href="/admin/waiting" class="waves-effect"><span> 講座擬聘處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('lecture', $user_menu) )
                        <!-- 講座資料登錄 -->
                        <li class="{{ @active('lecture') }}">
                            <a href="/admin/lecture" class="waves-effect"><span> 講座資料登錄 </span></a>
                        </li>
                        @endif

                        @if( in_array('employ', $user_menu) )
                        <!-- 講座聘任處理 -->
                        <li class="{{ @active('employ') }}">
                            <a href="/admin/employ" class="waves-effect"><span> 講座聘任處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('transfer_processing', $user_menu) )
                        <!-- 鐘點費轉帳處理 -->
                        <li class="{{ @active('transfer_processing') }}">
                            <a href="/admin/transfer_processing" class="waves-effect"><span> 鐘點費轉帳處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('tax_processing', $user_menu) )
                        <!-- 所得稅申報處理 -->
                        <li class="{{ @active('tax_processing') }}">
                            <a href="/admin/tax_processing" class="waves-effect"><span> 所得稅申報處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('teacher_related', $user_menu) )
                        <!-- 講座用餐、住宿、派車資料登錄 -->
                        <li class="{{ @active('teacher_related') }}">
                            <a href="/admin/teacher_related" class="waves-effect"><span> 講座用餐、住宿、派車資料登錄 </span></a>
                        </li>
                        @endif

                        @if( in_array('teacher_reception', $user_menu) )
                        <!-- 講座接待管理 -->
                        <li class="{{ @active('teacher_reception') }}">
                            <a href="/admin/teacher_reception" class="waves-effect"><span> 講座接待管理 </span></a>
                        </li>
                        @endif

                        @if( in_array('satisfaction', $user_menu) )
                        <!-- 課程及講座查詢(滿意度) -->
                        <li class="{{ @active('satisfaction') }}">
                            <a href="/admin/satisfaction" class="waves-effect"><span> 課程及講座查詢(滿意度) </span></a>
                        </li>
                        @endif

                        @if( in_array('parameter_setting', $user_menu) )
                        <!-- 講座服務參數維護 -->
                        <li class="{{ @active('parameter_setting') }}">
                            <a href="/admin/parameter_setting_1" class="waves-effect"><span> 講座服務參數維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('menu-3-1', $user_menu) )
                        <?php $menuUnit = ['lecture_list','teacher_information','lecture_mail','lecture_post'
                    ,'yearly_lecture_roster','lecture_categories', 'lecture_class', 'lecture_course', 'lecture_signature'
                    ,'lecture_money_detail','lecture_money_all','yearly_income_all','yearly_income_detail','thinktank_all'
                    ,'application_stickynote','entrusted_save_all','post_save_slip','interbank_remittance_form','interbank_remittance_detail'
                    ,'hourlyfee_notice','remittance_detail'];?>
                        <!--報表列印-->
                        <li class="has_sub">
                            <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 報表列印 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                            <ul class="list-unstyled">

                            	@if( in_array('lecture_list', $user_menu) )
                                <!-- 講座名單 -->
                                <li class="{{ @active('lecture_list') }}">
                                    <a href="/admin/lecture_list" class="waves-effect"><span> 講座名單 </span></a>
                                </li>
                                @endif

                                @if( in_array('teacher_information', $user_menu) )
                                <!-- 講師基本資料 -->
                                <li class="{{ @active('teacher_information') }}">
                                    <a href="/admin/teacher_information" class="waves-effect"><span> 講師基本資料 </span></a>
                                </li>
                                @endif

                                @if( in_array('lecture_mail', $user_menu) )
                                <!-- 講座聘函 -->
                                <li class="{{ @active('lecture_mail') }}">
                                    <a href="/admin/lecture_mail" class="waves-effect"><span> 講座聘函 </span></a>
                                </li>
                                @endif

                                @if( in_array('lecture_signature', $user_menu) )
                                <!-- 講師簽名單 -->
                                <li class="{{ @active('lecture_signature') }}">
                                    <a href="/admin/lecture_signature" class="waves-effect"><span> 講師簽名單 </span></a>
                                </li>
                                @endif

                                @if( in_array('lecture_post', $user_menu) )
                                <!-- 講座郵寄名條 -->
                                <li class="{{ @active('lecture_post') }}">
                                    <a href="/admin/lecture_post" class="waves-effect"><span> 講座郵寄名條 </span></a>
                                </li>
                                @endif

                                @if( in_array('yearly_lecture_roster', $user_menu) )
                                <!-- 年度講座名冊錄 -->
                                <li class="{{ @active('yearly_lecture_roster') }}">
                                    <a href="/admin/yearly_lecture_roster" class="waves-effect"><span> 年度講座名冊錄 </span></a>
                                </li>
                                @endif

                                @if( in_array('menu-3-1-1', $user_menu) )
                                <?php $menuUnit = ['lecture_categories', 'lecture_class', 'lecture_course'];?>
                                <!--講座一覽表-->
                                <li class="has_sub">
                                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 講座一覽表 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                                    <ul class="list-unstyled">

                                    	@if( in_array('lecture_categories', $user_menu) )
                                        <!-- 各類別 -->
                                        <li class="{{ @active('lecture_categories') }}">
                                            <a href="/admin/lecture_categories" class="waves-effect"><span> 各類別 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('lecture_class', $user_menu) )
                                        <!-- 各班期 -->
                                        <li class="{{ @active('lecture_class') }}">
                                            <a href="/admin/lecture_class" class="waves-effect"><span> 各班期 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('lecture_course', $user_menu) )
                                        <!-- 各課程 -->
                                        <li class="{{ @active('lecture_course') }}">
                                            <a href="/admin/lecture_course" class="waves-effect"><span> 各課程 </span></a>
                                        </li>
                                        @endif

                                    </ul>
                                </li>
                                @endif

                                @if( in_array('application_stickynote', $user_menu) )
                                <!-- 申請表及黏存單 -->
                                <li class="{{ @active('application_stickynote') }}">
                                    <a href="/admin/application_stickynote" class="waves-effect"><span> 申請表及黏存單 </span></a>
                                </li>
                                @endif

                                @if( in_array('lecture_money_detail', $user_menu) )
	                            <!-- 講座費用請領清冊 -->
                                <li class="{{ @active('lecture_money_detail') }}">
                                    <a href="/admin/lecture_money_detail" class="waves-effect"><span> 講座費用請領清冊 </span></a>
                                </li>
                                @endif

                                @if( in_array('lecture_money_all', $user_menu) )
                                <!-- 講座費用請領總表 -->
                                <li class="{{ @active('lecture_money_all') }}">
                                    <a href="/admin/lecture_money_all" class="waves-effect"><span> 講座費用請領總表 </span></a>
                                </li>
                                @endif

                                @if( in_array('yearly_income_all', $user_menu) )
                                <!-- 年度講座所得統計表 -->
                                <li class="{{ @active('yearly_income_all') }}">
                                    <a href="/admin/yearly_income_all" class="waves-effect"><span> 年度講座所得統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('yearly_income_detail', $user_menu) )
                                <!-- 年度講座所得明細表 -->
                                <li class="{{ @active('yearly_income_detail') }}">
                                    <a href="/admin/yearly_income_detail" class="waves-effect"><span> 年度講座所得明細表 </span></a>
                                </li>
                                @endif

                                @if( in_array('entrusted_save_all', $user_menu) )
                                <!-- 委託郵局代存總表 -->
                                <li class="{{ @active('entrusted_save_all') }}">
                                    <a href="/admin/entrusted_save_all" class="waves-effect"><span> 委託郵局代存總表 </span></a>
                                </li>
                                @endif

                                @if( in_array('post_save_slip', $user_menu) )
                                <!-- 郵政存款單 -->
                                <li class="{{ @active('post_save_slip') }}">
                                    <a href="/admin/post_save_slip" class="waves-effect"><span> 郵政存款單 </span></a>
                                </li>
                                @endif

                                @if( in_array('interbank_remittance_form', $user_menu) )
                                <!-- 郵政跨行匯款申請書 -->
                                <li class="{{ @active('interbank_remittance_form') }}">
                                    <a href="/admin/interbank_remittance_form" class="waves-effect"><span> 郵政跨行匯款申請書 </span></a>
                                </li>
                                @endif

                                @if( in_array('interbank_remittance_detail', $user_menu) )
	                            <!-- 跨行匯款明細表 -->
                                <li class="{{ @active('interbank_remittance_detail') }}">
                                    <a href="/admin/interbank_remittance_detail" class="waves-effect"><span> 跨行匯款明細表 </span></a>
                                </li>
                                @endif

                                @if( in_array('hourlyfee_notice', $user_menu) )
                                <!-- 鐘點費入帳通知書 -->
                                <li class="{{ @active('hourlyfee_notice') }}">
                                    <a href="/admin/hourlyfee_notice" class="waves-effect"><span> 鐘點費入帳通知書 </span></a>
                                </li>
                                @endif

                                @if( in_array('remittance_detail', $user_menu) )
                                <!-- 匯款明細表 -->
                                <li class="{{ @active('remittance_detail') }}">
                                    <a href="/admin/remittance_detail" class="waves-effect"><span> 匯款明細表 </span></a>
                                </li>
                                @endif

                                @if( in_array('lecture_special_need', $user_menu) )
                                <!-- H19 講座特殊需求一覽表 -->
                                <li class="{{ @active('lecture_special_need') }}">
                                    <a href="/admin/lecture_special_need" class="waves-effect"><span> 講座特殊需求一覽表 </span></a>
                                </li>
                                @endif

                                @if( in_array('lecture_reception_list', $user_menu) )
                                <!-- H20 講座接待一覽表 -->
                                <li class="{{ @active('lecture_reception_list') }}">
                                    <a href="/admin/lecture_reception_list" class="waves-effect"><span> 講座接待一覽表 </span></a>
                                </li>
                                @endif

                                @if( in_array('lecture_pickup_record', $user_menu) )
                                <!-- H21 接送講座紀錄結算表 -->
                                <li class="{{ @active('lecture_pickup_record') }}">
                                    <a href="/admin/lecture_pickup_record" class="waves-effect"><span> 接送講座紀錄結算表 </span></a>
                                </li>
                                @endif

                                @if( in_array('lecture_pickup_record_summary', $user_menu) )
                                <!-- H22 接送講座紀錄結算總表 -->
                                <li class="{{ @active('lecture_pickup_record_summary') }}">
                                    <a href="/admin/lecture_pickup_record_summary" class="waves-effect"><span> 接送講座紀錄結算總表 </span></a>
                                </li>
                                @endif

                                @if( in_array('lecture_bedroom_usage', $user_menu) )
                                <!-- H23 講座寢室使用情形一覽表 -->
                                <li class="{{ @active('lecture_bedroom_usage') }}">
                                    <a href="/admin/lecture_bedroom_usage" class="waves-effect"><span> 講座寢室使用情形一覽表 </span></a>
                                </li>
                                @endif

                                @if( in_array('pickup_location_times', $user_menu) )
                                <!-- H24 接送地點及次數一覽表 -->
                                <li class="{{ @active('pickup_location_times') }}">
                                    <a href="/admin/pickup_location_times" class="waves-effect"><span> 接送地點及次數一覽表 </span></a>
                                </li>
                                @endif

                                <!--講座鐘點費轉帳-->
                                <!-- <li class="has_sub">
                                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 講座鐘點費轉帳 以下沒有 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                                    <ul class="list-unstyled">

                                    </ul>
                                </li> -->

                                @if( in_array('thinktank_all', $user_menu) )
                                <!-- 智庫一覽表 -->
                                <li class="{{ @active('thinktank_all') }}">
                                    <a href="/admin/thinktank_all" class="waves-effect"><span> 智庫一覽表 </span></a>
                                </li>
                                @endif


                            </ul>
                        </li>
                        @endif

                    </ul>
                </li>
                @endif

                @if( in_array('menu-4', $user_menu) )
                {{-- 學員資料 --}}
                <?php $menuUnit = ['student_apply', 'leave', 'student_grade', 'train_certification', 'digital', 'student', 'site_review', 'punch', 'certification', 'signature', 'student_seat_list_south'];?>
                <li class="has_sub">
                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 學員資料 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                    <ul class="list-unstyled">

                    	@if( in_array('student_apply', $user_menu) )
                        <!-- 學員報名處理 -->
                        <li class="{{ @active('student_apply') }}">
                            <a href="/admin/student_apply/class_list" class="waves-effect"><span> 學員報名處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('leave', $user_menu) )
                        <!-- 學員請假處理 -->
                        <li class="{{ @active('leave') }}">
                            <a href="/admin/leave/class_list" class="waves-effect"><span> 學員請假處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('student_grade', $user_menu) )
                        <!-- 成績輸入處理 -->
                        <li class="{{ @active('student_grade') }}">
                            <a href="/admin/student_grade/class_list" class="waves-effect"><span> 成績輸入處理 </span></a>
                        </li>
                        @endif

                        <!-- 學員結訓處理 -->
                        <!-- <li class="{{ @active('train_certification') }}">
                            <a href="/admin/train_certification" class="waves-effect"><span> 學員結訓處理 </span></a>
                        </li> -->

                        @if( in_array('digital', $user_menu) )
                        <!-- 數位時數處理 -->
                        <li class="{{ @active('digital') }}">
                            <a href="/admin/digital/class_list" class="waves-effect"><span> 數位時數處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('student', $user_menu) )
                        <!-- 學員資料登錄 -->
                        <li class="{{ @active('student') }}">
                            <a href="/admin/student" class="waves-effect"><span> 學員資料登錄 </span></a>
                        </li>
                        @endif

                        @if( in_array('site_review', $user_menu) )
                        <!-- 洽借場地班期選員處理 -->
                        <li class="{{ @active('site_review') }}">
                            <a href="/admin/site_review/class_list" class="waves-effect"><span> 洽借場地班期選員處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('punch', $user_menu) )
                        <!-- 學員刷卡處理 -->
                        <li class="{{ @active('punch') }}">
                            <a href="/admin/punch/class_list" class="waves-effect"><span> 學員刷卡處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('signature', $user_menu) )
                        <!-- 研習證書列印電子章設定 -->
                        <li class="{{ @active('signature') }}">
                            <a href="/admin/signature" class="waves-effect"><span> 研習證書列印電子章設定 </span></a>
                        </li>
                        @endif

                        <!-- 認證上傳設定 -->
                        <!-- <li class="{{ @active('certification') }}">
                            <a href="/admin/certification" class="waves-effect"><span> 認證上傳設定 </span></a>
                        </li> -->

                        @if( in_array('menu-4-1', $user_menu) )
                        <?php $menuUnit = ['student_seat_list', 'student_signin', 'student_leave', 'student_grade_rpt',
                         'student_study_certificate', 'student_address_book', 'student_mail_nametape', 'student_training_record',
                         'count_signin', 'count_participate', 'count_train', 'count_onjob_train', 'organ_burden_detail',
                         'organ_address_letter', 'student_registration_comparison', 'organ_sid_comparison',
                         'student_checklist', 'student_card_record', 'trainees_promotion_record','student_seat_namecard',
                         'student_list','student_registration','student_namecard', 'student_seat_list_south'];?>
                        <!--報表列印-->
                        <li class="has_sub">
                            <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 報表列印 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                            <ul class="list-unstyled">

                            	@if( in_array('student_registration', $user_menu) )
    	                        <!-- 學員報名表 -->
                                <li class="{{ @active('student_registration') }}">
                                    <a href="/admin/student_registration" class="waves-effect"><span> 學員報名表 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_list', $user_menu) )
                                <!-- 學員名冊 -->
                                <li class="{{ @active('student_list') }}">
                                    <a href="/admin/student_list" class="waves-effect"><span> 學員名冊 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_seat_namecard', $user_menu) )
                                <!-- 學員座位名牌卡 -->
                                <li class="{{ @active('student_seat_namecard') }}">
                                    <a href="/admin/student_seat_namecard" class="waves-effect"><span> 學員座位名牌卡 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_seat_namecard', $user_menu) )
                                <!-- 學員識別證 -->
                                <li class="{{ @active('student_namecard') }}">
                                    <a href="/admin/student_namecard" class="waves-effect"><span> 學員識別證 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_signin', $user_menu) )
                                <!-- 學員簽到表 -->
                                <li class="{{ @active('student_signin') }}">
                                    <a href="/admin/student_signin" class="waves-effect"><span> 學員簽到表 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_seat_list', $user_menu) )
                                <!-- 學員座位表 -->
                                <li class="{{ @active('student_seat_list') }}">
                                    <a href="/admin/student_seat_list" class="waves-effect"><span> 學員座位表 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_seat_list_south', $user_menu) )
                                <!-- 學員座位表 -->
                                <li class="{{ @active('student_seat_list_south') }}">
                                    <a href="/admin/student_seat_list_south" class="waves-effect"><span> 學員座位表(南投院區) </span></a>
                                </li>
                                @endif

                                @if( in_array('student_leave', $user_menu) )
                                <!-- 學員請假 -->
                                <li class="{{ @active('student_leave') }}">
                                    <a href="/admin/student_leave" class="waves-effect"><span> 學員請假 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_grade_rpt', $user_menu) )
                                <!-- 學員成績 -->
                                <li class="{{ @active('student_grade_rpt') }}">
                                    <a href="/admin/student_grade_rpt" class="waves-effect"><span> 學員成績 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_address_book', $user_menu) )
                                <!-- 學員通訊錄 -->
                                <li class="{{ @active('student_address_book') }}">
                                    <a href="/admin/student_address_book" class="waves-effect"><span> 學員通訊錄 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_card_record', $user_menu) )
                                <!-- 學員刷卡紀錄 -->
                                <li class="{{ @active('student_card_record') }}">
                                    <a href="/admin/student_card_record" class="waves-effect"><span> 學員刷卡紀錄 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_study_certificate', $user_menu) )
                                <!-- 學員研習證書 -->
                                <li class="{{ @active('student_study_certificate') }}">
                                    <a href="/admin/student_study_certificate" class="waves-effect"><span> 學員研習證書 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_mail_nametape', $user_menu) )
                                <!-- 學員郵寄名條 -->
                                <li class="{{ @active('student_mail_nametape') }}">
                                    <a href="/admin/student_mail_nametape" class="waves-effect"><span> 學員郵寄名條 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_training_record', $user_menu) )
                                <!-- 學員歷次受訓紀錄 -->
                                <li class="{{ @active('student_training_record') }}">
                                    <a href="/admin/student_training_record" class="waves-effect"><span> 學員歷次受訓紀錄 </span></a>
                                </li>
                                @endif

                                @if( in_array('menu-4-1-1', $user_menu) )
                                <?php $menuUnit = ['count_signin', 'count_participate', 'count_train', 'count_onjob_train'];?>
                                <!--人數統計-->
                                <li class="has_sub">
                                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 人數統計 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                                    <ul class="list-unstyled">

                                    	@if( in_array('count_signin', $user_menu) )
                                        <!-- 報到人數 -->
                                        <li class="{{ @active('count_signin') }}">
                                            <a href="/admin/count_signin" class="waves-effect"><span> 報到人數 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('count_participate', $user_menu) )
                                        <!-- 各機關參訓人數 -->
                                        <li class="{{ @active('count_participate') }}">
                                            <a href="/admin/count_participate" class="waves-effect"><span> 各機關參訓人數 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('count_train', $user_menu) )
                                        <!-- 訓練人數 -->
                                        <li class="{{ @active('count_train') }}">
                                            <a href="/admin/count_train" class="waves-effect"><span> 訓練人數 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('count_onjob_train', $user_menu) )
                                        <!-- 在職訓練人數 -->
                                        <li class="{{ @active('count_onjob_train') }}">
                                            <a href="/admin/count_onjob_train" class="waves-effect"><span> 在職訓練人數 </span></a>
                                        </li>
                                        @endif

                                    </ul>
                                </li>
                                @endif

                                @if( in_array('student_registration_comparison', $user_menu) )
                                <!-- 名額分配及需求填報對照表 -->
                                <li class="{{ @active('student_registration_comparison') }}">
                                    <a href="/admin/student_registration_comparison" class="waves-effect"><span> 名額分配及需求填報對照表 </span></a>
                                </li>
                                @endif

                                @if( in_array('organ_sid_comparison', $user_menu) )
                                <!-- 學員服務機關與學號對照表 -->
                                <li class="{{ @active('organ_sid_comparison') }}">
                                    <a href="/admin/organ_sid_comparison" class="waves-effect"><span> 學員服務機關與學號對照表 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_registration', $user_menu) )
	                            <!-- 學員報名表 -->
                                <!-- <li class="{{ @active('student_registration') }}">
                                    <a href="/admin/student_registration" class="waves-effect"><span> 學員報名表 以下沒有</span></a>
                                </li> -->
                                @endif

                                @if( in_array('organ_burden_detail', $user_menu) )
                                <!-- 各委託機關經費負擔明細表 -->
                                <li class="{{ @active('organ_burden_detail') }}">
                                    <a href="/admin/organ_burden_detail" class="waves-effect"><span> 各委託機關經費負擔明細表 </span></a>
                                </li>
                                @endif

                                @if( in_array('organ_address_letter', $user_menu) )
                                <!-- 委託機關地址條及函稿 -->
                                <li class="{{ @active('organ_address_letter') }}">
                                    <a href="/admin/organ_address_letter" class="waves-effect"><span> 委託機關地址條及函稿 </span></a>
                                </li>
                                @endif

                                @if( in_array('student_checklist', $user_menu) )
                                <!-- 學員資料檢核表 -->
                                <li class="{{ @active('student_checklist') }}">
                                    <a href="/admin/student_checklist" class="waves-effect"><span> 學員資料檢核表 </span></a>
                                </li>
                                @endif

                                @if( in_array('trainees_promotion_record', $user_menu) )
                                <!-- 培訓學員升遷異動紀錄 -->
                                <li class="{{ @active('trainees_promotion_record') }}">
                                    <a href="/admin/trainees_promotion_record" class="waves-effect"><span> 培訓學員升遷異動紀錄 </span></a>
                                </li>
                                @endif

                            </ul>
                        </li>
                        @endif

                    </ul>
                </li>
                @endif

                @if( in_array('menu-5', $user_menu) )
                {{-- 問卷調查 --}}
                <?php $menuUnit = ['effectiveness_survey', 'effectiveness_process', 'training_survey', 'training_process', 'train_quest_setting', 'notice_emai'];?>
                <li class="has_sub">
                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 問卷調查 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                    <ul class="list-unstyled">

                    	@if( in_array('effectiveness_survey', $user_menu) )
                        <!-- 成效問卷製作 -->
                        <li class="{{ @active('effectiveness_survey') }}">
                            <a href="/admin/effectiveness_survey" class="waves-effect"><span> 成效問卷製作 </span></a>
                        </li>
                        @endif

                        @if( in_array('effectiveness_process', $user_menu) )
                        <!-- 成效問卷處理(105) -->
                        <li class="{{ @active('effectiveness_process') }}">
                            <a href="/admin/effectiveness_process" class="waves-effect"><span> 成效問卷處理(105) </span></a>
                        </li>
                        @endif

                        <!-- 訓後問卷製作 -->
                        <!-- <li class="{{ @active('training_survey') }}">
                            <a href="/admin/training_survey" class="waves-effect"><span> 訓後問卷製作 </span></a>
                        </li> -->

                        <!-- 訓後問卷處理 -->
                        <!-- <li class="{{ @active('training_process') }}">
                            <a href="/admin/training_process" class="waves-effect"><span> 訓後問卷處理 </span></a>
                        </li> -->

                        @if( in_array('train_quest_setting', $user_menu) )
                        <!-- 訓後問卷處理 -->
                        <li class="{{ @active('train_quest_setting') }}">
                            <a href="/admin/trainQuestSetting" class="waves-effect"><span> 訓前訓中訓後問卷設定 </span></a>
                        </li>
                        @endif

                        @if( in_array('notice_emai', $user_menu) )
                        <!-- E-Mail線上問卷填答通知 -->
                        <li class="{{ @active('notice_emai') }}">
                            <a href="/admin/notice_emai" class="waves-effect"><span> E-Mail線上問卷填答通知 </span></a>
                        </li>
                        @endif

                        @if( in_array('menu-5-1', $user_menu) )
                        <?php $menuUnit = ['training_evaluation_105', 'training_result_105', 'class_result_105', 'yearly_statistic'
                        , 'lecture_satisfactionlist', 'participation_reason_statistics', 'class_support_comparison', 'training_evaluation_102_104'
                        , 'training_result_104', 'training_result_102', 'class_result_102_104', 'training_evaluation_96_101'
                        , 'training_result_96_101', 'class_result_96_101', 'training_evaluation_93_95', 'training_result_93_95'
                        , 'class_result_93_95', 'training_evaluation_90_92', 'training_result_90_92', 'class_result_90_92'
                        , 'training_evaluation_all', 'training_result_all'];?>
                        <!--報表列印-->
                        <li class="has_sub">
                            <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 報表列印 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                            <ul class="list-unstyled">

                            	@if( in_array('training_evaluation_105', $user_menu) )
                                <!-- 訓練成效評估表(105) -->
                                <li class="{{ @active('training_evaluation_105') }}">
                                    <a href="/admin/training_evaluation_105" class="waves-effect"><span> 訓練成效評估表(105) </span></a>
                                </li>
                                @endif

                                @if( in_array('training_result_105', $user_menu) )
                                <!-- 訓練成效評估結果統計圖表(105) -->
                                <li class="{{ @active('training_result_105') }}">
                                    <a href="/admin/training_result_105" class="waves-effect"><span> 訓練成效評估結果統計圖表(105) </span></a>
                                </li>
                                @endif

                                @if( in_array('class_result_105', $user_menu) )
                                <!-- 年度各班期訓練成效評估統計表(105) -->
                                <li class="{{ @active('class_result_105') }}">
                                    <a href="/admin/class_result_105" class="waves-effect"><span> 年度各班期訓練成效評估統計表(105) </span></a>
                                </li>
                                @endif

                                @if( in_array('yearly_statistic', $user_menu) )
                                <!-- 年度講座之滿意度統計表 -->
                                <li class="{{ @active('yearly_statistic') }}">
                                    <a href="/admin/yearly_statistic" class="waves-effect"><span> 年度講座之滿意度統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('lecture_satisfactionlist', $user_menu) )
                                <!-- 講座滿意度一覽表 -->
                                <li class="{{ @active('lecture_satisfactionlist') }}">
                                    <a href="/admin/lecture_satisfactionlist" class="waves-effect"><span> 講座滿意度一覽表 </span></a>
                                </li>
                                @endif

                                @if( in_array('class_support_comparison', $user_menu) )
                                <!-- 各班次行政支援成效比較表 -->
                                <li class="{{ @active('class_support_comparison') }}">
                                    <a href="/admin/class_support_comparison" class="waves-effect"><span> 各班次行政支援成效比較表 </span></a>
                                </li>
                                @endif

                                @if( in_array('participation_reason_statistics', $user_menu) )
                                <!-- 參訓原因統計 -->
                                <li class="{{ @active('participation_reason_statistics') }}">
                                    <a href="/admin/participation_reason_statistics" class="waves-effect"><span> 參訓原因統計 </span></a>
                                </li>
                                @endif

                                @if( in_array('menu-5-1-1', $user_menu) )
                                <?php $menuUnit = ['training_evaluation_102_104', 'training_result_104', 'training_result_102', 'class_result_102_104'];?>
                                <!--問卷(102~104)-->
                                <li class="has_sub">
                                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 問卷(102~104) </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                                    <ul class="list-unstyled">

                                    	@if( in_array('training_evaluation_102_104', $user_menu) )
                                        <!-- 訓練成效評估表 -->
                                        <li class="{{ @active('training_evaluation_102_104') }}">
                                            <a href="/admin/training_evaluation_102_104" class="waves-effect"><span> 訓練成效評估表 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('training_result_104', $user_menu) )
                                        <!-- 訓練成效評估結果統計圖表(104) -->
                                        <li class="{{ @active('training_result_104') }}">
                                            <a href="/admin/training_result_104" class="waves-effect"><span> 訓練成效評估結果統計圖表(104) </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('training_result_102', $user_menu) )
                                        <!-- 訓練成效評估結果統計圖表(102) -->
                                        <li class="{{ @active('training_result_102') }}">
                                            <a href="/admin/training_result_102" class="waves-effect"><span> 訓練成效評估結果統計圖表(102) </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('class_result_102_104', $user_menu) )
                                        <!-- 年度各班期訓練成效評估統計表 -->
                                        <li class="{{ @active('class_result_102_104') }}">
                                            <a href="/admin/class_result_102_104" class="waves-effect"><span> 年度各班期訓練成效評估統計表(102) </span></a>
                                        </li>
                                        @endif

                                    </ul>
                                </li>
                                @endif

                                @if( in_array('menu-5-1-2', $user_menu) )
                                <?php $menuUnit = ['training_evaluation_96_101', 'training_result_96_101', 'class_result_96_101'];?>
                                <!--問卷(96~101)-->
                                <li class="has_sub">
                                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 問卷(96~101) </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                                    <ul class="list-unstyled">

                                    	@if( in_array('training_evaluation_96_101', $user_menu) )
                                        <!-- 訓練成效評估表 -->
                                        <li class="{{ @active('training_evaluation_96_101') }}">
                                            <a href="/admin/training_evaluation_96_101" class="waves-effect"><span> 訓練成效評估表 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('training_result_96_101', $user_menu) )
                                        <!-- 訓練成效評估結果統計圖表 -->
                                        <li class="{{ @active('training_result_96_101') }}">
                                            <a href="/admin/training_result_96_101" class="waves-effect"><span> 訓練成效評估結果統計圖表 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('class_result_96_101', $user_menu) )
                                        <!-- 年度各班期訓練成效評估統計表 -->
                                        <li class="{{ @active('class_result_96_101') }}">
                                            <a href="/admin/class_result_96_101" class="waves-effect"><span> 年度各班期訓練成效評估統計表 </span></a>
                                        </li>
                                        @endif

                                    </ul>
                                </li>
                                @endif

                                @if( in_array('menu-5-1-3', $user_menu) )
                                <?php $menuUnit = ['training_evaluation_93_95', 'training_result_93_95', 'class_result_93_95'];?>
                                <!--問卷(93~95)-->
                                <li class="has_sub">
                                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 問卷(93~95) </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                                    <ul class="list-unstyled">

                                    	@if( in_array('training_evaluation_93_95', $user_menu) )
                                        <!-- 訓練成效評估表 -->
                                        <li class="{{ @active('training_evaluation_93_95') }}">
                                            <a href="/admin/training_evaluation_93_95" class="waves-effect"><span> 訓練成效評估表 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('training_result_93_95', $user_menu) )
                                        <!-- 訓練成效評估結果統計圖表 -->
                                        <li class="{{ @active('training_result_93_95') }}">
                                            <a href="/admin/training_result_93_95" class="waves-effect"><span> 訓練成效評估結果統計圖表 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('class_result_93_95', $user_menu) )
                                        <!-- 年度各班期訓練成效評估統計表 -->
                                        <li class="{{ @active('class_result_93_95') }}">
                                            <a href="/admin/class_result_93_95" class="waves-effect"><span> 年度各班期訓練成效評估統計表 </span></a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif

                                @if( in_array('menu-5-1-4', $user_menu) )
                                <?php $menuUnit = ['training_evaluation_90_92', 'training_result_90_92', 'class_result_90_92'];?>
                                <!--問卷(90~92)-->
                                <li class="has_sub">
                                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 問卷(90~92) </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                                    <ul class="list-unstyled">

                                    	@if( in_array('training_evaluation_90_92', $user_menu) )
                                        <!-- 訓練成效評估表 -->
                                        <li class="{{ @active('training_evaluation_90_92') }}">
                                            <a href="/admin/training_evaluation_90_92" class="waves-effect"><span> 訓練成效評估表 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('training_result_90_92', $user_menu) )
                                        <!-- 訓練成效評估結果統計圖表 -->
                                        <li class="{{ @active('training_result_90_92') }}">
                                            <a href="/admin/training_result_90_92" class="waves-effect"><span> 訓練成效評估結果統計圖表 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('class_result_90_92', $user_menu) )
                                        <!-- 年度各班期訓練成效評估統計表 -->
                                        <li class="{{ @active('class_result_90_92') }}">
                                            <a href="/admin/class_result_90_92" class="waves-effect"><span> 年度各班期訓練成效評估統計表 </span></a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif

                                @if( in_array('training_evaluation_all', $user_menu) )
                                <!-- 訓後成效評估表 -->
                                <li class="{{ @active('training_evaluation_all') }}">
                                    <a href="/admin/training_evaluation_all" class="waves-effect"><span> 訓後成效評估表 </span></a>
                                </li>
                                @endif

                                @if( in_array('training_result_all', $user_menu) )
                                <!-- 訓後成效評估結果統計圖表 -->
                                <li class="{{ @active('training_result_all') }}">
                                    <a href="/admin/training_result_all" class="waves-effect"><span> 訓後成效評估結果統計圖表 </span></a>
                                </li>
                                @endif

                            </ul>
                        </li>
                        @endif

                    </ul>
                </li>
                @endif

                @if( in_array('menu-6', $user_menu) )
                {{-- 場地管理 --}}
                <?php $menuUnit = ['session', 'site_survey', 'stay_list', 'class_control', 'site_survey_old','webbookplace', 'site_check', 'bookplace','classes_requirements', 'space_charges', 'roomset', 'time_setting', 'place', 'StudentRoomQuery', 'nantou_bedroom'];?>
                <li class="has_sub">
                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 場地管理 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                    <ul class="list-unstyled">

                        @if( in_array('classes_requirements', $user_menu) )
                       <!-- 辦班需求(確認)處理 -->
                        <li class="{{ @active('classes_requirements') }}">
                            <a href="/admin/classes_requirements" class="waves-effect"><span> 辦班需求(確認)處理 </span></a>
                        </li>
                        @endif

                    	@if( in_array('session', $user_menu) )
                        <!-- 會議資料處理 -->
                        <li class="{{ @active('session') }}">
                            <a href="/admin/session" class="waves-effect"><span> 會議資料處理 </span></a>
                        </li>
                        @endif

                        <!-- 控管辦班處理 -->
                        <!-- <li class="{{ @active('class_control') }}">
                            <a href="/admin/class_control" class="waves-effect"><span> 控管辦班處理 </span></a>
                        </li> -->

                        <!-- 早餐及住宿名單處理 -->
                        <!-- <li class="{{ @active('stay_list') }}">
                            <a href="/admin/stay_list" class="waves-effect"><span> 早餐及住宿名單處理 </span></a>
                        </li> -->

                        <!-- 場地審核處理 -->
                        <!-- <li class="{{ @active('site_check') }}">
                            <a href="/admin/site_check" class="waves-effect"><span> 場地審核處理 </span></a>
                        </li> -->

                        @if( in_array('bookplace', $user_menu) )
                        <!-- 場地預約處理 -->
                        <li class="{{ @active('bookplace') }}">
                            <a href="/admin/bookplace/index" class="waves-effect"><span> 場地預約處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('webbookplace', $user_menu) )
                        <!--網路預約場地審核處理-->
                        <li class="{{ @active('webbookplace') }}">
                            <a href="/admin/webbookplace" class="waves-effect"><span> 網路預約場地審核處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('space_charges', $user_menu) )
                        <!--場地收費(南投院區)-->
                        <li class="{{ @active('space_charges') }}">
                            <a href="/admin/space_charges" class="waves-effect"><span> 場地收費(南投院區) </span></a>
                        </li>
                        @endif

                        @if( in_array('roomset', $user_menu) )
                        <!--寢室床位安排(南投院區)-->
                        <li class="{{ @active('roomset') }}">
                            <a href="/admin/roomset" class="waves-effect"><span> 寢室床位安排(南投院區) </span></a>
                        </li>
                        @endif

                        @if( in_array('StudentRoomQuery', $user_menu) )
                        <!--學員寢室床位查詢-->
                        <li class="{{ @active('StudentRoomQuery') }}">
                            <a href="/admin/StudentRoomQuery" class="waves-effect"><span> 學員寢室床位查詢 </span></a>
                        </li>
                        @endif

                        @if( in_array('time_setting', $user_menu) )
                        <!--時段設定-->
                        <li class="{{ @active('time_setting') }}">
                            <a href="/admin/time_setting" class="waves-effect"><span> 時段設定 </span></a>
                        </li>
                        @endif

                        @if( in_array('place', $user_menu) )
                        <!-- 場地資料維護 -->
                        <li class="{{ @active('place') }}">
                            <a href="/admin/place" class="waves-effect"><span> 場地資料維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('place_nantou', $user_menu) )
                        <!--場地收費(南投院區)-->
                        <li class="{{ @active('place_nantou') }}">
                            <a href="/admin/place_nantou" class="waves-effect"><span> 場地資料維護(南投院區) </span></a>
                        </li>
                        @endif

                        @if( in_array('place_nantou', $user_menu) )
                        <!--場地收費(南投院區)-->
                        <li class="{{ @active('nantou_bedroom') }}">
                            <a href="/admin/nantou_bedroom" class="waves-effect"><span> 寢室資料維護(南投院區) </span></a>
                        </li>
                        @endif

                        @if( in_array('site_survey', $user_menu) )
                        <!-- 場地問卷處理(101) -->
                        <li class="{{ @active('site_survey') }}">
                            <a href="/admin/site_survey" class="waves-effect"><span> 場地問卷處理(101) </span></a>
                        </li>
                        @endif

                        @if( in_array('site_survey_old', $user_menu) )
                        <!-- 場地問卷處理(96~100) -->
                        <li class="{{ @active('site_survey_old') }}">
                            <a href="/admin/site_survey_old" class="waves-effect"><span> 場地問卷處理(96~100) </span></a>
                        </li>
                        @endif

                        @if( in_array('menu-6-1', $user_menu) )
                        <?php $menuUnit = ['use_apply_process', 'apply_process_query', 'use_return_maintainance', 'use_happening_list',
                         'borrow_return_apply', 'train_dining_living', 'monthly_demand', 'weekly_confirm', 'daily_distribution_classroom',
                         'daily_distribution_conference', 'daily_distribution_living', 'daily_distribution_dining', 'manage_monthly_classroom', 'manage_monthly_conference',
                         'manage_monthly_living', 'manage_monthly_dining', 'manage_monthly_site', 'breakfast_living_student', 'breakfast_living_worker',
                         'site_survey_101', 'site_survey_96_100', 'conference_use_statistics', 'class_dining_living', 'monthly_money',
                          'dining_table'];?>
                        <!--報表列印-->
                        <li class="has_sub">
                            <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 報表列印 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                            <ul class="list-unstyled">

                            	@if( in_array('monthly_demand', $user_menu) )
                                <!-- 每月需求表 -->
                                <li class="{{ @active('monthly_demand') }}">
                                    <a href="/admin/monthly_demand" class="waves-effect"><span> 每月需求表 </span></a>
                                </li>
                                @endif

                                @if( in_array('weekly_confirm', $user_menu) )
                                <!-- 每周確認表 -->
                                <li class="{{ @active('weekly_confirm') }}">
                                    <a href="/admin/weekly_confirm" class="waves-effect"><span> 每周確認表 </span></a>
                                </li>
                                @endif

                                @if( in_array('dining_table', $user_menu) )
                                <!-- 用餐人數概況表 -->
                                <li class="{{ @active('dining_table') }}">
                                    <a href="/admin/dining_table" class="waves-effect"><span> 用餐人數概況表 </span></a>
                                </li>
                                @endif

                                @if( in_array('menu-6-1-1', $user_menu) )
                                <?php $menuUnit = ['daily_distribution_classroom', 'daily_distribution_conference', 'daily_distribution_living', 'daily_distribution_dining'];?>
                                <!--每日分配表-->
                                <li class="has_sub">
                                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 每日分配表 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                                    <ul class="list-unstyled">

                                    	@if( in_array('daily_distribution_classroom', $user_menu) )
                                        <!-- 教室場地 -->
                                        <li class="{{ @active('daily_distribution_classroom') }}">
                                            <a href="/admin/daily_distribution_classroom" class="waves-effect"><span> 教室場地 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('daily_distribution_conference', $user_menu) )
                                        <!-- 會議場地 -->
                                        <li class="{{ @active('daily_distribution_conference') }}">
                                            <a href="/admin/daily_distribution_conference" class="waves-effect"><span> 會議場地 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('daily_distribution_living', $user_menu) )
                                        <!-- 住宿及休閒設施 -->
                                        <li class="{{ @active('daily_distribution_living') }}">
                                            <a href="/admin/daily_distribution_living" class="waves-effect"><span> 住宿及休閒設施 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('daily_distribution_dining', $user_menu) )
                                        <!-- 用餐數量 -->
                                        <li class="{{ @active('daily_distribution_dining') }}">
                                            <a href="/admin/daily_distribution_dining" class="waves-effect"><span> 用餐數量 </span></a>
                                        </li>
                                        @endif

                                    </ul>
                                </li>
                                @endif

                                @if( in_array('menu-6-1-2', $user_menu) )
                                <?php $menuUnit = ['manage_monthly_classroom', 'manage_monthly_conference',
                                'manage_monthly_living', 'manage_monthly_dining', 'manage_monthly_site'];?>
                                <!--管理月報表-->
                                <li class="has_sub">
                                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 管理月報表 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                                    <ul class="list-unstyled">

                                    	@if( in_array('manage_monthly_classroom', $user_menu) )
                                        <!-- 教室場地 -->
                                        <li class="{{ @active('manage_monthly_classroom') }}">
                                            <a href="/admin/manage_monthly_classroom" class="waves-effect"><span> 教室場地 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('manage_monthly_living', $user_menu) )
                                        <!-- 住宿及休閒設施 -->
                                        <li class="{{ @active('manage_monthly_living') }}">
                                            <a href="/admin/manage_monthly_living" class="waves-effect"><span> 寢室住宿及休閒設施 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('manage_monthly_dining', $user_menu) )
                                        <!-- 用餐數量 -->
                                        <li class="{{ @active('manage_monthly_dining') }}">
                                            <a href="/admin/manage_monthly_dining" class="waves-effect"><span> 用餐數量 </span></a>
                                        </li>
                                        @endif

                                        @if( in_array('manage_monthly_conference', $user_menu) )
                                        <!-- 會議場地 -->
                                        <li class="{{ @active('manage_monthly_conference') }}">
                                            <a href="/admin/manage_monthly_conference" class="waves-effect"><span> 場地使用 </span></a>
                                        </li>
                                        @endif

                                    </ul>
                                </li>
                                @endif

                                @if( in_array('site_survey_101', $user_menu) )
                                <!-- 場地問卷與統計表(101) -->
                                <li class="{{ @active('site_survey_101') }}">
                                    <a href="/admin/site_survey_101" class="waves-effect"><span> 場地問卷與統計表(101) </span></a>
                                </li>
                                @endif

                                @if( in_array('conference_use_statistics', $user_menu) )
	                            <!-- 會議場地使用統計表 -->
                                <li class="{{ @active('conference_use_statistics') }}">
                                    <a href="/admin/conference_use_statistics" class="waves-effect"><span> 會議場地使用統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('monthly_money', $user_menu) )
                                <!-- 各月份費用統計表 -->
                                <li class="{{ @active('monthly_money') }}">
                                    <a href="/admin/monthly_money" class="waves-effect"><span> 各月份費用統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('dinner_survey', $user_menu) )
                                <!-- N15 不用晚餐調查表 -->
                                <li class="{{ @active('dinner_survey') }}">
                                    <a href="/admin/dinner_survey" class="waves-effect"><span> 不用晚餐調查表 </span></a>
                                </li>
                                @endif

                                @if( in_array('breakfast_statics', $user_menu) )
                                <!-- N16 兩週班以上週一用早餐統計表 -->
                                <li class="{{ @active('breakfast_statics') }}">
                                    <a href="/admin/breakfast_statics" class="waves-effect"><span> 兩週班以上週一用早餐統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('food_expense_writeoff', $user_menu) )
                                <!-- N17 伙食費核銷明細表 -->
                                <li class="{{ @active('food_expense_writeoff') }}">
                                    <a href="/admin/food_expense_writeoff" class="waves-effect"><span> 伙食費核銷明細表 </span></a>
                                </li>
                                @endif

                                @if( in_array('food_expense_writeoff_summary', $user_menu) )
                                <!-- N18 伙食費核銷總表 -->
                                <li class="{{ @active('food_expense_writeoff_summary') }}">
                                    <a href="/admin/food_expense_writeoff_summary" class="waves-effect"><span> 伙食費核銷總表 </span></a>
                                </li>
                                @endif

                                @if( in_array('stay_registration', $user_menu) )
                                <!-- N19 住宿登記概況表 -->
                                <li class="{{ @active('stay_registration') }}">
                                    <a href="/admin/stay_registration" class="waves-effect"><span> 住宿登記概況表 </span></a>
                                </li>
                                @endif

                                @if( in_array('staylist_byfloor', $user_menu) )
                                <!-- N20 各樓住宿班次人員一覽表 -->
                                <li class="{{ @active('staylist_byfloor') }}">
                                    <a href="/admin/staylist_byfloor" class="waves-effect"><span> 各樓住宿班次人員一覽表 </span></a>
                                </li>
                                @endif

                                @if( in_array('stay_distribution_dutylist', $user_menu) )
                                <!-- N21 學員住宿分配暨輔導員執勤表 -->
                                <li class="{{ @active('stay_distribution_dutylist') }}">
                                    <a href="/admin/stay_distribution_dutylist" class="waves-effect"><span> 學員住宿分配暨輔導員執勤表 </span></a>
                                </li>
                                @endif

                                @if( in_array('stay_distribution_byclass', $user_menu) )
                                <!-- N22 學員住宿分配一覽表(分班) -->
                                <li class="{{ @active('stay_distribution_byclass') }}">
                                    <a href="/admin/stay_distribution_byclass" class="waves-effect"><span> 學員住宿分配一覽表(分班) </span></a>
                                </li>
                                @endif

                                @if( in_array('bedroom_distribution', $user_menu) )
                                <!-- N23 寢室分配情形一覽表 -->
                                <li class="{{ @active('bedroom_distribution') }}">
                                    <a href="/admin/bedroom_distribution" class="waves-effect"><span> 寢室分配情形一覽表 </span></a>
                                </li>
                                @endif

                                @if( in_array('class_status', $user_menu) )
                                <!-- N24 開辦班次概況表(含住宿) -->
                                <li class="{{ @active('class_status') }}">
                                    <a href="/admin/class_status" class="waves-effect"><span> 開辦班次概況表(含住宿) </span></a>
                                </li>
                                @endif

                                @if( in_array('bedding_laundry_statics', $user_menu) )
                                <!-- N25 寢具洗滌數量統計表 -->
                                <li class="{{ @active('bedding_laundry_statics') }}">
                                    <a href="/admin/bedding_laundry_statics" class="waves-effect"><span> 寢具洗滌數量統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('stay_statics_after_reg', $user_menu) )
                                <!-- N26 住宿統計表(報到後) -->
                                <li class="{{ @active('stay_statics_after_reg') }}">
                                    <a href="/admin/stay_statics_after_reg" class="waves-effect"><span> 住宿統計表(報到後) </span></a>
                                </li>
                                @endif

                                @if( in_array('level9_list', $user_menu) )
                                <!-- N27 薦任9職等主管名單 -->
                                <li class="{{ @active('level9_list') }}">
                                    <a href="/admin/level9_list" class="waves-effect"><span> 薦任9職等主管名單 </span></a>
                                </li>
                                @endif

                                @if( in_array('room_card_box_label', $user_menu) )
                                <!-- N28 房卡盒標示紙 -->
                                <li class="{{ @active('room_card_box_label') }}">
                                    <a href="/admin/room_card_box_label" class="waves-effect"><span> 房卡盒標示紙 </span></a>
                                </li>
                                @endif

                                @if( in_array('room_card_receipt', $user_menu) )
                                <!-- N29 房卡簽收單 -->
                                <li class="{{ @active('room_card_receipt') }}">
                                    <a href="/admin/room_card_receipt" class="waves-effect"><span> 房卡簽收單 </span></a>
                                </li>
                                @endif

                                @if( in_array('loan_bedding_laundry_statics', $user_menu) )
                                <!-- N30 借住寢室寢具洗滌數量統計表 -->
                                <li class="{{ @active('loan_bedding_laundry_statics') }}">
                                    <a href="/admin/loan_bedding_laundry_statics" class="waves-effect"><span> 借住寢室寢具洗滌數量統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('site_using_list', $user_menu) )
                                <!-- N31 場地借用行事曆 -->
                                <li class="{{ @active('site_using_list') }}">
                                    <a href="/admin/site_using_list" class="waves-effect"><span> 場地借用行事曆 </span></a>
                                </li>
                                @endif

                                @if( in_array('site_usage_statics', $user_menu) )
                                <!-- N32 場地使用成效統計表 -->
                                <li class="{{ @active('site_usage_statics') }}">
                                    <a href="/admin/site_usage_statics" class="waves-effect"><span> 場地使用成效統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('site_using_maintain_datail', $user_menu) )
                                <!-- N33 場地借用維護費收入明細統計表 -->
                                <li class="{{ @active('site_using_maintain_datail') }}">
                                    <a href="/admin/site_using_maintain_datail" class="waves-effect"><span> 場地借用維護費收入明細統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('site_application', $user_menu) )
                                <!-- N34 場地借用申請表 -->
                                <li class="{{ @active('site_application') }}">
                                    <a href="/admin/site_application" class="waves-effect"><span> 場地借用申請表 </span></a>
                                </li>
                                @endif

                                @if( in_array('site_using_maintain_all', $user_menu) )
                                <!-- N35 各場地借用情形及維護費收入統計表 -->
                                <li class="{{ @active('site_using_maintain_all') }}">
                                    <a href="/admin/site_using_maintain_all" class="waves-effect"><span> 各場地借用情形及維護費收入統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('site_using_overview', $user_menu) )
                                <!-- N36 場地借用概況表 -->
                                <li class="{{ @active('site_using_overview') }}">
                                    <a href="/admin/site_using_overview" class="waves-effect"><span> 場地借用概況表 </span></a>
                                </li>
                                @endif

                                @if( in_array('site_survey_96_100', $user_menu) )
                                <!-- 場地問卷與統計表(96~100) -->
                                <li class="{{ @active('site_survey_96_100') }}">
                                    <a href="/admin/site_survey_96_100" class="waves-effect"><span> 場地問卷與統計表(96~100) </span></a>
                                </li>
                                @endif


                                <!-- <?php $menuUnit = ['breakfast_living_student', 'breakfast_living_worker'];?>
                                <li class="has_sub">
                                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 早餐及住宿籤名單 以下沒有</span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                                    <ul class="list-unstyled">
                                        <li class="{{ @active('breakfast_living_student') }}">
                                            <a href="/admin/breakfast_living_student" class="waves-effect"><span> 學員 </span></a>
                                        </li>

                                        <li class="{{ @active('breakfast_living_worker') }}">
                                            <a href="/admin/breakfast_living_worker" class="waves-effect"><span> 講座工作人員 </span></a>
                                        </li>

                                    </ul>
                                </li> -->

                                @if( in_array('class_dining_living', $user_menu) )
                                <!-- 各班期用餐及住宿統計表 -->
                                <li class="{{ @active('class_dining_living') }}">
                                    <a href="/admin/class_dining_living" class="waves-effect"><span> 各班期用餐及住宿統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('manage_monthly_site', $user_menu) )
                                <!-- 場地使用管理月報表 -->
                                <li class="{{ @active('manage_monthly_site') }}">
                                    <a href="/admin/manage_monthly_site" class="waves-effect"><span> 場地使用管理月報表 </span></a>
                                </li>
                                @endif

                                @if( in_array('use_apply_process', $user_menu) )
	                            <!-- 填寫領用申請處理 -->
                                <li class="{{ @active('use_apply_process') }}">
                                    <a href="/admin/use_apply_process" class="waves-effect"><span> 填寫領用申請處理 </span></a>
                                </li>
                                @endif

                                @if( in_array('apply_process_query', $user_menu) )
                                <!-- 申請流程查詢作業 -->
                                <li class="{{ @active('apply_process_query') }}">
                                    <a href="/admin/apply_process_query" class="waves-effect"><span> 申請流程查詢作業 </span></a>
                                </li>
                                @endif

                                @if( in_array('use_return_maintainance', $user_menu) )
                                <!-- 領用歸還資料維護 -->
                                <li class="{{ @active('use_return_maintainance') }}">
                                    <a href="/admin/use_return_maintainance" class="waves-effect"><span> 領用歸還資料維護 </span></a>
                                </li>
                                @endif

                                @if( in_array('use_happening_list', $user_menu) )
                                <!-- 領用情形一覽表 -->
                                <li class="{{ @active('use_happening_list') }}">
                                    <a href="/admin/use_happening_list" class="waves-effect"><span> 領用情形一覽表 </span></a>
                                </li>
                                @endif

                                @if( in_array('borrow_return_apply', $user_menu) )
                                <!-- 借用歸還申請 -->
                                <li class="{{ @active('borrow_return_apply') }}">
                                    <a href="/admin/borrow_return_apply" class="waves-effect"><span> 借用歸還申請 </span></a>
                                </li>
                                @endif

                                @if( in_array('train_dining_living', $user_menu) )
                                <!-- 各訓練班期教室場地用餐及住宿 -->
                                <li class="{{ @active('train_dining_living') }}">
                                    <a href="/admin/train_dining_living" class="waves-effect"><span> 各訓練班期教室場地用餐及住宿 </span></a>
                                </li>
                                @endif


                            </ul>
                        </li>
                        @endif

                    </ul>
                </li>
                @endif

                @if( in_array('menu-7', $user_menu) )
                {{-- 教材交印處理 --}}
                <?php $menuUnit = ['teaching_material_print', 'teaching_material_statistics', 'teaching_material_form', 'teaching_material_statistics_rpt'];?>
                <li class="has_sub">
                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 教材交印 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                    <ul class="list-unstyled">

                    	@if( in_array('teaching_material_print', $user_menu) )
                        <!-- 教材交印資料處理 -->
                        <li class="{{ @active('teaching_material_print') }}">
                            <a href="/admin/teaching_material_print" class="waves-effect"><span> 教材交印資料處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('teaching_material_statistics', $user_menu) )
                        <!-- 教材印製統計處理 -->
                        <li class="{{ @active('teaching_material_statistics') }}">
                            <a href="/admin/teaching_material_statistics" class="waves-effect"><span> 教材印製統計處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('menu-7-1', $user_menu) )
                        <?php $menuUnit = ['teaching_material_form', 'teaching_material_statistics_rpt'];?>
                        <!--報表列印-->
                        <li class="has_sub">
                            <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 報表列印 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                            <ul class="list-unstyled">

                                @if( in_array('teaching_material_statistics_rpt', $user_menu) )
                                <!-- 教材交印統計表 -->
                                <li class="{{ @active('teaching_material_statistics_rpt') }}">
                                    <a href="/admin/teaching_material_statistics_rpt" class="waves-effect"><span> 教材交印統計表 </span></a>
                                </li>
                                @endif

                                @if( in_array('teaching_material_form', $user_menu) )
                                <!-- 教材交印單 -->
                                <li class="{{ @active('teaching_material_form') }}">
                                    <a href="/admin/teaching_material_form" class="waves-effect"><span> 教材交印單 </span></a>
                                </li>
                                @endif

                            </ul>
                        </li>
                        @endif

                    </ul>
                </li>
                @endif

                @if( in_array('menu-8', $user_menu) )
                {{-- 例行業務 --}}
                <?php $menuUnit = ['training_organ', 'yearly_teaching_material', 'class_teaching_material', 'lecture_teaching_material',
                        'course_teaching_material', 'receipt_inventory'];?>
                <li class="has_sub">
                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 例行業務 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                    <ul class="list-unstyled">

                        <!-- 班別教材資料處理 -->
                        <!-- <li class="{{ @active('class_material') }}">
                            <a href="/admin/class_material" class="waves-effect"><span> 班別教材資料處理 </span></a>
                        </li> -->

                        <!-- 講座授課及教材資料查詢 -->
                        <!-- <li class="{{ @active('print') }}">
                            <a href="/admin/print" class="waves-effect"><span> 講座授課及教材資料查詢 </span></a>
                        </li> -->

                        @if( in_array('menu-8-1', $user_menu) )
                        <?php $menuUnit = ['training_organ', 'yearly_teaching_material', 'class_teaching_material', 'lecture_teaching_material',
                        'course_teaching_material', 'receipt_inventory'];?>
                        <!--報表列印-->
                        <li class="has_sub">
                            <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 報表列印 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                            <ul class="list-unstyled">

                                @if( in_array('training_organ', $user_menu) )
                                <!-- 訓練機構基本資料表 -->
                                <li class="{{ @active('training_organ') }}">
                                    <a href="/admin/training_organ" class="waves-effect"><span> 訓練機構基本資料表 </span></a>
                                </li>
                                @endif

                                @if( in_array('yearly_teaching_material', $user_menu) )
                                <!-- 年度教材總清冊 -->
                                <li class="{{ @active('yearly_teaching_material') }}">
                                    <a href="/admin/yearly_teaching_material" class="waves-effect"><span> 年度教材總清冊 </span></a>
                                </li>
                                @endif

                                @if( in_array('class_teaching_material', $user_menu) )
                                <!-- 班別教材一覽表 -->
                                <li class="{{ @active('class_teaching_material') }}">
                                    <a href="/admin/class_teaching_material" class="waves-effect"><span> 班別教材一覽表 </span></a>
                                </li>
                                @endif

                                @if( in_array('lecture_teaching_material', $user_menu) )
                                <!-- 講座教材一覽表 -->
                                <li class="{{ @active('lecture_teaching_material') }}">
                                    <a href="/admin/lecture_teaching_material" class="waves-effect"><span> 講座教材一覽表 </span></a>
                                </li>
                                @endif

                                @if( in_array('course_teaching_material', $user_menu) )
                                <!-- 開班教材清單 -->
                                <li class="{{ @active('course_teaching_material') }}">
                                    <a href="/admin/course_teaching_material" class="waves-effect"><span> 開班教材清單 </span></a>
                                </li>
                                @endif

                                <!-- 收據清冊 -->
                                <!-- <li class="{{ @active('receipt_inventory') }}">
                                    <a href="/admin/receipt_inventory" class="waves-effect"><span> 收據清冊 這個沒有</span></a>
                                </li> -->

                            </ul>
                        </li>
                        @endif

                    </ul>
                </li>
                @endif

                @if( in_array('menu-9', $user_menu) )
                {{-- 巡迴研習 --}}
                <?php $menuUnit = ['itineracy', 'itineracy_theme', 'itineracy_unit', 'itineracy_annual', 'itineracy_surveylogin', 'itineracy_schedule'];?>
                <li class="has_sub">
                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 巡迴研習 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                    <ul class="list-unstyled">

                    	@if( in_array('itineracy', $user_menu) )
                        <!-- 巡迴研習類別 -->
                        <li class="{{ @active('itineracy') }}">
                            <a href="/admin/itineracy" class="waves-effect"><span> 巡迴研習類別 </span></a>
                        </li>
                        @endif

                        @if( in_array('itineracy_theme', $user_menu) )
                        <!-- 巡迴研習主題 -->
                        <li class="{{ @active('itineracy_theme') }}">
                            <a href="/admin/itineracy_theme" class="waves-effect"><span> 巡迴研習主題 </span></a>
                        </li>
                        @endif

                        @if( in_array('itineracy_unit', $user_menu) )
                        <!-- 巡迴研習單元 -->
                        <li class="{{ @active('itineracy_unit') }}">
                            <a href="/admin/itineracy_unit" class="waves-effect"><span> 巡迴研習單元 </span></a>
                        </li>
                        @endif

                        @if( in_array('itineracy_annual', $user_menu) )
                        <!-- 年度主題設定 -->
                        <li class="{{ @active('itineracy_annual') }}">
                            <a href="/admin/itineracy_annual" class="waves-effect"><span> 年度主題設定 </span></a>
                        </li>
                        @endif

                        @if( in_array('itineracy_surveylogin', $user_menu) )
                        <!-- 巡迴研習需求調查登錄 -->
                        <li class="{{ @active('itineracy_surveylogin') }}">
                            <a href="/admin/itineracy_surveylogin" class="waves-effect"><span>巡迴研習需求調查登錄</span></a>
                        </li>
                        @endif

                        @if( in_array('itineracy_schedule', $user_menu) )
                        <!-- 實施日程表 -->
                        <li class="{{ @active('itineracy_schedule') }}">
                            <a href="/admin/itineracy_schedule" class="waves-effect"><span> 實施日程表 </span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if( in_array('menu-10', $user_menu) )
                {{-- 資料匯出作業 --}}
                <?php $menuUnit = ['dataexport','entryexport','libraryexport'];?>
                <li class="has_sub">
                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 資料匯出 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                    <ul class="list-unstyled">

                    	@if( in_array('dataexport', $user_menu) )
                        <!-- 資料匯出處理 -->
                        <li class="{{ @active('dataexport') }}">
                            <a href="/admin/dataexport/index" class="waves-effect"><span> 資料匯出處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('entryexport', $user_menu) )
                        <!-- 入口網站資料匯出 -->
                        <li class="{{ @active('entryexport') }}">
                            <a href="/admin/entryexport" class="waves-effect"><span> 入口網站資料匯出 </span></a>
                        </li>
                        @endif

                        @if( in_array('libraryexport', $user_menu) )
                        <!-- 圖書系統匯出 -->
                        <li class="{{ @active('libraryexport') }}">
                            <a href="/admin/libraryexport" class="waves-effect"><span> 圖書系統匯出 </span></a>
                        </li>
                        @endif


                    </ul>
                </li>
                @endif

                @if( in_array('menu-11', $user_menu) )
                {{-- 進修業務 --}}
                <?php $menuUnit = ['people_statistics'];?>
                <li class="has_sub">
                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 進修業務 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                    <ul class="list-unstyled">

                    	@if( in_array('menu-11-1', $user_menu) )
                        <?php $menuUnit = ['people_statistics'];?>
                        <!--報表列印-->
                        <li class="has_sub">
                            <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 報表列印 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                            <ul class="list-unstyled">

                            	@if( in_array('people_statistics', $user_menu) )
                                <!-- 人數統計表 -->
                                <li class="{{ @active('people_statistics') }}">
                                    <a href="/admin/people_statistics" class="waves-effect"><span> 人數統計表 </span></a>
                                </li>
                                @endif

                            </ul>
                        </li>
                        @endif

                    </ul>
                </li>
                @endif

                {{-- 網頁維護 --}}
                <!-- <?php $menuUnit = ['password_maintenance', 'password_maintenance_user', 'news_tw', 'news_en', 'train', 'site', 'forum', 'poll'];?> -->
                 <!--<li class="has_sub">
                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 網頁維護 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                    <ul class="list-unstyled"> -->

                        <!-- 機關個人密碼維護 -->
                        <!-- <li class="{{ @active('password_maintenance') }}">
                            <a href="/admin/password_maintenance" class="waves-effect"><span> 機關個人密碼維護</span></a>
                        </li> -->

                        <!-- 個人密碼維護 -->
                        <!-- <li class="{{ @active('password_maintenance_user') }}">
                            <a href="/admin/password_maintenance_user" class="waves-effect"><span> 個人密碼維護</span></a>
                        </li> -->

                        <!-- 中文最新消息維護 -->
                        <!-- <li class="{{ @active('news_tw') }}">
                            <a href="/admin/news_tw" class="waves-effect"><span> 中文最新消息維護</span></a>
                        </li> -->

                        <!-- 英文最新消息維護 -->
                        <!-- <li class="{{ @active('news_en') }}">
                            <a href="/admin/news_en" class="waves-effect"><span> 英文最新消息維護</span></a>
                        </li> -->

                        <!-- 訓練班期公告 -->
                        <!-- <li class="{{ @active('train') }}">
                            <a href="/admin/train" class="waves-effect"><span> 訓練班期公告</span></a>
                        </li> -->

                        <!-- 洽借場地班期公告 -->
                        <!-- <li class="{{ @active('site') }}">
                            <a href="/admin/site" class="waves-effect"><span> 洽借場地班期公告</span></a>
                        </li> -->

                        <!-- 人資發展論壇 -->
                        <!-- <li class="{{ @active('forum') }}">
                            <a href="/admin/forum" class="waves-effect"><span> 人資發展論壇</span></a>
                        </li> -->

                        <!-- 網路民調維護 -->
                        <!-- <li class="{{ @active('poll') }}">
                            <a href="/admin/poll" class="waves-effect"><span> 網路民調維護</span></a>
                        </li> -->

                    <!-- </ul>
                </li> -->

                @if( in_array('menu-12', $user_menu) )
                <?php $menuUnit = ['role_simulate', 'user_group', 'system_account', 'program','program_search', 'holiday', 'institution', 'recommend', 'recommend_user','agency', 'users', 'system_code', 'system_parameter','web_portal','reportmg', 'class_process','teaching_material_maintain'];?>
                <li class="has_sub">
                    <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 系統維護 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                    <ul class="list-unstyled">

                        @if( in_array('role_simulate', $user_menu) )
                        <!-- 角色模擬 -->
                        <li class="{{ @active('role_simulate') }}">
                            <a href="/admin/role_simulate" class="waves-effect"><span> 角色模擬 </span></a>
                        </li>
                        @endif

                    	@if( in_array('user_group', $user_menu) )
                        <!-- 權限群組維護 -->
                        <li class="{{ @active('user_group') }}">
                            <a href="/admin/user_group" class="waves-effect"><span> 權限群組維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('system_account', $user_menu) )
                        <!-- 系統帳號維護 -->
                        <li class="{{ @active('system_account') }}">
                            <a href="/admin/system_account" class="waves-effect"><span> 系統帳號維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('program', $user_menu) )
                        <!-- 異動紀錄設定 -->
                        <li class="{{ @active('program') }}">
                            <a href="/admin/program" class="waves-effect"><span> 異動紀錄設定 </span></a>
                        </li>
                        @endif

                        @if( in_array('program_search', $user_menu) )
                        <!-- 異動記錄查詢 -->
                        <li class="{{ @active('program_search') }}">
                            <a href="/admin/program_search" class="waves-effect"><span> 異動記錄查詢 </span></a>
                        </li>
                        @endif

                        @if( in_array('holiday', $user_menu) )
                        <!-- 國定假日維護 -->
                        <li class="{{ @active('holiday') }}">
                            <a href="/admin/holiday" class="waves-effect"><span> 國定假日維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('institution', $user_menu) )
                        <!-- 機關資料維護 -->
                        <li class="{{ @active('institution') }}">
                            <a href="/admin/institution" class="waves-effect"><span> 機關資料維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('recommend', $user_menu) )
                        <!-- 薦送機關維護 -->
                        <li class="{{ @active('recommend') }}">
                            <a href="/admin/recommend" class="waves-effect"><span> 薦送機關維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('recommend_user', $user_menu) )
                        <!-- 機關個人帳號維護 -->
                        <li class="{{ @active('recommend_user') }}">
                            <a href="/admin/recommend_user" class="waves-effect"><span> 機關個人帳號維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('agency', $user_menu) )
                        <!-- 訓練機構資料維護 -->
                        <li class="{{ @active('agency') }}">
                            <a href="/admin/agency" class="waves-effect"><span> 訓練機構資料維護 </span></a>
                        </li>
                        @endif

                        <!-- 個人帳號維護 -->
                        <!-- <li class="{{ @active('users') }}">
                            <a href="/admin/users" class="waves-effect"><span> 個人帳號維護 </span></a>
                        </li> -->

                        @if( in_array('system_code', $user_menu) )
                        <!-- 系統代碼維護 -->
                        <li class="{{ @active('system_code') }}">
                            <a href="/admin/system_code" class="waves-effect"><span> 系統代碼維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('system_parameter', $user_menu) )
                        <!-- 系統參數維護 -->
                        <li class="{{ @active('system_parameter') }}">
                            <a href="/admin/system_parameter/edit" class="waves-effect"><span> 系統參數維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('web_portal', $user_menu) )
                        <!-- 入口網站代碼維護 -->
                        <li class="{{ @active('web_portal') }}">
                            <a href="/admin/web_portal" class="waves-effect"><span> 入口網站代碼維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('teaching_material_maintain', $user_menu) )
                        <!-- 教材交印參數處理 -->
                        <li class="{{ @active('teaching_material_maintain') }}">
                            <a href="/admin/teaching_material_maintain" class="waves-effect"><span> 教材交印參數處理 </span></a>
                        </li>
                        @endif

                        @if( in_array('class_process', $user_menu) )
                        <!-- 班務流程指引維護 -->
                        <li class="{{ @active('class_process') }}">
                            <a href="/admin/class_process" class="waves-effect"><span> 班務流程指引維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('reportmg', $user_menu) )
                        <!-- 重要訊息維護 -->
                        <li class="{{ @active('reportmg') }}">
                            <a href="/admin/reportmg" class="waves-effect"><span> 重要訊息維護 </span></a>
                        </li>
                        @endif

                        @if( in_array('menu-12-1', $user_menu) )
                        <?php $menuUnit = ['change_organ_contact','production_book_system'];?>
                        <!--報表列印-->
                        <li class="has_sub">
                            <a class="waves-effect {{ @subdrop($menuUnit) }}"><span> 報表列印 </span><span class="pull-right"><i class="md {{ @removeoradd($menuUnit) }}"></i></span></a>
                            <ul class="list-unstyled">

                            	@if( in_array('change_organ_contact', $user_menu) )
                                <!-- 調訓機關承辦人員聯絡名冊 -->
                                <li class="{{ @active('change_organ_contact') }}">
                                    <a href="/admin/change_organ_contact" class="waves-effect"><span> 調訓機關承辦人員聯絡名冊 </span></a>
                                </li>
                                @endif

                                @if( in_array('production_book_system', $user_menu) )
                                <!-- 產製圖書系統用資料 -->
                                <li class="{{ @active('production_book_system') }}">
                                    <a href="/admin/production_book_system" class="waves-effect"><span> 產製圖書系統用資料 </span></a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif

                    </ul>
                </li>
                @endif

            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>