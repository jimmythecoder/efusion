{if $parent_category.id == 0}
	{assign var="page_title" value="Product Categories"}
{else}
	{assign var="page_title" value="Sub Categories for `$parent_category.name`"}
{/if}

To sort your categories, simply click on a category name and drag it up or down
to your preferred spot. To change a category name, click modify. To view a categories
hierarchy, click view sub categories.

<ul id="category-list">
	<li><span class="col1"><strong>Name</strong></span><span class="col2"><strong>Action</strong></span></li>
{foreach item=category from=$categories}
	<li id="category-{$category.id}" class="sortable {cycle values=',alt-row'}">
	<span class="col1">{$category.name|escape:"html"}</span>
	<span class="col2"><a href="admin/categories/edit/{$category.id}">Modify</a> | <a href="admin/categories/index/{$category.id}">View Sub Categories</a> | <a title="View products in {$category.name|escape:"html"}" href="admin/products/index?c={$category.id}">View products ({$category.product_count})</a></span></li>
{foreachelse}
<li>No categories to display</li>
{/foreach}
</ul>
<span class="left small">{if $parent_category.id != 0}<a href="admin/categories/index/{$parent_category.category_id}">&lt;&lt; View parent category</a>{else}&nbsp;{/if}</span>
<span class="right small"><strong><a href="admin/categories/create/{$parent_category.id|default:0}">Create new category</a></strong></span>


{literal}
<script type="text/javascript">
<!--
$(document).ready(function()
{
	$('#category-list').Sortable(
	{
		accept : 'sortable',
		activeclass : 'sortable-active',
		hoverclass : 'sortable-hover',
		helperclass : 'sort-helper',
		opacity: 	0.7,
		fit :	false,
		onStop : save_category_order,
		axis : 'vertically'
	}
)
});

function save_category_order()
{
	var json_category_order = $.SortSerialize('category-list');
	
	$.post('admin/categories/save-category-order',json_category_order.hash,function(response){
		$("#category-list").before('<ul id="flash-notices"><li>Sort order updated successfully.</li></ul>');
		setTimeout('fade_flash_notices()',5000);
	});
}
-->
</script>
{/literal}