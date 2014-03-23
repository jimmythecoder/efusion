<orders>
	{foreach item=order from=$orders}
	<order>
		<id>{$order.id}</id>
		<longitude>{$order.longitude}</longitude>
		<latitude>{$order.latitude}</latitude>
		<total>{$order.total_order_value|number_format:2}</total>
	</order>
	{/foreach}
</orders>