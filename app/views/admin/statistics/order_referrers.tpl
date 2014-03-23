{assign var="page_title" value="Orders by referrer"}

A summary of orders purchased by referrer. From here you can tell who
are your most profitable referrers for your marketing campaigns.

<table class="paginated" cellspacing="0" cellpadding="0" summary="Orders purchased by referrer">
<tr>
	<th>Referrer URL</th>
	<th>Number of orders</th>
	<th>Total order value</th>
</tr>
{foreach item=row from=$order_referrers}
<tr {cycle values=',class="alt-row"'}>
	<td>{$row.url}</td>
	<td>{$row.number_of_orders}</td>
	<td>${$row.order_value|number_format:2}</td>
</tr>
{/foreach}
</table>

<p><a href="admin/statistics/index">&lt;&lt; Back to statistics</a></p>