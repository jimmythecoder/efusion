<product>
	<code>{$product.code}</code>
	<name>{$product.name}</name>
	<description><![CDATA[{$product.description}]]></description>
	<price>{$product.price|number_format:2}</price>
	<category>{$product.category.name}</category>
	<weight>{$product.weight}</weight>
	<quantity>{$product.quantity_in_stock}</quantity>
	<image>{*$product.fullsize_image_url*}</image>
</product>