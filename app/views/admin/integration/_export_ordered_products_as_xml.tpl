<?xml version="1.0" ?>
<ordered_products>
	{foreach item=product from=$ordered_products}
	<order_product>
		<id>{$product.id}</id>
		<order_id>{$product.order_id}</order_id>
		<product_id>{$product.product_id}</product_id>
		<name><![CDATA[{$product.name}]]></name>
		<cost_price>{$product.cost_price|number_format:2}</cost_price>
		<sale_price>{$product.sale_price|number_format:2}</sale_price>
		<quantity>{$product.quantity}</quantity>
	</order_product>
	{/foreach}
</ordered_products>