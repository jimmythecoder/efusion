{if $controller.name != 'newsletters'}
<div id="newsletters">
	<h3>Newsletters</h3>
	<p>Enter your name and email address to be informed of our latest special offers.</p>
	
	<form action="{$http_location}/newsletters/subscribe" method="post">
		<p class="required"><input class="text" type="text" name="subscriber[first_name]" id="subscriber_first_name" maxlength="100" /></p>
		<p class="required"><input class="text required" type="text" name="subscriber[email]" id="subscriber_email" maxlength="100" />
		<input type="submit" name="subscribe" value="Subscribe" /></p>
	</form>
	
	<p>All newsletters are <a href="{$http_location}/newsletters/view">published online</a></p>
</div>
{/if}