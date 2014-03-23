{* Banner list *}
{assign var="page_title" value="Site banners"}

You can set which banner you wish to appear on the site by clicking activate.

<table class="paginated" cellspacing="0" cellpadding="0" summary="Banners">
<thead>
<tr>
	<th>Name</th>
	<th>Action</th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3" class="pagination"><a href="admin/banners/create">Upload a new banner</a></td>
</tr>
</tfoot>
{foreach item=banner from=$banners}
<tr class="{cycle values='alt-row,'} {if $banner.is_active}active{/if}">
	<td>{$banner.name|escape:html}</td>
	<td><a href="admin/banners/edit/{$banner.id}">Modify</a> {if NOT $banner.is_active}| <a href="admin/banners/activate/{$banner.id}">Activate</a>{/if}
	| <a class="popup-link" href="images/banners/{$banner.filename}">Preview</a></td>
</tr>
{/foreach}
</table>

