{* Site statistics *}
{assign var="page_title" value="Website statistics"}

<ol id="product-stats">
	<li><a href="{$https_location}/admin/statistics/product-views">Product Views</a><br /><small>A list of the highest viewed products.</small></li>
	<li><a href="{$https_location}/admin/statistics/products-sold">Products Sold</a><br /><small>A summary of products sold and their profitability.</small></li>
</ol>

<ol id="order-stats">
	<li><a href="{$https_location}/admin/statistics/orders-today">Orders today</a><br /><small>A chart of orders created in the last 24hrs.</small></li>
	<li><a href="{$https_location}/admin/statistics/orders-this-week">Orders this week</a><br /><small>A chart of orders created in the last 7 days.</small></li>
	<li><a href="{$https_location}/admin/statistics/orders-this-year">Orders this year</a><br /><small>A chart of orders created in the last 12 months.</small></li>
</ol>

<ol id="referrer-stats">
	<li><a href="{$https_location}/admin/statistics/website-referrers">Website Referrers</a><br /><small>A list of the most popular referrers to your site.</small></li>
	<li><a href="{$https_location}/admin/statistics/order-referrers">Orders by referrer</a><br /><small>A list of referrers by the amount the customer purchased.</small></li>
	{if $is_google_maps_api_key_set}<li><a href="{$https_location}/admin/statistics/order-locations">Orders by location</a><br /><small>A geographic representation of your orders.</small></li>{/if}
</ol>