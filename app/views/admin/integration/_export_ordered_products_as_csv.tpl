"ID","Order ID","Product ID","Name","Cost Price","Sale Price","Quantity"
{foreach item=product from=$ordered_products}
"{$product.id}","{$product.order_id}","{$product.product_id}","{$product.name|replace:'"':'\"'}","{$product.cost_price|number_format:2}","{$product.sale_price|number_format:2}","{$product.quantity}"
{/foreach}