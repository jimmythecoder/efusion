{* Page list *}
{assign var="page_title" value="Customer account management"}


<form action="{$https_location}/admin/customers/index" method="get">
<p><label for="filter_by">Filter results by: </label><input class="text" type="text" name="filter_by" id="filter_by" value="{$smarty.get.filter_by}" /> <input type="submit" value="&gt;&gt;" /></p>
<input type="hidden" name="sort" value="{if !empty($smarty.get.sort)}{$smarty.get.sort}{/if}"/>
</form>

<table class="paginated" cellspacing="0" cellpadding="0" summary="Content pages">
<thead>
<tr>
	<th><a title="Sort by Customer last name" href="{$https_location}/admin/customers/index?page={$accounts_paged.current_page_index}&amp;sort=last_name{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Full name</a></th>
	<th><a title="Sort by E-Mail address" href="{$https_location}/admin/customers/index?page={$accounts_paged.current_page_index}&amp;sort=email{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">E-Mail address</a></th>
	<th><a title="Sort by Phone number" href="{$https_location}/admin/customers/index?page={$accounts_paged.current_page_index}&amp;sort=phone{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Phone number</a></th>
	<th><a title="Sort by Joined on date" href="{$https_location}/admin/customers/index?page={$accounts_paged.current_page_index}&amp;sort=created_at{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Joined on</a></th>
	<th>Action</th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5" class="pagination">
	{section name=pagenation loop=$accounts_paged.total_pages_available}
	{if $smarty.section.pagenation.iteration == $accounts_paged.current_page_index}
	  	<em>{$smarty.section.pagenation.iteration}</em>
	{else}
	  	<a title="View page {$smarty.section.pagenation.iteration}" href="{$https_location}/admin/customers/index?page={$smarty.section.pagenation.iteration}{if !empty($smarty.get.sort)}&amp;sort={$smarty.get.sort}{/if}{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">[{$smarty.section.pagenation.iteration}]</a>
	{/if}
	{/section}
	</td>
</tr>
</tfoot>
{foreach item=account from=$accounts_paged.records}
<tr {cycle values='class="alt-row",'}>
	<td>{$account.first_name|escape:html} {$account.last_name|escape:html}</td>
	<td><a title="E-Mail this customer" href="mailto:{$account.email|escape:html}">{$account.email|escape:html}</a></td>
	<td>{$account.phone|escape:html}</td>
	<td>{$account.created_at|date_format:'%A, %e %b %Y.'}</td>
	<td><a href="{$https_location}/admin/customers/edit/{$account.id}">Modify</a></td>
</tr>
{foreachelse}
<tr>
	<td colspan="5">No customers to display</td>
</tr>
{/foreach}
</table>
