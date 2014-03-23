{* Product Catalogue template *}
{assign var="page_title" value=$category.name|default:'Featured Products'}
{assign var="meta_description" value=$category.description}
{javascript_include_tag file="catalog"}

{$category.description}

{if $category.id}
<div id="sort-page-by">
	<form action="{$http_location}/catalog/{$category.url_name}" method="get">
	Sort by 
	<select name="sort" id="sort-select">
	<option {if $smarty.get.sort == 'newest'}selected="selected"{/if} value="newest">Newest</option>
	<option {if $smarty.get.sort == 'price'}selected="selected"{/if} value="price">Price</option>
	<option {if $smarty.get.sort == 'name'}selected="selected"{/if} value="name">Name</option>
	</select>
	{if isset($smarty.get.page)}<input type="hidden" name="page" value="{$smarty.get.page}" />{/if}
	<input type="submit" value="&gt;&gt;" id="sort-button" />
	</form>
</div>
{/if}

{include file="components/list_products.tpl" products=$category_products}

{if isset($pagination) && $pagination.number_of_pages > 1}
<div id="pagenation">
	<ul class="pagenation">
	{if $pagination.show_previous_arrow}<li><a href="catalog/{$category.url_name}?page={$pagination.previous_page_number}{if isset($smarty.get.sort)}&amp;sort={$smarty.get.sort}{/if}">&lt;</a></li></a>{/if}
	{section name=pagenation start=$pagination.previous_page_number loop=$pagination.upper_page_limit}
	  {if $smarty.section.pagenation.index_next == $pagination.current_page_number}
	  	<li class="active">{$smarty.section.pagenation.index_next}</li>
	  {else}
	  	<li><a href="catalog/{$category.url_name}?page={$smarty.section.pagenation.index_next}{if isset($smarty.get.sort)}&amp;sort={$smarty.get.sort}{/if}">{$smarty.section.pagenation.index_next}</a></li>
	  {/if}
	{/section}
	{if $pagination.show_next_arrow}<li><a href="catalog/{$category.url_name}?page={$pagination.next_page_number}{if isset($smarty.get.sort)}&amp;sort={$smarty.get.sort}{/if}">&gt;</a></li></a>{/if}
	</ul>
</div>
{/if}

<br class="clearer" />