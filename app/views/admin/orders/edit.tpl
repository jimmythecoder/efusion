{* View details of an order *}
{assign var="page_title" value="View Order #`$order.reference_code`"}
{stylesheet_include_tag file="thickbox"}
{javascript_include_tag file="lib/thickbox"}

<table id="order-summary" cellspacing="0" summary="A summary of the order">
<caption>A summary of the order</caption>
{foreach name=order_products item=product key=SKU from=$order.products}
<tr{cycle values=', class="alt-row"'}>
	<td class="product-thumbnail">
		{if $product.image_id != DEFAULT_IMAGE_ID}<a title="{$product.name|escape:"quotes"}" class="thickbox" href="images/products/{$product.image.filename|escape:'url'}">{/if}<img src="{get_image_src_resized_and_cropped_to width="40" height="30" filename=$product.image.filename}" alt="{$product.name|escape:"html"}" />{if $product.image_id != DEFAULT_IMAGE_ID}</a>{/if}
	</td>
	<td class="product-name" colspan="2">{$product.quantity} x <a class="popup-link" href="{$http_location}/product/{$product.url_name}">{$product.name|escape:"html"}</a>
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
<address id="delivery-address">
	{if !empty($order.delivery_address.company)}{$order.delivery_address.company|escape:'html'}, {/if}
	{$order.delivery_address.first_name|escape:'html'}
	{$order.delivery_address.last_name|escape:'html'}<br />
	{$order.delivery_address.street|escape:'html'} <br />
	{$order.delivery_address.suburb|escape:'html'}<br />
	{$order.delivery_address.city}<br />
	{$order.delivery_address.country.name|upper}<br /><br />
	Ph. {$order.account.phone|escape:'html'}<br />
	E-Mail: <a href="mailto:{$order.account.email|escape:'html'}">{$order.account.email|escape:'html'}</a>
</address>

<address id="billing-address">
	{if !empty($order.billing_address.company)}{$order.billing_address.company|escape:'html'}, {/if}
	{$order.billing_address.first_name|escape:'html'}
	{$order.billing_address.last_name|escape:'html'}<br />
	{$order.billing_address.street|escape:'html'} <br />
	{$order.billing_address.suburb|escape:'html'}<br />
	{$order.billing_address.city}<br />
	{$order.billing_address.country.name|upper}
	
</address>

<div class="clearer"></div>

<form action="{$https_location}/admin/orders/edit/{$order.id}" method="post">
<fieldset><legend>Update order information</legend>
<table class="form" summary="Order information">
<tr>
	<td>Payment Method:</td>
	<td>{$order.payment_method|replace:'_':' '|capitalize}</td>
</tr>
<tr>
	<td>Payment Reference:</td>
	<td>{if $order.payment_method == 'credit_card'}{$order.transaction_reference|escape:"html"}
		{else}{$order.reference_code}{/if}</td>
</tr>
<tr>
	<td>Amount Paid:</td>
	<td>{if $order.payment_method == 'credit_card'}${$order.amount_paid|number_format:2}
		{else}<input class="text numeric" name="order[amount_paid]" id="order_amount_paid" value="{$order.amount_paid|number_format:2}" />{/if}</td>
</tr>
<tr>
	<td><label for="order_tracking_number">Delivery Tracking Number:</label></td>
	<td><input type="text" class="text" name="order[tracking_number]" id="order_tracking_number" value="{$order.tracking_number|escape:"html"}" /></td>
</tr>
<tr class="required">
	<td><label for="order_status">Order Status:</label></td>
	<td><select class="select" name="order[status]" id="order_status">
		{html_options options=$order_status_options selected=$order.status}
	</select><em>*</em></td>
</tr>
<tr>
	<td><label for="order_comments">Order notes:</label></td>
	<td><textarea class="text" rows="8" cols="60" name="order[comments]" id="order_comments">{$order.comments|escape:"html"}</textarea></td>
</tr>
<tr>
	<td><a href="admin/orders/index">&lt;&lt; Back to orders without saving</a></td>
	<td><input class="button" type="submit" name="save" id="save_order" value="Save Order &gt;&gt;" /></td>
</tr>
</table>
</fieldset>
</form>

