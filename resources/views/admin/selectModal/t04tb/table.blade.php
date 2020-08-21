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
		<th class="text-center">期別</th>
		<th>班別名稱</th>
	</thead>
	<tbody>
		@foreach($t04tbs as $t04tb)
		<tr>
			<td class="text-center"><button type="button" class="btn" onclick="chooseT04tb('{{ $t04tb->class }}', '{{ $t04tb->class_name }}')" data-dismiss='modal'>選擇</button></td>
			<td>{{ $t04tb->class }}</td>
			<td class="text-center">{{ $t04tb->term }}</td>
			<td>{{ $t04tb->class_name }}</td>
		</tr>
		@endforeach 
	</tbody>
</table>
<div id="modalPagination" style="margin-top: 20px;">
{{ $t04tbs->links() }}
</div>