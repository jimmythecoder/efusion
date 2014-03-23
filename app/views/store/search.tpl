{* Site search form and results *}
{assign var="page_title" value="Search"}

<form action="{$http_location}/search" method="get">
	<input type="text" class="text" name="q" id="search_query" value="{$search.query|escape:'html'}" maxlength="255" tabindex="1" />
	<input type="submit" class="button" name="b" id="search_button" value="Search" tabindex="2" />
	in
	<select name="c" id="product_category" tabindex="3">
		<option value="0" class="strong">All Categories</option>
		{include file="components/recursive_option_select.tpl" categories=$product.categories selected=$search.in_category}
	</select>
</form>
{if isset($search.results_count) AND $search.results_count > 0 AND $search.is_active == true}
	<p id="summary">Displaying results <b>{$search.start_from_row+1} - {$search.last_record}</b> of <b>{$search.results_count}</b></p>
{/if}

<ul id="search-results">
{if $search.is_active == true}
	{foreach item=product from=$search.results}
		<li>
			<a class="product-image" title="{$product.name|strip_tags|escape:"quotes"}" href="{$http_location}/product/{$product.url_name}"><img src="{get_image_src_resized_and_cropped_to filename=$product.image_filename width="60" height="50"}" alt="{$product.name|escape:"html"}" /></a
			><div class="right-content"><h3><a href="{$http_location}/product/{$product.url_name}">{highlight_keywords keywords=$search.keywords content=$product.name}</a></h3>
			<p>{highlight_keywords keywords=$search.keywords content=$product.description|strip_tags|truncate:'120':' ...'}</p>
			<a class="url" title="View product" href="{$http_location}/product/{$product.url_name}">{$http_location}/product/{$product.url_name|escape:'url'}</a>
			</div>
		</li>
	{foreachelse}
		<li>Your search did not return any results</li>
	{/foreach}
{else}
	<li>Please enter your search query above</li>
{/if}
</ul>

{if $search.page_count > 1}
<div id="pagenation">
	<ul class="pagenation">
	{section name=pagenation loop=$search.page_count}
	  {if $smarty.section.pagenation.iteration == $search.page_number}
	  	<li class="active">{$smarty.section.pagenation.iteration}</li>
	  {else}
	  	<li><a href="{$http_location}/search?page={$smarty.section.pagenation.iteration}&amp;q={$smarty.get.q|escape:'url'}&amp;c={$smarty.get.c|escape:'url'}">{$smarty.section.pagenation.iteration}</a></li>
	  {/if}
	{/section}
	</ul>
</div>
{/if}