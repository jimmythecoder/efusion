{* Display a full product list of the site *}
{assign var="page_title" value='Product Listing'}
{stylesheet_include_tag file="show_all_products"}

All prices displayed on this page are <b>inclusive</b> of GST.

<table class="product-list" cellspacing="0" summary="Store product list">
<caption>Product List</caption>
<tr>
	<th>Name</th>
	<th>Price</th>
</tr>
{foreach item=product from=$products}
<tr{cycle values=', class="alt-row"'}>
	<td><a href="{$http_location}/product/{$product.url_name}">{$product.name}</a></td>
	<td>${$product.sale_price|number_format:2}</td>
</tr>
{/foreach}
</table>
