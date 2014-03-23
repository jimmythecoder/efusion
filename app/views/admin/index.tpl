{* Administration home *}
{assign var="page_title" value="Store Administration"}

You have <em>{$total_pending_orders}</em> new <a href="admin/orders/index">orders</a> to process.

<ul id="administrative-sections">
	<li id="products"><a href="{$https_location}/admin/products/index">Product Catalogue</a>
		<p>Manage the products on my site.</p>
	</li>
	<li id="categories"><a href="{$https_location}/admin/categories/index">Product Categories</a>
		<p>Manage the product categories on the site.</p>
	</li>
	<li id="orders"><a href="{$https_location}/admin/orders/index">Customer Orders</a>
		<p>Manage your customers orders.</p>
	</li>
	<li id="statistics"><a href="{$https_location}/admin/statistics/index">Site Statistics</a>
		<p>View statistical graphs of your stores progress.</p>
	</li>
	<li id="emails"><a href="{$https_location}/admin/emails/index">Automated Emails</a>
		<p>Manage emails automatically sent out by your store.</p>
	</li>
	<li id="configuration"><a href="{$https_location}/admin/config/index">Store Configuration</a>
		<p>Update the site core configuration.</p>
	</li>
</ul>

