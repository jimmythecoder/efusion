{* View details of an order *}
{assign var="page_title" value="View Order #`$order.reference_code`"}
{stylesheet_include_tag file="thickbox"}
{javascript_include_tag file="lib/thickbox"}

<table id="order-summary" cellspacing="0" summary="A summary of your order">
<caption>A summary of your order</caption>
{foreach name=order_products item=product key=SKU from=$order.products}
<tr{cycle values=', class="alt-row"'}>
	<td class="product-thumbnail">
		{if $product.image_id}<a title="{$product.name|escape:"quotes"}" class="thickbox" href="images/products/{$product.image.filename|escape:'url'}">{/if}<img src="{get_image_src_resized_and_cropped_to width="40" height="30" filename=$product.image.filename}" alt="{$product.name|escape:"html"}" />{if $product.image_id}</a>{/if}
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
	<td class="price">{$product.sale_price*$product.quantity|number_format:2}</td>
</tr>
{/foreach}
<tr id="product-subtotals-divider">
	<td colspan="5">&nbsp;</td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
	<td class="label">Subtotal:</td>
	<td class="currency-sign">$</td>
	<td class="price">{$order.total-$order.shipping_total|number_format:2}</td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
	<td class="label">Delivery:</td>
	<td class="currency-sign">$</td>
	<td class="price">{$order.shipping_total|number_format:'2'}</td>
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
	<td id="grand-total" class="price">{$order.total|number_format:'2'}</td>
</tr>
</table>
<hr />
<address id="delivery-address">{include file="components/address.tpl" address=$order.delivery_address}</address>

<address id="billing-address">{include file="components/address.tpl" address=$order.billing_address}</address>

<div class="clearer"></div>
<div id="order-comments">
{if $order.comments != ''}
	<h3>Special requests</h3>
	<p>{$order.comments|escape:"html"}</p>
{else}
&nbsp;
{/if}
</div>

<ul>
	<li>Tracking Number: {$order.tracking_number|default:Unassigned}</li>
	<li>Payment Method: {$order.payment_method|replace:'_':' '|capitalize}</li>
	<li>Status: {$order.status|capitalize}</li>
</ul>

<a href="/my-account/orders/index">&lt;&lt; Back to my orders</a>