{* Subscribe to newsletter page *}
{assign var="page_title" value="Newsletters Subscription"}
<p>If you would like to subscribe to our regular newsletters, enter your name and email address below.</p>

<fieldset><legend>Subscribe</legend>
<form action="{$http_location}/newsletters/subscribe" method="post">
<table class="form">
<tr class="required">
	<td><label for="subscriber_first_name">Your Name</label></td>
	<td><input type="text" class="text" name="subscriber[first_name]" id="subscriber_first_name" value="{$subscriber.first_name|escape:html}{if $subscriber.last_name} {$subscriber.last_name|escape:html}{/if}" maxlength="255" /><em>*</em></td>
</tr>
<tr class="required">
	<td><label for="subscriber_email">Your E-Mail</label></td>
	<td><input type="text" class="text" name="subscriber[email]" id="subscriber_email" value="{$subscriber.email|escape:html}" maxlength="255" /><em>*</em></td>
</tr>
<tr>
	<td>Select Lists</td>
	<td>
	{foreach from=$lists item=list}
		<label><input type="checkbox" class="checkbox" name="newsletter_list[]" id="newsletter_list_{$list.id}" value="{$list.id}" /> {$list.name}</label>
	{/foreach}
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" name="subscribe" id="submit" value="Subscribe" /></td>
</tr>
</table>
</form>
</fieldset>