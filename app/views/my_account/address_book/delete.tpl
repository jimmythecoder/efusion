{* Deletes an address *}
{assign var="page_title" value="Delete address"}

Please confirm that you wish to delete this addresss by pressing delete, otherwise
click cancel.

<address>{include file="components/address.tpl" address=$address}</address>

<form action="{$https_location}/my-account/address-book/delete/{$address.id}{if isset($smarty.get.redirect)}?redirect={$smarty.get.redirect}{/if}" method='post'>
<table class="form" cellspacing="0" summary="Delete address confirmation">
<tr>
	<td><input class="button" type="submit" name="delete" id="delete_address_book" value="Delete Address" />
	<input class="button" type="submit" name="cancel" id="cancel" value="Cancel" onclick="window.history.go(-1);return false;" /></td>
</tr>
</table>
</form>