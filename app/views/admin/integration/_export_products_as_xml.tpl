<?xml version="1.0" ?>
<products>
	{foreach item=product from=$products}
	<product>
		<id>{$product.id}</id>
		<category_id>{$product.category_id}</category_id>
		<name><![CDATA[{$product.name}]]></name>
		<description><![CDATA[{$product.description}]]></description>
		<cost_price>{$product.cost_price|number_format:2}</cost_price>
		<sale_price>{$product.sale_price|number_format:2}</sale_price>
		<weight>{$product.weight}</weight>
		<url_name>{$product.url_name}</url_name>
		<image_url>{$http_location}/images/products/{$product.filename}</image_url>
		<code>{$product.code}</code>
		<quantity_in_stock>{$product.quantity_in_stock}</quantity_in_stock>
		<created_at>{$product.created_at}</created_at>
		<is_featured>{$product.is_featured}</is_featured>
	</product>
	{/foreach}
</products>