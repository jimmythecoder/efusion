{stylesheet_include_tag file="store"}
{javascript_include_tag file="store"}
{include file='components/header.tpl'}
		<div id="nav-area">
			<ul id="speed-bar">
				<li {if $controller.action == 'index'}class="active"{/if}><a accesskey="h" title="Return to home" href="{$http_location}">HOME</a></li>
				<li {if $controller.action == 'catalog'}class="active"{/if}><a accesskey="c" title="Browse product catalogue" href="{$http_location}/catalog">CATALOGUE</a></li>
				<li {if $controller.action == 'search'}class="active"{/if}><a accesskey="s" title="Search catalogue" href="{$http_location}/search">SEARCH</a></li>
				<li {if $controller.action == 'cart'}class="active"{/if}><a accesskey="r" title="Your shopping cart" href="{$http_location}/cart">MY CART</a></li>
				<li {if $controller.action == 'checkout'}class="active"{/if}><a accesskey="e" title="Purchase your products" href="{$https_location}/checkout">CHECKOUT</a></li>
				<li {if $controller.action == 'login'}class="active"{/if}>{if $account_group == 'administrators'}<a accesskey="a" title="Access administration" href="{$https_location}/admin/index">ADMINISTRATION{elseif $account_group == 'members'}<a accesskey="a" title="Login / Access your account" href="{$https_location}/my-account/index">MY ACCOUNT{else}<a accesskey="a" title="Login / Access your account" href="{$https_location}/my-account/index">LOGIN{/if}</a></li>
			</ul>
			<p id="phone">{$config.contact.phone}</p>
		</div>
		<div id="bread-crumb">
			{include file='components/bread_crumb.tpl'}
		</div>
		
		<div id="wrapper">
			<div id="content" class="{$controller.action}">
				<h1 id="content-title">{$page_heading|default:$page_title|escape:"html"}</h1>
				<div class="title-clearer"></div>
				{include file="components/flash.tpl" flash=$flash}
				{$content_for_layout}
			</div>
		</div>
		
		<div id="left-column">
			<ul class="left-navigation">{include file='components/categories.tpl' categories=$categories}</ul>
		</div>
		
		{if $controller.action != 'cart' AND $controller.action != 'checkout' AND $controller.action != 'confirm-order' AND $controller.action != 'order-placed'}
		<div id="right-column">
			{include file='components/mini_cart.tpl'}
			{if $controller.action != "search"}{include file='components/mini_search.tpl'}{/if}
			{if $config.adsense.enabled == true}
				{include file='components/google-adsense.tpl' key=$config.adsense.key}
			{/if}
			<p id="rss-link"><a title="Latest Products via RSS 2.0" href="{$current_location}/feeds/rss">RSS Feed</a></p>
		
			{include_from_modules file="partials/right_column_extra.tpl"}
		</div>
		{/if}
{include file='components/footer.tpl'}