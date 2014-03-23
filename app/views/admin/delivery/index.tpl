{* Shipping tiers *}
{assign var="page_title" value="Delivery Charges"}
The following table is used to calculate the delivery charges on the site and works
from a weight/zone matrix. The further the delivery has to go from the stores city to
its destination puts it into another zone. A higher weight will use a higher tier. 

<form action="{$https_location}/admin/delivery/index" method="post">
{foreach key=zone_name item=zone from=$delivery_matrix}
<fieldset><legend>{$zone_name}</legend>
	<table class="form" cellpadding="3" cellspacing="0" summary="Delivery Charges for {$zone_name}">
	<tr>
		<th>Order weight &lt; (Kg)</th>
		<th>Charge amount ($)</th>
	</tr>
	{foreach item=tier from=$zone}
	<tr class="required">
		<td><input type="text" class="text numeric" name="shipping_tier[{$tier.id}][max_weight]" id="shipping_tier_max_weight_{$tier.id}" value="{$tier.max_weight}" /></td>
		<td><input type="text" class="text numeric" name="shipping_tier[{$tier.id}][amount]" id="shipping_tier_amount_{$tier.id}" value="{$tier.amount|number_format:2}" /></td>
	</tr>
	{/foreach}
	</table>
</fieldset>
{/foreach}

<fieldset><legend>Save delivery charges</legend>
	<input type="submit" name="save" id="save_delivery_charges" value="Save" />
	<input type="submit" name="cancel" id="cancel_delivery_charges" value="Cancel" />
</fieldset>
</form>