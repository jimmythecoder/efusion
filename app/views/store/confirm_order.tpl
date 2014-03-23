{* Confirm order page *}
{assign var="page_title" value='Confirm Your Order'}
{stylesheet_include_tag file="confirm_order"}
{javascript_include_tag file="lib/thickbox"}

<p class="confirm-article">{$content.content}</p>

{include file="components/order_summary.tpl"}

<span class="left"><a title="Change my order" href="{$https_location}/checkout"><img src="images/layout/change_my_order.gif" alt="Change my order" /></a></span>
<form method="post" action="{$https_location}/confirm-order"><span class="right"><input type="image" name="place_order" id="place-order" src="images/layout/place_order.gif" alt="Place Order" /></span></form>
<div class="clearer"></div>