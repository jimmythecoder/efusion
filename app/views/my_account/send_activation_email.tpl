{* Re-activate members E-Mail address *}
{assign var="page_title" value="Activate your E-Mail address"}
Please confirm you wish to have a new activation E-Mail sent to <strong>{$account.email}</strong> 
or <a href="{$current_location}/my-account/details/edit">change my E-Mail address</a>.

<br /><br />
<form action="{$https_location}/my-account/send-activation-email" method="post">
<input type="submit" name="send_activation_email" value="Send activation E-Mail" />
</form>