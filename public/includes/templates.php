
<script type="text/html" id="strip-template">
	<div class="strip" data-open="false">
		<div>
			<div class="strip-info"> 
				<div style="width:40px;">
					{{#icon}}
					<img class="strip-icon" src="img/{{icon}}" />
					{{/icon}}
				</div>
				<div class="strip-name"> {{name}} </div>
				<div class="separator"> </div>
				<div class="strip-value"> </div>
				<div class="separator"> </div>
			</div>
			<div class="strip-content"> </div>
		</div>
	</div>
</script>

<script type="text/html" id="entry-template">
	<div class="user span3"> 
		<div class="user-stats"> <canvas class="span3" height=200 width=200> </canvas> </div>
		<div class="user-icon"> <img src="{{pic}}" /> </div>
		<div class="user-name"> {{user}} </div> 
	</div>
</script>

<script type="text/html" id="row-template">
	<div class="row-wrap"> 
		<div class="row-results row-fluid"> </div>
	</div>
</script>

<script type="text/html" id="userdetails-template">
	<div class="user-details row-fluid">
			<h1> {{user}} </h1>
	</div>
</script>

<script type="text/html" id="usertooltip-template">
	<div>
		{{value}} {{event}} 
	</div>
</script>