{* Members address book *}
{assign var="page_title" value="Edit address"}

Update your address details and click save.

<form action="{$https_location}/my-account/address-book/edit/{$address_book.id}{if isset($smarty.get.redirect)}?redirect={$smarty.get.redirect}{/if}" method='post'>
	{include file='my_account/address_book/_address_book_form.tpl'}	
</form>