{* Members address book *}
{assign var="page_title" value="Add a new address"}

Please enter the details for your new address

<form action="{$https_location}/my-account/address-book/add{if isset($smarty.get.redirect)}?redirect={$smarty.get.redirect}{/if}" method='post'>
	{include file='my_account/address_book/_address_book_form.tpl'}	
</form>