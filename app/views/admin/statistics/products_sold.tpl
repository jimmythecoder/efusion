{assign var="page_title" value="Products Sold"}

<form action="admin/statistics/products-sold" method="get">
<p><label for="filter_by">Filter results by: </label><input class="text" type="text" name="filter_by" id="filter_by" value="{$smarty.get.filter_by}" /> <input type="submit" value="&gt;&gt;" /></p>
<input type="hidden" name="sort" value="{if !empty($smarty.get.sort)}{$smarty.get.sort}{/if}"/>
</form>

<table class="paginated" cellspacing="0" cellpadding="0" summary="Products sold">
<thead>
<tr>
	<th><a title="Sort by Product name" href="admin/statistics/products-sold?page={$products_paged.current_page_index}&amp;sort=name{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Name</a></th>
	<th><a title="Sort by Units sold" href="admin/statistics/products-sold?page={$products_paged.current_page_index}&amp;sort=views{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Units Sold</a></th>
	<th>Total Sale Value</th>
	<th>Profitability</th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4" class="pagination">
	{section name=pagenation loop=$products_paged.total_pages_available}
	{if $smarty.section.pagenation.iteration == $products_paged.current_page_index}
	  	<em>{$smarty.section.pagenation.iteration}</em>
	{else}
	  	<a title="View page {$smarty.section.pagenation.iteration}" href="admin/statistics/products-sold?page={$smarty.section.pagenation.iteration}{if !empty($smarty.get.sort)}&amp;sort={$smarty.get.sort}{/if}{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">[{$smarty.section.pagenation.iteration}]</a>
	{/if}
	{/section}</td>
</tr>
</tfoot>
{foreach item=product from=$products_paged.records}
{assign var="total_sale_value" value=$product.sale_price*$product.units_sold}
{assign var="total_cost_value" value=$product.cost_price*$product.units_sold}
<tr {cycle values=',class="alt-row"'}>
	<td><a href="{$http_location}/product/{$product.url_name}">{$product.name}</a></td>
	<td>{$product.units_sold}</td>
	<td>${$total_sale_value|number_format:2}</td>
	<td>${$total_sale_value-$total_cost_value|number_format:2}</td>
</tr>
{/foreach}
</table>

<p><a href="admin/statistics/index">&lt;&lt; Back to statistics</a></p>