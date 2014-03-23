<table id="cart" cellspacing="0" cellpadding="0" summary="Products currently in your cart">
<tr>
	<th>Remove</th>
	<th>Product</th>
	<th>In Stock</th>
	<th>Price</th>
	<th>Qty</th>
	<th>Sub Total</th>
</tr>
{foreach name=cart item=product key=SKU from=$cart.products}
<tr {cycle values='class="alt",'}>
	<td class="remove"><input class="checkbox" type="checkbox" name="cart[remove_product][]" value="{$SKU}" /></td>
	<td class="product"><a href="{$http_location}/product/{$product.url_name}">{$product.name}
		{if isset($product.variants)} -
			{foreach name=variants item=variant from=$product.variants}
				{$variant}
				{if !$smarty.foreach.variants.last}, {/if}
			{/foreach}
		{/if}</a>
	</td>
	<td class="instock">{if $product.quantity_in_stock > 0}Yes{else}No{/if}</td>
	<td class="price">${$product.sale_price|number_format:2}</td>
	<td class="quantity"><input type="text" class="text product_quantity numeric" id="product_quantity_{$SKU}" name="cart[quantity][{$SKU}]" value="{$product.quantity}" maxlength="3" /></td>
	<td class="subtotal">${$product.subtotal|number_format:2}</td>
</tr>
{foreachelse}
<tr>
	<td colspan="6">There are no products in your cart</td>
</tr>
{/foreach}
<tr>
	<td id="cart-footer" colspan="5">{if $smarty.foreach.cart.total > 0}<input class="button" type="submit" name="cart[update]" value="Update Cart" id="button_update_cart" />{else}&nbsp;{/if}</td>
	<td class="total">$ {$cart.price_total|number_format:2}</td>
</tr>
</table>