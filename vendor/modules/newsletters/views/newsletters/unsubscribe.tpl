{* Unsubscribe from newsletters *}
{assign var="page_title" value="Unsubscribe"}

<p>Please confirm you wish to unsubscribe and not receive future newsletters from us by entering your E-Mail address below.</p>

<fieldset><legend>Unsubscribe</legend>
<form action="{$http_location}/newsletters/unsubscribe" method="post">
<table class="form">
<tr class="required">
	<td><label for="subscriber_email">Your E-Mail</label></td>
	<td><input type="text" class="text" name="subscriber[email]" id="subscriber_first_name" value="" maxlength="255" /><em>*</em></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" name="unsubscribe" id="submit" value="Unsubscribe" /></td>
</tr>
</table>
</form>
</fieldset>