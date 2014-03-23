{* Member account home page *}
{assign var="page_title" value="My Account Information"}
<ul id="account-sections">
	<li id='orders'><a href="{$https_location}/my-account/orders/index">View my orders</a>
		<p>Check for order updates and information.</p>
	</li>
	<li id='personal-details'><a href="{$https_location}/my-account/details/edit">Update my account details</a>
		<p>Change your password and update your contact details.</p>
	</li>
	<li id='address-book'><a href="{$https_location}/my-account/address-book/index">Update my address books</a>
		<p>Update your delivery address or add a new one.</p>
	</li>
	
	<li id='account-activation' class="{if $account.is_email_activated}activated{else}not-activated{/if}">
	{if $account.is_email_activated}Your E-Mail address is verified
	{else}You have not verified your E-Mail address with us, please check your email inbox for instructions or get a <a href="{$current_location}/my-account/send-activation-email">new activation email</a> sent.{/if}</li>
</ul>

