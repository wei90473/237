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
		<th class="text-center">姓名</th>
		<th class="text-center">服務單位</th>
		<th class="text-center">職稱</th>
	</thead>
	<tbody>
		@foreach($teachers as $teacher)
		<tr>
			<td class="text-center"><button type="button" class="btn" onclick="chooseTeacher('{{ $teacher->idno }}', '{{ $teacher->cname }}')" data-dismiss='modal'>選擇</button></td>
			<td class="text-center">{{ $teacher->cname }}</td>
			<td class="text-center">{{ $teacher->dept }}</td>
			<td class="text-center">{{ $teacher->position }}</td>
		</tr>
		@endforeach 
	</tbody>
</table>
<div id="modalPagination" style="margin-top: 20px;">
{{ $teachers->links() }}
</div>