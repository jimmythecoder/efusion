<?xml version="1.0" ?>
<orders>
	{foreach item=order from=$orders}
	<order>
		<id>{$order.id}</id>
		<customer_id>{$order.account_id}</customer_id>
		<delivery_address>
			<company><![CDATA[{$order.delivery_address_company}]]></company>
			<first_name><![CDATA[{$order.delivery_address_first_name}]]></first_name>
			<last_name><![CDATA[{$order.delivery_address_last_name}]]></last_name>
			<street><![CDATA[{$order.delivery_address_street}]]></street>
			<suburb><![CDATA[{$order.delivery_address_suburb}]]></suburb>
			<city><![CDATA[{$order.delivery_address_city}]]></city>
			<post_code><![CDATA[{$order.delivery_address_post_code}]]></post_code>
		</delivery_address>
		<billing_address>
			<company><![CDATA[{$order.billing_address_company}]]></company>
			<first_name><![CDATA[{$order.billing_address_first_name}]]></first_name>
			<last_name><![CDATA[{$order.billing_address_last_name}]]></last_name>
			<street><![CDATA[{$order.billing_address_street}]]></street>
			<suburb><![CDATA[{$order.billing_address_suburb}]]></suburb>
			<city><![CDATA[{$order.billing_address_city}]]></city>
			<post_code><![CDATA[{$order.billing_address_post_code}]]></post_code>
		</billing_address>
		<email_address><![CDATA[{$order.email_address}]]></email_address>
		<status>{$order.status}</status>
		<tracking_number><![CDATA[{$order.tracking_number}]]></tracking_number>
		<created_at>{$order.created_at|date_format:'%A, %e %b %Y. %l:%M %p'}</created_at>
		<payment_method>{$order.payment_method}</payment_method>
		<transaction_reference><![CDATA[{$order.transaction_reference}]]></transaction_reference>
		<shipping_total>{$order.shipping_total|number_format:2}</shipping_total>
		<gst_component>{$order.gst_component|number_format:2}</gst_component>
		<amount_paid>{$order.amount_paid|number_format:2}</amount_paid>
		<order_total>{$order.total|number_format:2}</order_total>
	</order>
	{/foreach}
</orders>