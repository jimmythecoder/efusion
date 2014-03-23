{* Displays a breadcrumb trail of where you are in the site hierarchy *}
<p class="link-trail">You are in:
{foreach name=breadcrumb item=item from=$breadcrumb}
	{if NOT $smarty.foreach.breadcrumb.first}
		&gt;&gt;
	{/if}
	
	{foreach name=link item=content key=url from=$item}
		{if NOT $smarty.foreach.breadcrumb.last}
			<a href="{$current_location}/{$url}">{$content|truncate:30|escape:'html'}</a>
		{else}
			{$content|truncate:30|escape:'html'}
		{/if}
	{/foreach}
{/foreach}
</p>
