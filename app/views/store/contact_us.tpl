{* Contact Us page *}
{assign var="page_title" value="Contact Us"}
{assign var="meta_keywords" value=$content.keywords}
{assign var="meta_description" value=$content.description}

{if isset($smarty.post.contact)}
	{if $form_has_been_sent}
	<p id="success-message">Your enquiry has been sent.  You should receive a response within the next 2-3 working days.</p>
	{/if}
{/if}
{$content.content}
<form action="{$http_location}/contact-us" method="post">
<fieldset><legend>Your contact details &amp; enquiry</legend>
<table class="form" summary="Your contact details">
<tr class="required">
	<td><label for="contact_name">Full Name:</label></td>
	<td><input class="text" type="text" name="contact[name]" id="contact_name" maxlength="100" value="{$contact.name|escape:"html"}" /><em>*</em></td>
</tr>
<tr class="required">
	<td><label for="contact_email">E-Mail:</label></td>
	<td><input class="text" type="text" name="contact[email]" id="contact_email" maxlength="200" value="{$contact.email|escape:"html"}" /><em>*</em></td>
</tr>
<tr>
	<td><label for="contact_phone">Phone:</label></td>
	<td><input class="text" type="text" name="contact[phone]" id="contact_phone" maxlength="100" value="{$contact.phone|escape:"html"}" /></td>
</tr>
<tr>
	<td><label for="contact_subject">Subject:</label></td>
	<td><select name="contact[subject]" id="contact_subject">
	<option value="General">General information</option>
	<option value="Technical">Technical</option>
	<option value="Support">Support</option>
	<option value="Sales">Sales</option>
	</select></td>
</tr>
<tr class="required">
	<td><label for="contact_enquiry">Enquiry:</label></td>
	<td><textarea name="contact[enquiry]" rows="6" cols="30" id="contact_enquiry">{$contact.enquiry|escape:"html"}</textarea></td>
</tr>
<tr>
	<td>&nbsp;<input type="hidden" name="user_form_key" value="{$session_key}" /></td>
	<td><input class="button" type="submit" name="send" id="form_submit" value="Send Enquiry" /></td>
</tr>
</table>
</fieldset>
</form>