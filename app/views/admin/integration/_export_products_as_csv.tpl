"ID","Category ID","Name","Description","Cost Price","Sale Price","Weight","URL Name","Image URL","Code","Quantity In Stock","Created At","Is Featured"
{foreach item=product from=$products}
"{$product.id}","{$product.category_id}","{$product.name|replace:'"':'\"'}","{$product.description|replace:"\n":' '|replace:"\r":' '|replace:'"':"'"}","{$product.cost_price|number_format:2}","{$product.sale_price|number_format:2}","{$product.weight}","{$product.url_name}","{$http_location}/images/products/{$product.filename}","{$product.code|replace:'"':'\"'}","{$product.quantity_in_stock}","{$product.created_at}","{$product.is_featured}"
{/foreach}