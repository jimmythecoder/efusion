{* Product variants list *}
{assign var="page_title" value="Product Variation Management"}


<form action="admin/variations/index" method="get">
<p><label for="filter_by">Filter results by: </label><input class="text" type="text" name="filter_by" id="filter_by" value="{$smarty.get.filter_by}" /> <input type="submit" value="&gt;&gt;" /></p>
<input type="hidden" name="sort" value="{if !empty($smarty.get.sort)}{$smarty.get.sort}{/if}"/>
</form>

<table class="paginated" cellspacing="0" cellpadding="0" summary="Product variations created">
<thead>
<tr>
	<th><a title="Sort by variation group name" href="admin/variations/index?page={$product_variants_paged.current_page_index}&amp;sort=name{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Variation Group Name</a></th>
	<th>Action</th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="2" class="pagination">
	{section name=pagenation loop=$product_variants_paged.total_pages_available}
	{if $smarty.section.pagenation.iteration == $product_variants_paged.current_page_index}
	  	<em>{$smarty.section.pagenation.iteration}</em>
	{else}
	  	<a title="View page {$smarty.section.pagenation.iteration}" href="admin/variations/index?page={$smarty.section.pagenation.iteration}{if !empty($smarty.get.sort)}&amp;sort={$smarty.get.sort}{/if}{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">[{$smarty.section.pagenation.iteration}]</a>
	{/if}
	{/section}
	- <a href="admin/variations/create">Create variation group</a></td>
</tr>
</tfoot>
{foreach item=variation from=$product_variants_paged.records}
<tr {cycle values='class="alt-row",'}>
	<td>{$variation.name}</td>
	<td><a href="admin/variations/edit/{$variation.id}">Modify</a> | <a href="admin/variations/variants/{$variation.id}">View variant options</a> </td>
</tr>
{foreachelse}
<tr>
	<td colspan="2">No variations to display</td>
</tr>
{/foreach}
</table>
