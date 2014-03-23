{foreach item=category key=category_id from=$categories}
	<li class="{if $first_child == true}first-child{/if}{if isset($selected_category_id) AND $category_id == $selected_category_id AND $controller.action == 'catalog'} active{/if}"><a title="{$category.description|escape:html}" href="{$http_location}/catalog/{$category.url_name|escape:"url"}{if isset($smarty.get.sort)}?sort={$smarty.get.sort}{/if}">{$category.name|escape:"html"}</a></li>
	{assign var="first_child" value=false}
	{if isset($category.children)}
		<li class="child-parent"><ul class="left-navigation">{include file='components/categories.tpl' categories=$category.children first_child=true}</ul></li>
	{/if}	
{/foreach}
