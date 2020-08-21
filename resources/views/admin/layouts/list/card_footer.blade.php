<div class="card-footer">
    <p class="list_card_footer_text" style="">總共<span>{{ (isset($data)) ? number_format($data->total()) : number_format($t04tbs->total()) }}</span>筆資料，每頁顯示
        <select id="paginate_qty">
            @foreach(config('app.paginate_qty') as $va)
                <option value="{{ $va }}" {{ $queryData['_paginate_qty'] == $va? 'selected' : '' }}>{{ $va }}</option>
            @endforeach
        </select>
        筆
    </p>
</div>