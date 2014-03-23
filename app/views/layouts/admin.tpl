{stylesheet_include_tag file="admin"}
{javascript_include_tag file="admin"}
{include file='components/header.tpl'}
		<div id="nav-area">
			<ul id="speed-bar">
				<li {if $controller.name == 'admin'}class="active"{/if}><a accesskey="a" title="Return to administration" href="{$https_location}/admin/index">ADMINISTRATION</a></li>
				<li {if $controller.name == 'orders'}class="active"{/if}><a accesskey="o" title="View customer orders" href="{$https_location}/admin/orders/index">ORDERS</a></li>
				<li {if $controller.name == 'products'}class="active"{/if}><a accesskey="p" title="Manage store products" href="{$https_location}/admin/products/index">PRODUCTS</a></li>
				<li {if $controller.name == 'categories'}class="active"{/if}><a accesskey="c" title="Manage product categories" href="{$https_location}/admin/categories/index">CATEGORIES</a></li>
				<li {if $controller.name == 'statistics'}class="active"{/if}><a accesskey="s" title="View store statistics" href="{$https_location}/admin/statistics/index">STATISTICS</a></li>
				<li><a accesskey="l" id="speedbar_logout" title="Logout of administration" href="{$https_location}/admin/logout">LOGOUT</a></li>
			</ul>
			<p id="phone">{$config.contact.phone}</p>
		</div>
		<div id="bread-crumb">
			{include file='components/bread_crumb.tpl'}
		</div>
		
		<div id="wrapper">
			<div id="content" class="{$controller.name}">
				<h1 id="content-title">{$page_title|escape:"html"}</h1>
				<div class="title-clearer"></div>
				{include file="components/flash.tpl" flash=$flash}
				{$content_for_layout}
			</div>
		</div>
		
		<div id="left-column">
			<h4>Shop Functions</h4>
			<ul class="left-navigation">
				<li {if $controller.name == 'orders'}class="active"{/if}><a title="Manage your customers orders" href="{$https_location}/admin/orders/index">Orders</a></li>
				<li {if $controller.name == 'products'}class="active"{/if}><a title="Manage variations attached to your products" href="{$https_location}/admin/products/index">Products</a></li>
					{if $controller.name == 'products' OR $controller.name == 'variations'}
					<li><ul class="left-navigation"><li {if $controller.name == 'variations'}class="active first-child"{else}class="first-child"{/if}><a title="Manage variations attached to your products" href="{$https_location}/admin/variations/index">Variations</a></li></ul></li>{/if}
				<li {if $controller.name == 'categories'}class="active"{/if}><a title="Manage the product categories on the site" href="{$https_location}/admin/categories/index">Categories</a></li>
				<li {if $controller.name == 'statistics'}class="active"{/if}><a title="View statistical graphs of your stores progress" href="{$https_location}/admin/statistics/index">Statistics</a></li>
				<li {if $controller.name == 'emails'}class="active"{/if}><a title="Manage emails automatically sent out by your store" href="{$https_location}/admin/emails/index">Emails</a></li>
				<li {if $controller.name == 'content'}class="active"{/if}><a title="Manage the content pages on your site" href="{$https_location}/admin/content/index">Content</a></li>
				<li {if $controller.name == 'customers'}class="active"{/if}><a title="Manage customer accounts on your site" href="{$https_location}/admin/customers/index">Customers</a></li>
				<li {if $controller.name == 'accounts'}class="active"{/if}><a title="Manage the administrative accounts for your store" href="{$https_location}/admin/accounts/index">Accounts</a></li>
				<li {if $controller.name == 'banners'}class="active"{/if}><a title="Update the banner on the site" href="{$https_location}/admin/banners/index">Banners</a></li>
				<li {if $controller.name == 'delivery'}class="active"{/if}><a title="Delivey Charges" href="{$https_location}/admin/delivery/index">Delivery</a></li>
				<li {if $controller.name == 'integration'}class="active"{/if}><a title="Data integration" href="{$https_location}/admin/integration/index">Integration</a></li>
				<li {if $controller.name == 'config'}class="active"{/if}><a title="Update the core site configuration" href="{$https_location}/admin/config/index">Configuration</a></li>
			</ul>
		</div>
		
{include file='components/footer.tpl'}