{* Displays a miniture view of users cart contents *}

<p class="cart-title"><a title="View My Shopping Cart details" href="cart">My Cart</a></p>

<ul id="mini-cart">
{foreach name=mini_cart key=SKU item=cart_product from=$cart.products}
	<li>{$cart_product.quantity} x <a href="product/{$cart_product.url_name}">{$cart_product.name}
	{if isset($cart_product.variants)} -
		{foreach name=variants item=variant from=$cart_product.variants}
			{$variant}
			{if !$smarty.foreach.variants.last}, {/if}
		{/foreach}
	{/if}</a></li>
{foreachelse}
	<li>Cart empty</li>
{/foreach}
</ul>
<p id="cart-total">$ {$cart.price_total|number_format:2}</p>
{if $smarty.foreach.mini_cart.total > 0 AND $controller.action != 'checkout' AND $controller.action != 'shipping' AND $controller.action != 'payment'}
<a title="Proceed to Checkout" href="{$https_location}/checkout"><img id="mini-checkout" src="images/layout/miniCheckout.gif" width="110" height="15" alt="Proceed to Checkout" /></a>
<br /><br />
{/if}