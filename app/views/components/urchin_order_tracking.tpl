<form class="hidden" name="utmform" action="/place-order">
	<textarea id="utmtrans" rows="5" cols="5">
	UTM:T|{$order_number}|{$config.content.title}|{$cart.price_total}|{$order.gst_component}|{$total_cost_of_shipping}|{$delivery_address.city}|{$delivery_address.suburb}|{$delivery_address.country.name}
	{foreach name=cart item=product key=SKU from=$cart.products}
	UTM:I|{$order_number}|{$product.code|escape:"html"}|{$product.name|escape:"html"}|{$product.category.name|escape:"html"}|{$product.sale_price}|{$product.quantity}
	{/foreach}
	</textarea>
</form>