{* Shopping Cart checkout page *}
{assign var="page_title" value="Checkout"}
{stylesheet_include_tag file="checkout"}
{javascript_include_tag file="checkout"}

{if $is_logged_in_as_member}
	To confirm your order, please complete the following 4 sections.
	<form action="{$https_location}/checkout" method="post">
	<fieldset><legend>1. Cart contents</legend>
	Update your cart as necessary to suit your order.
	{include file="components/cart.tpl"}
	</fieldset>
	</form>
	
	<form action="{$https_location}/checkout" method="post">
	<fieldset><legend>2. Delivery &amp; Billing information</legend>
	Select the address you wish to have this order delivered to.

	<div id="address-container">
		<ul id="delivery-addresses">
		{foreach name=address_loop item=address from=$address_books}
		<li {if $address.id == $delivery_address.id}class="selected"{/if}>
			<input class="radio-input" type="radio" name="delivery_address_book_id" id="delivery_address_{$address.id}" value="{$address.id}" {if $address.id == $delivery_address.id}checked="checked"{/if} />
			<label for="delivery_address_{$address.id}">Select<br />{include file="components/address.tpl" address=$address}</label><br /><br />
			<a href="{$https_location}/my-account/address-book/edit/{$address.id}?redirect=checkout">Edit</a>{if NOT $address.is_primary} | <a href="{$https_location}/my-account/address-book/delete/{$address.id}?redirect=checkout">Delete</a>{/if}
		</li>
		{/foreach}
		</ul>
		
		<br />Select the address you wish to have this order billed to.
		<ul id="billing-addresses">
		{foreach name=address_loop item=address from=$address_books}
		<li {if $address.id == $billing_address.id}class="selected"{/if}>
			<input class="radio-input" type="radio" name="billing_address_book_id" id="billing_address_{$address.id}" value="{$address.id}" {if $address.id == $billing_address.id}checked="checked"{/if} />
			<label for="billing_address_{$address.id}">Select<br />{include file="components/address.tpl" address=$address}</label><br /><br />
			<a href="{$https_location}/my-account/address-book/edit/{$address.id}?redirect=checkout">Edit</a>{if NOT $address.is_primary} | <a href="{$https_location}/my-account/address-book/delete/{$address.id}?redirect=checkout">Delete</a>{/if}
		</li>
		{/foreach}
		</ul><br />
		
		<p id="change-address">
		<a href="{$https_location}/my-account/address-book/add?redirect=checkout">Add a new address</a></p>
	</div>
	
	<table id="shipping-info" summary="A summary of your order delivery costs">
	<caption>Order delivery information</caption>
	<tr>
		<th>From:</th>
		<td id="delivery_from">{$config.shipping.city}</td>
	</tr>
	<tr>
		<th>To:</th>
		<td id="delivery_to">{$delivery_address.country.name}</td>
	</tr>
	<tr>
		<th>Zone:</th>
		<td id="delivery_zone">{$shipping_zone.display_name}</td>	
	</tr>
	<tr>
		<th>Weight (Kg):</th>
		<td id="delivery_weight">{$total_weight|number_format:2}</td>
	</tr>
	<tr>
		<th>Discount:</th>
		<td id="delivery_discount">${$delivery_discount|number_format:2}</td>
	</tr>
	<tr class="last">
		<th>Delivery Cost</th>
		<td id="delivery_total">{if $total_cost_of_shipping == 0}Free shipping!{else}${$total_cost_of_shipping|number_format:'2'}{/if}</td>
	</tr>
	</table>
	</fieldset>

	<fieldset><legend>3. Payment information</legend>
	{if $payment_method.credit_card}
		{if $payment_method.bank_deposit}<input class="radio" type="radio" name="payment_method" id="payment_method_credit_card" value="credit_card" checked="checked" /><label for="payment_method_credit_card"><strong>Pay by Credit Card</strong></label><br />{/if}
		<p id="credit-card-payment">You will be redirected to our secure payment page to enter your credit card details. We support the latest security protocols, including 128-bit SSL, PCI DSS compliance and the 3D secure protocol. All major credit cards are supported, including Visa, MasterCard, Amex, Bankcard, Diners and JCB</p>
	{/if}
	{if $payment_method.bank_deposit}
		{if $payment_method.credit_card}<div class="divider"></div><br />{/if}
	<input class="radio" type="radio" name="payment_method" id="payment_method_bank_deposit" {if NOT $payment_method.credit_card}checked="checked"{/if} value="bank_deposit" /><label for="payment_method_bank_deposit"><strong>Pay by Bank Deposit</strong></label>
	<p id="bank-deposit-payment">To pay by direct bank deposit, please complete the order and deposit the total order amount into the bank account given
	in the email you will receive after placing this order. Please use the order number as a reference for your payment so
	we can process your order quickly.</p>
	{/if}
	</fieldset>
	
	<fieldset><legend>4. Additional information</legend>
	<span>Please enter any special requests you have about your order (optional). <br />
	<textarea rows="2" cols="40" name="order[comments]" class="text-input" id="order_comments">{$order.comments|escape:'html'}</textarea></span>
	</fieldset>
		
	<p id="confirm_order_button"><input type="image" src="images/layout/confirm_order_button.gif" name="confirm_order[button]" alt="Confirm your order" /></p>
	</form>
	<div class="clearer"></div>
{else}
	{if !isset($smarty.post.signup)}
		<form action="{$https_location}/checkout" method="post">
		<fieldset><legend>Existing customers</legend>
		If you have shopped with us before, you may simply enter your E-Mail address and password below to continue.
		{include file="components/login_form.tpl"}
		</fieldset>
		</form>
	{/if}
	
	{if !isset($smarty.post.login)}
		<form action="{$https_location}/checkout" method="post">
		<fieldset><legend>New Customers</legend>
		New customers must fill out the form below to continue.
		{include file="components/signup_form.tpl"}
		</fieldset>	
		</form>
	{/if}
{/if}