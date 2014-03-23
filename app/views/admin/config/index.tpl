{* Store configuration *}
{assign var="page_title" value="Your online store settings"}
{literal}
<script type="text/javascript">
<!--
$(document).ready(function()
{
	$('#config_payment_method_credit_card').bind('change',function(){
		if(this.checked)
		{
			enable_field('#payment_gateway_username');
			enable_field('#payment_gateway_password');
		}
		else
		{
			disable_field('#payment_gateway_username');
			disable_field('#payment_gateway_password');
		}
	});

	$('#host_enable_ssl').bind('change',function(){
		if(this.checked)
			enable_field('#host_ssl_subdomain');
		else
			disable_field('#host_ssl_subdomain');
	});
	
	$('#config_adsense_enabled').bind('change',function(){
		if(this.checked)
			enable_field('#config_adsense_key');
		else
			disable_field('#config_adsense_key');
	});
	
	{/literal}
	{if NOT $config.payment_method.credit_card}
		disable_field('#payment_gateway_username');
		disable_field('#payment_gateway_password');
	{/if}
	{if NOT $config.host.enable_ssl}
		disable_field('#host_ssl_subdomain');
	{/if}
	{if NOT $config.adsense.enabled}
		disable_field('#config_adsense_key');
	{/if}
	{literal}
});

function enable_field(field)
{
	$(field).removeAttr('disabled');
	$(field).removeClass('disabled');
}

function disable_field(field)
{
	$(field).attr('disabled','disabled');
	$(field).addClass('disabled');
}

-->
</script>
{/literal}

Update your settings as necessary and click save. Please double check your settings are
correct before saving. You may wish to consult your manual for more detailed information.

<form action="{$https_location}/admin/config/index" method="post">
<fieldset><legend>Core installation settings</legend>
<table class="form" cellspacing="0" cellpadding="0" summary="Core installation settings">
<tr class="required">
	<td><label>Accept payment via</label></td>
	<td><label><input type="checkbox" name="config[payment_method][credit_card]" id="config_payment_method_credit_card" value="1" {if $config.payment_method.credit_card}checked="checked"{/if} />Credit Card (Recommended)</label><br />
		<label><input type="checkbox" name="config[payment_method][bank_deposit]" id="config_payment_method_bank_deposit" value="1" {if $config.payment_method.bank_deposit}checked="checked"{/if} />Bank deposit</label></td>
</tr>
<tr>
	<td><label for="payment_gateway_username">DPS User ID</label></td>
	<td><input class="text" type="text" name="config[payment_gateway][username]" id="payment_gateway_username" value="{$config.payment_gateway.username}" /></td>
</tr>
<tr>
	<td><label for="payment_gateway_password">DPS PX Pay Key</label></td>
	<td><input class="text" type="text" name="config[payment_gateway][password]" id="payment_gateway_password" value="{$config.payment_gateway.password}" /></td>
</tr>
<tr class="required">
	<td><label for="host_domain">Domain name</label></td>
	<td><input class="text" type="text" name="config[host][domain]" id="host_domain" value="{$config.host.domain}" /><em>*</em></td>
</tr>
<tr class="required">
	<td><label for="host_subdomain">Sub domain</label></td>
	<td><input class="text" type="text" name="config[host][subdomain]" id="host_subdomain" value="{$config.host.subdomain}" /><em>*</em></td>
</tr>
<tr>
	<td><label for="host_enable_ssl">Enable SSL (Recommended)</label></td>
	<td><label><input type="checkbox" name="config[host][enable_ssl]" id="host_enable_ssl" value="1" {if $config.host.enable_ssl}checked="checked"{/if} /> Yes</label></td>
</tr>
<tr>
	<td><label for="host_ssl_subdomain">SSL sub domain name</label></td>
	<td><input class="text" type="text" name="config[host][ssl_subdomain]" id="host_ssl_subdomain" value="{$config.host.ssl_subdomain}" /></td>
