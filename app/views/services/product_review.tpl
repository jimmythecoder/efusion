<successfull>{if isset($flash.error)}0{else}1{/if}</successfull>
<average_rating>{$average_rating}</average_rating>
<reviewed_at>{$reviewed_at|history_date_format}</reviewed_at>
<flash_errors>
	{if isset($flash.error)}
		{section name=message loop=$flash.error}
			<error>{$flash.error[message]|replace:'_':' '|escape:'html'}</error>
		{/section}
	{/if}
</flash_errors>