{* Product Reviews *}
{javascript_include_tag file="product_reviews"}

{assign var="page_title" value="Product reviews for `$product.name`"}

Double click on a review comment to edit it.

<ol id="user-submitted-reviews">
	{foreach item=review from=$product.reviews}
		{assign var="review_rating_as_a_percentage" value=$review.rating*20}
		<li>
			<ul class="star-rating">
				<li class="current-rating" style="width:{$review_rating_as_a_percentage}%;">{$review.rating}/5</li>
			</ul>
			<form class="ajax" action="admin/reviews/edit/{$review.id}" method="post">
				<p class="review-comment">{$review.comment|escape:"html"}</p>
				<small>Reviewed {$review.reviewed_at|history_date_format} by {$review.first_name[0]}. {$review.last_name}</small>
				<span>[ <a class="delete" href="admin/reviews/delete/{$review.id}">delete</a> ]</span>
			</form>
		</li>
		
	{foreachelse}
		<li id="no-reviews-note">No reviews have yet been posted for this product.</li>
	{/foreach}
</ol>

<a href="admin/products/index"><< Back to products</a>