</tr>
</table>
</fieldset>
	
	<fieldset><legend>Your store contact information</legend>
	<table class="form" cellspacing="0" cellpadding="0" summary="Your contact information">
	<tr>
		<td><label for="config_contact_phone">Phone</label></td>
		<td><input class="text" type="text" name="config[contact][phone]" id="config_contact_phone" value="{$config.contact.phone}" /></td>
		<td><span class="field-definition">Store contact phone number, it is highly recommended that you provide one</span></td>
	</tr>
	<tr class="required">
		<td><label for="config_contact_email">Email</label></td>
		<td><input class="text" type="text" name="config[contact][email]" id="config_contact_email" value="{$config.contact.email}" /><em>*</em></td>
		<td><span class="field-definition">The E-Mail address that all automated store emails will be sent to</span></td>
	</tr>
	<tr class="required">
		<td><label for="config_shipping_city">Town / City</label></td>
		<td><input class="text" type="text" name="config[shipping][city]" id="config_shipping_city" value="{$config.shipping.city}" /><em>*</em></td>
	<td><span class="field-definition">The city that your products will be delivered from</span></td>
	</tr>
	<tr class="required">
		<td><label for="config_shipping_country">Country</label></td>
		<td><select name="config[shipping][country]" id="config_shipping_country">
		{html_options options=$countries selected=$config.shipping.country}
	</select><em>*</em></td>
	<td><span class="field-definition">The country that your products will be delivered from</span></td>
	</tr>
	</table>
	</fieldset>
	
	<fieldset><legend>E-Mail settings</legend>
	<table class="form" cellspacing="0" cellpadding="0" summary="E-Mail settings">
	<tr class="required">
		<td><label for="config_mail_host">Mail server address</label></td>
		<td><input class="text" type="text" name="config[mail][host]" id="config_mail_host" value="{$config.mail.host}" /><em>*</em></td>
	</tr>
	<tr class="required">
		<td><label for="config_mail_method">Send method</label></td>
		<td><select name="config[mail][method]" id="config_mail_method">
			<option {if $config.mail.method == "mail"}selected="selected"{/if} value="mail">Mail (Default)</option>
			<option {if $config.mail.method == "sendmail"}selected="selected"{/if} value="sendmail">Sendmail</option>
			<option {if $config.mail.method == "smtp"}selected="selected"{/if} value="smtp">SMTP</option>
		</select><em>*</em></td>
	</tr>
	<tr>
		<td><label for="config_mail_pop_before_smtp">Use pop-before-smtp</label></td>
		<td><input class="checkbox" type="checkbox" name="config[mail][pop_before_smtp]" id="config_mail_pop_before_smtp" {if $config.mail.pop_before_smtp}checked="checked"{/if} value="1" /> <label for="config_mail_pop_before_smtp">Yes</label></td>
	</tr>
	<tr>
		<td><label for="config_mail_username">Mail server username</label></td>
		<td><input class="text" type="text" name="config[mail][username]" id="config_mail_username" value="{$config.mail.username}" /></td>
	</tr>
	<tr>
		<td><label for="config_mail_password">Mail server password</label></td>
		<td><input class="text" type="text" name="config[mail][password]" id="config_mail_password" value="{$config.mail.password}" /></td>
	</tr>
	</table>
	</fieldset>	
	
	<fieldset><legend>Site content control</legend>
	<table class="form" cellspacing="0" cellpadding="0" summary="Site content control">
	<tr class="required">
		<td><label for="config_content_site_title">Site title</label></td>
		<td><input class="text" type="text" name="config[content][title]" id="config_content_site_title" value="{$config.content.title}" /><em>*</em></td>
	</tr>
	<tr class="required">
		<td><label for="config_admin_results_per_page">Rows per page (admin)</label></td>
		<td><input class="text numeric" type="text" name="config[admin][results_per_page]" id="config_admin_results_per_page" value="{$config.admin.results_per_page}" /><em>*</em></td>
	</tr>
	<tr class="required">
		<td><label for="config_catalogue_products_per_page">Products per page (catalog)</label></td>
		<td><input class="text numeric" type="text" name="config[catalogue][products_per_page]" id="config_catalogue_products_per_page" value="{$config.catalogue.products_per_page}" /><em>*</em></td>
	</tr>
	<tr class="required">
		<td><label for="config_search_results_per_page">Search results per page</label></td>
		<td><input class="text numeric" type="text" name="config[search][results_per_page]" id="config_search_results_per_page" value="{$config.search.results_per_page}" /><em>*</em></td>
	</tr>
	</table>
	</fieldset>
	
	<fieldset><legend>Additional feature settings</legend>
	<table class="form" cellspacing="0" cellpadding="0" summary="Additional feature settings">
	<tr>
		<td><label for="config_core_gzipcompress">Enable Gzip Compression</label></td>
		<td><input class="checkbox" type="checkbox" name="config[core][gzipcompress]" id="config_core_gzipcompress" value="1" {if $config.core.gzipcompress}checked="checked"{/if} /><label for="config_core_gzipcompress">Yes</label></td>
	</tr>
	<tr>
		<td><label for="config_adsense_enabled">Enable Google Adsense</label></td>
		<td><input class="checkbox" type="checkbox" name="config[adsense][enabled]" id="config_adsense_enabled" value="1" {if $config.adsense.enabled}checked="checked"{/if} /><label for="config_adsense_enabled">Yes</label></td>
	</tr>
	<tr>
		<td><label for="config_adsense_key">Google adsense key</label></td>
		<td><input class="text" type="text" name="config[adsense][key]" id="config_adsense_key" value="{$config.adsense.key}" /></td>
	</tr>
	<tr>
		<td><label for="config_core_google_maps_api_key">Google maps API key</label></td>
		<td><input class="text" type="text" name="config[core][google_maps_api_key]" id="config_core_google_maps_api_key" value="{$config.core.google_maps_api_key}" /></td>
	</tr>
	</table>
	</fieldset>
	
	<fieldset><legend>Confirm &amp; Save</legend>
	<input type="hidden" name="config[host][port]" value="{$config.host.port}" />
	<input type="hidden" name="config[host][ssl_port]" value="{$config.host.ssl_port}" />
	<input type="submit" value="Save all settings &gt;&gt;" name="save" onclick="return confirm('Are you sure you wish to update your configuration?')" />
	</fieldset>
</form>