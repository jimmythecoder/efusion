{* Export dynamic store data *}
{assign var="page_title" value="Export store data"}
{javascript_include_tag file="export_store_data"}

<form action="admin/integration/export" method="post">
<table summary="Export criteria">
<tr>
	<th>Download</th>
 	<td>
		<label><input {if empty($smarty.get.what) OR $smarty.post.what == "products"}checked="checked"{/if} type="radio" value="products" name="what" />Products</label><br />
		<label><input {if $smarty.post.what == "categories"}checked="checked"{/if} type="radio" value="categories" name="what" />Categories</label><br />
		<label><input {if $smarty.post.what == "orders"}checked="checked"{/if} type="radio" value="orders" name="what" />Orders</label><br />
		<label><input {if $smarty.post.what == "ordered-products"}checked="checked"{/if} type="radio" value="ordered-products" name="what" />Ordered Products</label><br />
	</td>
</tr>
<tr>
	<th>Format</th>
	<td>
		<label><input {if $smarty.post.format != "csv"}checked="checked"{/if} type="radio" value="xml" name="format" />XML</label>
		<label><input {if $smarty.post.format == "csv"}checked="checked"{/if} type="radio" value="csv" name="format" />CSV</label>
	</td>
</tr>
<tr>
	<th>Filters</th>
	<td><label><input id="export-all" type="checkbox" name="export_all" checked="checked" value="1" />None</label></td>
</tr>
<tr id="date-filter">
	<th>In Date Range</th>
	<td><input class="text" id="start_date" type="text" value="{$start_date}" name="start_date" /><input type="button" id="start_date_trigger" value="..." />
 		To
		<input class="text" id="end_date" type="text" value="{$end_date}" name="end_date" /><input type="button" id="end_date_trigger" value="..." />
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" name="export" value="Export &gt;&gt;" /></td>
</tr>
</table>
</form>
