{* List all orders for this user *}
{assign var="page_title" value="View My Orders"}

<table id="order-list" cellspacing="0" summary="Sorted list of my orders">
<thead>
<tr>
	<th><a title="Sort by Order Number" href="my-account/orders/index?page={$orders_paged.current_page_index}&amp;sort=reference_code">Order #</a></th>
	<th><a title="Sort by Order Status" href="my-account/orders/index?page={$orders_paged.current_page_index}&amp;sort=status">Status</a></th>
	<th><a title="Sort by Order Total" href="my-account/orders/index?page={$orders_paged.current_page_index}&amp;sort=total">Order Amount</a></th>
	<th><a title="Sort by Order Date" href="my-account/orders/index?page={$orders_paged.current_page_index}&amp;sort=created_at">Ordered at</a></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4" class="pagination">View page: 
	{section name=pagenation loop=$orders_paged.total_pages_available}
	{if $smarty.section.pagenation.iteration == $orders_paged.current_page_index}
	  	<em>{$smarty.section.pagenation.iteration}</em>
	{else}
	  	<a title="View page {$smarty.section.pagenation.iteration}" href="my-account/orders/index?page={$smarty.section.pagenation.iteration}{if !empty($smarty.get.sort)}&amp;sort={$smarty.get.sort}{/if}">[{$smarty.section.pagenation.iteration}]</a>
	{/if}
	{/section}</td>
</tr>
</tfoot>
{foreach item=order from=$orders_paged.records}
<tr class="{$order.status} {cycle values='alt,'}">
	<td><a title="View order" href="/my-account/orders/view/{$order.id}">{$order.reference_code}</a></td>
	<td>{$order.status|capitalize}</td>
	<td>${$order.total|number_format:2}</td>
	<td>{$order.created_at|date_format:'%A, %e %b %Y. %l:%M %p'}</td>
</tr>
{foreachelse}
<tr>
	<td colspan="4">You have not yet placed any orders.</td>
</tr>
{/foreach}
</table>