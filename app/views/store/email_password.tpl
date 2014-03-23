{* Forgot Password Page *}
{assign var="page_title" value="Forgot your password?"}

If you have forgotten your password, enter your E-Mail address below and we will 
send you an e-mail message containing your new password.

<form action="{$http_location}/email-password" method="post">
<fieldset><legend>Verification</legend>
<table class="form" summary="Account verification form">
<tr class="required">
	<td><label for="account_email">E-Mail Address:</label></td>
	<td><input class="text" type="text" name="account[email]" id="account_email" value="{$account_email_address}" maxlength="100" /><em>*</em></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input class="button" type="submit" name="account[send_new_password]" id="send_new_password_button" value="Send Password" /></td>
</tr>
</table>
</fieldset>
</form>