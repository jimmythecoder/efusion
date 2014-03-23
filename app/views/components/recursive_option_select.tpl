{if !isset($depth)}{assign var=depth value=0}{/if}
{foreach item=category from=$categories}
	<option value="{$category.id}"{if isset($selected) && $selected == $category.id} selected="selected"{/if}>{$category.name|escape:'html'|indent:$depth:"&nbsp;&nbsp;"}</option>
	{if !empty($category.children)}{include file="components/recursive_option_select.tpl" categories=$category.children depth=$depth+1}{/if}
{/foreach}