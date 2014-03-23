{* Member and administration login page *}
{assign var="page_title" value="Login to your account"}

{if !isset($smarty.post.signup)}
	<form action="login" method="post">
	<fieldset><legend>Existing customers</legend>
	If you have shopped with us before, you may simply enter your E-Mail address and password below to login to your account.
	{include file="components/login_form.tpl"}
	</fieldset>
	</form>
{/if}

{if !isset($smarty.post.login)}
	<form action="login" method="post">
	<fieldset><legend>New Customers</legend>
	New customers must create an account to login.
	{include file="components/signup_form.tpl"}
	</fieldset>	
	</form>
{/if}