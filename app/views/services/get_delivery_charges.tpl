<from>{$from_city|escape}</from>
<to>{$to_city|escape}</to>
<zone>{$zone.display_name|escape}</zone>
<weight>{$weight|number_format:2}</weight>
<discount_amount>0.00</discount_amount>
<total>{$total_cost_of_delivery|number_format:2}</total>
<is_successfull>{if $is_successfull}1{else}0{/if}</is_successfull>
{if NOT $is_successfull}
<errors>
{foreach item=error from=$errors}
	<error>{$error}</error>
{/foreach}
</errors>
{/if}