{* Display a paginated table view *}

<table class="paginated" cellspacing="0" cellpadding="0" summary="Paginated table">
<tr>
	{foreach key=col item=column from=$records.records[0]}
	<th>{$col|replace:'_':' '|capitalize}</th>
	{/foreach}
</tr>
{foreach item=row from=$records.records}
<tr {cycle values=',class="alt-row"'}>
	{foreach item=value from=$row}
	<td>{if isset($link_url)}<a href="{$link_url}">{/if}{$value}{if isset($link_url)}</a>{/if}</td>
	{/foreach}
</tr>
{/foreach}
</table>