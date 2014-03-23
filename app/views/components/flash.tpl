{if isset($flash.error)}
	<ul id="flash-errors">
	{section name=message loop=$flash.error}
		<li>{$flash.error[message]|replace:'_':' '|escape:'html'}</li>
	{/section}</ul>
{/if}

{if isset($flash.notice)}
	<ul id="flash-notices">
	{section name=message loop=$flash.notice}
		<li>{$flash.notice[message]|replace:'_':' '|escape:'html'}</li>
	{/section}</ul>
{/if}