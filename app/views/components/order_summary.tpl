<table id="order-summary" cellspacing="0" summary="A summary of your order">
<caption>A summary of your order</caption>
{foreach name=cart item=product key=SKU from=$cart.products}
<tr{cycle values=', class="alt-row"'}>
	<td class="product-thumbnail">
		{if $product.image_id != DEFAULT_IMAGE_ID}<a title="{$product.name|escape:"quotes"}" class="thickbox" href="images/products/{$product.image.filename|escape:'url'}">{/if}<img src="{get_image_src_resized_and_cropped_to width="40" height="30" filename=$product.image.filename}" alt="{$product.name|escape:"html"}" />{if $product.image_id != DEFAULT_IMAGE_ID}</a>{/if}
	</td>
	<td class="product-name" colspan="2">{$product.quantity} x {$product.name|escape:"html"}
		{if isset($product.variants)} -
			{foreach name=variants item=variant from=$product.variants}
				{$variant}
				{if !$smarty.foreach.variants.last}, {/if}
			{/foreach}
		{/if}
	</td>
	<td class="currency-sign">$</td>
	<td class="price">{$product.subtotal|number_format:2}</td>
</tr>
{/foreach}
<tr id="product-subtotals-divider">
	<td colspan="5">&nbsp;</td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
	<td class="label">Subtotal:</td>
	<td class="currency-sign">$</td>
	<td class="price">{$cart.price_total|number_format:2}</td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
	<td class="label">Delivery:</td>
	<td class="currency-sign">$</td>
	<td class="price">{if !$total_cost_of_shipping}Free{else}{$total_cost_of_shipping|number_format:'2'}{/if}</td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
	<td class="label">GST Amount:</td>
	<td class="currency-sign">$</td>
	<td class="price">{$order.gst_component|number_format:'2'}</td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
	<td class="label">Grand Total:</td>
	<td class="currency-sign">$</td>
	<td id="grand-total" class="price">{$cart.price_total+$total_cost_of_shipping|number_format:'2'}</td>
</tr>
</table>
<hr />
<address id="delivery-address">
	{include file="components/address.tpl" address=$delivery_address}
</address>

<address id="billing-address">
	{include file="components/address.tpl" address=$billing_address}
</address>

<div class="clearer"></div>
<div id="order-comments">
{if $order_comments != ''}
	<h3>Special requests</h3>
	<p>{$order_comments|escape:"html"}</p>
{else}
&nbsp;
{/if}
</div>