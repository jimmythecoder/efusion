<html><![CDATA[
	{foreach item=product from=$search_results}
	<li><a href="{$http_location}/product/{$product.url_name}">{$product.name|escape:"html"|truncate:20}</a></li>
	{foreachelse}
	<li class="faded">Live Search</li>
	{/foreach}
	]]>
</html>