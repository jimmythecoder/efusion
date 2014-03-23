{* Product display template *}
{assign var="page_title" value=$product.name}
{assign var="meta_description" value=$product.description}
{javascript_include_tag file="lib/thickbox"}
{javascript_include_tag file="product"}

<div id="product-right-content">
	{if $product.image_id != DEFAULT_IMAGE_ID}<a title="{$product.name|escape:"quotes"}" class="thickbox" href="{$current_location}/images/products/{$product.image.filename|escape:'url'}">{/if}
	<img class="thumbnail" src="{get_image_src_resized_and_cropped_to width="130" height="85" filename=$product.image.filename}" alt="{$product.name|escape:"quotes"}" />{if $product.image_id != DEFAULT_IMAGE_ID}</a>{/if}
	<p class="price">${$product.sale_price|number_format:2}</p>

	<form action="{$http_location}/cart" method="post">
	<input type="hidden" name="product[id]" value="{$product.id}" />
	{if $product.variant_groups}<fieldset><legend>Options</legend>
	<ul class="variants">
		{foreach item=variant_group from=$product.variant_groups}
			<li><label for="product_variant_{$variant_group.id}">{$variant_group.label}</label>: 
				<select class="variant_option" name="product[variants][{$variant_group.id}]" id="product_variant_{$variant_group.id}">
				{foreach item=variant from=$variant_group.variants}
					<option value="{$variant.id|escape:'html'}">{$variant.name|escape:"html"}</option>
				{foreachelse}
					<option value="0">----------</option>
				{/foreach}
				</select>
			</li>
		{/foreach}
	</ul>
	</fieldset>{/if}
	<p><label for="product_quantity">Qty: </label><input class="text product_quantity numeric" type="text" size="3" maxlength="3" name="product[quantity]" id="product_quantity" value="1" /></p>
	<p><input type="image" src="images/layout/addToCart.gif" name="product[add_product_to_cart]" id="add_product_to_cart" alt="Add to cart" /></p>
	</form>
</div>
<div id="product-left-content" class="description">
	{$product.description}
</div>
<br class="clearer" />

	<img src="images/layout/product_reviews_heading.jpg" alt="Product Reviews" />
    <div id="reviews">
    	{if $account_group == "members"}
    		{if $is_user_email_activated}
	    		{if NOT $product.has_user_submitted_review}
		    	<form id="review-form" action="{$current_location}/services/product-review" method="post">
		    	<fieldset><legend>Post a review</legend>
		    	<table class="form" summary="Post a review for {$product.name|escape:"html"}">
		    	<tr>
		    		<td><span style="float: left;">Your rating <small>(select a star)</small></span>
		    			<ul id="star-rating" class="star-rating">
		    				{assign var="average_rating_as_a_percentage" value=$product.average_review_rating*20}
							<li id="current-rating" class="current-rating" style="width:{if isset($smarty.get.rating)}{$smarty.get.rating*20}{else}{$average_rating_as_a_percentage}{/if}%;">Currently {$product.average_review_rating}/5 Stars.</li>
							<li><a href="product/{$product.url_name}?rating=1#review_comment" title="1 star out of 5" class="one-star">1</a></li>
							<li><a href="product/{$product.url_name}?rating=2#review_comment" title="2 stars out of 5" class="two-stars">2</a></li>
							<li><a href="product/{$product.url_name}?rating=3#review_comment" title="3 stars out of 5" class="three-stars">3</a></li>
							<li><a href="product/{$product.url_name}?rating=4#review_comment" title="4 stars out of 5" class="four-stars">4</a></li>
							<li><a href="product/{$product.url_name}?rating=5#review_comment" title="5 stars out of 5" class="five-stars">5</a></li>
						</ul>
		    			<input type="hidden" id="rating" name="rating" value="{$smarty.get.rating|default:$product.average_review_rating}" />
		    			<input type="hidden" id="product_id" name="product_id" value="{$product.id}" />
		    			<input type="hidden" id="method" name="method" value="form" />
		    			<input type="hidden" id="product_url_name" name="product_url_name" value="{$product.url_name|escape:"html"}" />
		    		</td>
		    	</tr>
		    	<tr>
		    		<td><textarea class="text" id="comment" name="comment" rows="5" cols="50">Enter your review here...</textarea>
		    		<br /><input class="button" type="submit" name="submit_review" id="submit_review" value="Submit Review" /></td>
				</tr>
		    	</table>
		    	</fieldset>
		    	</form>
		    	{/if}
		    {else}
		    	<p class="small note">You must activate your E-Mail address to submit a review. Please check your email for the activation URL.</p>
		    {/if}
    	{else}
    	<p class="small note">Please <a href="login">Log-In</a> to post a review</p>
		{/if}
		
		<ol id="user-submitted-reviews">
			{foreach name=reviews item=review from=$product.reviews}
				{assign var="review_rating_as_a_percentage" value=$review.rating*20}
				<li>
					<ul class="star-rating"><li class="current-rating" style="width:{$review_rating_as_a_percentage}%;">{$review.rating}/5</li></ul>
					<p>{$review.comment|escape:"html"}</p>
					<small>Reviewed {$review.reviewed_at|history_date_format} by {$review.first_name[0]}. {$review.last_name}</small>
				</li>
			{foreachelse}
				<li id="no-reviews-note">No reviews have yet been posted for this product. Please take some time to give your review which will help others make a more informed buying decision.</li>
			{/foreach}
		</ol>
    </div>