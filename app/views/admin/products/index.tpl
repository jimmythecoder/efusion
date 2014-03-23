{* Product list *}
{assign var="page_title" value="Product Management"}
{stylesheet_include_tag file="thickbox"}
{javascript_include_tag file="lib/thickbox"}

<form action="admin/products/index" method="get">
<p><label for="filter_by">Filter results by: </label><input class="text" type="text" name="filter_by" id="filter_by" value="{$smarty.get.filter_by|escape:"html"}" /> 
 In Category: 
 <select name="c" id="product_category" tabindex="3">
	<option value="0" class="strong">All Categories</option>
	{include file="components/recursive_option_select.tpl" categories=$product_categories selected=$search_in_category}
 </select>
 <input type="submit" value="&gt;&gt;" /></p>

<input type="hidden" name="sort" value="{if !empty($smarty.get.sort)}{$smarty.get.sort}{/if}"/>
</form>

<table class="paginated" cellspacing="0" cellpadding="0" summary="Products in store">
<thead>
<tr>
	<th>&nbsp;</th>
	<th><a title="Sort by Product name" href="admin/products/index?page={$products_paged.current_page_index}&amp;sort=name{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">Name</a></th>
	<th>Action</th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3" class="pagination">
	{section name=pagenation loop=$products_paged.total_pages_available}
	{if $smarty.section.pagenation.iteration == $products_paged.current_page_index}
	  	<em>{$smarty.section.pagenation.iteration}</em>
	{else}
	  	<a title="View page {$smarty.section.pagenation.iteration}" href="admin/products/index?page={$smarty.section.pagenation.iteration}{if !empty($smarty.get.sort)}&amp;sort={$smarty.get.sort}{/if}{if !empty($smarty.get.filter_by)}&amp;filter_by={$smarty.get.filter_by}{/if}">[{$smarty.section.pagenation.iteration}]</a>
	{/if}
	{/section}
	- <a href="admin/products/create{if !empty($smarty.get.category)}?category={$smarty.get.category}{/if}">Create product</a></td>
</tr>
</tfoot>
{foreach item=product from=$products_paged.records}
<tr {cycle values='class="alt-row",'}>
	<td class="product-image">{if $product.image_id != DEFAULT_IMAGE_ID}<a title="{$product.name|escape:"quotes"}" class="thickbox" href="/images/products/{$product.image_filename|escape:'url'}">{/if}<img src="{get_image_src_resized_and_cropped_to width="40" height="30" filename=$product.image_filename}" alt="{$product.name|escape:"html"}" />{if $product.image_id != DEFAULT_IMAGE_ID}</a>{/if}</td>
	<td class="{if NOT $product.is_active}disabled{/if}">{$product.name}</td>
	<td><a title="Update details for this product" href="admin/products/edit/{$product.id}">Modify</a> | <a title="View and modify reviews for this product" href="admin/reviews/product/{$product.id}">Reviews</a> | <a  title="View how this product looks to a customer" class="popup-link" href="{$http_location}/product/{$product.url_name}">Preview</a></td>
</tr>
{foreachelse}
<tr>
	<td colspan="2">No products to display</td>
</tr>
{/foreach}
</table>
