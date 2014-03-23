	<fieldset><legend>Address Details</legend>
	<table class="form" cellspacing="0" summary="New address registration form">
	<tr class="required">
		<td><label for="address_book_first_name">First Name</label></td>
		<td><input class="text" type="text" name="address_book[first_name]" id="address_book_first_name" value="{$address_book.first_name}" /><em>*</em></td>
	</tr>
	<tr class="required">
		<td><label for="address_book_last_name">Last Name</label></td>
		<td><input class="text" type="text" name="address_book[last_name]" id="address_book_last_name" value="{$address_book.last_name}" /><em>*</em></td>
	</tr>
	<tr>
		<td><label for="address_book_company">Company</label></td>
		<td><input class="text" type="text" name="address_book[company]" id="address_book_company" value="{$address_book.company}" />
	</td>
	</tr>
	<tr class="required">
		<td><label for="address_book_street">Street</label></td>
		<td><input class="text" type="text" name="address_book[street]" id="address_book_street" value="{$address_book.street}" /><em>*</em></td>
	</tr>
	<tr class="required">
		<td><label for="address_book_suburb">Suburb</label></td>
		<td><input class="text" type="text" name="address_book[suburb]" id="address_book_suburb" value="{$address_book.suburb}" /><em>*</em></td>
	</tr>
	<tr class="required">
		<td><label for="address_book_city">Town / City:</label></td>
		<td><input class="text" type="text" name="address_book[city]" id="address_book_city" maxlength="100" value="{$address_book.city|escape:"html"}" /><em>*</em></td>
	</tr>
	<tr>
		<td><label for="address_book_post_code">Postal Code:</label></td>
		<td><input class="text" type="text" name="address_book[post_code]" id="address_book_post_code" maxlength="100" value="{$address_book.post_code|escape:"html"}" /> <a title="Find your postcode" class="popup-link" href="http://www.nzpost.co.nz/Cultures/en-NZ/OnlineTools/PostCodeFinder/">Not sure?</a></td>
	</tr>
	<tr class="required">
		<td><label for="address_book_country_id">Country:</label></td>
		<td><select name="address_book[country_id]" id="address_book_country_id">
			{html_options options=$countries selected=$address_book.country_id}
		</select><em>*</em></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
		<label for="address_book_is_primary"><input class="checkbox" type="checkbox" name="address_book[is_primary]" id="address_book_is_primary" {if $address_book.is_primary}checked="checked"{/if} value="1" /> Set as my primary address</label></td>
	</tr>
	<tr class="hidden">
		<td colspan="2"><input type="hidden" name="address_book[id]" id="address_book_id" value="{$address_book.id}" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input class="button" type="submit" name="save" id="address_book_save" value="Save address" />
		<input class="button" type="submit" name="cancel" id="cancel" value="Cancel" onclick="window.history.go(-1);return false;" /></td>
	</tr>
	</table>
	</fieldset>