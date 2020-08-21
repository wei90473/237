@inject('downloadClass', 'App\Presenters\DownloadClassPresenter')
<script>
    // 是否是list
    var isSearch = $('#is_search').val();
    // 階段預設id
    var stage_id = $('#default_stage_id').val();
    // 計畫預設id
    var plan_id = $('#default_plan_id').val();
    // 項目預設id
    var item_id = $('#default_item_id').val();
    // 所有階段的json
    var stageJson = {!! $downloadClass->getStageJson() !!};
    // 所有計劃的json
    var planJson = {!! $downloadClass->getPlanJson() !!};
    // 所有項目的json
    var itemJson = {!! $downloadClass->getItemJson() !!};

    // 計畫變更觸法,顯示項目
    function planChange() {

        plan_id = $('#download_service_plan_id').val();

        // 清空這層的內容
        $("#download_service_item_id").empty();

        // 列表搜尋時增加"所有階段"選項
        if (isSearch == '1') {
            var html = "<option value=''>所有階段</option>";
            $("#download_service_item_id").append(html);
        }

        if (plan_id) {

            $.each(itemJson, function( key, va ) {

                if (va.download_service_plan_id == plan_id) {

                    var html = "<option value='" + va.download_service_item_id + "' ";;
                    html += (item_id == va.download_service_item_id)? 'selected' : '';
                    html += ">" + va.name + "</option>";

                    $("#download_service_item_id").append(html);
                }
            });
        }

        // 重建階段的select2
        $("#download_service_item_id").select2("destroy");

        $("#download_service_item_id").select2();
    }

    // 階段變更觸法,顯示計畫
    function stageChange() {

        stage_id = $('#download_service_stage_id').val();

        // 清空這層的內容
        $("#download_service_plan_id").empty();

        // 列表搜尋時增加"所有階段"選項
        if (isSearch == '1') {
            var html = "<option value=''>所有階段</option>";
            $("#download_service_plan_id").append(html);
        }

        if (stage_id) {

            $.each(planJson, function( key, va ) {

                if (va.download_service_stage_id == stage_id) {

                    var html = "<option value='" + va.download_service_plan_id + "' ";;
                    html += (plan_id == va.download_service_plan_id)? 'selected' : '';
                    html += ">" + va.name + "</option>";

                    $("#download_service_plan_id").append(html);
                }
            });
        }

        // 重建階段的select2
        $("#download_service_plan_id").select2("destroy");

        $("#download_service_plan_id").select2();

        // 檢查是否還有下一層
        if ($("#download_service_plan_id").attr('onclick') != '') {

            planChange();
        }
    }

    // 分類變更觸發,顯示階段
    function classChange() {

        // 取得類別id
        class_id = $('#download_service_class_id').val();
        // 清空這層的內容
        $("#download_service_stage_id").empty();

        // 列表搜尋時增加"所有階段"選項
        if (isSearch == '1') {
            var html = "<option value=''>所有階段</option>";
            $("#download_service_stage_id").append(html);
        }


        if (class_id) {

            $.each(stageJson, function( key, va ) {

                if (va.download_service_class_id == class_id) {

                    var html = "<option value='" + va.download_service_stage_id + "' ";;
                    html += (stage_id == va.download_service_stage_id)? 'selected' : '';
                    html += ">" + va.name + "</option>";

                    $("#download_service_stage_id").append(html);
                }
            });
        }

        // 重建階段的select2
        $("#download_service_stage_id").select2("destroy");

        $("#download_service_stage_id").select2();

        // 檢查是否還有下一層
        if ($("#download_service_stage_id").attr('onclick') != '') {

            stageChange();
        }
    }



    $(document).ready(function(e) {
        // 初始化
        classChange();
    });
</script>