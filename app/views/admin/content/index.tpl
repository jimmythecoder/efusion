{* Page list *}
{assign var="page_title" value="Content Management"}


<form action="admin/content/index" method="get">
<p><label for="filter_by">Filter results by: </label><input class="text" type="text" name="filter_by" id="filter_by" value="{$smarty.get.filter_by}" /> <input type="submit" value="&gt;&gt;" /></p>
<input type="hidden" name="sort" value="{if !empty($smarty.get.sort)}{$smarty.get.sort}{/if}"/>
</form>

<table class="paginated" cellspacing="0" cellpadding="0" summary="Content pages">
<thead>
<tr>
	<th><a title="Sort by page title" href="admin/content/index?page={$content_paged.current_page_index}&amp;sort=title{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Page Title</a></th>
	<th>Action</th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="2" class="pagination">
	{section name=pagenation loop=$content_paged.total_pages_available}
	{if $smarty.section.pagenation.iteration == $content_paged.current_page_index}
	  	<em>{$smarty.section.pagenation.iteration}</em>
	{else}
	  	<a title="View page {$smarty.section.pagenation.iteration}" href="admin/content/index?page={$smarty.section.pagenation.iteration}{if !empty($smarty.get.sort)}&amp;sort={$smarty.get.sort}{/if}{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">[{$smarty.section.pagenation.iteration}]</a>
	{/if}
	{/section}
	- <a href="admin/content/create">Create new page</a></td>
</tr>
</tfoot>
{foreach item=content from=$content_paged.records}
<tr {cycle values='class="alt-row",'}>
	<td>{$content.title|escape:html}</td>
	<td><a href="admin/content/edit/{$content.id}">Modify</a> | <a class="popup-link" href="{$http_location}/page/{$content.url_name}">Preview</a></td>
</tr>
{foreachelse}
<tr>
	<td colspan="2">No pages to display</td>
</tr>
{/foreach}
</table>
