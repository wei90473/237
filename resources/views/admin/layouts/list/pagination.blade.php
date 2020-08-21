<?php $paginator->appends($queryData);?>

<ul class="pagination mt-3 m-b-5">
    {{-- 上一頁 --}}
    @if ($paginator->currentPage() != 1)
        <li class="page-item"><a class="page-link" href="{{ $paginator->url($paginator->currentPage()-1) }}"><i class="fa fa-angle-left"></i></a></li>
    @endif

    {{-- 每一頁,大於一頁時顯示 --}}
    @if($paginator->lastPage() > 1 )

        @if($paginator->lastPage() < 13)
            {{-- 分頁不多時,顯示全部分頁按鈕 --}}
            @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                <?php
                // 補足雙位數
                // $showValue = ($i < 10)? str_pad($i, 2, "0", STR_PAD_LEFT) : $i;
                $showValue = $i;
                ?>
                <li class="page-item {{ $paginator->currentPage() == $i? 'active' : '' }}"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $showValue }}</a></li>
            @endfor
        @else

            {{-- 分頁太多,縮減,取得中心 --}}
            <?php
            if ($paginator->currentPage() < 5) {

                $middle = 5;
            } elseif ($paginator->currentPage() > $paginator->lastPage() - 4 ) {

                $middle = $paginator->lastPage() - 4;
            } else {

                $middle = $paginator->currentPage();
            }
            ?>

            {{-- 離第一頁太遠時顯示第一頁 --}}
            @if($middle > 5)
                <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) }}">1</a></li>
            @endif

            {{-- 離第一頁太遠時加上 ... --}}
            @if($middle > 6)
                <li class="px-3">...</li>
            @endif

            {{-- 中心的前面四頁,後面四頁 --}}
            @for($i = $middle - 4; ($i <= ($middle + 4) && $i <= $paginator->lastPage()) ; $i++)
                <?php
                // 補足雙位數
                // $showValue = ($i < 10)? str_pad($i, 2, "0", STR_PAD_LEFT) : $i;
                $showValue = $i;
                ?>
                <li class="page-item {{ $paginator->currentPage() == $i? 'active' : '' }}"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $showValue }}</a></li>
            @endfor


            {{-- 是否顯示... --}}
            @if($middle < $paginator->lastPage() - 5)
                <li class="px-3">...</li>
            @endif

            {{-- 顯示最後一頁 --}}
            @if($middle < $paginator->lastPage() -4)
                <li class="page-item"><a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a></li>
            @endif


        @endif
    @endif

    {{--<li class="px-3">{{ $paginator->lastPage() }}</li>--}}


    {{-- 下一頁 --}}
    @if ($paginator->currentPage() < $paginator->lastPage())
        <li class="page-item"><a class="page-link" href="{{ $paginator->url($paginator->currentPage()+1) }}"><i class="fa fa-angle-right"></i></a></li>
    @endif

</ul>