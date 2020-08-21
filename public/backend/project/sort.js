$(document).ready(function (){

    $('[data-toggle="sort"]').click(function(){

        // 現在的排序欄位
        sort_field = $('#_sort_field').val();
        // 現在的排序方式
        sort_mode = $('#_sort_mode').val();
        // 新的排序欄位
        field = $(this).data('sort-field');

        if (sort_field != field) {
            // 跟目前排序欄位不同,使用新的欄位
            $('#_sort_field').val(field);
            // 新的欄位預設正向
            $('#_sort_mode').val(1);
        } else {

            // 與目前的排序欄位相同,切換方向
            $('#_sort_mode').val((sort_mode == '1')? '0' : '1');
        }

        // 送出表單
        $('#search_form').submit();
    })

    if ($('#_sort_field').val() != '') {
        // sort icon 顏色
        $('[data-sort-field="' + $('#_sort_field').val() + '"]').css('color', '#000');
    }

});