{stylesheet_include_tag file="store"}
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
			<h4>Newsletters</h4>
			<ul class="left-navigation">
				<li {if $controller.action == 'view'}class="active"{/if}><a accesskey="2" title="View newsletters online" href="newsletters/view">View Online</a></li>
				<li {if $controller.action == 'subscribe'}class="active"{/if}><a accesskey="3" title="Subscribe to our newsletters" href="newsletters/subscribe">Subscribe</a></li>
				<li {if $controller.action == 'unsubscribe'}class="active"{/if}><a accesskey="4" title="Unsubscribe from our newsletters" href="newsletters/unsubscribe">Unsubscribe</a></li>
			</ul>
		</div>

		<div id="right-column">
			{include file='components/mini_cart.tpl'}
			{include file='components/mini_search.tpl'}
			{if $config.adsense.enabled == true}
				{include file='components/google-adsense.tpl' key=$config.adsense.key}
			{/if}
			<p id="rss-link"><a title="Latest Products via RSS 2.0" href="{$current_location}/feeds/rss">RSS Feed</a></p>
		
			{include_from_modules file="partials/right_column_extra.tpl"}
		</div>		
{include file='components/footer.tpl'}