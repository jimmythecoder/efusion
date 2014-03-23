{foreach name=products item=product key=id from=$products}
{cycle name="start" values='<dl class="product-list">,,'}
<dd><a href="{$current_location}/product/{$product.url_name}">{$product.name|escape:"html"}</a><br /><br />
<a title="{$product.name|escape:"quotes"}" href="{$current_location}/product/{$product.url_name}"><img src="{get_image_src_resized_and_cropped_to width="130" height="85" filename=$product.image.filename}" alt="{$product.name|escape:"html"}" /></a><br />
<p><strong>${$product.sale_price|number_format:2}</strong><br />{$product.description|strip_tags|truncate:150}</p>
<a class="small" href="{$current_location}/product/{$product.url_name}">View Product &gt;</a>
</dd>{if $smarty.foreach.products.last}</dl>{else}{cycle name="end" values=',,</dl>'}{/if}
{foreachelse}
There are currently no products
{/foreach}