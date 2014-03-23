{* List all addresses for this user *}
{assign var="page_title" value="Update my addresses"}

Please select the address you wish to edit or add a new address. Each address can be used
as your delivery and/or billing address for your orders. Your primary address is shown in bold
and is used if you do not select an address when creating a new order. You are allowed
a maximum of 5 addresses.

<div id="customer-addresses">
{foreach name=addresses item=address from=$address_books}
<address {if $address.is_primary}class="primary"{/if}>{include file="components/address.tpl" address=$address}</address>
<a href="/my-account/address-book/edit/{$address.id}">Edit this address</a> {if NOT $address.is_primary}| <a href="/my-account/address-book/delete/{$address.id}">Delete</a>{/if}
<div class="dotted-ruler"></div>
{/foreach}
</div>

{if $smarty.foreach.addresses.total < $max_address_books_allowed}
<a href="/my-account/address-book/add">Add a new address</a>
{/if}