<div class="container"  style="max-width:700px;">
    <div class="table-responsive">
        <table class="table table-bordered mb-0">
            <thead>
            <tr>
                <th class="text-center">班號</th>
                <th>班別名稱</th>
                <th>期別</th>
                <th>停止換員</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($t04tbs as $t04tb)
            <tr>
                <td>{{ $t04tb->class }}</td>
                <td>{{ $t04tb->t01tb->name }}</td>
                <td>{{ $t04tb->term }}</td>
                <td><input type="checkbox" {{ ($t04tb->is_stop_change == 'Y') ? 'checked' : null }} onchange="stopChange('{{ $t04tb->class }}', '{{ $t04tb->term }}', this.checked)"></td>
            </tr>
            @endforeach 
            </tbody>
        </table>                        
    </div> 
</div>