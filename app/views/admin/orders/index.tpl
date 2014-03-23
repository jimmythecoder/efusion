{* Customer orders *}
{assign var="page_title" value="Customer orders"}


<form action="admin/orders/index" method="get">
<p><label for="filter_by">Find by customer name or order #: </label><input class="text" type="text" name="filter_by" id="filter_by" value="{$smarty.get.filter_by}" /> <input type="submit" value="&gt;&gt;" /></p>
<input type="hidden" name="sort" value="{if !empty($smarty.get.sort)}{$smarty.get.sort}{/if}"/>
</form>

<table class="paginated" cellspacing="0" cellpadding="0" summary="Customer orders">
<thead>
<tr>
	<th><a title="Sort by order number" href="admin/orders/index?page={$orders_paged.current_page_index}&amp;sort=order-number{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Order #</a></th>
	<th><a title="Sort by order status" href="admin/orders/index?page={$orders_paged.current_page_index}&amp;sort=status{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Status</a></th>
	<th><a title="Sort by customer name" href="admin/orders/index?page={$orders_paged.current_page_index}&amp;sort=customer-name{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Customer Name</a></th>
	<th><a title="Sort by order value" href="admin/orders/index?page={$orders_paged.current_page_index}&amp;sort=total{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Amount</a></th>
	<th><a title="Sort by creation date" href="admin/orders/index?page={$orders_paged.current_page_index}&amp;sort=created-at{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Created At</a></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5" class="pagination">View page: 
	{section name=pagenation loop=$orders_paged.total_pages_available}
	{if $smarty.section.pagenation.iteration == $orders_paged.current_page_index}
	  	<em>{$smarty.section.pagenation.iteration}</em>
	{else}
	  	<a title="View page {$smarty.section.pagenation.iteration}" href="admin/orders/index?page={$smarty.section.pagenation.iteration}{if !empty($smarty.get.sort)}&amp;sort={$smarty.get.sort}{/if}{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">[{$smarty.section.pagenation.iteration}]</a>
	{/if}
	{/section}</td>
</tr>
</tfoot>
{foreach item=order from=$orders_paged.records}
<tr class="{cycle values='alt-row,'} {$order.status}">
	<td><a title="View / Modify order" href="admin/orders/edit/{$order.id}">{$order.reference_code}</a></td>
	<td>{$order.status|upper}</td>
	<td>{$order.first_name} {$order.last_name}</td>
	<td>${$order.total|number_format:2}</td>
	<td>{$order.created_at|date_format:'%A, %e %b %Y. %l:%M %p'}</td>
</tr>
{foreachelse}
<tr>
	<td colspan="5">No orders to display</td>
</tr>
{/foreach}
</table>
