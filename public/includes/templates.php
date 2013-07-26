

<script type="text/html" id="user-template">
	{{#.}}
		<option data-avatar="{{avatar}}" value="{{name}}">{{name}}</option>
	{{/.}}
</script>

<script type="text/html" id="event-template">
	{{#.}}
		<option value="{{value}}">{{name}}</option>
	{{/.}}
</script>

<script type="text/html" id="strip-template">
	<div class="strip" data-open="false">
		<div>
			<div class="strip-info"> 
				<img class="strip-icon" src="{{pic}}" />
				<div class="strip-name"> {{user}} </div>
				<div class="separator"> </div>
				<div class="strip-value"> </div>
				<div class="separator"> </div>
			</div>
			<div class="strip-content"> </div>
		</div>
	</div>
</script>