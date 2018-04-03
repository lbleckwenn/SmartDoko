<div class="container">
	{if $success}
	<div class="alert alert-success" role="alert">
		<h4 class="alert-heading">Erfolg</h4>
		<p>
			{$success}</a>
		</p>
	</div>
	{elseif $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Fehler!</h4>
		<p>{$error}</p>
	</div>
	{/if} 
	{if $step==1} 
		{include file="round_step_1.tpl"} 
	{elseif $step==2}
		{include file="round_step_2.tpl"}
	{elseif $step==3}
		{include file="round_step_3.tpl"}
	{elseif $step==4}
		{include file="round_step_4.tpl"}
	{/if}
</div>
