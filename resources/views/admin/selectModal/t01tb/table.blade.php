<style>
.pagination li a{
	padding: 8px;
}

.pagination li span{
	padding: 8px;
}
</style>
<table class="table table-bordered mb-0">
	<thead>
		<th class="text-center">選擇</th>
		<th>班號</th>
		<th>班別名稱</th>
	</thead>
	<tbody>
		@foreach($t01tbs as $t01tb)
		<tr>
			<td class="text-center"><button type="button" class="btn" onclick="chooseT01tb('{{ $t01tb->class }}', '{{ $t01tb->name }}', '{{ join(',', $t01tb->t04tbs->pluck('term')->toArray()) }}')" data-dismiss='modal'>選擇</button></td>
			<td>{{ $t01tb->class }}</td>
			<td>{{ $t01tb->name }}</td>
		</tr>
		@endforeach 
	</tbody>
</table>
<div id="modalPagination" style="margin-top: 20px;">
{{ $t01tbs->links() }}
</div>