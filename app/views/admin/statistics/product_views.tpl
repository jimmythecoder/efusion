{assign var="page_title" value="Product Views"}

<form action="admin/statistics/product-views" method="get">
<p><label for="filter_by">Filter results by: </label><input class="text" type="text" name="filter_by" id="filter_by" value="{$smarty.get.filter_by}" /> <input type="submit" value="&gt;&gt;" /></p>
<input type="hidden" name="sort" value="{if !empty($smarty.get.sort)}{$smarty.get.sort}{/if}"/>
</form>

<table class="paginated" cellspacing="0" cellpadding="0" summary="Product views">
<thead>
<tr>
	<th><a title="Sort by Product name" href="admin/statistics/product-views?page={$products_paged.current_page_index}&amp;sort=name{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Name</a></th>
	<th><a title="Sort by Product views" href="admin/statistics/product-views?page={$products_paged.current_page_index}&amp;sort=views{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Views</a></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="2" class="pagination">
	{section name=pagenation loop=$products_paged.total_pages_available}
	{if $smarty.section.pagenation.iteration == $products_paged.current_page_index}
	  	<em>{$smarty.section.pagenation.iteration}</em>
	{else}
	  	<a title="View page {$smarty.section.pagenation.iteration}" href="admin/statistics/product-views?page={$smarty.section.pagenation.iteration}{if !empty($smarty.get.sort)}&amp;sort={$smarty.get.sort}{/if}{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">[{$smarty.section.pagenation.iteration}]</a>
	{/if}
	{/section}</td>
</tr>
</tfoot>
{foreach item=product from=$products_paged.records}
<tr {cycle values=',class="alt-row"'}>
	<td><a href="{$http_location}/product/{$product.url_name}">{$product.name}</a></td>
	<td>{$product.views}</td>
</tr>
{/foreach}
</table>

<p><a href="admin/statistics/index">&lt;&lt; Back to statistics</a></p>