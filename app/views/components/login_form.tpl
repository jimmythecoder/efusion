<table class="form" cellspacing="0" summary="My Account Log-In form">
<tr class="required">
	<td><label for="login_email">E-Mail Address:</label></td>
	<td><input class="text" type="text" name="login[email]" id="login_email" value="{$login.email|escape:'html'}" maxlength="100" /></td>
</tr>
<tr class="required">
	<td><label for="login_password">Password:</label></td>
	<td><input class="text" type="password" name="login[password]" id="login_password" value="" maxlength="100" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><label for="login_save_details"><input class="checkbox" type="checkbox" name="login[save_details]" id="login_save_details" value="1" />Keep me logged in on this computer</label></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input class="button" type="submit" name="login[submit]" id="login_submit" value="Continue &gt;&gt;" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><a title="Click to retrieve your password" href="{$http_location}/email-password">Forgot your password?</a></td>
</tr>
</table>