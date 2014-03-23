{* Display the users shopping cart contents *}
{assign var="page_title" value="Shopping Cart"}
{stylesheet_include_tag file="cart"}

Update your cart as necessary to suit your order.
<form action="cart" method="post">
{include file="components/cart.tpl"}
</form>
{if $smarty.foreach.cart.total > 0}<p id="checkout-link"><a title="Proceed to Checkout" href="{$https_location}/checkout"><img src="images/layout/checkout.gif" width="150" height="20" alt="Proceed to Checkout" /></a></p>{/if}

{if count($related_and_featured_products)}
	<div id="related_and_featured_products">
		<h3>You may also be interested in...</h3>
		{include file="components/list_products.tpl" products=$related_and_featured_products}
	</div>
{/if}