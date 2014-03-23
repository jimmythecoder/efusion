{* Site Home Page *}
{assign var="page_title" value=$content.title}
{assign var="meta_keywords" value=$content.keywords}
{assign var="meta_description" value=$content.description}

{$content.content}
<h3>Latest Products</h3>
{include file="components/list_products.tpl" products=$latest_products}
<div class="clearer"></div>