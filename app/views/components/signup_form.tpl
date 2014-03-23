<table class="form" cellspacing="0" summary="New customer registration form">
<caption><em>*</em> denotes a required field</caption>
<tr class="required">
	<td><label for="address_book_first_name">First Name:</label></td>
	<td><input class="text" type="text" name="address_book[first_name]" id="address_book_first_name" maxlength="100" value="{$address_book.first_name|escape:"html"}" /><em>*</em></td>
</tr>
<tr class="required">
	<td><label for="address_book_last_name">Last Name:</label></td>
	<td><input class="text" type="text" name="address_book[last_name]" id="address_book_last_name" maxlength="100" value="{$address_book.last_name|escape:"html"}" /><em>*</em></td>
</tr>
<tr class="required">
	<td><label for="account_email">E-Mail Address:</label></td>
	<td><input class="text" type="text" name="account[email]" id="account_email" maxlength="250" value="{$account.email|escape:"html"}" /><em>*</em></td>
</tr>
<tr class="required">
	<td><label for="account_password">Password:</label></td>
	<td><input class="text" type="password" name="account[password]" id="account_password" maxlength="100" value="" /><em>*</em></td>
</tr>
<tr class="required">
	<td><label for="account_password_confirm">Confirm Password:</label></td>
	<td><input class="text" type="password" name="account[password_confirm]" id="account_password_confirm" maxlength="100" value="" /><em>*</em></td>
</tr>
<tr class="required">
	<td><label for="account_phone">Phone:</label></td>
	<td><input class="text" type="text" name="account[phone]" id="account_phone" maxlength="100" value="{$account.phone|escape:"html"}" /><em>*</em></td>
</tr>
<tr>
	<td><label for="account_cellphone">Cell Phone:</label></td>
	<td><input class="text" type="text" name="account[cellphone]" id="account_cellphone" maxlength="100" value="{$account.cellphone|escape:"html"}" /></td>
</tr>
<tr class="required">
	<td><label for="address_book_street">Street:</label></td>
	<td><input class="text" type="text" name="address_book[street]" id="address_book_street" maxlength="100" value="{$address_book.street|escape:"html"}" /><em>*</em></td>
</tr>
<tr class="required">
	<td><label for="address_book_suburb">Suburb:</label></td>
	<td><input class="text" type="text" name="address_book[suburb]" id="address_book_suburb" maxlength="100" value="{$address_book.suburb|escape:"html"}" /><em>*</em></td>
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
		<input class="button" type="submit" name="signup[submit]" id="signup_submit" value="Continue &gt;&gt;" />
	</td>
</tr>
</table>