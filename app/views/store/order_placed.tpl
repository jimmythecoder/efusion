{* Order placed page *}
{assign var="page_title" value=$order_placed_page.title}
{stylesheet_include_tag file="confirm_order"}
{javascript_include_tag file="lib/thickbox"}

{$order_placed_page.content}

{include file="components/order_summary.tpl"}

{* include file="components/urchin_order_tracking.tpl" *}