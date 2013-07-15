
<script type="text/html" id="row-template">
	<tr>
		<td class="pic">  <img src="{{pic}}" style="box-shadow: 0 0 8px 2px {{color}}" </td>
		<td class="user">{{user}}</td>
		<td class="actions">{{total}}</td>
		<td class="lastevent">{{lastEvent}}</td>
	</tr>
</script>

<script type="text/html" id="table-template">
	<table class="table table-striped" > 
		<thead>
			<th> </th>
			<th class="sort" data-sort="user" > User </th>
			<th class="sort" data-sort="actions"> {{eventtype}} </th>
			<th> Last seen </th>
		</thead>
		<tbody id="user-list" class="list">
		</tbody >
	</table>
</script>

<script type="text/html" id="option-template">
	{{#.}}
		<option value="{{.}}">{{.}}</option>
	{{/.}}
</script>