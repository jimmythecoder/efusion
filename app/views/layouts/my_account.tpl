{stylesheet_include_tag file="my_account"}
{include file='components/header.tpl'}
		<div id="nav-area">
			<ul id="speed-bar">
				<li><a accesskey="h" id="speedbar_home" title="Return to home" href="{$http_location}">HOME</a></li>
				<li><a accesskey="c" id="speedbar_catalog" title="View our catalogue" href="{$http_location}/catalog">CATALOGUE</a></li>
				<li><a accesskey="s" id="speedbar_search" title="Search catalogue" href="{$http_location}/search">SEARCH</a></li>
				<li><a accesskey="r" id="speedbar_cart" title="Your shopping cart" href="{$http_location}/cart">MY CART</a></li>
				<li><a accesskey="p" id="speedbar_checkout" title="Purchase your products" href="{$https_location}/checkout">CHECKOUT</a></li>
				<li><a accesskey="l" id="speedbar_logout" title="Logout of your account" href="{$https_location}/my-account/logout">LOGOUT</a></li>
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
			<h4>Account sections</h4>
			<ul class="left-navigation">
				<li {if $controller.name == 'my_account'}class="active"{/if}><a accesskey="1" title="Return to my account" href="my-account/index">My Account</a></li>
				<li {if $controller.name == 'orders'}class="active"{/if}><a accesskey="2" title="Check for order updates and information" href="my-account/orders/index">Orders</a></li>
				<li {if $controller.name == 'details'}class="active"{/if}><a accesskey="3" title="Change your password and update your contact details" href="my-account/details/edit">My Account Details</a></li>
				<li {if $controller.name == 'address_book'}class="active"{/if}><a accesskey="4" title="Update your delivery address or add a new one" href="my-account/address-book/index">Address Book</a></li>
				<li><a accesskey="5" title="Logout of your account" href="my-account/logout">Logout</a></li>
			</ul>
		</div>
		
{include file='components/footer.tpl'}